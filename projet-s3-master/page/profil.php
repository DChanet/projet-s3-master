<?php
require_once('../autoload.php') ;
$p = new WebPage("profil");

$p->appendBootstrap();
$p->appendJsUrl('../js/ajaxrequest.js');
$p->appendJs(<<<JAVASCRIPT
    function deleteAlert(id)
        {
            var idAlert = id;
            console.log(idAlert);
            //requetes ajax

            new AjaxRequest(
            {
                asynchronous : true,
                url: '../script/deleteAlert.php',
                method: 'POST',
                parameters: {id1: idAlert},
                onSuccess : function(){
                    window.location.reload(false); 
                },
                onError : function(){
                    alert('Problème requete ajax deleteAlert non validée');
                }
            });
            
            
        }

        function accept(id, studient, idAlert)
        {
            var idStage = id;
            var idstudient = studient;
            console.log("idStage : "+idStage);
            console.log("idStudent : "+idstudient);
            console.log("idAlert : "+idAlert);
            //requetes ajax

            new AjaxRequest(
            {
                asynchronous : true,
                url: '../script/accepterStagiaire.php',
                method: 'POST',
                parameters: {idStage: idStage, idStudent: idstudient, idAlert: idAlert},
                onSuccess : function(){
                    window.location.reload(false); 
                },
                onError : function(){
                    alert('Problème requete ajax accept non validée');
                }
            });
            
            
        }

        function send(idStudent, idStage)
        {

            //requetes ajax

            new AjaxRequest(
            {
                asynchronous : true,
                url: '../script/refuserStagiaire.php',
                method: 'POST',
                parameters: {idStudent: idStudent, idStage: idStage},
                onSuccess : function(){
                    window.location.reload(false); 
                },
                onError : function(){
                    alert('Problème requete ajax accept non validée');
                }
            });
            
            
        }
        

JAVASCRIPT
    );
if (!empty($_POST)) {
    header("Location: profil.php");
}
$user = User::createFromSession();
$profilInfo = $user->profil();
$role = $profilInfo[10];
$_SESSION['__user__']['page'] = 'profil';
$_SESSION['__user__']['id'] = $profilInfo[11];
$_SESSION['__user__']['nom'] = $profilInfo[1].' '.$profilInfo[2];

