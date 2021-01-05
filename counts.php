<?php
	$file = "covid-tp.xml";
	$doc = new DOMDocument();
	$doc-> validateOnParse = true;
	$doc->preserveWhiteSpace = false;
	$doc->load($file);
	$xpath = new DOMXpath($doc);

	echo "nbContinents : ".$xpath->evaluate("count(//continent)");
	echo "\nnbAnnees : ".$xpath->evaluate("count(//year)");
	echo "\nnbMois : ".$xpath->evaluate("count(//month)");
	echo "\nnbJours : ".$xpath->evaluate("count(//day)");
	echo "\nnbRecords : ".$xpath->evaluate("count(//record)");
	echo "\nnbPays : ".$xpath->evaluate("count(//country)");
	echo "\nbNoeudsEnfants : ".$xpath->evaluate("count(/covid-eu/*)");
	echo "\nnbNoeuds : ".$xpath->evaluate("count(//*)");
	echo "\n";

?>