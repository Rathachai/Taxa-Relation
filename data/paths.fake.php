<?php
header('Content-Type: application/json');
include('../config.php');
include('../lib/sparql/sparql.php');

$qr1 = $_GET["q1"];
$qr2 = $_GET["q2"];

$nr = array(rand(100,999),rand(100,999),rand(100,999),rand(100,999),rand(100,999),rand(100,999));

$data= array(
	"nodes"=>array(
				array("id"=> "$qr1"  , "name"=> "source"),
				array("id"=> "$qr2"  , "name"=> "target"),
				array("id"=> "http://ex.org/mid_$nr[0]"  , "name"=> "Mid $nr[0]"),
				array("id"=> "http://ex.org/mid_$nr[1]"  , "name"=> "Mid $nr[1]"),
				array("id"=> "http://ex.org/mid_$nr[2]"  , "name"=> "Mid $nr[2]"),
				array("id"=> "http://ex.org/mid_$nr[3]"  , "name"=> "Mid $nr[3]"),
				array("id"=> "http://ex.org/mid_$nr[4]"  , "name"=> "Mid $nr[4]"),
				array("id"=> "http://ex.org/mid_$nr[5]"  , "name"=> "Mid $nr[5]")
			),

	"links"=>array(
				array("source"=> "$qr1"  , "target"=> "http://ex.org/mid_$nr[0]" , "predicate"=> "http://ex.org/pred1" , "label"=> "link"),
				array("source"=> "http://ex.org/mid_$nr[0]"  , "target"=> "http://ex.org/mid_$nr[1]" , "predicate"=> "http://ex.org/pred1" , "label"=> "link"),
				array("source"=> "http://ex.org/mid_$nr[1]"  , "target"=> "http://ex.org/mid_$nr[2]" , "predicate"=> "http://ex.org/pred1" , "label"=> "link"),
				array("source"=> "http://ex.org/mid_$nr[2]"  , "target"=> "$qr2" , "predicate"=> "http://ex.org/pred1" , "label"=> "link"),
				array("source"=> "$qr1"  , "target"=> "http://ex.org/mid_$nr[3]" , "predicate"=> "http://ex.org/pred1" , "label"=> "link"),
				array("source"=> "http://ex.org/mid_$nr[3]"  , "target"=> "http://ex.org/mid_$nr[4]" , "predicate"=> "http://ex.org/pred1" , "label"=> "link"),
				array("source"=> "http://ex.org/mid_$nr[4]"  , "target"=> "http://ex.org/mid_$nr[5]" , "predicate"=> "http://ex.org/pred1" , "label"=> "link"),
				array("source"=> "http://ex.org/mid_$nr[5]"  , "target"=> "$qr2" , "predicate"=> "http://ex.org/pred1" , "label"=> "link")
			)
) ;


$json = json_encode($data);
echo $json;

?>