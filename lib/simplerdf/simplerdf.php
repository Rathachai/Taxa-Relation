<?php
// Namespaces
define('NS_RDF', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
define('NS_RDFS', 'http://www.w3.org/2000/01/rdf-schema#');
define('NS_XSD', 'http://www.w3.org/2001/XMLSchema#');
defined('NS_DC') || define('NS_DC', 'http://purl.org/dc/elements/1.1/');

// Points to the SimpleRDF XSL filters...
defined('RDF_FILTERS') || define('RDF_FILTERS', dirname(__FILE__).'/simplerdf-filters');

// Special vars
define('RDF_FILTER_IN', 'in');
define('RDF_FILTER_OUT', 'out');
define('RDF_FILTER_BOTH', 'both');
define('RDF_RETURN_VALUE', 1);
define('RDF_RETURN_OBJECT', 2);
define('RDF_RETURN_SUBJECT', 4);
define('RDF_RETURN_PREDICATE', 8);

// Version of the Package
define('SIMPLERDF_VERSION', preg_replace('/^\$'.'Revision: ([0-9.]+) \$$/', '0.\1', '$Revision: 2.10 $'));

/**
 * Note: When using this attribute there are new statements created.
 * 
 * Statements in repository haystack:
 * NT1: < a > < b > < rdf:Seq >
 * NT2: < rdf:Seq > < rdf:li > < c >
 * 
 * Returned triple:
 * NT: < a > < b > < c >
 *
 * Note the triple '< a > < b > < c >' really does not exist in repository.
 * It is being returned insted of '< rdf:Seq > < rdf:li > < c >'!
 *
 * This behavior is experimental and may change in the future (I hope that will not change ;-)
 */
define('RDF_RESOLVE_CONTAINER', 16);

/**
 * For terms of use see LICENSE.txt in this distribution.
 * 
 * @package    System
 * @subpackage RDF
 * @author     Daniel Sevcik <simplerdf@webdevelopers.cz>
 * @version    $Revision: 2.10 $
 * @copyright  2004 Daniel Sevcik
 * @since      2004-10-13
 * @access     public
 * @todo when importing blank nodes (rdf:resource & rdf:nodeID) - suffix them with '_{resource ID}' to solve ambiguity. What if exported from more then one source?
 */
class SimpleRdf {
  /**
   * Contains all known statements represented by SimpleRdfTriple objects.
   * @var int Last assinged Rdf Resource ID.
   * @static
   */
  static private $lastRdfResourceId=0;

  /**
   * Contains all known resources
   * @todo Destruct the Resources when there are no statements containing them...
   * @var array of SimpleRdfResource objects
   * @static
   */
  static private $resources=array();

  /**
   * Contains all known statements represented by SimpleRdfTriple objects.
   * @var array of SimpleRdfTriple objects
   * @static
   */
  static private $statements=array();

  /**
   * Used for escaping characters in NT-Triples
   * @static
   * @var array
   */
  static public $ntEscTranslation=array('\n', '\r', '\t');

  /**
   * Used for escaping characters in NT-Triples
   * @static
   * @var array
   */
  static public $ntEscString=array("\n", "\r", "\t");

  /**
   * Every opened RDF has Resource ID
   * @var int Resource ID of the object.
   */
  private $rdfResourceId;

  /**
   * Contains DOMDocument object representing currently parsed RDF.
   * @var DOMDocument
   */
  private $dom;

  /**
   * Contains initialized DOMXPath object for currently parsed RDF.
   * @var DOMXPath
   */
  private $xpath;

  /**
   * While saving it contains the DOM document.
   * @var DOMDocument
   */
  private $currSaveDom;
  /**
   * While saving it contains list of N-Triples to save.
   * @var array
   */
  private $currSaveList;
  /**
   * Contains current nesting depth of XML elements in saving session.
   * @var int
   */
  private $currSaveDepth;
  /**
   * Contains maximum allowed XML element depth.
   * @var int
   * @access public
   */
  public $tidyMaxDepth=3;
  /**
   * How many attributes can the element have.
   * @var int
   * @access public
   */
  public $tidyMaxAttrs=2;
  /**
   * How long can the RDF Literal be to be serialized as XML attribute.
   * @var int
   * @access public
   */
  public $tidyAttrMaxLength=12;
  
  /**
   * Constructor.
   *
   * @access public
   * @return void
   */
  public function __construct() {
    $this->rdfResourceId=SimpleRdf::$lastRdfResourceId++;
  }

  /**
   * Returns the RDF Resource ID of this object.
   * Every SimpleRdf object has unique Resource ID.
   *
   * @access private
   * @return int Resource ID of this SimpleRdf object.
   */
  private function getRdfResourceId() {
    return 'RDF#'.$this->rdfResourceId;
  }

  /**
   * Load a N-Triples file.
   *
   * @access private
   * @param string $file File name.
   * @return int how many statements were recognized in the document.
   */
  private function loadNtFile($file) {
    return $this->loadNt(file_get_contents($file));
  }

  /**
   * Load string in the N-Triples format.
   *
   * @access private
   * @param string $string with statements.
   * @return int how many statements were recognized in the document.
   */
  public function loadNt($string) {
    $pResource="<([^>]+)>";
    $pNodeId="_:([^\s<\"]*)";
    $pLiteral="\"(.*)\"";
    $pType="\\^\\^$pResource";
    $pLang="@([a-zA-Z_.@0-9-]*)";    
    $pattern="/^\s* (?:$pResource|$pNodeId)  \s*  $pResource  \s*  (?:$pResource|$pNodeId|$pLiteral(?:$pLang)?(?:$pType)?) [\s.]* $/xm";

    $total=preg_match_all($pattern, $string, $matches, PREG_SET_ORDER);

    foreach($matches as $num => $match) {

      //echo "MATCHED[$num]:[$line\n"; print_r($matches); echo "]\n";
      
      $subject=$this->prepareNt(@$match[1], @$match[2]);
      $predicate=@$match[3];
      $object=$this->prepareNt(@$match[4], @$match[5], @$match[6], @$match[7]);
      
      $this->makeStatement($subject, $predicate, $object);

      if (@$match[9]) { // has type
        $this->makeStatement($subject, NS_RDF.'type', @$match[9]);	  
      }
    }

    return $total;
  }

  /**
   * Feed the SimpleRDF object with data.
   *
   * @access private
   * @param string $resource URI
   * @param string $id NodeID if any
   * @param string $value value if any
   * @param string $lang language if any
   * @return SimpleRdfNode
   */
  private function prepareNt($resource, $id=NULL, $value=NULL, $lang=NULL) {
    if ($resource || $id) {
      $o=new SimpleRdfResource;
      $resource && $o->setUri($resource);
      $id && $o->setNodeId($id);
    } else {
      $o=new SimpleRdfLiteral;
      $value=str_replace(SimpleRdf::$ntEscTranslation, SimpleRdf::$ntEscString, $value);
      $o->setValue($value);
      $lang && $o->setLang($lang);
    }
    return $o;
  }

  /**
   * Load/Read the RDF file.
   *
   * @access public
   * @param string $file Name of the file.
   * @param string $filters list of whitespace separated names of input filters (XSL) to be applied before parsing.
   * @return bool True on success otherwise False.
   */
  public function loadFile($file, $filters="") {
    if (!is_file($file) || !($xml=file_get_contents($file))) {
      trigger_error('Cannot load RDF file or file is empty: "'.$file.'"', E_USER_ERROR);
      return false;
    }
    return $this->loadXml($xml, $filters);
  }

  /**
   * Load/Read the RDF/XML string.
   *
   * @access public
   * @param string $xml RDF/XML string.
   * @param string $filters list of whitespace separated names of input filters (XSL) to be applied before parsing.
   * @return bool True on success otherwise False.
   */
  public function loadXml($xml, $filters="") {
    $dom=DOMDocument::loadXML($xml);
    if (!is_object($dom)) {
      trigger_error('Cannot load RDF/XML or document empty ('.strlen($xml).' bytes): '.$xml, E_USER_ERROR);
      return false;
    }

    $this->loadDom($dom, $filters);
  }

  /**
   * Load/Read the RDF/XML DOM representation.
   *
   * @access public
   * @param DOMDocument $dom RDF/XML string.
   * @param string $filters list of whitespace separated names of input filters (XSL) to be applied before parsing.
   * @return bool True on success otherwise False.
   */
  public function loadDom(DOMDocument $xmlDom, $filters="") {
    if ($this->dom) {
      trigger_error('You cannot merge new RDF/XML into existing one!', E_USER_ERROR);
      return false;
    }

    // Filters
    $this->dom=$this->applyFilters($xmlDom, RDF_FILTER_IN, $filters.' simplerdf');
    if (!$this->dom) return false;

    // Init properties
    $this->dom->formatOutput=true; // activate formatting
    $this->xpath=new DOMXPath($this->dom);
    // echo "<pre style='border:1px solid red; padding: 8px'>ORIGINAL:\n".htmlspecialchars($xmlDom->saveXML())."</pre>";
    // echo "<pre style='border:1px solid red; padding: 8px'>SimpleRDF XSL:\n".htmlspecialchars($this->dom->saveXML())."</pre>";
    
    // Get top level elements
    if (!is_object($list=$this->xpath->query("/*/*"))) {
      trigger_error('This is not valid RDF/XML.', E_USER_ERROR);
      return false;
    }

    // Cycle all the top level Subjects.
    $c=0;
    while($node=$list->item($c++)) {
      $this->parseStatement($node);
    }
    //echo "<pre>".htmlentities($this->saveNt())."</pre>";
    //echo "<pre>"; var_dump(SimpleRdf::$statements, SimpleRdf::$resources); echo "</pre>";    
    return true;
  }

  /**
   * This method applies all the XSL filters listed in $filters string and returns resulting DOM document.
   *
   * @access private
   * @param DOMDocument $xmlDom Source XML
   * @param string $type can contains the prefix (type) of the xsl filter. Use only constants RDF_FILTER_IN or RDF_FILTER_OUT!
   * @param string $filters whitespace separated list of filter names.
   * @return DOMDocument with all the stylesheets aplied or FALSE on error (mostly when filter not found).
   */
  private function applyFilters(DOMDocument $xmlDom, $type, $filters) {
    assert('is_dir(RDF_FILTERS) && is_readable(RDF_FILTERS) /* SimpleRDF folder containing filters is unreadble! */');

    // Process XSLT
    $proc = new xsltprocessor;

    foreach(split("[\t\n\r ,;]+", trim($filters, "\t\n\r ,;")) as $filter) {
      $file=RDF_FILTERS.'/'.$type.'.'.$filter.'.xsl';
      if (!is_readable($file) && !is_readable($file=RDF_FILTERS.'/'.RDF_FILTER_BOTH.'.'.$filter.'.xsl')) {
	trigger_error('Unreadable filter: '.RDF_FILTERS."/($type|both).$filter.xsl", E_USER_ERROR);
	return false;
      }
	  
      // Read SimpleRDF XSL
      $xslDom=new DOMDocument;
      $xslDom->substituteEntities=true; // Must be here otherwise &rdf; and &xml; won't expand...
      $xslDom->load($file);
      $proc->importStyleSheet($xslDom); // attach the xsl rules
      $xmlDom=$proc->transformToDoc($xmlDom); // actual transformation
    }

    return $xmlDom;
  }


  /**
   * Parses given node. Expects that given node represents Subject.
   * @param DOMNode $subjectNode Subject node.
   * @return bool if the Statement was found.
   * @access private
   */
  private function parseStatement(DOMNode $subjectNode) {
    $subject=false;
    // Has rdf:about
    $subject=new SimpleRdfResource;
    $subjectNode->getAttributeNS(NS_RDF, 'about') && $subject->setUri($subjectNode->getAttributeNS(NS_RDF, 'about'));
    $subjectNode->getAttributeNS(NS_RDF, 'nodeID') && $subject->setNodeId($subjectNode->getAttributeNS(NS_RDF, 'nodeID'));

    // @todo if (!$subject) then "blank node [2.10]" - should not happen when using simplerdf.xsl
    if (!$subject->getUri() && !$subject->getNodeId()) {
      trigger_error('SimpleRDF XSL is invalid... I do not know what to do with this node: '."\n".$this->dom->saveXML($subjectNode), E_USER_ERROR);
      return false;
    }

    // Find all Predicates
    if (!$list=$this->xpath->query('*', $subjectNode)) {
      trigger_error('No predictes foud: '."\n".$this->dom->saveXML($subjectNode), E_USER_WARNING);
      return false; // No predicates found.
    }

    // After simplerdf.xsl there will be only one subelement
    $predicateNode=$list->item(0);
    $predicate=$predicateNode->namespaceURI.$predicateNode->localName;

    // Find Object
    if ($predicateNode->hasAttributeNS(NS_RDF, 'resource')) { // Is Resource
      $object=$predicateNode->getAttributeNS(NS_RDF, 'resource');
    } elseif ($predicateNode->hasAttributeNS(NS_RDF, 'nodeID')) { // Is Resource
      $objectNodeId=$predicateNode->getAttributeNS(NS_RDF, 'nodeID');
      $object=SimpleRdf::getResourceByNodeId($objectNodeId, true);
    } else { // Is Literal: should be @rdf:parseType='Literal'
      $object=new SimpleRdfLiteral;
      $object->setLang($subjectNode->getAttribute('lang')); // @xml:lang
      // Save the content (may contain XML elements...)
      foreach($predicateNode->childNodes as $child) {
	$object->appendValue($this->dom->saveXML($child));
      }
    }

    // Now we have Subject, Predicate, Object - let's create Statement.
    return $this->makeStatement($subject, $predicate, $object);
  }

  /**
   * This method looks for the existing Resource object and returns it.
   * Optionaly it creates new Resource object.
   *
   * @access private
   * @static
   * @param string $uri URI of the Resource object.
   * @param bool $autoCreate Create new Resource object if not found.
   * @return SimpleRdfResource or False if not found and $autoCreate=false
   */
  static private function getResourceByUri($uri, $autoCreate=false) {
    $resource=false;
    
    foreach(SimpleRdf::$resources as $candidate) {
      if ($candidate->getUri() == $uri) {
	$resource=$candidate;
	break;
      }
    }

    if (!$resource && $autoCreate) {
      $resource=new SimpleRdfResource;
      $resource->setUri($uri);
      SimpleRdf::registerResource($resource);	
    }

    return $resource;
  }

  /**
   * This method looks for the existing Resource object and returns it.
   * Optionaly it creates new Resource object.
   *
   * @access private
   * @static
   * @param string $nodeId Find the object by given @rdf:nodeID attribute.
   * @param bool $autoCreate Create new Resource object if not found.
   * @return SimpleRdfResource or False if not found and $autoCreate=false
   */
  static private function getResourceByNodeId($nodeId, $autoCreate=false) {
    $resource=false;
    
    foreach(SimpleRdf::$resources as $candidate) {
      if ($candidate->getNodeId() == $nodeId) {
	$resource=$candidate;
	break;
      }
    }
    
    if (!$resource && $autoCreate) {
      $resource=new SimpleRdfResource;
      $resource->setNodeId($nodeId);
      SimpleRdf::registerResource($resource);	
    }

    return $resource;
  }

  /**
   * Checks if given Resource object has its equivalent already registered
   * with SimpleRDF. If yes return the already registered object instead
   * otherwise register this new Resource object with SimpleRDF.
   *
   * @access private
   * @static
   * @param SimpleRdfResource $resource object to find or register.
   * @return SimpleRdfResource object registered with SimpleRDF
   */
  static private function getResourceByResource(SimpleRdfResource $resource) {
    if ($resource->getUri()) {
      $knownResource=SimpleRdf::getResourceByUri($resource->getUri());      
      $knownResource && $resource->getNodeId() && $knownResource->setNodeId($resource->getNodeId()); // Update nodeID - can be old from older import...
    } elseif ($resource->getNodeId()) {
      $knownResource=SimpleRdf::getResourceByNodeId($resource->getNodeId());
    } else {
      trigger_error('Given resource '.$resource.' has neither URI nor nodeID specified!', E_USER_ERROR);
      return false;
    }

    if (!($knownResource instanceOf SimpleRdfResource)) { // Is not registered
      SimpleRdf::registerResource($resource);
      return $resource;
    } else { // Is registered - return already registered instead
      return $knownResource;
    }
  }

  /**
   * Register new RDF Resource object.
   *
   * @access private
   * @static
   * @param SimpleRdfResource $resource Object to register.
   * @return void
   */
  static private function registerResource(SimpleRdfResource $resource) {
    SimpleRdf::$resources[]=$resource;
  }

  /**
   * Converts mixed input to have SimpleRDF objects at the end.
   *
   * @access private
   * @static
   * @param mixed $subject SimpleRdfResource object or URI string representing the resource RDF Subject or FALSE for any
   * @param mixed $predicate SimpleRdfResource object or URI string representing the resource RDF Predicate or FALSE for any
   * @param mixed $object SimpleRdfResource object or SimpleRdfLiteral object or resource URI string representing the RDF Object or FALSE for any
   * @param bool $acceptAny set to TRUE if any of the objects can be FALSE
   * @return array($subject, $predicate, $object) or FALSE on error
   */
  static private function convertObjects($subject, $predicate, $object, $acceptAny) {
    
    // Take care of inputs
    if (is_string($subject)) {
      $subject=SimpleRdf::getResourceByUri($subject, true);
    } elseif ($subject instanceOf SimpleRdfResource) {
      $subject=SimpleRdf::getResourceByResource($subject);
    } elseif ($acceptAny && $subject !== FALSE) {
      trigger_error('RDF Subject is expected to be FALSE, string or SimpleRdfResource!', E_USER_ERROR);
      return false;
    }
    
    if (is_string($predicate)) {
      $predicate=SimpleRdf::getResourceByUri($predicate, true);
    } elseif ($predicate instanceOf SimpleRdfResource) {
      $predicate=SimpleRdf::getResourceByResource($predicate);
    } elseif ($acceptAny && $predicate !== FALSE) {
      trigger_error('RDF Predicate is expected to be FALSE, string or SimpleRdfResource!', E_USER_ERROR);
      return false;
    }

    if (is_string($object)) {
      $object=SimpleRdf::getResourceByUri($object, true);
    } elseif ($object instanceOf SimpleRdfResource) {
      $object=SimpleRdf::getResourceByResource($object);
    } elseif (!($object instanceOf SimpleRdfLiteral) && !($object===FALSE && $acceptAny)) {
      trigger_error('RDF Object is expected to be FALSE, string, SimpleRdfResource or SimpleRdfLiteral!', E_USER_ERROR);
      return false;
    }

    return array($subject, $predicate, $object);
  }  

  /**
   * Find the first matching statements.
   *
   * @access public
   * @static
   * @param mixed $subject SimpleRdfResource object or URI string representing the resource RDF Subject or FALSE for any
   * @param mixed $predicate SimpleRdfResource object or URI string representing the resource RDF Predicate or FALSE for any
   * @param mixed $object SimpleRdfResource object or SimpleRdfLiteral object or resource URI string representing the RDF Object or FALSE for any
   * @param int $attrs Miscellaneous attributes that control the search mechanism.
   * @return mixed or NULL if not found.
   */
  static public function findFirst($subject, $predicate, $object, $attrs=0) {
    $found=array_shift(SimpleRdf::find($subject, $predicate, $object, $attrs, 1));
    return $found;
  }

  /**
   * Find the first matching statements using RegExp patterns.
   *
   * @access public
   * @static
   * @see SimpleRdf::findFirst()
   * @see SimpleRdf::regExpFind()
   * @param mixed $subject 
   * @param mixed $predicate
   * @param mixed $object 
   * @param int $attrs
   * @param array $haystack limit search to the given array of triples. Leave false to use all known triples.
   * @return mixed or NULL if not found. 
   */
  static public function regExpFindFirst($subject, $predicate, $object, $attrs=0, $haystack=false) {
    $found=array_shift(SimpleRdf::regExpFind($subject, $predicate, $object, $attrs, 1, $haystack));
    return $found;
  }
  
  /**
   * Find the first matching statements using wildcard patterns.
   *
   * @access public
   * @static
   * @see SimpleRdf::findFirst()
   * @see SimpleRdf::wildcardFind()
   * @param mixed $subject 
   * @param mixed $predicate
   * @param mixed $object 
   * @param int $attrs 
   * @return mixed or NULL if not found.
   */
  static public function wildcardFindFirst($subject, $predicate, $object, $attrs=0) {
    $found=array_shift(SimpleRdf::wildcardFind($subject, $predicate, $object, $attrs, 1));
    return $found;
  }

  /**
   * Find statements.
   *
   * @access public
   * @static
   * @param mixed $subject SimpleRdfResource object or URI string representing the resource RDF Subject or FALSE for any. Accepts array of strings or SimpleRdfResources as well.
   * @param mixed $predicate SimpleRdfResource object or URI string representing the resource RDF Predicate or FALSE for any. Accepts array of strings or SimpleRdfResources as well.
   * @param mixed $object SimpleRdfResource object or SimpleRdfLiteral object or resource URI string representing the RDF Object or FALSE for any. Accepts array of strings or SimpleRdfResources as well.
   * @param int $attrs Miscellaneous attributes that control the search mechanism. Following constants can be used
   *			RDF_RETURN_VALUE - returns array of string URIs or strings form RDF Object Literals or N-Triple if no RDF_RETURN_OBJECT or RDF_RETURN_SUBJECT or RDF_RETURN_PREDICATE specified
   *			RDF_RETURN_OBJECT | RDF_RETURN_SUBJECT | RDF_RETURN_PREDICATE - return only given part instead of whole RDF Statement (default).
   *			RDF_RESOLVE_CONTAINER - do not return the RDF:Bag, RDF:Seq, RDF:Alt but directly their contained resources
   * @param int $limit Terminate find after the limit of find Statements was reached. 0 for no-limit.
   * @param array $haystack limit search to the given array of triples. Leave false to use all known triples.
   * @return mixed depends on the attributes: can be array of strings|array of SimpleRdfTriple objects|array of SimpleRdfNode objects|string|SimpleRdfNode.  Note: The haystack's keys are preserved.
   */
  static public function find($subject, $predicate, $object, $attrs=0, $limit=0, $haystack=false) {
    $subject=SimpleRdf::regExpFindPrepareInput($subject, 1);
    $predicate=SimpleRdf::regExpFindPrepareInput($predicate, 1);
    $object=SimpleRdf::regExpFindPrepareInput($object, 1);    

    // echo " [ regExpFind(".print_r($subject, 1).', '.print_r($predicate, 1).', '.print_r($object, 1).") ]\n";
    return SimpleRdf::regExpFind($subject, $predicate, $object, $attrs, $limit, $haystack);
  }

  /**
   * Find using the wilecard enabled patterns.
   * Wildcards are often used for file matching in OS. You can use the same syntax...
   * Example of wildcard pattern: *gr[ae]y??
   *
   * All string parameters are expected to be wildcard patterns.
   *
   * For the parameter and output description see SimpleRdf::find()
   *
   * Example: $rdf->wildcardFind('*://slashdot.org/', FALSE, '*chan?el');
   *
   * @see SimpleRdf::find()
   * @access public
   * @static
   * @param mixed $subject 
   * @param mixed $predicate 
   * @param mixed $object 
   * @param int $attrs 
   * @param int $limit
   * @param array $haystack limit search to the given array of triples. Leave false to use all known triples. 
   * @return mixed  Note: The haystack's keys are preserved.
   */
  static public function wildcardFind($subject, $predicate, $object, $attrs=0, $limit=0, $haystack=false) {
    $subject=SimpleRdf::regExpFindPrepareInput($subject, 2);
    $predicate=SimpleRdf::regExpFindPrepareInput($predicate, 2);
    $object=SimpleRdf::regExpFindPrepareInput($object, 2);    

    // die("wildcardFind($subject, $predicate, $object, $attrs=0, $limit=0);");
    return SimpleRdf::regExpFind($subject, $predicate, $object, $attrs, $limit, $haystack);
  }
  
  /**
   * Perl-compatible regular expression find. Same method as SimpleRdf::find() except the fact that all string
   * parameters are considered to be Perl RegExp patterns.
   *
   * For the parameter and output description see SimpleRdf::find()
   *
   * Example: $rdf->regExpFind('"//slashdot.org/$"', FALSE, '/channel/');
   *
   * @see SimpleRdf::find()
   * @access public
   * @static
   * @param mixed $subject 
   * @param mixed $predicate 
   * @param mixed $object 
   * @param int $attrs 
   * @param int $limit
   * @param array $haystack limit search to the given array of triples. Leave false to use all known triples.
   * @param int $_recursion Do not use! It's internal recursion loop counter!
   * @return mixed 
   * @todo Can the SimpleRdfLiteral be also RegExp? (using ATTR RDF_REGEXP_OBJECT?) Note: The haystack's keys are preserved.
   */
  static public function regExpFind($subject, $predicate, $object, $attrs=0, $limit=0, $haystack=false, $_recursion=0) {
    
    $subject=SimpleRdf::regExpFindPrepareInput($subject);
    $predicate=SimpleRdf::regExpFindPrepareInput($predicate);
    $object=SimpleRdf::regExpFindPrepareInput($object);    

    // If RDF_RESOLVE_CONTAINER then it must match objects of container type even if the object pattern does not match...
    if ($attrs & RDF_RESOLVE_CONTAINER) {
      $object[]=NS_RDF.'Bag';
      $object[]=NS_RDF.'Seq';    
      $object[]=NS_RDF.'Alt';
    }
    
    // Find
    $return=array();
    $haystack=(is_array($haystack) ? $haystack : SimpleRdf::$statements);
    foreach($haystack as $id=>$statement) {
      if ($limit && count($return) >= $limit) {
	break;
      }
      
      // @todo Use === operator
      if (
	  SimpleRdf::regExpFindMatch($subject, $statement->getSubject()->getValue()) &&
	  SimpleRdf::regExpFindMatch($predicate, $statement->getPredicate()->getValue()) &&
	  SimpleRdf::regExpFindMatch($object, $statement->getObject()->getValue())	  
	  ) {
	$return[$id]=$statement;
      }
    }
    // echo " [ found: ".count($return)." ]\n ";
    return SimpleRdf::findResolveAttributes($subject, $predicate, $object, $attrs, $limit, $return, $haystack, $_recursion);
  }

  /**
   * Compare multiple regexp patterns with value.
   *
   * @access private
   * @static
   * @param array $patterns of regexp patterns
   * @param string value
   * @return bool true if at least one pattern matches
   */
  static private function regExpFindMatch($patterns, $value) {
    foreach($patterns as $pattern) {
      assert('preg_match($pattern, $value)!==FALSE /* Invalid SimpleRDF pattern: '.str_replace('*/', '*[ESC]/', $pattern.', value: '.$value).' */');
      if (preg_match($pattern, $value)) {
	return true;
      }
    }

    return false;
  }

  /**
   * Convert parameters to arrays of regexp patterns
   *
   * @access private
   * @static
   * @param mixed $input parameter to conver to regexp
   * @param int $type consider the strings not being regexp patterns. 1: string is plain text, 2: string is wilcard pattern
   * @return array of regexp pattern(s)
   */
  static private function regExpFindPrepareInput($input, $type=0) {
    if (!is_array($input)) {
      $input=array($input);
    }

    foreach($input as $id => $value) {
      if ($type==1 && is_string($input[$id])) { // Plain text convert
	$input[$id]='/'.str_replace('/', '\/', preg_quote($input[$id])).'/';
      } elseif ($type==2 && is_string($input[$id])) { // Wildcard convert
	$input[$id]='/'.str_replace(array('\*', '\?', '\]', '\['), array('.*', '.', ']', '['), str_replace('/', '\/', preg_quote($value))).'/';
      } elseif ($input[$id] instanceOf SimpleRdfNode) {
	$input[$id]='/'.str_replace('/', '\/', preg_quote($input[$id]->getValue())).'/';
      } elseif ($input[$id] === FALSE) {
	$input[$id]='/.*/';
      }
    }
    // echo ' --- [ '.implode(' ] [ ', $input)." ]\n";
    return $input;
  }


  /**
   * Apply various actions on the find result set depending on given ATTRIBUTES.
   *
   * @access private
   * @static
   * @param mixed $subject
   * @param mixed $predicate
   * @param mixed $object
   * @param int $attrs
   * @param array $result result set containing the SimpleRdfTriples objects.
   * @param array $haystack limit search to the given array of triples. Leave false to use all known triples.
   * @param int $recursion counter
   * @return mixed desired result type based on ATTRIBUTES.  Note: The haystack's keys are preserved.
   */
  static private function findResolveAttributes($subject, $predicate, $object, $attrs, $limit, $result, $haystack, $recursion) {
    // Resolve containers Seq Bag Alt List
    if ($recursion < 16 && $attrs & RDF_RESOLVE_CONTAINER) {
      // Find all Containers and remove them from result set
      foreach($result as $id => $statement) {

	$checkObject=$statement->getObject();
	$type=false;
    	if ($checkObject instanceOf SimpleRdfResource && $type=$checkObject->isOfType(array(NS_RDF.'Seq', NS_RDF.'Bag', NS_RDF.'Alt'))) {
	  unset($result[$id]);
	}

	// Resolve Container
	if ($type && (!$limit || $limit > count($result))) {
	  $predicatePattern='"^'.preg_quote(NS_RDF).'(li|_\d+)$"';
	  $resolved=SimpleRdf::regExpFind($statement->getObject(), $predicatePattern, $object, RDF_RESOLVE_CONTAINER | RDF_RETURN_OBJECT, $limit - count($result), $haystack, $recursion+1);

	  // @todo different behaviour for Bag/Seq/Alt...!
	  switch ($type->getUri()) {
	  case NS_RDF.'Seq':
	  case NS_RDF.'Bag':
	    foreach($resolved as $resolvedId=>$resolvedObject) {
	      // Create new virtual statement that is not part of SimpleRdf...
	      $virtualStatement=new SimpleRdfTriple;
	      $virtualStatement->set($statement->getSubject(), $statement->getPredicate(), $resolvedObject);
	      $result[$resolvedId]=$virtualStatement;
	    }
	    break;
	  case NS_RDF.'Alt': // Chose one element randomly! You shoud not use RDF_RESOLVE_CONTAINER with rdf:Alt - build your own logic for it...
	    $randomId=array_rand($resolved);
	    $virtualStatement=new SimpleRdfTriple;
	    $virtualStatement->set($statement->getSubject(), $statement->getPredicate(), $resolved[$randomId]);
	    $result[$randomId]=$virtualStatement;
	    break;
	  }
	}
      }
    }

    // Do not return whole statement.
    if ($attrs & RDF_RETURN_SUBJECT) {
      foreach($result as $id => $statement) {
	$result[$id]=$statement->getSubject();
      }
    } elseif ($attrs & RDF_RETURN_PREDICATE) {
      foreach($result as $id => $statement) {
	$result[$id]=$statement->getPredicate();
      }
    } elseif ($attrs & RDF_RETURN_OBJECT) {
      foreach($result as $id => $statement) {
	$result[$id]=$statement->getObject();
      }
    }

    // Return string values only
    if ($attrs & RDF_RETURN_VALUE) {
      foreach($result as $id => $object) {
	$result[$id]=$object->getValue();
      }      
    }

    // If not returning statements then return only unique values
    if ($attrs & (RDF_RETURN_OBJECT | RDF_RETURN_SUBJECT | RDF_RETURN_PREDICATE | RDF_RETURN_VALUE)) {
      $result=array_unique($result);
    }
    
    // Return only 
    return $result;
  }

  /**
   * Register new RDF Statement.
   *
   * @access private
   * @param mixed $subject SimpleRdfResource object or URI string representing the resource RDF Subject
   * @param mixed $predicate SimpleRdfResource object or URI string representing the resource RDF Predicate
   * @param mixed $object SimpleRdfResource object or SimpleRdfLiteral object or resource URI string representing the RDF Object
   * @return SimpleRdfTriple or FALSE on error
   */
  public function makeStatement($subject, $predicate, $object) {
    list($subject, $predicate, $object)=$ok=$this->convertObjects($subject, $predicate, $object, FALSE);
    
    if (!$ok) {
      trigger_error('The RDF Statement is not complete: makeStatement('.$subject.', '.$predicate.', '.$object.')!', E_USER_ERROR);
      return false;
    }

    if ($existing=$this->findFirst($subject, $predicate, $object)) {
      $existing->addOwner($this->getRdfResourceId());
    } else {
      // Add new
      $statement=new SimpleRdfTriple;
      $statement->addOwner($this->getRdfResourceId());    
      $statement->set($subject, $predicate, $object);
      return SimpleRdf::$statements[]=&$statement;
    }
  }

  /**
   * Returns all Triples that are owned by this SimpleRdf object.
   *
   * @access private
   * @return array of SimpleRdfTriple objects owned by this SimpleRdf object
   */
  public function getStatementsAll() {
    $return=array();
    foreach(SimpleRdf::$statements as &$statement) {
      if ($statement->isOwner($this->getRdfResourceId())) {
	$return[]=&$statement;
      }
    }

    return $return;
  }

  /**
   * Returns/Save the RDF in N-Triples RDF graph.
   *
   * @access public
   * @param int
   * @return void
   */
  public function saveNt() {
    $return='';
    foreach($this->getStatementsAll() as $statement) {
      // The __toString() method was not called autmaticly when object-to-string conversion was made... so explicit call...      
      $return.=$statement->__toString()."\n";
    }

    return $return;
  }

  public function __destruct() {
    // trigger_error('Destructing the resource '.$this->getRdfResourceId());
   
    // It looks like the $statement object are destroyed just after leaving foreach().
    foreach(SimpleRdf::$statements as $id => $statement) {
      $statement->removeOwner($this->getRdfResourceId());
      if ($statement->isOrphan()) {
	unset(SimpleRdf::$statements[$id]);
      }
    }
  }

  /**
   * Splits URI into two parts. First part is the NS and the other part is the element name.
   *
   * @access private
   * @param string $uri URI to split
   * @return array (namespace, element_name, recommended_prefix) where element_name or namespace can be empty...
   */
  private function namespaceAnalyzer($uri) {
    static $lookup=array(
			 NS_RDF => 'rdf',
			 NS_DC => 'dc'
			 );
    /*
     * Name ::= (Letter | '_' | ':') (NameChar)*
     * NameChar ::= Letter | Digit | '.' | '-' | '_' | ':' |
     * @todo regexp is not accurate - missing combining chars and extenders: see XML spec
     * @todo make global NS asigning to avoid duplicite NS declarations
     */
    preg_match('/^(.*?)([a-zA-Z_][a-zA-Z0-9._-]*)$/', $uri, $parts);
    $ns=$parts[1];
    $name=$parts[2];
    $prefix=false;

    // Find nice NS prefix
    if (isset($lookup[$ns])) {
      $prefix=$lookup[$ns];
    } else {
      // |\.aero|\.biz|\.com|\.coop|\.edu|\.gov|\.info|\.int|\.mil|\.museum|\.name|\.net|\.org|\.pro|\.arpa
      $prefixVariants=preg_replace('/^[a-z]{3,}:\/\/(?:www\.|ns\.|rdf\.)?([a-z0-9.-]+?)(?:\.[a-z]{2,3})?([^a-z0-9.-].*)$/i', '\2 \1', $ns);
      $prefixVariants='ns '.preg_replace('/\.rdf|[^a-z][0-9]+|[a-z]+:\/\/|&[a-z]+;|[^a-z0-9]/i', ' ', $prefixVariants);
      $prefixVariants=array_reverse(split('[^a-z0-9]+', trim($prefixVariants)));
      $suffix='';
      // echo print_r($prefixVariants);
      do {
	foreach($prefixVariants as $variant) {
	  if (!in_array($variant.$suffix, $lookup)) {
	    $lookup[$ns]=$prefix=$variant.$suffix;
	    break;
	  }
	}
	$suffix=(int) $suffix + 1;
      } while(!$prefix);
    }
    // echo "\n"; print_r($lookup); echo "\n";
    return array($ns, $name, $prefix);
  }  
  
  /**
   * Save the RDF/XML.
   *
   * @access public
   * @return 
   */
  public function saveXml() {
    $this->currSaveList=$this->getStatementsAll();
    $this->currSaveDom=new DOMDocument;
    $this->currSaveDom->formatOutput=true; // activate formatting
    $this->currSaveDepth=0;    

    // Comment
    $comment=$this->currSaveDom->createComment(' Generated by SimpleRDF v'.SIMPLERDF_VERSION.' <http://simplerdf.sourceforge.net> ');
    $this->currSaveDom->appendChild($comment);
    
    // Root element
    $root=$this->currSaveDom->createElementNS(NS_RDF, 'rdf:RDF');
    $this->currSaveDom->appendChild($root);
    while(list($current, $triple)=each($this->currSaveList)) {
      $this->saveXmlSubject($root, $triple->getSubject());
    }

    return $this->currSaveDom->saveXML();
  }

  /**
   * 
   *
   * @access private
   * @param int
   * @param 
   * @param 
   * @return void
   */
  private function saveXmlSubject(&$parentNode, $rdfSubject) {
    // New RDF:Description element
    $subject=$this->currSaveDom->createElementNS(NS_RDF, 'rdf:Description');
    $this->currSaveDepth++;

    $parentNode->appendChild($subject);
    $subject->setAttributeNS(NS_RDF, 'rdf:about', $rdfSubject->getValue());

    // Find predicates
    $rdfPredicates=SimpleRdf::find($rdfSubject, false, false, RDF_RETURN_PREDICATE, false, $this->currSaveList);
    
    foreach($rdfPredicates as $rdfPredicate) {
      $this->saveXmlPredicate($subject, $rdfSubject, $rdfPredicate);
    }
    $this->currSaveDepth--;    
  }

  
  /**
   * 
   *
   * @access private
   * @param int
   * @param 
   * @param 
   * @return void
   */
  private function saveXmlPredicate(&$parentNode, $rdfSubject, $rdfPredicate) {
    list($ns, $name, $prefix)=$this->namespaceAnalyzer($rdfPredicate->getUri());

    // Find objects
    $rdfObjects=SimpleRdf::find($rdfSubject, $rdfPredicate, false, RDF_RETURN_OBJECT, false, $this->currSaveList);

    // Remove triples from the List
    foreach(array_keys($rdfObjects) as $key) {
      assert('isset($this->currSaveList[$key])');
      unset($this->currSaveList[$key]);
    }

    // Insert the type in the parent's node - abbreviated type
    if ($rdfPredicate->getUri() == NS_RDF.'type' and $parentNode->namespaceURI.$parentNode->localName == NS_RDF.'Description') {
      $rdfType=array_shift($rdfObjects);
      list($typeNs, $typeName, $prefix)=$this->namespaceAnalyzer($rdfType->getUri());
      $parentNode=$this->renameElement($parentNode, $typeName, $typeNs, $prefix);
    }

    // One text node -> insert it as parent's attribute
    // @todo if xml:lang is different then Literal...
    if (count($rdfObjects) == 1 and !$parentNode->hasAttributeNS($ns, $name) &&	$this->tidyMaxAttrs > $parentNode->attributes->length) {
      list($key, $rdfObject)=each($rdfObjects);
      
      // Check if the only object can be inserted as attribute
      if ($rdfObject instanceOf SimpleRdfLiteral && $this->tidyAttrMaxLength >= strlen($rdfObject->getValue())) {
	if (!$ns) {
	  $parentNode->setAttribute($name, $rdfObject->getValue());
	} else {
	  $parentNode->setAttributeNS($ns, $prefix.':'.$name, $rdfObject->getValue());	  
	}

	unset($rdfObjects[$key], $this->currSaveList[$key]); // There should be only ONE combination of this S-P-O triple and the *Find() preserves keys...
      }
    }

    // Make the predicates as child element (literals)
    foreach($rdfObjects as $key => $rdfObject) {
      if ($rdfObject instanceOf SimpleRdfLiteral) {
        // New predicate element
        $predicate=$this->createElementAppend($parentNode, $ns, $prefix, $name);	
        $predicate->setAttributeNS(NS_RDF, 'rdf:parseType', 'Literal');
        
        // Content / Text node
        $textNode=$this->currSaveDom->createTextNode($rdfObject->getValue());
        $predicate->appendChild($textNode);
        unset($rdfObjects[$key], $this->currSaveList[$key]);
      } elseif ($this->currSaveDepth >= $this->tidyMaxDepth || !SimpleRdf::find($rdfObject, false, false, false, 1, $this->currSaveList)) { // This Resource has no further info
        // New predicate element
        $predicate=$this->createElementAppend($parentNode, $ns, $prefix, $name);
        $predicate->setAttributeNS(NS_RDF, 'rdf:resource', $rdfObject->getUri());
	unset($rdfObjects[$key], $this->currSaveList[$key]);
      }
    }

    // Remaining triples shoud have only object of type resource
    // @todo nesting limit
    if (count($rdfObjects) && $this->currSaveDepth < $this->tidyMaxDepth) {
      // New predicate element
      $predicate=$this->createElementAppend($parentNode, $ns, $prefix, $name);
      // $predicate->setAttributeNS(NS_RDF, 'rdf:parseType', 'Resource');
      
      foreach($rdfObjects as $key => $rdfObject) {
        // Content / Text node
        unset($rdfObjects[$key], $this->currSaveList[$key]);
	$this->saveXmlSubject($predicate, $rdfObject);	
      }
    }
  }

  private function createElementAppend($parentNode, $ns, $prefix, $name) {
    // echo "\n [ creating Predicate: $prefix:$name <$ns>, parent; $parentNode ]\n".$this->currSaveDom->saveXML();
    if ($ns) {
      $newElement=$this->currSaveDom->createElementNS($ns, $prefix.':'.$name);
    } else {
      $newElement=$this->currSaveDom->createElement($name);
    }
    $parentNode->appendChild($newElement);
    return $newElement;
  }

  /**
   * Rename given XML element.
   *
   * @access private
   * @param DOMElement $node Element to be renamed.
   * @param string $name new name.
   * @return DOMElement new renamed element.
   */
  private function renameElement(&$node, $name, $ns=NULL, $prefix=NULL) {
    //create new node
    if ($ns) {
      $newNode=$node->ownerDocument->createElementNS($ns, ($prefix ? $prefix.':' : '').$name);
    } else {
      $newNode=$node->ownerDocument->createElement($name);
    }

    //copy attributes
    foreach($node->attributes as $attribute) {
      $newNode->setAttributeNode($attribute->cloneNode(true));
    }

    //copy child nodes
    foreach($node->childNodes as $childNode) {
      $newNode->appendChild($childNode->cloneNode(true));
    }

    //replace nodes
    $node->parentNode->replaceChild($newNode, $node);

    return $node=$newNode;
  }

  //RATHACHAI
  public function getStatements(){
    return SimpleRDF::$statements;
  }

  public function setStatements($new_triples){
    SimpleRDF::$statements = $new_triples;
  }
}
?>