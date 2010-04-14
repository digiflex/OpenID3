<?php

/**
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright (c) Digiflex Development Team 2010
 * @version 1.0 
 * @author Johnny Mast <mastjohnny@gmail.com>
 * @author Paul Dragoonis <dragoonis@php.net>
 * @since Version 1.0
 */

define('ID3_CLASS_VERSION', 1.0);

abstract class ID3_Base
{
	const ID3v1   = "TAG";
 	const ID3v2   = "ID3";
	const ID3v24  = "IDI";
	const ID3vUnk = "Unknown";
	
	/**
	 * The mapping to convert the actual ID3 version to a human-readable format.
	 * @see $this->fileinfo()
	 * @var array
	 */
	protected $versionDesc = array(
		self::ID3v1       => 'ID3 version 1.0',
		self::ID3v2       => 'ID3 version 2.0',
		self::ID3v24      => 'ID3 version 2.4',
	);
	
	/**
	 * Contains the ID3 version tag
	 * @var null|string
	 */
	protected $tagVersion = null;
	
	/**
	 * Instance of the mp3 parser.  
	 * @var instance
	 */	
	private $instance;

   /**
	* The filename of the file to parse.
	* @var null|string
	*/	
	protected $file = null;	
	
   /**
	* This content of the file to be parsed. 
	* @var integer
	*/	
	protected $headerpos = -1;	
	
	/**
	 * The current files data
	 * @var null|string
	 */
	private $data = null;
	
	/**
	 * Constructor
	 * @param string $file The file path.
	 */
	public function __construct($file = "") {
	}
	
	public function __set($key, $value) {
		if ($this->instance != null) {
			$this->instance->$key = $value;
		}
	}
	
	
	public function save() {
		if ($this->instance != null)
		  $this->instance->save();
	}
	
	protected function fileinfo() {
		return array(
			  'description' => $this->versionDesc[$this->tagVersion],
			  'fileinfo'    => $this->instance->info()
			);
	}
	
	/**
	 * Set the current file data
	 * @param string $data
	 * @return void
	 */
	protected function setFileData($data) {
		$this->data = $data;
	}
	
	protected function getFileData() {
		return $this->data;
	}
	
	
	/**
	 * Debug formatted output message
	 * @param $msg
     */
	public function debug($msg) {
	  print sprintf('<pre>%s: %s</pre>', date(DATE_RFC822), $msg);
	}
	
	/**
	 * Get the current class version
	 */
	public function version() 
	{
		return ID3_CLASS_VERSION;
	}
}

