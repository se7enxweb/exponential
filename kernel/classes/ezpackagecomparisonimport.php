<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/**
 * Imports what a package brings for single items of its comparison with the site
 * (eZPackageComparison, package/compare): a new object is created where the package places it, a
 * changed object gets the package's values, a new class is created and a changed class brought up
 * to the package's definition. Nothing is ever removed: what only the site has - an object, a
 * translation, a class attribute, a location - stays.
 *
 * The work is done by the kernel's own package installation, one install item per compared item
 * (eZPackage::installItem() with eZContentObjectPackageHandler / eZContentClassPackageHandler), with
 * the conflict choice the install wizard offers for "already exists": update existing
 * (eZContentObject::PACKAGE_UPDATE, eZContentClassPackageHandler::ACTION_UPDATE). The item handed
 * to the installer is the package's own XML of that one object or class, reduced by plan():
 *  - values the user unticked are left out, so the object keeps the site's value (the installer
 *    starts the new version as a copy of the current one and sets only the values it is given);
 *  - a location the site already has (by node remote id) is left out, so a node is never
 *    duplicated or moved - the placement of an existing node is not changed by an import;
 *  - a top node of the package keeps only its node settings, as when the whole package is
 *    reinstalled.
 *
 * plan() tells, without writing anything, what an import of the given items would do, and why an
 * item cannot be imported; run() does it. A request imports at most MAX_ITEMS items: every item is
 * a publish of its own (versions, search index, caches), and the kernel has no background job
 * runner for package installs, so a larger selection is imported in several steps.
 */
class eZPackageComparisonImport
{
    /** Items imported in one request, at most. */
    const MAX_ITEMS = 25;

    /**
     * What importing the items $indices of $index would do. $excluded: array( <item index> =>
     * array( '<language>/<attribute identifier>' => true ) ), values not to import. Returns a list,
     * in the package's order, of array( 'index', 'kind', 'status', 'name', 'remote_id',
     * 'class_identifier', 'importable' (bool), 'reason' (why not), 'changes' (lines: what is set),
     * 'kept' (lines: what stays as the site has it) ).
     */
    static function plan( eZPackage $package, array $index, array $indices, array $excluded = array(), $addNeededClasses = true )
    {
        $plan = array();
        $indices = array_values( array_unique( array_map( 'intval', $indices ) ) );
        // A class an object's values need (see dependency()) is part of the plan, chosen or not; the
        // confirmation lists it with the objects that need it. $addNeededClasses false: exactly the
        // items given (the confirmed ones - a needed class the user left out stays out)
        $neededBy = array();
        foreach ( $indices as $i )
        {
            if ( !isset( $index['items'][$i] ) || $index['items'][$i]['kind'] !== 'object' )
                continue;
            $dependency = self::dependency( $index['items'][$i], $index );
            if ( $dependency && $dependency['class_index'] !== null && $dependency['addable'] )
                $neededBy[$dependency['class_index']][] = $index['items'][$i]['name'];
        }
        if ( $addNeededClasses )
        {
            foreach ( array_keys( $neededBy ) as $classIndex )
            {
                if ( !in_array( $classIndex, $indices, true ) )
                    $indices[] = $classIndex;
            }
        }
        // Classes come first in the index, so the package's order imports a class before the objects that need it
        sort( $indices );
        $chosen = array_flip( $indices );
        // Parents that the objects imported before in the same run create, and the classes it imports
        $comingNodes = array();
        $comingClasses = array();
        foreach ( $indices as $i )
        {
            if ( !isset( $index['items'][$i] ) )
                continue;
            $item = $index['items'][$i];
            $entry = array(
                'index' => $i, 'kind' => $item['kind'], 'status' => $item['status'], 'name' => $item['name'],
                'remote_id' => $item['remote_id'], 'class_identifier' => $item['class_identifier'],
                'importable' => false, 'reason' => '', 'changes' => array(), 'kept' => array(), 'values' => array(), 'excluded' => array(),
                'needed_by' => isset( $neededBy[$i] ) ? $neededBy[$i] : array(), 'needs_class' => null,
            );
            $offer = self::offerState( $item, $index );
            if ( !$offer['offered'] )
            {
                $entry['reason'] = $offer['reason'];
                $plan[] = $entry;
                continue;
            }
            $detail = eZPackageComparison::itemDetail( $package, $item );
            if ( !$detail )
            {
                $entry['reason'] = ezpI18n::tr( 'design/admin/package', 'The item is no longer in the package or on the site; compare again.' );
                $plan[] = $entry;
                continue;
            }
            if ( $item['kind'] === 'class' )
            {
                self::planClass( $entry, $detail, $item );
                if ( $entry['importable'] )
                    $comingClasses[$item['class_identifier']] = array( 'index' => $i, 'name' => $item['name'], 'addable' => $item['addable'] );
            }
            else
                self::planObject( $package, $entry, $item, $detail, isset( $excluded[$i] ) ? $excluded[$i] : array(), $comingNodes, $index, $comingClasses );
            $plan[] = $entry;
        }
        return $plan;
    }

    /** Why an item of this status is not imported at all, or '' (its values may still hold no importable one, see offerState()). */
    static function statusReason( array $item )
    {
        switch ( $item['status'] )
        {
            case 'identical':
                return ezpI18n::tr( 'design/admin/package', 'Identical: there is nothing to import.' );
            case 'removed':
                return ezpI18n::tr( 'design/admin/package', 'Only on the site: an import never removes anything.' );
        }
        return '';
    }

