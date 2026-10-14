{* Administration: the page shown when a form is sent without its form token,
   or with one that does not match (extension/ezformtoken). Nothing was saved;
   the editor only has to reload the form and send it again.

   Included by error/kernel/6.tpl with:
     reason       'missing' or 'wrong'
     reload_url   where "Reload the form" goes (a path on this site)
     home_url     the start page of this siteaccess (the dashboard)
     signed_out   true() when the editor is not signed in
     login_form   true() when the refused form was a sign-in form

   admin4: drawn with the shared components (stylesheets/components.css), a warning notice
   with the two links as buttons; no CSS of its own.
   Same wording as every other design (context design/standard/error/formtoken). *}
<div class="a4-page a4-notice is-warn formtoken-error formtoken-{$reason|wash}" role="alert">
    <h2 class="a4-h2">{'This form has expired'|i18n( 'design/standard/error/formtoken' )}</h2>
    <p>{'The page with this form was open for a long time, or the form was sent from another page. To keep your information safe, nothing was saved.'|i18n( 'design/standard/error/formtoken' )}</p>
    <p>{'Reload the form and send it again.'|i18n( 'design/standard/error/formtoken' )}</p>
    {if $signed_out}
    <p>{'You may have been signed out in the meantime. If so, please sign in again.'|i18n( 'design/standard/error/formtoken' )}</p>
    {elseif and( is_set( $login_form ), $login_form )}
    <p>{'If you were signing in, please sign in again.'|i18n( 'design/standard/error/formtoken' )}</p>
    {/if}
    <div class="a4-actions formtoken-actions">
        <a class="a4-btn a4-btn-primary formtoken-primary" href="{$reload_url|wash}">{'Reload the form'|i18n( 'design/standard/error/formtoken' )}</a>
        <a class="a4-btn formtoken-secondary" href="{$home_url|wash}">{'Go to the dashboard'|i18n( 'design/standard/error/formtoken' )}</a>
    </div>
</div>
