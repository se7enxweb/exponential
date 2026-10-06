<?php
/**
 * File containing the expRolePolicySentence class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A policy of a role in words, for the role pages (role/view, role/edit): "May read content in section Standard, of
 * the classes Article and Folder".
 *
 * Works on plain arrays, so it needs no database and can be tested on its own. A policy is
 *
 *     array( 'module' => 'content', 'function' => 'read', 'limitations' => array(
 *         array( 'identifier' => 'Section', 'label' => 'Section', 'values' => array( 'Standard' ), 'denies' => false ),
 *         ... ) )
 *
 * where 'values' are the names the role pages show for the stored values (eZPolicyLimitation's
 * values_as_array_with_names) and 'denies' says that no handler evaluates the limitation, so the policy gives no
 * access (eZPolicyLimitation::deniesWithoutHandler()). expRolePage builds these arrays from eZPolicy objects.
 *
 * Every text is plain: the templates escape it. Every text goes through the translator, the context
 * design/admin/role/sentence; a test passes a translator of its own.
 */
class expRolePolicySentence
{
    /** The translation context of every text of this class */
    const CONTEXT = 'design/admin/role/sentence';

    /** Values named in a sentence before the rest is counted ("A, B, C and 4 more") */
    const MAX_NAMED_VALUES = 6;

    /** @var callable|null ( $source, array $arguments ) => string */
    protected $translator;

    /**
     * @param callable|null $translator ( string $source, array $arguments ) => string; null uses ezpI18n::tr() in the
     *        context CONTEXT, or a plain replacement of the arguments when ezpI18n is not loaded
     */
    public function __construct( $translator = null )
    {
        $this->translator = is_callable( $translator ) ? $translator : null;
    }

    /**
     * What a policy allows, in words and in parts.
     *
     * @param array $policy see the class description
     * @return array sentence (the whole sentence), action ("read content"), conditions (one text per limitation),
     *         full_access (every function of every module), all_functions (every function of one module),
     *         manages_roles (may change roles, so may give itself any access), denies (a limitation no handler
     *         evaluates: the policy gives no access), module, function
     */
    public function describe( array $policy )
    {
        $module = isset( $policy['module'] ) ? (string)$policy['module'] : '';
        $function = isset( $policy['function'] ) ? (string)$policy['function'] : '';
        $limitations = isset( $policy['limitations'] ) && is_array( $policy['limitations'] ) ? $policy['limitations'] : array();

        $action = $this->action( $module, $function );
        $conditions = array();
        $denies = false;
        foreach ( $limitations as $limitation )
        {
            if ( !is_array( $limitation ) )
                continue;
            $conditions[] = $this->condition( $limitation );
            if ( !empty( $limitation['denies'] ) )
                $denies = true;
        }

        if ( $conditions )
            $sentence = $this->t( 'May %action, %conditions', array( '%action' => $action, '%conditions' => implode( ', ', $conditions ) ) );
        else
            $sentence = $this->t( 'May %action', array( '%action' => $action ) );

        return array(
            'sentence'      => $sentence,
            'action'        => $action,
            'conditions'    => $conditions,
            'full_access'   => $module === '*',
            'all_functions' => $module !== '*' && $function === '*',
            'manages_roles' => self::managesRoles( $module, $function ),
            'denies'        => $denies,
            'module'        => $module,
            'function'      => $function,
        );
    }

    /**
     * Whether a policy lets its users change roles and policies, and so give themselves any access: the role module
     * (which has no functions of its own) or every module.
     *
     * @param string $module
     * @param string $function
     * @return bool
     */
    public static function managesRoles( $module, $function )
    {
        return $module === '*' || $module === 'role';
    }

    /**
     * The policies in groups by module, in the order the modules first appear: module => list of policies. The
     * policies keep their order inside a group.
     *
     * @param array $policies arrays with a 'module'
     * @return array
     */
    public static function groupByModule( array $policies )
    {
        $groups = array();
        foreach ( $policies as $key => $policy )
        {
            $module = is_array( $policy ) && isset( $policy['module'] ) ? (string)$policy['module'] : '';
            $groups[$module][$key] = $policy;
        }
        return $groups;
    }

    /**
     * What a policy lets do, without its limitations: "read content", "use every function of the shop module", "do
     * everything, in every module".
     *
     * @param string $module
     * @param string $function
     * @return string
     */
    public function action( $module, $function )
    {
        if ( $module === '*' )
            return $this->t( 'do everything, in every module' );

        $modules = self::moduleActions();
        if ( $function === '*' || $function === '' )
        {
            if ( isset( $modules[$module] ) )
                return $this->t( $modules[$module] );
            return $this->t( 'use every function of the %module module', array( '%module' => $module ) );
        }

        $functions = self::functionActions();
        if ( isset( $functions[$module . '/' . $function] ) )
            return $this->t( $functions[$module . '/' . $function] );
        return $this->t( 'use the function %function of the %module module', array( '%function' => $function, '%module' => $module ) );
    }

