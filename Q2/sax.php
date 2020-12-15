<?php 
header('Content-type: text/plain');
include('../Sax4PHP.php');

class CovidHandler extends DefaultHandler {
	public $continents; // Contient le nom d'un continent en clé, et sa population, son aire, et les cas et décès par mois en valeur.
	public $paysContinent; // Contient le code d'un pays en clé, et le nom d'un continent en valeur, par exemple ["FR"] = "Europe"
	public $continentActuel; // Contient le dernier continent rencontré lors du parsing.
	public $anneeActuelle, $moisActuel, $jourActuel; // Contient la dernière date de record rencontrée lors du parsing
	public $idMois;

	function startDocument(){
	}
  
	function startElement($nom, $att)
	{
		switch($nom)
		{
			case "continent":
				$this->continents[$att["name"]] = array("name" => $att["name"], "population" => 0, "area" => 0, "months" =>
					array());
				$this->continentActuel = $att["name"];
			break;			
			case "country":
				$this->paysContinent[$att["xml:id"]] = $this->continentActuel;
				$this->continents[$this->continentActuel]["population"] += $att["population"];
				$this->continents[$this->continentActuel]["area"] += $att["area"];
			break;
			case "year":
				$this->anneeActuelle = $att["no"];
			break;
			case "month":				
				$this->moisActuel = $att["no"];
				$this->idMois = $this->anneeActuelle.'-'.$this->moisActuel;
				foreach($this->paysContinent as $paysCode)
				$this->continents[$paysCode]["months"][$this->idMois] = array("no" => $this->idMois, "cases" => 0, "deaths" => 0);
			break;
			case "record":
				$this->continents[$this->paysContinent[$att["country"]]]["months"][$this->idMois]["cases"] += $att["cases"];
				$this->continents[$this->paysContinent[$att["country"]]]["months"][$this->idMois]["deaths"] += $att["deaths"];
			break;
		}
	}
}

$file = "../covid-tp.xml";
$output = "";
try
{	
    $nbl = new CovidHandler();
    $sax = new SaxParser($nbl);
	$sax->parse($file);

	$output = $output. <<<XML
	<?xml version="1.0" encoding="UTF-8"?>\n<!DOCTYPE bilan-continents>\n<SYSTEM "info.dtd">
	XML;

	$output = $output."\n<bilan-continents>";

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