    /**
     * What an object item's values need that the site's class does not have: null when nothing,
     * else array( 'class_index' (the package's class item that adds them, or null), 'class_name',
     * 'addable' (attribute identifiers that importing that class adds), 'blocked' (those it cannot:
     * not in the package's class, or of a datatype the site does not have) ).
     * An object whose class the site lacks altogether needs its whole class the same way.
     */
    static function dependency( array $item, array $index )
    {
        if ( $item['kind'] !== 'object' )
            return null;
        $missing = isset( $item['missing_attributes'] ) ? (array)$item['missing_attributes'] : array();
        $wholeClass = $item['status'] === 'class_missing';
        if ( !$missing && !$wholeClass )
            return null;
        $classes = self::classItems( $index );
        $class = isset( $classes[$item['class_identifier']] ) ? $classes[$item['class_identifier']] : null;
        $importable = $class && in_array( $class['status'], array( 'new', 'changed' ), true );
        $addable = $importable && isset( $class['addable'] ) ? (array)$class['addable'] : array();
        $out = array( 'class_index' => $importable ? (int)$class['index'] : null,
                      'class_name' => $class ? $class['name'] : $item['class_identifier'],
                      'addable' => array(), 'blocked' => array(), 'whole_class' => $wholeClass );
        if ( $wholeClass )
        {
            // A new class brings every attribute it can create
            $out['addable'] = $importable && $class['status'] === 'new' ? array( '*' ) : array();
            return $out;
        }
        foreach ( $missing as $identifier )
        {
            if ( in_array( $identifier, $addable, true ) )
                $out['addable'][] = $identifier;
            else
                $out['blocked'][] = $identifier;
        }
        return $out;
    }

    /** The class items of an index by identifier, worked out once per index (the classes come first in it). */
    protected static function classItems( array $index )
    {
        static $cache = array();
        $key = ( isset( $index['stamp'] ) ? $index['stamp'] : '' ) . ':' . ( isset( $index['built'] ) ? $index['built'] : '' ) . ':' . count( $index['items'] );
        if ( !isset( $cache[$key] ) )
        {
            $cache = array( $key => array() );
            foreach ( $index['items'] as $candidate )
            {
                if ( $candidate['kind'] !== 'class' )
                    break;
                $cache[$key][$candidate['class_identifier']] = $candidate;
            }
        }
        return $cache[$key];
    }

    /**
     * Whether an item is offered for import at all, and if not why: array( 'offered', 'reason',
     * 'needs_class' (the class item it needs imported first, or null), 'class_name', 'blocked' ).
     * An object is offered when it has a value an import can set - its own, or one for an attribute
     * the package's class adds (then the class is imported with it); never when every difference
     * is one an import cannot bring. plan() gives such an item a value to import every time.
     */
    static function offerState( array $item, array $index )
    {
        $state = array( 'offered' => false, 'reason' => self::statusReason( $item ), 'needs_class' => null, 'class_name' => '', 'blocked' => array() );
        if ( $state['reason'] !== '' )
            return $state;
        if ( $item['kind'] === 'class' )
        {
            $state['offered'] = true;
            return $state;
        }
        $dependency = self::dependency( $item, $index );
        if ( $dependency )
        {
            $state['class_name'] = $dependency['class_name'];
            $state['blocked'] = $dependency['blocked'];
            if ( $dependency['addable'] )
                $state['needs_class'] = $dependency['class_index'];
        }
        if ( $item['status'] === 'class_missing' )
        {
            $state['offered'] = $state['needs_class'] !== null;
            if ( !$state['offered'] )
                $state['reason'] = ezpI18n::tr( 'design/admin/package', 'The site has no class "%class" and the package does not bring it.', null, array( '%class' => $item['class_identifier'] ) );
            return $state;
        }
        if ( $item['status'] === 'new' )
        {
            $state['offered'] = true;
            return $state;
        }
        // A changed object: its own differences, those the needed class lets in, or nothing
        $own = (int)$item['fields'] - ( isset( $item['missing_fields'] ) ? (int)$item['missing_fields'] : 0 ) + (int)$item['lang_package'];
        if ( $own > 0 || $state['needs_class'] !== null )
        {
            $state['offered'] = true;
            return $state;
        }
        if ( $state['blocked'] )
            $state['reason'] = ezpI18n::tr( 'design/admin/package', 'Its differing values are for attributes the site\'s class "%class" lacks and an import of the package\'s class cannot add: %attributes.', null,
                                            array( '%class' => $state['class_name'], '%attributes' => implode( ', ', $state['blocked'] ) ) );
        else
            $state['reason'] = ezpI18n::tr( 'design/admin/package', 'Only its placement or a translation only on the site differ, and an import changes neither.' );
        return $state;
    }

    /** Whether an item of the index is offered for import (see offerState()). */
    static function isOffered( array $item, array $index )
    {
        $state = self::offerState( $item, $index );
        return $state['offered'];
    }

