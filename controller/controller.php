<?php

require "../vendor/autoload.php";

use App\Evaluate;
use App\Framework;
use App\sqlRequests;
use \DateTime;

session_start();


$sql = new sqlRequests;
$Framework_Request = new Framework;
$Evaluate_Request= new Evaluate;

//*****************************************************************************************************************************

// Demande de connexion depuis la page login.
    // -> Ouvrir une session sur  le serveur du framework avec les identifiants de la page login
    // -> Si on arrive à se connecter au serveur alors on fait un direct vers la page home sinon on reste sur le login

if ( (isset($_POST["username"])) and (isset($_POST["password"])) )  {
    

    $Framework_Request->setUrl("https://traffic.irit.fr/comper/repository/framework-management-api/login");
    $Framework_Request->setData('{"email" : "'.$_POST["username"].'" , "password" : "'.$_POST["password"].'"}');

    $Framework_Request->connect("POST");

    $result=json_decode($Framework_Request->getResult(), true);

    if (isset($result['id-session']))  {
        
        $idsession=$result['id-session'];
        session_start();

        $_SESSION['session'] = $idsession;
        $_SESSION['connect']='Go';
        
        $_SESSION['username'] =  $_POST["username"];
        $_SESSION['password'] =  $_POST["password"];
        header('Location: ../home.php');

    }
    else
        header('Location: ../templates/login.php');
    
}
//*****************************************************************************************************************************

    // Demande pour ajouter une nouvelle réponse dans table des réponses
                //(remplir cette table dans un prochain temps depuis le serveur XAPI: rajouter l'identifiant de l'apprenant dans la table)


if (isset($_POST['AddNoteToResource'])){

    $sql->addNewAnswer($_POST["resource"], $_POST["note"], $_POST["tnote"]);
    $sql->getUserAnswers();
    $_SESSION['answers'] = $sql->getResult();
    

    if (isset($_POST["viewanswers"])) // afficher les réponses dans la page des réponses simulés
        header('Location: ../templates/Answers.php');
    else
        header('Location: ../home.php?refid='.$_SESSION['refid']);

}

//*****************************************************************************************************************************

    // Supprimer une réponse 
if (isset($_POST['deleteanswer'])){
        if (isset($_POST['choix'])){
        $list=implode(',',$_POST['choix']);    
        $sql->deleteanswers($list);
    }
    header('Location: ../templates/Answers.php');

}

//*****************************************************************************************************************************

// Supprimer toutes les réponses
if (isset($_POST['deleteAllAnswers'])){
    
    $sql->deleteAllAnswers();
    header('Location: ../templates/Answers.php');

}


//*****************************************************************************************************************************
// Demande d'évaluation du profil

if (isset($_POST['startEvaluation'])){
    if (!isset($_SESSION['Framework'])){
        header('Location: ../home.php');
        exit;
    }

    
    //************************************************************
    // récupérer les variables du formulaire depuis la page home

    $time = new DateTime($_POST["tmin"]);
    $tmin= $time->format('Y-m-d H:i:s');

    $time = new DateTime($_POST["tmax"]);
    $tmax= $time->format('Y-m-d H:i:s');

    $time = new DateTime($_POST["tdc"]);
    $tstart=$time->format('U');
    

    $kA=$_POST["KA"];
    $kB=$_POST["KB"];

    $k1=$_POST["K1"];
    $k2=$_POST["K2"];

    $nbminrep=$_POST["nbminrep"];

    $checkedTM=true;        
    $checkedTC=true;
    $checkedCV=false;

    /***************************************************************************************************************************** */

    // récupérer toutes les réponses afin de les dessiner dans l'arboressance depuis la session
    
    $sql->getUserAnswersTimeFilter($tmin, $tmax);
    $_SESSION['answers'] = $sql->getResult();
    $Framework=  $_SESSION['Framework'];

    /***************************************************************************************************************************** */

    $X=array(); // X contient les noeud évalué
    $Y=array(); // Y contient les noeuds à évaluer
    $ChildrensId=array(); // vecteur temporaire qui sert à trouver les noeuds fils 

    // Trouver les noeuds qui n'ont pas de resources: ce sont les noeuds intermédiéres
    // construire X et Y

    $Evaluate_Request->initComputation($Framework,$tmin, $tmax, $tstart,$k1, $k2,$nbminrep, $X, $Y);

    // Lancer l'évaluation à partir de X: à chaque fois qu'un noeud de Y est évalué on le mets dans X et on le supprime
    // de Y jusqu'a ce que Y serait vide

    // Cet algorithme est donc la clé de  l'évaluation qui part des feuilles vers la raçine

    if (!empty($X)){
        do {
            foreach($Y as $node){
                $ChildrensId=array();

                foreach ($X as $nodeId)
                    array_push($ChildrensId, $nodeId['Id']);
            
                $List= $Evaluate_Request->FindChildrens($Framework, $node);
                $exist=$Evaluate_Request->AreChidrensInMyList($ChildrensId, $List);
                
                if ($exist) {

                    $Evaluate_Request->UpadeNode($node, $List, $X, $kA, $kB, $checkedTM, $checkedTC, $checkedCV);
                    array_push($X, $node);
                    $n= $Evaluate_Request->findNodePosition($node['Id'], $Y);
                    array_splice($Y, $n, 1);
                
                }  
            
            }
            $iSEmptyY=empty($Y);
        
        }while(!$iSEmptyY);   

        
        $_SESSION['results']=$X; 
    }


    $_SESSION['thisPost']=$_POST;
    unset($time);
    
    if (isset($_POST["viewinTree"]))
        header('Location: ../templates/TreeView.php?view=1');
    else
        header('Location: ../home.php?refid='.$_SESSION['refid']);



}



//***********************************************************************************************************************
//***********************************************************************************************************************

unset($sql);
unset($Framework_Request); 
unset($Evaluate_Request);



?>

