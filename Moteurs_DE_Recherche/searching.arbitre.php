<?php
session_start();
include("../connexion_OCI.php");
include_once("../fonctions.php");
$idcom = connexion_OCI();
if (!$idcom) {
    echo "connexion à la base de données IMPOSSIBLE ///";
    exit;
} else {
    echo "connexion reussi";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['reset_form1'])) {
        setcookie("NOM_ARBITRE_COOKIE", "", time() - 3600, "/");
        setcookie("NATIONALITE_ARBITRE_COOKIE", "", time() - 3600, "/");
        setcookie("AGEMIN_ARBITRE_COOKIE", "", time() - 3600, "/");
        setcookie("AGEMAX_ARBITRE_COOKIE", "", time() - 3600, "/");
        header("Location: {$_SERVER['REQUEST_URI']}");
        exit();
    }
}
// les cookies pour l'arbitre : 
$Nom_arbitre = isset($_COOKIE["NOM_ARBITRE_COOKIE"]) ? $_COOKIE["NOM_ARBITRE_COOKIE"] : "";
$Nationalite_arbitre = isset($_COOKIE["NATIONALITE_ARBITRE_COOKIE"]) ? $_COOKIE["NATIONALITE_ARBITRE_COOKIE"] : "";
$AgeMin_arbitre = isset($_COOKIE["AGEMIN_ARBITRE_COOKIE"]) ? $_COOKIE["AGEMIN_ARBITRE_COOKIE"] : "";
$AgeMax_arbitre = isset($_COOKIE["AGEMAX_ARBITRE_COOKIE"]) ? $_COOKIE["AGEMAX_ARBITRE_COOKIE"] : "";

$requete_arbitres = "SELECT DISTINCT p.NOM , p.PRENOM , pr.ID_PERS
    FROM PARTICIPANT p , PERSONNEL pr
    WHERE pr.id_part = p.id_part
    AND upper(pr.TYPE_PERS) = 'ARBITRE'
    ";
$to_bind = array();
$the_values = array();
$Le_Where = array();

// mettre à jour les cookies ...
if ($_SERVER["REQUEST_METHOD"] == "POST") {


    if (isset($_POST["nom_arbitre"]) && !empty($_POST["nom_arbitre"])) {
        $requete_arbitres .= " AND ( upper(p.NOM) LIKE upper(:nom_arbitre) OR upper(p.PRENOM) LIKE upper(:nom_arbitre) )";
        $to_bind[] = ":nom_arbitre";
        $the_values[] = '%' . htmlentities(trim($_POST["nom_arbitre"])) . '%';

        setcookie("NOM_ARBITRE_COOKIE", $_POST["nom_arbitre"], strtotime('+1 day'));
    }
    if (isset($_POST["nationalite_arbitre"]) && !empty($_POST["nationalite_arbitre"]) ) {
        $requete_arbitres .= " AND ( p.NATIONALITE = :nationalite_arbitre )";
        $to_bind[] = ":nationalite_arbitre";
        $the_values[] =  htmlentities(trim($_POST["nationalite_arbitre"]));

        setcookie("NATIONALITE_ARBITRE_COOKIE", $_POST["nationalite_arbitre"], strtotime('+1 day'));
    }
    if (isset($_POST["age_min_arbitre"]) && is_numeric($_POST["age_min_arbitre"])) {
        $requete_arbitres .= " AND  ( EXTRACT(YEAR FROM DATE '2024-01-01') - EXTRACT(YEAR FROM p.DATENAISS) ) >= :age_min_arbitre";
        $to_bind[] = ":age_min_arbitre";
        $the_values[] = intval($_POST["age_min_arbitre"]);

        setcookie("AGEMIN_ARBITRE_COOKIE", $_POST["age_min_arbitre"], strtotime('+1 day'));
    }
    if (isset($_POST["age_max_arbitre"]) && is_numeric($_POST["age_max_arbitre"])) {
        $requete_arbitres .= " AND ( EXTRACT(YEAR FROM DATE '2024-01-01') - EXTRACT(YEAR FROM p.DATENAISS) ) <= :age_max_arbitre";
        $to_bind[] = ":age_max_arbitre";
        $the_values[] = intval($_POST["age_max_arbitre"]);

        setcookie("AGEMAX_ARBITRE_COOKIE", $_POST["age_max_arbitre"], strtotime('+1 day'));
    }

    if (isset($_POST["competition"]) && !empty($_POST["competition"])) {
        $requete_arbitres .= "AND EXISTS ( SELECT * FROM PARTICIPE ptcp , PARTICIPANT p1 
        WHERE p1.id_part = p.id_part 
        AND ptcp.id_part = p.id_part 
        AND ptcp.ID_COMPET = :id_compet )";

        $to_bind[] = ":id_compet";
        $the_values[] = $_POST["competition"];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche Arbitre</title>
</head>

<body>
    <h2>Formulaire de Recherche Arbitre</h2>
    <form method="POST">
        <label for="nom">Nom:</label>
        <input type="text" id="nom" name="nom_arbitre" value="<?= $Nom_arbitre ?>"><br><br>

        <label for="age_min">Âge Minimum:</label>
        <input type="number" id="age_min" min='0' name="age_min_arbitre" value="<?= $AgeMin_arbitre ?>"><br><br>

        <label for="age_max">Âge Maximum:</label>
        <input type="number" id="age_max" min='0' name="age_max_arbitre" value="<?= $AgeMax_arbitre ?>"><br><br>
		
		<label for="nationalite">Nationalité:</label>
		<select id="nationalite" name="nationalite_arbitre">
		<option value="">Sélectionnez une nationalité</option>
			<?php
			$requete_nation = "SELECT DISTINCT NATIONALITE FROM PARTICIPANT";
			$nation_stid = executerReq($idcom, $requete_nation, [], []);
			if ($nation_stid) {
				while ($row = oci_fetch_assoc($nation_stid)) {
					echo "<option value='{$row["NATIONALITE"]}' $selected> {$row["NATIONALITE"]} </option>";
				}
			}
			?>
		</select><br><br>

          
		<label for="competition">Compétitions Arbitrés :</label>
		<select id="competition" name="competition">
		<option value="">Sélectionnez une compétition</option> 
		<?php
		$requete = "SELECT ID_COMPET, TYPE_COMPET FROM COMPETITION";
		$compet_stid = executerReq($idcom, $requete, [], []);
		if ($compet_stid) {
			while ($row = oci_fetch_assoc($compet_stid)) {
				$selected = ($_POST["competition"] == $row["ID_COMPET"]) ? "selected" : "";
				echo "<option value='{$row["ID_COMPET"]}' $selected> {$row["TYPE_COMPET"]} </option>";
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
$arbitre_stid = executerReq($idcom, $requete_arbitres, $to_bind, $the_values);
if ($arbitre_stid) {
    while ($result = oci_fetch_array($arbitre_stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
        $ID_PERS = $result['ID_PERS'];
        $nom_arbitre = $result['NOM'];
        $prenom_arbitre = $result['PRENOM'];

        echo "<a href='../arbitre.php?id_arb=" . $ID_PERS . "'>
                <div class='arbitre-box'>
                <p><strong>'$nom_arbitre'</strong>   '$prenom_arbitre'</p>
                </div>
                </a>";
        echo "<br>------------------------------<br>";
    }
}

?>