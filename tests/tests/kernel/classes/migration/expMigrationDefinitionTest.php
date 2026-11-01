<?php
/**
 * Migration files without a database: their names, their header and their checksum.
 *
 *  MD-01 file names: YYYYMMDDHHMMSS_name.{php,yml,sql,<engine>.sql,ini}; other names are no migration, names that
 *        start like one but are wrong are reported
 *  MD-02 the header in the comment syntax of each type; lists, flags, prose, unknown and repeated keys
 *  MD-03 the environment filter (appliesTo) and the engines (portable and engine files)
 *  MD-04 the checksum: SHA-256 of the content, stable, line endings normalized, every other byte counts
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */
class expMigrationDefinitionTest extends PHPUnit\Framework\TestCase
{
    private static $root;

    /** @var string|null */
    private $dir;

    public static function setUpBeforeClass(): void
    {
        self::$root = dirname( __DIR__, 5 );
        foreach ( array( 'expmigration', 'expmigrationdefinition' ) as $f )
            require_once self::$root . "/kernel/classes/migration/$f.php";
    }

    protected function tearDown(): void
    {
        if ( $this->dir !== null )
        {
            array_map( 'unlink', glob( $this->dir . '/*' ) );
            rmdir( $this->dir );
            $this->dir = null;
        }
    }

    private function file( $name, $content )
    {
        if ( $this->dir === null )
        {
            $this->dir = sys_get_temp_dir() . '/expmigrationdefinition-' . getmypid() . '-' . mt_rand();
            mkdir( $this->dir );
        }
        file_put_contents( $this->dir . "/$name", $content );
        return $this->dir . "/$name";
    }

    /** MD-01 */
    public function testFileNames()
    {
        $this->assertSame( array( 'id' => '20261031120000_add_news_class', 'timestamp' => '20261031120000', 'name' => 'add_news_class', 'type' => 'php', 'engine' => '*' ),
                           expMigrationDefinition::parseName( '20261031120000_add_news_class.php' ) );
        $this->assertSame( 'postgresql', expMigrationDefinition::parseName( '20261031120000_index.postgresql.sql' )['engine'] );
        foreach ( array( 'yml', 'sql', 'ini' ) as $type )
            $this->assertSame( $type, expMigrationDefinition::parseName( "20261031120000_x-1.$type" )['type'] );

        foreach ( array( 'README.md', '.gitkeep', 'status.json', '2026103112000_short.sql', 'migration_20261031120000.sql' ) as $name )
            $this->assertNull( expMigrationDefinition::parseName( $name ), $name );

        $errors = array(
            '20261031120000_.sql' => 'not a migration file name',
            '20261031120000_a b.sql' => 'not a migration file name',
            '20261031120000_a.yaml' => 'unknown type .yaml',
            '20261031120000_a.mysql.php' => 'only SQL files name an engine',
            '20261031120000_a.db2.sql' => 'only SQL files name an engine',
            '20261331120000_a.sql' => 'valid date',
            '20260230120000_a.sql' => 'valid date',
            '20261031240000_a.sql' => 'valid date',
            '19691231235959_a.sql' => 'valid date',
        );
        foreach ( $errors as $name => $message )
            $this->assertStringContainsString( $message, expMigrationDefinition::parseName( $name )['error'], $name );
    }

