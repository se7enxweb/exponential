<?php
/**
 * File containing the expUpgradeStepRssOpmlPodcast class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * OPML exports and podcast feeds: ezrss_export.opml_head, podcast_head and the table ezrss_export_opml_item.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepRssOpmlPodcast extends expUpgradeSchemaStep
{
    public function id()
    {
        return '6.0.15-rss-opml-podcast';
    }

    public function description()
    {
        return 'OPML and podcast feeds: ezrss_export.opml_head, podcast_head and the table ezrss_export_opml_item';
    }

    public function tables()
    {
        return array( 'ezrss_export_opml_item' );
    }

    public function fields()
    {
        return array( 'ezrss_export' => array( 'opml_head', 'podcast_head' ) );
    }
}
