<?php
/**
 * The live tests of expCassandraDB (expCassandraLiveTestBase) on Apache Cassandra (EXP_TEST_CASSANDRA_HOST, _PORT,
 * _USER, _PASSWORD, _KEYSPACE; skipped without them).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

require_once __DIR__ . '/expCassandraLiveTestBase.php';

#[\PHPUnit\Framework\Attributes\Group('lib')]
#[\PHPUnit\Framework\Attributes\Group('ezdb')]
#[\PHPUnit\Framework\Attributes\Group('cassandra')]
#[\PHPUnit\Framework\Attributes\Group('cassandra-live')]
class expCassandraLiveTest extends expCassandraLiveTestBase
{
    protected function product()
    {
        return 'cassandra';
    }

    public function testTheServerIsCassandra()
    {
        $this->assertSame( 'cassandra', $this->db->product() );
        $this->assertSame( 'cassandra', $this->db->databaseName() );
        $version = $this->db->databaseServerVersion();
        $this->assertGreaterThanOrEqual( 4, $version['values'][0] );
    }
}
?>
