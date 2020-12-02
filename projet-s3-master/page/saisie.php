<?php
    require_once('../autoload.php');
    $p = new WebPage('Semestre 1');
    $p->appendBootstrap();
    $user = User::createFromSession();
    $_SESSION['__user__']['page'] = 'bulletins'; 
    $profilInfo = $user->profil();
    $_SESSION['__user__']['id'] = $profilInfo[11];
    $_SESSION['__user__']['role'] = $profilInfo[10];
    $p->appendNavBar();
    $p->appendContent("<div class='container'>");
    $stmt0 = MyPDO::getInstance()->prepare(<<<SQL
        SELECT DISTINCT users.lastname, users.firstname
        FROM `users`,`grades`
        WHERE users.schoolClass = :schoolClass
        AND users.id = grades.student_id
        AND grades.subject_id IN (SELECT id
                                FROM subjects
                                WHERE teaching_unit_id IN (SELECT id
                                                            FROM teaching_units
                                                            WHERE code = :code))
SQL
);
    $stmt0->execute(array('schoolClass' => $_POST['group'],'code' => $_POST['ue']));
    $copy_fetch = $stmt0->fetchAll();
    $res = "";
    $res .= <<<HTML
    <div class="">
        <table class="table table-responsive-sm table-bordered table-striped text-center" style="margin-top:80px">
            <thead>
                <td>Nom</td>
                <td>Prénom</td>
                <td>Groupe</td>
                <td>Fiche</td>
            </thead>
            <tbody>
HTML;
    foreach ($copy_fetch as $student) {
        $fname = $student['firstname'];
        $lname = $student['lastname'];
        $group = $_POST['group'];
        $res .= <<<HTML
            <tr>
                <td>$lname</td>
                <td>$fname</td>
                <td>$group</td>
                <form action="bulletins.php" method="POST">
                    <td>
                        <input type="text" name="fname" value="$lname" style="display:none">
                        <input type="text" name="lname" value="$fname" style="display:none">
                        <button type="submit" name="view" value="yes" class="btn btn-success btn-sm" style="">Voir</button>
                    </td>
                </form>
            </tr>
HTML;
    }   
    $res .= "</tbody>";
    $p->appendContent($res);
    $p->appendContent(<<<HTML
        </form>
HTML
);
    $p->appendContent("</table></div></div>");

    
echo $p->toHTML();

