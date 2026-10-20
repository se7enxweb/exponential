{* The license notice under every help page (doc/en/us/README-LICENSE.md, exphelp.ini [License]): the documentation is free
   documentation under the license named in exphelp.ini, with links to the license in the help, on the web and to
   doc/en/us/help/free-documentation.md. Inherited pages (OtherLicensePathRules[]), an extension's pages and the license text
   itself get the notice that is true for them. It is printed with the page (help.css keeps it in print).
   Variables: license_page_id (page id below doc/; empty for a help page that is not a document: search, report).
   The same file is in design/admin and design/admin4. *}
{def $lic = fetch( 'exphelp', 'license', hash( 'id', cond( is_set( $license_page_id ), $license_page_id, '' ) ) )
     $lic_ctx = 'kernel/exphelp'}
{if $lic}
<section class="a4-help-license{if $lic.free|not} is-other{/if}" aria-labelledby="exphelp-license-title">
    <h2 class="a4-help-license-title" id="exphelp-license-title"><span class="a4-icon a4-bi-journal-check" aria-hidden="true"></span>{'Documentation license'|i18n( $lic_ctx )}</h2>
    {if $lic.extension|ne( '' )}
    <p>{'This page is part of the documentation of the extension %extension and is distributed under the license of that extension.'|i18n( $lic_ctx,, hash( '%extension', $lic.extension ) )|wash}</p>
    {elseif $lic.free|not}
    <p>{'This page was inherited from the documentation of the original software. It is not covered by the %license; it is distributed under the %other.'|i18n( $lic_ctx,, hash( '%license', $lic.name, '%other', $lic.other_name ) )|wash}</p>
    {if $lic.other_copyright|ne( '' )}<p class="a4-help-license-copy">{$lic.other_copyright|wash}</p>{/if}
    <ul class="a4-help-license-links">
        {if $lic.other_url|ne( '' )}<li><a href="{$lic.other_url|wash}" rel="license noopener noreferrer" target="_blank" hreflang="en"><span class="a4-icon a4-bi-box-arrow-up-right" aria-hidden="true"></span>{$lic.other_name|wash}<span class="a4-sr-only"> {'(opens in a new window)'|i18n( $lic_ctx )}</span></a></li>{/if}
        {if $lic.notice_page|ne( '' )}<li><a href={concat( 'exphelp/viewer/', $lic.notice_page )|ezurl}><span class="a4-icon a4-bi-info-circle" aria-hidden="true"></span>{'Which pages have which license'|i18n( $lic_ctx )}</a></li>{/if}
    </ul>
    {else}
    {if $lic.id|eq( $lic.text_page )}
    <p>{'This is the text of the %license, version %version, as the Free Software Foundation publishes it. Everyone may copy and share it word for word; changing it is not allowed.'|i18n( $lic_ctx,, hash( '%license', $lic.name, '%version', $lic.version ) )|wash}</p>
    {elseif $lic.or_later}
    <p>{'This documentation is free documentation, licensed under the %license %version or later.'|i18n( $lic_ctx,, hash( '%license', $lic.name, '%version', $lic.version ) )|wash}</p>
    {else}
    <p>{'This documentation is free documentation, licensed under the %license %version.'|i18n( $lic_ctx,, hash( '%license', $lic.name, '%version', $lic.version ) )|wash}</p>
    {/if}
    {if $lic.id|ne( $lic.text_page )}<p class="a4-help-license-copy">{$lic.copyright|wash}</p>{/if}
    <ul class="a4-help-license-links">
        {if and( $lic.text_page|ne( '' ), $lic.id|ne( $lic.text_page ) )}<li><a href={concat( 'exphelp/viewer/', $lic.text_page )|ezurl} rel="license"><span class="a4-icon a4-bi-file-earmark" aria-hidden="true"></span>{'Read the license'|i18n( $lic_ctx )}</a></li>{/if}
        {if $lic.url|ne( '' )}<li><a href="{$lic.url|wash}" rel="license noopener noreferrer" target="_blank" hreflang="en"><span class="a4-icon a4-bi-box-arrow-up-right" aria-hidden="true"></span>{'The license on the web'|i18n( $lic_ctx )}<span class="a4-sr-only"> {'(opens in a new window)'|i18n( $lic_ctx )}</span></a></li>{/if}
        {if and( $lic.explainer_page|ne( '' ), $lic.id|ne( $lic.explainer_page ) )}<li><a href={concat( 'exphelp/viewer/', $lic.explainer_page )|ezurl}><span class="a4-icon a4-bi-people" aria-hidden="true"></span>{'What you can do with free documentation'|i18n( $lic_ctx )}</a></li>{/if}
        {if and( $lic.notice_page|ne( '' ), $lic.id|ne( $lic.notice_page ) )}<li><a href={concat( 'exphelp/viewer/', $lic.notice_page )|ezurl}><span class="a4-icon a4-bi-info-circle" aria-hidden="true"></span>{'Copyright and license notice'|i18n( $lic_ctx )}</a></li>{/if}
    </ul>
    {/if}
</section>
{/if}
{undef $lic $lic_ctx}
