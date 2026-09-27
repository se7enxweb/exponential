{* Administration: the page shown when a form is sent without its form token,
   or with one that does not match (extension/ezformtoken). Nothing was saved;
   the editor only has to reload the form and send it again.

   Included by error/kernel/6.tpl with:
     reason       'missing' or 'wrong'
     reload_url   where "Reload the form" goes (a path on this site)
     home_url     the start page of this siteaccess (the dashboard)
     signed_out   true() when the editor is not signed in

   The colours live in error/parts/formtoken_style.tpl so a design (admin3)
   can match its own theme without repeating the page.
   Same wording as every other design (context design/standard/error/formtoken). *}
{include uri='design:error/parts/formtoken_style.tpl'}
<div class="message-warning formtoken-error formtoken-{$reason|wash}" role="alert">
    <h2>{'This form has expired'|i18n( 'design/standard/error/formtoken' )}</h2>
    <p>{'The page with this form was open for a long time, or the form was sent from another page. To keep your information safe, nothing was saved.'|i18n( 'design/standard/error/formtoken' )}</p>
    <p>{'Reload the form and send it again.'|i18n( 'design/standard/error/formtoken' )}</p>
    {if $signed_out}
    <p>{'You may have been signed out in the meantime. If so, please sign in again.'|i18n( 'design/standard/error/formtoken' )}</p>
    {/if}
    <div class="formtoken-actions">
        <a class="formtoken-primary" href="{$reload_url|wash}">{'Reload the form'|i18n( 'design/standard/error/formtoken' )}</a>
        <a class="formtoken-secondary" href="{$home_url|wash}">{'Go to the dashboard'|i18n( 'design/standard/error/formtoken' )}</a>
    </div>
</div>