    /**
     * One limitation in words: "in section Standard", "of the classes Article and Folder", "Tag: News".
     *
     * @param array $limitation identifier, label, values, denies
     * @return string
     */
    public function condition( array $limitation )
    {
        $identifier = isset( $limitation['identifier'] ) ? (string)$limitation['identifier'] : '';
        $label = isset( $limitation['label'] ) && trim( (string)$limitation['label'] ) !== '' ? (string)$limitation['label'] : $identifier;
        $values = array();
        foreach ( isset( $limitation['values'] ) && is_array( $limitation['values'] ) ? $limitation['values'] : array() as $value )
        {
            if ( is_scalar( $value ) && trim( (string)$value ) !== '' )
                $values[] = (string)$value;
        }
        $count = count( $values );

        if ( $count === 0 )
            return $this->t( '%label: none of the chosen values exists any more', array( '%label' => $label ) );

        $list = $this->joinList( $values );
        $one = $count === 1;
        switch ( $identifier )
        {
            case 'Section':
                return $this->t( $one ? 'in section %list' : 'in the sections %list', array( '%list' => $list ) );
            case 'Class':
                return $this->t( $one ? 'of class %list' : 'of the classes %list', array( '%list' => $list ) );
            case 'ParentClass':
                return $this->t( $one ? 'below objects of class %list' : 'below objects of the classes %list', array( '%list' => $list ) );
            case 'ParentDepth':
                return $this->t( $one ? 'at depth %list' : 'at the depths %list', array( '%list' => $list ) );
            case 'Owner':
                return $this->t( 'only content they own' );
            case 'ParentOwner':
                return $this->t( 'only below content they own' );
            case 'Group':
                return $this->t( 'only content owned by a member of their groups' );
            case 'ParentGroup':
                return $this->t( 'only below content owned by a member of their groups' );
            case 'Node':
                return $this->t( $one ? 'on the node %list' : 'on the nodes %list', array( '%list' => $list ) );
            case 'Subtree':
                return $this->t( $one ? 'in the subtree %list' : 'in the subtrees %list', array( '%list' => $list ) );
            case 'SiteAccess':
                return $this->t( $one ? 'on the siteaccess %list' : 'on the siteaccesses %list', array( '%list' => $list ) );
            case 'Language':
                return $this->t( $one ? 'in the language %list' : 'in the languages %list', array( '%list' => $list ) );
            case 'NewSection':
                return $this->t( $one ? 'into section %list' : 'into the sections %list', array( '%list' => $list ) );
            case 'NewState':
                return $this->t( $one ? 'to the state %list' : 'to the states %list', array( '%list' => $list ) );
            case 'FunctionList':
                return $this->t( $one ? 'only the function %list' : 'only the functions %list', array( '%list' => $list ) );
        }
        if ( strpos( $identifier, 'StateGroup_' ) === 0 )
            return $this->t( $one ? 'in the state %list (%label)' : 'in the states %list (%label)', array( '%list' => $list, '%label' => $label ) );

        return $this->t( '%label: %list', array( '%label' => $label, '%list' => $list ) );
    }

    /**
     * "A", "A and B", "A, B and C"; more than one past MAX_NAMED_VALUES "A, B, C, D, E, F and 3 more" (one more is named, never "and 1 more").
     *
     * @param string[] $values
     * @return string
     */
    public function joinList( array $values )
    {
        $values = array_values( $values );
        $count = count( $values );
        if ( $count === 0 )
            return '';
        if ( $count === 1 )
            return $values[0];
        if ( $count > self::MAX_NAMED_VALUES + 1 )
        {
            $named = array_slice( $values, 0, self::MAX_NAMED_VALUES );
            return $this->t( '%list and %count more', array( '%list' => implode( ', ', $named ), '%count' => $count - self::MAX_NAMED_VALUES ) );
        }
        $last = array_pop( $values );
        return $this->t( '%list and %last', array( '%list' => implode( ', ', $values ), '%last' => $last ) );
    }

    /**
     * What a policy over every function of a known module lets do. Other modules get a general text.
     *
     * @return array module => source text
     */
    public static function moduleActions()
    {
        return array(
            'content' => 'do everything with content',
            'user'    => 'do everything in the user module (log in, edit accounts, activate users)',
            'role'    => 'manage roles and policies',
            'setup'   => 'use every setup and maintenance page',
            'section' => 'manage sections',
            'class'   => 'manage content classes',
            'state'   => 'manage object states',
            'shop'    => 'do everything in the shop',
            'rss'     => 'manage and read RSS feeds',
            'url'     => 'manage links',
            'search'  => 'use every search function',
        );
    }

