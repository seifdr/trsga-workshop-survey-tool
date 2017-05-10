<?php 

/**
 * 
 */
class WorkshopSurveyViews 
{
    private $wsModel;
    public $fy;

    function __construct( )
    {

        $this->fy = $this->find_current_FY()['fy_year'];

        $this->wsModel = new WorkshopSurvey( '2017' );
    }

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


}

$wsView = new WorkshopSurveyViews( );


?>