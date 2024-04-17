<?php
function executerReq($idcom, $requete, $to_bind, $the_values)
{
    if (count($the_values) !==  count($to_bind)) {
        return null;
    }
    $stid = oci_parse($idcom, $requete);
    if (!$stid) {
        $e = oci_error($idcom);
        trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
    }

    for ($i = 0; $i < count($to_bind); $i++) {
        // Liaison des valeurs aux paramètres
        oci_bind_by_name($stid, $to_bind[$i], $the_values[$i]);
    }

    $r = oci_execute($stid);
    if (!$r) {
        $e = oci_error($stid);
        trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
    }

    return $stid;
}
function afficherRes($stid, $les_th, $colonnes)
{
    if (!$stid) {
        $e = oci_error();
        trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
    }

    echo "<table border='1'>";
    echo "<tr>";
    foreach ($les_th as $un_th) {
        echo "<th>$un_th</th>";
    }
    echo "</tr>";

    while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
        echo "<tr>";
        foreach ($colonnes as $col) {
            echo "<td>" . htmlentities($row["$col"], ENT_QUOTES) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}



function consrtuire_conditions($tab_recherche, &$Le_Where, &$to_bind, &$the_values, $condition)
{
    for ($i = 0; $i < count($tab_recherche); $i++) {
        if ($tab_recherche[$i] !== " ") {

            $new_condition = str_replace("#-#", $tab_recherche[$i], $condition);

            $Le_Where[] = $new_condition;

            $to_bind[] = ":$tab_recherche[$i]";
            $the_values[] = '%' . $tab_recherche[$i] . '%';
        }
    }
}
