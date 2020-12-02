<?php

require_once('../autoload.php');


if ($_POST['idStage'] != null && $_POST['idStudent'] != null && $_POST['idAlert'] != null) {
    $stmt1 = MyPDO::getInstance()->prepare(<<<SQL
    UPDATE internships
    SET student_id = :idS
    WHERE id = :id
SQL
    );
    $stmt1->execute(array('idS'=>$_POST['idStudent'], 'id'=>$_POST['idStage']));

    $stmt2 = MyPDO::getInstance()->prepare(<<<SQL
    DELETE FROM alerts
    WHERE id = :id
SQL
    );
    $stmt2->execute(array('id'=>$_POST['idAlert']));



    $stmt3 = MyPDO::getInstance()->prepare(<<<SQL
    SELECT title
    FROM internships
    WHERE id = :id
SQL
    );
    $stmt3->execute(array('id'=>$_POST['idStage']));
    $res = $stmt3->fetch();
    $text ="Vous êtes stagiaire au stage ". "'".$res['title']."'.";

    $stmt4 = MyPDO::getInstance()->prepare(<<<SQL
    INSERT INTO alerts (title, text, id_users, id_stage, type) VALUES ('Bravo !', :text, :id_users, :id_stage, 'accepter')
SQL
    );
    $stmt4->execute(array('text'=>$text, 'id_users'=>$_POST['idStudent'], 'id_stage'=>$_POST['idStage']));
}