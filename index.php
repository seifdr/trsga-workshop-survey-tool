<?php 

    include('includes/initialize.php');

    $wsModel = new WorkshopSurvey();
    $wsController = new WorkshopSurveyController( $wsModel );
    $wsView = new WorkshopSurveyViews( $wsController, $wsModel );

    // $result = $users->find_all();

?>
<html>
    <header>
        <link rel="stylesheet" type="text/css" href="css/main.css">
        
    </header>
    <body>
        <div class="container">
            <div class="row">
                <div class="col">
                    <h1>Workshop Overview - FY <?php echo $wsView->current_fy; ?></h1>
                </div>
            </div>
            <div class="row">
                <div class="col mb-4">
                    <a href="report.php">View Workshop Survey Data</a> | <a href="remove.php">Remove Survey</a>
                </div>
            </div>     
            <div class="row mb-4">
                <div class="col-12 col-sm-6">
                    <h5>FY Workshop Rating</h5>
                    <p><small>aggregated from all planners in current FY</small></p>
                    <?php 

                        $wsView->workshop_rating();

                    ?>
                </div>
            </div>
            <div class="row mb-4">
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
 
            <div class="row mb-4">
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
            <div class="row mb-4">
                <div class="col">
                    <h5>FY Survey Satisfaction Percentages by Planner</h5>
                    <?php

                        $wsView->survey_sat_percentages_by_planner();

                    ?>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col">
                    <h5>Events Attended in the past</h5>
                    <?php 

                        $wsView->past_attendance();

                    ?>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col">
                    <h5>Understanding of the following topics increased or stayed the same?</h5>
                    <?php 

                        $wsView->understandings();

                    ?>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-12 col-sm-6">
                    <h5>Knowledge and Skills gained will be useful</h5>
                    <?php 

                        $wsView->knowledge_useful();

                    ?>
                </div>
            </div>
        </div>
        <footer>
            <script src="js/main.js"></script>
        </footer>
    </body>
</html>