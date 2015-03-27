<?php
header("Content-Type:text/plain; charset=UTF-8");

function csv_to_array($filename='', $delimiter=',')
{
	if(!file_exists($filename) || !is_readable($filename))
		return FALSE;
	
	$header = NULL;
	$data = array();
	if (($handle = fopen($filename, 'r')) !== FALSE)
	{
		while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
		{
			if(!$header)
				$header = $row;
			else
				$data[] = array_combine($header, $row);
		}
		fclose($handle);
	}
	return $data;
}

function toUri($label){
	$label = trim($label);
	$label = str_replace(" ", "_", $label);
	$label = str_replace("{", "", $label);
	$label = str_replace("}", "", $label);
	$label = str_replace("(", "", $label);
	$label = str_replace(")", "", $label);
	$label = str_replace("?", "", $label);
	$label = str_replace("-", "_", $label);
	$label = str_replace('//', "_", $label);
	$label = str_replace(".", "", $label);
	$label = str_replace("'", "", $label);

	return "lodac:".$label;
}

function toRank($label){
	$label = trim($label);
	$labelNoSpace = str_replace(" ", "", $label);
	$count = strlen($label) - strlen($labelNoSpace);
	echo $count."\n";
	$res = "lodacns:NoRank" ;

	switch($count){
		case 0	: $res = "lodacns:Genus" ; break;
		case 1  : $res = "lodacns:Species" ; break;
		default: $res = "lodacns:SubSpecies" ; break;
	}

	return $res;
}

function toTaxon($uri, $eName, $jName, $higher, $rank, $group){
	//English Name
	$res = "$uri rdfs:label \"$eName\"@en . \n";

	//Japanese Name
	if(!empty($jName)){
		$res .= "$uri rdfs:label \"$jName\"@jp . \n";
	}else{
		$res .= "$uri rdfs:label \"$eName\"@jp . \n";
	}

	//Higher Taxon
	if(!empty($higher)){
		$res .= "$uri lodacns:hasSuperTaxon $higher . \n";
		$res .= "$higher lodacns:hasSubTaxon $uri . \n";
	}

	//Rank
	$res .= "$uri lodacns:hasTaxonRank $rank . \n";
	$res .= "$rank lodacns:isTaxonRankOf $uri . \n";

	//Group
	$res .= "$uri skos:subject $group . \n";
	$res .= "$group skos:isSubjectOf $uri . \n";

	return $res;

}

$uGROUP = array(
		"A"	=>	"lodacns:Animalia",
		"F"	=>	"lodacns:Fungi",
		"P"	=>	"lodacns:Plantae"
);

$csv = csv_to_array('T_1001utf8.csv');

$taxa = array();
$links = array();


echo "@prefix lodac: <http://lod.ac/species/> .	\n";																							
echo "@prefix lodacns: <http://lod.ac/ns/species#> . \n";																							
echo "@prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> . \n";																							
echo "@prefix owl: <http://www.w3.org/2002/07/owl#> . \n";																							
echo "@prefix skos: <http://www.w3.org/2004/02/skos/core#> . \n";	

echo "\n\n\n";

foreach($csv as $line){
//DAT
	$SGroup = 	trim($line["SGroup"]);
	$SGenus = 	trim($line["SGenus"]);
	$SSpecies =	trim($line["SSpecies"]);
	$SISName = 	trim($line["SISName"]);
	$SSName = 	trim($line["SSName"]);
	$SWamei = 	trim($line["SWamei"]);

	$TGroup = 	trim($line["TGroup"]);
	$TGenus = 	trim($line["TGenus"]);
	$TSpecies = trim($line["TSpecies"]);
	$TISName = 	trim($line["TISName"]);
	$TSName = 	trim($line["TSName"]);
	$TWamei = 	trim($line["TWamei"]);

//Name
	$nSGenus = 		$SGenus ;
	$nSSpecies =	$nSGenus." ".$SSpecies ;
	$nSSubs = 		$nSSpecies." ".$SISName ;
	$nSSName =		$SSName ;

	$nTGenus = 		$TGenus ;
	$nTSpecies = 	$nTGenus." ".$TSpecies ;
	$nTSubs = 		$nTSpecies." ".$TISName ;
	$nTSName = 		$TSName ;

//URI
	$uSGroup = 		$uGROUP[$SGroup];	
	$uSGenus = 		toUri($nSGenus);
	$uSSpecies =	toUri($nSSpecies);
	$uSSubs = 		toUri($nSSubs);
	$uSSName = 		toUri($nSSName);

	$uTGroup = 		$uGROUP[$TGroup];
	$uTGenus = 		toUri($nTGenus);
	$uTSpecies = 	toUri($nTSpecies);
	$uTSubs = 		toUri($nTSubs);
	$uTSName = 		toUri($nTSName);

	$uSRank	= "lodacns:Genus";
	if(!empty($SSpecies)) $uSRank	= "lodacns:Species";
	if(!empty($SISName)) $uSRank	= "lodacns:SubSpecies";

	$uTRank	= "lodacns:Genus";
	if(!empty($TSpecies)) $uTRank	= "lodacns:Species";
	if(!empty($TISName)) $uTRank	= "lodacns:SubSpecies";

//ECHO

	$lid = $uSSName.$uTSName;
	if(!in_array($lid,$links)){ 
		//LINK
		$links[] = $lid;
		echo "$uSSName lodacns:isOn $uTSName . \n";
		echo "$uTSName lodacns:associated $uSSName . \n";


		//Source 
		if(!in_array($uSGenus,$taxa)){
			$taxa[] = $uSGenus ;
			echo toTaxon($uSGenus, $nSGenus, $uSRank=="lodacns:Genus"?$SWamei:"", "", "lodacns:Genus", $uSGroup);
		}

		if(!in_array($uSSpecies,$taxa)){
			$taxa[] = $uSSpecies ;
			echo toTaxon($uSSpecies, $nSSpecies, $uSRank=="lodacns:Species"?$SWamei:"", $uSGenus, "lodacns:Species", $uSGroup);
		}

		if(!in_array($uSSubs,$taxa)){
			$taxa[] = $uSSubs ;
			echo toTaxon($uSSubs, $nSSubs, $uSRank=="lodacns:SubSpecies"?$SWamei:"", $uSSpecies, "lodacns:SubSpecies", $uSGroup);
		}

		//Target
		if(!in_array($uTGenus,$taxa)){
			$taxa[] = $uTGenus ;
			echo toTaxon($uTGenus, $nTGenus, $uTRank=="lodacns:Genus"?$TWamei:"", "", "lodacns:Genus", $uTGroup);
		}

		if(!in_array($uTSpecies,$taxa)){
			$taxa[] = $uTSpecies ;
			echo toTaxon($uTSpecies, $nTSpecies, $uTRank=="lodacns:Species"?$TWamei:"", $uTGenus, "lodacns:Species", $uTGroup);
		}

		if(!in_array($uTSubs,$taxa)){
			$taxa[] = $uTSubs ;
			echo toTaxon($uTSubs, $nTSubs, $uTRank=="lodacns:SubSpecies"?$TWamei:"", $uTSpecies, "lodacns:SubSpecies", $uTGroup);
		}

	}
}

//print_r($csv);

?>