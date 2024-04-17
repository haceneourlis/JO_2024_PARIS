<?php

function binding($tab_recherche, &$Le_Where, &$to_bind, &$the_values, $condition)
{
    for ($i = 0; $i < count($tab_recherche); $i++) {
        if ($tab_recherche[$i] !== " ") {
            // Replace #-# with the current element from tab_recherche
            $new_condition = str_replace("#-#", $tab_recherche[$i], $condition);

            // Add the modified condition to Le_Where array
            $Le_Where[] = $new_condition;

            // Add the binding parameter and the wildcard pattern for the search value
            $to_bind[] = ":$tab_recherche[$i]";
            $the_values[] = '%' . $tab_recherche[$i] . '%';
        }
    }
}

$condition = "(p.nom LIKE :#-#
OR p.prenom LIKE :#-#
OR p.nationalite LIKE :#-#)";

$Le_Where = array();
$to_bind = array();
$the_values = array();

$tab_recherche = array("messi", "ronaldo", "neymar");
binding($tab_recherche, $Le_Where, $to_bind, $the_values, $condition);

// Output the result
foreach ($Le_Where as $condition) {
    echo $condition . "\n";
}
