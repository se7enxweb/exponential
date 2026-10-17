<?php
/**
 * The log manager the views of the log viewer use (doc/features/6.0/log-viewer.md, "Managing logs"): the kernel's
 * service expLogManager when it is there, else expLogManagerFallback, which does the same on the log viewer's own
 * classes. Both implement expLogManagerInterface.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expLogManagerFactory
{
    /**
     * @param expLogViewerConfig|null $config null: this request's
     * @param expLogFileLocator|null $locator null: one on $config
     * @return expLogManagerInterface
     */
    public static function get( ?expLogViewerConfig $config = null, ?expLogFileLocator $locator = null )
    {
        $config = $config !== null ? $config : expLogViewerConfig::instance();
        $locator = $locator !== null ? $locator : new expLogFileLocator( $config );
        if ( class_exists( 'expLogManager' ) )
        {
            $manager = new expLogManager( $config, $locator );
            if ( $manager instanceof expLogManagerInterface || method_exists( $manager, 'plan' ) )
                return $manager;
        }
        return new expLogManagerFallback( $config, $locator );
    }
}
