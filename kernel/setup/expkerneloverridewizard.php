<?php
/**
 * The kernel override wizard: replacing a kernel class outright.
 *
 * The heaviest way there is to change this system, and the last resort. An
 * override does not extend the kernel class - it *is* the kernel class, under
 * the same name, loaded instead of it. So there is no parent to call, nothing
 * is inherited, and everything the original did has to keep being done by the
 * copy.
 *
 * Which is why the interesting part of this wizard is not the copy. It is the
 * record it keeps of what was copied and when, and the script it ships for
 * telling you the day the kernel's version moves on - because an override that
 * nobody notices has gone stale is how a bug fixed in the kernel stays in a
 * site for years.
 *
 * @copyright Copyright (C) 7x / Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package kernel
 */


if ( !class_exists( 'expKernelOverrideWizard', false ) ) {
class expKernelOverrideWizard extends expExtensionWizard
{
    /**
     * What wrote the files, for the head of each one.
     *
     * @return string
     */
    public static function wizardName()
    {
        return 'kernel override wizard';
    }

    /**
     * Every kernel class that could be overridden, and where it lives.
     *
     * Read out of the kernel autoload map, so it is this installation's kernel
     * rather than a list of what some kernel once had.
     *
     * @return array class name to path
     */
    public static function kernelClasses()
    {
        static $classes = null;

        if ( $classes !== null )
            return $classes;

        $classes = array();
        $map     = is_file( 'autoload/ezp_kernel.php' ) ? @include 'autoload/ezp_kernel.php' : false;

        if ( !is_array( $map ) )
            return $classes;

        foreach ( $map as $name => $path )
        {
            $name = (string) $name;
            $path = (string) $path;

            // Only the kernel and the libraries. vendor/ is somebody else's and
            // is not reached by this mechanism at all.
            if ( strpos( $path, 'kernel/' ) !== 0 && strpos( $path, 'lib/' ) !== 0 )
                continue;

            if ( !preg_match( '/^[A-Za-z_][A-Za-z0-9_]*$/', $name ) )
                continue;

            $classes[$name] = $path;
        }

        ksort( $classes );

        return $classes;
    }

    /**
     * The ones whose name contains all of these words.
     *
     * @param string $query
     * @param int $limit
     * @return array
     */
    public static function matching( $query, $limit = 60 )
    {
        $query = trim( (string) $query );

        if ( $query === '' )
            return array();

        $words = preg_split( '/\s+/', strtolower( $query ) );
        $found = array();

        foreach ( self::kernelClasses() as $name => $path )
        {
            $against = strtolower( $name . ' ' . $path );

            foreach ( $words as $word )
                if ( strpos( $against, $word ) === false )
                    continue 2;

            $found[$name] = $path;

            if ( count( $found ) >= $limit )
                break;
        }

        return $found;
    }

    /**
     * Whether this installation would load an override at all.
     *
     * Two things have to be true and neither is the default, so a wizard that
     * did not say so would hand somebody a working extension that does nothing.
     *
     * @return array with keys allowed, generated and message
     */
    public static function readiness()
    {
        $allowed   = defined( 'EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE' ) && EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE;
        $generated = is_file( 'var/autoload/ezp_override.php' );

        if ( $allowed && $generated )
            return array( 'allowed' => true, 'generated' => true,
                          'message' => 'Overrides are switched on and the map has been generated, so one written here will be loaded.' );

        $missing = array();

        if ( !$allowed )
            $missing[] = "config.php has to define EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE as true. It ships commented out and set to false, and without it the map below is never even read.";

        if ( !$generated )
            $missing[] = "var/autoload/ezp_override.php has to exist. It is written by bin/php/ezpgenerateautoloads.php -o, which is a different run from the ordinary one.";

        return array( 'allowed' => $allowed, 'generated' => $generated,
                      'message' => implode( ' ', $missing ) );
    }

    // ── What this wizard can put in ──────────────────────────────────────────

    /**
     * @return array
     */
    public static function parts()
    {
        return array(
            'override' => array(
                'label' => 'The override',
                'description' => 'The kernel file copied under your extension, with a header recording exactly which file it came from and what it looked like at the time.',
                'default' => true ),
            'check' => array(
                'label' => 'Drift check',
                'description' => 'A script that compares the copy against the kernel file it came from and says whether the kernel has moved on. The thing that makes an override survivable; without it nobody finds out until something breaks.',
                'default' => true ),
            'readme' => array(
                'label' => 'README.md',
                'description' => 'What was overridden, why an override rather than anything lighter, and what has to be done at every upgrade.',
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
            'gitignore' => array(
                'label' => '.gitignore',
                'description' => 'Keeps editor leftovers and build output out of the repository.',
                'default' => true ),
            'licence' => array(
                'label' => 'LICENSE',
                'description' => 'The licence text named below. An override carries kernel code, so what it is licensed under is not an afterthought.',
                'default' => true ),
        );
    }

    // ── Settings ─────────────────────────────────────────────────────────────

    /**
     * @param array $input
     * @return array
     */
    public static function settings( array $input )
    {
        $settings = array(
            'name'     => self::safeName( isset( $input['name'] ) ? $input['name'] : '' ),
            'title'    => self::text( isset( $input['title'] ) ? $input['title'] : '', 120 ),
            'summary'  => self::text( isset( $input['summary'] ) ? $input['summary'] : '', 250 ),
            'author'   => self::text( isset( $input['author'] ) ? $input['author'] : '', 120 ),
            'vendor'   => self::safeName( isset( $input['vendor'] ) ? $input['vendor'] : '', true ),
            'version'  => self::text( isset( $input['version'] ) ? $input['version'] : '', 20 ),
            'licence'  => self::licence_id( isset( $input['licence'] ) ? $input['licence'] : '' ),
            'reason'   => self::text( isset( $input['reason'] ) ? $input['reason'] : '', 400 ),
            'find'     => self::text( isset( $input['find'] ) ? $input['find'] : '', 60 ),
            'class'    => '',
            'source'   => '',
        );

        // The class has to be one this kernel really has. Anything else is not
        // an override of anything.
        $wanted = isset( $input['class'] ) && is_string( $input['class'] ) ? $input['class'] : '';
        $kernel = self::kernelClasses();

        if ( $wanted !== '' && isset( $kernel[$wanted] ) )
        {
            $settings['class']  = $wanted;
            $settings['source'] = $kernel[$wanted];
        }

        if ( $settings['title'] === '' && $settings['class'] !== '' )
            $settings['title'] = $settings['class'] . ' override';
        if ( $settings['title'] === '' && $settings['name'] !== '' )
            $settings['title'] = ucwords( str_replace( '_', ' ', $settings['name'] ) );
        if ( $settings['version'] === '' )
            $settings['version'] = '1.0.0';
        if ( $settings['vendor'] === '' )
            $settings['vendor'] = 'exponential';
        if ( $settings['summary'] === '' && $settings['class'] !== '' )
            $settings['summary'] = 'Replaces ' . $settings['class'] . ' in the kernel.';

        $chosen = isset( $input['parts'] ) && is_array( $input['parts'] ) ? $input['parts'] : null;
        foreach ( self::parts() as $key => $part )
            $settings['parts'][$key] = $chosen === null ? $part['default'] : in_array( $key, $chosen, true );

        return $settings;
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

        if ( $settings['class'] === '' )
            $problems[] = 'Choose a kernel class to override. It has to be one this kernel really has: an override of a name nothing uses is a file nothing loads.';

        if ( $settings['class'] !== '' && !is_file( $settings['source'] ) )
            $problems[] = 'The autoload map says ' . $settings['class'] . ' is in ' . $settings['source'] . ' and that file is not there. Regenerate the autoloads before overriding anything.';

        if ( $settings['reason'] === '' )
            $problems[] = 'Say why this has to be an override. Whoever meets it at the next upgrade will want to know whether it is still needed, and by then nobody will remember.';

        // Somebody else overriding the same class is the one failure mode this
        // mechanism has no defence against: two files, one name, and whichever
        // the generator found last wins.
        foreach ( self::alreadyOverridden( $settings ) as $where )
            $problems[] = $settings['class'] . ' is already overridden by ' . $where . '. Two overrides of one class do not combine - whichever the autoload generator finds last wins, and nothing reports the other. Change that one instead.';

        return $problems;
    }

    /**
     * Whether something already overrides this class.
     *
     * @param array $settings
     * @return array of string
     */
    public static function alreadyOverridden( array $settings )
    {
        if ( $settings['class'] === '' || !is_file( 'var/autoload/ezp_override.php' ) )
            return array();

        $map = @include 'var/autoload/ezp_override.php';

        if ( !is_array( $map ) )
            return array();

        foreach ( $map as $name => $path )
            if ( strcasecmp( (string) $name, $settings['class'] ) === 0 )
                return array( (string) $path );

        return array();
    }

    // ── What it writes ───────────────────────────────────────────────────────

    /**
     * @param array $settings
     * @return array
     */
    public static function files( array $settings )
    {
        if ( $settings['name'] === '' || $settings['class'] === '' || !is_file( $settings['source'] ) )
            return array();

        $parts = $settings['parts'];
        $files = array();

        if ( $parts['override'] )
            $files['kernel_override/' . basename( $settings['source'] )] = self::override( $settings );

        if ( $parts['check'] )
            $files['bin/checkdrift.php'] = self::driftCheck( $settings );

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
     * What the kernel file looks like now.
     *
     * @param array $settings
     * @return string
     */
    public static function checksum( array $settings )
    {
        return $settings['source'] !== '' && is_file( $settings['source'] )
               ? md5_file( $settings['source'] ) : '';
    }

    /**
     * The kernel file, copied, with a header saying what it is.
     *
     * @param array $settings
     * @return string
     */
    protected static function override( array $settings )
    {
        $source   = file_get_contents( $settings['source'] );
        $checksum = self::checksum( $settings );

        $header  = "<?php\n/**\n";
        $header .= " * ###########################################################################\n";
        $header .= " * ##  KERNEL OVERRIDE - this file replaces a kernel class                  ##\n";
        $header .= " * ###########################################################################\n *\n";
        $header .= " * " . self::commentText( $settings['class'] ) . ", copied from\n";
        $header .= " *\n";
        $header .= " *     " . self::commentText( $settings['source'] ) . "\n";
        $header .= " *     md5 " . $checksum . "\n";
        $header .= " *     copied " . date( 'Y-m-d' ) . "\n *\n";
        $header .= " * Why this is an override rather than something lighter:\n *\n";
        $header .= " * " . wordwrap( self::commentText( $settings['reason'] ), 74, "\n * " ) . "\n *\n";
        $header .= " * ── What you have taken on ────────────────────────────────────────────────\n *\n";
        $header .= " * This is not a subclass. There is no parent to call and nothing is\n";
        $header .= " * inherited: this file *is* " . self::commentText( $settings['class'] ) . " as far as the rest of\n";
        $header .= " * the system is concerned, and everything the original did has to keep\n";
        $header .= " * being done here.\n *\n";
        $header .= " * Which means every fix the kernel makes to that class from now on is a fix\n";
        $header .= " * this site does not get, until somebody notices and copies it across. That\n";
        $header .= " * is what bin/checkdrift.php is for. Run it after every upgrade:\n *\n";
        $header .= " *     php extension/" . self::commentText( $settings['name'] ) . "/bin/checkdrift.php\n *\n";
        $header .= " * ── Keep the changes findable ─────────────────────────────────────────────\n *\n";
        $header .= " * Mark every line you change, so that the next person can tell your work\n";
        $header .= " * from the kernel's without a diff:\n *\n";
        $header .= " *     // OVERRIDE: and why\n *\n";
        $header .= " * The whole file below this line is the kernel's, unchanged. Change as\n";
        $header .= " * little of it as will do.\n *\n";
        $header .= self::licenceNotice( $settings );
        $header .= " */\n\n";

        // The kernel file keeps its own opening tag and its own doc comment;
        // ours goes above, and the original's is left exactly as it was so a
        // diff against the kernel shows only what was really changed.
        $body = preg_replace( '/^<\?php\s*\r?\n/', '', $source, 1 );

        return $header . $body;
    }

    /**
     * A script that says whether the kernel has moved on.
     *
     * @param array $settings
     * @return string
     */
    protected static function driftCheck( array $settings )
    {
        $php  = "#!/usr/bin/env php\n<?php\n/**\n";
        $php .= " * Has the kernel changed since this override was taken?\n *\n";
        $php .= " * Run it after every upgrade, and from whatever runs the deploy:\n *\n";
        $php .= " *     php extension/" . self::commentText( $settings['name'] ) . "/bin/checkdrift.php\n *\n";
        $php .= " * Exit status 0 when the kernel file is as it was, 1 when it has changed and\n";
        $php .= " * 2 when it has gone. A build that fails on 1 is the whole point: an override\n";
        $php .= " * nobody notices has gone stale is how a bug fixed in the kernel lives on in\n";
        $php .= " * a site for years.\n *\n";
        $php .= self::licenceNotice( $settings );
        $php .= " */\n\n";

        $php .= "const KERNEL_FILE = " . self::quoted( $settings['source'] ) . ";\n";
        $php .= "const TAKEN_MD5   = " . self::quoted( self::checksum( $settings ) ) . ";\n";
        $php .= "const TAKEN_ON    = " . self::quoted( date( 'Y-m-d' ) ) . ";\n";
        $php .= "const CLASS_NAME  = " . self::quoted( $settings['class'] ) . ";\n\n";

        $php .= "// Run from the installation root, whichever directory this was called from.\n";
        $php .= "\$root = dirname( dirname( dirname( __DIR__ ) ) );\n";
        $php .= "\$path = \$root . '/' . KERNEL_FILE;\n\n";

        $php .= "if ( !is_file( \$path ) )\n";
        $php .= "{\n";
        $php .= "    fwrite( STDERR, CLASS_NAME . \": the kernel file it was copied from has gone.\\n\" );\n";
        $php .= "    fwrite( STDERR, '  ' . KERNEL_FILE . \"\\n\" );\n";
        $php .= "    fwrite( STDERR, \"This override may now be replacing nothing, or replacing something that moved.\\n\" );\n";
        $php .= "    exit( 2 );\n";
        $php .= "}\n\n";

        $php .= "\$now = md5_file( \$path );\n\n";

        $php .= "if ( \$now === TAKEN_MD5 )\n";
        $php .= "{\n";
        $php .= "    echo CLASS_NAME, \": the kernel file is as it was when this was taken on \", TAKEN_ON, \".\\n\";\n";
        $php .= "    exit( 0 );\n";
        $php .= "}\n\n";

        $php .= "fwrite( STDERR, CLASS_NAME . \": the kernel has changed since this override was taken.\\n\" );\n";
        $php .= "fwrite( STDERR, '  file    ' . KERNEL_FILE . \"\\n\" );\n";
        $php .= "fwrite( STDERR, '  taken   ' . TAKEN_ON . ' as ' . TAKEN_MD5 . \"\\n\" );\n";
        $php .= "fwrite( STDERR, '  now     ' . \$now . \"\\n\" );\n";
        $php .= "fwrite( STDERR, \"\\nWhat changed in the kernel:\\n\\n\" );\n";
        $php .= "fwrite( STDERR, '  git -C ' . \$root . ' log --oneline -- ' . KERNEL_FILE . \"\\n\" );\n";
        $php .= "fwrite( STDERR, \"\\nWhat this override changed:\\n\\n\" );\n";
        $php .= "fwrite( STDERR, '  diff -u ' . \$path . ' ' . __DIR__ . '/../kernel_override/'\n";
        $php .= "                . basename( KERNEL_FILE ) . \"\\n\" );\n";
        $php .= "fwrite( STDERR, \"\\nDecide which of the kernel's changes this override needs, take them\\n\" );\n";
        $php .= "fwrite( STDERR, \"across, and update TAKEN_MD5 above to the new one.\\n\" );\n\n";
        $php .= "exit( 1 );\n\n";
        $php .= "?>\n";

        return $php;
    }

    /**
     * A php string literal, quoted and escaped.
     *
     * @param string $value
     * @return string
     */
    protected static function quoted( $value )
    {
        return "'" . self::phpString( $value ) . "'";
    }

    /**
     * What was overridden and what has to be done at every upgrade.
     *
     * @param array $settings
     * @param array $paths
     * @return string
     */
    protected static function readme( array $settings, array $paths = array() )
    {
        $ready = self::readiness();

        $readme  = "# " . $settings['title'] . "\n\n" . $settings['summary'] . "\n\n";
        $readme .= "Replaces `" . $settings['class'] . "`, copied from `" . $settings['source'] . "`\n";
        $readme .= "on " . date( 'Y-m-d' ) . " at md5 `" . self::checksum( $settings ) . "`.\n\n";

        $readme .= "## Why an override\n\n" . $settings['reason'] . "\n\n";

        $readme .= "## What this costs\n\n";
        $readme .= "An override is not a subclass. There is no parent to call and nothing is\n";
        $readme .= "inherited: this file **is** `" . $settings['class'] . "` as far as the rest of the\n";
        $readme .= "system is concerned.\n\n";
        $readme .= "So every fix the kernel makes to that class from now on is a fix this site\n";
        $readme .= "does not get, until somebody notices and copies it across. Overrides are\n";
        $readme .= "not usually wrong when they are written; they go wrong by sitting there.\n\n";

        $readme .= "## After every upgrade\n\n";
        $readme .= "```\nphp extension/" . $settings['name'] . "/bin/checkdrift.php\n```\n\n";
        $readme .= "Exit status 0 means the kernel file is as it was. 1 means it has changed\n";
        $readme .= "and somebody has to decide which of those changes this override needs. 2\n";
        $readme .= "means the file has gone entirely.\n\n";
        $readme .= "Worth running from whatever runs the deploy, failing the build on 1. The\n";
        $readme .= "point of the check is that it is not optional.\n\n";

        $readme .= "## Switching it on\n\n";
        $readme .= "1. Put this directory in `extension/" . $settings['name'] . "`.\n";
        $readme .= "2. Add it to `settings/override/site.ini.append.php`:\n\n";
        $readme .= "```\n" . self::activation( $settings ) . "\n```\n\n";
        $readme .= "3. `config.php` must allow overrides at all:\n\n";
        $readme .= "```php\ndefine( 'EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE', true );\n```\n\n";
        $readme .= "4. Generate the override map, which is a different run from the ordinary one:\n\n";
        $readme .= "```\nphp bin/php/ezpgenerateautoloads.php -o\nphp bin/php/ezcache.php --clear-all\n```\n\n";

        $readme .= $ready['allowed'] && $ready['generated']
                   ? "On the installation this was written from, both of those were already in place.\n\n"
                   : "On the installation this was written from, they were not: " . $ready['message'] . "\n\n";

        $readme .= "## Before reaching for this again\n\n";
        $readme .= "Almost everything in this system has a lighter way in - a handler named by a\n";
        $readme .= "setting, an event listener, a filter, a template override. **Setup → RAD\n";
        $readme .= "tools** lists them, and the survey beside it lists every one this\n";
        $readme .= "installation actually has. An override is the answer when none of those\n";
        $readme .= "reaches the thing that has to change, and it is worth being sure of that\n";
        $readme .= "first.\n";

        if ( count( $paths ) )
        {
            $readme .= "\n## What is in here\n\n```\n";
            foreach ( $paths as $path )
                $readme .= $path . "\n";
            $readme .= "```\n";
        }

        return $readme;
    }
}
}

require_once 'kernel/setup/expextensionwizard.php';


?>
