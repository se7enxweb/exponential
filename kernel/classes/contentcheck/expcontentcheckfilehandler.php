<?php
/**
 * File containing the expContentCheckFileHandler interface.
 *
 * Says which storage files a file-backed field stands for (doc/eng-us/features/6.0/content-checks.md, "Missing files"). One
 * handler per datatype, named in contentcheck.ini [MissingFileSettings] Handlers[<datatype>]=<class>; the kernel has
 * expContentCheckFileImage (ezimage) and expContentCheckFileTable (ezbinaryfile, ezmedia and any datatype that keeps
 * its file in a table with the same columns). An extension adds its datatype with a class of its own.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

interface expContentCheckFileHandler
{
    /**
     * The files of a chunk of field versions, read with as few queries as possible.
     *
     * @param array $attributes list of rows of ezcontentobject_attribute: id, version, contentobject_id,
     *                          data_type_string, data_text, data_int, language_code
     * @return array "<attribute id>-<version>" => list of files, each array(
     *               'role' => 'original' | 'alias', 'alias' => alias name ('' for the original), 'path' => the
     *               expected path below the installation (var/<site>/storage/...), 'alias_key' => the stored key
     *               of an alias ('' when none), 'timestamp' => the stored time of an alias (0 when none),
     *               'name' => the file's original name or '' )
     *               A field version without a file (an empty field) has no entry or an empty list.
     */
    public function files( array $attributes );

    /**
     * Removes the field version's reference to its missing file: what "Remove the dead reference" does. The field
     * is empty afterwards. Changes the database only, never a file.
     *
     * @param eZContentObjectAttribute $attribute
     * @return bool whether the reference is gone
     */
    public function removeReference( eZContentObjectAttribute $attribute );

    /**
     * Whether the datatype has image aliases that can be generated from the original.
     *
     * @return bool
     */
    public function hasAliases();
}

?>
