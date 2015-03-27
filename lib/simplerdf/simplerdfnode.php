<?php

/**
 * For terms of use see LICENSE.txt in this distribution.
 *  
 * @package    System
 * @subpackage RDF
 * @author     Daniel Sevcik <simplerdf@webdevelopers.cz>
 * @version    $Revision: 2.0 $
 * @copyright  2004 Daniel Sevcik
 * @since      2004-10-13
 * @access     public
 */
abstract class SimpleRdfNode {

  /**
   * This method should return URI or Literal value. (no nodeID!)
   *
   * @access public
   * @return string
   */
  abstract function getValue();

}

?>