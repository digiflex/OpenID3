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
	
	private $tags =  array(
		self::ID3v2 => array(
			'2.3' => array(
				/**
				 * These are the frame identifiers for the 2.3 specification
				 * of the ID3 documentation.
				 * @author Johnny Mast
				 * @link http://www.id3.org/id3v2.3.0
				 */
			   'TALB','TBPM','TCOM','TCON', 'TCOP', 'TDAT',	'TDLY',	'TENC',
			   'TEXT','TFLT','TIME','TIT1', 'TIT2', 'TIT3',	'TKEY',	'TLAN',
			   'TLEN','TMED','TOAL','TOFN',	'TOLY', 'TOPE',	'TORY',	'TOWN',
			   'TPE1','TPE2','TPE3','TPE4',	'TPOS',	'TPUB',	'TRCK', 'TRDA',
			   'TRSN','TRSO','TSIZ','TSRC', 'TSSE',	'TYER', 'TDRC',
			
	
			),
		   '2.4' => array(
				/**
				 * These are the frame identifiers for the 2.4 specification
				 * of the ID3 documentation.
				 * @author Johnny Mast
				 * @link http://www.id3.org/id3v2.4.0-structure
				 */
		    	'TALB', 'TBPM', 'TCOM', 'TCON', 'TCOP', 'TDAT',	'TDLY',	'TENC',
				'TEXT', 'TFLT',	'TIME', 'TIT1', 'TIT2', 'TIT3',	'TKEY',	'TLAN',
				'TLEN',	'TMED',	'TOAL', 'TOFN',	'TOLY', 'TOPE',	'TORY',	'TOWN',
				'TPE1',	'TPE2',	'TPE3',	'TPE4',	'TPOS',	'TPUB',	'TRCK', 'TRDA',
				'TRSN',	'TRSO',	'TSIZ',	'TSRC', 'TSSE',	'TYER', 'TDRC',
				
				'APIC', 'COMM'
		 ),
		),	
	);
	
	private $activeSet = null;
	
	function __construct($set = self::ID3v2) {
		$this->activeSet = $set;
	}
	
	public function getTags() {
		return $this->tags;
	}
	
	public function getParameters() {
		return $this->tags[$this->activeSet];
	} 
}
?>