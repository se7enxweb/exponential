{* The optional e-mail on the registration form (user/register): one unticked box per optional category. Nothing is
   ticked for the person; a category that needs a confirmation sends its confirmation link after the registration.
   Variables: categories (hash( identifier, name, description, checked, double_opt_in ), from the register view;
   empty when the e-mail preferences are not installed), css (optional: the class of the wrapper, e.g. form-group). *}
{if and( is_set( $categories ), $categories|count|gt( 0 ) )}
<fieldset class="{first_set( $css, 'mp-signup' )|wash}" style="border:0;padding:0;margin:1em 0;min-width:0;">
    <legend style="font-weight:650;padding:0;margin:0 0 .3em;border:0;font-size:1em;width:auto;">{'E-mail from us (optional)'|i18n( 'design/standard/mailpreferences' )}</legend>
    <p style="margin:0 0 .5em;">{'Tick what you want to receive. You can change it at any time on your e-mail preference page, and every one of these e-mails has an unsubscribe link.'|i18n( 'design/standard/mailpreferences' )}</p>
{foreach $categories as $category}
    <div style="display:flex;gap:.5em;align-items:flex-start;margin:0 0 .45em;">
        <input type="checkbox" id="mp-signup-{$category.identifier|wash}" name="MailPreferenceCategory[]" value="{$category.identifier|wash}"{if $category.checked} checked="checked"{/if} style="width:1.1em;height:1.1em;margin:.25em 0 0;flex:none;" />
        <label for="mp-signup-{$category.identifier|wash}" style="font-weight:400;display:block;margin:0;float:none;width:auto;"><b>{$category.name|wash}</b>{if $category.description|ne( '' )} &ndash; {$category.description|wash}{/if}{if $category.double_opt_in} <span style="opacity:.8;">({'we send you a link to confirm it first'|i18n( 'design/standard/mailpreferences' )})</span>{/if}</label>
    </div>
{/foreach}
    <input type="hidden" name="MailPreferenceSignupShown" value="1" />
</fieldset>
{/if}
