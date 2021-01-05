<?php

$file = "../covid-tp.xml";
$doc = new DOMDocument();
$doc-> validateOnParse = true;
$doc->preserveWhiteSpace = false;
$doc->load($file);
$xpath = new DOMXpath($doc);

// Récupérer la liste des noms des continents


$output = "";
$output = $output. <<<XML
<?xml version="1.0" encoding="UTF-8"?>\n<!DOCTYPE bilan-continents>\n<SYSTEM "info.dtd">
XML;

$output = $output."\n<bilan-continents>\n";

//Contrairement aux autres solutions PHP présentées, à la place de générer un dictionnaire, nous avons choisi
//de générer une chaine de caractères à mesure que l'on avance dans l'analyse du fichier XML.
//Grace aux requêtes XPath, le code est plus léger et plus simplement à comprendre, si l'on connait XPath.


$continentsNoms = $xpath->evaluate("/covid-eu/country_list/continent/@name");
foreach($continentsNoms as $continent)
{
    // Récupérer une balise <continent> pour chaque continent trouvé dans la requête précédente
    //Pour chaque continent trouvé dans le fichier XML, on génère une balise XML "continent", et on effectue 2 requêtes
    //pour récupérer les valeurs qui nous intéressent, à savoir le total de population et d'aire.
    $nomContinent = $continent->value;
    $output = $output."\t<continent name='".$nomContinent."' ";
    $output = $output."population='".$xpath->evaluate("sum(/covid-eu/country_list/continent[@name = '".$nomContinent."']/country/@population)")."' "; // Somme aire d'un continent     
    $output = $output."area='".$xpath->evaluate("sum(/covid-eu/country_list/continent[@name = '".$nomContinent."']/country/@area)")."'>\n"; // Somme aire d'un continent

    $annees = $xpath->evaluate("/covid-eu/record_list/year/@no");
    for($i = $annees->length - 1; $i >= 0; $i--)
    {
        //Ensuite, pour chaque année trouvée dans les records, que l'on itère par ordre décroissante de recherche,
        //On récupère les mois de cette année
        $noAnnee = $annees[$i]->value;
        $moiss = $xpath->evaluate("/covid-eu/record_list/year[@no='".$noAnnee."']/month/@no");        
        for($j = $moiss->length - 1; $j >= 0; $j--)
        {
            //Puis pour chaque mois,on itère par ordre décroissant toujours, et on récupère le nombre de décès et
            //de cas par mois pour le continent actuellement étudié.
            $noMois = $moiss[$j]->value;
            // Récupérer le nombre de cas pour une année d'un mois
            $nbCas = $xpath->evaluate("sum(/covid-eu/record_list/year[number(@no)=".$noAnnee."]/month[number(@no)=".$noMois."]/day/record[id(@country)/../@name='".$nomContinent."']/@cases)");
            // Récupérer le nombre de cas pour une année d'un mois
            $nbMorts = $xpath->evaluate("sum(/covid-eu/record_list/year[number(@no)=".$noAnnee."]/month[number(@no)=".$noMois."]/day/record[id(@country)/../@name='".$nomContinent."']/@deaths)");
            $output = $output."\t\t<month no='".$noAnnee."-".$noMois."' cases='".$nbCas."' deaths='".$nbMorts."'/>\n";
        }
    }

    $output = $output."\t</continent>\n";
}

//Il suffit ensuite d'écrire notre chaine dans le fichier output.
$output = $output."</bilan-continents>\n";
file_put_contents("./outputxpath.xml", $output);

?>