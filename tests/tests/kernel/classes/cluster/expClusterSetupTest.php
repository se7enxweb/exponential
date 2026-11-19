<?php
/**
 * exp:cluster:enable and exp:cluster:disable (expClusterSetup): the pure parts, no database.
 *
 *  CS-01 - The plan: one database for every siteaccess, its engine's backend in this kernel, one move per var directory
 *  CS-02 - The INI block goes before the closing comment, keeps the installation's own lines byte for byte, is replaced
 *          and taken out again; a file without a closing comment is refused
 *  CS-03 - Cluster settings that are not ours (the installation's own, a hosting panel's) are seen; values that could
 *          end the PHP comment are refused
 *  CS-04 - config.cluster.php is valid PHP with every value quoted, and is recognized as ours
 *  CS-05 - The schema statements, the default share, the web server rules, the aliases of the commands
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expClusterSetupTest extends PHPUnit\Framework\TestCase
{
    private static function facts( $impl = 'ezmysqli', array $backends = array( 'mysqli' => 'eZDFSFileHandlerMySQLiBackend' ) )
    {
        $db = array( 'DatabaseImplementation' => $impl, 'Server' => 'localhost', 'Port' => '', 'Socket' => 'disabled', 'User' => 'u1', 'Password' => "p'w\"\$x", 'Database' => 'd1' );
        return array( 'default' => 'site', 'backends' => $backends, 'siteaccesses' => array(
            'site' => array( 'db' => $db, 'var' => 'var/site', 'handler' => 'eZFSFileHandler' ),
            'admin' => array( 'db' => $db, 'var' => 'var/site', 'handler' => 'eZFSFileHandler' ) ) );
    }

    public function testThePlan()
    {
        $p = expClusterSetup::plan( self::facts() );
        $this->assertSame( array( 'mysqli', 'dfsmysqli', 'localhost', 3306, '' ), array( $p['engine'], $p['gateway'], $p['host'], $p['port'], $p['socket'] ) );
        $this->assertSame( array( 'var/site' => 'site' ), $p['vardirs'] );
        $f = self::facts();
        $f['siteaccesses']['admin']['db']['Database'] = 'd2';
        $this->assertStringContainsString( 'more than one database', expClusterSetup::plan( $f ) );
        $this->assertStringContainsString( 'no PostgreSQL cluster backend yet', expClusterSetup::plan( self::facts( 'ezpostgresql' ) ) );
        $pg = expClusterSetup::plan( self::facts( 'ezpostgresql', array( 'postgresql' => 'eZDFSFileHandlerPostgresqlBackend' ) ) );
        $this->assertSame( array( 'postgresql', 'dfspostgresql', 5432 ), array( $pg['engine'], $pg['gateway'], $pg['port'] ) );
        $this->assertStringContainsString( 'needs MySQL/MariaDB or PostgreSQL', expClusterSetup::plan( self::facts( 'ezsqlite3' ) ) );
        $this->assertStringContainsString( 'not postgresql', expClusterSetup::plan( self::facts(), 'postgresql' ) );
    }

    public function testTheIniBlock()
    {
        $p = expClusterSetup::plan( self::facts() );
        $block = expClusterSetup::iniBlock( $p, '/var/www/vhosts/a.example/dfs' );
        $new = expClusterSetup::iniText( null, $block );
        $this->assertStringStartsWith( "<?php /* #?ini charset=\"utf-8\"?\n", $new );
        $this->assertStringContainsString( "FileHandler=eZDFSFileHandler\n", $new );
        $this->assertTrue( expClusterSetup::iniOurs( $new ) );
        $this->assertFalse( expClusterSetup::iniForeign( $new ) );
        $this->assertSame( '/var/www/vhosts/a.example/dfs', expClusterSetup::iniShare( $new ) );
        $own = "<?php /* #?ini charset=\"utf-8\"?\r\n\r\n[PassThroughSettings]\r\nDefaultContentDisposition=inline\r\n*/ ?>";
        $with = expClusterSetup::iniText( $own, $block );
        $this->assertSame( 1, substr_count( expClusterSetup::iniText( $with, $block ), '# BEGIN Exponential cluster' ) );
        $this->assertSame( $own, expClusterSetup::iniText( $with, '' ) );
        $this->assertNull( expClusterSetup::iniText( "[ClusteringSettings]\n", $block ) );
    }

    public function testSettingsThatAreNotOursAndUnsafeValues()
    {
        $this->assertTrue( expClusterSetup::iniForeign( "<?php /*\n[eZDFSClusteringSettings]\nMountPointPath=/nfs\n*/ ?>" ) );
        $katalyst = "<?php /*\n# BEGIN Katalyst cluster (set in Katalyst)\n[ClusteringSettings]\nFileHandler=eZDFSFileHandler\n# END Katalyst cluster\n*/ ?>";
        $this->assertTrue( expClusterSetup::iniForeign( $katalyst ), 'a hosting panel\'s block is not ours' );
        $this->assertFalse( expClusterSetup::iniOurs( $katalyst ) );
        foreach ( array( "a\nb", 'a*/b', 'a?>b' ) as $bad )
            $this->assertFalse( expClusterSetup::iniSafe( $bad ) );
        $this->assertTrue( expClusterSetup::iniSafe( 'Kx-9aZ_!#%&()' ) );
    }

    public function testConfigClusterPhp()
    {
        $text = expClusterSetup::gatewayText( expClusterSetup::plan( self::facts() ), '/srv/dfs' );
        $this->assertTrue( expClusterSetup::gatewayOurs( $text ) );
        $strings = array();
        foreach ( token_get_all( $text ) as $t )
            if ( is_array( $t ) && $t[0] === T_CONSTANT_ENCAPSED_STRING )
                $strings[] = $t[1];
        $this->assertContains( var_export( "p'w\"\$x", true ), $strings );
        $this->assertStringContainsString( "define( 'CLUSTER_STORAGE_BACKEND', 'dfsmysqli' );", $text );
        $this->assertFalse( expClusterSetup::gatewayOurs( "<?php\ndefine( 'CLUSTER_STORAGE_BACKEND', 'dfsmysqli' );\n" ) );
    }

    public function testTheKickstartersClusterChoice()
    {
        require_once eZSys::rootDir() . '/tests/tests/kernel/private/classes/commands/expApplyFlagInstallerCommandsTest.php';
        $this->assertSame( array( 'mode' => 'single', 'share' => '' ), expKickstarter::clusterChoice( new expApplyFlagInstallerFakeIni( array() ) ) );
        $ini = new expApplyFlagInstallerFakeIni( array( 'cluster' => array( 'Mode' => 'One-Node', 'Share' => '/mnt/e1/dfs/a.example' ) ) );
        $this->assertSame( array( 'mode' => 'one-node', 'share' => '/mnt/e1/dfs/a.example' ), expKickstarter::clusterChoice( $ini ) );
        $this->assertStringContainsString( 'one-node DFS cluster after the install', implode( "\n", expKickstarter::planLines( $ini ) ) );
        $this->assertStringContainsString( 'single or one-node', expKickstarter::clusterChoice( new expApplyFlagInstallerFakeIni( array( 'cluster' => array( 'Mode' => 'two' ) ) ) ) );
        $this->assertStringContainsString( 'absolute path', expKickstarter::clusterChoice( new expApplyFlagInstallerFakeIni( array( 'cluster' => array( 'Mode' => 'one-node', 'Share' => 'dfs' ) ) ) ) );
    }

    public function testSchemaShareRulesAndAliases()
    {
        $root = eZSys::rootDir();
        $st = expClusterSetup::schemaStatements( (string)file_get_contents( $root . '/kernel/sql/mysql/cluster_dfs_schema.sql' ) );
        $this->assertSame( array( 'ezdfsfile', 'ezdfsfile_cache' ), array_column( $st, 'name' ) );
        $this->assertSame( '/var/www/vhosts/a.example/dfs', expClusterSetup::defaultShare( '/var/www/vhosts/a.example/httpdocs/' ) );
        $rules = expClusterSetup::webServerRules();
        $this->assertStringContainsString( '[PT,END]', $rules['apache-vhost'] );
        foreach ( array( 'enable', 'disable' ) as $n )
        {
            $this->assertStringContainsString( '@alias exp:cluster:' . $n, (string)file_get_contents( $root . '/bin/php/expcluster' . $n . '.php' ) );
            $this->assertStringContainsString( '@alias exp:cluster:' . $n, (string)file_get_contents( $root . '/kernel/private/classes/commands/expcluster' . $n . '.php' ) );
        }
    }
}
