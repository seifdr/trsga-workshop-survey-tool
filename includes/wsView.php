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
            <table class="table table-striped text-center">
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

                                $td1 = $database->escape_values( $row['FirstName'] ." ". $row['LastName'] );
                                $td2 = $database->escape_values( $row['Qtr1_Perc'] );
                                $td3 = $database->escape_values( $row['Qtr2_Perc'] );
                                $td4 = $database->escape_values( $row['Qtr3_Perc'] );
                                $td5 = $database->escape_values( $row['Qtr4_Perc'] );
                                $td6 = $database->escape_values( $row['Tot_Perc'] );

                            
                            ?>
                                <tr <?php if( $zeros ){ echo ' class="zero" '; } ?> >
                                    <td><?php echo $td1; ?></td>
                                    <td><?php echo !empty( $td2 )? $td2 . "%" : '--';  ?></td>
                                    <td><?php echo !empty( $td3 )? $td3 . "%" : '--';  ?></td>
                                    <td><?php echo !empty( $td4 )? $td4 . "%" : '--';  ?></td>
                                    <td><?php echo !empty( $td5 )? $td5 . "%" : '--';  ?></td>
                                    <td><?php echo !empty( $td6 )? $td6 . "%" : '--';  ?></td>
                                    <td>Grade Here</td>
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

            $fy_quarters = array( 'Fiscal Quarter 1', 'Fiscal Quarter 2', 'Fiscal Quarter 3', 'Fiscal Quarter 4' );

            $years = $this->wsModel->find_all_years();

            $fiscal_years = $this->wsModel->find_all_fiscal_years();

        ?>
            <form class="form-inline">
                <!--<label class="mr-sm-2" for="inlineFormCustomSelect">Preference</label>-->
                <select class="custom-select mb-2 mr-sm-2 mb-sm-0" id="inlineFormCustomSelect">
                    <option>All Counselors</option>
                    <optgroup label="Active">
                    <?php 

                    $activeInactiveBreakPoint = FALSE;

                    foreach ($counselors as $counselor ) {
                        $fullname = $counselor->FirstName . " " . $counselor->LastName;

                        if( $counselor->active == 0 && $activeInactiveBreakPoint == FALSE ){
                            echo "<optgroup label='Inactive'>";
                            $activeInactiveBreakPoint == TRUE;
                        }
                        echo  "<option value=\"{$counselor->surveyID}\">{$fullname}</option>";
                    }

                    ?>
                </select>

                <select class="custom-select mb-2 mr-sm-2 mb-sm-0" id="inlineFormCustomSelect">
                    <option>All Months</option>
                    <optgroup label="By Month">
                    <?php 
                    
                    for ($i=0; $i < count($months) ; $i++) { 
                        echo  "<option value=\"{$i}\">{$months[$i]}</option>";
                    }
                    
                    ?>
                    </optgroup>
                    <optgroup label="By Fiscal Quarters">
                    <?php 

                    for ($i=0; $i < count($fy_quarters) ; $i++) { 
                        echo  "<option value=\"{$i}\">{$fy_quarters[$i]}</option>";
                    }

                    ?>    
                    </optgroup>
                </select>

                 <select class="custom-select mb-2 mr-sm-2 mb-sm-0" id="inlineFormCustomSelect">
                    <option>All Years</option>
                    <?php 
                        foreach ($years as $year ) {
                            echo  "<option value=\"{$year}\">{$year}</option>";
                        }
                    ?>
                </select>

                <select class="custom-select mb-2 mr-sm-2 mb-sm-0" id="inlineFormCustomSelect">
                    <option>All FY Years</option>
                    <?php 
                        foreach ($fiscal_years as $year ) {
                            echo  "<option value=\"{$year}\">{$year}</option>";
                        }
                    ?>
                </select>

                 <select class="custom-select mb-2 mr-sm-2 mb-sm-0" id="inlineFormCustomSelect">
                    <option value="25" >25 Surveys</option>
                    <option value="50" >50 Surveys</option>
                    <option value="100" >100 Surveys</option>
                    <option value="all" >All Surveys</option>
                </select>

                <button type="submit" class="btn btn-primary">Submit</button>
            </form>

        <?php 
    }

    public function report_heading( $counselor = NULL, $month = NULL, $year = NULL, $fyear = NULL, $survey = NULL ) {
        $total_surverys = 0; 
        $total_fails    = 0;
        $count_5s       = 0;
        $count_4s       = 0;
        $count_45s       = 0;
        $surverySatPer  = 0;
        $avgSurveyScore = 0; 
        ?>

            <h1>Workshop Survey Report</h1>
            <p><strong>Returned all surveys collected for all counselors in April 2017</strong></p>
            <div class="row mb-4" >
                <div class="col-6 col-sm-4 col-md-3" >Show only fails</div>
                <div class="col-6 col-sm-4 col-md-3" >Download CSV</div>
            </div>
            <table class='table' id="reportheading">
                <tbody>
                    <tr>
                        <td>Total Suverys:</td>
                        <td>92</td>
                        <td>Count of 5's:</td>
                        <td>80</td>
                        <td>Survey Satisfaction Percentage:</td>
                        <td>95%</td>
                    </tr>
                    <tr>
                        <td>Total Fails:</td>
                        <td>5</td>
                        <td>Count of 4's:</td>
                        <td>2</td>
                        <td>Average Survey Score:</td>
                        <td>4.84</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td>Count of 4's and 5's:</td>
                        <td>87</td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>

        <?php
    }

    public function ws_survey_report(){

        $result = $this->wsModel->get_suvery_report();

        ?>

        <table class='table table-striped' id="reportbody">
            <tbody>
                <tr>
                    <th>Suvery ID</th>
                    <th>Event Date</th>
                    <th>Location</th>
                    <th>Counselor</th>
                    <th>Prepared</th>
                    <th>Knowledge</th>
                    <th>Understand</th>
                    <th>Helpful</th>
                    <th>Satisfaction</th>
                    <th>Overall</th>
                    <th></th>
                </tr>
            </tbody>
        </table>


        <?php
        

    }


}


?>