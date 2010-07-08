<?php

/**
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright (c) Digiflex Development Team 2010
 * @version 1.0 
 * @author Johnny Mast <mastjohnny@gmail.com>
 * @author Paul Dragoonis <dragoonis@php.net>
 * @since Version 1.0
 */

class ID3
{
	
	/**
	 * The mapping to convert the actual ID3 version to a human-readable format.
	 * @see $this->fileinfo()
	 * @var array
	 */
	private $versionDesc = array(
		ID3Data::ID3v1       => 'ID3 version 1.0',
		ID3Data::ID3v2       => 'ID3 version 2.0',
		ID3Data::ID3v24      => 'ID3 version 2.4',
		ID3Data::ID3vUnk     => 'Unknown'
	);
	
	/**
	 * This is the current file being processed. 
	 * @var null|string
	 */
	private $file = null;

	/**
	 * Contains the ID3 version tag
	 * @var null|string
	 */
	private $tagVersion = null;
	
	/**
	 * Instance of the mp3 parser.  
	 * @var instance
	 */	
	private $instance;
	
	/**
	 * Constructor
	 * @param string $file The file path.
	 */
	public function __construct($file="")
	{	
		
		// Detect if this is an absolute path or not. If it's not we make it one!
		if(strpos($file, DIRECTORY_SEPARATOR) === false) {
			$file = realpath($file);
		}
		
		// Check if the file exists and we can read from it.
		if(is_readable($file) === false) {
			throw new Exception('Unable to read file. Make sure it exists and has the correct permissions');
		}
		
		// Set the current processed filename.
		$this->file = $file;
		
		// Set the current processed file's data
		$data = file_get_contents($file, FILE_BINARY);	
			
		// Lets identify the version and boot up that specific class
		if (($pos = strpos($data, ID3Data::ID3v2)) !== FALSE) {
			$this->tagVersion = ID3Data::ID3v2;			
			$this->instance   = new ID3v2($data, new ID3Data($this->tagVersion));
			
		} else
		if (($pos = strpos($data, ID3Data::ID3v1)) !== FALSE) {			
			$this->tagVersion = ID3Data::ID3v1;
			$this->instance   = new ID3v1($data, new ID3Data($this->tagVersion));
			
			$this->instance->debug('Found ID3v1 tag');
		}
		
		// No ID3 version identified, no further processing needed.
		if ($this->instance === null) {
			throw new Exception('Error, No parser found for this file.');
		}
	}
	
	/**
	 * Magic set function to set the meta data for our file
	 * @param $key The key to set
	 * @param $value The value to set for our key
	 * @return void
	 */
	function __set($key, $value) {
		if ($this->instance !== null) {
			$this->instance->$key = $value;
		}
	}
	
	/**
	 * Save the file information
	 */
	public function save() {
		if ($this->instance !== null) {
			$this->instance->save();	
		}
	}
	
	/**
	 * Retreive the file information.
	 * @return array
	 */
	public function fileinfo() {
		return array(
			  'description' => $this->versionDesc[$this->tagVersion],
			  'fileinfo'    => $this->instance->info()
			);
	}
	
	/**
	 * Get the current class version
	 */
	public function version() {
		return ID3_CLASS_VERSION;
	}
}

