<?php
/**
 * File containing the eZExtensionPackageHandler class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZExtensionPackageHandler ezextensionpackagehandler.php
  \brief Handles extenstions in the package system

*/

class eZExtensionPackageHandler extends eZPackageHandler
{
    public $HandlerType;
    const ERROR_EXISTS = 1;

    const ACTION_REPLACE = 1;
    const ACTION_SKIP = 2;

    public function __construct()
    {
        parent::__construct( 'ezextension', array( 'extract-install-content' => true ) );
    }

    /*!
     Returns an explanation for the extension install item.
    */
    function explainInstallItem( $package, $installItem, $requestedInfo = array() )
    {
        if ( $installItem['filename'] )
        {
            $filename = $installItem['filename'];
            $subdirectory = $installItem['sub-directory'];
            if ( $subdirectory )
                $filepath = $subdirectory . '/' . $filename . '.xml';
            else
                $filepath = $filename . '.xml';

            $filepath = $package->path() . '/' . $filepath;

            $dom = $package->fetchDOMFromFile( $filepath );
            if ( $dom )
            {
                $root = $dom->documentElement;
                $extensionName = $root->getAttribute( 'name' );
                return array( 'description' => ezpI18n::tr( 'kernel/package', 'Extension \'%extensionname\'', false,
                                                       array( '%extensionname' => $extensionName ) ) );
            }
        }
    }

    /*!
     Uninstalls extensions.
    */
    function uninstall( $package, $installType, $parameters,
                      $name, $os, $filename, $subdirectory,
                      $content, &$installParameters,
                      &$installData )
    {
        $extensionName = $content->getAttribute( 'name' );

        $extensionDir = eZExtension::extensionPath( $extensionName );

        // TODO: don't delete modified files?

        if ( $extensionDir !== false && file_exists( $extensionDir ) )
            eZDir::recursiveDelete( $extensionDir );

        // Deactivate extension
        $siteINI = eZINI::instance( 'site.ini', 'settings/override', null, null, false, true );
        $selectedExtensions = $siteINI->variable( 'ExtensionSettings', "ActiveExtensions" );

        if ( in_array( $extensionName, $selectedExtensions ) )
        {
            $extensionsFlipped = array_flip( $selectedExtensions );

            $extKey = $extensionsFlipped[$extensionName];
            unset( $selectedExtensions[$extKey] );

            $siteINI->setVariable( "ExtensionSettings", "ActiveExtensions", $selectedExtensions );
            $siteINI->save( 'site.ini.append', '.php', false, false );
        }

        // Regenerate the autoloads to remove of no longer existing classes
        ezpAutoloader::updateExtensionAutoloadArray();

        return true;
    }

    /*!
     Copy extension from the package to extension repository.
    */
    function install( $package, $installType, $parameters,
                      $name, $os, $filename, $subdirectory,
                      $content, &$installParameters,
                      &$installData )
    {
        //$this->Package =& $package;

        $trans = eZCharTransform::instance();
        $name = $content->getAttribute( 'name' );
        $extensionName = $trans->transformByGroup( $name, 'urlalias' );
        if ( strcmp( $name, $extensionName ) !== 0 )
        {
            $description = ezpI18n::tr( 'kernel/package', 'Package contains an invalid extension name: %extensionname', false, array( '%extensionname' => $name ) );
            $installParameters['error'] = array( 'error_code' => false,
                                                 'element_id' => $name,
                                                 'description' => $description );
            return false;
        }

        $extensionRootDir = eZExtension::baseDirectory();
        $extensionDir = $extensionRootDir . '/' . $extensionName;
        $existingExtensionPath = eZExtension::extensionPath( $extensionName );
        $packageExtensionDir = $package->path() . '/' . $parameters['sub-directory'] . '/' . $extensionName;

        // Error: extension already exists.
        if ( $existingExtensionPath !== false && file_exists( $existingExtensionPath ) )
        {
            $description = ezpI18n::tr( 'kernel/package', "Extension '%extensionname' already exists.",
                                   false, array( '%extensionname' => $extensionName ) );
            $choosenAction = $this->errorChoosenAction( self::ERROR_EXISTS,
                                                        $installParameters, $description, $this->HandlerType );
            switch( $choosenAction )
            {
            case self::ACTION_SKIP:
                return true;

            case eZPackage::NON_INTERACTIVE:
            case self::ACTION_REPLACE:
                eZDir::recursiveDelete( $existingExtensionPath );
                if ( $existingExtensionPath !== $extensionDir && file_exists( $extensionDir ) )
                {
                    eZDir::recursiveDelete( $extensionDir );
                }
                break;

            default:
                $installParameters['error'] = array( 'error_code' => self::ERROR_EXISTS,
                                                     'element_id' => $extensionName,
                                                     'description' => $description,
                                                     'actions' => array( self::ACTION_REPLACE => ezpI18n::tr( 'kernel/package', "Replace extension" ),
                                                                         self::ACTION_SKIP => ezpI18n::tr( 'kernel/package', 'Skip' ) ) );
                return false;
            }
        }

        eZDir::mkdir( $extensionDir, false, true );
        eZDir::copy( $packageExtensionDir, $extensionRootDir );

        // Regenerate autoloads for extensions to pick up the newly created extension
        ezpAutoloader::updateExtensionAutoloadArray();

        // Activate extension
        $siteINI = eZINI::instance( 'site.ini', 'settings/override', null, null, false, true );

        if ( $siteINI->hasVariable( 'ExtensionSettings', "ActiveExtensions" ) )
        {
            $selectedExtensions = $siteINI->variable( 'ExtensionSettings', "ActiveExtensions" );
        }
        else
        {
            $selectedExtensions = array();
        }

        if ( !in_array( $extensionName, $selectedExtensions ) )
        {
            $selectedExtensions[] = $extensionName;
            $siteINI->setVariable( "ExtensionSettings", "ActiveExtensions", $selectedExtensions );
            $siteINI->save( 'site.ini.append', '.php', false, false );
        }
        return true;
    }

