<?php
require_once('../autoload.php') ;
include_once "../script/importNotes.php";
$p = new WebPage('Semestre 1');
$p->appendJsUrl('../js/ajaxrequest.js');
//META\\
$p->appendBootstrap();
//CSS\\

$user = User::createFromSession();
$_SESSION['__user__']['page'] = 'bulletins'; 
$profilInfo = $user->profil();
$_SESSION['__user__']['id'] = $profilInfo[11];
$_SESSION['__user__']['role'] = $profilInfo[10];

$p->appendJs(<<<JAVASCRIPT
    function semestreMoins(i,user){
        i--;
        if(i == 0){
            i = 4;
        }
        document.getElementById('btnGroupDrop1').innerHTML =  "Semestre "+i;
        document.getElementById('plus').value = i;
        document.getElementById('moins').value = i;
        document.getElementById('btnGroupDrop1').value = i;
        new AjaxRequest(
            {
                asynchronous : true,
                url: '../script/generationBulletin.php',
                method: 'POST',
                handleAs   : 'text',
                parameters: {semestre: i, user: user},
                onSuccess : function(res){
                    //console.log('resultat: '+res);
                    document.getElementById('tableau').innerHTML = res;

                },
                onError : function(){
                    alert('Problème requete ajax génBulletin- non validée');
                }
            });
        
    }


    function semestrePlus(i, user){
        i++;
        if(i == 5){
            i = 1;
        }
        document.getElementById('btnGroupDrop1').innerHTML =  "Semestre "+i;
        document.getElementById('plus').value = i;
        document.getElementById('moins').value = i;
        document.getElementById('btnGroupDrop1').value = i;
        new AjaxRequest(
            {
                asynchronous : true,
                url: '../script/generationBulletin.php',
                method: 'POST',
                handleAs   : 'text',
                parameters: {semestre: i, user: user},
                onSuccess : function(res){
                    //console.log('resultat: '+res);
                    document.getElementById('tableau').innerHTML = res;

                },
                onError : function(){
                    alert('Problème requete ajax génBulletin+ non validée');
                }
            });
    }
    
    function recharger(i, user){
        new AjaxRequest(
            {
                asynchronous : true,
                url: '../script/generationBulletin.php',
                method: 'POST',
                handleAs   : 'text',
                parameters: {semestre: i, user: user},
                onSuccess : function(res){
                    //console.log('resultat: '+res);
                    document.getElementById('tableau').innerHTML = res;

                },
                onError : function(){
                    alert('Problème requete ajax génBulletin+ non validée');
                }
            });

    }
    

JAVASCRIPT
);
$p->appendNavBar();
$p->appendCss(<<<CSS

        .colsTable{
          display: block;
        }
        table{
            text-align:center;
        }
        th[id="mat"]{
            text-align:left;
        }
        td[id="mat"]{
            text-align:left;
        }
CSS
);
;
$role = $_SESSION['__user__']['role'];
if (!isset($_POST['view'])) {
    $_POST['view'] = 'no';
}
switch ($role) {
    case 'teacher':
        switch ($_POST['view']) {
            case 'yes':
                $stmt0 = MyPDO::getInstance()->prepare(<<<SQL
                    SELECT id 
                    FROM users
                    WHERE firstname = :firstname
                    AND lastname = :lastname
SQL
            );
                $stmt0->execute(array('firstname' => $_POST['fname'],'lastname' => $_POST['lname']));
                $id = $stmt0->fetch()['id'];
                $p->appendContent(Bulletin::menu($id));
                $p->appendContent(Bulletin::createSubjectLine('S1',$id));
                break;
            default:
                $p->appendContent(Bulletin::tu_teacher($_SESSION['__user__']['id']));
                break;
        }
      break;
    case 'student':
      $p->appendContent(Bulletin::menu(null));
      if(isset($_POST['semestre'])){
        $p->appendContent(Bulletin::createSubjectLine($_POST['semestre'],$profilInfo[11]));
      }
      else{
        $p->appendContent(Bulletin::createSubjectLine('S1',$profilInfo[11]));
      }
      break;
    default:
  }

//importNotes('../test.xlsx',3,1,'exam','S2');
echo $p->toHTML();