<?php
/**
 * File containing the eZSearchLog class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZSearchLog ezsearchlog.php
  \brief eZSearchLog handles logging of search phrases

*/

class eZSearchLog
{
    /*!
     Logs a search query so that we can retrieve statistics afterwords.
    */
    static function addPhrase( $phrase, $returnCount )
    {
        $db = eZDB::instance();
        // MongoDB: ezsearch SQL phrase log is not used with MongoDB — skip silently
        if ( $db->databaseName() === 'mongo' )
            return;
        $db->begin();
        $db->lock( "ezsearch_search_phrase" );

        $trans = eZCharTransform::instance();
        $phrase = $trans->transformByGroup( trim( $phrase ), 'search' );

        // 250 is the numbers of characters accepted by the DB table, so shorten to fit
        if ( strlen( $phrase ) > 250 )
        {
            $phrase = substr( $phrase , 0 , 247 ) . "...";
        }
        $phrase = $db->escapeString( $phrase );

        // find or store the phrase
        $phraseRes = $db->arrayQuery( "SELECT id FROM ezsearch_search_phrase WHERE phrase='$phrase'" );

        if ( count( $phraseRes ) == 1 )
        {
            $phraseID = $phraseRes[0]['id'];
            $db->query( "UPDATE ezsearch_search_phrase
                         SET    phrase_count = phrase_count + 1,
                                result_count = result_count + $returnCount
                         WHERE  id = $phraseID" );
        }
        else
        {
            $db->query( "INSERT INTO
                              ezsearch_search_phrase ( phrase, phrase_count, result_count )
                         VALUES ( '$phrase', 1, $returnCount )" );
        }
        $db->unlock();
        $db->commit();
    }

    /*!
     Returns the most frequent search phrases, which did not get hits.
    */
    static function mostFrequentPhraseArray( $parameters = array( ) )
    {
        $db = eZDB::instance();
        if ( $db->databaseName() === 'mongo' )
        {
            $pipeline = [
                [ '$sort' => [ 'phrase_count' => -1 ] ],
                [ '$project' => [
                    '_id'          => 0,
                    'id'           => 1,
                    'phrase'       => 1,
                    'phrase_count' => 1,
                    'result_count' => [ '$cond' => [
                        'if'   => [ '$gt' => [ '$phrase_count', 0 ] ],
                        'then' => [ '$divide' => [ '$result_count', '$phrase_count' ] ],
                        'else' => 0,
                    ] ],
                ] ],
            ];
            if ( isset( $parameters['offset'] ) && $parameters['offset'] > 0 )
                $pipeline[] = [ '$skip'  => (int) $parameters['offset'] ];
            if ( isset( $parameters['limit']  ) && $parameters['limit']  > 0 )
                $pipeline[] = [ '$limit' => (int) $parameters['limit']  ];
            return $db->aggregate( 'ezsearch_search_phrase', $pipeline );
        }
        $query = 'SELECT phrase_count, result_count / phrase_count AS result_count, id, phrase
                  FROM   ezsearch_search_phrase
                  ORDER BY phrase_count DESC';

        return $db->arrayQuery( $query, $parameters );
    }

    /*!
     \static
     Removes all stored phrases and search match counts from the database.
    */
    static function removeStatistics()
    {
        $db = eZDB::instance();
        $query = "DELETE FROM ezsearch_search_phrase";
        $db->query( $query );
    }
}

?>
