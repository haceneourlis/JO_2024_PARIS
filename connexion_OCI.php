<?php
// Connexion à la base de données

function connexion_OCI()
{
    include_once("myparam.inc.php");
    $conn = oci_connect(MYUSER, MYPASS, MYHOST);
    if (!$conn) {
    	echo "what";
        $e = oci_error();
        trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
    } else
        return $conn;
}
