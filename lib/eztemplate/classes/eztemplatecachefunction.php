<?php
/**
 * File containing the eZTemplateCacheFunction class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*!
  \class eZTemplateCacheFunction eztemplatecachefunction.php
  \ingroup eZTemplateFunctions
  \brief Advanced cache handling

*/

class eZTemplateCacheFunction
{
    const DEFAULT_TTL = 7200; // 2 hours = 60*60*2

    /**
     * Initializes the object with names.
     *
     * @param string $blockName
     */
    public function __construct( $blockName = 'cache-block' )
    {
        $this->BlockName = $blockName;
    }

    /*!
     Returns an array containing the name of the block function, default is "block".
     The name is specified in the constructor.
    */
    function functionList()
    {
        return array( $this->BlockName );
    }

    function functionTemplateHints()
    {
        return array( $this->BlockName => array( 'parameters' => true,
                                                 'static' => false,
                                                 'transform-children' => true,
                                                 'tree-transformation' => true,
                                                 'transform-parameters' => true ) );
    }

    function templateNodeTransformation( $functionName, &$node,
                                         $tpl, $parameters, $privateData )
    {
        $ini = eZINI::instance();
        $children = eZTemplateNodeTool::extractFunctionNodeChildren( $node );
        if ( $ini->variable( 'TemplateSettings', 'TemplateCache' ) != 'enabled' )
        {
            return $children;
        }

        $functionPlacement = eZTemplateNodeTool::extractFunctionNodePlacement( $node );
        $placementKeyString = eZTemplateCacheBlock::placementString( $functionPlacement );

        $newNodes = array();
        $ignoreContentExpiry = false;

        if ( isset( $parameters['expiry'] ) )
        {
            if ( eZTemplateNodeTool::isConstantElement( $parameters['expiry'] ) )
            {
                $expiryValue = eZTemplateNodeTool::elementConstantValue( $parameters['expiry'] );
                $ttlCode = $expiryValue > 0 ? eZPHPCreator::variableText( $expiryValue , 0, 0, false ) : 'null';
            }
            else
            {
                $newNodes[] = eZTemplateNodeTool::createVariableNode( false, $parameters['expiry'], false, array(), 'localExpiry' );
                $ttlCode = "( \$localExpiry > 0 ? \$localExpiry : null )";
            }
        }
        else
        {
            $ttlCode = eZPHPCreator::variableText( self::DEFAULT_TTL, 0, 0, false );
        }

        if ( isset( $parameters['ignore_content_expiry'] ) )
        {
            $ignoreContentExpiry = eZTemplateNodeTool::elementConstantValue( $parameters['ignore_content_expiry'] );
        }

        $keysData = false;
        $hasKeys = false;
        $subtreeExpiryData = null;
        $subtreeValue = null;
        if ( isset( $parameters['keys'] ) )
        {
            $keysData = $parameters['keys'];
            $hasKeys = true;
        }
        if ( isset( $parameters['subtree_expiry'] ) )
        {
            $subtreeExpiryData = $parameters['subtree_expiry'];
            if ( !eZTemplateNodeTool::isConstantElement( $subtreeExpiryData ) )
                $hasKeys = true;
            else
                $subtreeValue = eZTemplateNodeTool::elementConstantValue( $subtreeExpiryData );

            $ignoreContentExpiry = true;
        }
        $accessName = false;
        if ( isset( $GLOBALS['eZCurrentAccess']['name'] ) )
            $accessName = $GLOBALS['eZCurrentAccess']['name'];
        if ( $hasKeys )
        {
            $placementKeyStringText = eZPHPCreator::variableText( $placementKeyString, 0, 0, false );
            $accessNameText = eZPHPCreator::variableText( $accessName, 0, 0, false );
            $newNodes[] = eZTemplateNodeTool::createVariableNode( false, $keysData, false, array(), 'cacheKeys' );
            $newNodes[] = eZTemplateNodeTool::createVariableNode( false, $subtreeExpiryData, false, array(), 'subtreeExpiry' );

            // The access path (/site under URI matching) and the UI switch variant (expUISwitch, e.g. another
            // interface language) are read at run time and only join the key when there is one, so the keys of
            // everybody else stay as they were (eZTemplateCacheBlock::accessSuffix()).
            $code = "\$cacheKeys = array( \$cacheKeys, $placementKeyStringText, $accessNameText . eZTemplateCacheBlock::accessSuffix() );\n";
            $cachePathText = "\$cachePath";
        }
        else
        {
            $nodeID = $subtreeValue ? eZTemplateCacheBlock::decodeNodeID( $subtreeValue ) : false;
            $cachePath = eZTemplateCacheBlock::cachePath( eZTemplateCacheBlock::keyString( array( $placementKeyString, $accessName ) ), $nodeID );
            $placementKeyStringText = eZPHPCreator::variableText( $placementKeyString, 0, 0, false );
            $accessNameText = eZPHPCreator::variableText( $accessName, 0, 0, false );
            $nodeIDText = var_export( $nodeID, true );
            $code = "\$cacheBlockAccessSuffix = eZTemplateCacheBlock::accessSuffix();\n" .
                    "\$cachePath = \$cacheBlockAccessSuffix === '' ? " . eZPHPCreator::variableText( $cachePath, 0, 0, false ) .
                    " : eZTemplateCacheBlock::cachePath( eZTemplateCacheBlock::keyString( array( $placementKeyStringText, $accessNameText . \$cacheBlockAccessSuffix ) ), $nodeIDText );\n";
            $cachePathText = "\$cachePath";
        }

        $newNodes[] = eZTemplateNodeTool::createCodePieceNode( $code );

        $code = '';

        $codePlacementHash = md5( $placementKeyString );
        if ( $hasKeys )
        {
            $code .= "list(\$cacheHandler_{$codePlacementHash}, \$contentData) =\n  eZTemplateCacheBlock::retrieve( \$cacheKeys, \$subtreeExpiry, $ttlCode, " . ($ignoreContentExpiry ? "false" : "true") . " );\n";
        }
        else
        {
            $nodeIDText = var_export( $nodeID, true );
            $code .= "list(\$cacheHandler_{$codePlacementHash}, \$contentData) =\n  eZTemplateCacheBlock::handle( $cachePathText, $nodeIDText, $ttlCode, " . ($ignoreContentExpiry ? "false" : "true") . ", $placementKeyStringText );\n";
        }
        $code .=
            "if ( !( \$contentData instanceof eZClusterFileFailure ) )\n" .
            "{\n";

        $newNodes[] = eZTemplateNodeTool::createCodePieceNode( $code, array( 'spacing' => 0 ) );
        $newNodes[] = eZTemplateNodeTool::createWriteToOutputVariableNode( 'contentData', array( 'spacing' => 4 ) );
        $newNodes[] = eZTemplateNodeTool::createCodePieceNode( "    unset( \$contentData );\n" .
                                                               "}\n" .
                                                               "else\n" .
                                                               "{\n" .
                                                               "    unset( \$contentData );\n" .
                                                               // A template that includes itself runs this block again in the same scope, which sets and
                                                               // unsets the handler variable: the handler is kept on a stack until the block's text is stored.
                                                               "    \$cacheBlockHandlerStack_{$codePlacementHash}[] = \$cacheHandler_{$codePlacementHash};" );

        $newNodes[] = eZTemplateNodeTool::createOutputVariableIncreaseNode( array( 'spacing' => 4 ) );
        $newNodes[] = eZTemplateNodeTool::createSpacingIncreaseNode( 4 );
        $newNodes = array_merge( $newNodes, $children );
        $newNodes[] = eZTemplateNodeTool::createSpacingDecreaseNode( 4 );
        $newNodes[] = eZTemplateNodeTool::createAssignFromOutputVariableNode( 'cachedText', array( 'spacing' => 4 ) );

        $code =
            "\$cacheHandler_{$codePlacementHash} = array_pop( \$cacheBlockHandlerStack_{$codePlacementHash} );\n" .
            "eZTemplateCacheBlock::store( \$cacheHandler_{$codePlacementHash}, \$cachedText );\n";

        $newNodes[] = eZTemplateNodeTool::createCodePieceNode( $code, array( 'spacing' => 4 ) );
        $newNodes[] = eZTemplateNodeTool::createOutputVariableDecreaseNode( array( 'spacing' => 4 ) );
        $newNodes[] = eZTemplateNodeTool::createWriteToOutputVariableNode( 'cachedText', array( 'spacing' => 4 ) );
        $newNodes[] = eZTemplateNodeTool::createCodePieceNode( "    unset( \$cachedText, \$cacheHandler_{$codePlacementHash} );\n" .
                                                               "    if ( !\$cacheBlockHandlerStack_{$codePlacementHash} )\n" .
                                                               "        unset( \$cacheBlockHandlerStack_{$codePlacementHash} );\n}\n" );

        return $newNodes;
    }

