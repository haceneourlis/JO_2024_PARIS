<?php
session_start();
include("connexion_OCI.php");
include_once("fonctions.php");
$idcom = connexion_OCI();
if (!$idcom) {
    echo "connexion à la base de données IMPOSSIBLE ///";
    exit;
}

if(isset($_POST['type_co']) && isset($_POST['Id_compet']) ){
	    $type_co = $_POST["type_co"];
	    $id_compet = $_POST["Id_compet"];
}

if (isset($_POST['modifier'])) {
    if (
        isset($_POST['classement'])
        && isset($_POST['result'])
        && is_numeric($_POST['classement'])
        && is_numeric($_POST['result'])
    ) {
        $classement = $_POST['classement'];
        $resultat = $_POST['result'];
        $id_athlete = $_POST['id_athlete'];
        $id_compet = $_POST['id_compet'];

        // Update the athlete's result in the database
        $requete = "UPDATE obtient_resultats_athlete 
                    SET CLASSEMENT_ATHLETE = :classement, RESULTAT_ATHLETE = :resultat 
                    WHERE ID_ATHLETE = :id_athlete AND ID_COMPET = :id_compet";

        $resultat_stid = executerReq(
            $idcom,
            $requete,
            [":classement", ":resultat", ":id_athlete", "id_compet"],
            [$classement, $resultat, $id_athlete, $id_compet]
        );
          
        if ($resultat_stid) {
        	setcookie("resultat_modifier_now","succes_modif",time() + 60 , "/");  
			header("Location: competition.administration.modif.php?id_compet=".$id_compet."&type_co=".$type_co."");
			exit; 
        } else {
        	setcookie("resultat_modifier_now","error",time() + 60 , "/");  
			header("Location: competition.administration.modif.php?id_compet=".$id_compet."&type_co=".$type_co."");
			exit; 
        }
    }
}

echo "<a href='competition.administration.modif.php?id_compet=".$id_compet."&type_co=".$type_co."'> GO BACK </a> <br><br><br><br>";

if (isset($_POST["modify_result"])) {
    $result_to_modify = $_POST["id_athlete_result_modify"];
    $result_to_modify = explode("###", $result_to_modify);
    $id_athlete = $result_to_modify[0];
    $id_compet = $result_to_modify[1];

    

    echo "<form method='POST'>
    Classement : <input type='number' name='classement' step='1' required ><br>
    Resultat : <input type='number' name='result'step='0.01' required ><br>
    
    <input type='hidden' name='id_athlete' value='$id_athlete'>
    <input type='hidden' name='id_compet' value='$id_compet'>

    <input type='submit' name='modifier' value='modify'>
    </form>";
}


