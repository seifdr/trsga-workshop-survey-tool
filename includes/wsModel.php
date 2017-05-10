<?php 

/**
 * 
 */
class WorkshopSurvey extends DatabaseObject
{

    protected static $table_name = "workshopSurvey17";	
	protected static $db_fields  = array('id','respondentID', 'question1a', 'question1b', 'question1c', 'question1d', 'question2', 'question3a', 'question3b', 'question3c', 'question3d', 'question4', 'question5', 'question6', 'DLC', 'rep_code', 'survey_month', 'survey_month_num', 'survey_yr', 'fiscal_qtr', 'fiscal_yr', 'name', 'location', 'removed', 'FirstName', 'LastName');
	
    public $id;
    public $respondentID;
    public $question1a;
    public $question1b;
    public $question1c;
    public $question1d;
    public $question2;
    public $questions3a;
    public $question3b;
    public $question3c;
    public $question3d;
    public $question4;
    public $question5;
    public $question6;
    public $DLC;
    public $survey_month;
    public $survey_month_num;
    public $survey_yr;
    public $fiscal_qtr;
    public $fiscal_yr;
    public $name;
    public $location;
    public $removed;

    //counselor info
    public $rep_code;
    public $FirstName;
    public $LastName;

    // function __construct() {
    // {
    //     # code...
    // }

    public function find_current_FY(){
		$date =  getdate();
		
		$month_num = $date['mon'];
		$year = $date['year'];

		if(($month_num == 1)||($month_num == 2)||($month_num == 3)){
			$fy_array = array('fy_quarter' => '3', 'fy_year' => $year);
			return $fy_array;
		} elseif(($month_num == 4)||($month_num == 5)||($month_num == 6)){
			$fy_array = array('fy_quarter' => '4', 'fy_year' => $year);
			return $fy_array;
		} elseif(($month_num == 7)||($month_num == 8)||($month_num == 9)){
			$adjusted_yr = $year + 1;
			$fy_array = array('fy_quarter' => '1', 'fy_year' => $adjusted_yr);
			return $fy_array;
		} elseif(($month_num == 10)||($month_num == 11)||($month_num == 12)){
			$adjusted_yr = $year + 1;
			$fy_array = array('fy_quarter' => '2', 'fy_year' => $adjusted_yr);
			return $fy_array;
		} 	
		
	}	

    // public function get_survey_base_sql( $fy = NULL ){

    //     if( is_null( $fy ) ){
    //         $fy = $this->find_current_FY()['fy_year'];
    //     } 

    //     date_default_timezone_set('America/Chicago');

    //     $sqla = " SELECT ws.id, ws.question1a, ws.question1b, ws.question1c, ws.question1d, ws.fiscal_qtr, ws.fiscal_yr, ws.survey_yr, ws.DLC, ws.rep_code, u.FirstName, u.LastName, u.Code FROM workshopSurvey17 AS ws LEFT OUTER JOIN users AS u ON ws.rep_code = u.Code WHERE ws.fiscal_yr = '". $fy ."' ";

    //     $sqlb = " SELECT workshopSurvey17.id, IF( workshopSurvey17.question1a IS NULL, 0, workshopSurvey17.question1a ) AS question1a, IF( workshopSurvey17.question1b IS NULL, 0, workshopSurvey17.question1b ) AS question1b, IF( workshopSurvey17.question1c IS NULL, 0, workshopSurvey17.question1c ) AS question1c, IF( workshopSurvey17.question1d IS NULL, 0, workshopSurvey17.question1d ) AS question1d, workshopSurvey17.fiscal_qtr, IF( workshopSurvey17.fiscal_yr IS NULL, ". $fy .", workshopSurvey17.fiscal_yr ) AS fiscal_yr, IF( workshopSurvey17.survey_yr IS NULL, ". date('Y') .", workshopSurvey17.survey_yr ) AS survey_yr, IF( workshopSurvey17.DLC IS NULL, CONCAT( DATE_FORMAT( CURRENT_DATE(), '%m' ), DATE_FORMAT( CURRENT_DATE(), '%d' ) , 'XXXX' ), workshopSurvey17.DLC ) AS DLC, IF( workshopSurvey17.rep_code IS NULL, users.Code, workshopSurvey17.rep_code ) AS rep_code, users.FirstName, users.LastName, users.Code FROM workshopSurvey17 RIGHT OUTER JOIN users ON workshopSurvey17.rep_code = users.Code WHERE ( workshopSurvey17.fiscal_yr = '". $fy ."' OR workshopSurvey17.fiscal_yr IS NULL ) ";

    //     $sqlc = $sqla . " UNION " . $sqlb;

    //     $sqld = "SELECT *, ( STR_TO_DATE( CONCAT( SUBSTRING( t.DLC, 3, 2 ), '-', SUBSTRING( t.DLC, 1, 2 ), '-', ( IF( t.survey_yr IS NULL, DATE_FORMAT( CURRENT_DATE(), '%Y' ),  t.survey_yr ) ) ), '%d-%m-%Y' ) ) AS session_date FROM (". $sqlc .") AS t ORDER BY t.rep_code ASC, session_date ASC";
        
    //     return $sqld;
    // }

