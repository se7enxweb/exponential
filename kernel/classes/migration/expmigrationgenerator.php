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
 * plan(), up() and down() (type php), or an empty YAML migration with its header and no steps (type yml), named
 * YYYYMMDDHHMMSS_<name>.<type> after the time of now, in the site's migrations directory (the first
 * [MigrationSettings] DirectoryList entry) or in an extension's (ExtensionDirectory).
 *
 * The class of a PHP migration is named after the name and the time, in a namespace of its source (Site\Migrations,
 * Extension\<Name>\Migrations), so two migrations never declare the same class. An existing file is never replaced.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationGenerator
{
    /** The types a skeleton can be written for. */
    const SKELETON_TYPES = array( 'php', 'yml', 'sql', 'ini', 'graphql' );

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
     *                       source's), description, author, time (a Unix time, default now), type (php or yml), root,
     *                       ini (migration.ini)
     * @return array ok, errors (string[]), id, file (absolute), path (relative to root), class, namespace, content
     */
    public static function skeleton( $name, array $options = array() )
    {
        $result = array( 'ok' => false, 'errors' => array(), 'id' => '', 'file' => '', 'path' => '', 'class' => '', 'namespace' => '', 'content' => '' );
        $type = isset( $options['type'] ) && $options['type'] !== '' ? (string)$options['type'] : 'php';
        if ( !in_array( $type, self::SKELETON_TYPES, true ) )
            $result['errors'][] = "no skeleton of the type $type: the types are " . implode( ', ', self::SKELETON_TYPES );
        $engine = isset( $options['engine'] ) && $options['engine'] !== '' ? strtolower( (string)$options['engine'] ) : '';
        if ( isset( expMigrationDefinition::ENGINE_ALIASES[$engine] ) )
            $engine = expMigrationDefinition::ENGINE_ALIASES[$engine];
        if ( $engine !== '' && $type !== 'sql' )
            $result['errors'][] = '--engine goes with --type=sql only';
        else if ( $engine !== '' && !in_array( $engine, expMigrationDefinition::ENGINES, true ) )
            $result['errors'][] = "the engine $engine is not one of " . implode( ', ', expMigrationDefinition::ENGINES );
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
        $file = rtrim( $directory, '/' ) . '/' . $id . '.' . self::extension( $type, $engine );
        $class = self::className( $slug, $timestamp );
        $namespace = self::namespaceOf( $source );
        $description = isset( $options['description'] ) && trim( (string)$options['description'] ) !== ''
                     ? trim( preg_replace( '/\s+/', ' ', (string)$options['description'] ) )
                     : ucfirst( str_replace( '_', ' ', $slug ) );
        $author = isset( $options['author'] ) ? trim( preg_replace( '/\s+/', ' ', (string)$options['author'] ) ) : '';
        $result['id'] = $id;
        $result['file'] = $file;
        $result['path'] = expMigrationCheck::relative( $file, $root );
        if ( $type === 'yml' )
        {
            $result['content'] = self::yamlSkeleton( $description, $author );
        }
        else if ( $type === 'graphql' )
            $result['content'] = self::graphqlSkeleton( $description, $author );
        else if ( $type === 'ini' )
            $result['content'] = self::iniSkeleton( $description, $author );
        else if ( $type === 'sql' )
            $result['content'] = $engine === 'mongodb' ? self::mongodbSkeleton() : self::sqlSkeleton( $description, $author, $engine );
        else
        {
            $result['class'] = $class;
            $result['namespace'] = $namespace;
            $result['content'] = self::phpSkeleton( $namespace, $class, $description, $author );
        }
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
        if ( !is_dir( $dir ) && !eZDir::mkdir( $dir, false, true ) && !is_dir( $dir ) )
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
     * The content of a YAML skeleton: the header and an empty list of steps, with an example in comments. Pure.
     *
     * @param string $description
     * @param string $author
     * @return string
     */
    public static function yamlSkeleton( $description, $author = '' )
    {
        $lines = array( '# description: ' . $description );
        if ( $author !== '' )
            $lines[] = '# author: ' . $author;
        $lines = array_merge( $lines, array(
            '#',
            '# The comment lines at the top of the file are the header the migration engine reads. Other keys, one line each:',
            '# "requires" (migration ids, extension:<name>), "environments" (staging, production, !development to leave one',
            '# out), "engines" (mysql, postgresql, sqlite, oracle, mongodb), "maintenance" (yes or no) and "author".',
            'steps: []',
            '',
            '# Replace [] with the steps, which run in order. "type" names the step type (migration.ini [StepSettings]',
            '# StepTypeList); the other keys are the type\'s, plus if, environments, engines and references:',
            '#',
            '# steps:',
            '#     - type: reference',
            '#       set:',
            '#           news_parent: ${idmap.node.news}',
            '#     - type: assert',
            '#       that: { ref: news_parent, set: true }',
            '#',
            '# down: the steps that undo the migration, for ./console exp:migration:rollback <id> (optional).',
            '',
        ) );
        return implode( "\n", $lines );
    }

    /**
     * The file extension of a skeleton: php, yml (also for graphql), ini, sql or <engine>.sql, mongodb.json. Pure.
     *
     * @param string $type
     * @param string $engine for sql: empty (the portable file) or an engine
     * @return string
     */
    public static function extension( $type, $engine = '' )
    {
        if ( $type === 'graphql' )
            return 'yml';
        if ( $type === 'sql' && $engine === 'mongodb' )
            return 'mongodb.json';
        if ( $type === 'sql' && $engine !== '' )
            return ( $engine === 'postgresql' ? 'pgsql' : $engine ) . '.sql';
        return $type;
    }

    /**
     * A YAML migration with an example of the step graphql in comments. Pure.
     *
     * @param string $description
     * @param string $author
     * @return string
     */
    public static function graphqlSkeleton( $description, $author = '' )
    {
        $lines = array( '# description: ' . $description );
        if ( $author !== '' )
            $lines[] = '# author: ' . $author;
        $lines = array_merge( $lines, array(
            '#',
            '# The comment lines at the top of the file are the header the migration engine reads (see a YAML migration).',
            '# The header key "events" says how the content events of the run reach webhooks: each (the default), summary or none.',
            'steps: []',
            '',
            '# Replace [] with the steps. The step graphql runs a query or mutation of the GraphQL API as the run\'s user;',
            '# query: holds its text, file: names a .graphql file next to this one, persisted: a persisted query.',
            '#',
            '# steps:',
            '#     - type: graphql',
            '#       query: |',
            '#           mutation ($parent: Int!) {',
            '#             createContent(parentId: $parent, classIdentifier: "folder", fields: [ { identifier: "name", value: "News" } ]) { contentId nodeId }',
            '#           }',
            '#       variables: { parent: 2 }',
            '#       references: { news: result }',
            '',
        ) );
        return implode( "\n", $lines );
    }

    /**
     * An INI migration with examples in comments. Pure.
     *
     * @param string $description
     * @param string $author
     * @return string
     */
    public static function iniSkeleton( $description, $author = '' )
    {
        $lines = array( '#?ini charset="utf-8"?', '# description: ' . $description );
        if ( $author !== '' )
            $lines[] = '# author: ' . $author;
        $lines = array_merge( $lines, array(
            '#',
            '# Each block names its file, and for a siteaccess the siteaccess; the settings go to settings/override or',
            '# settings/siteaccess/<name>, never to settings/*.ini or an extension. Var=value sets, Var[] followed by',
            '# Var[]=... makes the array exactly these values, Var[]=value adds one, -Var removes.',
            '#',
            '# [site.ini/SiteSettings]',
            '# SiteName=News',
            '#',
            '# [siteaccess:admin site.ini/SiteSettings]',
            '# DefaultPage=content/dashboard',
            '',
        ) );
        return implode( "\n", $lines );
    }

    /**
     * An SQL migration: the portable file or one engine's. Pure.
     *
     * @param string $description
     * @param string $author
     * @param string $engine empty: the portable file
     * @return string
     */
    public static function sqlSkeleton( $description, $author = '', $engine = '' )
    {
        $lines = array( '-- description: ' . $description );
        if ( $author !== '' )
            $lines[] = '-- author: ' . $author;
        $lines = array_merge( $lines, array(
            '--',
            $engine === '' ? '-- This file runs on every SQL engine without a file of its own (<id>.mysql.sql, <id>.pgsql.sql ...);'
                           : "-- This file runs on $engine only, instead of the portable <id>.sql;",
            '-- MongoDB needs <id>.mongodb.json. Statements end with ; at the end of a line. The part after "-- down"',
            '-- undoes the migration. "-- transactional: no" in the header for DDL that cannot run in a transaction.',
            '',
            '-- CREATE INDEX exp_news_list ON ezcontentobject ( published );',
            '',
            '-- down',
            '',
            '-- DROP INDEX exp_news_list;',
            '',
        ) );
        return implode( "\n", $lines );
    }

    /**
     * The MongoDB file of an SQL migration: no commands yet. Pure.
     *
     * @return string
     */
    public static function mongodbSkeleton()
    {
        return "{\n    \"up\": [],\n    \"down\": []\n}\n";
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
