<?php
/**
 * The recording of changes without a database: how audit events become recorded events (the step each stands for,
 * stable identifiers, personal data and secrets left out), the plan a generated migration would have, the file of
 * running recordings, ids and the admin view's pure helpers.
 *
 *  RT-1  every audit name of STEPS maps to a step type and mode; the names a recording keeps
 *  RT-2  content create: class, parent and remote id by remote id, the fields per language, relations as remote ids,
 *        the account attribute of any class left out
 *  RT-3  a user object: the account and every field not kept left out, groups by remote id
 *  RT-4  node events: move, hide, sort (values from the event), priority, section, swap, removal by a remembered id
 *  RT-5  class, role (policy events record the whole role), assignment with a subtree limitation, section, state group
 *  RT-6  settings: scope by path, a secret is left out, a removed variable; workflow triggers; URL aliases
 *  RT-7  a thing that is gone and an unknown name: nothing recorded
 *  RT-8  the plan: create + update fold into create, create + delete leave nothing, assignments stay one by one, order
 *  RT-9  the file of running recordings: written, read, matched by user, removed when empty; start problems; ids
 *  RT-10 the admin view: redirect targets, who may stop; the generator's missing step types
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package tests
 */

class expMigrationRecordingFakeSource implements expMigrationRecordingSource
{
    public $nodes = array();
    public $objects = array();
    public $classes = array();
    public $roles = array();
    public $sections = array();
    public $groups = array();

    public function nodeRef( $nodeId )
    {
        return isset( $this->nodes[$nodeId] ) ? 'remote:' . $this->nodes[$nodeId]['remote_id'] : null;
    }

    public function objectRef( $objectId )
    {
        return isset( $this->objects[$objectId] ) ? 'remote:' . $this->objects[$objectId]['remote_id'] : null;
    }

    public function node( $nodeId )
    {
        return isset( $this->nodes[$nodeId] ) ? $this->nodes[$nodeId] : null;
    }

    public function object( $objectId )
    {
        return isset( $this->objects[$objectId] ) ? $this->objects[$objectId] : null;
    }

    public function contentClass( $class )
    {
        return isset( $this->classes[$class] ) ? $this->classes[$class] : null;
    }

    public function role( $role )
    {
        return isset( $this->roles[$role] ) ? $this->roles[$role] : null;
    }

    public function section( $sectionId )
    {
        return isset( $this->sections[$sectionId] ) ? $this->sections[$sectionId] : null;
    }

    public function stateGroup( $group )
    {
        return isset( $this->groups[$group] ) ? $this->groups[$group] : null;
    }

    public function sectionIdentifier( $sectionId )
    {
        return isset( $this->sections[$sectionId] ) ? $this->sections[$sectionId]['identifier'] : null;
    }

    public function isUser( $objectId )
    {
        return !empty( $this->objects[$objectId]['is_user'] );
    }
}

class expMigrationRecordingTest extends PHPUnit\Framework\TestCase
{
    /** @var expMigrationRecordingFakeSource */
    private $source;

    /** @var string|null */
    private $dir;

