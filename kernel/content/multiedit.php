<?php
/**
 * Editing the full attribute set of several objects in one form.
 *
 * The one thing that makes this possible without touching a single datatype:
 * every datatype edit template names its inputs after the content object
 * attribute id -
 *
 *     name="{$attribute_base}_ezstring_data_text_{$attribute.id}"
 *
 * - and that id is unique per object, per version, per language. So one form
 * can carry the attributes of any number of objects with no name collisions,
 * and fetchInput()/validateInput()/storeInput() are called per object exactly
 * as content/edit calls them for one. A datatype that works in the normal
 * editor works here, including datatypes that do not exist yet.
 *
 * What this view adds is the assembly around that: resolving a selection,
 * opening a draft per object, grouping by class so the form reads as something
 * rather than a wall, and - the part that actually needs care - reporting what
 * happened to each object when publishing several of them is not one atomic
 * act.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

// The code is in kernel/private/classes/views/content/multiedit.php (#207); this file is the entry point.
return \Exponential\View\Kernel\Content\Multiedit::main( __FILE__, get_defined_vars() );
