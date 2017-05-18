<?php 

    include('includes/initialize.php');

    // 'counselorCode', 'monthNumber', 'year', 'fy', 'fq'
    $modelParams = array();

    if( !empty( $monthNum ) ){
        $modelParams['monthNumber'] = $monthNumber; 
    }
    
    if( !empty( $year ) ){
        $modelParams['year'] = $year; 
    }

    if( !empty( $fyYear ) ){
        $modelParams['fy'] = $fyYear; 
    }

    if( !empty( $fyQuarter ) ){
        $modelParams['fq'] = $fyQuarter; 
    }

    $wsModel = new WorkshopSurvey( $modelParams );
    $wsController = new WorkshopSurveyController( $wsModel );
    $wsView = new WorkshopSurveyViews( $wsController, $wsModel );
    // $model = new Model();
    // $controller = new Controller($model);
    // $view = new View($controller, $model);


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