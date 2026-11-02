<?php
/**
 * File containing the expStaticTargetPackageHandler class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Static publishing targets in a package (install item type expstatictarget), so a site package carries the targets
 * of the site it was made from.
 *
 * Adding: `php bin/php/ezpm.php -r <repository> add <package> expstatictarget <id>[,<id>...]|all` writes each target
 * as statictargets/target-<id>.xml in the package. Only the wizard's settings travel (expStaticAdmin::WIZARD_SETTINGS)
 * and of the credential only its name (token:<secret name>, sshkey:<key id>, app:<installation>, none): a secret's
 * value is kept in Setup > Secrets and is never written into a package. A user and password in the git address are
 * left out, and so is the webhook (its address is often the secret itself).
 *
 * Installing writes the target into settings/override/staticcache.ini.append.php through expIniEditor, as the wizard
 * does (audited as system.staticpublish.target), and lists it in TargetList[]. A target the installation already has
 * is kept as it is. Static publishing itself is not switched on, and the receiving installation stores the values of
 * the credentials in its own Setup > Secrets.
 *
 * Uninstalling leaves the settings alone, as the INI additions of a package do: remove a target in Setup > Static
 * publishing.
 */
class expStaticTargetPackageHandler extends eZPackageHandler
{
    /** The package folder of the target files */
    const SUBDIRECTORY = 'statictargets';

    public function __construct()
    {
        parent::__construct( 'expstatictarget', array( 'extract-install-content' => true ) );
    }

    /**
     * The settings of a target as they travel. Pure.
     *
     * @param array $settings a target's settings
     * @return array only the wizard's settings, the credential as a name, no user or password in Remote, no Webhook
     */
    public static function travelSettings( array $settings )
    {
        $out = array();
        foreach ( expStaticAdmin::WIZARD_SETTINGS as $name )
        {
            if ( !array_key_exists( $name, $settings ) )
                continue;
            $value = $settings[$name];
            if ( in_array( $name, expStaticTarget::LIST_SETTINGS, true ) )
                $out[$name] = array_values( array_filter( array_map( 'trim', array_map( 'strval', (array)$value ) ), 'strlen' ) );
            else
                $out[$name] = is_array( $value ) ? '' : trim( (string)$value );
        }
        if ( isset( $out['Credential'] ) )
            $out['Credential'] = self::credentialName( $out['Credential'] );
        if ( isset( $out['Remote'] ) )
            $out['Remote'] = preg_replace( '#^([a-z][a-z0-9+.-]*://)[^/@]*@#i', '$1', $out['Remote'] );
        if ( isset( $out['Webhook'] ) )
            $out['Webhook'] = '';
        return $out;
    }

    /**
     * A credential setting reduced to a name: token:<name>, token:env:<NAME>, sshkey:<name>, app:<installation> or
     * none. Anything else (a value pasted in by mistake) becomes none. Pure.
     *
     * @param string $credential
     * @return string
     */
    public static function credentialName( $credential )
    {
        $credential = trim( (string)$credential );
        $kind = strpos( $credential, ':' ) === false ? $credential : substr( $credential, 0, strpos( $credential, ':' ) );
        if ( $kind === 'none' || !in_array( $kind, expStaticTarget::CREDENTIAL_KINDS, true ) )
            return 'none';
        $name = (string)substr( $credential, strlen( $kind ) + 1 );
        if ( !preg_match( '#^(env:)?[A-Za-z0-9_.-]{1,100}$#', $name ) || ( $kind === 'app' && !ctype_digit( $name ) ) )
            return 'none';
        // a token's own shapes are values, not names
        if ( preg_match( '#^(gh[pousr]_|github_pat_)#', $name ) || ( strlen( $name ) >= 40 && preg_match( '#^[0-9a-f]+$#i', $name ) ) )
            return 'none';
        return "$kind:$name";
    }

    /**
     * A target as the element written into the package: <static-target id="..."><setting name="X">value</setting>
     * <setting name="Extras" list="1"><item>nojekyll</item></setting></static-target>.
     *
     * @param string $id
     * @param array $settings
     * @return DOMElement
     */
    public static function targetElement( $id, array $settings )
    {
        $dom = new DOMDocument( '1.0', 'utf-8' );
        $root = $dom->createElement( 'static-target' );
        $root->setAttribute( 'id', (string)$id );
        foreach ( self::travelSettings( $settings ) as $name => $value )
        {
            $node = $dom->createElement( 'setting' );
            $node->setAttribute( 'name', $name );
            if ( is_array( $value ) )
            {
                $node->setAttribute( 'list', '1' );
                foreach ( $value as $item )
                {
                    $itemNode = $dom->createElement( 'item' );
                    $itemNode->appendChild( $dom->createTextNode( $item ) );
                    $node->appendChild( $itemNode );
                }
            }
            else
                $node->appendChild( $dom->createTextNode( $value ) );
            $root->appendChild( $node );
        }
        $dom->appendChild( $root );
        return $root;
    }

