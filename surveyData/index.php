<?php 
 
    include('../includes/initialize.php');

    $wsModel = new WorkshopSurvey();
    $wsController = new WorkshopSurveyController( $wsModel, $login_user );
    $wsView = new WorkshopSurveyViews( $wsController, $wsModel );

    $wsController->setDashboardParams();
    // $result = $users->find_all();

	//check form inputs are valid like month, year, etc
	
	//pull the first two rows of the csv and make sure they match

	



?>
<html>
    <header>
        <link rel="stylesheet" type="text/css" href="../css/main.css">
        <style type="text/css">
			.custom-file-control:before{
				content: "Browse";
			}
			.custom-file-control:after{
				content: "Add files..";
			}
		</style>
    </header>
    <body>
        <div class="container">
            <div class="row">
                <div class="col">
                    <h1>Upload Workshop Survey</h1>
                    
                    <form id="csvUp" action="index.php" method="post" enctype="multipart/form-data" value="1048576">
					<div class="form-group row">
						<div class="col-4">
							<select class="form-control" name="Type">
								<option value="callCenter">Call Center</option>
								<option value="outreach">Outreach</option>
								<option value="workshop">Workshop</option>
							</select>
						</div>
						<div class="col-4">
							<select class="form-control" name="Month">
									<option value="January">January</option><option value="February">February</option><option value="March">March</option><option value="April">April</option><option value="May">May</option><option value="June" selected >June</option><option value="July">July</option><option value="August">August</option><option value="September">September</option><option value="October">October</option><option value="November">November</option><option value="December">December</option>
							</select>
						</div>
						<div class="col-4">
							<select class="form-control" name="Year">
									<option value="2012">2012</option><option value="2013">2013</option><option value="2014">2014</option><option value="2015">2015</option><option value="2016">2016</option><option value="2017" selected >2017</option>
							</select>
						</div>
					</div>
					<div class="form-group row">
						<div class="col">
							 <label class="custom-file">
								<input id="daFile" type="file" name="csv" />
								<span class="custom-file-control"></span>
							</label> 
						</div>
					</div>
					<div class="form-group row d-flex justify-content-center">
						<input class="btn btn-primary col-6" type="submit" value="Submit" name="submit" />
					</div>
				</form>
                </div>
            </div>
        </div>
		<footer>
            <script src="../js/main.js"></script>
        </footer>
    </body>
</html>    