<?php
session_start();
include("connexion_OCI.php");
include_once("fonctions.php");
$idcom = connexion_OCI();
if (!$idcom) {
    echo "connexion à la base de données IMPOSSIBLE ///";
    exit;
} else {
    echo "connextion reussi";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membre du comite</title>
</head>

<body>
    <h1>Page réservée aux membres du comité</h1>
    <form method="post">
        <input type="text" name="id_membre" placeholder="Entrez votre identifiant ici">
        <input type="password" name="mdp_membre" placeholder="Entrez votre mot de passe ici">
        <input type="submit" name="seConnecter" value="Se connecter">
    </form>
</body>

</html>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $typed_id = trim($_POST['id_membre']);
    $typed_mdp = trim($_POST['mdp_membre']);


    if (!empty($typed_id) && !empty($typed_mdp)) {
    	echo $typed_id ."----".$typed_mdp."<br>";
        $requete_psswd = "SELECT ID_MEMBRE, MOT_DE_PASSE
            FROM COMITE
            WHERE ID_MEMBRE = :ID_MEMBRE";

        $stid_passwd = executerReq($idcom, $requete_psswd, [":ID_MEMBRE"], [$typed_id]);
        if ($row = oci_fetch_array($stid_passwd, OCI_ASSOC + OCI_RETURN_NULLS)) {
            // Verifiant le mot de passe .
            //  if (password_verify($typed_mdp, $row['MOT_DE_PASSE'])) {
            if ($typed_mdp === $row['MOT_DE_PASSE']) {
                // le membre du comité maintenant est correctement connecté à une session . 
                $_SESSION['ID_MEMBRE_CONNECTE'] = $row['ID_MEMBRE'];
                header('Location: membreProfile.php');
                exit;
            } else {
                echo "Mot de passe ou identifiant incorrect.";
                echo "attention , ces lignes sont distiner à nous aider au debogage , c tt." . "<br>";
                echo "mot de passe dans la base de données : " . $row['MOT_DE_PASSE'];
                echo "<br> ---------- <br>";
                echo "ce que tu as écrit : " . $typed_mdp;
            }
        } else {
            echo "la connexion à la base a echoué ... !";
        }
    }
}
?>