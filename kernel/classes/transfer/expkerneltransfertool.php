<?php
/**
 * File containing the expKernelTransferTool class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * One import or export tool of the transfer registry (expKernelTransferRegistry), as it declares itself in a
 * [Service_transfer.<id>] block of kernelservices.ini tagged transfer.tool (doc/eng-us/features/6.0/transfer-registry.md).
 *
 *   [Service_transfer.package]
 *   Class=expKernelTransferTool
 *   Factory=expKernelTransferTool::fromSettings
 *   Arguments[]=transfer.package
 *   Scope=worker
 *   Tags[]=transfer.tool
 *   Description=Move content, classes and designs between installations as package files.
 *   Name=Packages
 *   Direction=both                  import, export or both
 *   Scopes[]=subtree                site, subtree, object, class, data
 *   Formats[]=ezpkg
 *   EntryURL=package/list           the tool's main page; its first part is the tool's module
 *   Permission=package/read         module/function, several separated by "|" (any of them is enough)
 *   Modules[]=package               the modules whose pages belong to the tool (the module of EntryURL is always one)
 *   HelpPage=guides/packages.md     a help page id (below doc/eng-us/)
 *   RoundTrip[]=transfer.package    tools that read what this one writes, or write what it reads
 *   Keywords[]=copy to another site task words for the admin search and the "which tool" page
 *   Actions[]=container|export|Export this subtree as a package…|package/create/(creator)/ezcontentobject/(node)/%nodeID%/(subtree)/1
 *   Weight=10                       the order in lists (lower first, then the name)
 *   Engine=auto                     how its background runs go (expKernelTransferRunner): auto, expqueue, contentjob,
 *                                   process
 *   QueueJobType=xrowextract.export the expqueue job type of its runs, a [JobType_<type>] the tool declares in its
 *                                   expqueue.ini with Handler=expKernelTransferQueueHandler
 *   HistoryAllPermission=xrowextract/all_jobs   who sees every user's runs of the tool in the run history
 *                                   (expKernelTransferHistory); everybody else sees their own
 *
 * An action is context|direction|label|URL[|module/function]: context is node (any published item), container (an
 * item below which the user may create content), class (a class page), setup (the Setup pages); the URL may hold
 * %nodeID%, %objectID%, %classID% and %classIdentifier%; the optional permission replaces the tool's for this action.
 *
 * Immutable: it holds nothing of a request, so the container keeps it for the worker.
 *
 * @since 6.0.15
 * @package kernel
 */
class expKernelTransferTool
{
    /** The service tag every transfer tool carries */
    const TAG = 'transfer.tool';

    const DIRECTIONS = array( 'import', 'export', 'both' );

    const SCOPES = array( 'site', 'subtree', 'object', 'class', 'data' );

    const CONTEXTS = array( 'node', 'container', 'class', 'setup' );

    /** @var array */
    protected $data;

