// Script to display individual surveys on report.php as modal
$(document).ready( function(){

    $('a.indivSurvey').click( function(){
            var url = $(this).attr('href');
            var id  = $(this).data('id');

            $('div.modal-body').load(url, function( response, status, xhr ){
                if ( status == "error" ) {
                    var msg = "Sorry but there was an error: ";
                    alert( msg );
                }

                if( status == "success" ){
                    $("h5.modal-title").css('display', "block").text( 'Workshop Survey # ' + id );
                    $("#myModal").modal();
                }
            });
        return false; 
    }); 

    $('button#surveyDelete').click( function() {
            var isGood=confirm('Are you sure you want to delete this survey?');
            if (isGood) {
                return true;
            } else {
                return false; 
            }
    });
});



