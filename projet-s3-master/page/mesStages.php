<?php
require_once('../autoload.php') ;

//création page web

$p = new WebPage('mesStages') ;

//implementation de Bootstrap

$p->appendBootstrap();
$p->appendJsUrl('../js/ajaxrequest.js');
$user = User::createFromSession();
$_SESSION['__user__']['page'] = 'mesStages';
$p->appendNavbar();

$profilInfo = $user->profil();

$_SESSION['__user__']['id'] = $profilInfo[11];
$p->appendContent("<div class='mt-5' style='font-size:0px'>.</div>");
$p->appendContent(Stage::afficheStage($_SESSION['__user__']['id']));

echo $p->toHTML();