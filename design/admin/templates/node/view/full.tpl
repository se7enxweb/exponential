{* The top node (node 1) has no content object: it gets a view of its own, see node/top_node_full.tpl. *}
{if eq( $node.contentobject_id, 0 )}
{include uri='design:node/top_node_full.tpl'}
{else}
<div class="content-view-full">
{if ne( $node.contentobject_id, 0 )}
 <div class="class-{$node.class_identifier}">
{else}
 <div class="class-root">
{/if}

{include uri='design:infocollection_validation.tpl'}

<div class="content-navigation">

{* Content window. *}
<div class="context-block">

{* DESIGN: Header START *}<div class="box-header">

{def $js_class_languages = '[]'
     $disable_another_language = '-1'
     $disabled_sub_menu = "['class-createnodefeed', 'class-removenodefeed']"
     $hide_status = ''
     $has_content_class = eq( $node.object.content_class|not, false )}
{if $has_content_class}
    {if eq( 0, count( $node.object.content_class.can_create_languages ) )}
        {set $disable_another_language = "'edit-class-another-language'"}
    {/if}
    {if is_set( $node.object.content_class.prioritized_languages_js_array )}
        {set $js_class_languages = $node.object.content_class.prioritized_languages_js_array}
        {if is_array( $js_class_languages )}
            {set $js_class_languages = $js_class_languages|implode( ',' )}
        {/if}
    {/if}
{/if}

{if $node.is_invisible}
    {set $hide_status = concat( '(', $node.hidden_status_string, ')' )}
{/if}

{* Check if user has rights and if there are any RSS/ATOM Feed exports for current node *}
{if and( ne( $node.contentobject_id, 0 ), is_set( ezini( 'RSSSettings', 'DefaultFeedItemClasses', 'site.ini' )[ $node.class_identifier ] ) )}
    {def $create_rss_access = fetch( 'user', 'has_access_to', hash( 'module', 'rss', 'function', 'edit' ) )}
    {if $create_rss_access}
        {if fetch( 'rss', 'has_export_by_node', hash( 'node_id', $node.node_id ) )}
            {set $disabled_sub_menu = "'class-createnodefeed'"}
        {else}
            {set $disabled_sub_menu = "'class-removenodefeed'"}
        {/if}
    {/if}
{/if}

{* the language the name is shown in: its current language when the object has it, else unknown (nothing marked) *}{def $exp_title_language = cond( $node.object.available_languages|contains( $node.object.current_language ), $node.object.current_language, '' )}
{if ne( $node.contentobject_id, 0 )}
<h1 class="context-title"><a href={concat( '/class/view/', $node.object.contentclass_id )|ezurl} onclick="ezpopmenu_showTopLevel( event, 'ClassMenu', ez_createAArray( new Array( '%classID%', {$node.object.contentclass_id}, '%objectID%', {$node.contentobject_id}, '%nodeID%', {$node.node_id}, '%currentURL%', '{$node.url|wash( javascript )|wash}', '%languages%', {$js_class_languages|wash} ) ), '{$node.class_name|wash(javascript)|wash}', {$disabled_sub_menu}, {$disable_another_language} ); return false;">{$node.class_identifier|class_icon( normal, $node.class_name )}</a>&nbsp;{set-block variable=exp_name_attrs}{include uri='design:parts/exp_content_direction.tpl' exp_locale_code=$exp_title_language}{/set-block}{if $exp_name_attrs|ne( '' )}<bdi{$exp_name_attrs}>{$node.name|wash}</bdi>{else}{$node.name|wash}{/if}{undef $exp_name_attrs}&nbsp;[{$node.class_name|wash}]&nbsp;{$hide_status}</h1>
{else}
<h1 class="context-title"{include uri='design:parts/exp_content_direction.tpl' exp_locale_code=$exp_title_language}>{$node.name|wash}</h1>
{/if}{undef $exp_title_language}

{undef $js_class_languages $disable_another_language $disabled_sub_menu $hide_status}

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div>


{* DESIGN: Content START *}<div class="box-content">

<div class="context-information">
<p class="left modified">{'Last modified'|i18n( 'design/admin/node/view/full' )}: {$node.object.modified|l10n(shortdatetime)}, <a href={$node.object.current.creator.main_node.url_alias|ezurl}>{$node.object.current.creator.name|wash}</a> ({'Node ID'|i18n( 'design/admin/node/view/full' )}: {$node.node_id}, {'Object ID'|i18n( 'design/admin/node/view/full' )}: {$node.object.id})</p>
<p class="right translation">{$node.object.current_language_object.locale_object.intl_language_name}&nbsp;<img src="{$node.object.current_language|flag_icon}" width="18" height="12" alt="{$language_code|wash}" class="a4-flag" /></p>
<div class="break"></div>
</div>

<div id="window-controls" class="tab-block">

{include uri='design:window_controls.tpl'}

</div>

{* DESIGN: Content END *}</div>

<div class="controlbar">
{* DESIGN: Control bar START *}

<form method="post" action={'content/action'|ezurl}>
<input type="hidden" name="TopLevelNode" value="{$node.object.main_node_id}" />
<input type="hidden" name="ContentNodeID" value="{$node.node_id}" />
<input type="hidden" name="ContentObjectID" value="{$node.contentobject_id}" />

