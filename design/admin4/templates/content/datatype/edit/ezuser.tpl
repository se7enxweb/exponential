{* admin4: the account's fields stand in one row (.a4-content-parts), each label above its field; no colon after a label; the status is a badge *}
{default attribute_base=ContentObjectAttribute}

{* User ID. *}
<div class="block">
    <p class="a4-label">{'User ID'|i18n( 'design/standard/content/datatype' )}</p>
    <p>{$attribute.content.contentobject_id}</p>
</div>

{if ne( $attribute_base, 'ContentObjectAttribute' )}
    {def $id_base = concat( 'ezcoa-', $attribute_base, '-', $attribute.contentclassattribute_id, '_', $attribute.contentclass_attribute_identifier )}
{else}
    {def $id_base = concat( 'ezcoa-', $attribute.contentclassattribute_id, '_', $attribute.contentclass_attribute_identifier )}
{/if}

<div class="block a4-content-parts">

{* Username. *}
<div>
    <label for="{$id_base}_login">{'Username'|i18n( 'design/standard/content/datatype' )}</label>
    {if $attribute.content.has_stored_login}
        <input id="{$id_base}_login" type="text" name="{$attribute_base}_data_user_login_{$attribute.id}_stored_login" size="16" value="{$attribute.content.login|wash()}" disabled="disabled" />
        <input id="{$id_base}_login_hidden" type="hidden" name="{$attribute_base}_data_user_login_{$attribute.id}" value="{$attribute.content.login|wash()}" />
    {else}
        <input id="{$id_base}_login" class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_data_user_login_{$attribute.id}" size="16" value="{$attribute.content.login|wash()}" />
    {/if}
</div>

{* Password #1. *}
<div>
    <label for="{$id_base}_password">{'Password'|i18n( 'design/standard/content/datatype' )}</label>
    <input id="{$id_base}_password" class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="password" name="{$attribute_base}_data_user_password_{$attribute.id}" size="16" value="{if $attribute.content.original_password}{$attribute.content.original_password|wash}{else}{if $attribute.content.has_stored_login}_ezpassword{/if}{/if}" />
</div>

{* Password #2. *}
<div>
    <label for="{$id_base}_password_confirm">{'Confirm password'|i18n( 'design/standard/content/datatype' )}</label>
    <input id="{$id_base}_password_confirm" class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="password" name="{$attribute_base}_data_user_password_confirm_{$attribute.id}" size="16" value="{if $attribute.content.original_password_confirm}{$attribute.content.original_password_confirm|wash}{else}{if $attribute.content.has_stored_login}_ezpassword{/if}{/if}" />
</div>

{* Email. *}
<div>
    <label for="{$id_base}_email">{'Email'|i18n( 'design/standard/content/datatype' )}</label>
    <input id="{$id_base}_email" class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_data_user_email_{$attribute.id}" size="28" value="{$attribute.content.email|wash( xhtml )}" />
</div>

{* Email #2. Require e-mail confirmation *}
{if ezini( 'UserSettings', 'RequireConfirmEmail' )|eq( 'true' )}
<div>
    <label for="{$id_base}_email_confirm">{'Confirm email'|i18n( 'design/standard/content/datatype' )}</label>
    <input id="{$id_base}_email_confirm" class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_data_user_email_confirm_{$attribute.id}" size="28" value="{cond( ezhttp_hasvariable( concat( $attribute_base, '_data_user_email_confirm_', $attribute.id ), 'post' ), ezhttp( concat( $attribute_base, '_data_user_email_confirm_', $attribute.id ), 'post')|wash( xhtml ), $attribute.content.email|wash )}" />
</div>
{/if}

</div>

{* Change password at next sign-in: on another person's account only (not on one's own, not on self-registration);
   ticked by default while the account is being created (expPasswordChangeRequired) *}
{def $pwc_editor = fetch( 'user', 'current_user' )
     $pwc_checked = true()}
{if and( $pwc_editor.is_logged_in, $pwc_editor.contentobject_id|ne( $attribute.contentobject_id ) )}
{if $attribute.content}{set $pwc_checked = $attribute.content.password_change_checked}{/if}
<div class="block">
    <input type="hidden" name="{$attribute_base}_data_user_password_change_shown_{$attribute.id}" value="1" />
    <label class="a4-check" for="{$id_base}_password_change_required">
        <input id="{$id_base}_password_change_required" type="checkbox" name="{$attribute_base}_data_user_password_change_required_{$attribute.id}" value="1"{if $pwc_checked} checked="checked"{/if} />
        <span><strong>{'Change password at next sign-in'|i18n( 'design/standard/content/datatype' )}</strong>
        <span class="a4-help">{'After signing in, the user must choose a new password before doing anything else. Use it when you give someone a temporary password.'|i18n( 'design/standard/content/datatype' )}</span></span>
    </label>
</div>
{/if}
{undef $pwc_editor $pwc_checked}

{* Status: a text, not the label of a control *}
<div class="block">
    <p>{'Current account status:'|i18n( 'design/standard/content/datatype' )}
    {if $attribute.content.is_enabled}
        <span class="a4-badge is-ok userstatus-enabled">{'enabled'|i18n( 'design/standard/content/datatype' )}</span>
    {else}
        <span class="a4-badge userstatus-disabled">{'disabled'|i18n( 'design/standard/content/datatype' )}</span>
    {/if}
    </p>
</div>

{/default}
