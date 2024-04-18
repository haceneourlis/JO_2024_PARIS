<?php
session_start();
include("connexion_OCI.php");
include_once("fonctions.php");
$idcom = connexion_OCI();
if (!$idcom) {
    echo "connexion à la base de données IMPOSSIBLE ///";
    exit;
}


if (isset($_GET["id_at"])) {
    $id_athlete = $_GET["id_at"];
    $requete_athletes = "SELECT p.NOM, p.PRENOM, p.DATENAISS, p.NATIONALITE, a.POIDS, a.TAILLE
                      FROM participant p, athlete a 
                      WHERE p.id_part = a.id_part 
                      AND a.ID_ATHLETE = :ID_ATHLETE";



    $athlete_stid = executerReq($idcom, $requete_athletes, [":ID_ATHLETE"], [$id_athlete]);


    afficherRes(
        $athlete_stid,
        ["Nom", "Prenom ", "Date de naissance", "Nationalite", "Poids (en kg)", "taille (en m)"],
        ["NOM", "PRENOM", "DATENAISS", "NATIONALITE", "POIDS", "TAILLE"]
    );

    // afficher le palmarés de l'athlete /
    echo "<br><br> <strong> PALMARES </strong>";
    $requete_palmares = "SELECT p.CLASSEMENT, p.SCORE_PAL, p.DATE_PAL, d.NOM_DISCP
					FROM palmares p, athlete a, discipline d 
					WHERE a.id_athlete = :id_athlete 
					AND a.id_athlete = p.id_athlete
					AND d.id_discp = p.id_discp 
					ORDER BY p.CLASSEMENT";

    $palmares_stid = executerReq($idcom, $requete_palmares, [":id_athlete"], [$id_athlete]);
    afficherRes(
        $palmares_stid,
        ["Classment", "Score", "Date du resultat", "Discipline"],
        ["CLASSEMENT", "SCORE_PAL", "DATE_PAL", "NOM_DISCP"]
    );

    // afficher les resultats 2024: 
    echo "<br><br> <strong> RESULTATS 2024 </strong>";
    $requete_resultats = "SELECT o.CLASSEMENT_ATHLETE, o.RESULTAT_ATHLETE, d.NOM_DISCP
					FROM OBTIENT_RESULTATS_ATHLETE o, athlete a, discipline d, competition c 
					WHERE a.id_athlete = :id_athlete 
					AND a.id_athlete = o.id_athlete 
					AND o.id_compet = c.id_compet
					AND c.id_discp = d.id_discp 
					ORDER BY o.CLASSEMENT_ATHLETE";

    $results_stid = executerReq($idcom, $requete_resultats, [":id_athlete"], [$id_athlete]);
    afficherRes($results_stid, ["Classment", "Resultat ", "Discipline"], ["CLASSEMENT_ATHLETE", "RESULTAT_ATHLETE", "NOM_DISCP"]);
}


echo "<a href='membreProfile.php'>go back to main profile</a>";
