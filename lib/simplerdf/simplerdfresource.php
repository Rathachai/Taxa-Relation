<?php

/**
 * For terms of use see LICENSE.txt in this distribution.
 *  
 * @package    System
 * @subpackage RDF
 * @author     Daniel Sevcik <simplerdf@webdevelopers.cz>
 * @version    $Revision: 2.1 $
 * @copyright  2004 Daniel Sevcik
 * @since      2004-10-13
 * @access     public
 */
class SimpleRdfResource extends SimpleRdfNode {
  /**
   * Contains the URI of the resource (@rdf:about).
   * @var string
   */
  private $uri;
  
  /**
   * Contains the nodeID of the resource (@rdf:nodeID).
   * @var string
   */
  private $nodeId;
  
  /**
   * Set the URI of this resource.
   *
   * @access public
   * @param string $uri URI of the resource (@rdf:about)
   * @return bool TRUE on success.
   */
  public function setUri($uri) {
    if (!strlen($uri)) {
      trigger_error('The URI cannot be empty string!', E_USER_ERROR);
      return false;
    }
    // http://www.w3.org/TR/rdf-mt/
    $uriNew=ereg_replace(
		 array('^rdf:', '^rdfs:', '^xsd:'),
		 array(NS_RDF, NS_RDFS, NS_XSD),
		 $uri
		 );
    return $this->uri=$uriNew;
  }

  /**
   * Get the URI of this resource (@rdf:about)
   *
   * @access public
   * @return string URI of the resource (@rdf:about)
   */
  public function getUri() {
    return $this->uri;
  }

  /**
   * Alias to getUri()
   *
   * @access public
   * @return string
   */
  public function getValue() {
    return $this->getUri();
  }

  /**
   * Set the nodeID of this resource.
   *
   * @access public
   * @param string $nodeId nodeID of the resource (@rdf:nodeID)
   * @return bool TRUE on success.
   */
  public function setNodeId($nodeId) {
    if (!strlen($nodeId)) {
      trigger_error('The nodeID cannot be empty string!', E_USER_ERROR);
      return false;
    }

    $this->nodeId=$nodeId;
 
    if (!$this->getUri()) { // make @rdf:about='_:{nodeID}'
      $this->setUri('_:'.$nodeId);
    }
    
    return $this->nodeId;
  }

  /**
   * Get the nodeID of this resource (@rdf:nodeID)
   *
   * @access public
   * @return string nodeID of the resource (@rdf:nodeID)
   */
  public function getNodeId() {
    return $this->nodeId;
  }

  /**
   * Is current resource of given type? If the argument is array then return TRUE if the resource is
   * type at least of one of given types.
   *
   * Examples:
   * $object->isOfType(NS_RDF.'Bag');
   * $object->isOfType(array(NS_RDF.'Seq', NS_RDF.'Bag', NS_RDF.'Alt', NS_RDF.'List'));
   *
   * @access public
   * @param mixed $type string rdf:type URI or SimpleRdfResource object representing type, use array of mixed for multiple search.
   * @return SimpleRdfResource representing the first matching style or NULL if is not of specified types...
   */
  public function isOfType($types) {
    return SimpleRdf::findFirst($this, NS_RDF.'type', $types, RDF_RETURN_OBJECT);
  }

  /**
   * This is the PHP Magic function.
   *
   * @access private
   * @return string String representation of this object.
   */
  public function __toString() {
    return $this->getUri() && substr_compare($this->getUri(), '_:', 0, 2) ? '<'.$this->getUri().'>' : '_:'.$this->getNodeId();
  }
  
}

?>