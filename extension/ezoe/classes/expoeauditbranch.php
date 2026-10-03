<?php
/**
 * The audit taxonomy branch of the online editor: the choice of the editor engine. Registered in
 * settings/audit.ini.append.php.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package ezoe
 */

class expOEAuditBranch implements expAuditTaxonomyBranch
{
    public function events()
    {
        return array(
            'content.ezoe.engine.change' => array( 'label' => 'Editor engine chosen', 'severity' => 'info', 'channel' => 'content', 'default' => 'on' ),
            'content.ezoe.upload.url' => array( 'label' => 'File fetched from a URL by the editor', 'severity' => 'info', 'channel' => 'content', 'default' => 'on' ),
        );
    }
}
