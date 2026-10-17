{* Copy a subtree, the step after the target was chosen in the browser (content/copysubtree): the choices
   content.ini [CopySettings] leaves to the user - the versions, the creator and the times of the copies.

   Copy (CopyButton) copies to the chosen location, Cancel (CancelButton) goes back. Every name is the one the view
   has always read (VersionChoice, CreatorChoice, TimeChoice: 1 keeps, 2 is the new value; SelectedNodeID); the run
   now or in the background choice of the browse page comes along as ContentJobMode. *}
<form action={concat( '/content/copysubtree/', $node.node_id, '/' )|ezurl} method="post" name="ContentCopySubtree" class="a4-page a4-standalone">
<div class="context-block">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Copying subtree from node %1'|i18n( 'design/standard/content/copy_subtree',, array( $node.name|wash ) )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

{if $choose_versions}
<div class="a4-field">
<ul class="a4-radios">
    <li><label><input type="radio" name="VersionChoice" value="1" /> <span>{'Copy all versions.'|i18n( 'design/standard/content/copy_subtree' )}</span></label></li>
    <li><label><input type="radio" name="VersionChoice" value="2" checked="checked" /> <span>{'Copy current version.'|i18n( 'design/standard/content/copy_subtree' )}</span></label></li>
</ul>
</div>
{/if}

{if $choose_creator}
<div class="a4-field">
<ul class="a4-radios">
    <li><label><input type="radio" name="CreatorChoice" value="1" /> <span>{'Keep creators of content objects being copied unchanged.'|i18n( 'design/standard/content/copy_subtree' )}</span></label></li>
    <li><label><input type="radio" name="CreatorChoice" value="2" checked="checked" /> <span>{'Set new creator for content objects being copied.'|i18n( 'design/standard/content/copy_subtree' )}</span></label></li>
</ul>
</div>
{/if}

{if $choose_time}
<div class="a4-field">
<ul class="a4-radios">
    <li><label><input type="radio" name="TimeChoice" value="1" /> <span>{'Keep time of creation and modification of content objects being copied unchanged.'|i18n( 'design/standard/content/copy_subtree' )}</span></label></li>
    <li><label><input type="radio" name="TimeChoice" value="2" checked="checked" /> <span>{'Copy and publish content objects at current time.'|i18n( 'design/standard/content/copy_subtree' )}</span></label></li>
</ul>
</div>
{/if}

{if and( is_set( $content_job_mode ), $content_job_mode )}<input type="hidden" name="ContentJobMode" value="{$content_job_mode|wash}" />{/if}
<input type="hidden" name="SelectedNodeID" value="{$selected_node_id|wash}" />

<div class="a4-toolbar is-bottom">
    <div class="a4-actions">
        <button class="a4-btn a4-btn-primary" type="submit" name="CopyButton" value="1">{'Copy'|i18n( 'design/standard/content/copy_subtree' )}</button>
        <button class="a4-btn" type="submit" name="CancelButton" value="1">{'Cancel'|i18n( 'design/standard/content/copy_subtree' )}</button>
    </div>
</div>

</div></div></div>
</div>
</form>
