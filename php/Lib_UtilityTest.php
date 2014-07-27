<?php
//require_once 'PHPUnit/Framework.php';
require_once 'Lib_Utility.php';
 
class Lib_UtilityTest extends PHPUnit_Framework_TestCase
{
	public function testMbStrlen()
	{
		require_once "class/logging.php";
		$logger = new Logging(basename(__FILE__), '/var/logs/', 7);
		$util = new Lib_Utility;
		$string = "０１２３４５６７８９";
		$this->assertEquals(2, $util->mbStrlen($string,0,2,'UTF-8'));
	}
}