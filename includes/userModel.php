<?php 

/**
 * 
 */
class User extends DatabaseObject
{

    protected static $table_name = "users";	
	protected static $db_fields  = array('id','first_name', 'last_name', "surveyID", "clearance", "user_type", "active");
	
    public $id;
    public $first_name;
    public $last_name;
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