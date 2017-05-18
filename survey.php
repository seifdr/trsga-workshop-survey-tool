<?php 

    include('includes/initialize.php');

    $wsModel = new WorkshopSurvey();
    $wsController = new WorkshopSurveyController( $wsModel );
    $wsView = new WorkshopSurveyViews( $wsController, $wsModel );

?>
<html>
    <body>
        <h1>Survey Page</h1>
    </body>
</html>