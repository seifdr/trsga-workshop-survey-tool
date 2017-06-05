<?php 

/**
 * 
 */
class WorkshopSurveyViews 
{
    private $wsModel;
    private $wsController;
    private $userModel;
    private $trsgaTime;

    public $current_fy;

    public $currentMonth;
    public $currentYear;

    public $monthNameToNum = array(
        '1' => 'January',
        '2' => 'February',
        '3' => 'March',
        '4' => 'April',
        '5' => 'May',
        '6' => 'June',
        '7' => 'July',
        '8' => 'August',
        '9' => 'September',
        '10' => 'October',
        '11' => 'Novemebr',
        '12' => 'December'
    );

    function __construct( $wsController, $wsModel )
    {

        $this->wsController = $wsController; 
        $this->wsModel      = $wsModel;

        $this->userModel = new User();

        $this->trsgaTime = new trsgaTime();

		$this->currentMonth = $wsModel->monthNumber;
		$this->currentYear  = $wsModel->year;
        $this->current_fy   = $wsModel->fy;

    }

    public function workshop_rating(){
        global $database;

        $result = $this->wsModel->get_workshop_ranking();

        ?>
         <div class="table-responsive">
            <table class="table table-striped text-center">
                <thead>
                    <tr>
                    <th>How would you rank the workshop overall</th>
                    </tr>
                </thead>
                <tbody>
                    <td><?php echo $database->escape_values( $result[0]['questDavg'] ); ?></td>
                </tbody>
            </table>
        </div>
        <?php
        
    }

    public function survey_counts_by_planner( $type = NULL ){
        global $database;

        $result = $this->wsModel->get_survey_totals_by_FY( $type );
        
        $qtr1Counter        = 0;
        $qtr2Counter        = 0; 
        $qtr3Counter        = 0; 
        $qtr4Counter        = 0;
        $qtrTotalCounter    = 0;       

        ?>
        <div class="table-responsive">
            <table class="table table-striped text-center">
                <thead>
                    <tr>
                    <th>Counselor</th>
                    <th>QTR 1</th>
                    <th>QTR 2</th>
                    <th>QTR 3</th>
                    <th>QTR 4</th>
                    <th>TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        foreach ($result as $row) {
                            ?>

                                <tr <?php if( $database->escape_values( $row['Total'] ) == 0 ){
                                    echo ' class="zero" ';
                                } ?> >
                                    <td><?php echo $database->escape_values( $row['FirstName'] ." ". $row['LastName'] ); ?></td>
                                    <td><?php echo $database->escape_values( $row['Qtr1'] ); ?></td>
                                    <td><?php echo $database->escape_values( $row['Qtr2'] ); ?></td>
                                    <td><?php echo $database->escape_values( $row['Qtr3'] ); ?></td>
                                    <td><?php echo $database->escape_values( $row['Qtr4'] ); ?></td>
                                    <td><?php echo $database->escape_values( $row['Total'] ); ?></td>
                                </tr>

                            <?php

                                    $qtr1Counter += $row['Qtr1'];
                                    $qtr2Counter += $row['Qtr2'];
                                    $qtr3Counter += $row['Qtr3'];
                                    $qtr4Counter += $row['Qtr4'];
                                    $qtrTotalCounter += ( $row['Qtr1'] + $row['Qtr2'] + $row['Qtr3'] + $row['Qtr4'] );

                                    $row['Qtr1'] = $row['Qtr2'] = $row['Qtr3'] = $row['Qtr4'] = $row['Total'] = 0;
                        }
                    
                    ?>
                    <tr class="totals">
                        <td>TOTALS</td>
                        <td><?php echo $database->escape_values( $qtr1Counter ); ?></td>
                        <td><?php echo $database->escape_values( $qtr2Counter ); ?></td>
                        <td><?php echo $database->escape_values( $qtr3Counter ); ?></td>
                        <td><?php echo $database->escape_values( $qtr4Counter ); ?></td>
                        <td><?php echo $database->escape_values( $qtrTotalCounter ); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <?php 
    }

