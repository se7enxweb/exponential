<?php
/**
 * @description Make or show the SSH deploy key of a static publishing target: ed25519, the private half in the secret store, the public half to add to the repository
 * @alias exp:static:keys
 *
 * File containing the exp:static:keys command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expstatickeys extends ExpstaticBase
{
    protected function name()
    {
        return 'exp:static:keys';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "The SSH deploy key of a static publishing target\n" .
                             "With --generate it makes a key pair for the target (ed25519, with ssh-keygen), stores the private half\n" .
                             "in the secret store (Setup > Secrets, never in a file of the site) and prints the public half: add it\n" .
                             "to the repository as a deploy key with write access, set the target's Credential=sshkey:<secret> and\n" .
                             "its Remote to the SSH address (git@github.com:owner/repo.git). Without --generate it shows the public\n" .
                             "half and the fingerprint of the key the target has.\n" .
                             "\n" .
                             "./console exp:static:keys github-public --generate\n" .
                             "./console exp:static:keys github-public\n" .
                             "./console exp:static:keys github-public --generate --replace       a new key; the old one stops working",
        ) + self::SCRIPT );
        return (array)$this->startup( '[generate][replace][format:]', '[target]', array(
            'generate' => 'Make a new deploy key (ed25519) and store its private half in the secret store',
            'replace' => 'With --generate: replace the target\'s deploy key (the old one stops working)',
            'format' => 'text (default) or json (the machine output exp-static/1)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        if ( !empty( $options['replace'] ) && empty( $options['generate'] ) )
            $this->usage( '--replace goes with --generate' );
        $target = $this->target( $options, $format );
        $this->database( $format );
        $secret = \expStaticCredentials::keySecretName( $target->id() );
        list( $kind, $name ) = \expStaticCredentials::parse( (string)$target->setting( 'Credential' ) );
        $report = array( 'mode' => 'keys', 'target' => $target->id(), 'secret' => $secret, 'credential_setting' => 'sshkey:' . $secret,
                         'credential' => (string)$target->setting( 'Credential' ), 'generated' => false, 'public_key' => '', 'fingerprint' => '',
                         'errors' => array(), 'warnings' => array() );
        $stored = \expSecrets::installed() ? \expSecrets::names( \expStaticCredentials::SCOPE ) : array();
        $exists = isset( $stored[$secret] ) && $stored[$secret]['source'] === 'store';

        if ( !empty( $options['generate'] ) )
        {
            if ( !\expSecrets::installed() )
                $report['errors'][] = 'the secret store is not set up (the table exp_secret: ./console exp:upgrade --apply)';
            else if ( $exists && empty( $options['replace'] ) )
                $report['errors'][] = "the target has a deploy key already ($secret): --generate --replace makes a new one, and the old one stops working";
            else
            {
                $publisher = $this->publisher( $target, null );
                $host = preg_replace( '/[^A-Za-z0-9.-]/', '', (string)php_uname( 'n' ) ) ?: 'localhost';
                $key = \expStaticCredentials::generateKey( $publisher->workDirectory(), 'exponential-static-publish-' . $target->id() . '@' . $host );
                if ( $key['error'] !== '' )
                    $report['errors'][] = $key['error'];
                else if ( !\expSecrets::set( $secret, $key['private'], \expStaticCredentials::SCOPE, $key['public'] ) )
                    $report['errors'][] = "the secret $secret could not be stored (Setup > Secrets: the store's key)";
                else
                {
                    $report['generated'] = true;
                    $report['public_key'] = $key['public'];
                    $report['fingerprint'] = $key['fingerprint'];
                }
                \expStaticPublisher::audit( 'system.staticpublish.credential', array(
                    'object' => array( 'type' => 'static_target', 'id' => $target->id() ), 'verb' => $exists ? 'replace' : 'generate',
                    'result' => $report['generated'] ? 'success' : 'failure', 'reason' => $report['generated'] ? '' : (string)reset( $report['errors'] ),
                    'x' => array( 'kind' => 'sshkey', 'secret' => $secret, 'fingerprint' => $report['fingerprint'] ) ) );
            }
        }
        else if ( $exists )
        {
            $report['public_key'] = (string)$stored[$secret]['description'];
            $report['fingerprint'] = \expStaticCredentials::fingerprint( $report['public_key'] );
        }
        else
            $report['warnings'][] = "the target has no deploy key yet: ./console exp:static:keys {$target->id()} --generate";

        if ( ( $report['generated'] || $exists ) && ( $kind !== 'sshkey' || $name !== $secret ) )
            $report['warnings'][] = "the target's Credential is {$report['credential']}: set Credential=sshkey:$secret to sign in with this key";
        if ( ( $report['generated'] || $exists ) && preg_match( '#^https?://#i', (string)$target->setting( 'Remote' ) ) )
            $report['warnings'][] = 'a deploy key signs in over SSH: set Remote to the SSH address (git@github.com:owner/repo.git)';
        $this->finish( $report, $format );
    }

    protected function lines( array $report )
    {
        $lines = array( 'Static publishing: the deploy key of ' . $report['target'] . ' (secret ' . $report['secret'] . ')' );
        if ( $report['public_key'] === '' )
            return $lines;
        $lines[] = $report['generated'] ? 'A new key was made. Its public half (add it to the repository as a deploy key with write access;'
                                        . ' GitHub: Settings > Deploy keys > Add deploy key, Allow write access):'
                                        : 'The public half of the key:';
        $lines[] = '';
        $lines[] = $report['public_key'];
        $lines[] = '';
        $lines[] = 'Fingerprint: ' . $report['fingerprint'];
        $lines[] = 'Setting:     Credential=' . $report['credential_setting'];
        return $lines;
    }
}

}
