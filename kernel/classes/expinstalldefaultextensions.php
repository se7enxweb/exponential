<?php
/**
 * File containing the expInstallDefaultExtensions class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The extensions every new installation gets set up, whatever site package it is made from: two-step sign-in
 * (sevenx_authentication_2fa) and imports (sqliimport, with expqueue and expui). ezodf is in the setup wizard's own
 * list already.
 *
 * The maintained site packages (sevenx_multisite, sevenx_site, cjw_multisite and their _clean copies) configure all
 * of this themselves; the older ones (ezwebin_site, ezflow_site, ezdemo_site and their _clean copies, plain_site) do
 * not. The setup wizard and the kickstarter call this after the site package, and each part is done only where the
 * package has not done it, so both kinds of package end with the same installation:
 *
 * - withTwoFactorSettings(): the login handler sevenxUser2fa before standard, and
 *   settings/override/sevenxauthentication2fa.ini.append.php with the default policy (twoFactorPolicySettings():
 *   required for Administrator users and Editors, a choice for everyone else, a key of this installation);
 * - adminAccessExtensions(): sqliimport in the administration siteaccess (ActiveAccessExtensions[]), after expui and
 *   expqueue when they are not global; the editor siteaccess is made without it (EDITOR_EXCLUDED);
 * - withImportsTab(): its Imports tab in a TopAdminMenu Tabs[] list the package wrote with a reset;
 * - ensureDatabase(): the tables of those extensions, the "Two-step sign-in" field of the user class, and the
 *   policies user2fa/setup and user2fa/verify for the roles whose users sign in.
 *
 * Every part is guarded by the extension being in the installation: a login handler or an access extension that is
 * not there would break the site. The plain-array parts are tested without a database
 * (tests/tests/kernel/classes/expInstallDefaultExtensionsTest.php).
 */
class expInstallDefaultExtensions
{
    /** The two-step sign-in extension, its login handler and its settings file */
    const TWO_FACTOR = 'sevenx_authentication_2fa';
    const TWO_FACTOR_HANDLER = 'sevenxUser2fa';
    const TWO_FACTOR_INI = 'sevenxauthentication2fa.ini';
    const TWO_FACTOR_DATATYPE = 'sevenxauthentication2fa';

    /** Object remote ids of the base data's user groups whose members must have a second step */
    const ADMINISTRATOR_USERS_REMOTE_ID = '9b47a45624b023b1a76c73b74d704acf';
    const EDITORS_REMOTE_ID = '3c160cca19fb135f83bd02d911f04db2';

    /** The imports extension and the extensions it needs, in load order */
    const IMPORTS = 'sqliimport';
    const IMPORTS_REQUIRES = array( 'expui', 'expqueue' );

    /** Access extensions of the administration that the editor siteaccess is made without */
    const EDITOR_EXCLUDED = array( 'sqliimport' );

    /** The roles whose users sign in and manage their own second step */
    const SIGN_IN_ROLES = array( 'Member', 'Editor', 'Partner' );

    /**
     * Is an extension in the installation?
     *
     * @param string $extension
     * @param string $extensionRoot
     * @return bool
     */
    public static function available( $extension, $extensionRoot = 'extension' )
    {
        return is_dir( rtrim( (string)$extensionRoot, '/' ) . '/' . $extension );
    }

    /**
     * The extensions to switch on globally (ActiveExtensions[]) when the installation has them.
     *
     * @param string $extensionRoot
     * @return string[]
     */
    public static function globalExtensions( $extensionRoot = 'extension' )
    {
        return self::available( self::TWO_FACTOR, $extensionRoot ) ? array( self::TWO_FACTOR ) : array();
    }

    /**
     * The default policy of two-step sign-in: required for Administrator users and Editors (by remote id, the same
     * on every installation made from the base data), a choice for every other account, no exemptions, no grace
     * period, ten recovery codes, the authenticator secrets encrypted with a key made for this installation.
     *
     * @param string|null $secretKey null makes a new random one
     * @return array an INI settings entry: name, reset_arrays, settings
     */
    public static function twoFactorPolicySettings( $secretKey = null )
    {
        return array(
            'name' => self::TWO_FACTOR_INI,
            'reset_arrays' => true,
            'settings' => array(
                'General' => array(
                    'Enforce2FA' => 'enabled',
                    'DefaultMethod' => 'totp',
                    'EnforcedGroups' => array( self::ADMINISTRATOR_USERS_REMOTE_ID, self::EDITORS_REMOTE_ID ),
                    'ExemptGroups' => array(),
                    'ExemptEmailDomains' => array(),
                    'GracePeriodDays' => '0'
                ),
                'Security' => array( 'RecoveryCodes' => '10' ),
                'TOTPSettings' => array( 'SecretKey' => $secretKey === null ? base64_encode( random_bytes( 48 ) ) : (string)$secretKey )
            )
        );
    }

