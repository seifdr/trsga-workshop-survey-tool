<?php 

    include('includes/initialize.php');

    $wsController = new WorkshopSurveyController();

    $wsView = new WorkshopSurveyViews( );

    // $result = $users->find_all();

?>
<html>
    <header>
        <link rel="stylesheet" type="text/css" href="css/main.css">
    </header>
    <body>
        <div class="container">
            <div class="row mb-4">
                <div class="col">
                    <h1>TRS Workshop Survey Report - FY <?php echo $wsView->current_fy; ?></h1>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <?php 

                        $wsView->report_dropdowns();

                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <?php 

                        $wsView->report_heading();

                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <?php 

                        $wsView->ws_survey_report();

                    ?>
                </div>
            </div>
            
        </div> <!-- close container -->
    </body>
</html>