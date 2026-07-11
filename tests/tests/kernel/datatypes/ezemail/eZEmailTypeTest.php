<?php
require_once __DIR__ . '/../eZDatatypeAbstractTest.php';
#[\PHPUnit\Framework\Attributes\Group('database')]
/**
 * File containing the eZEmailTypeTest class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 * @group database
 */
class eZEmailTypeTest extends eZDatatypeAbstractTest
{

    private function defaultDataSet()
    {
        $dataSet = new ezpDatatypeTestDataSet();
        $dataSet->fromString = 'test.user@ez.no';
        $dataSet->dataText = 'test.user@ez.no';
        $dataSet->content = 'test.user@ez.no';
        return $dataSet;
    }
}
?>
