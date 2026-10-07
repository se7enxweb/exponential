<?php
/**
 * File containing the eZStepSiteTypes class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZStepSiteTypes ezstep_site_types.php
  \brief The class eZStepSiteTypes does

*/

class eZStepSiteTypes extends eZStepInstaller
{
    /**
     * @var bool
     */
    public $ShowURL;
    /**
     * Constructor
     *
     * @param eZTemplate $tpl
     * @param eZHTTPTool $http
     * @param eZINI $ini
     * @param array $persistenceList
     */
    public function __construct( $tpl, $http, $ini, &$persistenceList )
    {
        $ini = eZINI::instance( 'package.ini' );
        $indexURL = trim( $ini->variable( 'RepositorySettings', 'RemotePackagesIndexURL' ) );
        if ( $indexURL === '' )
        {
            $indexURL = trim( $ini->variable( 'RepositorySettings', 'RemotePackagesIndexURLBase' ) );
            if ( substr( $indexURL, -1, 1 ) !== '/' )
            {
                $indexURL .= '/';
            }
            $indexURL .= ExponentialSDK::version( false, false, false ) . '/' . ExponentialSDK::version() . '/';
        }
        $this->IndexURL = $indexURL;

        if ( substr( $this->IndexURL, -1, 1 ) == '/' )
            $this->XMLIndexURL = $this->IndexURL . 'index.xml';
        else
            $this->XMLIndexURL = $this->IndexURL . '/index.xml';

        parent::__construct( $tpl, $http, $ini, $persistenceList, 'site_types', 'Site types' );
    }

    /**
     * Downloads file.
     *
     * A failed attempt is tried again ([RepositorySettings] DownloadAttempts in
     * package.ini, waiting DownloadRetryDelays seconds in between): a package
     * server that is being republished answers with errors for a few minutes.
     * An attempt fails on a transfer error, an HTTP status other than 200, an
     * empty or truncated body, and an .ezpkg file that is not gzip data. A
     * failed attempt leaves no file behind.
     *
     * Sets $this->ErrorMsg in case of an error: the reason of the last attempt
     * (curl error, HTTP status, bytes received). $this->DownloadAttemptCount
     * says how many attempts were made.
     *
     * \private
     * \param $url            URL.
     * \param $outDir         Directory where to put downloaded file to.
     * \param $forcedFileName Force saving downloaded file under this name.
     * \return false on error, path to downloaded package otherwise.
     */
    function downloadFile( $url, $outDir, $forcedFileName = false )
    {
        $fileName = $outDir . "/" . ( $forcedFileName ? $forcedFileName : basename( $url ) );

        eZDebug::writeNotice( "Downloading file '$fileName' from $url" );

        // Create the out directory if not exists.
        if ( !file_exists( $outDir ) )
            eZDir::mkdir( $outDir, false, true );

        $attempts = max( 1, (int)$this->downloadSetting( 'DownloadAttempts', 3 ) );
        $delays = (array)$this->downloadSetting( 'DownloadRetryDelays', array( 2, 5, 10 ) );
        $this->DownloadAttemptCount = 0;

        for ( $attempt = 1; $attempt <= $attempts; ++$attempt )
        {
            $this->DownloadAttemptCount = $attempt;
            $failure = $this->DownloadTransport === 'stream' || ( $this->DownloadTransport !== 'curl' && !extension_loaded( 'curl' ) )
                ? $this->downloadAttemptStream( $url, $fileName )
                : $this->downloadAttemptCurl( $url, $fileName );

            if ( $failure === null )
                return $fileName;

            $this->removeDownloadedFile( $fileName );
            $this->ErrorMsg = $failure['reason'];

            if ( !$failure['retry'] || $attempt >= $attempts )
                break;

            $delays = array_values( $delays );
            $delay = $delays ? (float)$delays[min( $attempt, count( $delays ) ) - 1] : 0;
            $message = "Download of $url, attempt $attempt of $attempts failed: {$failure['reason']}; trying again in {$delay} s";
            eZDebug::writeWarning( $message );
            expSetupLog::problem( 'WARNING', $message );
            if ( $delay > 0 )
                usleep( (int)round( $delay * 1000000 ) );
        }

        return false;
    }

