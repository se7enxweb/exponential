<?php
/**
 * The module wizard: a module, its views, and the policies that reach them.
 *
 * A module is how this system serves a page that is not content. It is three
 * things that have to agree with each other:
 *
 *   a module.php    declaring the views and the policies
 *   a view script   per view, setting $Result
 *   a template      per view, drawing it
 *
 * plus the two ini lines that make the kernel look for any of it. When they
 * disagree the failure is quiet: a view with no script is a blank page, a view
 * naming a policy no module declares can be reached by nobody, and a module
 * the ini does not list is not there at all.
 *
 * The module extension wizard beside this one builds a module over database
 * tables and writes the persistent object classes with it. This one is for the
 * other case: a module that does something, with nothing to store.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */

require_once 'kernel/setup/expextensionwizard.php';

class expModuleWizard extends expExtensionWizard
{
    /**
     * What wrote the files, for the head of each one.
     *
     * @return string
     */
    public static function wizardName()
    {
        return 'module wizard';
    }

    // ── What a view can be ───────────────────────────────────────────────────

    /**
     * Where in the admin a view puts itself.
     *
     * @return array
     */
    public static function contexts()
    {
        return array(
            'administration' => 'An administration page, with the admin toolbar and menus around it.',
            'edit'           => 'An editing page. The admin drops most of its furniture so the form has the screen.',
            'browse'         => 'A browse page, for choosing something and coming back with it.',
            ''               => 'Neither. A page on the site itself rather than in the admin.',
        );
    }

    /**
     * The parts of the admin a view can belong to, read off this installation.
     *
     * @return array
     */
    public static function navigationParts()
    {
        $parts = array();

        foreach ( eZINI::instance( 'menu.ini' )->groups() as $group => $settings )
            if ( isset( $settings['NavigationPartIdentifier'] ) && is_string( $settings['NavigationPartIdentifier'] ) )
                $parts[$settings['NavigationPartIdentifier']] = $settings['NavigationPartIdentifier'];

        // The ones that always exist, whether menu.ini mentions them or not.
        foreach ( array( 'ezcontentnavigationpart', 'ezsetupnavigationpart', 'ezusernavigationpart',
                         'ezmedianavigationpart', 'ezmynavigationpart', 'ezshopnavigationpart',
                         'ezvisualnavigationpart' ) as $part )
            $parts[$part] = $part;

        ksort( $parts );

        return $parts;
    }

    /**
     * The limitations a policy can carry, and where each gets its values.
     *
     * A policy with no limitation is granted or not. A policy with one is
     * granted for some content and not for other content, and the values a
     * role editor may choose from come from the class and method below.
     *
     * @return array
     */
    public static function limitations()
    {
        return array(
            'Section' => array(
                'name'     => 'Section',
                'class'    => 'eZSection',
                'function' => 'fetchList',
                'what'     => 'Which sections of content this applies to. The usual first limitation, and the cheapest to check.' ),
            'Class' => array(
                'name'     => 'Class',
                'class'    => 'eZContentClass',
                'function' => 'fetchList',
                'what'     => 'Which content classes. Granting a policy for articles and not for folders.' ),
            'Owner' => array(
                'name'     => 'Owner',
                'class'    => '',
                'function' => '',
                'what'     => 'Whether the user owns the content. Takes no list: the values are fixed at self and anyone.' ),
            'SiteAccess' => array(
                'name'     => 'SiteAccess',
                'class'    => 'eZSiteAccess',
                'function' => 'siteAccessList',
                'what'     => 'Which siteaccess the request came through. How a view is opened on the admin and closed on the public site.' ),
            'Language' => array(
                'name'     => 'Language',
                'class'    => 'eZContentLanguage',
                'function' => 'fetchLimitationList',
                'what'     => 'Which translations. Granting an editor one language and not another.' ),
        );
    }

    // ── What this wizard can put in ──────────────────────────────────────────

