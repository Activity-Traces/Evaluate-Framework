<?php

namespace App;

use App\sqlRequests;
use \Datetime;

//*****************************************************************************************************************************
//*****************************************************************************************************************************

class Evaluate
{

    public $url;
    public $data;
    public $result;
    public $FrameworkUrlService;

    //*****************************************************************************************************************************

    public function initComputation($sourceTree, $TDF, $TFF, $TDC, $k1, $k2, $nbminrep, &$X, &$Y){
        // recalculer TDAX et TFAX en fonction du TDF et TFF

        $sql = new sqlRequests;

        $date1 = new DateTime($TDF);
        $t1 = $date1->getTimestamp();

        $date2 = new DateTime($TFF);
        $t2 = $date2->getTimestamp();

        if ($sourceTree != Null) {

            foreach ($sourceTree->objects as $node) {

                $moyenne = 0;
                $cf = 0;
                $nbrep = 0;

                if ((!empty($node->relations->hasTraining))) {

                    $vector = array();

                    foreach ($node->relations->hasTraining as $id) {
                        foreach ($sourceTree->resources as $resource) {
                            if ($resource->id == $id)
                                array_push($vector, "'" . $resource->name . "'");
                        }
                    }

                    $resourcesList = implode(",", $vector);


                    $sql->connect();

                    $sql->getUserAnswersByNode($resourcesList, $TDF, $TFF);
                    $result = $sql->getResult();

                    // trouver le temps du début d'activité et fin d'activité pour le groupe resourceLirs

                    $sql->getMinMaxResourcesTime($resourcesList, $TDF, $TFF);
                    $time = $sql->getResult();

                    $d = new DateTime($time[0]->TDA);
                    $TDAX = $d->format('U');

                    $d = new DateTime($time[0]->TFA);
                    $TFAX = $d->format('U');

                    if (!empty($result)) {

                        foreach ($result as $row) {

                            $date = new DateTime($row->temps);
                            $b = $date->getTimestamp();

                            $a =  $row->note / 100;
                            $c =  $t1;
                            $d =  $t2;

                            $moyenne += $a * ($b - $c) / ($d - $c);
                            $cf += ($b - $c) / ($d - $c);
                            $nbrep++;
                        }

                        // on calcule ici  le TC: 
                        if ($nbrep == 0)
                            $TC = 0;
                        else
                            $TC = round(
                                (($k1 * (min(1, $nbrep / $nbminrep)) + $k2 * (($TFAX - $TDAX) / ($TDC - $TDAX))) / ($k1 + $k2)),
                                2
                            );
                        //
                        $moyenne = round($moyenne / $cf, 2);
                        $EvalNode = array(
                            'Id' => $node->name,
                            'TM' => $moyenne,
                            'TC' => $TC,
                            'CV' => 1
                        );

                        if (
                            (empty($node->relations->isComposedOf)) and
                            (empty($node->relations->isSkillOf)) and
                            (empty($node->relations->isKnowledgeOf))
                        )

                            array_push($X, $EvalNode);
                        else
                            array_push($Y, $EvalNode);
                    }
                } else { // ici le bug: si le noeud ne containt pas des resources, mais il est dans la feuille? on ne peut pas le traiter?

                    $EvalNode = array(
                        'Id' => $node->name,
                        'TM' => 0,
                        'TC' => 0,
                        'CV' => 0
                    );

                    array_push($Y, $EvalNode);
                }
            }

            $EvalNode = array(
                'Id' => $sourceTree->frameworkName,
                'TM' => 0,
                'TC' => 0,
                'CV' => 0
            );

            array_push($Y, $EvalNode);
        }
    
        unset($sql, $date, $date1, $date2, $d);

    }

    //******************************************************************************************************************************/
    
    public function FindChildrens($sourceTree, $Node){

        $Childrens = array();
        foreach ($sourceTree->objects as $node) {
            if ($node->name == $Node['Id']) {
                if (!empty($node->relations->isComposedOf))
                    array_push($Childrens, $node->relations->isComposedOf);
                elseif (!empty($node->relations->hasSkill))
                    array_push($Childrens, $node->relations->hasSkill);
                elseif (!empty($node->relations->hasKnowledge))
                    array_push($Childrens, $Childrens = $node->relations->hasKnowledge);
                elseif (!empty($node->relations->comprises))
                    array_push($Childrens, $Childrens = $node->relations->comprises);
            }
        }
        return ($Childrens);
    }


    //******************************************************************************************************************************/

    public function AreChidrensInMyList($A, $L){
        foreach ($L as $Line) {

            for ($i = 0; $i < count($Line); $i++) {

                if (!in_array($Line[$i], $A, true))
                    return (false);
            }
        }
        return (true);
    }

    //******************************************************************************************************************************/
    
    public function UpadeNode(&$node, $L, $X, $k1, $k2, $checkedTM, $checkedTC, $checkedCV)
    {
        $countTM = 0;
        $countCV = 0;
        $countTC = 0;
        
        $val = 0;
        $val2 = 0;
        $val3 = 0;

        if (!empty($L)) {
            foreach ($L as $Line) {

                for ($i = 0; $i < count($Line); $i++) {
                    $n = $this->findNode($Line[$i],  $X);

                    $val += $n['TM'];
                    $val2 += $n['TC'];
                    $val3 += $n['CV'];

                    
                    // prendre seulement le traux de confiance >0
                    
                    if ($checkedTM)    {
                        if ($n['TC'] > 0) 
                            $countTM = $countTM + 1;
                    }
                    else
                        $countTM = $countTM + 1;

                        
                    if ($checkedTC)    {
                        if ($n['TC'] > 0) 
                            $countTC = $countTC + 1;
                    }
                    else
                        $countTC = $countTC + 1;

                    if ($checkedCV)    {
                        if ($n['TC'] > 0) 
                            $countCV = $countCV + 1;
                    }
                    else
                        $countCV = $countCV + 1;
    
                }
            }

            if ($node['TM'] == 0) {
                $node['TM'] = round($val / $countTM, 2);
                $node['TC'] = round($val2 / $countTC, 2);

                $node['CV'] = round($val3 / $countCV, 2);

            } else { // cas où le noeud contient déja un TM, TC et CV: combinaison entre valeurs A et B
                $node['TM'] = round((($node['TM'] * $k1) + (($val / $countTM) * $k2)) / ($k1 + $k2), 2);
                $node['TC'] = round(($node['TC'] + ($val2 / $countTC)) / 2, 2);
                $node['CV'] = round(($node['CV'] + ($val3 / $countCV)) / 2, 2);
            }
        }
    }

    //******************************************************************************************************************************/

    public function findNode($Name, $X)
    {

        foreach ($X as $XNode) {
            if ($Name == $XNode['Id'])
                return ($XNode);
        }
    }

    //******************************************************************************************************************************/

    public function findNodePosition($Name, $X)
    {
        $c = 0;
        foreach ($X as $XNode) {

            if ($Name == $XNode['Id'])
                return ($c);
            else
                $c++;
        }
    }

    //******************************************************************************************************************************/
    //******************************************************************************************************************************/

}
