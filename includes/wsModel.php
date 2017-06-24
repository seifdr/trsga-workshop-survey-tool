<?php 

/**
 * 
 */
class WorkshopSurvey extends DatabaseObject
{

    protected static $table_name = "workshopSurvey17";	
	protected static $db_fields  = array('id','respondentID', 'question1a', 'question1b', 'question1c', 'question1d', 'question2', 'question3a', 'question3b', 'question3c', 'question3d', 'question4', 'question5', 'question6', 'DLC', 'rep_code', 'survey_month', 'survey_month_num', 'survey_yr', 'fiscal_qtr', 'fiscal_yr', 'name', 'location', 'type', 'removed', 'FirstName', 'LastName');
	public static $month_array = array(1 => "January", 2 => "February", 3 => "March", 4 => "April", 5 => "May", 6 => "June", 7 => "July", 8 => "August", 9 => "September", 10 => "October", 11 => "November", 12 => "December"); 

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
    public $type;
    public $workshopType;
    public $removed;

    //counselor info
    public $rep_code;
    public $FirstName;
    public $LastName;

    //for code
    public $params;

    public $counselorCode;
    public $monthNumber;
    public $year;
    public $fy;
    public $fq;
    //for pagination
    public $offset;
    public $block;
    public $paginationObj;


    public $currentMonth;
    public $currentYear;

    public $workshopTypes = array( 'This is my first TRS event', 'One-on-one Counseling', 'Half-Day Seminar', 'Pre-Retirement Workshop', 'Mid-Career Workshop', 'New Hire Workshop' );

    public $typesKey = array(
        0 => 'Half-Day Seminar',
        1 => 'Pre-Retirement Workshop',
        2 => 'Mid-Career Workshop',
        3 => 'New Hire Workshop'
    );

    public $colorArr = array(
            '3366cc',
            'dc3912',
            'ff9900',
            '109618',
            '990099'
        );

    function __construct() {
        
        $this->trsgaTime = new trsgaTime();

        $this->currentMonth = $this->find_current_month(TRUE);
        $this->currentYear  = $this->find_year();
        $this->fy = $this->find_current_FY()['fy_year'];

    }

    private function loadParams( $params ){
        $allowedKeys = array( 'counselorCode', 'monthNumber', 'year', 'fy', 'fq', 'offset', 'block' );

        foreach ($params as $key => $value) {
            if(  in_array( $key, $allowedKeys ) ){
                $this->$key = $value;
                $this->params[$key] = $value;
            }
        }

        $this->currentMonth = $this->find_current_month(TRUE);
        $this->currentYear  = $this->find_year();

        if( empty( $this->fy ) ){
            $this->fy = $this->find_current_FY()['fy_year'];
        }
    }

    public function sanitizeAndLoadParams( $params ){
        $this->loadParams( $params );
    }
 
    public function get_workshop_ranking(){
        global $database;

        $sql = "SELECT ROUND( AVG( question1d ), 2 ) as questDavg 
                    FROM workshopSurvey17 
                    WHERE fiscal_yr = '". $this->fy ." AND removed != 1'
               ";

        $result = array(); 

        foreach ( $database->query( $sql ) as $row ) {
            array_push( $result, $row );
        }

        return $result;
        
    }

    public function getNumberPercentages(){
        global $database;

        $sql = "SELECT COUNT(*) as tot, SUM( 
                        IF( ( ws.question1a = 4 AND ws.question1b = 4 AND  ws.question1c = 4 ), 1, 0 ) 
                       ) AS 'all4s',
                       SUM( 
                        IF( (  ws.question1a = 5 AND  ws.question1b = 5 AND  ws.question1c = 5 ), 1, 0 ) 
                       ) AS 'all5s',
                       SUM(
                           IF( (  ws.question1a > 3 AND  ws.question1b > 3 AND  ws.question1c > 3 ), 1, 0 ) 
                          ) AS 'all45s'    
                      FROM ". static::$table_name ." AS ws ";

                      $sql = $this->addWhereCatsToSql( $sql );

        $sql = "SELECT *, 
            ( t2.all45s - ( t2.all5s + t2.all4s ) ) as 'allu45s',
            ROUND( ( t2.all4s / t2.tot ) * 100, 2 ) as '4perc',
            ROUND( ( t2.all5s / t2.tot ) * 100, 2 ) as '5perc',
            ROUND( ( t2.all45s / t2.tot ) * 100, 2 ) as '45perc'
            FROM ( ". $sql .") as t2 ";

