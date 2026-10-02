<?php
/**
 * File containing the expIniWriteResult class: what expIniEditor::save() did.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The outcome of one expIniEditor::save().
 */
class expIniWriteResult
{
    protected $data;

    /**
     * @param array $data path, relativePath, created, backup, changed, written, dryRun, diff, warnings
     */
    public function __construct( array $data )
    {
        $this->data = $data + array(
            'path' => null, 'relativePath' => null, 'created' => false, 'backup' => null,
            'changed' => false, 'written' => false, 'dryRun' => false, 'diff' => '', 'warnings' => array(),
        );
    }

    /** @return string Absolute path of the file */
    public function path() { return $this->data['path']; }
    /** @return string Path relative to the installation root */
    public function relativePath() { return $this->data['relativePath']; }
    /** @return bool The file did not exist before (and was, or in a dry run would be, created) */
    public function created() { return $this->data['created']; }
    /** @return string|null Absolute path of the backup copy, null when none was taken */
    public function backup() { return $this->data['backup']; }
    /** @return bool There was something to change (false: the file already said so; nothing written) */
    public function changed() { return $this->data['changed']; }
    /** @return bool The file was written */
    public function written() { return $this->data['written']; }
    /** @return bool It was a dry run */
    public function dryRun() { return $this->data['dryRun']; }
    /** @return string The unified diff of the change ('' when nothing changed) */
    public function diff() { return $this->data['diff']; }
    /** @return string[] Things that went less than perfectly but did not fail the write (e.g. ownership) */
    public function warnings() { return $this->data['warnings']; }

    /** @return array Every field, for --json */
    public function toArray() { return $this->data; }
}
