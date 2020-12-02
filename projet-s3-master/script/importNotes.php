<?php
require_once "../autoload.php";
function importNotes(string $path, int $subject, int $coef, string $semester):void{
    $xlsx = new XLSXReader($path);
    $data = $xlsx->getSheetData('Feuil1');
    $stmtO = MyPDO::getInstance()->prepare(<<<SQL
        SELECT id
        FROM users
        WHERE lastname = :lastname 
        AND firstname = :firstname
SQL
    );
    $stmt1 = MyPDO::getInstance()->prepare(<<<SQL
        INSERT INTO grades (`value`,`coefficient`,`type`,`semester`,`student_id`,`subject_id`)
        VALUES(:value, :coefficient, :type, :semester, :student_id, :subject_id);
SQL
    );
    $valide = false;
    for ($i=0; $i < sizeof($data); $i++){ 
        $lname = $data[$i][0];
        $fname = $data[$i][1];
        $value = $data[$i][2];
        $type = $data[$i][3];
        if(isset($lname) && isset($fname) && isset($value) && isset($type)){
            if ($value >= 0 && $value <= 20) {
                $stmtO->execute(array('lastname' => $lname,'firstname' => $fname));
                $student_id = $stmtO->fetch()['id'];
                $stmt1->execute(array('value' => $value, 'coefficient' => $coef, 'type' => $type ,'semester' => $semester, 'student_id' => $student_id,'subject_id' => $subject));
                header('Location: ../page/bulletins.php');
            }
        }
    }
}