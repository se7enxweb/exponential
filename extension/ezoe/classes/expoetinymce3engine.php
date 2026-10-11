<?php
/**
 * The default engine: eZ Online Editor on TinyMCE 3.5, rendered by ezxmltext_ezoe.tpl itself.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package ezoe
 */

class expOETinyMCE3Engine extends expOEEditorEngineBase
{
    public function identifier()
    {
        return 'tinymce3';
    }

    public function label()
    {
        return 'TinyMCE 3';
    }

    public function version()
    {
        return self::tinyMCEVersion( dirname( __DIR__ ) . '/design/standard/javascript/tiny_mce.js' );
    }

    public function description()
    {
        return 'The Online Editor on TinyMCE 3.5, the editor of every installation.';
    }
}
