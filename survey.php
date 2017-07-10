<?php 

    include('includes/initialize.php');

    $wsModel = new WorkshopSurvey();
    $wsController = new WorkshopSurveyController( $wsModel, $login_user );
    $wsView = new WorkshopSurveyViews( $wsController, $wsModel );

    $proceedWithQuery = FALSE;

    if ( isset( $_GET['action'] ) && !empty( $_GET['action'] ) ) {
            if( $wsController->is_url_accessible( $_GET['action'] ) ){
                $wsController->{$_GET['action']}();
                $proceedWithQuery = TRUE;
            }
    }

?>
<html>
    <header>
        <link rel="stylesheet" type="text/css" href="css/main.css">
    </header>
    <body>
        <?php 
            if( $proceedWithQuery ){
                $wsView->singleSurvey( $login_user[0] );
            } else {
                echo "<p>No survey was found. Please try again.</p>";
            }
            

        ?>
    </body>
</html>