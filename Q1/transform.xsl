<?xml version="1.0" encoding="utf-8" standalone="yes"?>
<xsl:transform version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
		<xsl:output method="xml" indent="yes" doctype-system="info.dtd"/>
				
		<xsl:template match="/">
			<bilan-continents>
				<xsl:apply-templates select="/covid-eu/country_list/continent" />
			</bilan-continents>
		</xsl:template>

		<xsl:template match="continent">
			<continent name="{@name}" population="{sum(./country/@population)}" area="{sum(./country/@area)}">
				<xsl:apply-templates select="/covid-eu/record_list/year/month[./day/record/@country = current()/country/@xml:id]">
					<xsl:sort select="concat(../@no, '-', ./@no)" />
					<xsl:with-param name="paysContinent" select="current()/country/@xml:id"/>
				</xsl:apply-templates>
			</continent>
		</xsl:template>
		  
		<xsl:template match="month">
			<xsl:param name="paysContinent" />

			<month no="{concat(../@no, '-', ./@no)}" cases="{sum(./day/record[@country = $paysContinent]/@cases)}" deaths="{sum(./day/record[@country = $paysContinent]/@deaths)}"/>
		</xsl:template>

</xsl:transform>
