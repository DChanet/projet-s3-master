<?php
require_once('../autoload.php') ;

//création page web

$p = new WebPage('stages') ;

//implementation de Bootstrap

$p->appendBootstrap();

//implementation de ajax

$p->appendJsUrl('../js/ajaxrequest.js');

//fonction JS appelée au clique de l'utilisateur sur le bouton de suppression

$p->appendJs(<<<JAVASCRIPT
    function deleteStage(id)
        {
            var idStage = id;
            console.log(idStage);
            //requetes ajax

            new AjaxRequest(
            {
                asynchronous : true,
                url: '../script/deleteStage.php',
                method: 'POST',
                parameters: {id1: idStage},
                onSuccess : function(){
                    window.location.reload(false); 
                },
                onError : function(){
                    alert('Problème requete ajax Stage non validée');
                }
            });
            
            
        }

        function demande(student, stage)
        {
            var idstudent = student;
            var idstage = stage;
            console.log(idstudent);
            console.log(idstage);
            //requetes ajax

            new AjaxRequest(
            {
                asynchronous : true,
                url: '../script/demande.php',
                method: 'POST',
                parameters: {student: idstudent, stage: idstage},
                onSuccess : function(){
                    window.location.reload(false);
                },
                onError : function(){
                    alert('Problème requete ajax demande non validée');
                }
            });
            
            
        }

JAVASCRIPT
    );


$user = User::createFromSession();

//evite l'ajout du stage a chaque refresh de la page si l'on vient de le faire

if (!empty($_POST)) {
    header("Location: stages.php");
}

//récuperation du role et de l'id de l'utilisateur connecté

$profilInfo = $user->profil();
$role = $profilInfo[10];
$_SESSION['__user__']['id'] = $profilInfo[11];

//insertion du stage si le $_POST en contient un

if(isset($_POST['title']) && null != $_POST['desc'] && null != $_POST['start_date'] && null != $_POST['end_date'] && null != $_POST['place']){
    Stage::insertStage($_POST['title'], $_POST['desc'], $_POST['start_date'], $_POST['end_date'], $_POST['place'], $profilInfo[11]);
    $idStage = Stage::idMax();
    alert::insertNewAlertStage($profilInfo[11],$idStage);
    mkdir("../upload/stages/$idStage", 0700);
    
}
//permet d'avoir la page courante en gras dans la navbar

$_SESSION['__user__']['page'] = 'stages';

//implementation de la navbar

$p->appendNavbar();

if($_FILES != null){

    $dossier = '../upload/stages/';

    $fichier1 = basename($_FILES['lm']['name']);
    $fichier2 = basename($_FILES['cv']['name']);

    $taille_maxi = 100000;

    $taille1 = filesize($_FILES['lm']['tmp_name']);
    $taille2 = filesize($_FILES['cv']['tmp_name']);


    //Début des vérifications de sécurité...

    if($taille1 > $taille_maxi || $taille2 > $taille_maxi)
    {
        //var_dump('Le fichier est trop gros...');
    }
    else{
        //var_dump('nickel');
    }
    if(!isset($erreur)) //S'il n'y a pas d'erreur, on upload
    {
            //On formate le nom du fichier ici...
            $fichier1 = strtr($fichier1, 
                'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 
                'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
            $fichier1 = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier1);
            move_uploaded_file($_FILES['lm']['tmp_name'], $dossier . $fichier1);

            $fichier2 = strtr($fichier2, 
                'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 
                'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
            $fichier2 = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier2);
            move_uploaded_file($_FILES['cv']['tmp_name'], $dossier . $fichier2);

    }
}
//affiche différent selon chaque role (ajout ou retire des foncionnalités en fonction)

    switch($role){
    case 'student':

        $p->appendContent(<<<HTML
            <div class="p-5" style="font-size:0px;">.<div>
HTML
        );
        break;


    case 'company':

        //bouton permettant l'accès au formulaire + formulaire ajout de stage

        $p->appendContent(<<<HTML
                <div class="d-flex justify-content-end align-items-center mt-5 pt-5">
                    <div class="container">
                        <button type="button" class="btn btn-primary btn-block btn-lg mb-5 p-5" data-toggle="modal" data-target="#modalLoginForm"><div style="font-size:50px;"><i class="fas fa-plus"></div></i>Ajouter un stage</button>
                    </div>
                        <div class="modal fade" id="modalLoginForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                <div class="modal-header text-center">
                                    <h4 class="modal-title w-100 font-weight-bold">Ajout de stage</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form action='stages.php' method='POST'>
                                    <div>Titre:</div>
                                            <input type ="text"  class="form-control" maxlength="100" required name='title'>
                                            <small class="text-muted">
                                                100 caractères max.
                                            </small>
                                        <div>Description:</div>
                                            <input type ="text" class="form-control" maxlength="1500" required name='desc'>
                                            <small class="text-muted">
                                                1500 caractères max.
                                            </small>
                                        <div>Date de début:</div>
                                            <input required name='start_date' type="date" class="form-control">
                                        <div>Date de fin:</div>
                                            <input required name='end_date' type="date" class="form-control">
                                        <div>Lieu:</div>
                                            <input maxlength="200" required name='place' class="form-control">
                                        <input type='submit' class="mt-2 btn btn-primary btn btn-block" name="add" value="Ajouter">
                                    </form>
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

         break;


    case 'teacher':
        //bouton permettant l'accès au formulaire + formulaire ajout de stage

        $p->appendContent(<<<HTML
                <div class="d-flex justify-content-end align-items-center mt-5 pt-5">
                    <div class="container">
                        <button type="button" class="btn btn-primary btn-block btn-lg mb-5 p-5" data-toggle="modal" data-target="#modalLoginForm"><div style="font-size:50px;"><i class="fas fa-plus"></div></i>Ajouter un stage</button>
                    </div>
                        <div class="modal fade" id="modalLoginForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                <div class="modal-header text-center">
                                    <h4 class="modal-title w-100 font-weight-bold">Ajout de stage</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form action='stages' method='POST'>
                                    <div>Titre:</div>
                                            <input type ="text"  class="form-control" maxlength="100" required name='title'>
                                            <small class="text-muted">
                                                100 caractères max.
                                            </small>
                                        <div>Description:</div>
                                            <input type ="text" class="form-control" maxlength="1500" required name='desc'>
                                            <small class="text-muted">
                                                1500 caractères max.
                                            </small>
                                        <div>Date de début:</div>
                                            <input required name='start_date' type="date" class="form-control">
                                        <div>Date de fin:</div>
                                            <input required name='end_date' type="date" class="form-control">
                                        <div>Lieu:</div>
                                            <input maxlength="200" required name='place' class="form-control">
                                        <input type='submit' class="mt-2 btn btn-primary btn btn-block" name="add" value="Ajouter">
                                    </form>
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
        break;

}



//affichage de la liste des stages

$p->appendContent(Stage::afficheStage(null));

// Envoi du code HTML au navigateur du client

echo $p->toHTML() ;
