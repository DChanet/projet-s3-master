<?php
require_once('../autoload.php') ;

//création page web

$p = new WebPage('monStage') ;

//implementation de Bootstrap

$p->appendBootstrap();
$p->appendJsUrl('../js/ajaxrequest.js');
$user = User::createFromSession();
$_SESSION['__user__']['page'] = 'monStage';
$p->appendNavbar();
//récuperation du role et de l'id de l'utilisateur connecté
$p->appendJs(<<<JAVASCRIPT
    function desist(id)
        {
            var stage = id;
            console.log(stage);
            //requetes ajax

            new AjaxRequest(
            {
                asynchronous : true,
                url: '../script/desist.php',
                method: 'POST',
                parameters: {idStage: stage},
                onSuccess : function(){
                    window.location.reload(false); 
                },
                onError : function(){
                    alert('Problème requete ajax deleteAlert non validée');
                }
            });
            
            
        }
JAVASCRIPT
    );
$profilInfo = $user->profil();

$_SESSION['__user__']['id'] = $profilInfo[11];
$stmt1 = MyPDO::getInstance()->prepare(<<<SQL
    select title, description, place, DATE_FORMAT(start_date, "%d-%m-%Y") as start_date, DATE_FORMAT(end_date, "%d-%m-%Y") as end_date, supervisor_id, id, student_id
    from internships
    where student_id = :id

SQL
        );
$stmt1->execute(array('id'=>$_SESSION['__user__']['id']));

$stmt2 = MyPDO::getInstance()->prepare(<<<SQL
    select lastname, firstname
    from users
    where id = :id

SQL
        );


$res = $stmt1->fetch();

$stmt2->execute(array('id'=>$res['supervisor_id']));

$res1 = $stmt2->fetch();

if($res['title'] != null){

    $p->appendContent(<<<HTML
    <div class="container mb-5" style="margin-top:100px;">
    <div class="card border-dark p-3">
        <div class="text-center" style="font-size:30px;">Votre stage</div>
        <div class="row mt-5 pt-5">
            <div class="col-5">Titre :</div>
            <div class="col-7"> {$res['title']}</div>
        </div>
        <hr class="bg-dark">
        <div class="row">
            <div class="col-5">Description :</div>
            <div class="col-7"> {$res['description']}</div>
        </div>
        <hr class="bg-dark">
        <div class="row">
            <div class="col-5">Lieu :</div>
            <div class="col-7"> {$res['place']}</div>
        </div>
        <hr class="bg-dark">
        <div class="row">
            <div class="col-5">Dates :</div>
            <div class="col-7">Du  {$res['start_date']} au  {$res['end_date']}</div>
        </div>
        <hr class="bg-dark">
        <div class="row">
            <div class="col-5">Lieu :</div>
            <div class="col-7"> {$res['place']}</div>
        </div>
        <hr class="bg-dark">
        <div class="row mb-2">
            <div class="col-5">Maître de stage :</div>
            <div class="col-7"> {$res1['lastname']} {$res1['firstname']}</div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="d-flex justify-content-end align-items-center col-6">
                <button type="button" class="btn btn-primary btn-block p-3" data-toggle="modal" data-target="#form" >Documents soutenance</button>
            <div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header text-center">
                            <h4 class="modal-title w-100 font-weight-bold">Dépôt des documents</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    
                    <div class="modal-body">
                        <form name="formPostuler" enctype="multipart/form-data"  method='POST'>
                            
                            <h6>Insérer votre Diaporama:</h6>
                            <input class="mb-3" type="file"  name="cv" accept=".pdf">
                            <h6>Insérer votre rapport de stage:</h6>
                            <input class="mb-3" type="file" name="lm" accept=".pdf">
                            
                            <input type='submit' class="mt-2 btn btn-primary btn btn-block"   value="Déposer" required>
                        </form>
                    </div>
                </div>
                </div>
                </div>
        </div>
        

        <div class="d-flex justify-content-end align-items-center col-6">
            <button type="button" class="btn btn-danger btn-block p-3" data-toggle="modal" data-target="#desist" >Se désister</button>
            <div class="modal fade" id="desist" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header text-center">
                        <h5 class="modal-title w-100 font-weight-bold">Voulez-vous vraiment vous désister ?</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col"><button onclick="desist({$res['id']})" class='btn btn-primary btn-block' name='delte'>Oui</button></div>
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
    );
    
}
else{
    $p->appendContent(<<<HTML
        <div class="text-center" style="font-size:30px;margin-top: 200px;">Vous n'avez pas encore de stage pour le moment.</div>
HTML
        );
}

echo $p->toHTML();