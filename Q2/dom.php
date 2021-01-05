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

//L'idée de ce programme est d'avoir une construction de données similaire au résultat XML attendu.
//Nous utilisons un dictionnaire qui prend en clé chaque nom de continent, et en valeur un second dictionnaire, qui contient :
//une case "population", une case "area", et un troisième dictionnaire dont les clés sont des numéros de mois, et contenant
//une case "cases" et une case "deaths".

foreach($res->childNodes as $node)
{
    //Pour les deux enfants du noeud racine, on fait une action précise, séparée dans un switch.
    switch($node->tagName)
    {
        case "country_list":
            //Si c'est le noeud country_list, alors on itère à travers tous les continents du document XML
            foreach($node->childNodes as $continent)
            {
                //Pour chaque continent, on ajoute une case dans notre dictionnaire qui porte le nom de ce continent
                $continents[$continent->getAttribute("name")] = array("name" => $continent->getAttribute("name"), "population" => 0, "area" => 0, "months" => array());
                $continentActuel = $continent->getAttribute("name");
                foreach($continent->childNodes as $country)
                {
                        //Puis, pour chaque pays du continent, on calcule la population et l'aire.
                        $continents[$continentActuel]["population"] += $country->getAttribute("population");
                        $continents[$continentActuel]["area"] += $country->getAttribute("area");
                        //De plus, nous avons besoin de savoir le continent de chaque pays. Nous stockons alors un dictionnaire qui a en clé, le code d'un pays, et en valeur, son continent associé.
                        $paysContinent[$country->getAttribute("xml:id")] = $continentActuel;
                }
            }
        break;
        case "record_list":
            //Dans le cas où on rencontre le noeud qui contient les enregistrements,
            foreach($node->childNodes as $year)
            {
                $anneeActuelle = $year->getAttribute("no");
                foreach($year->childNodes as $month)
                {
                    $moisActuel = $month->getAttribute("no");
                    $idMois = $anneeActuelle.'-'.$moisActuel;
                    //On parcourt chaque mois de chaque année et on ajoute ce mois à la liste des mois de chaque continent.
                    foreach($continents as $c)
                    {
                        $continents[$c["name"]]["months"][$idMois] = array("no" => $idMois, "cases" => 0, "deaths" => 0);
                    }
                    //Ensuite, on fait le total des cas et des décès de ce mois, pour chaque continent.
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

//Après la construction de notre dictionnaire, il suffit d'itérer à travers toute la variable pour générer notre XML.
foreach($continents as $p)
{
    //Pour chaque continent, on crée une balise continent,
    $output = $output. "\n\t<continent name='".$p["name"]."' population='".$p["population"]."' area='".$p["area"]."'>";

    //On retourne les mois de chaque continent pour avoir un ordre croissant,
    $p["months"] = array_reverse($p["months"]);

    //Et pour chaque mois, on crée une balise mois.
    foreach($p["months"] as $mois)
    {
        $output = $output."\n\t\t<month no='".$mois["no"]."' cases='".$mois["cases"]."' deaths='".$mois["deaths"]."'/>";
    }
    $output = $output."\n\t</continent>";
}

$output = $output."</bilan-continents>\n";
file_put_contents("./outputdom.xml", $output);

?>