    /*!
     Processes the function with all it's children.
    */
    function process( $tpl, &$textElements, $functionName, $functionChildren, $functionParameters, $functionPlacement, $rootNamespace, $currentNamespace )
    {
        switch ( $functionName )
        {
            case $this->BlockName:
            {
                // Check for disabled cache.
                $ini = eZINI::instance();
                if ( $ini->variable( 'TemplateSettings', 'TemplateCache' ) != 'enabled' )
                {
                    $text = eZTemplateCacheFunction::processUncached( $tpl, $functionChildren,
                                                                      $rootNamespace, $currentNamespace );
                    $textElements[] = $text;
                    return;
                }
                else
                {
                    $text = eZTemplateCacheFunction::processCachedPreprocess( $tpl,  $functionChildren,
                                                                              $functionParameters, $functionPlacement,
                                                                              $rootNamespace, $currentNamespace );
                    $textElements[] = $text;
                }
            } break;
        }
    }

    function processCachedPreprocess( $tpl, $functionChildren, $functionParameters, $functionPlacement, $rootNamespace, $currentNamespace )
    {
        $keys                = null;
        $subtreeExpiry       = null;
        $expiry              = self::DEFAULT_TTL;
        $ignoreContentExpiry = null;
        $subtreeExpiry       = null;

        if ( isset( $functionParameters["keys"] ) )
        {
            $keys = $tpl->elementValue( $functionParameters["keys"], $rootNamespace, $currentNamespace, $functionPlacement );
        }
        if ( isset( $functionParameters['subtree_expiry'] ) )
        {
            $subtreeExpiry = $tpl->elementValue( $functionParameters["subtree_expiry"], $rootNamespace, $currentNamespace, $functionPlacement );
        }
        if ( isset( $functionParameters["expiry"] ) )
        {
            $expiry = $tpl->elementValue( $functionParameters["expiry"], $rootNamespace, $currentNamespace, $functionPlacement );
        }
        if ( isset( $functionParameters["ignore_content_expiry"] ) )
        {
            $ignoreContentExpiry = $tpl->elementValue( $functionParameters["ignore_content_expiry"], $rootNamespace, $currentNamespace, $functionPlacement ) === true;
        }
        if ( isset( $functionParameters['subtree_expiry'] ) )
        {
            $ignoreContentExpiry = true;
        }

        $placementString = eZTemplateCacheBlock::placementString( $functionPlacement );

        return eZTemplateCacheFunction::processCached( $tpl, $functionChildren, $rootNamespace, $currentNamespace,
                                                       $placementString, $keys, $subtreeExpiry, $expiry, $ignoreContentExpiry );
    }

