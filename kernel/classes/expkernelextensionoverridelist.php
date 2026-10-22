<?php
/**
 * File containing the expKernelExtensionOverrideList class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The service "extensions.overrides": what the two extension activation passes of the web kernel
 * (eZExtension::activateExtensions() 'default' and 'access') give for the current siteaccess, as compiled.
 *
 * Read-only. The kernel applies the compiled passes itself (expKernelServices::applyExtensionStage()); this object
 * is for code and tools that want to see them: the settings override directories in order, the extension
 * directories, and whether this request used the compiled result or ran the passes live.
 *
 * @since 6.0.15
 * @package kernel
 */
class expKernelExtensionOverrideList
{
    /** @var array|null */
    protected $index;

    /** @var array|null */
    protected $siteAccessData;

    public function __construct()
    {
        $this->index = expKernelServices::compiledIndex();
        $this->siteAccessData = expKernelServices::compiledSiteAccess();
    }

    /**
     * Whether a compiled result exists for this siteaccess.
     *
     * @return bool
     */
    public function isCompiled()
    {
        return $this->index !== null && $this->siteAccessData !== null;
    }

    /**
     * Whether this request applied the compiled result of a pass ('default' or 'access') instead of running it.
     *
     * @param string $stage
     * @return bool
     */
    public function wasApplied( $stage = 'access' )
    {
        // asked anew each time: this object is kept for the worker, the answer belongs to the request
        $status = expKernelServices::status();
        return !empty( $status['applied'][$stage] );
    }

    /**
     * The siteaccess, or null.
     *
     * @return string|null
     */
    public function siteAccess()
    {
        return $this->siteAccessData !== null ? $this->siteAccessData['siteaccess'] : null;
    }

    /**
     * The generation of the compiled container, or null.
     *
     * @return string|null
     */
    public function generation()
    {
        return $this->index !== null ? $this->index['generation'] : null;
    }

    /**
     * The settings override directories after a pass, in eZINI's raw form (scope => identifier => array( dir,
     * global )), or the current ones of eZINI when nothing is compiled.
     *
     * @param string $stage 'default' or 'access'
     * @return array
     */
    public function overrideDirs( $stage = 'access' )
    {
        if ( $stage === 'default' && $this->index !== null )
            return $this->index['default']['after'];
        if ( $stage === 'access' && $this->siteAccessData !== null )
            return $this->siteAccessData['stage']['after'];
        return eZINI::globalOverrideDirs( false );
    }

    /**
     * The settings directories in the order eZINI reads them (last wins), as a flat list of paths.
     *
     * @param string $stage
     * @return array
     */
    public function directoryList( $stage = 'access' )
    {
        $dirs = $this->overrideDirs( $stage );
        $list = array();
        foreach ( array( 'sa-extension', 'siteaccess', 'extension', 'override' ) as $scope )
        {
            foreach ( isset( $dirs[$scope] ) ? $dirs[$scope] : array() as $item )
                $list[] = $item[1] ? $item[0] : 'settings/' . $item[0];
        }
        return $list;
    }

    /**
     * The directory of each active extension, name => path, as recorded.
     *
     * @return array
     */
    public function extensionPaths()
    {
        if ( $this->siteAccessData !== null )
            return $this->siteAccessData['extensions']['paths'];
        if ( $this->index !== null )
            return $this->index['extensions']['paths'];
        $caches = eZExtension::exportCaches();
        return $caches['paths'];
    }
}
