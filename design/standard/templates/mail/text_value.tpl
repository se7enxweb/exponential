{*
    The plain text of a value that is printed as HTML (a datatype's view, e.g. the "info" result view of a collected
    answer), for the text part of a mail: what the reader of the HTML part sees, never markup. A line break, a
    paragraph or a list item between two values becomes ", ", the tags go, the entities are decoded (a value the
    view escaped is written as it was typed) and the white space of each value is one space.

    Variables: html (the HTML). The output is the text alone, on one line; the caller trims it.
    Used by content/collectedinfomail_body.tpl: a selection shows the chosen option's name, as in the HTML part,
    rather than its stored id.
*}
{def $tv_marker = '|exp-mail-break|'
     $tv_html = concat( '', first_set( $html, '' ) )
     $tv_parts = array()
     $tv_part = ''}
{set $tv_html = $tv_html|explode( '<br' )|implode( concat( $tv_marker, '<br' ) )}
{set $tv_html = $tv_html|explode( '<BR' )|implode( concat( $tv_marker, '<BR' ) )}
{set $tv_html = $tv_html|explode( '</p>' )|implode( concat( '</p>', $tv_marker ) )}
{set $tv_html = $tv_html|explode( '</li>' )|implode( concat( '</li>', $tv_marker ) )}
{set $tv_html = $tv_html|explode( '&nbsp;' )|implode( ' ' )}
{set $tv_html = $tv_html|strip_tags|html_entity_decode}
{foreach $tv_html|explode( $tv_marker ) as $tv_raw}
{set $tv_part = $tv_raw|explode( "\r" )|implode( ' ' )|explode( "\n" )|implode( ' ' )|explode( "\t" )|implode( ' ' )|simplify|trim}
{if $tv_part|ne( '' )}{set $tv_parts = $tv_parts|append( $tv_part )}{/if}
{/foreach}
{$tv_parts|implode( ', ' )}
{undef $tv_marker $tv_html $tv_parts $tv_part}
