<?php
/**
 * A check of the files sent with mutations of the GraphQL API (graphql.ini [GraphQLSettings] UploadCheckList[]): a
 * virus scanner, a filter of file types, a quota. Every check of the list runs before a file is stored; the first
 * that gives a reason refuses the file (the mutation answers BAD_USER_INPUT with that reason). A class of the list
 * that is missing refuses every file, so a scanner that was removed never lets files through unchecked.
 *
 *   class myClamAvUploadCheck implements expGraphQLUploadCheck
 *   {
 *       public function check( expGraphQLUpload $upload )
 *       {
 *           exec( 'clamdscan --no-summary ' . escapeshellarg( $upload->tmpName ), $out, $status );
 *           return $status === 0 ? null : 'The file contains a virus.';
 *       }
 *   }
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

interface expGraphQLUploadCheck
{
    /**
     * @param expGraphQLUpload $upload the file (tmpName is where it is now; name and type are what the client said)
     * @return string|null why the file is refused (shown to the client), or null to let it through
     */
    public function check( expGraphQLUpload $upload );
}

?>
