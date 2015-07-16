<?php

ini_set('include_path', '/var/www/html/prod/drupal');
require_once '/var/www/html/prod/drupal/sites/all/modules/civicrm/civicrm.config.php';
require_once 'CRM/Core/Config.php';
CRM_Core_Config::singleton();

/**
 * Generated by PHPUnit_SkeletonGenerator on 2015-07-15 at 19:08:58.
 */
class CRM_Phonenumbervalidator_InvalidNumberRetrieverTest extends PHPUnit_Framework_TestCase {


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    public function testCreation(){
      $invalidNumberRetriever = new CRM_Phonenumbervalidator_InvalidNumberRetriever(array(), array(), 1, 1);
      $invalidNumberRetriever->getErrorDetails();
      $this->assertTrue(true);
    }
    
    /**
     * @covers InvalidNumberRetriever::getInvalidPhoneNumbers
     * @todo   Implement testGetInvalidPhoneNumbers().
     */
    public function testGetInvalidPhoneNumbers() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers InvalidNumberRetriever::getInvalidPhoneNumbersCount
     * @todo   Implement testGetInvalidPhoneNumbersCount().
     */
    public function testGetInvalidPhoneNumbersCount() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers InvalidNumberRetriever::buildFromStatementMyqlString
     * @todo   Implement testBuildFromStatementMyqlString().
     */
    public function testBuildFromStatementMyqlString() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers InvalidNumberRetriever::buildReplacementMysqlString
     * @todo   Implement testBuildReplacementMysqlString().
     */
    public function testBuildReplacementMysqlString() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers InvalidNumberRetriever::buildWhereStatementMysqlString
     * @todo   Implement testBuildWhereStatementMysqlString().
     */
    public function testBuildWhereStatementMysqlString() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers InvalidNumberRetriever::getErrorDetails
     * @todo   Implement testGetErrorDetails().
     */
    public function testGetErrorDetails() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    function testBuildReplacementMysqlString () {
    // Test hyphens and brackets.
    $selectedAllowCharactersArray = array('hyphens', 'brackets');
    $expectedOutput = "REPLACE(REPLACE(REPLACE(phone, '-', ''), '(', ''), ')', '')";
    $output = CRM_Phonenumbervalidator_Utils::buildReplacementMysqlString($selectedAllowCharactersArray);
    $this->assertEquals($expectedOutput, $output, "Found " . print_r($output, TRUE));
    
    // Test no ignore characts.
    $selectedIgnoreCharactersArray = array();
    $expectedOutput = "phone";
    $output = CRM_Phonenumbervalidator_Utils::buildReplacementMysqlString($selectedIgnoreCharactersArray);
    $this->assertEquals($expectedOutput, $output, "Found " . print_r($output, TRUE));
  }
  
  function testBuildFromStatementMyqlString () {
    $selectedRegexRuleIds = array('Britain_0', 'Britain_1', 'Britain_2', 'Britain_3');
    $selectedAllowCharacterRules = array('hyphens', 'brackets');
    
    $expectedOutput = "FROM (SELECT id, phone, phone_ext, phone_type_id, contact_id FROM civicrm_phone WHERE "
            . "(REPLACE(REPLACE(REPLACE(phone, '-', ''), '(', ''), ')', '') NOT REGEXP '^0[^7][0-9]{9}$') AND "
            . "(REPLACE(REPLACE(REPLACE(phone, '-', ''), '(', ''), ')', '') NOT REGEXP '^07[0-9]{9}$') AND "
            . "(REPLACE(REPLACE(REPLACE(phone, '-', ''), '(', ''), ')', '') NOT REGEXP '^0044[^7][0-9]{9}$') AND "
            . "(REPLACE(REPLACE(REPLACE(phone, '-', ''), '(', ''), ')', '') NOT REGEXP '^00447[0-9]{9}$')) "
            . "AS phone JOIN civicrm_contact AS contact ON phone.contact_id = contact.id ";
    $output = CRM_Phonenumbervalidator_Utils::buildFromStatementMyqlString($selectedRegexRuleIds, $selectedAllowCharacterRules);
    
    $this->assertEquals($expectedOutput, $output, "Found " . print_r($output, TRUE));
  }
  
  function testBuildWhereStatementMyqlString () {
    $testData = array(
      array(
        'contactTypeId' => '1', 
        'phoneTypeId' => '1', 
        'expectedStatementOutput' => "WHERE 1 AND contact_type LIKE '%%1%' AND phone_type_id = '%2' ",
        'expectedParamsOutput' => array(1 => array(0 => 'Individual', 1 => 'String', 2 => 2), 2 => array(0 => 1, 1 => 'Int')),
      ),
      array(
        'contactTypeId' => '', 
        'phoneTypeId' => '1', 
        'expectedOutputStatement' => "WHERE 1 AND phone_type_id = '%2' ",
        'expectedParamsOutput' => array(2 => array(0 => 1, 1 => 'Int')),
      ),
      array(
        'contactTypeId' => '1', 
        'phoneTypeId' => '', 
        'expectedOutputStatement' => "WHERE 1 AND contact_type LIKE '%%1%' ", 
        'expectedParamsOutput' => array(1 => array(0 => 'Individual', 1 => 'String', 2 => 2)),
      ),
//      array('contactTypeId' => '1000', 'phoneTypeId' => '1', 'expectedOutput' => ''),
//      array('contactTypeId' => '1', 'phoneTypeId' => '1000', 'expectedOutput' => ''),
//      array('contactTypeId' => 'string where id should be', 'phoneTypeId' => '1', 'expectedOutput' => ''),
//      array('contactTypeId' => '1', 'phoneTypeId' => 'string where id should be', 'expectedOutput' => ''),
    );
    
    foreach($testData as $eachTest){
      $actualOutput = CRM_Phonenumbervalidator_Utils::buildWhereStatementMysqlString($eachTest['contactTypeId'], $eachTest['phoneTypeId']);
      $this->assertEquals($eachTest['expectedStatementOutput'], $actualOutput['statement'], "Test failed, actual output was: " . print_r($actualOutput['statement'], TRUE));
      $this->assertEquals($eachTest['expectedParamsOutput'], $actualOutput['params'], "Test failed, actual output was: " . print_r($actualOutput['params'], TRUE));
    }
  }
    
}
