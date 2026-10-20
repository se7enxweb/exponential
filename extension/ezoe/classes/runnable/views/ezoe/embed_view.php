<?php
/**
 * The code of extension/ezoe/modules/ezoe/embed_view.php, moved into a class (#207 stage 1). The file extension/ezoe/modules/ezoe/embed_view.php is one call to it.
 * Guide: doc/en/us/bc/6.0/cli_cronjob_view_abstractions.md
 */

namespace Exponential\View\Extension\Ezoe\Ezoe
{

class EmbedView extends \Exponential\Runnable\ModuleView
{
    /**
     * A request value as a string: an array (class[]=x) or anything else that is not a string or a number gives
     * the default, so the template keys and object_parameters always get a string.
     *
     * @param mixed $value
     * @param string $default
     * @return string
     */
    public static function stringParameter( $value, $default )
    {
        if ( is_string( $value ) )
            return $value;
        if ( is_int( $value ) || is_float( $value ) )
            return (string) $value;
        return $default;
    }

    /**
     * The view modes an embed may be shown in: the tag's own view ('embed' for a block embed, 'embed-inline' for an
     * inline one) and content.ini AvailableViewModes[] of that tag's sections only, [embed] / [embed_<class>] or
     * [embed-inline] / [embed-inline_<class>] where the class of the embedded object has one (the lists
     * ezoe/relations offers in the dialog). A block embed cannot ask for a view of the inline lists, and the other
     * way round.
     *
     * @param \eZINI $contentIni
     * @param string $tagName 'embed' or 'embed-inline'
     * @param string|false $classIdentifier
     * @return string[] the tag's own view first
     */
    public static function availableViewModes( $contentIni, $tagName, $classIdentifier )
    {
        $tagName = $tagName === 'embed-inline' ? 'embed-inline' : 'embed';
        $sections = array( $tagName );
        if ( is_string( $classIdentifier ) && $classIdentifier !== '' )
            $sections[] = $tagName . '_' . $classIdentifier;
        $modes = array( $tagName );
        foreach ( $sections as $section )
        {
            if ( $contentIni->hasVariable( $section, 'AvailableViewModes' ) )
                $modes = array_merge( $modes, (array) $contentIni->variable( $section, 'AvailableViewModes' ) );
        }
        return array_values( array_unique( array_filter( array_map( 'strval', $modes ), 'strlen' ) ) );
    }

    /**
     * The view the embed is shown in: the requested one when it is an available view mode, else the first of the
     * list (the tag's own view, see availableViewModes()).
     *
     * @param mixed $view the requested view
     * @param string[] $available
     * @return string
     */
    public static function allowedView( $view, array $available )
    {
        if ( is_string( $view ) && in_array( $view, $available, true ) )
            return $view;
        return $available ? (string) reset( $available ) : 'embed';
    }

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

        // A missing or malformed EmbedID, a node or an object that no longer exists: the unknown embed, not a fatal
        // error (the editors call this view for every embed of a draft, also one whose target was removed)
        $embedParsed = Relations::parseEmbedId( isset( $Params['EmbedID'] ) ? $Params['EmbedID'] : null );
        if ( $embedParsed !== false )
        {
            list( $embedType, $embedId ) = $embedParsed;
            if ( $embedType === 'eZNode' )
            {
                $embedNode = \eZContentObjectTreeNode::fetch( $embedId );
                if ( $embedNode instanceof \eZContentObjectTreeNode )
                {
                    $embedObject = $embedNode->object();
                    $tplSuffix   = '_node';
                }
                $idString = 'eZNode_' . $embedId;
            }
            else
            {
                $embedObject = \eZContentObject::fetch( $embedId );
                $idString    = 'eZObject_' . $embedId;
            }
        }

        // a node without its object, or no node or object at all: the unknown embed of embed.tpl
        if ( !$embedObject instanceof \eZContentObject )
        {
            $embedObject = false;
            $tplSuffix   = '';
            unset( $embedNode );
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
            $className = self::stringParameter( $_GET['class'], '' );
        }
        else if ( $http->hasPostVariable('class') )
        {
            $className = self::stringParameter( $http->postVariable('class'), '' );
        }

        if ( isset( $_GET['size'] ) )
        {
            $size = self::stringParameter( $_GET['size'], 'medium' );
        }
        else if ( $http->hasPostVariable('size') )
        {
            $size = self::stringParameter( $http->postVariable('size'), 'medium' );
        }

        if ( isset( $_GET['view'] ) )
        {
            $view = self::stringParameter( $_GET['view'], 'embed' );
        }
        else if ( $http->hasPostVariable('view') )
        {
            $view = self::stringParameter( $http->postVariable('view'), 'embed' );
        }

        if ( isset( $_GET['align'] ) )
        {
            $align = self::stringParameter( $_GET['align'], 'none' );
            if ( $align === 'middle' )
                $align = 'center';
        }
        else if ( $http->hasPostVariable('align') )
        {
            $align = self::stringParameter( $http->postVariable('align'), 'none' );
            if ( $align === 'middle' )
                $align = 'center';
        }

        // the view names a template (content/view/<view>.tpl): only an available view mode
        $view = self::allowedView( $view, self::availableViewModes( \eZINI::instance( 'content.ini' ), $tagName, $classIdentifier ) );

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
