<?php

/**
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright (c) Digiflex Development Team 2010
 * @version 1.0 
 * @author Johnny Mast <mastjohnny@gmail.com>
 * @author Paul Dragoonis <dragoonis@php.net>
 * @since Version 1.0
 */

if(!class_exists('ID3_Base')) {
	require_once(CLASSPATH . 'class.ID3_Base.php');
}

if(!interface_exists('ID3ParserInterface')) {
	require_once(INTERFACEPATH . 'ID3Parser.php');
}

class ID3v1 extends ID3_Base implements ID3ParserInterface
{
   /**
	* This content of the file to be parsed. 
	* @var array
	*/	
	private $parsedheader = null;
	
	/**
 	* This is the byte positions for ID3v1
 	* @var array
 	*/
	private $headerInfo = array(
		'TAG'     => array(0, 2),
		'Title'   => array(3, 32),
		'Artist'  => array(33, 62),
		'Album'   => array(63, 92),
		'Year'    => array(93, 96),
		'Comment' => array(97, 125),
		'Genre'   => array(125, 126)	
	 );
	
	/**
	 * Constructor
	 * @param string $data Contains the data to parse.
	 */
	public function __construct($data)
	{
		try
		{			
			// Set the data in the parent
			$this->setFileData($data);
			// If we have found a ID3v1 match
			if (($pos = strpos($data, parent::ID3v1)) !== FALSE) {
				$this->headerpos = $pos;
			} else {
				throw new Exception('Unable to parse file, No ID3v1 tag found');
			}
			$this->parse();
			
		} catch (Exception $e)  {
			print 'Error: '.$e->getMessage();
		}
	}

	/**
	 * Magic set function to set the meta data for our file.
	 * @param $key The key to set
	 * @param $value The value to set for our key
	 * @return void
	 */
	function __set($key, $value) {
		if ($this->tagVersion != null && is_array($this->parserHeader) && isset($this->parsedheader[$key])) {
			$this->parsedheader[$key] = $value;
		}
	}
	
	/**
    * Return the parsed information.
    *
    * @return array
    */
	function info() {
		return $this->parsedheader;
	}
	
	/**
	* Save the newly created media header.
	*
	* @var array
	* @return BOOLEAN
	*/	
	public function save() {
		throw new Exception('TODO');
	}
	
	/**
	 * This is the parse function for ID3v1. The header information is in the last 128 bytes
	 */
	public function parse() {
	
		if ($this->headerpos !== FALSE) {
			$header = substr($this->getFileData(), $this->headerpos, 127);
			$info   = array();
			
			foreach(array_keys($this->headerInfo) as $key) {
				$info[$key] = '';
			}

			foreach($this->headerInfo as $key => $positions) {
				$length     = ($positions[1] -  $positions[0]) +1;
				$info[$key] = trim ( substr($header, $positions[0], $length),  "\x00\x30");
				$info[$key] = trim($info[$key]);
				if($key === 'Genre') {
					$info[$key] = ord($info[$key]);
				}
			}
			$this->parsedheader = $info;
		}
	}
	
}