    protected function setUp(): void
    {
        $s = new expMigrationRecordingFakeSource();
        $s->nodes = array(
            2 => array( 'remote_id' => 'root-node', 'object_id' => 1, 'object' => 'remote:root-object', 'parent' => null, 'hidden' => false, 'priority' => 0,
                        'sort_field' => 'path', 'sort_order' => 'asc', 'main' => true ),
            60 => array( 'remote_id' => 'news-node', 'object_id' => 58, 'object' => 'remote:news-object', 'parent' => 'remote:root-node', 'hidden' => false,
                         'priority' => 0, 'sort_field' => 'published', 'sort_order' => 'desc', 'main' => true ),
            61 => array( 'remote_id' => 'archive-node', 'object_id' => 59, 'object' => 'remote:archive-object', 'parent' => 'remote:root-node',
                         'hidden' => true, 'priority' => 2, 'sort_field' => 'path', 'sort_order' => 'asc', 'main' => true ),
            13 => array( 'remote_id' => 'members-node', 'object_id' => 12, 'object' => 'remote:members-object', 'parent' => 'remote:users-node',
                         'hidden' => false, 'priority' => 0, 'sort_field' => 'path', 'sort_order' => 'asc', 'main' => true ),
        );
        $s->objects = array(
            1 => array( 'remote_id' => 'root-object' ),
            12 => array( 'remote_id' => 'members-object' ),
            58 => array( 'remote_id' => 'news-object', 'class' => 'folder', 'section' => 'standard', 'always_available' => false, 'initial_language' => 'eng-GB',
                         'is_user' => false, 'parent' => 'remote:root-node', 'locations' => array(), 'states' => array( 'ez_lock' => 'not_locked' ),
                         'languages' => array( 'eng-GB' => array( 'name' => array( 'datatype' => 'ezstring', 'value' => 'News' ),
                                                                  'related' => array( 'datatype' => 'ezobjectrelationlist', 'value' => array( 'remote:archive-object' ) ),
                                                                  'owner_account' => array( 'datatype' => 'ezuser', 'value' => null ) ),
                                               'ger-DE' => array( 'name' => array( 'datatype' => 'ezstring', 'value' => 'Nachrichten' ) ) ) ),
            59 => array( 'remote_id' => 'archive-object' ),
            77 => array( 'remote_id' => 'user-77', 'class' => 'user', 'section' => 'users', 'always_available' => true, 'initial_language' => 'eng-GB',
                         'is_user' => true, 'parent' => 'remote:members-node', 'locations' => array(), 'states' => array(),
                         'user_groups' => array( 'remote:members-object' ), 'enabled' => true,
                         'languages' => array( 'eng-GB' => array( 'first_name' => array( 'datatype' => 'ezstring', 'value' => 'Private' ),
                                                                  'last_name' => array( 'datatype' => 'ezstring', 'value' => 'Person' ),
                                                                  'team' => array( 'datatype' => 'ezstring', 'value' => 'Editors' ),
                                                                  'user_account' => array( 'datatype' => 'ezuser', 'value' => null ) ) ) ),
        );
        $s->classes = array( 7 => array( 'identifier' => 'news', 'names' => array( 'eng-GB' => 'News' ), 'groups' => array( 'Content' ),
                                         'attributes' => array( 'title' => array( 'datatype' => 'ezstring' ) ) ) );
        $s->classes['news'] = $s->classes[7];
        $s->roles = array( 3 => array( 'name' => 'Editor', 'policies' => array( array( 'module' => 'content', 'function' => 'read',
                                                                                      'limitations' => array( 'Class' => array( 'news' ) ) ) ),
                                       'notes' => array() ) );
        $s->sections = array( 1 => array( 'identifier' => 'standard', 'name' => 'Standard', 'navigation_part' => 'ezcontentnavigationpart' ),
                              6 => array( 'identifier' => 'archive', 'name' => 'Archive', 'navigation_part' => 'ezcontentnavigationpart' ) );
        $s->groups = array( 4 => array( 'identifier' => 'review', 'names' => array( 'eng-GB' => 'Review' ),
                                        'states' => array( 'open' => array( 'eng-GB' => 'Open' ) ), 'default_language' => 'eng-GB' ) );
        $this->source = $s;
    }

    protected function tearDown(): void
    {
        if ( $this->dir !== null && is_dir( $this->dir ) )
        {
            foreach ( glob( $this->dir . '/*' ) as $f )
                unlink( $f );
            rmdir( $this->dir );
        }
    }

    private function norm( $name, array $data, array $options = array() )
    {
        return expMigrationRecordingEvent::normalise( $name, $data, $this->source, $options );
    }