    /**
     * @param array $data id, name, description, direction, scopes, formats, entry_url, permissions, modules,
     *                    help_page, round_trip, keywords, actions, weight
     */
    public function __construct( array $data )
    {
        $direction = isset( $data['direction'] ) ? strtolower( trim( (string)$data['direction'] ) ) : '';
        if ( !in_array( $direction, self::DIRECTIONS, true ) )
            $direction = 'both';
        $entryUrl = isset( $data['entry_url'] ) ? trim( (string)$data['entry_url'], " \t/" ) : '';
        $modules = self::listOf( isset( $data['modules'] ) ? $data['modules'] : array(), true );
        $entryModule = $entryUrl !== '' ? strtolower( strtok( $entryUrl, '/' ) ) : '';
        if ( $entryModule !== '' && !in_array( $entryModule, $modules, true ) )
            array_unshift( $modules, $entryModule );

        $this->data = array(
            'id' => isset( $data['id'] ) ? (string)$data['id'] : '',
            'name' => isset( $data['name'] ) && trim( (string)$data['name'] ) !== '' ? trim( (string)$data['name'] ) : ( isset( $data['id'] ) ? (string)$data['id'] : '' ),
            'description' => isset( $data['description'] ) ? trim( (string)$data['description'] ) : '',
            'direction' => $direction,
            'scopes' => array_values( array_intersect( self::listOf( isset( $data['scopes'] ) ? $data['scopes'] : array(), true ), self::SCOPES ) ),
            'formats' => self::listOf( isset( $data['formats'] ) ? $data['formats'] : array() ),
            'entry_url' => $entryUrl,
            'module' => $entryModule,
            'permissions' => self::permissionList( isset( $data['permissions'] ) ? $data['permissions'] : array() ),
            'modules' => $modules,
            'help_page' => isset( $data['help_page'] ) ? trim( (string)$data['help_page'], " \t/" ) : '',
            'round_trip' => self::listOf( isset( $data['round_trip'] ) ? $data['round_trip'] : array() ),
            'keywords' => self::listOf( isset( $data['keywords'] ) ? $data['keywords'] : array() ),
            'actions' => self::actionList( isset( $data['actions'] ) ? $data['actions'] : array() ),
            'weight' => isset( $data['weight'] ) && is_numeric( $data['weight'] ) ? (int)$data['weight'] : 100,
            'engine' => isset( $data['engine'] ) && in_array( strtolower( trim( (string)$data['engine'] ) ), array( 'auto', 'expqueue', 'contentjob', 'process' ), true )
                        ? strtolower( trim( (string)$data['engine'] ) ) : 'auto',
            'queue_job_type' => isset( $data['queue_job_type'] ) && preg_match( '/^[A-Za-z0-9_.-]{1,64}$/', trim( (string)$data['queue_job_type'] ) )
                                ? trim( (string)$data['queue_job_type'] ) : '',
            'history_all_permissions' => self::permissionList( isset( $data['history_all_permissions'] ) ? $data['history_all_permissions'] : array() ),
        );
    }

    /**
     * The factory of the service: the tool of the [Service_<id>] block of kernelservices.ini.
     *
     * @param string $id the service id (transfer.package)
     * @param eZINI|null $ini kernelservices.ini (the current siteaccess's when null)
     * @return expKernelTransferTool
     */
    public static function fromSettings( $id, $ini = null )
    {
        if ( !$ini instanceof eZINI )
            $ini = eZINI::instance( 'kernelservices.ini' );
        $group = $ini->hasGroup( 'Service_' . $id ) ? $ini->group( 'Service_' . $id ) : array();
        return self::fromBlock( (string)$id, is_array( $group ) ? $group : array() );
    }

    /**
     * A tool from the values of its settings block.
     *
     * @param string $id
     * @param array $block
     * @return expKernelTransferTool
     */
    public static function fromBlock( $id, array $block )
    {
        $value = function ( $name, $default = '' ) use ( $block ) {
            return isset( $block[$name] ) ? $block[$name] : $default;
        };
        return new self( array(
            'id' => $id,
            'name' => $value( 'Name' ),
            'description' => $value( 'Description' ),
            'direction' => $value( 'Direction', 'both' ),
            'scopes' => $value( 'Scopes', array() ),
            'formats' => $value( 'Formats', array() ),
            'entry_url' => $value( 'EntryURL' ),
            'permissions' => $value( 'Permission', array() ),
            'modules' => $value( 'Modules', array() ),
            'help_page' => $value( 'HelpPage' ),
            'round_trip' => $value( 'RoundTrip', array() ),
            'keywords' => $value( 'Keywords', array() ),
            'actions' => $value( 'Actions', array() ),
            'weight' => $value( 'Weight', 100 ),
            'engine' => $value( 'Engine', 'auto' ),
            'queue_job_type' => $value( 'QueueJobType' ),
            'history_all_permissions' => $value( 'HistoryAllPermission', array() ),
        ) );
    }

    /**
     * @return string
     */
    public function id()
    {
        return $this->data['id'];
    }

    /**
     * One value: id, name, description, direction, scopes, formats, entry_url, module, permissions, modules,
     * help_page, round_trip, keywords, actions, weight.
     *
     * @param string $name
     * @return mixed
     */
    public function get( $name )
    {
        return isset( $this->data[$name] ) ? $this->data[$name] : null;
    }

