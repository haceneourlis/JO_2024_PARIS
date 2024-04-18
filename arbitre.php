<?php
session_start();
include("connexion_OCI.php");
include_once("fonctions.php");
$idcom = connexion_OCI();
if (!$idcom) {
	echo "connexion à la base de données IMPOSSIBLE ///";
	exit;
}


if (isset($_GET["id_arbitre"])) {
	$id_arbitre_pers = $_GET["id_arbitre"];
	$requete_arb = "SELECT  p.NOM, p.PRENOM, p.DATENAISS, p.NATIONALITE
    FROM participant p, personnel pr 
    WHERE p.id_part = pr.id_part 
	AND pr.id_pers = :id_pers";

	$arb_stid = executerReq($idcom, $requete_arb, [":id_pers"], [$id_arbitre_pers]);


	afficherRes(
		$arb_stid,
		["Nom", "Prenom ", "Date de naissance", "Nationalite"],
		["NOM", "PRENOM", "DATENAISS", "NATIONALITE"]
	);

	echo "<br><br>";
	// afficher les coompets ou ils étaient arbitres : 
	echo "était abitre de : <br>";


	$requete = "SELECT TYPE_EPREUVE , NUM_EPREUVE
	FROM PERSONNEL pr , participe ptcp , participant p
	WHERE p.id_part = pt.id_part 
	AND ptcp.id_part = pr.id_part 
	AND pr.id_pers = :id_pers ";

	$arbi_stid = executerReq($idcom, $requete, [":id_pers"], [$id_arbitre_pers]);

	afficherRes(
		$arbi_stid,
		["Type Epreuve", "Num Epreuve"],
		["TYPE_EPREUVE", "NUM_EPREUVE"]
	);
}
echo "<a href='membreProfile.php'>go back to main profile</a>";
