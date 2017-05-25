<?php 

class WorkshopSurveyController {
    
    private $wsModel;

    public $urlAccessibleMethods = array( 'customReport', 'survey', 'prepareDelete', 'completeDelete' );

    function __construct( $wsModel ){
        $this->wsModel = $wsModel;
    }

    public function is_url_accessible( $name = NULL ){
        return ( in_array( $name, $this->urlAccessibleMethods ) )? TRUE : FALSE;
    }

    public function customReport(){
        $params = array();

        if( isset( $_GET['counselor'] ) && !empty( $_GET['counselor'] ) ){
            if( ( $_GET['counselor'] ) != 'all' ){
                $params['counselorCode'] = strtoupper( $_GET['counselor'] );
            }
        }

        if( isset( $_GET['month'] ) && !empty( $_GET['month'] ) ){
            if( ( $_GET['month'] ) != 'all' ){
                if( ( substr( $_GET['month'], 0, 2 ) == "fq" ) ){
                    $params['fq'] = substr( $_GET['month'], 2, 4 );
                } else {
                    $params['monthNumber'] = $_GET['month'];
                }
            }
        }

        if( isset( $_GET['yr'] ) && !empty( $_GET['yr'] ) ){
            if( ( $_GET['yr'] ) != 'all' ){
                $params['year'] = $_GET['yr'];
            }
        }

        if( isset( $_GET['fq'] ) && !empty( $_GET['fq'] ) ){
            $params['fq'] = $_GET['fq'];
        }

        if( isset( $_GET['fy'] ) && !empty( $_GET['fy'] ) ){
            if( ( $_GET['fy'] ) != 'all' ){
                $params['fy'] = $_GET['fy'];
            }
        }

        if( isset( $_GET['offset'] ) && !empty( $_GET['offset'] ) ){
            if( ( $_GET['offset'] ) != 'all' ){
                $params['offset'] = $_GET['offset'];
            }
        } else {
            $params['offset'] = 25;
        }

        if( isset( $_GET['block'] ) && !empty( $_GET['block'] ) ){
            $params['block'] = $_GET['block'];
        }

        //look( $params );

        $this->wsModel->sanitizeAndLoadParams( $params );
    }

    public function survey(){
        if( isset( $_GET['sid'] ) && !empty( $_GET['sid'] ) ){
            $this->wsModel->id = $_GET['sid'];
        }
    }

    public function prepareDelete(){
        if( isset( $_POST['sid'] ) && !empty( $_POST['sid'] ) ){
            $this->wsModel->id = $_POST['sid'];
        }
    }

    public function completeDelete(){
        if( isset( $_POST['sid'] ) && !empty( $_POST['sid'] ) ){
            $this->wsModel->id = $_POST['sid'];
        }
    }
}

?>