    public function get_survey_totals_by_FY( $type = NULL, $fy = NULL ){
        global $database;
        
        if( is_null( $fy ) ){
            $fy = $this->find_current_FY()['fy_year'];
        } 

        date_default_timezone_set('America/Chicago');

        $sqla = " SELECT ws.id, ws.question1a, ws.question1b, ws.question1c, ws.question1d, ws.fiscal_qtr, ws.fiscal_yr, ws.survey_yr, ws.DLC, ws.rep_code, u.FirstName, u.LastName, u.Code FROM workshopSurvey17 AS ws LEFT OUTER JOIN users AS u ON ws.rep_code = u.Code WHERE ws.fiscal_yr = '". $fy ."' ";

        $sqlb = " SELECT workshopSurvey17.id, IF( workshopSurvey17.question1a IS NULL, 0, workshopSurvey17.question1a ) AS question1a, IF( workshopSurvey17.question1b IS NULL, 0, workshopSurvey17.question1b ) AS question1b, IF( workshopSurvey17.question1c IS NULL, 0, workshopSurvey17.question1c ) AS question1c, IF( workshopSurvey17.question1d IS NULL, 0, workshopSurvey17.question1d ) AS question1d, workshopSurvey17.fiscal_qtr, IF( workshopSurvey17.fiscal_yr IS NULL, ". $fy .", workshopSurvey17.fiscal_yr ) AS fiscal_yr, IF( workshopSurvey17.survey_yr IS NULL, ". date('Y') .", workshopSurvey17.survey_yr ) AS survey_yr, IF( workshopSurvey17.DLC IS NULL, CONCAT( DATE_FORMAT( CURRENT_DATE(), '%m' ), DATE_FORMAT( CURRENT_DATE(), '%d' ) , 'XXXX' ), workshopSurvey17.DLC ) AS DLC, IF( workshopSurvey17.rep_code IS NULL, users.Code, workshopSurvey17.rep_code ) AS rep_code, users.FirstName, users.LastName, users.Code FROM workshopSurvey17 RIGHT OUTER JOIN users ON workshopSurvey17.rep_code = users.Code WHERE ( workshopSurvey17.fiscal_yr = '". $fy ."' OR workshopSurvey17.fiscal_yr IS NULL ) ";

        $sqlc = $sqla . " UNION " . $sqlb;

        $sqld = "SELECT *, ( STR_TO_DATE( CONCAT( SUBSTRING( t.DLC, 3, 2 ), '-', SUBSTRING( t.DLC, 1, 2 ), '-', ( IF( t.survey_yr IS NULL, DATE_FORMAT( CURRENT_DATE(), '%Y' ),  t.survey_yr ) ) ), '%d-%m-%Y' ) ) AS session_date FROM (". $sqlc .") AS t ORDER BY t.rep_code ASC, session_date ASC";

        if( $type == 'countFails' ){

            $sqle  = " SELECT *, IF( ( ( t2.question1a <= 3 AND t2.question1a != 0 ) AND ( t2.question1b <= 3 AND t2.question1b != 0 ) AND ( t2.question1c <= 3 AND t2.question1c != 0 ) ), '1', '0' ) AS fail FROM (". $sqld .") as t2 ";
            
            $sqlLayer2  = "SELECT t.FirstName, t.LastName, t.fiscal_qtr, t.Code, 
                COUNT( IF( ( t.fiscal_qtr = '1' AND t.fail = '1' ), 1, NULL) ) as Qtr1, 
                COUNT( IF( ( t.fiscal_qtr = '2' AND t.fail = '1' ), 1, NULL) ) as Qtr2, 
                COUNT( IF( ( t.fiscal_qtr = '3' AND t.fail = '1' ), 1, NULL) ) as Qtr3, 
                COUNT( IF( ( t.fiscal_qtr = '4' AND t.fail = '1' ), 1, NULL) ) as Qtr4, 
                SUM( Fail ) as Total FROM (". $sqle .") AS t GROUP BY t.code";
    
        } elseif ( $type == 'count45s' ){

            $sqlLayer2    = "SELECT t.FirstName, t.LastName, t.Code, COUNT( IF( ( t.question1a = '4' AND t.question1b = '4' AND t.question1c = '4' and t.question1d = '4')  , 1, NULL) ) as All_4s, COUNT( IF( ( t.question1a = '5' AND t.question1b = '5' AND t.question1c = '5' and t.question1d = '5'), 1, NULL) ) as All_5s, COUNT( IF( ( t.question1a >= '4' AND t.question1b >= '4' AND t.question1c >= '4' and t.question1d = '4'), 1, NULL) ) as All_4s_5s FROM (". $sqld .") AS t GROUP BY t.code";
        
        } elseif ( $type == 'fyavgs' ){

            $sqlLayer2 = "SELECT FirstName, LastName, ROUND( AVG( question1a ), 2 ) AS q1a_avg, ROUND( AVG( question1b ), 2 ) AS q1b_avg, ROUND( AVG( question1c ), 2 ) q1c_avg, ROUND( AVG( ( question1a + question1b + question1c ) / 3 ), 2 ) AS total_avg FROM ( ". $sqld ." ) as t2 GROUP BY rep_code";

        } else {
            //catch all
            $sqlLayer2    = "SELECT t.FirstName, t.LastName, t.fiscal_qtr, t.Code, COUNT( IF( t.fiscal_qtr = '1', 1, NULL) ) as Qtr1, COUNT( IF( t.fiscal_qtr = '2', 1, NULL) ) as Qtr2, COUNT( IF( t.fiscal_qtr = '3', 1, NULL) ) as Qtr3, COUNT( IF( t.fiscal_qtr = '4', 1, NULL) ) as Qtr4, COUNT( IF( ( t.fiscal_qtr = '1' OR t.fiscal_qtr = '2' OR t.fiscal_qtr = '3' OR t.fiscal_qtr = '4' ), 1, NULL ) ) as Total FROM (". $sqld .") AS t GROUP BY t.code";
        }

        $result = array(); 

        foreach ( $database->query( $sqlLayer2 ) as $row ) {
            array_push( $result, $row );
        }

        return $result;
    }

}

$wsModel = new WorkshopSurvey;


?>