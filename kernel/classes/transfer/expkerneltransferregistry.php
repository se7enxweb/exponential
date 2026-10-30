<?php
/**
 * File containing the expKernelTransferRegistry class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The transfer registry: every import and export tool of the installation, as each declares itself in
 * kernelservices.ini (a [Service_transfer.<id>] block tagged transfer.tool, expKernelTransferTool). Every link to a
 * tool, every "Looking for…?" box, the context entries of the content view's Import/Export menu, the class and Setup
 * pages and the admin search read it, so they never point at a tool that is not active or that the user may not use,
 * and a new tool appears everywhere by declaring itself (doc/eng-us/features/6.0/transfer-registry.md).
 *
 *   $registry = expKernelTransferRegistry::instance();
 *   $registry->available();                                  // the tools the current user may use
 *   $registry->select( array( 'direction' => 'import', 'scope' => 'subtree' ) );
 *   $registry->actions( 'container', array( '%nodeID%' => 42 ) );
 *   $registry->related( 'xrowextract' );                     // for the "Looking for…?" box of a tool's page
 *   $registry->search( 'csv spreadsheet import' );
 *
 * The kernel's service transfer.registry (kernelservices.ini); worker-scoped, so it holds the tools only. What the
 * user may use is decided on each call (the $access callable, the current user's policies by default).
 *
 * @since 6.0.15
 * @package kernel
 */
class expKernelTransferRegistry
{
    /** The help page that answers "Which import or export tool do I need?" */
    const HELP_PAGE = 'help/import-export-tools.md';

    /** @var expKernelTransferTool[] id => tool, in list order */
    protected $tools = array();

    /** @var callable|null module => bool, eZModule::exists() when null */
    protected $moduleExists;

    /**
     * @param expKernelTransferTool[] $tools
     * @param callable|null $moduleExists module => bool (tests); eZModule::exists() when null
     */
    public function __construct( array $tools = array(), $moduleExists = null )
    {
        foreach ( $tools as $tool )
        {
            if ( $tool instanceof expKernelTransferTool && $tool->id() !== '' )
                $this->tools[$tool->id()] = $tool;
        }
        uasort( $this->tools, function ( expKernelTransferTool $a, expKernelTransferTool $b ) {
            $order = $a->get( 'weight' ) <=> $b->get( 'weight' );
            return $order !== 0 ? $order : strcasecmp( $a->get( 'name' ), $b->get( 'name' ) );
        } );
        $this->moduleExists = $moduleExists;
    }

    /**
     * The registry of the current siteaccess: the kernel service transfer.registry, or, when the container cannot
     * give it (a Velocity worker started before the class existed, a broken definition), read from the settings.
     *
     * @return expKernelTransferRegistry
     */
    public static function instance()
    {
        if ( class_exists( 'expKernelServices' ) )
        {
            try
            {
                $registry = expKernelServices::get( 'transfer.registry' );
                if ( $registry instanceof self )
                    return $registry;
            }
            catch ( Exception $e )
            {
                eZDebug::writeWarning( 'transfer.registry: ' . $e->getMessage(), __METHOD__ );
            }
        }
        return self::fromSettings();
    }

    /**
     * The factory of the service transfer.registry: the services tagged transfer.tool.
     *
     * @return expKernelTransferRegistry
     */
    public static function fromContainer()
    {
        $tools = array();
        foreach ( expKernelServices::tagged( expKernelTransferTool::TAG ) as $id => $tool )
        {
            if ( $tool instanceof expKernelTransferTool )
                $tools[] = $tool;
        }
        return new self( $tools );
    }

    /**
     * The registry read straight from kernelservices.ini (every [Service_*] block tagged transfer.tool and not
     * switched off), without the container.
     *
     * @param eZINI|null $ini
     * @param callable|null $moduleExists
     * @return expKernelTransferRegistry
     */
    public static function fromSettings( $ini = null, $moduleExists = null )
    {
        if ( !$ini instanceof eZINI )
            $ini = eZINI::instance( 'kernelservices.ini' );
        $tools = array();
        foreach ( $ini->groups() as $group => $values )
        {
            if ( strncmp( $group, 'Service_', 8 ) !== 0 || !is_array( $values ) )
                continue;
            $enabled = isset( $values['Enabled'] ) ? strtolower( trim( (string)$values['Enabled'] ) ) : 'true';
            if ( in_array( $enabled, array( 'false', 'disabled', 'no', '0' ), true ) )
                continue;
            if ( !in_array( expKernelTransferTool::TAG, array_map( 'trim', (array)( isset( $values['Tags'] ) ? $values['Tags'] : array() ) ), true ) )
                continue;
            $tools[] = expKernelTransferTool::fromBlock( substr( $group, 8 ), $values );
        }
        return new self( $tools, $moduleExists );
    }

