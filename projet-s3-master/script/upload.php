<?php
require_once "../class/xlsxreader.class.php";
require_once "importNotes.php";

if (isset($_POST['submit'])) {
    $path = $_FILES['file']['tmp_name'];
    importNotes($path,$_POST['ue'],$_POST['coef'],$_POST['semester']);
}