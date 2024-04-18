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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (
        !empty($_POST["nom_athlete"]) && !empty($_POST["prenom_athlete"])
        && !empty($_POST["nationalite"]) && !empty($_POST["date_naissance"]) && !empty($_POST["poids"]) && !empty($_POST["taille"])
        && !empty($_POST["sonCoach"])
    ) {
        $to_bind = array();
        $the_values = array();

        $nom = $_POST["nom_athlete"];
        $to_bind[] = ":nom";
        $the_values[] = $nom;

        $prenom = $_POST["prenom_athlete"];
        $to_bind[] = ":prenom";
        $the_values[] = $prenom;

        $nationalite = $_POST["nationalite"];
        $to_bind[] = ":nationalite";
        $the_values[] = $nationalite;

        $date_naissance = $_POST["date_naissance"];
        $date_naissance = preg_replace("/^([0-9]+)-([0-9]+)-([0-9]+)$/", "$3/$2/$1", $date_naissance);
        $to_bind[] = ":date_naissance";
        $the_values[] = $date_naissance;

        $poids = $_POST["poids"];
        $to_bind[] = ":poids";
        $the_values[] = $poids;

        $taille = $_POST["taille"];
        $to_bind[] = ":taille";
        $the_values[] = $taille;

        $son_coach_id = $_POST["sonCoach"];
        $to_bind[] = ":sonCoach";
        $the_values[] = $son_coach_id;

        // requete pour extraire le dernier ID de la base de données ( table participant );
        $requete_max_id_part = "SELECT MAX(ID_PART) AS MAX_ID FROM PARTICIPANT";
        $maxID_PART_stid = executerReq($idcom, $requete_max_id_part, [], []);
        if ($maxID_PART_stid) {
            $row = oci_fetch_assoc($maxID_PART_stid);
            $next_id_part = (int)$row['MAX_ID'] + 1;
            $to_bind[] = ":next_id_part";
            $the_values[] = $next_id_part;
        }

        if (isset($next_id_part) && !empty($next_id_part)) {
            $requete_insertion = "INSERT INTO PARTICIPANT (ID_PART, NOM, PRENOM, DATENAISS, NATIONALITE) VALUES
            (:next_id_part, :nom, :prenom, TO_DATE(:date_naissance, 'DD/MM/YYYY'), :nationalite)";
            $insert_participant_stid = executerReq($idcom, $requete_insertion, $to_bind, $the_values);

            if ($insert_participant_stid) {
                // requete pour extraire le dernier ID de la base de données ( table participant );
                $requete_max_id_at = "SELECT MAX(ID_ATHLETE) AS MAX_ID_AT FROM ATHLETE";
                $maxID_AT_stid = executerReq($idcom, $requete_max_id_at, [], []);
                if ($maxID_AT_stid) {
                    $row = oci_fetch_assoc($maxID_AT_stid);
                    $next_id_at = (int)$row['MAX_ID_AT'] + 1;
                    $to_bind[] = ":next_id_at";
                    $the_values[] = $next_id_at;
                }
                if (isset($next_id_at) && !empty($next_id_at)) {
                    $requete_insertion_athlete = "INSERT INTO ATHLETE (ID_ATHLETE, POIDS, TAILLE, ID_COACH, ID_PART) VALUES
                    (:next_id_at, :poids, :taille, :sonCoach, :next_id_part)";
                    $insert_athlete_stid = executerReq($idcom, $requete_insertion_athlete, $to_bind, $the_values);
                    if (!$insert_athlete_stid) {
                        echo " insertion impossible ! ";
                        die;
                    } else {
                        setcookie("athlete_ajoute_now", $nom . "#--#" . $prenom, time() + 60, "/");
                        header("Location: athletes.administration.php");
                        exit();
                    }
                }
            } else {
                echo " can not insert athlete ! ";
                die;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Athlete Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: black;
            color: #00FF00;
            /* Green text */
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #222;
            /* Dark gray background */
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }

        label {
            font-weight: bold;
            margin-bottom: 10px;
            display: block;
        }

        input[type="text"],
        input[type="date"],
        input[type="number"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            background-color: #444;
            /* Dark gray background for input fields */
            color: #00FF00;
            /* Green text for input fields */
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Add Athlete</h2>
        <form method="POST">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom_athlete" required>
            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom_athlete" required>
            <label for="date">Date de naissance :</label>
            <input type="date" id="date" name="date_naissance" required>
            <label for="nationalite">Nationalité :</label>
            <input type="text" id="nationalite" name="nationalite" required>
            <label for="poids">Poids (en kg) :</label>
            <input type="number" id="poids" name="poids" required>
            <label for="taille">Taille (en cm) :</label>
            <input type="number" id="taille" name="taille" required>

            <label for="sonCoach">Son coach :</label>
            <select name="sonCoach" required>
                <?php
                $requete = "SELECT c.ID_COACH, p.NOM, p.PRENOM
                            FROM PARTICIPANT p, COACH c
                            WHERE c.id_part = p.id_part ";
                $coachs_stid = executerReq($idcom, $requete, [], []);
                while ($row = oci_fetch_array($coachs_stid)) {
                    echo "<option value='{$row['ID_COACH']}'>{$row['NOM']} {$row['PRENOM']}</option>";
                }
                ?>
            </select>
            <input type="submit" value="Ajouter Athlète">
        </form>
    </div>
</body>

</html>