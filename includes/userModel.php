<?php 

/**
 * 
 */
class User extends DatabaseObject
{

    protected static $table_name = "users";	
	protected static $db_fields  = array('id','FirstName', 'LastName', "surveyID", "clearance", "user_type", "active");
	
    public $id;
    public $FirstName;
    public $LastName;
    public $clearance;
	public $user_type;
	public $surveyID;
	public $active;

    // function __construct() {
    // {
    //     # code...
    // }

}

// $userModel = new User;


?>