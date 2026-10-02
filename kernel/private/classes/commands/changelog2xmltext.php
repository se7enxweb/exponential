<?php
/**
 * The code of bin/php/changelog2xmltext.php, moved into a class (#207 stage 1). The file bin/php/changelog2xmltext.php is one call to it.
 * @description Convert a Changelog file to XML text format and print it to standard output
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of bin/php/changelog2xmltext.php:
 *
 *
 * File containing the changelog2xmltext.php script.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace
{


function isChangeEntry( $line, &$changeText )
{
    if ( preg_match( "/^- (.+)$/", $line, $matches ) )
    {
        $changeText = $matches[1];
        return true;
    }
    return false;
}

function addListItem( &$listLines, $changeText )
{
    if ( $changeText !== false )
    {
        $changeText = str_replace( array( "<", ">",   ),
                                   array( "[", "]" ),
                                   $changeText );
        $methodText = 'http|https|ftp|sftp';
        $elements = preg_split( "#((?:(?:$methodText)://)(?:(?:[a-zA-Z0-9_-]+\\.)*[a-zA-Z0-9_-]+(?:(?:[\/a-zA-Z0-9_+$-]+\\.)*(?:[\/a-zA-Z0-9_+$-])*))(?:\#[a-zA-Z0-9-]+)?\?*(?:[a-zA-Z0-9_-]+=[a-zA-Z0-9_-]+&*)*)#m",
                                $changeText,
                                false,
                                PREG_SPLIT_DELIM_CAPTURE );
        $newElements = array();
        $i = 0;
        foreach ( $elements as $element )
        {
            if ( ( $i % 2 ) == 1 )
            {
                $element = "<link href=\"$element\">$element</link>";
            }
            $newElements[] = $element;
            ++$i;
        }
        $changeText = implode( '', $newElements );

        $elements = preg_split( "/bugs? *# *([0-9]+)/im",
                                $changeText,
                                false,
                                PREG_SPLIT_DELIM_CAPTURE );
        $newElements = array();
        $i = 0;
        foreach ( $elements as $element )
        {
            if ( ( $i % 2 ) == 1 )
            {
                $element = "<link href=\"/bugs/view/$element\">bug #$element</link>";
            }
            $newElements[] = $element;
            ++$i;
        }
        $changeText = implode( '', $newElements );

        $elements = preg_split( "/enhancements? *# *([0-9]+)/im",
                                $changeText,
                                false,
                                PREG_SPLIT_DELIM_CAPTURE );
        $newElements = array();
        $i = 0;
        foreach ( $elements as $element )
        {
            if ( ( $i % 2 ) == 1 )
            {
                $element = "<link href=\"/bugs/view/$element\">enhancement #$element</link>";
            }
            $newElements[] = $element;
            ++$i;
        }
        $changeText = implode( '', $newElements );

        $changeText = preg_replace( "# *\( *?(:?manually +)?merged +from +[a-z0-9.-]+(?:/[a-z0-9.-]+)*[,/]? +(\([0-9](?:[.-][0-9a-z]+)*\))? *(?:rev|erv)(?:ision|\.)? *[0-9]+ *\)#i", '', $changeText );

        $listLines[] = array( 'type' => 'li',
                              'text' => $changeText );
    }
}

function createList( &$newLines, &$listLines )
{
    if ( count( $listLines ) > 0 )
    {
        $ulEntry = array( 'type' => 'ul',
                          'items' => $listLines );
        $newLines[] = $ulEntry;
        $listLines = array();
    }
}

function isEmptyLine( $line )
{
    return strlen( trim( $line ) ) == 0;
}

function dumpToText( $nodes )
{
    global $cli, $script;

    $text = '';
    foreach ( $nodes as $node )
    {
        if ( is_string( $node ) )
        {
            $text .= $node . "\n";
        }
        else
        {
            if ( !isset( $node['type'] ) )
            {
                    var_dump( $node );
            }
            $type = $node['type'];
            switch ( $type )
            {
                case 'title':
                {
                    $text .= $node['name'] . "\n";
                } break;
                case 'section':
                {
                    if ( count( $node['items'] ) > 0 )
                    {
                        $text .= "<header level='" . $node['level'] . "'>" . $node['name'] . "</header>\n";
                        $text .= dumpToText( $node['items'] );
                    }
                } break;
                case 'ul':
                {
                    $text .= "<ul>\n";
                    $text .= dumpToText( $node['items'] );
                    $text .= "</ul>\n\n";
                } break;
                case 'li':
                {
                    $text .= "  <li>" . $node['text'] . "</li>\n";
                } break;
                default:
                {
                    $cli->error( "Unknown type [$type]\n" );
                    $script->shutdown( 1 );
                }
            }
        }
    }
    return $text;
}
}

namespace Exponential\Command\Kernel
{

class Changelog2xmltext extends \Exponential\Runnable\Command
{
    public function run()
    {
        // the script's variables were globals; functions of the script read them with "global"
        foreach ( array( 'changeText', 'changelogFilename', 'cli', 'currentListEntry', 'fd', 'header', 'headerLevel', 'lastSection', 'line', 'lineNumber', 'lines', 'listCounter', 'listLines', 'matches', 'newLines', 'newText', 'options', 'script', 'section', 'text' ) as $__name )
            ${$__name} = &$GLOBALS[$__name];
        unset( $__name );

        $cli = $this->cli();
        $script = $this->script( array( 'description' => ( "Exponential Changelog converter\n\n" .
                                                                "Converts a Changelog into XML text format usable in Exponential\n" .
                                                                "The result is printed to the standard output\n\nExample: ./bin/php/changelog2xmltext.php Changelog" ),
                                             'use-session' => false,
                                             'use-modules' => true,
                                             'use-extensions' => true ) );

        $options = $this->startup( "",
                                        "[changelog]",
                                        false, false,
                                        array( 'log' => false,
                                               'siteaccess' => false ) );

        if ( count( $options['arguments'] ) < 1 )
        {
            $cli->error( "Missing changelog file" );
            $script->shutdown( 1 );
        }

        $changelogFilename = $options['arguments'][0];

        $fd = fopen( $changelogFilename, 'r' ) or $script->shutdown( 1, "Couldn't not open file $changelogFilename" );
        $text = fread( $fd, filesize( $changelogFilename ) );
        fclose( $fd );

        $lines = explode( "\n", $text );

        $newLines = array();
        $lineNumber = 0;
        $listCounter = 0;
        $listLines = array();
        $currentListEntry = false;
        $lastSection = null;
        foreach ( $lines as $line )
        {
            $line = trim( $line, "\r" );

            ++$lineNumber;
            if ( $lineNumber == 1 )
            {
                $newLines[] = array( 'type' => 'title',
                                     'name' => $line );
                $newLines[] = '';
                continue;
            }
            if ( $listCounter > 0 )
            {
                if ( isChangeEntry( $line, $changeText ) )
                {
                    addListItem( $listLines, $currentListEntry );
                    $currentListEntry = $changeText;
                }
                else if ( isEmptyLine( $line ) )
                {
                    addListItem( $listLines, $currentListEntry );
                    $currentListEntry = false;
                    $listCounter = 0;
                }
                else
                {
                    $currentListEntry .= ' ' . trim( $line );
                }
            }
            else if ( preg_match( "/^(\*?)(.+):/", $line, $matches ) )
            {
                addListItem( $listLines, $currentListEntry );
                $currentListEntry = false;
                createList( $lastSection['items'], $listLines );

                $header = $matches[2];
                $headerLevel = 1;
                if ( !$matches[1] )
                    $headerLevel += 1;
                $section = array( 'type' => 'section',
                                  'level' => $headerLevel,
                                  'name' => "$header",
                                  'items' => array() );
                $newLines[] = $section;
                unset( $lastSection );
                $lastSection =& $newLines[count( $newLines ) - 1];
                $listCounter = 1;
            }
        }
        addListItem( $listLines, $currentListEntry );
        $currentListEntry = false;
        if ( $lastSection !== null )
        {
            createList( $lastSection['items'], $listLines );
        }

        $newText = dumpToText( $newLines );

        $cli->output( $newText, false );

        $script->shutdown();
    }
}

}
