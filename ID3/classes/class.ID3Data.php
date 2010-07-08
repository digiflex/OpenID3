<?php
/**
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright (c) Digiflex Development Team 2010
 * @version 1.0 
 * @author Johnny Mast <mastjohnny@gmail.com>
 * @author Paul Dragoonis <dragoonis@php.net>
 * @since Version 1.0
 */

if(!interface_exists('ID3DataSet')) {
	require_once(INTERFACEPATH . 'ID3DataSet.php');
}

class ID3Data implements ID3DataSet {
	
	const ID3v1   = "TAG";
 	const ID3v2   = "ID3";
	const ID3v24  = "IDI";
	const ID3vUnk = "Unknown";
	
	
	
	function __construct() {
	
	}
	
	public function getParameters() {
		
	} 
}
?>