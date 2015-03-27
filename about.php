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
		div#organizers img{
			max-width:60%;
			max-height: 150px;
			padding-bottom: 20px;
		}

		img.person{
			height: 130px;
			padding-bottom: 20px;
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
  		<h1><?php echo L("About"); ?></h1>
	</div>
	

	<div class="row">
		<div class="col-md-8">
			<div class="jumbotron">
			  <p class="text-justify">
			  	<?php echo L("about text") ?>
			  </p>
			</div>

  			<div id="developers" class="panel panel-default">
			  <div class="panel-heading"><strong><?php echo L("Development Team"); ?></strong></div>
			  <div class="panel-body row" align='center'>
			  	<div class="col-md-4">
			  		<img src="images/about/hosoya.jpg" class="img-circle person"> <br/>
				    <strong><?php echo L("Hosoya-name"); ?></strong> <br/>
				    <em><?php echo L("Hosoya-position"); ?></em> <br/>
				    <a target="_blank" href="http://www.kahaku.go.jp/english/research/researcher/researcher.php?d=hosoya">website</a><br/><br/>
				    <?php echo L("KAHAKU"); ?>
			    </div>

			    <div class="col-md-4">
			    	<img src="images/about/takeda.jpg" class="img-circle person"> <br/>
				    <strong><?php echo L("Takeda-name"); ?></strong> <br/>
				    <em><?php echo L("Takeda-position"); ?></em><br/>
				    <a target="_blank" href="https://twitter.com/takechan2000">twitter: @Takechan2000</a><br/><br/>
				    <?php echo L("LODI"); ?>
				</div>
			    <div class="col-md-4">
			    	<img src="images/about/rathachai.jpg" class="img-circle person"> <br/>
				    <strong><?php echo L("Rathachai-name"); ?></strong> <br/>
				    <em><?php echo L("Rathachai-position"); ?></em> <br/>
				    <a target="_blank" href="https://www.linkedin.com/in/rathachai">linkedin: rathachai</a><br/><br/>
				    <?php echo L("LODI"); ?>
				</div>
			  </div>
			</div>

			<div class="panel panel-default">
			  <div class="panel-heading"><strong><?php echo L("Software License"); ?></strong></div>
			  <div class="panel-body">
			  	<?php echo L("Our License Statement") ?>
			 	<ul>
			 		<li>&copy; <?php echo date("Y")." - ".(date("Y")+5); ?>  <?php echo L("KAHAKU"); ?> (http://www.kahaku.go.jp)</li>
			 		<li>&copy; <?php echo date("Y")." - ".(date("Y")+5); ?>  <?php echo L("LODI"); ?> (http://linkedopendata.jp)</li>
			 		<li>&copy; <?php echo date("Y")." - ".(date("Y")+5); ?>  <?php echo L("Rathachai-name"); ?> (https://www.linkedin.com/in/rathachai)</li>
			 	</ul>
			 	<?php echo L("Note 1") ?>
				<hr/>
				<?php echo L("Soucecode Host") ?> : <a target="_blank" href="https://github.com/Rathachai/Taxa-Relation">https://github.com/Rathachai/Taxa-Relation</a>
			  </div>
			</div>

			<div class="panel panel-default">
			  <div class="panel-heading"><strong><?php echo L("Third-Party Libraries"); ?></strong></div>
			  <div class="panel-body">
			  	<table class="table table-hover">
			  		<tr>
			  			<td>Twitter Bootstrap</td>
			  			<td><a href="http://getbootstrap.com" target="_blank" class="text-info">http://getbootstrap.com</a></td>
			  		</tr>
			  		<tr>
			  			<td>JQuery</td>
			  			<td><a href="http://jquery.com" target="_blank" class="text-info">http://jquery.com</a></td>
			  		</tr>
			  		<tr>
			  			<td>D3</td>
			  			<td><a href="http://d3js.org" target="_blank" class="text-info">http://d3js.org</a></td>
			  		</tr>
			  		<tr>
			  			<td>SimpleRDF</td>
			  			<td><a href="http://simplerdf.sourceforge.net" target="_blank" class="text-info">http://simplerdf.sourceforge.net</a></td>
			  		</tr>
			  		<tr>
			  			<td>RelFinder</td>
			  			<td><a href="http://www.visualdataweb.org/relfinder.php" target="_blank" class="text-info">http://www.visualdataweb.org/relfinder.php</a></td>
			  		</tr>
			  		<tr>
			  			<td>SparqlLib</td>
			  			<td><a href="http://graphite.ecs.soton.ac.uk/sparqllib" target="_blank" class="text-info">http://graphite.ecs.soton.ac.uk/sparqllib</a></td>
			  		</tr>
			  		<tr>
			  			<td>Sesame</td>
			  			<td><a href="http://rdf4j.org" target="_blank" class="text-info">http://rdf4j.org</a></td>
			  		</tr>
			  	</table>
			  </div>
			</div>


		</div>
  		<div class="col-md-4">
  			<div id="organizers" class="panel panel-default">
			  <div class="panel-heading"><strong><?php echo L("Organizers"); ?></strong></div>
			  <div class="panel-body" align='center'>
			    <img src="images/about/kahaku.jpg" /> <br/>
			    <strong><?php echo L("KAHAKU"); ?></strong> <br/>
			    <a  target="_blank" href="http://www.kahaku.go.jp">http://www.kahaku.go.jp</a>
			    <hr/>
			    <img src="images/about/lodi.jpg" /> <br/>
			    <strong><?php echo L("LODI"); ?></strong><br/>
			    <a  target="_blank" href="http://linkedopendata.jp">http://linkedopendata.jp</a>
			    <hr/>
			    <img src="images/about/lodac.png" /> <br/>
			    <strong><?php echo L("LODAC"); ?></strong><br/>
			    <a  target="_blank" href="http://lod.ac">http://lod.ac</a>
			    <hr/>
			    <img src="images/about/nii.gif" /> <br/>
			    <strong><?php echo L("NII"); ?></strong><br/>
			    <a target="_blank" href="http://www.nii.ac.jp">http://www.nii.ac.jp</a>
			  </div>
			</div>
  
			</div>
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