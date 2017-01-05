<?php

//ouverture du fichier questions et stockage dans la variable $fichier_questions
$fichier_questions = file('questions.qs');
//initialisation des 3 tableaux qui stockent le fichier de questions en thèmes/questions/choix qcm
//boucle pour remplir chaque tableau avec le contenu du fichier question
foreach ($fichier_questions as $lineNumber => $lineContent) {
    if (preg_match_all("/^\#\#/", trim($lineContent))) {  //si la ligne commence par ## c'est une question on stock dans le tableau themes
        $tab_themes[] = $lineContent;
    } else if (preg_match_all("/^\#/", $lineContent)) { //si la ligne commence par # c'est une question on stock dans le tableau questions
        $tab_questions["enonce_question"][] = $lineContent;
        $tab_questions["num_theme"][] = count($tab_themes) - 1;
    } else if (preg_match_all("/^\-/", $lineContent)) { //si la ligne commence par - c'est un choix on stock dans le tableau qcm
        $tab_qcm["choix"][] = $lineContent;
        $tab_qcm["num_question"][] = count($tab_questions["enonce_question"]) - 1;
    }
}
//si la variable post n'existe pas (qu'on ne vient pas d'un submit) alors on affiche le formulaire
if (!$_POST) {
    $index = fopen("index.html", "w+");
    fputs($index, '<html><head><meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>evaluations du 03 janvier 2017 – ville</title><link rel="stylesheet" type="text/css" href="style.css"></head><body><h1>Evaluations du 03 Janviers 2017</h1><h2>Alice Gabbana</h2><form action="evaluation.php" method="POST" >');

    for ($i = 0; $i < count($tab_themes); $i++) {

        $t = str_replace("##", "", $tab_themes[$i]);
        $texte = "<section><h3>" . $t . "</h3>";
        fputs($index, $texte);
        for ($j = 0; $j < count($tab_questions["enonce_question"]); $j++) {


            $test_qcm = false;
            if ($tab_questions["num_theme"][$j] == $i) {
                $t = str_replace("#", "", $tab_questions["enonce_question"][$j]);
                $nb = $j + 1;

                fputs($index, '<article><span>' . $nb . ') ' . $t . '</span>');

                if (in_array($j, $tab_qcm["num_question"])) {
                    fputs($index, '<ul>');
                    $ul_tag = true;
                }
                for ($k = 0; $k < count($tab_qcm["choix"]); $k++) {



                    if ($tab_qcm["num_question"][$k] == $j) {

                        $t = '<li><INPUT type= "radio" onclick="updateTime(\'h' . $j . '\')" name="' . $j . '" value="' . $tab_qcm["choix"][$k] . '">' . str_replace("-", "", $tab_qcm["choix"][$k]) . '</li>';
                        fputs($index, $t);
                        $test_qcm = true;
                    }
                }
                if ($ul_tag) {
                    fputs($index, '</ul>');
                    $ul_tag = false;
                }
                if ($test_qcm == false) {
                    fputs($index, '</br><textarea onkeyup="updateTime(\'h' . $j . '\')" rows="4" name="' . $j . '" cols="50"></textarea>');
                }

                fputs($index, "</article>");
                fputs($index, '<input name="h' . $j . '" type="hidden">');
            }
        }
        fputs($index, "</section><br>");
    }

    fputs($index, '<INPUT TYPE="submit" NAME="submit" VALUE="Valider"></form><script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.1.1.min.js"></script><script src="javascript.js"></script></body></html>');
} else {
    $c = count($_POST);

    $resultat = fopen("resultat/resultat.xml", "w+");
    $_xml = '<?xml version="1.0" encoding="UTF-8"?>';
    fwrite($resultat, $_xml);
    fputs($resultat, '<reponses>');
    for ($i = 0; $i < count($tab_questions["enonce_question"]); $i++) {

        $nb = $i + 1;

        fputs($resultat, '<question> Temps : ' . $_POST["h" . $i] . ' / Question ' . $nb . ' : ' . rtrim($_POST[$i]) . '</question>');
        fputs($resultat, $heure);
    }
    fputs($resultat, '</reponses>');
    fclose($file);
    echo "Le fichier résultat à bien été créé.  <a href=\"resultat/resultat.xml\">View the XML.</a>";
}