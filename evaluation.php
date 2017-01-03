<?php

$file=fopen("questions.qs","r");
$lines=file('questions.qs');
foreach ($lines as $lineNumber => $lineContent)
{
    if (substr_count($lineContent,'#')==2){
        $tab_theme[]=$lineContent;
    }
    else if(substr_count($lineContent,'#')==1){

      $tab_question["texte"][]=$lineContent;
      $tab_question["theme"][]=count($tab_theme)-1;
    }
     if(preg_match_all("#^\-#", trim($lineContent))){
        $tab_qcm["texte"][] = $lineContent;
        $tab_qcm["question"][] = count($tab_question["texte"])-1;
      }

}
if(!$_POST){
$index = fopen("index.html","w+");
fputs($index,'<html><head><meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>evaluations du 03 janvier 2017 – ville</title><link rel="stylesheet" type="text/css" href="style.css"></head><body><h1>Evaluations du 03 Janviers 2017</h1><h2>Alice Gabbana</h2><form action="evaluation.php" method="POST" >');



for($i=0;$i<count($tab_theme);$i++){
  $t = str_replace("##", "", $tab_theme[$i]);
  $texte="<section><h3>". $t ."</h3>";
  fputs($index,$texte);


    for($j=0;$j<count($tab_question["texte"]);$j++){
          $test_qcm=false;
          if ($tab_question["theme"][$j]==$i)
             {
               $t = str_replace("#", "", $tab_question["texte"][$j]);
               $nb = $j +1;
               fputs($index,'<article><span>'.$nb.') '.$t.'</span>');



                  for($k=0;$k<count($tab_qcm["texte"]);$k++){
                      
                      

                      if ($tab_qcm["question"][$k]==$j){
                          $t = '<ul><li><INPUT type= "radio" id = "qcm '. $j .'" name="'. $j .'" value="' . $tab_qcm["texte"][$k] . '">' . str_replace("-", "", $tab_qcm["texte"][$k]) . '</li></ul>';
                          fputs($index, $t);
                          $test_qcm = true;

                          }



                  }
                  if($test_qcm==false){
                    fputs($index, '</br><textarea onclick="myFunction(this.id)" id="'. $j .'" rows="4" name="' . $j . '" cols="50"></textarea>');
                  }
                  fputs($index, "</article>");
            }

      }
      fputs($index,"</section><br>");
}

fputs($index,'<INPUT TYPE="submit" NAME="submit" VALUE="Valider"><input name="secret" type="hidden" value=""></form><script src="javascript.js"></script></body></html>');
}
else{
    
 $c = count($_POST);
for($i=0;$i< $c-1;$i++){
   if (!$_POST[$i]) { header('Location: index.html'); }
    else {
            
$resultat = fopen("resultat/resultat.xml","w+");
    $_xml = '<?xml version="1.0" encoding="UTF-8"?>';
    fwrite($resultat, $_xml);
  fputs($resultat, '<Réponses>');  
for($i=0;$i<count($tab_question["texte"]);$i++){
   $nb= $i+1;
   fputs($resultat, '<Question>' . $nb . ' : ' . rtrim($_POST[$i]) . '</Question>');
 }
    fputs($resultat, '</Réponses>');
 fclose($file);
 echo "Le fichier résultat à bien été créé.  <a href=\"resultat/resultat.xml\">View the XML.</a>";
        
    }
 }

}