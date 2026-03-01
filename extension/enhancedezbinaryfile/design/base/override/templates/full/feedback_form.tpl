{* letter to the editor form - Full view *}
<div class="content-view-full">
<div class="class-letter">
<div class="half-line"></div>
<h1>{$node.name}</h1>
<div class="blank-line"></div>
{if $node.data_map.lt_desc_xmlb.content.is_empty|not}
	{attribute_view_gui attribute=$node.data_map.lt_desc_xmlb}
	<div class="blank-line"></div>
{/if}
<div class="blank-line"></div>

{"Fields marked with an asterisk"|i18n("design/standard/user")}<span class="red"> * </span>{"are required."|i18n("design/standard/user")}

{include name=Validation uri='design:content/collectedinfo_validation.tpl'
class='message-warning'
validation=$validation collection_attributes=$collection_attributes}
<div class="blank-line"></div>

<form method="post" action={"content/action"|ezurl} enctype="multipart/form-data">

{foreach $node.object.data_map as $attribute}
<div class="input-row">
<div class="blank-line"></div>
   <div class="label">{if $attribute.contentclass_attribute.is_required}
   <span class="red">*</span>{else}&nbsp;{/if}
   {$attribute.contentclass_attribute_name|i18n("design/base")}</div>
   {attribute_view_gui attribute=$attribute}
</div>
{/foreach}

<div class="input-row">
<div class="input">
<div class="half-line"></div>
     <input type="submit" class="button" name="ActionCollectInformation" value="{"Send"|i18n("design/base")}" />

<input type="hidden" name="ContentNodeID" value="{$node.node_id}" />
<input type="hidden" name="ContentObjectID" value="{$node.object.id}" />
<input type="hidden" name="ViewMode" value="full" />
<div class="half-line"></div>
</div>
</div>
</form>

&nbsp;
</div>
</div>
