<?php
session_start();
include("connexion_OCI.php");
include_once("fonctions.php");
$idcom = connexion_OCI();
if (!$idcom) {
    echo "connexion à la base de données IMPOSSIBLE ///";
    exit;
}

if (!isset($_SESSION['ID_MEMBRE_CONNECTE'])) {
    echo "ACCES FORBIDDEN";
    die;
}

if (isset($_COOKIE['nations_deja_choisies']) && !empty($_COOKIE['nations_deja_choisies'])) {
    $nations_selectionnés = explode("#--#", $_COOKIE["nations_deja_choisies"]);
}

if (isset($_COOKIE['age_selectionné']) && !empty($_COOKIE['age_selectionné'])) {
    $age_selectionné = (int) $_COOKIE['age_selectionné'];
}

if (isset($_COOKIE['poids_selectionné']) && !empty($_COOKIE['poids_selectionné'])) {
    $poids_selectionné =  $_COOKIE['poids_selectionné'];
}

if (isset($_COOKIE['taille_selectionné']) && !empty($_COOKIE['taille_selectionné'])) {
    $taille_selectionné =  $_COOKIE['taille_selectionné'];
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste déroulante avec checkboxes</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <!-- Form for nationality filter -->
    <form method="post">
        <div class="dropdown-container">
            <input type="text" name="nationalite" placeholder="Sélectionner nationalité" id="nationaliteInput" readonly>
            <div class="dropdown-arrow" onclick="toggle_liste_dEroulante('liste_dEroulante_nationalite')">▼</div>
            <div class="liste_dEroulante" id="liste_dEroulante_nationalite">
                <?php
                $requete_all_nationalities = "SELECT DISTINCT NATIONALITE FROM participant ";
                $nation_stid = executerReq($idcom, $requete_all_nationalities, [], []);

                while ($row = oci_fetch_assoc($nation_stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
                    // ici je pre-check le pays déjà choisi par l'utilisateur ...
                    $checked = (isset($nations_selectionnés) && in_array($row['NATIONALITE'], $nations_selectionnés)) ? 'checked' : '';
                    echo "<label> <input type='checkbox' name='nations[]' value='{$row['NATIONALITE']}' $checked> 
                    {$row['NATIONALITE']}</label> <br>";
                }
                ?>
                <input type="submit" name="nations_part" value="Valider">
            </div>
        </div>
    </form>


    <!-- Form for age filter -->
    <form method="post">
        <div>
            <label for="age">Age:</label>
            <input type="number" name="age" id="age" min="0">
        </div>
        <input type="submit" name='age_selected' value="Valider" class="small-submit">
    </form>


    <!-- Form for weight filter -->
    <form method="post">
        <div>
            <label for="weight">Poids:</label>
            <input type="number" name="weight" id="weight" min="0" step="0.01">
        </div>
        <input type="submit" value="Valider" class="small-submit">
    </form>
    <!-- Form for height filter -->
    <form method="post">
        <div>
            <label for="height">Taille:</label>
            <input type="number" name="height" id="height" min="0" step="0.01">
        </div>
        <input type="submit" value="Valider" class="small-submit">
    </form>


    <script src="script.js"></script>
</body>

</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // SI M; A CLIQUE SUR VALIDER : nation_part 
    if (isset($_POST['nations_part']) && isset($_POST['nations'])) {

        // récupérer les nationalités choisies par le membre du comité
        $tableau_nations = $_POST['nations'];

        // stocker tous ça maintenant dans un cookiiiiiie :
        $chaine_pour_cookie = "";
        $chaine_pour_cookie .= implode("#--#", $nations_assoc);
        setcookie("nations_deja_choisies", $chaine_pour_cookie, time() + 3600 * 24, "/");


        // attacher les  paraméters : 
        $Le_Where = array();
        $to_bind = array();
        $the_values = array();

        $condition_pays = "( upper(p.nationalite) LIKE upper(:#-#) )";
        consrtuire_conditions($tableau_nations, $Le_Where, $to_bind, $the_values, $condition_pays);



        /*  ------------ > CHERCHONS LES ATHLETES  <------------ */
        $requete_search_athlete = "SELECT DISTINCT a.ID_ATHLETE, p.NOM, p.PRENOM
            FROM participant p , athlete a 
            WHERE p.id_part = a.id_part 
            and (";

        $requete_search_athlete .= implode(" OR ", $Le_Where);
        $requete_search_athlete .= ")";

        // what if we have submitted the age ? the height ? the weight ? 
        // we have to get those as well ! 
        if (isset($age_selectionné)) {
            $requete_search_athlete .= "AND (EXTRACT(YEAR FROM DATE '2024-01-01') - EXTRACT(YEAR FROM p.DATENAISS) = $age_selectionné)";
        }
        if (isset($poids_selectionné)) {
            $requete_search_athlete .= "AND (a.poids = $poids_selectionné)";
        }
        if (isset($taille_selectionné)) {
            $requete_search_athlete .= "AND (a.taille = $taille_selectionné)";
        }

        echo "requete = = = > : " . $requete_search_athlete;

        $stid_search_athlete = executerReq($idcom, $requete_search_athlete, $to_bind, $the_values);
        // Compter le nombre de résultats
        $num_rows = oci_num_rows($stid_search_athlete);
        echo "nombre d'athletes trouvés : " . $num_rows . "<br>";
        while ($result = oci_fetch_array($stid_search_athlete, OCI_ASSOC + OCI_RETURN_NULLS)) {
            $id_athlete = $result['ID_ATHLETE'];
            echo "<a href='SAE/athlete.php?id_at=" . $id_athlete . "'>
                <p><strong>{$result['NOM']}</strong>{$result['PRENOM']}</p>
                </a>";
            echo "<br>---<br>";
        }



        /*  ------------ > CHERCHONS LES ARBITRES  <------------ */
        $requete_search_arbitre = "SELECT DISTINCT pr.ID_PERS, p.NOM, p.PRENOM
            FROM participant p , personnel pr 
            WHERE p.id_part = pr.id_part 
            and upper(TYPE_PERS) = 'ARBITRE'
            and (";

        $requete_search_arbitre .= implode(" OR ", $Le_Where);
        $requete_search_arbitre .= ")";

        if (isset($age_selectionné)) {
            $requete_search_athlete .= "AND (EXTRACT(YEAR FROM DATE '2024-01-01') - EXTRACT(YEAR FROM p.DATENAISS) = $age_selectionné)";
        }

        $requete_search_arbitre = executerReq($idcom, $requete_search_arbitre, $to_bind, $the_values);

        // Compter le nombre de résultats
        $num_rows = oci_num_rows($requete_search_arbitre);
        echo "nombre d'arbitres trouvés : " . $num_rows . "<br>";

        while ($result = oci_fetch_array($requete_search_arbitre, OCI_ASSOC + OCI_RETURN_NULLS)) {
            $id_athlete = $result['ID_PERS'];
            echo "<a href='SAE/arbitre.php?id_arbitre=" . $ID_PERS . "'>
                <p><strong>{$result['NOM']}</strong>{$result['PRENOM']}</p>
                </a>";
            echo "<br>---<br>";
        }
    }

    // SI M; A CLIQUE SUR VALIDER : age_selected
    if (isset($_POST['age_selected']) && !empty($_POST['age'])) {

        $age_selecteddd = $_POST['age'];
        setcookie("age_selectionné", $age_selecteddd, time() * 3600 * 24, "/");
    }
}
?>