    /** RT-1 */
    public function testStepMap()
    {
        foreach ( expMigrationRecordingEvent::STEPS as $name => $step )
        {
            $this->assertMatchesRegularExpression( '/^(content|access|system)\.[a-z_.]+$/', $name );
            $this->assertCount( 2, $step );
            $this->assertMatchesRegularExpression( '/^[a-z_]+$/', $step[0] );
            $this->assertMatchesRegularExpression( '/^[a-z]+$/', $step[1] );
        }
        $this->assertSame( array( 'content', 'create' ), expMigrationRecordingEvent::stepOf( 'content.object.create' ) );
        $this->assertNull( expMigrationRecordingEvent::stepOf( 'content.node.view' ) );
        $this->assertTrue( expMigrationRecordingEvent::recorded( 'content.node.move' ) );
        $this->assertFalse( expMigrationRecordingEvent::recorded( 'content.node.move', array( 'content.object.create' ) ) );
        $this->assertFalse( expMigrationRecordingEvent::recorded( 'access.session.login' ) );
        foreach ( array( 'content.object.publish', 'content.node.move', 'content.class.change', 'access.role.assign', 'access.user.create',
                         'system.setting.write', 'system.workflow.trigger.change', 'content.urlalias.change', 'content.node.section',
                         'content.object.translate' ) as $required )
            $this->assertContains( $required, expMigrationRecordingEvent::names(), "the brief's $required is recorded" );
    }

    /** RT-2 */
    public function testContentCreate()
    {
        $e = $this->norm( 'content.object.create', array( 'object' => array( 'type' => 'object', 'id' => 58 ) ) );
        $this->assertSame( 'object remote:news-object', $e['subject'] );
        $step = $e['step'];
        $this->assertSame( array( 'content', 'create', 'folder', 'remote:root-node', 'news-object' ),
                           array( $step['type'], $step['mode'], $step['class'], $step['parent'], $step['remote_id'] ) );
        $this->assertSame( 'News', $step['fields']['eng-GB']['name'] );
        $this->assertSame( 'Nachrichten', $step['fields']['ger-DE']['name'] );
        $this->assertSame( array( 'remote:archive-object' ), $step['fields']['eng-GB']['related'] );
        $this->assertArrayNotHasKey( 'owner_account', $step['fields']['eng-GB'] );
        $this->assertSame( array( 'owner_account (a user account)' ), $e['omitted'] );
        $this->assertSame( array( 'ez_lock' => 'not_locked' ), $step['states'] );
        $this->assertSame( 'standard', $step['section'] );

        $u = $this->norm( 'content.object.publish', array( 'object' => array( 'id' => 58 ), 'after' => array( 'changed_attributes' => array( 'name' ) ) ) );
        $this->assertSame( array( 'content', 'update', 'remote:news-object' ), array( $u['step']['type'], $u['step']['mode'], $u['step']['match'] ) );
        $this->assertArrayNotHasKey( 'class', $u['step'] );
        $this->assertSame( array( 'name' ), $u['step']['changed'] );

        $t = $this->norm( 'content.object.translation.remove', array( 'object' => array( 'id' => 58 ), 'before' => array( 'languages' => array( 'eng-GB', 'nor-NO' ) ),
                                                                       'after' => array( 'languages' => array( 'eng-GB' ) ) ) );
        $this->assertSame( array( 'nor-NO' ), $t['step']['remove_translations'] );
        $this->assertStringNotContainsString( '"actor"', expMigrationRecordingEvent::encode( $e ) );
        $this->assertSame( $e, expMigrationRecordingEvent::decode( expMigrationRecordingEvent::encode( $e ) ) );
        $this->assertSame( 'content/create object remote:news-object', expMigrationRecordingEvent::line( $e ) );
    }

