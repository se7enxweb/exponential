<?php
/**
 * File containing the expExtensionWizard class.
 *
 * @copyright Copyright (C) Exponential Open Source Project. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

/**
 * What every extension wizard needs, whatever kind of extension it builds.
 *
 * The names, the licence, the packaging files, and turning a path => contents
 * map into an extension on disk or an archive to download. A wizard built on
 * this describes its own parts and writes its own files; everything an
 * extension has regardless of what is inside it lives here.
 */
abstract class expExtensionWizard
{
    /** A name has to be usable as a directory, a design name and an ini value. */
    const NAME_PATTERN = '/^[a-z][a-z0-9_]{2,40}$/';

    /**
     * What this wizard can put in, as key => array( label, description, default ).
     *
     * @return array
     */
    abstract public static function parts();

    /**
     * Everything the wizard was asked for, with the gaps filled in.
     *
     * @param array $input what the form sent.
     * @return array
     */
    abstract public static function settings( array $input );

    /**
     * What is wrong with those settings, in words the form can show.
     *
     * @param array $settings
     * @return array of string, empty when there is nothing wrong.
     */
    abstract public static function problems( array $settings );

    /**
     * Every file the extension is made of, as path => contents.
     *
     * @param array $settings
     * @return array
     */
    abstract public static function files( array $settings );

    /**
     * The licences the wizard can write.
     *
     * @return array key => label
     */
    public static function licences()
    {
        return array( 'GPL-2.0-or-later' => 'GNU General Public License v2.0 or later (GPLv2+)',
                      'MIT'              => 'MIT',
                      'proprietary'      => 'Proprietary - all rights reserved' );
    }

    /**
     * The identifier an offered licence is stored and written under.
     *
     * SPDX renamed the plain GPL identifiers when it made the distinction
     * between a version and that version or later explicit, and composer
     * validates against the current list. An extension generated before the
     * rename still says GPL-2.0, so that is read as what it always meant here -
     * version 2 or later - rather than being dropped for the default.
     *
     * @param string $licence what was asked for.
     * @return string one of licences().
     */
    public static function licence_id( $licence )
    {
        $offered = self::licences();

        if ( is_string( $licence ) && isset( $offered[$licence] ) )
            return $licence;

        $renamed = array( 'GPL-2.0'       => 'GPL-2.0-or-later',
                          'GPL-2.0+'      => 'GPL-2.0-or-later',
                          'GPL2'          => 'GPL-2.0-or-later',
                          'GPLv2'         => 'GPL-2.0-or-later',
                          'GPL-3.0'       => 'GPL-2.0-or-later',
                          'GPL-2.0-only'  => 'GPL-2.0-or-later' );

        if ( is_string( $licence ) && isset( $renamed[$licence] ) )
            return $renamed[$licence];

        $keys = array_keys( $offered );

        return $keys[0];
    }

    /**
     * Where an extension of this name would live.
     *
     * @param string $name
     * @return string
     */
    public static function extensionPath( $name )
    {
        return self::installationRoot() . '/extension/' . $name;
    }

    /**
     * The installation this page belongs to.
     *
     * eZSys::rootDir() answers with the document root of the request, which on
     * an admin vhost is a directory of symlinks rather than the installation.
     *
     * @return string
     */
    public static function installationRoot()
    {
        $root = realpath( dirname( __FILE__ ) . '/../..' );

        return $root === false ? '.' : $root;
    }

    /**
     * A name that can be a directory, a design and an ini value.
     *
     * @param string $value
     * @param bool $allowEmpty
     * @return string empty when what was typed cannot be one.
     */
    public static function safeName( $value, $allowEmpty = false )
    {
        if ( !is_string( $value ) )
            return '';

        $value = strtolower( trim( $value ) );
        $value = preg_replace( '/[^a-z0-9_]+/', '_', $value );
        $value = trim( (string) $value, '_' );

        if ( $value === '' )
            return '';

        return preg_match( self::NAME_PATTERN, $value ) ? $value : ( $allowEmpty ? '' : '' );
    }

    /**
     * Text on its way into a file somebody else will read.
     *
     * @param string $value
     * @param int $max
     * @return string
     */
    public static function text( $value, $max = 250 )
    {
        if ( !is_scalar( $value ) )
            return '';

        $value = preg_replace( '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', (string) $value );
        if ( $value === null )
            return '';

        // Everything typed here ends up inside a generated php comment, or
        // inside an eZ ini file, which is a php file whose whole body is one
        // comment. A value carrying the characters that end a comment closes it,
        // and what follows is no longer a comment but php - in a file that will
        // be deployed and included. A title is not worth an execution hole, so
        // the sequence does not survive being typed.
        $value = self::commentText( $value );

        $value = trim( $value );

        return mb_strlen( $value, 'UTF-8' ) > $max ? mb_substr( $value, 0, $max, 'UTF-8' ) : $value;
    }

