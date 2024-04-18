<?php
session_start();
include("connexion_OCI.php");
include_once("fonctions.php");
$idcom = connexion_OCI();
if (!$idcom) {
	echo "connexion à la base de données IMPOSSIBLE ///";
	exit;
} else {
	echo "connextion reussi" . "<BR>";
}


echo "<a href='membreProfile.php'>Retour au profil</a><br><br>";

if (isset($_GET["id_compet"]) && isset($_GET["type_co"])) {
	$id_compet = $_GET["id_compet"]; // Récupérer l'identifiant de la compétition depuis l'URL
	$type_compet = $_GET["type_co"]; // Récupérer le type de compétition depuis l'URL

	echo "COMPETITION : " . $type_compet . "<br><br>";

	// ajouter un participant à la competition .

	// si c'est un ajout en quart de finale : 
	// un formulaire : select un participant dans une liste (TOUS - ceux qui participent déjà .) ... 
	// 				   pouvoit creér un nouveau participant (arbitre ? athlete ? ... ) si il n'existe pas .

	/* 	UN ATHLETE 	*/


	// si c'est un ajout en demi : 
	// dans ce cas il faut choisir quelqu'un qui a participé au QUART DE FINAL . 
	// idem pour la finale . 

	// afficher les athletes participants à la compet :
	$requete_athletes = "SELECT p.id_part, p.NOM, p.PRENOM 
	FROM competition c, participe pt, participant p, athlete a 
	WHERE c.id_compet =  :ID_COMPET
	AND a.id_part = p.id_part
	AND pt.id_part = p.id_part
	AND c.id_compet = pt.id_compet
	AND upper(pt.TYPE_EPREUVE) = :TYPE_EPREUVE
	AND NUM_EPREUVE = :NUM_EPREUVE";
	// afficher les arbitres : 
	$requete_arbitres = "SELECT p.ID_PART,p.NOM,p.PRENOM 
		from competition c , participe pt , participant p , PERSONNEL prs 
		where c.id_compet = pt.id_compet 
		and p.id_part = pt.id_part 
		and p.id_part = prs.id_part 
		and upper(prs.TYPE_PERS) ='ARBITRE'
		and c.ID_COMPET = :ID_COMPET 
		AND upper(pt.TYPE_EPREUVE) = :TYPE_EPREUVE
		AND NUM_EPREUVE = :NUM_EPREUVE ";


	echo "<table border='5'><th colspan='4'> QUART DE FINALE </th> </table>";

	echo "<form method='post' action='delete_participant.php'>";
	echo "<input type='hidden' name='id_compet' value='$id_compet'>";
	echo "<input type='hidden' name='type_compet' value='$type_compet'>";


	echo "<table border='3'>";
	echo "<th> Epreuve  </th> <th>Athlete </th> <th>Arbitre </th>";
	for ($j = 1; $j <= 4; $j++) {
		$athlete_stid = executerReq($idcom, $requete_athletes, [":ID_COMPET", ":TYPE_EPREUVE", "NUM_EPREUVE"], ["$id_compet", "QUART DE FINALE", $j]);
		$arbitre_stid = executerReq($idcom, $requete_arbitres, [":ID_COMPET", ":TYPE_EPREUVE", "NUM_EPREUVE"], ["$id_compet", "QUART DE FINALE", $j]);
		echo "<tr> <td> Quart de finale num {$j} : </td>";
		//athletes:
		echo "<td>";
		while ($row = oci_fetch_array($athlete_stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
			echo "<input type='checkbox' name='part_supp_array[]' value='QUART DE FINALE_{$row["ID_PART"]}_{$j}'> {$row["NOM"]} {$row["PRENOM"]}<br>";
		}
		echo "</td>";
		// arbitres: 
		echo "<td>";
		while ($row = oci_fetch_array($arbitre_stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
			echo "<input type='checkbox' name='part_supp_array[]' value='QUART DE FINALE_{$row["ID_PART"]}_{$j}'> {$row["NOM"]} {$row["PRENOM"]}<br>";
		}
		echo "</td>";
		echo "</tr>";
	}
	echo "</table>";



	// les demis-finale : 
	echo "<table border = 5><th colspan=3> DEMI FINALE </th> </table>";
	echo "<table border = 3>";
	echo "<th> Epreuve  </th> <th>athlete </th> <th>arbitres </th>";
	for ($j = 1; $j <= 2; $j++) {
		$athlete_stid = executerReq($idcom, $requete_athletes, [":ID_COMPET", ":TYPE_EPREUVE", "NUM_EPREUVE"], ["$id_compet", "DEMI-FINALE", $j]);
		$arbitre_stid = executerReq($idcom, $requete_arbitres, [":ID_COMPET", ":TYPE_EPREUVE", "NUM_EPREUVE"], ["$id_compet", "DEMI-FINALE", $j]);
		echo "<tr> <td> demi-finale num {$j} : </td>";
		// afficher les athletes :
		echo "<td>";
		while ($row = oci_fetch_array($athlete_stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
			echo "<input type='checkbox' name='part_supp_array[]' value='DEMI-FINALE_{$row["ID_PART"]}_{$j}'> {$row["NOM"]} {$row["PRENOM"]}<br>";
		}
		echo "</td>";

		//afficher les arbitres :
		echo "<td>";
		while ($row = oci_fetch_array($arbitre_stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
			echo "<input type='checkbox' name='part_supp_array[]' value='DEMI-FINALE_{$row["ID_PART"]}_{$j}'> {$row["NOM"]} {$row["PRENOM"]}<br>";
		}
		echo "</td>";
		echo "</tr>";
	}
	echo "</table>";


	// la finale : 
	echo "<table border = 5><th colspan=3>FINALE </th> </table>";
	echo "<table border = 3>";
	echo "<th>athlete </th> <th>arbitres </th>";

	$athlete_stid = executerReq($idcom, $requete_athletes, [":ID_COMPET", ":TYPE_EPREUVE", "NUM_EPREUVE"], ["$id_compet", "FINALE", 1]);
	$arbitre_stid = executerReq($idcom, $requete_arbitres, [":ID_COMPET", ":TYPE_EPREUVE", "NUM_EPREUVE"], ["$id_compet", "FINALE", 1]);
	echo "<tr><td>";
	while ($row = oci_fetch_array($athlete_stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
		echo "<input type='checkbox' name='part_supp_array[]' value='FINALE_{$row["ID_PART"]}_1'> {$row["NOM"]} {$row["PRENOM"]}<br>";
	}
	echo "</td>";
	echo "<td>";
	while ($row = oci_fetch_array($arbitre_stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
		echo "<input type='checkbox' name='part_supp_array[]' value='FINALE_{$row["ID_PART"]}_1'> {$row["NOM"]} {$row["PRENOM"]}<br>";
	}
	echo "</td>";
	echo "</tr>";
	echo "</table>";

	echo "<button type='submit' name ='supprimerAA' >retirer les participants selectionnés </button>";
	echo "</form>";
	// Assurez-vous d'avoir une fonction executerReq() sécurisée qui utilise des requêtes préparées.
	
	// Requête SQL avec des paramètres nommés
	$requete_resultats = "SELECT p.ID_PART, p.NOM, p.PRENOM, o.CLASSEMENT_ATHLETE, o.RESULTAT_ATHLETE
						 FROM competition c
						 JOIN obtient_resultats_athlete o ON c.id_compet = o.id_compet
						 JOIN athlete a ON a.id_athlete = o.id_athlete
						 JOIN participant p ON a.id_part = p.id_part
						 WHERE c.id_compet = :id_compet
						 ORDER BY o.CLASSEMENT_ATHLETE";
	
	// Exécution de la requête avec le bon paramètre
	$compet_stid = executerReq($idcom, $requete_resultats, [":id_compet"], [$id_compet]);
	
	// Vérification si des résultats sont retournés avant d'afficher le formulaire
	if (oci_fetch($compet_stid)) {
		echo "<form method='post' action='competition.administration.change_Results.php'>";
		echo "<input type='hidden' name='competID' value='{$id_compet}'>";
		echo "<table border='1'>";
		echo "<th>NOM</th><th>PRENOM</th><th>Classement</th><th>Résultats</th>";
		
		// Boucle sur les résultats et affichage dans le formulaire
		while ($row = oci_fetch_assoc($compet_stid)) {
			echo "<tr>";
			echo "<td>{$row['NOM']}</td>
				  <td>{$row['PRENOM']}</td>
				  <td><input type='text' name='classement_change[{$row['ID_PART']}]' value='{$row['CLASSEMENT_ATHLETE']}'></td>
				  <td><input type='text' name='result_change[{$row['ID_PART']}]' value='{$row['RESULTAT_ATHLETE']}'></td>";
			echo "</tr>";
		}
		
		echo "</table>";
		echo "<input type='submit' name='change-results' value='Changer les résultats'>";
		echo "</form>";
	} else {
		echo "Aucun résultat trouvé pour cette compétition.";
	}
}