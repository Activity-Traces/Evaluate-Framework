<?php

namespace App;

use App\Tree;
use App\Framework;

/*********************************************************************************************************************** */
/*********************************************************************************************************************** */

class initHome
{

    public $privateFrameworks;
    public $publicFrameworks;
    public $resources;
    public $treeview;

    /*********************************************************************************************************************** */

    public function openSession() {

        session_start();
        
        if ((!isset($_SESSION['connect']) and ($_SESSION['connect'] != 'Go') )

            or (isset($_SESSION["Framework"]) and ($_SESSION["Framework"]=="Expired session, Pleased login again !") ) ){
            var_dump($_SESSION["Framework"]);
            session_unset();
            session_destroy();
            header('location: templates/login.php');
            exit;
            
        }
    }

    /*********************************************************************************************************************** */

    public function init(){

        $this->openSession();

        $Tree_Request = new Tree;
        $Framework_Request = new Framework;

        $this->treeview = $Tree_Request->getTreeView();
        $Framework_Request->setIdSession($_SESSION['session']);

        $Framework_Request->FrameworksList(0);
        $this->privateFrameworks = $Framework_Request->getFrameworksList();

        $Framework_Request->FrameworksList(1);
        $this->publicFrameworks = $Framework_Request->getFrameworksList();

        if (isset($_GET["refid"])) {

            $TreeMarks = array();
            $answers = array();
            $Framework_Request->setFrameworkId($_GET["refid"]);
            $Framework_Request->FrameworkContent();
            $Source =  $Framework_Request->getFrameworksContent();
            if (isset($_SESSION['answers']))
                $answers = $_SESSION['answers'];
            if (isset($_SESSION['results']))
                $TreeMarks = $_SESSION['results'];
            $this->treeview = $Tree_Request->frameworkToTreeView($Source, $SimpleTree, $answers, $TreeMarks);
            $_SESSION['Framework'] = $Source;
            $_SESSION['tree'] = $SimpleTree;
            $_SESSION['refid'] = $_GET["refid"];
            $Framework_Request->getResourcesList($Source);
            $this->resources =   $Framework_Request->getResources();
        }

        unset($Tree_Request, $Framework_Request);
        
    }

    /*********************************************************************************************************************** */

    public function getLastConfigurationValues(
        &$nbminrep,
        &$tmin,
        &$tmax,
        &$tdc,
        &$CM,
        &$MC,
        &$PI,
        &$OEQ,
        &$OI,
        &$KA,
        &$KB,
        &$K1,
        &$K2,
        &$TM,
        &$TC,
        &$CV) {

        $nbminrep = "2";
        $tmin = "2018-09-01T00:00:00";
        $tmax = "2019-09-01T00:00:00";
        $tdc = "2019-05-01T00:00:00";

        $CM = "1";
        $MC = "1";
        $PI = "1";
        $OEQ = "1";
        $OI = "1";

        $KA = "1.2";
        $KB = "0.8";
        $K1 = "1.2";
        $K2 = "0.8";
        $TM = 1;
        $TC = 1;
        $CV = 1;



        if (isset($_SESSION['thisPost'])) {

            $post = $_SESSION['thisPost'];
            $nbminrep = $post["nbminrep"];
            $tmin = $post["tmin"];
            $tmax = $post["tmax"];
            $tdc = $post["tdc"];
            $CM = $post["CM"];
            $MC = $post["MC"];
            $PI = $post["PI"];
            $OEQ = $post["OEQ"];
            $OI = $post["OI"];
            $KA = $post["KA"];
            $KB = $post["KB"];
            $K1 = $post["K1"];
            $K2 = $post["K2"];
            $TM = isset($post["TM"]);
            $TC = isset($post["TC"]);
            $CV = isset($post["CV"]);
        }
    }

   
/*********************************************************************************************************************** */
/*********************************************************************************************************************** */

}