    /**
     * One download over curl. Null on success, otherwise array( 'reason' => string, 'retry' => bool ).
     */
    protected function downloadAttemptCurl( $url, $fileName )
    {
        $fp = $this->fopen( $fileName, 'wb' );
        if ( $fp === false )
        {
            return array( 'reason' => ezpI18n::tr( 'design/standard/setup/init', 'Cannot write to file' ) .
                                      ': ' . $this->FileOpenErrorMsg,
                          'retry' => false );
        }

        $ch = curl_init( $url );
        curl_setopt( $ch, CURLOPT_FILE, $fp );
        curl_setopt( $ch, CURLOPT_HEADER, 0 );
        // The status is checked below, so that the reason can name it
        curl_setopt( $ch, CURLOPT_FAILONERROR, 0 );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
        curl_setopt( $ch, CURLOPT_MAXREDIRS, 5 );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, (int)$this->downloadSetting( 'DownloadConnectTimeout', 30 ) );
        curl_setopt( $ch, CURLOPT_TIMEOUT, (int)$this->downloadSetting( 'DownloadTimeout', 300 ) );
        // Get proxy
        $ini = eZINI::instance();
        $proxy = $ini->hasVariable( 'ProxySettings', 'ProxyServer' ) ? $ini->variable( 'ProxySettings', 'ProxyServer' ) : false;
        if ( $proxy )
        {
            curl_setopt ( $ch, CURLOPT_PROXY , $proxy );
            $userName = $ini->hasVariable( 'ProxySettings', 'User' ) ? $ini->variable( 'ProxySettings', 'User' ) : false;
            $password = $ini->hasVariable( 'ProxySettings', 'Password' ) ? $ini->variable( 'ProxySettings', 'Password' ) : false;
            if ( $userName )
            {
                curl_setopt ( $ch, CURLOPT_PROXYUSERPWD, "$userName:$password" );
            }
        }

        $ok = curl_exec( $ch );
        $errno = curl_errno( $ch );
        $error = curl_error( $ch );
        $status = (int)curl_getinfo( $ch, CURLINFO_HTTP_CODE );
        $expected = (float)curl_getinfo( $ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD );
        // curl_close() does nothing since PHP 8.0 and is deprecated in 8.5: the handle closes when it is released
        unset( $ch );
        fclose( $fp );

        $bytes = $this->downloadedSize( $fileName );
        if ( $ok === false || $errno )
        {
            $reason = 'curl error ' . $errno . ': ' . $error;
            if ( $status > 0 )
                $reason .= ', HTTP ' . $status;
            return array( 'reason' => $reason . ', ' . $bytes . ' bytes received', 'retry' => true );
        }

