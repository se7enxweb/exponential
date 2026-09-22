<?php
//
// Definition of ezjscJavascriptOptimizer class
//
// Created on: <26-Sep-2011 00:00:00 dj>
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ JSCore extension for eZ Publish
// SOFTWARE RELEASE: 1.x
// COPYRIGHT NOTICE: Copyright (C) 1999-2014 eZ Systems AS
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

class ezjscJavascriptOptimizer
{
    /**
     * 'compress' javascript code by removing whitespace
     *
     * @param string $script Concated JavaScript string
     * @param int $packLevel Level of packing, values: 2-3
     * @return string
     */
    public static function optimize( $script, $packLevel = 2 )
    {
        // Normalize line feeds
        $script = str_replace( array( "\r\n", "\r" ), "\n", $script );

        // Remove whitespace from start & end of line + singelline comment + multiple linefeeds
        $script = preg_replace( array( '/\n\s+/', '/\s+\n/', '#\n\s*//.*#', '/\n+/' ), "\n", $script );

        // Remove multiline comments.
        //
        // The replacement puts back whatever whitespace the pattern consumed,
        // because the pattern deliberately eats one whitespace character in
        // front of the comment and it matters enormously which one it was.
        //
        // Dropping it entirely (the original behaviour) breaks this, which
        // webpack emits constantly:
        //
        //     } // eslint-disable-next-line import/no-unused-modules
        //     /* harmony default export */ __webpack_exports__["default"] = ({
        //
        // -- the newline goes, the assignment slides up onto the // line, and
        // everything after it is commented out. All three of the media theme's
        // bundles parsed before this function and failed after it.
        //
        // Always replacing it with a newline breaks the other shape, which
        // webpack emits just as often:
        //
        //     popperGenerator: function() { return /* binding */ popperGenerator; }
        //
        // -- a newline after "return" is an automatic semicolon, so every
        // export so written returns undefined. That version parsed cleanly and
        // took the site down anyway: popperGenerator is not a function.
        //
        // Neither constant is right, because the pattern matches both a line
        // break before a comment on its own line and a space before a comment
        // in the middle of one. So capture it and restore it.
        $script = preg_replace( '!(\n|\s|^)/\*[^*]*\*+(?:[^/][^*]*\*+)*/!', '$1', $script );
        $script = preg_replace( '!(?:;)/\*[^*]*\*+(?:[^/][^*]*\*+)*/!', ';', $script );

        // Collapse the runs of line feeds the step above can leave behind, the
        // same way the whitespace pass does.
        $script = preg_replace( '/\n+/', "\n", $script );

        return $script;
    }
}
?>
