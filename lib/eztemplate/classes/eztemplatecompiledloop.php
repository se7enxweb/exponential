<?php
/**
 * File containing the eZTemplateCompiledLoop class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package lib
 */

/*!
  \class eZTemplateCompiledLoop eztemplatecompiledloop.php
  \ingroup eZTemplateFunctions
  \brief Common code for compiling the loop functions
*/
class eZTemplateCompiledLoop
{
    /**
     * Constructor
     *
     * @param string $name
     * @param array $newNodes
     * @param array $parameters
     * @param $nodePlacement
     * @param string $uniqid
     * @param mixed $node
     * @param eZTemplate $tpl
     * @param array $privateData
     */
    public function __construct( $name, &$newNodes, $parameters, $nodePlacement, $uniqid, $node, $tpl, $privateData )
    {
        $this->Name          = $name;
        $this->Parameters    = $parameters;
        $this->NodePlacement = $nodePlacement;
        $this->UniqID        = $uniqid;
        $this->NewNodes      =& $newNodes;
        $this->Node          = $node;
        $this->Tpl           = $tpl;
        $this->PrivateData   = $privateData;
    }

    /*!
     * Returns true if sequence has been specified for the loop in its parameters.
     */
    function hasSequence()
    {
        return isset( $this->Parameters['sequence_var'] );
    }

    /**
     * The PHP variables of the loop's sequence, named after the loop: a loop that saves its state for a template that
     * includes itself (eZTemplateForeachFunction, eZTemplateForFunction) saves them too.
     *
     * @return string[]
     */
    function sequenceVariableNames()
    {
        return array( "{$this->Name}_sequence_array_{$this->UniqID}", "{$this->Name}_sequence_var_{$this->UniqID}" );
    }

    /**
     * Destroys PHP and template variables defined by the loop.
     *
     * @param bool $templateVariable also the template variable of the sequence; false for a loop that saves its state
     *                               for a template that includes itself: it removes the template variable with
     *                               sequenceTemplateVariableCleanup() once no loop of its code runs any more, so the
     *                               loop outside still reads it after the include, as when interpreted
     */
    function cleanup( $templateVariable = true )
    {
        if ( $this->hasSequence() )
            $this->destroySequenceVars( $templateVariable );

        $this->NewNodes[] = eZTemplateNodeTool::createCodePieceNode( "\$skipDelimiter = false;" );
    }

    /**
     * For a loop whose only PHP state is its sequence ({while}, {do}): saves the sequence's PHP variables before
     * initVars() when a loop of the same code runs (a template that includes itself runs the same code in the same
     * scope), so the include does not reset or unset them under the loop outside. Call restoreSequenceState() after
     * cleanup( false ).
     */
    function saveSequenceState()
    {
        if ( !$this->hasSequence() )
            return;
        $stack = "{$this->Name}_sequence_stack_{$this->UniqID}";
        $this->NewNodes[] = eZTemplateNodeTool::createCodePieceNode( "if ( isset( \$$stack ) ) \$" . $stack . "[] = compact( '" . implode( "', '", $this->sequenceVariableNames() ) . "' );\n" .
                                                                     "else \$$stack = [];" );
    }

    /**
     * Restores what saveSequenceState() saved, or removes the sequence's template variable and the stack when no loop
     * of this code runs any more.
     */
    function restoreSequenceState()
    {
        if ( !$this->hasSequence() )
            return;
        $stack = "{$this->Name}_sequence_stack_{$this->UniqID}";
        $this->NewNodes[] = eZTemplateNodeTool::createCodePieceNode( "if ( count( \$$stack ) ) extract( array_pop( \$$stack ) );\nelse\n{\n" );
        $this->sequenceTemplateVariableCleanup();
        $this->NewNodes[] = eZTemplateNodeTool::createVariableUnsetNode( $stack );
        $this->NewNodes[] = eZTemplateNodeTool::createCodePieceNode( "}\n" );
    }

    /**
     * Removes the template variable of the sequence (see cleanup()).
     */
    function sequenceTemplateVariableCleanup()
    {
        if ( $this->hasSequence() )
            $this->NewNodes[] = eZTemplateNodeTool::createVariableUnsetNode( $this->Parameters['sequence_var'][0][1] );
    }

    /*!
    \private
    */
    function destroySequenceVars( $templateVariable = true )
    {
        $fName      = $this->Name;
        $uniqid     = $this->UniqID;
        $this->NewNodes[] = eZTemplateNodeTool::createVariableUnsetNode( "{$fName}_sequence_array_$uniqid" );
        $this->NewNodes[] = eZTemplateNodeTool::createVariableUnsetNode( "{$fName}_sequence_var_$uniqid" );
        if ( $templateVariable )
            $this->NewNodes[] = eZTemplateNodeTool::createVariableUnsetNode( $this->Parameters['sequence_var'][0][1] );
    }


