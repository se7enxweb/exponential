<?php
/**
 * File containing the expMigrationGenerator class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Writes new migration files, for exp:migration:generate. The skeleton is an empty PHP migration with its header,
 * plan(), up() and down(), named YYYYMMDDHHMMSS_<name>.php after the time of now, in the site's migrations
 * directory (the first [MigrationSettings] DirectoryList entry) or in an extension's (ExtensionDirectory).
 *
 * The class is named after the name and the time, in a namespace of its source (Site\Migrations,
 * Extension\<Name>\Migrations), so two migrations never declare the same class. An existing file is never replaced.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationGenerator
{
    /** The types a skeleton can be written for. */
    const SKELETON_TYPES = array( 'php' );

    /**
     * A migration name as a file name takes it: lower case, words joined with "_". Pure.
     *
     * @param string $name e.g. "Add news class"
     * @return string e.g. add_news_class; empty when nothing is left
     */
    public static function slug( $name )
    {
        $slug = strtolower( trim( preg_replace( '/[^A-Za-z0-9]+/', '_', (string)$name ), '_' ) );
        return substr( $slug, 0, 100 );
    }

    /**
     * The class name of a migration. Pure.
     *
     * @param string $slug
     * @param string $timestamp YYYYMMDDHHMMSS
     * @return string e.g. AddNewsClass20261031120000
     */
    public static function className( $slug, $timestamp )
    {
        $studly = str_replace( ' ', '', ucwords( str_replace( array( '_', '-' ), ' ', (string)$slug ) ) );
        if ( $studly === '' || ctype_digit( $studly[0] ) )
            $studly = 'Migration' . $studly;
        return $studly . $timestamp;
    }

    /**
     * The namespace of a source's migrations. Pure.
     *
     * @param string $source site or extension:<name>
     * @return string
     */
    public static function namespaceOf( $source )
    {
        if ( strpos( (string)$source, 'extension:' ) === 0 )
        {
            $name = str_replace( ' ', '', ucwords( preg_replace( '/[^A-Za-z0-9]+/', ' ', substr( $source, 10 ) ) ) );
            if ( $name === '' || ctype_digit( $name[0] ) )
                $name = 'X' . $name;
            return 'Extension\\' . $name . '\\Migrations';
        }
        return 'Site\\Migrations';
    }

    /**
     * The skeleton of a migration: its file and content, written by write().
     *
     * @param string $name the name (made a slug())
     * @param array $options source (site, the default, or extension:<name>), directory (the folder instead of the
     *                       source's), description, author, time (a Unix time, default now), type (php), root,
     *                       ini (migration.ini)
     * @return array ok, errors (string[]), id, file (absolute), path (relative to root), class, namespace, content
     */
    public static function skeleton( $name, array $options = array() )
    {
        $result = array( 'ok' => false, 'errors' => array(), 'id' => '', 'file' => '', 'path' => '', 'class' => '', 'namespace' => '', 'content' => '' );
        $type = isset( $options['type'] ) && $options['type'] !== '' ? (string)$options['type'] : 'php';
        if ( !in_array( $type, self::SKELETON_TYPES, true ) )
            $result['errors'][] = "no skeleton of the type $type: the types are " . implode( ', ', self::SKELETON_TYPES );
        $slug = self::slug( $name );
        if ( $slug === '' )
            $result['errors'][] = 'a migration needs a name of letters and digits, e.g. add_news_class';
        $source = isset( $options['source'] ) && $options['source'] !== '' ? (string)$options['source'] : 'site';
        if ( $source !== 'site' && !preg_match( '/^extension:[A-Za-z0-9_.-]+$/', $source ) )
            $result['errors'][] = "the source must be site or extension:<name>, not $source";
        $root = rtrim( isset( $options['root'] ) && $options['root'] !== '' ? (string)$options['root'] : (string)eZSys::rootDir(), '/' );
        if ( $result['errors'] )
            return $result;

        $directory = isset( $options['directory'] ) && $options['directory'] !== '' ? (string)$options['directory'] : self::directoryOf( $source, $root, isset( $options['ini'] ) ? $options['ini'] : null );
        if ( $directory === '' )
        {
            $result['errors'][] = 'no migrations directory: [MigrationSettings] DirectoryList in migration.ini names none';
            return $result;
        }
        if ( $directory[0] !== '/' )
            $directory = $root . '/' . $directory;
        $timestamp = date( 'YmdHis', isset( $options['time'] ) ? (int)$options['time'] : time() );
        $id = $timestamp . '_' . $slug;
        $file = rtrim( $directory, '/' ) . '/' . $id . '.' . $type;
        $class = self::className( $slug, $timestamp );
        $namespace = self::namespaceOf( $source );
        $description = isset( $options['description'] ) && trim( (string)$options['description'] ) !== ''
                     ? trim( preg_replace( '/\s+/', ' ', (string)$options['description'] ) )
                     : ucfirst( str_replace( '_', ' ', $slug ) );
        $author = isset( $options['author'] ) ? trim( preg_replace( '/\s+/', ' ', (string)$options['author'] ) ) : '';
        $result['id'] = $id;
        $result['file'] = $file;
        $result['path'] = expMigrationCheck::relative( $file, $root );
        $result['class'] = $class;
        $result['namespace'] = $namespace;
        $result['content'] = self::phpSkeleton( $namespace, $class, $description, $author );
        if ( file_exists( $file ) )
            $result['errors'][] = $result['path'] . ' exists already';
        $result['ok'] = !$result['errors'];
        return $result;
    }

    /**
     * Writes a skeleton (skeleton()); never replaces a file.
     *
     * @param array $skeleton
     * @return string[] errors, empty when written
     */
    public static function write( array $skeleton )
    {
        if ( empty( $skeleton['ok'] ) )
            return $skeleton['errors'] ?: array( 'nothing to write' );
        $dir = dirname( $skeleton['file'] );
        if ( !is_dir( $dir ) && !mkdir( $dir, 0775, true ) && !is_dir( $dir ) )
            return array( "cannot make the folder $dir" );
        $handle = @fopen( $skeleton['file'], 'x' );
        if ( !$handle )
            return array( $skeleton['path'] . ' cannot be written (it exists, or the folder is not writable)' );
        $ok = fwrite( $handle, $skeleton['content'] ) === strlen( $skeleton['content'] );
        fclose( $handle );
        return $ok ? array() : array( $skeleton['path'] . ' was not written completely' );
    }

    /**
     * The folder a source's new migrations go to: the first DirectoryList entry for the site, the extension's
     * ExtensionDirectory for an extension. Relative to the root, or absolute.
     *
     * @param string $source
     * @param string $root
     * @param eZINI|null $ini
     * @return string
     */
    public static function directoryOf( $source, $root, $ini = null )
    {
        $settings = expMigrationLocator::settings( $ini ?: eZINI::instance( 'migration.ini' ) );
        if ( strpos( $source, 'extension:' ) === 0 )
            return rtrim( eZExtension::baseDirectory(), '/' ) . '/' . substr( $source, 10 ) . '/' . $settings['extension_directory'];
        return $settings['directories'] ? (string)$settings['directories'][0] : '';
    }

    /**
     * The content of a PHP skeleton. Pure.
     *
     * @param string $namespace
     * @param string $class
     * @param string $description
     * @param string $author
     * @return string
     */
    public static function phpSkeleton( $namespace, $class, $description, $author = '' )
    {
        $lines = array(
            '<?php',
            '// description: ' . $description,
        );
        if ( $author !== '' )
            $lines[] = '// author: ' . $author;
        $lines = array_merge( $lines, array(
            'namespace ' . $namespace . ';',
            '',
            '/**',
            ' * The comment lines at the top of the file are the header the migration engine reads. Other keys, one line each:',
            ' * "requires" (migration ids, extension:<name>), "environments" (staging, production, !development to leave one',
            ' * out), "engines" (mysql, postgresql, sqlite, oracle, mongodb), "maintenance" (yes or no) and "author".',
            ' */',
            'final class ' . $class . ' extends \\expMigrationPhp',
            '{',
            '    /**',
            '     * What up() would change, one line each, without changing anything (the dry run shows it).',
            '     *',
            '     * @param \\expMigrationContext $context',
            '     * @return string[]',
            '     */',
            '    public function plan( $context )',
            '    {',
            '        return array( \'nothing yet: describe the change here\' );',
            '    }',
            '',
            '    /**',
            '     * Makes the change. Throw an exception when it fails: the run stops and the caption taken before it is',
            '     * restored. $context->db() is the database, $context->setReference() leaves a value for later migrations.',
            '     *',
            '     * @param \\expMigrationContext $context',
            '     * @return string[] what was done, one line each',
            '     */',
            '    public function up( $context )',
            '    {',
            '        return array();',
            '    }',
            '',
            '    /**',
            '     * Undoes up(), for ./console exp:migration:rollback <id>. Remove this method when the change cannot be undone',
            '     * by itself; the caption taken before the run is the way back then.',
            '     *',
            '     * @param \\expMigrationContext $context',
            '     * @return string[]',
            '     */',
            '    public function down( $context )',
            '    {',
            '        return array();',
            '    }',
            '}',
            '',
        ) );
        return implode( "\n", $lines );
    }
}
