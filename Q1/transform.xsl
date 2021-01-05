<?xml version="1.0" encoding="utf-8" standalone="yes"?>
<xsl:transform version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
		<xsl:output method="xml" indent="yes" doctype-system="info.dtd"/>
				
		<xsl:template match="/"><!--Règle qui matche le noeud racine-->
			<bilan-continents>
				<xsl:apply-templates select="/covid-eu/country_list/continent" />
			</bilan-continents>
		</xsl:template>

		<xsl:template match="continent"><!--Règle qui matche chaque continent-->
			<continent name="{@name}" population="{sum(./country/@population)}" area="{sum(./country/@area)}">
			<!--Pour chaque continent, on récupère la liste des mois dont les enregistrements sont concernés par les pays de ce continent.-->
				<xsl:apply-templates select="/covid-eu/record_list/year/month[./day/record/@country = current()/country/@xml:id]">
					<xsl:sort select="concat(../@no, '-', ./@no)" /><!--On les trie par code de mois croissant pour obtenir le résultat escompté-->
					<xsl:with-param name="paysContinent" select="current()/country/@xml:id"/><!--On définit le paramètre du template qui est utilisé pour les mois, en donnant les pays du continent actuel-->
				</xsl:apply-templates>
			</continent>
		</xsl:template>
		  
		<xsl:template match="month"><!--Règle qui matche tous les éléments mois. On peut se permettre de match chaque élément mois, car la DTD ne contient qu'une définition pour un élément mois, et ce sera donc toujours le même type appelé par ce template.-->
			<xsl:param name="paysContinent" /><!--Afin de récupérer uniquement les enregistrements qui concernent un continent, on utilise un paramètre de template-->

			<month no="{concat(../@no, '-', ./@no)}" cases="{sum(./day/record[@country = $paysContinent]/@cases)}" deaths="{sum(./day/record[@country = $paysContinent]/@deaths)}"/>
		</xsl:template>

</xsl:transform>
