{* admin4: the node view's body, included by node/view/full.tpl and by override/templates/node/view/full_caching_disabled.tpl
   (the users' view, which ezmbpaex renders with the view cache off). See node/view/full.tpl.

   Compact, so the content (the tabs and the sub items) starts near the top of the card:
   - one header row: the class icon (its class menu), the title with small badges, and on the right the actions
     (the same content/action form as before: language, Edit, Move, Remove) and icon buttons for View on site,
     Preview and Manage versions;
   - one quiet meta line: modified and by whom, published, version, section, the node, object and remote ids
     (click to copy), the translations (click to switch);
   - the tabs (window_controls.tpl, untouched, so extensions' tabs keep working), then the children.
   Every id, class and form field the scripts and extensions use is kept: .content-view-full, .context-block,
   h1.context-title, .context-information, #window-controls, .controlbar and its form, #content-view-children. *}
<div class="content-view-full a4-node">
 <div class="class-{$node.class_identifier}">

{include uri='design:infocollection_validation.tpl'}

<div class="content-navigation">

<div class="context-block a4-node-card">

{def $js_class_languages = '[]'
     $disable_another_language = '-1'
     $disabled_sub_menu = "['class-createnodefeed', 'class-removenodefeed']"
     $has_content_class = eq( $node.object.content_class|not, false )
     $object = $node.object
     $section = fetch( 'section', 'object', hash( 'section_id', $object.section_id ) )
     $locations = $object.assigned_nodes|count
     $translations = $object.languages
     $site_url = concat( siteaccess_url()|trim( '/' ), '/', $node.url_alias )}
{if $has_content_class}
    {if eq( 0, count( $object.content_class.can_create_languages ) )}
        {set $disable_another_language = "'edit-class-another-language'"}
    {/if}
    {if is_set( $object.content_class.prioritized_languages_js_array )}
        {set $js_class_languages = $object.content_class.prioritized_languages_js_array}
        {if is_array( $js_class_languages )}
            {set $js_class_languages = $js_class_languages|implode( ',' )}
        {/if}
    {/if}
{/if}
{* Check if user has rights and if there are any RSS/ATOM Feed exports for current node *}
{if is_set( ezini( 'RSSSettings', 'DefaultFeedItemClasses', 'site.ini' )[ $node.class_identifier ] )}
    {def $create_rss_access = fetch( 'user', 'has_access_to', hash( 'module', 'rss', 'function', 'edit' ) )}
    {if $create_rss_access}
        {if fetch( 'rss', 'has_export_by_node', hash( 'node_id', $node.node_id ) )}
            {set $disabled_sub_menu = "'class-createnodefeed'"}
        {else}
            {set $disabled_sub_menu = "'class-removenodefeed'"}
        {/if}
    {/if}
{/if}

{* ---- One header row: icon, title + badges, actions ---- *}
<div class="box-header a4-node-head">
    <a class="a4-node-icon" href={concat( '/class/view/', $object.contentclass_id )|ezurl} title="{'Class menu'|i18n( 'design/admin/node/view/full' )|wash}" onclick="ezpopmenu_showTopLevel( event, 'ClassMenu', ez_createAArray( new Array( '%classID%', {$object.contentclass_id}, '%objectID%', {$node.contentobject_id}, '%nodeID%', {$node.node_id}, '%currentURL%', '{$node.url|wash( javascript )}', '%languages%', {$js_class_languages} ) ), '{$node.class_name|wash(javascript)}', {$disabled_sub_menu}, {$disable_another_language} ); return false;">{$node.class_identifier|class_icon( small, $node.class_name )}</a>
    <div class="a4-node-heading">
        <h1 class="context-title">{$node.name|wash}</h1>
        <span class="a4-node-badges">
            <span class="a4-badge a4-badge-class">{$node.class_name|wash}</span>
            {if $node.is_hidden}<span class="a4-badge a4-badge-warn">{'Hidden'|i18n( 'design/admin/node/view/full' )}</span>
            {elseif $node.is_invisible}<span class="a4-badge a4-badge-warn">{'Hidden by a parent'|i18n( 'design/admin/node/view/full' )}</span>{/if}
            {if ne( $node.node_id, $node.main_node_id )}<span class="a4-badge">{'Secondary location'|i18n( 'design/admin/node/view/full' )}</span>{/if}
        </span>
    </div>

    <div class="controlbar a4-node-actions">
    <form method="post" action={'content/action'|ezurl}>
    <input type="hidden" name="TopLevelNode" value="{$object.main_node_id}" />
    <input type="hidden" name="ContentNodeID" value="{$node.node_id}" />
    <input type="hidden" name="ContentObjectID" value="{$node.contentobject_id}" />
    <div class="button-left"><div class="block">
    {def $can_create_languages = $object.can_create_languages
         $languages            = fetch( 'content', 'prioritized_languages' )}
    {if $node.can_edit}
        {if and( eq( $languages|count, 1 ), is_set( $languages[0] ) )}
            <input name="ContentObjectLanguageCode" value="{$languages[0].locale}" type="hidden" />
        {else}
            <select name="ContentObjectLanguageCode" aria-label="{'Language to edit'|i18n( 'design/admin/node/view/full' )|wash}">
            {foreach $object.can_edit_languages as $language}
                <option value="{$language.locale|wash}"{if $language.locale|eq( $object.current_language )} selected="selected"{/if}>{$language.name|wash}</option>
            {/foreach}
            {if gt( $can_create_languages|count, 0 )}
                <option value="">{'New translation'|i18n( 'design/admin/node/view/full' )}</option>
            {/if}
            </select>
        {/if}
        <input class="defaultbutton" type="submit" name="EditButton" value="{'Edit'|i18n( 'design/admin/node/view/full' )}" title="{'Edit the contents of this item.'|i18n( 'design/admin/node/view/full' )}" />
    {else}
        <select name="ContentObjectLanguageCode" disabled="disabled">
            <option value="">{'Not available'|i18n( 'design/admin/node/view/full' )}</option>
        </select>
        <input class="button-disabled" type="submit" name="EditButton" value="{'Edit'|i18n( 'design/admin/node/view/full' )}" title="{'You do not have permission to edit this item.'|i18n( 'design/admin/node/view/full' )}" disabled="disabled" />
    {/if}
    {undef $can_create_languages}
    {if $node.can_move}
        <input class="button" type="submit" name="MoveNodeButton" value="{'Move'|i18n( 'design/admin/node/view/full' )}" title="{'Move this item to another location.'|i18n( 'design/admin/node/view/full' )}" />
    {else}
        <input class="button-disabled" type="submit" name="MoveNodeButton" value="{'Move'|i18n( 'design/admin/node/view/full' )}" title="{'You do not have permission to move this item to another location.'|i18n( 'design/admin/node/view/full' )}" disabled="disabled" />
    {/if}
    {if $node.can_remove}
        <input class="button a4-danger" type="submit" name="ActionRemove" value="{'Remove'|i18n( 'design/admin/node/view/full' )}" title="{'Remove this item.'|i18n( 'design/admin/node/view/full' )}" />
    {else}
        <input class="button-disabled" type="submit" name="ActionRemove" value="{'Remove'|i18n( 'design/admin/node/view/full' )}" title="{'You do not have permission to remove this item.'|i18n( 'design/admin/node/view/full' )}" disabled="disabled" />
    {/if}
    </div></div>
    <div class="button-right a4-node-links">
        <a class="a4-icon-button" href="{$site_url|wash}" target="_blank" rel="noopener" title="{'View on site (new window)'|i18n( 'design/admin/node/view/full' )|wash}" aria-label="{'View on site'|i18n( 'design/admin/node/view/full' )|wash}"><svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path d="M14 4h6v6M20 4l-9 9M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
        <a class="a4-icon-button" href={concat( 'content/versionview/', $object.id, '/', $object.current_version, '/', $object.current_language )|ezurl} title="{'Preview the current version'|i18n( 'design/admin/node/view/full' )|wash}" aria-label="{'Preview'|i18n( 'design/admin/node/view/full' )|wash}"><svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" fill="none" stroke="currentColor" stroke-width="2"/></svg></a>
        <span class="versions"><a class="a4-icon-button" href={concat( 'content/history/', $node.contentobject_id )|ezurl} title="{'View and manage (copy, delete, etc.) the versions of this object.'|i18n( 'design/admin/content/edit' )}" aria-label="{'Manage versions'|i18n( 'design/admin/content/edit' )|wash}"><svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path d="M12 7v5l3 2M3.5 12a8.5 8.5 0 1 0 2.5-6M3 4v4h4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></a></span>
    </div>
    </form>
    </div>
</div>

{* ---- One quiet meta line ---- *}
<div class="box-content">
<p class="context-information a4-node-meta">
    <span title="{$object.modified|l10n( 'datetime' )}">{'Modified'|i18n( 'design/admin/node/view/full' )} {$object.modified|l10n( 'shortdatetime' )}{if $object.current.creator} {'by'|i18n( 'design/admin/node/view/full' )} <a href={$object.current.creator.main_node.url_alias|ezurl}>{$object.current.creator.name|wash}</a>{/if}</span>
    {if $object.published}<span title="{$object.published|l10n( 'datetime' )}">{'Published'|i18n( 'design/admin/node/view/full' )} {$object.published|l10n( 'shortdate' )}</span>{/if}
    <span>{'Version'|i18n( 'design/admin/node/view/full' )} {$object.current_version}</span>
    {if $section}<span>{$section.name|wash}</span>{/if}
    <button type="button" class="a4-copy" data-a4-copy="{$node.node_id}" title="{'Click to copy'|i18n( 'design/admin/node/view/full' )|wash}">{'Node'|i18n( 'design/admin/node/view/full' )} {$node.node_id}</button>
    <button type="button" class="a4-copy" data-a4-copy="{$object.id}" title="{'Click to copy'|i18n( 'design/admin/node/view/full' )|wash}">{'Object'|i18n( 'design/admin/node/view/full' )} {$object.id}</button>
    <button type="button" class="a4-copy" data-a4-copy="{$object.remote_id|wash}" title="{'Click to copy the remote ID'|i18n( 'design/admin/node/view/full' )|wash}">{'Remote ID'|i18n( 'design/admin/node/view/full' )}</button>
    {if gt( $locations, 1 )}<span>{$locations} {'locations'|i18n( 'design/admin/node/view/full' )}</span>{/if}
    <span class="a4-node-langs">
    {foreach $translations as $language}
        <a class="a4-node-lang{if eq( $language.locale, $object.current_language )} a4-current{/if}" href={concat( 'content/view/full/', $node.node_id, '/(language)/', $language.locale )|ezurl} title="{$language.name|wash}"><img src="{$language.locale|flag_icon}" width="18" height="12" alt="{$language.locale|wash}" /></a>
    {/foreach}
    </span>
</p>
</div>

{* ---- The tabs (unchanged template: extensions add theirs) ---- *}
<div id="window-controls" class="tab-block a4-node-tabs">
{include uri='design:window_controls.tpl'}
</div>

{undef $js_class_languages $disable_another_language $disabled_sub_menu $has_content_class $object $section
       $locations $translations $site_url}
</div>

{* ---- Children ---- *}
<div id="content-view-children" class="a4-node-children">
{if $node.is_container}
    {include uri='design:children.tpl'}
{else}
    {include uri='design:no_children.tpl'}
{/if}
</div>

</div>

 </div>
</div>