    /*!
     * Create PHP and template variables representing sequence specified for the loop.
     */
    function createSequenceVars()
    {
        if ( !$this->hasSequence() )
            return;

        $fName      = $this->Name;
        $uniqid     = $this->UniqID;
        $this->NewNodes[] = eZTemplateNodeTool::createCodePieceNode( "// creating sequence variables for \{$fName} loop" );
        $this->NewNodes[] = eZTemplateNodeTool::createVariableNode( false,
                                                                    $this->Parameters['sequence_array'],
                                                                    $this->NodePlacement,
                                                                    array( 'treat-value-as-non-object' => true, 'text-result' => false ),
                                                                    "{$fName}_sequence_array_$uniqid" );
        $this->NewNodes[] = eZTemplateNodeTool::createCodePieceNode( "\${$fName}_sequence_var_$uniqid = current( \${$fName}_sequence_array_$uniqid );\n" );
    }

    /*!
     * Export current sequence value to the template variable specified in loop parameters.
     */
    function setCurrentSequenceValue()
    {
        if ( !$this->hasSequence() )
            return;

        $fName    = $this->Name;
        $uniqid   = $this->UniqID;
        $seqVar   = "{$fName}_sequence_var_$uniqid";
        $this->NewNodes[] = eZTemplateNodeTool::createCodePieceNode( "// setting current sequence value" );
        $this->NewNodes[] = eZTemplateNodeTool::createVariableNode( false, $seqVar, $this->NodePlacement, array(),
                                                                    $this->Parameters['sequence_var'][0][1],
                                                                    false, true, true );
    }

    /*!
     * Increments loop sequence.
     */
    function iterateSequence()
    {
        if ( !$this->hasSequence() )
            return;

        $fName    = $this->Name;
        $uniqid   = $this->UniqID;
        $seqArray = "{$fName}_sequence_array_$uniqid";
        $seqVar   = "{$fName}_sequence_var_$uniqid";
        $alterSeqValCode =
            "if ( ( \$$seqVar = next( \$$seqArray ) ) === false )\n" .
            "{\n" .
            "   reset( \$$seqArray );\n" .
            "   \$$seqVar = current( \$$seqArray );\n" .
            "}\n";
        $this->NewNodes[] = eZTemplateNodeTool::createCodePieceNode( "// sequence iteration" );
        $this->NewNodes[] = eZTemplateNodeTool::createCodePieceNode( $alterSeqValCode );
    }

