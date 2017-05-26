<?php 
ob_start();

// output headers so that the file is downloaded rather than displayed
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=data.csv');

include('includes/initialize.php');

$wsModel = new WorkshopSurvey();
$wsController = new WorkshopSurveyController( $wsModel, "make_csv" );
$wsView = new WorkshopSurveyViews( $wsController, $wsModel );

if ( isset( $_GET['action'] ) && !empty( $_GET['action'] ) ) {
    if( $_GET['action'] == 'customReport'){
        $wsController->{$_GET['action']}();

        $wsView->generate_csv();

    }
}

ob_flush();

?>