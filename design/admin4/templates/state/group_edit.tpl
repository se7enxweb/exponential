{* Create or edit an object state group: its identifier, its main language and a name and a description per
   language. The field names, their order and the buttons are the kernel's (eZHTTPPersistence reads
   ContentObjectStateGroup_* in the order of the group's translations). The same file is in design/admin and
   design/admin4. *}
{include uri='design:state/style.tpl'}

{def $translations = $group.all_translations
     $is_new = $group.id|not}

<form action={concat( "/state/group_edit", cond( and( is_set( $form_identifier ), $form_identifier|ne('') ), concat( '/', $form_identifier ), $group.id, concat( '/', $group.identifier ), true(), '' ) )|ezurl} method="post">

<div class="context-block exp-states exp-own-card">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{if $is_new}{'New state group'|i18n( 'design/admin/state/group_edit' )}{else}{'Edit the state group "%group_name"'|i18n( 'design/admin/state/group_edit',, hash( '%group_name', $group.current_translation.name ) )|wash}{/if}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

{if is_set( $is_valid )}
{if $is_valid}
<div class="exp-feedback is-ok" role="status">{'The content object state group was successfully stored.'|i18n( 'design/admin/state/groups' )}</div>
{else}
<div class="exp-feedback is-bad" role="alert">
    <h2>{'The content object state group could not be stored.'|i18n( 'design/admin/state/groups' )}</h2>
    <p>{'Required data is either missing or is invalid'|i18n( 'design/admin/state/groups' )}:</p>
    <ul>
    {foreach $validation_messages as $message}
    <li>{$message|wash}</li>
    {/foreach}
    </ul>
</div>
{/if}
{/if}

<p class="exp-intro">
{if $is_new}
    {'A group holds the states content can be in, one at a time: for example a group "Review" with the states Draft, In review and Approved. After creating it you add its states; the first one is given to every object.'|i18n( 'design/admin/state/group_edit' )}
{else}
    {'Changing the names and descriptions changes what editors see. Templates, request rules and searches refer to the group by its identifier, and policies limit by StateGroup_ followed by it, so change the identifier only together with them.'|i18n( 'design/admin/state/group_edit' )}
{/if}
</p>

{if and( $group_info, $group_info.roles|count|gt(0) )}
<div class="exp-feedback is-info" role="note">
    {'These roles limit by %limitation:'|i18n( 'design/admin/state/group_edit',, hash( '%limitation', $group_info.limitation ) )|wash}
    {foreach $group_info.roles as $role_info}{delimiter}, {/delimiter}<a href={concat( '/role/view/', $role_info.role_id )|ezurl}>{$role_info.role_name|wash}</a>{/foreach}.
    {'Look at their policies after changing the identifier.'|i18n( 'design/admin/state/group_edit' )}
</div>
{/if}

<div class="exp-form">

<div class="exp-field">
    <label for="state-group-identifier">{'Identifier'|i18n( 'design/admin/state/group_edit' )}</label>
    <input type="text" class="exp-mono" id="state-group-identifier" name="ContentObjectStateGroup_identifier" size="45" maxlength="45" value="{$group.identifier|wash}" required="required" pattern="[a-z0-9_]+" autocomplete="off" spellcheck="false" aria-describedby="state-group-identifier-hint" />
    <span class="exp-hint" id="state-group-identifier-hint">{'Lower-case letters a-z, digits and underscores, at most 45 characters, unique. Identifiers starting with "ez" are reserved for Exponential. Policies limit by StateGroup_ followed by the identifier.'|i18n( 'design/admin/state/group_edit' )}</span>
</div>

{if $translations|count|gt(1)}
<div class="exp-field">
    <label for="state-group-default-language">{'Main language'|i18n( 'design/admin/state/group_edit' )}</label>
    <select id="state-group-default-language" name="ContentObjectStateGroup_default_language_id" aria-describedby="state-group-default-language-hint">
    {foreach $translations as $translation}
    <option value="{$translation.language.id}"{if $group.default_language_id|eq( $translation.language.id )} selected="selected"{/if}>{$translation.language.locale_object.intl_language_name|wash}</option>
    {/foreach}
    </select>
    <span class="exp-hint" id="state-group-default-language-hint">{'Shown where a language has no name of its own. The main language needs a name.'|i18n( 'design/admin/state/group_edit' )}</span>
</div>
{/if}

<p class="exp-meta">{'A name and description per language. A language left empty is not translated; emptying a translation removes it.'|i18n( 'design/admin/state/group_edit' )}</p>

{foreach $translations as $translation}
<fieldset class="exp-lang">
    <legend><img class="exp-flag" src="{$translation.language.locale|flag_icon}" width="18" height="12" alt="" /> {$translation.language.locale_object.intl_language_name|wash} <code class="exp-key">{$translation.language.locale|wash}</code></legend>
    <div class="exp-field">
        <label for="state-group-name-{$translation.language.id}">{'Name'|i18n( 'design/admin/state/group_edit' )}</label>
        <input type="text" id="state-group-name-{$translation.language.id}" size="45" maxlength="45" name="ContentObjectStateGroup_name[]" value="{$translation.name|wash}" />
    </div>
    <div class="exp-field">
        <label for="state-group-description-{$translation.language.id}">{'Description'|i18n( 'design/admin/state/group_edit' )}</label>
        <textarea id="state-group-description-{$translation.language.id}" rows="4" name="ContentObjectStateGroup_description[]">{$translation.description|wash}</textarea>
    </div>
</fieldset>
{/foreach}

</div>

<div class="exp-actions-bar">
    <p class="exp-meta">{if $is_new}{'Cancel goes back to the list of groups without creating anything.'|i18n( 'design/admin/state/group_edit' )}{else}{'Cancel goes back to the list of groups without saving.'|i18n( 'design/admin/state/group_edit' )}{/if}</p>
    <div class="exp-actions">
        {if $is_new}
        <button type="submit" class="exp-btn exp-btn-primary" name="StoreButton" value="1" title="{'Create this state group.'|i18n( 'design/admin/state/group_edit' )|wash}">{'Create'|i18n( 'design/admin/state/group_edit' )}</button>
        <button type="submit" class="exp-btn" name="CancelButton" value="1" formnovalidate="formnovalidate" title="{'Cancel creating this state group.'|i18n( 'design/admin/state/group_edit' )|wash}">{'Cancel'|i18n( 'design/admin/state/group_edit' )}</button>
        {else}
        <button type="submit" class="exp-btn exp-btn-primary" name="StoreButton" value="1" title="{'Save changes to this state group.'|i18n( 'design/admin/state/group_edit' )|wash}">{'Save changes'|i18n( 'design/admin/state/group_edit' )}</button>
        <button type="submit" class="exp-btn" name="CancelButton" value="1" formnovalidate="formnovalidate" title="{'Cancel saving any changes.'|i18n( 'design/admin/state/group_edit' )|wash}">{'Cancel'|i18n( 'design/admin/state/group_edit' )}</button>
        {/if}
    </div>
</div>

</div></div></div>

</div>{* class="context-block exp-states" *}

</form>

{undef $translations $is_new}
