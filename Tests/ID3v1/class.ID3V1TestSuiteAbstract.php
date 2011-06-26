<?php

/**
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright (c) Digiflex Development Team 2010
 * @version 1.0 
 * @author Johnny Mast <mastjohnny@gmail.com>
 * @since Version 1.0
 */

 abstract class ID3V1TestSuiteAbstract extends PHPUnit_Framework_TestCase {
 	
	protected function setUp() {
		parent::setUp();
		
		$this->generatexml();
	}
	
	private function generatexml() {
		$rawData = file_get_contents("Data/generation.log");
		$dataArray = explode("\n\nTest case", $rawData);
		
		$cases = array();
		
		foreach($dataArray as $index =>$item) {
			//$dataArray[$index] = 'Test case'.$item;

			/*
			 * Get filename.
			 */
			$file_pos_start = strpos($item, 'Generated test file "') +strlen('Generated test file "');
			$file_pos_end  = strpos($item, '"', $file_pos_start);
			$file = substr($item, $file_pos_start, $file_pos_end - $file_pos_start);
			
			$comment_pos_start = $file_pos_end;
			$comment_pos_end   = strpos($item, "\nTag structure");
			
			$desc = substr($item, $comment_pos_start+1, $comment_pos_end-$comment_pos_start);
			$desc = trim($desc);
			
			$tags_pos_start = strpos($item, "Tag structure\n") + strlen("Tag structure\n");
			
			$tags = substr($item, $tags_pos_start);
			
			$tmptags = explode("\n", $tags);
			$tags = array();
			foreach($tmptags as $t) {
				$t = explode(" : ", $t);
				for($i = 0; $i < count($t); $i+=2) {
					$key = $t[$i];
					$val = $t[$i+1];
					
					$key = trim($key);
					$val = trim($val);
					
					$key = str_replace(array('"', "'", "\n"), '', $key);
					$val = str_replace(array('"', "'", "\n"), '', $val);
					
					if (strlen($key) > 0)
					$tags[$key] = $val;
				}		
			}
			
			if ($index > 1) {
				echo '@@'." ".$index."\n";
				if (substr($dataArray[$index], 0, 1) != " ") continue;
				
				//break;
				//if (substr($dataArray[$index], 0, strlen(" ". $index)) !=  " ".$index) continue;
				$cases[] = array(
					'testid' => (int)$index-1,
					'title'  => "Test case ".($index-1),
					'file'   => $file,
					'desc'   => $desc,
					'tags'   => $tags
				);
			}

		}

		ob_start();
		foreach($cases as $case) {
		?>
		<test id="<?php print $case['testid']; ?>">
			<id><?php print $case['testid']; ?></id>
			<title><![CDATA[<?php print $case['title']; ?>]]></title>
			<file><![CDATA[<?php print $case['file']; ?>]]></file>
			<desc><![CDATA[<?php print $case['desc']; ?>]]></desc>
			<?php foreach($case['tags'] as $name =>  $val): ?>
				<<?php print $name; ?>><![CDATA[<?php print $val; ?>]]></<?php print $name; ?>>
			<?php endforeach; ?>
		</test>
		
		
		<?php
		}
		$data = ob_get_contents();
		ob_end_clean();
		
		$data = trim($data);
		
		$xml = sprintf('
			%sxml version="1.0" encoding="UTF-8"?>
			<testcases>
				%s
			</testcases>
		', '<?', $data);
		$xml = trim($xml);
		print '<pre>';
	//	print_r($dataArray);
		print '</pre>';
		
		print '<pre>';
		print_r($cases);
		print '</pre>';
		file_put_contents('Data/tests.xml', $xml);
	}
 }

 