    protected static function planClass( array &$entry, array $detail, array $item )
    {
        $entry['importable'] = true;
        if ( $entry['needed_by'] )
            $entry['changes'][] = ezpI18n::tr( 'design/admin/package', 'Needed by %objects: their values are for attributes this import adds.', null,
                                               array( '%objects' => implode( '; ', array_map( function ( $name ) { return mb_strlen( $name ) > 50 ? mb_substr( $name, 0, 50 ) . '…' : $name; }, $entry['needed_by'] ) ) ) );
        $unaddable = isset( $item['unaddable'] ) ? (array)$item['unaddable'] : array();
        if ( $unaddable )
            $entry['kept'][] = ezpI18n::tr( 'design/admin/package', 'Not added, the site has no datatype for them: %attributes', null, array( '%attributes' => implode( ', ', $unaddable ) ) );
        // Sections of a class detail: 0 the class's settings, 1 its attributes
        if ( $detail['status'] === 'new' )
        {
            $count = isset( $detail['sections'][1] ) ? count( $detail['sections'][1]['rows'] ) - count( $unaddable ) : 0;
            $entry['changes'][] = ezpI18n::tr( 'design/admin/package', 'The class is created, with %count attribute(s).', null, array( '%count' => $count ) );
            return;
        }
        foreach ( $detail['sections'] as $sectionIndex => $section )
        {
            foreach ( $section['rows'] as $row )
            {
                $label = $row['name'] . ' (' . $row['identifier'] . ')';
                if ( $sectionIndex === 0 )
                {
                    if ( $row['state'] === 'changed' )
                        $entry['changes'][] = ezpI18n::tr( 'design/admin/package', 'Class setting: %name', null, array( '%name' => $row['name'] ) );
                    continue;
                }
                switch ( $row['state'] )
                {
                    case 'package_only':
                        if ( !in_array( $row['identifier'], $unaddable, true ) )
                            $entry['changes'][] = ezpI18n::tr( 'design/admin/package', 'Attribute added: %attribute', null, array( '%attribute' => $label ) );
                        break;
                    case 'changed':
                        if ( in_array( 'datatype', $row['aspects'], true ) )
                            $entry['kept'][] = ezpI18n::tr( 'design/admin/package', 'Not changed, its datatype differs: %attribute', null, array( '%attribute' => $label ) );
                        else
                            $entry['changes'][] = ezpI18n::tr( 'design/admin/package', 'Attribute updated: %attribute', null, array( '%attribute' => $label ) );
                        break;
                    case 'site_only':
                        $entry['kept'][] = ezpI18n::tr( 'design/admin/package', 'Kept, only on the site: %attribute', null, array( '%attribute' => $label ) );
                        break;
                }
            }
        }
        $entry['kept'][] = ezpI18n::tr( 'design/admin/package', 'Names in languages only the site has are kept.' );
    }