    function add( $packageType, $package, $cli, $parameters )
    {
        foreach ( $parameters as $extensionName )
        {
            $cli->output( 'adding extension ' . $cli->stylize( 'dir', $extensionName ) );
            $this->addExtension( $package, $extensionName );
        }
    }

    /** Files that hold key material, anywhere in an extension: by name */
    const KEY_FILE_PATTERN = '/(\.(pem|key|p12|pfx|jks|keystore|kdbx)$|^\.env(\.(?!(example|sample|template)$).*)?$|^id_(rsa|dsa|ecdsa|ed25519)|^\.htpasswd$|^\.netrc$)/i';

    /** Directories whose files are key material, anywhere in an extension */
    const KEY_DIRECTORY_PATTERN = '/^(jwt|\.ssh)$/i';

    /** Copies and backups of settings files: never packed, whatever they hold */
    const BACKUP_FILE_PATTERN = '/(\.(bak|orig|dist|old|save|swp|tmp|rej)$|~$)/i';

    /**
     * Whether $extensionName names an extension directory that can be packed: a plain name (no path), present below
     * the extension directory.
     *
     * @param string $extensionName
     * @return bool
     */
    static function isPackableName( $extensionName )
    {
        $extensionName = (string)$extensionName;
        if ( !preg_match( '/^[A-Za-z0-9][A-Za-z0-9_.\-]*$/', $extensionName ) )
            return false;
        return is_dir( eZExtension::baseDirectory() . '/' . $extensionName );
    }

