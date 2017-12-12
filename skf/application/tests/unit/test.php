<?php
require_once 'PHPUnit/Framework.php';
 
require_once 'Framework/AllTests.php';
// ...
 
class AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('PHPUnit');
 
        $suite->addTest(Framework_AllTests::suite());
        // ...
 
        return $suite;
    }
}
?>