        return $this->checkDownloadedFile( $url, $fileName, $status, $bytes, $expected > 0 ? (int)$expected : -1 );
    }

    /**
     * One download over the PHP stream wrappers, for an installation without
     * curl. Null on success, otherwise array( 'reason' => string, 'retry' => bool ).
     */
    protected function downloadAttemptStream( $url, $fileName )
    {
        $parsedUrl = parse_url( $url );
        $isHttp = isset( $parsedUrl['scheme'] ) && in_array( strtolower( $parsedUrl['scheme'] ), array( 'http', 'https' ) );
        if ( $isHttp )
        {
            $host = isset( $parsedUrl['host'] ) ? $parsedUrl['host'] : '';
            if ( $host === '' || ip2long( gethostbyname( $host ) ) === false )
                return array( 'reason' => "cannot resolve the host '$host'", 'retry' => true );
        }

        $timeout = (int)$this->downloadSetting( 'DownloadTimeout', 300 );
        $context = stream_context_create( array( 'http' => array( 'timeout' => $timeout,
                                                                  'ignore_errors' => true,
                                                                  'follow_location' => 1,
                                                                  'max_redirects' => 5 ) ) );
        // Note: Could be blocked by not allowing remote calls.
        error_clear_last();
        $body = @file_get_contents( $url, false, $context );
        $headers = array();
        if ( function_exists( 'http_get_last_response_headers' ) )
            $headers = (array)http_get_last_response_headers();
        else
            $headers = (array)( get_defined_vars()['http_response_header'] ?? array() );

        $status = 0;
        $expected = -1;
        foreach ( $headers as $header )
        {
            // After a redirect, the last response's lines come last
            if ( preg_match( '#^HTTP/\S+\s+(\d{3})#', $header, $matches ) )
            {
                $status = (int)$matches[1];
                $expected = -1;
            }
            elseif ( preg_match( '#^Content-Length:\s*(\d+)#i', $header, $matches ) )
                $expected = (int)$matches[1];
        }

        if ( $body === false && $isHttp && strtolower( $parsedUrl['scheme'] ) === 'http' )
        {
            $buf = eZHTTPTool::sendHTTPRequest( $url, 80, false, 'Exponential', false );
            $header = false;
            if ( $buf && eZHTTPTool::parseHTTPResponse( $buf, $header, $body ) &&
                 preg_match( '#^HTTP/\S+\s+(\d{3})#', $buf, $matches ) )
            {
                $status = (int)$matches[1];
                $expected = isset( $header['content-length'] ) ? (int)$header['content-length'] : -1;
            }
            else
                $body = false;
        }

        if ( $body === false )
        {
            $last = error_get_last();
            $reason = ezpI18n::tr( 'design/standard/setup/init', 'Failed to copy %url to local file %filename', null,
                                   array( "%url" => $url, "%filename" => $fileName ) );
            if ( $last && !empty( $last['message'] ) )
                $reason .= ': ' . $last['message'];
            if ( $status > 0 )
                $reason .= ', HTTP ' . $status;
            return array( 'reason' => $reason, 'retry' => true );
        }

        if ( @file_put_contents( $fileName, $body ) === false )
        {
            return array( 'reason' => ezpI18n::tr( 'design/standard/setup/init', 'Cannot write to file' ) . ': ' . $fileName,
                          'retry' => false );
        }

        return $this->checkDownloadedFile( $url, $fileName, $status, strlen( $body ), $expected );
    }

    /**
     * Whether a finished transfer is a complete file. Null when it is,
     * otherwise array( 'reason' => string, 'retry' => true ).
     *
     * \param $expected the Content-Length the server announced, -1 when it announced none.
     */
    protected function checkDownloadedFile( $url, $fileName, $status, $bytes, $expected )
    {
        $scheme = strtolower( (string)parse_url( $url, PHP_URL_SCHEME ) );
        $reason = false;
        if ( ( $scheme === 'http' || $scheme === 'https' ) && $status !== 200 )
            $reason = 'HTTP ' . $status;
        elseif ( $bytes <= 0 )
            $reason = 'the server sent an empty file';
        elseif ( $expected >= 0 && $bytes != $expected )
            $reason = "the file is truncated, $expected bytes expected";
        elseif ( substr( $fileName, -6 ) === '.ezpkg' && !$this->isGzipFile( $fileName ) )
            $reason = 'the file is not a gzip-compressed package';

        if ( $reason === false )
            return null;
        return array( 'reason' => $reason . ', ' . $bytes . ' bytes received', 'retry' => true );
    }

    /** Whether a file starts with the gzip magic bytes. */
    protected function isGzipFile( $fileName )
    {
        $fp = @fopen( $fileName, 'rb' );
        if ( !$fp )
            return false;
        $magic = fread( $fp, 2 );
        fclose( $fp );
        return $magic === "\x1f\x8b";
    }

    protected function downloadedSize( $fileName )
    {
        clearstatcache( true, $fileName );
        return file_exists( $fileName ) ? (int)filesize( $fileName ) : 0;
    }

    protected function removeDownloadedFile( $fileName )
    {
        clearstatcache( true, $fileName );
        if ( file_exists( $fileName ) )
            @unlink( $fileName );
    }

    /**
     * A download setting: the property of the same name when it is set (tests
     * set it), else package.ini [RepositorySettings], else $default.
     */
    protected function downloadSetting( $name, $default )
    {
        if ( isset( $this->DownloadSettings[$name] ) )
            return $this->DownloadSettings[$name];
        $ini = eZINI::instance( 'package.ini' );
        if ( $ini->hasVariable( 'RepositorySettings', $name ) )
        {
            $value = $ini->variable( 'RepositorySettings', $name );
            if ( $value !== '' && $value !== array() )
                return $value;
        }
        return $default;
    }

    /**
     * Downloads and imports package.
     *
     * Sets $this->ErrorMsg in case of an error.
     *
     * \param $forceDownload  download even if this package already exists.
     * \private
     * \return false on error, package object otherwise.
     */
    function downloadAndImportPackage( $packageName, $packageUrl, $forceDownload = false, $repositoryID = false, $skipExisting = false )
    {
        if ( !$skipExisting )
        {
            $package = eZPackage::fetch( $packageName, false, false, false );

            if ( is_object( $package ) )
            {
                if ( $forceDownload )
                {
                    $package->remove();
                }
                else
                {
                    eZDebug::writeNotice( "Skipping download of package '$packageName': package already exists." );
                    return $package;
                }
            }
        }

        $archiveName = $this->downloadFile( $packageUrl, /* $outDir = */ eZStepSiteTypes::tempDir() );
        if ( $archiveName === false )
        {
            $reason = $this->ErrorMsg;
            eZDebug::writeWarning( "Download of package '$packageName' from '$packageUrl' failed after $this->DownloadAttemptCount attempt(s): $reason" );
            if ( $this->DownloadAttemptCount > 1 )
                $this->ErrorMsg = ezpI18n::tr( 'design/standard/setup/init',
                                          'Download of package \'%pkg\' failed after %count attempts: %reason. You may upload the package manually.',
                                          false, array( '%pkg' => $packageName, '%count' => $this->DownloadAttemptCount, '%reason' => $reason ) );
            else
                $this->ErrorMsg = ezpI18n::tr( 'design/standard/setup/init',
                                          'Download of package \'%pkg\' failed: %reason. You may upload the package manually.',
                                          false, array( '%pkg' => $packageName, '%reason' => $reason ) );

            return false;
        }

        $package = eZPackage::import( $archiveName, $packageName, false, $repositoryID, $skipExisting );

        // Remove downloaded ezpkg file
        $ezFileHandler = new eZFileHandler();
        $ezFileHandler->unlink( $archiveName );

        if ( !$package instanceof eZPackage )
        {
            if ( $package == eZPackage::STATUS_INVALID_NAME )
            {
                eZDebug::writeNotice( "The package name $packageName is invalid" );
            }
            else
            {
                eZDebug::writeNotice( "Invalid package" );
            }

            $this->ErrorMsg = ezpI18n::tr( 'design/standard/setup/init', 'Invalid package' );
            return false;
        }

        return $package;
    }


    /*!
     * Download packages required by the given package.
     *
     * \private
     */
    function downloadDependantPackages( $sitePackage, $repositoryID = false, $skipExisting = false )
    {
        $dependencies = $sitePackage->attribute( 'dependencies' );
        $requirements = $dependencies['requires'];
        $remotePackagesInfo = $this->retrieveRemotePackagesList();

        foreach ( $requirements as $req )
        {
            $requiredPackageName = $req['name'];

            if ( isset( $req['min-version'] ) )
                $requiredPackageVersion = $req['min-version'];
            else
                $requiredPackageVersion = 0;

            $downloadNewPackage   = false;
            $removeCurrentPackage = false;

            if ( $skipExisting )
            {
                // Dry-run mode: download all required packages from the remote
                // source so the full dependency chain can be verified.
                $downloadNewPackage = true;
            }
            else
            {
                // try to fetch the required package
                $package = eZPackage::fetch( $requiredPackageName, false, false, false );

                // if it already exists
                if ( is_object( $package ) )
                {
                    // check its version
                    $currentPackageVersion = $package->getVersion();

                    // if existing package's version is less than required one
                    // we remove the package and download newer one.

                    if ( eZPackageVersion::compareFull( $currentPackageVersion, $requiredPackageVersion ) < 0 )
                    {
                        $downloadNewPackage   = true;
                        $removeCurrentPackage = true;
                    }

                    // else (if the version is greater or equal to the required one)
                    // then do nothing (skip downloading)
                }
                else
                    // if the package does not exist, we download it.
                    $downloadNewPackage   = true;
            }

            if ( $removeCurrentPackage )
            {
                $package->remove();
                unset( $package );
            }

            if ( $downloadNewPackage )
            {
                if ( !isset( $remotePackagesInfo[$requiredPackageName]['url'] ) )
                {
                    eZDebug::writeWarning( "Download of package '$requiredPackageName' failed: the URL is unknown." );
                    $this->ErrorMsg = ezpI18n::tr( 'design/standard/setup/init',
                                              'Download of package \'%pkg\' failed. You may upload the package manually.',
                                              false, array( '%pkg' => $requiredPackageName ) );
                    $this->ShowURL = true;

                    return false;
                }

                $requiredPackageURL = $remotePackagesInfo[$requiredPackageName]['url'];
                $rc = $this->downloadAndImportPackage( $requiredPackageName, $requiredPackageURL, false, $repositoryID, $skipExisting );
                if( !is_object( $rc ) )
                {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Upload local package.
     *
     * \private
     */
    function uploadPackage()
    {

        if ( !eZHTTPFile::canFetch( 'PackageBinaryFile' ) )
        {
            $this->ErrorMsg = ezpI18n::tr( 'design/standard/setup/init',
                                      'No package selected for upload' ) . '.';
            return;
        }

        $file = eZHTTPFile::fetch( 'PackageBinaryFile' );
        if ( !$file )
        {
            $this->ErrorMsg = ezpI18n::tr( 'design/standard/setup/init',
                                      'Failed fetching upload package file' );
            return;
        }

        $packageFilename = $file->attribute( 'filename' );
        $packageName = $file->attribute( 'original_filename' );
        if ( preg_match( "#^(.+)-[0-9](\.[0-9]+)-[0-9].ezpkg$#", $packageName, $matches ) )
            $packageName = $matches[1];
        $packageName = preg_replace( array( "#[^a-zA-Z0-9]+#",
                                            "#_+#",
                                            "#(^_)|(_$)#" ),
                                     array( '_',
                                            '_',
                                            '' ), $packageName );
        $package = eZPackage::import( $packageFilename, $packageName, false );

        if ( is_object( $package ) )
        {
            // package successfully imported
            return;
        }
        elseif ( $package == eZPackage::STATUS_ALREADY_EXISTS )
        {
            eZDebug::writeWarning( "Package '$packageName' already exists." );
        }
        else
        {
            $this->ErrorMsg = ezpI18n::tr( 'design/standard/setup/init',
                                  'Uploaded file is not an Exponential package' );
        }
    }

    /**
     * Process POST data.
     *
     * \reimp
     */
    function processPostData()
    {
        if ( $this->Http->hasPostVariable( 'UploadPackageButton' ) )
        {
            $this->uploadPackage();
            return false; // force displaying the same step.
        }

        if ( !$this->Http->hasPostVariable( 'eZSetup_site_type' ) )
        {
            $this->ErrorMsg = ezpI18n::tr( 'design/standard/setup/init',
                                      'No site package chosen.' );
            return false;
        }

        $sitePackageInfo = $this->Http->postVariable( 'eZSetup_site_type' );
        $downloaded = false; // true - if $sitePackageName package has been downloaded.
        if ( preg_match( '/^(\w+)\|(.+)$/', $sitePackageInfo, $matches ) )
        {
            // remote site package chosen: download it.
            $sitePackageName = $matches[1];
            $sitePackageURL  = $matches[2];

            // we already know that we should download the package anyway as it has newer version
            // so use force download mode
            $package = $this->downloadAndImportPackage( $sitePackageName, $sitePackageURL, true );
            if ( is_object( $package ) )
            {
                $downloaded = true;
                $this->Message = ezpI18n::tr( 'design/standard/setup/init', 'Package \'%packageName\' and it\'s dependencies have been downloaded successfully. Press \'Next\' to continue.', false, array( '%packageName' => $sitePackageName ) );
            }
        }
        else
        {
            // local (already imported) site package chosen: just fetch it.
            $sitePackageName = $sitePackageInfo;

            $package = eZPackage::fetch( $sitePackageName, false, false, false );

            if ( !is_object( $package ) )
                $this->ErrorMsg = ezpI18n::tr( 'design/standard/setup/init', 'Invalid package' ) . '.';
            else
                $downloaded = true;
        }

        // Verify package.
        if ( !is_object( $package ) || !$this->selectSiteType( $sitePackageName ) )
            return false;

        // Download packages that the site package requires.
        $downloadDependandPackagesResult = $this->downloadDependantPackages( $package );
        return $downloadDependandPackagesResult == false ? false : $downloaded;
    }

    function init()
    {
        if ( $this->hasKickstartData() )
        {
            $data = $this->kickstartData();
            $remoteSitePackages = $this->retrieveRemoteSitePackagesList();
            $importedSitePackages = $this->fetchAvailableSitePackages();
            $dependenciesStatus = array();

            // check site package dependencies to show their status in the template
            foreach ( $importedSitePackages as $sitePackage )
            {
                $sitePackageName = $sitePackage->attribute( 'name' );
                $dependencies = $sitePackage->attribute( 'dependencies' );
                $requirements = $dependencies['requires'];

                foreach ( $requirements as $req )
                {
                    $requiredPackageName    = $req['name'];
                    $requiredPackageVersion = $req['min-version'];
                    $packageOK = false;

                    $package = eZPackage::fetch( $requiredPackageName, false, false, false );
                    if ( is_object( $package ) )
                    {
                        $currentPackageVersion = $package->getVersion();
                        if ( eZPackageVersion::compareFull( $currentPackageVersion, $requiredPackageVersion ) >= 0 )
                            $packageOK = true;
                    }

                    $dependenciesStatus[$sitePackageName][$requiredPackageName] = array( 'version' => $requiredPackageVersion,
                                                                                     'status'  => $packageOK );
                }
            }

            $sitePackages = $this->createSitePackagesList( $remoteSitePackages, $importedSitePackages, $dependenciesStatus, true );
            $chosenSitePackage = $data['Site_package'];
            $downloaded = false;
            foreach( $sitePackages as $sitePackagesInfo )
            {
                if( $sitePackagesInfo['name'] == $chosenSitePackage )
                {
                    $sitePackagesInfoChoosen = $sitePackagesInfo;
                }
            }
            if ( isset( $sitePackagesInfoChoosen ) and array_key_exists( 'url', $sitePackagesInfoChoosen ) )
            {
                // Dry-run mode imports into a temporary repository so it does not overwrite
                // the locally cached packages before the install is confirmed.
                $dryRun = ( isset( $this->Http ) && $this->Http->hasPostVariable( 'eZSetupKickstartDryRun' ) );
                $repositoryID = $dryRun ? 'dryrun' : false;
                $skipExisting = $dryRun;
                // In kickstart mode, prefer local packages and only download from the
                // remote repository if the package is missing locally.
                $forceDownload = false;

                $package = $this->downloadAndImportPackage( $chosenSitePackage, $sitePackagesInfoChoosen['url'], $forceDownload, $repositoryID, $skipExisting );
                if ( is_object( $package ) )
                {
                    $downloadDependandPackagesResult = $this->downloadDependantPackages( $package, $repositoryID, $skipExisting );
                    if ( $downloadDependandPackagesResult != false )
                    {
                        $downloaded = true;
                    }
                }
            }
            else if ( isset( $sitePackagesInfoChoosen ) and !isset( $sitePackagesInfoChoosen['url'] ) )
            {
                // Site package found locally. Checking if all requiremens are downloaded.
                // This would be the case for offline installations with manually downloaded packages.
                $chosenRequirements = $sitePackagesInfoChoosen['requires'];
                $reqsDownloaded = true;
                foreach( $chosenRequirements as $creq )
                {
                    if( !$creq['status'] )
                    {
                        // Required packages missing. Stalling on this step.
                        $reqsDownloaded = false;
                        break;
                    }
                }
                $downloaded = $reqsDownloaded;
                // Template should show Site_package set in kickstart.ini
                $this->selectSiteType( $chosenSitePackage );
            }

            if ( $downloaded and $this->selectSiteType( $chosenSitePackage ) )
            {
                return $this->kickstartContinueNextStep();
            }
        }

        if ( !isset( $this->ErrorMsg ) )
            $this->ErrorMsg = false;

        return false; // Always show site template selection
    }

    /**
     * \private
     */
    function createSitePackagesList( $remoteSitePackages, $importedSitePackages, $dependenciesStatus, $preferRemote = false )
    {
        $sitePackages = array();

        if ( is_array( $remoteSitePackages ) )
        {
            foreach ( $remoteSitePackages as $packageInfo )
            {
                $packageName = $packageInfo['name'];
                $sitePackages[$packageName] = $packageInfo;
            }
        }

        foreach ( $importedSitePackages as $package )
        {
            $packageName = $package->attribute( 'name' );
            $packageVersion = $package->getVersion();

            if ( isset( $sitePackages[$packageName] ) )
            {
                // Kickstart mode should prefer the remote package list. The package index
                // version attribute is not comparable with the local package version, so
                // always keep the remote entry when a kickstart configuration is active.
                if ( $preferRemote )
                    continue;

                $remoteVersion = $sitePackages[$packageName]['version'];
                $localVersion = $packageVersion;

                if ( eZPackageVersion::compareFull( $remoteVersion, $localVersion ) > 0 )
                    continue;
            }

            $thumbnails = $package->attribute( 'thumbnail-list' );

            $thumbnailPath = false;
            if ( $thumbnails )
            {
                $thumbnailFile = $thumbnails[0];
                $thumbnailPath = $package->fileItemPath( $thumbnailFile, 'default' );
            }

            $dependencies = $package->attribute( 'dependencies' );
            $requirements = $dependencies['requires'];

            $requiresPackageInfo = isset( $dependenciesStatus[$packageName] ) ? $dependenciesStatus[$packageName] : null;
            $packageInfo = array(
                'name' => $packageName,
                'version' => $package->getVersion(),
                'type' => $package->attribute( 'type' ),
                'summary' => $package->attribute( 'summary' ),
                'description' => $package->attribute( 'description' ),
                'requires' => $requiresPackageInfo,
                );

            if ( $thumbnailPath )
                $packageInfo['thumbnail_path'] = $thumbnailPath;

            $sitePackages[$packageName] = $packageInfo;
        }

        // Set availability status for each package.
        foreach ( $sitePackages as $idx => $packageInfo )
            $sitePackages[$idx]['status'] = !isset( $packageInfo['url'] );

        return $sitePackages;
    }

    function display()
    {
        $remoteSitePackages = $this->retrieveRemoteSitePackagesList();
        $importedSitePackages = $this->fetchAvailableSitePackages();
        $dependenciesStatus = array();

        // check site package dependencies to show their status in the template
        foreach ( $importedSitePackages as $sitePackage )
        {
            $sitePackageName = $sitePackage->attribute( 'name' );
            $dependencies = $sitePackage->attribute( 'dependencies' );
            $requirements = $dependencies['requires'];

            foreach ( $requirements as $req )
            {
                $requiredPackageName    = $req['name'];
                $requiredPackageVersion = $req['min-version'];
                $packageOK = false;

                $package = eZPackage::fetch( $requiredPackageName, false, false, false );
                if ( is_object( $package ) )
                {
                    $currentPackageVersion = $package->getVersion();
                    if ( eZPackageVersion::compareFull( $currentPackageVersion, $requiredPackageVersion ) >= 0 )
                        $packageOK = true;
                }

                $dependenciesStatus[$sitePackageName][$requiredPackageName] = array( 'version' => $requiredPackageVersion,
                                                                                     'status'  => $packageOK );
            }
        }

        $sitePackages = $this->createSitePackagesList( $remoteSitePackages, $importedSitePackages, $dependenciesStatus );

        $chosenSitePackage = $this->chosenSitePackage();
        if ( !$chosenSitePackage && !$this->Message )
        {
            // Nothing chosen yet: preselect the first site package that is
            // already in the local repository (the bundled default site), so
            // that pressing Next installs it without a download.
            foreach ( $sitePackages as $packageInfo )
            {
                if ( !isset( $packageInfo['url'] ) )
                {
                    $chosenSitePackage = $packageInfo['name'];
                    break;
                }
            }
        }

        $this->Tpl->setVariable( 'site_packages', $sitePackages );
        $this->Tpl->setVariable( 'dependencies_status', $dependenciesStatus );
        $this->Tpl->setVariable( 'chosen_package', $chosenSitePackage );
        $this->Tpl->setVariable( 'error', $this->ErrorMsg );
        $this->Tpl->setVariable( 'index_url', $this->IndexURL );
        $this->Tpl->setVariable( 'message', $this->Message );

        // Return template and data to be shown
        $result = array();
        // Display template
        $result['content'] = $this->Tpl->fetch( 'design:setup/init/site_types.tpl' );
        $result['path'] = array( array( 'text' => ezpI18n::tr( 'design/standard/setup/init',
                                                          'Site selection' ),
                                        'url' => false ) );
        return $result;
    }

    /**
     * Fetches list of site packages already available locally.
     *
     * \private
     */
    function fetchAvailableSitePackages()
    {
        $packageList = eZPackage::fetchPackages( array( 'db_available' => false ), array( 'type' => 'site' ) );

        return $packageList;
    }

    /**
     * Fetches list of packages already available locally.
     *
     * \private
     */
    function fetchAvailablePackages( $type = false )
    {
        $typeArray  = array();
        if ( $type )
            $typeArray['type'] = $type;

        $packageList = eZPackage::fetchPackages( array( 'db_available' => false ), $typeArray );

        return $packageList;
    }


    /**
     * Retrieve list of packages available to download.
     *
     * Example of return value:
     * array(
     *  'packages' => array(
     *                      '<package_name1>' => array( "name" =>... , "version" =>... , "summary" => ... "url" =>... ),
     *                      '<package_name2>' => array( "name" =>... , "version" =>... , "summary" => ... "url" =>... )
     *                     )
     *      );
     *
     */
    function retrieveRemotePackagesList( $onlySitePackages = false )
    {
        // Download index file.
        $idxFileName = $this->downloadFile( $this->XMLIndexURL, /* $outDir = */ eZStepSiteTypes::tempDir(), 'index.xml' );

        if ( $idxFileName === false )
        {
            // Searching for a local index.xml file to use for offline installation
            $destIndexPath = eZStepSiteTypes::tempDir() . DIRECTORY_SEPARATOR . 'index.xml';
            $repo = eZPackage::systemRepositoryInformation();

            if ( $repo )
            {
                $sourceIndexPath = $repo['path'] . DIRECTORY_SEPARATOR . 'index.xml';
                if ( file_exists( $sourceIndexPath ) )
                {
                    eZFileHandler::copy( $sourceIndexPath, $destIndexPath );
                    $idxFileName = $destIndexPath;
                    // Removing error message from downloadFile
                    $this->ErrorMsg = false;
                }
            }
        }

        if ( $idxFileName === false )
        {
            $this->ErrorMsg = ezpI18n::tr( 'design/standard/setup/init',
                                      'Retrieving remote site packages list failed. ' .
                                      'You may upload packages manually.' );

            eZDebug::writeNotice( "Cannot download remote packages index file from '$this->XMLIndexURL'." );
            return false;
        }

        // Parse it.
        $dom = new DOMDocument( '1.0', 'utf-8' );
        $dom->preserveWhiteSpace = false;
        $success = $dom->load( realpath( $idxFileName ) );

        @unlink( $idxFileName );

        if ( !$success )
        {
            eZDebug::writeError( "Unable to open index file." );
            return false;
        }

        $root = $dom->documentElement;

        if ( $root->localName != 'packages' )
        {
            eZDebug::writeError( "Malformed index file." );
            return false;
        }

        $packageList = array();
        foreach ( $root->childNodes as $packageNode )
        {
            if ( $packageNode->localName != 'package' ) // skip unwanted chilren
                continue;
            if ( $onlySitePackages && $packageNode->getAttribute( 'type' ) != 'site' )  // skip non-site packages
                continue;
            $packageAttributes = array();
            foreach ( $packageNode->attributes as $attributeNode )
            {
                $packageAttributes[$attributeNode->localName] = $attributeNode->value;
            }
            $packageList[$packageAttributes['name']] = $packageAttributes;
        }

        return $packageList;
    }

    /**
     * Retrieve list of site packages available to download.
     * \private
     */
    function retrieveRemoteSitePackagesList()
    {
        return $this->retrieveRemotePackagesList( true );
    }

    /**
     * Wrapper for standard fopen() doing error checking.
     *
     * \private
     * \static
     */
    function fopen( $fileName, $mode )
    {
        error_clear_last();

        if ( ( $handle = @fopen( $fileName, 'wb' ) ) === false )
            $this->FileOpenErrorMsg = implode(',', error_get_last());

        return $handle;
    }

    /**
     * Returns temporary directory used to download files to.
     *
     * \static
     */
    function tempDir()
    {
        return eZDir::path( array( eZSys::cacheDirectory(),
                                    'packages' ) );
    }

    // current repository URL
    public $IndexURL;
    public $XMLIndexURL;

    public $Error = 0;
    public $ErrorMsg = false;
    public $FileOpenErrorMsg = false;
    public $Message = false;

    /** How many attempts the last downloadFile() made. */
    public $DownloadAttemptCount = 0;
    /** Download settings that take precedence over package.ini, e.g. array( 'DownloadRetryDelays' => array( 0.01 ) ). */
    public $DownloadSettings = array();
    /** 'curl', 'stream', or false for curl when the extension is loaded. */
    public $DownloadTransport = false;
}

?>
