<?php

    require "vendor/autoload.php";

    /************************************************************************************************************************ */
    use App\initHome;
    
    $initdata = new initHome;

    $initdata->init();
        
    // get public frameworks list
    
    $privateFrameworks = $initdata->privateFrameworks;

    // get private frameworks list
    $publicFrameworks = $initdata->publicFrameworks;

    // get linked resources to framewok // if session is oprened and user select framework
    $Resources = $initdata->resources;

    // get treeview to plot it
    $Treeview = $initdata->treeview;

    // restaurer l'ancien jeux de paramètres si il y'a lieu (suite à une première évaluation)
    $initdata->getLastConfigurationValues(
        $nbminrep, $tmin, $tmax, $tdc, $CM, $MC, $PI, $OEQ, $OI, $KA, $KB, $K1,$K2, $TM, $TC, $CV
    );

    unset($initdata);

?>

<html>

<head>
    <title> Learners Profil Simulator</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.min.css" />
    <link rel="stylesheet" href="css/sidebar.css" />

</head>

<!--------------------------------------------------------------------------------------------------------------------------->

<body onLoad="keepopen()">

    <!--------------------------------------------------------------------------------------------------------------------------->

    <div id="mySidebar" class="sidebar">

        <li><a href="#pageSubmenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                <i class="fa fa-lock" aria-hidden="true"></i>
                My private Frameworks</a>
        </li>

        <ul id='pageSubmenu' class="list-group collapse off">

            <?php

            if (isset($privateFrameworks)) {

                foreach ($privateFrameworks as $node) {
                    if ($node->status == 'Private')
                        echo '<li style="background-color:#1E8449;"> <a  href="?refid=' . $node->Id . '"> <br>&nbsp;&nbsp;<i class="fas fa-book"></i>&nbsp;' . $node->name . '</a><br></li>';
                }
            }

            ?>

        </ul>


        <li><a href="#pageSubmenu2" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                <i class="fa fa-unlock" aria-hidden="true"></i>
                Public Frameworks</a>
        </li>

        <ul id='pageSubmenu2' class="list-group collapse off">

            <?php

            if (is_array($publicFrameworks)) {


                foreach ($publicFrameworks as $node) {
                    if ($node->status == 'Public')
                        echo '<li style="background-color:#1E8449;"><a  href="?refid=' . $node->Id . '"><br>&nbsp;&nbsp;<i class="fas fa-book"></i>&nbsp;' . $node->name . ' <br></a></li>';
                }
            }

            ?>

        </ul>

    </div>

    <!--------------------------------------------------------------------------------------------------------------------------->

    <div id="main">
        <div class="navbar">
            <div class="navbar-inner">

                <ul class="nav" id="topmenue">
                    <a class="openbtn btn-primary pull-right" href="templates/logout.php" role="button">Logout</a>

                    <button class="openbtn pull-right" data-toggle="modal" data-target="#LeftMenu">
                        <i class="fa fa-question-circle" aria-hidden="true"></i>
                    </button>

                    <p id="click_advance"><i class="icon-circle-arrow-down"></i></p>
                    <button class="openbtn" onclick="openNav()">☰</button>

                </ul>
            </div>
        </div>

        <div class="col-sm-12">
            <div class="col-sm-12">
                <div class="row">
                    <div class="panel-group" id="draggable1">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h4 class="panel-title">

                                    <a data-toggle="collapse" href="#collapse1"> Configuration</a>
                                </h4>
                            </div>
                            <div id="collapse1" class="panel-collapse collapse in">


                                <div class="panel-body">

                                    <form method='post' action='controller/controller.php'>
                                        <div class="form-row">

                                            <div class="form-group col-md-3">
                                                Min Nb Answers<input type="text" class="form-control" style=" background-color: #EEE9B3;" id="nbminrep" name="nbminrep" value=<?php echo $nbminrep; ?> placeholder="0">
                                                <br><button type="submit" class="btn btn-primary" name="startEvaluation">Start Evaluation</button><br><br>

                                                    <a href="templates/Answers.php" target="_blank" class="btn btn-primary " role="button">View Answers</a><br><br>

                                                    <a href="templates/TreeView.php" target="_blank" class="btn btn-primary " role="button">Tree View
                                                    <span class="glyphicon glyphicon-tree-deciduous"> 
                                                </a>
  
                                            </div>

                                            <div class="form-group col-md-3">
                                                Training-DT-TBegin<input class="form-control" type="datetime-local" style=" background-color: #EEE9B3;" value=<?php echo $tmin; ?> id="tmin" name="tmin"><br>
                                                Training-DT-TEnd<input class="form-control" type="datetime-local" style=" background-color: #EEE9B3;"  value=<?php echo $tmax; ?> id="tmax" name="tmax"><br>
                                                Computation Start<input class="form-control" type="datetime-local" style=" background-color: #EEE9B3;" value=<?php echo $tdc; ?> id="tdc" name="tdc">
                                            </div>

                                            <div class="form-group col-md-3">
                                                <div class="form-row">
                                                    <div class="form-group col-md-6">
                                                        MC <input type="text" class="form-control" style=" background-color: #EEE9B3;" id="MC" name="MC" value=<?php echo $MC; ?> placeholder="1">
                                                        CM <input type="text" class="form-control" style=" background-color: #EEE9B3;" id="CM" name="CM" value=<?php echo $CM; ?> placeholder="1">
                                                        PI  <input type="text" class="form-control" style=" background-color: #EEE9B3;" id="PI" name="PI" value=<?php echo $PI; ?> placeholder="1">

                                                    </div>

                                                    <div class="form-group col-md-6">
                                                        OEQ: <input type="text" class="form-control" style=" background-color: #EEE9B3;" id="OEQ" name="OEQ" value=<?php echo $OEQ; ?> placeholder="1">
                                                        OI: <input type="text" class="form-control" style=" background-color: #EEE9B3;" id="OI" name="OI" value=<?php echo $OI; ?> placeholder="1">
                                                    </div>

                                                </div>

                                            </div>

                                            <div class="form-group col-md-3">

                                                    <div class="form-group col-md-6">
                                                        K1 <input type="text" class="form-control" style=" background-color: #EEE9B3;" id="K2" name="K1" value=<?php echo $K1; ?> placeholder="0">
                                                        K2 <input type="text" class="form-control" style=" background-color: #EEE9B3;" id="K2" name="K2" value=<?php echo $K2; ?> placeholder="0">
                                                                        
                                                    </div>

                                                    <div class="form-group col-md-6">
                                                        KA <input type="text" class="form-control" style=" background-color: #EEE9B3;" id="KA" name="KA" value=<?php echo $KA; ?> placeholder="0">
                                                        KB <input type="text" class="form-control" style=" background-color: #EEE9B3;" id="KB" name="KB" value=<?php echo $KB; ?> placeholder="0">
                                                                        
                                                    </div>                                     
                                                
                                            </div>
                                
                                        </div>
                                    </form>    
                                        

                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!--------------------------------------------------------------------------------------------------------------------------->
        
            <Framework class="row">
                <h2></h2>
                <p></p>

                <search>
                    <div class="panel-group" id="draggable2">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" href="#collapse2">Search in my Framework</a>
                                </h4>
                            </div>
                            <div id="collapse2" class="panel-collapse collapse in">


                                <div class="panel-body">
                                    <label for="input-select-node" class="sr-only">Search in Tree:</label>
                                    <input type="text" class="form-control" id="Search" placeholder="Search  K/S">
                                </div>

                            </div>
                        </div>
                    </div>

                </search>

                <Tree>
                    <div class="panel-group" id="draggable3">
                        <div class="panel panel-primary">

                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" href="#collapse3">My Framework Objects</a>
                                </h4>
                            </div>

                            <div id="collapse3" class="panel-collapse collapse in">

                                <div class="panel-body" style="word-break:break-all;">
                                    <framework id="Tree">
                                    </framework>

                                </div>

                            </div>
                        </div>

                    </div>
                </Tree>

            </Framework>
        
        </div>

        <!--------------------------------------------------------------------------------------------------------------------------->

    </div>

</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script src="js/jstree.min.js"></script>

<script type="text/javascript">
    var openside = false;
    var NodeType = '';
    var NodeID = '';
    var Treeview=  <?php echo  $Treeview; ?>
    
</script>
<script src="js/home.js"></script>

</html>
