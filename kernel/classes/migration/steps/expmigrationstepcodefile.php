<?php
/**
 * File containing the expMigrationStepCodeFile class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The step type "code_file": writes, replaces or removes one code or design file of the installation (a template, a
 * stylesheet, a script, an extension's file), below the folders migration.ini [FileStepSettings] AllowedRootList[]
 * names (design and extension by default) and nowhere else.
 *
 *   - type: code_file
 *     mode: ensure                          create (the file must not exist), update (it must), ensure, delete
 *     path: design/mysite/templates/page_footer.tpl   relative to the installation, below an allowed folder
 *     source: files/page_footer.tpl         a file next to the migration (below its folder), or
 *     content: "..."                        the content itself
 *     checksum: 3f1c...                     optional: the SHA-256 the new content must have
 *     expect: 9a0b...                       optional: the SHA-256 the file must have before (absent: it must not exist)
 *
 * The file as it was is copied first (var/backup/migration/files/<migration id>/<path>, readable by its owner only)
 * and the change listed with its SHA-256 before and after (expMigrationFiles); the write is atomic and keeps the
 * file's mode. When a later step of the same migration fails, the runner puts the file back. The step undoes itself:
 * a rollback of the migration puts the old file back (or removes the new one), but only while the file is still the
 * one the migration wrote.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationStepCodeFile extends expMigrationStepTypeBase implements expMigrationRevertibleStepType
{
    /** The modes. */
    const MODES = array( 'create', 'update', 'ensure', 'delete' );

    /** @var string[]|null the allowed roots (tests) */
    protected $roots;

    /**
     * @param string[]|null $roots the allowed roots instead of migration.ini's (tests)
     */
    public function __construct( ?array $roots = null )
    {
        $this->roots = $roots;
    }

    /** @return string */
    public function name()
    {
        return 'code_file';
    }

    /** @return array */
    public function schema()
    {
        return array(
            'description' => 'Writes, replaces or removes one code or design file below the allowed folders, with a backup and checksums',
            'modes' => self::MODES,
            'mode' => 'ensure',
            'keys' => array(
                'path' => array( 'type' => 'string', 'required' => true,
                                 'description' => 'the file, relative to the installation, below a folder of [FileStepSettings] AllowedRootList[] (design, extension)' ),
                'source' => array( 'type' => 'string', 'modes' => array( 'create', 'update', 'ensure' ),
                                   'description' => 'the new content: a file below the folder of the migration file (files/page.tpl)' ),
                'content' => array( 'type' => 'string', 'modes' => array( 'create', 'update', 'ensure' ),
                                    'description' => 'the new content itself (instead of source:)' ),
                'checksum' => array( 'type' => 'string', 'modes' => array( 'create', 'update', 'ensure' ),
                                     'description' => 'the SHA-256 the new content must have (64 hex digits)' ),
                'expect' => array( 'type' => 'string',
                                   'description' => 'the SHA-256 the file must have before the step, or absent: it must not exist' ),
            ),
            'values' => array(
                'path' => 'the file, relative to the installation',
                'changed' => 'true when the file changed',
                'before' => 'its SHA-256 before (empty: it did not exist)',
                'after' => 'its SHA-256 after (empty: removed)',
            ),
            'example' => "- type: code_file\n  mode: ensure\n  path: design/mysite/templates/page_footer.tpl\n  source: files/page_footer.tpl\n",
        );
    }

    /** @return string[] */
    protected function roots()
    {
        return $this->roots !== null ? $this->roots : expMigrationFiles::allowedRoots();
    }

    /**
     * @param array $step
     * @return string[]
     */
    public function validate( array $step )
    {
        $errors = array();
        $mode = isset( $step['mode'] ) ? (string)$step['mode'] : 'ensure';
        if ( isset( $step['path'] ) && !expMigrationReferences::contains( $step['path'] ) )
        {
            $problem = expMigrationFiles::pathProblem( (string)$step['path'], $this->roots() );
            if ( $problem !== null )
                $errors[] = "path: $problem";
        }
        $has = (int)isset( $step['source'] ) + (int)isset( $step['content'] );
        if ( $mode !== 'delete' && $has !== 1 )
            $errors[] = 'source: or content: (one of them): the new content';
        if ( isset( $step['source'] ) && !expMigrationReferences::contains( $step['source'] ) )
        {
            $problem = expMigrationStepFile::fileProblem( (string)$step['source'] );
            if ( $problem !== null )
                $errors[] = "source: $problem";
        }
        foreach ( array( 'checksum', 'expect' ) as $key )
            if ( isset( $step[$key] ) && !expMigrationReferences::contains( $step[$key] )
                 && !( $key === 'expect' && $step[$key] === 'absent' ) && !preg_match( '/^[0-9a-f]{64}$/', strtolower( (string)$step[$key] ) ) )
                $errors[] = "$key: a SHA-256 (64 hex digits)" . ( $key === 'expect' ? ', or absent' : '' );
        if ( $mode === 'create' && isset( $step['expect'] ) && $step['expect'] !== 'absent' )
            $errors[] = 'expect: a file of mode create does not exist before (expect: absent, or leave it out)';
        return $errors;
    }

    /**
     * The caches a file affects, by its name: templates, settings, packed stylesheets and scripts. Pure.
     *
     * @param array $step
     * @return string[]
     */
    public function caches( array $step )
    {
        $path = isset( $step['path'] ) ? strtolower( (string)$step['path'] ) : '';
        $suffix = pathinfo( $path, PATHINFO_EXTENSION );
        if ( $suffix === 'tpl' )
            return array( 'template', 'template-override', 'template-block' );
        if ( $suffix === 'ini' || substr( $path, -11 ) === '.append.php' )
            return array( 'tag:ini' );
        if ( in_array( $suffix, array( 'css', 'js' ), true ) )
            return array( 'ezjscore-packer', 'template-block' );
        return array();
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function plan( array $step, $context )
    {
        $mode = isset( $step['mode'] ) ? (string)$step['mode'] : 'ensure';
        $path = expMigrationFiles::normalize( (string)$step['path'] );
        $file = expMigrationFiles::resolve( $path, $context->root(), $this->roots() );
        $before = expMigrationFiles::checksum( $file );
        $problem = $this->stateProblem( $step, $mode, $before );
        if ( $problem !== null )
            return array( "$path: $problem (the run would fail)" );
        if ( $mode === 'delete' )
            return array( $before === null ? "$path: not there, nothing to remove" : "remove $path (a backup first)" );
        $content = $this->content( $step, $context );
        if ( $before === hash( 'sha256', $content ) )
            return array( "$path: is so already, nothing to write" );
        return array( ( $before === null ? 'create ' : 'replace ' ) . $path . ' (' . strlen( $content ) . ' bytes' . ( $before === null ? '' : ', a backup first' ) . ')' );
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return array
     */
    public function apply( array $step, $context )
    {
        $mode = isset( $step['mode'] ) ? (string)$step['mode'] : 'ensure';
        $path = expMigrationFiles::normalize( (string)$step['path'] );
        $roots = $this->roots();
        $file = expMigrationFiles::resolve( $path, $context->root(), $roots );
        $before = expMigrationFiles::checksum( $file );
        $problem = $this->stateProblem( $step, $mode, $before );
        if ( $problem !== null )
            throw new RuntimeException( "$path: $problem" );
        $content = $mode === 'delete' ? null : $this->content( $step, $context );
        $change = expMigrationFiles::write( $context->root(), $path, $content, self::migrationId( $context ), $roots );
        if ( $change === null )
            return self::result( array( $mode === 'delete' ? "$path: not there" : "$path: is so already" ),
                                 array( 'path' => $path, 'changed' => false, 'before' => (string)$before, 'after' => (string)$before ) );
        if ( method_exists( $context, 'addFileChange' ) )
            $context->addFileChange( $change );
        $line = ( $content === null ? 'removed ' : ( $change['existed'] ? 'replaced ' : 'created ' ) ) . $path
              . ( $change['backup'] !== '' ? ', the old file kept as ' . $change['backup'] : '' );
        return self::result( array( $line ), array( 'path' => $path, 'changed' => true, 'before' => (string)$change['before'], 'after' => (string)$change['after'] ) );
    }

    /**
     * Puts the file back as it was before the migration: the old file from its backup, or the new one removed.
     *
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function revert( array $step, $context )
    {
        $path = expMigrationFiles::normalize( (string)$step['path'] );
        expMigrationFiles::resolve( $path, $context->root(), $this->roots() );
        $changes = expMigrationFiles::changes( $context->root(), self::migrationId( $context ) );
        if ( !isset( $changes[$path] ) )
            return array( "$path: the migration did not change it, nothing to put back" );
        return array( expMigrationFiles::restore( $context->root(), $changes[$path] ) );
    }

    /**
     * Why the file is not in the state the step needs, or null. Pure.
     *
     * @param array $step
     * @param string $mode
     * @param string|null $before the SHA-256 of the file, null: none
     * @return string|null
     */
    public function stateProblem( array $step, $mode, $before )
    {
        if ( $mode === 'create' && $before !== null )
            return 'exists already (mode create; ensure replaces it)';
        if ( $mode === 'update' && $before === null )
            return 'does not exist (mode update; ensure creates it)';
        if ( isset( $step['expect'] ) )
        {
            $expect = strtolower( (string)$step['expect'] );
            if ( $expect === 'absent' && $before !== null )
                return 'exists, and expect: absent says it must not';
            if ( $expect !== 'absent' && $before !== $expect )
                return 'is not the file expect: names (SHA-256 ' . ( $before === null ? 'none: it does not exist' : substr( $before, 0, 12 ) ) . ')';
        }
        return null;
    }

    /**
     * The new content of a step, checked against checksum:.
     *
     * @param array $step
     * @param expMigrationContext $context
     * @return string
     * @throws RuntimeException
     */
    protected function content( array $step, $context )
    {
        if ( isset( $step['source'] ) )
        {
            $source = expMigrationStepFile::migrationPath( (string)$step['source'], $context );
            $content = file_get_contents( $source );
            if ( $content === false )
                throw new RuntimeException( 'source: ' . $step['source'] . ' could not be read' );
        }
        else
            $content = (string)$step['content'];
        if ( isset( $step['checksum'] ) && hash( 'sha256', $content ) !== strtolower( (string)$step['checksum'] ) )
            throw new RuntimeException( 'checksum: the new content of ' . $step['path'] . ' is not the one checksum: names (SHA-256 '
                                        . substr( hash( 'sha256', $content ), 0, 12 ) . ')' );
        return $content;
    }

    /**
     * The id of the migration whose step runs: the name of its file without the suffix.
     *
     * @param expMigrationContext $context
     * @return string
     * @throws RuntimeException
     */
    public static function migrationId( $context )
    {
        $file = $context instanceof expMigrationContext ? $context->migrationFile() : null;
        if ( $file === null || $file === '' )
            throw new RuntimeException( 'a code_file step runs in a migration file (its backups are kept by the migration)' );
        return pathinfo( basename( (string)$file ), PATHINFO_FILENAME );
    }
}
