<?php
session_start();
include("connexion_OCI.php");
include_once("fonctions.php");
$idcom = connexion_OCI();
if (!$idcom) {
    echo "connexion à la base de données IMPOSSIBLE ///";
    exit;
} else {
    echo "connexion réussie" . "<br>";
}

if (!empty($_POST["id_compet"]) && !empty($_POST["type_compet"])) {
    $id_compet = $_POST["id_compet"];
    $type_compet = $_POST["type_compet"];

    if (isset($_POST["supprimerAA"])) {
        if (!empty($_POST['part_supp_array'])) {
            $part_a_retirer_array = $_POST['part_supp_array'];
            foreach ($part_a_retirer_array as $part) {
                $array_id_num = explode("_", $part);
                $type_epreuve = $array_id_num[0];

                $requete_supp_part = "DELETE FROM participe 
                WHERE  ID_COMPET = :ID_COMPET 
                AND  ID_PART = :ID_PART
                AND upper(TYPE_EPREUVE) = upper(:TYPE_EPREUVE)
                AND NUM_EPREUVE = :NUM_EPREUVE";
                $delete_stid = executerReq(
                    $idcom,
                    $requete_supp_part,
                    [":ID_COMPET", ":ID_PART", "TYPE_EPREUVE", "NUM_EPREUVE"],
                    [$id_compet, $array_id_num[1], $type_epreuve, $array_id_num[2]]
                );

                if (!$delete_stid) {
                    echo "Échec de la suppression de l'arbitre avec l'ID : {$array_id_num[1]} <br>";
                    die;
                }
            }
        }
    }
}
header("Location: competition.administration.modif.php?id_compet=" . $id_compet . "&type_co=" . urlencode($type_compet));
