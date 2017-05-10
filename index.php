<?php 

    include('includes/initialize.php');

    // $result = $users->find_all();

?>
<html>
    <header>
        <link rel="stylesheet" type="text/css" href="css/main.css">
        
    </header>
    <body>
        <div class="container">
            <div class="row">
                <div class="col mb-4">
                    <h1>TRS Workshop Survey Database</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-6">
                    <h5>Survey Counts By Planner</h5>
                    <?php 

                        $wsView->survey_counts_by_planner();

                    ?>
                    <br />
                    <?php 

                        // $results = $userModel->find_all();
                        // look( $results );
                    ?>
                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-6">
                    <h5>Survey Fails By Planner</h5>
                    <?php 

                        $wsView->survey_counts_by_planner('countFails');

                    ?>
                </div>
            </div>
 
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-5">
                    <h5>FY Counts of all 4's, all 5's, and all combos</h5>
                    <?php 

                        $wsView->survey_counts_by_planner_4s_5s();

                    ?>
                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-7">
                    <h5>FY Survey Averages</h5>
                    <?php 

                        $wsView->survey_avgs_by_planner();

                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <h5>FY Survey Satisfaction Percentages by Planner</h5>
                    <?php

                        $wsView->survey_sat_percentages_by_planner();

                    ?>
                </div>
            </div>
        </div>
        <footer>
            <script src="js/main.js"></script>
        </footer>
    </body>
</html>