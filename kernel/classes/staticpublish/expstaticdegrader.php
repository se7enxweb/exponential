<?php
/**
 * File containing the expStaticDegrader class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * What a static copy cannot do, found in its pages and given a fallback (static publishing,
 * doc/eng-us/guides/static-publishing-dynamic-features.md). Pure: a page in (after the link rewriter), the page with
 * its fallbacks out, and the report of what was found.
 *
 * The features and what happens to them:
 *
 *   search    a search form (an action to content/search, or a GET form with SearchText): with Search=enabled it
 *             goes to the build's own search page (search.html, expStaticSearchIndex); otherwise to the live site
 *   collect   an information collector (a form with ActionCollectInformation): with Forms=endpoint and a live site
 *             it posts to the form endpoint there (staticpublish/form) with a token signed at build time
 *             (expStaticFormToken); with Forms=hide it is replaced by the fallback text
 *   comments  a comment form: replaced by the fallback text (comments need a session)
 *   shop      a basket or shop form: replaced by the fallback text
 *   login     a login form: taken out (the links to the login go to the live site, as every link the copy does
 *             not hold)
 *   ajax      a page that calls the server from a script (ezjscore): reported; it calls the live site or nothing
 *   form      any other form that posts: left as it is, so it posts to the live site; reported
 *
 * The fallback text is a <p class="exp-static-fallback"> with a link to the same page on the live site when one is
 * known; a site styles it with that class.
 *
 * @since 6.0.15
 * @package kernel
 */
class expStaticDegrader
{
    /** The features, in the order of the report */
    const FEATURES = array( 'search', 'collect', 'comments', 'shop', 'login', 'ajax', 'form' );

    /** What happened to a feature */
    const SEARCH_PAGE = 'search-page';
    const ENDPOINT = 'endpoint';
    const FALLBACK = 'fallback';
    const REMOVED = 'removed';
    const LIVE = 'live';

    /** The file of the search page in the build */
    const SEARCH_FILE = 'search.html';

    /** The fields the form endpoint reads besides the form's own */
    const TARGET_FIELD = 'ExpStaticTarget';
    const TOKEN_FIELD = 'ExpStaticToken';

    /** The default fallback text and link text */
    const FALLBACK_TEXT = 'This form works on the live site only.';
    const FALLBACK_LINK = 'Open this page there';

    /** How many page paths the report names per feature */
    const EXAMPLES = 10;

    private $options;
    private $report = array();

    /**
     * @param array $options target (the target id), search (bool: the build has a search page), forms (endpoint or
     *        hide), endpoint (the address of the form endpoint, '' when there is no live site), token
     *        (callable( $objectId ) => string, the signed token), link (callable( $file, $fromFile ) => string, a link
     *        in the build), live_url (https://www.example.com, '' when unknown), fallback_text, fallback_link
     */
    public function __construct( array $options = array() )
    {
        $this->options = $options + array( 'target' => '', 'search' => false, 'forms' => 'hide', 'endpoint' => '', 'token' => null,
                                           'link' => null, 'live_url' => '', 'fallback_text' => self::FALLBACK_TEXT,
                                           'fallback_link' => self::FALLBACK_LINK );
        $this->options['live_url'] = rtrim( (string)$this->options['live_url'], '/' );
    }

    /**
     * The features of a page, without changing it. Pure.
     *
     * @param string $html
     * @return array feature => how many times (only those found)
     */
    public static function features( $html )
    {
        $found = array();
        foreach ( self::forms( (string)$html ) as $form )
        {
            $feature = self::classify( $form['open'], $form['body'] );
            if ( $feature !== null )
                $found[$feature] = ( isset( $found[$feature] ) ? $found[$feature] : 0 ) + 1;
        }
        if ( self::callsServer( (string)$html ) )
            $found['ajax'] = 1;
        return $found;
    }

    /**
     * The forms of a page: each with its offset, the whole form, its opening tag and its content.
     *
     * @param string $html
     * @return array[]
     */
    public static function forms( $html )
    {
        $forms = array();
        if ( preg_match_all( '#(<form\b[^>]*>)(.*?)</form\s*>#is', (string)$html, $all, PREG_SET_ORDER | PREG_OFFSET_CAPTURE ) )
            foreach ( $all as $m )
                $forms[] = array( 'offset' => $m[0][1], 'whole' => $m[0][0], 'open' => $m[1][0], 'body' => $m[2][0] );
        return $forms;
    }

