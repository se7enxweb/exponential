{* Design > Templates > edit one template file: its source in a full-width text field (textarea.a4-visual-editor,
   stylesheets/modules/workflow.css), saved with Apply changes. The same file is in design/admin and design/admin4. *}
<form method="post" action={concat( '/visual/templateedit/', $template )|ezurl}>
<div class="context-block a4-page a4-visual">

<div class="box-header"><div class="box-ml">
<h1 class="context-title" id="templateedit-title">{'Edit template: <%template>'|i18n( 'design/admin/visual/templateedit',, hash( '%template', $template ) )|wash}</h1>
<div class="header-mainline"></div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<textarea aria-labelledby="templateedit-title" class="a4-visual-editor" name="TemplateContent" cols="40" rows="30" spellcheck="false" wrap="off">{$template_content|wash(xhtml)}</textarea>

<div class="a4-toolbar is-bottom">
    <div class="a4-actions">
    {if $is_writable}
        <input class="a4-btn a4-btn-primary" type="submit" name="SaveButton" value="{'Apply changes'|i18n( 'design/admin/visual/templateedit' )|wash}" title="{'Click this button to save the contents of the text field above to the template file.'|i18n( 'design/admin/visual/templateedit' )|wash}" />
    {else}
        <input class="a4-btn a4-btn-primary" disabled="disabled" type="submit" name="SaveButton" value="{'Apply changes'|i18n( 'design/admin/visual/templateedit' )|wash}" title="{'You do not have permission to save the contents of the text field above to the template file.'|i18n( 'design/admin/visual/templateedit' )|wash}" />
    {/if}
        <input class="a4-btn" type="submit" name="DiscardButton" value="{'Back to overrides'|i18n( 'design/admin/visual/templateedit' )|wash}" title="{'Back to override overview.'|i18n( 'design/admin/visual/templateedit' )|wash}" />
    </div>
</div>

</div></div></div>

</div>
</form>
