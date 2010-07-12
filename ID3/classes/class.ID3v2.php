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

class ID3v2 extends ID3_Base implements ID3ParserInterface
{
	const ID3v2   = "ID3";
	
	private $tags = array(
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
   /**
	* This content of the file to be parsed. 
	* @var array
	*/	
	private $parsedheader = null;
	

	
	private $fileinfo = array(
		'TAG'     => array(0, 2), 
		'VERSION' => array(3, 4),
		'FLAGS'   => array(5, 5),
		'SIZE'    => array(6, 10),
	);
	
	
	private $dataSet = null;
	
	/**
	 * Constructor
	 * @param string $data Contains the data to parse.
	 */
	public function __construct($data, $dataSet)
	{
		try
		{	
			$this->dataSet = $dataSet;
			
			$this->parsedheader['HEADER'] = array();
			$this->parsedheader['TAGS']	= array();	
			
			// Set the data in the parent
			$this->setFileData($data);
			
			// If we have found a ID3v1 match
			if (($pos = strpos($data, parent::ID3v2)) !== FALSE) {
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
	
	private function parseComment($content="") {
		$encoding = ord($content[0]);
		$language = substr($content, 1, 3);
		$description = substr($content, 4);
		 
		return array(
			'encoding' => $encoding,
			'language' => $language,
			'description'=> $description
		);
	}
	
	/**
	 * This is the parse function for ID3v1. The header information is in the last 128 bytes
	 */
	public function parse() {
	
		if ($this->headerpos !== FALSE) {
			$header = substr($this->getFileData(), $this->headerpos, 10);
			$info   = array();
			
			foreach(array_keys($this->fileinfo) as $key) {
				$info[$key] = '';
			}
			
			
			/*
			** ID3v2/file identifier   "ID3" 
			** ID3v2 version           $03 00
			** ID3v2 flags             %abc00000
			** ID3v2 size              4 * %0xxxxxxx
			*/
			
			$bitpos = 1;
			foreach($this->fileinfo as $key => $positions) {
				$length     = ($positions[1] -  $positions[0]) +1;
				$info[$key] = trim ( substr($header, $positions[0], $length),  "\x00\x30");
				$info[$key] = trim($info[$key]);
				
				for($i = 0; $i < $length; $i++) {
					$bit = @$info[$key][$i];
					$bit = ord($bit);
					$bitpos ++;
				}
				
				if ($key == 'VERSION') {
					$info[$key] = sprintf('2.%d', ord($info[$key]));
				}
				
				
				if ($key == 'SIZE') {
					$a = '';
					for($i= 0; $i < strlen($info[$key]); $i++)
					  $a .= ord($info[$key][$i]);
					
					$info[$key] = (int)$a;
					$info[$key] = sprintf('%d', ord($info[$key]));
				}
				
				if ($key == 'FLAGS') {
					$a = '';
					for($i= 0; $i < strlen($info[$key]); $i++)
					  $a .= ord($info[$key][$i]);
					
					$info[$key] = (int)$a;
					$info[$key] = sprintf('%d', ord($info[$key]));
				}
			}
			
			$identifiers = $this->tags[self::ID3v2][$info['VERSION']];
			$mediainfo   = substr($this->getFileData(), 10);
						
			$i = 0;
			while(strlen($mediainfo) > 0) { 
				$identifiers = array_values($identifiers);
				
				/* 
				** Tag frame
				** Frame ID       $xx xx xx xx (four characters) 
				** Size           $xx xx xx xx
				** Flags          $xx xx
				*/
				$frameID  = substr($mediainfo, 0, 4);
				$Size     = substr($mediainfo, 4, 4);
				$Flags    = substr($mediainfo, 8, 1);
					
				for($i = 0; $i < 4; $i++) $Size[$i]  = ord($Size[$i]);
				for($i = 0; $i < 1; $i++) $Flags[$i] = ord($Flags[$i]);
					
				$Size = (int)$Size;
				
				
			//	$Flags = (int)$Flags;
			//	if ($frameID == "COMM") $Size += 10;
					
				$StepSize = 10 + (int)$Size;
				$Value    = substr($mediainfo, 10, (int)$Size);
				
				if ($frameID == 'COMM') $Value = $this->parseComment( substr($mediainfo, 10));
				
				if (in_array($frameID, $identifiers)) {
					
					$this->parsedheader['TAGS'][$frameID] = array(
						'TAG'   => $frameID,
						'SIZE'  => $Size,
						'FLAGS' => $Flags,
						'DATA'  => $Value
					);
				
				}
				
				$mediainfo = substr($mediainfo, $StepSize);
			
				if ($i == count($identifiers)) break;
			 	$i++;
			}
		
			
			$this->parsedheader['HEADER'] = $info;
		
		}
	}
	
}


