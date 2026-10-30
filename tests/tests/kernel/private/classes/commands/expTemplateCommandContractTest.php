<?php
/**
 * The stability contract of the template commands (exp:template:*; doc/eng-us/guides/template-syntax-check.md,
 * "Compatibility promise"): command names and aliases, option names, exit codes, rule ids, engine values and the
 * fields of the machine output schema exp-template-check/1 are permanent.
 *
 * NOTE for whoever changes this file: an ADDITION (a new alias, option, rule id, exit code above 4, schema field) is
 * added to the snapshot below in the same commit. A REMOVAL or RENAME is not allowed: a released name stays, a
 * better one is added as an alias, a rule is retired (it never fires) but its id stays listed here.
 *
 * No database, no siteaccess, no engine library.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package tests
 * @group kernel
 */

class expTemplateCommandContractTest extends PHPUnit\Framework\TestCase
{
    /** bin script => its aliases */
    const COMMANDS = array(
        'exptemplateconvert' => array( 'exp:template:convert' ),
        'exptemplateconverttpltostpl' => array( 'exp:template:convert:tpl-to-stpl' ),
        'exptemplateconvertstpltotpl' => array( 'exp:template:convert:stpl-to-tpl' ),
        'exptemplateconverttpltotwig' => array( 'exp:template:convert:tpl-to-twig' ),
        'exptemplateconverttwigtotpl' => array( 'exp:template:convert:twig-to-tpl' ),
        'exptemplateconvertstpltotwig' => array( 'exp:template:convert:stpl-to-twig' ),
        'exptemplateconverttwigtostpl' => array( 'exp:template:convert:twig-to-stpl' ),
        'exptemplateconvertdiff' => array( 'exp:template:convert:diff' ),
        'exptemplatecheck' => array( 'exp:template:check', 'exp:template:lint', 'exp:template:convert:check' ),
        'exptemplateengines' => array( 'exp:template:engines' ),
        'eztemplatecheck' => array(),
    );

    /** command class file => its option names */
    const OPTIONS = array(
        'exptemplateconvert' => array( 'from-engine', 'to-engine', 'from', 'to', 'file', 'override-ini', 'twig-suffix', 'dry-run', 'force' ),
        'exptemplateconverttpltostpl' => array( 'from', 'to', 'file', 'override-ini', 'dry-run', 'force' ),
        'exptemplateconvertstpltotpl' => array( 'from', 'to', 'file', 'override-ini', 'dry-run', 'force' ),
        'exptemplateconverttpltotwig' => array( 'from', 'to', 'file', 'override-ini', 'twig-suffix', 'dry-run', 'force' ),
        'exptemplateconverttwigtotpl' => array( 'from', 'to', 'file', 'override-ini', 'dry-run', 'force' ),
        'exptemplateconvertstpltotwig' => array( 'from', 'to', 'file', 'override-ini', 'twig-suffix', 'dry-run', 'force' ),
        'exptemplateconverttwigtostpl' => array( 'from', 'to', 'file', 'override-ini', 'dry-run', 'force' ),
        'exptemplateconvertdiff' => array( 'file', 'from-engine', 'to-engine', 'against' ),
        'exptemplatecheck' => array( 'design', 'design-dir', 'extension', 'extensions', 'all-extensions', 'siteaccess', 'all-siteaccesses', 'engine',
                                     'suffix-map', 'level', 'security', 'includes', 'overrides', 'parity', 'deprecations', 'encoding', 'max-depth',
                                     'format', 'output', 'color', 'no-progress', 'progress', 'summary-by', 'fail-on', 'baseline', 'generate-baseline',
                                     'ignore', 'rules', 'explain', 'schema', 'stdin-engine', 'stdin-name', 'allow-missing-engine', 'keep-compiled',
                                     'jobs', 'changed-since', 'files-from' ),
        'exptemplateengines' => array( 'format' ),
    );

    const CHECK_EXIT_CODES = array( 'EXIT_OK' => 0, 'EXIT_FINDINGS' => 1, 'EXIT_USAGE' => 2, 'EXIT_UNAVAILABLE' => 3, 'EXIT_INTERNAL' => 4 );
    const CONVERT_EXIT_CODES = array( 'EXIT_OK' => 0, 'EXIT_ERRORS' => 1, 'EXIT_USAGE' => 2, 'EXIT_UNAVAILABLE' => 3 );

    const RULES = array(
        'TPL-SYNTAX-001', 'TPL-SYNTAX-002', 'TPL-COMPILE-001', 'TPL-STRICT-INCLUDE-MISSING', 'STPL-SYNTAX-001', 'STPL-COMPILE-001',
        'STPL-SECURITY-001', 'STPL-STRICT-INCLUDE-MISSING', 'TWIG-SYNTAX-001', 'TWIG-COMPILE-001', 'TWIG-SECURITY-001',
        'TPL-STRICT-UNKNOWN-OPERATOR', 'TWIG-STRICT-UNKNOWN-OPERATOR', 'TWIG-STRICT-INCLUDE-MISSING', 'TWIG-DEPRECATION-001', 'ENCODING-UTF8-001', 'ENCODING-BOM-002',
        'ENCODING-CRLF-003', 'OVERRIDE-MISSING-FILE', 'OVERRIDE-ENGINE-MISMATCH', 'PARITY-PAIR', 'ENGINE-UNAVAILABLE', 'CHECK-READ',
        'CHECK-INTERNAL-ERROR',
    );

