<?php

//****************************************************************************************************
//  PROJECT             :   Visualization of Taxonomic Interactions
//  Developer           :   Rathachai CHAWUTHAI     (National Institute of Informatics,Japan)
//  Supervisor          :   Hideaki TAKEDA          (National Institute of Informatics,Japan)
//  Product Mananger    :   Tsuyoshi HOSOYA         (National Museum of Nature and Science, Japan)
//  Created             :   2015
//****************************************************************************************************

$lang = $GLOBAL["lang"];

?>

<nav class="navbar navbar-fixed-top navbar-inverse">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#"><?php echo L("Project Name"); ?></a>
    </div>
    <div id="navbar" class="collapse navbar-collapse">
      <ul class="nav navbar-nav">
        <?php
        	$nav_now = trim(basename($_SERVER['PHP_SELF']));
        	foreach ($NAVIGATIONS as $nav_name => $nav_url){
        		$nav_active = strcmp($nav_now, $nav_url)==0?"class='active'":"" ;
        		echo "<li $nav_active ><a href='$nav_url' >$nav_name</a></li>";
        	}
        ?>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"> 
              Language (<?php echo strtoupper($lang);  ?>)
              <span class="caret"></span>
          </a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="<?php echo $nav_now."?lang=en"  ?>">English (EN)</a></li>
            <li><a href="<?php echo $nav_now."?lang=jp"  ?>">日本語 (JP)</a></li>
          </ul>
        </li>
      </ul>    </div><!-- /.nav-collapse -->
  </div><!-- /.container -->
</nav><!-- /.navbar -->

<br/><br/>