<?php
namespace App;

//*****************************************************************************************************************************
//*****************************************************************************************************************************

class Tree
{
    private $answers;
    private $marks;
    private $treeview;
    private $simpleTreeview;
    
    //******************************************************************************************************************************

    public function __construct(){
        $this->treeview ='[{ "id" : "none", "parent" : "#", "text" : "/" }]';
        $this->answers=array();
        $this->marks=array();
        $this->simpleTreeview=array();
        
    }

    //******************************************************************************************************************************

    public function getTreeView(){
        return $this->treeview;

    }
 
    //*****************************************************************************************************************************

    public function createnode($id, $text, $parent, $icon, &$Tree, $mode){
        if ($mode==1)
            $id=rand() . ':' .$id;

        $MyNode = array(
            'id' => $id,
            'parent' => $parent,
            'text' => $text,
            'icon' => $icon
        );
        array_push($Tree, $MyNode);
        
    }

    //******************************************************************************************************************************

    public function findNode($Name, $X){

        foreach ($X as $XNode) {
            if ($Name == $XNode['Id'])
                return ($XNode);
        }
    }

    //******************************************************************************************************************************

    public function filter($str, $charset='utf-8') {

	    $str = htmlentities($str, ENT_NOQUOTES, $charset);
	    $str = preg_replace('#\&([A-za-z])(?:acute|cedil|circ|grave|ring|tilde|uml)\;#', '\1', $str);
	    $str = preg_replace('#\&([A-za-z]{2})(?:lig)\;#', '\1', $str); // pour les ligatures e.g. '&oelig;'
	    $str = preg_replace('#\&[^;]+\;#', '', $str); // supprime les autres caractères
	    $str = preg_replace('@[^a-zA-Z0-9_]@','',$str);
	    return $str;
    }
    
    //*****************************************************************************************************************************
    
    public function createTree($node, $relation, &$Destination, &$SimpleTree, $icon, $mode){

        if (isset($relation)) {

            foreach ($relation as $RelationEelement) {
                $this->createnode($node->name, $node->name, $RelationEelement, $icon, $Destination, $mode);
             
                    $SimpleTree[]=[ 
                        'name' => $this->filter($node->name, 'utf-8'), 
                        'parent' => $this->filter($RelationEelement, 'utf-8'), 
                        'color'=>'', 'hide'=>false, 
                        'type'=>$node->type
                    ];
    
            }
        }

    }

    //*****************************************************************************************************************************
    
    public function frameworkToTreeView($Source, &$SimpleTree, $answers, $TreeMarks){

        if ($Source != Null) {

            $Destination = array();            

            $this->createnode($Source->frameworkName, $Source->frameworkName, "#", $icon= 'fas fa-book text-danger', $Destination, 0);

            $icon = 'fas fa-registered';
            foreach ($Source->resources as $nodeR) 
                $this->createTree($nodeR, $nodeR->relations->isTrainingOf, $Destination, $SimpleTree, $icon, 1);
    
            if (!empty($answers)) 
                $this->addAnswersToTreeView($Destination, $SimpleTree, $answers);
            
            foreach ($Source->objects as $node) {
          
                if ($node->type == 'Competency')
                    $icon = 'fab fa-cuttlefish text-warning';

                if ($node->type == 'Skills')
                    $icon = 'fab fa-stripe-s text-primary';

                if ($node->type == 'Knowledge')
                    $icon = 'fab fa-kickstarter-k text-success';

                 $this->createTree($node, $node->relations->composes, $Destination, $SimpleTree,  $icon, 0);
                 $this->createTree($node, $node->relations->isKnowledgeOf, $Destination, $SimpleTree, $icon, 0);
                 $this->createTree($node, $node->relations->isSkillOf, $Destination, $SimpleTree, $icon, 0);
                 $this->createTree($node, $node->relations->isComprisedIn, $Destination, $SimpleTree, $icon, 0);

            }

            $this->Marks=$TreeMarks;

            if (isset($this->Marks)){
                $X=$this->Marks;

                for ($i=0; $i<count($Destination); $i++){
                    $n= $this->findNode($Destination[$i]['id'], $X);
                    if (isset($n))
                        $Destination[$i]['text'].='&nbsp;<b>(TM:'.$n['TM'].', TC: '.$n['TC'].', CV: '.$n['CV'].')<b>';
                       
                }
            }
            
            return (json_encode($Destination));
        }
    }

    //*****************************************************************************************************************************

     public function addAnswersToTreeView(&$Destination, &$SimpleTree, $answers){
            
        $this->answers=$answers;

        foreach ($this->answers as $row) {

            foreach ($Destination as $FindNode) {
                $id = $FindNode["text"];
                if (isset($id)) {

                    if ($id == $row->idresource) {
                        $parent = $FindNode["id"];
                        if ($row->note >= 50)
                            $icon = 'fas fa-smile-beam text-success';
                        else
                            $icon = 'fas fa-frown text-danger';

                        $text= $row->note . '_' . $row->temps;
                        $rand=rand();
                        $this->createnode($rand, $text, $parent, $icon, $Destination, 0);
                        $parentName=explode(':', $parent);

                        $SimpleTree[]=['name'=>$text, 'parent'=>$parentName[1], 'color'=>'', 'hide'=>false, 'type'=>'answer'];

                    }
                }
            }
        }
    
    }

//*****************************************************************************************************************************
//*****************************************************************************************************************************

}