    /**
     * Every value, as an array (what the template operators hand out).
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Whether it imports (direction import or both).
     *
     * @return bool
     */
    public function imports()
    {
        return $this->data['direction'] !== 'export';
    }

    /**
     * Whether it exports (direction export or both).
     *
     * @return bool
     */
    public function exports()
    {
        return $this->data['direction'] !== 'import';
    }

    /**
     * Whether it goes this way: import, export, or '' / both for any tool.
     *
     * @param string $direction
     * @return bool
     */
    public function goes( $direction )
    {
        $direction = strtolower( trim( (string)$direction ) );
        if ( $direction === 'import' )
            return $this->imports();
        if ( $direction === 'export' )
            return $this->exports();
        return true;
    }

    /**
     * Whether it works on this scope ('' for any).
     *
     * @param string $scope
     * @return bool
     */
    public function covers( $scope )
    {
        $scope = strtolower( trim( (string)$scope ) );
        return $scope === '' || in_array( $scope, $this->data['scopes'], true );
    }

    /**
     * Whether it reads or writes this format ('' for any); compared without case, "xlsx" finds "XLSX".
     *
     * @param string $format
     * @return bool
     */
    public function handles( $format )
    {
        $format = strtolower( trim( (string)$format ) );
        if ( $format === '' )
            return true;
        foreach ( $this->data['formats'] as $own )
        {
            if ( strtolower( $own ) === $format )
                return true;
        }
        return false;
    }

    /**
     * Whether a page of this module belongs to the tool.
     *
     * @param string $module
     * @return bool
     */
    public function ownsModule( $module )
    {
        return in_array( strtolower( trim( (string)$module ) ), $this->data['modules'], true );
    }

    /**
     * Whether its module exists in this installation and siteaccess (a tool whose extension is not active never
     * reaches the registry anyway: its block is not read).
     *
     * @param callable|null $moduleExists module name => bool (eZModule::exists() when null)
     * @return bool
     */
    public function isActive( $moduleExists = null )
    {
        if ( $this->data['module'] === '' )
            return false;
        if ( $moduleExists === null )
            return (bool)eZModule::exists( $this->data['module'] );
        return (bool)call_user_func( $moduleExists, $this->data['module'] );
    }

    /**
     * Whether the user may use it: one of its permissions (module/function) is not refused. A tool without a
     * permission needs the module's own: module/* is not asked, any function of the module will do.
     *
     * @param callable|null $access (module, function) => bool; the current user's policies when null
     * @param array|null $permissions the permissions to check instead of the tool's (an action's own)
     * @return bool
     */
    public function isAllowed( $access = null, $permissions = null )
    {
        if ( $access === null )
            $access = self::currentUserAccess();
        $permissions = $permissions === null ? $this->data['permissions'] : $permissions;
        if ( !$permissions )
            $permissions = array( array( $this->data['module'], false ) );
        foreach ( $permissions as $permission )
        {
            if ( call_user_func( $access, $permission[0], $permission[1] ) )
                return true;
        }
        return false;
    }

    /**
     * Whether the user sees every user's runs of the tool in the run history: one of its HistoryAllPermission
     * permissions is not refused. Without that setting everybody sees only their own runs.
     *
     * @param callable|null $access (module, function) => bool; the current user's policies when null
     * @return bool
     */
    public function mayReadAllRuns( $access = null )
    {
        if ( !$this->data['history_all_permissions'] )
            return false;
        return $this->isAllowed( $access, $this->data['history_all_permissions'] );
    }

    /**
     * The access check of the current user: (module, function) => whether it is not refused.
     *
     * @return callable
     */
    public static function currentUserAccess()
    {
        return function ( $module, $function ) {
            $user = eZUser::currentUser();
            if ( !$user instanceof eZUser )
                return false;
            $result = $user->hasAccessTo( $module, $function === false ? false : (string)$function );
            return isset( $result['accessWord'] ) && $result['accessWord'] !== 'no';
        };
    }