    /*
     * Compiles loop children (=code residing between start and end tags of the loop).
     * Besides, does special handling of {break}, {continue}, {skip} and {delimiter} functions.
     * \return true if the caller loop should break, false otherwise
     */
    function processChildren()
    {
        // process the loop body
        $children            = eZTemplateNodeTool::extractFunctionNodeChildren( $this->Node );
        $transformedChildren = eZTemplateCompiler::processNodeTransformationNodes( $this->Tpl, $this->Node, $children, $this->PrivateData );

        $childrenNodes = array();
        $delimiter = null;

        if ( is_array( $transformedChildren ) )
        {
            foreach ( $transformedChildren as $child )
            {
                if ( $child[0] == eZTemplate::NODE_FUNCTION ) // check child type
                {
                    $childFunctionName = $child[2];
                    if ( $childFunctionName == 'delimiter' )
                    {
                        // save delimiter for it to be processed below
                        $delimiter = $child;
                        continue;
                    }
                    elseif ( $childFunctionName == 'break' )
                    {
                        $childrenNodes[] = eZTemplateNodeTool::createCodePieceNode( "break;\n" );
                        continue;
                    }
                    elseif ( $childFunctionName == 'continue' )
                    {
                        $childrenNodes[] = eZTemplateNodeTool::createCodePieceNode( "continue;\n" );
                        continue;
                    }
                    elseif ( $childFunctionName == 'skip' )
                    {
                        $childrenNodes[] = eZTemplateNodeTool::createCodePieceNode( "\$skipDelimiter = true;\ncontinue;\n" );
                        continue;
                    }
                }

                $childrenNodes[] = $child;
            }
        }

        if ( $delimiter ) // if delimiter is specified
        {
            $delimiterNodes = eZTemplateNodeTool::extractNodes( $children,
                                                    array( 'match' => array( 'type' => 'equal',
                                                                             'matches' => array( array( 'match-keys' => array( 0 ),
                                                                                                       'match-with' => eZTemplate::NODE_FUNCTION ),
                                                                                                 array( 'match-keys' => array( 2 ),
                                                                                                        'match-with' => 'delimiter' ) ) ) ) );
            $delimiterNode = false;
            if ( count( $delimiterNodes ) > 0 )
                $delimiterNode = $delimiterNodes[0];

            $delimiterChildren = eZTemplateNodeTool::extractFunctionNodeChildren( $delimiterNode );
            $delimiterParameters = eZTemplateNodeTool::extractFunctionNodeParameters( $delimiterNode );

            $checkModulo = array();
            $checkModuloEnd = array();
            $delemiterModuloValue = array();
            if ( isset( $delimiterParameters['modulo'] ) )
            {
                switch ( $this->Name )
                {
                    case 'foreach':
                    {
                        $delimiterModulo = $delimiterParameters['modulo'];
                        $delimiterModulo = eZTemplateCompiler::processElementTransformationList( $this->Tpl, $delimiterModulo, $delimiterModulo, $this->PrivateData );
                        // Get unique index
                        $currentIndex = "\$fe_i_$this->UniqID";

                        if ( eZTemplateNodeTool::isConstantElement( $delimiterModulo ) )
                        {
                            $moduloValue = (int)eZTemplateNodeTool::elementConstantValue( $delimiterModulo );
                            $matchCode = "( ( $currentIndex ) % $moduloValue ) == 0";
                        }
                        else
                        {
                            $delemiterModuloValue[] = eZTemplateNodeTool::createVariableNode( false, $delimiterModulo, eZTemplateNodeTool::extractFunctionNodePlacement( $this->Node ),
                                                                                        array( 'spacing' => 0 ), 'moduloValue' );
                            $matchCode = "( ( $currentIndex ) % \$moduloValue ) == 0";
                        }
                        $checkModulo[] = eZTemplateNodeTool::createCodePieceNode( "if ( $matchCode ) // Check modulo\n{" );
                        $checkModulo[] = eZTemplateNodeTool::createSpacingIncreaseNode( 4 );

                        $checkModuloEnd[] = eZTemplateNodeTool::createSpacingDecreaseNode( 4 );
                        $checkModuloEnd[] = eZTemplateNodeTool::createCodePieceNode( "}\n" );
                    }
                }
            }
            $delimiterNodes = array();
            $delimiterNodes[] = eZTemplateNodeTool::createCodePieceNode( "if ( \$skipDelimiter )\n" .
                                                                         "    \$skipDelimiter = false;\n" .
                                                                         "else\n" .
                                                                         "{ // delimiter begins" );
            $delimiterNodes[] = eZTemplateNodeTool::createSpacingIncreaseNode();
            if ( is_array( $delimiter[1] ) ) // if delimiter has children
            {
                // If modulo is specified
                $delimiterNodes = array_merge( $delimiterNodes, $checkModulo );

                foreach ( $delimiter[1] as $delimiterChild )
                    $delimiterNodes[] = $delimiterChild;

                // Set end of checking for modulo
                $delimiterNodes = array_merge( $delimiterNodes, $checkModuloEnd );
            }

            $delimiterNodes[] = eZTemplateNodeTool::createSpacingDecreaseNode();
            $delimiterNodes[] = eZTemplateNodeTool::createCodePieceNode( "} // delimiter ends\n" );

            // we place its code right before other loop children,
            // if delemiter and modulo are specified and value of modulo is not static
            // $delemiterModuloValue is initialization of variable
            // we should place initialization of moduloValue before checking for delimiter
            $childrenNodes = array_merge( $delemiterModuloValue, $delimiterNodes, $childrenNodes );
        }

        $this->NewNodes = array_merge( $this->NewNodes, $childrenNodes );
    }

    /*!
     * Generates loop body.
     */
    function processBody()
    {
        // export current sequence value to the specified template variable <$sequence_var>
        $this->setCurrentSequenceValue();

        // process the loop body
        $this->processChildren();

        $this->iterateSequence();
    }

    /*!
     * create PHP and template variables needed for the loop.
     */
    function initVars()
    {
        // initialize delimiter processing
        $this->NewNodes[] = eZTemplateNodeTool::createCodePieceNode( "\$skipDelimiter = true;" );

        // initialize sequence
        $this->createSequenceVars();
    }

    ///
    /// \privatesection
    ///
    public $Name;
    public $Parameters;
    public $NodePlacement;
    public $UniqID;
    public $NewNodes;
    public $Node;
    public $Tpl;
    public $PrivateData;

}

?>
