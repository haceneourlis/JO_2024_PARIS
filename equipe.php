<?php
session_start();
include("connexion_OCI.php");
include_once("fonctions.php");
$idcom = connexion_OCI();
if (!$idcom) {
    echo "connexion à la base de données IMPOSSIBLE ///";
    exit;
}
?>

<h1> EQUIPES </h1>
<div class="athlete-container">
    <?php
	$id_equipe = isset($_GET["id_eq"]) ? $_GET["id_eq"] : "";
	$NOM_EQUIPE = isset($_GET["nom_eq"]) ? $_GET["nom_eq"] : "";
	$nom_coach = isset($_GET["nom_coach"]) ? $_GET["nom_coach"] : "";
	$prenom_coach = isset($_GET["prenom_coach"]) ? $_GET["prenom_coach"] : "";
	



    echo "NOM EQUIPE : $NOM_EQUIPE" . "<br>";
    echo "NOM DU COACH : $nom_coach $prenom_coach" . "<br>";

    // afficher les membre de l'equipe avec des lines sur leurs pages athlete.php?id_at = ...
    echo "les membres de l'equipes sont : "."<br>";
    $requete_athlete_dequipe = "SELECT p.NOM  , p.PRENOM , p.ID_ATHLETE , te.ROLE_TEAM
    FROM participant p , ALLTEAMED te , athlete a 
    WHERE p.id_part = a.id_part 
    and a.id_athlete = te.id_athlete 
    and te.id_equipe = :id_equipe 
    order by p.NOM,p.PRENOM ";
    $athl_eq_stid = executerReq($idcom, $requete_athlete_dequipe, [":id_equipe"], [$id_equipe]);
    echo "hh";
    echo "<table border = 1>";
    echo "<th> NOM </th> <th> PRENOM </th> <th> ROLE </th>";
    while ($row = oci_fetch_array($athl_eq_stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
		$id_athlete = $row['ID_ATHLETE'];
		echo "<tr>";
		echo "<td>{$row['NOM']}</td>";
		echo "<td>{$row['PRENOM']}</td>";
		echo "<td>{$row['ROLE_TEAM']}</td>";
		echo "</tr>";
    }

    echo "</table> ";
    
    echo "<br><br><br> ------------------------------------ <br><br><br>";
    // afficher ses resultats pendant ces JO :
    $requete_equipe_res = "SELECT o.CLASSEMENT_EQUIPE , o.RESULTAT_EQUIPE , d.NOM_DISCP
        from OBTIENT_RESULTATS_EQUIPE o , equipe  e , discipline d , competition c 
        where e.id_equipe =:id_eq
        and  e.id_equipe = o.id_equipe 
        and o.id_compet = c.id_compet
        and c.id_discp = d.id_discp 
        order by o.CLASSEMENT_EQUIPE ";

    $equipe_results_stid = executerReq($idcom, $requete_equipe_res, [":id_eq"], [$id_equipe]);
    afficherRes(
        $equipe_results_stid,
        ["classement", "resultats ", "Discipline"],
        ["CLASSEMENT_EQUIPE", "RESULTAT_EQUIPE", "NOM_DISCP"]
    );
    echo "<a href='membreProfile.php' >go back to main profile </a>";
    ?>
</div>