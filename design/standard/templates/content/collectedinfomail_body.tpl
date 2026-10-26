{*
    The body of the mails of collected information (content/collectinformation): content/collectedinfomail.tpl,
    content/collectedinfomail/form.tpl, content/collectedinfomail/feedback.tpl and the overrides of the designs
    include it after setting the subject and the receivers. It sets content_type (text/html) and body_text (the
    plain-text part) at root scope and prints the HTML part.

    Variables: collection (eZInformationCollection), ci_title (the heading), ci_intro (the first sentence),
    ci_gui (true: each value is the attribute's "info" result view, attribute_result_gui, which escapes its output
    itself, as on the result page; false: the stored text, escaped by the layout).
    The look is design:mail/layout.tpl (HTML) and design:mail/layout_text.tpl (text). In the text part a value is the
    stored text or number, never markup.
*}
{def $ci_html_rows = array()
     $ci_text_rows = array()
     $ci_value = ''
     $ci_text = ''}
{foreach $collection.attributes as $ci_attribute}
    {set $ci_text = cond( $ci_attribute.data_text|ne( '' ), $ci_attribute.data_text,
                          $ci_attribute.data_int|ne( 0 ), $ci_attribute.data_int,
                          $ci_attribute.data_float|ne( 0 ), $ci_attribute.data_float, '' )}
    {if first_set( $ci_gui, false() )}
        {set-block variable=ci_value}{attribute_result_gui view=info attribute=$ci_attribute}{/set-block}
        {set $ci_html_rows = $ci_html_rows|append( hash( 'label', $ci_attribute.contentclass_attribute_name, 'value', $ci_value|trim, 'washed', true() ) )}
    {else}
        {set $ci_html_rows = $ci_html_rows|append( hash( 'label', $ci_attribute.contentclass_attribute_name, 'value', $ci_text ) )}
    {/if}
    {set $ci_text_rows = $ci_text_rows|append( hash( 'label', $ci_attribute.contentclass_attribute_name, 'value', $ci_text ) )}
{/foreach}
{set-block scope=root variable=content_type}text/html{/set-block}
{set-block scope=root variable=body_text}{include uri='design:mail/layout_text.tpl'
    mail_title=$ci_title mail_intro=$ci_intro mail_rows=$ci_text_rows}{/set-block}
{include uri='design:mail/layout.tpl'
    mail_title=$ci_title mail_intro=$ci_intro mail_rows=$ci_html_rows}
{undef $ci_html_rows $ci_text_rows $ci_value $ci_text}