    /** RT-3 */
    public function testUserLeftOut()
    {
        $e = $this->norm( 'content.object.create', array( 'object' => array( 'id' => 77 ) ) );
        $this->assertSame( array( 'user', 'create', 'user-77', array( 'remote:members-object' ), true ),
                           array( $e['step']['type'], $e['step']['mode'], $e['step']['remote_id'], $e['step']['groups'], $e['step']['enabled'] ) );
        $this->assertArrayNotHasKey( 'fields', $e['step'] );
        foreach ( array( 'login', 'email', 'password', 'first_name', 'last_name', 'team' ) as $left )
            $this->assertContains( $left, $e['omitted'] );
        $json = expMigrationRecordingEvent::encode( $e );
        foreach ( array( 'Private', 'Person', 'Editors' ) as $personal )
            $this->assertStringNotContainsString( $personal, $json );

        $kept = $this->norm( 'access.user.change', array( 'object' => array( 'type' => 'user', 'id' => 77, 'login' => 'someone' ) ),
                             array( 'keepUserAttributes' => array( 'team' ) ) );
        $this->assertSame( array( 'user', 'update', 'remote:user-77' ), array( $kept['step']['type'], $kept['step']['mode'], $kept['step']['match'] ) );
        $this->assertSame( 'Editors', $kept['step']['fields']['eng-GB']['team'] );
        $this->assertStringNotContainsString( 'someone', expMigrationRecordingEvent::encode( $kept ), 'the login of the audit event is not recorded' );
        $this->assertNotContains( 'team', $kept['omitted'] );

        $removed = $this->norm( 'access.user.remove', array( 'object' => array( 'type' => 'user', 'id' => 77, 'login' => 'someone', 'email' => 'a@b.c' ) ) );
        $this->assertSame( array( 'user', 'delete', 'remote:user-77' ), array( $removed['step']['type'], $removed['step']['mode'], $removed['step']['match'] ) );
        $this->assertStringNotContainsString( 'a@b.c', expMigrationRecordingEvent::encode( $removed ) );
    }

    /** RT-4 */
    public function testNodeEvents()
    {
        $move = $this->norm( 'content.node.move', array( 'object' => array( 'type' => 'node', 'id' => 61 ), 'after' => array( 'parent' => 60 ) ) );
        $this->assertSame( array( 'location', 'move', 'remote:archive-node', 'remote:news-node' ),
                           array( $move['step']['type'], $move['step']['mode'], $move['step']['match'], $move['step']['parent'] ) );
        $hide = $this->norm( 'content.node.hide', array( 'object' => array( 'type' => 'node', 'id' => 61 ) ) );
        $this->assertSame( array( 'type' => 'location', 'mode' => 'hide', 'match' => 'remote:archive-node' ), $hide['step'] );
        $sort = $this->norm( 'content.node.sort', array( 'object' => array( 'type' => 'node', 'id' => 60 ), 'after' => array( 'field' => 9, 'order' => 1 ) ) );
        $this->assertSame( array( 'name', 'asc' ), array( $sort['step']['sort_field'], $sort['step']['sort_order'] ), 'the values of the event, not the stored ones' );
        $priority = $this->norm( 'content.node.priority', array( 'object' => array( 'type' => 'node', 'id' => 2 ), 'after' => array( 'priorities' => array( '60' => 1, '61' => 2, '999' => 3 ) ) ) );
        $this->assertSame( array( 'remote:news-node' => 1, 'remote:archive-node' => 2 ), $priority['step']['child_priorities'] );
        $section = $this->norm( 'content.node.section', array( 'object' => array( 'type' => 'node', 'id' => 60 ), 'target' => array( 'type' => 'section', 'id' => 6 ) ) );
        $this->assertSame( array( 'section', 'assign', 'archive', 'remote:news-node' ),
                           array( $section['step']['type'], $section['step']['mode'], $section['step']['match'], $section['step']['subtree'] ) );
        $swap = $this->norm( 'content.node.swap', array( 'object' => array( 'type' => 'node', 'id' => 60 ), 'target' => array( 'type' => 'node', 'id' => 61 ) ) );
        $this->assertSame( array( 'remote:news-node', 'remote:archive-node' ), array( $swap['step']['match'], $swap['step']['with'] ) );
        $add = $this->norm( 'content.node.add', array( 'object' => array( 'type' => 'object', 'id' => 58 ), 'after' => array( 'node' => 61 ) ) );
        $this->assertSame( array( 'location', 'create', 'remote:news-object', 'remote:root-node', 'archive-node' ),
                           array( $add['step']['type'], $add['step']['mode'], $add['step']['object'], $add['step']['parent'], $add['step']['remote_id'] ) );
        $trash = $this->norm( 'content.node.remove.trash', array( 'object' => array( 'type' => 'node', 'id' => 61 ) ) );
        $this->assertSame( array( 'trash', 'move', 'remote:archive-node' ), array( $trash['step']['type'], $trash['step']['mode'], $trash['step']['match'] ) );
    }

