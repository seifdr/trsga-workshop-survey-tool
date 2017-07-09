<?php 

class WorkshopSurveyController {
    
    private $wsModel;
    private $pageRestriction;

    public $urlAccessibleMethods = array( 'customReport', 'survey', 'prepareDelete', 'completeDelete', 'make_csv' );

    public $login_user;
    public $isOutreachUser = FALSE;
    public $isManager = FALSE;

    //first parameter is the workshopSurveyModel. This is mandatory
    //second parameter is an option page restriction, will only run a function if is allowed
    function __construct( $wsModel, $login_user=NULL ){
        $this->wsModel = $wsModel;

        $this->login_user = $login_user[0];
        $this->is_outreach_user();
        $this->is_manager();
    }

    public function is_url_accessible( $name = NULL ){
        return ( in_array( $name, $this->urlAccessibleMethods ) )? TRUE : FALSE;
    }

    private function is_outreach_user(){
        $this->isOutreachUser = ( !empty( $this->login_user ) && $this->login_user->user_type == "outreach" )? TRUE : FALSE;
    }

    private function is_manager(){
        $this->isManager = ( !empty( $this->login_user ) && ( $this->login_user->clearance == "Outreach Manger" || $this->login_user->clearance == "Master" )  )? TRUE : FALSE;
    }

    public function customReport(){
        //used for both the report page, and the make csv page 

        $params = array();

        look( $this->login_user );
        look( $this->isOutreachUser );
        look( $this->isManager );
       
        if( $this->isOutreachUser && $this->isManager ){
            if( isset( $_GET['counselor'] ) && !empty( $_GET['counselor'] ) ){
                if( ( $_GET['counselor'] ) != 'all' ){
                    $params['counselorCode'] = strtoupper( $_GET['counselor'] );
                }
            }
        } elseif ( $this->isOutreachUser && !$this->isManager ){
            if( !empty( $this->login_user->surveyID ) ){
                $params['counselorCode'] = strtoupper( $this->login_user->surveyID );
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

        look( $params );

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
