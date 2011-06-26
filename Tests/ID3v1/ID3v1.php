<?php

/**
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright (c) Digiflex Development Team 2010
 * @version 1.0 
 * @author Johnny Mast <mastjohnny@gmail.com>
 * @since Version 1.0
 */

 require 'class.ID3V1TestSuiteAbstract.php';
 
 class ID3v1TestSuite extends ID3V1TestSuiteAbstract {
 	
	protected function setUp() {
		parent::setUp();
	}
	
	function testLala() {
		$this->assertTrue(true);
	}
	
 }
