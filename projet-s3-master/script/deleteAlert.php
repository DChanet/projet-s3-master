<?php
require_once('../autoload.php');
//script supprimant le stage dont l'id est passé en post
if ($_POST['id1'] != null) {
    $stmt = MyPDO::getInstance()->prepare(<<<SQL
    DELETE FROM alerts
    WHERE id = :id
SQL
    );
    $stmt->execute(array('id'=>$_POST['id1']));
}
