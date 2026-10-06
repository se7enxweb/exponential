{* Create or edit an object state: its identifier, its main language and a name and a description per language.
   The field names, their order and the buttons are the kernel's (eZHTTPPersistence reads ContentObjectState_*
   in the order of the group's translations). The same file is in design/admin and design/admin4. *}
{include uri='design:state/style.tpl'}

{def $translations = $state.all_translations
     $is_new = $state.id|not
     $state_count = $group_info.states|count}

<form action={concat( "/state/edit/", $group.identifier, cond( and( is_set( $form_identifier ), $form_identifier|ne('') ), concat( "/", $form_identifier ), $state.id, concat( "/", $state.identifier ), true(), '' ) )|ezurl} method="post">

<div class="context-block exp-states exp-own-card">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{if $is_new}{'New state in %group'|i18n( 'design/admin/state/edit',, hash( '%group', $group_info.name ) )|wash}{else}{'Edit the state "%state_name"'|i18n( 'design/admin/state/edit',, hash( '%state_name', $state.current_translation.name ) )|wash}{/if}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

{if is_set( $is_valid )}
{if $is_valid}
<div class="exp-feedback is-ok" role="status">{'The content object state was successfully stored.'|i18n( 'design/admin/state/edit' )}</div>
{else}
<div class="exp-feedback is-bad" role="alert">
    <h2>{'The content object state could not be stored.'|i18n( 'design/admin/state/edit' )}</h2>
    <p>{'Required data is either missing or is invalid'|i18n( 'design/admin/state/edit' )}:</p>
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
    {if $state_count|eq(0)}
    {'This is the first state of the group. Every object that exists gets it when it is saved, and it becomes the default for new objects.'|i18n( 'design/admin/state/edit' )}
    {else}
    {'The new state is added at the end, as number %position. Objects keep their state; set it on content under Content state in the Details tab of a node. The order can be changed on the page of the group.'|i18n( 'design/admin/state/edit',, hash( '%position', $state_count|inc ) )|wash}
    {/if}
{else}
    {'Changing the names and descriptions changes what editors see. Templates, request rules and searches refer to the state by its identifier, so change that only together with them.'|i18n( 'design/admin/state/edit' )}
{/if}
</p>

<div class="exp-form">

<div class="exp-field">
    <label for="state-identifier">{'Identifier'|i18n( 'design/admin/state/edit' )}</label>
    <input type="text" class="exp-mono" id="state-identifier" name="ContentObjectState_identifier" size="45" maxlength="45" value="{$state.identifier|wash}" required="required" pattern="[a-z0-9_]+" autocomplete="off" spellcheck="false" aria-describedby="state-identifier-hint" />
    <span class="exp-hint" id="state-identifier-hint">{'Lower-case letters a-z, digits and underscores, at most 45 characters, unique in the group. It is written %example in templates and request rules.'|i18n( 'design/admin/state/edit',, hash( '%example', concat( $group.identifier, '/', cond( $state.identifier|ne(''), $state.identifier, true(), '...' ) ) ) )|wash}</span>
</div>

{if $translations|count|gt(1)}
<div class="exp-field">
    <label for="state-default-language">{'Main language'|i18n( 'design/admin/state/edit' )}</label>
    <select id="state-default-language" name="ContentObjectState_default_language_id" aria-describedby="state-default-language-hint">
    {foreach $translations as $translation}
    <option value="{$translation.language.id}"{if $state.default_language_id|eq( $translation.language.id )} selected="selected"{/if}>{$translation.language.locale_object.intl_language_name|wash}</option>
    {/foreach}
    </select>
    <span class="exp-hint" id="state-default-language-hint">{'Shown where a language has no name of its own. The main language needs a name.'|i18n( 'design/admin/state/edit' )}</span>
</div>
{/if}

<p class="exp-meta">{'A name and description per language. A language left empty is not translated; emptying a translation removes it.'|i18n( 'design/admin/state/edit' )}</p>

{foreach $translations as $translation}
<fieldset class="exp-lang">
    <legend><img class="exp-flag" src="{$translation.language.locale|flag_icon}" width="18" height="12" alt="" /> {$translation.language.locale_object.intl_language_name|wash} <code class="exp-key">{$translation.language.locale|wash}</code></legend>
    <div class="exp-field">
        <label for="state-name-{$translation.language.id}">{'Name'|i18n( 'design/admin/state/edit' )}</label>
        <input type="text" id="state-name-{$translation.language.id}" size="45" maxlength="45" name="ContentObjectState_name[]" value="{$translation.name|wash}" />
    </div>
    <div class="exp-field">
        <label for="state-description-{$translation.language.id}">{'Description'|i18n( 'design/admin/state/edit' )}</label>
        <textarea id="state-description-{$translation.language.id}" rows="4" name="ContentObjectState_description[]">{$translation.description|wash}</textarea>
    </div>
</fieldset>
{/foreach}

</div>

<div class="exp-actions-bar">
    <p class="exp-meta">{'Cancel goes back to the group without saving.'|i18n( 'design/admin/state/edit' )}</p>
    <div class="exp-actions">
        <button type="submit" class="exp-btn exp-btn-primary" name="StoreButton" value="1" title="{'Save changes to this state.'|i18n( 'design/admin/state/edit' )|wash}">{if $is_new}{'Create state'|i18n( 'design/admin/state/edit' )}{else}{'Save changes'|i18n( 'design/admin/state/edit' )}{/if}</button>
        <button type="submit" class="exp-btn" name="CancelButton" value="1" formnovalidate="formnovalidate" title="{'Cancel saving any changes.'|i18n( 'design/admin/state/edit' )|wash}">{'Cancel'|i18n( 'design/admin/state/edit' )}</button>
    </div>
</div>

</div></div></div>

</div>{* class="context-block exp-states" *}

</form>

{undef $translations $is_new $state_count}
