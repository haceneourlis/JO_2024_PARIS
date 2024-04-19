<?php
session_start();
include("connexion_OCI.php");
include_once("fonctions.php");
$idcom = connexion_OCI();
if (!$idcom) {
    echo "connexion à la base de données IMPOSSIBLE ///";
    exit;
}

if (
    isset($_POST["athlete_choisi"]) && !empty($_POST["athlete_choisi"])  && is_numeric($_POST["athlete_choisi"])
    && isset($_POST["type_epreuve"]) && !empty($_POST["type_epreuve"])
    && isset($_POST["num_epreuve"]) && !empty($_POST["num_epreuve"])  && is_numeric($_POST["num_epreuve"])
) {
    $to_bind = array();
    $the_values = array();

    $to_bind[] = ":id_part";
    $the_values[] = $_POST["athlete_choisi"];

    $to_bind[] = ":id_compet";
    $the_values[] = $_POST["id_compet"];

    $to_bind[] = ":num_epreuve";
    $the_values[] = $_POST["num_epreuve"];

    $to_bind[] = ":type_epreuve";
    $the_values[] = $_POST["type_epreuve"];

    $requete_ajout = "INSERT INTO PARTICIPE (ID_PART, ID_COMPET, NUM_EPREUVE, TYPE_EPREUVE) 
    VALUES (:id_part, :id_compet, :num_epreuve, :type_epreuve)";

    $ajout_stid = executerReq($idcom, $requete_ajout, $to_bind, $the_values);
    if ($ajout_stid) {
        setcookie("athlete_added", "success", time() + 60, "/");
        header("Location :'competition.administration.modif.php?id_compet=" . $_POST["id_compet"] . "?type_co=" . $_POST["type_co"]  . "");
        exit();
    } else {
        setcookie("athlete_added", "failed", time() + 60, "/");
        header("Location :'competition.administration.modif.php?id_compet=" . $_POST["id_compet"] . "?type_co=" . $_POST["type_co"]  . "");
        exit();
    }
}



if (isset($_GET["id_compet"]) && isset($_GET["type_co"])) {
    $id_compet = $_GET["id_compet"];
    $type_compet = $_GET["type_co"];

    // form :
    // liste déroulante (select) des nom prenom des athletes
    // type epreuve 
    // num epreuve 

    echo "<form method='POST'>";
    echo "<select name='athlete_choisi' required>
    <option value=''>séléctionner un athlete </option>";
    $requete = "SELECT DISTINCT p.ID_PART , p.NOM , p.PRENOM 
    FROM PARTICIPANT p , ATHLETE a 
    WHERE p.id_part = a.id_part ";
    $stid = executerReq($idcom, $requete, [], []);
    while ($row = oci_fetch_assoc($stid)) {
        echo "<option value='{$row['ID_PART']}'>{$row['NOM']} {$row['PRENOM']} </option>";
    }
    echo "</select><br><br>";

    echo "<input type='hidden' name='id_compet' value='{$id_compet}'>";
    echo "<input type='hidden' name='type_co' value='{$type_compet}'>";

    echo "<select name='type_epreuve' required>
    <option value=''>Stage de competition</option>
    <option value='Quart de finale'>Quart de finale</option>
    <option value='Demi-finale'>Demi finale</option>
    <option value='Finale'>Finale</option>
    </select><br><br>";

    echo "numero épreuve : <input type='number' name='num_epreuve' max=4 min=1 required >";
    echo "<input type='submit' value='ajouter'>";
    echo "</form>";
}