    /** RT-5 */
    public function testClassRoleSectionState()
    {
        $class = $this->norm( 'content.class.create', array( 'object' => array( 'type' => 'class', 'id' => 7, 'identifier' => 'news' ) ) );
        $this->assertSame( array( 'class', 'create', 'news' ), array( $class['step']['type'], $class['step']['mode'], $class['step']['identifier'] ) );
        $this->assertSame( 'ezstring', $class['step']['attributes']['title']['datatype'] );
        $change = $this->norm( 'content.class.change', array( 'object' => array( 'type' => 'class', 'id' => 7, 'identifier' => 'news' ) ) );
        $this->assertSame( 'news', $change['step']['match'] );
        $this->assertArrayNotHasKey( 'identifier', $change['step'] );
        $gone = $this->norm( 'content.class.remove', array( 'object' => array( 'type' => 'class', 'id' => 99, 'identifier' => 'old' ) ) );
        $this->assertSame( array( 'class', 'delete', 'old' ), array( $gone['step']['type'], $gone['step']['mode'], $gone['step']['match'] ) );

        $policy = $this->norm( 'access.policy.add', array( 'object' => array( 'type' => 'policy', 'id' => 5 ), 'target' => array( 'type' => 'role', 'id' => 3, 'name' => 'Editor' ) ) );
        $this->assertSame( array( 'role', 'update', 'Editor' ), array( $policy['step']['type'], $policy['step']['mode'], $policy['step']['match'] ) );
        $this->assertSame( array( 'news' ), $policy['step']['policies'][0]['limitations']['Class'] );
        $create = $this->norm( 'access.role.create', array( 'object' => array( 'type' => 'role', 'id' => 3, 'name' => 'Editor' ) ) );
        $this->assertSame( 'Editor', $create['step']['name'] );

        $assign = $this->norm( 'access.role.assign', array( 'object' => array( 'type' => 'role', 'id' => 3, 'name' => 'Editor' ),
                                                             'target' => array( 'type' => 'group', 'id' => 12 ),
                                                             'after' => array( 'limitation' => array( 'identifier' => 'Subtree', 'value' => '/1/2/60/', 'node' => 60 ) ) ) );
        $this->assertSame( array( 'role', 'assign', 'Editor', 'remote:members-object', array( 'subtree' => 'remote:news-node' ) ),
                           array( $assign['step']['type'], $assign['step']['mode'], $assign['step']['match'], $assign['step']['group'], $assign['step']['limitation'] ) );
        $unassign = $this->norm( 'access.role.unassign', array( 'object' => array( 'type' => 'role', 'id' => 3, 'name' => 'Editor' ),
                                                                 'target' => array( 'type' => 'user', 'id' => 77 ),
                                                                 'before' => array( 'limitation' => array( 'identifier' => 'Section', 'value' => '6' ) ) ) );
        $this->assertSame( array( 'remote:user-77', array( 'section' => 'archive' ) ), array( $unassign['step']['user'], $unassign['step']['limitation'] ) );

        $section = $this->norm( 'content.section.change', array( 'object' => array( 'type' => 'section', 'id' => 6, 'identifier' => 'archive' ) ) );
        $this->assertSame( array( 'section', 'ensure', 'archive', 'Archive' ), array( $section['step']['type'], $section['step']['mode'], $section['step']['match'], $section['step']['name'] ) );
        $state = $this->norm( 'content.state.change', array( 'object' => array( 'type' => 'state_group', 'id' => 4, 'identifier' => 'review' ) ) );
        $this->assertSame( array( 'object_state_group', 'ensure', 'review' ), array( $state['step']['type'], $state['step']['mode'], $state['step']['match'] ) );
        $this->assertSame( array( 'open' => array( 'eng-GB' => 'Open' ) ), $state['step']['states'] );
    }

