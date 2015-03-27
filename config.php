<?php

//****************************************************************************************************
//  PROJECT             :   Visualization of Taxonomic Interactions
//  Developer           :   Rathachai CHAWUTHAI     (National Institute of Informatics,Japan)
//  Supervisor          :   Hideaki TAKEDA          (National Institute of Informatics,Japan)
//  Product Mananger    :   Tsuyoshi HOSOYA         (National Museum of Nature and Science, Japan)
//  Created             :   2015
//****************************************************************************************************

include('common/first.php');
//include('localization.php');
$lang = $GLOBAL["lang"];


$SERVICES = array(
        "sparql"               => "http://rc.lodac.nii.ac.jp/ltk-service/sparql/txi"
        //"sparql"               => "http://lod.ac/species/sparql"
);

$SYSTEM = array(
        "header-accept"         => "Accept: text/plain", // LTK/TXI
        //"header-accept"       => "Accept: application/rdf+xml", // LODAC
        "query-limit"           => 5
);

$NS = array(
        "owl"                   => "http://www.w3.org/2002/07/owl#",
        "skos"                  => "http://www.w3.org/2004/02/skos/core#",
        "cka"                   => "http://www.cka.org/2012/01/cka-onto#" ,
        "lodacns"               => "http://lod.ac/ns/species#" ,
        "rdf"                   => "http://www.w3.org/1999/02/22-rdf-syntax-ns#" ,
        "rdfs"			        => "http://www.w3.org/2000/01/rdf-schema#" ,
        "tl"                    => "http://purl.org/NET/c4dm/timeline.owl#" ,
        "dct"                   => "http://purl.org/dc/elements/1.1/" ,
        "bibo"                  => "http://purl.org/ontology/bibo/" ,
        "xsd"                   => "http://www.w3.org/2001/XMLSchema#" ,
        "foaf"                  => "http://xmlns.com/foaf/0.1/" ,
        "skos"                  => "http://www.w3.org/2004/02/skos/core#",
        "ltk"                   => "http://rc.lodac.nii.ac.jp/ns/ltk#",
        "taxmeon"               => "http://www.yso.fi/onto/taxmeon/",
        "dwc"                   => "http://rs.tdwg.org/dwc/terms/"
);

$NAVIGATIONS = array(
        L("Home")       => "index.php" ,
        L("Guide")      => "guide.php" ,
        L("About")      => "about.php"
);


$PROPSET = array(
        "noise"        => array(
                                        $NS["owl"]."sameAs",
                                        $NS["lodacns"]."hasTaxonRank",
                                        $NS["lodacns"]."isTaxonRankOf",
                                        $NS["skos"]."subject",
                                        $NS["skos"]."isSubjectOf",
                                        $NS["rdf"]."type"
                                ) ,

        "taxonomy"      => array(
                                        $NS["lodacns"]."hasSuperTaxon",
                                        $NS["lodacns"]."hasSubTaxon"
                                ) ,

        "interaction"    => array(
                                        $NS["lodacns"]."isOn",
                                        $NS["lodacns"]."associated"
                                )
);


$INVERSEPROPS = array(
        $NS["lodacns"]."isParasitizedBy"      =>  $NS["lodacns"]."parasitizes" ,
        $NS["lodacns"]."associated"           =>  $NS["lodacns"]."isOn" ,
        $NS["lodacns"]."isTaxonRankOf"        =>  $NS["lodacns"]."hasTaxonRank" ,
        $NS["lodacns"]."hasSubTaxon"          =>  $NS["lodacns"]."hasSuperTaxon"
);

$LINKTYPES = array(
        $NS["lodacns"]."parasitizes"          =>  "interaction" ,
        $NS["lodacns"]."hasSuperTaxon"        =>  "taxonomy" ,
        "default"                             =>  "default" 
);

$SEARCH = array(
        "default"       => "abies",
        "random"        => array( 
                                        "Muscina", "Drosophila", "Megaselia", "Tricimba",
                                        "Dasyscyphella", "Sphaerotheca", "Erysiphe", "Fusicoccum",
                                        "Fagus", "Quercus", "Pinus", "Cryptomeria", "Prunus"
                                )
);

$DATA_LANGUAGE = array(
        "possible"    =>   array("en", "jp"),
        "default"     =>   "en"
);

