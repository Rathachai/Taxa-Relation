<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet [
	<!ENTITY rdf 'http://www.w3.org/1999/02/22-rdf-syntax-ns#'>
	<!ENTITY xml 'http://www.w3.org/XML/1998/namespace'>
]>
<!--
SimpleRDF
$Revision: 2.1 $    
http://sourceforge.net/projects/simplerdf/

For terms of use see LICENSE.txt in this distribution.

TODO
Relative URI resolving.
     
DIDN'T PASS:
rdf-containers-syntax-vs-schema/test004.rdf
     
-->
<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
  xmlns:rdf-wd="http://www.w3.org/TR/WD-rdf-syntax#"
  xmlns:tri="http://www.webdevelopers.cz/elixon/SimpleRDF"
  >	
<xsl:output indent="yes"/>

<!-- Start parsing the document -->
<xsl:template match="/">
  <xsl:comment>&#xA;SimpleRDF/XSL by Daniel Sevcik, Web Developers(tm) &lt;http://www.webdevelopers.cz&gt;&#xA;$Revision: 2.1 $&#xA;XSLT processor: <xsl:value-of select="concat(system-property('xsl:vendor'), ' &lt;', system-property('xsl:vendor-url'), '&gt;')"/>&#xA;</xsl:comment>
  <tri:RDF>
    <xsl:choose>
      <!-- There is a rdf:RDF element  -->
      <xsl:when test="//rdf:RDF/*">
        <xsl:apply-templates select="//rdf:RDF/*" mode="FindPredicates"/>
      </xsl:when>
      <!-- rdf:RDF ommited -->
      <xsl:otherwise>
        <xsl:apply-templates select="/*" mode="FindPredicates"/>
      </xsl:otherwise>
    </xsl:choose>
  </tri:RDF>	
</xsl:template>	

<!-- FIGHTING PREDICATES ==================================== -->

<!-- Context node Subject -->
<xsl:template mode="FindPredicates" match="*">
  <!-- Abbreviated type -->
  <xsl:if test="concat(namespace-uri(), local-name())!='&rdf;Description'">
    <xsl:call-template name="AbbrevType"/>
  </xsl:if>
  <!-- Find regular predicates, predicate as the child of the subject element -->
  <xsl:apply-templates select="./*" mode="ProcessPredicate">
    <xsl:with-param name="comment">Regular predicate</xsl:with-param>
  </xsl:apply-templates>
  <!-- Find abbreviated literals [2.12], <subject xyz:predicate="literal"/> -->
  <xsl:apply-templates select="./@*[concat(namespace-uri(), local-name())='&rdf;type' or not(namespace-uri()='&rdf;' or namespace-uri()='&xml;')]" mode="ProcessPredicate">
    <xsl:with-param name="comment">Abbreviated literal</xsl:with-param>                  
  </xsl:apply-templates>
  <!-- Find abbreviated classes [2.13] <xyz:SubjectClass .../> - same as ABBREVIATED TYPE? -->
  <!--
  <xsl:apply-templates select="current()[concat(namespace-uri(),local-name())!='&rdf;Description' and concat(namespace-uri(),local-name())!='&rdf;Bag']" mode="ProcessPredicate">
    <xsl:with-param name="comment">Abbreviated class</xsl:with-param>                  
  </xsl:apply-templates>
   -->
  <!-- Ommited blank node: see test file rdf-charmod-literals/test001.rdf -->
  <xsl:apply-templates select="./*/@*[namespace-uri()!='&rdf;' and namespace-uri()!='&xml;']" mode="ProcessPredicate">
    <xsl:with-param name="comment">Ommited blank node</xsl:with-param>                                    
    <xsl:with-param name="current">ommitedBlankNode</xsl:with-param>
  </xsl:apply-templates>
  <!-- RDF:Bag-like -->
  <xsl:apply-templates select="./*/*[contains('Bag Seq Alt List', local-name()) and namespace-uri()='&rdf;']" mode="FindPredicates">
    <xsl:with-param name="comment">Container</xsl:with-param>                                    
  </xsl:apply-templates>
</xsl:template>

<!-- Abbreviated RDF Type [2.13] -->
<xsl:template name="AbbrevType">
  <xsl:comment>Abbreviated RDF type [2.13]: <xsl:call-template name="describeElement"/></xsl:comment>
  <rdf:Description>
    <!-- Add info about Subject -->
    <xsl:apply-templates select="." mode="SubjectName"/>
    <!-- Create predicate and copy the content -->
    <rdf:type>
      <xsl:attribute name="rdf:resource">
        <xsl:value-of select="concat(namespace-uri(), local-name())"/>
      </xsl:attribute>
    </rdf:type>
  </rdf:Description>
</xsl:template>

<!-- Nested descriptions -->
<xsl:template match="*[not(@rdf:parseType='Literal') and *]" mode="ProcessPredicate">
  <xsl:param name="comment" />
  <xsl:param name="current" />          
  
  <xsl:comment>Nested Descriptions/<xsl:value-of select="$comment"/>: <xsl:call-template name="describeElement"/></xsl:comment>
  <xsl:for-each select="*">
    <rdf:Description>
      <!-- SUBJECT: Add info about Subject -->
      <xsl:apply-templates select="../.." mode="SubjectName">
        <xsl:with-param name="current" select="$current"/>
      </xsl:apply-templates>
      <!-- PREDICATE: Create predicate element from the element or abbreviated literals [2.12] -->
      <xsl:element name="{local-name(..)}" namespace="{namespace-uri(..)}">
        <!-- OBJECT -->
        <!-- !!!rdf-charmod-literals/test001.rdf: Matching also '@*'??? -->
        <!-- <xsl:apply-templates select="." mode="Object"/> -->
        <xsl:call-template name="mkObjectReference" />
      </xsl:element>
    </rdf:Description>
  </xsl:for-each>
  <xsl:apply-templates select="*" mode="FindPredicates"/>