    /** MD-02 */
    public function testHeaderInEachCommentSyntax()
    {
        $texts = array(
            'php docblock' => "<?php\n/**\n * Prose about the migration.\n *\n * description: News\n * requires: a, b\n * maintenance: yes\n */\nreturn 1;\n",
            'php one-line block' => "<?php\n/* description: News */\n// requires: a\n# requires: b\n// maintenance: true\n",
            'sql' => "-- description: News\n--requires: [a, b]\n-- maintenance: on\nSELECT 1;\n-- requires: c\n",
            'ini' => "#?ini charset=\"utf-8\"?\n# description: News\n; requires: a\n# requires: b\n# maintenance: 1\n\n[S]\nA=1\n",
            'yml' => "---\n# description: News\n# requires: 'a', \"b\"\n# maintenance: enabled\n- type: content\n",
            'crlf' => "-- description: News\r\n-- requires: a,b\r\n-- maintenance: yes\r\n",
        );
        foreach ( $texts as $label => $text )
        {
            $errors = array();
            $header = expMigrationDefinition::parseHeader( $text, $errors );
            $this->assertSame( array(), $errors, $label );
            $this->assertSame( 'News', $header['description'], $label );
            $this->assertSame( array( 'a', 'b' ), $header['requires'], $label );
            $this->assertTrue( $header['maintenance'], $label );
        }

        $errors = array();
        $header = expMigrationDefinition::parseHeader( "-- Note: prose\n-- see: elsewhere\n-- author: A\n-- author: B\n-- maintenance: perhaps\n-- engines: mysql, mysql\n", $errors );
        $this->assertSame( array( 'author' => 'A', 'engines' => array( 'mysql' ) ), $header );
        $this->assertCount( 3, $errors );
        $this->assertStringContainsString( 'unknown header key see', $errors[0] );
        $this->assertStringContainsString( 'author is given twice', $errors[1] );
        $this->assertStringContainsString( 'perhaps is not yes or no', $errors[2] );

        $this->assertSame( array(), expMigrationDefinition::parseHeader( "<?php\nreturn 1;\n// description: too late\n" ) );
    }

    /** MD-02 */
    public function testHeaderOfAFile()
    {
        $definition = new expMigrationDefinition( '20261031120000_news', 'site', 'sql' );
        $this->assertTrue( $definition->addFile( $this->file( '20261031120000_news.sql',
            "-- description: News\n-- author: Jane Doe\n-- requires: 20261030120000_class, extension:expnews\n-- environments: staging\n-- maintenance: yes\n" ) ) );
        $this->assertSame( array(), $definition->errors() );
        $this->assertSame( '20261031120000_news', $definition->id() );
        $this->assertSame( '20261031120000', $definition->timestamp() );
        $this->assertSame( 'news', $definition->name() );
        $this->assertSame( 'site', $definition->source() );
        $this->assertSame( 'sql', $definition->type() );
        $this->assertSame( 'News', $definition->description() );
        $this->assertSame( 'Jane Doe', $definition->author() );
        $this->assertSame( array( '20261030120000_class' ), $definition->requiredMigrations() );
        $this->assertSame( array( 'expnews' ), $definition->requiredExtensions() );
        $this->assertTrue( $definition->maintenance() );
        $this->assertSame( expMigrationDefinition::SQL_ENGINES, $definition->engines(), 'a portable .sql file: the SQL engines' );

        $other = new expMigrationDefinition( '20261031120000_other', 'site', 'sql' );
        $other->addFile( $this->file( '20261031120000_other.sql', "-- id: 20261031120000_news\n-- engines: db2\n" ) );
        $this->assertStringContainsString( 'the header names the id 20261031120000_news', $other->errors()[0] );
        $this->assertStringContainsString( 'unknown engine db2', $other->errors()[1] );
        $this->assertFalse( $other->maintenance() );
        $this->assertSame( '', $other->description() );

        $this->assertFalse( $other->addFile( $this->file( '20261031120000_other.sql', '' ) ), 'a second portable file' );
        $missing = new expMigrationDefinition( '20261031120000_gone', 'site', 'sql' );
        $this->assertFalse( @$missing->addFile( $this->dir . '/20261031120000_gone.sql' ) );
        $this->assertStringContainsString( 'cannot be read', $missing->errors()[0] );
    }

    /** MD-03 */
    public function testEnvironmentFilter()
    {
        $make = function ( $environments ) {
            $definition = new expMigrationDefinition( '20261031120000_env', 'site', 'sql' );
            $definition->addFile( $this->file( '20261031120000_env.sql', $environments === null ? '' : "-- environments: $environments\n" ) );
            return $definition;
        };
        $every = $make( null );
        $this->assertTrue( $every->appliesTo( 'production' ) );
        $this->assertTrue( $every->appliesTo( '' ) );
        $only = $make( 'staging, production' );
        $this->assertTrue( $only->appliesTo( 'production' ) );
        $this->assertFalse( $only->appliesTo( 'development' ) );
        $this->assertFalse( $only->appliesTo( '' ), 'an installation without an environment runs no limited migration' );
        $not = $make( '!production' );
        $this->assertTrue( $not->appliesTo( 'development' ) );
        $this->assertFalse( $not->appliesTo( 'production' ) );
        $both = $make( 'staging, production, !production' );
        $this->assertFalse( $both->appliesTo( 'production' ), 'an exclusion wins' );
        $this->assertTrue( $both->appliesTo( 'staging' ) );
    }