    /**
     * The common settings of a site package with what two-step sign-in needs added where the package left it out:
     * the login handler (site.ini [UserSettings] LoginHandler[] sevenxUser2fa, standard) and the policy file. A
     * package that set either keeps its own. Without the extension in the installation nothing is added.
     *
     * @param array $commonSettings INI settings entries (name, settings, reset_arrays)
     * @param string $extensionRoot
     * @param string|null $secretKey for the tests; null makes a random one
     * @return array
     */
    public static function withTwoFactorSettings( array $commonSettings, $extensionRoot = 'extension', $secretKey = null )
    {
        if ( !self::available( self::TWO_FACTOR, $extensionRoot ) )
            return $commonSettings;
        $hasHandler = false;
        $hasPolicy = false;
        foreach ( $commonSettings as $entry )
        {
            if ( !is_array( $entry ) || !isset( $entry['name'] ) )
                continue;
            if ( $entry['name'] === self::TWO_FACTOR_INI )
                $hasPolicy = true;
            if ( $entry['name'] === 'site.ini' && isset( $entry['settings']['UserSettings']['LoginHandler'] )
                 && in_array( self::TWO_FACTOR_HANDLER, (array)$entry['settings']['UserSettings']['LoginHandler'], true ) )
                $hasHandler = true;
        }
        if ( !$hasHandler )
            $commonSettings[] = array( 'name' => 'site.ini',
                                       'settings' => array( 'UserSettings' => array( 'LoginHandler' => array( self::TWO_FACTOR_HANDLER, 'standard' ) ) ) );
        if ( !$hasPolicy )
            $commonSettings[] = self::twoFactorPolicySettings( $secretKey );
        return $commonSettings;
    }

    /**
     * The access extensions of the administration siteaccess with the imports extension added (last), and before it
     * the extensions it needs that are not global, each once and only when the installation has it.
     *
     * @param array $accessExtensions ActiveAccessExtensions[] of the admin siteaccess now
     * @param array $globalExtensions ActiveExtensions[] of the installation
     * @param string $extensionRoot
     * @return string[]
     */
    public static function adminAccessExtensions( array $accessExtensions, array $globalExtensions, $extensionRoot = 'extension' )
    {
        $list = array();
        foreach ( $accessExtensions as $extension )
        {
            $extension = trim( (string)$extension );
            if ( $extension !== '' && !in_array( $extension, $list, true ) )
                $list[] = $extension;
        }
        if ( !self::available( self::IMPORTS, $extensionRoot ) || in_array( self::IMPORTS, $globalExtensions, true ) )
            return $list;
        foreach ( self::IMPORTS_REQUIRES as $required )
        {
            if ( !self::available( $required, $extensionRoot ) )
                return $list; // sqliimport without what it needs would break the administration
        }
        foreach ( array_merge( self::IMPORTS_REQUIRES, array( self::IMPORTS ) ) as $extension )
        {
            if ( !in_array( $extension, $globalExtensions, true ) && !in_array( $extension, $list, true ) )
                $list[] = $extension;
        }
        return $list;
    }

    /**
     * The editor siteaccess's access extensions: the admin's without the ones it is made without (EDITOR_EXCLUDED).
     *
     * @param array $accessExtensions
     * @return string[]
     */
    public static function editorAccessExtensions( array $accessExtensions )
    {
        return array_values( array_diff( $accessExtensions, self::EDITOR_EXCLUDED ) );
    }

    /**
     * A TopAdminMenu Tabs[] list with the Imports tab: after Export (xrowextract), else before CIE (bccie_overview)
     * or the newsletter, else at the end. A list that has it is returned as it is.
     *
     * @param array $tabs
     * @return string[]
     */
    public static function withImportsTab( array $tabs )
    {
        $tabs = array_values( $tabs );
        if ( in_array( self::IMPORTS, $tabs, true ) )
            return $tabs;
        $at = array_search( 'xrowextract', $tabs, true );
        if ( $at !== false )
            $at++;
        else
        {
            foreach ( array( 'bccie_overview', 'newsletter' ) as $before )
            {
                $at = array_search( $before, $tabs, true );
                if ( $at !== false )
                    break;
            }
        }
        if ( $at === false )
            $at = count( $tabs );
        array_splice( $tabs, $at, 0, array( self::IMPORTS ) );
        return $tabs;
    }

