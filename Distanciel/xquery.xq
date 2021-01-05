xquery version "1.0" encoding "UTF-8";
declare namespace output = "http://www.w3.org/2010/xslt-xquery-serialization";
declare option saxon:output "indent=yes";

(:Ce programme reprend le modèle du programme PHP utilisant des requêtes XPath, de la question 2 du TP1.
  De la même manière, nous itérons sur les continents, et pour chaque continent, on récupère les couples mois-année,
  et pour chaque couple, on récupère les décès et les cas.:)

for $continent in doc("../covid-tp.xml")/covid-eu/country_list/continent
    return <continent name='{$continent/@name}' population='{sum($continent/country/@population)}' area='{sum($continent/country/@area)}'>
    {
        (:Pour chaque continent trouvé dans le document, on récupère la liste des mois par année, par ordre croissant, et
          on itère sur ces mois.:)
        for $month in $continent/../../record_list/year/month
            let $no := concat($month/../@no, '-', $month/@no)
            order by $no
            return 
                (:Ensuite, pour chaque combinaison mois-année, on crée une balise month qui contient les informations que l'on veut.:)
                if(count($month/day/record[@country = $continent/country/@xml:id]) > 0)
                then(<month no="{$no}" cases="{sum($month/day/record[@country = $continent/country/@xml:id]/@cases)}" deaths="{sum($month/day/record[@country = $continent/country/@xml:id]/@deaths)}"/>)
                else()
    }
</continent>


(:Commande pour exécuter dans le dossier courant : "java -cp ../saxon9he.jar net.sf.saxon.Query -q:xquery.xq":)