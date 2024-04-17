<?php
session_start();
include("connexion_OCI.php");
include_once("fonctions.php");
$idcom = connexion_OCI();
if (!$idcom) {
    echo "connexion à la base de données IMPOSSIBLE ///";
    exit;
} else {
    echo "connextion reussi"."<BR>";
}


echo "<a href='membreProfile.php'>Retour au profil</a><br><br>";



$id_compet = $_GET["id_compet"]; // Récupérer l'identifiant de la compétition depuis l'URL
$type_compet = $_GET["nom_compet"]; // Récupérer le type de compétition depuis l'URL

echo "COMPETITION : " . $type_compet . "<br><br>";


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
    
    
    
// les quarts de finale : 
echo "<table border = 5><th colspan=3> QUART DE FINALE </th> </table>";

echo "<table border = 3>";
echo "<th> Epreuve  </th> <th>athlete </th> <th>arbitres </th>";
for ($j = 1; $j <= 4; $j++) {
	$athlete_stid = executerReq($idcom, $requete_athletes, [":ID_COMPET",":TYPE_EPREUVE", "NUM_EPREUVE"], ["$id_compet","QUART DE FINALE", $j]);
	$arbitre_stid = executerReq($idcom, $requete_arbitres, [":ID_COMPET",":TYPE_EPREUVE", "NUM_EPREUVE"], ["$id_compet","QUART DE FINALE", $j]);
	echo "<tr> <td> quart de finale num {$j} : </td>";
	// afficher les athletes :
	echo "<td>";
	while ($row = oci_fetch_array($athlete_stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
		echo "<p>{$row["NOM"]} {$row["PRENOM"]}</p>";
	}
	echo "</td>";
	
	//afficher les arbitres :
	echo "<td>";
	while ($row = oci_fetch_array($arbitre_stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
		echo "<p>{$row["NOM"]} {$row["PRENOM"]}</p>";
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
	$athlete_stid = executerReq($idcom, $requete_athletes, [":ID_COMPET",":TYPE_EPREUVE", "NUM_EPREUVE"], ["$id_compet","DEMI-FINALE", $j]);
	$arbitre_stid = executerReq($idcom, $requete_arbitres, [":ID_COMPET",":TYPE_EPREUVE", "NUM_EPREUVE"], ["$id_compet","DEMI-FINALE", $j]);
	echo "<tr> <td> demi-finale num {$j} : </td>";
	// afficher les athletes :
	echo "<td>";
	while ($row = oci_fetch_array($athlete_stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
		echo "<p>{$row["NOM"]} {$row["PRENOM"]}</p>";
	}
	echo "</td>";
	
	//afficher les arbitres :
	echo "<td>";
	while ($row = oci_fetch_array($arbitre_stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
		echo "<p>{$row["NOM"]} {$row["PRENOM"]}</p>";
	}
	echo "</td>";
	echo "</tr>";
}
echo "</table>";


// la finale : 
echo "<table border = 5><th colspan=3>FINALE </th> </table>";
echo "<table border = 3>";
echo "<th>athlete </th> <th>arbitres </th>";

$athlete_stid = executerReq($idcom, $requete_athletes, [":ID_COMPET",":TYPE_EPREUVE", "NUM_EPREUVE"], ["$id_compet","FINALE", 1]);
$arbitre_stid = executerReq($idcom, $requete_arbitres, [":ID_COMPET",":TYPE_EPREUVE", "NUM_EPREUVE"], ["$id_compet","FINALE", 1]);
echo "<tr><td>";
while ($row = oci_fetch_array($athlete_stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
	echo "<p>{$row["NOM"]} {$row["PRENOM"]}</p>";
}
echo "</td>";
echo "<td>";
while ($row = oci_fetch_array($arbitre_stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
	echo "<p>{$row["NOM"]} {$row["PRENOM"]}</p>";
}
echo "</td>";
echo "<tr>";
echo "</table>";



// RESULTATS DE LA COMPETITIONS : 
$requete_resultats = "SELECT p.NOM , p.PRENOM , o.CLASSEMENT_ATHLETE , o.RESULTAT_ATHLETE
FROM competition c , obtient_resultats_athlete o ,athlete a ,participant p
where c.id_compet = o.id_compet 
and a.id_athlete = o.id_athlete 
and a.id_part = p.id_part 
and c.id_compet =:id_compet
ORDER BY o.CLASSEMENT_ATHLETE ";

$compet_stid = executerReq($idcom, $requete_resultats, [":id_compet"], [$id_compet]);
afficherRes($compet_stid,
	["NOM", "PRENOM ", "CLASSEMENT", "RESULTATS"],
	["NOM", "PRENOM", "CLASSEMENT_ATHLETE", "RESULTAT_ATHLETE"]);