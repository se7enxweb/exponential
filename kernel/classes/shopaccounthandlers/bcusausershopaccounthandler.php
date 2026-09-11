<?php
/**
 * File containing the bcusauserShopAccountHandler class.
 *
 * Shop account handler for the US address model: the address line is stored
 * as <city> rather than <place>, and a telephone number is collected and kept
 * as <phone>.
 *
 * Selected with shopaccount.ini:
 *
 *   [AccountSettings]
 *   Handler=bcusauser
 *
 * Both of those fields are handled by eZUserShopAccountHandler itself, which
 * reads <city> with a fallback to <place> so orders written before the rename
 * still render, and returns the address under both the 'city' and 'place'
 * keys. This handler exists so a site configured for 'bcusauser' resolves to
 * a real class rather than duplicating that logic.
 */

class bcusauserShopAccountHandler extends eZUserShopAccountHandler
{
}

?>
