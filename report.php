<?php 

    include('includes/initialize.php');

    $wsModel = new WorkshopSurvey( );
    $wsController = new WorkshopSurveyController( $wsModel );
    $wsView = new WorkshopSurveyViews( $wsController, $wsModel );

    // if ( isset( $_GET['action'] ) && !empty( $_GET['action'] ) ) {
    //     $wsController->{$_GET['action']}();
    // } else {
        $wsController->customReport();
    // }

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


                <!-- Modal -->
                <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel" style="display:none">Modal title</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        ...
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close Survey</button>
                    </div>
                    </div>
                </div>
                </div>


        </div> <!-- close container -->
        <footer>
            <script src="js/main.js"></script>
        </footer>
    </body>
</html>