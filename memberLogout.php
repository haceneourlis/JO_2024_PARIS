<?php
session_start();
if (isset($_SESSION['ID_MEMBRE_CONNECTE'])) {
    unset($_SESSION['ID_MEMBRE_CONNECTE']);
}
header("Location: jesuismembre.php");
die;
