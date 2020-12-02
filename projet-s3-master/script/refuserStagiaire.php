<?php

require_once('../autoload.php');

if($_POST['idStudent'] != null && $_POST['idStage'] != null){

    $stmt1 = MyPDO::getInstance()->prepare(<<<SQL
    SELECT title
    FROM internships
    WHERE id = :id
SQL
    );
    $stmt1->execute(array('id'=>$_POST['idStage']));
    $res = $stmt1->fetch();
    $text = "Vous avez été refuser au stage '".$res['title']."'";

    $stmt2 = MyPDO::getInstance()->prepare(<<<SQL
    INSERT INTO alerts (title, text, id_users, id_stage, type) VALUES ('Dommage !', :text, :id_users, :id_stage, 'refuser')
SQL
    );
    $stmt2->execute(array('text'=>$text, 'id_users'=>$_POST['idStudent'], 'id_stage'=>$_POST['idStage']));
}