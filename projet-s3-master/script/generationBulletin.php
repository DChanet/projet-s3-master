<?php
require_once '../class/mypdo.class.php';


$student_id = $_POST['user'];
$semester;
switch ($_POST['semestre']) {
    case 1:
        $semester = 'S1';
        break;
    case 2:
        $semester = 'S2';
        break;
    case 3:
        $semester = 'S3';
        break;
    case 4:
        $semester = 'S4';
        break;
}
    $stmt0 = MyPDO::getInstance()->prepare(<<<SQL

    SELECT name
    FROM subjects, teaching_units
    WHERE subjects.teaching_unit_id = teaching_units.id
    AND semester = :semester
SQL
    );
    $stmt0->execute(array('semester' => $semester));
    $matiere = $stmt0->fetchAll();
    $ligneMat = "";
    $sumCoefsBulletin = 0;
    $sumNotesBulletin = 0;
    $ligneMat .= <<<HTML

    <table class="table table-bordered table-striped text-center">
        <thead>
            <tr>
                <th id="mat"scope="col"class='bg-light align-middle'>Matières</th>
                <th scope="col"class='bg-light align-middle'>Examens</th>
                <th scope="col"class='bg-light align-middle'>coef</th>
                <th scope="col"class='bg-light align-middle'>contrôle continu</th>
                <th scope="col"class='bg-light align-middle'>coef</th>
                <th scope="col"class='bg-light align-middle'>coef matière</th>
                <th scope="col"class='bg-light align-middle'>Moyenne</th>
            </tr>
        </thead>
HTML;
    for ($i=0; $i < sizeof($matiere); $i++){
    //GENERATION NOTES EXAM\\
        $stmt = MyPDO::getInstance()->prepare(<<<SQL
            SELECT value, coefficient
            FROM grades
            WHERE semester = :semester
            AND student_id = :student_id
            AND type = 'exam'
            AND subject_id IN (SELECT id
                            FROM subjects
                            WHERE name = :matiere)
SQL
);
        $stmt->execute(array('matiere' => $matiere[$i]["name"],'semester' => $semester,'student_id' => $student_id));
        $tabNotesExam = $stmt->fetchAll();
        $stmt2 = MyPDO::getInstance()->prepare(<<<SQL
            SELECT value, coefficient
            FROM grades
            WHERE semester = :semester
            AND student_id = :student_id
            AND type = 'continuous_assessment'
            AND subject_id IN (SELECT id
                            FROM subjects
                            WHERE name = :matiere)
SQL
);
        $stmt2->execute(array('matiere' => $matiere[$i]["name"],'semester' => $semester,'student_id' => $student_id));
        $tabNotesCc = $stmt2->fetchAll();

        //GENERATION NOTES CC\\

        $ligneMat .= "<tr><td class='text-left align-middle'>{$matiere[$i]['name']}</td>";
        $colsExam = "<td class='align-middle'>";
        $colsCoefExam = "<td class='align-middle'>";
        $colsCc = "<td class='align-middle'>";
        $colsCoefCc = "<td class='align-middle'>";
        $sumNotesE = 0;
        $sumCoefsE = 0;
        $sumNotesCc = 0;
        $sumCoefsCc = 0;

        //EXAM LOOP\\

        for ($j=0; $j < sizeof($tabNotesExam) ; $j++) {
            $val1 = $tabNotesExam[$j]['value'];
            $val2 = $tabNotesExam[$j]['coefficient'];
            $colsExam .= "<span class='colsTable'>$val1</span>";
            $colsCoefExam .= "<span class='font-italic colsTable'>$val2</span>";
            $sumNotesE += $val1 * $val2;
            $sumCoefsE += $val2;
        }

        $colsCoefExam .= "</td>";
        $colsExam .= "</td>";

        //CC LOOP\\

        for ($j=0; $j < sizeof($tabNotesCc) ; $j++) {
            $val1 = $tabNotesCc[$j]['value'];
            $val2 = $tabNotesCc[$j]['coefficient'];
            $colsCc .= "<span class='colsTable'>$val1</span>";
            $colsCoefCc .= "<span class='font-italic colsTable'>$val2</span>";
            $sumNotesE += $val1 * $val2;
            $sumCoefsCc += $val2;
        }

        $stmt4 = MyPDO::getInstance()->prepare(<<<SQL
            SELECT coefficient
            FROM subjects
            WHERE name = :name   
SQL
);
        $stmt4->execute(array('name' => $matiere[$i]["name"]));
        $colsCoeffMat = $stmt4->fetch()['coefficient'];
        $colsCc .= "</td>";
        $colsCoefCc .= "</td>";
        $ligneMat .= $colsExam;
        $ligneMat .= $colsCoefExam;
        $ligneMat .= $colsCc;
        $ligneMat .= $colsCoefCc;
        $ligneMat .= "<td class='font-italic align-middle'>$colsCoeffMat</td>";
        $moyenneMat = 0;
        $sumMat = 0;
        if($sumCoefsE + $sumCoefsCc != 0){
            $moyenneMat = round(($sumNotesE + $sumNotesCc) / ($sumCoefsE + $sumCoefsCc),2);
            $ligneMat .= "<td class='font-weight-bold align-middle'>" . $moyenneMat . "</td>" ;
            $sumNotesBulletin += $moyenneMat * $colsCoeffMat;
            $sumCoefsBulletin += $colsCoeffMat;
        }
        else{
            $ligneMat .= "<td class='align-middle'></td>" ;
        }   
    }
    $ligneMat  .= "</tbody>";
    $main_avg = 'N/A';
    if($sumCoefsBulletin > 0){
        (int) $main_avg = round($sumNotesBulletin/$sumCoefsBulletin,2);
    }
    $ligneMat .= <<<HTML
    <tfoot>
    <tr>
        <th id="mat"scope="col"style='border-color:transparent'></th>
        <th scope="col"style='border-color:transparent'></th>
        <th scope="col"style='border-color:transparent'></th>
        <th scope="col"style='border-color:transparent'></th>
        <th scope="col"style='border-bottom-color:transparent'></th>
        <th scope="col"class='bg-light align-middle'>Moyenne Générale</th>
        <th scope="col"class='bg-light align-middle'>$main_avg</th>
    </tr>
    </tfoot>
HTML;
    $ligneMat .= "</table>";

echo $ligneMat;