<?php

namespace App;

//*****************************************************************************************************************************
//*****************************************************************************************************************************
class plotd3TreeView{


    //*****************************************************************************************************************************

    public function  deleteExistingNodes($haystack = array(), $needles = array()){
        $result=null;
    
        foreach ($haystack as $row) {
            $key = implode('', array_intersect_key($row, array_flip($needles)));  
    
            if (!isset($result[$key])) {
                $result[$key] = $row;
            } 
        }
        return array_values($result);
    }
    
    //*****************************************************************************************************************************
            
    function fromArrayToTree($items){

        $childs = [];

        foreach ($items as &$item)
            if ($item['hide'] != true)
                $childs[$item['parent']][] = &$item;
        unset($item);

        foreach ($items as &$item)
            if (isset($childs[$item['name']]))
                if ($item['hide'] != true)
                    $item['children'] = $childs[$item['name']];

        return $item;
    }

    //*****************************************************************************************************************************
    // ajouter les valeurs calculés aux noeuds

    public function addValuesToNodes($X, &$tree){

        foreach ($X as $node) {

            for ($i = 0; $i < count($tree); $i++) {

                if ($node['Id'] == $tree[$i]['name']) {

                    if ($node['TM'] >= 0.5)
                        $color = 'Green';
                    else
                        $color = 'Red';

                    $tree[$i]['name'] = $node['Id'] . '(' . $node['TM'] . ':' . $node['TC'] . ':' . $node['CV'] . ')';
                    $tree[$i]['color'] = $color;
                }

                if ($node['Id'] == $tree[$i]['parent']) {
                    $tree[$i]['parent'] = $node['Id'] . '(' . $node['TM'] . ':' . $node['TC'] . ':' . $node['CV'] . ')';
                }
            }
        }

        
        for ($i = 0; $i < count($tree); $i++) {

            if ($tree[$i]['type']=='answer'){
                $mark=explode('_',$tree[$i]['name']);
                if ($mark[0] >= 50)
                    $color = 'Green';
                else
                    $color = 'Red';
                $tree[$i]['color'] = $color;
            }
        }
        
    }

    //*****************************************************************************************************************************

}
