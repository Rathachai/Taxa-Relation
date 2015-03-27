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
class SimpleRdfLiteral extends SimpleRdfNode {
  /**
   * Contains the literal value.
   * @var string
   */
  private $value='';

  /**
   * Language
   * @var string
   */
  private $lang=false;

  /**
   * Set the value of the RDF Object (of literal type).
   *
   * @access private
   * @param string $value Value of the RDF Object
   * @return string value of the RDF Object
   */
  public function getValue() {
    return $this->value;
  }

  /**
   * Set the value of the RDF Object (of literal type).
   *
   * @access private
   * @param string $value Value of the RDF Object
   * @return string value of the RDF Object
   */
  public function setValue($value) {
    return $this->value=$value;
  }
  /**
   * Append the value at the end of the existing value.
   *
   * @access private
   * @param string $value Value of the RDF Object to append
   * @return string value of the RDF Object
   */
  public function appendValue($value) {
    $this->value.=$value;
    return $this->value;
  }
  /**
   * Set the lang of the RDF Object (of literal type).
   *
   * @access private
   * @param string $lang Lang of the RDF Object
   * @return string lang of the RDF Object
   */
  public function getLang() {
    return $this->lang;
  }

  /**
   * Set the lang of the RDF Object (of literal type).
   *
   * @access private
   * @param string $lang Lang of the RDF Object
   * @return string lang of the RDF Object
   */
  public function setLang($lang) {
    return $this->lang=$lang;
  }

  /**
   * This is the PHP Magic function.
   *
   * @access private
   * @return string String representation of this object.
   */
  public function __toString() {
    return '"'.$value=str_replace(SimpleRdf::$ntEscString, SimpleRdf::$ntEscTranslation, $this->getValue()).'"'.($this->getLang() ? '@'.$this->getLang() : '');
  }
}

?>