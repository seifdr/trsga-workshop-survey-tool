<?php 

/**
  	This is an extention of the native PHP DateTime object
 */
 
// class trsgaTime extends DateTime {
 
class trsgaTime extends DateTime {
	
	public function __construct(){
		DateTime::__construct('America/New_York');
	} 
	
	public function rightNow($time=NULL, $sql=NULL, $type=NULL){
		
		date_default_timezone_set('America/New_York');
		
		$this->setTimezone(new DateTimeZone('America/New_York'));
		
		$now = new DateTime('NOW', new DateTimeZone('America/New_York')); 
		
		if($sql){
			if($time){
				return $this->format('Y-m-d H:i:s');
			} 
		} else {
			if(($time)&&($type == "pcFullDate")){
				return $this->format('F j, Y, g:i a');
			}elseif($type == "year"){
				return $this->format('Y');	
			} elseif($time){
				return $this->format('m-d-Y g:i');	
			} else {
				return $this->format('m-d-Y');
			}
		}
	}
	
	public function returnYear($input){
		return date('Y', strtotime($input));
	}
	
	public function convert_date_to_MYSQL_date($date){
		date_default_timezone_set('America/New_York');
		return date('Y-m-d', strtotime($date));
	}
	
	public function convert_MYSQL_date_to_date($date, $time=false){
		if(!$time){
			return date('m/d/Y', strtotime($date));
		} else {
			return date('m/d/Y g:i a', strtotime($date));
		}
		
	}
	
	public function convert_date_to_MYSQL_datetime($date){
		return date('Y-m-d H:i:s', strtotime($date));
	}
		
	public function convert_MYSQL_datetime_to_datetime($datetime, $fullYear=true){			
		if($fullYear == "true"){
			return date('m/d/Y g:i a', strtotime($datetime));
		} else {
			return date('m/d/y g:i a', strtotime($datetime));
			// echo "hello";
		}
	}	
	
	public function convert_time_to_MYSQL_time($time){
		return date('H:i', strtotime($time));
	}
	
	public function convert_MYSQL_time_to_time($time){
		return date('g:i a', strtotime($time));
	}
	
}

$trsgaTime = new trsgaTime(); 

?>