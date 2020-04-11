<?php
namespace App;

//***************************************************************************************************************************** */
//***************************************************************************************************************************** */

class sqlRequests
{

    public $user;
    private $link;
    public $request;
    public $result;

    //***************************************************************************************************************************** */

    public function connect(){

        try {
            $this->link = new \PDO(
                'mysql:host=localhost;dbname=askerdatabase',
                'root',
                '',
                array(
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_PERSISTENT => false
                )
            );
        } catch (\PDOException $ex) {
            print($ex->getMessage());
        }
    }

    //*****************************************************************************************************************************

    public function Execute(){
        try {

            $this->connect();
            $handle = $this->link->prepare($this->request);
            $handle->execute();

            $this->result = $handle->fetchAll(\PDO::FETCH_OBJ);
        } catch (\PDOException $ex) {
            print($ex->getMessage());
        }
    }

    //*****************************************************************************************************************************

    public function Update(){
        $this->connect();
        $handle = $this->link->prepare($this->request);
        $handle->execute();
    }

    //*****************************************************************************************************************************
    
    public function getResult(){
        return $this->result;
    }

    //*****************************************************************************************************************************
    
    public function getUserAnswers() {

        $this->request = "
            SELECT id, idresource, note, temps
            from resourcesnotes 
            ORDER BY temps ASC
    ";
        $this->Execute();
    }

    //*****************************************************************************************************************************
    
    public function getUserAnswersTimeFilter($tmin, $tmax){

        $this->request = "
            SELECT idresource, note, temps
            from resourcesnotes
            
            WHERE 
                (temps >='" . $tmin . "') 
            AND (temps <='" . $tmax . "') 


            ORDER BY temps ASC
        ";

        $this->Execute();
    }

    //*****************************************************************************************************************************

    public function getUserAnswersByNode($Mymodels, $tmin, $tmax){

        $this->request = "
            SELECT idresource, note, temps
            from resourcesnotes
            
            WHERE 
                (temps >='" . $tmin . "') 
            AND (temps <='" . $tmax . "') 
            
            AND (idresource in (" . $Mymodels . ")) 
                                
            ORDER BY temps ASC

        ";

        $this->Execute();
    }

    //*****************************************************************************************************************************

    public function getMinMaxResourcesTime($Mymodels, $tmin, $tmax){

        $this->request = "  select min(temps) as TDA, max(temps) as TFA 
                        from resourcesnotes
    
                        WHERE 
                        (temps >='" . $tmin . "') 
                        AND (temps <='" . $tmax . "') 
                        AND (idresource in (" . $Mymodels . ")) 
        ";

        $this->Execute();
    }

    //*****************************************************************************************************************************

    public function addNewAnswer($modelid, $note, $temps){

        $this->request =    "insert into `resourcesnotes` (idresource,note,temps)
                            values ('" . $modelid . "','" . $note . "','" . $temps . "')";

        $this->Update();
    }

    //*****************************************************************************************************************************

    public function deleteanswers($list){
        $this->request = "delete from resourcesnotes where id in(" . $list . ")";
        $this->Update();
    }
    //*****************************************************************************************************************************

    public function deleteAllAnswers(){

        $this->request = "truncate table resourcesnotes";
        $this->Update();

    }
    
//*****************************************************************************************************************************
//*****************************************************************************************************************************
}