    /** RT-6 */
    public function testSettingsTriggersAliases()
    {
        $set = $this->norm( 'system.setting.write', array( 'object' => array( 'type' => 'setting', 'file' => 'site.ini', 'block' => 'SiteSettings', 'variable' => 'SiteName',
                                                                             'scope' => 'siteaccess:site', 'path' => 'settings/siteaccess/site/site.ini.append.php' ),
                                                         'after' => array( 'value' => 'News site', 'set' => true ) ) );
        $this->assertSame( array( 'ini', 'set', 'site.ini', 'siteaccess:site', 'SiteSettings', 'SiteName', 'News site' ),
                           array( $set['step']['type'], $set['step']['mode'], $set['step']['file'], $set['step']['scope'], $set['step']['block'], $set['step']['variable'], $set['step']['value'] ) );
        $secret = $this->norm( 'system.setting.write', array( 'object' => array( 'type' => 'setting', 'file' => 'site.ini', 'block' => 'MailSettings', 'variable' => 'TransportPassword',
                                                                                'path' => 'settings/override/site.ini.append.php' ),
                                                            'after' => array( 'value' => '***', 'set' => true ) ) );
        $this->assertArrayNotHasKey( 'value', $secret['step'] );
        $this->assertTrue( $secret['step']['secret'] );
        $this->assertSame( 'override', $secret['step']['scope'] );
        $this->assertContains( 'value (a secret)', $secret['omitted'] );
        $removed = $this->norm( 'system.setting.write', array( 'object' => array( 'file' => 'site.ini', 'block' => 'A', 'variable' => 'B', 'path' => 'extension/expnews/settings/site.ini.append.php' ),
                                                             'after' => array( 'value' => null, 'set' => false ) ) );
        $this->assertSame( array( 'remove', 'extension:expnews' ), array( $removed['step']['mode'], $removed['step']['scope'] ) );

        $trigger = $this->norm( 'system.workflow.trigger.change', array( 'after' => array( 'workflows' => array( 'content/publish/b' => 3, 'content/read/a' => null ) ) ) );
        $this->assertSame( array( 'module' => 'content', 'function' => 'publish', 'connect' => 'b', 'workflow' => 3 ), $trigger['step']['triggers'][0] );
        $this->assertNull( $trigger['step']['triggers'][1]['workflow'] );

        $alias = $this->norm( 'content.urlalias.change', array( 'object' => array( 'type' => 'alias', 'id' => 'news/latest' ), 'target' => array( 'type' => 'node', 'id' => 60 ),
                                                              'verb' => 'add', 'after' => array( 'alias' => 'news/latest' ) ) );
        $this->assertSame( array( 'url_alias', 'ensure', 'news/latest', 'remote:news-node' ),
                           array( $alias['step']['type'], $alias['step']['mode'], $alias['step']['path'], $alias['step']['node'] ) );
        $dropped = $this->norm( 'content.urlalias.change', array( 'object' => array( 'type' => 'alias', 'id' => 'feedback-removed' ), 'target' => array( 'type' => 'node', 'id' => 60 ),
                                                                'verb' => 'remove', 'before' => array( 'removed' => 'selected' ) ) );
        $this->assertSame( array( 'delete', 'remote:news-node' ), array( $dropped['step']['mode'], $dropped['step']['match'] ) );
    }

