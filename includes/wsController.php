<?php 

class WorkshopSurveyController {
    
    private $wsModel;

    public $urlAccessibleMethods = array( 'customReport', 'survey' );

    function __construct( $wsModel ){
        $this->wsModel = $wsModel;
    }

    public function is_url_accessible( $name = NULL ){
        return ( in_array( $name, $this->urlAccessibleMethods ) )? TRUE : FALSE;
    }

    public function customReport(){
        $params = array();

        if( isset( $_GET['counselor'] ) && !empty( $_GET['counselor'] ) ){
            $params['counselorCode'] = strtoupper( $_GET['counselor'] );
        }

        if( isset( $_GET['month'] ) && !empty( $_GET['month'] ) ){
            $params['monthNumber'] = $_GET['month'];
        }

        if( isset( $_GET['year'] ) && !empty( $_GET['year'] ) ){
            $params['year'] = $_GET['year'];
        }

        if( isset( $_GET['fy'] ) && !empty( $_GET['fy'] ) ){
            $params['fy'] = $_GET['fy'];
        }

        if( isset( $_GET['fq'] ) && !empty( $_GET['fq'] ) ){
            $params['fq'] = $_GET['fq'];
        }

        $this->wsModel->sanitizeAndLoadParams( $params );
    }

    public function survey(){
        if( isset( $_GET['sid'] ) && !empty( $_GET['sid'] ) ){
            $this->wsModel->id = $_GET['sid'];
        }
    }
}

?>
