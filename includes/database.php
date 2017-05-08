<?php
require_once("constants.php");

class MySQLDatabase {
	
	private $connection;
	public $last_query;
	public $db_error;
	private $magic_quote_active;
	private $real_escape_string_exists; 
	
	function __construct() {
		$this->open_connection();
		
		//Moved this up from escaped_values() so the server doesn't have to run these everything time we need to escape some values
		$this->magic_quote_active = get_magic_quotes_gpc();
		$this->real_escape_string_exists = function_exists("mysql_real_escape_string"); 
	}
	
	public function open_connection(){
		$this->connection = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASS, DB_NAME); 
        if(!$this->connection) {
        	die("Database connection failed: " . mysqli_connect_error() . " " . mysqli_connect_errno());	
        }
	}
	
	public function close_connection(){
		if(isset($this->connection)) {
			mysqli_close($this->connection);
			unset($this->connection);
		}
	}
	
	public function query($sql) {
		$this ->last_query = $sql;
		$result = mysqli_query($this->connection, $sql);
		//$this->confirm_query($result);
		return $result;
	}
	
	private function confirm_query($result) {
		if(!$result) {
			$output = "Database query failed: " . mysqli_error($this->connection) . "<br /><br />";
			// Probably want to comment this out whne out of the testing enviroment 
			$output .= "Last SQL Query: " . $this->last_query;
			die($output);
		}
	}
	
	public function release_data($result) {
		 return mysqli_free_result($result)? true : false;
	}
	
	//This used to be my_sql_prep... however, in the spirit of being agnostic we are lableing it without a specfic DB in mind
	public function escape_values($value) {

		// new enough is defined as php >= v4.3.0 
		if($this->real_escape_string_exists){
			// php v4.3.0 or higher, undo any magic quote effects so mysql_real_escape can do the work
			// if($this->magic_quotes_active){
			// 	$value = stripslashes($value);
			// }
			$value = mysqli_real_escape_string($this->connection, $value);
		} else {
			// before PHP v4.3.0, if magic quotes aren't already on then add slashes manually
			// if(!$this->magic_quotes_active){
				$value = addslashes($value);
			// }
			// if magic quotes are active then the slashes already exist. 	
		}
		return $value;
	}
	
	// Makes our function agnostic, in the future if we use orcale or some other DB, well can hook up here. 
	public function fetch_array($result_set) {
		return mysqli_fetch_array($result_set);
	}
	
	public function num_rows($result_set){
		// returns how many rows are in a result set
		return mysqli_num_rows($result_set);
	}
	
	public function insert_id(){
		// get the last id inserted over the current db connection
		return mysqli_insert_id($this->connection);
	}
	
	public function affected_rows() {
		// how many rows were affected by the last sql command on the current db connection
		return mysqli_affected_rows($this->connection);
	}
	
	public function db_error() {
		global $connection;
		return mysqli_error();
	}
}

$database = new MySQLDatabase();

?>