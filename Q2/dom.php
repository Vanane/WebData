<?php

$file = "../covid-tp.xml";
$doc = new DOMDocument();

$doc-> validateOnParse = true;
$doc->preserveWhiteSpace = false;
$doc->load($file);
$res = $doc->documentElement;


$continents = array(); // Contient le nom d'un continent en clé, et sa population, son aire, et les cas et décès par mois en valeur.
$paysContinent = array(); // Contient le code d'un pays en clé, et le nom d'un continent en valeur, par exemple ["FR"] = "Europe"
$continentActuel; // Contient le dernier continent rencontré lors du parsing.
$anneeActuelle; $moisActuel; $jourActuel; // Contient la dernière date de record rencontrée lors du parsing
$idMois;

$output = "";

foreach($res->childNodes as $node)
{
    switch($node->tagName)
    {
        case "country_list":
            foreach($node->childNodes as $continent)
            {
                $continents[$continent->getAttribute("name")] = array("name" => $continent->getAttribute("name"), "population" => 0, "area" => 0, "months" => array());
                $continentActuel = $continent->getAttribute("name");
                foreach($continent->childNodes as $country)
                {
                        $paysContinent[$country->getAttribute("xml:id")] = $continentActuel;
                        $continents[$continentActuel]["population"] += $country->getAttribute("population");
                        $continents[$continentActuel]["area"] += $country->getAttribute("area");
                }
            }
        break;
        case "record_list":
            foreach($node->childNodes as $year)
            {
                $anneeActuelle = $year->getAttribute("no");
                foreach($year->childNodes as $month)
                {
                    $moisActuel = $month->getAttribute("no");
                    $idMois = $anneeActuelle.'-'.$moisActuel;
                    foreach($paysContinent as $paysCode)
                        $continents[$paysCode]["months"][$idMois] = array("no" => $idMois, "cases" => 0, "deaths" => 0);

                    foreach($month->childNodes as $day)
                    {
                        
                        foreach($day->childNodes as $record)
                        {      
                            $continents[$paysContinent[$record->getAttribute("country")]]["months"][$idMois]["cases"] += $record->getAttribute("cases");
                            $continents[$paysContinent[$record->getAttribute("country")]]["months"][$idMois]["deaths"] += $record->getAttribute("deaths");
                        }
                    }
                }
            }
        break;
    }
}

$output = $output. <<<XML
<?xml version="1.0" encoding="UTF-8"?>\n<!DOCTYPE bilan-continents>\n<SYSTEM "info.dtd">
XML;

$output = $output."\n<bilan-continents>";

foreach($continents as $p)
{
    $output = $output. "\n\t<continent name='".$p["name"]."' population='".$p["population"]."' area='".$p["area"]."'>";
    $p["months"] = array_reverse($p["months"]);

    foreach($p["months"] as $mois)
        $output = $output."\n\t\t<month no='".$mois["no"]."' cases='".$mois["cases"]."' deaths='".$mois["deaths"]."'/>";
    $output = $output."\n\t</continent>";
}

$output = $output."</bilan-continents>\n";
file_put_contents("./outputdom.xml", $output);

?>