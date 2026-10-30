<?php
/**
 * A batch loader of the GraphQL API (the DataLoader pattern): a resolver asks for a value by its key and gets a
 * promise (GraphQL\Deferred); the executor runs every resolver of a level first, so the keys of a whole list are
 * collected before the first promise is kept, and one call of the batch function fetches them all. A key fetched
 * once is answered from memory for the rest of the request.
 *
 *   50 articles with their owner's name: 1 query for the 50 owners instead of 50.
 *
 * One loader per kind of value and per request (expGraphQLContext::loader()); never kept between requests, so a
 * persistent worker (Velocity) never answers from another request's memory. Loaded only after webonyx/graphql-php.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expGraphQLBatchLoader
{
    /** @var callable function( array $keys ): array key => value (a key left out is null) */
    protected $batch;

    /** @var array key => true, the keys asked for and not fetched yet */
    protected $pending = array();

    /** @var array key => value, the keys fetched */
    protected $values = array();

    /** @var int how often the batch function ran (tests, exp:graphql:query --stats) */
    public $batches = 0;

    /** @var int how many keys it was given in all */
    public $keys = 0;

    /**
     * @param callable $batch function( array $keys ): array key => value
     */
    public function __construct( callable $batch )
    {
        $this->batch = $batch;
    }

    /**
     * @param int|string $key
     * @return \GraphQL\Deferred the value of the key (null when there is none)
     */
    public function load( $key )
    {
        if ( !array_key_exists( $key, $this->values ) )
            $this->pending[$key] = true;
        $loader = $this;
        return new \GraphQL\Deferred( function () use ( $loader, $key ) {
            return $loader->value( $key );
        } );
    }

    /**
     * Asks for a key without a promise: it is fetched with the next batch.
     *
     * @param int|string $key
     */
    public function want( $key )
    {
        if ( !array_key_exists( $key, $this->values ) )
            $this->pending[$key] = true;
    }

    /**
     * The value of a key, fetching every pending key first (one call of the batch function).
     *
     * @param int|string $key
     * @return mixed
     */
    public function value( $key )
    {
        if ( !array_key_exists( $key, $this->values ) )
        {
            $this->pending[$key] = true;
            $this->flush();
        }
        return $this->values[$key];
    }

    /**
     * Fetches the pending keys.
     */
    public function flush()
    {
        if ( !$this->pending )
            return;
        $keys = array_keys( $this->pending );
        $this->pending = array();
        $this->batches++;
        $this->keys += count( $keys );
        $values = call_user_func( $this->batch, $keys );
        foreach ( $keys as $key )
            $this->values[$key] = is_array( $values ) && array_key_exists( $key, $values ) ? $values[$key] : null;
    }

    /**
     * Stores a value fetched another way (a list that already has it), so it is not fetched again.
     *
     * @param int|string $key
     * @param mixed $value
     */
    public function prime( $key, $value )
    {
        if ( !array_key_exists( $key, $this->values ) )
            $this->values[$key] = $value;
    }
}

?>