    /**
     * What in the extension directory $dir must not leave the server in a package, one line each, without a value:
     *  - key material anywhere: KEY_FILE_PATTERN (*.pem, *.key, *.p12, *.pfx, .env but not .env.example, .sample or
     *    .template, id_rsa, ...) and every file in a
     *    KEY_DIRECTORY_PATTERN directory (jwt/, .ssh/);
     *  - below settings/: a copy or backup (BACKUP_FILE_PATTERN: *.bak, *.orig, *.dist, *~, ...), and in every
     *    other file a setting whose name holds a secret (expSystemReportMask::isSecretSettingName()) or whose value
     *    carries one (expSecretRule::hasInlineSecret()). Empty values, numbers and switches (true, enabled, none,
     *    ...) are not secrets.
     * Reads the files only.
     *
     * @param string $dir
     * @return string[] "<path below $dir>: <setting>" or "<path below $dir>: <reason>", sorted
     */
    static function secretFiles( $dir )
    {
        $dir = rtrim( (string)$dir, '/' );
        if ( $dir === '' || !is_dir( $dir ) )
            return array();
        $found = array();
        $files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) );
        foreach ( $files as $file )
        {
            if ( !$file->isFile() )
                continue;
            $relative = substr( $file->getPathname(), strlen( $dir ) + 1 );
            $parts = explode( '/', $relative );
            $name = array_pop( $parts );
            if ( preg_match( self::KEY_FILE_PATTERN, $name ) )
            {
                $found[$relative . ': key material'] = true;
                continue;
            }
            foreach ( $parts as $part )
            {
                if ( preg_match( self::KEY_DIRECTORY_PATTERN, $part ) )
                {
                    $found[$relative . ': key material'] = true;
                    continue 2;
                }
            }
            if ( !isset( $parts[0] ) || $parts[0] !== 'settings' )
                continue;
            if ( preg_match( self::BACKUP_FILE_PATTERN, $name ) )
            {
                $found[$relative . ': copy of a settings file'] = true;
                continue;
            }
            foreach ( (array)@file( $file->getPathname(), FILE_IGNORE_NEW_LINES ) as $line )
            {
                if ( !preg_match( '/^\s*([A-Za-z0-9_.\-]+)(\[[^\]]*\])?\s*=(.*)$/', (string)$line, $m ) )
                    continue;
                $value = trim( $m[3] );
                // a number or a switch is a limit or a choice (MaxTokens=4096, RequireToken=enabled), never a secret
                if ( $value === '' || preg_match( '/^(-?\d+(\.\d+)?|true|false|enabled|disabled|yes|no|on|off|none|null)$/i', $value ) )
                    continue;
                if ( expSystemReportMask::isSecretSettingName( $m[1] ) || expSecretRule::hasInlineSecret( $value ) )
                    $found[$relative . ': ' . $m[1]] = true;
            }
        }
        $found = array_keys( $found );
        sort( $found );
        return $found;
    }

    /**
     * Why the extension $extensionName cannot be packed, or an empty string when it can (isPackableName() and no
     * secretFiles()).
     *
     * @param string $extensionName
     * @return string
     */
    static function packRefusal( $extensionName )
    {
        if ( !self::isPackableName( $extensionName ) )
            return ezpI18n::tr( 'kernel/package', 'The extension %name is not known.', null, array( '%name' => (string)$extensionName ) );
        $secrets = self::secretFiles( eZExtension::baseDirectory() . '/' . $extensionName );
        if ( !$secrets )
            return '';
        return ezpI18n::tr( 'kernel/package', 'The extension %name was not packed: it holds secret values or key material (%files). Copy it from the server instead.', null,
                            array( '%name' => $extensionName,
                                   '%files' => implode( ', ', array_slice( $secrets, 0, 5 ) ) . ( count( $secrets ) > 5 ? ', ...' : '' ) ) );
    }

    /**
     * Adds the extension $extensionName to $package. Refuses (RuntimeException, nothing copied) an extension that
     * packRefusal() names a reason for: every way to a package (Setup > Extensions, the package wizard, the command
     * line) passes here.
     *
     * @param eZPackage $package
     * @param string $extensionName
     * @throws RuntimeException
     */
    static function addExtension( $package, $extensionName )
    {
        $refusal = self::packRefusal( $extensionName );
        if ( $refusal !== '' )
            throw new RuntimeException( $refusal );

        $fileList = array();
        $sourceDir = eZExtension::baseDirectory() . '/' . $extensionName;
        $targetDir = $package->path() . '/ezextension';

        eZDir::mkdir( $targetDir, false, true );
        eZDir::copy( $sourceDir, $targetDir );

        eZDir::recursiveList( $targetDir, '', $fileList );

        $doc = new DOMDocument;

        $packageRoot = $doc->createElement( 'extension' );
        $packageRoot->setAttribute( 'name', $extensionName );

        foreach( $fileList as $file )
        {
            $fileNode = $doc->createElement( 'file' );
            $fileNode->setAttribute( 'name', $file['name'] );

            if ( $file['path'] )
                $fileNode->setAttribute( 'path', $file['path'] );

            $fullPath = $targetDir . $file['path'] . '/' . $file['name'];
            $fileNode->setAttribute( 'md5sum', $package->md5sum( $fullPath ) );

            if ( $file['type'] == 'dir' )
                 $fileNode->setAttribute( 'type', 'dir' );

            $packageRoot->appendChild( $fileNode );
            unset( $fileNode );
        }

        $filename = 'extension-' . $extensionName;

        $package->appendInstall( 'ezextension', false, false, true,
                                 $filename, 'ezextension',
                                 array( 'content' => $packageRoot ) );
        $package->appendInstall( 'ezextension', false, false, false,
                                 $filename, 'ezextension',
                                 array( 'content' => false ) );
    }

    function handleAddParameters( $packageType, $package, $cli, $arguments )
    {
        $arguments = array_unique( $arguments );
        $extensionsToAdd = array();

        $extensionList = array();
        foreach ( eZExtension::extensionRootDirectories() as $extensionRoot )
        {
            if ( is_dir( $extensionRoot ) )
            {
                foreach ( eZDir::findSubItems( $extensionRoot, 'dl' ) as $extensionName )
                {
                    $extensionList[$extensionName] = $extensionName;
                }
            }
        }
        $extensionList = array_values( $extensionList );

        foreach ( $arguments as $argument )
        {
            if ( in_array( $argument, $extensionList ) )
            {
                $refusal = self::packRefusal( $argument );
                if ( $refusal !== '' )
                {
                    $cli->error( $refusal );
                    return false;
                }
                $extensionsToAdd[] = $argument;
            }
            else
            {
                $cli->error( 'Extension ' . $cli->style( 'dir' ) . $argument .  $cli->style( 'dir-end' ) . ' not found.' );
                return false;
            }
        }

        return $extensionsToAdd;
    }

    public $Package = null;
}

?>
