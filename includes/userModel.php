<?php 

/**
 * 
 */
class User extends DatabaseObject
{

    protected static $table_name = "users";	
	protected static $db_fields  = array('id','FirstName', 'LastName', "Code");
	
    public $id;
    public $FirstName;
    public $LastName;
    public $Code;

    // function __construct() {
    // {
    //     # code...
    // }




}

$users = new User;


?>