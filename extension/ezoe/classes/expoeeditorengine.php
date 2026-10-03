<?php
/**
 * An editor engine of the online editor: the thing that turns an ezxmltext attribute into an editor in the browser.
 *
 * Engines are registered in ezoe.ini [EditorSettings] Engines[<identifier>]=<class>; an extension adds one with a
 * class implementing this interface and one ini line, no ezoe code changes. expOEEditor resolves which engine an
 * editor gets (user preference, then siteaccess, then the global default) and falls back to tinymce3 when the
 * choice is unknown or not available. Guide: doc/bc/6.0/ezoe-tinymce8.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package ezoe
 */

interface expOEEditorEngine
{
    /** @return string the identifier the engine is registered under (a-z, 0-9, no spaces), e.g. 'tinymce8' */
    public function identifier();

    /** @return string the English name shown to editors, translated by the caller (context design/standard/ezoe) */
    public function label();

    /** @return bool whether the engine can run here (its files are present); false hides it and falls back */
    public function isAvailable();

    /**
     * @return string design URI of the template that renders the editor for one attribute (variables attribute,
     *                input_handler, attribute_base, editorRow), or '' for the editor built into ezxmltext_ezoe.tpl
     */
    public function template();

    /** @return array scripts and styles, as design paths: array( 'scripts' => array(...), 'styles' => array(...) ) */
    public function assets();

    /** @return array ezoe.ini [EditorLayout] button name => the engine's own toolbar item */
    public function toolbarMap();

    /** @return array plugin name => design path of its script, for engines that load plugins by URL */
    public function plugins();

    /**
     * The settings the engine needs for one content attribute, ready for json_encode().
     *
     * @param array $context attribute_id, contentobject_id, version, language
     * @return array
     */
    public function config( array $context = array() );
}