<div class="button-left">
<div class='block'>
{* Edit button. *}
{def $can_create_languages = $node.object.can_create_languages
     $languages            = fetch( 'content', 'prioritized_languages' )}
{if $node.can_edit}
    {if and(eq( $languages|count, 1 ), is_set( $languages[0] ) )}
            <input name="ContentObjectLanguageCode" value="{$languages[0].locale}" type="hidden" />
    {else}
            <select name="ContentObjectLanguageCode" aria-label="{'Language to edit'|i18n( 'design/admin/node/view/full' )|wash}">
            {foreach $node.object.can_edit_languages as $language}
                       <option value="{$language.locale|wash}"{if $language.locale|eq($node.object.current_language)} selected="selected"{/if}>{$language.name|wash}</option>
            {/foreach}
            {if gt( $can_create_languages|count, 0 )}
                <option value="">{'New translation'|i18n( 'design/admin/node/view/full')}</option>
            {/if}
            </select>
    {/if}
    <input class="button" type="submit" name="EditButton" value="{'Edit'|i18n( 'design/admin/node/view/full' )}" title="{'Edit the contents of this item.'|i18n( 'design/admin/node/view/full' )}" />
{else}
    <select name="ContentObjectLanguageCode" disabled="disabled" aria-label="{'Language to edit'|i18n( 'design/admin/node/view/full' )|wash}">
        <option value="">{'Not available'|i18n( 'design/admin/node/view/full')}</option>
    </select>
    <input class="button-disabled" type="submit" name="EditButton" value="{'Edit'|i18n( 'design/admin/node/view/full' )}" title="{'You do not have permission to edit this item.'|i18n( 'design/admin/node/view/full' )}" disabled="disabled" />
{/if}
{undef $can_create_languages}

{* Upload button: ezmultiupload's upload page for this node, only where the user can upload (node/view/upload_button.tpl) *}
{include uri='design:node/view/upload_button.tpl' node=$node}
{* Import/Export: the export and import menu of this node (node/view/import_export_button.tpl), only with an entry for the user *}
{include uri='design:node/view/import_export_button.tpl' node=$node}

{* Move button. *}
{if $node.can_move}
    <input class="button" type="submit" name="MoveNodeButton" value="{'Move'|i18n( 'design/admin/node/view/full' )}" title="{'Move this item to another location.'|i18n( 'design/admin/node/view/full' )}" />
{else}
    <input class="button-disabled" type="submit" name="MoveNodeButton" value="{'Move'|i18n( 'design/admin/node/view/full' )}" title="{'You do not have permission to move this item to another location.'|i18n( 'design/admin/node/view/full' )}" disabled="disabled" />
{/if}

{* Copy button: content/copy (choose where the copy goes), offered with create access at the parent, as in the
   context menu. Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 4d56593614), Copyright (C) Mugo Web, GPL v2. *}
{if $node.parent.can_create}
    <input class="button" type="submit" formaction={concat( 'content/copy/', $node.contentobject_id )|ezurl} value="{'Copy'|i18n( 'design/admin/node/view/full' )}" title="{'Copy this item to another location.'|i18n( 'design/admin/node/view/full' )}" />
{else}
    <input class="button-disabled" type="submit" value="{'Copy'|i18n( 'design/admin/node/view/full' )}" title="{'You do not have permission to copy this item.'|i18n( 'design/admin/node/view/full' )}" disabled="disabled" />
{/if}

{* Remove button. *}
{if $node.can_remove}
    <input class="button" type="submit" name="ActionRemove" value="{'Remove'|i18n( 'design/admin/node/view/full' )}" title="{'Remove this item.'|i18n( 'design/admin/node/view/full' )}" />
{else}
    <input class="button-disabled" type="submit" name="ActionRemove" value="{'Remove'|i18n( 'design/admin/node/view/full' )}" title="{'You do not have permission to remove this item.'|i18n( 'design/admin/node/view/full' )}" disabled="disabled" />
{/if}

{* Deactivate and move / Reactivate and move back (user/deactivate): for a user account, when site.ini
   [UserSettings] DeactivateAndMove=enabled and you may (user/deactivate, edit the account); a confirmation follows.
   Guide: doc/features/6.0/user-hygiene.md *}
{if ezini( 'UserSettings', 'DeactivateAndMove' )|eq( 'enabled' )}
    {def $deactivation_offers = fetch( 'user', 'deactivation_offers', hash( 'object_id', $node.contentobject_id ) )}
    {if $deactivation_offers.deactivate}
    <input class="button" type="submit" name="DeactivateMoveButton" formaction={'user/deactivate'|ezurl} value="{'Deactivate and move'|i18n( 'design/admin/node/view/full' )}" title="{'Disable this account and move it to the group of deactivated users, remembering where it was. Nothing is removed.'|i18n( 'design/admin/node/view/full' )}" />
    {/if}
    {if $deactivation_offers.reactivate}
    <input class="button" type="submit" name="ReactivateMoveButton" formaction={'user/deactivate'|ezurl} value="{'Reactivate and move back'|i18n( 'design/admin/node/view/full' )}" title="{'Enable this account and move it back to the locations it had.'|i18n( 'design/admin/node/view/full' )}" />
    {/if}
    {undef $deactivation_offers}
{/if}
</div>
</div>

<div class="button-right">
	<p class='versions'>
    {* Link to manage versions *}
    <a href={concat("content/history/", $node.contentobject_id )|ezurl} title="{'View and manage (copy, delete, etc.) the versions of this object.'|i18n( 'design/admin/content/edit' )}">{'Manage versions'|i18n( 'design/admin/content/edit' )}</a>
    </p>
</div>

<div class="float-break"></div>
</form>
{* DESIGN: Control bar END *}
</div>


</div>

{* Children window.*}
<div id="content-view-children">
{if $node.is_container}
    {include uri='design:children.tpl'}
{else}
    {include uri='design:no_children.tpl'}
{/if}
</div>

</div>

 </div>
</div>
{/if}