    protected static function planObject( eZPackage $package, array &$entry, array $item, array $detail, array $excluded, array &$comingNodes, array $index = array(), array $comingClasses = array() )
    {
        $packageData = self::packageObjectData( $package, $item );
        if ( !$packageData )
        {
            $entry['reason'] = ezpI18n::tr( 'design/admin/package', 'The object is no longer in the package; compare again.' );
            return;
        }
        // The class the object's values need: coming in this run (imported first), or not
        $dependency = $index ? self::dependency( $item, $index ) : null;
        $coming = $dependency && isset( $comingClasses[$item['class_identifier']] ) ? $comingClasses[$item['class_identifier']] : null;
        if ( $dependency && $coming )
        {
            $entry['needs_class'] = $coming['index'];
            $entry['changes'][] = ezpI18n::tr( 'design/admin/package', 'Needs the class %class to be imported first; it is imported before this object, in the same step.', null, array( '%class' => $coming['name'] ) );
        }
        if ( $item['status'] === 'class_missing' && !$coming )
        {
            $entry['reason'] = ezpI18n::tr( 'design/admin/package', 'Needs the class %class to be imported first, and it is not part of this import.', null, array( '%class' => $dependency ? $dependency['class_name'] : $item['class_identifier'] ) );
            return;
        }
        if ( $item['status'] === 'new' || $item['status'] === 'class_missing' )
        {
            $main = null;
            foreach ( $packageData['placement'] as $place )
            {
                if ( $place['main'] )
                    $main = $place;
            }
            if ( !$main && $packageData['placement'] )
                $main = $packageData['placement'][0];
            if ( !$main )
            {
                $entry['reason'] = ezpI18n::tr( 'design/admin/package', 'The package gives the object no location.' );
                return;
            }
            if ( $main['top'] )
            {
                $entry['reason'] = ezpI18n::tr( 'design/admin/package', 'A top node of the package is placed by the install wizard, which asks where to put it.' );
                return;
            }
            if ( self::nodeByRemoteID( $main['node_remote_id'] ) )
            {
                $entry['reason'] = ezpI18n::tr( 'design/admin/package', 'Its node remote ID %remote is already used on the site by another object.', null, array( '%remote' => $main['node_remote_id'] ) );
                return;
            }
            $parent = self::nodeByRemoteID( $main['parent_remote_id'] );
            if ( !$parent && !isset( $comingNodes[$main['parent_remote_id']] ) )
            {
                $entry['reason'] = ezpI18n::tr( 'design/admin/package', 'Its parent node %remote is neither on the site nor imported before it; import the parent first.', null, array( '%remote' => $main['parent_remote_id'] ) );
                return;
            }
            $entry['importable'] = true;
            $comingNodes[$main['node_remote_id']] = true;
            $entry['changes'][] = $parent
                ? ezpI18n::tr( 'design/admin/package', 'The object is created under "%parent".', null, array( '%parent' => $parent['path_identification_string'] !== '' ? '/' . $parent['path_identification_string'] : $parent['name'] ) )
                : ezpI18n::tr( 'design/admin/package', 'The object is created under the object imported before it (node %remote).', null, array( '%remote' => $main['parent_remote_id'] ) );
            foreach ( $detail['sections'] as $section )
                $entry['changes'][] = ezpI18n::tr( 'design/admin/package', 'Translation %language with %count value(s)', null, array( '%language' => $section['language'], '%count' => count( $section['rows'] ) ) );
            return;
        }

        // A changed object. First what would make an import unsafe: then it is refused, not attempted
        $siteState = self::languageState( $item['remote_id'] );
        $siteObject = eZContentObject::fetchByRemoteID( $item['remote_id'] );
        if ( !$siteState || !$siteObject instanceof eZContentObject )
        {
            $entry['reason'] = ezpI18n::tr( 'design/admin/package', 'The object is no longer on the site; compare again.' );
            return;
        }
        if ( (int)$siteObject->attribute( 'status' ) !== eZContentObject::STATUS_PUBLISHED )
        {
            $entry['reason'] = ezpI18n::tr( 'design/admin/package', 'The object is not published on the site (it is in the trash or a draft); restore it first.' );
            return;
        }
        if ( $item['class_changed'] || $packageData['class_identifier'] !== $siteObject->attribute( 'class_identifier' ) )
        {
            $entry['reason'] = ezpI18n::tr( 'design/admin/package', 'The site has this object as a %site object, the package as a %package object; an import would change its class.', null,
                                            array( '%site' => $siteObject->attribute( 'class_identifier' ), '%package' => $packageData['class_identifier'] ) );
            return;
        }
        if ( !$siteState['languages'] )
        {
            $entry['reason'] = ezpI18n::tr( 'design/admin/package', 'The object has no values on the site to build a new version from.' );
            return;
        }

        $entry['importable'] = true;
        $any = false;
        $excludable = self::untickable( $package, $item );
        $missing = isset( $item['missing_attributes'] ) ? (array)$item['missing_attributes'] : array();
        $classLabel = $dependency ? $dependency['class_name'] : $item['class_identifier'];
        foreach ( $detail['sections'] as $section )
        {
            if ( $section['state'] === 'site' )
            {
                $entry['kept'][] = ezpI18n::tr( 'design/admin/package', 'Translation %language, only on the site, is kept.', null, array( '%language' => $section['language'] ) );
                continue;
            }
            if ( $section['state'] === 'package' && !eZContentLanguage::fetchByLocale( $section['language'] ) )
                $entry['changes'][] = ezpI18n::tr( 'design/admin/package', 'The language %language is added to the site\'s languages.', null, array( '%language' => $section['language'] ) );
            foreach ( $section['rows'] as $row )
            {
                $key = $section['language'] . '/' . $row['identifier'];
                $label = $section['language'] . ' / ' . $row['name'] . ' (' . $row['identifier'] . ')';
                if ( $row['state'] === 'site_only' )
                {
                    $entry['kept'][] = ezpI18n::tr( 'design/admin/package', 'Kept, only on the site: %attribute', null, array( '%attribute' => $label ) );
                    continue;
                }
                if ( $row['state'] === 'identical' && $section['state'] === 'both' )
                    continue;
                // A value for an attribute the site's class lacks: the installer sets only the
                // class's attributes, so it comes in only with the class import before it
                $needsClass = in_array( $row['identifier'], $missing, true );
                if ( $needsClass && !( $coming && in_array( $row['identifier'], $coming['addable'], true ) ) )
                {
                    $entry['kept'][] = !$coming && ( $dependency && $dependency['class_index'] !== null && in_array( $row['identifier'], $dependency['addable'], true ) )
                        ? ezpI18n::tr( 'design/admin/package', 'Not imported, it needs the class %class, which is left out of this import: %attribute', null, array( '%class' => $classLabel, '%attribute' => $label ) )
                        : ezpI18n::tr( 'design/admin/package', 'Not imported, the class %class on the site lacks the attribute and the package\'s class cannot add it: %attribute', null, array( '%class' => $classLabel, '%attribute' => $label ) );
                    continue;
                }
                // Each value the import sets is one tick of the confirmation; one that can only go
                // with the item (see untickable()) is shown ticked and fixed
                // Only a value that can be kept on its own is ever left out, whatever a request asks
                $ticked = !isset( $excluded[$key] ) || empty( $excludable[$key] );
                if ( !$ticked )
                    $entry['excluded'][$key] = true;
                $entry['values'][] = array(
                    'key' => $key, 'label' => $label, 'language' => $section['language'], 'name' => $row['name'], 'identifier' => $row['identifier'],
                    'new_translation' => $section['state'] === 'package', 'ticked' => $ticked, 'untickable' => !empty( $excludable[$key] ),
                    // Comes with the class import listed before it; unticked only by leaving that class out
                    'needs_class' => $needsClass ? $coming['index'] : null,
                );
                // Listed in 'values' only (the confirmation shows them as ticks, the result as set or kept)
                if ( $ticked )
                    $any = true;
            }
        }

        // The object's own settings stay the site's, and a language list out of step with the values is repaired
        if ( self::languageMaskMismatch( $siteState ) )
        {
            $unlisted = array();
            foreach ( $siteState['languages'] as $language )
            {
                $bit = (int)eZContentLanguage::idByLocale( $language );
                if ( ( $siteState['mask'] & $bit ) !== $bit || ( $siteState['version_mask'] & $bit ) !== $bit )
                    $unlisted[] = $language;
            }
            $entry['kept'][] = ezpI18n::tr( 'design/admin/package', 'The object\'s language list lacks %languages, although it has values in it; the import corrects the list, so those values are kept.', null,
                                            array( '%languages' => implode( ', ', $unlisted ) ) );
        }
        $source = self::packageObjectElement( $package, $item );
        $packageAlwaysAvailable = $source && $source->getAttributeNS( eZPackageComparison::NS_REMOTE, 'always_available' ) === '1';
        if ( $packageAlwaysAvailable !== $siteState['always_available'] )
            $entry['kept'][] = $siteState['always_available']
                ? ezpI18n::tr( 'design/admin/package', 'The object stays always available, as on the site (the package has it otherwise).' )
                : ezpI18n::tr( 'design/admin/package', 'The object stays not always available, as on the site (the package has it otherwise).' );
        $siteMainLanguage = eZContentLanguage::fetch( $siteState['initial_language_id'] );
        $siteMain = $siteMainLanguage ? $siteMainLanguage->attribute( 'locale' ) : '';
        $packageMain = $source ? (string)$source->getAttribute( 'initial_language' ) : '';
        if ( $packageMain !== '' && $siteMain !== '' && $packageMain !== $siteMain )
            $entry['kept'][] = ezpI18n::tr( 'design/admin/package', 'The main language stays %site, as on the site (the package\'s is %package).', null,
                                            array( '%site' => $siteMain, '%package' => $packageMain ) );
        if ( $detail['placement'] && $detail['placement']['changed'] )
            $entry['kept'][] = ezpI18n::tr( 'design/admin/package', 'Placement: existing locations are not moved; a location the package has and the site has not is added.' );
        if ( !$any )
        {
            $entry['importable'] = false;
            if ( $missing && !$coming )
                $entry['reason'] = ezpI18n::tr( 'design/admin/package', 'Needs the class %class to be imported first, and it is not part of this import.', null, array( '%class' => $classLabel ) );
            else
                $entry['reason'] = ezpI18n::tr( 'design/admin/package', 'Every value is unticked; nothing is left to import.' );
        }
    }

