{* Copy an object, the step after the target was chosen in the browser (content/copy, content.ini [CopySettings]
   VersionHandling=user-defined): copy all versions or only the current one.

   Copy (CopyButton) copies to the chosen location, Cancel (CancelButton) goes back. Every name is the one the view
   has always read (VersionChoice 1 = all versions, 2 = the current one; SelectedNodeID). *}
{def $copy_version_count = $object.versions|count()}
<form action={concat( '/content/copy/', $object.id, '/' )|ezurl} method="post" name="ContentCopy" class="a4-page a4-standalone">
<div class="context-block">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Copying %1'|i18n( 'design/standard/content/copy',, array( $object.name|wash ) )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="a4-intro">{'Version count is %1 and current version is %2.'|i18n( 'design/standard/content/copy',, array( $copy_version_count, $object.current_version ) )}</p>

<div class="a4-field">
<ul class="a4-radios">
    <li><label><input type="radio" name="VersionChoice" value="1" /> <span>{'Copy all versions'|i18n( 'design/standard/content/copy' )}</span></label></li>
    <li><label><input type="radio" name="VersionChoice" value="2" checked="checked" /> <span>{'Copy current version'|i18n( 'design/standard/content/copy' )}</span></label></li>
</ul>
</div>

<input type="hidden" name="SelectedNodeID" value="{$selected_node_id|wash}" />

<div class="a4-toolbar is-bottom">
    <div class="a4-actions">
        <button class="a4-btn a4-btn-primary" type="submit" name="CopyButton" value="1">{'Copy'|i18n( 'design/standard/content/copy' )}</button>
        <button class="a4-btn" type="submit" name="CancelButton" value="1">{'Cancel'|i18n( 'design/standard/content/copy' )}</button>
    </div>
</div>

</div></div></div>
</div>
</form>
{undef $copy_version_count}
