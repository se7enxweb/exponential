<?php
/**
 * File containing the expDataTableFieldSource interface.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Something that can hand the fields of a new data table to the module table wizard (doc/eng-us/features/6.0/
 * data-tables.md, "The module table wizard"): a content class, an existing table, an import source of 7x SQLI
 * Import, anything an extension declares. A provider is a kernel service tagged datatable.field_source:
 *
 *   [Service_datatable.field_source.myimport]
 *   Class=myImportFieldSource
 *   Scope=worker
 *   Tags[]=datatable.field_source
 *   Description=The fields of an import of my tool.
 *
 * The wizard asks it with the reference the user typed or chose (array( 'ref' => ... ), plus whatever the link to the
 * wizard carried) and turns the fields into columns.
 *
 * @since 6.0.15
 * @package kernel
 */
interface expDataTableFieldSource
{
    /**
     * A name for the wizard's choice of source ("A content class", "An import of 7x SQLI Import").
     *
     * @return string
     */
    public function dataTableSourceName();

    /**
     * The references the wizard offers in a list (ref => label); empty to let the user type one.
     *
     * @return array
     */
    public function dataTableReferences();

    /**
     * The fields.
     *
     * @param array $options ref, and the options of the link to the wizard
     * @return array list of array( name, label, type (string, text, integer, float, boolean, date, datetime, json,
     *               object, node, user), length, required, unique )
     * @throws RuntimeException naming what is wrong with the reference
     */
    public function dataTableFields( array $options );

    /**
     * A label of the source for the generated files' headers ("the content class Article").
     *
     * @param array $options
     * @return string
     */
    public function dataTableSourceLabel( array $options );
}
?>
