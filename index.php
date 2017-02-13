<?php

//Déclaration des variables
$cpt_hand = 0;
$nb_fichier = 0;
$path_history_files = '';
//Déclaration des fonctions

function count_hand($filename)
{

  $cpt_hand = 0;
  $file = fopen("./log/$filename",'r');
  while($line = fgets($file))
  {
    if(strlen($line) == 2)
    {
      $cpt_hand = $cpt_hand + 1;
    }
  }
  fclose($file);
  return $cpt_hand/2+1;
}

function eko($msg)
{
  echo "$msg";
  echo "<br/>";
  echo "<br/>";
}

eko("Tracker Poker Lamzouille");

eko("Liste des fichiers historiques : ");

if($dossier = opendir('./log'))
{
  while(false !== ($fichier = readdir($dossier)))
  {
    if($fichier != '.' && $fichier != '..' && $fichier != 'index.php')
    {
    $nb_fichier++;
    echo '<li><a href="./log/' . $fichier . '">' . $fichier . '</a> [',count_hand($fichier),']</li>';
    $cpt_hand = $cpt_hand + count_hand($fichier);
    //echo "[",$cpt_hand,"]";
    }
  }

echo '</ul><br />';
echo 'Il y a <strong>' . $nb_fichier .'</strong> fichier(s) dans le dossier';

closedir($dossier);
}
else
     echo 'Le dossier n\' a pas pu être ouvert';

echo "<br/>";
echo "<br/>";
echo ("Nombre de mains jouées : $cpt_hand");
echo "<br/>";
echo "<br/>";



?>
