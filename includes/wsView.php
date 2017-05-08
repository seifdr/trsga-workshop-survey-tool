<?php 

/**
 * 
 */
class WorkshopSurveyViews 
{
    private $wsModel;

    function __construct( )
    {
         $this->wsModel = new WorkshopSurvey();
    }

    public function survey_counts_by_planner( $countFails = FALSE ){
        global $database;

            if( $countFails ){
                $result = $this->wsModel->get_survey_totals_by_FY('TRUE');
            } else {
                $result = $this->wsModel->get_survey_totals_by_FY();
            }
            
            $qtr1Counter        = 0;
            $qtr2Counter        = 0; 
            $qtr3Counter        = 0; 
            $qtr4Counter        = 0;
            $qtrTotalCounter    = 0;       

        ?>
        <h4>Survey Counts By Planner</h4>
        <table class="table table-striped table-responsive text-center">
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

                                        <tr>
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

        <?php 
    }

    public function survey_counts_by_planner_4s_5s(){
        global $database;

           
            $result = $this->wsModel->get_survey_totals_by_FY(FALSE, TRUE);
            
            $all4sCounter        = 0;
            $all5sCounter        = 0; 
            $all4and5sCounter    = 0; 

        ?>
        <h4>FY Counts of all 4's, all 5's, and all combos</h4>
        <table class="table table-striped table-responsive text-center">
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
                                    ?>

                                        <tr>
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

        <?php 
    }
}

$wsView = new WorkshopSurveyViews( );


?>