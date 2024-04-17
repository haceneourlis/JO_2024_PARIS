<?php

session_start();
include("connexion_OCI.php");
include_once("fonctions.php");
$idcom = connexion_OCI();
if (!$idcom) {
    echo "connexion à la base de données IMPOSSIBLE ///";
    exit;
}

if (empty($_SESSION['ID_MEMBRE_CONNECTE'])) {
    echo "you are not allowed to enter this page ";
    die;
}

if (!empty($_POST["change-results"])) {
    if (!empty($_POST["classement_change"]) && !empty($_POST["result_change"])) {
        $id_competition = $_POST['competID'];
        foreach ($_POST["classement_change"] as $idpart => $new_classement) {
            $new_result = $_POST["result_change"][$idpart];

            $requete_modify = "UPDATE obtient_resultats_athlete 
                      SET CLASSEMENT_ATHLETE = :classement, RESULTAT_ATHLETE = :result 
                      WHERE ID_PART = :idpart 
                      AND ID_COMPET = :id_compet";

            $compet_stid = executerReq(
                $idcom,
                $requete_modify,
                [":classement", "result", "idpart", "id_compet"],
                [$new_classement, $new_result, $idpart, $id_competition]
            );
        }

        header("Location: competition.administration.modif.php");
        exit;
    } else {
        echo "ACCES FORBIDDEN <:>";
        echo "<a href='index.php' > go back to main page</a>";
        exit;
    }
}
