<?php
/**
 * The common base of the ezoe tests: the live installation (admin siteaccess, admin user), no test database.
 * The ezoe.ini values are changed in memory only and the admin's engine preference is put back in tearDown.
 *
 * Run: php vendor/bin/phpunit tests/tests/extension/ezoe/
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/../expservices/core/expServicesCoreTestCase.php';

/** A third engine, the way an extension would add one: one class, one ini line. */
class expOETestThirdEngine extends expOEEditorEngineBase
{
    public function identifier() { return 'third'; }
    public function label() { return 'Third editor'; }
    public function template() { return 'design:content/datatype/edit/ezxmltext_third.tpl'; }
    public function config( array $context = array() ) { return array( 'attribute' => isset( $context['attribute_id'] ) ? $context['attribute_id'] : 0 ); }
}

class expOETestNotAnEngine {}

class expOETestWrongIdEngine extends expOEEditorEngineBase
{
    public function identifier() { return 'other'; }
    public function label() { return 'Other'; }
}

class expOETestUnavailableEngine extends expOEEditorEngineBase
{
    public function identifier() { return 'gone'; }
    public function label() { return 'Gone'; }
    public function isAvailable() { return false; }
}

class expOETestThrowingEngine extends expOEEditorEngineBase
{
    public function __construct() { throw new RuntimeException( 'cannot start' ); }
    public function identifier() { return 'throws'; }
    public function label() { return 'Throws'; }
}

abstract class expOETestCase extends expServicesCoreTestCase
{
    protected $savedEngines = null;
    protected $savedConfigured = null;
    protected $savedCheck = null;
    protected $savedExtensions = null;
    protected $savedSwitch = null;
    protected $savedPreference = false;

    public function setUp(): void
    {
        parent::setUp();
        if ( !class_exists( 'expOEEditor' ) )
            $this->markTestSkipped( 'ezoe is not loaded' );
        $ini = eZINI::instance( 'ezoe.ini' );
        $this->savedEngines = $ini->variable( 'EditorSettings', 'Engines' );
        $this->savedConfigured = $ini->variable( 'EditorSettings', 'EditorEngine' );
        $this->savedCheck = $ini->hasVariable( 'EditorSettings', 'UploadExtensionCheck' ) ? $ini->variable( 'EditorSettings', 'UploadExtensionCheck' ) : 'engine';
        $this->savedExtensions = $ini->variable( 'EditorSettings', 'UploadFileExtensions' );
        $this->savedSwitch = $ini->variable( 'EditorSettings', 'EngineSwitch' );
        $this->savedPreference = $this->dbPreference();
        expOEEditor::reset();
    }

    public function tearDown(): void
    {
        if ( class_exists( 'expOEEditor' ) && $this->savedEngines !== null )
        {
            $ini = eZINI::instance( 'ezoe.ini' );
            $ini->setVariable( 'EditorSettings', 'Engines', $this->savedEngines );
            $ini->setVariable( 'EditorSettings', 'EditorEngine', $this->savedConfigured );
            $ini->setVariable( 'EditorSettings', 'UploadExtensionCheck', $this->savedCheck );
            $ini->setVariable( 'EditorSettings', 'UploadFileExtensions', $this->savedExtensions );
            $ini->setVariable( 'EditorSettings', 'EngineSwitch', $this->savedSwitch );
            $this->loginAdmin();
            // put the admin's stored preference back exactly as it was (no row stays no row)
            $admin = eZUser::fetchByName( 'admin' );
            if ( $this->savedPreference === false )
                eZDB::instance()->query( 'DELETE FROM ezpreferences WHERE user_id = ' . (int) $admin->attribute( 'contentobject_id' ) . " AND name = 'ezoe_engine'" );
            else
                eZPreferences::setValue( expOEEditor::PREFERENCE, $this->savedPreference );
            expOEEditor::reset();
        }
        parent::tearDown();
    }

    protected function ini( $name, $value )
    {
        eZINI::instance( 'ezoe.ini' )->setVariable( 'EditorSettings', $name, $value );
        expOEEditor::reset();
    }

    /** The stored preference of the admin straight from the database (the query cache and the session are bypassed); false when there is none. */
    protected function dbPreference()
    {
        $admin = eZUser::fetchByName( 'admin' );
        $rows = eZDB::instance()->arrayQuery( 'SELECT value, ' . mt_rand() . ' AS nonce FROM ezpreferences WHERE user_id = ' . (int) $admin->attribute( 'contentobject_id' ) . " AND name = 'ezoe_engine'" );
        return $rows ? $rows[0]['value'] : false;
    }

    protected function preference( $value )
    {
        eZPreferences::setValue( expOEEditor::PREFERENCE, $value );
    }

    /** Runs a view of the ezoe module in a process of its own (see helpers/run_ezoe_view.php). */
    protected function runView( $view, array $params = array(), array $post = array(), $user = 'admin' )
    {
        $helper = __DIR__ . '/helpers/run_ezoe_view.php';
        $cmd = escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( $helper ) . ' ' . escapeshellarg( $view ) . ' '
             . escapeshellarg( json_encode( $params ) ) . ' ' . escapeshellarg( json_encode( (object) $post ) ) . ' ' . escapeshellarg( $user ) . ' 2>/dev/null';
        return (string) shell_exec( $cmd );
    }
}
