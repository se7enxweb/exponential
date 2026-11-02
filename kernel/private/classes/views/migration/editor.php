<?php
/**
 * The migration/editor view: write and change YAML migrations in the administration. Without a parameter a new
 * migration; with an id (migration/editor/<migration id>) a YAML migration of the searched migration folders that has
 * not run yet. The text is edited as it is, or a step is built in a form made from its step type's schema (each key
 * with its type, the values it takes, the modes it belongs to); a step of the text can be loaded into the form,
 * removed or moved. Check runs the step types' own checks (what exp:migration:check does) and shows the YAML as it
 * will be saved. Save writes it through the kernel (expMigrationEditor::save()): a new migration into the site's
 * migrations folder or an extension's, never over a file and never outside those folders. The state of the page is
 * the posted form; the view parameter names the migration. Policy migration/edit.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Migration
{

class Editor extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();
        $isPost = strtoupper( (string)\eZSys::serverVariable( 'REQUEST_METHOD', true ) ) === 'POST';
        $migrationId = isset( $Params['MigrationID'] ) ? (string)$Params['MigrationID'] : '';
        $root = rtrim( (string)\eZSys::rootDir(), '/' );
        $messages = array();
        $registry = \expMigrationStepRegistry::fromSettings();
        $types = \expMigrationEditor::stepTypes( $registry );
        $locator = \expMigrationLocator::fromSettings();
        $applied = self::applied();

        $found = null;
        if ( $migrationId !== '' )
        {
            $found = \expMigrationEditor::find( $locator, $migrationId );
            if ( $found === null )
                $messages[] = array( 'kind' => 'bad', 'text' => \ezpI18n::tr( 'design/admin/migration', 'There is no YAML migration %id in the migration folders.', null, array( '%id' => $migrationId ) ) );
        }
        $migration = null;
        if ( $found !== null )
        {
            $definition = $found['definition'];
            $migration = array( 'id' => $definition->id(), 'source' => $definition->source(), 'path' => \expMigrationCheck::relative( $found['file'], $root ),
                                'applied' => isset( $applied[$definition->source() . '/' . $definition->id()] ), 'writable' => is_writable( $found['file'] ) );
        }

        $text = $isPost ? str_replace( "\r\n", "\n", (string)$http->postVariable( 'MigrationText', '' ) )
                        : ( $found !== null ? (string)file_get_contents( $found['file'] ) : \expMigrationEditor::newText( '' ) );
        $name = trim( (string)$http->postVariable( 'MigrationName', '' ) );
        $source = trim( (string)$http->postVariable( 'MigrationSource', 'site' ) );
        $builder = null;
        $builderFields = array( 'mode' => '', 'keys' => array(), 'if' => '', 'references' => '', 'environments' => '', 'engines' => '', 'comment' => '', 'unknown' => array() );
        $builderIndex = '';
        $builderList = 'steps';
        $checked = false;

        if ( $isPost )
        {
            $postedType = trim( (string)$http->postVariable( 'BuilderType', '' ) );
            $builderList = $http->postVariable( 'BuilderList', 'steps' ) === 'down' ? 'down' : 'steps';
            $builderIndex = ctype_digit( (string)$http->postVariable( 'BuilderIndex', '' ) ) ? (string)$http->postVariable( 'BuilderIndex', '' ) : '';
            $errors = array();
            if ( $http->hasPostVariable( 'ChooseTypeButton' ) )
            {
                if ( isset( $types[$postedType] ) )
                {
                    $builder = $types[$postedType];
                    $builderIndex = '';
                }
                else
                    $errors[] = \ezpI18n::tr( 'design/admin/migration', 'Choose a step type.' );
            }
            else if ( $http->hasPostVariable( 'AddStepButton' ) && isset( $types[$postedType] ) )
            {
                $fields = self::postedFields( $http );
                $step = \expMigrationEditor::stepFromForm( $postedType, $registry->get( $postedType )->schema(), $fields, $errors );
                if ( !$errors )
                {
                    $new = \expMigrationEditor::withStep( $text, $step, $builderIndex === '' ? null : (int)$builderIndex, $builderList, $errors );
                    if ( $new !== null )
                    {
                        $text = $new;
                        $messages[] = array( 'kind' => 'ok', 'text' => $builderIndex === '' ? \ezpI18n::tr( 'design/admin/migration', 'The step is added. Check the migration before you save it.' )
                                                                                            : \ezpI18n::tr( 'design/admin/migration', 'The step is changed. Check the migration before you save it.' ) );
                        $checked = true;
                        $builderIndex = '';
                    }
                }
                if ( $errors )
                {
                    $builder = $types[$postedType];
                    $builderFields = $fields + $builderFields;
                }
            }
            else if ( $http->hasPostVariable( 'EditStepButton' ) || $http->hasPostVariable( 'RemoveStepButton' ) || $http->hasPostVariable( 'MoveUpButton' ) || $http->hasPostVariable( 'MoveDownButton' ) )
            {
                $button = $http->hasPostVariable( 'EditStepButton' ) ? 'EditStepButton' : ( $http->hasPostVariable( 'RemoveStepButton' ) ? 'RemoveStepButton' : ( $http->hasPostVariable( 'MoveUpButton' ) ? 'MoveUpButton' : 'MoveDownButton' ) );
                $target = self::stepTarget( (string)$http->postVariable( $button, '' ) );
                if ( $target === null )
                    $errors[] = \ezpI18n::tr( 'design/admin/migration', 'Choose a step.' );
                else if ( $button === 'EditStepButton' )
                {
                    $document = \expMigrationEditor::document( $text, $errors );
                    $steps = $document !== null && $document[$target['list']] !== null ? $document[$target['list']] : array();
                    $step = isset( $steps[$target['index']] ) && is_array( $steps[$target['index']] ) ? $steps[$target['index']] : null;
                    if ( $step !== null && isset( $step['type'] ) && isset( $types[(string)$step['type']] ) )
                    {
                        $builder = $types[(string)$step['type']];
                        $builderFields = \expMigrationEditor::formFromStep( $registry->get( (string)$step['type'] )->schema(), $step );
                        $builderIndex = (string)$target['index'];
                        $builderList = $target['list'];
                    }
                    else if ( !$errors )
                        $errors[] = \ezpI18n::tr( 'design/admin/migration', 'This step has no form (a loop, or a step type this installation does not have): change it in the text.' );
                }
                else
                {
                    $new = \expMigrationEditor::changeStep( $text, $target['index'], $button === 'RemoveStepButton' ? 'remove' : ( $button === 'MoveUpButton' ? 'up' : 'down' ), $target['list'], $errors );
                    if ( $new !== null )
                    {
                        $text = $new;
                        $checked = true;
                    }
                }
            }
            else if ( $http->hasPostVariable( 'CheckButton' ) )
                $checked = true;
            else if ( $http->hasPostVariable( 'SaveButton' ) )
            {
                $checked = true;
                if ( $found === null && $migrationId !== '' )
                    $errors[] = \ezpI18n::tr( 'design/admin/migration', 'There is no YAML migration %id in the migration folders.', null, array( '%id' => $migrationId ) );
                else
                {
                    $options = $found !== null ? array( 'file' => $found['file'], 'applied' => $migration['applied'] )
                                               : array( 'name' => $name, 'source' => $source === '' ? 'site' : $source );
                    if ( $found === null && !in_array( $options['source'], self::sources(), true ) )
                        $errors[] = \ezpI18n::tr( 'design/admin/migration', 'Choose where the migration goes: the site or an active extension.' );
                    else
                    {
                        $saved = \expMigrationEditor::save( $text, $options + array( 'registry' => $registry, 'root' => $root ) );
                        if ( $saved['ok'] )
                        {
                            $http->setSessionVariable( 'ExpMigrationEditorMessage', \ezpI18n::tr( 'design/admin/migration', 'Saved %file. Run a dry run before the migration runs.', null, array( '%file' => $saved['path'] ) ) );
                            return $Module->redirectTo( '/migration/editor/' . ( $found !== null ? $migration['id'] : $saved['id'] ) );
                        }
                        foreach ( $saved['errors'] as $error )
                            $errors[] = \ezpI18n::tr( 'design/admin/migration', 'Not saved: %reason', null, array( '%reason' => $error ) );
                    }
                }
            }
            foreach ( $errors as $error )
                $messages[] = array( 'kind' => 'bad', 'text' => $error );
        }
        if ( $http->hasSessionVariable( 'ExpMigrationEditorMessage' ) )
        {
            $messages[] = array( 'kind' => 'ok', 'text' => (string)$http->sessionVariable( 'ExpMigrationEditorMessage' ) );
            $http->removeSessionVariable( 'ExpMigrationEditorMessage' );
            $checked = true;
        }

        // the check is always shown; $checked opens the preview after a change, a check or a save
        $check = \expMigrationEditor::check( $text, $registry );
        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'messages', $messages );
        $tpl->setVariable( 'migration', $migration );
        $tpl->setVariable( 'migration_id', $migrationId );
        $tpl->setVariable( 'text', $text );
        $tpl->setVariable( 'check', $check );
        $tpl->setVariable( 'checked', $checked );
        $tpl->setVariable( 'types', array_values( $types ) );
        $tpl->setVariable( 'builder', $builder );
        $tpl->setVariable( 'builder_fields', $builderFields );
        $tpl->setVariable( 'builder_index', $builderIndex );
        $tpl->setVariable( 'builder_list', $builderList );
        $tpl->setVariable( 'files', \expMigrationEditor::files( $locator, $applied, $root ) );
        $tpl->setVariable( 'sources', self::sources() );
        $tpl->setVariable( 'name', $name );
        $tpl->setVariable( 'source', $source );
        $tpl->setVariable( 'can_run', \expMigrationAdmin::canDo( 'run' ) );
        $tpl->setVariable( 'can_rollback', \expMigrationAdmin::canDo( 'rollback' ) );
        $tpl->setVariable( 'view', 'editor' );
        $path = array( array( 'url' => 'setup/menu', 'text' => \ezpI18n::tr( 'design/admin/migration', 'Setup' ) ),
                       array( 'url' => 'migration/status', 'text' => \ezpI18n::tr( 'design/admin/migration', 'Migrations' ) ),
                       array( 'url' => $migration !== null ? 'migration/editor' : false, 'text' => \ezpI18n::tr( 'design/admin/migration', 'Editor' ) ) );
        if ( $migration !== null )
            $path[] = array( 'url' => false, 'text' => $migration['id'] );
        return array( 'content' => $tpl->fetch( 'design:migration/editor.tpl' ), 'path' => $path );
    }

    /**
     * The fields of the step form as posted.
     *
     * @param \eZHTTPTool $http
     * @return array
     */
    protected static function postedFields( $http )
    {
        $keys = $http->postVariable( 'BuilderKey', array() );
        $fields = array( 'mode' => (string)$http->postVariable( 'BuilderMode', '' ), 'keys' => array() );
        foreach ( is_array( $keys ) ? $keys : array() as $key => $value )
            if ( is_string( $key ) && is_string( $value ) )
                $fields['keys'][$key] = $value;
        foreach ( array( 'if' => 'BuilderIf', 'references' => 'BuilderReferences', 'environments' => 'BuilderEnvironments', 'engines' => 'BuilderEngines', 'comment' => 'BuilderComment' ) as $field => $post )
            $fields[$field] = (string)$http->postVariable( $post, '' );
        return $fields;
    }

    /**
     * The step a button names: "steps:3" or "down:0". Pure.
     *
     * @param string $value
     * @return array|null list, index
     */
    public static function stepTarget( $value )
    {
        if ( !preg_match( '/^(steps|down):(\d{1,5})$/', (string)$value, $m ) )
            return null;
        return array( 'list' => $m[1], 'index' => (int)$m[2] );
    }

    /** @return string[] where a new migration can go: site and extension:<name> of every active extension */
    public static function sources()
    {
        $sources = array( 'site' );
        foreach ( \eZExtension::activeExtensions() as $extension )
            $sources[] = 'extension:' . $extension;
        return $sources;
    }

    /** @return array source/id => true for the migrations that have run (state done) */
    protected static function applied()
    {
        $applied = array();
        try
        {
            $db = \eZDB::instance();
            if ( \expMigrationRecord::exists( $db ) )
                foreach ( \expMigrationRecord::all( $db ) as $row )
                    if ( $row['state'] === \expMigration::DONE )
                        $applied[$row['source'] . '/' . $row['migration_id']] = true;
        }
        catch ( \Throwable $e )
        {
            \eZDebug::writeWarning( 'The migration records cannot be read: ' . $e->getMessage(), __METHOD__ );
        }
        return $applied;
    }
}

}
?>
