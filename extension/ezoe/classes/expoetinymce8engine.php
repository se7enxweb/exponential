<?php
/**
 * The opt-in engine: eZ Online Editor on TinyMCE 8 (GPL), rendered by ezxmltext_ezoe_tinymce8.tpl.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package ezoe
 */

class expOETinyMCE8Engine extends expOEEditorEngineBase
{
    const SCRIPT = 'javascript/tinymce8/tinymce.min.js';

    public function identifier()
    {
        return 'tinymce8';
    }

    public function label()
    {
        return 'TinyMCE 8';
    }

    public function isAvailable()
    {
        return is_file( dirname( __DIR__ ) . '/design/standard/' . self::SCRIPT );
    }

    public function template()
    {
        return 'design:content/datatype/edit/ezxmltext_ezoe_tinymce8.tpl';
    }

    public function config( array $context = array() )
    {
        return array(
            'license_key'        => 'gpl',
            'cache_key'          => eZOEXMLInput::getTinyMCE8CacheKey(),
            'upload_extensions'  => expOEEditor::uploadExtensions(),
            'upload_from_url'    => expOEUrlFetcher::enabled(),
        );
    }
}
