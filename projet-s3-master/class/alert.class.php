<?php

class alert {
    
    private $id; //int
    private $title; //string
    private $text; //string
    private $id_users; //int

    private function __construct(int $id, string $title, string $text, int $id_users)
	{
        $this->id = $id;
        $this->title = $title;      
        $this->text = $text;      
        $this->id_users = $id_users;
      
    }
    
    public function createById(int $id):self
    {
        $stmt = MyPDO::getInstance()->prepare(<<<SQL
            SELECT id, title, text, id_users
            FROM alerts
            WHERE id = :id
SQL
            );
        $stmt->execute(array('id'=> $id));
        $sql = $stmt->fetch();
        
        $res = new alert($id, $sql[1], $sql[2], $sql[3]);
        return $res;
    }


    static public function afficheAlertStage(int $id_users):string
    {
        $res = "";
        $stmt = MyPDO::getInstance()->prepare(<<<SQL
        SELECT title, text , id_stage, id, type, expediteur
        FROM alerts
        WHERE id_users = :id_users
SQL
        );
        $stmt->execute(array('id_users'=> $id_users));
        $sql = $stmt->fetchAll();
        for($i=0;$i < count($sql);$i++ ){
            $title = $sql[$i]['title'];
            $text = $sql[$i]['text'];
            $etudiant = $sql[$i]['expediteur'];
            if($sql[$i]['type'] == 'demande'){

                $res .= <<<HTML
                <div class="row m-2">
                    <div class="col">
                        <h4>$title</h4>
                        <div>$text</div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-success m-1" data-toggle="modal" data-target="#accepter{$sql[$i]['id']}" >Accepter</button>
                            <button type="button" class="btn btn-danger m-1" data-toggle="modal" data-target="#refuser{$sql[$i]['id']}" >Refuser</button>
                        </div>
                        <div class="d-flex justify-content-end align-items-center">
                            
                            <div class="modal fade" id="accepter{$sql[$i]['id']}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header text-center">
                                            <h5 class="modal-title w-100 font-weight-bold">Voulez-vous vraiment accepter ?</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                                <div class="row ">
                                                    <div class="col"><button onclick="accept({$sql[$i]['id_stage']}, $etudiant, {$sql[$i]['id']})" class='btn btn-primary btn-block' name='delte' id='{}'>Oui</button></div>
                                                    <div class="col"><button class='btn btn-danger btn-block' data-dismiss="modal" aria-label="Close" name='notDelete' >Non</button></div>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="refuser{$sql[$i]['id']}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header text-center">
                                        <h5 class="modal-title w-100 font-weight-bold">Voulez-vous vraiment refuser ?</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    <div class="modal-body">
                                        <div class="row ">
                                            <div class="col"><button onclick="deleteAlert(this.id);send($etudiant, {$sql[$i]['id_stage']})" class='btn btn-primary btn-block' name='delte' id='{$sql[$i]["id"]}'>Oui</button></div>
                                            <div class="col"><button class='btn btn-danger btn-block' data-dismiss="modal" aria-label="Close" name='notDelete' >Non</button></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                        </div>
                    </div>
                
HTML
                ;

            }
            elseif($sql[$i]['type'] == 'accepter'){
                $res .= <<<HTML
            <div class="row m-2">
                <div class="col">
                    <h4>$title</h4>
                    <div>$text</div>
                    <div class="d-flex justify-content-end">
                        <a class="btn btn-primary m-1" href="monStage.php" role="button">voir</a>
                        <button type="button" class="btn btn-danger m-1" data-toggle="modal" data-target="#deleteAlert{$sql[$i]['id']}" >Supprimer</button>
                    </div>
                    <div class="d-flex justify-content-end align-items-center">
                        
                    <div class="modal fade" id="deleteAlert{$sql[$i]['id']}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header text-center">
                                <h5 class="modal-title w-100 font-weight-bold">Voulez-vous vraiment supprimer cette notification ?</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <div class="modal-body">
                                    <div class="row ">
                                        <div class="col"><button onclick='deleteAlert(this.id)' class='btn btn-primary btn-block' name='delte' id='{$sql[$i]["id"]}'>Oui</button></div>
                                        <div class="col"><button class='btn btn-danger btn-block' data-dismiss="modal" aria-label="Close" name='notDelete' >Non</button></div>
                                    </div>
                            </div>
                        </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
            
HTML
            ;
            }
            elseif($sql[$i]['type'] == 'refuser'){
                $res .= <<<HTML
            <div class="row m-2">
                <div class="col">
                    <h4>$title</h4>
                    <div>$text</div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-danger m-1" data-toggle="modal" data-target="#deleteAlert{$sql[$i]['id']}" >Supprimer</button>
                    </div>
                    <div class="d-flex justify-content-end align-items-center">
                        
                    <div class="modal fade" id="deleteAlert{$sql[$i]['id']}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header text-center">
                                <h5 class="modal-title w-100 font-weight-bold">Voulez-vous vraiment supprimer cette notification ?</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <div class="modal-body">
                                    <div class="row ">
                                        <div class="col"><button onclick='deleteAlert(this.id)' class='btn btn-primary btn-block' name='delte' id='{$sql[$i]["id"]}'>Oui</button></div>
                                        <div class="col"><button class='btn btn-danger btn-block' data-dismiss="modal" aria-label="Close" name='notDelete' >Non</button></div>
                                    </div>
                            </div>
                        </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
            
HTML
            ;
            }
            else{
                $res .= <<<HTML
            <div class="row m-2">
                <div class="col">
                    <h4>$title</h4>
                    <div>$text</div>
                    <div class="d-flex justify-content-end">
                        <a class="btn btn-primary m-1" href="stages#{$sql[$i]['id_stage']}" role="button">Consulter</a>
                        <button type="button" class="btn btn-danger m-1" data-toggle="modal" data-target="#deleteAlert{$sql[$i]['id']}" >Supprimer</button>
                    </div>
                    <div class="d-flex justify-content-end align-items-center">
                        
                    <div class="modal fade" id="deleteAlert{$sql[$i]['id']}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header text-center">
                                <h5 class="modal-title w-100 font-weight-bold">Voulez-vous vraiment supprimer cette notification ?</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <div class="modal-body">
                                    <div class="row ">
                                        <div class="col"><button onclick='deleteAlert(this.id)' class='btn btn-primary btn-block' name='delte' id='{$sql[$i]["id"]}'>Oui</button></div>
                                        <div class="col"><button class='btn btn-danger btn-block' data-dismiss="modal" aria-label="Close" name='notDelete' >Non</button></div>
                                    </div>
                            </div>
                        </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
            
HTML
            ;
            }
            
            if($i < count($sql)-1){
                $res .=<<<HTML
                    <div class='row'>
                        <div class='col col-12'>
                            <hr class='bg-light'>
                        </div>
                    </div>
HTML
            ;
            }
        }
        return $res;
    }

    static public function insertNewAlertStage(int $id_users, int $idStage):void{
        $stmt1 = MyPDO::getInstance()->prepare(<<<SQL
        SELECT id
        FROM users
        WHERE role != compagny
        AND id != :id_users
SQL
        );
        $stmt1->execute(array('id_users'=>$id_users));
        $result1 = $stmt1->fetchAll();

        $stmt2 = MyPDO::getInstance()->prepare(<<<SQL
        SELECT title
        FROM internships
        WHERE id = :id
SQL
        );
        $stmt2->execute(array('id'=>$idStage));
        $res = $stmt2->fetch();
        $title = $res['title'];

        
        $stmt3 = MyPDO::getInstance()->prepare(<<<SQL
        INSERT INTO alerts (title, text, id_users, id_stage) VALUES (:title, :text, :id_users, :id_stage)
SQL
        );
        for($i=0;$i<count($result1);$i++){
            $stmt3->execute(array('title'=>'Nouveau Stage !', 'text'=>$title, 'id_users'=>$result1[$i]['id'], 'id_stage'=>$idStage));
        }
        
    }

}

