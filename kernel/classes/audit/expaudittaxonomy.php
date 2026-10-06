<?php
/**
 * The audit taxonomy registry (doc/bc/6.0/audit.md, "Taxonomy", "Naming rules", "The event catalogue").
 *
 * A name is 3 to 6 ranks of [a-z][a-z0-9_]*, joined by dots, in one of five domains (content, access, system,
 * commerce, data). The kernel's catalogue is built in (each name with its severity, its default and its channel);
 * extensions add branches through [AuditEventSettings] Branches[<ext>]=<class implementing expAuditTaxonomyBranch>.
 *
 * decide( $name ) answers, from the settings, whether a name is recorded, in which channel, with which severity,
 * and whether it is written at once (ImmediateEvents[]). Patterns are "content.*", "content.node.remove.*" or a
 * whole name; "*" stands for one or more whole ranks at the end and the pattern also matches the name itself
 * without them. The most specific pattern (most literal ranks) wins; on a tie the later line wins, and Disabled[]
 * counts as later than Enabled[]. The answers are compiled once per settings state (expAuditConfig's hash) and kept
 * for the life of the process, so a name that is off costs one array lookup.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expAuditTaxonomy
{
    const NAME_PATTERN = '/^(content|access|system|commerce|data)(\.[a-z][a-z0-9_]*){2,5}$/';

    /** @var string[] RFC 5424 severities, most severe first (index = the numeric severity) */
    public static $severities = array( 'emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug' );

    /** @var array name => decision, for the settings state in $compiledFor */
    protected static $compiled = array();

    /** @var string|null */
    protected static $compiledFor = null;

    /** @var array|null name => definition, catalogue plus branches, for $registryFor */
    protected static $registry = null;

    /** @var string|null */
    protected static $registryFor = null;

    /** @var array Branch problems found while building the registry: branch => why */
    protected static $problems = array();

    /**
     * The kernel's catalogue: name => severity, default (on | off | always | sampled), channel ('' = the channel
     * of the file it describes). 135 names, generated from the catalogue tables of doc/bc/6.0/audit.md.
     *
     * @var array
     */
    protected static $catalogue = array(
        'content.object.create' => array( 'severity' => 'info', 'default' => 'off', 'channel' => 'content' ),
        'content.object.publish' => array( 'severity' => 'info', 'default' => 'off', 'channel' => 'content' ),
        'content.object.translate' => array( 'severity' => 'info', 'default' => 'off', 'channel' => 'content' ),
        'content.object.translation.remove' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'content' ),
        'content.version.remove' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'content' ),
        'content.node.move' => array( 'severity' => 'info', 'default' => 'on', 'channel' => 'content' ),
        'content.node.copy' => array( 'severity' => 'info', 'default' => 'off', 'channel' => 'content' ),
        'content.node.add' => array( 'severity' => 'info', 'default' => 'off', 'channel' => 'content' ),
        'content.node.remove' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'content' ),
        'content.node.remove.trash' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'content' ),
        'content.object.remove' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'content' ),
        'content.object.purge' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'content' ),
        'content.object.restore' => array( 'severity' => 'info', 'default' => 'on', 'channel' => 'content' ),
        'content.trash.empty' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'content' ),
        'content.node.hide' => array( 'severity' => 'info', 'default' => 'on', 'channel' => 'content' ),
        'content.node.reveal' => array( 'severity' => 'info', 'default' => 'on', 'channel' => 'content' ),
        'content.node.swap' => array( 'severity' => 'info', 'default' => 'on', 'channel' => 'content' ),
        'content.node.section' => array( 'severity' => 'info', 'default' => 'on', 'channel' => 'content' ),
        'content.object.state' => array( 'severity' => 'info', 'default' => 'on', 'channel' => 'content' ),
        'content.node.main' => array( 'severity' => 'info', 'default' => 'off', 'channel' => 'content' ),
        'content.node.sort' => array( 'severity' => 'info', 'default' => 'off', 'channel' => 'content' ),
        'content.node.priority' => array( 'severity' => 'info', 'default' => 'off', 'channel' => 'content' ),
        'content.object.always_available' => array( 'severity' => 'info', 'default' => 'off', 'channel' => 'content' ),
        'content.object.initial_language' => array( 'severity' => 'info', 'default' => 'off', 'channel' => 'content' ),
        'content.urlalias.change' => array( 'severity' => 'info', 'default' => 'off', 'channel' => 'content' ),
        'content.class.create' => array( 'severity' => 'info', 'default' => 'on', 'channel' => 'content' ),
        'content.class.change' => array( 'severity' => 'info', 'default' => 'on', 'channel' => 'content' ),
        'content.class.remove' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'content' ),
        'content.class.copy' => array( 'severity' => 'info', 'default' => 'off', 'channel' => 'content' ),
        'content.section.change' => array( 'severity' => 'info', 'default' => 'on', 'channel' => 'content' ),
        'content.section.remove' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'content' ),
        'content.state.change' => array( 'severity' => 'info', 'default' => 'on', 'channel' => 'content' ),
        'content.state.remove' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'content' ),
        'content.job.create' => array( 'severity' => 'info', 'default' => 'on', 'channel' => 'content' ),
        'content.job.start' => array( 'severity' => 'info', 'default' => 'on', 'channel' => 'content' ),
        'content.job.finish' => array( 'severity' => 'info', 'default' => 'on', 'channel' => 'content' ),
        'content.job.fail' => array( 'severity' => 'warning', 'default' => 'on', 'channel' => 'content' ),
        'content.job.cancel' => array( 'severity' => 'info', 'default' => 'on', 'channel' => 'content' ),
        'content.job.resume' => array( 'severity' => 'info', 'default' => 'on', 'channel' => 'content' ),
        'content.node.view' => array( 'severity' => 'info', 'default' => 'sampled', 'channel' => 'read' ),
        'content.search.query' => array( 'severity' => 'info', 'default' => 'sampled', 'channel' => 'read' ),
        'content.object.download' => array( 'severity' => 'info', 'default' => 'sampled', 'channel' => 'read' ),
        'access.session.login' => array( 'severity' => 'info', 'default' => 'on', 'channel' => 'access' ),
        'access.session.login.failed' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'access' ),
        'access.session.logout' => array( 'severity' => 'info', 'default' => 'on', 'channel' => 'access' ),
        'access.session.regenerate' => array( 'severity' => 'info', 'default' => 'off', 'channel' => 'access' ),
        'access.session.expire' => array( 'severity' => 'info', 'default' => 'off', 'channel' => 'access' ),
        'access.session.reauth' => array( 'severity' => 'info', 'default' => 'on', 'channel' => 'access' ),
        'access.session.reauth.failed' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'access' ),
        'access.session.revoke' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'access' ),
        'access.user.lock' => array( 'severity' => 'warning', 'default' => 'on', 'channel' => 'access' ),
        'access.user.unlock' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'access' ),
        'access.permission.refused' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'access' ),
        'access.token.refused' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'access' ),
        'access.view.sensitive' => array( 'severity' => 'info', 'default' => 'always', 'channel' => 'access' ),
        'access.user.create' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'access' ),
        'access.user.activate' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'access' ),
        'access.user.enable' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'access' ),
        'access.user.disable' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'access' ),
        'access.user.remove' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'access' ),
        'access.user.email.change' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'access' ),
        'access.user.login.change' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'access' ),
        'access.user.password.change' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'access' ),
        'access.user.password.change.failed' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'access' ),
        'access.user.password.reset.request' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'access' ),
        'access.user.password.reset' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'access' ),
        'access.user.password.reset.failed' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'access' ),
        'access.role.create' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'access' ),
        'access.role.change' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'access' ),
        'access.role.remove' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'access' ),
        'access.role.copy' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'access' ),
        'access.role.assign' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'access' ),
        'access.role.unassign' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'access' ),
        'access.policy.add' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'access' ),
        'access.policy.remove' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'access' ),
        'access.apikey.create' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'access' ),
        'access.apikey.revoke' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'access' ),
        'access.apikey.use' => array( 'severity' => 'info', 'default' => 'on', 'channel' => 'access' ),
        'access.apikey.use.failed' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'access' ),
        'system.setting.write' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'system' ),
        'system.setting.undo' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'system' ),
        'system.extension.change' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'system' ),
        'system.cache.clear' => array( 'severity' => 'info', 'default' => 'on', 'channel' => 'system' ),
        'system.cronjob.run' => array( 'severity' => 'info', 'default' => 'on', 'channel' => 'system' ),
        'system.cronjob.fail' => array( 'severity' => 'warning', 'default' => 'on', 'channel' => 'system' ),
        'system.command.run' => array( 'severity' => 'info', 'default' => 'on', 'channel' => 'system' ),
        'system.package.install' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'system' ),
        'system.package.uninstall' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'system' ),
        'system.package.import' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'system' ),
        'system.install.run' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'system' ),
        'system.upgrade.run' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'system' ),
        'system.velocity.deploy' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'system' ),
        'system.repair.queue' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'system' ),
        'system.maintenance.change' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'system' ),
        'system.template.change' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'system' ),
        'system.workflow.trigger.change' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'system' ),
        'system.error.fatal' => array( 'severity' => 'error', 'default' => 'on', 'channel' => 'system' ),
        'system.audit.enable' => array( 'severity' => 'notice', 'default' => 'always', 'channel' => 'system' ),
        'system.audit.disable' => array( 'severity' => 'warning', 'default' => 'always', 'channel' => 'system' ),
        'system.audit.setting.write' => array( 'severity' => 'notice', 'default' => 'always', 'channel' => 'system' ),
        'system.audit.read' => array( 'severity' => 'info', 'default' => 'always', 'channel' => 'system' ),
        'system.audit.export' => array( 'severity' => 'info', 'default' => 'always', 'channel' => 'system' ),
        'system.audit.rotate' => array( 'severity' => 'info', 'default' => 'always', 'channel' => 'system' ),
        'system.audit.archive' => array( 'severity' => 'info', 'default' => 'always', 'channel' => 'system' ),
        'system.audit.purge' => array( 'severity' => 'info', 'default' => 'always', 'channel' => 'system' ),
        'system.audit.pseudonymise' => array( 'severity' => 'info', 'default' => 'always', 'channel' => 'system' ),
        'system.audit.verify' => array( 'severity' => 'info', 'default' => 'always', 'channel' => 'system' ),
        'system.audit.chain.broken' => array( 'severity' => 'error', 'default' => 'always', 'channel' => 'system' ),
        'system.audit.chain.repair' => array( 'severity' => 'notice', 'default' => 'always', 'channel' => 'system' ),
        'system.audit.checkpoint' => array( 'severity' => 'info', 'default' => 'always', 'channel' => 'system' ),
        'system.audit.reindex' => array( 'severity' => 'info', 'default' => 'always', 'channel' => 'system' ),
        'system.audit.import' => array( 'severity' => 'info', 'default' => 'always', 'channel' => 'system' ),
        'system.audit.key.create' => array( 'severity' => 'notice', 'default' => 'always', 'channel' => 'system' ),
        'system.audit.key.rotate' => array( 'severity' => 'notice', 'default' => 'always', 'channel' => 'system' ),
        'system.audit.sink.failed' => array( 'severity' => 'warning', 'default' => 'always', 'channel' => 'system' ),
        'system.audit.alert' => array( 'severity' => 'warning', 'default' => 'always', 'channel' => 'system' ),
        'system.audit.overflow' => array( 'severity' => 'warning', 'default' => 'always', 'channel' => 'system' ),
        'system.audit.file.open' => array( 'severity' => 'info', 'default' => 'always', 'channel' => '' ),
        'system.audit.file.close' => array( 'severity' => 'info', 'default' => 'always', 'channel' => '' ),
        'commerce.order.delete' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'commerce' ),
        'commerce.order.purge' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'commerce' ),
        'commerce.order.item.remove' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'commerce' ),
        'commerce.order.create' => array( 'severity' => 'info', 'default' => 'off', 'channel' => 'commerce' ),
        'commerce.order.status' => array( 'severity' => 'info', 'default' => 'off', 'channel' => 'commerce' ),
        'commerce.order.archive' => array( 'severity' => 'info', 'default' => 'off', 'channel' => 'commerce' ),
        'commerce.order.unarchive' => array( 'severity' => 'info', 'default' => 'off', 'channel' => 'commerce' ),
        'commerce.basket.checkout' => array( 'severity' => 'info', 'default' => 'off', 'channel' => 'commerce' ),
        'commerce.payment.approve' => array( 'severity' => 'info', 'default' => 'off', 'channel' => 'commerce' ),
        'commerce.vat.change' => array( 'severity' => 'info', 'default' => 'on', 'channel' => 'commerce' ),
        'commerce.currency.change' => array( 'severity' => 'info', 'default' => 'on', 'channel' => 'commerce' ),
        'commerce.discount.change' => array( 'severity' => 'info', 'default' => 'on', 'channel' => 'commerce' ),
        'data.export.csv' => array( 'severity' => 'info', 'default' => 'on', 'channel' => 'commerce' ),
        'data.export.package' => array( 'severity' => 'info', 'default' => 'on', 'channel' => 'commerce' ),
        'data.export.pdf' => array( 'severity' => 'info', 'default' => 'off', 'channel' => 'commerce' ),
        'data.import.csv' => array( 'severity' => 'info', 'default' => 'on', 'channel' => 'commerce' ),
        'data.import.dba' => array( 'severity' => 'info', 'default' => 'on', 'channel' => 'commerce' ),
        'data.import.rss' => array( 'severity' => 'info', 'default' => 'off', 'channel' => 'commerce' ),
        'data.infocollection.remove' => array( 'severity' => 'notice', 'default' => 'on', 'channel' => 'commerce' ),
        'data.infocollection.view' => array( 'severity' => 'info', 'default' => 'off', 'channel' => 'commerce' ),
        'data.index.rebuild' => array( 'severity' => 'info', 'default' => 'on', 'channel' => 'commerce' ),
    );

    /**
     * The kernel catalogue.
     *
     * @return array name => array( severity, default, channel )
     */
    public static function catalogue()
    {
        return self::$catalogue;
    }

    /**
     * Every known name: the catalogue and the branches the settings register. A branch that names no class, a
     * class not implementing expAuditTaxonomyBranch, or a name claimed twice is reported in problems() and left
     * out (the first claim wins).
     *
     * @param array|null $config expAuditConfig::get()
     * @return array name => array( severity, default, channel, label, branch )
     */
    public static function registry( ?array $config = null )
    {
        $config = $config ?: expAuditConfig::get();
        $key = md5( serialize( $config['branches'] ) );
        if ( self::$registry !== null && self::$registryFor === $key )
            return self::$registry;
        $registry = array();
        foreach ( self::$catalogue as $name => $def )
            $registry[$name] = $def + array( 'label' => self::label( $name ), 'branch' => 'kernel' );
        $problems = array();
        foreach ( $config['branches'] as $branch => $class )
        {
            if ( !is_string( $class ) || $class === '' || !class_exists( $class ) )
            {
                $problems[$branch] = 'the class does not exist';
                continue;
            }
            if ( !is_subclass_of( $class, 'expAuditTaxonomyBranch' ) )
            {
                $problems[$branch] = 'the class does not implement expAuditTaxonomyBranch';
                continue;
            }
            try
            {
                $object = new $class();
                foreach ( (array)$object->events() as $name => $def )
                {
                    if ( !self::isValidName( $name ) )
                    {
                        $problems[$branch . ':' . $name] = 'not a valid name';
                        continue;
                    }
                    if ( isset( $registry[$name] ) )
                    {
                        $problems[$branch . ':' . $name] = 'already registered by ' . $registry[$name]['branch'];
                        continue;
                    }
                    $def = (array)$def;
                    $registry[$name] = array(
                        'severity' => isset( $def['severity'] ) && in_array( $def['severity'], self::$severities, true ) ? $def['severity'] : 'info',
                        'default' => isset( $def['default'] ) && in_array( $def['default'], array( 'on', 'off', 'always', 'sampled' ), true ) ? $def['default'] : 'off',
                        'channel' => isset( $def['channel'] ) ? (string)$def['channel'] : '',
                        'label' => isset( $def['label'] ) ? (string)$def['label'] : self::label( $name ),
                        'privacy' => isset( $def['privacy'] ) ? (array)$def['privacy'] : array(),
                        'branch' => $branch,
                    );
                }
            }
            catch ( Throwable $e )
            {
                $problems[$branch] = 'events() failed: ' . $e->getMessage();
            }
        }
        self::$problems = $problems;
        self::$registryFor = $key;
        return self::$registry = $registry;
    }

    /** @return array The branch problems of the last registry() */
    public static function problems()
    {
        return self::$problems;
    }

    /** @return bool A well-formed name */
    public static function isValidName( $name )
    {
        return is_string( $name ) && strlen( $name ) <= 128 && preg_match( self::NAME_PATTERN, $name ) === 1;
    }

    /**
     * Why a name pattern of a filter (exp:audit --name, the console's (name), the fetch functions) is not valid, or
     * null when it is. The forms: '*' (every name); a prefix of one to five ranks followed by '.*' (access.*,
     * access.session.*, content.node.remove.*), which matches the prefix itself and every name below it; or a
     * whole name (3 to 6 ranks). The first rank is a domain (content, access, system, commerce, data); a rank is
     * lower case letters, digits and '_', starting with a letter. A '*' anywhere else (access.session.login*,
     * access.*.failed) is refused rather than read as a literal, which would match nothing, or dropped, which
     * would match everything.
     *
     * @param mixed $pattern
     * @return string|null
     */
    public static function patternProblem( $pattern )
    {
        $forms = "allowed: *, a prefix of ranks ending in .* (access.*, access.session.*) or a whole name (access.session.login)";
        if ( !is_string( $pattern ) || trim( $pattern ) === '' )
            return "an empty name pattern; $forms";
        $p = trim( $pattern );
        $shown = "'" . ( strlen( $p ) > 80 ? substr( $p, 0, 80 ) . '...' : $p ) . "'";
        if ( strlen( $p ) > 128 )
            return "$shown is longer than 128 characters";
        if ( $p === '*' )
            return null;
        $wild = substr( $p, -2 ) === '.*';
        $body = $wild ? substr( $p, 0, -2 ) : $p;
        if ( strpos( $body, '*' ) !== false )
        {
            $hint = preg_replace( '/\.?\*+$/', '', $body );
            return "$shown: a * may only be the whole pattern or the last rank after a dot" .
                   ( $hint !== '' && strpos( $hint, '*' ) === false ? " (did you mean '$hint.*'?)" : '' ) . "; $forms";
        }
        $ranks = explode( '.', $body );
        foreach ( $ranks as $r )
            if ( !preg_match( '/^[a-z][a-z0-9_]*$/', $r ) )
                return "$shown: '$r' is not a rank (lower case letters, digits and _, starting with a letter); $forms";
        if ( !in_array( $ranks[0], array( 'content', 'access', 'system', 'commerce', 'data' ), true ) )
            return "$shown: '{$ranks[0]}' is not a domain (content, access, system, commerce, data)";
        if ( $wild )
            return count( $ranks ) <= 5 ? null : "$shown: a prefix has at most 5 ranks";
        if ( count( $ranks ) < 3 )
            return "$shown is not a whole name (3 to 6 ranks): for every name below it write '$body.*'";
        if ( count( $ranks ) > 6 )
            return "$shown: a name has at most 6 ranks";
        return null;
    }

    /** @return bool A valid name pattern of a filter (see patternProblem()) */
    public static function isValidPattern( $pattern )
    {
        return self::patternProblem( $pattern ) === null;
    }

    /**
     * A readable label from a name: content.node.remove.trash -> "Node remove trash".
     *
     * @param string $name
     * @return string
     */
    public static function label( $name )
    {
        $parts = explode( '.', $name );
        array_shift( $parts );
        return ucfirst( str_replace( '_', ' ', implode( ' ', $parts ) ) );
    }

    /**
     * Whether a pattern matches a name, and how specific it is.
     *
     * @param string $pattern content.*, content.node.remove.*, or a name
     * @param string $name
     * @return int -1 no match, else the number of literal ranks (a whole name: its ranks + 1, so it beats any *)
     */
    public static function match( $pattern, $name )
    {
        $pattern = trim( $pattern );
        if ( $pattern === '*' )
            return 0;
        if ( substr( $pattern, -2 ) === '.*' )
        {
            $prefix = substr( $pattern, 0, -2 );
            if ( $name === $prefix || strncmp( $name, $prefix . '.', strlen( $prefix ) + 1 ) === 0 )
                return substr_count( $prefix, '.' ) + 1;
            return -1;
        }
        return $pattern === $name ? substr_count( $name, '.' ) + 2 : -1;
    }

    /**
     * The best match of a list of patterns: later lines win ties.
     *
     * @param string[] $patterns
     * @param string $name
     * @return int -1 none, else the specificity
     */
    public static function bestMatch( array $patterns, $name )
    {
        $best = -1;
        foreach ( $patterns as $p )
        {
            $m = self::match( $p, $name );
            if ( $m >= $best && $m >= 0 )
                $best = $m;
        }
        return $best;
    }

    /**
     * What happens to a name.
     *
     * @param string $name
     * @param array|null $config expAuditConfig::get()
     * @return array on (bool), always (bool), sampled (bool), channel, severity, immediate (bool), valid (bool)
     */
    public static function decide( $name, ?array $config = null )
    {
        $config = $config ?: expAuditConfig::get();
        if ( self::$compiledFor !== $config['hash'] )
        {
            self::$compiled = array();
            self::$compiledFor = $config['hash'];
        }
        if ( isset( self::$compiled[$name] ) )
            return self::$compiled[$name];
        if ( count( self::$compiled ) > 2000 )
            self::$compiled = array();
        return self::$compiled[$name] = self::compile( $name, $config );
    }

    /**
     * @param string $name
     * @param array $config
     * @return array
     */
    protected static function compile( $name, array $config )
    {
        $off = array( 'on' => false, 'always' => false, 'sampled' => false, 'channel' => null, 'severity' => 'info',
                      'immediate' => false, 'valid' => self::isValidName( $name ) );
        if ( !$off['valid'] )
            return $off;
        $registry = self::registry( $config );
        $def = isset( $registry[$name] ) ? $registry[$name] : null;
        if ( $def === null && preg_match( '/^system\.audit\./', $name ) )
            $def = array( 'severity' => 'info', 'default' => 'always', 'channel' => '' );
        $default = $def ? $def['default'] : 'off';
        $always = $default === 'always';

        $enabled = self::bestMatch( $config['enabledPatterns'], $name );
        $disabled = self::bestMatch( $config['disabledPatterns'], $name );
        if ( $always )
            $on = true;
        elseif ( $default === 'sampled' )
            $on = $config['reads'] && $disabled < 0;
        elseif ( $enabled < 0 && $disabled < 0 )
            $on = $default === 'on';
        else
            $on = $enabled > $disabled;

        $severity = $def ? $def['severity'] : 'info';
        if ( !$always && self::rank( $severity ) > self::rank( $config['minSeverity'] ) )
            $on = false;

        return array(
            'on' => $on,
            'always' => $always,
            'sampled' => $default === 'sampled',
            'channel' => self::channel( $name, $def, $config ),
            'severity' => $severity,
            'immediate' => !$config['buffering'] || self::bestMatch( $config['immediate'], $name ) >= 0,
            'valid' => true,
        );
    }

    /**
     * The channel of a name: an exact Route[] entry, else the branch's own channel, else the most specific Route[]
     * pattern, else DefaultChannel; only channels in Channels[] (else DefaultChannel).
     *
     * @return string
     */
    public static function channel( $name, $def, array $config )
    {
        $channel = null;
        if ( isset( $config['routes'][$name] ) )
            $channel = $config['routes'][$name];
        elseif ( $def && !empty( $def['branch'] ) && $def['branch'] !== 'kernel' && $def['channel'] !== '' )
            $channel = $def['channel'];
        else
        {
            $best = -1;
            foreach ( $config['routes'] as $pattern => $c )
            {
                $m = self::match( $pattern, $name );
                if ( $m >= 0 && $m >= $best )
                {
                    $best = $m;
                    $channel = $c;
                }
            }
        }
        if ( $channel === null || !in_array( $channel, $config['channels'], true ) )
            $channel = $config['defaultChannel'];
        return $channel;
    }

    /**
     * @param string $severity
     * @return int 0 (emergency) ... 7 (debug); unknown = 6 (info)
     */
    public static function rank( $severity )
    {
        $i = array_search( strtolower( (string)$severity ), self::$severities, true );
        return $i === false ? 6 : $i;
    }

    /** Forgets the compiled answers and the registry (tests, a changed setting). */
    public static function reset()
    {
        self::$compiled = array();
        self::$compiledFor = null;
        self::$registry = null;
        self::$registryFor = null;
    }
}
