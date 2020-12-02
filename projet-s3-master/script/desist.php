<?php
require_once('../autoload.php');

if ($_POST['idStage'] != null) {
    $stmt1 = MyPDO::getInstance()->prepare(<<<SQL
    UPDATE internships
    SET student_id = null
    WHERE id = :id
SQL
    );
    $stmt1->execute(array('id'=>$_POST['idStage']));
}