</xsl:template>

<!-- Create simplified tripples: context node Predicate -->
<xsl:template match="@*|*" mode="ProcessPredicate">
  <xsl:param name="comment" />
  <xsl:param name="current" />          
  
  <xsl:comment>Single predicate/<xsl:value-of select="$comment"/>: <xsl:call-template name="describeElement"/></xsl:comment>
  <rdf:Description>
    <!-- SUBJECT: Add info about Subject -->
    <xsl:apply-templates select=".." mode="SubjectName">
      <xsl:with-param name="current" select="$current"/>
    </xsl:apply-templates>
    <!-- PREDICATE: Create predicate element from the element or abbreviated literals [2.12] -->
    <xsl:element name="{local-name()}" namespace="{namespace-uri()}">
      <!-- OBJECT -->
      <!-- !!!rdf-charmod-literals/test001.rdf: Matching also '@*'??? -->
      <xsl:apply-templates select="." mode="Object"/>
    </xsl:element>
  </rdf:Description>
</xsl:template>

<!-- FIGHTING OBJECTS ==================================== -->
<!-- If there are only text nodes or type LITERAL: match="*[@rdf:parseType='Literal']" -->
<xsl:template mode="Object" match="text()|*[not(@rdf:nodeID|@rdf:resource|@rdf:about) and (@rdf:parseType='Literal' or not(*))]" priority="9">
  <xsl:attribute name="rdf:parseType">Literal</xsl:attribute>
  <xsl:copy-of select="@rdf:datatype|node()"/>
</xsl:template>

<!-- If omitted empty node as in test file rdf-charmod-literals/test001.rdf -->
<xsl:template name="mkObjectReference" mode="Object" match="*" priority="8">   
  <xsl:choose>
    <!-- [2.11] Omitting Blank Nodes -->
    <!-- @rdf:resource reference -->
    <xsl:when test="@rdf:nodeID|@rdf:resource|@rdf:about">
      <xsl:copy-of select="@rdf:nodeID|@rdf:resource"/>
      <xsl:if test="@rdf:about and not(@rdf:resource)">
        <xsl:attribute name="rdf:resource"><xsl:value-of select="@rdf:about"/></xsl:attribute>
      </xsl:if>
    </xsl:when>
    <xsl:otherwise>
      <xsl:attribute name="rdf:resource">
        <xsl:value-of select="concat('_:','SimpleRDF_', generate-id())"/>
      </xsl:attribute>
    </xsl:otherwise>
  </xsl:choose>
  
  <xsl:attribute name="rdf:parseType">
    <xsl:text>Resource</xsl:text>
  </xsl:attribute>
</xsl:template>

<!-- abbreviated predicate -->
<xsl:template mode="Object" match="@*">
  <xsl:attribute name="rdf:parseType">Literal</xsl:attribute>
  <xsl:value-of select="."/>
</xsl:template>

<!-- FIGHTING SUBJECTS ==================================== -->
<!-- Output the subject's URI ref -->
<xsl:template match="*" mode="SubjectName">
  <xsl:param name="current" />
  
  <!-- Language -->
  <xsl:attribute name="xml:lang">
    <!-- @todo With command line's xsltproc does not select? -->
    <xsl:value-of select="ancestor-or-self::*[@xml:lang][1]/@xml:lang"/>
  </xsl:attribute>
  
  <!-- URIref -->		
  <xsl:choose>
    <!-- Standard URI ref attr @rdf:about -->
    <!-- Explicitly given blank node ID @rdf:nodeID [2.10] -->
    <xsl:when test="@rdf:about|@rdf:nodeID">
      <xsl:copy-of select="@rdf:about|@rdf:nodeID"/>
    </xsl:when>
    <!-- Abbreviated -->
    <xsl:when test="@rdf:ID">
      <xsl:attribute name="rdf:about">
        <xsl:value-of select="concat('#', @rdf:ID)"/>
      </xsl:attribute>
    </xsl:when>
    <!-- Implicitly given blank node ID -->
    <xsl:otherwise>
      <xsl:attribute name="rdf:nodeID">
        <xsl:value-of select="concat('SimpleRDF_', generate-id())"/>
      </xsl:attribute>
    </xsl:otherwise>
  </xsl:choose>
</xsl:template>

<!-- OTHERS ==================================== -->

<!-- Just for outputting some comments about current node -->
<xsl:template name="describeElement" match="*">
  <xsl:text>&lt;</xsl:text>
  <xsl:value-of select="local-name()"/>
  <xsl:for-each select="@*">
    <xsl:text> </xsl:text>
    <xsl:value-of select="concat(local-name(), '=&quot;', ., '&quot;')"/>
  </xsl:for-each>
  <xsl:text>&gt;</xsl:text>            
</xsl:template>

   
</xsl:stylesheet>
