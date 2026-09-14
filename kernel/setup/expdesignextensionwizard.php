<?php
/**
 * File containing the expDesignExtensionWizard class.
 *
 * @copyright Copyright (C) Exponential Open Source Project. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

/**
 * Builds a design extension: the directories, the settings and the templates
 * that a working design needs, ready to switch on.
 *
 * Nothing here writes anything until it is asked to. The files are produced as
 * a path => contents map, which the page can show, put in an archive, or write
 * into extension/ - and which a test can read without a web server anywhere
 * near it.
 */
class expDesignExtensionWizard
{
    /** A name has to be usable as a directory, a design name and an ini value. */
    const NAME_PATTERN = '/^[a-z][a-z0-9_]{2,40}$/';

    /**
     * The parts the wizard can put in, and what each one is for.
     *
     * The keys are what the form posts; the order is the order they are listed.
     *
     * @return array key => array( label, description, default )
     */
    public static function parts()
    {
        return array(
            'pagelayout' => array(
                'label' => 'Page layout',
                'description' => 'pagelayout.tpl, the frame every page is drawn inside.',
                'default' => true ),
            'page_parts' => array(
                'label' => 'Page parts',
                'description' => 'The head, header and footer the page layout includes, so each can be overridden on its own.',
                'default' => true ),
            'stylesheets' => array(
                'label' => 'Stylesheets',
                'description' => 'A site stylesheet, registered in design.ini so it is loaded without touching a template.',
                'default' => true ),
            'print_stylesheet' => array(
                'label' => 'Print stylesheet',
                'description' => 'A second stylesheet for print, so a page can be put on paper without the furniture.',
                'default' => false ),
            'javascript' => array(
                'label' => 'JavaScript',
                'description' => 'A site script, registered in design.ini alongside the stylesheet.',
                'default' => true ),
            'images' => array(
                'label' => 'Images directory',
                'description' => 'design/<name>/images, where ezimage looks.',
                'default' => true ),
            'overrides' => array(
                'label' => 'Template overrides',
                'description' => 'override.ini and an example full view, to show where an override goes and how it is matched.',
                'default' => true ),
            'siteaccess' => array(
                'label' => 'Siteaccess settings',
                'description' => 'A settings/siteaccess/<name> directory, for settings that apply to one site only.',
                'default' => false ),
            'autoloads' => array(
                'label' => 'Template operators',
                'description' => 'An autoloads directory with a registered operator, ready to extend.',
                'default' => false ),
            'ezinfo' => array(
                'label' => 'ezinfo.php',
                'description' => 'What the admin interface reads to show the extension name, version and licence.',
                'default' => true ),
            'extension_xml' => array(
                'label' => 'extension.xml',
                'description' => 'The packaged description of the extension.',
                'default' => true ),
            'composer' => array(
                'label' => 'composer.json',
                'description' => 'So the extension can be required by name rather than copied in.',
                'default' => true ),
            'readme' => array(
                'label' => 'README.md',
                'description' => 'What it is, how to switch it on, and what is inside it.',
                'default' => true ),
            'gitignore' => array(
                'label' => '.gitignore',
                'description' => 'Keeps editor leftovers and build output out of the repository.',
                'default' => true ),
            'licence' => array(
                'label' => 'LICENSE',
                'description' => 'The licence text named below.',
                'default' => false ),
        );
    }

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
     * The designs a new one can fall back on, in the order eZ would try them.
     *
     * @return array key => label
     */
    public static function baseDesigns()
    {
        $bases = array( 'standard' => 'standard - the kernel templates, and nothing else' );

        foreach ( array( 'admin3', 'admin2', 'admin', 'base', 'ezwebin' ) as $design )
        {
            if ( is_dir( 'design/' . $design ) )
                $bases[$design] = $design . ' - fall back on this design first';
        }

        return $bases;
    }

