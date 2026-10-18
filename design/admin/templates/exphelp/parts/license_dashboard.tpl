{* The dashboard's "Free documentation" block: the license of the help (exphelp.ini [License]) and what readers may
   do with it, in the words of doc/help/free-documentation.md (which it links to, with the license in the help and on
   the web). No variables; the license comes from fetch( 'exphelp', 'license' ).
   The same file is in design/admin and design/admin4. *}
{def $lic = fetch( 'exphelp', 'license', hash( 'id', '' ) )
     $lic_ctx = 'kernel/exphelp'}
{if $lic}
<section class="a4-card a4-help-freedoc" aria-labelledby="help-dash-freedoc">
    <div class="a4-help-section-head">
        <h2 id="help-dash-freedoc"><span class="a4-icon a4-bi-journal-check" aria-hidden="true"></span>{'Free documentation: use it to help yourself and others'|i18n( $lic_ctx )}</h2>
        <p>{cond( $lic.or_later,
                  'This help is free documentation, licensed under the %license %version or later. You may use it, share it and improve it:'|i18n( $lic_ctx,, hash( '%license', $lic.name, '%version', $lic.version ) ),
                  'This help is free documentation, licensed under the %license %version. You may use it, share it and improve it:'|i18n( $lic_ctx,, hash( '%license', $lic.name, '%version', $lic.version ) ) )|wash}</p>
    </div>
    <ul class="a4-help-freedoc-list">
        <li><span class="a4-icon a4-bi-journal-text" aria-hidden="true"></span><span><strong>{'Study it'|i18n( $lic_ctx )}</strong> {'Read any page as often as you like, for any purpose.'|i18n( $lic_ctx )}</span></li>
        <li><span class="a4-icon a4-bi-people" aria-hidden="true"></span><span><strong>{'Copy it and share it'|i18n( $lic_ctx )}</strong> {'Give pages to other users, on paper or as files. Keep the copyright notice, the license notice and a copy of the license with every copy.'|i18n( $lic_ctx )}</span></li>
        <li><span class="a4-icon a4-bi-translate" aria-hidden="true"></span><span><strong>{'Translate, adapt and improve it'|i18n( $lic_ctx )}</strong> {'Share your version under the same license, with the original notices and a note of what you changed.'|i18n( $lic_ctx )}</span></li>
        <li><span class="a4-icon a4-bi-link-45deg" aria-hidden="true"></span><span><strong>{'Cite it'|i18n( $lic_ctx )}</strong> {'Refer to a page by its title and its address in the help, or its file in the doc directory.'|i18n( $lic_ctx )}</span></li>
        <li><span class="a4-icon a4-bi-git" aria-hidden="true"></span><span><strong>{'Contribute fixes back'|i18n( $lic_ctx )}</strong> {'Use "Edit on GitHub" on any page. Changes are accepted under the same license, so they stay free for everyone.'|i18n( $lic_ctx )}</span></li>
    </ul>
    <p class="a4-help-license-copy">{$lic.copyright|wash}</p>
    <div class="a4-help-freedoc-actions">
        {if $lic.explainer_page|ne( '' )}<a class="a4-btn a4-btn-primary" href={concat( 'exphelp/viewer/', $lic.explainer_page )|ezurl}><span class="a4-icon a4-bi-people" aria-hidden="true"></span>{'What you can do with free documentation'|i18n( $lic_ctx )}</a>{/if}
        {if $lic.text_page|ne( '' )}<a class="a4-btn" href={concat( 'exphelp/viewer/', $lic.text_page )|ezurl} rel="license"><span class="a4-icon a4-bi-file-earmark" aria-hidden="true"></span>{'Read the license'|i18n( $lic_ctx )}</a>{/if}
        {if $lic.url|ne( '' )}<a class="a4-btn a4-btn-ghost" href="{$lic.url|wash}" rel="license noopener noreferrer" target="_blank" hreflang="en"><span class="a4-icon a4-bi-box-arrow-up-right" aria-hidden="true"></span>{'The license on the web'|i18n( $lic_ctx )}<span class="a4-sr-only"> {'(opens in a new window)'|i18n( $lic_ctx )}</span></a>{/if}
    </div>
</section>
{/if}
{undef $lic $lic_ctx}
