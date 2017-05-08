<?php

require_once('database.php');

class DatabaseObject {
	
	public static function incremented_insert($increment_point) {
		global $database;
		
		$sql = "UPDATE ". static::$table_name . " SET position = position + 1 where position >= " . $increment_point;
		$result = $database->query($sql);		
		return $result;
	}
	
	public static function increment_decrement($selected_position, $previous_position) {
		global $database;
		global $result;
		
		if($selected_position == 1){
			global $result;
			$sql = "UPDATE ". static::$table_name . " SET position = position + 1 where position >= 1";
			
			$result = $database->query($sql);
		} else {
			if($selected_position > $previous_position){
				global $result;
				//if selected position is greated than the previous position
				$sql = "UPDATE ". static::$table_name . " SET position = position - 1 where position <= " . $selected_position;
				
				if(($previous_position != NULL) OR !empty($previous_position)){
					$sql .= " AND position >= " . $previous_position;	
				} 
				
				$result = $database->query($sql);
			
			} else {
				global $result;
				//if selected position is less than previous position 
				$sql = "UPDATE ". static::$table_name . " SET position = position + 1 where position >= " . $selected_position;
				if(($previous_position != NULL) OR !empty($previous_position)){
					$sql .= " AND position < " . $previous_position;	
				} 
				
				$result = $database->query($sql);
			}
		}
		return $result;
	}

	public static function decremented_delete($decrement_point) {
		global $database;
		
		$sql = "UPDATE ". static::$table_name . " SET position = position - 1 where position >= " . $decrement_point;
		$result = $database->query($sql);		
		return $result;
	}
	
	public function countBy($sql, $outreachDB = false){
		global $database;

		$result = $database->query($sql);		
		
		echo $result;
		
		return $result;
	}		
	
	public static function find_all($outreachDB = false){
		global $database;

		//the SQL used to be find_by_sql("SELECT * FROM users")
		$find_all_sql = "SELECT * FROM " . static::$table_name;
		
		if(((static::$table_name) == ("call_center_surveys"))||((static::$table_name) == ("outreach_surveys"))){
			$sql_by_id .= " WHERE removed=0 ";
		}
		
		return static::find_by_sql($find_all_sql, $outreachDB);
	}	
	
	public static function count_all($sql_count=""){
		global $database;
		$sql = "SELECT COUNT(*) FROM " . static::$table_name;
		if($sql_count != ""){
			$sql .= " {$sql_count}";
		}
		
		if(((static::$table_name) == ("call_center_surveys"))||((static::$table_name) == ("outreach_surveys"))){
			$sql_by_id .= "  WHERE removed=0 ";
		}
				
		$result_set = $database->query($sql);
		$row = $database->fetch_array($result_set);
		return array_shift($row);
	}
			
	public static function find_by_id($id=0, $outreachDB = false){		
		global $database;

		// Used to be equal to $result_set, however, find_by_sql is now returning an object array
		
		//the SQL used to be find_by_sql("SELECT * FROM users where id=($id)")
		$sql_by_id = "SELECT * FROM " . static::$table_name . " WHERE id=" . $database->escape_values($id);
		
		if(((static::$table_name) == ("call_center_surveys"))||((static::$table_name) == ("outreach_surveys"))){
			$sql_by_id .= " AND removed=0 ";
		}
		
		$sql_by_id .= " LIMIT 1";

		$result_array = static::find_by_sql($sql_by_id);
		// This was for pulling the information out of that single array... now its a single object so it need to change.
		// $found = $database->fetch_array($result_set);
		// return $found;
		
		return !empty($result_array) ? array_shift($result_array) : false;
	} 		
	
	public static function find_by_sql($sql="", $outreachDB = false){
		global $database;

		$result_set = $database->query($sql);
		// The old way returned just the result set array ($result_set), that returned rows in arrays. We don't want that. We want to pull out
		// a row and instantiate an object based off that information, then assign all those objects to a new array
		$object_array = array();
		while ($row = $database->fetch_array($result_set)){
			$object_array[] = static::instantiate($row);
		}
		return $object_array;
	}
	
	public static function instantiate($record){
		//could check that $record exists and is an array 
		
		// the non late static binding way of doing this was...
		// $object = new self;
		
		$class_name = get_called_class();
		$object = new $class_name;
	
		// This is the simple long form approach:
		
		// $object->id = $record['id'];
		// $object->username = $record['username'];
		// $object->password = $record['password'];
		// $object->first_name = $record['first_name'];
		// $object->last_name = $record['last_name'];
		
		// More dynamic, short form approach 
		foreach ($record as $attribute => $value){
			if($object->has_attribute($attribute)){
				$object->$attribute = $value;
			}
		}
		
		return $object;
	}	

