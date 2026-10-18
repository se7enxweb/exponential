<?php
/**
 * Front matter of a help page (workstream B): a leading "---" block in a tiny YAML subset. Skeleton.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpFrontMatter
{
    /**
     * @param string $text
     * @return array array( array $meta, string $body )
     */
    public static function split( $text ) { return array( array(), (string)$text ); }
}

?>
