<?php
require_once('../autoload.php');

if ($_POST['student'] != null && $_POST['stage'] != null){
    $stmt1 = MyPDO::getInstance()->prepare(<<<SQL
        SELECT users.id, internships.title
        FROM users, internships
        WHERE internships.supervisor_id = users.id
        AND internships.id = :idStage
SQL
        );
    $stmt1->execute(array('idStage'=>$_POST['stage']));

    $receveur = $stmt1->fetch();


    $stmt2 = MyPDO::getInstance()->prepare(<<<SQL
        SELECT lastname, firstname
        FROM users
        WHERE id = :id
SQL
        );
    $stmt2->execute(array('id'=>$_POST['student']));
    $envoie = $stmt2->fetch();
    $nom = $envoie['lastname']." ".$envoie['firstname'];
    $text = $nom . " a envoyé une candidature pour le stage '" .$receveur['title'] ."'";

        
    $stmt3 = MyPDO::getInstance()->prepare(<<<SQL
        INSERT INTO alerts (title, text, id_users, id_stage, type, expediteur) VALUES ('Candidature', :text, :id_users, :id_stage, 'demande', :expediteur)
SQL
    );
    $stmt3->execute(array('text'=>$text, 'id_users'=> $receveur['id'], 'id_stage'=> $_POST['stage'], 'expediteur'=>$_POST['student']));

        
}