	private function has_attribute($attribute) {
		// get_object_vars returns an associative array with all attributes 
		// (including private ones!) as keys and their current values as the value
		
		$object_vars = $this->attributes();
		// We don't care about the value, we just want to know if the key exists
		// Will return true or false
		return array_key_exists($attribute, $object_vars);
	}	
	
	protected function attributes(){
		//returns an array of attribute (names)keys and their values. Makes create(), read(), update() interchangable for different classes with different attributes. 
		//for example User->create() was listed out like this before:	
					
					// $this->hashed_password = sha1($this->password);
					
					// $sql = "INSERT INTO ". static::$table_name . " (";
					// $sql .= "username, hashed_password, first_name, last_name";
					// $sql .= ") VALUES ('";
					// $sql .= $database->escape_values($this->username) . "', '";
					// $sql .= $database->escape_values($this->hashed_password) . "', '";
					// $sql .= $database->escape_values($this->first_name) . "', '";
					// $sql .= $database->escape_values($this->last_name) ."')";
		
		// This is the dynamic way of achieving the samething
		
		//Go through each attribute and as the object to return it's value, and put it into an associative array.
		
		$attributes = array();
		foreach(static::$db_fields as $field){
			if(property_exists($this, $field)){
				//makes array name = sets array value
				$attributes[$field] = $this->$field;
			}
		}
		
		return $attributes;
		// this will return all the attributes of db_fields declared in the class
	}
	
	protected function escaped_attributes($outreachDB = false){
		//returns an array of attribute keys and their values. Makes create(), read(), update() interchangable for different classes with different attributes. 
		//This version will also sanitize the values, to prevent SQL injection
		global $database;
		
		$clean_attributes = array();
		//santize the values before submitting
		// Note: does not alter the actual value of each attribute
		foreach($this->attributes() as $key => $value){
			$clean_attributes[$key] = $database->escape_values($value);
		}                                       

		return $clean_attributes;

	}	
	
	
	public function save($outreachDB = false){
		//save will create a user if need be, otherwise it will update, if the user already exists
		// This is easy to do since a new record won't have any id
		return isset($this->id) ? $this->update($outreachDB) : $this->create($outreachDB);
	}
	
	public function create($outreachDB = false) {

		global $database;
		
		$attributes = $this->escaped_attributes();
		
		$this->hashed_password = sha1($this->password);
		
		$sql = "INSERT INTO ". static::$table_name . " (";
		$sql .= join(", ", array_keys($attributes));
		$sql .= ") VALUES ('";
		$sql .= join("', '", array_values($attributes));
		$sql .= "')";

		if($database->query($sql)){
			// When running a database insert query, we want to make sure to update the id attribute of the object to be whatever the database just saved it as.
			// We don't know what that is because its autoincrementing, and there could be serveral insert statments happen from various users at any given time. Gives use our ID and we put it in this object.  
			// We already had the username, password, first_name, last_name stored (from above), now we have the id
			$this->id = $database->insert_id();
			return true;
		} else {
			return false;
		}
		
	}
	
	protected function update($outreachDB = false) {
		global $database;
		// already pulled an instance of the object. We want to update a few attributes. 
		
		$attributes = $this->escaped_attributes();
		// The equals signs complicated it a little mroe than the create()
		$attribute_pairs = array();
		foreach($attributes as $key => $value){
			if($value != ''){
				$attribute_pairs[] = "{$key} = '{$value}'";	
			}	
		}
		
		$this->hashed_password = sha1($this->password);
		
		$sql = "UPDATE ". static::$table_name . " SET ";
		$sql .= join(", ", $attribute_pairs);
		$sql .= " WHERE id=" . $database->escape_values($this->id);
		$database->query($sql);
		// With update action you don't test if the SQL ran like in create. You use affected_rows() as a boolean
		return($database->affected_rows()==1) ? true : false;
	}

	public function delete() {
		global $database;
		
		$sql = "DELETE FROM ". static::$table_name;
		$sql .= " WHERE id=" . $database->escape_values($this->id);
		$sql .= " LIMIT 1";
		
		$database->query($sql);
		
		return($database->affected_rows()==1) ? true : false;
	}
	
	protected function hasSpecialChars($string, $type=NULL){
		if($type == "periodsOk"){
			$illegal = "-#$%^&*()+=-[]';,/{}|:<>?~";		
		} else {
			$illegal = "-#$%^&*()+=-[]';,./{}|:<>?~";
		}	
		
		if(strpbrk($string, $illegal) == false){
			return true;
		} else {
			return false;
		}
	}		
	
	protected function objectArrayToThisObject($object){
		foreach ($object as $key => $value) {
			$this->{$key} = $value;
		}
	}
}

?>