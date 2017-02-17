<?php

//Déclaration des variables
$cpt_hand = 0;
$nb_fichier = 0;
$path_history_files = '';
//Déclaration des fonctions
/*
try
{
  $bdd = new PDO('mysql:host=localhost;dbname=test;charset=utf8', 'root', '');
}
catch(Exception $e)
{
  die('Erreur : '.$e->getMessage());
}


function insert_hand_db()
{
  $bdd->exec('INSERT INTO HAND_HISTORY(ID,CARD1,CARD2,VILLAIN1,VILLAIN2,VILLAIN3,VILLAIN4,VILLAIN5,POT_FLOP,POT_TURN,POT_RIVER,WINNER) VALUES(\'\', \'\', \'\', , , \'\')');
}
*/

/*$link = mysql_connect("localhost", "root", "");

if (!$link)
{
  die('Connexion impossible : ' . mysql_error());
}
echo 'Connecté correctement';
*/

function eko($msg)
{
  echo "$msg";
  echo "<br />";
  echo "<br />";
}

$mysqli = new mysqli("localhost", "root", "","tracker");
if ($mysqli->connect_errno)
{
  echo "Echec lors de la connexion à MySQL : (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
else eko("Connexion OK");

function insert_hand($hand)
{
  $mysqli = new mysqli("localhost", "root", "","tracker");
  if (!$mysqli->query("INSERT INTO TEST_HAND(CARD1,CARD2) VALUES(SUBSTRING('$hand',1,2),SUBSTRING('$hand',-2,2))"))
  {
    echo "Echec de  la requete (". $mysqli->errno . ") " . $mysqli->error;
  }
}

function get_cards($filename)
{
  $file = fopen("./log/$filename",'r');
  while($line = fgets($file))
  {
    if(!substr_compare($line,"Dealt to Lamzouille59",0,21))
    {
      $poker_hand = substr($line,23,5);
      //eko ($poker_hand);
      insert_hand($poker_hand);
      //return substr($line,23,5);
    }
  }

  fclose($file);
}

function get_top3_hands()
{
  $req = "SELECT COUNT(*) AS nbr_doublon, card1, card2 FROM test_hand GROUP BY card1, card2 order by nbr_doublon DESC LIMIT 3;";
  $mysqli = new mysqli("localhost", "root", "","tracker");
  $result = mysqli_query($mysqli,$req);
  $row=mysqli_fetch_array($result,MYSQLI_NUM);
  if ($result = $mysqli->query($req)) {

	/* Récupère un tableau associatif */
	while ($row = $result->fetch_row()) {
		eko ("$row[1], $row[2], $row[0]");
	}

	/* Libère le jeu de résultats */
	$result->close();
}
}

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
      get_cards($fichier);
      //insertion en base des mains

    }
  }

echo '</ul><br />';
echo 'Il y a <strong>' . $nb_fichier .'</strong> fichier(s) dans le dossier';

closedir($dossier);
}
else
     echo 'Le dossier n\' a pas pu être ouvert';

echo "<br />";
echo "<br />";
eko ("Nombre de mains jouées : $cpt_hand");

eko("TOP 3 des mains");
get_top3_hands();

?>
