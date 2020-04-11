<?php

require "../vendor/autoload.php";

use App\sqlRequests;
use App\Framework;


//*********************************************************************************************************************** */
// simuler les réponses : ces réponses sont à récupérer par la suite depuis XAPI server

    session_start();

    //************************************************************************************ */
    // afficher les réponses

    $sql = new sqlRequests;
    $Framework_Request = new Framework;

    $sql->getUserAnswers();
    $result = $sql->getResult();

    //************************************************************************************ */
    // récupérer la liste des resources attachés au framework

    if (isset($_SESSION['Framework'])){
        $Framework_Request->getResourcesList($_SESSION['Framework']);
        $RSNames=$Framework_Request->getResources();
    }

    //************************************************************************************ */
    
    unset($sql,  $Framework_Request);
?>

<html>

<head>
    <title>add new answers</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">


</head>



<div class="row">
    <div class="col-sm-7">

        <div class="panel-body">

            <form action='../controller/controller.php' method='post'>

                <body>

                    <table class="table table-striped">
                        <thead class="thead-white">
                            <tr>
                                <th scope="col">Id answer</th> 
                                <th scope="col">ResourceName</th>
                                <th scope="col">Mark</th>
                                <th scope="col">Value</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            foreach($result as $node) {             
                                echo "<tr>
                                        <td>". $node->id."</td>

                                        <td>". $node->idresource ."</td>
                                        <td>".$node->note."</td>
                                        <td>".$node->temps."</td>
                                        <td> <input class='form-check-input' type='checkbox' name='choix[]' value='".$node->id."'></td>
                                    </tr>";
                            }
                        ?>
                        </tbody>
                    </table>
                    <button type="submit" class="btn btn-primary" name='deleteanswer'>Delete selected answers</button>
                    <button type="submit" class="btn btn-primary" name='deleteAllAnswers'>Delete ALL answers</button>


                   
            </form>
        </div>
    </div>
    <div class="col-sm-5">
    <form action='../controller/controller.php' method='post'>

        <jeuxTest>
            <div class="panel-group" id="b2b">
                <div class="panel " >
                
                      
                            
                                <div class="panel-body">
                                    <br>Mark: <input type="text" class="form-control"
                                        style=" background-color: #EEE9B3;" id="note" name="note" value="0"
                                        placeholder="0">
                                    <br>Resource:

                                    <select class="form-control" name="resource">
                                        <?php 
                                            foreach ($RSNames as $row) 
                                            echo "<option>".$row."</option>"
                                        ?>
                                    </select>

                                    <br>DateTime answer <input class="form-control" type="datetime-local"
                                        style=" background-color: #EEE9B3;" value="2019-01-01T00:00:00" id="tnote"
                                        name="tnote">
                                        <input type='hidden' name='viewanswers' id='mode2' value='viewanswers'>

                                    <br><button type="submit" class="btn btn-primary"
                                        name="AddNoteToResource">add new answer</button>
                                    

                                </div>
                           
                    
                </div>
            </div>
        </jeuxTest>
    </form>    
    </div>
</div>