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
echo $_SESSION['ID_MEMBRE_CONNECTE'];
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
        <strong> Changer votre mot de passe </strong>
        <br><br> ANCIEN MOT DE PASSE : <input type="text" name="old_mdp">
        <br><br> NOUVEAU MOT DE PASSE : <input type="text" name="new_mdp">
        <br><br>
        <a href="index.php">Retour à la page principale</a>
        <input type="submit" value="Submit">
    </form>
</body>

</html>

<?php
if (isset($_POST["new_mdp"]) && isset($_POST["old_mdp"])) {
    $old_passwd = $_POST["old_mdp"];
    
    $requete_passwd = "SELECT MOT_DE_PASSE FROM comite WHERE ID_MEMBRE = :ID_MEMBRE";
    
    $stid_passwd = executerReq($idcom, $requete_passwd, [":ID_MEMBRE"], [$_SESSION['ID_MEMBRE_CONNECTE']]);

    if ($result = oci_fetch_array($stid_passwd, OCI_ASSOC + OCI_RETURN_NULLS)) {
        if ($old_passwd !== $result['MOT_DE_PASSE']) {
            echo "Le mot de passe est incorrect !";
        } else {
            $new_passwd = $_POST["new_mdp"];
            echo "new_passwd==>".$new_passwd ."<br>";
            $pattern = "/^[A-Za-z0-9@!#$%^&*_+-=]{1,32}$/";
            if (!preg_match($pattern, $new_passwd)) {
                echo "Veuillez entrer un mot de passe valide s'il vous plaît. Il doit contenir au moins une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial.";
            } 
            else {
				$mdp_Hashé = password_hash($new_passwd, PASSWORD_DEFAULT);
				if ($mdp_Hashé === false) {
					echo "Password hashing failed.";
					exit;
				}
				
				// Limit the hashed password to 32 characters
				$mdp_Hashé = substr($mdp_Hashé, 0, 32);

                echo "hashed potato ==>".$mdp_Hashé;
                $requete_change_passwd = "UPDATE comite SET MOT_DE_PASSE = :nouveauPsswd WHERE id_membre = :id_membre";          
                $stid_change_passwd = executerReq($idcom, $requete_change_passwd, [":nouveauPsswd", ":id_membre"], [$mdp_Hashé, $_SESSION['ID_MEMBRE_CONNECTE']]);
                var_dump($stid_change_passwd);
                if (!$stid_change_passwd) {
                    echo "Impossible de changer le mot de passe !";
                    exit;
                } else {
                    echo "Mot de passe changé avec succès.";
                }
            }
        }
    } else {
        echo "Problème avec la requête de sélection du mot de passe.";
    }
}

?>
