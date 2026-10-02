{* A page address names a module and a view (/<module>/<view>/...) and that view does not exist. *}
{def $missing_module = cond( is_set( $parameters.module ), $parameters.module|wash, '' )
     $missing_view = cond( is_set( $parameters.view ), $parameters.view|wash, '' )
     $missing_page = cond( and( $missing_module, $missing_view ), concat( $missing_module, '/', $missing_view ), $missing_module ) }
<div class="message-error">
<h2><span class="time">[{currentdate()|l10n( shortdatetime )}]</span> {'This page does not exist'|i18n( 'design/admin/error/kernel' )}</h2>
{if $missing_page}
<p>{'There is no page at "%page".'|i18n( 'design/admin/error/kernel',, hash( '%page', $missing_page ) )}
   {if and( $missing_module, $missing_view )}{'The "%module" part of the system has no page called "%view".'|i18n( 'design/admin/error/kernel',, hash( '%module', $missing_module, '%view', $missing_view ) )}{/if}</p>
{else}
<p>{'The address does not lead to a page of the system.'|i18n( 'design/admin/error/kernel' )}</p>
{/if}
<p>{'What you can do'|i18n( 'design/admin/error/kernel' )}:</p>
<ul>
    <li>{'Check the address for a typing mistake.'|i18n( 'design/admin/error/kernel' )}</li>
    <li>{'Use the menu or the tabs to get to the page instead of a saved link; the page may have moved or been renamed.'|i18n( 'design/admin/error/kernel' )}</li>
    <li>{'If the site uses the address to choose a siteaccess (for example /admin/...), make sure the siteaccess name is at the start of the address.'|i18n( 'design/admin/error/kernel' )}</li>
    <li>{'If the page belongs to an extension, the extension may not be active on this site.'|i18n( 'design/admin/error/kernel' )}</li>
</ul>
<p class="error-code">{'Error code: kernel 21 (page not found)'|i18n( 'design/admin/error/kernel' )}</p>
</div>
{undef $missing_module $missing_view $missing_page}

{if $embed_content}
    {$embed_content}
{/if}