    /**
     * The directories those files sit in, deepest last.
     *
     * @param array $files from files().
     * @return array of string
     */
    public static function directories( array $files )
    {
        $directories = array();

        foreach ( array_keys( $files ) as $path )
        {
            $directory = dirname( $path );
            while ( $directory !== '.' && $directory !== '' && $directory !== '/' )
            {
                $directories[$directory] = $directory;
                $directory = dirname( $directory );
            }
        }

        sort( $directories );

        return $directories;
    }

    protected static function iniHeader( array $settings, $what )
    {
        return "<?php /* #?ini charset=\"utf-8\"?\n\n"
             . "#\n# " . $what . " for " . $settings['title'] . ".\n"
             . "#\n# Written by the " . static::wizardName() . " in the admin interface.\n"
             . "# Nothing reads it back: edit it freely.\n#\n\n";
    }

    /**
     * What wrote this, for the head of a generated file.
     *
     * Each wizard says its own name. The base answers for one that has not
     * bothered, rather than claiming to be a wizard it is not.
     *
     * @return string
     */
    public static function wizardName()
    {
        return 'extension wizard';
    }

    /**
     * The licence in a line or two, for the head of a generated file.
     *
     * A file that carries no notice says nothing about how it may be used, and
     * "GPL" on its own does not say version 2 or later either.
     *
     * @param array $settings
     * @return string, empty when there is nothing worth saying.
     */
    protected static function licenceNotice( array $settings )
    {
        $holder = $settings['author'] !== '' ? $settings['author'] : $settings['title'];
        $year   = date( 'Y' );

        switch ( $settings['licence'] )
        {
            case 'MIT':
                return " * Copyright (c) " . $year . " " . $holder . ". Released under the MIT licence;\n"
                     . " * see LICENSE for the terms.\n";

            case 'proprietary':
                return " * Copyright (c) " . $year . " " . $holder . ". All rights reserved.\n"
                     . " * Not to be copied, distributed or used without written permission.\n";
        }

        return " * Copyright (c) " . $year . " " . $holder . ".\n"
             . " *\n"
             . " * This program is free software; you can redistribute it and/or modify it under\n"
             . " * the terms of the GNU General Public License as published by the Free Software\n"
             . " * Foundation; either version 2 of the License, or (at your option) any later\n"
             . " * version. See LICENSE for the full terms.\n";
    }

    protected static function ezinfo( array $settings )
    {
        $class = ucfirst( str_replace( '_', '', $settings['name'] ) ) . 'Info';

        return "<?php\n"
             . "/**\n * What the admin interface reads about this extension.\n *\n"
             . self::licenceNotice( $settings )
             . " */\n\n"
             . "class " . $class . "\n"
             . "{\n"
             . "    const SOFTWARE_VERSION = '" . $settings['version'] . "';\n\n"
             . "    static function info()\n"
             . "    {\n"
             . "        return array(\n"
             . "            'Name'      => '" . self::phpString( $settings['title'] ) . "',\n"
             . "            'Version'   => self::SOFTWARE_VERSION,\n"
             . "            'Copyright' => '" . self::phpString( $settings['author'] ) . "',\n"
             . "            'License'   => '" . self::phpString( $settings['licence'] ) . "' );\n"
             . "    }\n"
             . "}\n";
    }

    protected static function extensionXml( array $settings )
    {
        return "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n"
             . "<extension>\n"
             . "  <name>" . self::xml( $settings['name'] ) . "</name>\n"
             . "  <summary>" . self::xml( $settings['summary'] ) . "</summary>\n"
             . "  <version>" . self::xml( $settings['version'] ) . "</version>\n"
             . "  <license>" . self::xml( $settings['licence'] ) . "</license>\n"
             . "  <maintainers>\n"
             . "    <maintainer>" . self::xml( $settings['author'] ) . "</maintainer>\n"
             . "  </maintainers>\n"
             . "</extension>\n";
    }

