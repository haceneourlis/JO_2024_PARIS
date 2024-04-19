<?php
session_start();
include("connexion_OCI.php");
include_once("fonctions.php");
$idcom = connexion_OCI();
if (!$idcom) {
    echo "Connexion à la base de données IMPOSSIBLE ///";
    exit;
}
if(empty($_SESSION['ID_MEMBRE_CONNECTE'])) {
    echo "You are not logged in.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form method="post">
        NOM : <input type="text" name="nom"><br><br>
        PRENOM : <input type="text" name="prenom">
        <!-- <br><br> DATE_DE_NAISSANCE : <input type="text" name="dateNaiss"> -->
        <input type="submit" value="submit">

        <br>
        <a href="membreProfile.php">Go back to profile</a>
    </form>
</body>

<?php
if (isset($_POST["nom"])) {
    $new_name = $_POST["nom"];

    $requete_change_nom = "UPDATE participant p SET p.nom = :nouveauNom WHERE EXISTS (SELECT 1 FROM comite c WHERE p.id_part = c.id_part AND c.id_membre = :id_membre)";
    $stid_change_nom = executerReq($idcom, $requete_change_nom, [":nouveauNom",":id_membre"], [ $new_name, $_SESSION['ID_MEMBRE_CONNECTE'] ]);
    if(!$stid_change_nom)
    {
        echo "Error: Unable to change name.";
    } else {
        echo "Name changed successfully.";
    }
}

if (isset($_POST["prenom"])) {
    $new_prenom = $_POST["prenom"];
    $requete_change_prenom = "UPDATE participant p SET p.prenom = :nouveauPrenom WHERE EXISTS (SELECT 1 FROM comite c WHERE p.id_part = c.id_part AND c.id_membre = :id_membre)";
    $stid_change_prenom = executerReq($idcom, $requete_change_prenom, [":nouveauPrenom",":id_membre"], [$new_prenom,$_SESSION['ID_MEMBRE_CONNECTE']]);
    if(!$stid_change_prenom)
    {
        echo "Error: Unable to change prenom.";
    } else {
        echo "Prenom changed successfully.";
    }
}
?>


</html>
