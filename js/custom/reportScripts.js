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

    $('a#showFails').click( function(){
        
        $('table#reportbody tr.normal').toggle();

        var text = $(this).text().toLowerCase();
        $(this).text(
             ( text == "show only fails" ) ? "Show all surveys" : "Show only fails");


        return false;
    });

    // Pie chart on ws dashboard
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {

        var knowledgeAndSkillsChartData = $('div#pieChart').data('chart');

        var data = google.visualization.arrayToDataTable( knowledgeAndSkillsChartData );

        var options = {
           // title: 'My Daily Activities'
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);

    }

});



