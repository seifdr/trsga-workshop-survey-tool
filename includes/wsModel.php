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

    public function get_survey_totals_by_FY( $countFails = FALSE, $count45s = FALSE, $fy = NULL ){
        global $database;

        if( is_null( $fy ) ){
            $fy = $this->find_current_FY()['fy_year'];
        } 

        $sqlLayer1    = "SELECT workshopSurvey17.id, workshopSurvey17.question1a, workshopSurvey17.question1b, workshopSurvey17.question1c, workshopSurvey17.question1d, workshopSurvey17.fiscal_qtr, workshopSurvey17.fiscal_yr, workshopSurvey17.rep_code, users.FirstName, users.LastName, users.Code FROM workshopSurvey17 LEFT JOIN users ON workshopSurvey17.rep_code = users.Code WHERE workshopSurvey17.fiscal_yr = '". $fy ."' ";
            if( $countFails == TRUE ){
                $sqlLayer1 .= " AND workshopSurvey17.question1a <= 3 AND workshopSurvey17.question1b <= 3 AND workshopSurvey17.question1c <= 3 AND workshopSurvey17.question1d <= 3 ";
            }
        
        $sqlLayer1   .= "ORDER BY workshopSurvey17.rep_code ASC, STR_TO_DATE( CONCAT( SUBSTRING( workshopSurvey17.DLC, 3, 2 ), '-', SUBSTRING( workshopSurvey17.DLC, 1, 2 ), '-', workshopSurvey17.survey_yr ), '%d-%m-%Y' ) ASC";
        
        if( $count45s == TRUE ){
            $sqlLayer2    = "SELECT t.FirstName, t.LastName, t.Code, COUNT( IF( ( t.question1a = '4' AND t.question1b = '4' AND t.question1c = '4' and t.question1d = '4')  , 1, NULL) ) as All_4s, COUNT( IF( ( t.question1a = '5' AND t.question1b = '5' AND t.question1c = '5' and t.question1d = '5'), 1, NULL) ) as All_5s, COUNT( IF( ( t.question1a >= '4' AND t.question1b >= '4' AND t.question1c >= '4' and t.question1d = '4'), 1, NULL) ) as All_4s_5s FROM (". $sqlLayer1 .") AS t GROUP BY t.code";
        } else {
            $sqlLayer2    = "SELECT t.FirstName, t.LastName, t.fiscal_qtr, t.Code, COUNT( IF( t.fiscal_qtr = '1', 1, NULL) ) as Qtr1, COUNT( IF( t.fiscal_qtr = '2', 1, NULL) ) as Qtr2, COUNT( IF( t.fiscal_qtr = '3', 1, NULL) ) as Qtr3, COUNT( IF( t.fiscal_qtr = '4', 1, NULL) ) as Qtr4, COUNT(*) as Total FROM (". $sqlLayer1 .") AS t GROUP BY t.code";
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