    protected static function composerJson( array $settings )
    {
        $package = array(
            'name'        => $settings['vendor'] . '/' . str_replace( '_', '-', $settings['name'] ),
            'description' => $settings['summary'],
            'type'        => 'ezpublish-legacy-extension',
            'license'     => $settings['licence'],
            'require'     => array( 'php' => '>=7.4' ),
            'extra'       => array( 'installer-name' => $settings['name'] ),
        );

        if ( $settings['author'] !== '' )
            $package['authors'] = array( array( 'name' => $settings['author'] ) );

        return json_encode( $package, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n";
    }

    /**
     * The licence as a sentence, for the readme.
     *
     * @param array $settings
     * @return string
     */
    protected static function licenceLine( array $settings )
    {
        switch ( $settings['licence'] )
        {
            case 'MIT':
                return "MIT. See [LICENSE](LICENSE).";

            case 'proprietary':
                return "Proprietary - all rights reserved. See [LICENSE](LICENSE).";
        }

        return "GNU General Public License, version 2 or, at your option, any later version\n"
             . "(`GPL-2.0-or-later`). See [LICENSE](LICENSE).";
    }

    protected static function gitignore( array $settings )
    {
        return "# Editor leftovers\n*~\n#*#\n.#*\n*.orig\n*.rej\n*.bak\n*.swp\n*.swo\n\n"
             . "# Operating system\n.DS_Store\nThumbs.db\n\n"
             . "# Build output and dependencies\n/vendor/\n/node_modules/\n*.log\n";
    }

    protected static function licence( array $settings )
    {
        $year   = date( 'Y' );
        $holder = $settings['author'] !== '' ? $settings['author'] : $settings['title'];

        switch ( $settings['licence'] )
        {
            case 'MIT':
                return "MIT License\n\nCopyright (c) " . $year . " " . $holder . "\n\n"
                     . "Permission is hereby granted, free of charge, to any person obtaining a copy\n"
                     . "of this software and associated documentation files (the \"Software\"), to deal\n"
                     . "in the Software without restriction, including without limitation the rights\n"
                     . "to use, copy, modify, merge, publish, distribute, sublicense, and/or sell\n"
                     . "copies of the Software, and to permit persons to whom the Software is\n"
                     . "furnished to do so, subject to the following conditions:\n\n"
                     . "The above copyright notice and this permission notice shall be included in\n"
                     . "all copies or substantial portions of the Software.\n\n"
                     . "THE SOFTWARE IS PROVIDED \"AS IS\", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR\n"
                     . "IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,\n"
                     . "FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE\n"
                     . "AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER\n"
                     . "LIABILITY, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE\n"
                     . "OR OTHER DEALINGS IN THE SOFTWARE.\n";

            case 'proprietary':
                return "Copyright (c) " . $year . " " . $holder . "\n\nAll rights reserved.\n\n"
                     . "This software and its source may not be copied, distributed or used in any\n"
                     . "form without the written permission of the copyright holder.\n";
        }

        // GPL-2.0-or-later. The "or (at your option) any later version" clause
        // is what makes it that rather than version 2 on its own, so it is in
        // the notice as the Free Software Foundation words it.
        return "Copyright (c) " . $year . " " . $holder . "\n\n"
             . "This program is free software; you can redistribute it and/or modify it under\n"
             . "the terms of the GNU General Public License as published by the Free Software\n"
             . "Foundation; either version 2 of the License, or (at your option) any later\n"
             . "version.\n\n"
             . "This program is distributed in the hope that it will be useful, but WITHOUT ANY\n"
             . "WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A\n"
             . "PARTICULAR PURPOSE. See the GNU General Public License for more details.\n\n"
             . "You should have received a copy of the GNU General Public License along with\n"
             . "this program; if not, see <https://www.gnu.org/licenses/>.\n";
    }

    /**
     * Text on its way into single quotes in generated php.
     *
     * @param string $value
     * @return string
     */
    protected static function phpString( $value )
    {
        return str_replace( array( '\\', "'" ), array( '\\\\', "\\'" ), (string) $value );
    }

    /**
     * Text on its way into generated xml.
     *
     * @param string $value
     * @return string
     */
    protected static function xml( $value )
    {
        return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' );
    }

    /**
     * Writes the extension into extension/.
     *
     * Refuses to touch anything outside the installation's own extension
     * directory, and refuses to write over an extension that is already there:
     * a wizard that can overwrite a design somebody is using is not a wizard,
     * it is an accident waiting for a typed name.
     *
     * @param array $settings from settings().
     * @return array ok, message, and the paths written.
     */
    public static function write( array $settings )
    {
        $problems = static::problems( $settings );
        if ( count( $problems ) )
            return array( 'ok' => false, 'message' => implode( ' ', $problems ), 'written' => array() );

        $target = self::extensionPath( $settings['name'] );
        $root   = self::installationRoot() . '/extension/';

        // Belt and braces: the name has been through safeName, and the result
        // still has to sit inside the extension directory.
        if ( strpos( $target, $root ) !== 0 )
            return array( 'ok' => false, 'message' => 'That would write outside extension/.', 'written' => array() );

        $files = static::files( $settings );

        if ( !@mkdir( $target, 0775, true ) && !is_dir( $target ) )
            return array( 'ok' => false,
                          'message' => 'extension/ could not be written to. Check that the web server owns it, or take the archive instead.',
                          'written' => array() );

        $written = array();
        foreach ( $files as $path => $contents )
        {
            $full = $target . '/' . $path;
            $directory = dirname( $full );

            if ( !is_dir( $directory ) && !@mkdir( $directory, 0775, true ) && !is_dir( $directory ) )
                return array( 'ok' => false,
                              'message' => 'Could not create ' . $path . '. ' . count( $written ) . ' file(s) were written before that.',
                              'written' => $written );

            if ( @file_put_contents( $full, $contents ) === false )
                return array( 'ok' => false,
                              'message' => 'Could not write ' . $path . '. ' . count( $written ) . ' file(s) were written before that.',
                              'written' => $written );

            // Readable by the web server, writable by its owner, and nothing
            // else: the umask of whatever ran this is not a permission policy.
            @chmod( $full, 0644 );

            $written[] = $path;
        }

        return array( 'ok' => true,
                      'message' => count( $written ) . ' files written to extension/' . $settings['name'] . '.',
                      'written' => $written );
    }

    /**
     * The extension as a zip, for an installation whose extension directory the
     * web server cannot write to - which is most of the ones worth having.
     *
     * @param array $settings
     * @return array ok, message, path to a temporary file, and the name to send.
     */
    public static function archive( array $settings )
    {
        if ( !class_exists( 'ZipArchive' ) )
            return array( 'ok' => false, 'message' => 'This installation has no zip support, so an archive cannot be built.' );

        $problems = static::problems( $settings );

        // An extension that already exists is a reason not to write over it, but
        // no reason not to hand somebody a copy to look at.
        $problems = array_values( array_filter( $problems, function ( $problem ) {
            return strpos( $problem, 'already exists' ) === false;
        } ) );

        if ( count( $problems ) )
            return array( 'ok' => false, 'message' => implode( ' ', $problems ) );

        $path = eZSys::cacheDirectory() . '/designextension';
        if ( !is_dir( $path ) )
            eZDir::mkdir( $path, false, true );

        $file = $path . '/' . $settings['name'] . '-' . getmypid() . '.zip';

        $zip = new ZipArchive();
        if ( $zip->open( $file, ZipArchive::CREATE | ZipArchive::OVERWRITE ) !== true )
            return array( 'ok' => false, 'message' => 'The archive could not be opened for writing.' );

        foreach ( static::files( $settings ) as $relative => $contents )
            $zip->addFromString( $settings['name'] . '/' . $relative, $contents );

        $zip->close();

        return array( 'ok' => true,
                      'message' => 'Archive built.',
                      'path' => $file,
                      'filename' => $settings['name'] . '.zip' );
    }

    /**
     * Whether extension/ can be written to at all, for the page to say so
     * before somebody presses the button and finds out.
     *
     * @return bool
     */
    public static function canWrite()
    {
        return is_writable( self::installationRoot() . '/extension' );
    }

    /**
     * Which of the parts a set of settings turned on, by name.
     *
     * @param array $settings
     * @return array of string
     */
    public static function chosenParts( array $settings )
    {
        $chosen = array();
        foreach ( static::parts() as $key => $part )
            if ( !empty( $settings['parts'][$key] ) )
                $chosen[] = $key;

        return $chosen;
    }

    // ── Values on their way into generated files ─────────────────────────────

    /**
     * Text that is safe inside a generated php comment.
     *
     * A generated file puts a name, a title and a summary into a doc comment,
     * and an eZ ini file is a php file whose whole body is one comment. A value
     * containing the two characters that end a comment closes it, and whatever
     * follows is no longer a comment - it is php, in a file that will be
     * deployed and included. That is remote code execution by way of a form
     * field, so the sequence never survives, and neither does an opening or
     * closing php tag.
     *
     * @param mixed $value
     * @return string
     */
    public static function commentText( $value )
    {
        if ( !is_scalar( $value ) )
            return '';

        $value = (string) $value;

        // The end of a comment, however it is spelled or spaced.
        $value = preg_replace( '#\*+/#', '*', $value );

        // And the tags that would open or close php around it.
        $value = str_replace( array( '<?', '?>' ), array( '< ?', '? >' ), $value );

        return $value;
    }

    /**
     * A value that is safe on the right of an ini setting.
     *
     * An ini file here is a php file wrapped in a comment, so everything above
     * applies. A newline matters as well: it would end the setting and let the
     * rest of the value be read as another one, which is how a title becomes an
     * extra ActiveExtensions line.
     *
     * @param mixed $value
     * @param int $max
     * @return string
     */
    public static function iniValue( $value, $max = 1000 )
    {
        $value = self::commentText( $value );

        // One line, whatever was typed.
        $value = str_replace( array( "\r\n", "\r", "\n" ), ' ', $value );

        // Characters ini gives its own meaning to at the start of a line cannot
        // reach the start of one any more, but a bracket or a hash in the middle
        // of a value is still worth leaving alone.
        $value = trim( $value );

        return mb_strlen( $value, 'UTF-8' ) > $max ? mb_substr( $value, 0, $max, 'UTF-8' ) : $value;
    }
}
