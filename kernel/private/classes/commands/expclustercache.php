<?php
/**
 * @description Clear the caches of this installation on every host of settings/cluster.ini: every cache (with the Valkey keys of the cache clear listeners) and Velocity's response cache
 * @alias exp:cluster:cache
 *
 * File containing the exp:cluster:cache command (bin/php/expclustercache.php); guide: doc/eng-us/features/6.0/cluster-host-commands.md
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x; All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Expclustercache extends Expclusterbase
{
    public function run()
    {
        $this->script( array( 'description' => "Clear the caches on every host\n" .
                                               "On each host of settings/cluster.ini: exp:ezcache --clear-all, which also clears the caches\n" .
                                               "extensions keep elsewhere through the cache clear listeners (Valkey INI and view keys) and\n" .
                                               "the template-block cache; then, on a host with Engine=velocity, Velocity's response cache\n" .
                                               "(exp:velocity cache clear --apply). --tag and --id clear only those caches. --full runs\n" .
                                               "exp:velocity deploy --all-caches --no-autoload instead, which also reloads PHP-FPM and\n" .
                                               "restarts Velocity in the right order.\n" .
                                               "\n" .
                                               "./console exp:cluster:cache clear\n" .
                                               "./console exp:cluster:cache clear --tag=template,content\n" .
                                               "./console exp:cluster:cache clear --full --parallel\n" .
                                               "./console exp:cluster:cache clear --dry-run\n" .
                                               "\n" .
                                               "Exit code: 0 every host passed; 1 a host failed; 2 wrong options or settings.",
                              'use-session' => false,
                              'use-modules' => false,
                              'use-extensions' => true ) );
        $options = $this->startup( '[tag:][id:][purge][full]' . self::HOST_OPTIONS . self::RUN_OPTIONS, '[action]', array(
            'tag' => 'Only the caches with these tags (comma list, as exp:ezcache --clear-tag)',
            'id' => 'Only the caches with these ids (comma list, as exp:ezcache --clear-id)',
            'purge' => 'Remove the cache files for real instead of expiring them (exp:ezcache --purge)',
            'full' => 'Every cache, PHP-FPM and Velocity in order: exp:velocity deploy --all-caches --no-autoload',
        ) + self::HOST_HELP + self::RUN_HELP );
        $action = isset( $options['arguments'][0] ) ? (string)$options['arguments'][0] : 'clear';
        $request = self::request( $action, $options, $error );
        if ( $request === null )
            $this->fail( (string)$error, self::EXIT_USAGE );
        $hosts = $this->loadHosts();
        $selected = $this->selectHosts( $hosts, $options );
        $steps = array();
        foreach ( $selected as $name => $host )
            $steps[$name] = \expClusterOperations::cacheSteps( $host, $request );
        $this->execute( $hosts, $selected, $steps, $options, 'exp:cluster:cache clear' . ( $request['full'] ? ' --full' : '' ) );
    }

    /**
     * The options of a cache clear.
     *
     * @param string $action only clear
     * @param array $options
     * @param string|null $error
     * @return array|null tags, ids, purge, full
     */
    public static function request( $action, array $options, &$error = null )
    {
        $error = null;
        if ( $action !== 'clear' )
        {
            $error = "Unknown action $action: exp:cluster:cache clear";
            return null;
        }
        $split = function ( $value ) {
            return $value ? array_values( array_filter( array_map( 'trim', explode( ',', (string)$value ) ), 'strlen' ) ) : array();
        };
        $request = array( 'tags' => $split( isset( $options['tag'] ) ? $options['tag'] : '' ), 'ids' => $split( isset( $options['id'] ) ? $options['id'] : '' ),
                          'purge' => !empty( $options['purge'] ), 'full' => !empty( $options['full'] ) );
        foreach ( array_merge( $request['tags'], $request['ids'] ) as $word )
            if ( !preg_match( '/^[a-z0-9_-]+$/i', $word ) )
            {
                $error = "Not a cache tag or id: $word";
                return null;
            }
        if ( $request['full'] && ( $request['tags'] || $request['ids'] || $request['purge'] ) )
        {
            $error = '--full clears every cache; it does not go with --tag, --id or --purge';
            return null;
        }
        return $request;
    }
}

}
?>
