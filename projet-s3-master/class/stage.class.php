<?php

class Stage {

    private $id; //int
    private $supervisor_id; //int
    private $title; //string

    private function __construct(){
        
    }

    /**
     * function statique afficheStage
     * 
     * elle affiche les stages de la base de données
     * 
     * @return string code html sous forme de tableau
     */
    static public function afficheStage($id):string{
        $result;
        $result1;
        $stmt1;
        if($id != null){
            $stmt = MyPDO::getInstance()->prepare(<<<SQL
            select title, description, place, start_date, end_date, supervisor_id, id, student_id
            from internships
            where start_date >= NOW() && start_date < end_date
            And supervisor_id = :id
            and student_id is not null
SQL
            );
            $stmt->execute(array('id'=>$id));
            $result = $stmt->fetchAll();
            

            $stmt1 = MyPDO::getInstance()->prepare(<<<SQL
            select lastname, firstname
            from users
            where id =:id
SQL
            );
            
            
        }
        else{
            $stmt = MyPDO::getInstance()->prepare(<<<SQL
            select title, description, place, start_date, end_date, supervisor_id, id, student_id
            from internships
            where start_date >= NOW() && start_date < end_date
            order by id desc
SQL
        );
            $stmt->execute();
            $result = $stmt->fetchAll();
        }
        
        $res = "<div class='container mt-5 card p-5 border-dark'>";
        for($i = 0; $i < count($result); $i++){
            
            if(($result[$i]['student_id'] == null && $_SESSION['__user__']['page'] == 'stages') || $_SESSION['__user__']['page'] == 'mesStages'){
                $res .= <<<HTML
                    <div class="mb-5" style="font-size:0px;"id='{$result[$i]["id"]}'>{$result[$i]["id"]}</div>
                    <div class='row' >
                        <div class='col col-12' style="font-size:15px;">
                            <h3 class='font-weight-bold'>{$result[$i]['title']}</h3>
                            <h5 class='font-weight-bold'>Description</h5><div>{$result[$i]['description']}</div>
HTML
                ;
                if(date('d-m-Y', strtotime($result[$i]['start_date'])) > date('d-m-Y', strtotime($result[$i]['end_date']))){
                    $res .= '<script>window.alert("Erreur : Dates invalides")</script>';
                }
                elseif(date('d-m-Y', strtotime($result[$i]['start_date'])) < date('d-m-Y', strtotime($result[$i]['end_date']))){
                    $res .= "<h5 class='font-weight-bold'>Date</h5><div>Du ".date('d-m-Y', strtotime($result[$i]['start_date']))." au ".date('d-m-Y', strtotime($result[$i]['end_date']))."</div><h5 class='font-weight-bold'>Lieu</h5><div>{$result[$i]['place']}</div>";
                }
                if($id != null){
                    $stmt1->execute(array('id'=>$result[$i]['student_id']));
                    $result1 = $stmt1->fetch();
                    $res .= "<h5 class='font-weight-bold mt-1'>Votre stagiaire</h5><div>{$result1['lastname']} {$result1['firstname']}</div>";
                }
                if($result[$i]['supervisor_id'] == $_SESSION['__user__']['id']){
                    //$res .= "<div class='d-flex justify-content-end align-items-center'><button onclick='deleteStage(this.id)' class='mt-2 btn btn-danger btn-sm' name='delte' id='{$result[$i]['id']}'>Supprimer le stage</button></div>";
                    $res .= <<<HTML
                    <div class="d-flex justify-content-end align-items-center">
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteStage{$result[$i]['id']}" >Supprimer</button>
                        <div class="modal fade" id="deleteStage{$result[$i]['id']}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header text-center">
                                    <h5 class="modal-title w-100 font-weight-bold">Voulez-vous vraiment supprimer ce stage ?</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col"><button onclick='deleteStage(this.id)' class='btn btn-primary btn-block' name='delte' id='{$result[$i]["id"]}'>Oui</button></div>
                                            <div class="col"><button class='btn btn-danger btn-block' data-dismiss="modal" aria-label="Close" name='notDelete' >Non</button></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
HTML
                    ;
                }
                $idStage = $result[$i]['id'];
                $idStudient = $_SESSION['__user__']['id'];
                if($_SESSION['__user__']['role'] == 'student'){
                    $res .=<<<HTML
                        <div class="d-flex justify-content-end align-items-center">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#form$idStage" >Postuler</button>
                        <div class="modal fade" id="form$idStage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header text-center">
                                        <h4 class="modal-title w-100 font-weight-bold">Postuler au stage</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                
                                <div class="modal-body">
                                    <form name="formPostuler" enctype="multipart/form-data" action="stages.php" method='POST'>
                                        
                                        <h6>Insérer CV:</h6>
                                        <input class="mb-3" type="file"  name="cv" accept=".pdf">
                                        <h6>Insérer lettre de motivation:</h6>
                                        <input class="mb-3" type="file" name="lm" accept=".pdf">
                                        
                                        <input type='submit' onclick="demande($idStudient,$idStage)" class="mt-2 btn btn-primary btn btn-block"  value="Envoyer" required>
                                    </form>
                                </div>
                            </div>
                            </div>
                        </div>
                        </div>
HTML
                    ;
                
                }
                
                $res .="</div></div>";
                
                if($i < count($result)-1){
                    $res .=<<<HTML
                        <div class='row'>
                            <div class='col col-12'>
                                <hr class='bg-dark'>
                            </div>
                        </div>
HTML
                    ;
                }
            }
            
            }
        if($result == null){
            $res .= "<p class='text-center'>Vous n'avez pas encore accepter de stagiaire pour vos stages.</p>";
        }
        $res .= "</div><div class='pb-5'></div>";
            
        return $res;

    }

    /**
     * function statique insertStage
     * 
     * elle insert les stages dans la base de données
     * 
     * @param string $title
     *  titre du stage
     * @param string $desc
     *  description du stage
     * @param string $start
     *  date du début du stage
     * @param string $end
     *  date de la fin du stage
     * @param string $place
     *  lieu du stage
     * @param string $supervisor_id
     *  id de la personne voulant ajouter un stage
     * @return void rien
     */
    static public function insertStage($title, $desc, $start, $end, $place, $supervisor_id):void {
       
        $stmt = MyPDO::getInstance()->prepare(<<<SQL
        INSERT INTO internships (title, description, start_date, end_date, place, supervisor_id) VALUES (:title, :desc, STR_TO_DATE(:start,'%Y-%m-%d'), STR_TO_DATE(:end,'%Y-%m-%d'), :place, :supervisor_id)
SQL
        );
        $stmt->execute(array('title'=>$title, 'desc'=>$desc, 'start'=>$start, 'end'=>$end, 'place'=>$place, 'supervisor_id'=>$supervisor_id));

    }

    static public function idMax():int{
        $stmt2 = MyPDO::getInstance()->prepare(<<<SQL
        SELECT MAX(id)
        FROM internships
SQL
        );
        $stmt2->execute();
        $id = $stmt2->fetch();
        $id1 = intval($id['MAX(id)']);
        return $id1;
    }
}


