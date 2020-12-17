<?php

$file = "../covid-tp.xml";
$doc = new DOMDocument();
$doc-> validateOnParse = true;
$doc->preserveWhiteSpace = false;
$doc->load($file);
$xpath = new DOMXpath($doc);

// Récupérer la liste des noms des continents
$continentsNoms = $xpath->evaluate("/covid-eu/country_list/continent/@name");


$output = "";
$output = $output. <<<XML
<?xml version="1.0" encoding="UTF-8"?>\n<!DOCTYPE bilan-continents>\n<SYSTEM "info.dtd">
XML;

$output = $output."\n<bilan-continents>\n";


foreach($continentsNoms as $continent)
{
    // Récupérer une balise <continent> pour chaque continent trouvé dans la requête précédente
    $nomContinent = $continent->value;
    $output = $output."\t<continent name='".$nomContinent."' ";
    $output = $output."population='".$xpath->evaluate("sum(/covid-eu/country_list/continent[@name = '".$nomContinent."']/country/@population)")."' "; // Somme aire d'un continent     
    $output = $output."area='".$xpath->evaluate("sum(/covid-eu/country_list/continent[@name = '".$nomContinent."']/country/@area)")."'>\n"; // Somme aire d'un continent

    $annees = $xpath->evaluate("/covid-eu/record_list/year/@no");
    for($i = $annees->length - 1; $i >= 0; $i--)
    {
        $noAnnee = $annees[$i]->value;
        $moiss = $xpath->evaluate("/covid-eu/record_list/year[@no='".$noAnnee."']/month/@no");
        for($j = $moiss->length - 1; $j >= 0; $j--)
        {
            $noMois = $moiss[$j]->value;
            $nbCas = $xpath->evaluate("sum(/covid-eu/record_list/year[number(@no)=".$noAnnee."]/month[number(@no)=".$noMois."]/day/record[id(@country)/../@name='".$nomContinent."']/@cases)"); // Récupérer le nombre de cas pour une année d'un mois
            $nbMorts = $xpath->evaluate("sum(/covid-eu/record_list/year[number(@no)=".$noAnnee."]/month[number(@no)=".$noMois."]/day/record[id(@country)/../@name='".$nomContinent."']/@deaths)"); // Récupérer le nombre de cas pour une année d'un mois
            $output = $output."\t\t<month no='".$noAnnee."-".$noMois."' cases='".$nbCas."' deaths='".$nbMorts."'/>\n";
        }
    }

    $output = $output."\t</continent>\n";
}


$output = $output."</bilan-continents>\n";
file_put_contents("./outputxpath.xml", $output);

?>