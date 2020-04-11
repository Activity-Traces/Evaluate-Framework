<?php
require "../vendor/autoload.php";

use App\initHome;

   use App\plotd3TreeView;

    
    session_start();

    $view=1;
    
    $initdata = new initHome;
    $initdata->getLastConfigurationValues(
        $nbminrep, $tmin, $tmax, $tdc, $CM, $MC, $PI, $OEQ, $OI, $KA, $KB, $K1,$K2, $TM, $TC, $CV
    );
    unset($initdata);

    if(isset($_POST['view']))
        $view=$_POST['view'];

    if (isset($_SESSION['results'])) {

        $tree = $_SESSION['tree'];
        $d3tree = new plotd3TreeView;

        // Ajouter les valeurs depuis le résultat du calcul
        $d3tree->addValuesToNodes($_SESSION['results'], $tree);

        // Voir/Cacher les resources
        if (isset($_POST['hideResources'])) {
            $hide = $_POST['hideResources'];
            if ($hide)
                for ($i = 0; $i < count($tree); $i++) {
                    if (!in_array($tree[$i]['type'], ['Skills', 'Competency', 'Knowledge', 'answer']))
                        $tree[$i]['hide'] = true;
                }
        }

        // Voir/Cacher les réponses
        if (isset($_POST['hideAnswers'])) {
            $hideAnswers = $_POST['hideAnswers'];
            if ($hideAnswers)
                for ($i = 0; $i < count($tree); $i++) {
                    if ($tree[$i]['type'] == 'answer')
                        $tree[$i]['hide'] = true;
                }
        }
        
        // Transformer l'arbre linaire tree vers un vecteur

        $tree1 = $d3tree->fromArrayToTree($d3tree->deleteExistingNodes($tree, array("name", "parent")));


        // Ajouter la racine

        $node = [array(

            "name" => "Racine",
            "parent" => 'null',
            "children" => [$tree1]
        )];

        $data = json_encode($node);

        unset($d3tree);
            
    }
?>


<!---------------------------------------------------------------------------------------------------------------------------------------------------->
<!---------------------------------------------------------------------------------------------------------------------------------------------------->

<!DOCTYPE html>
<html lang="en">

<!---------------------------------------------------------------------------------------------------------------------------------------------------->

<head>

    <style>
    .link {
        fill: none;
        stroke: #ccc;
        stroke-width: 2px;
    }
    </style>


    <title> Learners Profil Simulator</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/sidebar.css" />

</head>

<!---------------------------------------------------------------------------------------------------------------------------------------------------->