    function processCached( $tpl, $functionChildren, $rootNamespace, $currentNamespace,
                            $placementString, $keys, $subtreeExpiry, $expiry, $ignoreContentExpiry )
    {
        // the current siteaccess, its access path and the UI switch variant, as in the compiled code
        $accessName = eZTemplateCacheBlock::accessKey();
        if ( $keys === null )
        {
            $keyArray = array( $placementString, $accessName );
        }
        else
        {
            $keyArray = array( $keys, $placementString, $accessName );
        }

        $nodeID = $subtreeExpiry ? eZTemplateCacheBlock::decodeNodeID( $subtreeExpiry ) : false;
        $phpPath = eZTemplateCacheBlock::cachePath( eZTemplateCacheBlock::keyString( $keyArray ), $nodeID );

        $ttl = $expiry > 0 ? $expiry : null;

        if ( $subtreeExpiry !== null )
        {
            $ignoreContentExpiry = true;
        }
        else if ( $ignoreContentExpiry === null )
        {
            $ignoreContentExpiry = false;
        }

        $globalExpiryTime = -1;
        if ( $ignoreContentExpiry == false )
        {
            $globalExpiryTime = eZExpiryHandler::getTimestamp( 'template-block-cache', -1 );
        }
        $globalExpiryTime = max( eZExpiryHandler::getTimestamp( 'global-template-block-cache', -1 ), // This expiry value is the true global expiry for cache-blocks
                                 $globalExpiryTime );

        // Check if we can restore
        $cacheFile = eZClusterFileHandler::instance( $phpPath );
        $args = array( "tpl" => $tpl,
                       "functionChildren" => $functionChildren,
                       "rootNamespace" => $rootNamespace,
                       "currentNamespace" => $currentNamespace );
        $noted = ( !empty( $GLOBALS['eZDebugEnabled'] ) || !empty( $GLOBALS['eZDebugCollect'] ) ) && class_exists( 'expRenderStats' );
        $start = microtime( true );
        $this->generateMs = null;
        $result = $cacheFile->processCache( array( 'eZTemplateCacheBlock', 'retrieveContent' ),
                                            array( $this, 'generateProcessedContent' ),
                                            $ttl,
                                            $globalExpiryTime,
                                            $args );
        if ( $noted )
        {
            $total = ( microtime( true ) - $start ) * 1000;
            $generated = $this->generateMs !== null;
            expRenderStats::noteCacheBlock( spl_object_id( $cacheFile ), $phpPath, $placementString, $keys, $nodeID, $ttl, !$generated,
                                            $generated ? max( 0.0, $total - $this->generateMs ) : $total, $this->generateMs,
                                            is_string( $result ) ? strlen( $result ) : null );
        }
        return $result;
    }

