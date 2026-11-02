<?php
/**
 * The staticpublish/target view (/staticpublish/target/new and /staticpublish/target/<id>): the wizard that makes or
 * changes a target, one step at a time (page/staticpublish_wizard.js; without JavaScript all steps show at once):
 *
 *   1. the site: the target's id and name, the front siteaccesses (one or several, with the one at the root of the
 *      copy; administration siteaccesses are never offered)
 *   2. the host: GitHub Pages or another git host, and whether the GitHub integration can be used
 *   3. the credential: a token named in Setup > Secrets, a deploy key made for the target, a GitHub App, or none
 *   4. the repository: the git address, branch, folder and (GitHub) branch or Actions; for a saved GitHub target
 *      "Set up on GitHub" (GitHubSetupButton) creates the repository when asked and switches Pages on
 *      (expStaticGitHub::setupTarget(), "Verify it's you")
 *   5. links and base path, with a live example of a rewritten link
 *   6. the custom domain, with the DNS check (DnsCheckButton, expStaticDnsCheck)
 *   7. extras, post-processing, search, forms, exclusions, the crawl
 *   8. triggers, schedule, notifications, webhook
 *   9. review: Review checks the settings (expStaticTarget::validate()) and shows the lines that will be written to
 *      settings/override/staticcache.ini.append.php; Save writes them through expIniEditor (backup, audit) and asks
 *      "Verify it's you". A target with a deploy key then opens its keys page.
 *
 * Policy staticpublish/setup (for the target; a new target needs it without a Target limitation).
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Staticpublish
{

class Target extends Page
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $param = isset( $Params['TargetID'] ) ? trim( rawurldecode( (string)$Params['TargetID'] ) ) : 'new';
        $isNew = $param === '' || $param === 'new';
        $context = \expStaticTarget::siteAccessContext();
        $existing = \expStaticTarget::ids();

        if ( $isNew )
        {
            if ( !\expStaticAdmin::canDoAll( 'setup' ) )
                return $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );
            $target = null;
            $id = '';
            $settings = \expStaticAdmin::newSettings( $context );
        }
        else
        {
            $target = self::target( array( 'TargetID' => $param ), 'setup' );
            if ( $target === null )
                return $Module->handleError( \expStaticTarget::get( $param ) ? \eZError::KERNEL_ACCESS_DENIED : \eZError::KERNEL_NOT_FOUND, 'kernel' );
            $id = $target->id();
            $settings = $target->settings();
        }

        $errors = array();
        $warnings = array();
        $messages = array();
        $review = false;
        $diff = '';
        $enable = !\expStaticTarget::enabled();
        if ( self::isPost() && isset( $_POST['EditButton'] ) )
        {
            // back from the review to the form, with what was entered
            if ( $isNew )
                $id = isset( $_POST['TargetIdentifier'] ) ? strtolower( trim( (string)$_POST['TargetIdentifier'] ) ) : '';
            $settings = \expStaticAdmin::settingsFromPost( $_POST, $id ) + $settings;
            $enable = !empty( $_POST['EnableOnSave'] );
        }
        else if ( self::isPost() && ( isset( $_POST['ReviewButton'] ) || isset( $_POST['SaveButton'] ) ) )
        {
            if ( $isNew )
                $id = isset( $_POST['TargetIdentifier'] ) ? strtolower( trim( (string)$_POST['TargetIdentifier'] ) ) : '';
            $settings = \expStaticAdmin::settingsFromPost( $_POST, $id ) + $settings;
            $enable = !empty( $_POST['EnableOnSave'] );
            $check = \expStaticAdmin::check( $id, $settings, $context, $existing, $isNew );
            $errors = $check['errors'];
            $warnings = $check['warnings'];
            if ( !$errors && isset( $_POST['SaveButton'] ) )
            {
                $problem = \expStaticAdmin::saveTarget( $id, $settings, $isNew, $enable );
                if ( $problem === '' )
                {
                    $saved = \expStaticTarget::get( $id ) ?: new \expStaticTarget( $id, $settings );
                    if ( strpos( (string)$settings['Credential'], 'sshkey:' ) === 0 && !\expStaticAdmin::deployKey( $saved )['exists'] )
                        return $Module->redirectTo( '/staticpublish/keys/' . $id );
                    return $Module->redirectTo( '/staticpublish/build/' . $id );
                }
                $messages[] = array( 'kind' => 'bad', 'text' => self::t( 'The target could not be saved: %problem', array( '%problem' => $problem ) ) );
            }
            else if ( !$errors )
            {
                $review = true;
                try
                {
                    $diff = \expStaticAdmin::editor( $id, $settings, $enable )->diff();
                }
                catch ( \Throwable $e )
                {
                    $messages[] = array( 'kind' => 'bad', 'text' => self::t( 'The settings file cannot be read: %problem', array( '%problem' => $e->getMessage() ) ) );
                    $review = false;
                }
            }
        }

        // the GitHub steps: set the saved target up on GitHub (step 4), the DNS check of the domain entered (step 6);
        // both keep what was entered in the form
        $github = \expStaticAdmin::github( $target );
        $githubReport = null;
        $dns = null;
        if ( self::isPost() && ( isset( $_POST['GitHubSetupButton'] ) || isset( $_POST['DnsCheckButton'] ) ) )
        {
            if ( $isNew )
                $id = isset( $_POST['TargetIdentifier'] ) ? strtolower( trim( (string)$_POST['TargetIdentifier'] ) ) : '';
            $settings = \expStaticAdmin::settingsFromPost( $_POST, $id ) + $settings;
            $enable = !empty( $_POST['EnableOnSave'] );
            if ( isset( $_POST['DnsCheckButton'] ) )
            {
                $domain = strtolower( trim( (string)$settings['CustomDomain'] ) );
                if ( $domain === '' )
                    $messages[] = array( 'kind' => 'warn', 'text' => self::t( 'Enter the custom domain first.' ) );
                else
                {
                    $remote = \expStaticGitHub::parseRemote( (string)$settings['Remote'] );
                    $dns = ( new \expStaticDnsCheck() )->check( $domain, $remote ? $remote['owner'] : '' );
                }
            }
            else if ( $target === null || !$github['usable'] )
                $messages[] = array( 'kind' => 'bad', 'text' => self::t( 'Save the target with a GitHub repository and a token or GitHub App first.' ) );
            else
                $githubReport = \expStaticGitHub::setupTarget( $target, array(
                    'create' => !empty( $_POST['GitHubCreate'] ),
                    'private' => isset( $_POST['GitHubVisibility'] ) && $_POST['GitHubVisibility'] === 'private',
                ) );
        }

        $front = array();
        foreach ( $context['siteaccesses'] as $name )
            if ( !in_array( $name, $context['admin'], true ) && !\expStaticTarget::adminName( $name ) )
            {
                $prefix = \expStaticTarget::prefixOf( $context, $name );
                $front[] = array( 'name' => $name, 'url' => isset( $context['urls'][$name] ) ? $context['urls'][$name] : '',
                                  'folder' => $prefix !== null ? '/' . $prefix . '/' : '' );
            }
        $chosen = \expStaticTarget::siteAccessesOf( $settings );
        $chosenDefault = (string)$settings['DefaultSiteAccess'];
        if ( count( $chosen ) === 1 )
            $chosenDefault = $chosen[0];

        $credential = \expStaticAdmin::credentialChoice( $settings['Credential'] );
        return self::page( 'target.tpl', array(
            'target' => $target ? \expStaticAdmin::overview( $target ) : null,
            'view' => 'target',
            'is_new' => $isNew,
            'id' => $id,
            'settings' => $settings,
            'credential' => $credential,
            'siteaccesses' => $front,
            'chosen' => $chosen,
            'chosen_default' => $chosenDefault,
            'members' => \expStaticAdmin::memberList( new \expStaticTarget( $id, $settings ), $context ),
            'secret_names' => \expStaticAdmin::secretNames(),
            'key' => $target ? \expStaticAdmin::deployKey( $target ) : null,
            'key_secret' => \expStaticCredentials::keySecretName( $id !== '' ? $id : '<id>' ),
            'extras' => \expStaticTarget::EXTRAS,
            'post_process' => \expStaticTarget::POST_PROCESS,
            'triggers' => \expStaticTarget::TRIGGERS,
            'notify' => \expStaticTarget::NOTIFY,
            'link_relative' => \expStaticAdmin::linkExample( 'relative', '', '/news/', '/about-us/team' ),
            'link_base' => \expStaticAdmin::linkExample( 'base-path', $settings['BasePath'], '/news/', '/about-us/team' ),
            'errors' => $errors,
            'warnings' => $warnings,
            'messages' => $messages,
            'review' => $review,
            'diff' => $diff,
            'enabled' => \expStaticTarget::enabled(),
            'enable' => $enable,
            'site_limit' => \expStaticAdmin::GITHUB_SITE_BYTES,
            'file_limit' => \expStaticAdmin::GITHUB_FILE_BYTES,
            'github' => $github,
            'github_report' => $githubReport,
            'dns' => $dns,
            'pages_a' => \expStaticGitHub::PAGES_A,
            'start_step' => $dns !== null || isset( $_POST['DnsCheckButton'] ) ? 6 : ( $githubReport !== null || isset( $_POST['GitHubSetupButton'] ) ? 4 : 0 ),
        ), self::path( $isNew ? self::t( 'New target' ) : self::t( 'Settings' ), $target ) );
    }
}

}
?>