        $sql = "SELECT *, ROUND( ( t3.allu45s / t3.tot ) * 100, 2 ) as 'u45perc' FROM (". $sql .") AS t3";

        $result = array(); 

        foreach ( $database->query( $sql ) as $row ) {
            array_push( $result, $row );
        }

        return $result;

    }

    public function get_survey_by_type(){
        global $database;

        $sql = "SELECT 
             COUNT( IF( ( ws.type = '0' ), 1, NULL) ) as 'Half-Day Seminar',
             COUNT( IF( ( ws.type = '1' ), 1, NULL) ) as 'Pre-Retirement Workshop',
             COUNT( IF( ( ws.type = '2' ), 1, NULL) ) as 'Mid-Career Workshop',
             COUNT( IF( ( ws.type = '3' ), 1, NULL) ) as 'New Hire Workshop'
             FROM ". static::$table_name ." AS ws ";

        $result = array(); 

        foreach ( $database->query( $sql ) as $row ) {
            array_push( $result, $row );
        }

        return $result;
        
    }

    public function get_survey_totals_by_FY( $type = NULL ){
        global $database;

        date_default_timezone_set('America/Chicago');

        $sqla = " SELECT ws.id, ws.question1a, ws.question1b, ws.question1c, ws.question1d, ws.fiscal_qtr, ws.fiscal_yr, ws.survey_yr, ws.DLC, ws.rep_code, ws.removed, u.FirstName, u.LastName, u.surveyID 
                    FROM workshopSurvey17 AS ws LEFT OUTER JOIN users AS u ON ws.rep_code = u.surveyID 
                        WHERE ws.removed != '1' ";

                        if( !empty( $this->counselorCode ) ){
                            $sqla .= " AND ws.rep_code = '". $this->counselorCode ."' ";
                        }

                        if( !empty( $this->monthNumber ) ){
                            $sqla .= " AND ws.survey_month_num = '". $this->monthNumber ."' ";
                        }

                        if( !empty( $this->year ) ){
                            $sqla .= " AND ws.survey_yr = '". $this->year ."' ";
                        }

                        if( !empty( $this->fy ) ){
                            $sqla .= " AND ws.fiscal_yr = '". $this->fy ."' ";
                        }
                        
                        if( !empty( $this->fq ) ){
                            $sqla .= " AND ws.fiscal_qtr = '". $this->fq ."' ";
                        }

        $sqlb = " SELECT workshopSurvey17.id, 
                    IF( workshopSurvey17.question1a IS NULL, 0, workshopSurvey17.question1a ) AS question1a, 
                    IF( workshopSurvey17.question1b IS NULL, 0, workshopSurvey17.question1b ) AS question1b, 
                    IF( workshopSurvey17.question1c IS NULL, 0, workshopSurvey17.question1c ) AS question1c, 
                    IF( workshopSurvey17.question1d IS NULL, 0, workshopSurvey17.question1d ) AS question1d, 
                    workshopSurvey17.fiscal_qtr, 
                    IF( workshopSurvey17.fiscal_yr IS NULL, ". $this->fy .", workshopSurvey17.fiscal_yr ) AS fiscal_yr, 
                    IF( workshopSurvey17.survey_yr IS NULL, ". date('Y') .", workshopSurvey17.survey_yr ) AS survey_yr, 
                    IF( workshopSurvey17.DLC IS NULL, CONCAT( DATE_FORMAT( CURRENT_DATE(), '%m' ), DATE_FORMAT( CURRENT_DATE(), '%d' ) , 'XXXX' ), workshopSurvey17.DLC ) AS DLC, 
                    IF( workshopSurvey17.rep_code IS NULL, users.surveyID, workshopSurvey17.rep_code ) AS rep_code, 
                    workshopSurvey17.removed,
                    users.FirstName, 
                    users.LastName, 
                    users.surveyID 
                        FROM workshopSurvey17 RIGHT OUTER JOIN users ON workshopSurvey17.rep_code = users.surveyID 
                            WHERE ( workshopSurvey17.fiscal_yr = '". $this->fy ."' 
                                OR workshopSurvey17.fiscal_yr IS NULL ) 
                                    AND ( workshopSurvey17.removed != 1 || workshopSurvey17.removed IS NULL ) 
                                    AND users.active = 1";

                                     if( !empty( $this->counselorCode ) ){
                                        $sqlb .= " AND workshopSurvey17.rep_code = '". $this->counselorCode ."' ";
                                    }

        $sqlc = $sqla . " UNION " . $sqlb;

        $sqld = "SELECT *, ( STR_TO_DATE( CONCAT( SUBSTRING( t.DLC, 3, 2 ), '-', SUBSTRING( t.DLC, 1, 2 ), '-', ( IF( t.survey_yr IS NULL, DATE_FORMAT( CURRENT_DATE(), '%Y' ),  t.survey_yr ) ) ), '%d-%m-%Y' ) ) AS session_date FROM (". $sqlc .") AS t ORDER BY t.rep_code ASC, session_date ASC";

        if( $type == 'countFails' ){

            $sqle  = " SELECT *, IF( ( ( t2.question1a <= 3 AND t2.question1a != 0 ) AND ( t2.question1b <= 3 AND t2.question1b != 0 ) AND ( t2.question1c <= 3 AND t2.question1c != 0 ) ), '1', '0' ) AS fail FROM (". $sqld .") as t2 ";
            
            $sqlLayer2  = "SELECT t.FirstName, t.LastName, t.surveyID, 
                COUNT( IF( ( t.fiscal_qtr = '1' AND t.fail = '1' ), 1, NULL) ) as Qtr1, 
                COUNT( IF( ( t.fiscal_qtr = '2' AND t.fail = '1' ), 1, NULL) ) as Qtr2, 
                COUNT( IF( ( t.fiscal_qtr = '3' AND t.fail = '1' ), 1, NULL) ) as Qtr3, 
                COUNT( IF( ( t.fiscal_qtr = '4' AND t.fail = '1' ), 1, NULL) ) as Qtr4, 
                SUM( Fail ) as Total FROM (". $sqle .") AS t GROUP BY t.FirstName, t.LastName, t.surveyID";
    
        } elseif ( $type == 'count45s' ){

            $sqlLayer2    = "SELECT t.FirstName, t.LastName, t.surveyID, COUNT( IF( ( t.question1a = '4' AND t.question1b = '4' AND t.question1c = '4' )  , 1, NULL) ) as All_4s, COUNT( IF( ( t.question1a = '5' AND t.question1b = '5' AND t.question1c = '5' ), 1, NULL) ) as All_5s, COUNT( IF( ( t.question1a >= '4' AND t.question1b >= '4' AND t.question1c >= '4' ), 1, NULL) ) as All_4s_5s FROM (". $sqld .") AS t GROUP BY t.FirstName, t.LastName, t.surveyID";
        
        } elseif ( $type == 'fyavgs' ){

            $sqlLayer2 = "SELECT FirstName, LastName, ROUND( AVG( question1a ), 2 ) AS q1a_avg, ROUND( AVG( question1b ), 2 ) AS q1b_avg, ROUND( AVG( question1c ), 2 ) q1c_avg, ROUND( AVG( ( question1a + question1b + question1c ) / 3 ), 2 ) AS total_avg FROM ( ". $sqld ." ) as t2 GROUP BY FirstName, LastName, rep_code";

        } elseif ( $type == 'satPers' ) {

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
                    
                    FROM ( ". $sqld ." ) as t2 GROUP BY t2.firstName, t2.LastName, t2.surveyId";

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
            //$sqlLayer2    = "SELECT t.id, t.FirstName, t.LastName, t.fiscal_qtr, t.surveyID, COUNT( IF( t.fiscal_qtr = '1', 1, NULL) ) as Qtr1, COUNT( IF( t.fiscal_qtr = '2', 1, NULL) ) as Qtr2, COUNT( IF( t.fiscal_qtr = '3', 1, NULL) ) as Qtr3, COUNT( IF( t.fiscal_qtr = '4', 1, NULL) ) as Qtr4, COUNT( IF( ( t.fiscal_qtr = '1' OR t.fiscal_qtr = '2' OR t.fiscal_qtr = '3' OR t.fiscal_qtr = '4' ), 1, NULL ) ) as Total FROM (". $sqld .") AS t GROUP BY t.surveyID";
            $sqlLayer2    = "SELECT t.FirstName, t.LastName, t.surveyID, COUNT( IF( t.fiscal_qtr = '1', 1, NULL) ) as Qtr1, COUNT( IF( t.fiscal_qtr = '2', 1, NULL) ) as Qtr2, COUNT( IF( t.fiscal_qtr = '3', 1, NULL) ) as Qtr3, COUNT( IF( t.fiscal_qtr = '4', 1, NULL) ) as Qtr4, COUNT( IF( ( t.fiscal_qtr = '1' OR t.fiscal_qtr = '2' OR t.fiscal_qtr = '3' OR t.fiscal_qtr = '4' ), 1, NULL ) ) as Total FROM (". $sqld .") AS t GROUP BY  t.FirstName, t.LastName, t.surveyID";
        }

        $result = array(); 

        foreach ( $database->query( $sqlLayer2 ) as $row ) {
            array_push( $result, $row );
        }

        return $result;
    }

    public function get_past_six_mon_sat_percentages(){
        global $database;

        $last_six = $this->find_previous_six_months();

        $yearsToInclude     = array();
        $yearsToIncludeTxt  = "";

        foreach ( $last_six as $row ) {
            $yrNum = substr( $row, -4, 4 );
            if( !in_array( $yrNum, $yearsToInclude ) ){

                if( count( $yearsToInclude ) > 0 ){
                    $yearsToIncludeTxt .= " OR ";
                }

                $yearsToIncludeTxt .= " survey_yr='" . $yrNum . "'";

                array_push( $yearsToInclude, $yrNum ); 
            }
        }

        $sql    = "SELECT id, question1a, question1b, question1c, question1d, survey_month_num, survey_yr, type FROM ". static::$table_name ." WHERE " . $yearsToIncludeTxt;

        $sql1   = " SELECT 
                        SUM( IF( ( t1.question1a > 3 AND t1.question1b > 3 AND t1.question1c > 3 ), 1, 0 ) ) AS passes,
                        SUM( IF( ( t1.question1a <= 3 OR t1.question1b <= 3 OR t1.question1c <= 3 ), 1, 0 ) ) AS fails,
                        COUNT(*) as total,

                        SUM( IF( ( t1.question1a > 3 AND t1.question1b > 3 AND t1.question1c > 3 AND t1.type = 0 ), 1, 0 ) ) AS passesHalfDay,
                        SUM( IF( ( t1.question1a <= 3 OR t1.question1b <= 3 OR t1.question1c <= 3 AND t1.type = 0 ), 1, 0 ) ) AS failsHalfDay,
                        SUM( IF( t1.type = 0, 1, 0 ) ) as totalHalfDay,

                        SUM( IF( ( t1.question1a > 3 AND t1.question1b > 3 AND t1.question1c > 3 AND t1.type = 1 ), 1, 0 ) ) AS passesPreRet,
                        SUM( IF( ( t1.question1a <= 3 OR t1.question1b <= 3 OR t1.question1c <= 3 AND t1.type = 1 ), 1, 0 ) ) AS failsPreRet,
                        SUM( IF( t1.type = 1, 1, 0 ) ) as totalPreRet,

                        SUM( IF( ( t1.question1a > 3 AND t1.question1b > 3 AND t1.question1c > 3 AND t1.type = 2 ), 1, 0 ) ) AS passesMidCar,
                        SUM( IF( ( t1.question1a <= 3 OR t1.question1b <= 3 OR t1.question1c <= 3 AND t1.type = 2 ), 1, 0 ) ) AS failsMidCar,
                        SUM( IF( t1.type = 2, 1, 0 ) ) as totalMidCar,

                        SUM( IF( ( t1.question1a > 3 AND t1.question1b > 3 AND t1.question1c > 3 AND t1.type = 3 ), 1, 0 ) ) AS passesNewHire,
                        SUM( IF( ( t1.question1a <= 3 OR t1.question1b <= 3 OR t1.question1c <= 3 AND t1.type = 3 ), 1, 0 ) ) AS failsNewHire,
                        SUM( IF( t1.type = 3, 1, 0 ) ) as totalNewHire,

                        survey_month_num, 
                        survey_yr FROM ( ". $sql ." ) as t1 
                            GROUP BY survey_month_num, survey_yr
                            ORDER BY survey_yr ASC, survey_month_num ASC";

        $result = array(); 

        foreach ( $database->query( $sql1 ) as $row ) {
            array_push( $result, $row );
        }

        $modifiedResult = array();

        for ($i=0; $i < count($result) ; $i++) { 
            $row = $result[$i];
            $modifiedResult[$i]['percentage']       = ( ( $row['passes'] > 0 && $row['total'] > 0 ) )? $row['passes'] / $row['total'] * 100  : 0;

            $modifiedResult[$i]['hdPercentage']       = ( ( $row['passesHalfDay'] > 0 && $row['totalHalfDay'] > 0 ) )? $row['passesHalfDay'] / $row['totalHalfDay'] * 100  : 0;

            $modifiedResult[$i]['prPercentage']       = ( ( $row['passesPreRet'] > 0 && $row['totalPreRet'] > 0 ) )? $row['passesPreRet'] / $row['totalPreRet'] * 100  : 0;

            $modifiedResult[$i]['mcPercentage']       = ( ( $row['passesMidCar'] > 0 && $row['totalMidCar'] > 0 ) )? $row['passesMidCar'] / $row['totalMidCar'] * 100  : 0;

            $modifiedResult[$i]['nhPercentage']       = ( ( $row['passesNewHire'] > 0 && $row['totalNewHire'] > 0 ) )? $row['passesNewHire'] / $row['totalNewHire'] * 100  : 0;

            $modifiedResult[$i]['monthNum']         = $row['survey_month_num'];
            $modifiedResult[$i]['monthName']        = self::$month_array[ $row['survey_month_num'] ];
            $modifiedResult[$i]['monthYear']        = $row['survey_yr'];
        }

        return $modifiedResult;
            
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
                    
                    FROM ". static::$table_name ." AS ws ";

                     $sql = $this->addWhereCatsToSql( $sql );

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
                    FROM ". static::$table_name ." AS ws ";

       $sql = $this->addWhereCatsToSql( $sql );

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
                        SUBSTRING( ROUND( ( SUM( IF( question4 = 1, 1, 0 ) ) / ( COUNT(*) ) ), 3) * 100, 1, 4 )  as YesPerc,
                        count(*) as test
                    FROM ". static::$table_name . " as WS ";

        $sql = $this->addWhereCatsToSql( $sql );

        $result = array(); 

        foreach ( $database->query( $sql ) as $row ) {
            array_push( $result, $row );
        }

        return $result;
    }

    private function addWhereCatsToSql( $sql ){
        //table previous must be aliased as ws
         $whereCnt = 0;

        $sql .= " WHERE ws.removed != '1' "; 
        $whereCnt = 1;

        if( !empty( $this->counselorCode ) ){
            $sql .= ( $whereCnt > 0 )? " AND " : " WHERE ";
            $sql .= " ws.rep_code = '". $this->counselorCode ."' ";
            $whereCnt++;
        }

        if( !empty( $this->monthNumber ) ){
            $sql .= ( $whereCnt > 0 )? " AND " : " WHERE ";
            $sql .= " ws.survey_month_num = '". $this->monthNumber ."' ";
            $whereCnt++;
        }

        if( !empty( $this->year ) ){
            $sql .= ( $whereCnt > 0 )? " AND " : " WHERE ";
            $sql .= " ws.survey_yr = '". $this->year ."' ";
            $whereCnt++;
        }

        if( !empty( $this->fy ) ){
            $sql .= ( $whereCnt > 0 )? " AND " : " WHERE ";
            $sql .= " ws.fiscal_yr = '". $this->fy ."' ";
            $whereCnt++;
        }
        
        if( !empty( $this->fq ) ){
            $sql .= ( $whereCnt > 0 )? " AND " : " WHERE ";
            $sql .= " ws.fiscal_qtr = '". $this->fq ."' ";
            $whereCnt++;
        }

        return $sql;
    }

    
    // SURVEY REPORT FUNCTIONS
	public function find_current_month($number = FALSE){
		$date =  getdate();
        return ( $number )? $date['mon'] : $date['month'];
	}
		
	public function find_previous_month(){
		$date =  getdate();
		$month_num = $date['mon'];
		
		// $previous_month_num = $month_num - 1;
		
		if($month_num ==1){
			$previous_month_num = 12;
		} else {
			$previous_month_num = $month_num - 1;
		}
		
		$month_array = array(1 => "January", 2 => "February", 3 => "March", 4 => "April", 5 => "May", 6 => "June", 7 => "July", 8 => "August", 9 => "September", 10 => "October", 11 => "November", 12 => "December");
		$previous_month = $month_array[$previous_month_num];
		
		return $previous_month; // Displays the current month
	}	
	
	public function find_current_FY(){

        $month_num = $this->currentMonth;
		$year = $this->currentYear;

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
	
	public function find_year($offset=false){
			$date =  getdate();
			$month = $date['mon'];
			$year = $date['year'];
		
		if(($offset == true)&&($month == 1)){
			//Offset compensates for pulling previous month on page load. Ex. December 2012 surveys will be posted in January 2013. While the previous
			// month will be selected fine, the current year needs to be decremented 1 year to compensate for the new year. 
			$decremented_year = $year - 1;
			return $decremented_year;
		} else {
			return $year;	
		}
	}

	public function find_FY(){
	
		$month = $this->trsgaTime->format('m');
		
		$year = $this->trsgaTime->format('Y');
			
		if($month >= 7){
			//Offset compensates for pulling previous month on page load. Ex. December 2012 surveys will be posted in January 2013. While the previous
			// month will be selected fine, the current year needs to be decremented 1 year to compensate for the new year. 
			$incremented_year = $year + 1;
			return $incremented_year;
		} else {
			return $year;	
		}			
	}

	public function find_all_fiscal_years(){
		//returns an array of all fiscal years since we started surveying in 2012 		
		$start_FY = 2016;
		$current_FY = $this->find_FY();
		
		$all_fys = array();
		
		while ($start_FY <= $current_FY) {
			array_push($all_fys, $start_FY);
			$start_FY++;
		}
		
		return $all_fys;
	}

    public function find_all_years(){
		//returns an array of all years since we started surveying in 2012
		$start_yr = 2016;
		$current_yr = $this->find_year();
		
		$all_yrs = array();
		
		while ($start_yr <= $current_yr) {
			array_push($all_yrs, $start_yr);
			$start_yr++;
		}
		
		return $all_yrs;
	}

    // SURVEY REPORT FUNCTIONS

    public function get_survey_report_header_numbers(){
        global $database;

        $sql = " SELECT 

                    COUNT(*) as TotalCount,

                    SUM( IF( ( ws.question1a <= 3 OR ws.question1b <= 3 OR ws.question1c <= 3 ) , 1, 0) ) AS Fails,

                    SUM( IF( 
                        ws.question1a = 4 AND ws.question1b = 4 AND ws.question1c = 4, 1, 0
                    ) ) AS all4s,

                    SUM( IF( 
                        ws.question1a = 5 AND ws.question1b = 5 AND ws.question1c = 5, 1, 0
                    ) ) AS all5s,

                    SUM( IF( 
                        ( ws.question1a = 4 OR ws.question1a = 5 ) AND 
                        ( ws.question1b = 4 OR ws.question1b = 5 ) AND 
                        ( ws.question1c = 4 OR ws.question1c = 5 ), 1, 0
                    ) ) AS all45s,

                    ROUND( AVG( 
                        ws.question1d
                    ), 2 ) AS avgScore

                    FROM workshopSurvey17 AS ws ";

                    $whereCnt = 0;

                    $sql .= " WHERE ws.removed != '1' ";
                    $whereCnt = 1;

                    if( !empty( $this->counselorCode ) ){
                        $sql .= ( $whereCnt > 0 )? " AND " : " WHERE ";
                        $sql .= " WHERE ws.rep_code = '". $this->counselorCode ."' ";
                        $whereCnt++;
                    }

                    if( !empty( $this->monthNumber ) ){
                        $sql .= ( $whereCnt > 0 )? " AND " : " WHERE ";
                        $sql .= " ws.survey_month_num = '". $this->monthNumber ."' ";
                        $whereCnt++;
                    }

                    if( !empty( $this->year ) ){
                        $sql .= ( $whereCnt > 0 )? " AND " : " WHERE ";
                        $sql .= " ws.survey_yr = '". $this->year ."' ";
                        $whereCnt++;
                    }

                    if( !empty( $this->fy ) ){
                        $sql .= ( $whereCnt > 0 )? " AND " : " WHERE ";
                        $sql .= " ws.fiscal_yr = '". $this->fy ."' ";
                        $whereCnt++;
                    }
                    
                    if( !empty( $this->fq ) ){
                        $sql .= ( $whereCnt > 0 )? " AND " : " WHERE ";
                        $sql .= " ws.fiscal_qtr = '". $this->fq ."' ";
                        $whereCnt++;
                    }
    
       $sqla = " SELECT *, ROUND( ( ( ( t1.TotalCount - t1.Fails ) / t1.TotalCount ) * 100 ), 2 ) AS surveySatPercentage FROM (". $sql .") AS t1";

        $result = array(); 

        foreach ( $database->query( $sqla ) as $row ) {
            array_push( $result, $row );
        }
        return $result[0];

    }

    public function survey_report( $avgs = FALSE, $singleSurvey = FALSE, $csv = FALSE ){
        global $database;

        $avgs = ( $avgs == TRUE )? (bool) TRUE : (bool) FALSE;

        date_default_timezone_set('America/Chicago');

        $sqla = " SELECT ";

        if( !$avgs ){
            $sqla .= "  ws.id, 
                        CONCAT( SUBSTRING( ws.DLC, 1, 2 ), '/', SUBSTRING( ws.DLC, 3, 2), '/', SUBSTRING( ws.survey_yr, 3, 2 ) ) AS Date,
                        ws.location,
                        CONCAT( u.FirstName, ' ', u.LastName ) AS Counselor, 
                        ws.question1a AS Knowledgable, 
                        ws.question1b AS Effective, 
                        ws.question1c AS Organized, 
                        ws.question1d AS Overall ";

                        if( $csv ){ 
                            $sqla .= ", ws.question2 AS Q2_PastAttend,
                            ws.question3a AS Q3_Eligibility,
                            ws.question3b AS Q3_Plans,
                            ws.question3c AS Q3_Beneficiary,
                            ws.question3d AS Q3_Service_Credit,
                            IF( ws.question4 = 1, 'Yes', 'No' ) AS Q4_Useful,
                            ws.question5 AS Q5_OtherTopics,
                            ws.question6 AS Q6_MostValuable ";
                        }
        } else {
            $sqla .= "  ROUND( AVG( ws.question1a ), 2 ) AS Knowledgable, 
                        ROUND( AVG( ws.question1b ), 2 ) AS Effective, 
                        ROUND( AVG( ws.question1c ), 2 ) AS Organized, 
                        ROUND( AVG( ws.question1d ), 2 ) AS Overall";

                         if( $csv ){ 
                            $sqla .= ", ROUND( AVG( ws.question3a ), 2 ) AS Eligibility, 
                            ROUND( AVG( ws.question3b ), 2 ) AS Plans, 
                            ROUND( AVG( ws.question3c ), 2 ) AS Beneficiary, 
                            ROUND( AVG( ws.question3d ), 2 ) AS ServiceCredit";
                         }
        }

        if( !$avgs ){
            $sqla .= "  , IF( ( ws.question1a <= 3 OR ws.question1b <= 3 OR ws.question1c <= 3 ) , 1, 0) AS Failed ";
        }

        $sqla .= "  FROM workshopSurvey17 AS ws JOIN users AS u ON ws.rep_code = u.surveyID WHERE removed != 1 ";

                        if( $singleSurvey ){
                            $sqla .= " AND ws.id='". $this->id ."' ";
                        }

                        if( !empty( $this->counselorCode ) ){
                            $sqla .= " AND ws.rep_code = '". $this->counselorCode ."' ";
                        }

                        if( !empty( $this->monthNumber ) ){
                            $sqla .= " AND ws.survey_month_num = '". $this->monthNumber ."' ";
                        }

                        if( !empty( $this->year ) ){
                            $sqla .= " AND ws.survey_yr = '". $this->year ."' ";
                        }

                        if( !empty( $this->fy ) && ( $singleSurvey == FALSE ) ){
                            $sqla .= " AND ws.fiscal_yr = '". $this->fy ."' ";
                        }
                        
                        if( !empty( $this->fq ) ){
                            $sqla .= " AND ws.fiscal_qtr = '". $this->fq ."' ";
                        }

                        //ORDER BY DATE 
                        if( !$avgs ){
                            $sqla .= " ORDER BY Date DESC ";
                        }

                        if( !empty( $this->offset ) && ($this->offset != "all") && !$avgs && !$csv ) {

                            //Pagination 
                            //1. the current page number ($current_page)
                            $page = !empty( $this->block ) ? (int)$this->block : 1;
                            
                            //2. record per page ($per_page)
                            $per_page = $this->offset;

                            //3. total record count ($total_count)
                            // $total_count = outreachSurveys::count_all();
                            $countSQL = "SELECT count(*) as total_count FROM (". $sqla .") AS t";

                            $total_count = mysqli_fetch_object( $database->query( $countSQL ) )->total_count;

                            $this->paginationObj = new WsPagination($page, $per_page, $total_count, $this);
                        }
    
                        if( !empty( $this->offset ) && $this->offset != "all" && !$avgs && !$csv ){
                            //Add Pagination SQL
                            $sqla .= " LIMIT {$this->paginationObj->per_page} OFFSET {$this->paginationObj->offset()}";
                        }
              
        $result = array(); 

        foreach ( $database->query( $sqla ) as $row ) {
            array_push( $result, $row );
        }

        return $result;

    }

    public function get_suvery_report(){
       $resultBody      = $this->survey_report(FALSE);
       $resultBottom    = $this->survey_report(TRUE);

       return array( $resultBody, $resultBottom );
    }

    public function get_single_survey(){
        global $database;
        $sql = " SELECT *, CONCAT( SUBSTRING( ws.DLC, 1, 2 ), '/', SUBSTRING( ws.DLC, 3, 2), '/', SUBSTRING( ws.survey_yr, 3, 2 ) ) AS Date FROM workshopSurvey17 AS ws JOIN users AS u ON ws.rep_code = u.surveyID WHERE ws.id = ". $this->id;
        return $database->fetch_array( ( $database->query( $sql ) ) );

    }

    public function deleteSurvey(){
        //actually just marking it as removed
        global $database;
        $sql = "UPDATE workshopSurvey17 SET removed = 1 WHERE id='". $database->escape_values( $this->id ) ."' AND removed != 1";
        $database->query( $sql );
        return($database->affected_rows()==1) ? true : false;
        //return $database->fetch_array( $database->query( $sql ) );
    }

    // get data for CSV
    public function get_csv_report(){
                           //survey_report( $avgs = FALSE, $singleSurvey = FALSE, $csv = FALSE ){
        $avgsData   = $this->survey_report( TRUE, FALSE, TRUE );
        $data       = $this->survey_report( FALSE, FALSE, TRUE );
        $counts     = $this->get_survey_report_header_numbers();

        $attresult      = $this->get_past_attendance();
        $underResult    = $this->get_understandings();
        $krResult       = $this->get_knowledge_useful();
        $stResult       = $this->get_survey_by_type();

        return array( 'Avgs' => $avgsData, 'Data' => $data, 'Counts' => $counts, 'attResult' => $attresult[0], 'underResult' => $underResult[0], 'krResult' => $krResult[0], 'stResult' => $stResult[0] );
    }

    public function find_previous_six_months($formatted=false, $names=false){
		
		$month_num = $this->currentMonth;		
		$month_name = self::$month_array[$month_num];
		$year_num = $this->currentYear;
			
		if($month_num > 1){
			$month_num--;
		} else {
			$month_num = 12;
			$year_num--;
		}
			
		$month_counter = 5;
		
		$formatted_date = $month_num . "/" . $year_num;
		$csv_date = $month_num . "," . $year_num;
		$csv_date_name = $month_name . "," . $year_num;
		
		$last_six_csv = array($csv_date);
		$formatted_last_six = array($formatted_date);
		$last_six_names = array($csv_date_name);
						
		while ($month_counter > 0) {
			$month_counter--;
			
			if($month_num > 1){
				$month_num--;
				$formatted_date = $month_num . "/" . $year_num;
				$csv_date = $month_num . "," . $year_num;
				$csv_date_name = static::$month_array[$month_num]. "," . $year_num;
				
				array_push($last_six_csv, $csv_date);
				array_push($formatted_last_six, $formatted_date);
				array_push($last_six_names, $csv_date_name);
			} else {
				$month_num = 12;
				$year_num--;
				$formatted_date = $month_num . "/" . $year_num; 
				$csv_date = $month_num . "," . $year_num;
				$csv_date_name = static::$month_array[$month_num]. "," . $year_num;
				
				array_push($last_six_csv, $csv_date);
				array_push($formatted_last_six, $formatted_date);
				array_push($last_six_names, $csv_date_name);
			}
		}
		
		if($formatted == true){
			return $formatted_last_six;
		} else {
			if($names == true){
				return $last_six_names;
			} else {
				return $last_six_csv;	
			}
		}
		
	}

}

//$wsModel = new WorkshopSurvey();


?>