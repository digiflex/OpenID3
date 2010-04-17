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

require 'ID3/ID3.php';

$file = new ID3('c.mp3');
$info = $file->fileinfo();
$info = $info['fileinfo']['TAGS']['APIC'];

header('Content-type: '.$info['MIME']);
echo $info['DATA'];
?>