    /**
     * Switch on the imports extension in an admin siteaccess's files (site.ini.append.php ActiveAccessExtensions[],
     * and the Imports tab in its menu.ini.append.php when that has a Tabs[] list of its own). Files are written only
     * when they change.
     *
     * @param string $siteaccessDir settings/siteaccess/<admin>
     * @param array $globalExtensions ActiveExtensions[] of the installation
     * @param string $extensionRoot
     * @return string what was done, for the setup log
     */
    public static function activateInAdminSiteAccess( $siteaccessDir, array $globalExtensions, $extensionRoot = 'extension' )
    {
        $siteaccessDir = rtrim( (string)$siteaccessDir, '/' );
        if ( !file_exists( $siteaccessDir . '/site.ini.append.php' ) )
            return "no $siteaccessDir/site.ini.append.php";
        $site = new eZINI( 'site.ini.append.php', $siteaccessDir, false, false, null, true, true );
        $site->setReadOnlySettingsCheck( false );
        $current = $site->hasVariable( 'ExtensionSettings', 'ActiveAccessExtensions' )
                 ? array_values( (array)$site->variable( 'ExtensionSettings', 'ActiveAccessExtensions' ) ) : array();
        $list = self::adminAccessExtensions( $current, $globalExtensions, $extensionRoot );
        $done = array();
        if ( $list !== array_values( array_filter( $current, 'strlen' ) ) )
        {
            $site->setVariable( 'ExtensionSettings', 'ActiveAccessExtensions', $list );
            if ( !$site->save( false, false, false, false, true, true ) )
                return "could not write $siteaccessDir/site.ini.append.php";
            $done[] = 'ActiveAccessExtensions: ' . implode( ', ', $list );
        }
        if ( in_array( self::IMPORTS, $list, true ) || in_array( self::IMPORTS, $globalExtensions, true ) )
        {
            if ( file_exists( $siteaccessDir . '/menu.ini.append.php' ) )
            {
                $menu = new eZINI( 'menu.ini.append.php', $siteaccessDir, false, false, null, true, true );
                if ( $menu->hasVariable( 'TopAdminMenu', 'Tabs' ) )
                {
                    $tabs = array_values( array_filter( (array)$menu->variable( 'TopAdminMenu', 'Tabs' ), 'strlen' ) );
                    $withTab = self::withImportsTab( $tabs );
                    if ( $withTab !== $tabs )
                    {
                        $menu->setVariable( 'TopAdminMenu', 'Tabs', $withTab );
                        if ( !$menu->save( false, false, false, false, true, true ) )
                            return "could not write $siteaccessDir/menu.ini.append.php";
                        $done[] = 'Imports tab';
                    }
                }
            }
        }
        return $done ? implode( '; ', $done ) : 'nothing to do';
    }

    /**
     * After the site package: the tables of the default extensions, the two-step sign-in field of the user class and
     * the user2fa policies of the roles that sign in, each only where missing.
     *
     * @param array $extensions the extensions to create tables for (global and admin access ones)
     * @param string $language locale the new field is named in, false for the default
     * @param string $extensionRoot
     * @return string[] what was done, for the setup log
     */
    public static function ensureDatabase( array $extensions, $language = false, $extensionRoot = 'extension' )
    {
        $log = array();
        foreach ( array_merge( self::IMPORTS_REQUIRES, array( self::IMPORTS ) ) as $extension )
        {
            if ( in_array( $extension, $extensions, true ) )
                $log[] = self::ensureExtensionTables( $extension, $extensionRoot );
        }
        if ( self::available( self::TWO_FACTOR, $extensionRoot ) )
        {
            $log[] = self::ensureTwoFactorUserField( $language, $extensionRoot );
            $log[] = self::ensureTwoFactorPolicies();
        }
        return $log;
    }

    /**
     * Create the tables of an extension's share/db_schema.dba that the database lacks (none dropped or changed).
     *
     * @param string $extension
     * @param string $extensionRoot
     * @return string
     */
    public static function ensureExtensionTables( $extension, $extensionRoot = 'extension' )
    {
        $file = rtrim( (string)$extensionRoot, '/' ) . '/' . $extension . '/share/db_schema.dba';
        if ( !file_exists( $file ) )
            return "$extension: no share/db_schema.dba";
        $schemaArray = eZDbSchema::read( $file, true );
        if ( !is_array( $schemaArray ) || empty( $schemaArray['schema'] ) )
            return "$extension: share/db_schema.dba could not be read";
        $db = eZDB::instance();
        $reader = eZDbSchema::instance( $db );
        $live = $reader ? $reader->schema( array( 'format' => 'local' ) ) : array();
        $missing = array();
        foreach ( array_keys( $schemaArray['schema'] ) as $table )
        {
            if ( $table === '_info' )
                continue;
            if ( isset( $live[$table] ) )
                unset( $schemaArray['schema'][$table] );
            else
                $missing[] = $table;
        }
        if ( !$missing )
            return "$extension: tables present";
        $schemaArray['type'] = strtolower( $db->databaseName() );
        $schemaArray['instance'] = $db;
        $dbSchema = eZDbSchema::instance( $schemaArray );
        if ( !$dbSchema || !$dbSchema->insertSchema( array( 'schema' => true, 'data' => false ) ) )
        {
            eZDebug::writeError( "$extension: its tables could not be created: " . $db->errorMessage(), __METHOD__ );
            return "$extension: tables NOT created (" . implode( ', ', $missing ) . ')';
        }
        return "$extension: created " . count( $missing ) . ' tables';
    }