    /**
     * Every declared tool, id => tool, whether or not it is active.
     *
     * @return expKernelTransferTool[]
     */
    public function tools()
    {
        return $this->tools;
    }

    /**
     * One declared tool by its id (transfer.package) or by its short name (package), or null.
     *
     * @param string $id
     * @return expKernelTransferTool|null
     */
    public function tool( $id )
    {
        $id = (string)$id;
        if ( isset( $this->tools[$id] ) )
            return $this->tools[$id];
        if ( isset( $this->tools['transfer.' . $id] ) )
            return $this->tools['transfer.' . $id];
        return null;
    }

    /**
     * The tools that are active here and that the user may use, id => tool.
     *
     * @param callable|null $access (module, function) => bool; the current user's policies when null
     * @return expKernelTransferTool[]
     */
    public function available( $access = null )
    {
        if ( $access === null )
            $access = expKernelTransferTool::currentUserAccess();
        $available = array();
        foreach ( $this->tools as $id => $tool )
        {
            if ( $tool->isActive( $this->moduleExists ) && $tool->isAllowed( $access ) )
                $available[$id] = $tool;
        }
        return $available;
    }

    /**
     * The available tools that match a filter: direction (import, export), scope, format, module, exclude (an id or
     * a list of ids), query (task words, best match first).
     *
     * @param array $filter
     * @param callable|null $access
     * @return expKernelTransferTool[]
     */
    public function select( array $filter = array(), $access = null )
    {
        $direction = isset( $filter['direction'] ) ? (string)$filter['direction'] : '';
        $scope = isset( $filter['scope'] ) ? (string)$filter['scope'] : '';
        $format = isset( $filter['format'] ) ? (string)$filter['format'] : '';
        $module = isset( $filter['module'] ) ? (string)$filter['module'] : '';
        $exclude = array();
        foreach ( (array)( isset( $filter['exclude'] ) ? $filter['exclude'] : array() ) as $id )
        {
            $tool = $this->tool( $id );
            if ( $tool !== null )
                $exclude[$tool->id()] = true;
        }
        $selected = array();
        foreach ( $this->available( $access ) as $id => $tool )
        {
            if ( isset( $exclude[$id] ) || !$tool->goes( $direction ) || !$tool->covers( $scope ) || !$tool->handles( $format ) )
                continue;
            if ( $module !== '' && !$tool->ownsModule( $module ) )
                continue;
            $selected[$id] = $tool;
        }
        if ( isset( $filter['query'] ) && trim( (string)$filter['query'] ) !== '' )
            $selected = $this->rank( $selected, (string)$filter['query'] );
        return $selected;
    }

    /**
     * The entries of a context (node, container, class, setup) of every available tool, placeholders replaced.
     *
     * @param string $context
     * @param array $values '%nodeID%' => 42, '%classID%' => 2, ...
     * @param callable|null $access
     * @param string $direction only import or export entries ('' for both)
     * @return array list of array( tool, name, context, direction, label, url )
     */
    public function actions( $context, array $values = array(), $access = null, $direction = '' )
    {
        if ( $access === null )
            $access = expKernelTransferTool::currentUserAccess();
        $actions = array();
        foreach ( $this->available( $access ) as $tool )
        {
            foreach ( $tool->actionsFor( (string)$context, $values, $access ) as $action )
            {
                if ( $direction !== '' && $action['direction'] !== $direction )
                    continue;
                $actions[] = $action + array( 'name' => $tool->get( 'name' ) );
            }
        }
        return $actions;
    }

