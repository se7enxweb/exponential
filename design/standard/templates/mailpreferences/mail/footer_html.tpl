{* The footer of an optional HTML e-mail; footer.tpl includes it when is_html is set. The same variables as footer.tpl.
   Inline styles only: mail programs drop style sheets. *}
<div class="exp-mail-footer" style="margin:24px 0 0;padding:12px 0 0;border-top:1px solid #d9dde3;font:13px/1.5 Arial,Helvetica,sans-serif;color:#5d6573;">
<p style="margin:0 0 6px;">{'You get this e-mail because you turned on "%category" on %site.'|i18n( 'design/standard/mailpreferences',, hash( '%category', $category.name|i18n( 'kernel/mailpreferences/categories' ), '%site', cond( is_set( $site_name ), $site_name, ezini( 'SiteSettings', 'SiteName' ) ) ) )|wash}</p>
<p style="margin:0 0 6px;">{if $unsubscribe_url|ne( '' )}<a href="{$unsubscribe_url|wash}" style="color:#9a3412;">{'Unsubscribe'|i18n( 'design/standard/mailpreferences' )}</a> &middot; {/if}<a href="{$manage_url|wash}" style="color:#9a3412;">{'Manage my e-mail preferences'|i18n( 'design/standard/mailpreferences' )}</a>{if and( is_set( $privacy_url ), $privacy_url|ne( '' ) )} &middot; <a href="{$privacy_url|wash}" style="color:#9a3412;">{'Privacy notice'|i18n( 'design/standard/mailpreferences' )}</a>{/if}</p>
{if or( $organisation_name|ne( '' ), $organisation_address|ne( '' ) )}
<p style="margin:0;">{$organisation_name|wash}{if and( $organisation_name|ne( '' ), $organisation_address|ne( '' ) )}<br />{/if}{$organisation_address|wash|nl2br}</p>
{/if}
</div>
