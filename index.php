<html>
<head>
  <title> Tracker Lamzouille </title>
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
<div id="header" align="center">
  -
</div>
<div id="main">
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


class PokerFile {

  public function readPokerFile($filename) {
    $file = fopen("./log/$filename",'r');
    //tant qu'il y'a du contenu on crée les mains associées
    while(!feof($file))
    {
      $a = new Hand();
      $a->readHand($file);
      echo $a->poker_hand.'<br/>';
      $a->insertHandDB($a->poker_hand,$a->date,$a->id,$a->result);
    }
  }
}

/*class Phase {
  // PREFLOP, FLOP, ...
  list<action>
}
class Action {
  combien
  type : relance,

}*/

class Hand {
  public $id = '';
  public $poker_hand = '';
  public $date = '';
  public $result = 'lose';
  public $type = '';

  //public $actions[];

  public function insertHandDB($hand_,$date_,$id_,$result_)
  {
    $mysqli = new mysqli("localhost", "root", "","tracker");
    if (!$mysqli->query("INSERT INTO TEST_HAND(ID,CARD1,CARD2,date_,result) VALUES('$id_',SUBSTRING('$hand_',1,2),SUBSTRING('$hand_',-2,2),'$date_','$result_')"))
    {
      echo "Echec de  la requete (". $mysqli->errno . ") " . $mysqli->error;
    }
  }
  public function readHand($file) {
    while($line = fgets($file)) {
      if (strlen($line) == 2) {
        fgets($file);
        return;
      }
      //echo $line.'<br/>';
      if(!substr_compare($line,"Dealt to Lamzouille59",0,21))
      {
        $this->poker_hand = substr($line,23,5);
      }
      if(!substr_compare($line,"PokerStars",0,10))
      {
        $arr_info = explode(" ",$line);
        $this->date = $arr_info[15];//5Hand NumberFormatted
        $this->id = $arr_info[2];
        //echo $arr_info[5]; BuyIN Tournoi
      }
      if(!substr_compare($line,"Lamzouille59 collected",0,22))
      {
        $this->result = "win";
      }
    }
  }

}



$mysqli = new mysqli("localhost", "root", "","tracker");
if ($mysqli->connect_errno)
{
  echo "Echec lors de la connexion à MySQL : (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
else eko("Connexion OK");

function get_top3_hands()
{
  $req = "SELECT COUNT(*) AS nbr_doublon, card1, card2 FROM test_hand GROUP BY card1, card2 order by nbr_doublon DESC LIMIT 3;";
  $mysqli = new mysqli("localhost", "root", "","tracker");
  $result = mysqli_query($mysqli,$req);
  $row=mysqli_fetch_array($result,MYSQLI_NUM);
  if ($result = $mysqli->query($req)) {

	/* Récupère un tableau associatif */
	while ($row = $result->fetch_row()) {
		//eko ("$row[1], $row[2], $row[0]");
    echo "<img src=\"img/$row[1].png\" width=\"40px\" heigth=\"40px\" border=\"1\"\">";
    echo "  ";
    echo "<img src=\"img/$row[2].png\" width=\"40px\" heigth=\"40px\"  border=\"1\"\">";
    echo "<br/>";
    //echo "<img src=\"img/$row[0].png\" width=\"50px\" heigth=\"50px\" \">";
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
      //get_cards($fichier);
      //getDateHand($fichier);
      //insertion en base des mains

      $file = new PokerFile();
      $file->readPokerFile($fichier);
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
echo "<div id=\"top3\" align=\"center\">";
eko("TOP 3 des mains");
get_top3_hands();
echo "<br/><br/></div>";
echo "</div>";

?>
<div id="bottom" align="center">-</div>
</body>
