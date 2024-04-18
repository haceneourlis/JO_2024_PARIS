<?php
session_start();

// les cookies milka et nutella : 
$Nom_at = isset($_COOKIE["NOM_ATHLETE_COOKIE"]) ? $_COOKIE["NOM_ATHLETE_COOKIE"] : "";
$Prenom_at = isset($_COOKIE["PRENOM_ATHLETE_COOKIE"]) ? $_COOKIE["PRENOM_ATHLETE_COOKIE"] : "";
$Nationalite_at = isset($_COOKIE["NATIONALITE_ATHLETE_COOKIE"]) ? $_COOKIE["NATIONALITE_ATHLETE_COOKIE"] : "";
$AgeMin_at = isset($_COOKIE["AGEMIN_ATHLETE_COOKIE"]) ? $_COOKIE["AGEMIN_ATHLETE_COOKIE"] : "";
$AgeMax_at = isset($_COOKIE["AGEMAX_ATHLETE_COOKIE"]) ? $_COOKIE["AGEMAX_ATHLETE_COOKIE"] : "";
$TailleMin_at = isset($_COOKIE["TAILLEMIN_ATHLETE_COOKIE"]) ? $_COOKIE["TAILLEMIN_ATHLETE_COOKIE"] : "";
$TailleMax_at = isset($_COOKIE["TAILLEMAX_ATHLETE_COOKIE"]) ? $_COOKIE["TAILLEMAX_ATHLETE_COOKIE"] : "";
$PoidsMin_at = isset($_COOKIE["POIDSMIN_ATHLETE_COOKIE"]) ? $_COOKIE["POIDSMIN_ATHLETE_COOKIE"] : "";
$PoidsMax_at = isset($_COOKIE["POIDSMAX_ATHLETE_COOKIE"]) ? $_COOKIE["POIDSMAX_ATHLETE_COOKIE"] : "";
$Medailles_at = isset($_COOKIE["MEDAILLE_ATHLETE_COOKIE"]) ? $_COOKIE["MEDAILLE_ATHLETE_COOKIE"] : "";

$requete_athletes = "SELECT DISTINCT p.NOM , p.PRENOM , p.ID_ATHLETE
    FROM PARTICIPANT p , ATHLETE a
    WHERE p.id_part = a.id_part ";


// mettre les cookies à jour ...
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $to_bind = array();
    $the_values = array();
    $Le_Where = array();

    if (isset($_POST["nom_athlete"])) {
        $requete_athletes .= "AND ( upper(p.NOM) LIKE upper(:nom_athlete) )";
        $to_bind[] = ":nom_athlete";
        $the_values[] = '%' . htmlentities(trim($_POST["nom_athlete"])) . '%';

        setcookie("NOM_ATHLETE_COOKIE", $_POST["nom_athlete"], strtotime('+1 day'));
    }
    if (isset($_POST["prenom_athlete"])) {
        $requete_athletes .= "AND ( upper(p.PRENOM) LIKE upper(:prenom_athlete) )";
        $to_bind[] = ":prenom_athlete";
        $the_values[] = '%' . htmlentities(trim($_POST["prenom_athlete"])) . '%';

        setcookie("PRENOM_ATHLETE_COOKIE", $_POST["prenom_athlete"], strtotime('+1 day'));
    }
    if (isset($_POST["nationalite_athlete"])) {
        $requete_athletes .= "AND ( upper(p.NATIONALITE) LIKE upper(:nationalite_athlete) )";
        $to_bind[] = ":nationalite_athlete";
        $the_values[] = '%' . htmlentities(trim($_POST["nationalite_athlete"])) . '%';

        setcookie("NATIONALITE_ATHLETE_COOKIE", $_POST["nom_athlete"], strtotime('+1 day'));
    }
    if (isset($_POST["age_min_athlete"]) && is_numeric($_POST["age_min_athlete"])) {
        $requete_athletes .= " AND  ( EXTRACT(YEAR FROM DATE '2024-01-01') - EXTRACT(YEAR FROM p.DATENAISS) ) >= :age_min_athlete";
        $to_bind[] = ":age_min_athlete";
        $the_values[] = intval($_POST["age_min_athlete"]);

        setcookie("AGEMIN_ATHLETE_COOKIE", $_POST["age_min_athlete"], strtotime('+1 day'));
    }
    if (isset($_POST["age_max_athlete"]) && is_numeric($_POST["age_max_athlete"])) {
        $requete_athletes .= " AND ( EXTRACT(YEAR FROM DATE '2024-01-01') - EXTRACT(YEAR FROM p.DATENAISS) ) <= :age_max_athlete";
        $to_bind[] = ":age_max_athlete";
        $the_values[] = intval($_POST["age_max_athlete"]);

        setcookie("AGEMAX_ATHLETE_COOKIE", $_POST["age_max_athlete"], strtotime('+1 day'));
    }
    if (isset($_POST["taille_min_athlete"]) && is_numeric($_POST["taille_min_athlete"])) {
        $requete_athletes .= " AND a.TAILLE >= :taille_min_athlete";
        $to_bind[] = ":taille_min_athlete";
        $the_values[] = intval($_POST["taille_min_athlete"]);

        setcookie("TAILLEMIN_ATHLETE_COOKIE", $_POST["taille_min_athlete"], strtotime('+1 day'));
    }
    if (isset($_POST["taille_max_athlete"]) && is_numeric($_POST["taille_max_athlete"])) {
        $requete_athletes .= " AND a.TAILLE <= :taille_max_athlete";
        $to_bind[] = ":taille_max_athlete";
        $the_values[] = intval($_POST["taille_max_athlete"]);

        setcookie("TAILLEMAX_ATHLETE_COOKIE", $_POST["taille_max_athlete"], strtotime('+1 day'));
    }
    if (isset($_POST["poids_min_athlete"]) && is_numeric($_POST["poids_min_athlete"])) {
        $requete_athletes .= " AND a.POIDS >= :poids_min_athlete";
        $to_bind[] = ":poids_min_athlete";
        $the_values[] = intval($_POST["poids_min_athlete"]);

        setcookie("POIDSMIN_ATHLETE_COOKIE", $_POST["poids_min_athlete"], strtotime('+1 day'));
    }
    if (isset($_POST["poids_max_athlete"]) && is_numeric($_POST["poids_max_athlete"])) {
        $requete_athletes .= " AND a.POIDS <= :poids_max_athlete";
        $to_bind[] = ":poids_max_athlete";
        $the_values[] = intval($_POST["poids_max_athlete"]);

        setcookie("POIDSMAX_ATHLETE_COOKIE", $_POST["poids_max_athlete"], strtotime('+1 day'));
    }
    // recherche par medailles : 2024 & PALMARES . 
    if (isset($_POST["medailles"]) && is_numeric($_POST["medailles"])) {
        $requete_athletes .= "AND EXISTS ( 
                                select *
                                from palmares plm ,OBTIENT_RESULTATS_ATHLETE o, athlete a1 
                                where a1.id_athlete = a.id_athlete 
                                AND o.id_athlete = a1.id_athlete
                                AND  plm.id_athlete = a1.id_athlete 
                                AND ( CLASSEMENT IN (1,2,3) OR CLASSEMENT_ATHLETE IN(1,2,3) ) 
                                having count(*) = :nb_medailles )";

        $to_bind[] = ":nb_medailles";
        $the_values[] = $_POST["medailles"];

        setcookie("MEDAILLE_ATHLETE_COOKIE", $_POST["medailles"], strtotime('+1 day'));
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche</title>
</head>