$p->appendNavBar();
if($_SESSION['__user__']['nbNotif'] == '0'){
    $notifs =  <<<HTML
        <div class='row'>
            <div class='col col-12'>
                <h6 class='text-center mt-2'>Aucune notification</h6>
            </div>
        </div>
HTML
    ;
}
else{
    $notifs = alert::afficheAlertStage($profilInfo[11]);
}
switch ($role) {
    case 'student':
        $p->appendContent(<<<HTML

    <div class="row p-5 mt-5">
        <div class="card border-dark p-2 m-3 col-md-7 col-xs-12">
            <form method="post">

                <div class="row">
                    <div class="col-10">
                        <div class="profile-head m-2">
                            <h1>{$profilInfo[0]} {$profilInfo[1]}</h1>
                            
                        </div>
                    </div>
                    <div class="col-md-2 col-xs-12">
                        <div class="profile-image">
                            <div class="">
                                <img src="{$profilInfo[8]}" alt="Photo" class="rounded border border-dark m-2" style="height: 125px;width: 125px; float: right;">
                            </div>
                        </div>
                    </div>
                </div>
                    
                <div class="row">
                    <div class="col-12">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active text-dark" id="home-tab" data-toggle="tab" href="#informations" role="tab" aria-controls="home" aria-selected="true">Informations</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" id="profile-tab" data-toggle="tab" href="#listeBulletins" role="tab" aria-controls="profile" aria-selected="false">Bulletins</a>
                            </li>
                        </ul>
                        <div class="tab-content profile-tab" id="myTabContent">
                            <div class="tab-pane fade show active m-2 m-4" id="informations" role="tabpanel" aria-labelledby="home-tab">
                                <table class="table table-striped ">
                                    <tr>
                                        <div class="row">
                                            <div class="col-4">
                                                <td>
                                                    <label>Identifiant</label>
                                                </td>
                                            </div>
                                            <div class="col-8">
                                                <td>
                                                    <p>{$profilInfo[6]}</p>
                                                </td>
                                            </div>
                                        </div>
                                    </tr>
                                    <tr>
                                        <div class="row">
                                            <div class="col-4">
                                                <td>
                                                    <i class="fas fa-user-alt" style="font-size:25px"></i>
                                                </td>
                                            </div>
                                            <div class="col-8">
                                                <td>
                                                    <p>{$profilInfo[0]} {$profilInfo[1]}</p>
                                                </td>
                                            </div>
                                        </div>
                                    </tr>
                                    <tr>
                                        <div class="row">
                                            <div class="col-4">
                                                <td>
                                                    <i class="far fa-envelope" style="font-size:25px"></i>
                                                </td>
                                            </div>
                                            <div class="col-8">
                                                <td>
                                                    <p>{$profilInfo[4]}</p>
                                                </td>
                                            </div>
                                        </div>
                                    </tr>
                                    <tr>
                                        <div class="row">
                                            <div class="col-4">
                                                <td>
                                                    <i class="fas fa-mobile-alt" style="font-size:25px"></i>
                                                </td>
                                            </div>
                                            <div class="col-8">
                                                <td>
                                                    <p>{$profilInfo[5]}</p>
                                                </td>
                                            </div>
                                        </div>
                                    </tr>
                                    <tr>
                                        <div class="row">
                                            <div class="col-4">
                                                <td>
                                                    <i class="fas fa-map-marker-alt" style="font-size:25px"></i>
                                                </td>
                                            </div>
                                            <div class="col-8">
                                                <td>
                                                    <p>{$profilInfo[3]}</p>
                                                </td>
                                            </div>
                                        </div>
                                    </tr>
                                    <tr>
                                        <div class="row">
                                            <div class="col-4">
                                                <td>
                                                    <i class="fas fa-birthday-cake"></i>
                                                </td>
                                            </div>
                                            <div class="col-8">
                                                <td>
                                                    <p>{$profilInfo[2]}</p>
                                                </td>
                                            </div>
                                        </div>
                                    </tr>
                                </table>
                            </div>
                            <div class="tab-pane fade m-2 m-4" id="listeBulletins" role="tabpanel" aria-labelledby="profile-tab">
                                <table class="table table-striped">
                                    <form></form>
                                    <tr>
                                        <div class="row">
                                            <div class="col-4">
                                                <td>
                                                    <label>Semestre 1</label>
                                                </td>
                                            </div>
                                            <div class="col-8">
                                                <td>
                                                    <form name="profilS1" action="bulletins.php" method='POST'>
                                                        <input type="hidden" name="semestre" value="S1">
                                                        <input type='submit' class="btn btn-link" value="Consulter">
                                                    </form>
                                                </td>
                                            </div>
                                        </div>
                                    </tr>
                                    <tr>
                                        <div class="row">
                                            <div class="col-4">
                                                <td>
                                                    <label>Semestre 2</label>
                                                </td>
                                            </div>
                                            <div class="col-8">
                                                <td>
                                                    <form name="profilS2" action="bulletins.php" method='POST'>
                                                        <input type="hidden" name="semestre" value="S2">
                                                        <input type='submit' class="btn btn-link" value="Consulter">
                                                    </form>
                                                </td>
                                            </div>
                                        </div>
                                    </tr>
                                    <tr>
                                        <div class="row">
                                            <div class="col-4">
                                                <td>
                                                    <label>Semestre 3</label>
                                                </td>
                                            </div>
                                            <div class="col-8">
                                                <td>
                                                    <form name="profilS3" action="bulletins.php" method='POST'>
                                                        <input type="hidden" name="semestre" value="S3">
                                                        <input type='submit' class="btn btn-link" value="Consulter">
                                                    </form>
                                                </td>
                                            </div>
                                        </div>
                                    </tr>
                                    <tr>
                                        <div class="row">
                                            <div class="col-4">
                                                <td>
                                                    <label>Semestre 4</label>
                                                </td>
                                            </div>
                                            <div class="col-8">
                                                <td>
                                                    <form name="profilS4" action="bulletins.php" method='POST'>
                                                        <input type="hidden" name="semestre" value="S4">
                                                        <input type='submit' class="btn btn-link" value="Consulter">
                                                    </form>
                                                </td>
                                            </div>
                                        </div>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </form>   
        </div>
            
        <div class="card m-3 pt-2 pb-2 col-md-4 col-xs-12 border-dark">
             $notifs
        </div>

    </div>

HTML
);
        break;
    
    case 'company':
        $p->appendContent(<<<HTML
        <div class="row p-5 mt-5">
            <div class="card border-dark p-2 m-3 col-md-7 col-xs-12">
                <form method="post">

                    <div class="row">
                        <div class="col-10">
                            <div class="profile-head m-2">
                                <h1>{$profilInfo[0]} {$profilInfo[1]}</h1>
                                
                            </div>
                        </div>
                        <div class="col-md-2 col-xs-12">
                            <div class="profile-image">
                                <div class="">
                                    <img src="{$profilInfo[8]}" alt="Photo" class="rounded border border-dark m-2" style="height: 125px;width: 125px; float: right;">
                                </div>
                            </div>
                        </div>
                    </div>
                        
                    <div class="row">
                        <div class="col-12">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active text-dark" id="home-tab" data-toggle="tab" href="#informations" role="tab" aria-controls="home" aria-selected="true">Informations</a>
                                </li>
                            </ul>
                            <div class="tab-content profile-tab" id="myTabContent">
                                <div class="tab-pane fade show active m-2 m-4" id="informations" role="tabpanel" aria-labelledby="home-tab">
                                    <table class="table table-striped ">
                                        <tr>
                                            <div class="row">
                                                <div class="col-4">
                                                    <td>
                                                        <label>Identifiant</label>
                                                    </td>
                                                </div>
                                                <div class="col-8">
                                                    <td>
                                                        <p>{$profilInfo[6]}</p>
                                                    </td>
                                                </div>
                                            </div>
                                        </tr>
                                        <tr>
                                            <div class="row">
                                                <div class="col-4">
                                                    <td>
                                                        <i class="fas fa-user-alt" style="font-size:25px"></i>
                                                    </td>
                                                </div>
                                                <div class="col-8">
                                                    <td>
                                                        <p>{$profilInfo[0]} {$profilInfo[1]}</p>
                                                    </td>
                                                </div>
                                            </div>
                                        </tr>
                                        <tr>
                                            <div class="row">
                                                <div class="col-4">
                                                    <td>
                                                        <i class="far fa-envelope" style="font-size:25px"></i>
                                                    </td>
                                                </div>
                                                <div class="col-8">
                                                    <td>
                                                        <p>{$profilInfo[4]}</p>
                                                    </td>
                                                </div>
                                            </div>
                                        </tr>
                                        <tr>
                                            <div class="row">
                                                <div class="col-4">
                                                    <td>
                                                        <i class="fas fa-mobile-alt" style="font-size:25px"></i>
                                                    </td>
                                                </div>
                                                <div class="col-8">
                                                    <td>
                                                        <p>{$profilInfo[5]}</p>
                                                    </td>
                                                </div>
                                            </div>
                                        </tr>
                                        <tr>
                                            <div class="row">
                                                <div class="col-4">
                                                    <td>
                                                        <i class="fas fa-map-marker-alt" style="font-size:25px"></i>
                                                    </td>
                                                </div>
                                                <div class="col-8">
                                                    <td>
                                                        <p>{$profilInfo[3]}</p>
                                                    </td>
                                                </div>
                                            </div>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>   
            </div>
                
            <div class="card m-3 pt-2 pb-2 col-md-4 col-xs-12 border-dark">
                $notifs
            </div>

        </div>

HTML
);
        break;
    case 'teacher':
        $p->appendContent(<<<HTML

    <div class="row p-5 mt-5">
        <div class="card border-dark p-2 m-3 col-md-7 col-xs-12">
            <form method="post">

                <div class="row">
                    <div class="col-10">
                        <div class="profile-head m-2">
                            <h1>{$profilInfo[0]} {$profilInfo[1]}</h1>
                            
                        </div>
                    </div>
                    <div class="col-md-2 col-xs-12">
                        <div class="profile-image">
                            <div class="">
                                <img src="{$profilInfo[8]}" alt="Photo" class="rounded border border-dark m-2" style="height: 125px;width: 125px; float: right;">
                            </div>
                        </div>
                    </div>
                </div>
                    
                <div class="row">
                    <div class="col-12">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active text-dark" id="home-tab" data-toggle="tab" href="#informations" role="tab" aria-controls="home" aria-selected="true">Informations</a>
                            </li>
                           
                        </ul>
                        <div class="tab-content profile-tab" id="myTabContent">
                            <div class="tab-pane fade show active m-2 m-4" id="informations" role="tabpanel" aria-labelledby="home-tab">
                                <table class="table table-striped ">
                                    <tr>
                                        <div class="row">
                                            <div class="col-4">
                                                <td>
                                                    <label>Identifiant</label>
                                                </td>
                                            </div>
                                            <div class="col-8">
                                                <td>
                                                    <p>{$profilInfo[6]}</p>
                                                </td>
                                            </div>
                                        </div>
                                    </tr>
                                    <tr>
                                        <div class="row">
                                            <div class="col-4">
                                                <td>
                                                    <i class="fas fa-user-alt" style="font-size:25px"></i>
                                                </td>
                                            </div>
                                            <div class="col-8">
                                                <td>
                                                    <p>{$profilInfo[0]} {$profilInfo[1]}</p>
                                                </td>
                                            </div>
                                        </div>
                                    </tr>
                                    <tr>
                                        <div class="row">
                                            <div class="col-4">
                                                <td>
                                                    <i class="far fa-envelope" style="font-size:25px"></i>
                                                </td>
                                            </div>
                                            <div class="col-8">
                                                <td>
                                                    <p>{$profilInfo[4]}</p>
                                                </td>
                                            </div>
                                        </div>
                                    </tr>
                                    <tr>
                                        <div class="row">
                                            <div class="col-4">
                                                <td>
                                                    <i class="fas fa-mobile-alt" style="font-size:25px"></i>
                                                </td>
                                            </div>
                                            <div class="col-8">
                                                <td>
                                                    <p>{$profilInfo[5]}</p>
                                                </td>
                                            </div>
                                        </div>
                                    </tr>
                                    <tr>
                                        <div class="row">
                                            <div class="col-4">
                                                <td>
                                                    <i class="fas fa-map-marker-alt" style="font-size:25px"></i>
                                                </td>
                                            </div>
                                            <div class="col-8">
                                                <td>
                                                    <p>{$profilInfo[3]}</p>
                                                </td>
                                            </div>
                                        </div>
                                    </tr>
                                    <tr>
                                        <div class="row">
                                            <div class="col-4">
                                                <td>
                                                    <i class="fas fa-birthday-cake"></i>
                                                </td>
                                            </div>
                                            <div class="col-8">
                                                <td>
                                                    <p>{$profilInfo[2]}</p>
                                                </td>
                                            </div>
                                        </div>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </form>   
        </div>
            
        <div class="card m-3 pt-2 pb-2 col-md-4 col-xs-12 border-dark">
             $notifs
        </div>

    </div>

HTML
);
        break;
}


echo $p->toHTML();