    /**
     * Which feature a form is; null for a GET form that is not a search.
     *
     * @param string $open the opening tag
     * @param string $body the content
     * @return string|null
     */
    public static function classify( $open, $body )
    {
        $action = html_entity_decode( (string)self::attribute( $open, 'action' ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
        $method = strtolower( (string)self::attribute( $open, 'method' ) );
        if ( preg_match( '#\bname\s*=\s*(["\']?)ActionCollectInformation\1#i', $body ) )
            return 'collect';
        if ( preg_match( '#\bname\s*=\s*(["\']?)(ActionAddToBasket|ActionAddToWishList|AddToBasket)\1#i', $body ) || preg_match( '#/shop/#i', $action ) )
            return 'shop';
        if ( preg_match( '#/(comment|ezcomments)/|/comments?/add\b#i', $action ) || preg_match( '#\bname\s*=\s*(["\']?)(AddCommentButton|PostCommentButton|ezcomments_\w*)\1#i', $body ) )
            return 'comments';
        if ( preg_match( '#/user/login\b#i', $action ) || ( preg_match( '#type\s*=\s*(["\']?)password\1#i', $body ) && preg_match( '#\bname\s*=\s*(["\']?)Login\1#i', $body ) ) )
            return 'login';
        if ( preg_match( '#/content/search\b#i', $action ) || ( $method !== 'post' && preg_match( '#\bname\s*=\s*(["\']?)SearchText\1#i', $body ) ) )
            return 'search';
        return $method === 'post' ? 'form' : null;
    }

    /** Whether a page calls the server from a script (ezjscore). */
    public static function callsServer( $html )
    {
        return (bool)preg_match( '#ezjscore/(call|run)\b|ezjscServer|\$\.ez\(|\bjQuery\.ez\(|\bY\.io\.ez\(#', (string)$html );
    }

    /**
     * A page with its fallbacks, and the report updated.
     *
     * @param string $html the page as written (links already rewritten)
     * @param string $documentPath its path on the site (/about-us), for the link to the live site
     * @param string $fromFile its file in the build (about-us/index.html)
     * @return string
     */
    public function apply( $html, $documentPath, $fromFile )
    {
        $html = (string)$html;
        $out = '';
        $position = 0;
        foreach ( self::forms( $html ) as $form )
        {
            $feature = self::classify( $form['open'], $form['body'] );
            if ( $feature === null )
                continue;
            list( $replacement, $handled ) = $this->degrade( $feature, $form, $documentPath, $fromFile );
            $this->note( $feature, $handled, $documentPath );
            $out .= substr( $html, $position, $form['offset'] - $position ) . $replacement;
            $position = $form['offset'] + strlen( $form['whole'] );
        }
        $out .= substr( $html, $position );
        if ( self::callsServer( $out ) )
            $this->note( 'ajax', self::LIVE, $documentPath );
        return $out;
    }

    /** @return array the replacement of a form and what happened to it */
    private function degrade( $feature, array $form, $documentPath, $fromFile )
    {
        switch ( $feature )
        {
            case 'search':
                if ( !$this->options['search'] || !is_callable( $this->options['link'] ) )
                    return array( $form['whole'], self::LIVE );
                $open = self::setAttribute( self::setAttribute( $form['open'], 'action', (string)call_user_func( $this->options['link'], self::SEARCH_FILE, $fromFile ) ), 'method', 'get' );
                $open = self::setAttribute( $open, 'data-exp-static', 'search' );
                return array( $open . $form['body'] . '</form>', self::SEARCH_PAGE );

            case 'collect':
                $objectId = self::fieldValue( $form['body'], 'ContentObjectID' );
                if ( $this->options['forms'] === 'endpoint' && $this->options['endpoint'] !== '' && ctype_digit( (string)$objectId ) && is_callable( $this->options['token'] ) )
                {
                    $open = self::setAttribute( self::setAttribute( $form['open'], 'action', (string)$this->options['endpoint'] ), 'method', 'post' );
                    $open = self::setAttribute( $open, 'data-exp-static', 'form' );
                    $hidden = "\n" . '<input type="hidden" name="' . self::TARGET_FIELD . '" value="' . htmlspecialchars( (string)$this->options['target'], ENT_QUOTES, 'UTF-8' ) . '" />'
                            . '<input type="hidden" name="' . self::TOKEN_FIELD . '" value="' . htmlspecialchars( (string)call_user_func( $this->options['token'], (int)$objectId ), ENT_QUOTES, 'UTF-8' ) . '" />';
                    return array( $open . $hidden . $form['body'] . '</form>', self::ENDPOINT );
                }
                return array( $this->fallback( $documentPath ), self::FALLBACK );

            case 'comments':
            case 'shop':
                return array( $this->fallback( $documentPath ), self::FALLBACK );

            case 'login':
                return array( '', self::REMOVED );
        }
        return array( $form['whole'], self::LIVE );
    }

    /** The fallback text, with the link to the page on the live site when one is known. */
    public function fallback( $documentPath )
    {
        $text = htmlspecialchars( (string)$this->options['fallback_text'], ENT_QUOTES, 'UTF-8' );
        if ( $this->options['live_url'] !== '' )
            $text .= ' <a href="' . htmlspecialchars( $this->options['live_url'] . '/' . ltrim( (string)$documentPath, '/' ), ENT_QUOTES, 'UTF-8' ) . '">'
                   . htmlspecialchars( (string)$this->options['fallback_link'], ENT_QUOTES, 'UTF-8' ) . '</a>';
        return '<p class="exp-static-fallback">' . $text . '</p>';
    }

    /** Counts one feature on one page. */
    private function note( $feature, $handled, $documentPath )
    {
        if ( !isset( $this->report[$feature] ) )
            $this->report[$feature] = array( 'count' => 0, 'pages' => 0, 'handled' => array(), 'examples' => array() );
        $entry = &$this->report[$feature];
        $entry['count']++;
        $entry['handled'][$handled] = ( isset( $entry['handled'][$handled] ) ? $entry['handled'][$handled] : 0 ) + 1;
        if ( !in_array( (string)$documentPath, $entry['examples'], true ) )
        {
            $entry['pages']++;
            if ( count( $entry['examples'] ) < self::EXAMPLES )
                $entry['examples'][] = (string)$documentPath;
        }
    }

    /**
     * The report: what was found, on how many pages, and what happened to it, in the order of FEATURES.
     *
     * @return array feature => count, pages, handled (what happened => how many), examples (page paths)
     */
    public function report()
    {
        $out = array();
        foreach ( self::FEATURES as $feature )
            if ( isset( $this->report[$feature] ) )
                $out[$feature] = $this->report[$feature];
        return $out;
    }

    /** The same report, merged into another (for a partial build that keeps the rest). Pure. */
    public static function merge( array $a, array $b )
    {
        foreach ( $b as $feature => $entry )
        {
            if ( !isset( $a[$feature] ) )
            {
                $a[$feature] = $entry;
                continue;
            }
            $a[$feature]['count'] += $entry['count'];
            $a[$feature]['pages'] += $entry['pages'];
            foreach ( $entry['handled'] as $k => $n )
                $a[$feature]['handled'][$k] = ( isset( $a[$feature]['handled'][$k] ) ? $a[$feature]['handled'][$k] : 0 ) + $n;
            $a[$feature]['examples'] = array_slice( array_values( array_unique( array_merge( $a[$feature]['examples'], $entry['examples'] ) ) ), 0, self::EXAMPLES );
        }
        return $a;
    }

    /** @return string|null the value of an attribute of a tag, decoded quotes left alone */
    public static function attribute( $tag, $name )
    {
        if ( preg_match( '#\s' . preg_quote( $name, '#' ) . '\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s"\'>]+))#i', (string)$tag, $m ) )
            return isset( $m[3] ) ? $m[3] : ( isset( $m[2] ) && $m[2] !== '' ? $m[2] : $m[1] );
        return null;
    }

    /** A tag with an attribute set (replaced, or added before its end). */
    public static function setAttribute( $tag, $name, $value )
    {
        $pair = $name . '="' . htmlspecialchars( (string)$value, ENT_QUOTES, 'UTF-8' ) . '"';
        $pattern = '#(\s)' . preg_quote( $name, '#' ) . '\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s"\'>]+)#i';
        if ( preg_match( $pattern, (string)$tag ) )
            return expStaticPostProcessor::checked( preg_replace_callback( $pattern, function ( $m ) use ( $pair ) { return $m[1] . $pair; }, (string)$tag, 1 ), 'degrader' );
        return expStaticPostProcessor::checked( preg_replace( '#\s*(/?)>$#', ' ' . $pair . '$1>', (string)$tag, 1 ), 'degrader' );
    }

    /** @return string|null the value of a field (input) of a form by its name */
    public static function fieldValue( $body, $name )
    {
        if ( preg_match_all( '#<input\b[^>]*>#i', (string)$body, $all ) )
            foreach ( $all[0] as $input )
                if ( self::attribute( $input, 'name' ) === $name )
                    return (string)self::attribute( $input, 'value' );
        return null;
    }
}