    /** RT-7 */
    public function testNothingToRecord()
    {
        $this->assertNull( $this->norm( 'content.object.publish', array( 'object' => array( 'id' => 404 ) ) ) );
        $this->assertNull( $this->norm( 'content.node.move', array( 'object' => array( 'type' => 'node', 'id' => 404 ), 'after' => array( 'parent' => 2 ) ) ) );
        $this->assertNull( $this->norm( 'content.node.view', array( 'object' => array( 'type' => 'node', 'id' => 2 ) ) ) );
        $this->assertNull( $this->norm( 'system.setting.write', array( 'object' => array( 'file' => 'site.ini' ) ) ) );
    }

    /** RT-8 */
    public function testPlan()
    {
        $create = $this->norm( 'content.object.create', array( 'object' => array( 'id' => 58 ) ) );
        $update = $create;
        $update['step'] = array( 'type' => 'content', 'mode' => 'update', 'match' => 'remote:news-object', 'fields' => array( 'eng-GB' => array( 'name' => 'Latest' ) ) );
        $update['audit'] = 'content.object.publish';
        $assign = $this->norm( 'access.role.assign', array( 'object' => array( 'type' => 'role', 'id' => 3, 'name' => 'Editor' ), 'target' => array( 'type' => 'group', 'id' => 12 ) ) );
        $class = $this->norm( 'content.class.create', array( 'object' => array( 'type' => 'class', 'id' => 7, 'identifier' => 'news' ) ) );
        $classGone = $this->norm( 'content.class.remove', array( 'object' => array( 'type' => 'class', 'id' => 7, 'identifier' => 'news' ) ) );

        $plan = expMigrationRecordingGenerator::plan( array( $create, $assign, $update, $assign, $class, $classGone ) );
        $this->assertCount( 3, $plan, 'one content create, two assignments, the class created and removed is nothing' );
        $this->assertSame( array( 'content', 'create' ), array( $plan[0]['step']['type'], $plan[0]['step']['mode'] ) );
        $this->assertSame( 'Latest', $plan[0]['step']['fields']['eng-GB']['name'] );
        $this->assertSame( 'remote:root-node', $plan[0]['step']['parent'], 'the create keeps its parent' );
        $this->assertArrayNotHasKey( 'match', $plan[0]['step'] );
        $this->assertSame( array( 'content.object.create', 'content.object.publish' ), $plan[0]['audit'] );
        $this->assertSame( array( 'role', 'role' ), array( $plan[1]['step']['type'], $plan[2]['step']['type'] ) );

        $updateThenDelete = expMigrationRecordingGenerator::plan( array( $update, array( 'audit' => 'content.object.remove', 'subject' => $update['subject'],
                                                                                        'step' => array( 'type' => 'content', 'mode' => 'delete', 'match' => 'remote:news-object' ) ) ) );
        $this->assertSame( 'delete', $updateThenDelete[0]['step']['mode'] );
        $this->assertTrue( expMigrationRecordingGenerator::foldable( 'content', 'update' ) );
        $this->assertFalse( expMigrationRecordingGenerator::foldable( 'location', 'move' ) );
        $this->assertSame( array( 'content', 'role' ), expMigrationRecordingEvent::stepTypes( array( $create, $assign ) ) );
    }

