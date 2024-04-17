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
    echo "YOU  are NOT allowed TO enter THIS page , get the hell out !";
    exit;
}

$ID_MEMBRE_CONNECTE = $_SESSION['ID_MEMBRE_CONNECTE'];

$requete_membre = "SELECT NOM, PRENOM, DATENAISS, NATIONALITE 
    FROM comite c, participant p
    WHERE p.id_part = c.id_part 
    and id_membre= :id_membre";

$stid_membre = executerReq($idcom, $requete_membre, [":id_membre"], [$ID_MEMBRE_CONNECTE]);

if ($result = oci_fetch_array($stid_membre, OCI_ASSOC + OCI_RETURN_NULLS)) {
    $nom = $result['NOM'];
    $prenom = $result['PRENOM'];
    $dateNaiss = $result['DATENAISS'];
    $nation = $result['NATIONALITE'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Profile</title>
</head>

<body>
    <a href="memberLogout.php" class="logout">Logout</a>
    <div class="info_perso">
        <p>Information personnelles</p><br>
        <label>Nom :</label> <?php echo "<strong>" . $nom . "</strong><br>" ?>
        <label>Prénom :</label> <?php echo "<strong>" . $prenom . "</strong><br>" ?>
        <label>Date de Naissance :</label> <?php echo "<strong>" . $dateNaiss . "</strong><br>" ?>
        <label>Nationalité :</label> <?php echo "<strong>" . $nation . "</strong><br>" ?>
    </div>

    <div class="modify">
        <a href="modifyInfo.php">modifier les informations</a>
        <a href="modify_psswd.php">modifier le mot de passe </a>
    </div>
    <br><br><br><br>
    <form method="post">
        <div class="moteur-de-recherche">
            <input type="text" name="moteurDeRecherche" placeholder="chercher ...">
            <input type="submit" value="search" name="search">
        </div>
    </form>

</body>

</html>
<?php
if (isset($_POST["moteurDeRecherche"])) {
    $recherche = trim($_POST["moteurDeRecherche"]);
    if ($recherche !== '') {

        $recherche_sans_espaces = preg_replace("/\s{2,}/", " ", $recherche); // remplacer tous les espaces entre les mots {2 ou plus } par un seul . 
        $tab_recherche = explode(" ", $recherche_sans_espaces);

        /*  ------------ > CHERCHONS LES ATHLETES  <------------ */
        $requete_search_athlete = "SELECT DISTINCT a.ID_ATHLETE, p.NOM, p.PRENOM
            FROM participant p , athlete a 
            WHERE p.id_part = a.id_part 
            and (";


        // tableaux associatifs qui nous aide à executer la reuquete prépareé .
        $Le_Where = array();
        $to_bind = array();
        $the_values = array();


        $condition = "(upper(p.nom) LIKE upper(:#-#)
        OR upper(p.prenom) LIKE upper(:#-#)
        OR upper(p.nationalite) LIKE upper(:#-#) )";
        consrtuire_conditions($tab_recherche, $Le_Where, $to_bind, $the_values, $condition);

        // mettre tous ensemble
        $requete_search_athlete .= implode(" OR ", $Le_Where);
        $requete_search_athlete .= ") ORDER BY p.nom , p.prenom ";

        echo "requete ==> " . $requete_search_athlete;

        var_dump($to_bind);
        var_dump($the_values);
        var_dump($condition);

        // execution : 
        $stid_search_athlete = executerReq($idcom, $requete_search_athlete, $to_bind, $the_values);
        //var_dump($stid_search_athlete);


        $compt = 0;

        while ($result = oci_fetch_array($stid_search_athlete, OCI_ASSOC + OCI_RETURN_NULLS)) {
            $compt++;
            $id_athlete = $result['ID_ATHLETE'];
            $nom_athlete = $result['NOM'];
            $prenom_athlete = $result['PRENOM'];

            echo "<a href='athlete.php?id_at=" . $id_athlete . "'>
                <div class='athlete-box'>
                <p><strong>'$nom_athlete'</strong>   '$prenom_athlete'</p>
                </div>
                </a>";
            echo "<br>------------------------------<br>";
        }
        // combien d'athletes trouvés ?
        echo $compt . "athletes trouvés";

        /* -------------> CHERCHONS LES EQUIPES <------------ */

        $requete_equipes = "SELECT DISTINCT e.ID_EQUIPE,e.NOM_EQUIPE,p.NOM,p.PRENOM
            from equipe e,coach c,participant p  
            where p.id_part = c.id_part
            and c.id_coach = e.id_coach
            and (";

        $Le_Where = array();
        $to_bind = array();
        $the_values = array();

        $condition = "( upper(e.NOM_EQUIPE) LIKE upper(:#-#)
        OR upper(p.nom) LIKE upper(:#-#)
        OR upper(p.prenom) LIKE upper(:#-#) )";

        consrtuire_conditions($tab_recherche, $Le_Where, $to_bind, $the_values, $condition);
        $requete_equipes .= implode(" OR ", $Le_Where);
        $requete_equipes .= ")";


        $stid_search_equipes = executerReq($idcom, $requete_equipes, $to_bind, $the_values);

        while ($result = oci_fetch_array($stid_search_equipes, OCI_ASSOC + OCI_RETURN_NULLS)) {
            $id_equipe = $result['ID_EQUIPE'];
            $NOM_EQUIPE = $result['NOM_EQUIPE'];
            $nom_coach = $result['NOM'];
            $prenom_coach = $result['PRENOM'];

            echo "<a href='equipe.php?id_eq=" . $id_equipe . "&nom_eq=" . $NOM_EQUIPE . "&nom_coach=" . $nom_coach . "&prenom_coach=" . $prenom_coach . "'>
                <div class='equipe-box'>
                <strong>'$NOM_EQUIPE'</strong><br> <p> le coach : '$nom_coach'</p>
                </div>
                <a>";
            echo "<br>------------------------------<br>";
        }



        /* ------------------> CHERCHONS LES COMPETITIONS <------------------ */

        $requete_compets = "SELECT  ID_COMPET , TYPE_COMPET , ID_CAT 
            from competition c , discipline d  
            where c.id_discp = d.id_discp 
            and (";

        $Le_Where = array();
        $to_bind = array();
        $the_values = array();

        $condition = "(upper(TYPE_COMPET) LIKE upper(:#-#) OR upper(NOM_DISCP) LIKE upper(:#-#) )";
        consrtuire_conditions($tab_recherche, $Le_Where, $to_bind, $the_values, $condition);

        $requete_compets .= implode(" OR ", $Le_Where);
        $requete_compets .= ")";

        echo "<br>requete ==> " . $requete_compets;


        $stid_search_compets = executerReq($idcom, $requete_compets, $to_bind, $the_values);



        while ($result = oci_fetch_array($stid_search_compets, OCI_ASSOC + OCI_RETURN_NULLS)) {
            $id_compet = $result["ID_COMPET"];
            $type_compet = $result["TYPE_COMPET"];
            $genre_compet = $result["ID_CAT"];

            $genre_compet = (int)$genre_compet % 2 == 0 ? "competition d'hommes" : "competition de femmes";

            echo "<a href='competition.php?id_compet=" . $id_compet . "&nom_compet=" . $type_compet . "'>
            <div class='competition-box'>
            <strong>'$type_compet'</strong><br> <p> '$genre_compet'</p>
            </div>
            <a>";
            echo "<br>------------------------------<br>";
        }
    }
}
?>