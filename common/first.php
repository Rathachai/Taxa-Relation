<?php

//****************************************************************************************************
//	PROJECT				: 	Visualization of Taxonomic Interactions
//	Developer			: 	Rathachai CHAWUTHAI		(National Institute of Informatics,Japan)
//	Supervisor			:	Hideaki TAKEDA			(National Institute of Informatics,Japan)
//	Product Mananger	:	Tsuyoshi HOSOYA			(National Museum of Nature and Science, Japan)
//	Created 			:	2015
//****************************************************************************************************

if(!isset($_COOKIE["lang"])) {
	setcookie( "lang", "en", time() + (10 * 365 * 24 * 60 * 60) );
}

$GLOBAL["lang"] = $_COOKIE["lang"];

if(isset($_GET["lang"])){
	$lang = $_GET["lang"];
	setcookie( "lang", $lang, time() + (10 * 365 * 24 * 60 * 60) );
	$GLOBAL["lang"] = $lang;
}



?>