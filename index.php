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

	<style type="text/css">
		body > .container{
			width:100%;
		}

		.fixheight1 {
			height:680px;
		}

		.outer {
			height:500px;
			overflow-y:scroll;
		}

		.node {
		  stroke: #fff;
		  fill:#ddd;
		  stroke-width: 1.5px;
		}

		.visited-node {
		  stroke: #000;	
		  stroke-width: 1.5px;	
		}

		.selected-node {
		  stroke: #f80;
		  stroke-width: 2px;
		}

		.node-Fungi {
			fill: rgb(255, 193, 0);
			fill: url("#img_fungi");
		}

		.node-Animalia {
			fill: rgb(239, 126, 50);
			fill: url("#img_animalia");
		}

		.node-Plantae {
			fill: rgb(112, 173, 70);
			fill: url("#img_plantae");
		}

		.source-node {
		  /*stroke: #045FB4;*/
		  stroke: #08f;
		}

		.target-node {
		  stroke: #08f;
		}

		.link {
		  stroke: #999;
		  stroke-opacity: .6;
		}

		.link-interaction{

		}

		.link-taxonomy{
			stroke-dasharray: 5 5;
		}

		marker {
			stroke: #999;
			fill:rgba(124,240,10,0);
		}

		.node-text {
		  font: 10px sans-serif;
		  fill:black;
		}

		.link-text {
		  font: 9px sans-serif;
		  fill:grey;
		}

		.search-result {
		  width:70%;
		  word-wrap: break-word !important;
          word-break: break-all;
		}
		.search-action {
		  width:30%;
		  text-align:right;
		}

		.search-result .uri{
			padding-top:7px;
			font-size:small;
			color:#555;
			font-style: italic;

		}

		.search-result-icon {
			padding:12px 0 7px 7px !important;
			width:5px;
		}

		.find-path{
			float:left;
			display:none;
		}

		.panel-symbol{
			float:right;
		}

		.lock{
			stroke: #81DAF5;
			fill: #A9F5F2;
		}

		.to-lock{
			stroke: #81DAF5;
			fill: #fff;
		}

		#nameSearch1{
			
		}
		#imgLoader1{
		}
	</style>

	<script src="./lib/jquery/jquery2.1.3.js"></script>
	<script src="./lib/bootstrap/js/bootstrap.js"></script>
	<script src="./lib/d3/d3.v3.min.js"></script>

	<script>


		$(document).ready(function(){

			$('#imgLoader1').hide();
			$('#imgLoader2').hide();

			<?php

				if(!isset($qr)){
					//$defaultSearch = $SEARCH['default'];
					$randomSearchList = $SEARCH['random'];
					$defaultSearch = $randomSearchList[array_rand($randomSearchList, 1)];
					echo "$('#nameSearch1').val('$defaultSearch');";
					echo "searchName();";
				}

			?>

			//ENTER
		    $("#nameSearch1").keyup(function(e){
		    	//if($("#nameSearch1").val().length > 2){
		    	if(e.keyCode==13){	
		    		searchName();
		    	}
		    });

		});



		function searchName(){
			if($("#nameSearch1").val().length > 2){
	    		$('#imgLoader1').show();
		        $.getJSON("./data/search.php?q="+$("#nameSearch1").val(), function(result){
		        	$("#searchResult1").empty();
		            $.each(result, function(i, field){
		            	var qr = $("#nameSearch1").val();
		            	var reg = new RegExp(qr, 'gi');
		            	var label = field['label'];
		            	var labelx = label.replace(reg, function(str) {return "<strong>"+str+"</strong>"});
		            	var id = field['id'];
		            	var subject = field['subject'];
		            	var rank = field['rank'];
		                $("#searchResult1").append(
		                  "<tr>" 
		                  		+ "<td class='search-result-icon'>"
		                  			+ "<img src='images/" +  subject.toLowerCase() + ".jpg' height='15' width='15' class='img-circle' />"
		                  		+ "</td>"
		                  		+ "<td class='search-result'>" 
		                  			+ labelx 
		                  			+ "<br/>"
		                  			//+ "<span class='uri'>Type: " + subject + "</span>"
		                  			//+ "<br/>"
		                  			//+ "<span class='uri'>Rank: " + rank + "</span>"
		                  			//+ "<br/>"
		                  			+ "<span class='uri'>URI: " + id + "</span>"
		                  		+ "</td>"
			                	+ "<td class='search-action'>" 
			                		//+ "<button type='button' title='Add'  onclick=\"addNode('" + label + "' , '" + id + "', '" + subject + "', '" + rank + "');\" class='btn btn-default btn-sm'> <span class='glyphicon glyphicon-plus' aria-hidden='true'></span> </button>" 
			                		+ "<button type='button' title='<?php echo L('Add'); ?>'  onclick=\"addNode('" + id + "');\" class='btn btn-default btn-sm'> <span class='glyphicon glyphicon-plus' aria-hidden='true'></span> </button>" 
			                		+ "  " 
			                		+ "<button type='button' title='<?php echo L('Add and Expand'); ?>'  onclick=\"explore('" + id + "');\" class='btn btn-default btn-sm'> <span class='glyphicon glyphicon-fullscreen' aria-hidden='true'></span> </button>" 
			                	+ "</td>"

		                   + "</tr>"
		                );
		            });
		            $('#imgLoader1').hide();
		        });
	    	}
		}
	</script>

	
	<script>
		var width = 800 ;
		var height = 600;
		var graph = null;
		var links;
		var nodes;
		var errObj;
		var link_len  = 200;
		var link_lenx = link_len * 1;

		var leftLock = 100;
		var rightLock = width-100;

		var xSourceNode=null;
		var xTargetNode=null;
		var xSourceId = "";
		var xTargetId = "";

		var selectedNode=null;
		 
		var color = d3.scale.category20();

		var force = d3.layout.force()
		    .size([width, height]);
			
		function update(){

		  //Re Width
			width = $("svg").parent().width() ;
			rightLock = width-100;
			force = d3.layout.force().size([width, height]);


		  //MARKER =========================================================== MARKER ===========================================================
		  svg.selectAll("defs").remove();

		 	defs = svg.append("svg:defs");
			defs.selectAll("marker")
			    .data(["end"])
			  .enter().append("svg:marker")
			    .attr("id", String)
			    .attr("viewBox", "0 -5 10 10")
			    .attr("refX", 50)
			    .attr("refY", -0.5)
			    .attr("markerWidth", 6)
			    .attr("markerHeight", 6)
			    .attr("orient", "auto")
			  .append("svg:polyline")
			    .attr("points", "0,-5 10,0 0,5")
			    ;

			defs.append("pattern")
					.attr("id","img_fungi")
					.attr("patternUnits", "userSpaceOnUse")
					.attr("width", 40)
					.attr("height", 40)
					.append("image")
						.attr("xlink:href", "images/fungi.jpg")
						.attr("x", 0)
						.attr("y", 0)
						.attr("width", 40)
						.attr("height", 40)
					;

			defs.append("pattern")
					.attr("id","img_animalia")
					.attr("patternUnits", "userSpaceOnUse")
					.attr("width", 40)
					.attr("height", 40)
					.append("image")
						.attr("xlink:href", "images/animalia.jpg")
						.attr("x", 0)
						.attr("y", 0)
						.attr("width", 40)
						.attr("height", 40)
					;

			defs.append("pattern")
					.attr("id","img_plantae")
					.attr("patternUnits", "userSpaceOnUse")
					.attr("width", 40)
					.attr("height", 40)
					.append("image")
						.attr("xlink:href", "images/plantae.jpg")
						.attr("x", 0)
						.attr("y", 0)
						.attr("width", 40)
						.attr("height", 40)
					;

		 //LOCK  ========================================================== LOCK ============================================================
		 var lock = svg.selectAll(".lock");
		 lock.remove();

		 var lCir = svg.append("circle")
				.attr("class", "lock to-lock")
				.attr("stroke-width",1)
				.attr("cx",-100)
				.attr("cy",-100)
				.attr("r",30)
				;

		 var rCir = svg.append("circle")
				.attr("class", "lock to-lock")
				.attr("stroke-width",1)
				.attr("cx",-100)
				.attr("cy",-100)
				.attr("r",30)
				;

		var xlCir = svg.append("circle")
				.attr("class", "lock")
				.attr("stroke-width",1)
				.attr("cx",-100)
				.attr("cy",-100)
				.attr("r",20)
				;

		 var xrCir = svg.append("circle")
				.attr("class", "lock")
				.attr("stroke-width",1)
				.attr("cx",-100)
				.attr("cy",-100)
				.attr("r",20)
				;

		  //LINK =========================================================== LINK ===========================================================
		  var link = svg.selectAll(".link")
		        .data(force.links, function(d) {
		            return d.source.id + "-" + d.target.id; });	  
		  link.exit().remove();
		  
		  
		  var graph_links = [];
		  graph.links.forEach(function(e) { 
		    // Get the source and target nodes
		    var sourceNode = filterNodeById(e.source)[0],
		        targetNode = filterNodeById(e.target)[0];
		    // Add the edge to the array
		    graph_links.push({source: sourceNode,  target: targetNode, label: e.label, link: e.predicate, type: e.type});
		  });
		  
			 
		  links = svg.selectAll(".link")
		      .data(graph_links)
		      .enter().append("line")
		        .attr("marker-end", "url(#end)")
				.attr("class", function(d){ return "link link-"+d.type;})
				.style("stroke-width", 1);

		  
		  //NODE ========================================================= NODE =============================================================
		  var nodex = svg.selectAll(".node")
		        .data(force.nodes, function(d) { return d.id;});
		  nodex.exit().remove();
						
		  var node = svg.selectAll(".node")
		      .data(graph.nodes)
			  .enter().append("circle")
			      .attr("class", function(d) { return "node node-"+d.subject; })
			      //.attr("r", 10)
			      .attr("r", function(d) { 
			      	d.r = 10;
			      	if(d.rank=="Genus"){
			      		d.r = 15;
			      	}
			      	return d.r;
			      })
			      //.attr("y", function(d) { if(isNaN(d.y)) return d.py=d.y=0; else return d.y; })
				  //.on("mouseover", function(d) { d.over=true;})
				  //.on("mousedown", function() { d3.select(this).attr("class","node nodeover");})
				  //.on("mouseup", function() { d3.select(this).attr("class","node");})
				  //.on("mouseout", function(d) { d3.select(this).attr("class","node");})
				  .on("dblclick", function(d) { 
				  						explore(d.id); 
				  				 	})
				  .on("mousemove", function(d){
				  						if(xSourceId==d.id){xSourceId = "";}
				  						if(xTargetId==d.id){xTargetId = "";}

				  						if(d.x<leftLock){
				  							xSourceId = d.id;	
				  						}else if(d.x>rightLock){
				  							xTargetId = d.id;	
				  						}


				  						lCir.attr("cx", 2*leftLock - d.x -10)
				  							.attr("cy", d.y);

				  						rCir.attr("cx", 2*rightLock - d.x -10)
				  							.attr("cy", d.y);


				  						if(xSourceId!="" && xSourceId==d.id){lCir.attr("cx", -100).attr("cy", -100);}
				  						if(xTargetId!="" && xTargetId==d.id){rCir.attr("cx", -100).attr("cy", -100);}

				  					})
				  .on("mouseup", function(d){
				  						lCir.attr("cx", -100).attr("cy", -100);
				  						rCir.attr("cx", -100).attr("cy", -100);
				  					})
				  .on("mouseout", function(d){
				  						lCir.attr("cx", -100).attr("cy", -100);
				  						rCir.attr("cx", -100).attr("cy", -100);
				  					})
				  //.on("click", function(d) { selectedNode = d; force.drag();})
			      //.style("fill", function(d) { return color(d.type); })
			      .call(force.drag)
			  ;

			//node.append("title")
          	//	.text(function(d) { return d.id; });


		 


		   //LINK TEXT ========================================================== LINK TEXT ============================================================
		   var link_text= svg.selectAll(".link-text");
		   link_text.remove();
		  
		   link_text = svg.selectAll(".link-text")
		                .data(graph_links)
		                .enter()
		                .append("text")
							.attr("class", "link-text")
							.text( function (d) { return d.label; })
						;

		   link_text.append("title")
          		.text(function(d) { return d.link; });

			
 		//NODE TEXT ========================================================== NODE TEXT ============================================================
		   var node_text= svg.selectAll(".node-text");
		   node_text.remove();
		  
		   node_text = svg.selectAll(".node-text")
		                .data(graph.nodes)
		                .enter()
		                .append("text")
							.attr("class", "node-text")
							.text( function (d) { return d.label; })
						;

			node_text.append("title")
          		.text(function(d) { return d.id; });
			
		  // TICK ========================================================== force.TICK() ============================================================
		  //selectedNode = node[0];
		  
				
			force.on("tick", function() {
			  

				node.attr("cx", function(d) { 
							if(d.id==xSourceId) d.x=leftLock;
							if(d.id==xTargetId) d.x=rightLock;
							return d.x; 
						})
					.attr("cy", function(d) { return d.y; })
					.attr("class", function(d) { 
							nclass = "node"
							switch(d.status){
								case "selected": 	nclass = "node selected-node node-"+d.subject; break;
								case "visited": 	nclass = "node visited-node node-"+d.subject; break;
								default: 			nclass = "node node-"+d.subject;
							}

							if(d.id==xSourceId) nclass="node source-node node-"+d.subject;
							if(d.id==xTargetId) nclass="node target-node node-"+d.subject;

							return nclass;
						})
					;

					
				links.attr("x1", 	function(d)	{ return d.source.x; })
			        .attr("y1", 	function(d) { return d.source.y; })
			        .attr("x2", 	function(d) { return d.target.x; })
			        .attr("y2", 	function(d) { return d.target.y; })
			       ;
						

				node_text
					.attr("x", function(d) { return d.x + d.r + 4 ; })
					.attr("y", function(d) { return d.y + 3; })
					//.text( function (d) { return d.label; })
					;
					

				link_text
					.attr("x", function(d) { return (d.source.x + d.target.x)/2  ; })
					.attr("y", function(d) { return (d.source.y + d.target.y)/2 ; })
					;


				if(xSourceId!=""){
					var xSourceNode = filterNodeById(xSourceId)[0];
					xlCir.attr("cx", xSourceNode.x).attr("cy", xSourceNode.y);
					xSourceNode.status = "visited";
					//lCir.attr("cx", -100).attr("cy", -100);
				}else{
					xlCir.attr("cx", -100).attr("cy", -100);
				}

				if(xTargetId!=""){
					var xTargetNode = filterNodeById(xTargetId)[0];
					xrCir.attr("cx", xTargetNode.x).attr("cy", xTargetNode.y);
					xTargetNode.status = "visited";
					//rCir.attr("cx", -100).attr("cy", -100);
				}else{
					xrCir.attr("cx", -100).attr("cy", -100);
				}



				if( xSourceId!="" && xTargetId!="" ) {$("#findpath").show(); }else { $("#findpath").hide()};
					
			});
		  
		    force
		      .nodes(graph.nodes)
		      .links(graph_links)
			  .charge(-500)
			  .linkDistance(function(d){ 
					if(d.source.status!=null && d.target.status!=null){ return link_lenx; }
					return link_len; 
				  })
		      .start();
		}

		function filterNodeById(uri){
			if(graph && graph.nodes){
				return graph.nodes.filter(function(n) { return n.id === uri; });
			}else{
				return array();
			}	
		}


		function filterLinkById(id){
			if(graph && graph.links){
				return graph.links.filter(function(n) { return n.id === id; });
			}else{
				return array();
			}	
		}


		function explore(uri){
			//window.location= "exploration.php?q=" + uri;

			var json = "./data/info.php?q=" + uri + getPropOptions();

			d3.json(json, function(error, newGraph) {
				addGraph(newGraph);
				errObj = newGraph;
				update();

				highlight(uri);
			});
		}


		function addNode(uri){
			//window.location= "exploration.php?q=" + uri;

			var json = "./data/one.php?q=" + uri + getPropOptions();

			d3.json(json, function(error, newGraph) {
				addGraph(newGraph);
				errObj = newGraph;
				update();

				highlight(uri);
			});
		}

		function findPaths(){
			//window.location= "exploration.php?q=" + uri;
			$('#imgLoader2').show();
			var i =1;
			var max = 4;
			for(i=1 ; i<max; i++){
				var json = "./data/paths.php?q1=" + xSourceId + "&q2=" + xTargetId + "&hops=" + i + getPropOptions();

				d3.json(json, function(error, newGraph) {
					addGraph(newGraph);
					errObj = newGraph;
					update();
				});
			}

			//Last
			var json = "./data/paths.php?q1=" + xSourceId + "&q2=" + xTargetId + "&hops=" + max + getPropOptions();

			d3.json(json, function(error, newGraph) {
				$('#imgLoader2').hide();
				addGraph(newGraph);
				errObj = newGraph;
				update();
			});
		}

		function highlight(uri){
				if(selectedNode!=null) { selectedNode.status ="visited"; } 
			  	selectedNode = filterNodeById(uri)[0];
			  	selectedNode.status = "selected" ;
		}

		function addGraph(grp){

			//errObj = grp;

			//init graph
			if(graph==null){
				initGraph();
			}

			for(var i in grp.nodes){
				var gnode = grp.nodes[i];
				if (filterNodeById(gnode.id).length==0) {
					graph.nodes.push(gnode);
				}
			}

			for(var i in grp.links){
				var glink = grp.links[i];
				if (filterLinkById(glink.id).length==0) {
					graph.links.push(glink) ;
				}	
			}

		}

		function initGraph(){
			graph = {nodes:[],links:[]};
			xSourceId="";
			xTargetId="";
			selectedNode=null;
			xSourceNode=null;
			xTargetNode=null;
		}

		function clearLinks(){
			var nodes = graph.nodes;
			initGraph();
			for(var i in nodes){
				node=nodes[i];
				var n = {	id:node.id, 
							label:node.label, 
							rank:node.rank,
							subject:node.subject,
							x:node.x,
							y:node.y,
						};
				graph.nodes.push(n);
			}
		}

		function addNodeX(label,uri,subject, rank){
			var grp = {nodes:[{id:uri, label:label, subject:subject, rank:rank}]};
			addGraph(grp);
			update();
			highlight(uri);
		}

		function getPropOptions(){
			var option = "";
			if($('#chkInteraction').prop('checked')){
				option += "&interaction=yes" ;
			}

			if($('#chkTaxonomy').prop('checked')){
				option += "&taxonomy=yes" ;
			}

			return option;
		}

	</script>


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
  		<h1><?php echo L("Exploration"); ?></h1>
	</div>

	<section id="info">

		<div class="container-fluid">
		 	<div class="row">
				<div class="col-md-3">
					<div class="panel panel-default">
						<div class="panel-heading"><?php echo L("Search"); ?></div>
					  	<div class="panel-body fixheight1">
					    	<div class="panel panel-default">
					    		<div class="panel-body">
								  <div class="input-group">
								      <input id="nameSearch1" type="text" class="form-control" placeholder="Search for...">
								      <span class="input-group-btn">
								        <button class="btn btn-default" onclick="searchName()" type="button">&nbsp;<span class="glyphicon glyphicon-search" aria-hidden="true"></span>&nbsp;</button>
								      </span>
								    </div><!-- /input-group -->
					    		</div>
					    	</div>
					    	<img id="imgLoader1" src="images/loader.gif" />
					    	<div class="outer">
				    			<table class="table table-hover" style="width:100%">
				    				<tbody id="searchResult1">
				    				</tbody>
				    			</table>
			    			</div>
					  	</div>
					</div>
				</div>
				<div class="col-md-9">
					<div class="panel panel-default">
						<div class="panel-heading"><?php echo L("Exploration"); ?></div>
					  	<div class="panel-body fixheight1" align="center">
					  		<div align="left">
					  			<?php echo L("Relations"); ?> : 
					  			<span> <input id="chkInteraction" type="checkbox" checked /> <?php echo L("Interactions"); ?> </span> ,
					  			<span> <input id="chkTaxonomy" type="checkbox"/> <?php echo L("Taxonomy"); ?> </span>

					  			<div align="right" class="panel-symbol">
					  				<img src="images/fungi.jpg" alt="Fungi" height="15" width="15" class="img-circle" /> <?php echo L("Fungi"); ?>,&nbsp;&nbsp;
					  				<img src="images/plantae.jpg" alt="Plantae" height="15" width="15" class="img-circle" /> <?php echo L("Plant"); ?>,&nbsp;&nbsp;
					  				<img src="images/animalia.jpg" alt="Fungi" height="15" width="15" class="img-circle" /> <?php echo L("Animal"); ?>
					  			</div>
					  		</div>

					  		<div id="svg-body" align="center" >
						    	<script>
									var svg = d3.select("#svg-body").append("svg")
										.attr("width", "100%")
										.attr("height", height)
										;

									var uri = "<?php echo $qr; ?>" ;

									var json = "./data/info.php?q=" + uri + getPropOptions();
									//var json = "http://127.0.0.1/taxi/data/data100.json";

									d3.json(json, function(error, jgraph) {
										graph = jgraph;
										errObj = error;
										selectedNode = filterNodeById(uri)[0];
										selectedNode.status = "selected";
										update();
									});
									
								</script>
							</div>
							<div align="right">
								<div class="btn-group dropup">
								  <button type="button" onclick="initGraph();update();" class="btn btn-default"><?php echo L("Clear Screen"); ?></button>
								  <button type="button" class="btn btn-default dropdown-toggle" style="height:34px;" data-toggle="dropdown" aria-expanded="false">
								    <span class="caret"></span>
								    <span class="sr-only">Toggle Dropdown</span>							  </button>
								  <ul class="dropdown-menu" role="menu">
								    <li><a onclick="clearLinks();update();"  href="#"><?php echo L("Clear Links"); ?></a></li>
								  </ul>
								</div>
								<div id="findpath" class="find-path">
									<button  type="button" onclick="findPaths();update();" class="btn btn-info"> <?php echo L("Find Paths"); ?> </button>
									&nbsp;&nbsp;&nbsp;&nbsp;<img id="imgLoader2" src="images/loader.gif" />
								</div>
							</div>

					  	</div>
					</div>
			</div>
		</div>
	</section>
</div>
<?php
/*****************************************************************************/
/***************************** FOOTER ****************************************/
	include "common/footer.php" ; 
/*****************************************************************************/
?>
</body>
</html>