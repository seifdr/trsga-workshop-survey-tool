<?php 

    include('includes/initialize.php');

    $wsModel = new WorkshopSurvey();
    $wsController = new WorkshopSurveyController( $wsModel );
    $wsView = new WorkshopSurveyViews( $wsController, $wsModel );

    if ( isset( $_POST['action'] ) && !empty( $_POST['action'] ) ) {
        if( $wsController->is_url_accessible( $_POST['action'] ) ){
            $wsController->{$_POST['action']}();
        }
    }

    // $result = $users->find_all();

?>
<html>
    <header>
        <link rel="stylesheet" type="text/css" href="css/main.css">
        
    </header>
    <body>
        <div class="container">
            <?php $wsView->deleteSurvey(); ?>
        </div> <!-- close container -->
        <footer>
            <script src="js/main.js"></script>
        </footer>
    </body>
</html>