    /**
     * Add the field "Two-step sign-in" (two_factor, datatype sevenxauthentication2fa) to the user class, unless it
     * has one. The datatype is registered for this process first, as the extension is switched on only in the files
     * just written.
     *
     * @param string|false $language
     * @param string $extensionRoot
     * @return string
     */
    public static function ensureTwoFactorUserField( $language = false, $extensionRoot = 'extension' )
    {
        $contentINI = eZINI::instance( 'content.ini' );
        $repositories = (array)$contentINI->variable( 'DataTypeSettings', 'ExtensionDirectories' );
        if ( !in_array( self::TWO_FACTOR, $repositories, true ) )
            $repositories[] = self::TWO_FACTOR;
        $available = (array)$contentINI->variable( 'DataTypeSettings', 'AvailableDataTypes' );
        if ( !in_array( self::TWO_FACTOR_DATATYPE, $available, true ) )
            $available[] = self::TWO_FACTOR_DATATYPE;
        $contentINI->setVariables( array( 'DataTypeSettings' => array( 'ExtensionDirectories' => $repositories,
                                                                       'AvailableDataTypes' => $available ) ) );
        if ( !eZDataType::create( self::TWO_FACTOR_DATATYPE ) )
            return 'user class: the ' . self::TWO_FACTOR_DATATYPE . ' datatype could not be loaded, no two-step sign-in field';
        $class = eZContentClass::fetchByIdentifier( 'user' );
        if ( !$class instanceof eZContentClass )
            return 'user class: not found';
        foreach ( $class->fetchAttributes() as $attribute )
        {
            if ( $attribute->attribute( 'data_type_string' ) === self::TWO_FACTOR_DATATYPE )
                return 'user class: has a two-step sign-in field';
        }
        $installer = new eZSiteInstaller();
        $db = eZDB::instance();
        $db->begin();
        $installer->addClassAttributes( array(
            'class' => array( 'identifier' => 'user' ),
            'attributes' => array(
                array( 'identifier' => 'two_factor', 'name' => 'Two-step sign-in', 'data_type_string' => self::TWO_FACTOR_DATATYPE,
                       'can_translate' => 0, 'is_required' => 0, 'is_searchable' => 0, 'language' => $language ? $language : false )
            )
        ) );
        $db->commit();
        return 'user class: two-step sign-in field added';
    }

    /**
     * Does a role's policy list (module, function pairs) let its users set up and use their second step?
     *
     * @param array $policies each array( module, function )
     * @param string $function setup or verify
     * @return bool
     */
    public static function grants( array $policies, $function )
    {
        foreach ( $policies as $policy )
        {
            list( $module, $policyFunction ) = $policy;
            if ( $module === '*' || ( $module === 'user2fa' && ( $policyFunction === '*' || $policyFunction === $function ) ) )
                return true;
        }
        return false;
    }

    /**
     * Give the roles whose users sign in (SIGN_IN_ROLES that exist) user2fa/setup and user2fa/verify where missing.
     *
     * @return string
     */
    public static function ensureTwoFactorPolicies()
    {
        $added = array();
        foreach ( self::SIGN_IN_ROLES as $roleName )
        {
            $role = eZRole::fetchByName( $roleName );
            if ( !$role instanceof eZRole )
                continue;
            $policies = array();
            foreach ( $role->policyList() as $policy )
                $policies[] = array( $policy->attribute( 'module_name' ), $policy->attribute( 'function_name' ) );
            foreach ( array( 'setup', 'verify' ) as $function )
            {
                if ( self::grants( $policies, $function ) )
                    continue;
                $role->appendPolicy( 'user2fa', $function );
                $added[] = "$roleName user2fa/$function";
            }
        }
        return $added ? 'policies added: ' . implode( ', ', $added ) : 'policies: the roles have user2fa/setup and user2fa/verify';
    }
}
