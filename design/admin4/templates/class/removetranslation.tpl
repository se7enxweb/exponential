{* The confirmation of removing translations of a class (class/translation).

   Field and button names (LanguageID[], ContentClassID, ContentClassLanguageCode, ConfirmRemoval,
   RemoveTranslationButton, CancelButton) are unchanged. The same file is in design/admin and design/admin4. *}
{include uri='design:class/exp_style.tpl'}

<form method="post" action={'class/translation'|ezurl}>
<div class="context-block exp-lists exp-classgroups">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Confirm translation removal'|i18n( 'design/admin/class/removetranslation' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<section class="exp-confirm">
    <h2 class="exp-h2">{"Are you sure you want to remove the following translations from class <%1>?"|i18n( "design/admin/class/removetranslation",, hash( "%1", $class.nameList[$language_code] ) )|wash}</h2>
    <ul class="exp-consequences">
        <li>{'The class name, description and attribute names in these languages go. The class, its attributes and its objects stay.'|i18n( 'design/admin/class/removetranslation' )}</li>
    </ul>
    <ul class="exp-plain" style="margin-top: 10px;">
    {foreach $languages as $language}
        <li><input type="hidden" name="LanguageID[]" value="{$language.id}" /><strong>{$language.name|wash}</strong></li>
    {/foreach}
    </ul>
</section>

<div class="exp-bottombar">
    <div class="exp-actions">
        <input type="hidden" name="ContentClassID" value="{$class_id|wash}" />
        <input type="hidden" name="ContentClassLanguageCode" value="{$language_code|wash}" />
        <input type="hidden" name="ConfirmRemoval" value="1" />
        <button class="exp-btn exp-btn-danger" type="submit" name="RemoveTranslationButton" value="1">{'OK'|i18n( 'design/admin/class/removetranslation' )}</button>
        <button class="exp-btn" type="submit" name="CancelButton" value="1" title="{'Cancel the removal of translations.'|i18n( 'design/admin/class/removetranslation' )}">{'Cancel'|i18n( 'design/admin/class/removetranslation' )}</button>
    </div>
</div>

</div></div></div>
</div>
</form>
