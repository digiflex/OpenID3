<?php
/**
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright (c) Digiflex Development Team 2010
 * @version 1.0 
 * @author Johnny Mast <mastjohnny@gmail.com>
 * @author Paul Dragoonis <dragoonis@php.net>
 * @since Version 1.0
 */
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require 'ID3/ID3.php';

$file = new ID3('c.mp3');
//$file->Title = 'Custom title';
//$file->Genre = 5;
//$file->Album = "Johnny and Paul rock.";
//$file->save();
?>
<hr />
File information
<pre>
<?php print_r($file->fileinfo());

//$file->title

?>
</pre>