{* The page shown when a form is sent without its form token, or with one that
   does not match (extension/ezformtoken). Nothing was saved; the visitor only
   has to reload the form and send it again.

   Included by error/kernel/6.tpl with:
     reason       'missing' or 'wrong'
     reload_url   where "Reload the form" goes (a path on this site)
     home_url     the front page of this siteaccess
     signed_out   true() when the visitor is not signed in

   The same wording is used by every design (context design/standard/error/formtoken). *}
<style>
{literal}
.formtoken-error{max-width:40rem;margin:2rem auto;padding:1.5rem 1.75rem;border:1px solid #d6c07a;border-left:6px solid #e0b000;background:#fffbea;color:#333;line-height:1.5}
.formtoken-error h2{margin:0 0 .75rem;font-size:1.5em;line-height:1.25}
.formtoken-error p{margin:0 0 .75rem}
.formtoken-error .formtoken-actions{display:flex;flex-wrap:wrap;gap:.75rem;margin-top:1.25rem}
.formtoken-error .formtoken-actions a{display:inline-block;padding:.5rem 1rem;border:1px solid #555;border-radius:3px;text-decoration:none;color:#222;background:#fff}
.formtoken-error .formtoken-actions a.formtoken-primary{background:#333;border-color:#333;color:#fff}
.formtoken-error .formtoken-actions a:focus{outline:3px solid #e0b000;outline-offset:2px}
{/literal}
</style>
<div class="warning formtoken-error formtoken-{$reason|wash}" role="alert">
    <h2>{'This form has expired'|i18n( 'design/standard/error/formtoken' )}</h2>
    <p>{'The page with this form was open for a long time, or the form was sent from another page. To keep your information safe, nothing was saved.'|i18n( 'design/standard/error/formtoken' )}</p>
    <p>{'Reload the form and send it again.'|i18n( 'design/standard/error/formtoken' )}</p>
    {if $signed_out}
    <p>{'You may have been signed out in the meantime. If so, please sign in again.'|i18n( 'design/standard/error/formtoken' )}</p>
    {/if}
    <div class="formtoken-actions">
        <a class="formtoken-primary" href="{$reload_url|wash}">{'Reload the form'|i18n( 'design/standard/error/formtoken' )}</a>
        <a href="{$home_url|wash}">{'Go to the front page'|i18n( 'design/standard/error/formtoken' )}</a>
    </div>
</div>