    /** MD-03 */
    public function testEngines()
    {
        $definition = new expMigrationDefinition( '20261031120000_index', 'site', 'sql' );
        $definition->addFile( $this->file( '20261031120000_index.postgresql.sql', "-- description: PostgreSQL\nSELECT 2;\n" ), 'postgresql' );
        $definition->addFile( $this->file( '20261031120000_index.mysql.sql', "-- description: MySQL\nSELECT 1;\n" ), 'mysql' );
        $this->assertSame( array( 'mysql', 'postgresql' ), $definition->engines(), 'engine files only: those engines' );
        $this->assertSame( 'MySQL', $definition->description(), 'the first engine file\'s header counts' );
        $this->assertTrue( $definition->supportsEngine( 'mysql' ) );
        $this->assertFalse( $definition->supportsEngine( 'sqlite' ) );
        $this->assertNull( $definition->file( 'sqlite' ) );
        $this->assertSame( $this->dir . '/20261031120000_index.mysql.sql', $definition->file() );
        $this->assertNull( $definition->checksumFor( 'sqlite' ) );
        $this->assertFalse( $definition->addFile( $this->dir . '/20261031120000_index.mysql.sql', 'mysql' ), 'one file per engine' );

        $definition->addFile( $this->file( '20261031120000_index.sql', "-- description: Portable\n-- engines: sqlite, postgresql, mysql\nSELECT 0;\n" ) );
        $this->assertSame( 'Portable', $definition->description(), 'the portable file\'s header counts' );
        $this->assertSame( array( 'sqlite', 'postgresql', 'mysql' ), $definition->engines() );
        $this->assertSame( $this->dir . '/20261031120000_index.sql', $definition->file( 'sqlite' ) );
        $this->assertSame( $this->dir . '/20261031120000_index.mysql.sql', $definition->file( 'mysql' ) );
        $this->assertSame( array( '*', 'mysql', 'postgresql' ), array_keys( $definition->files() ) );
        $this->assertSame( hash( 'sha256', "-- description: MySQL\nSELECT 1;\n" ), $definition->checksumFor( 'mysql' ) );
        $this->assertSame( $definition->checksums()['*'], $definition->checksumFor( 'sqlite' ) );
        $this->assertFalse( $definition->supportsEngine( 'oracle' ) );
    }

    /** MD-04 */
    public function testChecksum()
    {
        $content = "-- description: News\nSELECT 1;\n";
        $path = $this->file( '20261031120000_sum.sql', $content );
        $sum = expMigrationDefinition::checksum( $path );
        $this->assertSame( hash( 'sha256', $content ), $sum, 'SHA-256 of the content' );
        $this->assertSame( hash_file( 'sha256', $path ), $sum, 'a file with LF line endings: the plain SHA-256 of the file' );
        $this->assertSame( 64, strlen( $sum ) );
        $this->assertSame( $sum, expMigrationDefinition::checksum( $path ), 'stable' );

        $this->assertSame( $sum, expMigrationDefinition::checksum( $this->file( '20261031120000_crlf.sql', str_replace( "\n", "\r\n", $content ) ) ), 'CRLF' );
        $this->assertSame( $sum, expMigrationDefinition::checksumOf( str_replace( "\n", "\r", $content ) ), 'CR' );
        $this->assertNotSame( $sum, expMigrationDefinition::checksumOf( "-- description: News\nSELECT 1; \n" ), 'a space' );
        $this->assertNotSame( $sum, expMigrationDefinition::checksumOf( "\xEF\xBB\xBF" . $content ), 'a byte order mark' );
        $this->assertNotSame( $sum, expMigrationDefinition::checksumOf( $content . "\n" ), 'an empty line' );
        $this->assertNull( @expMigrationDefinition::checksum( $this->dir . '/missing.sql' ) );

        $definition = new expMigrationDefinition( '20261031120000_sum', 'site', 'sql' );
        $definition->addFile( $path );
        $this->assertSame( $sum, $definition->checksumFor() );
        $this->assertSame( $sum, $definition->checksumFor( 'oracle' ), 'the portable file runs on every engine' );
    }
}
