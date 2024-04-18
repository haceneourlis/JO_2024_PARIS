<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['reset'])) {
        setcookie("NOM_COACH_COOKIE", "", time() - 3600, "/");
        setcookie("NATIONALITE_COACH_COOKIE", "", time() - 3600, "/");
        setcookie("AGEMIN_COACH_COOKIE", "", time() - 3600, "/");
        setcookie("AGEMAX_COACH_COOKIE", "", time() - 3600, "/");
        header("Location: {$_SERVER['REQUEST_URI']}");
        exit();
    }
}

// les cookies pour le coach : 
$Nom_coach = isset($_COOKIE["NOM_COACH_COOKIE"]) ? $_COOKIE["NOM_COACH_COOKIE"] : "";
$Nationalite_coach = isset($_COOKIE["NATIONALITE_COACH_COOKIE"]) ? $_COOKIE["NATIONALITE_COACH_COOKIE"] : "";
$AgeMin_coach = isset($_COOKIE["AGEMIN_COACH_COOKIE"]) ? $_COOKIE["AGEMIN_COACH_COOKIE"] : "";
$AgeMax_coach = isset($_COOKIE["AGEMAX_COACH_COOKIE"]) ? $_COOKIE["AGEMAX_COACH_COOKIE"] : "";

$requete_coachs = "SELECT DISTINCT c.NOM , c.PRENOM , pr.ID_COACH
    FROM PARTICIPANT p , COACH c
    WHERE c.id_part = p.id_part
    ";

// mettre à jour les cookies ...
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $to_bind = array();
    $the_values = array();
    $Le_Where = array();

    if (isset($_POST["nom_coach"])) {
        $requete_coachs .= " AND ( upper(c.NOM) LIKE upper(:nom_coach) OR upper(c.PRENOM) LIKE upper(:nom_coach) )";
        $to_bind[] = ":nom_coach";
        $the_values[] = '%' . htmlentities(trim($_POST["nom_coach"])) . '%';

        setcookie("NOM_COACH_COOKIE", $_POST["nom_coach"], strtotime('+1 day'));
    }
    if (isset($_POST["nationalite_coach"])) {
        $requete_coachs .= " AND ( upper(c.NATIONALITE) LIKE upper(:nationalite_coach) )";
        $to_bind[] = ":nationalite_coach";
        $the_values[] = '%' . htmlentities(trim($_POST["nationalite_coach"])) . '%';

        setcookie("NATIONALITE_COACH_COOKIE", $_POST["nationalite_coach"], strtotime('+1 day'));
    }
    if (isset($_POST["age_min_coach"]) && is_numeric($_POST["age_min_coach"])) {
        $requete_coachs .= " AND  ( EXTRACT(YEAR FROM DATE '2024-01-01') - EXTRACT(YEAR FROM c.DATENAISS) ) >= :age_min_coach";
        $to_bind[] = ":age_min_coach";
        $the_values[] = intval($_POST["age_min_coach"]);

        setcookie("AGEMIN_COACH_COOKIE", $_POST["age_min_coach"], strtotime('+1 day'));
    }
    if (isset($_POST["age_max_coach"]) && is_numeric($_POST["age_max_coach"])) {
        $requete_coachs .= " AND ( EXTRACT(YEAR FROM DATE '2024-01-01') - EXTRACT(YEAR FROM c.DATENAISS) ) <= :age_max_coach";
        $to_bind[] = ":age_max_coach";
        $the_values[] = intval($_POST["age_max_coach"]);

        setcookie("AGEMAX_COACH_COOKIE", $_POST["age_max_coach"], strtotime('+1 day'));
    }

    if (isset($_POST["DIPLOME"])) {
        $requete_coachs .= " AND c.DIPLOME = :coach_diplome ";
        $to_bind[] = ":coach_diplome";
        $the_values[] = $_POST["DIPLOME"];

        setcookie("DIPLOME_COACH_COOKIE", $_POST["DIPLOME"], strtotime('+1 day'));
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche Coach</title>
</head>

<body>
    <h2>Formulaire de Recherche Coach</h2>
    <form method="POST">
        <label for="nom">Nom:</label>
        <input type="text" id="nom" name="nom_coach" value="<?= $Nom_coach ?>"><br><br>

        <label for="age_min">Âge Minimum:</label>
        <input type="number" id="age_min" min='0' name="age_min_coach" value="<?= $AgeMin_coach ?>"><br><br>

        <label for="age_max">Âge Maximum:</label>
        <input type="number" id="age_max" min='0' name="age_max_coach" value="<?= $AgeMax_coach ?>"><br><br>

        <label for="nationalite">Nationalité:</label>
        <input type="text" id="nationalite" name="nationalite_coach" value="<?= $Nationalite_coach ?>"><br><br>

        <label for="DIPLOME">Compétition:</label>
        <select id="DIPLOME" name="DIPLOME">
            <?php
            $requete = "SELECT DIPLOME FROM COACH ";
            $coach_stid = executerReq($idcom, $requete, [], []);
            if ($coach_stid) {
                while ($row = oci_fetch_assoc($coach_stid)) {
                    echo "<option value='{$row["DIPLOME"]}'> {$row["DIPLOME"]} </option>";
                }
            }
            ?>
        </select><br><br>

        <input type="submit" value="Rechercher">
        <input type="submit" name='reset' value="Réinitialiser">
    </form>
</body>

</html>

<?php

// exécuter la requête ici :
$coach_stid = executerReq($idcom, $requete_coachs, $to_bind, $the_values);
if ($coach_stid) {
    while ($result = oci_fetch_array($coach_stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
        $ID_COACH = $result['ID_COACH'];
        $nom_coach = $result['NOM'];
        $prenom_coach = $result['PRENOM'];

        echo "<a href='coach.php?id_coach=" . $ID_COACH . "'>
                <div class='coach-box'>
                <p><strong>'$nom_coach'</strong>   '$prenom_coach'</p>
                </div>
                </a>";
        echo "<br>------------------------------<br>";
    }
}

?>