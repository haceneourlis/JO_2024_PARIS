<?php
session_start();
include("connexion_OCI.php");
include_once("fonctions.php");
$idcom = connexion_OCI();
if (!$idcom) {
    echo "connexion à la base de données IMPOSSIBLE ///";
    exit;
}

if (!empty($_SESSION['ID_MEMBRE_CONNECTE'])) {
    $ID_MEMBRE_CONNECTE = $_SESSION['ID_MEMBRE_CONNECTE'];

    $requete_membre = "SELECT NOM, PRENOM, DATENAISS, NATIONALITE 
        FROM comite c, participant p
        WHERE p.id_part = c.id_part 
        and id_membre= :id_membre";

    $stid_membre = executerReq($idcom, $requete_membre, [":id_membre"], [$ID_MEMBRE_CONNECTE]);

    if ($result = oci_fetch_array($stid_membre, OCI_ASSOC + OCI_RETURN_NULLS)) {
        $nom = $result['NOM'];
        $prenom = $result['PRENOM'];
        $dateNaiss = $result['DATENAISS'];
        $nation = $result['NATIONALITE'];
    }
} else {
    echo "you are not allowed to enter this page : access forbidden <>";
    die;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Profile</title>
</head>

<body>
    <a href="memberLogout.php" class="logout">Logout</a>
    <div class="info_perso">
        <p>Information personnelles</p><br>
        <label>Nom :</label> <?php echo "<strong>" . $nom . "</strong><br>" ?>
        <label>Prénom :</label> <?php echo "<strong>" . $prenom . "</strong><br>" ?>
        <label>Date de Naissance :</label> <?php echo "<strong>" . $dateNaiss . "</strong><br>" ?>
        <label>Nationalité :</label> <?php echo "<strong>" . $nation . "</strong><br>" ?>
    </div>

    <div class="modify">
        <a href="login_logout/modifyInfo.php">modifier les informations</a>
        <a href="login_logout/modify_psswd.php">modifier le mot de passe </a>
    </div>
    <br><br><br><br>

    <p>chercher des participants :</p>
    <a href='Moteurs_DE_Recherche/searching.athletes.php'>chercher athletes</a>
    <a href='Moteurs_DE_Recherche/searching.arbitre.php'>chercher arbitre</a>
    <a href='Moteurs_DE_Recherche/searching.coachs.php'>chercher coachs</a>
    <a href='Moteurs_DE_Recherche/searching.competition.php'>chercher competition</a>

    <p>chercher des equipes :</p>
</body>

</html>