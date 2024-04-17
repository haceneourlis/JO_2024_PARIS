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
echo "
<style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        .delete-button,
        .modify-button {
            width: 30px;
            height: 30px;
            border-radius: 50%; /* Makes the button round */
            font-size: 16px;
            cursor: pointer;
            border: none;
            outline: none;
            background-color: #ff6347; /* Red color */
            color: #fff;
            background-size: cover; /* Make the background image cover the entire button */
            background-repeat: no-repeat; 
            background-position: center; 
        }

        .modify-button {
            background-image: url('assets/image_modifier.png'); /* Path to your image */
            background-color: transparent; /* Remove the background color */
        }

        .delete-button {
            background-image: url('assets/delete_image.png'); /* Path to your image */
            background-color: transparent; /* Remove the background color */
        }
    </style>
";

$requete_athletes = "SELECT p.ID_PART , p.NOM , p.PRENOM , p.DATENAISS , p.NATIONALITE 
from participant p , athlete a 
where p.id_part = a.id_part 
order by p.nom , p.prenom ";

$athlete_stid = executerReq($idcom, $requete_athletes, [], []);
if (!$athlete_stid) {
    echo "can not execute query !";
    exit;
}


echo "<table border = 1>";
echo "<th> LES ATHLETES </th> ";
echo "<tr> <td><strong> NOM </strong></td> <td><strong> PRENOM </strong></td>
<td><strong>date de naissance </strong></td> <td><strong> NATIONALITE</strong></td> 
<td> MODIFIER </td><td>  SUPPRIMER </td></tr>";

// Traitement des résultats
while ($row = oci_fetch_array($athlete_stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
    $id_tobe_deleted = $row["ID_PART"];
    echo "<tr> 
    <td>{$row["NOM"]}</td> 
    <td>{$row["PRENOM"]}</td>
    <td>{$row["DATENAISS"]}</td>
    <td>{$row["NATIONALITE"]}</td>
    <!-- button modifier -->
    <td><form method='post'>
    <input type='hidden' name='athlete_id_tomodify' value='$id_tobe_deleted'>
    <button class='modify-button' type='submit' name='modify_athlete'></button>
    </form> </td>

    <!-- button supprimer -->
    <td> <form method='post'>
    <input type='hidden' name='athlete_id_todelete' value='$id_tobe_deleted'>
    <button class='delete-button' type='submit' name='supp_athlete'></button>
    </form> </td>
    </tr> ";
}
echo "</table>";

if (isset($_POST['supp_athlete'])) // si on a cliqué sur le button supprimer .
{
    $id_part_dathlete = $_POST['athlete_id_todelete'];
    $requete_athletes = "DELETE FROM participant  WHERE ID_PART = :ID_PART";
    $athlete_stid_d = executerReq($idcom, $requete_athletes, [":ID_PART"], [$id_part_dathlete]);
    if ($athlete_stid_d != null) {
        // Libération des ressources
        oci_free_statement($athlete_stid_d);
        oci_close($idcom);

        echo "l'athlete $id_part_dathlete a été supprimé avec succès !";
    }
    echo "<meta http-equiv='refresh' content='0;url=athletes.administration.php'>";
    exit;
}

if (isset($_POST["modify_athlete"])) {
    // on doit afficher un FORMULAIRE (mais un peu plus stylé que ça ...)

    echo "<div class='modifier-container'
    <form method='post' >
    nom : <input type='text' name='modified_nom'><br>
    prenom : <input type='text' name='modified_prenom'><br>
    Date de Naissance : <input type='text' name='modified_date'><br>
    Nationalite : <input type='text' name ='nation'><br>
    </form>
    </div>";
}