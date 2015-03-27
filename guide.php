<?php

//****************************************************************************************************
//	PROJECT				: 	Visualization of Taxonomic Interactions
//	Developer			: 	Rathachai CHAWUTHAI		(National Institute of Informatics,Japan)
//	Supervisor			:	Hideaki TAKEDA			(National Institute of Informatics,Japan)
//	Product Mananger	:	Tsuyoshi HOSOYA			(National Museum of Nature and Science, Japan)
//	Created 			:	2015
//****************************************************************************************************

include('common/first.php');
include('localization.php');
include('config.php');

$qr = $_GET["q"];
$lang = $GLOBAL["lang"];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo L("Project Name"); ?></title>
	<link href="./lib/bootstrap/css/bootstrap.css" rel="stylesheet">
	<link href="./lib/bootstrap/css/bootstrap-theme.css" rel="stylesheet">
	<script src="./lib/jquery/jquery2.1.3.js"></script>
	<script src="./lib/bootstrap/js/bootstrap.js"></script>
	<script src="./lib/d3/d3.v3.min.js"></script>

	<style type="text/css">
		div.center{
			padding-top:7px;
		}

		li img{
			max-width:80%;
		}

	</style>
</head>
<body>

<?php  
/*****************************************************************************/
/***************************** HEADER ****************************************/
	include "common/header.php" ; 
/*****************************************************************************/
?>

<div class="container">
	<div class="page-header">
  		<h1><?php echo L("Guide"); ?></h1>
	</div>
	



	<div class="panel panel-default">
	  <div class="panel-heading">
	    <h3 class="panel-title"><?php echo L("search head") ?></h3>
	  </div>
	  <div class="panel-body">
	    <ol>
	    	<li>
	    		<?php echo L("search 1") ?>
	    		<div class='center' align='center'><img src='images/guide/search.png' alt='search panel' /></div>
	    	</li>
	    	 <hr/>
	    	<li>
	    		<?php echo str_replace("{1}","<img src='images/guide/add.png' alt='button [x]' />", L("search 2")); ?>
	    	</li>
	    	 <hr/>
	    	<li>
	    		<?php echo str_replace("{1}","<img src='images/guide/expand.png' alt='button [x]' />", L("search 3")); ?>
	    	</li>
	    	<hr/>
	    	<li>
	    		<?php echo L("search 4") ?>
	    		<div class='center' align='center'><img src='images/guide/search4.png' alt='search panel' /></div>
	    	</li>
	    </ol>
	  </div>
	</div>


	<div class="panel panel-default">
	  <div class="panel-heading">
	    <h3 class="panel-title"><?php echo L("path head") ?></h3>
	  </div>
	  <div class="panel-body">
	    <ol>
	    	<li>
	    		<?php echo L("path 1") ?>
	    		<div class='center' align='center'><img src='images/guide/fp1.png' alt='search panel' align='middle' /></div>
	    	</li>
	    	<hr/>
	    	<li>
	    		<?php echo L("path 2") ?>
	    		<div class='center' align='center'><img src='images/guide/fp2.png' alt='search panel' align='middle' /></div>
	    	</li>
	    	<hr/>
	    	<li>
	    		<?php echo L("path 3") ?>
	    		<div class='center' align='center'><img src='images/guide/fp3.png' alt='search panel' align='middle' /></div>
	    	</li>
	    	<hr/>
	    	<li>
	    		<?php echo L("path 4") ?>
	    		<div class='center' align='center'><img src='images/guide/fp4.png' alt='search panel' align='middle' /></div>
	    	</li>
	    	<hr/>
	    	<li>
	    		<?php echo L("path 5") ?>
	    		<div class='center' align='center'><img src='images/guide/fp5.png' alt='search panel' align='middle' /></div>
	    	</li>
	    	<hr/>
	    	<li>
	    		<?php echo L("path 6") ?>
	    		<div class='center' align='center'><img src='images/guide/fp6.png' alt='search panel' align='middle' /></div>
	    	</li>
	    	<hr/>
	    	<li>
	    		<?php echo L("path 7") ?>
	    		<div class='center' align='center'><img src='images/guide/fp7.png' alt='search panel' align='middle' /></div>
	    	</li>
	    	<hr/>
	    	<li>
	    		<?php echo L("path 8") ?>
	    	</li>
	    </ol>
	  </div>
	</div>


</div>

<?php
/*****************************************************************************/
/***************************** FOOTER ****************************************/
	include "common/footer.php" ; 
/*****************************************************************************/
?>

</body>