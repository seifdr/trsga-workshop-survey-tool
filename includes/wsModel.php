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

    //for code
    private $fy;

    function __construct( $fy = NULL ) {
        $this->fy = $fy;
    }
 
    public function get_workshop_ranking(){
        global $database;

        $sql = "SELECT ROUND( AVG( question1d ), 2 ) as questDavg 
                    FROM workshopSurvey17 
                    WHERE fiscal_yr = '". $this->fy ."'
               ";

        $result = array(); 

        foreach ( $database->query( $sql ) as $row ) {
            array_push( $result, $row );
        }

        return $result;
        
    }

    public function get_survey_totals_by_FY( $type = NULL ){
        global $database;

        date_default_timezone_set('America/Chicago');

        $sqla = " SELECT ws.id, ws.question1a, ws.question1b, ws.question1c, ws.question1d, ws.fiscal_qtr, ws.fiscal_yr, ws.survey_yr, ws.DLC, ws.rep_code, u.FirstName, u.LastName, u.Code FROM workshopSurvey17 AS ws LEFT OUTER JOIN users AS u ON ws.rep_code = u.Code WHERE ws.fiscal_yr = '". $this->fy ."' ";

        $sqlb = " SELECT workshopSurvey17.id, IF( workshopSurvey17.question1a IS NULL, 0, workshopSurvey17.question1a ) AS question1a, IF( workshopSurvey17.question1b IS NULL, 0, workshopSurvey17.question1b ) AS question1b, IF( workshopSurvey17.question1c IS NULL, 0, workshopSurvey17.question1c ) AS question1c, IF( workshopSurvey17.question1d IS NULL, 0, workshopSurvey17.question1d ) AS question1d, workshopSurvey17.fiscal_qtr, IF( workshopSurvey17.fiscal_yr IS NULL, ". $this->fy .", workshopSurvey17.fiscal_yr ) AS fiscal_yr, IF( workshopSurvey17.survey_yr IS NULL, ". date('Y') .", workshopSurvey17.survey_yr ) AS survey_yr, IF( workshopSurvey17.DLC IS NULL, CONCAT( DATE_FORMAT( CURRENT_DATE(), '%m' ), DATE_FORMAT( CURRENT_DATE(), '%d' ) , 'XXXX' ), workshopSurvey17.DLC ) AS DLC, IF( workshopSurvey17.rep_code IS NULL, users.Code, workshopSurvey17.rep_code ) AS rep_code, users.FirstName, users.LastName, users.Code FROM workshopSurvey17 RIGHT OUTER JOIN users ON workshopSurvey17.rep_code = users.Code WHERE ( workshopSurvey17.fiscal_yr = '". $this->fy ."' OR workshopSurvey17.fiscal_yr IS NULL ) ";

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

        } elseif ( $type == 'satPers' ) {

           // echo $sqld;

            $x = " SELECT 
                    t2.FirstName,
                    t2.LastName,
                    SUM( IF( ( ( t2.question1a > 3 AND t2.question1b > 3 AND t2.question1c > 3 ) AND t2.fiscal_qtr = 1 ), 1, 0 ) ) AS Qtr1_pass_cnt,
                    SUM( IF( t2.fiscal_qtr = 1, 1, 0 ) ) as Qtr1_tot_cnt,
                    
                    SUM( IF( ( ( t2.question1a > 3 AND t2.question1b > 3 AND t2.question1c > 3 ) AND t2.fiscal_qtr = 2 ), 1, 0 ) ) AS Qtr2_pass_cnt,
                    SUM( IF( t2.fiscal_qtr = 2, 1, 0 ) ) as Qtr2_tot_cnt,
                    
                    SUM( IF( ( ( t2.question1a > 3 AND t2.question1b > 3 AND t2.question1c > 3 ) AND t2.fiscal_qtr = 3 ), 1, 0 ) ) AS Qtr3_pass_cnt,
                    SUM( IF( t2.fiscal_qtr = 3, 1, 0 ) ) as Qtr3_tot_cnt,
                    
                    SUM( IF( ( ( t2.question1a > 3 AND t2.question1b > 3 AND t2.question1c > 3 ) AND t2.fiscal_qtr = 4 ), 1, 0 ) ) AS Qtr4_pass_cnt,
                    SUM( IF( t2.fiscal_qtr = 4, 1, 0 ) ) as Qtr4_tot_cnt,    

                    SUM( IF( ( t2.question1a > 3 AND t2.question1b > 3 AND t2.question1c > 3 ), 1, 0 ) ) AS Tot_pass_cnt,
                    SUM( IF( t2.fiscal_qtr IS NOT NULL, 1, 0 ) ) as Tot_cnt  
                    
                    FROM ( ". $sqld ." ) as t2 GROUP BY t2.code";

           $y = " SELECT 
                    FirstName,
                    LastName,
                    ROUND( ( ( t3.Qtr1_pass_cnt / t3.Qtr1_tot_cnt ) * 100 ), 2 ) AS Qtr1_Perc,

                    ROUND( ( ( t3.Qtr2_pass_cnt / t3.Qtr2_tot_cnt ) * 100 ), 2 ) AS Qtr2_Perc,

                    ROUND( ( ( t3.Qtr3_pass_cnt / t3.Qtr3_tot_cnt ) * 100 ), 2 ) AS Qtr3_Perc,

                    ROUND( ( ( t3.Qtr4_pass_cnt / t3.Qtr4_tot_cnt ) * 100 ), 2 ) AS Qtr4_Perc,

                    ROUND( ( ( t3.Tot_pass_cnt / t3.Tot_cnt ) * 100 ), 2 ) AS Tot_Perc

                    FROM ( ". $x ." ) as t3";

            $sqlLayer2 = $y;

        } else {
            //catch all
            $sqlLayer2    = "SELECT t.FirstName, t.LastName, t.fiscal_qtr, t.Code, COUNT( IF( t.fiscal_qtr = '1', 1, NULL) ) as Qtr1, COUNT( IF( t.fiscal_qtr = '2', 1, NULL) ) as Qtr2, COUNT( IF( t.fiscal_qtr = '3', 1, NULL) ) as Qtr3, COUNT( IF( t.fiscal_qtr = '4', 1, NULL) ) as Qtr4, COUNT( IF( ( t.fiscal_qtr = '1' OR t.fiscal_qtr = '2' OR t.fiscal_qtr = '3' OR t.fiscal_qtr = '4' ), 1, NULL ) ) as Total FROM (". $sqld .") AS t GROUP BY t.code";
        }

        $result = array(); 

        foreach ( $database->query( $sqlLayer2 ) as $row ) {
            array_push( $result, $row );
        }

        if( $type == 'satPers' ){
            //look( $result );
        }

        return $result;
    }

    public function get_past_attendance(){
        global $database;

        $sql = " SELECT 
                    ROUND( SUM( 
                        IF( question2 LIKE '%1%', 1, 0 )
                    ) / COUNT(*) * 100, 2 ) as 'First TRS Event',

                    SUM( IF( question2 LIKE '%1%', 1, 0 ) ) as 'First TRS Event Count',

                    ROUND( SUM( 
                        IF( question2 LIKE '%2%', 1, 0 )
                    ) / COUNT(*) * 100, 2 ) as 'One-on-one Couseling',

                    SUM( IF( question2 LIKE '%2%', 1, 0 ) ) as 'One-on-one Couseling Count',


                    ROUND( SUM( 
                        IF( question2 LIKE '%3%', 1, 0 )
                    ) / COUNT(*) * 100, 2 ) as 'Half-Day Seminar',
                    
                    SUM( IF( question2 LIKE '%3%', 1, 0 ) ) as 'Half-Day Seminar Count',
                    
                    ROUND( SUM( 
                        IF( question2 LIKE '%4%', 1, 0 )
                    ) / COUNT(*) * 100, 2 ) as 'Pre-Retirement Workshop',
                    
                    SUM( IF( question2 LIKE '%4%', 1, 0 ) ) as 'Pre-Retirement Workshop Count',
                    
                    ROUND( SUM( 
                        IF( question2 LIKE '%5%', 1, 0 )
                    ) / COUNT(*) * 100, 2 ) as 'Mid-Career Workshop',
                    
                    SUM( IF( question2 LIKE '%5%', 1, 0 ) ) as 'Mid-Career Workshop Count',
                    
                    ROUND( SUM( 
                        IF( question2 LIKE '%6%', 1, 0 )
                    ) / COUNT(*) * 100, 2 ) as 'New Hire Workshop',
                    
                    SUM( IF( question2 LIKE '%6%', 1, 0 ) ) as 'New Hire Workshop Count'
                    
                    FROM ". static::$table_name ."
                    WHERE fiscal_yr = ". $this->fy ." ";

        
        $result = array(); 

        foreach ( $database->query( $sql ) as $row ) {
            array_push( $result, $row );
        }

        return $result;
        
    }

    public function get_understandings(){
        global $database;

        $sql = " SELECT ROUND( AVG( question3a ), 2) as 'Eligibility Requirements', 
                        ROUND( AVG( question3b ), 2) as 'Plans of Retiremnet/Options' ,
                        ROUND( AVG( question3c ), 2) as 'Beneficiary Information',
                        ROUND( AVG( question3d ), 2) as 'Service Credit'
                    FROM ". static::$table_name ."
                    WHERE fiscal_yr = ". $this->fy ." ";

        
        $result = array(); 

        foreach ( $database->query( $sql ) as $row ) {
            array_push( $result, $row );
        }

        return $result;
        
    }

    public function get_knowledge_useful(){
        global $database;

        $sql = " SELECT SUM( IF( question4 = 1, 1, 0 ) ) as Yes,
                        SUM( IF( question4 != 1, 1, 0 ) ) as No,
                        ROUND( ( SUM( IF( question4 = 1, 1, 0 ) ) / ( COUNT(*) ) ), 1) * 100  as YesPerc
                    FROM ". static::$table_name ."
                    WHERE fiscal_yr = ". $this->fy ." ";

        $result = array(); 

        foreach ( $database->query( $sql ) as $row ) {
            array_push( $result, $row );
        }

        return $result;
    }

}

//$wsModel = new WorkshopSurvey();


?>