    /**
     * @return array
     */
    public static function parts()
    {
        return array(
            'module' => array(
                'label' => 'module.php',
                'description' => 'The declaration: every view, the policies each needs, the parameters each takes, and the policies the module offers a role.',
                'default' => true ),
            'views' => array(
                'label' => 'View scripts',
                'description' => 'One php file per view, each reading its parameters, checking what it was given, and setting $Result the way the kernel expects.',
                'default' => true ),
            'templates' => array(
                'label' => 'Templates',
                'description' => 'One template per view, drawing what the script put in front of it. Working templates rather than empty files.',
                'default' => true ),
            'settings' => array(
                'label' => 'Registration',
                'description' => 'module.ini so the kernel finds the module, and design.ini so it finds the templates. Without the second the module works and draws nothing.',
                'default' => true ),
            'menu' => array(
                'label' => 'Admin menu entry',
                'description' => 'menu.ini, so the module appears in the left hand menu of the part it belongs to rather than only at an address somebody has to know.',
                'default' => true ),
            'examples' => array(
                'label' => 'API examples',
                'description' => 'How a view is reached, what is in $Params, what $Result may carry, and how to check a policy from inside one.',
                'default' => true ),
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
                'description' => 'Every view, its address, and what reaches it.',
                'default' => true ),
            'gitignore' => array(
                'label' => '.gitignore',
                'description' => 'Keeps editor leftovers and build output out of the repository.',
                'default' => true ),
            'licence' => array(
                'label' => 'LICENSE',
                'description' => 'The licence text named below. On by default: an extension with no licence file says nothing about how it may be used.',
                'default' => true ),
        );
    }

    // ── Settings ─────────────────────────────────────────────────────────────

    /**
     * Everything the wizard was asked for.
     *
     * @param array $input
     * @return array
     */
    public static function settings( array $input )
    {
        $settings = array(
            'name'       => self::safeName( isset( $input['name'] ) ? $input['name'] : '' ),
            'module'     => self::safeIdentifier( isset( $input['module'] ) ? $input['module'] : '' ),
            'title'      => self::text( isset( $input['title'] ) ? $input['title'] : '', 120 ),
            'summary'    => self::text( isset( $input['summary'] ) ? $input['summary'] : '', 250 ),
            'author'     => self::text( isset( $input['author'] ) ? $input['author'] : '', 120 ),
            'vendor'     => self::safeName( isset( $input['vendor'] ) ? $input['vendor'] : '', true ),
            'version'    => self::text( isset( $input['version'] ) ? $input['version'] : '', 20 ),
            'licence'    => self::licence_id( isset( $input['licence'] ) ? $input['licence'] : '' ),
            'context'    => self::safeContext( isset( $input['context'] ) ? $input['context'] : '' ),
            'navigation' => self::safeIdentifier( isset( $input['navigation'] ) ? $input['navigation'] : '' ),
        );

        $settings['views']    = self::viewList( isset( $input['views'] ) ? $input['views'] : '' );
        $settings['policies'] = self::policyList( isset( $input['policies'] ) ? $input['policies'] : '' );

        if ( $settings['module'] === '' && $settings['name'] !== '' )
            $settings['module'] = self::safeIdentifier( $settings['name'] );
        if ( $settings['title'] === '' && $settings['module'] !== '' )
            $settings['title'] = ucwords( str_replace( '_', ' ', $settings['module'] ) );
        if ( $settings['version'] === '' )
            $settings['version'] = '1.0.0';
        if ( $settings['vendor'] === '' )
            $settings['vendor'] = 'exponential';
        if ( $settings['navigation'] === '' )
            $settings['navigation'] = 'ezsetupnavigationpart';
        if ( $settings['summary'] === '' )
            $settings['summary'] = 'A module for Exponential.';

        // Every policy a view asks for has to be one the module declares, or
        // nobody can be granted it and the view is reachable by nobody.
        $declared = array();
        foreach ( $settings['policies'] as $policy )
            $declared[] = $policy['name'];

        foreach ( $settings['views'] as $at => $view )
        {
            $settings['views'][$at]['undeclared'] = array_values(
                array_diff( $view['policies'], $declared ) );
        }

        $chosen = isset( $input['parts'] ) && is_array( $input['parts'] ) ? $input['parts'] : null;
        foreach ( self::parts() as $key => $part )
            $settings['parts'][$key] = $chosen === null ? $part['default'] : in_array( $key, $chosen, true );

        return $settings;
    }

    /**
     * The views, one per line: a name, then its policies and parameters.
     *
     * A word in capitals is an ordered parameter, a word in lower case is a
     * policy. That is not a convention this wizard invented: eZ writes ordered
     * parameters in capitals everywhere, and policies in lower case everywhere.
     *
     * @param mixed $value
     * @return array
     */
    public static function viewList( $value )
    {
        if ( !is_scalar( $value ) )
            return array();

        $views = array();
        $seen  = array();

        foreach ( preg_split( '/[\r\n]+/', (string) $value ) as $line )
        {
            $line = trim( $line );
            if ( $line === '' )
                continue;

            $colon = strpos( $line, ':' );
            $name  = self::safeIdentifier( $colon === false ? $line : substr( $line, 0, $colon ) );

            if ( $name === '' || isset( $seen[$name] ) )
                continue;

            $seen[$name] = true;

            $policies   = array();
            $parameters = array();
            $optional   = array();

            if ( $colon !== false )
                foreach ( preg_split( '/[\s,]+/', substr( $line, $colon + 1 ) ) as $word )
                {
                    $word = trim( $word );

                    if ( $word === '' )
                        continue;

                    // A trailing ? means the parameter may be left out.
                    $mayBeMissing = substr( $word, -1 ) === '?';
                    $word = $mayBeMissing ? substr( $word, 0, -1 ) : $word;

                    if ( preg_match( '/^[A-Z][A-Za-z0-9_]*$/', $word ) )
                    {
                        if ( $mayBeMissing )
                            $optional[] = $word;
                        else
                            $parameters[] = $word;

                        continue;
                    }

                    $policy = self::safeIdentifier( $word );

                    if ( $policy !== '' )
                        $policies[] = $policy;
                }

            $views[] = array(
                'name'       => $name,
                'script'     => $name . '.php',
                'policies'   => array_values( array_unique( $policies ) ),
                'parameters' => array_values( array_unique( $parameters ) ),
                'optional'   => array_values( array_unique( $optional ) ),
                'undeclared' => array() );

            if ( count( $views ) >= 30 )
                break;
        }

        return $views;
    }

    /**
     * The policies, one per line: a name, then its limitations.
     *
     * @param mixed $value
     * @return array
     */
    public static function policyList( $value )
    {
        if ( !is_scalar( $value ) )
            return array();

        $known    = self::limitations();
        $policies = array();
        $seen     = array();

        foreach ( preg_split( '/[\r\n]+/', (string) $value ) as $line )
        {
            $line = trim( $line );
            if ( $line === '' )
                continue;

            $colon = strpos( $line, ':' );
            $name  = self::safeIdentifier( $colon === false ? $line : substr( $line, 0, $colon ) );

            if ( $name === '' || isset( $seen[$name] ) )
                continue;

            $seen[$name] = true;

            $limitations = array();

            if ( $colon !== false )
                foreach ( preg_split( '/[\s,]+/', substr( $line, $colon + 1 ) ) as $word )
                {
                    $word = trim( $word );

                    // Matched without regard to case, because nobody remembers
                    // whether it is SiteAccess or siteaccess.
                    foreach ( $known as $key => $limitation )
                        if ( strcasecmp( $word, $key ) === 0 && !in_array( $key, $limitations, true ) )
                            $limitations[] = $key;
                }

            $policies[] = array( 'name' => $name, 'limitations' => $limitations );

            if ( count( $policies ) >= 30 )
                break;
        }

        return $policies;
    }

    /**
     * A lower case identifier: a module, a view, a policy.
     *
     * @param mixed $value
     * @return string
     */
    public static function safeIdentifier( $value )
    {
        if ( !is_scalar( $value ) )
            return '';

        $value = strtolower( preg_replace( '/[^A-Za-z0-9_]+/', '_', (string) $value ) );
        $value = trim( (string) $value, '_' );

        return preg_match( '/^[a-z][a-z0-9_]{0,60}$/', $value ) ? $value : '';
    }

    /**
     * One of the contexts the admin knows, or none.
     *
     * @param mixed $value
     * @return string
     */
    public static function safeContext( $value )
    {
        $contexts = self::contexts();

        return is_string( $value ) && isset( $contexts[$value] ) ? $value : 'administration';
    }

    /**
     * What is wrong with these settings.
     *
     * @param array $settings
     * @return array of string
     */
    public static function problems( array $settings )
    {
        $problems = array();

        if ( $settings['name'] === '' )
            $problems[] = 'The extension needs a name: lower case letters, digits and underscores, three to forty one characters, starting with a letter.';

        if ( $settings['name'] !== '' && is_dir( self::extensionPath( $settings['name'] ) ) )
            $problems[] = 'extension/' . $settings['name'] . ' already exists. Choose another name, or remove it first.';

        if ( $settings['module'] === '' )
            $problems[] = 'The module needs a name: lower case letters, digits and underscores, starting with a letter. It is the first part of every address this module answers.';

        if ( $settings['module'] !== '' && eZModule::exists( $settings['module'] ) !== null )
            $problems[] = 'A module called ' . $settings['module'] . ' already exists on this installation. Two modules of the same name cannot both answer; choose another.';

        if ( count( $settings['views'] ) === 0 )
            $problems[] = 'A module with no views answers nothing. Name at least one.';

        // A view asking for a policy the module does not declare cannot be
        // granted to anybody: the role editor has nothing to tick.
        foreach ( $settings['views'] as $view )
            foreach ( $view['undeclared'] as $policy )
                $problems[] = 'The view ' . $view['name'] . ' needs the policy ' . $policy
                            . ', and the module does not declare it. Nobody could be granted it, so nobody could reach the view. Add ' . $policy . ' to the policies below.';

        return $problems;
    }

    // ── What it writes ───────────────────────────────────────────────────────

    /**
     * Every file the extension is made of.
     *
     * @param array $settings
     * @return array
     */
    public static function files( array $settings )
    {
        if ( $settings['name'] === '' || $settings['module'] === '' || count( $settings['views'] ) === 0 )
            return array();

        $parts = $settings['parts'];
        $files = array();
        $base  = 'modules/' . $settings['module'] . '/';

        if ( $parts['module'] )
            $files[$base . 'module.php'] = self::moduleFile( $settings );

        if ( $parts['views'] )
            foreach ( $settings['views'] as $view )
                $files[$base . $view['script']] = self::viewScript( $settings, $view );

        if ( $parts['templates'] )
            foreach ( $settings['views'] as $view )
                $files['design/' . $settings['name'] . '/templates/' . $settings['module'] . '/'
                       . $view['name'] . '.tpl'] = self::viewTemplate( $settings, $view );

        if ( $parts['settings'] )
        {
            $files['settings/module.ini.append.php'] = self::moduleIni( $settings );
            $files['settings/design.ini.append.php'] = self::designIni( $settings );
        }

        if ( $parts['menu'] )
            $files['settings/menu.ini.append.php'] = self::menuIni( $settings );

        if ( $parts['examples'] )
            $files['doc/examples.php'] = self::examples( $settings );

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

        if ( $parts['readme'] )
        {
            $files['README.md'] = self::readme( $settings, array_keys( $files ) );
            ksort( $files );
        }

        return $files;
    }

    /**
     * The lines that switch the extension on.
     *
     * @param array $settings
     * @return string
     */
    public static function activation( array $settings )
    {
        return "[ExtensionSettings]\nActiveExtensions[]=" . $settings['name'];
    }

    /**
     * The address a view answers at.
     *
     * @param array $settings
     * @param array $view
     * @return string
     */
    public static function addressOf( array $settings, array $view )
    {
        $address = '/' . $settings['module'] . '/' . $view['name'];

        foreach ( $view['parameters'] as $parameter )
            $address .= '/<' . strtolower( $parameter ) . '>';

        foreach ( $view['optional'] as $parameter )
            $address .= '/(' . strtolower( $parameter ) . ')/<value>';

        return $address;
    }

    // ── module.php ───────────────────────────────────────────────────────────

    /**
     * The declaration.
     *
     * @param array $settings
     * @return string
     */
    protected static function moduleFile( array $settings )
    {
        $limitations = self::limitations();

        $php  = "<?php\n/**\n * The " . self::commentText( $settings['module'] ) . " module.\n *\n";
        $php .= " * " . wordwrap( $settings['summary'], 74, "\n * " ) . "\n *\n";
        $php .= " * Read rather than run: the kernel includes this file and looks at what it\n";
        $php .= " * declared. Nothing here executes on a request.\n *\n";
        $php .= self::licenceNotice( $settings );
        $php .= " */\n\n";

        $php .= "\$Module = array( 'name' => '" . self::phpString( $settings['title'] ) . "' );\n\n";

        $php .= "\$ViewList = array();\n";

        foreach ( $settings['views'] as $view )
        {
            $php .= "\n// " . self::addressOf( $settings, $view ) . "\n";
            $php .= "\$ViewList['" . self::phpString( $view['name'] ) . "'] = array(\n";
            $php .= "    'script' => '" . self::phpString( $view['script'] ) . "',\n";

            $php .= "    // What somebody needs before they reach this at all. An empty list\n";
            $php .= "    // means anybody who can reach the module can reach this view.\n";
            $php .= "    'functions' => array( ";
            $names = array();
            foreach ( $view['policies'] as $policy )
                $names[] = "'" . self::phpString( $policy ) . "'";
            $php .= implode( ', ', $names ) . " ),\n";

            if ( $settings['context'] !== '' )
                $php .= "    'ui_context' => '" . self::phpString( $settings['context'] ) . "',\n";

            $php .= "    'default_navigation_part' => '" . self::phpString( $settings['navigation'] ) . "',\n";

            if ( count( $view['parameters'] ) )
            {
                $php .= "    // Ordered, and part of the address: /" . self::commentText( $settings['module'] )
                      . "/" . self::commentText( $view['name'] );
                foreach ( $view['parameters'] as $parameter )
                    $php .= "/<" . strtolower( self::commentText( $parameter ) ) . ">";
                $php .= "\n";
                $php .= "    'params' => array( ";
                $names = array();
                foreach ( $view['parameters'] as $parameter )
                    $names[] = "'" . self::phpString( $parameter ) . "'";
                $php .= implode( ', ', $names ) . " ),\n";
            }

            if ( count( $view['optional'] ) )
            {
                $php .= "    // Named, so they may be left out and may be given in any order.\n";
                $php .= "    // Written as /(name)/value in the address.\n";
                $php .= "    'unordered_params' => array( ";
                $names = array();
                foreach ( $view['optional'] as $parameter )
                    $names[] = "'" . self::phpString( strtolower( $parameter ) ) . "' => '"
                             . self::phpString( $parameter ) . "'";
                $php .= implode( ",\n                                 ", $names ) . " ),\n";
            }

            $php .= ");\n";
        }

        $php .= "\n// The policies a role can grant on this module. Every one a view asks for\n";
        $php .= "// above has to be here, or nobody can be granted it and the view is\n";
        $php .= "// reachable by nobody.\n";
        $php .= "\$FunctionList = array();\n";

        foreach ( $settings['policies'] as $policy )
        {
            if ( count( $policy['limitations'] ) === 0 )
            {
                $php .= "\n// Granted or not, with nothing to narrow it by.\n";
                $php .= "\$FunctionList['" . self::phpString( $policy['name'] ) . "'] = array();\n";
                continue;
            }

            $php .= "\n\$FunctionList['" . self::phpString( $policy['name'] ) . "'] = array(\n";

            foreach ( $policy['limitations'] as $key )
            {
                $limitation = $limitations[$key];

                $php .= "    // " . wordwrap( $limitation['what'], 68, "\n    // " ) . "\n";
                $php .= "    '" . $key . "' => array(\n";
                $php .= "        'name'   => '" . self::phpString( $limitation['name'] ) . "',\n";
                $php .= "        'values' => array(),\n";

                if ( $limitation['class'] !== '' )
                {
                    $php .= "        // Where the role editor gets the list to choose from.\n";
                    $php .= "        'class'     => '" . self::phpString( $limitation['class'] ) . "',\n";
                    $php .= "        'function'  => '" . self::phpString( $limitation['function'] ) . "',\n";
                    $php .= "        'parameter' => array( false ) ),\n";
                }
                else
                {
                    $php .= "        'values_name' => 'name',\n";
                    $php .= "        'values_id'   => 'id' ),\n";
                }
            }

            $php .= ");\n";
        }

        return $php;
    }

    // ── A view script ────────────────────────────────────────────────────────

    /**
     * One view.
     *
     * @param array $settings
     * @param array $view
     * @return string
     */
    protected static function viewScript( array $settings, array $view )
    {
        $php  = "<?php\n/**\n * " . self::commentText( $settings['module'] ) . "/"
              . self::commentText( $view['name'] ) . "\n *\n";
        $php .= " * Reached at " . self::addressOf( $settings, $view ) . "\n";

        if ( count( $view['policies'] ) )
            $php .= " *\n * Needs " . implode( ', ', array_map( array( __CLASS__, 'commentText' ), $view['policies'] ) )
                  . " on this module. The kernel checks that before this\n"
                  . " * file is reached, so nothing here has to check it again - but a check of\n"
                  . " * your own is still needed for anything this view decides for itself.\n";

        $php .= " *\n";
        $php .= self::licenceNotice( $settings );
        $php .= " */\n\n";

        $php .= "\$Module = \$Params['Module'];\n";
        $php .= "\$tpl    = eZTemplate::factory();\n\n";

        if ( count( $view['parameters'] ) || count( $view['optional'] ) )
        {
            $php .= "// Everything in \$Params came out of the address, which means it came from\n";
            $php .= "// whoever typed it. The declared type is nothing: cast, and check.\n";

            foreach ( $view['parameters'] as $parameter )
            {
                $variable = lcfirst( $parameter );
                $php .= "\$" . $variable . " = isset( \$Params['" . self::phpString( $parameter ) . "'] )\n";
                $php .= "     " . str_repeat( ' ', strlen( $variable ) ) . " ? (int) \$Params['"
                      . self::phpString( $parameter ) . "'] : 0;\n";
            }

            foreach ( $view['optional'] as $parameter )
            {
                $variable = lcfirst( $parameter );
                $php .= "\$" . $variable . " = isset( \$Params['" . self::phpString( $parameter ) . "'] )\n";
                $php .= "     " . str_repeat( ' ', strlen( $variable ) ) . " ? (int) \$Params['"
                      . self::phpString( $parameter ) . "'] : 0;\n";
            }

            $php .= "\n";
        }

        if ( count( $view['parameters'] ) )
        {
            $first = $view['parameters'][0];
            $php .= "// A parameter that makes no sense is a 404 rather than a page saying so.\n";
            $php .= "// Anything else invites somebody to try every number in turn and read the\n";
            $php .= "// answers.\n";
            $php .= "if ( \$" . lcfirst( $first ) . " <= 0 )\n";
            $php .= "    return \$Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );\n\n";
        }

        $php .= "// Not written yet. Whatever is put here is what the template is given.\n";
        $php .= "\$tpl->setVariable( 'module', \$Module );\n";

        foreach ( array_merge( $view['parameters'], $view['optional'] ) as $parameter )
            $php .= "\$tpl->setVariable( '" . self::phpString( strtolower( $parameter ) ) . "', \$"
                  . lcfirst( $parameter ) . " );\n";

        $php .= "\n";
        $php .= "\$Result = array();\n";
        $php .= "\$Result['content'] = \$tpl->fetch( 'design:" . self::phpString( $settings['module'] )
              . "/" . self::phpString( $view['name'] ) . ".tpl' );\n\n";
        $php .= "// The trail at the top of the page. The last one has no url because it is\n";
        $php .= "// where you already are.\n";
        $php .= "\$Result['path'] = array( array( 'url'  => false,\n";
        $php .= "                                'text' => ezpI18n::tr( 'extension/"
              . self::phpString( $settings['name'] ) . "', '"
              . self::phpString( ucwords( str_replace( '_', ' ', $view['name'] ) ) ) . "' ) ) );\n";

        return $php;
    }

    /**
     * The template one view is drawn with.
     *
     * @param array $settings
     * @param array $view
     * @return string
     */
    protected static function viewTemplate( array $settings, array $view )
    {
        $title = ucwords( str_replace( '_', ' ', $view['name'] ) );

        $tpl  = "{* " . self::commentText( $settings['module'] ) . "/" . self::commentText( $view['name'] ) . "\n\n";
        $tpl .= "   Drawn by " . self::commentText( $view['script'] ) . ". Everything below came out of\n";
        $tpl .= "   setVariable() there, and anything that came from the address is washed\n";
        $tpl .= "   here whatever the script did with it. *}\n\n";

        $tpl .= "<div class=\"context-block\">\n\n";
        $tpl .= "{* DESIGN: Header START *}<div class=\"box-header\"><div class=\"box-ml\">\n";
        $tpl .= "<h1 class=\"context-title\">{'" . str_replace( "'", "\\'", $title )
              . "'|i18n( 'extension/" . $settings['name'] . "' )}</h1>\n";
        $tpl .= "{* DESIGN: Mainline *}<div class=\"header-mainline\"></div>\n";
        $tpl .= "{* DESIGN: Header END *}</div></div>\n\n";

        $tpl .= "{* DESIGN: Content START *}<div class=\"box-ml\"><div class=\"box-mr\"><div class=\"box-content\">\n\n";

        $parameters = array_merge( $view['parameters'], $view['optional'] );

        if ( count( $parameters ) )
        {
            $tpl .= "<div class=\"context-attributes\">\n";
            foreach ( $parameters as $parameter )
                $tpl .= "<p>" . ucwords( str_replace( '_', ' ', strtolower( $parameter ) ) )
                      . ": {\$" . strtolower( $parameter ) . "|wash}</p>\n";
            $tpl .= "</div>\n\n";
        }

        $tpl .= "<p>{'Nothing here yet.'|i18n( 'extension/" . $settings['name'] . "' )}</p>\n\n";
        $tpl .= "{* DESIGN: Content END *}</div></div></div>\n\n";
        $tpl .= "</div>\n";

        return $tpl;
    }

    // ── Registration ─────────────────────────────────────────────────────────

    /**
     * module.ini: where to look, and what to look for.
     *
     * @param array $settings
     * @return string
     */
    protected static function moduleIni( array $settings )
    {
        $ini  = self::iniHeader( $settings, 'Module registration' );
        $ini .= "[ModuleSettings]\n";
        $ini .= "# Both lines are needed. The first says which extension to look in, the\n";
        $ini .= "# second what to look for. With only the first the module is not found; with\n";
        $ini .= "# only the second the kernel looks for it in the kernel and reports it\n";
        $ini .= "# missing.\n";
        $ini .= "ExtensionRepositories[]=" . $settings['name'] . "\n";
        $ini .= "ModuleList[]=" . $settings['module'] . "\n";
        $ini .= "\n*/ ?>\n";

        return $ini;
    }

    /**
     * design.ini: where the templates are.
     *
     * @param array $settings
     * @return string
     */
    protected static function designIni( array $settings )
    {
        $ini  = self::iniHeader( $settings, 'Design settings' );
        $ini .= "[ExtensionSettings]\n";
        $ini .= "# Where the templates for this module are. Without this line every view\n";
        $ini .= "# runs, finds no template, and answers with an empty page.\n";
        $ini .= "DesignExtensions[]=" . $settings['name'] . "\n";
        $ini .= "\n*/ ?>\n";

        return $ini;
    }

    /**
     * menu.ini: the entry in the left hand menu.
     *
     * @param array $settings
     * @return string
     */
    protected static function menuIni( array $settings )
    {
        $first = $settings['views'][0];

        $ini  = self::iniHeader( $settings, 'Admin menu' );
        $ini .= "[TopAdminMenu]\n";
        $ini .= "# Which part of the admin this belongs to. Without an entry the module\n";
        $ini .= "# works and is only reachable by somebody who knows the address.\n";
        $ini .= "Tabs[]=" . $settings['navigation'] . "\n\n";

        $ini .= "[NavigationPartMenu_" . $settings['navigation'] . "]\n";
        $ini .= "# The menu item, and where it goes.\n";
        $ini .= "Menu[]=" . $settings['module'] . "\n";
        $ini .= "MenuTitle[" . $settings['module'] . "]=" . self::iniValue( $settings['title'], 60 ) . "\n";
        $ini .= "MenuURL[" . $settings['module'] . "]=/" . $settings['module'] . "/" . $first['name'] . "\n";
        $ini .= "\n*/ ?>\n";

        return $ini;
    }

    // ── Documentation ────────────────────────────────────────────────────────

    /**
     * How a view is reached, and what it is given.
     *
     * @param array $settings
     * @return string
     */
    protected static function examples( array $settings )
    {
        $first = $settings['views'][0];

        $php  = "<?php\n/**\n * " . $settings['title'] . " - worked examples.\n *\n";
        $php .= " * Not part of the extension: a file to read, and to copy lines out of. It is\n";
        $php .= " * under doc/ rather than modules/ so nothing loads it by accident.\n *\n";
        $php .= self::licenceNotice( $settings );
        $php .= " */\n\n";
        $php .= "// Nothing below runs on its own.\nreturn;\n\n";

        $php .= "// \xe2\x94\x80\xe2\x94\x80\xe2\x94\x80 The addresses " . str_repeat( "\xe2\x94\x80", 48 ) . "\n//\n";
        foreach ( $settings['views'] as $view )
        {
            $php .= "// " . self::addressOf( $settings, $view ) . "\n";
            $php .= "//     " . ( count( $view['policies'] )
                                  ? 'needs ' . implode( ', ', $view['policies'] )
                                  : 'no policy check' ) . "\n";
        }
        $php .= "\n\n";

        $php .= "// \xe2\x94\x80\xe2\x94\x80\xe2\x94\x80 What is in \$Params " . str_repeat( "\xe2\x94\x80", 44 ) . "\n//\n";
        $php .= "// Module, always. Then whatever the view declared, already separated out of\n";
        $php .= "// the address - and still exactly what somebody typed into it.\n\n";
        $php .= "\$Module = \$Params['Module'];\n";
        foreach ( array_merge( $first['parameters'], $first['optional'] ) as $parameter )
            $php .= "\$" . lcfirst( $parameter ) . " = (int) \$Params['" . $parameter . "'];\n";
        $php .= "\n\n";

        $php .= "// \xe2\x94\x80\xe2\x94\x80\xe2\x94\x80 What \$Result may carry " . str_repeat( "\xe2\x94\x80", 41 ) . "\n//\n";
        $php .= "\$Result = array(\n";
        $php .= "    'content' => \$tpl->fetch( 'design:" . $settings['module'] . "/" . $first['name'] . ".tpl' ),\n";
        $php .= "    'path'    => array( array( 'url' => '/" . $settings['module'] . "/" . $first['name'] . "', 'text' => 'Here' ),\n";
        $php .= "                        array( 'url' => false, 'text' => 'And here' ) ),\n";
        $php .= "    'left_menu'  => 'design:parts/setup/menu.tpl',\n";
        $php .= "    'pagelayout' => 'pagelayout.tpl',\n";
        $php .= "    'ui_context' => 'administration' );\n\n\n";

        $php .= "// \xe2\x94\x80\xe2\x94\x80\xe2\x94\x80 Answering something other than a page "
              . str_repeat( "\xe2\x94\x80", 26 ) . "\n//\n";
        $php .= "// A redirect, and a not found. Both end the request; neither returns.\n\n";
        $php .= "\$Module->redirectTo( '/" . $settings['module'] . "/" . $first['name'] . "' );\n\n";
        $php .= "return \$Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );\n\n\n";

        $php .= "// \xe2\x94\x80\xe2\x94\x80\xe2\x94\x80 Checking a policy yourself " . str_repeat( "\xe2\x94\x80", 36 ) . "\n//\n";
        $php .= "// The kernel checks the view's own policies before the script is reached.\n";
        $php .= "// Anything the view decides for itself - showing one row rather than another,\n";
        $php .= "// offering a button - has to be checked here, because nothing else will.\n\n";
        $php .= "\$access = eZUser::currentUser()->hasAccessTo( '" . $settings['module'] . "', '"
              . ( count( $settings['policies'] ) ? $settings['policies'][0]['name'] : 'read' ) . "' );\n\n";
        $php .= "if ( \$access['accessWord'] === 'no' )\n";
        $php .= "    return \$Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );\n\n";
        $php .= "// 'limited' means yes, for some of it. \$access['policies'] says which, and\n";
        $php .= "// it is the caller's job to apply that - the kernel has done all it can.\n\n\n";

        $php .= "// \xe2\x94\x80\xe2\x94\x80\xe2\x94\x80 Calling a view from somewhere else " . str_repeat( "\xe2\x94\x80", 29 ) . "\n//\n";
        $php .= "\$module = eZModule::exists( '" . $settings['module'] . "' );\n";
        $php .= "\$result = \$module->run( '" . $first['name'] . "', array() );\n";
        $php .= "echo \$result['content'];\n\n\n";

        $php .= "// \xe2\x94\x80\xe2\x94\x80\xe2\x94\x80 When the module is not found " . str_repeat( "\xe2\x94\x80", 34 ) . "\n//\n";
        $php .= "// Three things, in this order:\n";
        $php .= "//\n";
        $php .= "//   1. The extension is in ActiveExtensions[].\n";
        $php .= "//   2. module.ini names it in ExtensionRepositories[] and names the module\n";
        $php .= "//      in ModuleList[]. Both, not one.\n";
        $php .= "//   3. The caches are cleared. The module list is itself cached.\n";
        $php .= "//\n";
        $php .= "//      php bin/php/ezcache.php --clear-all\n";

        return $php;
    }

    /**
     * Every view, its address, and what reaches it.
     *
     * @param array $settings
     * @param array $paths
     * @return string
     */
    protected static function readme( array $settings, array $paths = array() )
    {
        $readme  = "# " . $settings['title'] . "\n\n" . $settings['summary'] . "\n\n";
        $readme .= "A module called `" . $settings['module'] . "`, with "
                 . count( $settings['views'] ) . " view" . ( count( $settings['views'] ) === 1 ? '' : 's' ) . ".\n\n";

        $readme .= "## Switching it on\n\n";
        $readme .= "1. Put this directory in `extension/" . $settings['name'] . "`.\n";
        $readme .= "2. Add it to `settings/override/site.ini.append.php`:\n\n";
        $readme .= "```\n" . self::activation( $settings ) . "\n```\n\n";
        $readme .= "3. Clear the caches:\n\n```\nphp bin/php/ezcache.php --clear-all\n```\n\n";

        $readme .= "## The views\n\n";
        $readme .= "| Address | Needs | Draws |\n| --- | --- | --- |\n";
        foreach ( $settings['views'] as $view )
            $readme .= "| `" . self::addressOf( $settings, $view ) . "` | "
                     . ( count( $view['policies'] ) ? '`' . implode( '`, `', $view['policies'] ) . '`' : '*nothing*' )
                     . " | `" . $settings['module'] . "/" . $view['name'] . ".tpl` |\n";
        $readme .= "\n";

        if ( count( $settings['policies'] ) )
        {
            $readme .= "## The policies\n\n";
            $readme .= "Granted to a role in the admin, under Users. A policy with a limitation is\n";
            $readme .= "granted for some content and not for the rest.\n\n";
            $readme .= "| Policy | Limitations |\n| --- | --- |\n";
            foreach ( $settings['policies'] as $policy )
                $readme .= "| `" . $settings['module'] . "/" . $policy['name'] . "` | "
                         . ( count( $policy['limitations'] ) ? '`' . implode( '`, `', $policy['limitations'] ) . '`' : 'none' )
                         . " |\n";
            $readme .= "\n";
        }

        if ( count( $paths ) )
        {
            $readme .= "## What is in here\n\n```\n";
            foreach ( $paths as $path )
                $readme .= $path . "\n";
            $readme .= "```\n\n";
        }

        $readme .= "## Before it is used for real\n\n";
        $readme .= "Every view runs, checks what it was given, and draws a page saying there is\n";
        $readme .= "nothing there yet. The addresses work, the policies can be granted, and the\n";
        $readme .= "menu entry appears - so the shape can be agreed before any of it is written.\n";

        return $readme;
    }
}

?>
