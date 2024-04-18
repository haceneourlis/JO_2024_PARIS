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
// afficher les competitions .
$requete_athletes = "SELECT ID_COMPET ,TYPE_COMPET
from COMPETITION
order by TYPE_COMPET";

$athlete_stid = executerReq($idcom, $requete_athletes, [], []);
if (!$athlete_stid) {
    echo "can not execute query !";
    exit;
}


echo "<table border>";
echo "<th> LES COMPETITIONS </th> ";
echo "<tr> <td><strong> COMPETITION </strong></td> </tr>";

// Traitement des résultats
while ($row = oci_fetch_array($athlete_stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
    $id_compet_row = $row["ID_COMPET"];
    $compet_rowName = $row["TYPE_COMPET"];
    echo "<tr> 
    <td>  {$row['TYPE_COMPET']} </td> 
    <!-- button modifier -->
	 <td>
		<form method='get'>
			<input type='hidden' name='compet_id_tomodify' value='$id_compet_row'>
			<a href='competition.administration.modif.php?id_compet=" . $id_compet_row . "&type_co=\"" . $compet_rowName . "\"'>
				<button class='modify-button' type='button' name='modify_compet'></button>
			</a>
		</form> 
	</td>


    <!-- button supprimer -->
    <td> 
        <form method='get'>
        <input type='hidden' name='compet_id_todelete' value='$id_compet_row'>
        <button class='delete-button' type='submit' name='supp_compet'></button>
        </form> 
    </td>
    </tr> ";
}
echo "</table>";

if (isset($_POST['supp_compet'])) // si on a cliqué sur le button supprimer .
{
    $id_compet_supp = $_POST['compet_id_todelete'];
    $requete_compet_supp = "DELETE FROM COMPETITION  WHERE ID_COMPET = :ID_COMPET";
    $compet_stid_supp = executerReq($idcom, $requete_compet_supp, [":ID_COMPET"], [$id_compet_supp]);
    if (!$compet_stid_supp) {
        // Libération des ressources
        oci_free_statement($compet_stid_supp);
        oci_close($idcom);

        echo "la competition $id_compet_supp a été supprimé avec succès !";
    }
    echo "<meta http-equiv='refresh' content='0;url=athletes.administration.php'>";
    exit;
}
