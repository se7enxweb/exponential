<?php
/**
 * The controller of the migration routes (expMigrationRestProvider): the status, the runs, one run and the snapshot, for the user
 * the REST layer authenticated, who needs the policy migration/read (403 without it). An unknown run is 404.
 *
 * Never kept in the REST application cache (rest.ini [expMigrationRestController_CacheSettings]): a status changes
 * with every run, and who may read it depends on the user.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The route's variables come from ezcMvcController::__get() (its properties); a declared property would hide them.
 *
 * @property-read string $runId the route's :runId
 */
class expMigrationRestController extends ezpRestMvcController
{
    /** @var expMigrationService|null the service (tests set another) */
    public static $service = null;

    public function doStatus()
    {
        $refusal = $this->refusal();
        if ( $refusal !== null )
            return $refusal;
        return self::result( self::service()->status() );
    }

    public function doRuns()
    {
        $refusal = $this->refusal();
        if ( $refusal !== null )
            return $refusal;
        $get = $this->request instanceof ezcMvcRequest && isset( $this->request->get ) ? (array)$this->request->get : array();
        list( $limit, $offset ) = expMigrationService::page( isset( $get['limit'] ) ? $get['limit'] : 20, isset( $get['offset'] ) ? $get['offset'] : 0 );
        return self::result( array( 'limit' => $limit, 'offset' => $offset, 'runs' => self::service()->runs( $limit, $offset ) ) );
    }

    public function doRun()
    {
        $refusal = $this->refusal();
        if ( $refusal !== null )
            return $refusal;
        $run = self::service()->run( (string)$this->runId );
        return $run === null ? self::status( 404, array( 'error' => 'not_found' ) ) : self::result( $run );
    }

    /**
     * The fingerprints another installation compares with (exp:migration:generate --from-diff=<address>): never a
     * value, secret settings left out (expMigrationDiff::localSnapshot()).
     */
    public function doSnapshot()
    {
        $refusal = $this->refusal();
        if ( $refusal !== null )
            return $refusal;
        $get = $this->request instanceof ezcMvcRequest && isset( $this->request->get ) ? (array)$this->request->get : array();
        $snapshot = expMigrationDiff::localSnapshot( $get );
        return $snapshot['errors'] && !$snapshot['kinds'] ? self::status( 400, array( 'error' => 'bad_request', 'error_message' => implode( '; ', $snapshot['errors'] ) ) )
                                                         : self::result( $snapshot );
    }

    /** @return expMigrationService */
    protected static function service()
    {
        return self::$service instanceof expMigrationService ? self::$service : new expMigrationService();
    }

    /**
     * 403 when the user lacks migration/read
     *
     * @return ezpRestMvcResult|null
     */
    protected function refusal()
    {
        if ( !self::service()->mayRead() )
            return self::status( 403, array( 'error' => 'access_denied', 'error_message' => 'The user may not read the migrations (migration/read).' ) );
        return null;
    }

    /**
     * @param array $data
     * @return ezpRestMvcResult
     */
    protected static function result( array $data )
    {
        $result = new ezpRestMvcResult();
        $result->variables = $data;
        return $result;
    }

    /**
     * @param int $code
     * @param array $body
     * @return ezpRestMvcResult
     */
    protected static function status( $code, array $body )
    {
        $result = new ezpRestMvcResult();
        $result->status = new ezpRestStatusResponse( (int)$code, $body );
        return $result;
    }
}

?>
