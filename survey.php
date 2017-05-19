<?php 

    include('includes/initialize.php');

    $wsModel = new WorkshopSurvey();
    $wsController = new WorkshopSurveyController( $wsModel );
    $wsView = new WorkshopSurveyViews( $wsController, $wsModel );

    if ( isset( $_GET['action'] ) && !empty( $_GET['action'] ) ) {
        $wsController->{$_GET['action']}();
    }

?>
<html>
    <header>
        <link rel="stylesheet" type="text/css" href="css/main.css">
    </header>
    <body>
        <?php 

            $wsView->singleSurvey();

        ?>
    </body>
</html>