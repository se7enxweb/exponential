<?php
/**
 * File containing the expHelpHighlighter class.
 *
 * Server-side syntax highlighting of the code blocks of the help pages (exphelp, doc/features/6.0/help-system.md),
 * without JavaScript or an external library, so it works under the admin's Content Security Policy and with
 * scripts off. highlight() returns HTML: every character of the code HTML-escaped, the recognised tokens wrapped in
 * <span class="a4-help-tok-<kind>">. The kinds (the CSS classes, coloured by the --a4-code-<kind> tokens of
 * components.css):
 *
 *   comment, keyword, string, number, variable, function (also a shell command), tag (markup tag, CSS selector),
 *   attr (attribute, key), operator, constant (true, null ...), meta (open tags, directives, prompts), section
 *   (INI [Section], diff @@ hunk), inserted and deleted (diff lines), property (CSS), type (class names, SQL types).
 *
 * PHP is tokenised with token_get_all() (the PHP lexer, nothing is executed). The other languages use one combined
 * regular expression each (highlightWith()), and Exponential templates and Twig are split into template tags
 * and the markup between them. A language that is not known, or text without a language, is only escaped, unless
 * guessLanguage() recognises it with certainty. Each match is escaped on its own, so a token can never produce
 * markup, and the unmatched text between tokens is escaped too.
 *
 * Extensible: register() adds or replaces a language (a rule list kind => pattern), alias() maps another name of
 * the info string (```shell -> bash) to a language.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpHighlighter
{
    /** Longest code block that is highlighted; a longer one is only escaped (keeps a page cheap to render) */
    const MAX_LENGTH = 200000;

    /** @var array|null language => array( kind => regex fragment ), in priority order */
    protected static $rules = null;

    /** @var array name of the info string => language */
    protected static $aliases = array(
        'php' => 'php', 'php5' => 'php', 'php7' => 'php', 'php8' => 'php', 'phtml' => 'php',
        'tpl' => 'tpl', 'smarty' => 'tpl', 'ezt' => 'tpl', 'eztpl' => 'tpl', 'eztemplate' => 'tpl', 'template' => 'tpl',
        'twig' => 'twig', 'jinja' => 'twig',
        'ini' => 'ini', 'cfg' => 'ini', 'toml' => 'ini', 'properties' => 'ini', 'dosini' => 'ini', 'env' => 'ini',
        'js' => 'js', 'javascript' => 'js', 'mjs' => 'js', 'ts' => 'js', 'typescript' => 'js', 'jsx' => 'js',
        'json' => 'json', 'jsonc' => 'json', 'json5' => 'json',
        'css' => 'css', 'scss' => 'css', 'less' => 'css',
        'bash' => 'bash', 'sh' => 'bash', 'shell' => 'bash', 'zsh' => 'bash', 'console' => 'bash', 'shell-session' => 'bash', 'shellsession' => 'bash',
        'sql' => 'sql', 'mysql' => 'sql', 'sqlite' => 'sql', 'pgsql' => 'sql', 'postgresql' => 'sql', 'plsql' => 'sql',
        'xml' => 'xml', 'html' => 'xml', 'xhtml' => 'xml', 'svg' => 'xml', 'htm' => 'xml', 'rss' => 'xml', 'dba' => 'php',
        'yaml' => 'yaml', 'yml' => 'yaml',
        'diff' => 'diff', 'patch' => 'diff', 'udiff' => 'diff',
        'apache' => 'conf', 'apacheconf' => 'conf', 'htaccess' => 'conf', 'nginx' => 'conf', 'caddy' => 'conf',
        'caddyfile' => 'conf', 'conf' => 'conf', 'systemd' => 'ini', 'dockerfile' => 'conf', 'docker' => 'conf',
        'cron' => 'cron', 'crontab' => 'cron',
        'python' => 'python', 'py' => 'python',
        'text' => 'text', 'txt' => 'text', 'plain' => 'text', 'plaintext' => 'text', 'none' => 'text', 'output' => 'text', 'log' => 'text',
    );

    /** @var array the names shown on a code block, by language */
    protected static $labels = array(
        'php' => 'PHP', 'tpl' => 'Template', 'twig' => 'Twig', 'ini' => 'INI', 'js' => 'JavaScript', 'json' => 'JSON',
        'css' => 'CSS', 'bash' => 'Shell', 'sql' => 'SQL', 'xml' => 'XML / HTML', 'yaml' => 'YAML', 'diff' => 'Diff',
        'conf' => 'Config', 'cron' => 'Cron', 'python' => 'Python', 'text' => 'Text',
    );

    /** @var array short kind => the word of its CSS class (a4-help-tok-<word>) and colour token (--a4-code-<word>) */
    protected static $kinds = array( 'com' => 'comment', 'kw' => 'keyword', 'str' => 'string', 'num' => 'number',
                                     'var' => 'variable', 'fn' => 'function', 'tag' => 'tag', 'attr' => 'attr',
                                     'op' => 'operator', 'const' => 'constant', 'meta' => 'meta', 'sec' => 'section',
                                     'ins' => 'inserted', 'del' => 'deleted', 'prop' => 'property', 'type' => 'type' );

    /**
     * The language for the info string of a fence (```php, ```PHP {.x}, ```language-php): a known language name,
     * else '' (no highlighting).
     *
     * @param string $info
     * @return string
     */
    public static function language( $info )
    {
        $info = strtolower( trim( (string)$info ) );
        if ( preg_match( '/^(?:language-)?([a-z0-9_+#.-]+)/', $info, $m ) !== 1 )
            return '';
        $name = rtrim( $m[1], '.' );
        return isset( self::$aliases[$name] ) ? self::$aliases[$name] : '';
    }

    /**
     * A label for the language ("PHP", "Shell" ...), '' for none.
     *
     * @param string $language
     * @return string
     */
    public static function label( $language )
    {
        return isset( self::$labels[$language] ) ? self::$labels[$language] : '';
    }

    /**
     * Map another info string name to a language.
     *
     * @param string $name
     * @param string $language
     */
    public static function alias( $name, $language )
    {
        self::$aliases[strtolower( $name )] = $language;
    }

    /**
     * Add or replace a language: an ordered list kind => regex fragment (no delimiters, no anchors other than ^ and
     * $ in multiline mode, no capturing groups; use (?: ) for groups). Kinds outside the known list are ignored.
     *
     * @param string $language
     * @param array $rules
     * @param string $label
     */
    public static function register( $language, array $rules, $label = '' )
    {
        self::rules();
        $clean = array();
        foreach ( $rules as $kind => $pattern )
        {
            // a kind may be given by its short name or its word; a key may carry #n to repeat a kind
            $kind = preg_replace( '/#.*$/', '', $kind );
            $short = array_search( $kind, self::$kinds, true );
            if ( $short !== false )
                $kind = $short;
            if ( isset( self::$kinds[$kind] ) )
                $clean[] = array( $kind, $pattern );
        }
        self::$rules[$language] = $clean;
        self::$aliases[$language] = $language;
        if ( $label !== '' )
            self::$labels[$language] = $label;
    }

    /**
     * A language for code without one, only when it is recognised with certainty, else 'text'.
     *
     * @param string $code
     * @return string
     */
    public static function guessLanguage( $code )
    {
        $trim = ltrim( $code );
        if ( $trim === '' )
            return 'text';
        if ( strncmp( $trim, '<?php', 5 ) === 0 )
            return 'php';
        if ( strncmp( $trim, '<?xml', 5 ) === 0 )
            return 'xml';
        if ( preg_match( '/^(?:diff --git |--- \S.*\n\+\+\+ \S|@@ -\d)/', $trim ) === 1 )
            return 'diff';
        if ( ( $trim[0] === '{' || $trim[0] === '[' ) && json_decode( $trim ) !== null && preg_match( '/^\[[A-Za-z]/', $trim ) !== 1 )
            return 'json';
        if ( preg_match( '/^\s*\[[A-Za-z][\w .:-]*\]\s*$/m', $code ) === 1 && preg_match( '/^\s*[A-Za-z][\w.]*(?:\[[^\]\n]*\])?\s*=/m', $code ) === 1 )
            return 'ini';
        if ( preg_match( '/\{(?:def|set|if|foreach|include|undef|cache-block|section|let|literal|\*|\$[A-Za-z])/', $code ) === 1
             && preg_match( '/\{\/(?:if|foreach|def|cache-block|section|let)\}|\{\$[A-Za-z][\w.]*(?:\|[a-z_]+)?[^}]*\}/', $code ) === 1 )
            return 'tpl';
        if ( preg_match( '/^(?:\$ |# )?(?:sudo |nice -n \d+ )?(?:php |\.\/console |git |composer |cd |systemctl |bash |sh |ls |cp |mv |mkdir |chmod |chown |curl |wget |npm |yarn |gh |mysql |sqlite3 |tar |grep |find |export |echo |ssh |docker |\.\/[\w.\/-]+\.sh\b|bin\/)/m', $trim ) === 1
             && strpos( $trim, '{' ) !== 0 && preg_match( '/^\s*\[/', $trim ) !== 1 )
            return 'bash';
        if ( preg_match( '/^\s*(?:SELECT|INSERT INTO|UPDATE|DELETE FROM|CREATE TABLE|ALTER TABLE|DROP TABLE)\b/i', $trim ) === 1 )
            return 'sql';
        // PHP statements without an open tag: a variable assigned or called, a static call, lines ending in ;
        if ( preg_match( '/\$[A-Za-z_]\w*\s*(?:=[^=]|->\w)|\b[A-Za-z_]\w*::[A-Za-z_]\w*\s*\(/', $code ) === 1
             && preg_match( '/;\s*$/m', $code ) === 1 && preg_match( '/^\s*[{\[]|^\s*\$ /', $trim ) !== 1 )
            return 'php';
        return 'text';
    }

    /**
     * The code as escaped HTML with token spans.
     *
     * @param string $code
     * @param string $language a language from language() (other names are mapped through it)
     * @return string
     */
    public static function highlight( $code, $language = '' )
    {
        $code = (string)$code;
        if ( !isset( self::$labels[$language] ) && !isset( self::rules()[$language] ) )
            $language = self::language( $language );
        if ( $code === '' || $language === '' || $language === 'text' || strlen( $code ) > self::MAX_LENGTH
             || !preg_match( '//u', $code ) )
            return self::escape( $code );
        switch ( $language )
        {
            case 'php':
                return self::highlightPhp( $code );
            case 'tpl':
                return self::highlightTemplate( $code, '/\{\*.*?\*\}|\{(?=[\/$a-zA-Z"\'])(?:[^{}"\'\n]|"[^"\n]*"|\'[^\'\n]*\')*\}/s', 'tplTag' );
            case 'twig':
                return self::highlightTemplate( $code, '/\{#.*?#\}|\{\{.*?\}\}|\{%.*?%\}/s', 'twigTag' );
        }
        $rules = self::rules();
        if ( !isset( $rules[$language] ) )
            return self::escape( $code );
        return self::highlightWith( $code, $rules[$language] );
    }

    /**
     * Escape text for HTML (also what highlight() does with every token).
     *
     * @param string $text
     * @return string
     */
    public static function escape( $text )
    {
        return htmlspecialchars( (string)$text, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8' );
    }

    /**
     * One span.
     *
     * @param string $kind
     * @param string $text
     * @return string
     */
    protected static function span( $kind, $text )
    {
        if ( $text === '' )
            return '';
        if ( !isset( self::$kinds[$kind] ) )
            return self::escape( $text );
        // a token over several lines is closed and reopened on every line, so a line never holds an open span
        $open = '<span class="a4-help-tok-' . self::$kinds[$kind] . '">';
        $out = array();
        foreach ( explode( "\n", $text ) as $line )
            $out[] = $line === '' ? '' : $open . self::escape( $line ) . '</span>';
        return implode( "\n", $out );
    }

    /**
     * Tokenise with a list of ( kind, pattern ): one alternation, each alternative a named group.
     *
     * @param string $code
     * @param array $rules list of array( kind, pattern )
     * @return string
     */
    protected static function highlightWith( $code, array $rules )
    {
        if ( !$rules )
            return self::escape( $code );
        $parts = array();
        $names = array();
        foreach ( array_values( $rules ) as $i => $rule )
        {
            $names['g' . $i] = $rule[0];
            $parts[] = '(?<g' . $i . '>' . $rule[1] . ')';
        }
        $regex = '/' . implode( '|', $parts ) . '/mu';
        if ( @preg_match_all( $regex, $code, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE ) === false )
            return self::escape( $code );
        $out = '';
        $pos = 0;
        foreach ( $matches as $match )
        {
            $text = $match[0][0];
            $offset = $match[0][1];
            if ( $text === '' )
                continue;
            $kind = null;
            foreach ( $names as $group => $k )
            {
                if ( isset( $match[$group] ) && $match[$group][1] !== -1 && $match[$group][0] !== '' )
                {
                    $kind = $k;
                    break;
                }
            }
            $out .= self::escape( substr( $code, $pos, $offset - $pos ) );
            $out .= $kind === null ? self::escape( $text ) : self::span( $kind, $text );
            $pos = $offset + strlen( $text );
        }
        return $out . self::escape( substr( $code, $pos ) );
    }

    /**
     * PHP through the PHP lexer. Code without an open tag is lexed with one added (and the added tag removed).
     *
     * @param string $code
     * @return string
     */
    protected static function highlightPhp( $code )
    {
        $added = false;
        if ( !preg_match( '/^\s*<\?(?:php|=)/', $code ) )
        {
            $code = "<?php " . $code;
            $added = true;
        }
        $tokens = token_get_all( $code );
        $out = '';
        $count = count( $tokens );
        for ( $i = 0; $i < $count; $i++ )
        {
            $token = $tokens[$i];
            if ( !is_array( $token ) )
            {
                if ( $token === '"' || $token === '`' )
                    $out .= self::span( 'str', $token );
                else
                    $out .= strpos( '=+-*/%<>!&|^~?:.', $token ) !== false ? self::span( 'op', $token ) : self::escape( $token );
                continue;
            }
            list( $id, $text ) = $token;
            if ( $added && $i === 0 && $id === T_OPEN_TAG )
            {
                $text = substr( $text, 6 );
                if ( $text === false || $text === '' )
                    continue;
                $out .= self::escape( $text );
                continue;
            }
            $out .= self::phpToken( $id, $text, $tokens, $i );
        }
        return $out;
    }

    /**
     * One PHP token.
     *
     * @param int $id
     * @param string $text
     * @param array $tokens
     * @param int $i
     * @return string
     */
    protected static function phpToken( $id, $text, array $tokens, $i )
    {
        switch ( $id )
        {
            case T_COMMENT:
            case T_DOC_COMMENT:
                return self::span( 'com', $text );
            case T_CONSTANT_ENCAPSED_STRING:
            case T_ENCAPSED_AND_WHITESPACE:
            case T_START_HEREDOC:
            case T_END_HEREDOC:
                return self::span( 'str', $text );
            case T_VARIABLE:
                return self::span( 'var', $text );
            case T_LNUMBER:
            case T_DNUMBER:
                return self::span( 'num', $text );
            case T_OPEN_TAG:
            case T_OPEN_TAG_WITH_ECHO:
            case T_CLOSE_TAG:
                return self::span( 'meta', $text );
            case T_INLINE_HTML:
                return self::highlightWith( $text, self::rules()['xml'] );
            case T_WHITESPACE:
                return self::escape( $text );
            case T_STRING:
            case T_NAME_QUALIFIED:
            case T_NAME_FULLY_QUALIFIED:
            case T_NAME_RELATIVE:
                $lower = strtolower( $text );
                if ( in_array( $lower, array( 'true', 'false', 'null' ), true ) )
                    return self::span( 'const', $text );
                $next = self::nextSignificant( $tokens, $i );
                $prev = self::previousSignificant( $tokens, $i );
                if ( $next === '(' && !( is_array( $prev ) && $prev[0] === T_NEW ) )
                    return self::span( 'fn', $text );
                if ( $next === '::' || ( is_array( $prev ) && in_array( $prev[0], array( T_NEW, T_EXTENDS, T_IMPLEMENTS, T_INSTANCEOF, T_CLASS, T_INTERFACE, T_TRAIT ), true ) )
                     || preg_match( '/^\\\\?[A-Z]/', $text ) === 1 && $next !== '->' )
                    return self::span( 'type', $text );
                if ( preg_match( '/^[A-Z][A-Z0-9_]+$/', $text ) === 1 )
                    return self::span( 'const', $text );
                return self::escape( $text );
            case T_OBJECT_OPERATOR:
            case T_NULLSAFE_OBJECT_OPERATOR:
            case T_DOUBLE_COLON:
            case T_DOUBLE_ARROW:
                return self::span( 'op', $text );
        }
        if ( preg_match( '/^[a-z_]+$/i', $text ) === 1 )
            return self::span( 'kw', $text );
        if ( preg_match( '/^[=+\-*\/%<>!&|^~?:.]+$/', $text ) === 1 )
            return self::span( 'op', $text );
        return self::escape( $text );
    }

    /**
     * The next token that is not white space: a one-character string, the text of '::' / '->', or null.
     */
    protected static function nextSignificant( array $tokens, $i )
    {
        for ( $j = $i + 1, $n = count( $tokens ); $j < $n; $j++ )
        {
            $t = $tokens[$j];
            if ( is_array( $t ) )
            {
                if ( $t[0] === T_WHITESPACE )
                    continue;
                if ( $t[0] === T_DOUBLE_COLON || $t[0] === T_OBJECT_OPERATOR )
                    return $t[1];
                return null;
            }
            return $t;
        }
        return null;
    }

    /**
     * The previous token that is not white space (array or string), or null.
     */
    protected static function previousSignificant( array $tokens, $i )
    {
        for ( $j = $i - 1; $j >= 0; $j-- )
        {
            $t = $tokens[$j];
            if ( is_array( $t ) && $t[0] === T_WHITESPACE )
                continue;
            return $t;
        }
        return null;
    }

    /**
     * A template language: the template tags found by $tagRegex, highlighted by $method, and the markup between them
     * as XML / HTML.
     *
     * @param string $code
     * @param string $tagRegex
     * @param string $method
     * @return string
     */
    protected static function highlightTemplate( $code, $tagRegex, $method )
    {
        if ( @preg_match_all( $tagRegex, $code, $matches, PREG_OFFSET_CAPTURE ) === false )
            return self::escape( $code );
        $xml = self::rules()['xml'];
        $out = '';
        $pos = 0;
        foreach ( $matches[0] as $match )
        {
            list( $text, $offset ) = $match;
            $out .= self::highlightWith( substr( $code, $pos, $offset - $pos ), $xml );
            $out .= self::$method( $text );
            $pos = $offset + strlen( $text );
        }
        return $out . self::highlightWith( substr( $code, $pos ), $xml );
    }

    /**
     * One Exponential template tag: {* comment *}, {$var|op(...)}, {function name=value}, {/function}.
     */
    protected static function tplTag( $tag )
    {
        if ( strncmp( $tag, '{*', 2 ) === 0 )
            return self::span( 'com', $tag );
        $inner = substr( $tag, 1, -1 );
        $out = self::span( 'meta', '{' );
        if ( preg_match( '/^(\/?)([a-z][\w-]*)/i', $inner, $m ) === 1 )
        {
            $out .= self::span( 'kw', $m[0] );
            $inner = (string)substr( $inner, strlen( $m[0] ) );
        }
        $out .= self::highlightWith( $inner, array(
            array( 'str', '"(?:[^"\\\\\n]|\\\\.)*"|\'(?:[^\'\\\\\n]|\\\\.)*\'' ),
            array( 'var', '\$[A-Za-z_#][\w]*(?:\.[\w$]+)*' ),
            array( 'fn', '\|[a-z_][\w]*|\b[a-z_][\w]*(?=\()' ),
            array( 'attr', '\b[a-z_][\w-]*(?=\s*=)' ),
            array( 'const', '\b(?:true|false|null)(?=\(\))' ),
            array( 'num', '\b\d+(?:\.\d+)?\b' ),
            array( 'op', '[=<>!]=?|\|\||&&' ),
        ) );
        return $out . self::span( 'meta', '}' );
    }

    /**
     * One Twig tag: {# comment #}, {{ expression }}, {% statement %}.
     */
    protected static function twigTag( $tag )
    {
        if ( strncmp( $tag, '{#', 2 ) === 0 )
            return self::span( 'com', $tag );
        $open = substr( $tag, 0, 2 );
        $close = substr( $tag, -2 );
        $inner = substr( $tag, 2, -2 );
        $out = self::span( 'meta', $open );
        if ( $open === '{%' && preg_match( '/^(\s*-?\s*)([a-z]+)/', $inner, $m ) === 1 )
        {
            $out .= self::escape( $m[1] ) . self::span( 'kw', $m[2] );
            $inner = (string)substr( $inner, strlen( $m[0] ) );
        }
        $out .= self::highlightWith( $inner, array(
            array( 'str', '"(?:[^"\\\\\n]|\\\\.)*"|\'(?:[^\'\\\\\n]|\\\\.)*\'' ),
            array( 'kw', '\b(?:in|is|not|and|or|as|with|only|if|else)\b' ),
            array( 'const', '\b(?:true|false|null|none)\b' ),
            array( 'fn', '\|\s*[a-z_]\w*|\b[a-z_]\w*(?=\()' ),
            array( 'num', '\b\d+(?:\.\d+)?\b' ),
            array( 'var', '\b[a-z_]\w*(?:\.\w+)*' ),
            array( 'op', '[=<>!]=?|~|\?\?' ),
        ) );
        return $out . self::span( 'meta', $close );
    }

    /**
     * The rule lists of the regex-based languages.
     *
     * @return array
     */
    protected static function rules()
    {
        if ( self::$rules !== null )
            return self::$rules;
        $dq = '"(?:[^"\\\\]|\\\\.)*"';
        $sq = '\'(?:[^\'\\\\]|\\\\.)*\'';
        $dqLine = '"(?:[^"\\\\\n]|\\\\.)*"';
        $sqLine = '\'(?:[^\'\\\\\n]|\\\\.)*\'';
        $num = '\b(?:0x[0-9a-fA-F]+|\d+(?:\.\d+)?(?:[eE][+-]?\d+)?)\b';
        $defs = array(
            'bash' => array(
                array( 'com', '(?:^|(?<=\s))#.*$' ),
                array( 'meta', '^\s*(?:\$|#|>|PS>)(?= )' ),
                array( 'str', $dq . '|\'[^\']*\'' ),
                array( 'var', '\$\{[^}\n]*\}|\$\(|\$[A-Za-z_]\w*|\$[0-9@#?$!*-]' ),
                array( 'kw', '\b(?:if|then|else|elif|fi|for|while|until|do|done|case|esac|in|function|return|local|export|readonly|declare|unset|break|continue|exit|source|set|shift|trap|eval|exec|select|time|sudo)\b' ),
                array( 'attr', '(?<=\s)--?[A-Za-z][\w-]*(?==)?' ),
                array( 'fn', '(?:^[ \t]*|(?<=^\$ )|(?<=[|;&] )|(?<=\$\()|(?<=sudo )|(?<=xargs ))(?:\.\/|\/)?[A-Za-z_.][\w.\/:-]*' ),
                array( 'num', '(?<![\w.\/-])\d+(?![\w.\/-])' ),
                array( 'op', '&&|\|\||[|;&]|>>?|<<?|2>&1' ),
            ),
            'ini' => array(
                array( 'com', '^\s*[#;].*$|^\s*\/\*.*?\*\/|^\s*\*\/?.*$' ),
                array( 'meta', '<\?php.*$|\?>' ),
                array( 'sec', '^\s*\[[^\]\n]*\]' ),
                array( 'attr', '^\s*[^=\n\[#;\s][^=\n]*?(?:\[[^\]\n]*\])?(?=\s*=)' ),
                array( 'op', '=' ),
                array( 'str', $dqLine . '|' . $sqLine ),
                array( 'const', '\b(?:true|false|enabled|disabled|yes|no|on|off|null)\b' ),
                array( 'num', $num ),
            ),
            'sql' => array(
                array( 'com', '--.*$|#.*$|\/\*[\s\S]*?\*\/' ),
                array( 'str', $sq . '|' . $dq . '|`[^`]*`' ),
                array( 'kw', '(?i)\b(?:select|from|where|and|or|not|insert|into|values|update|set|delete|create|table|alter|drop|index|unique|primary|key|foreign|references|join|left|right|inner|outer|cross|on|as|group|by|order|having|limit|offset|union|all|distinct|case|when|then|else|end|is|null|in|like|between|exists|default|auto_increment|engine|charset|collate|constraint|if|begin|commit|rollback|transaction|asc|desc|with|returning|add|column|rename|to|using|view|trigger|database|schema|grant|revoke|show|describe|explain|truncate|replace|ignore|pragma)\b(?-i)' ),
                array( 'type', '(?i)\b(?:int|integer|bigint|smallint|tinyint|varchar|char|text|longtext|mediumtext|blob|longblob|float|double|decimal|numeric|date|datetime|timestamp|boolean|bool|serial|clob|number|varchar2)\b(?-i)' ),
                array( 'fn', '\b[A-Za-z_]\w*(?=\()' ),
                array( 'num', $num ),
                array( 'op', '[=<>!]=?|<>|\|\||[*+\/-]' ),
            ),
            'json' => array(
                array( 'attr', $dqLine . '(?=\s*:)' ),
                array( 'str', $dqLine ),
                array( 'const', '\b(?:true|false|null)\b' ),
                array( 'num', '-?\b\d+(?:\.\d+)?(?:[eE][+-]?\d+)?\b' ),
                array( 'com', '\/\/.*$|\/\*[\s\S]*?\*\/' ),
            ),
            'yaml' => array(
                array( 'com', '(?:^|(?<=\s))#.*$' ),
                array( 'meta', '^(?:---|\.\.\.)\s*$|^%.*$' ),
                array( 'attr', '^\s*(?:-\s+)?[^\s#:\'"\-][^:\n]*?(?=:(?:\s|$))' ),
                array( 'str', $dqLine . '|' . $sqLine ),
                array( 'var', '[&*][A-Za-z_][\w-]*' ),
                array( 'const', '\b(?:true|false|null|yes|no|on|off|~)\b' ),
                array( 'num', '(?<![\w.-])-?\d+(?:\.\d+)?(?![\w.-])' ),
                array( 'op', '^\s*-(?=\s)|[|>][-+]?(?=\s*$)' ),
            ),
            'css' => array(
                array( 'com', '\/\*[\s\S]*?\*\/' ),
                array( 'str', $dqLine . '|' . $sqLine ),
                array( 'kw', '@[\w-]+|!important\b' ),
                array( 'var', '--[\w-]+' ),
                array( 'prop', '(?<=[{;\s])-?[a-z][\w-]*(?=\s*:(?![a-z-]+(?:\([^)]*\))?\s*[{,]))' ),
                array( 'fn', '\b[a-z-]+(?=\()' ),
                array( 'num', '#[0-9a-fA-F]{3,8}\b|(?<![\w-])-?\d*\.?\d+(?:%|[a-z]+\b)?' ),
                array( 'tag', '(?<![\w-])[.#][A-Za-z_][\w-]*|::?[a-z-]+' ),
                array( 'op', '[>+~*]|=' ),
            ),
            'js' => array(
                array( 'com', '\/\/.*$|\/\*[\s\S]*?\*\/' ),
                array( 'str', $dqLine . '|' . $sqLine . '|`(?:[^`\\\\]|\\\\.)*`' ),
                array( 'kw', '\b(?:var|let|const|function|return|if|else|for|while|do|switch|case|default|break|continue|new|delete|typeof|instanceof|in|of|try|catch|finally|throw|class|extends|super|import|export|from|async|await|yield|static|get|set|void|interface|type|enum|implements)\b' ),
                array( 'const', '\b(?:true|false|null|undefined|this|NaN|Infinity)\b' ),
                array( 'type', '\b[A-Z][\w$]*\b' ),
                array( 'fn', '\b[a-z_$][\w$]*(?=\s*\()' ),
                array( 'num', $num ),
                array( 'op', '=>|===?|!==?|[<>]=?|&&|\|\||\?\?|[+\-*\/%!?:]' ),
            ),
            'xml' => array(
                array( 'com', '<!--[\s\S]*?-->' ),
                array( 'meta', '<\?[\s\S]*?\?>|<!\[CDATA\[|\]\]>|<![A-Za-z][^>]*>' ),
                array( 'tag', '<\/?[A-Za-z][\w:.-]*|\/?>' ),
                array( 'attr', '\b[A-Za-z_:][\w:.-]*(?==)' ),
                array( 'str', '"[^"\n]*"|\'[^\'\n]*\'' ),
                array( 'const', '&(?:[a-zA-Z]+|#\d+|#x[0-9a-fA-F]+);' ),
            ),
            'diff' => array(
                array( 'meta', '^(?:diff |index |\+\+\+ |--- |new file|deleted file|similarity|rename ).*$' ),
                array( 'sec', '^@@.*$' ),
                array( 'ins', '^\+.*$' ),
                array( 'del', '^-.*$' ),
            ),
            'conf' => array(
                array( 'com', '(?:^|(?<=\s))#.*$' ),
                array( 'tag', '<\/?[A-Za-z][\w]*|>' ),
                array( 'str', $dqLine . '|' . $sqLine ),
                array( 'var', '\$\{[^}\n]*\}|\$[A-Za-z_]\w*|%\{[^}\n]*\}' ),
                array( 'kw', '^\s*[A-Za-z_][\w.-]*' ),
                array( 'const', '\b(?:on|off|true|false|yes|no|all|none|granted|denied)\b' ),
                array( 'num', '(?<![\w.\/-])\d+(?:\.\d+)*[kKmMgGsh]?(?![\w\/-])' ),
                array( 'op', '[{};=]' ),
            ),
            'cron' => array(
                array( 'com', '^\s*#.*$' ),
                array( 'attr', '^\s*[A-Z_]+(?==)' ),
                array( 'num', '^\s*(?:@\w+|(?:[\d*\/,-]+\s+){4}[\d*\/,A-Za-z-]+)' ),
                array( 'var', '\$\{?[A-Za-z_]\w*\}?' ),
                array( 'str', $dqLine . '|' . $sqLine ),
                array( 'op', '&&|\|\||>>?|2>&1|[|;&]' ),
            ),
            'python' => array(
                array( 'com', '#.*$' ),
                array( 'str', '(?:[rbfuRBFU]{1,2})?(?:"""[\s\S]*?"""|\'\'\'[\s\S]*?\'\'\'|' . $dqLine . '|' . $sqLine . ')' ),
                array( 'meta', '^\s*@[\w.]+' ),
                array( 'kw', '\b(?:def|class|return|if|elif|else|for|while|in|not|and|or|is|import|from|as|with|try|except|finally|raise|pass|break|continue|lambda|yield|global|nonlocal|assert|del|async|await)\b' ),
                array( 'const', '\b(?:True|False|None|self)\b' ),
                array( 'fn', '\b[A-Za-z_]\w*(?=\()' ),
                array( 'num', $num ),
                array( 'op', '[=<>!]=?|[+\-*\/%]' ),
            ),
        );
        self::$rules = $defs;
        return self::$rules;
    }
}
