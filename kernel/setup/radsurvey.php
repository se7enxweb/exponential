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
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

$Module = $Params['Module'];

require_once 'kernel/setup/expradsurvey.php';
require_once 'kernel/setup/expradhealth.php';

$tpl = eZTemplate::factory();

// ── What to show ────────────────────────────────────────────────────────────
$sections = array(
    'settings'     => array( 'title' => 'Settings that name a class',
                             'what'  => 'Every setting on this installation whose value is a class, or whose name says it takes one. Change one of these and something else answers instead.' ),
    'repositories' => array( 'title' => 'Places the kernel looks',
                             'what'  => 'Every setting that names a directory to search or an extension to search in. Add your extension to one of these and your file is found; leave it out and the class is never loaded however correctly it is written. Most of the time something works and should not, or does not work and should, the answer is one of these lines.' ),
    'contracts'    => array( 'title' => 'Interfaces and abstract classes',
                             'what'  => 'What the kernel declares for somebody else to implement, with how many methods each asks for and what already implements it. The ones with many methods and one implementation are the deep water.' ),
    'modules'      => array( 'title' => 'Modules and their views',
                             'what'  => 'Every page the system serves. A view can be replaced by an extension carrying a module of the same name, and a module of your own can add views beside them. Each view names the policies somebody needs to reach it.' ),
    'callables'    => array( 'title' => 'What a template can call',
                             'what'  => 'Every operator and function the engine has been taught, read out of the autoload arrays where they are really declared - there is no ini listing them. An operator not marked live belongs to an extension that is not active: the name is declared and nothing answers to it.' ),
    'events'       => array( 'title' => 'Events something can listen to',
                             'what'  => 'Every point the kernel announces as it works, swept out of the source rather than listed. A filter event uses what a listener returns, so one that forgets to return the value destroys it; a notify event ignores it. The lightest way there is to add behaviour: no module, no handler, no class to replace.' ),
    'overrides'    => array( 'title' => 'Templates already replaced',
                             'what'  => 'Every override registered here. Each is a place a template has already been replaced - which is both something to learn from and something to collide with, since two overrides matching the same thing are decided by load order rather than by intent.' ),
    'problems'     => array( 'title' => 'What is configured and cannot work',
                             'what'  => 'The same walk over the same files, asked the other question: not where something could go, but what is here that points at nothing. A module listed and not found answers every address under it with an error; a datatype offered and not found cannot be added and hides the values of the attributes that already use it. Add (check)/classes to the address to load every class as well, which takes a few seconds and is the only way to find one php refuses.' ),
    'replaced'     => array( 'title' => 'Kernel classes replaced outright',
                             'what'  => 'The heaviest mechanism there is, and the first thing to know before anything else is diagnosed: a replaced kernel class is not the kernel any more, whatever the kernel source says.' ) );

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

// Asked for once rather than per row: whether the engine really answers to an
// operator is the difference between a name that is declared and one that
// works, and that is the most useful thing this page can say about it.
$template = eZTemplate::factory();

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

    case 'problems':
        $findings = expRADHealth::findings();

        // The class loader check is seconds rather than milliseconds, so it is
        // asked for rather than done on every visit.
        $loader = isset( $Params['Check'] ) && $Params['Check'] === 'classes'
                  ? expRADHealth::loaderFindings()
                  : array( 'ran' => false, 'checked' => 0, 'findings' => array(), 'message' => '' );

        foreach ( $loader['findings'] as $finding )
            $findings[] = $finding;

        usort( $findings, array( 'expRADHealth', 'compare' ) );

        foreach ( $findings as $finding )
            $rows[] = array(
                'one'   => $finding['check'],
                'two'   => $finding['severity'],
                'three' => $finding['what'],
                'four'  => $finding['fix'],
                'note'  => $finding['where'],
                'state' => $finding['severity'] === expRADHealth::BROKEN ? 'bad'
                         : ( $finding['severity'] === expRADHealth::ODD ? 'ok' : 'empty' ) );
        break;

    case 'callables':
        foreach ( $survey['callables'] as $entry )
        {
            $live = $entry['kind'] === 'function'
                    || ( is_array( $template->Operators ) && isset( $template->Operators[$entry['name']] ) );

            $rows[] = array(
                'one'   => $entry['name'],
                'two'   => $entry['kind'],
                'three' => $live ? 'live' : 'declared, not active',
                'four'  => $entry['class'],
                'note'  => $entry['script'] !== '' ? $entry['script'] : $entry['from'],
                'state' => $live ? 'ok' : 'empty' );
        }
        break;

    case 'events':
        foreach ( $survey['events'] as $entry )
            $rows[] = array(
                'one'   => $entry['event'],
                'two'   => $entry['kind'],
                'three' => $entry['kind'] === 'filter' ? 'return the value' : 'return value ignored',
                'four'  => count( $entry['where'] ) . ' place' . ( count( $entry['where'] ) === 1 ? '' : 's' ),
                'note'  => implode( ', ', array_slice( $entry['where'], 0, 3 ) ),
                'state' => $entry['kind'] === 'filter' ? 'ok' : 'empty' );
        break;

    case 'overrides':
        foreach ( $survey['overrides'] as $entry )
            $rows[] = array(
                'one'   => $entry['name'],
                'two'   => $entry['source'],
                'three' => $entry['match'],
                'four'  => $entry['subdir'],
                'note'  => $entry['from'],
                'state' => $entry['source'] !== '' ? 'ok' : 'empty' );
        break;

    case 'replaced':
        foreach ( $survey['replaced'] as $entry )
            $rows[] = array(
                'one'   => $entry['class'],
                'two'   => 'kernel override',
                'three' => $entry['kernel'] !== '' ? 'replaces a kernel class' : 'replaces nothing in the kernel',
                'four'  => $entry['path'],
                'note'  => $entry['kernel'],
                'state' => $entry['kernel'] !== '' ? 'ok' : 'bad' );
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
                                  : ( $key === 'problems'
                                      ? expRADHealth::counts()['total']
                                      : ( isset( $counts[$key] ) ? $counts[$key] : 0 ) ),
                     'current_label' => $section['title'],
                     'url'     => $address( $key, 0, $find ) );

$pages = array();
for ( $at = 0; $at < $total; $at += $perPage )
    $pages[] = array( 'from'    => $at + 1,
                      'to'      => min( $at + $perPage, $total ),
                      'current' => $at === $offset,
                      'url'     => $address( $show, $at, $find ) );

// The headline numbers of the health check, so the page can lead with how many
// things are broken rather than making somebody click to find out.
$health = expRADHealth::counts();

$tpl->setVariable( 'survey_health', $health );
$tpl->setVariable( 'survey_check', isset( $Params['Check'] ) ? $Params['Check'] : '' );
$tpl->setVariable( 'survey_check_url', '/setup/radsurvey/(show)/problems/(check)/classes' );
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
