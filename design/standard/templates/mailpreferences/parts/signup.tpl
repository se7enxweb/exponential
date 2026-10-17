{* The optional e-mail on the registration form (user/register): one unticked box per optional category. Nothing is
   ticked for the person; a category that needs a confirmation sends its confirmation link after the registration.
   Variables: categories (hash( identifier, name, description, checked, double_opt_in ), from the register view;
   empty when the e-mail preferences are not installed), css (optional: the class of the wrapper, e.g. form-group).
   Its look is in stylesheets/mailpreferences_signup.css, linked here so it needs nothing of the form it is on. *}
{if and( is_set( $categories ), $categories|count|gt( 0 ) )}
<link rel="stylesheet" type="text/css" href={'stylesheets/mailpreferences_signup.css'|ezdesign} />
<fieldset class="{first_set( $css, 'mp-signup' )|wash} mp-signup-box">
    <legend>{'E-mail from us (optional)'|i18n( 'design/standard/mailpreferences' )}</legend>
    <p>{'Tick what you want to receive. You can change it at any time on your e-mail preference page, and every one of these e-mails has an unsubscribe link.'|i18n( 'design/standard/mailpreferences' )}</p>
{foreach $categories as $category}
    <div class="mp-signup-option">
        <input type="checkbox" id="mp-signup-{$category.identifier|wash}" name="MailPreferenceCategory[]" value="{$category.identifier|wash}"{if $category.checked} checked="checked"{/if} />
        <label for="mp-signup-{$category.identifier|wash}"><b>{$category.name|wash}</b>{if $category.description|ne( '' )} &ndash; {$category.description|wash}{/if}{if $category.double_opt_in} <span class="mp-signup-note">({'we send you a link to confirm it first'|i18n( 'design/standard/mailpreferences' )})</span>{/if}</label>
    </div>
{/foreach}
    <input type="hidden" name="MailPreferenceSignupShown" value="1" />
</fieldset>
{/if}
