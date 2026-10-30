<?php
/**
 * File containing the expDesignExtensionWizard class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */


if ( !class_exists( 'expDesignExtensionWizard', false ) ) {
/**
 * Builds a design extension: the directories, the settings and the templates
 * that a working design needs, ready to switch on.
 *
 * Nothing here writes anything until it is asked to. The files are produced as
 * a path => contents map, which the page can show, put in an archive, or write
 * into extension/ - and which a test can read without a web server anywhere
 * near it.
 */
class expDesignExtensionWizard extends expExtensionWizard
{
    /**
     * What wrote the files, for the head of each one.
     *
     * @return string
     */
    public static function wizardName()
    {
        return 'design extension wizard';
    }
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
                'description' => 'What the admin interface reads to show the extension name, version and license.',
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
                'description' => 'The license text named below. On by default: an extension with no license file says nothing about how it may be used.',
                'default' => true ),
        );
    }

    /**
     * The designs a new one can fall back on, in the order eZ would try them.
     *
     * @return array key => label
     */
    public static function baseDesigns()
    {
        $bases = array( 'standard' => ezpI18n::tr( 'kernel/setup/rad', 'standard - the kernel templates, and nothing else' ) );

        foreach ( array( 'admin3', 'admin2', 'admin', 'base', 'ezwebin' ) as $design )
        {
            if ( is_dir( 'design/' . $design ) )
                $bases[$design] = ezpI18n::tr( 'kernel/setup/rad', '%design - fall back on this design first', null, array( '%design' => $design ) );
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
            $problems[] = ezpI18n::tr( 'kernel/setup/rad', 'The extension needs a name: lower case letters, digits and underscores, three to forty one characters, starting with a letter.' );

        // An "already exists" problem is filed under a key starting with
        // "exists": archive() leaves those out, in whatever language they read.
        if ( $settings['name'] !== '' && is_dir( self::extensionPath( $settings['name'] ) ) )
            $problems['exists'] = ezpI18n::tr( 'kernel/setup/rad', 'extension/%name already exists. Choose another name, or remove it first.', null, array( '%name' => $settings['name'] ) );

        if ( $settings['parts']['siteaccess'] && $settings['siteaccess'] === '' )
            $problems[] = ezpI18n::tr( 'kernel/setup/rad', 'Siteaccess settings were asked for, but no siteaccess was named.' );

        // The licence is a strict choice from package.ini [LicenseSettings], checked here as well
        // as in the form, so a posted value that is not configured is never written
        $problems += self::licenceProblems( $settings );

        return $problems;
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
            $files['autoloads/eztemplateautoload.php'] = self::operatorAutoload( $settings );
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

    protected static function designIni( array $settings )
    {
        $name = $settings['name'];

        $ini = self::iniHeader( $settings, 'Design settings' );
        $ini .= "[ExtensionSettings]\n";
        $ini .= "# Makes the designs of this extension searchable: design/<design> here is\n";
        $ini .= "# used for every design in a siteaccess's chain. design/" . $name . " is used\n";
        $ini .= "# only by a siteaccess that names it in SiteDesign or\n";
        $ini .= "# AdditionalSiteDesignList[].\n";
        $ini .= "DesignExtensions[]=" . $name . "\n\n";

        if ( $settings['parts']['stylesheets'] || $settings['parts']['print_stylesheet'] )
        {
            $ini .= "[StylesheetSettings]\n";
            $ini .= "# Loaded on every page without a template having to name them.\n";
            $ini .= "# FrontendCSSFileList, not the deprecated CSSFileList.\n";
            if ( $settings['parts']['stylesheets'] )
                $ini .= "FrontendCSSFileList[]=site.css\n";
            if ( $settings['parts']['print_stylesheet'] )
                $ini .= "# print.css is loaded by page_head.tpl with media=\"print\" instead,\n"
                      . "# because this list has no way to say which medium a file is for.\n";
            $ini .= "\n";
        }

        if ( $settings['parts']['javascript'] )
        {
            $ini .= "[JavaScriptSettings]\n";
            $ini .= "# FrontendJavaScriptList, not the deprecated JavaScriptList.\n";
            $ini .= "FrontendJavaScriptList[]=site.js\n\n";
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
        $ini .= "# The template engine reads autoloads/eztemplateautoload.php of every\n";
        $ini .= "# extension named here.\n";
        $ini .= "ExtensionAutoloadPath[]=" . $name . "\n\n";
        $ini .= "*/ ?>\n";

        return $ini;
    }

    /**
     * autoloads/eztemplateautoload.php: the file the template engine includes from every ExtensionAutoloadPath[],
     * and the only place an operator is registered. Without it the operator class is never loaded.
     *
     * @param array $settings
     * @return string
     */
    protected static function operatorAutoload( array $settings )
    {
        $name = $settings['name'];

        return "<?php\n"
             . "/**\n"
             . " * The template operators of the " . self::commentText( $settings['title'] ) . " design.\n"
             . " *\n"
             . " * The template engine includes this file from every extension listed in\n"
             . " * [TemplateSettings] ExtensionAutoloadPath[] (settings/site.ini.append.php).\n"
             . " * The script path is from the installation root.\n"
             . " *\n"
             . self::licenceNotice( $settings )
             . " */\n\n"
             . "\$eZTemplateOperatorArray = array();\n\n"
             . "\$eZTemplateOperatorArray[] = array(\n"
             . "    'script' => 'extension/" . self::phpString( $name ) . "/autoloads/" . self::phpString( $name ) . "operators.php',\n"
             . "    'class'  => '" . self::phpString( $name ) . "Operators',\n"
             . "    'operator_names' => array( '" . self::phpString( $name ) . "_excerpt' ) );\n";
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
             . "{* The scripts: jQuery, the FrontendJavaScriptList of design.ini\n"
             . "   (and the older JavaScriptList, which other extensions may still fill), and whatever a\n"
             . "   template on this page asked for with ezscript_require(). They are\n"
             . "   printed here, at the end of the body, so every template has had its say. *}\n"
             . "{ezscript_load( array( 'ezjsc::jquery',\n"
             . "                       ezini( 'JavaScriptSettings', 'JavaScriptList', 'design.ini' ),\n"
             . "                       ezini( 'JavaScriptSettings', 'FrontendJavaScriptList', 'design.ini' ) ) )}\n"
             . "</body>\n"
             . "</html>\n";
    }

    protected static function pageHead( array $settings )
    {
        $head = "{* What goes in the head of every page: the title, the character set,\n"
              . "   and the files design.ini says this design loads. *}\n"
              . "<meta charset=\"utf-8\" />\n"
              . "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\" />\n"
              . "<title>{foreach \$module_result.path|reverse as \$path_item}{\$path_item.text|wash}{delimiter} / {/delimiter}{/foreach} - {ezini( 'SiteSettings', 'SiteName' )|wash}</title>\n\n"
              . "{* The stylesheets of design.ini, and whatever a template asked for with\n"
              . "   ezcss_require() before this point. *}\n"
              . "{ezcss_load( array( ezini( 'StylesheetSettings', 'CSSFileList', 'design.ini' ),\n"
              . "                    ezini( 'StylesheetSettings', 'FrontendCSSFileList', 'design.ini' ) ) )}\n";

        if ( $settings['parts']['print_stylesheet'] )
            $head .= "<link rel=\"stylesheet\" type=\"text/css\" media=\"print\" href={'stylesheets/print.css'|ezdesign} />\n";

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

    protected static function operatorClass( array $settings )
    {
        $name  = $settings['name'];
        $class = $name . 'Operators';

        return "<?php\n"
             . "/**\n"
             . " * Template operators for the " . $settings['title'] . " design.\n"
             . " *\n"
             . " * Registered in autoloads/eztemplateautoload.php, which the template engine\n"
             . " * reads because settings/site.ini.append.php names this extension in\n"
             . " * ExtensionAutoloadPath[].\n"
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
                . "`settings/design.ini.append.php` names this extension in `DesignExtensions[]`,\n"
                . "so the designs of this extension are searched. That alone does not make a\n"
                . "siteaccess draw with `design/" . $name . "`: a design is used only when a\n"
                . "siteaccess names it.\n\n"
                . "## Making it the design of a site\n\n";

        if ( $settings['parts']['siteaccess'] && $settings['siteaccess'] !== '' )
        {
            $readme .= "`settings/siteaccess/" . $settings['siteaccess'] . "/site.ini.append.php` sets this design\n"
                     . "as the one that siteaccess draws with, falling back on `"
                     . $settings['base_design'] . "`.\n\n";
        }
        else
        {
            $readme .= "Name it in the `site.ini.append.php` of the siteaccess that should use it\n"
                     . "(`settings/siteaccess/<siteaccess>/site.ini.append.php`):\n\n"
                     . "```ini\n[DesignSettings]\nSiteDesign=" . $name . "\nAdditionalSiteDesignList[]="
                     . $settings['base_design'] . "\n```\n\n"
                     . "Or add `AdditionalSiteDesignList[]=" . $name . "` to keep the current\n"
                     . "design and let this one override some of its templates.\n\n";
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

    // ── Doing something with them ────────────────────────────────────────────

}
}

require_once 'kernel/setup/expextensionwizard.php';

