<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require( 'database_object.php' );
    require( 'functions.php' );
    require( 'trsgaTime.php' );
    require( 'pagination.php');
    require( 'wsModel.php' );
    require( 'userModel.php');
    require( 'wsView.php' );
    require( 'wsController.php' );
    
    $login_user = array(
        (object)array(
            "id" => 1,
            "username" => "dseif",
            "password" => "",
            "hashed_password" => "", 
            "first_name" => "Duncan",
            "last_name" => "Seif",
            "clearance" => "Master",
            "user_type" => "outreach",
            "surveyID" => "",
            "active" => "1"
        ),
        (object)array(
            "id" => 2,
            "username" => "bbrewer",
            "password" => "",
            "hashed_password" => "", 
            "first_name" => "Britt",
            "last_name" => "Brewer",
            "clearance" => "Outreach Staff",
            "user_type" => "outreach",
            "surveyID" => "A",
            "active" => "1"
        ),
        (object)array(
            "id" => 1,
            "username" => "aswisher",
            "password" => "",
            "hashed_password" => "", 
            "first_name" => "Angela",
            "last_name" => "Swisher",
            "clearance" => "Outreach Staff",
            "user_type" => "outreach",
            "surveyID" => "",
            "active" => "0"
        )
    );

     
?>