    /** RT-9 */
    public function testActiveFileAndIds()
    {
        $this->dir = dirname( __DIR__, 5 ) . '/var/tmp/migration-recording-test-' . getmypid() . '-' . bin2hex( random_bytes( 3 ) );
        $file = $this->dir . '/active.json';
        $this->assertSame( array(), expMigrationRecording::active( $file ) );
        $list = array( array( 'recording_id' => '20261101-120000-a1b2c3', 'kind' => 'session', 'user_id' => 14, 'started_at' => 1 ),
                       array( 'recording_id' => '20261101-120100-d4e5f6', 'kind' => 'site', 'user_id' => 14, 'started_at' => 2 ),
                       array( 'recording_id' => 'not an id', 'kind' => 'site', 'user_id' => 0 ) );
        $this->assertTrue( expMigrationRecording::writeActive( $file, $list ) );
        $active = expMigrationRecording::active( $file );
        $this->assertCount( 2, $active, 'a broken entry is left out' );
        $this->assertCount( 2, expMigrationRecording::matching( $active, 14 ) );
        $this->assertSame( array( '20261101-120100-d4e5f6' ), array_column( expMigrationRecording::matching( $active, 10 ), 'recording_id' ) );
        $this->assertSame( array(), expMigrationRecording::matching( array( $active[0] ), 0 ), 'anonymous never matches a session' );
        $for = expMigrationRecording::forUser( 14, $file );
        $this->assertSame( array( '20261101-120000-a1b2c3', '20261101-120100-d4e5f6' ), array( $for['mine']['recording_id'], $for['site']['recording_id'] ) );
        $this->assertNull( expMigrationRecording::forUser( 10, $file )['mine'] );
        $this->assertTrue( expMigrationRecording::writeActive( $file, array() ) );
        $this->assertFileDoesNotExist( $file );

        $this->assertTrue( expMigrationRecording::validId( expMigrationRecording::newId() ) );
        $this->assertFalse( expMigrationRecording::validId( '../x' ) );
        $ids = array();
        for ( $i = 0; $i < 50; $i++ )
            $ids[] = expMigrationRecording::eventId();
        $sorted = $ids;
        sort( $sorted, SORT_STRING );
        $this->assertSame( $sorted, $ids, 'event ids sort in the order they were made' );
        $this->assertSame( 26, strlen( $ids[0] ) );

        $this->assertSame( array(), expMigrationRecording::startProblems( 'session', 14, 'x' ) );
        $this->assertNotEmpty( expMigrationRecording::startProblems( 'session', 0, '' ) );
        $this->assertNotEmpty( expMigrationRecording::startProblems( 'everyone', 14, '' ) );
        $this->assertNotEmpty( expMigrationRecording::startProblems( 'site', 1, str_repeat( 'x', 256 ) ) );
        $this->assertSame( 'extension:expnews', expMigrationRecordingEvent::settingScope( 'extension/expnews/settings/x.ini.append.php' ) );
        $this->assertSame( 'name', expMigrationRecordingEvent::sortFieldName( 9 ) );
        $this->assertSame( 'path', expMigrationRecordingEvent::sortFieldName( 99 ) );
    }

    /** RT-10 */
    public function testAdminHelpers()
    {
        $root = dirname( __DIR__, 5 );
        require_once $root . '/kernel/private/classes/runnable/runnable.php';
        require_once $root . '/kernel/private/classes/runnable/moduleview.php';
        require_once $root . '/kernel/private/classes/views/migration/recording.php';
        $view = '\\Exponential\\View\\Kernel\\Migration\\Recording';
        $this->assertSame( '/content/view/full/2', $view::redirectTarget( '/content/view/full/2' ) );
        foreach ( array( '', 'content/view', '//evil.example/x', 'https://evil.example/', "/a\nb", '/a\\b' ) as $bad )
            $this->assertSame( '', $view::redirectTarget( $bad ), "refused: $bad" );
        $this->assertTrue( $view::mayStop( array( 'kind' => 'site', 'user_id' => 14 ), 10 ) );
        $this->assertTrue( $view::mayStop( array( 'kind' => 'session', 'user_id' => 14 ), 14 ) );
        $this->assertFalse( $view::mayStop( array( 'kind' => 'session', 'user_id' => 14 ), 10 ) );

        $plan = array( array( 'step' => array( 'type' => 'content', 'mode' => 'create' ) ), array( 'step' => array( 'type' => 'reference', 'mode' => 'set' ) ) );
        $registry = new expMigrationStepRegistry();
        $registry->register( 'reference', 'expMigrationStepReference' );
        $this->assertSame( array( 'content' ), expMigrationRecordingGenerator::missingStepTypes( $plan, $registry ) );
    }
}
