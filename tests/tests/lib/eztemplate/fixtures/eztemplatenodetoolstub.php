<?php
/**
 * The eZTemplateNodeTool stub of the tests that call noParamTransformation(). It is loaded by those tests only,
 * and they run in their own process: a stub declared while the test file is loaded would hide the real class
 * from every template test that runs after it in the same process.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file.
 * @package tests
 */

// ---------------------------------------------------------------------------
// Minimal eZTemplateNodeTool stub so noParamTransformation() tests work
// without bootstrapping the full eZ kernel.
// The real class lives at lib/eztemplate/classes/eztemplatenodtool.php.
// ---------------------------------------------------------------------------
if ( !class_exists( 'eZTemplateNodeTool', false ) )
{
    class eZTemplateNodeTool
    {
        /** An element is "constant" when it carries a 'value' key. */
        public static function isConstantElement( $element ): bool
        {
            return is_array( $element ) && array_key_exists( 'value', $element );
        }

        public static function elementConstantValue( $element )
        {
            return $element['value'];
        }

        /** Returns a node-array with a 'const' key — matches what the real class emits. */
        public static function createConstantElement( $value ): array
        {
            return [ 'const' => $value ];
        }

        /** Returns a node-array with a 'code' key — matches what the real class emits. */
        public static function createCodePieceElement( string $code, array $values ): array
        {
            return [ 'code' => $code, 'values' => $values ];
        }
    }
}
