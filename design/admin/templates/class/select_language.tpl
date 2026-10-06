{* The language choice before a class is edited in a language it does not have yet (class/edit).

   Field and button names (EditLanguage, FromLanguage, SelectLanguageButton, DiscardButton, RedirectIfDiscarded)
   are unchanged; Cancel goes back to the page the form was opened from. The same file is in design/admin and
   design/admin4. Guide: doc/guides/class-groups.md *}
{include uri='design:class/exp_style.tpl'}

<form action={concat( $module.functions.edit.uri, '/', $class.id )|ezurl} method="post" id="SelectClassEditLanguageForm" name="SelectClassEditLanguage" class="exp-lists exp-classgroups exp-standalone">

<div class="context-block">
<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Edit <%class_name>'|i18n( 'design/admin/class/select_languages',, hash( '%class_name', $class.name ) )|wash}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'Translate the class: its name, description and attribute names in another language. Its attributes and objects stay as they are.'|i18n( 'design/admin/class/select_language' )}</p>

<div class="exp-panel">
<div class="exp-form-fields exp-form-wide">
    <fieldset class="exp-field">
        <legend>{'Select the language you want to add'|i18n( 'design/admin/class/select_language' )}</legend>
        {def $editLanguages = $class.can_create_languages}
        {foreach $editLanguages as $language}
        <label class="exp-check"><input name="EditLanguage" type="radio" value="{$language.locale|wash}"{run-once} checked="checked"{/run-once} /> {$language.name|wash}</label>
        {/foreach}
        {undef $editLanguages}
    </fieldset>
    {if $class}
    <fieldset class="exp-field">
        <legend>{'Select the language the added translation will be based on'|i18n( 'design/admin/class/select_language' )}</legend>
        <label class="exp-check"><input name="FromLanguage" type="radio" checked="checked" value="" /> {'None'|i18n( 'design/admin/class/select_language' )}</label>
        {foreach $class.prioritized_languages as $language}
        <label class="exp-check"><input name="FromLanguage" type="radio" value="{$language.locale|wash}" /> {$language.name|wash}</label>
        {/foreach}
    </fieldset>
    {/if}
</div>
</div>

<div class="exp-bottombar">
    <div class="exp-actions">
        <button class="exp-btn exp-btn-primary" type="submit" name="SelectLanguageButton" value="1">{'Edit'|i18n( 'design/admin/class/edit_language' )}</button>
        <button class="exp-btn" type="submit" name="DiscardButton" value="1">{'Cancel'|i18n( 'design/admin/class/select_language' )}</button>
    </div>
</div>

</div></div></div>
</div>
{if and( is_set( $redirect_if_discarded ), $redirect_if_discarded )}<input type="hidden" name="RedirectIfDiscarded" value="{$redirect_if_discarded|wash}" />{/if}
</form>