    /**
     * Everything the wizard was asked for, with the gaps filled in.
     *
     * The form is the only thing that ever fills this, so every value is
     * treated as text somebody typed: names are checked against a pattern and
     * everything else is trimmed, cut and escaped where it lands.
     *
     * @param array $input
     * @return array
     */
    public static function settings( array $input )
    {
        $name = self::safeName( isset( $input['name'] ) ? $input['name'] : '' );

        $settings = array(
            'name'        => $name,
            'title'       => self::text( isset( $input['title'] ) ? $input['title'] : '', 120 ),
            'summary'     => self::text( isset( $input['summary'] ) ? $input['summary'] : '', 250 ),
            'author'      => self::text( isset( $input['author'] ) ? $input['author'] : '', 120 ),
            'vendor'      => self::safeName( isset( $input['vendor'] ) ? $input['vendor'] : '', true ),
            'version'     => self::text( isset( $input['version'] ) ? $input['version'] : '', 20 ),
            'licence'     => self::licence_id( isset( $input['licence'] ) ? $input['licence'] : '' ),
            'base_design' => isset( $input['base_design'] ) && isset( self::baseDesigns()[$input['base_design']] )
                             ? $input['base_design'] : 'standard',
            'siteaccess'  => self::safeName( isset( $input['siteaccess'] ) ? $input['siteaccess'] : '', true ),
        );

        if ( $settings['title'] === '' )
            $settings['title'] = $settings['name'] !== '' ? ucwords( str_replace( '_', ' ', $settings['name'] ) ) : '';
        if ( $settings['version'] === '' )
            $settings['version'] = '1.0.0';
        if ( $settings['vendor'] === '' )
            $settings['vendor'] = 'exponential';
        if ( $settings['summary'] === '' && $settings['title'] !== '' )
            $settings['summary'] = 'The ' . $settings['title'] . ' design.';

        $parts = self::parts();
        $chosen = isset( $input['parts'] ) && is_array( $input['parts'] ) ? $input['parts'] : null;

        foreach ( $parts as $key => $part )
        {
            // On a first visit nothing has been posted, so the defaults stand.
            // Once the form has been sent, an unticked box sends nothing, and
            // the absence is the answer.
            $settings['parts'][$key] = $chosen === null
                                       ? $part['default']
                                       : in_array( $key, $chosen, true );
        }

        return $settings;
    }

    /**
     * What is wrong with these settings, in words the form can show.
     *
     * @param array $settings from settings().
     * @return array of string, empty when there is nothing wrong.
     */
    public static function problems( array $settings )
    {
        $problems = array();

        if ( $settings['name'] === '' )
            $problems[] = 'The extension needs a name: lower case letters, digits and underscores, three to forty one characters, starting with a letter.';

        if ( $settings['name'] !== '' && is_dir( self::extensionPath( $settings['name'] ) ) )
            $problems[] = 'extension/' . $settings['name'] . ' already exists. Choose another name, or remove it first.';

        if ( $settings['parts']['siteaccess'] && $settings['siteaccess'] === '' )
            $problems[] = 'Siteaccess settings were asked for, but no siteaccess was named.';

        return $problems;
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

        $value = trim( $value );

        return mb_strlen( $value, 'UTF-8' ) > $max ? mb_substr( $value, 0, $max, 'UTF-8' ) : $value;
    }

