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
	
	private $fileinfo = array(
		'TAG'     => array(0, 2), 
		'VERSION' => array(3, 4),
		'FLAGS'   => array(5, 5),
		'SIZE'    => array(7, 10),
	);
	
	/**
	 * Constructor
	 * @param string $data Contains the data to parse.
	 */
	public function __construct($data)
	{
		try
		{	
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
	
	private function parseTextHeader($header = "") {
		
		$id    = substr($header, 0, 4);
		$s     = substr($header, 4, 9); /* Size raw */
		$f     = ord(  substr($header, 9, 10)  ); /* Flogs raw */
		
		
		$size  = '';
		$flags = '';
		
		for($i = 0; $i < 4; $i++) { 
			if (ord($s[$i]) == 0) continue;
			$size .= (int)ord($s[$i]);
		}

		for($i = 0; $i < 4; $i++) { 
			if (ord($f[$i]) == 0) continue;
			$flags .= (int)ord($f[$i]);
		}
		
		$text  = trim( substr($header, 10, $size ) );
		
		return array(
			'ID'    => $id,
			'SIZE'  => $size,
			'FLAGS' => $flags,
			'TEXT'  => $text,
			'TOTALSIZE' => $size + 10
		);
	}
	
	private function parseComments($header="")
    {
		
		$id     = substr($header, 0, 4);
		$l      = substr($header, 4, 9) ; 
	    $flags  = substr($header, 8, 2);
		$enc    = substr($header, 10, 1);
		$lang   = substr($header, 11, 3);
		
		$length = '';
		for($i = 0; $i < 4; $i++) { 
			if (ord($l[$i]) == 0) continue;
			$length .= (int)ord($l[$i]);
		}
		
		$length -= 4; /* Why the -4 ? what am i missing ? */
		
		//	$short  = trim ( substr($header, 14, $length) ) ;
		$long   = trim ( substr($header, 14,  $length ) );
		
	//	$this->debug('Lang: '.trim($lang));
	//	$this->debug('Short: '.$short);
	//	$this->debug('Description: '.$desc);
		return array(
			'ID'    => $id,
			'SIZE'  => $length,
			'FLAGS' => ord( trim($flags)),
			'ENCODING' =>  ord( trim($enc)),
			'LANGUAGE' => $lang,
			'COMMENT'  => $long,
			'TOTALSIZE' => 14 + $length
			);
	}
		
	private function parseAttachmentHeader($header="") {
		
		$id    = substr($header, 0, 4);
		$s     = substr($header, 4, 7); /* Size raw */
		$f     = ord(  substr($header, 8,10)  ); /* Flogs raw */
		
		
		$size  = '';
		$flags = '';
		
		for($i = 0; $i < 4; $i++) { 
			if (ord($s[$i]) == 0) continue;
			$size .= (int)ord($s[$i]);
		}

		for($i = 0; $i < 4; $i++) { 
			if (ord($f[$i]) == 0) continue;
			$flags .= (int)ord($f[$i]);
		}
		
		$mime     = trim( substr($header, 10) );
		$iNullPos = strpos($mime, 0x00); /* Find the \x00 to terminate the mime type */
		$mime     = substr($mime, 0, $iNullPos);
		
		$size += 7000; /* HACK ! */
		$type = sprintf('%x', ord ( substr($header, 10 + $iNullPos + 2, 1) ) );
		$img  = substr($header, 10 + $iNullPos +1 +3, $size);
	//	$img  = trim($img);
		return array(
			'ID'    => $id,
			'SIZE'  => $size,
			'FLAGS' => ord( trim($flags)),
			'MIME' =>  $mime,
			'PICTURETYPE' => $type,
			'DATA'  => $img,
			'TOTALSIZE' => 10 + $iNullPos +  1 +  $size
		);
	}	
	
	private function hasPrefix($text, $char)
	{
		return ($text[0] == $char);
	}
	
	/**
	 * This is the parse function for ID3v1. The header information is in the last 128 bytes
	 */
	public function parse() {
	
		if ($this->headerpos !== FALSE) {
			$header = substr($this->getFileData(), $this->headerpos, 10);
			$info   = array();
			//echo sprintf('header => %s (size = %d)', $header,mb_strlen($header));
			
			foreach(array_keys($this->fileinfo) as $key) {
				$info[$key] = '';
			}
			
			$bitpos = 1;
			foreach($this->fileinfo as $key => $positions) {
				$length     = ($positions[1] -  $positions[0]) +1;
				$info[$key] = trim ( substr($header, $positions[0], $length),  "\x00\x30");
				$info[$key] = trim($info[$key]);
				
				for($i = 0; $i < $length; $i++) {
					$bit = @$info[$key][$i];
					$bit = ord($bit);
				//	$this->debug( sprintf('>%x< (%c) bitpos %d', $bit, $bit, $bitpos) );
					$bitpos ++;
				}
				
				if ($key == 'VERSION') {
//					$this->debug( 'Exact version is ID3v2.'.ord($info[$key]) );
					$info[$key] = sprintf('2.%d', ord($info[$key]));
				}
				
				
				if ($key == 'SIZE') { 
					$info[$key] = sprintf('%d', ord($info[$key]));
				}
				//	$this->debug( 'END OF SET<hr />' );
					
			    
			}
			/*
			** Extract the media information from the mp3 file
		
			$mediainfo = substr($this->getFileData(), 10);
			$i = 0;
			while($mediainfo) {
				
				if ($this->hasPrefix($mediainfo, 'T')) {
					$tag = $this->parseTextHeader($mediainfo);
					$this->parsedheader['TAGS'][$tag['ID']] = $tag;
					$mediainfo = substr($mediainfo, $tag['TOTALSIZE']);
					
			    }
			
				if ($this->hasPrefix($mediainfo,'C')) {
					$tag = $this->parseComments($mediainfo);
					$this->parsedheader['TAGS'][$tag['ID']] = $tag;
					$mediainfo = substr($mediainfo, $tag['TOTALSIZE']);
				}
			
			   elseif ($this->hasPrefix($mediainfo, 'A'))
			    {
					$tag = $this->parseAttachmentHeader($mediainfo);
					$this->parsedheader['TAGS'][$tag['ID']] = $tag;
					$mediainfo = substr($mediainfo, $tag['TOTALSIZE']);
				  	break;
			    } 
			
				if ($i == 1500) break;
					$i++;
			}
				*/
			$identifiers = $this->tags[self::ID3v2][$info['VERSION']];
			$mediainfo   = substr($this->getFileData(), 10);
			
			$i = 0;
			while($mediainfo) { /* TODO: Should be fixed without while */
			foreach($identifiers as $ident) {
				if (substr($mediainfo, 0 , strlen($ident)) == $ident) {
					if ($this->hasPrefix($mediainfo, 'T')) {
						$frame = $this->parseTextHeader($mediainfo);
						//print_r($frame);
						
						$this->parsedheader['TAGS'][$frame['ID']] = $frame;
						$mediainfo = substr($mediainfo, $frame['TOTALSIZE']);						
					}
					
					
					if ($this->hasPrefix($mediainfo, 'C')) {
						$frame = $this->parseComments($mediainfo);

						$this->parsedheader['TAGS'][$frame['ID']] = $frame;
						$mediainfo = substr($mediainfo, $frame['TOTALSIZE']);						
					}

					if ($this->hasPrefix($mediainfo, 'A')) {
						$frame = $this->parseAttachmentHeader($mediainfo);
					
						$this->parsedheader['TAGS'][$frame['ID']] = $frame;
						$mediainfo = substr($mediainfo, $frame['TOTALSIZE']);						
					}
					
				}
			}
			
			if ($i == 1500) break;
			 $i++;
		}
		
			
			$this->parsedheader['HEADER'] = $info;
		
		}
	}
	
}

