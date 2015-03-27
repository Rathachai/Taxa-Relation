<?php

/**
 * For terms of use see LICENSE.txt in this distribution.
 *  
 * This class represents RDF Statement consisting of Subject, Predicate and Object.
 * 
 * @package    System
 * @subpackage RDF
 * @author     Daniel Sevcik <simplerdf@webdevelopers.cz>
 * @version    $Revision: 2.0 $
 * @copyright  2004 Daniel Sevcik
 * @since      2004-10-13
 * @access     public
 */

class SimpleRdfTriple {
  /**
   * Contains list of RDF Resource IDs of owners.
   * @var array of int
   */
  private $owners=array();

  private $subject;
  private $predicate;
  private $object;

  /**
   * Set the whole Statement. Set RDF Subject, Predicate, Object at once.
   *
   * @access public
   * @param SimpleRdfResource $subject RDF Subject
   * @param SimpleRdfResource $predicate RDF Predicate
   * @param SimpleRdfNode $object RDF Object
   * @return void
   */
  public function set(SimpleRdfResource $subject, SimpleRdfResource $predicate, SimpleRdfNode $object) {
    $this->setSubject($subject);
    $this->setPredicate($predicate);
    $this->setObject($object);
  }
  
  /**
   * Returns the RDF Subject.
   *
   * @access public
   * @return SimpleRdfResource RDF Subject
   */
  public function getSubject() {
    return $this->subject;    
  }

  /**
   * Set the RDF Subject.
   *
   * @access public
   * @param SimpleRdfResource $subject RDF Subject.
   * @return SimpleRdfResource RDF Subject
   */
  public function setSubject(SimpleRdfResource $subject) {
    return $this->subject=$subject;
  }

  /**
   * Returns the RDF Predicate.
   *
   * @access public
   * @return SimpleRdfResource RDF Predicate
   */
  public function getPredicate() {
    return $this->predicate;    
  }

  /**
   * Set the RDF Predicate.
   *
   * @access public
   * @param SimpleRdfResource $predicate RDF Predicate.
   * @return SimpleRdfResource RDF Predicate
   */
  public function setPredicate(SimpleRdfResource $predicate) {
    return $this->predicate=$predicate;
  }

  /**
   * Returns the RDF Object.
   *
   * @access public
   * @return SimpleRdfNode RDF Object
   */
  public function getObject() {
    return $this->object;    
  }

  /**
   * Set the RDF Object.
   *
   * @access public
   * @param SimpleRdfNode $object RDF Object.
   * @return SimpleRdfNode RDF Object
   */
  public function setObject(SimpleRdfNode $object) {
    return $this->object=$object;
  }

  /**
   * Get string value of this Statement (N-Triple format).
   *
   * @access public
   * @return string
   */
  public function getValue() {
    return $this->getSubject()->__toString()."\t".$this->getPredicate()->__toString()."\t".$this->getObject()->__toString()." .";
  }

  /**
   * This is the PHP Magic function.
   *
   * @access private
   * @return string String representation of this object.
   */
  public function __toString() {
    // The __toString() method was not called autmaticly when object-to-string conversion was made... so explicit call...
    return $this->getValue();
  }

  /**
   * Mark the Triple as owned by givne SimpleRdf object of given ID. 
   *
   * @access private
   * @param int $ownerId of the owning SimpleRdf object.
   * @return void
   */
  public function addOwner($ownerId) {
    $this->owners[$ownerId]=true;
  }

  /**
   * Unmark the Triple as owned by givne SimpleRdf object of given ID. 
   *
   * @access private
   * @param int $ownerId of the owning SimpleRdf object.
   * @return void
   */
  public function removeOwner($ownerId) {
    unset($this->owners[$ownerId]);
  }

  /**
   * Is the SimpleRdf object of given ID among the owners of this Triple?
   *
   * @access private
   * @param int $ownerId of the owning SimpleRdf object.
   * @return bool TRUE if this Triple is owned by SimpleRdf object of given name otherwise FALSE
   */
  public function isOwner($ownerId) {
    return isset($this->owners[$ownerId]);
  }

  /**
   * Is the orphan Triple? Orphan Triple is the Triple that has no owners.
   *
   * @access private
   * @return bool TRUE if the Triple has no owners.
   */
  public function isOrphan() {
    return !count($this->owners);
  }

  public function __destruct() {
    // trigger_error('Destroying the triple '.$this->__toString());
  }
}

?>