    /**
     * The tools a "Looking for…?" box on a tool's page offers: every other available tool, the round-trip partners
     * first (both ways: those the tool names and those that name it), then the others in list order.
     *
     * @param string $toolOrModule a tool id, its short name, or the module of the page
     * @param callable|null $access
     * @return array list of array( tool, partner (bool) )
     */
    public function related( $toolOrModule, $access = null )
    {
        $self = $this->tool( $toolOrModule );
        if ( $self === null )
        {
            foreach ( $this->tools as $tool )
            {
                if ( $tool->ownsModule( $toolOrModule ) )
                {
                    $self = $tool;
                    break;
                }
            }
        }
        $selfId = $self !== null ? $self->id() : '';
        $partners = array();
        $others = array();
        foreach ( $this->available( $access ) as $id => $tool )
        {
            // itself, and a tool on the same page (RSS import and RSS export share rss/list)
            if ( $id === $selfId || ( $self !== null && $tool->get( 'entry_url' ) === $self->get( 'entry_url' ) ) )
                continue;
            $partner = $self !== null && ( in_array( $id, $self->get( 'round_trip' ), true ) || in_array( $selfId, $tool->get( 'round_trip' ), true ) );
            if ( $partner )
                $partners[] = array( 'tool' => $tool, 'partner' => true );
            else
                $others[] = array( 'tool' => $tool, 'partner' => false );
        }
        return array_merge( $partners, $others );
    }

    /**
     * The tool a module's pages belong to, or null.
     *
     * @param string $module
     * @return expKernelTransferTool|null
     */
    public function toolOfModule( $module )
    {
        foreach ( $this->tools as $tool )
        {
            if ( $tool->ownsModule( $module ) )
                return $tool;
        }
        return null;
    }

    /**
     * The help topics of a module's context help menu that the registry adds: the "Which import or export tool do I
     * need?" page and the tool's own help page, for a module that belongs to a tool; nothing otherwise.
     *
     * @param string $module
     * @return array list of page ids
     */
    public function helpTopicsForModule( $module )
    {
        $tool = $this->toolOfModule( $module );
        if ( $tool === null )
            return array();
        $topics = array( self::HELP_PAGE );
        if ( $tool->get( 'help_page' ) !== '' )
            $topics[] = $tool->get( 'help_page' );
        return array_values( array_unique( $topics ) );
    }

    /**
     * The available tools that match task words, best first; a tool matching none of the words is left out.
     *
     * @param string $query
     * @param callable|null $access
     * @return expKernelTransferTool[]
     */
    public function search( $query, $access = null )
    {
        return $this->rank( $this->available( $access ), (string)$query );
    }

    /**
     * The words of a query: lower case, at least two characters, without the words every tool would match.
     *
     * @param string $query
     * @return array
     */
    public static function words( $query )
    {
        $words = preg_split( '/[^\p{L}\p{N}.+-]+/u', mb_strtolower( (string)$query ), -1, PREG_SPLIT_NO_EMPTY );
        $stop = array( 'a', 'an', 'the', 'to', 'of', 'in', 'into', 'for', 'and', 'or', 'my', 'i', 'do', 'how', 'want',
                       'need', 'tool', 'tools', 'ein', 'eine', 'der', 'die', 'das', 'und', 'oder', 'in', 'zu', 'nach',
                       'ich', 'wie', 'mein', 'meine', 'werkzeug' );
        $result = array();
        foreach ( $words as $word )
        {
            $word = trim( $word, '.-+' );
            if ( mb_strlen( $word ) < 2 || in_array( $word, $stop, true ) )
                continue;
            $result[] = $word;
        }
        return array_values( array_unique( $result ) );
    }

    /**
     * @param expKernelTransferTool[] $tools
     * @param string $query
     * @return expKernelTransferTool[]
     */
    protected function rank( array $tools, $query )
    {
        $words = self::words( $query );
        if ( !$words )
            return array();
        $scores = array();
        $order = 0;
        foreach ( $tools as $id => $tool )
        {
            $score = $tool->score( $words );
            if ( $score > 0 )
                $scores[$id] = array( $score, $order++ );
        }
        uasort( $scores, function ( $a, $b ) {
            return $a[0] !== $b[0] ? $b[0] <=> $a[0] : $a[1] <=> $b[1];
        } );
        $ranked = array();
        foreach ( array_keys( $scores ) as $id )
            $ranked[$id] = $tools[$id];
        return $ranked;
    }
}
?>
