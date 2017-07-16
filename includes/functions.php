<?php

function look( $var ){

    echo "<pre>";
        print_r( $var );
    echo "</pre>";

}

function _e( $input ){
    // _e means trs escape
    echo htmlspecialchars( $input );
}

?>