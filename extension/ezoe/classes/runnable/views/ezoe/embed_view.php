<?php
/**
 * The code of extension/ezoe/modules/ezoe/embed_view.php, moved into a class (#207 stage 1). The file extension/ezoe/modules/ezoe/embed_view.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */

namespace Exponential\View\Extension\Ezoe\Ezoe
{

class EmbedView extends \Exponential\Runnable\ModuleView
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
        $tplSuffix       = '';
        $idString        = '';
        $tagName         = 'embed';
        $embedObject     = false;

        if ( isset( $Params['EmbedID'] )  && $Params['EmbedID'])
        {
            $embedType = 'ezobject';
            if (  is_numeric( $Params['EmbedID'] ) )
                $embedId = $Params['EmbedID'];
            else
                list($embedType, $embedId) = explode('_', $Params['EmbedID']);

            if ( strcasecmp( $embedType  , 'eznode'  ) === 0 )
            {
                $embedNode   = \eZContentObjectTreeNode::fetch( $embedId );
                $embedObject = $embedNode->object();
                $tplSuffix   = '_node';
                $idString    = 'eZNode_' . $embedId;
            }
            else
            {
                $embedObject = \eZContentObject::fetch( $embedId );
                $idString    = 'eZObject_' . $embedId;
            }
        }

        if ( $embedObject instanceof \eZContentObject )
        {
            $objectName      = $embedObject->attribute( 'name' );
            $classID         = $embedObject->attribute( 'contentclass_id' );
            $classIdentifier = $embedObject->attribute( 'class_identifier' );
            if ( !$embedObject->attribute( 'can_read' ) || !$embedObject->attribute( 'can_view_embed' ) )
            {
                $tplSuffix = '_denied';
            }
        }
        else
        {
            $objectName      = 'Unknown';
            $classID         = 0;
            $classIdentifier = false;
        }

        $className = '';
        $size  = 'medium';
        $view  = 'embed';
        $align = 'none';
        //$style = '';//'text-align: left;';

        if ( isset( $_GET['inline'] ) && $_GET['inline'] === 'true' )
        {
            $tagName = 'embed-inline';
        }
        else if ( $http->hasPostVariable('inline') &&
             $http->postVariable('inline') === 'true' )
        {
            $tagName = 'embed-inline';
        }

        if ( isset( $_GET['class'] ) )
        {
            $className = $_GET['class'];
        }
        else if ( $http->hasPostVariable('class') )
        {
            $className = $http->postVariable('class');
        }

        if ( isset( $_GET['size'] ) )
        {
            $size = $_GET['size'];
        }
        else if ( $http->hasPostVariable('size') )
        {
            $size = $http->postVariable('size');
        }

        if ( isset( $_GET['view'] ) )
        {
            $view = $_GET['view'];
        }
        else if ( $http->hasPostVariable('view') )
        {
            $view = $http->postVariable('view');
        }

        if ( isset( $_GET['align'] ) )
        {
            $align = $_GET['align'] === 'middle' ? 'center' : $_GET['align'];
        }
        else if ( $http->hasPostVariable('align') )
        {
            $align = $http->postVariable('align');
            if ( $align === 'middle' )
                $align = 'center';
        }

        //if ( $align === 'left' || $align === 'right' )
        //    $style .= ' float: ' . $align . ';';


        $res = \eZTemplateDesignResource::instance();
        $res->setKeys( array( array('classification', $className) ) );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'view', $view );
        $tpl->setVariable( 'object', $embedObject );
        $tpl->setVariable( 'link_parameters', array() );
        $tpl->setVariable( 'classification', $className );
        $tpl->setVariable( 'object_parameters', array( 'size' => $size, 'align' => $align, 'show_path' => true ) );
        if ( isset( $embedNode ) ) $tpl->setVariable( 'node', $embedNode );

        //if ( $style !== '' )
        //    $style = ' style="' . $style . '"';

        $templateOutput = $tpl->fetch( 'design:content/datatype/view/ezxmltags/' . $tagName . $tplSuffix . '.tpl' );
        //echo '<div id="' . $idString . '" title="' . $objectName . '"' . $style . '>' . $templateOutput . '</div>';

        //echo "<!--\r\n";
        //eZDebug::printReport( false, false );
        //echo "-->\r\n";
        echo $templateOutput;



        \eZDB::checkTransactionCounter();
        \eZExecution::cleanExit();

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
