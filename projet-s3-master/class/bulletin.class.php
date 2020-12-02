<?php
require_once 'mypdo.class.php';

class Bulletin{
    private $studentId; //int
    private $semester; //string
    private $studentName; //string
    private $studentFirstName; //string
    private $notes; //array

    private function __construct(){}

     //Liste des UE\\
     public static function tu_teacher(int $teacher_id){
      //UE\\
      $stmt0 = MyPDO::getInstance()->prepare(<<<SQL
      SELECT subjects.id, subjects.name ,teaching_units.code
      FROM teaching_units, subjects
      WHERE subjects.teaching_unit_id = teaching_units.id
      AND subjects.teacher_id = :teacher_id
SQL
 );   
      //groups\\
      $stmt1 = MyPDO::getInstance()->prepare(<<<SQL
      SELECT DISTINCT schoolClass
      FROM users, grades
      WHERE users.id = grades.student_id
      AND grades.subject_id IN (SELECT id
                                FROM subjects
                                WHERE teaching_unit_id = (SELECT id
                                                          FROM teaching_units
                                                          WHERE code = :code))
SQL
     );

     $stmt0->execute(array('teacher_id' => $teacher_id));
     $copy_stmt0 = $stmt0->fetchAll();
    $res = "<div class='container' style='margin-top:80px'><table class='table table-striped table-bordered table-responsive-sm'>";
      $res .= <<<HTML
        <thead>
          <tr>
            <td class="text-left">UE</td>
            <td>Groups</td>
            <td>notes</td>
          </tr>
        </thead>
HTML;
    $res .= "<tbody>";
    foreach ($copy_stmt0 as $matiere) {
      $stmt1->execute(array('code' => $matiere['code']));
      $id_mat = $matiere['id'];
      $mat = $matiere['name'];
      $ue = $matiere['code'];
      $sem = "S". $ue[2];
      $copy_fetch = $stmt1->fetchAll();
      $res .= <<<HTML
        <tr>
          <td class="text-left align-middle">$mat - $ue</td>
HTML;
        $grp= "";
        $res .= "<td class='align-middle'>";
        foreach ($copy_fetch as $group) {
          $res .= <<<HTML
            <span class='colsTable'>
              <span style="padding:0px">
                <form action="saisie.php" method="post" class="form-example">
                  <input type="text" name="ue" value="$ue" style="display:none">
                  <input type="text" name="group" value="{$group['schoolClass']}" style="display:none">
                  <button class="btn btn-primary" type='submit' name="submit" style="margin:0px;">{$group['schoolClass']}</button>
                </form>
              </span>
HTML;
        }
        $res .= "<td>";
        foreach($copy_fetch as $group){
          $res .= <<<HTML
              <span class="d-inline-block">
                <form action="../script/upload.php" method="post" enctype="multipart/form-data">
                    <input type="text" name="ue" value="$id_mat" style="display:none">
                    <input type="text" name="semester" value="$sem" style="display:none">
                    <input type="text" name="group" value="{$group['schoolClass']}" style="display:none">
                    <input type="file" name="file">
                    Coef
                    <input type="text" name="coef"style="width:30px">
                    <input type="submit" name="submit" value="ajouter">
                </form>
              </span>
            </span>
HTML;
        }
        for ($i=0; $i < sizeof($copy_fetch) ; $i++) { 
          //$grp = $group[$i]['schoolClass'];
          
        }
        $res .= "</td>";
        $res .= "</tr>";
    }
    $res .= "</tbody></table></div>";
      return $res;
    }

    //MENU CHOIX BULLETIN\\
    public static function menu($id){
      if($_SESSION['__user__']['role'] == 'student'){
        $user = $_SESSION['__user__']['id'];
      }
      else{
        $user = $id;
      }
      $menu = "";
      
      if(!isset($_POST['semestre'])){
        $i = 1;
      }
      else{
        switch ($_POST['semestre']) {
          case 'S1':
            $i = 1;
            break;
          
          case 'S2':
            $i = 2;
            break;

          case 'S3':
            $i = 3;
            break;
          
          case 'S4':
            $i = 4;
            break;
          
        }
      }
          $menu .= <<<HTML
            <div class="container text-center mt-5">
              <div class="row">
                <div class="col-12">
                  <div class="btn-group btn-group-lg mt-5" role="group" aria-label="Basic example">
                    <button class="btn btn-dark" id="moins" value="$i" onclick="semestreMoins(this.value, $user)"><i class="fas fa-angle-left"></i></button>
                    <div class="btn-group" role="group">
                      <button id="btnGroupDrop1" value="$i" type="button" onclick="recharger(this.value, $user)" class="btn btn-dark">
                        Semestre $i
                      </button>
                    </div>
                    <button class="btn btn-dark" id="plus"value="$i" onclick="semestrePlus(this.value, $user)"><i class="fas fa-angle-right"></i></button>     
                  </div>
                </div>
              </div>
            </div>
HTML
;
      return $menu;
}
    //AFFICHAGE DU BULLETIN\\
    public static function createSubjectLine(string $semester,string $student_id){

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
    <div class="container pt-5 " id="tableau">
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
    $ligneMat .= "</table></div>";
    return $ligneMat;  
  }
}