    function generateProcessedContent( $fname, $args )
    {
        extract( $args );
        $noted = ( !empty( $GLOBALS['eZDebugEnabled'] ) || !empty( $GLOBALS['eZDebugCollect'] ) ) && class_exists( 'expRenderStats' );
        if ( $noted )
            expRenderStats::cacheBlockGenerationStarted();
        $start = microtime( true );
        $content = eZTemplateCacheFunction::processUncached( $tpl, $functionChildren, $rootNamespace, $currentNamespace );
        $this->generateMs = ( microtime( true ) - $start ) * 1000;
        if ( $noted )
            expRenderStats::cacheBlockGenerationEnded();
        return array( 'scope'      => 'template-block',
                      'content'    => $content,
                      'binarydata' => $content );
    }

    /*!
     \private
     \static
     Performs processing of the cache-block using the non-compiled way and with caching off.
     */
    function processUncached( $tpl, $functionChildren, $rootNamespace, $currentNamespace )
    {
        $children = $functionChildren;

        $childTextElements = array();
        if ( is_array( $children ) )
        {
            foreach ( array_keys( $children ) as $childKey )
            {
                $child =& $children[$childKey];
                $tpl->processNode( $child, $childTextElements, $rootNamespace, $currentNamespace );
            }
        }
        $text = implode( '', $childTextElements );
        return $text;
    }

    /*!
     Returns true.
    */
    function hasChildren()
    {
        return true;
    }

    /// \privatesection
    /// Name of the function
    public $BlockName;

    /// Time the uncompiled path took to generate the last block, for the debug bar; null when it was read
    public $generateMs = null;
}

?>
