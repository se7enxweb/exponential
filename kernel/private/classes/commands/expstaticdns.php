<?php
/**
 * @description Check the DNS records of a custom domain for GitHub Pages: the A, AAAA and CNAME records compared with GitHub's addresses, and what to change
 * @alias exp:static:dns
 *
 * File containing the exp:static:dns command.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expstaticdns extends ExpstaticBase
{
    protected function name()
    {
        return 'exp:static:dns';
    }

    protected function begin()
    {
        $this->script( array(
            'description' => "The DNS check of a custom domain for GitHub Pages\n" .
                             "Looks the domain up and compares its records with what GitHub Pages needs: an apex domain\n" .
                             "(example.com) has the four A records 185.199.108.153 to 185.199.111.153 (and, optionally, the\n" .
                             "AAAA records), a subdomain (www.example.com) a CNAME to <owner>.github.io. It lists what to change.\n" .
                             "The owner comes from --owner, or from the Remote of the target whose CustomDomain is the domain.\n" .
                             "\n" .
                             "./console exp:static:dns www.example.com\n" .
                             "./console exp:static:dns example.com --owner=se7enxweb --format=json",
        ) + self::SCRIPT );
        return (array)$this->startup( '[owner:][format:]', '[domain]', array(
            'owner' => 'The GitHub account or organization that owns the repository (the CNAME must name <owner>.github.io)',
            'format' => 'text (default) or json (the machine output exp-static/1)',
        ) );
    }

    public function run()
    {
        $options = $this->begin();
        $format = $this->format( $options );
        $domain = isset( $options['arguments'][0] ) ? strtolower( trim( (string)$options['arguments'][0] ) ) : '';
        if ( $domain === '' )
            $this->usage( 'name the domain: exp:static:dns www.example.com' );
        $owner = isset( $options['owner'] ) && $options['owner'] !== false ? trim( (string)$options['owner'] ) : '';
        $target = '';
        if ( $owner === '' )
            foreach ( \expStaticTarget::all() as $candidate )
                if ( strcasecmp( (string)$candidate->setting( 'CustomDomain' ), $domain ) === 0
                     && ( $remote = \expStaticGitHub::parseRemote( (string)$candidate->setting( 'Remote' ) ) ) !== null )
                {
                    $owner = $remote['owner'];
                    $target = $candidate->id();
                    break;
                }
        $check = ( new \expStaticDnsCheck() )->check( $domain, $owner );
        $report = array( 'mode' => 'dns', 'owner' => $owner, 'target' => $target ) + $check;
        $report['errors'] = $check['problems'];
        $report['warnings'] = $owner === '' ? array( 'the owner is not known (--owner): the CNAME is only checked to end in .github.io' ) : array();
        $this->finish( $report, $format );
    }

    protected function lines( array $report )
    {
        $lines = array( 'DNS of ' . $report['domain'] . ( $report['kind'] !== '' ? ' (' . ( $report['kind'] === 'apex' ? 'apex domain' : 'subdomain' ) . ')' : '' )
                        . ( $report['target'] !== '' ? ', target ' . $report['target'] : '' ) );
        foreach ( array( 'a' => 'A', 'aaaa' => 'AAAA', 'cname' => 'CNAME' ) as $key => $label )
            if ( !empty( $report['records'][$key] ) )
                $lines[] = sprintf( '  %-6s %s', $label, implode( ', ', $report['records'][$key] ) );
        if ( $report['fixes'] )
        {
            $lines[] = 'Set these records at the domain\'s DNS provider:';
            foreach ( $report['fixes'] as $fix )
                $lines[] = '  ' . $fix;
        }
        foreach ( $report['notes'] as $note )
            $lines[] = 'Note: ' . $note;
        return $lines;
    }
}

}