    /**
     * Imports the importable items of plan( $package, $index, $indices, $excluded ), at most
     * MAX_ITEMS, one install item each, each in its own transaction. Returns the plan with
     * 'result' ('done', 'failed' or 'skipped') and 'message' per item, and refreshes the comparison
     * cache: the imported objects are compared again; after a class import the whole comparison is
     * built again on the next page (a class change touches every object of the class).
     */
    static function run( eZPackage $package, array $index, array $indices, array $excluded = array(), $addNeededClasses = true )
    {
        $plan = self::plan( $package, $index, $indices, $excluded, $addNeededClasses );
        // Classes that did not import in this run: an object that needs one is not imported
        $failedClasses = array();
        $done = 0;
        $touched = array();
        $classImported = false;
        foreach ( $plan as &$entry )
        {
            if ( !$entry['importable'] )
            {
                $entry['result'] = 'skipped';
                $entry['message'] = $entry['reason'];
                if ( $entry['kind'] === 'class' )
                    $failedClasses[$entry['index']] = $entry['reason'];
                continue;
            }
            if ( $entry['needs_class'] !== null && isset( $failedClasses[$entry['needs_class']] ) )
            {
                $entry['result'] = 'skipped';
                $entry['message'] = ezpI18n::tr( 'design/admin/package', 'Not imported: the class it needs was not imported (%reason).', null, array( '%reason' => $failedClasses[$entry['needs_class']] ) );
                continue;
            }
            if ( $done >= self::MAX_ITEMS )
            {
                $entry['result'] = 'skipped';
                $entry['message'] = ezpI18n::tr( 'design/admin/package', 'Not imported in this step: at most %count items are imported at a time.', null, array( '%count' => self::MAX_ITEMS ) );
                if ( $entry['kind'] === 'class' )
                    $failedClasses[$entry['index']] = $entry['message'];
                continue;
            }
            $item = $index['items'][$entry['index']];
            $error = '';
            $ok = $item['kind'] === 'class'
                ? self::importClass( $package, $item, $error, $entry )
                : self::importObject( $package, $item, $entry['excluded'], $error );
            $entry['result'] = $ok ? 'done' : 'failed';
            $entry['message'] = $ok ? ezpI18n::tr( 'design/admin/package', 'Imported.' ) : $error;
            if ( !$ok && $item['kind'] === 'class' )
                $failedClasses[$entry['index']] = $error;
            if ( $ok )
            {
                ++$done;
                $touched[] = $entry['index'];
                if ( $item['kind'] === 'class' )
                    $classImported = true;
            }
        }
        unset( $entry );

        eZContentObject::clearCache();
        if ( $classImported )
            eZPackageComparison::forget( $package->attribute( 'name' ) );
        elseif ( $touched )
            eZPackageComparison::refreshItems( $package, $touched );
        return $plan;
    }

    /** The package's data of an object item (eZPackageComparison::packageObjectData()), or null. */
    protected static function packageObjectData( eZPackage $package, array $item )
    {
        $node = self::packageObjectElement( $package, $item );
        if ( !$node )
            return null;
        $sources = eZPackageComparison::packageSources( $package );
        return eZPackageComparison::packageObjectData( $package, $node, $sources['top_nodes'] );
    }

    protected static function packageObjectElement( eZPackage $package, array $item )
    {
        if ( $item['file'] === null )
            return null;
        $documents = array();
        $node = eZPackageComparison::packageObjectNode( $package, array( 'file' => $item['file'], 'position' => $item['position'] ), $documents );
        return $node && $node->getAttribute( 'remote_id' ) === $item['remote_id'] ? $node : null;
    }

    /**
     * The site's current values of an object, serialized by each datatype as a package carries
     * them: array( language => array( identifier => DOMElement ) ), or null for a value that is a
     * file (its serializer hands a file to the package, which an import cannot bring back).
     */
    static function siteAttributeNodes( $remoteID )
    {
        $out = array();
        $object = eZContentObject::fetchByRemoteID( $remoteID );
        if ( !$object instanceof eZContentObject )
            return $out;
        $version = $object->currentVersion();
        if ( !$version )
            return $out;
        foreach ( $version->translationList( false, false ) as $language )
        {
            foreach ( $version->contentObjectAttributes( $language ) as $attribute )
            {
                $identifier = $attribute->contentClassAttributeIdentifier();
                $datatype = $attribute->dataType();
                $node = null;
                if ( $datatype && !in_array( $attribute->attribute( 'data_type_string' ), array( 'ezimage', 'ezbinaryfile', 'ezmedia' ), true ) )
                {
                    $collector = new eZPackageComparisonFileCollector();
                    try
                    {
                        $node = $datatype->serializeContentObjectAttribute( $collector, $attribute );
                    }
                    catch ( Exception $e )
                    {
                        $node = null;
                    }
                    if ( $collector->Files )
                        $node = null;
                }
                $out[$language][$identifier] = $node instanceof DOMElement ? $node : null;
            }
        }
        return $out;
    }

