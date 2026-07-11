<?php
require_once __DIR__ . '/../eZDatatypeAbstractTest.php';
#[\PHPUnit\Framework\Attributes\Group('database')]
/**
 * File containing the eZStringTypeTest class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 * @group database
 */
class eZStringTypeTest extends eZDatatypeAbstractTest
{

    private function defaultDataSet()
    {
        $dataSet = new ezpDatatypeTestDataSet();
        $dataSet->fromString = 'this is a string';
        $dataSet->dataText = 'this is a string';
        $dataSet->content = 'this is a string';
        return $dataSet;
    }
}
?>