    public function survey_counts_by_planner_4s_5s(){
            global $database;

            $result = $this->wsModel->get_survey_totals_by_FY( 'count45s');
            
            $all4sCounter        = 0;
            $all5sCounter        = 0; 
            $all4and5sCounter    = 0; 

        ?>
        <div class="table-responsive">
            <table class="table table-striped text-center">
                <thead>
                    <tr>
                    <th>Counselor</th>
                    <th>All 4's</th>
                    <th>All 5's</th>
                    <th>All 4's' + 5's</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        foreach ($result as $row) {
                                $zeros = ( ( $row['All_4s'] == 0 ) && ( $row['All_5s'] == 0 ) && ( $row['All_4s_5s'] == 0 ) )? TRUE : FALSE;
                            ?>
                                <tr <?php if( $zeros ){ echo ' class="zero" '; } ?> >
                                    <td><?php echo $database->escape_values( $row['FirstName'] ." ". $row['LastName'] ); ?></td>
                                    <td><?php echo $database->escape_values( $row['All_4s'] ); ?></td>
                                    <td><?php echo $database->escape_values( $row['All_5s'] ); ?></td>
                                    <td><?php echo $database->escape_values( $row['All_4s_5s'] ); ?></td>
                                </tr>

                            <?php

                                    $all4sCounter       += $row['All_4s'];
                                    $all5sCounter       += $row['All_5s'];
                                    $all4and5sCounter   += $row['All_4s_5s'];
                        }
                    