    /**
     * Which differing values of an object item the user may untick ('<language>/<identifier>' =>
     * bool). Every one whose site value can be written back (see siteAttributeNodes()); a file
     * value only in the language the installer starts its new version from (the package object's
     * initial language, when the site object has it), where leaving it out keeps the site's copy.
     */
    static function untickable( eZPackage $package, array $item )
    {
        $out = array();
        $source = self::packageObjectElement( $package, $item );
        if ( !$source || !eZContentObject::fetchByRemoteID( $item['remote_id'] ) )
            return $out;
        $initialLanguage = $source->getAttribute( 'initial_language' );
        foreach ( self::siteAttributeNodes( $item['remote_id'] ) as $language => $nodes )
        {
            foreach ( $nodes as $identifier => $node )
                $out[$language . '/' . $identifier] = $node instanceof DOMElement || $language === $initialLanguage;
        }
        return $out;
    }

    /** node_id, name and path of the site's node with this remote id, or null. */
    protected static function nodeByRemoteID( $remoteID )
    {
        if ( (string)$remoteID === '' )
            return null;
        $db = eZDB::instance();
        $rows = $db->arrayQuery( "SELECT node_id, path_identification_string, contentobject_id FROM ezcontentobject_tree WHERE remote_id = '" . $db->escapeString( (string)$remoteID ) . "'" );
        if ( !$rows )
            return null;
        $row = $rows[0];
        $row['name'] = '';
        $objects = $db->arrayQuery( 'SELECT name FROM ezcontentobject WHERE id = ' . (int)$row['contentobject_id'] );
        if ( $objects )
            $row['name'] = $objects[0]['name'];
        return $row;
    }

    /**
     * One object through the kernel's installer: the package's XML of this object alone, without
     * the values in $excluded ('<language>/<identifier>' => true) and without the locations the site
     * already has, as the one object of an install item; "already exists" answered with update.
     */
    static function importObject( eZPackage $package, array $item, array $excluded, &$error )
    {
        $source = self::packageObjectElement( $package, $item );
        if ( !$source )
        {
            $error = ezpI18n::tr( 'design/admin/package', 'The object is no longer in the package.' );
            return false;
        }
        $sources = eZPackageComparison::packageSources( $package );

        $dom = new DOMDocument( '1.0', 'utf-8' );
        $root = $dom->createElement( 'content-object' );
        $dom->appendChild( $root );
        $objectList = $dom->createElement( 'object-list' );
        $root->appendChild( $objectList );
        $root->appendChild( $dom->createElement( 'top-node-list' ) );
        $object = $dom->importNode( $source, true );
        $objectList->appendChild( $object );

        $exists = eZContentObject::fetchByRemoteID( $item['remote_id'] ) instanceof eZContentObject;
        // An unticked value keeps the site's: the package's value is replaced by the site's own,
        // serialized by its datatype. A value that is a file (the serializer hands over a file)
        // cannot travel that way; it is left out instead, and the installer's new version keeps the
        // copy of the current one - see siteAttributeNodes() and untickable().
        $siteNodes = $excluded && $exists ? self::siteAttributeNodes( $item['remote_id'] ) : array();
        foreach ( $object->getElementsByTagNameNS( eZPackageComparison::NS_OBJECT, 'object-translation' ) as $translation )
        {
            $language = $translation->getAttribute( 'language' );
            $replace = array();
            foreach ( $translation->getElementsByTagNameNS( eZPackageComparison::NS_OBJECT, 'attribute' ) as $attribute )
            {
                $identifier = $attribute->getAttributeNS( eZPackageComparison::NS_REMOTE, 'identifier' );
                if ( isset( $excluded[$language . '/' . $identifier] ) )
                    $replace[] = array( $attribute, isset( $siteNodes[$language][$identifier] ) ? $siteNodes[$language][$identifier] : null );
            }
            foreach ( $replace as $pair )
            {
                list( $attribute, $siteNode ) = $pair;
                if ( $siteNode instanceof DOMElement )
                    $attribute->parentNode->replaceChild( $dom->importNode( $siteNode, true ), $attribute );
                else
                    $attribute->parentNode->removeChild( $attribute );
            }
        }
        $assignments = array();
        foreach ( $object->getElementsByTagName( 'node-assignment' ) as $assignment )
            $assignments[] = $assignment;
        $mainParentFound = !$exists ? false : true;
        foreach ( $assignments as $assignment )
        {
            $nodeRemoteID = $assignment->getAttribute( 'remote-id' );
            if ( isset( $sources['top_nodes'][$nodeRemoteID] ) )
            {
                // A top node: its node settings only, as a reinstall of the whole package does
                $assignment->removeAttribute( 'parent-node-remote-id' );
                continue;
            }
            if ( self::nodeByRemoteID( $nodeRemoteID ) || !self::nodeByRemoteID( $assignment->getAttribute( 'parent-node-remote-id' ) ) )
            {
                // A location the site has already (never duplicated nor moved), or one whose parent
                // the site does not have (it could not be placed)
                $assignment->parentNode->removeChild( $assignment );
                continue;
            }
            if ( $assignment->getAttribute( 'is-main-node' ) )
                $mainParentFound = true;
        }
        if ( !$mainParentFound )
        {
            $error = ezpI18n::tr( 'design/admin/package', 'The object could not be placed: its parent node is not on the site.' );
            return false;
        }

        $parameters = array(
            'error' => array(),
            'error_default_actions' => array( 'ezcontentobject' => array( eZContentObject::PACKAGE_ERROR_EXISTS => eZContentObject::PACKAGE_UPDATE ) ),
            'language_map' => $package->defaultLanguageMap(),
            'site_access_map' => array(),
            'top_nodes_map' => array(),
            'user_id' => eZUser::currentUserID(),
        );
        $installItem = array( 'type' => 'ezcontentobject', 'name' => 'package-compare', 'os' => false,
                              'filename' => 'package-compare', 'sub-directory' => false, 'content' => $root );

        // An existing object keeps everything only the site has: see languageState(),
        // repairLanguageMask() and restoreLanguageState(). All of it, and the installer's own work, is
        // one transaction; if the installer fails, it rolls everything back itself.
        $db = eZDB::instance();
        $db->begin();
        $before = $exists ? self::languageState( $item['remote_id'] ) : null;
        if ( $before )
            self::repairLanguageMask( $before );
        $ok = $package->installItem( $installItem, $parameters );
        if ( !$ok )
        {
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            $error = !empty( $parameters['error']['description'] ) ? $parameters['error']['description'] : ezpI18n::tr( 'design/admin/package', 'The installer could not import the object.' );
            eZContentObject::clearCache();
            return false;
        }
        eZContentObject::clearCache();
        if ( $before )
        {
            $packageLanguages = array();
            foreach ( $object->getElementsByTagNameNS( eZPackageComparison::NS_OBJECT, 'object-translation' ) as $translation )
                $packageLanguages[] = $translation->getAttribute( 'language' );
            if ( !self::restoreLanguageState( $before, $packageLanguages, $error ) )
            {
                $db->rollback();
                eZContentObject::clearCache();
                return false;
            }
        }
        $db->commit();
        eZContentObject::clearCache();
        return true;
    }

