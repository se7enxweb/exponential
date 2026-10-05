<?php
/**
 * File containing the expMailCategoryRegistry class.
 *
 * All categories of e-mail, in the order the preference page shows them:
 *
 *  1. mailpreferences.ini [CategorySettings] Categories[] with a block [Category_<identifier>] each. A category the
 *     shipped settings/mailpreferences.ini lists has source 'ini'; one an extension's mailpreferences.ini.append.php
 *     adds has source 'extension'.
 *  2. The admin's category manager (table expmail_category): a row with an INI category's identifier changes its
 *     name, description, default and double opt-in (essential stays as the INI says); a row with a new identifier
 *     is a category of source 'admin'.
 *  3. register(): categories added in code for this process.
 *
 * \code
 * $registry = expMailCategoryRegistry::instance();
 * foreach ( $registry->optional() as $category ) ...
 * $registry->get( 'newsletter' )->doubleOptIn;      // true
 * \endcode
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expMailCategoryRegistry
{
    /** @var expMailCategoryRegistry|null */
    protected static $instance = null;

    /** @var expMailCategory[]|null identifier => category */
    protected $categories = null;

    /** @var expMailCategory[] registered in code */
    protected $registered = array();

    /** @var mixed the request the list was loaded for (a persistent worker serves many) */
    protected $loadedFor = null;

    /** @var string[] identifier => source of the optional categories whose DefaultOn=true was refused (last load) */
    protected $refusedDefaultOn = array();

    /** @return expMailCategoryRegistry */
    public static function instance()
    {
        if ( self::$instance === null )
            self::$instance = new self();
        return self::$instance;
    }

    /** Forgets the merged list (after a change in the admin, or between requests of a persistent worker). */
    public static function reset()
    {
        if ( self::$instance !== null )
            self::$instance->categories = null;
    }

    /** @return expMailCategory[] identifier => category */
    public function all()
    {
        $request = isset( $_SERVER['REQUEST_TIME_FLOAT'] ) ? $_SERVER['REQUEST_TIME_FLOAT'] : 0;
        if ( $this->categories === null || $this->loadedFor !== $request )
        {
            $this->categories = $this->load();
            $this->loadedFor = $request;
        }
        return $this->categories;
    }

    /** @return expMailCategory[] the switchable categories */
    public function optional()
    {
        return array_filter( $this->all(), function ( expMailCategory $c ) { return !$c->essential; } );
    }

    /** @return expMailCategory[] the categories that are always sent */
    public function essential()
    {
        return array_filter( $this->all(), function ( expMailCategory $c ) { return $c->essential; } );
    }

    /**
     * @param string $identifier
     * @return expMailCategory|null
     */
    public function get( $identifier )
    {
        $all = $this->all();
        $identifier = expMailCategory::cleanIdentifier( $identifier );
        return isset( $all[$identifier] ) ? $all[$identifier] : null;
    }

    /**
     * Adds a category for this process (an extension's code; it is not stored).
     *
     * @param expMailCategory $category
     */
    public function register( expMailCategory $category )
    {
        if ( !expMailCategory::validIdentifier( $category->identifier ) )
            throw new InvalidArgumentException( "Not a category identifier: '{$category->identifier}'" );
        $this->registered[$category->identifier] = $category;
        $this->categories = null;
    }

    /**
     * Stores a category from the admin's category manager: a new one (source 'admin') or the changes to an INI
     * category's name, description, default and double opt-in.
     *
     * @param expMailCategory $category
     * @return bool
     */
    public function saveAdmin( expMailCategory $category )
    {
        if ( !expMailCategory::validIdentifier( $category->identifier ) || $category->identifier === expMailPreferenceRow::MASTER )
            return false;
        $row = expMailCategoryRow::fetchByIdentifier( $category->identifier );
        $now = time();
        if ( !$row )
        {
            $priority = 0;
            foreach ( expMailCategoryRow::fetchAll() as $r )
                $priority = max( $priority, (int)$r->attribute( 'priority' ) );
            $row = new expMailCategoryRow( array( 'identifier' => $category->identifier, 'created' => $now, 'priority' => $priority + 1 ) );
        }
        $ini = $this->iniCategories();
        $essential = isset( $ini[$category->identifier] ) ? $ini[$category->identifier]->essential : $category->essential;
        $row->setAttribute( 'name', (string)$category->name );
        $row->setAttribute( 'description', (string)$category->description );
        $row->setAttribute( 'essential', $essential ? 1 : 0 );
        $row->setAttribute( 'default_on', !$essential && $category->defaultOn ? 1 : 0 );
        $row->setAttribute( 'frequencies', implode( ',', $category->frequencies ) );
        $row->setAttribute( 'double_opt_in', !$essential && $category->doubleOptIn ? 1 : 0 );
        $row->setAttribute( 'handler_class', (string)$category->handlerClass );
        $row->setAttribute( 'modified', $now );
        $row->store();
        $this->categories = null;
        return true;
    }

    /**
     * Removes an admin row: a category of source 'admin' is gone, an INI category is back to its settings.
     * Stored preferences of the category are kept (they are the person's record).
     *
     * @param string $identifier
     * @return bool a row was removed
     */
    public function removeAdmin( $identifier )
    {
        $row = expMailCategoryRow::fetchByIdentifier( expMailCategory::cleanIdentifier( $identifier ) );
        if ( !$row )
            return false;
        $row->remove();
        $this->categories = null;
        return true;
    }

    /** @return expMailCategory[] the categories of the settings alone */
    public function iniCategories()
    {
        $ini = eZINI::instance( 'mailpreferences.ini' );
        $shipped = $this->shippedIdentifiers();
        $out = array();
        $list = $ini->hasVariable( 'CategorySettings', 'Categories' ) ? (array)$ini->variable( 'CategorySettings', 'Categories' ) : array();
        foreach ( $list as $identifier )
        {
            $identifier = expMailCategory::cleanIdentifier( $identifier );
            $group = 'Category_' . $identifier;
            if ( !expMailCategory::validIdentifier( $identifier ) || isset( $out[$identifier] ) || !$ini->hasGroup( $group ) )
                continue;
            $v = function ( $name, $default ) use ( $ini, $group ) { return $ini->hasVariable( $group, $name ) ? $ini->variable( $group, $name ) : $default; };
            $out[$identifier] = new expMailCategory( $identifier, array(
                'name' => $v( 'Name', $identifier ), 'description' => $v( 'Description', '' ),
                'essential' => $v( 'Essential', 'false' ), 'defaultOn' => $v( 'DefaultOn', 'false' ),
                'frequencies' => array_filter( (array)$v( 'Frequencies', array() ), 'strlen' ),
                'doubleOptIn' => $v( 'DoubleOptIn', 'false' ), 'handlerClass' => $v( 'HandlerClass', '' ),
                'source' => in_array( $identifier, $shipped, true ) ? 'ini' : 'extension' ) );
        }
        return $out;
    }

    protected function load()
    {
        $out = $this->iniCategories();
        try
        {
            foreach ( expMailCategoryRow::fetchAll() as $row )
            {
                $id = (string)$row->attribute( 'identifier' );
                if ( !expMailCategory::validIdentifier( $id ) )
                    continue;
                $values = array( 'name' => $row->attribute( 'name' ), 'description' => $row->attribute( 'description' ),
                                 'essential' => (int)$row->attribute( 'essential' ), 'defaultOn' => (int)$row->attribute( 'default_on' ),
                                 'frequencies' => (string)$row->attribute( 'frequencies' ), 'doubleOptIn' => (int)$row->attribute( 'double_opt_in' ),
                                 'handlerClass' => (string)$row->attribute( 'handler_class' ), 'source' => 'admin' );
                if ( isset( $out[$id] ) )
                {
                    // the settings decide what is essential and which class reads the old data
                    $values['essential'] = $out[$id]->essential;
                    $values['source'] = $out[$id]->source;
                    if ( $values['handlerClass'] === '' )
                        $values['handlerClass'] = $out[$id]->handlerClass;
                    if ( $values['name'] === '' )
                        $values['name'] = $out[$id]->name;
                }
                $out[$id] = new expMailCategory( $id, $values );
            }
        }
        catch ( Throwable $e )
        {
            // no table yet (before the database update): the settings alone
            eZDebug::writeWarning( 'expmail_category: ' . $e->getMessage(), __METHOD__ );
        }
        foreach ( $this->registered as $id => $category )
            $out[$id] = $category;
        return $this->refuseDefaultOn( $out );
    }

    /**
     * Opt-in only: an optional category that is on for everybody who never chose (DefaultOn=true) is turned into an
     * opt-in one, and a warning names it; the status page lists it (refusedDefaultOn()). [CategorySettings]
     * AllowDefaultOn=enabled keeps DefaultOn, for a site whose mail and jurisdiction allow it (not marketing to
     * people in the EU or Canada).
     *
     * @param expMailCategory[] $categories
     * @return expMailCategory[]
     */
    protected function refuseDefaultOn( array $categories )
    {
        $this->refusedDefaultOn = array();
        if ( self::allowDefaultOn() )
            return $categories;
        foreach ( $categories as $id => $category )
        {
            if ( $category->essential || !$category->defaultOn )
                continue;
            $copy = clone $category;
            $copy->defaultOn = false;
            $categories[$id] = $copy;
            $this->refusedDefaultOn[$id] = $category->source;
            eZDebug::writeWarning( "The optional mail category '$id' ({$category->source}) says DefaultOn=true: it is treated as off "
                                   . 'until the person turns it on. Set [CategorySettings] AllowDefaultOn=enabled in mailpreferences.ini only where the law allows opt-out.', __METHOD__ );
        }
        return $categories;
    }

    /** @return bool mailpreferences.ini [CategorySettings] AllowDefaultOn=enabled */
    public static function allowDefaultOn()
    {
        $ini = eZINI::instance( 'mailpreferences.ini' );
        return $ini->hasVariable( 'CategorySettings', 'AllowDefaultOn' ) && trim( (string)$ini->variable( 'CategorySettings', 'AllowDefaultOn' ) ) === 'enabled';
    }

    /** @return string[] identifier => source of the optional categories whose DefaultOn=true is refused */
    public function refusedDefaultOn()
    {
        $this->all();
        return $this->refusedDefaultOn;
    }

    /** @return string[] the identifiers the shipped settings/mailpreferences.ini lists */
    protected function shippedIdentifiers()
    {
        static $shipped = null;
        if ( $shipped === null )
        {
            $shipped = array();
            $file = 'settings/mailpreferences.ini';
            if ( is_file( $file ) && preg_match_all( '/^\s*Categories\[\]\s*=\s*([A-Za-z0-9_]+)\s*$/m', (string)file_get_contents( $file ), $m ) )
                $shipped = array_map( array( 'expMailCategory', 'cleanIdentifier' ), $m[1] );
        }
        return $shipped;
    }
}
