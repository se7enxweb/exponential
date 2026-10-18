<?php
/**
 * The files the help may serve (workstream B): git-tracked files below the configured roots only, no '..', no
 * symlink, realpath inside the root, allowed suffix, not excluded, under the size limit. Skeleton: B replaces it.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpAllowlist
{
    /** @return string[] repo-relative paths of every servable file */
    public function files() { return array(); }

    /** @param string $repoPath @return bool */
    public function allows( $repoPath ) { return false; }
}

?>
