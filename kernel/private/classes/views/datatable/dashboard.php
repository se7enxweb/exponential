<?php
/**
 * The datatable/dashboard view (doc/eng-us/features/6.0/data-tables.md): every persistent class of the installation
 * with its table, connection, keys and relations, grouped by the kernel and each extension, with a filter by words
 * and by origin. With datatable/manage: create the kernel tables when they are missing (exp_data_connection,
 * exp_secret) and rebuild the catalogue. Policy datatable/read.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Datatable
{

class Dashboard extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $http = \eZHTTPTool::instance();
        $canManage = self::may( 'manage' );
        $messages = array();
        $isPost = strtoupper( (string)\eZSys::serverVariable( 'REQUEST_METHOD', true ) ) === 'POST';

        if ( $isPost && $http->hasPostVariable( 'CreateTablesButton' ) )
        {
            if ( !$canManage )
                return $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );
            $log = array();
            $ok = \expDataConnections::install( $log );
            $messages[] = array( 'kind' => $ok ? 'ok' : 'bad', 'text' => implode( '; ', $log ) );
        }
        $catalogue = null;
        if ( $isPost && $http->hasPostVariable( 'RebuildCatalogueButton' ) )
        {
            if ( !$canManage )
                return $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );
            $catalogue = \expDataTableCatalogue::instance( true );
            $messages[] = array( 'kind' => 'ok', 'text' => \ezpI18n::tr( 'design/admin/datatable', 'The catalogue was made again: %count classes.', null,
                                                                          array( '%count' => count( $catalogue->classes() ) ) ) );
        }
        $catalogue = $catalogue ?: \expDataTableCatalogue::instance();

        $query = isset( $Params['Query'] ) ? mb_substr( trim( (string)$Params['Query'] ), 0, 100 ) : '';
        if ( $http->hasGetVariable( 'q' ) )
            $query = mb_substr( trim( (string)$http->getVariable( 'q' ) ), 0, 100 );
        $origin = isset( $Params['Origin'] ) && preg_match( '/^[A-Za-z0-9_.-]{1,64}$/', (string)$Params['Origin'] ) ? (string)$Params['Origin'] : '';
        $words = array_filter( preg_split( '/\s+/', mb_strtolower( $query ) ) );

        $groups = array();
        $total = 0;
        $origins = array();
        foreach ( $catalogue->byOrigin() as $groupOrigin => $entries )
        {
            $origins[] = $groupOrigin;
            if ( $origin !== '' && $groupOrigin !== $origin )
                continue;
            $rows = array();
            foreach ( $entries as $class => $entry )
            {
                $haystack = mb_strtolower( $class . ' ' . $entry['table'] . ' ' . implode( ' ', array_keys( $entry['fields'] ) ) );
                $match = true;
                foreach ( $words as $word )
                    if ( mb_strpos( $haystack, $word ) === false )
                        $match = false;
                if ( !$match )
                    continue;
                $content = 0;
                foreach ( $entry['relations'] as $relation )
                    if ( in_array( $relation['kind'], array( 'object', 'node' ), true ) )
                        $content++;
                $rows[] = array( 'class' => $class, 'table' => $entry['table'], 'connection' => $entry['connection'] !== '' ? $entry['connection'] : 'site',
                                 'fields' => count( $entry['fields'] ), 'keys' => implode( ', ', $entry['keys'] ),
                                 'relations' => count( $entry['relations'] ), 'content_relations' => $content, 'error' => $entry['error'] );
            }
            if ( $rows )
            {
                $groups[] = array( 'origin' => $groupOrigin, 'rows' => $rows );
                $total += count( $rows );
            }
        }

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'groups', $groups );
        $tpl->setVariable( 'total', $total );
        $tpl->setVariable( 'all_count', count( $catalogue->classes() ) );
        $tpl->setVariable( 'origins', $origins );
        $tpl->setVariable( 'origin', $origin );
        $tpl->setVariable( 'query', $query );
        $tpl->setVariable( 'messages', $messages );
        $tpl->setVariable( 'can_manage', $canManage );
        $tpl->setVariable( 'can_connect', self::may( 'connections' ) );
        $tpl->setVariable( 'can_sync', self::may( 'sync' ) );
        $tpl->setVariable( 'connections_installed', \expDataConnections::installed( true ) );
        $tpl->setVariable( 'secrets_installed', \expSecrets::installed( true ) );
        $tpl->setVariable( 'connection_count', count( \expDataConnections::all() ) - 1 );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:datatable/dashboard.tpl' );
        $Result['path'] = array( array( 'url' => 'setup/menu', 'text' => \ezpI18n::tr( 'design/admin/datatable', 'Setup' ) ),
                                 array( 'url' => false, 'text' => \ezpI18n::tr( 'design/admin/datatable', 'Data tables' ) ) );
        return $Result;
    }

    /**
     * Whether the current user has a policy function of the module.
     *
     * @param string $function
     * @return bool
     */
    public static function may( $function )
    {
        $access = \eZUser::currentUser()->hasAccessTo( 'datatable', (string)$function );
        return $access['accessWord'] !== 'no';
    }
}

}
?>