    const ENGINES = array( 'tpl' => 'tpl', 'exp' => 'tpl', 'exponential' => 'tpl', 'stpl' => 'stpl', 'smarty' => 'stpl', 'twig' => 'twig' );

    const SCHEMA_FIELDS = array(
        'document' => array( 'schema', 'summary', 'findings', 'files' ),
        'summary' => array( 'files', 'ok', 'failed', 'skipped', 'errors', 'warnings', 'notes', 'engines', 'unavailable', 'seconds', 'level', 'exit', 'result' ),
        'finding' => array( 'rule', 'severity', 'engine', 'file', 'line', 'column', 'message', 'hint', 'source', 'belongs' ),
        'file' => array( 'file', 'engine', 'status', 'ms', 'belongs' ),
    );

    public static function setUpBeforeClass(): void
    {
        chdir( dirname( __DIR__, 6 ) );
    }

    private static function sameSet( array $expected, array $actual, $what )
    {
        $missing = array_values( array_diff( $expected, $actual ) );
        $added = array_values( array_diff( $actual, $expected ) );
        self::assertSame( array(), $missing, "$what removed or renamed: not allowed (keep it, add the new name as an alias)" );
        self::assertSame( array(), $added, "$what added: add it to the snapshot of expTemplateCommandContractTest in the same commit" );
    }

    public function testCommandNamesAndAliases()
    {
        foreach ( self::COMMANDS as $script => $aliases )
        {
            $this->assertFileExists( "bin/php/$script.php" );
            preg_match_all( '/^\s*\*\s*@alias\s+(\S+)$/m', file_get_contents( "bin/php/$script.php" ), $m );
            self::sameSet( $aliases, $m[1], "an alias of bin/php/$script.php" );
            if ( $aliases )
            {
                preg_match_all( '/^\s*\*\s*@alias\s+(\S+)$/m', file_get_contents( "kernel/private/classes/commands/$script.php" ), $m );
                self::sameSet( $aliases, $m[1], "an alias of the command class $script" );
            }
        }
        $all = array();
        foreach ( glob( 'bin/php/*.php' ) as $file )
        {
            preg_match_all( '/^\s*\*\s*@alias\s+(exp:template:\S+)$/m', file_get_contents( $file ), $m );
            $all = array_merge( $all, $m[1] );
        }
        self::sameSet( array_merge( ...array_values( self::COMMANDS ) ), $all, 'an exp:template:* command' );
    }

    public function testOptionNames()
    {
        foreach ( self::OPTIONS as $script => $options )
        {
            $code = file_get_contents( "kernel/private/classes/commands/$script.php" );
            if ( preg_match( "/const OPTIONS = ((?:'[^']*'\\s*\\.?\\s*)+);/", $code, $m ) )
                $config = implode( '', preg_match_all( "/'([^']*)'/", $m[1], $parts ) ? $parts[1] : array() );
            else
            {
                $this->assertSame( 1, preg_match( "/startup\\( '([^']*)'/", $code, $m ), $script );
                $config = $m[1];
            }
            preg_match_all( '/\[([a-z-]+)[:;]?\*?\]/', $config, $names );
            self::sameSet( $options, $names[1], "an option of $script" );
        }
    }

    public function testExitCodesRulesEnginesAndSchema()
    {
        foreach ( self::CHECK_EXIT_CODES as $name => $value )
            $this->assertSame( $value, constant( "expTemplateChecker::$name" ) );
        foreach ( self::CONVERT_EXIT_CODES as $name => $value )
            $this->assertSame( $value, constant( "expTemplateDesignConverter::$name" ) );
        self::sameSet( self::RULES, array_keys( expTemplateChecker::RULES ), 'a rule id' );
        foreach ( self::ENGINES as $value => $engine )
            $this->assertSame( $engine, expTemplateDesignConverter::engine( $value ), "the engine value $value" );
        $this->assertSame( 'exp-template-check/1', expTemplateChecker::SCHEMA );

        $formatter = new expTemplateCheckFormatter(
            array( array( 'rule' => 'TPL-SYNTAX-001', 'severity' => 'ERROR', 'engine' => 'tpl', 'file' => 'a.tpl', 'line' => 1, 'column' => 2,
                          'message' => 'm', 'hint' => null, 'source' => 's', 'belongs' => array() ) ),
            array( 'a.tpl' => array( 'engine' => 'tpl', 'ms' => 1, 'status' => 'ERROR', 'belongs' => array() ) ),
            array( 'files' => 1, 'ok' => 0, 'failed' => 1, 'skipped' => 0, 'errors' => 1, 'warnings' => 0, 'notes' => 0, 'engines' => array( 'tpl' => 1 ),
                   'unavailable' => array(), 'seconds' => 0.1, 'level' => 'compile', 'exit' => 1, 'result' => 'FAIL' ) );
        $document = $formatter->document();
        self::sameSet( self::SCHEMA_FIELDS['document'], array_keys( $document ), 'a field of the document' );
        self::sameSet( self::SCHEMA_FIELDS['finding'], array_keys( $document['findings'][0] ), 'a field of a finding' );
        self::sameSet( self::SCHEMA_FIELDS['file'], array_keys( $document['files'][0] ), 'a field of a file' );
        self::sameSet( self::SCHEMA_FIELDS['summary'], array_keys( $document['summary'] ), 'a field of the summary' );
    }
}
