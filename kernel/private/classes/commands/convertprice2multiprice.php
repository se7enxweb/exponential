<?php
/**
 * The code of bin/php/convertprice2multiprice.php, moved into a class (#207 stage 1). The file bin/php/convertprice2multiprice.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of bin/php/convertprice2multiprice.php:
 *
 *
 * File containing the convertprice2multiprice.php script.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 * @description Convert content objects with 'price' datatype attributes to 'multiprice'
 * @long-description Iterates over all content objects using the deprecated 'price' datatype and converts their attributes to the newer 'multiprice' datatype, preserving all existing pricing data.
 *
 */

namespace
{


function currencyForLocale( $localeString = false )
{
    global $cli;
    global $currencyList;
    $currency = false;

    if ( $currencyList === false )
    {
        $currencyList = eZCurrencyData::fetchList();
    }

    $locale = eZLocale::instance( $localeString );
    if ( is_object( $locale ) )
    {
        // get currency
        if ( $currencyCode = $locale->currencyShortName() )
        {
            if ( !isset( $currencyList[$currencyCode] ) )
            {
                $cli->warning( "Currency '$currencyCode' doesn't exist" );
                $cli->notice( "Creating currency '$currencyCode'... ", false );

                $currencySymbol = $locale->currencySymbol();
                $localeCode = $locale->localeFullCode();

                if ( $currency = eZCurrencyData::create( $currencyCode, $currencySymbol, $localeCode, '0.00000', '1.00000', '0.00000' ) )
                {
                    $cli->output( 'Ok' );
                    $currency->store();
                    $currencyList[$currencyCode] = $currency;
                }
                else
                {
                    $cli->error( 'Failed' );
                }
            }
            else
            {
                $currency = $currencyList[$currencyCode];
            }
        }
        else
        {
            $cli->error( "Unable to find currency code for the '$localeString' locale" );
        }
    }
    else
    {
        $cli->error( "Unable to find '$localeString' locale" );
    }

    return $currency;
}
}

namespace Exponential\Command\Kernel
{

class Convertprice2multiprice extends \Exponential\Runnable\Command
{
    public function run()
    {
        // the script's variables were globals; functions of the script read them with "global"
        foreach ( array( 'class', 'classID', 'classList', 'cli', 'contentObjectID', 'convertedObjectsCount', 'currency', 'currencyCode', 'currencyList', 'db', 'defaultCurrency', 'defaultCurrencyCode', 'limit', 'multiprice', 'object', 'objectAttribute', 'objectAttributeList', 'objectList', 'objectListCount', 'objectVersion', 'objectVersions', 'offset', 'priceClassAttribute', 'priceClassAttributeID', 'priceValue', 'script', 'scriptOptions', 'version' ) as $__name )
            ${$__name} = &$GLOBALS[$__name];
        unset( $__name );

        global $cli;
        global $currencyList;

        $currencyList = false;

        $cli = \eZCLI::instance();
        $script = \eZScript::instance( array( 'description' => ( "\n" .
                                                                 "This script will convert objects with 'price' datatype to\n" .
                                                                 "the objects with 'multiprice' datatype.\n" ),
                                              'use-session' => false,
                                              'use-modules' => true,
                                              'use-extensions' => true,
                                              'user' => true ) );
        $script->startup();

        $scriptOptions = $script->getOptions( "",
                                              "",
                                              array(),
                                              false,
                                              array( 'user' => true )
                                             );


        $script->initialize();


        $convertedObjectsCount = 0;

        $classList = \eZContentClass::fetchList();

        $db = \eZDB::instance();
        $db->begin();
        foreach ( $classList as $class )
        {
            if ( \eZShopFunctions::isSimplePriceClass( $class ) )
            {
                $classID = $class->attribute( 'id' );
                $objectListCount = \eZContentObject::fetchSameClassListCount( $classID );
                if ( $objectListCount == 0 )
                {
                    $cli->output( "No objects found for '" . $class->attribute( 'name' ) . "' class" );
                    continue;
                }

                $cli->output( "Processing objects of the '" . $class->attribute( 'name' ) . "' class" );

                $defaultCurrency = currencyForLocale();

                if ( !$defaultCurrency )
                    $script->shutdown( 1 );

                $defaultCurrencyCode = $defaultCurrency->attribute( 'code' );

                $priceClassAttribute = \eZShopFunctions::priceAttribute( $class );
                $priceClassAttributeID = $priceClassAttribute->attribute( 'id' );

                // replace 'ezprice' class attribute with 'ezmultiprice'.
                $priceClassAttribute->setAttribute( 'data_type_string', 'ezmultiprice' );
                $priceClassAttribute->setAttribute( \eZMultiPriceType::DEFAULT_CURRENCY_CODE_FIELD, $defaultCurrencyCode );
                $priceClassAttribute->store();

                unset( $GLOBALS['eZContentClassAttributeCache'][$priceClassAttributeID] );

                // update objects
                $offset = 0;
                $limit = 1000;

                $cli->output( 'Converting', false );
                while ( $offset < $objectListCount )
                {
                    $objectList = \eZContentObject::fetchSameClassList( $class->attribute( 'id' ), true, $offset, $limit );
                    $offset += count( $objectList );

                    foreach ( $objectList as $object )
                    {
                        $contentObjectID = $object->attribute( 'id' );
                        $objectVersions =& $object->versions();
                        foreach ( $objectVersions as $objectVersion )
                        {
                            $version = $objectVersion->attribute( 'version' );
                            $objectAttributeList = \eZContentObjectAttribute::fetchSameClassAttributeIDList( $priceClassAttributeID, true, $version, $contentObjectID );

                            foreach ( $objectAttributeList as $objectAttribute )
                            {
                                $priceValue = $objectAttribute->attribute( 'data_float' );

                                $multiprice = \eZMultiPriceData::create( $objectAttribute->attribute( 'id' ),
                                                                        $version,
                                                                        $defaultCurrencyCode,
                                                                        $priceValue,
                                                                        \eZMultiPriceData::VALUE_TYPE_CUSTOM );
                                $multiprice->store();

                                $objectAttribute->setAttribute( 'data_type_string', 'ezmultiprice' );
                                $objectAttribute->setAttribute( 'data_float', 0 );
                                $objectAttribute->setAttribute( 'sort_key_int', 0 );
                                $objectAttribute->store();
                            }
                        }

                        $cli->output( '.', false );
                        ++$convertedObjectsCount;
                    }
                }
                $cli->output( ' ' );
            }
        }


        // create/update autoprices.
        if ( is_array( $currencyList ) )
        {
            $cli->output( "Updating autoprices." );

            foreach ( $currencyList as $currencyCode => $currency )
            {
                \eZMultiPriceData::createPriceListForCurrency( $currencyCode );
            }

            \eZMultiPriceData::updateAutoprices();
        }

        $db->commit();

        \eZContentCacheManager::clearAllContentCache();

        $cli->output( "Total converted objects: $convertedObjectsCount" );
        $cli->output( "Done." );

        $script->shutdown( 0 );
    }
}

}
