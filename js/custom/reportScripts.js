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
    google.charts.load('current', {packages: ['corechart', 'line']});
    google.charts.setOnLoadCallback(drawLineChart);
    google.charts.setOnLoadCallback(drawPieChart);

    function drawLineChart() {

        var workshopSatPercentageChartData = $('div#lineChart').data('chart');
        
        var data = google.visualization.arrayToDataTable( workshopSatPercentageChartData );

        var options = {
          width: '100%',
          height: 400,
         chartArea:{left:0,top: 20,width:'100%',height:'80%'},
          //title: 'Workshop Survey Satisfaction Trending Graph (last six months)',
          //curveType: 'function',
          legend: { position: 'bottom' },
          vAxis: {
            maxValue: 100,
            viewWindowMode:'explicit',
            viewWindow: {
              max:100,
              min:0
            }
          }
        };

        var chart = new google.visualization.LineChart(document.getElementById('lineChart'));

        chart.draw(data, options);
    }

    function drawPieChart() {

        var knowledgeAndSkillsChartData = $('div#pieChart').data('chart');

        var data = google.visualization.arrayToDataTable( knowledgeAndSkillsChartData );

        var options = {
           // title: 'My Daily Activities'
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);

    }

});



