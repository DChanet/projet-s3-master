<?php
require_once('../autoload.php') ;

//création page web

$p = new WebPage('accueil') ;

//implementation de Bootstrap

$p->appendBootstrap();

$p->appendCss(<<<CSS
    .boite
    {
    color:#3B3D3E;
    }

    .boite:hover
    {
    color:#0275d8;
    }

    a:hover
    {
    text-decoration:none;
    }
CSS
);
//evite de devoir accepter le réenvoie du post

if (!empty($_POST)) {
    $_SESSION["formulaire_envoye"] = $_POST;
    header("Location: accueil.php");
}
if (isset($_SESSION["formulaire_envoye"])) {
    $_POST = $_SESSION["formulaire_envoye"];
}



if(User::isConnected()){
    $user = User::createFromSession();
    
    $profilInfo = $user->profil();
    $_SESSION['__user__']['role'] = $profilInfo[10];
    $_SESSION['__user__']['id'] = $profilInfo[11];
    $_SESSION['__user__']['boutonLogout'] = User::logoutForm('../index.php','Se déconnecter');
    $_SESSION['__user__']['page'] = 'accueil';
    
    //implementation de la navbar

    $p->appendNavbar();

}
else{
    try {
        $user = User::createFromAuthSHA512($_REQUEST);
        $user->saveIntoSession();
        $profilInfo = $user->profil();
        $_SESSION['__user__']['role'] = $profilInfo[10];
        $_SESSION['__user__']['id'] = $profilInfo[11];
        $_SESSION['__user__']['page'] = 'accueil';
    
        $_SESSION['__user__']['boutonLogout'] = User::logoutForm('../index.php','Se déconnecter');

        //implementation de la navbar

        $p->appendNavbar();
    }

    catch (AuthenticationException $e) {
        $p->appendContent("Échec d'authentification&nbsp;: {$e->getMessage()}") ;
    }
    catch (Exception $e) {
        $p->appendContent("Un problème est survenu&nbsp;: {$e->getMessage()}") ;
    }
}

switch ($_SESSION['__user__']['role']) {
    case 'student':
        $p->appendContent(<<<HTML
        <div class="row" style="margin-top: 200px;">
            <div class="col-1"></div>
            <div class="col-4 text-center">
                <a class="boite" href="stages.php">
                        <i class="fas fa-briefcase" style="font-size:317px;"></i>
                        <div style ="text-decoration: none;font-size:20px;">Stages</div>
                </a>
            </div>
            <div class="col-2"></div>
            <div class="col-4 text-center">
            <a class="boite" href="bulletins.php">
                    <i class="fas fa-file-alt"style="font-size:300px;"></i>
                    <div style ="text-decoration: none;margin-top:20px;font-size:20px;">Bulletins</div>

            </a>
            </div>
            <div class="col-1"></div>
        </div>
HTML
);
        break;
    default:
        break;

}



// Envoi du code HTML au navigateur du client
echo $p->toHTML() ;