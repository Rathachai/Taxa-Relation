<?xml version="1.0"?>
<!--
SimpleRDF To N-Triples
$Revision: 1.1 $     

For terms of use see LICENSE.txt in this distribution.

-->
<!DOCTYPE xsl:stylesheet [
	<!ENTITY rdf "http://www.w3.org/1999/02/22-rdf-syntax-ns#">
	<!ENTITY xml "http://www.w3.org/XML/1998/namespace">
]>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
	xmlns:tri="http://www.webdevelopers.cz/elixon/SimpleRDF"
>
	<xsl:output method="text"/>
	<!-- Start parsing the document -->
	<xsl:template match="/">
          <!-- <xsl:apply-templates select="tri:RDF/rdf:Description[*/text() or */* or */@rdf:resource]"/> -->
          <xsl:apply-templates select="tri:RDF/rdf:Description"/>
	</xsl:template>
	<xsl:template match="rdf:Description">
		<xsl:apply-templates select="." mode="Subject"/>
		<xsl:text> </xsl:text>
		<xsl:apply-templates select="./*" mode="Predicate"/>
		<xsl:text> </xsl:text>
		<xsl:apply-templates select="./*" mode="Object"/>
		<xsl:text> .&#10;</xsl:text>
	</xsl:template>
	<xsl:template match="*" mode="Subject">
		<xsl:choose>
			<xsl:when test="string-length(@rdf:about)">
				<xsl:text>&lt;</xsl:text>
				<xsl:value-of select="@rdf:about"/>
				<xsl:text>&gt;</xsl:text>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="@rdf:nodeID"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<xsl:template match="*" mode="Predicate">
		<xsl:text>&lt;</xsl:text>
		<xsl:value-of select="concat(namespace-uri(), local-name())"/>
		<xsl:text>&gt;</xsl:text>
	</xsl:template>
	<xsl:template match="*" mode="Object">
          <xsl:choose>
            <!-- Resource reference to the ommited blank node: short resource without '<' '>' -->
            <xsl:when test="starts-with(@rdf:resource, '_:')">
              <xsl:value-of select="@rdf:resource"/>
            </xsl:when>
            <xsl:when test="@rdf:resource">
              <xsl:text>&lt;</xsl:text>
              <xsl:value-of select="@rdf:resource"/>
              <xsl:text>&gt;</xsl:text>                
            </xsl:when>
            <!-- Literal -->
            <xsl:otherwise>
		<xsl:text>&quot;</xsl:text>
		<xsl:copy-of select="node()"/>
		<xsl:text>&quot;</xsl:text>
            </xsl:otherwise>
          </xsl:choose>
          <xsl:if test="string-length(../@xml:lang)">
            <xsl:value-of select="concat('@', ../@xml:lang)"/>
          </xsl:if>
          <xsl:if test="@rdf:datatype">
            <xsl:text>^^&lt;</xsl:text>
            <xsl:value-of select="@rdf:datatype"/>
            <xsl:text>&gt;</xsl:text>
          </xsl:if>
        </xsl:template>
</xsl:stylesheet>