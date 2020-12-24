xquery version "1.0" encoding "UTF-8";
declare namespace output = "http://www.w3.org/2010/xslt-xquery-serialization";
declare option saxon:output "indent=yes";


for $continent in doc("../covid-tp.xml")/covid-eu/country_list/continent
    return <continent name='{$continent/@name}' population='{sum($continent/country/@population)}' area='{sum($continent/country/@area)}'>
    {
        for $month in $continent/../../record_list/year/month
            let $no := concat($month/../@no, '-', $month/@no)
            order by $no
            return 
                if(count($month/day/record[@country = $continent/country/@xml:id]) > 0)
                then(<month no="{$no}" cases="{sum($month/day/record[@country = $continent/country/@xml:id]/@cases)}" deaths="{sum($month/day/record[@country = $continent/country/@xml:id]/@deaths)}"/>)
                else()
    }
</continent>



(:Commande : "java -cp ../saxon9he.jar net.sf.saxon.Query -q:xquery.xq":)