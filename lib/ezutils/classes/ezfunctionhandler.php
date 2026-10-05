<?php
/**
 * File containing the eZFunctionHandler class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*!
  \class eZFunctionHandler ezfunctionhandler.php
  \brief The class eZFunctionHandler does

*/

class eZFunctionHandler
{
    static function moduleFunctionInfo( $moduleName )
    {
        if ( !isset( $GLOBALS['eZGlobalModuleFunctionList'] ) )
        {
            $GLOBALS['eZGlobalModuleFunctionList'] = array();
        }
        if ( isset( $GLOBALS['eZGlobalModuleFunctionList'][$moduleName] ) )
        {
            return $GLOBALS['eZGlobalModuleFunctionList'][$moduleName];
        }
        $moduleFunctionInfo = new eZModuleFunctionInfo( $moduleName );
        $moduleFunctionInfo->loadDefinition();

        return $GLOBALS['eZGlobalModuleFunctionList'][$moduleName] = $moduleFunctionInfo;
    }

    /*!
     \static
     Execute alias fetch for simplified fetching of objects
    */
    static function executeAlias( $aliasFunctionName, $functionParameters )
    {
        $aliasSettings = eZINI::instance( 'fetchalias.ini' );
        if ( $aliasSettings->hasSection( $aliasFunctionName ) )
        {
            $moduleFunctionInfo = eZFunctionHandler::moduleFunctionInfo( $aliasSettings->variable( $aliasFunctionName, 'Module' ) );
            if ( !$moduleFunctionInfo->isValid() )
            {
                eZDebug::writeError( "Cannot execute function '$aliasFunctionName' in module '{$moduleFunctionInfo->ModuleName}', no valid data", __METHOD__ );
                return null;
            }

            $functionName = $aliasSettings->variable( $aliasFunctionName, 'FunctionName' );

            $functionArray = array();
            if ( $aliasSettings->hasVariable( $aliasFunctionName, 'Parameter' ) )
            {
                $parameterTranslation = $aliasSettings->variable( $aliasFunctionName, 'Parameter' );
                foreach( array_keys( $parameterTranslation ) as $functionKey )
                {
                    $translatedParameter = $parameterTranslation[$functionKey];
                    if ( array_key_exists( $translatedParameter, $functionParameters ) )
                         $functionArray[$functionKey] = $functionParameters[$translatedParameter];
                    else
                        $functionArray[$functionKey] = null;
                }
            }

            if ( $aliasSettings->hasVariable( $aliasFunctionName, 'Constant' ) )
            {
                $constantParameterArray = $aliasSettings->variable( $aliasFunctionName, 'Constant' );
                // prevent PHP warning in the loop below
                if ( !is_array( $constantParameterArray ) )
                    $constantParameterArray = array();
                foreach ( array_keys( $constantParameterArray ) as $constKey )
                {
                    // a parameter given to the alias overrides the constant of the same name
                    if ( array_key_exists( $constKey, $functionParameters ) )
                    {
                        $functionArray[$constKey] = $functionParameters[$constKey];
                        continue;
                    }
                    if ( $moduleFunctionInfo->isParameterArray( $functionName, $constKey ) )
                    {
                        $constantParameter = eZFunctionHandler::constantList( $constantParameterArray[$constKey] );
                        if ( $constantParameter ) // if the array is not empty
                            $functionArray[$constKey] = $constantParameter;
                    }
                    else
                        $functionArray[$constKey] = $constantParameterArray[$constKey];
                }
            }

/*
 */
            foreach ( $functionParameters as $paramName => $value )
            {
                if ( !array_key_exists( $paramName, $functionArray ) )
                {
                    $functionArray[$paramName] = $value;
                }
            }
            return $moduleFunctionInfo->execute( $functionName, $functionArray );
        }
        eZDebug::writeWarning( 'Could not execute. Function ' . $aliasFunctionName. ' not found.' , __METHOD__ );
    }

    /**
     * The items of a list constant of fetchalias.ini: split at semicolons, where \; is a semicolon in an item,
     * empty items left out. Used by the interpreted and the compiled fetch_alias alike.
     *
     * @param string $text
     * @return array
     */
    static function constantList( $text )
    {
        $items = preg_split( '/((?<=\x5c\x5c)|(?<!\x5c{1}));/', (string)$text );
        $items = array_values( array_diff( $items, array( '' ) ) );
        // remove the backslashes that escaped a delimiter, and unescape escaped backslashes
        $items = preg_replace( '/\x5c{1};/', ';', $items );
        return str_replace( '\\\\', '\\', $items );
    }

    static function execute( $moduleName, $functionName, $functionParameters )
    {
        $moduleFunctionInfo = eZFunctionHandler::moduleFunctionInfo( $moduleName );
        if ( !$moduleFunctionInfo->isValid() )
        {
            eZDebug::writeError( "Cannot execute function '$functionName' in module '$moduleName', no valid data", __METHOD__ );
            return null;
        }

        return $moduleFunctionInfo->execute( $functionName, $functionParameters );
    }
}

?>
