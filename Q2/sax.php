<?php 
header('Content-type: text/plain');
include('../Sax4PHP.php');

class CovidHandler extends DefaultHandler {
	public $continents; // Contient le nom d'un continent en clé, et sa population, son aire, et les cas et décès par mois en valeur.
	public $paysContinent; // Contient le code d'un pays en clé, et le nom d'un continent en valeur, par exemple ["FR"] = "Europe"
	public $continentActuel; // Contient le dernier continent rencontré lors du parsing.
	public $anneeActuelle, $moisActuel, $jourActuel; // Contient la dernière date de record rencontrée lors du parsing
	public $idMois;


	//L'idée de ce programme est d'avoir une construction de données similaire au résultat XML attendu.
	//Nous utilisons un dictionnaire qui prend en clé chaque nom de continent, et en valeur un second dictionnaire, qui contient :
	//une case "population", une case "area", et un troisième dictionnaire dont les clés sont des numéros de mois, et contenant
	//une case "cases" et une case "deaths".
	
	function startDocument(){
	}
  
	function startElement($nom, $att)
	{
		switch($nom)
		{
			//SAX permet de traverser chaque noeud de manière récursive. On utilise donc un switch-case
			//Et on agit en fonction du type de noeud.
			case "continent":
				//Si on rencontre un noeud continent, alors on ajoute à notre dictionnaire un continent avec les valeurs initiales.
				$this->continents[$att["name"]] = array("name" => $att["name"], "population" => 0, "area" => 0, "months" =>
					array());
				//On enregistre également le der continent visité, car selon la DTD, une balise continent serra toujours suivie des pays qui le constituent.
				$this->continentActuel = $att["name"];
			break;			
			case "country":
				//Si on rencontre une balise country, alors on : 
				//1) sait que l'on a traversé le continent qui contient ce pays
				//2) additionne la population et l'aire de ce pays à la case correspondant au dernier continent visité
				//3) ajoute ce pays à notre dictionnaire de pays qui permet de savoir à quel continent appartient ce pays.
				$this->paysContinent[$att["xml:id"]] = $this->continentActuel;
				$this->continents[$this->continentActuel]["population"] += $att["population"];
				$this->continents[$this->continentActuel]["area"] += $att["area"];
			break;
			case "year":
				//Si on rencontre une balise year, alors on note en mémoire l'année, car
				//les prochains éléments visités seront les mois de cette année.
				$this->anneeActuelle = $att["no"];
			break;
			case "month":
				//Si on rencontre un élément mois, alors on sait que la dernière année visitée est celle de ce mois,
				//on enregistre donc le mois, car les prochains éléments seront les jours de ce mois.
				//De plus, on crée pour chaque continent une ligne qui correspond à ce mois, avec les valeurs initiales.				
				$this->moisActuel = $att["no"];
				$this->idMois = $this->anneeActuelle.'-'.$this->moisActuel;
				foreach($this->paysContinent as $paysCode)
					$this->continents[$paysCode]["months"][$this->idMois] = array("no" => $this->idMois, "cases" => 0, "deaths" => 0);
			break;
			case "record":
				//Si on rencontre un élément record, on connait le dernier mois visité, et on peut donc
				//additionner les cas et les décès de ce record, en utilisant l'ID du pays concerné par le record,
				//et le numéro du mois.
				$this->continents[$this->paysContinent[$att["country"]]]["months"][$this->idMois]["cases"] += $att["cases"];
				$this->continents[$this->paysContinent[$att["country"]]]["months"][$this->idMois]["deaths"] += $att["deaths"];
			break;
		}
	}
}

$file = "../covid-tp.xml";
$output = "";

//Programme principal
//encapsulé dans un try-catch en cas d'erreur
try
{	
    $nbl = new CovidHandler();
    $sax = new SaxParser($nbl);
	$sax->parse($file);

	$output = $output. <<<XML
	<?xml version="1.0" encoding="UTF-8"?>\n<!DOCTYPE bilan-continents>\n<SYSTEM "info.dtd">
	XML;

	$output = $output."\n<bilan-continents>";

	//Après la construction de notre dictionnaire, nous avons simplement à l'afficher en itérant.
	foreach($nbl->continents as $p)
	{
		$output = $output. "\n\t<continent name='".$p["name"]."' population='".$p["population"]."' area='".$p["area"]."'>";
		$p["months"] = array_reverse($p["months"]);
		foreach($p["months"] as $mois)
		$output = $output."\n\t\t<month no='".$mois["no"]."' cases='".$mois["cases"]."' deaths='".$mois["deaths"]."'/>";
			$output = $output."\n\t</continent>\n";
	}
	$output = $output."</bilan-continents>\n";
	file_put_contents("./outputsax.xml", $output);
}
catch(SAXException $e)
{  
    echo $e;
}
?>
