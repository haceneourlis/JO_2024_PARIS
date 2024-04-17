<?php
// Connexion à la base de données
$idcom = oci_connect('haceneourlis', 'oracle', '10.1.16.56/oracle2');
if (!$idcom) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}

// Préparation de la requête
$requete = 'SELECT * FROM essai WHERE colonne = :valeur';
$stid = oci_parse($idcom, $requete);
if (!$stid) {
    $e = oci_error($idcom);
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}

// Liaison des valeurs aux paramètres
$valeur = 'valeur_recherchee';
oci_bind_by_name($stid, ':valeur', $valeur);

// Exécution de la requête
$r = oci_execute($stid);
if (!$r) {
    $e = oci_error($stid);
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}

// Traitement des résultats
while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
    // Faites quelque chose avec chaque ligne de résultat
}

// Libération des ressources
oci_free_statement($stid);
oci_close($idcom);
