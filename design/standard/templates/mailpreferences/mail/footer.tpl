{* The footer the mail gate (expMailGate::footer()) adds to every optional e-mail: why the person gets it, how to stop it
   in one click, where to choose, who sends it and from where. mailpreferences.ini [FooterSettings] Template names it.
   Variables: category (expMailCategory), manage_url (the personal preference page, or the "send me a link" page when
   the recipient is not known), unsubscribe_url ('' when the recipient is not known), organisation_name,
   organisation_address (may hold line breaks), is_html (an HTML e-mail: footer_html.tpl is used). *}{* The text footer: the template engine drops a line break next to a tag, so every line break of the text is an
   explicit {"\n"}. A link always ends its line, or mail programs run it into the next word. "-- " (with the space)
   is the signature separator of RFC 3676. *}{if $is_html}{include uri='design:mailpreferences/mail/footer_html.tpl'}{else}{"\n\n-- \n"}{'You get this e-mail because you turned on "%category" on %site.'|i18n( 'design/standard/mailpreferences',, hash( '%category', $category.name|i18n( 'kernel/mailpreferences/categories' ), '%site', ezini( 'SiteSettings', 'SiteName' ) ) )}{"\n"}
{if $unsubscribe_url|ne( '' )}{'Unsubscribe with one click:'|i18n( 'design/standard/mailpreferences' )} {$unsubscribe_url}{"\n"}{/if}
{'Choose which e-mail you get:'|i18n( 'design/standard/mailpreferences' )} {$manage_url}{"\n"}
{if or( $organisation_name|ne( '' ), $organisation_address|ne( '' ) )}{"\n"}{/if}
{if $organisation_name|ne( '' )}{$organisation_name}{"\n"}{/if}
{if $organisation_address|ne( '' )}{$organisation_address}{"\n"}{/if}
{/if}