    /**
     * Every file the extension is made of, as path => contents.
     *
     * Paths are relative to the extension directory. Nothing is written here;
     * the caller decides whether this becomes an archive, a preview, or files
     * on disk.
     *
     * @param array $settings from settings().
     * @return array
     */
    public static function files( array $settings )
    {
        $name  = $settings['name'];
        $parts = $settings['parts'];
        $files = array();

        $files['settings/design.ini.append.php'] = self::designIni( $settings );

        if ( $parts['overrides'] )
        {
            $files['settings/override.ini.append.php'] = self::overrideIni( $settings );
            $files['design/' . $name . '/override/templates/full/article.tpl'] = self::exampleOverride( $settings );
        }

        if ( $parts['pagelayout'] )
            $files['design/' . $name . '/templates/pagelayout.tpl'] = self::pageLayout( $settings );

        if ( $parts['page_parts'] )
        {
            $files['design/' . $name . '/templates/page_head.tpl']   = self::pageHead( $settings );
            $files['design/' . $name . '/templates/page_header.tpl'] = self::pageHeader( $settings );
            $files['design/' . $name . '/templates/page_footer.tpl'] = self::pageFooter( $settings );
        }

        if ( $parts['stylesheets'] )
            $files['design/' . $name . '/stylesheets/site.css'] = self::siteCss( $settings );

        if ( $parts['print_stylesheet'] )
            $files['design/' . $name . '/stylesheets/print.css'] = self::printCss( $settings );

        if ( $parts['javascript'] )
            $files['design/' . $name . '/javascript/site.js'] = self::siteJs( $settings );

        if ( $parts['images'] )
            $files['design/' . $name . '/images/.gitkeep'] = "";

        if ( $parts['siteaccess'] && $settings['siteaccess'] !== '' )
            $files['settings/siteaccess/' . $settings['siteaccess'] . '/site.ini.append.php'] = self::siteaccessIni( $settings );

        if ( $parts['autoloads'] )
        {
            $files['autoloads/' . $name . 'operators.php'] = self::operatorClass( $settings );
            $files['settings/site.ini.append.php'] = self::operatorIni( $settings );
        }

        if ( $parts['ezinfo'] )
            $files['ezinfo.php'] = self::ezinfo( $settings );

        if ( $parts['extension_xml'] )
            $files['extension.xml'] = self::extensionXml( $settings );

        if ( $parts['composer'] )
            $files['composer.json'] = self::composerJson( $settings );

        if ( $parts['gitignore'] )
            $files['.gitignore'] = self::gitignore( $settings );

        if ( $parts['licence'] )
            $files['LICENSE'] = self::licence( $settings );

        ksort( $files );

        // Last, because it lists everything above it.
        if ( $parts['readme'] )
        {
            $files['README.md'] = self::readme( $settings, array_keys( $files ) );
            ksort( $files );
        }

        return $files;
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

    /**
     * The lines that switch the extension on, for the page to show.
     *
     * @param array $settings
     * @return string
     */
    public static function activation( array $settings )
    {
        return "[ExtensionSettings]\nActiveExtensions[]=" . $settings['name'];
    }

    // ── The files themselves ─────────────────────────────────────────────────

    protected static function iniHeader( array $settings, $what )
    {
        return "<?php /* #?ini charset=\"utf-8\"?\n\n"
             . "#\n# " . $what . " for the " . $settings['title'] . " design.\n"
             . "#\n# Generated by the design extension wizard. Edit freely.\n#\n\n";
    }

    protected static function designIni( array $settings )
    {
        $name = $settings['name'];

        $ini = self::iniHeader( $settings, 'Design settings' );
        $ini .= "[ExtensionSettings]\n";
        $ini .= "# Puts design/" . $name . " into the design chain of every siteaccess\n";
        $ini .= "# this extension is active for.\n";
        $ini .= "DesignExtensions[]=" . $name . "\n\n";

        if ( $settings['parts']['stylesheets'] || $settings['parts']['print_stylesheet'] )
        {
            $ini .= "[StylesheetSettings]\n";
            $ini .= "# Loaded on every page without a template having to name them.\n";
            if ( $settings['parts']['stylesheets'] )
                $ini .= "CSSFileList[]=site.css\n";
            if ( $settings['parts']['print_stylesheet'] )
                $ini .= "# print.css is loaded by page_head.tpl with media=\"print\" instead,\n"
                      . "# because this list has no way to say which medium a file is for.\n";
            $ini .= "\n";
        }

        if ( $settings['parts']['javascript'] )
        {
            $ini .= "[JavaScriptSettings]\n";
            $ini .= "JavaScriptList[]=site.js\n\n";
        }

        $ini .= "*/ ?>\n";

        return $ini;
    }

    protected static function overrideIni( array $settings )
    {
        $name = $settings['name'];

        $ini = self::iniHeader( $settings, 'Template overrides' );
        $ini .= "# An override replaces a template for the content it matches, and nothing\n";
        $ini .= "# else. Source is the template being replaced, MatchFile is the one that\n";
        $ini .= "# replaces it, and every Match line has to be true for it to be used.\n\n";
        $ini .= "[full_article_" . $name . "]\n";
        $ini .= "Source=node/view/full.tpl\n";
        $ini .= "MatchFile=full/article.tpl\n";
        $ini .= "Subdir=templates\n";
        $ini .= "Match[class_identifier]=article\n\n";
        $ini .= "*/ ?>\n";

        return $ini;
    }

    protected static function siteaccessIni( array $settings )
    {
        $ini = self::iniHeader( $settings, 'Settings for the ' . $settings['siteaccess'] . ' siteaccess' );
        $ini .= "[DesignSettings]\n";
        $ini .= "# The design this siteaccess draws with, and what it falls back on.\n";
        $ini .= "SiteDesign=" . $settings['name'] . "\n";
        $ini .= "AdditionalSiteDesignList[]=" . $settings['base_design'] . "\n\n";
        $ini .= "*/ ?>\n";

        return $ini;
    }

    protected static function operatorIni( array $settings )
    {
        $name = $settings['name'];

        $ini = self::iniHeader( $settings, 'Template operators' );
        $ini .= "[TemplateSettings]\n";
        $ini .= "ExtensionAutoloadPath[]=" . $name . "\n\n";
        $ini .= "*/ ?>\n";

        return $ini;
    }

    protected static function pageLayout( array $settings )
    {
        $name = $settings['name'];

        return "{*\n"
             . "  " . $settings['title'] . " - the frame every page is drawn inside.\n\n"
             . "  \$module_result holds what the module produced. Everything around it is\n"
             . "  this design's own.\n"
             . "*}\n"
             . "<!DOCTYPE html>\n"
             . "<html lang=\"{ezini( 'RegionalSettings', 'ContentObjectLocale' )|extract_left( 2 )}\">\n"
             . "<head>\n"
             . "{include uri='design:page_head.tpl'}\n"
             . "</head>\n"
             . "<body class=\"" . $name . " {\$module_result.node_id|wash}\">\n\n"
             . "{include uri='design:page_header.tpl'}\n\n"
             . "<main id=\"content\" class=\"" . $name . "-content\">\n"
             . "{\$module_result.content}\n"
             . "</main>\n\n"
             . "{include uri='design:page_footer.tpl'}\n\n"
             . "{* Anything a template asked to be put at the end of the body. *}\n"
             . "{ezscript_require( array( 'ezjsc::jquery' ) )}\n"
             . "</body>\n"
             . "</html>\n";
    }

    protected static function pageHead( array $settings )
    {
        $head = "{* What goes in the head of every page: the title, the character set,\n"
              . "   and the files design.ini says this design loads. *}\n"
              . "<meta charset=\"{ezini( 'RegionalSettings', 'ContentObjectLocale' )|extract_left( 0 )}utf-8\" />\n"
              . "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\" />\n"
              . "<title>{\$module_result.path|reverse|implode_key( 'text', ' / ' )|wash} - {ezini( 'SiteSettings', 'SiteName' )|wash}</title>\n\n"
              . "{ezcss_require( ezini( 'StylesheetSettings', 'CSSFileList' ) )}\n";

        if ( $settings['parts']['print_stylesheet'] )
            $head .= "<link rel=\"stylesheet\" type=\"text/css\" media=\"print\" href={'print.css'|ezdesign} />\n";

        $head .= "\n{* An rss autodiscovery line, if this site publishes one. *}\n"
               . "{*<link rel=\"alternate\" type=\"application/rss+xml\" title=\"{ezini( 'SiteSettings', 'SiteName' )|wash}\" href={'/rss/feed/my_feed'|ezurl} />*}\n";

        return $head;
    }

    protected static function pageHeader( array $settings )
    {
        $name = $settings['name'];

        return "{* The top of every page. *}\n"
             . "<header class=\"" . $name . "-header\">\n"
             . "    <a class=\"" . $name . "-logo\" href={'/'|ezurl}>{ezini( 'SiteSettings', 'SiteName' )|wash}</a>\n\n"
             . "    {* The top level of the content tree, as a menu. *}\n"
             . "    {def \$" . $name . "_top=fetch( 'content', 'list',\n"
             . "                              hash( 'parent_node_id', 2,\n"
             . "                                    'sort_by', array( 'priority', true() ) ) )}\n"
             . "    <nav class=\"" . $name . "-menu\">\n"
             . "    <ul>\n"
             . "    {foreach \$" . $name . "_top as \$" . $name . "_item}\n"
             . "        <li><a href={\$" . $name . "_item.url_alias|ezurl}>{\$" . $name . "_item.name|wash}</a></li>\n"
             . "    {/foreach}\n"
             . "    </ul>\n"
             . "    </nav>\n"
             . "    {undef \$" . $name . "_top}\n"
             . "</header>\n";
    }

    protected static function pageFooter( array $settings )
    {
        $name = $settings['name'];

        return "{* The bottom of every page. *}\n"
             . "<footer class=\"" . $name . "-footer\">\n"
             . "    <p>{ezini( 'SiteSettings', 'SiteName' )|wash} &mdash; {currentdate()|l10n( 'year' )}</p>\n"
             . "</footer>\n";
    }

    protected static function exampleOverride( array $settings )
    {
        return "{* An article, drawn by this design rather than by the kernel.\n\n"
             . "   override.ini decides when this is used: Source names the template it\n"
             . "   replaces, and every Match line has to be true. Change the matching\n"
             . "   there, not here. *}\n"
             . "<article class=\"" . $settings['name'] . "-article\">\n"
             . "    <h1>{\$node.name|wash}</h1>\n\n"
             . "    {if \$node.object.data_map.intro.has_content}\n"
             . "    <div class=\"intro\">{attribute_view_gui attribute=\$node.object.data_map.intro}</div>\n"
             . "    {/if}\n\n"
             . "    {if \$node.object.data_map.body.has_content}\n"
             . "    <div class=\"body\">{attribute_view_gui attribute=\$node.object.data_map.body}</div>\n"
             . "    {/if}\n\n"
             . "    <p class=\"published\">{\$node.object.published|l10n( shortdate )}</p>\n"
             . "</article>\n";
    }

    protected static function siteCss( array $settings )
    {
        $name = $settings['name'];

        return "/*\n * " . $settings['title'] . "\n *\n * Loaded on every page by design.ini. Start here.\n */\n\n"
             . ":root {\n"
             . "    --" . $name . "-ink: #1c1c1e;\n"
             . "    --" . $name . "-muted: #6a6a72;\n"
             . "    --" . $name . "-line: #e2e2e6;\n"
             . "    --" . $name . "-accent: #2d6cdf;\n"
             . "    --" . $name . "-gap: 1.5rem;\n"
             . "}\n\n"
             . "body." . $name . " {\n"
             . "    margin: 0;\n"
             . "    color: var(--" . $name . "-ink);\n"
             . "    font: 16px/1.6 system-ui, -apple-system, \"Segoe UI\", Roboto, sans-serif;\n"
             . "}\n\n"
             . "." . $name . "-header,\n"
             . "." . $name . "-footer,\n"
             . "." . $name . "-content { max-width: 70rem; margin: 0 auto; padding: var(--" . $name . "-gap); }\n\n"
             . "." . $name . "-header { display: flex; flex-wrap: wrap; align-items: center; gap: var(--" . $name . "-gap); }\n"
             . "." . $name . "-logo { font-weight: 700; text-decoration: none; color: inherit; }\n"
             . "." . $name . "-menu ul { display: flex; flex-wrap: wrap; gap: 1rem; list-style: none; margin: 0; padding: 0; }\n"
             . "." . $name . "-menu a { color: var(--" . $name . "-accent); text-decoration: none; }\n"
             . "." . $name . "-menu a:hover { text-decoration: underline; }\n\n"
             . "." . $name . "-footer { border-top: 1px solid var(--" . $name . "-line); color: var(--" . $name . "-muted); }\n\n"
             . "img { max-width: 100%; height: auto; }\n";
    }

    protected static function printCss( array $settings )
    {
        $name = $settings['name'];

        return "/*\n * " . $settings['title'] . ", on paper.\n *\n"
             . " * Loaded by page_head.tpl with media=\"print\".\n */\n\n"
             . "." . $name . "-header nav,\n"
             . "." . $name . "-footer { display: none; }\n\n"
             . "body." . $name . " { font: 11pt/1.4 Georgia, \"Times New Roman\", serif; color: #000; }\n"
             . "a[href]:after { content: \" (\" attr(href) \")\"; font-size: .85em; }\n";
    }

    protected static function siteJs( array $settings )
    {
        $name = $settings['name'];

        return "/*\n * " . $settings['title'] . "\n *\n"
             . " * Loaded on every page by design.ini. The page works without it; anything\n"
             . " * here should only make it nicer.\n */\n\n"
             . "( function () {\n"
             . "    'use strict';\n\n"
             . "    // Example: mark the menu entry for the page being looked at.\n"
             . "    var links = document.querySelectorAll( '." . $name . "-menu a' ), i;\n\n"
             . "    for ( i = 0; i < links.length; i++ )\n"
             . "    {\n"
             . "        if ( links[i].pathname === window.location.pathname )\n"
             . "            links[i].className += ' is-current';\n"
             . "    }\n"
             . "} )();\n";
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

    protected static function operatorClass( array $settings )
    {
        $name  = $settings['name'];
        $class = $name . 'Operators';

        return "<?php\n"
             . "/**\n"
             . " * Template operators for the " . $settings['title'] . " design.\n"
             . " *\n"
             . " * Registered through ExtensionAutoloadPath in settings/site.ini.append.php.\n"
             . " * Use one from a template as {\$value|" . $name . "_excerpt( 40 )}.\n"
             . " *\n"
             . self::licenceNotice( $settings )
             . " */\n\n"
             . "class " . $class . "\n"
             . "{\n"
             . "    protected \$Operators;\n\n"
             . "    function __construct()\n"
             . "    {\n"
             . "        \$this->Operators = array( '" . $name . "_excerpt' );\n"
             . "    }\n\n"
             . "    function operatorList()\n"
             . "    {\n"
             . "        return \$this->Operators;\n"
             . "    }\n\n"
             . "    function namedParameterPerOperator()\n"
             . "    {\n"
             . "        return true;\n"
             . "    }\n\n"
             . "    function namedParameterList()\n"
             . "    {\n"
             . "        return array(\n"
             . "            '" . $name . "_excerpt' => array(\n"
             . "                'length' => array( 'type' => 'integer', 'required' => false, 'default' => 40 ) ) );\n"
             . "    }\n\n"
             . "    function modify( \$tpl, \$operatorName, \$operatorParameters, \$rootNamespace,\n"
             . "                     \$currentNamespace, &\$operatorValue, \$namedParameters )\n"
             . "    {\n"
             . "        switch ( \$operatorName )\n"
             . "        {\n"
             . "            case '" . $name . "_excerpt':\n"
             . "            {\n"
             . "                \$length = (int) \$namedParameters['length'];\n"
             . "                \$text   = trim( strip_tags( (string) \$operatorValue ) );\n\n"
             . "                if ( \$length > 0 && mb_strlen( \$text, 'UTF-8' ) > \$length )\n"
             . "                    \$text = rtrim( mb_substr( \$text, 0, \$length, 'UTF-8' ) ) . '...';\n\n"
             . "                \$operatorValue = \$text;\n"
             . "            } break;\n"
             . "        }\n"
             . "    }\n"
             . "}\n";
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

    protected static function readme( array $settings, array $paths = array() )
    {
        $name  = $settings['name'];

        $readme = "# " . $settings['title'] . "\n\n" . $settings['summary'] . "\n\n"
                . "A design extension for Exponential / eZ Publish legacy.\n\n"
                . "## Switching it on\n\n"
                . "Add it to the active extensions in `settings/override/site.ini.append.php`:\n\n"
                . "```ini\n" . self::activation( $settings ) . "\n```\n\n"
                . "Then clear the caches:\n\n"
                . "```sh\nphp bin/php/ezcache.php --clear-all\n```\n\n"
                . "`settings/design.ini.append.php` puts `design/" . $name . "` into the design\n"
                . "chain, so templates and images are found without anything else being set.\n\n";

        if ( $settings['parts']['siteaccess'] && $settings['siteaccess'] !== '' )
        {
            $readme .= "## Making it the design of a site\n\n"
                     . "`settings/siteaccess/" . $settings['siteaccess'] . "/site.ini.append.php` sets this design\n"
                     . "as the one that siteaccess draws with, falling back on `"
                     . $settings['base_design'] . "`.\n\n";
        }

        $readme .= "## What is inside\n\n";
        foreach ( $paths as $path )
            $readme .= "- `" . $path . "`\n";

        $readme .= "\n## Licence\n\n" . self::licenceLine( $settings ) . "\n\n"
                 . "## Overriding a template\n\n"
                 . "Templates in `design/" . $name . "/templates` replace the kernel's by name.\n"
                 . "To replace one for particular content instead, add a block to\n"
                 . "`settings/override.ini.append.php` and put the template under\n"
                 . "`design/" . $name . "/override/templates`.\n";

        return $readme;
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

    // ── Doing something with them ────────────────────────────────────────────

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
        $problems = self::problems( $settings );
        if ( count( $problems ) )
            return array( 'ok' => false, 'message' => implode( ' ', $problems ), 'written' => array() );

        $target = self::extensionPath( $settings['name'] );
        $root   = self::installationRoot() . '/extension/';

        // Belt and braces: the name has been through safeName, and the result
        // still has to sit inside the extension directory.
        if ( strpos( $target, $root ) !== 0 )
            return array( 'ok' => false, 'message' => 'That would write outside extension/.', 'written' => array() );

        $files = self::files( $settings );

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

        $problems = self::problems( $settings );

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

        foreach ( self::files( $settings ) as $relative => $contents )
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
        foreach ( self::parts() as $key => $part )
            if ( !empty( $settings['parts'][$key] ) )
                $chosen[] = $key;

        return $chosen;
    }
}