    /**
     * The id and settings of an element targetElement() wrote; the settings go through travelSettings() again, so
     * a package made elsewhere brings nothing else in.
     *
     * @param DOMElement $element
     * @return array|null id, settings; null when it is no target element or its id is no target id
     */
    public static function readElement( $element )
    {
        if ( !$element instanceof DOMElement || $element->localName !== 'static-target' )
            return null;
        $id = (string)$element->getAttribute( 'id' );
        if ( !preg_match( expStaticTarget::ID_PATTERN, $id ) )
            return null;
        $settings = array();
        foreach ( $element->childNodes as $node )
        {
            if ( !$node instanceof DOMElement || $node->localName !== 'setting' )
                continue;
            $name = (string)$node->getAttribute( 'name' );
            if ( $node->getAttribute( 'list' ) === '1' )
            {
                $settings[$name] = array();
                foreach ( $node->childNodes as $item )
                    if ( $item instanceof DOMElement && $item->localName === 'item' )
                        $settings[$name][] = (string)$item->textContent;
            }
            else
                $settings[$name] = (string)$node->textContent;
        }
        return array( 'id' => $id, 'settings' => self::travelSettings( $settings ) );
    }

    /**
     * Writes a target from a package unless the installation has one with its id.
     *
     * @param string $id
     * @param array $settings
     * @param string[]|null $existing the target ids there are (null: TargetList[] of staticcache.ini)
     * @param callable|null $save function ( $id, $settings ): string, '' when written (default expStaticAdmin::saveTarget)
     * @return array state (installed, kept, failed), message
     */
    public static function installTarget( $id, array $settings, $existing = null, $save = null )
    {
        if ( $existing === null )
            $existing = expStaticTarget::ids();
        if ( in_array( $id, (array)$existing, true ) )
            return array( 'state' => 'kept', 'message' => "The static publishing target $id is there already and is kept as it is." );
        $settings = self::travelSettings( $settings ) + array( 'Name' => $id );
        $problem = $save !== null ? (string)call_user_func( $save, $id, $settings )
                                  : expStaticAdmin::saveTarget( $id, $settings, true, false );
        if ( $problem !== '' )
            return array( 'state' => 'failed', 'message' => "The static publishing target $id could not be written: $problem" );
        $message = "The static publishing target $id was added.";
        $kind = expStaticCredentials::parse( $settings['Credential'] ?? 'none' )[0];
        if ( $kind === 'token' || $kind === 'sshkey' )
            $message .= ' Store the value of its credential (' . $settings['Credential'] . ') in Setup > Secrets.';
        return array( 'state' => 'installed', 'message' => $message );
    }

    function install( $package, $installType, $parameters,
                      $name, $os, $filename, $subdirectory,
                      $content, &$installParameters,
                      &$installData )
    {
        $target = self::readElement( $content );
        if ( $target === null )
        {
            eZDebug::writeError( "The item '$filename' of package '" . $package->attribute( 'name' ) . "' is no static publishing target.", __METHOD__ );
            return false;
        }
        $result = self::installTarget( $target['id'], $target['settings'] );
        if ( $result['state'] === 'failed' )
        {
            eZDebug::writeError( $result['message'], __METHOD__ );
            return false;
        }
        eZDebug::writeNotice( $result['message'], __METHOD__ );
        return true;
    }

    /**
     * The settings are left alone, as the INI additions of a package are; the reason is written to the debug output.
     */
    function uninstall( $package, $installType, $parameters,
                        $name, $os, $filename, $subdirectory,
                        $content, &$installParameters,
                        &$installData )
    {
        eZDebug::writeWarning( "The static publishing target '$name' of package '" . $package->attribute( 'name' ) . "' is not removed on uninstall: remove it in Setup > Static publishing.", __METHOD__ );
        return false;
    }

    function explainInstallItem( $package, $installItem, $requestedInfo = array() )
    {
        $id = isset( $installItem['name'] ) ? (string)$installItem['name'] : '';
        return array( 'description' => ezpI18n::tr( 'design/admin/staticpublish', 'Static publishing' ) . ': ' . $id );
    }

    /**
     * Adds targets to a package.
     *
     * @param string $packageType
     * @param eZPackage $package
     * @param eZCLI $cli
     * @param array $parameters target-list (expStaticTarget[])
     */
    function add( $packageType, $package, $cli, $parameters )
    {
        foreach ( $parameters['target-list'] as $target )
        {
            self::addTarget( $package, $target );
            $cli->notice( 'Adding ' . $cli->stylize( 'mark', 'static publishing target' ) . ' ' . $cli->stylize( 'emphasize', $target->id() )
                          . ' (credential name only: ' . self::credentialName( (string)$target->setting( 'Credential' ) ) . ')' );
        }
    }

    /**
     * Adds one target to a package.
     *
     * @param eZPackage $package
     * @param expStaticTarget $target
     */
    public static function addTarget( $package, expStaticTarget $target )
    {
        $package->appendInstall( 'expstatictarget', $target->id(), false, true, 'target-' . $target->id(), self::SUBDIRECTORY,
                                 array( 'content' => self::targetElement( $target->id(), $target->settings() ) ) );
    }

    function handleAddParameters( $packageType, $package, $cli, $arguments )
    {
        $wanted = array();
        foreach ( $arguments as $argument )
            foreach ( explode( ',', (string)$argument ) as $id )
                if ( trim( $id ) !== '' )
                    $wanted[] = trim( $id );
        if ( $wanted === array( 'all' ) )
            $wanted = expStaticTarget::ids();
        if ( !$wanted )
        {
            $cli->error( 'No static publishing target was named: give target ids (separated by commas) or all' );
            return false;
        }
        $targets = array();
        foreach ( array_unique( $wanted ) as $id )
        {
            $target = expStaticTarget::get( $id );
            if ( $target === null )
            {
                $cli->error( "There is no static publishing target $id in staticcache.ini [StaticPublishSettings] TargetList[]" );
                return false;
            }
            $targets[] = $target;
        }
        return array( 'target-list' => $targets );
    }
}

?>