                    ?>
                    <tr class="totals">
                        <td>TOTALS</td>
                        <td><?php echo $database->escape_values( $all4sCounter ); ?></td>
                        <td><?php echo $database->escape_values( $all5sCounter ); ?></td>
                        <td><?php echo $database->escape_values( $all4and5sCounter ); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <?php 
    }

    public function survey_avgs_by_planner(){
        global $database;

        $result = $this->wsModel->get_survey_totals_by_FY('fyavgs');

        $q1aAvg_counter = 0;
        $q1bAvg_counter = 0;
        $q1cAvg_counter = 0;
        $total_avg      = 0;

        $nonZeroRowCounter = 0;

        ?>

        <div class="table-responsive">
            <table class="table table-striped text-center">
                <thead>
                    <tr>
                    <th>Counselor</th>
                    <th>Knowledgeable</th>
                    <th>Effective</th>
                    <th>Organized</th>
                    <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        foreach ($result as $row) {
                            
                            $zeros = ( $row['q1a_avg'] == "0.00" && $row['q1a_avg'] == "0.00" && $row['q1a_avg'] == "0.00"  )? TRUE : FALSE;
                            
                            ?>
                                <tr <?php if( $zeros ){ echo ' class="zero" '; } ?> >
                                    <td><?php echo $database->escape_values( $row['FirstName'] ." ". $row['LastName'] ); ?></td>
                                    <td><?php echo $database->escape_values( $row['q1a_avg'] ); ?></td>
                                    <td><?php echo $database->escape_values( $row['q1b_avg'] ); ?></td>
                                    <td><?php echo $database->escape_values( $row['q1c_avg'] ); ?></td>
                                    <td><?php echo $database->escape_values( $row['total_avg'] ); ?></td>
                                </tr>

                            <?php
                                    $q1aAvg_counter       += $row['q1a_avg'];
                                    $q1bAvg_counter       += $row['q1b_avg'];
                                    $q1cAvg_counter       += $row['q1c_avg'];
                                    $total_avg            += $row['total_avg'];

                                    if( !$zeros ){ $nonZeroRowCounter++; }
                        } //end row
                    
                    ?>
                    <tr class="totals">
                        <td>TOTALS</td>
                        <td><?php echo round( $database->escape_values( $q1aAvg_counter ) / $nonZeroRowCounter, 2); ?></td>
                        <td><?php echo round( $database->escape_values( $q1bAvg_counter ) / $nonZeroRowCounter, 2); ?></td>
                        <td><?php echo round( $database->escape_values( $q1cAvg_counter ) / $nonZeroRowCounter, 2); ?></td>
                        <td><?php echo round( $database->escape_values( $total_avg ) / $nonZeroRowCounter, 2 ); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <?php            
    }

    public function survey_sat_percentages_by_planner(){

        global $database;

        $result = $this->wsModel->get_survey_totals_by_FY('satPers');

        $q1Per_counter = 0;
        $q2Per_counter = 0;
        $q3Per_counter = 0;
        $q4Per_counter = 0;
        $totalPer_avg  = 0;

        $nonZeroRowCounter = 0;

        ?>

        <div class="table-responsive">
            <table class="table table-striped text-center" id="satPerc" >
                <thead>
                    <tr>
                        <th>Counselor</th>
                        <th>Qtr 1</th>
                        <th>Qtr 2</th>
                        <th>Qtr 3</th>
                        <th>Qtr 4</th>
                        <th>Total</th>
                        <th>Grade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        foreach ($result as $row) {
                            
                            $zeros = ( is_null( $row['Tot_Perc'] ) )? TRUE : FALSE;
                            
                                $td1     = $database->escape_values( $row['FirstName'] ." ". $row['LastName'] );
                                $td2     = $database->escape_values( $row['Qtr1_Perc'] );
                                $td3     = $database->escape_values( $row['Qtr2_Perc'] );
                                $td4     = $database->escape_values( $row['Qtr3_Perc'] );
                                $td5     = $database->escape_values( $row['Qtr4_Perc'] );
                                $td6     = $database->escape_values( $row['Tot_Perc'] );
                                $mod_td6 = round( $td6 );

                                if(($mod_td6) >= 98){
                                    $gradeTxt = " Exceptional ";
                                    $passing  = TRUE;
                                } elseif (($mod_td6) >= 95){
                                    $gradeTxt = " Exceeds ";
                                    $passing  = TRUE;
                                } elseif (($mod_td6) >= 89){
                                    $gradeTxt = " Meets ";
                                    $passing  = TRUE;
                                } elseif ($mod_td6 != 0) {
                                    $gradeTxt = "Does Not Meet";
                                    $passing  = FALSE;
                                } else {
                                    $gradeTxt = " -- ";
                                    $passing  = TRUE;
                                }
                            ?>
                                <tr <?php 
                                        if( !$passing ){
                                            echo ' class="bg-danger" ';
                                        } elseif ( $zeros ){ echo ' class="zero" '; } 
                                    ?> >
                                    <td><?php echo $td1; ?></td>
                                    <td><?php echo !empty( $td2 )? $td2 . "%" : '--';  ?></td>
                                    <td><?php echo !empty( $td3 )? $td3 . "%" : '--';  ?></td>
                                    <td><?php echo !empty( $td4 )? $td4 . "%" : '--';  ?></td>
                                    <td><?php echo !empty( $td5 )? $td5 . "%" : '--';  ?></td>
                                    <td><?php echo !empty( $td6 )? $td6 . "%" : '--';  ?></td>
                                    <td><?php echo  $gradeTxt ?></td>
                                </tr>

                            <?php
                                    // $q1aAvg_counter       += $row['q1a_avg'];
                                    // $q1bAvg_counter       += $row['q1b_avg'];
                                    // $q1cAvg_counter       += $row['q1c_avg'];
                                    // $total_avg            += $row['total_avg'];

                                    // if( !$zeros ){ $nonZeroRowCounter++; }
                        } //end row
                    
                    ?>
                    <tr class="totals">
                        <td>TOTALS</td>
                        <td><?php //echo round( $database->escape_values( $q1aAvg_counter ) / $nonZeroRowCounter, 2); ?></td>
                        <td><?php //echo round( $database->escape_values( $q1bAvg_counter ) / $nonZeroRowCounter, 2); ?></td>
                        <td><?php //echo round( $database->escape_values( $q1cAvg_counter ) / $nonZeroRowCounter, 2); ?></td>
                        <td><?php //echo round( $database->escape_values( $q1cAvg_counter ) / $nonZeroRowCounter, 2); ?></td>
                        <td><?php //echo round( $database->escape_values( $q1cAvg_counter ) / $nonZeroRowCounter, 2); ?></td>
                        <td><?php //echo round( $database->escape_values( $total_avg ) / $nonZeroRowCounter, 2 ); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <?php   


    }

    public function suvery_sat_grading($total_percentage) {
		$total = $total_percentage;
		
		if(($total) >= 98){
			$grade_text_output = " Exceptional ";
		} elseif (($total) >= 95){
			$grade_text_output = " Exceeds ";
		} elseif (($total) >= 89){
			$grade_text_output = " Meets ";
		} elseif ($total != 0) {
			$grade_text_output = "Does Not Meet";
		} else {
			$grade_text_output = " No Surveys Yet ";
		}
		
		return $grade_text_output;
	}	

    public function past_six_mon_sat_percentages(){
        $result = $this->wsModel->get_past_six_mon_sat_percentages();

       

        $chartArr = array();

        array_push( $chartArr, array('Month', 'Percentage') );

        foreach ( $result as $row ) {
             array_push( $chartArr, array( $row['monthName'], round( $row['percentage'], 2 ) ) );
        }

        $lineChartObj = json_encode( $chartArr );

        ?>
            <div id="lineChart" data-chart='<?php echo $lineChartObj; ?>'></div>
        <?php

    }

    public function past_attendance(){
        global $database;

        $result = $this->wsModel->get_past_attendance();
                ?>

        <div class="table-responsive">
            <table class="table table-striped text-center">
                <thead>
                    <tr>
                        <th>Events</th>
                        <th>Precentage</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 

                        foreach ($result[0] as $key => $value) {
                        
                            if (strpos($key, 'Count') !== false) {
                               echo "<td>{$value}</td></tr>";
                            } else {
                                echo "<tr><td>{$key}</td><td>{$value}%</td>";
                            }
                        }
                    
                    ?>
                </tbody>
            </table>
        </div>

        <?php

    }

    public function understandings(){
        global $database;

        $result = $this->wsModel->get_understandings();

        ?>

        <div class="table-responsive">
            <table class="table table-striped text-center">
                <thead>
                    <tr>
                        <th>Topics</th>
                        <th>Average Value</th>
                        <th>Average Response</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 

                        $legend = array(
                            'N/A',
                            'Not sure',
                            'About the same',
                            'Increased a little',
                            'Increased a lot',
                        );
                    
                        foreach ($result[0] as $key => $value) {

                            $roundedVal = $database->escape_values( round( $value ) );
                            $rating     = $legend[$roundedVal];

                            echo "<tr><td>{$key}</td><td>". $database->escape_values( $value ) ."</td><td>{$rating}</td></tr>";
                        }

                    ?>
                </tbody>
            </table>
        </div>

        <?php   

    }

    public function knowledge_useful(){

       global $database;

        $result = $this->wsModel->get_knowledge_useful();

        ?>

        <div class="col-12 col-sm-12 col-md-6">
            <h5>Knowledge and Skills gained will be useful</h5>
            <p><?php echo $database->escape_values( $result[0]['YesPerc'] ); ?>% of people found the knowledge and information gained from the workshops useful.</p>
                <div class="table-responsive">
                    <table class="table table-striped text-center">
                        <thead>
                            <tr>
                            <th>Response</th>
                            <th>Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>Yes</td><td><?php echo $database->escape_values( $result[0]['Yes'] ); ?></td></tr>
                            <tr><td>No</td><td><?php echo $database->escape_values( $result[0]['No'] ); ?></td></tr>
                        </tbody>
                    </table>
                </div>
        </div> 
        <div class="col-12 col-sm-12 col-md-6">
            <?php 
                $chartObj = json_encode( [
                    ['Reponse', 'Count'],
                    ['Yes', (int) $result[0]['Yes']],
                    ['No', (int) $result[0]['No']]
                ] );
            ?>
            <div id="piechart" style="width: 550px; height: 350px;" data-chart='<?php echo $chartObj; ?>'></div>
        </div>
        <?php

    }

    //SURVEY REPORT FUNCTIONS

    public function report_dropdowns(){

            $counselors = $this->userModel->find_by_sql(
                // 'SELECT * FROM users WHERE active = 1'
                'SELECT * FROM users ORDER BY active DESC , LastName ASC'
            );

            $months = array( 
                'January',
                'February',
                'March',
                'April',
                'May',
                'June',
                'July',
                'August',
                'September',
                'October',
                'November',
                'December'
            );

            $offsets = array( 25, 50, 100 );

            $fy_quarters = array( 'Fiscal Quarter 1', 'Fiscal Quarter 2', 'Fiscal Quarter 3', 'Fiscal Quarter 4' );

            $years = $this->wsModel->find_all_years();

            $fiscal_years = $this->wsModel->find_all_fiscal_years();

        ?>
            <form class="form-inline" method="get" action="report.php" >
                <input type="hidden" name="action" value="customReport" />

                <select class="custom-select mb-2 mr-sm-2 mb-sm-0" id="inlineFormCustomSelect" name="counselor" >
                    <option value="all" >All Counselors</option>
                    <optgroup label="Active">
                    <?php 

                    $activeInactiveBreakPoint = FALSE;

                    foreach ($counselors as $counselor ) {
                        $fullname = $counselor->FirstName . " " . $counselor->LastName;

                        if( $counselor->active == 0 && $activeInactiveBreakPoint == FALSE ){
                            echo "<optgroup label='Inactive'>";
                            $activeInactiveBreakPoint == TRUE;
                        }
                        echo  "<option ";

                        if( !empty( $this->wsModel->counselorCode ) && ( $this->wsModel->counselorCode == $counselor->surveyID ) ){
                            echo " selected ";
                        }
                        
                        echo " value=\"{$counselor->surveyID}\">{$fullname}</option>";
                    }

                    ?>
                </select>

                <select class="custom-select mb-2 mr-sm-2 mb-sm-0" id="inlineFormCustomSelect" name="month" >
                    <option value="all">All Months</option>
                    <optgroup label="By Month">
                    <?php 
                    
                    for ($i=0; $i < count($months) ; $i++) {
                        $mn = $i + 1;
                        echo  "<option ";

                        if( empty( $this->wsModel->fq ) && ( !empty( $this->wsModel->monthNumber ) && ( $this->wsModel->monthNumber == $mn ) ) ) { echo " selected "; }

                        echo " value=\"". ( $i + 1 ) ."\">{$months[$i]}</option>";
                    }
                    
                    ?>
                    </optgroup>
                    <optgroup label="By Fiscal Quarters">
                    <?php 

                    for ($i=0; $i < count($fy_quarters) ; $i++) { 
                        echo  "<option ";
                        
                        if( !empty( $this->wsModel->fq ) && ($i + 1) == $this->wsModel->fq ){ echo " selected "; }

                        echo " value=\"fq". ( $i + 1 ) ."\">{$fy_quarters[$i]}</option>";
                    }

                    ?>    
                    </optgroup>
                </select>

                 <select class="custom-select mb-2 mr-sm-2 mb-sm-0" id="inlineFormCustomSelect" name="yr">
                    <option value="all" >All Years</option>
                    <?php 
                        foreach ($years as $year ) {
                            echo  "<option ";

                            if( $year == $this->wsModel->year ){
                                echo " selected ";
                            }
                            
                            echo " value=\"{$year}\">{$year}</option>";
                        }
                    ?>
                </select>

                <select class="custom-select mb-2 mr-sm-2 mb-sm-0" id="inlineFormCustomSelect" name="fy">
                    <option value="all" >All FY Years</option>
                    <?php 
                        foreach ($fiscal_years as $fyear ) {
                            echo  "<option ";
                            
                            if( !empty( $this->wsModel->fy ) && $fyear == $this->wsModel->fy ){
                                echo " selected ";
                            }
                            
                            echo " value=\"{$fyear}\">FY {$fyear}</option>";
                        }
                    ?>
                </select>

                 <select class="custom-select mb-2 mr-sm-2 mb-sm-0" id="inlineFormCustomSelect" name="offset" >
                    <option value="all" >All Surveys</option>
                    <?php 

                        foreach ($offsets as $offset) {
                           echo "<option ";
                                if( $this->wsModel->offset == $offset ){ echo " selected "; }
                           echo " value='". $offset ."' >". $offset ." Surveys</option>";
                        }

                    ?>
                </select>

                <button type="submit" class="btn btn-primary">Submit</button>
            </form>

        <?php 
    }

    public function report_heading( ) {
        global $database;
        $result = $this->wsModel->get_survey_report_header_numbers();

        ?>
            <p><strong><?php echo $this->sql_to_text(); ?></strong></p>
            <div class="row mb-4" >
                <div class="col-6 col-sm-4 col-md-3" ><a href="#" id="showFails" >Show only fails</a></div>
                <div class="col-6 col-sm-4 col-md-3" ><a href="make_csv.php?action=customReport">Download CSV</a></div>
            </div>
            <table class='table' id="reportheading">
                <tbody>
                    <tr>
                        <td>Total Suverys:</td>
                        <td><?php echo $database->escape_values( $result['TotalCount'] ); ?></td>
                        <td>Count of 5's:</td>
                        <td><?php echo $database->escape_values( $result['all5s'] ); ?></td>
                        <td>Survey Satisfaction Percentage:</td>
                        <td><?php echo $database->escape_values( $result['surveySatPercentage'] ); ?>%</td>
                    </tr>
                    <tr>
                        <td>Total Fails:</td>
                        <td><?php echo $database->escape_values( $result['Fails'] ); ?></td>
                        <td>Count of 4's:</td>
                        <td><?php echo $database->escape_values( $result['all4s'] ); ?></td>
                        <td>Average Survey Score:</td>
                        <td><?php echo $database->escape_values( $result['avgScore'] ); ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td>Count of 4's and 5's:</td>
                        <td><?php echo $database->escape_values( $result['all45s'] ); ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>

        <?php
    }

    public function ws_survey_report(){

        $result = $this->wsModel->get_suvery_report();

        $resultBody     = $result[0];
        $resultBottom   = $result[1];

        if( !empty( $resultBody ) AND count( $resultBody[0] ) > 0 ){

        ?>

        <table class='table table-striped' id="reportbody">
            <tbody>
                <tr>
                    <th>Suvery ID</th>
                    <th>Event Date</th>
                    <th>Location</th>
                    <th>Counselor</th>
                    <th>Knowledgable</th>
                    <th>Effective</th>
                    <th>Organized</th>
                    <th>Overall</th>
                    <th></th>
                </tr>
                <?php 

                    foreach ($resultBody as $row) {
                        echo $this->output_survey_row( $row );
                    }
                ?>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th><?php echo htmlspecialchars( $resultBottom[0]['Knowledgable'] ); ?></th>
                    <th><?php echo htmlspecialchars( $resultBottom[0]['Effective'] ); ?></th>
                    <th><?php echo htmlspecialchars( $resultBottom[0]['Organized'] ); ?></th>
                    <th><?php echo htmlspecialchars( $resultBottom[0]['Overall'] ); ?></th>
                    <th></th>
                </tr>
            </tbody>
        </table>
        <?php 
            if( ( isset( $this->wsModel->paginationObj ) ) && ( $this->wsModel->paginationObj->total_pages() > 1 ) ){
                $this->output_report_pagination();
            }
        ?>
        <?php
        // cloisng for count of $resultBody
        } else {
            echo "<p>No results found</p>";
        } 
    }

    private function output_survey_row( $row ){
        $return = ( $row['Failed'] )? "<tr class='bg-danger'>" : "<tr class='normal'>";
                        
        $return .= "<td>{$row['id']}</td>
                <td>{$row['Date']}</td>
                <td>{$row['location']}</td>
                <td>{$row['Counselor']}</td>
                <td>{$row['Knowledgable']}</td>
                <td>{$row['Effective']}</td>
                <td>{$row['Organized']}</td>
                <td>{$row['Overall']}</td>
                <td><a class='indivSurvey' data-id=\"{$row['id']}\" href='survey.php?action=survey&sid={$row['id']}' title='View Full Survey' >Full Survey</a></td>
            </tr>";
        return $return;
    }

    private function output_report_pagination(){
    
        ?>
            <nav aria-label="Survery Report Pagination">
                <ul class="pagination justify-content-center">
                    <?php if( $this->wsModel->paginationObj->has_previous_page() ){ ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo $this->wsModel->paginationObj->previousLink; ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                                <span class="sr-only">Previous</span>
                            </a>
                        </li>
                    <?php } 

                        // Iterate through the pages in between
                        for($i=1; $i <= $this->wsModel->paginationObj->total_pages(); $i++){
                                echo '<li class="page-item';
                                     if( $i == $this->wsModel->paginationObj->current_page ){ echo " active "; } 
                                echo '"><a class="page-link" href="'. $this->wsModel->paginationObj->genericLink .'&block='. $i .'">'. $i .'</a></li>';
                        }
                    
                    if( $this->wsModel->paginationObj->has_next_page() ){ ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo $this->wsModel->paginationObj->nextLink; ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                                <span class="sr-only">Next</span>
                            </a>
                        </li>
                    <?php 
                        }
                    ?> 
                </ul>
            </nav>
        <?php
    }

    public function sql_to_text(){
        global $database;

        $p = $this->wsModel->params;
        
        $text = "Returned all surveys collect for ";

        if( isset( $p['counselorCode'] ) && !empty( $p['counselorCode'] ) ){
            $userModel = new User;
            
            $sql = "SELECT FirstName, LastName FROM users WHERE surveyID = \"". $database->escape_values( $p['counselorCode'] ) ."\"";
            $counselor = $userModel->find_by_sql( $sql );
            $text .= $counselor[0]->FirstName . " " . $counselor[0]->LastName . " ";
        } else {
            $text .= "all counselors ";
        }

        $inCounter = FALSE;

        if( isset( $p['monthNumber'] ) && !empty( $p['monthNumber'] ) ){
            $text .= " in " . $this->monthNameToNum[ $p['monthNumber'] ] . " ";
            $inCounter = TRUE;
        } elseif( isset( $p['fq'] ) && !empty( $p['fq'] ) ){
            $text .= " in fiscal quarter " . htmlspecialchars( $p['fq'] ) .", ";
        }

        if( isset( $p['year'] ) && !empty( $p['year'] ) ){
            if( !$inCounter ){ $text .= "in "; }
            if( isset( $p['fq'] ) && !empty( $p['fq'] ) ){ $text .= " year"; }
            $text .=  $p['year'];
            $inCounter = TRUE;
        }

        if( isset( $p['fy'] ) && !empty( $p['fy'] ) ){
            if( !$inCounter ){ $text .= "in "; }
            if( isset( $p['year'] ) && !empty( $p['year'] ) ){ $text .= ", and"; }
            $text .=  " FY " . $p['fy'] . " ";
        } elseif ( $this->wsModel->fy && !empty( $this->wsModel->fy ) ){
             $text .=  " FY " . $this->wsModel->fy . " ";
        }

        return $text;
    }    

    public function singleSurvey() {
       global $database;

       $result = $this->wsModel->get_single_survey();

       if( !empty( $this->wsModel ) && !empty( $result ) ){
           ?>
                
           <div class="container">
                <div class="row">
                    <div class="col">
                            <div class="row">
                                <div class="col col-sm-8 col-md-7">
                                    <table class='table indivSurvey'>
                                        <tbody>
                                            <tr>
                                                <th>Workshop Date</th>
                                                <td><?php echo htmlentities( $result['Date'] ); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Counseling Location</th>
                                                <td><?php echo htmlentities( $result['location'] ); ?></td>
                                            </tr>
                                            <tr>
                                                <th>TRS Presentor Name</th>
                                                <td><?php echo htmlentities( $result['FirstName'] . " " . $result['LastName'] ); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                    <p><strong>1.) Please indicate your impression of the statements listed below:</strong></p>
                    <div class="row">
                        <div class="col-12 col-md-10 col-lg-9">
                            <table class='table indivSurvey'>
                                    <tbody>
                                        <tr>
                                            <td class="indent">The presenter was knowledgable</th>
                                            <td><?php echo htmlentities( $result['question1a'] ); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="indent">The presenter has an effective presentation style</th>
                                            <td><?php echo htmlentities( $result['question1b'] ); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="indent">The workshop content was organized and easy to follow</th>
                                            <td><?php echo htmlentities( $result['question1c'] ); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="indent">Please rate the workshop overall</th>
                                            <td><?php echo htmlentities( $result['question1d'] ); ?></td>
                                        </tr>
                                </tbody>
                            </table>
                        </div> <!-- close col -->
                    </div> <!-- close row -->
                    <p><strong>If any, which TRS events have you attended in the past?</strong></p>
                    <p><?php echo $this->explodeEventsAttended( $result['question2'] ); ?></p>
                    <p><strong>3.) After attending the Group Counseling event, has your understanding of the following topics increased or stayed the same?</strong></p>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <table class='table indivSurvey'>
                                <tbody>
                                    <tr>
                                        <td class="indent">Eligibility Options</th>
                                        <td><?php echo htmlentities( $result['question3a'] ); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="indent">Plans of Retirement/Options</th>
                                        <td><?php echo htmlentities( $result['question3b'] ); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="indent">Beneficiary Information</th>
                                        <td><?php echo htmlentities( $result['question3c'] ); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="indent">Service Credit</th>
                                        <td><?php echo htmlentities( $result['question3d'] ); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <p><strong>The knowledge and skills I gained from this group counseling will be useful when applying for retirement.</strong></p>
                    <p class="indent"><?php echo ( $result['question4'] )? "Yes" : "No"; ?></p>

                    <p><strong>What other topics would you like us to cover in future workshops?</strong></p>
                    <p class="indent"><?php 
                        if( !empty( $result['question5'] ) ){
                             echo htmlentities( $result['question5'] );
                        } else {
                            echo "No reponse given.";
                        }
                    ?></p>

                    <p><strong>Please tell us what you found most valuble about this workhop?</strong></p>
                    <p class="indent"><?php 
                        if( !empty( $result['question6'] ) ){
                             echo htmlentities( $result['question6'] );
                        } else {
                            echo "No reponse given.";
                        }
                    ?></p>

                </div> <!-- close col -->
                </div> <!-- close row -->
            </div>

           <?php 
       }

    }

    public function deleteSurvey(){
        ?>

            <div class="row">
                <div class="col">
                    <h1 class="mb-4">Remove a Workshop Survey</h1>
                        <div class="row justify-content-center" >
                            <div class="col">
                                <?php
                                    if( ( isset( $_POST['action'] ) && !empty( $_POST['action'] ) ) && ( isset( $_POST['sid'] ) && !empty( $_POST['sid'] ) ) ){

                                        if( $_POST['action'] == 'prepareDelete'){

                                            $result = $this->wsModel->survey_report( false, TRUE );
                                            
                                            if( empty( $result[0] ) ){
                                                $this->alert('<strong>No survey found.</strong> Please try again.');
                                            }
                                                ?>
                                                <table class='table table-striped' id="reportbody">
                                                    <tbody>
                                                        <tr>
                                                            <th>Suvery ID</th>
                                                            <th>Event Date</th>
                                                            <th>Location</th>
                                                            <th>Counselor</th>
                                                            <th>Knowledgable</th>
                                                            <th>Effective</th>
                                                            <th>Organized</th>
                                                            <th>Overall</th>
                                                            <th></th>
                                                        </tr>
                                                    <?php echo $this->output_survey_row( $result[0] ); ?>
                                                    </tbody>
                                                </table>
                                                <div class="row justify-content-center" >
                                                    <div class="col-4">
                                                        <form class="form-inline row" method="post" action="remove.php">
                                                            <input type="hidden" name="action" value="completeDelete" />
                                                            <input type="hidden" name="sid" value="<?php echo $_POST['sid']; ?>" />
                                                            <button id="surveyDelete" type="submit" class="btn btn-primary col m-2">Delete Survey #<?php echo $this->wsModel->id; ?></button>
                                                        </form>
                                                    </div>
                                                </div>
                                                

                                                <?php
                                        } elseif ( $_POST['action'] == 'completeDelete' ){
                                            if( $this->wsModel->deleteSurvey() ){
                                               echo $this->success('<strong>Success!</strong> Suvery #' . $this->wsModel->id . " was successfully deleted.");
                                            } else {
                                               echo $this->alert('<strong>Error# 0001</strong> There was a problem deleting the survey. Please try again.');
                                            }
                                            echo "<p><a href='../ws'>Return to the workshop survey dashboard.</a></p>";
                                        }
                                    } else {
                                ?>
                                            <p>Type in a survey number below and click search. Survey number / ID must be a number.</p>
                                            <form class="form-inline row" method="post" action="remove.php">
                                                <label class="sr-only" for="inlineFormInput">Survey ID</label>
                                                <input type="text" class="form-control col-9 ml-3" id="inlineFormInput" placeholder="Survey ID" name="sid" >
                                                <input type="hidden" name="action" value="prepareDelete" />
                                                <button type="submit" class="btn btn-primary col-2 ml-2">Submit</button>
                                            </form>
                                    
                            <?php } ?>
                      </div>
                    </div>
                </div>
            </div>


        <?php
    }

    private function alert( $message = ''){
        return "<div class='alert alert-danger' role='alert'>". $message ."</div>";
    }

    private function success( $message = ''){
        return "<div class='alert alert-success' role='alert'>". $message ."</div>";
    }

    private function explodeEventsAttended( $question2Input ){
        $returnStr = ""; 

        $eventsAttended = explode(',', $question2Input );
        
        if( count( $eventsAttended ) > 0 ){
                $cnt = 0;
                foreach ( explode(',', $question2Input ) as $value ) {
                        if( $cnt > 0 ){ $returnStr .= ", "; }
                        $returnStr .= $this->wsModel->workshopTypes[ ( $value - 1 ) ];
                        $cnt++;
                }
        } else {
            $returnStr = "<p>No reponse given.</p>";
        }
        return $returnStr;
    }


    //MAKE CSV 

    public function generate_csv(){
        $result = $this->wsModel->get_csv_report();

        // look( $result['Avgs'][0] );

        // look( $result['Data'] );

        // look( $result['Counts'] );

        // look( count( $result['Data'] ) );

        $sql_explain = $this->sql_to_text();


        // create a file pointer connected to the output stream


	    $output = fopen('php://output', 'w');

        if(count( $result['Data'] ) <= 0){
			fputcsv($output, array('There was an error preparing the file.'));
		} else {
            fputcsv($output, array('Workshop Surveys'));
			fputcsv($output, array($sql_explain));
			fputcsv($output, array(' '));
			fputcsv($output, array('Total Surveys: ', $result['Counts']['TotalCount'], ' ', 'Count of 5\'s: ', $result['Counts']['all5s'], ' ', 'Survey Satisfaction Perentage: ', $result['Counts']['surveySatPercentage']. '%'));
			fputcsv($output, array('Total Fails: ', $result['Counts']['Fails'], ' ', 'Count of 4\'s: ', $result['Counts']['all4s'], ' ', 'Average Survey Score: ', $result['Counts']['avgScore'] ));
			fputcsv($output, array(' ', ' ', ' ', 'Count of 4\'s and 5\'s: ', $result['Counts']['all45s'], ' ', ' ', ' ' ));
			fputcsv($output, array(' '));

            fputcsv($output, array('SurveryID',  'Event Date',  'Location', 'Counselor', 'Pass or Fail', 'The presenter was knowledgable', 'The presenter has an effective presentation style.', 'The workshop content was organized and easy to follow', 'Please rate the workshop overall', 'If any, which TRS events have you attened in the past?', 'Eligility Requirements', 'Plans of Retirement/Options', 'Beneficiary Information', 'Service Credit', 'The knowledge and skills I gained from this group counseling will be usefull when applying for retirement.', 'What other topics would you like us to cover in future workshops?', 'Please tell us what you found most valuable about this workshop?'        ));

            foreach ($result['Data'] as $row ) {

                $passOrFail = ( $row['Failed'] == 0 )? 'Pass' : 'Fail';
                $question2  = $this->explodeEventsAttended( $row['Q2_PastAttend'] );

                fputcsv($output, array( $row['id'], $row['Date'], $row['location'], $row['Counselor'], $passOrFail, $row['Knowledgable'], $row['Effective'], $row['Organized'], $row['Overall'], $question2, $row['Q3_Eligibility'], $row['Q3_Plans'], $row['Q3_Beneficiary'], $row['Q3_Service_Credit'], $row['Q4_Useful'], $row['Q5_OtherTopics'], $row['Q6_MostValuable'] ));

            }

            fputcsv($output, array('','','','','', $result['Avgs'][0]['Knowledgable'], $result['Avgs'][0]['Effective'], $result['Avgs'][0]['Organized'], $result['Avgs'][0]['Overall'], '', $result['Avgs'][0]['Eligibility'], $result['Avgs'][0]['Plans'], $result['Avgs'][0]['Beneficiary'], $result['Avgs'][0]['ServiceCredit'] ) );

        }
    }


}


?>