<body>
    <h2>Formulaire de Recherche</h2>
    <form method="POST">
        <label for="nom">Nom:</label>
        <input type="text" id="nom" name="nom_athlete" value="<?= $Nom_at ?>"><br><br>

        <label for="prenom">Prénom:</label>
        <input type="text" id="prenom" name="prenom_athlete" value="<?= $Prenom_at ?>"><br><br>


        <label for="age_min">Âge Minimum:</label>
        <input type="number" id="age_min" min='0' name="age_min_athlete" value="<?= $AgeMin_at ?>"><br><br>

        <label for="age_max">Âge Maximum:</label>
        <input type="number" id="age_max" min='0' name="age_max_athlete" value="<?= $AgeMax_at ?>"><br><br>

        <label for="taille_min">Taille Minimum (en cm):</label>
        <input type="number" id="taille_min" min='0' name="taille_min_athlete" value="<?= $TailleMin_at ?>"><br><br>

        <label for="taille_max">Taille Maximum (en cm):</label>
        <input type="number" id="taille_max" min='0' name="taille_max_athlete" value="<?= $TailleMax_at ?>"><br><br>

        <label for="poids_min">Poids Minimum (en kg):</label>
        <input type="number" id="poids_min" min='0' name="poids_min_athlete" value="<?= $PoidsMin_at ?>"><br><br>

        <label for="poids_max">Poids Maximum (en kg):</label>
        <input type="number" id="poids_max" min='0' name="poids_max_athlete" value="<?= $PoidsMax_at ?>"><br><br>

        <label for="medailles">Nombre de Médailles :</label>
        <input type="number" id="medailles" min='0' name="medailles" value="<?= $Medailles_at ?>"><br><br>

        <input type="submit" value="Rechercher">
    </form>
</body>

</html>

<?php

// executer la requete ici :

$athlete_stid = executerReq($idcom, $requete_athletes, $to_bind, $the_values);
if ($athlete_stid) {
    while ($result = oci_fetch_array($athlete_stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
        $id_athlete = $result['ID_ATHLETE'];
        $nom_athlete = $result['NOM'];
        $prenom_athlete = $result['PRENOM'];

        echo "<a href='athlete.php?id_at=" . $id_athlete . "'>
                <div class='athlete-box'>
                <p><strong>'$nom_athlete'</strong>   '$prenom_athlete'</p>
                </div>
                </a>";
        echo "<br>------------------------------<br>";
    }
}

?>