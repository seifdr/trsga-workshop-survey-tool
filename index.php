<?php 

    include('includes/initialize.php');

    // $result = $users->find_all();

?>
<html>
    <header>
        <link rel="stylesheet" type="text/css" href="css/main.css">
        
    </header>
    <body>
        <div class="container">
            <div class="row">
                <div class="col mb-4">
                    <h1>TRS Workshop Survey Database</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                    <?php 

                        $wsView->survey_counts_by_planner();

                    ?>
                </div>
                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                    <?php 

                        $wsView->survey_counts_by_planner('TRUE');

                    ?>
                </div>
            </div>
 
            <div class="row">
                <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                    <?php 

                        $wsView->survey_counts_by_planner_4s_5s();

                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam vehicula tortor ut sapien placerat pellentesque. Cras consequat velit laoreet erat lacinia ultrices. Aliquam finibus tortor vitae quam venenatis, quis fringilla nisl pulvinar. Suspendisse quis tincidunt erat. Aliquam hendrerit laoreet neque, sit amet rhoncus risus molestie vel. Quisque eu nisi quis mauris finibus pretium. Cras ac laoreet nulla. Praesent in vehicula massa.</p>
                </div>
            </div>
        </div>
        <footer>
            <script src="js/main.js"></script>
        </footer>
    </body>
</html>