<body>


    <div class="panel-group" id="draggable1">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title">

                    <a data-toggle="collapse" href="#collapse1"> Configuration</a>
                </h4>
            </div>
            <div id="collapse1" class="panel-collapse collapse in">


                <div class="panel-body">

                    <table class="table table-striped">
                        <!---------------------------------------------------------------------------------------------------------------------------------------------------->

                        <form method='post' action='../controller/controller.php'>
                            
                        <thead class="thead-white">
                                <tr>
                                    <th scope="col" style="width:10%">
                                        <button type="submit" class="btn btn-primary" name="startEvaluation">Go!</button><input type="hidden" name="viewinTree" value="viewinTree">       
                                    </th>
                                    <th scope="col" style="width:20%"></th>
                                    <th scope="col" style="width:20%"></th>
                                    <th scope="col"style="width:20%"></th>
                                    
                                    <th scope="col"></th>
                                    <th scope="col"></th>


                                </tr>                                
                            </thead>

                            </tbody>
                            <td>
                                <div class="form-row">
                                        Min Nb Answers<input type="text" class="form-control" style=" background-color: #EEE9B3;" id="nbminrep" name="nbminrep" value=<?php echo $nbminrep; ?> placeholder="0">
                                </div>  
                                
                            </td>
        
                                <td>
                                    <div class="form-row">
        
                                        Training-DT-TBegin<input class="form-control" type="datetime-local" style=" background-color: #F5F7F7;" value=<?php echo $tmin; ?> id="tmin" name="tmin"><br>
                                        Training-DT-TEnd<input class="form-control" type="datetime-local" style=" background-color: #F5F7F7;"  value=<?php echo $tmax; ?> id="tmax" name="tmax"><br>
                                        Computation Start<input class="form-control" type="datetime-local" style=" background-color: #F5F7F7;" value=<?php echo $tdc; ?> id="tdc" name="tdc">
                                    </div>
                                </td>


                                    <td>
                                    <div class="form-row">

                                        <div class="form-group col-md-6">
                                            MC <input type="text" class="form-control" style=" background-color: #F5F7F7;" id="MC" name="MC" value=<?php echo $MC; ?> placeholder="1">
                                        </div>        


                                        <div class="form-group col-md-6">
                                            CM <input type="text" class="form-control" style=" background-color: #F5F7F7;" id="CM" name="CM" value=<?php echo $CM; ?> placeholder="1">
                                        </div>        


                                      
                                    </div>
                                    <div class="form-row">

                                        <div class="form-group col-md-6">

                                            OEQ: <input type="text" class="form-control" style=" background-color: #F5F7F7;" id="OEQ" name="OEQ" value=<?php echo $OEQ; ?> placeholder="1">
                                        </div>
                                        <div class="form-group col-md-6">
                                            OI: <input type="text" class="form-control" style=" background-color: #F5F7F7;" id="OI" name="OI" value=<?php echo $OI; ?> placeholder="1">
                                            
                                        </div>
                                    </div>

                                    <div class="form-row">

                                        <div class="form-group col-md-6">
                                            PI  <input type="text" class="form-control" style=" background-color: #F5F7F7;" id="PI" name="PI" value=<?php echo $PI; ?> placeholder="1">
                                        </div>
                                    </div>       
                                    
                                    </td>


                                <td>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        K1 <input type="text" class="form-control" style=" background-color: #F5F7F7;" id="K2" name="K1" value=<?php echo $K1; ?> placeholder="0">
                                        K2 <input type="text" class="form-control" style=" background-color: #F5F7F7;" id="K2" name="K2" value=<?php echo $K2; ?> placeholder="0">
                                    </div> 

                                    <div class="form-group col-md-6">
                                        KA <input type="text" class="form-control" style=" background-color: #F5F7F7;" id="KA" name="KA" value=<?php echo $KA; ?> placeholder="0">
                                        KB <input type="text" class="form-control" style=" background-color: #F5F7F7;" id="KB" name="KB" value=<?php echo $KB; ?> placeholder="0">
                                    </div>    
                                </div>


                                </td>

                              
                            </form>

                        <!---------------------------------------------------------------------------------------------------------------------------------------------------->

                            <form method='post' action='#'>
                                <td bgcolor="#F5F7F7">
                                    <input type='checkbox' name='hideResources' onChange='submit();' <?php if (isset($hide)) if ($hide) echo "checked";?>>
                                    <label class="form-check-label" for="vertical">Hide Resources</label><br>

                                    <input type='checkbox' name='hideAnswers' onChange='submit();' <?php if (isset($hideAnswers))if ($hideAnswers) echo "checked";?>>
                                    <label class="form-check-label" for="vertical">Hide Answers</label><br>

                                    <input class="form-check-input" type="radio" name="view" id="vertical" value="1" onChange='submit();'>
                                    <label class="form-check-label" for="vertical">Vertical</label><br>

                                    <input class="form-check-input" type="radio" name="view" id="horizontal" value="2" onChange='submit();'>
                                    <label class="form-check-label" for="horizontal">Horizontal</label><br>

                                </td>
                            </form>
                        <!---------------------------------------------------------------------------------------------------------------------------------------------------->

                        </tbody>
                    </table>


                </div>

            </div>
        </div>
    </div>


    <!---------------------------------------------------------------------------------------------------------------------------------------------------->

    <div class="row">

        <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.17/d3.min.js"></script>
    </div>

    
    <!---------------------------------------------------------------------------------------------------------------------------------------------------->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>



    <script>
    
    var treeData = <?php echo $data; ?> ;
    </script>
    <!---------------------------------------------------------------------------------------------------------------------------------------------------->

    <script type="text/javascript" src=<?php
                                        if ($view == 1)
                                            echo "'../js/Vertical.js'";
                                        if ($view == 2)

                                            echo "'../js/Horizontal.js'";
                                        ?>>

    </script>

    <!---------------------------------------------------------------------------------------------------------------------------------------------------->
<script>
    $(function() {
        $("#draggable1").draggable();
    });
</script>
</body>

</html>