    /**
     * Its actions in a context, with the placeholders of $values replaced in the URL.
     *
     * @param string $context node, container, class or setup
     * @param array $values '%nodeID%' => 42, ...
     * @param callable|null $access as for isAllowed()
     * @return array list of array( tool, context, direction, label, url )
     */
    public function actionsFor( $context, array $values = array(), $access = null )
    {
        $actions = array();
        foreach ( $this->data['actions'] as $action )
        {
            if ( $action['context'] !== $context )
                continue;
            if ( $action['permissions'] && !$this->isAllowed( $access, $action['permissions'] ) )
                continue;
            $url = $action['url'];
            // a placeholder no value was given for: the action cannot be offered here (a value may be the
            // placeholder itself, for a menu whose script fills it in when it opens)
            preg_match_all( '/%[A-Za-z]+%/', $url, $placeholders );
            if ( array_diff( $placeholders[0], array_keys( $values ) ) )
                continue;
            if ( $values )
                $url = strtr( $url, array_map( 'strval', $values ) );
            $actions[] = array( 'tool' => $this->data['id'], 'context' => $context, 'direction' => $action['direction'],
                                'label' => $action['label'], 'url' => $url );
        }
        return $actions;
    }

    /**
     * How many of the words match the tool, weighted: the name 3, a keyword or a format 2, the description 1.
     *
     * @param array $words lower case words
     * @return int
     */
    public function score( array $words )
    {
        $name = mb_strtolower( $this->data['name'] );
        $description = mb_strtolower( $this->data['description'] );
        $keywords = mb_strtolower( implode( ' | ', $this->data['keywords'] ) );
        $formats = array_map( 'mb_strtolower', $this->data['formats'] );
        $score = 0;
        foreach ( $words as $word )
        {
            if ( $word === '' )
                continue;
            $hit = 0;
            if ( mb_strpos( $name, $word ) !== false )
                $hit = 3;
            else if ( in_array( $word, $formats, true ) || mb_strpos( $keywords, $word ) !== false )
                $hit = 2;
            else if ( mb_strpos( $description, $word ) !== false )
                $hit = 1;
            $score += $hit;
        }
        return $score;
    }

    /**
     * A list of non-empty trimmed strings from an INI value (a list, or one string).
     *
     * @param mixed $value
     * @param bool $lower lower case (scopes, modules)
     * @return array
     */
    protected static function listOf( $value, $lower = false )
    {
        $list = array();
        foreach ( (array)$value as $item )
        {
            if ( !is_scalar( $item ) )
                continue;
            $item = trim( (string)$item );
            if ( $item === '' )
                continue;
            $list[] = $lower ? strtolower( $item ) : $item;
        }
        return array_values( array_unique( $list ) );
    }

    /**
     * module/function pairs from Permission (a list, or one string with "|" between alternatives).
     *
     * @param mixed $value
     * @return array list of array( module, function|false )
     */
    protected static function permissionList( $value )
    {
        $pairs = array();
        foreach ( (array)$value as $item )
        {
            foreach ( explode( '|', (string)$item ) as $permission )
            {
                $permission = trim( $permission, " \t/" );
                if ( $permission === '' )
                    continue;
                $parts = explode( '/', $permission, 2 );
                $pairs[] = array( strtolower( $parts[0] ), isset( $parts[1] ) && $parts[1] !== '' && $parts[1] !== '*' ? $parts[1] : false );
            }
        }
        return $pairs;
    }

    /**
     * The actions from Actions[]: context|direction|label|URL[|permission].
     *
     * @param mixed $value
     * @return array
     */
    protected static function actionList( $value )
    {
        $actions = array();
        foreach ( (array)$value as $item )
        {
            $parts = array_map( 'trim', explode( '|', (string)$item ) );
            if ( count( $parts ) < 4 )
                continue;
            $context = strtolower( $parts[0] );
            $direction = strtolower( $parts[1] );
            if ( !in_array( $context, self::CONTEXTS, true ) || !in_array( $direction, array( 'import', 'export' ), true ) || $parts[2] === '' || $parts[3] === '' )
                continue;
            $actions[] = array( 'context' => $context, 'direction' => $direction, 'label' => $parts[2],
                                'url' => trim( $parts[3], '/' ),
                                'permissions' => self::permissionList( array_slice( $parts, 4 ) ) );
        }
        return $actions;
    }
}
?>
