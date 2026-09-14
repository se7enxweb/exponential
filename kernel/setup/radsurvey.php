<?php
/**
 * The extension point survey view.
 *
 * The RAD tools page lists the points somebody thought to write down. This one
 * lists what is actually there: every setting in every ini file on this
 * installation that names a class, every directory a handler is looked for in,
 * every interface the kernel declares, and every module view that could be
 * replaced or added to. It is read off disk on every request, so an extension
 * installed this morning is in it this afternoon.
 *
 * @copyright Copyright (C) Exponential Open Source Project. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

$Module = $Params['Module'];

require_once 'kernel/setup/expradsurvey.php';

$tpl = eZTemplate::factory();

// ── What to show ────────────────────────────────────────────────────────────
$sections = array(
    'settings'     => array( 'title' => 'Settings that name a class',
                             'what'  => 'Every setting on this installation whose value is a class, or whose name says it takes one. Change one of these and something else answers instead.' ),
    'repositories' => array( 'title' => 'Directories searched for handlers',
                             'what'  => 'Places the kernel looks for a file whose path it works out from a name. Add your extension to one of these and your file is found; leave it out and the class is never loaded however correctly it is written.' ),
    'contracts'    => array( 'title' => 'Interfaces and abstract classes',
                             'what'  => 'What the kernel declares for somebody else to implement, with how many methods each asks for and what already implements it. The ones with many methods and one implementation are the deep water.' ),
    'modules'      => array( 'title' => 'Modules and their views',
                             'what'  => 'Every page the system serves. A view can be replaced by an extension carrying a module of the same name, and a module of your own can add views beside them. Each view names the policies somebody needs to reach it.' ) );

$show = isset( $Params['Show'] ) && isset( $sections[$Params['Show']] ) ? $Params['Show'] : 'settings';

// A search box, so a list of several hundred can be got down to the handful
// somebody is actually looking for.
$find = isset( $Params['Find'] ) && is_string( $Params['Find'] )
        ? substr( preg_replace( '/[^A-Za-z0-9_.\[\]\/ -]+/', '', rawurldecode( $Params['Find'] ) ), 0, 60 )
        : '';

$offset  = isset( $Params['Offset'] ) ? (int) $Params['Offset'] : 0;
$perPage = 100;

$survey = expRADSurvey::survey();
$counts = $survey['counts'];

// ── The rows for whichever section is being shown ───────────────────────────
$rows = array();

switch ( $show )
{
    case 'repositories':
        foreach ( $survey['repositories'] as $entry )
            $rows[] = array(
                'one'   => $entry['ini'],
                'two'   => $entry['section'],
                'three' => $entry['variable'],
                'four'  => implode( ', ', array_slice( $entry['values'], 0, 6 ) ),
                'note'  => $entry['origin'],
                'state' => count( $entry['values'] ) ? 'ok' : 'empty' );
        break;

    case 'contracts':
        foreach ( expRADSurvey::contractsByWeight() as $entry )
            $rows[] = array(
                'one'   => $entry['name'],
                'two'   => $entry['kind'],
                'three' => $entry['methods'] . ' methods',
                'four'  => count( $entry['implementations'] )
                           ? implode( ', ', array_slice( $entry['implementations'], 0, 5 ) )
                           : 'nothing implements it yet',
                'note'  => $entry['source'],
                'state' => count( $entry['implementations'] ) ? 'ok' : 'empty' );
        break;

    case 'modules':
        foreach ( $survey['modules'] as $module )
        {
            foreach ( $module['views'] as $view )
                $rows[] = array(
                    'one'   => $module['name'] . '/' . $view['name'],
                    'two'   => $module['origin'],
                    'three' => $view['parameters'] . ' params'
                               . ( $view['unordered'] ? ', ' . $view['unordered'] . ' named' : '' ),
                    'four'  => count( $view['functions'] )
                               ? 'needs ' . implode( ', ', $view['functions'] )
                               : 'no policy check',
                    'note'  => $module['path'] . '/' . $view['script'],
                    'state' => count( $view['functions'] ) ? 'ok' : 'empty' );

            foreach ( $module['fetches'] as $fetch )
                $rows[] = array(
                    'one'   => $module['name'] . ' :: ' . $fetch,
                    'two'   => 'fetch function',
                    'three' => '',
                    'four'  => 'fetch( ' . $module['name'] . ', ' . $fetch . ' )',
                    'note'  => $module['path'] . '/function_definition.php',
                    'state' => 'ok' );
        }
        break;

    default:
        foreach ( $survey['settings'] as $entry )
            $rows[] = array(
                'one'   => $entry['ini'],
                'two'   => '[' . $entry['section'] . ']',
                'three' => $entry['variable'],
                'four'  => $entry['value'],
                'note'  => $entry['shape'] === 'class'   ? $entry['source']
                         : ( $entry['shape'] === 'unknown' ? 'looks like a class, and nothing declares one'
                                                           : 'an alias, resolved somewhere else' ),
                'state' => $entry['shape'] === 'class'   ? 'ok'
                         : ( $entry['shape'] === 'unknown' ? 'bad' : 'empty' ) );
}

// ── Narrowing, then paging ──────────────────────────────────────────────────
if ( $find !== '' )
{
    $needle = strtolower( $find );
    $rows = array_values( array_filter( $rows, function ( $row ) use ( $needle ) {
        foreach ( array( 'one', 'two', 'three', 'four', 'note' ) as $key )
            if ( strpos( strtolower( (string) $row[$key] ), $needle ) !== false )
                return true;

        return false;
    } ) );
}

$total  = count( $rows );
$offset = $offset < 0 || $offset >= $total ? 0 : $offset;
$page   = array_slice( $rows, $offset, $perPage );

// The address of this view, with whatever is set and nothing that is not, so a
// link out of the page comes back to the same place.
$address = function ( $show, $offset, $find ) {
    $url = '/setup/radsurvey/(show)/' . rawurlencode( $show );
    $url .= $offset ? '/(offset)/' . (int) $offset : '';
    $url .= $find !== '' ? '/(find)/' . rawurlencode( $find ) : '';

    return $url;
};

$tabs = array();
foreach ( $sections as $key => $section )
    $tabs[] = array( 'key'     => $key,
                     'title'   => $section['title'],
                     'what'    => $section['what'],
                     'current' => $key === $show,
                     'count'   => $key === 'modules'
                                  ? $counts['views'] + $counts['modules']
                                  : ( isset( $counts[$key] ) ? $counts[$key] : 0 ),
                     'url'     => $address( $key, 0, $find ) );

$pages = array();
for ( $at = 0; $at < $total; $at += $perPage )
    $pages[] = array( 'from'    => $at + 1,
                      'to'      => min( $at + $perPage, $total ),
                      'current' => $at === $offset,
                      'url'     => $address( $show, $at, $find ) );

$tpl->setVariable( 'survey_counts', $counts );
$tpl->setVariable( 'survey_tabs', $tabs );
$tpl->setVariable( 'survey_show', $show );
$tpl->setVariable( 'survey_section', $sections[$show] );
$tpl->setVariable( 'survey_rows', $page );
$tpl->setVariable( 'survey_total', $total );
$tpl->setVariable( 'survey_shown', count( $page ) );
$tpl->setVariable( 'survey_offset', $offset );
$tpl->setVariable( 'survey_pages', count( $pages ) > 1 ? $pages : array() );
$tpl->setVariable( 'survey_find', $find );
$tpl->setVariable( 'survey_reset', $address( $show, 0, '' ) );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:setup/radsurvey.tpl' );
$Result['path'] = array( array( 'url' => 'setup/rad',
                                'text' => ezpI18n::tr( 'kernel/setup', 'Rapid Application Development' ) ),
                         array( 'url' => false,
                                'text' => ezpI18n::tr( 'kernel/setup', 'Extension point survey' ) ) );