    /**
     * What of an existing object's languages an import must keep: its current version, the
     * languages that version has values in (read from the values themselves, not from the language
     * masks, which may say otherwise), its main language, its always-available setting, and each
     * language's name. Read straight from the database.
     */
    static function languageState( $remoteID )
    {
        $db = eZDB::instance();
        $rows = $db->arrayQuery( "SELECT id, current_version, language_mask, initial_language_id FROM ezcontentobject WHERE remote_id = '" . $db->escapeString( (string)$remoteID ) . "'" );
        if ( !$rows )
            return null;
        $objectID = (int)$rows[0]['id'];
        $versionNumber = (int)$rows[0]['current_version'];
        $languages = array();
        foreach ( (array)$db->arrayQuery( "SELECT DISTINCT language_code FROM ezcontentobject_attribute WHERE contentobject_id = $objectID AND version = $versionNumber" ) as $row )
            $languages[] = (string)$row['language_code'];
        sort( $languages );
        $names = array();
        foreach ( (array)$db->arrayQuery( "SELECT content_translation, name FROM ezcontentobject_name WHERE contentobject_id = $objectID AND content_version = $versionNumber" ) as $row )
            $names[(string)$row['content_translation']] = (string)$row['name'];
        $versionRows = $db->arrayQuery( "SELECT language_mask FROM ezcontentobject_version WHERE contentobject_id = $objectID AND version = $versionNumber" );
        return array(
            'object_id' => $objectID,
            'version' => $versionNumber,
            'languages' => $languages,
            'names' => $names,
            'mask' => (int)$rows[0]['language_mask'],
            'version_mask' => $versionRows ? (int)$versionRows[0]['language_mask'] : 0,
            'initial_language_id' => (int)$rows[0]['initial_language_id'],
            'always_available' => ( (int)$rows[0]['language_mask'] & 1 ) === 1,
        );
    }

    /** The mask bits of these languages (0 for a language the site does not know). */
    protected static function languageBits( array $languages )
    {
        $bits = 0;
        foreach ( $languages as $language )
            $bits |= (int)eZContentLanguage::idByLocale( $language );
        return $bits & ~1;
    }

    /**
     * Makes an object's language masks list every language its current version has values in.
     * Why: the installer starts its new version from the current one in a single language and
     * relies on the publish operation (eZContentOperationCollection::copyTranslations()) to copy
     * every other translation across - and that copies only the translations the object's language
     * mask lists. A translation whose values are there but whose language the mask does not list
     * (a mask out of step with the values) would silently be left out of the new version. The masks
     * describe the values; after the repair they do again, and the translation is carried over like
     * any other. Returns whether anything was repaired.
     */
    static function repairLanguageMask( array $state )
    {
        $bits = self::languageBits( $state['languages'] );
        if ( ( $state['mask'] & $bits ) === $bits && ( $state['version_mask'] & $bits ) === $bits )
            return false;
        $db = eZDB::instance();
        $db->query( 'UPDATE ezcontentobject SET language_mask = ' . ( $state['mask'] | $bits ) . ' WHERE id = ' . (int)$state['object_id'] );
        $db->query( 'UPDATE ezcontentobject_version SET language_mask = ' . ( $state['version_mask'] | $bits ) .
                    ' WHERE contentobject_id = ' . (int)$state['object_id'] . ' AND version = ' . (int)$state['version'] );
        eZContentObject::clearCache( $state['object_id'] );
        return true;
    }

    /** Whether an existing object's language masks are out of step with the languages it has values in (see repairLanguageMask()). */
    static function languageMaskMismatch( array $state )
    {
        $bits = self::languageBits( $state['languages'] );
        return ( $state['mask'] & $bits ) !== $bits || ( $state['version_mask'] & $bits ) !== $bits;
    }

