{*
    The plain-text version of design:mail/layout.tpl, with the same inputs (see there). Values are written as they
    are (a text part is not HTML); buttons and links become "Label: URL" lines.

    Line breaks: the template parser drops one newline right after a tag and one right before a tag, so the blank
    lines below are deliberate (a text between two tags needs three newlines to print one).
*}
{def $mt_title = first_set( $mail_title, '' )
     $mt_intro = first_set( $mail_intro, '' )
     $mt_rows = first_set( $mail_rows, array() )
     $mt_notice = first_set( $mail_notice, '' )
     $mt_buttons = first_set( $mail_buttons, array() )
     $mt_links = first_set( $mail_links, array() )
     $mt_links_note = first_set( $mail_links_note, '' )
     $mt_footer = first_set( $mail_footer, ezini( 'SiteSettings', 'SiteName' ) )}
{if $mt_title|ne( '' )}
{$mt_title}


{/if}
{if $mt_intro|ne( '' )}
{$mt_intro}


{/if}
{if $mt_rows|count}
{foreach $mt_rows as $mt_row}
{$mt_row.label}: {$mt_row.value}

{/foreach}


{/if}
{if $mt_notice|ne( '' )}
{$mt_notice}


{/if}
{if or( $mt_buttons|count, $mt_links|count )}
{foreach $mt_buttons as $mt_button}
{$mt_button.label}: {$mt_button.url}

{/foreach}
{foreach $mt_links as $mt_link}
{$mt_link.label}: {$mt_link.url}

{/foreach}


{/if}
{if $mt_links_note|ne( '' )}
{$mt_links_note}


{/if}
--
{$mt_footer}

{undef $mt_title $mt_intro $mt_rows $mt_notice $mt_buttons $mt_links $mt_links_note $mt_footer}