    /**
     * What a policy over one function of a known module lets do. Other functions get a general text.
     *
     * @return array "module/function" => source text
     */
    public static function functionActions()
    {
        return array(
            'content/read'               => 'read content',
            'content/create'             => 'create content',
            'content/edit'               => 'edit content',
            'content/remove'             => 'remove content',
            'content/move'               => 'move content',
            'content/hide'               => 'hide and reveal content',
            'content/translate'          => 'translate content',
            'content/manage_locations'   => 'manage the locations of content',
            'content/versionread'        => 'read old versions and drafts of content',
            'content/versionremove'      => 'remove versions of content',
            'content/diff'               => 'compare versions of content',
            'content/pdf'                => 'view content as PDF',
            'content/view_embed'         => 'view embedded content',
            'content/reverserelatedlist' => 'see what links to content',
            'content/restore'            => 'restore content from the trash',
            'content/cleantrash'         => 'empty the trash',
            'content/bookmark'           => 'use bookmarks',
            'content/dashboard'          => 'use the dashboard',
            'content/pendinglist'        => 'see the list of pending content',
            'content/tipafriend'         => 'send content to a friend',
            'content/urltranslator'      => 'manage URL aliases',
            'content/publish'            => 'publish content',
            'user/login'                 => 'log in',
            'user/password'              => 'change their password',
            'user/preferences'           => 'change their preferences',
            'user/register'              => 'register an account',
            'user/selfedit'              => 'edit their own account',
            'user/activation'            => 'activate user accounts',
            'setup/administrate'         => 'use the setup pages',
            'setup/managecache'          => 'clear caches',
            'setup/system_info'          => 'view the system information',
            'section/assign'             => 'assign sections to content',
            'section/edit'               => 'edit sections',
            'section/view'               => 'view sections',
            'state/assign'               => 'change the states of content',
            'state/administrate'         => 'manage object states',
            'rss/feed'                   => 'read RSS feeds',
            'rss/edit'                   => 'edit RSS feeds',
            'shop/buy'                   => 'buy in the shop',
            'shop/administrate'          => 'administer the shop',
            'shop/edit_status'           => 'change the status of orders',
            'shop/setup'                 => 'set up the shop',
            'class/edit'                 => 'edit content classes',
            'notification/use'           => 'use notifications',
            'collaboration/use'          => 'use collaboration',
            'search/read'                => 'search',
            'tags/view'                  => 'view tags',
            'tags/edit'                  => 'edit tags',
            'ezjscore/call'              => 'call server functions from the browser',
            'websitetoolbar/use'         => 'use the website toolbar',
        );
    }

    /**
     * Every source text this class can produce, for the translation file and the test. Texts of moduleActions() and
     * functionActions() included.
     *
     * @return string[]
     */
    public static function sourceTexts()
    {
        $texts = array(
            'May %action', 'May %action, %conditions', 'do everything, in every module',
            'use every function of the %module module', 'use the function %function of the %module module',
            '%label: none of the chosen values exists any more',
            'in section %list', 'in the sections %list', 'of class %list', 'of the classes %list',
            'below objects of class %list', 'below objects of the classes %list', 'at depth %list', 'at the depths %list',
            'only content they own', 'only below content they own', 'only content owned by a member of their groups',
            'only below content owned by a member of their groups', 'on the node %list', 'on the nodes %list',
            'in the subtree %list', 'in the subtrees %list', 'on the siteaccess %list', 'on the siteaccesses %list',
            'in the language %list', 'in the languages %list', 'into section %list', 'into the sections %list',
            'to the state %list', 'to the states %list', 'only the function %list', 'only the functions %list',
            'in the state %list (%label)', 'in the states %list (%label)', '%label: %list',
            '%list and %count more', '%list and %last',
        );
        return array_values( array_unique( array_merge( $texts, array_values( self::moduleActions() ), array_values( self::functionActions() ) ) ) );
    }

    /**
     * @param string $source
     * @param array $arguments
     * @return string
     */
    protected function t( $source, array $arguments = array() )
    {
        if ( $this->translator )
            return (string)call_user_func( $this->translator, $source, $arguments );
        if ( class_exists( 'ezpI18n' ) && class_exists( 'eZINI' ) )
            return ezpI18n::tr( self::CONTEXT, $source, null, $arguments );
        return strtr( $source, $arguments );
    }
}
