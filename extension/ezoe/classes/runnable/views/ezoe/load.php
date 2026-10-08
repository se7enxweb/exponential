<?php
/**
 * The code of extension/ezoe/modules/ezoe/load.php, moved into a class (#207 stage 1). The file extension/ezoe/modules/ezoe/load.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */

namespace Exponential\View\Extension\Ezoe\Ezoe
{

class Load extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $embedId         = 0;
        $http            = \eZHTTPTool::instance();

        // A missing or malformed EmbedID answers false, without a warning (Relations::parseEmbedId())
        $embedObject = false;
        $embedParsed = Relations::parseEmbedId( isset( $Params['EmbedID'] ) ? $Params['EmbedID'] : null );
        if ( $embedParsed !== false )
        {
            list( $embedType, $embedId ) = $embedParsed;
            if ( $embedType === 'eZNode' )
                $embedObject = \eZContentObject::fetchByNodeID( $embedId );
            else
                $embedObject = \eZContentObject::fetch( $embedId );
        }

        if ( !$embedObject instanceof \eZContentObject || !$embedObject->canRead() )
        {
           echo 'false';
           \eZExecution::cleanExit();
        }

        // Params for node to json encoder
        $params    = array('loadImages' => true);
        $params['imagePreGenerateSizes'] = array('small', 'original');

        // look for datamap parameter ( what datamap attribute we should load )
        if ( isset( $Params['DataMap'] )  && $Params['DataMap'])
            $params['dataMap'] = array($Params['DataMap']);

        // what image sizes we want returned with full data ( url++ )
        if ( $http->hasPostVariable( 'imagePreGenerateSizes' ) )
            $params['imagePreGenerateSizes'][] = $http->postVariable( 'imagePreGenerateSizes' );
        else if ( isset( $Params['ImagePreGenerateSizes'] )  && $Params['ImagePreGenerateSizes'])
            $params['imagePreGenerateSizes'][] = $Params['ImagePreGenerateSizes'];

        // encode embed object as a json response
        $json = \ezjscAjaxContent::nodeEncode( $embedObject, $params );

        // display debug as a js comment
        //echo "/*\r\n";
        //eZDebug::printReport( false, false );
        //echo "*/\r\n";
        echo $json;

        \eZDB::checkTransactionCounter();
        \eZExecution::cleanExit();

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
