<?php 
header('Content-type: text/plain');
include('Sax4PHP/Sax4php.php');

class nbl_sax2 extends DefaultHandler {
  public $nb;
  
  function startDocument() {$this->nb = 0;}
  
  function startElement($nom, $att) { 
      if ($nom=='livre') 
        if ($att['annee']>1960) 
          $this->nb += 1;}
}

$fic = file_get_contents('ex_asimov.xml');

try {
    $nbl = new nbl_sax2();
    $sax = new SaxParser($nbl);
    $sax->parse($fic);
    echo $nbl->nb;
}catch(SAXException $e){  
    echo $e;
}?>