    /**
     * After the installer: the object has every translation it had before (a translation only the
     * site has, with exactly its values - copied back from the version before the import if the
     * installer left one out), its masks list exactly the languages it has values in, and its main
     * language and always-available setting are the site's again (the installer takes both from the
     * package; an import sets values and adds translations, it does not change the object's
     * settings). Returns false, with $error, if the object is not in that state afterwards.
     */
    static function restoreLanguageState( array $before, array $packageLanguages, &$error )
    {
        $object = eZContentObject::fetch( $before['object_id'] );
        if ( !$object instanceof eZContentObject )
        {
            $error = ezpI18n::tr( 'design/admin/package', 'The object is gone after the import.' );
            return false;
        }
        $after = self::languageState( $object->attribute( 'remote_id' ) );
        $newVersionNumber = $after['version'];
        $newVersion = $object->version( $newVersionNumber );
        $oldVersion = $object->version( $before['version'] );

        $missing = array_diff( $before['languages'], $after['languages'] );
        foreach ( $missing as $language )
        {
            if ( !$oldVersion )
                break;
            foreach ( $oldVersion->contentObjectAttributes( $language ) as $attribute )
            {
                $clone = $attribute->cloneContentObjectAttribute( $newVersionNumber, $before['version'], $before['object_id'] );
                $clone->sync();
            }
            if ( isset( $before['names'][$language] ) && $before['names'][$language] !== '' )
                $object->setName( $before['names'][$language], $newVersionNumber, $language );
        }

        // The masks: exactly the languages with values, and the site's always-available bit
        $after = self::languageState( $object->attribute( 'remote_id' ) );
        $bits = self::languageBits( $after['languages'] );
        $db = eZDB::instance();
        $db->query( 'UPDATE ezcontentobject SET language_mask = ' . ( $bits | ( $after['mask'] & 1 ) ) . ', initial_language_id = ' . (int)$before['initial_language_id'] .
                    ' WHERE id = ' . (int)$before['object_id'] );
        $db->query( 'UPDATE ezcontentobject_version SET language_mask = ' . ( $bits | ( $after['version_mask'] & 1 ) ) .
                    ' WHERE contentobject_id = ' . (int)$before['object_id'] . ' AND version = ' . (int)$newVersionNumber );
        eZContentObject::clearCache( $before['object_id'] );
        $object = eZContentObject::fetch( $before['object_id'] );
        if ( ( ( $object->attribute( 'language_mask' ) & 1 ) === 1 ) !== $before['always_available'] )
            $object->setAlwaysAvailableLanguageID( $before['always_available'] ? $before['initial_language_id'] : false );

        if ( $missing )
        {
            eZContentCacheManager::clearContentCacheIfNeeded( $before['object_id'] );
            eZSearch::addObject( eZContentObject::fetch( $before['object_id'] ), true );
        }

        $final = self::languageState( $object->attribute( 'remote_id' ) );
        // Every value once: a version with two values for one attribute and language is broken
        $duplicates = $db->arrayQuery( 'SELECT language_code, contentclassattribute_id, COUNT( * ) AS c FROM ezcontentobject_attribute
                                        WHERE contentobject_id = ' . (int)$before['object_id'] . ' AND version = ' . (int)$final['version'] . '
                                        GROUP BY language_code, contentclassattribute_id HAVING COUNT( * ) > 1' );
        if ( $duplicates )
        {
            $error = ezpI18n::tr( 'design/admin/package', 'The installer wrote some values twice; nothing was imported.' );
            return false;
        }
        $lost = array_diff( $before['languages'], $final['languages'] );
        if ( $lost )
        {
            $error = ezpI18n::tr( 'design/admin/package', 'The translations %languages could not be kept; nothing was imported.', null, array( '%languages' => implode( ', ', $lost ) ) );
            return false;
        }
        return true;
    }

    /**
     * One class through the kernel's installer, "already exists" answered with update
     * (eZContentClassPackageHandler::ACTION_UPDATE, matched by identifier). What the update did is
     * added to $entry['changes'] / $entry['kept'].
     */
    static function importClass( eZPackage $package, array $item, &$error, array &$entry )
    {
        $path = eZPackageFileBrowser::filePath( $package, $item['file'] );
        $dom = $path !== false ? eZPackageComparison::loadDocument( $path ) : null;
        if ( !$dom || !$dom->documentElement )
        {
            $error = ezpI18n::tr( 'design/admin/package', 'The class is no longer in the package.' );
            return false;
        }
        // The installer reads the file as eZPackage::fetchDOMFromFile() does, without formatting white space
        $clean = new DOMDocument( '1.0', 'utf-8' );
        $clean->preserveWhiteSpace = false;
        $clean->loadXML( $dom->saveXML() );

        $parameters = array(
            'error' => array(),
            'error_default_actions' => array( 'ezcontentclass' => array( eZContentClassPackageHandler::ERROR_EXISTS => eZContentClassPackageHandler::ACTION_UPDATE ) ),
            'user_id' => eZUser::currentUserID(),
        );
        $installItem = array( 'type' => 'ezcontentclass', 'name' => $item['class_identifier'], 'os' => false,
                              'filename' => basename( $item['file'], '.xml' ), 'sub-directory' => dirname( $item['file'] ),
                              'content' => $clean->documentElement );
        $ok = $package->installItem( $installItem, $parameters );
        if ( !$ok )
        {
            $error = !empty( $parameters['error']['description'] ) ? $parameters['error']['description'] : ezpI18n::tr( 'design/admin/package', 'The installer could not import the class.' );
            return false;
        }
        if ( isset( $package->InstallData['ezcontentclass']['class_update'][$item['class_identifier']] ) )
        {
            $report = $package->InstallData['ezcontentclass']['class_update'][$item['class_identifier']];
            if ( $report['datatype_differs'] )
                $entry['kept'][] = ezpI18n::tr( 'design/admin/package', 'Not changed, the datatype differs: %list', null, array( '%list' => implode( ', ', $report['datatype_differs'] ) ) );
        }
        return true;
    }
}

?>
