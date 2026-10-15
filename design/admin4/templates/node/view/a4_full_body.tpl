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
     $a4_translations = fetch( 'content', 'language_states', hash( 'object', $node.object ) )
     $a4_main_language_name = $node.object.initial_language.name
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
<div class="box-header a4-node-head a4-pagehead">
    <a class="a4-node-icon" aria-haspopup="menu" aria-expanded="false" href={concat( '/class/view/', $object.contentclass_id )|ezurl} title="{'Class menu'|i18n( 'design/admin/node/view/full' )|wash}" onclick="ezpopmenu_showTopLevel( event, 'ClassMenu', ez_createAArray( new Array( '%classID%', {$object.contentclass_id}, '%objectID%', {$node.contentobject_id}, '%nodeID%', {$node.node_id}, '%currentURL%', '{$node.url|wash( javascript )|wash}', '%languages%', {$js_class_languages|wash} ) ), '{$node.class_name|wash(javascript)|wash}', {$disabled_sub_menu}, {$disable_another_language} ); return false;">{$node.class_identifier|class_icon( small, $node.class_name )}</a>
    <div class="a4-node-heading a4-pagehead-main">
        <h1 class="context-title a4-pagehead-title">{$node.name|wash}</h1>
        <span class="a4-node-badges">
            <span class="a4-badge a4-badge-class">{$node.class_name|wash}</span>
            {* visible, hidden or hidden by a parent: a badge with its status mark (parts/visibility_status.tpl) *}
            {include uri='design:parts/visibility_status.tpl' status_node=$node status_badge=true() status_quiet=false()}
            {* a user account that is disabled or locked, for those who may see it (expUserAccountStatus) *}
            {def $a4_account_status = fetch( 'user', 'account_status', hash( 'user_id', $node.contentobject_id ) )}
            {if eq( $a4_account_status, 'disabled' )}<span class="a4-badge a4-account-status" title="{'Account disabled: this user cannot log in.'|i18n( 'design/admin/node/view/full' )|wash}"><span class="a4-mark is-disabled" aria-hidden="true"></span>{'Disabled'|i18n( 'design/admin/node/view/full' )|wash}</span>
            {elseif eq( $a4_account_status, 'locked' )}<span class="a4-badge is-warn a4-account-status" title="{'Locked after too many failed logins.'|i18n( 'design/admin/node/view/full' )|wash}"><span class="a4-mark is-locked" aria-hidden="true"></span>{'Locked'|i18n( 'design/admin/node/view/full' )|wash}</span>{/if}
            {undef $a4_account_status}
            {if ne( $node.node_id, $node.main_node_id )}<span class="a4-badge">{'Secondary location'|i18n( 'design/admin/node/view/full' )}</span>{/if}
        </span>
    </div>

    <div class="controlbar a4-node-actions a4-pagehead-actions">
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
        <select name="ContentObjectLanguageCode" disabled="disabled" aria-label="{'Language to edit'|i18n( 'design/admin/node/view/full' )|wash}">
            <option value="">{'Not available'|i18n( 'design/admin/node/view/full' )}</option>
        </select>
        <input class="button-disabled" type="submit" name="EditButton" value="{'Edit'|i18n( 'design/admin/node/view/full' )}" title="{'You do not have permission to edit this item.'|i18n( 'design/admin/node/view/full' )}" disabled="disabled" />
    {/if}
    {undef $can_create_languages}
    {* Upload: ezmultiupload's upload page for this node, only where the user can upload (node/view/upload_button.tpl) *}
    {include uri='design:node/view/upload_button.tpl' node=$node}
    {* Import/Export: the export and import menu of this node (node/view/import_export_button.tpl), only with an entry for the user *}
    {include uri='design:node/view/import_export_button.tpl' node=$node}
    {if $node.can_move}
        <input class="button" type="submit" name="MoveNodeButton" value="{'Move'|i18n( 'design/admin/node/view/full' )}" title="{'Move this item to another location.'|i18n( 'design/admin/node/view/full' )}" />
    {else}
        <input class="button-disabled" type="submit" name="MoveNodeButton" value="{'Move'|i18n( 'design/admin/node/view/full' )}" title="{'You do not have permission to move this item to another location.'|i18n( 'design/admin/node/view/full' )}" disabled="disabled" />
    {/if}
    {* Copy: content/copy (choose where the copy goes), with create access at the parent, as in the context menu.
       Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 4d56593614), Copyright (C) Mugo Web, GPL v2. *}
    {if $node.parent.can_create}
        <input class="button" type="submit" formaction={concat( 'content/copy/', $node.contentobject_id )|ezurl} value="{'Copy'|i18n( 'design/admin/node/view/full' )}" title="{'Copy this item to another location.'|i18n( 'design/admin/node/view/full' )}" />
    {else}
        <input class="button-disabled" type="submit" value="{'Copy'|i18n( 'design/admin/node/view/full' )}" title="{'You do not have permission to copy this item.'|i18n( 'design/admin/node/view/full' )}" disabled="disabled" />
    {/if}
    {if $node.can_remove}
        <input class="button a4-danger" type="submit" name="ActionRemove" value="{'Remove'|i18n( 'design/admin/node/view/full' )}" title="{'Remove this item.'|i18n( 'design/admin/node/view/full' )}" />
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
    </div></div>
    <div class="button-right a4-node-links">
        <a class="a4-icon-button" href="{$site_url|wash}" target="_blank" rel="noopener" title="{'View on site (new window)'|i18n( 'design/admin/node/view/full' )|wash}" aria-label="{'View on site'|i18n( 'design/admin/node/view/full' )|wash}"><span class="a4-icon a4-bi-globe2 is-medium" aria-hidden="true"></span></a>
        <a class="a4-icon-button" href={concat( 'content/versionview/', $object.id, '/', $object.current_version, '/', $object.current_language )|ezurl} title="{'Preview the current version'|i18n( 'design/admin/node/view/full' )|wash}" aria-label="{'Preview'|i18n( 'design/admin/node/view/full' )|wash}"><span class="a4-icon a4-bi-eye is-medium" aria-hidden="true"></span></a>
        <span class="versions"><a class="a4-icon-button" href={concat( 'content/history/', $node.contentobject_id )|ezurl} title="{'View and manage (copy, delete, etc.) the versions of this object.'|i18n( 'design/admin/content/edit' )|wash}" aria-label="{'Manage versions'|i18n( 'design/admin/content/edit' )|wash}"><span class="a4-icon a4-bi-clock-history is-medium" aria-hidden="true"></span></a></span>
    </div>
    </form>
    </div>
</div>

{* ---- One quiet meta line ---- *}
<div class="box-content">
<p class="context-information a4-node-meta">
    <span title="{$object.modified|l10n( 'datetime' )}">{'Modified'|i18n( 'design/admin/node/view/full' )} {$object.modified|l10n( 'shortdatetime' )}{if $object.current.creator} {'by'|i18n( 'design/admin/node/view/full' )} <a href={$object.current.creator.main_node.url_alias|ezurl}>{$object.current.creator.name|wash}</a>{/if}</span>
    {if $object.published}<span title="{$object.published|l10n( 'datetime' )}">{'Published'|i18n( 'design/admin/node/view/full' )} {$object.published|l10n( 'shortdate' )}</span>{/if}
    <span class="exp-object-version" data-object-id="{$object.id}">{'Version'|i18n( 'design/admin/node/view/full' )} <span class="exp-object-version-number">{$object.current_version}</span></span>
    {if $section}<span>{$section.name|wash}</span>{/if}
    <button type="button" class="a4-copy" data-a4-copied="{'Copied to the clipboard'|i18n( 'design/admin/node/view/full' )|wash}" data-a4-copy-failed="{'Could not copy'|i18n( 'design/admin/node/view/full' )|wash}" data-a4-copy="{$node.node_id}" title="{'Click to copy'|i18n( 'design/admin/node/view/full' )|wash}"><span class="a4-copy-label">{'Node'|i18n( 'design/admin/node/view/full' )} {$node.node_id}</span></button>
    <button type="button" class="a4-copy" data-a4-copied="{'Copied to the clipboard'|i18n( 'design/admin/node/view/full' )|wash}" data-a4-copy-failed="{'Could not copy'|i18n( 'design/admin/node/view/full' )|wash}" data-a4-copy="{$object.id}" title="{'Click to copy'|i18n( 'design/admin/node/view/full' )|wash}"><span class="a4-copy-label">{'Object'|i18n( 'design/admin/node/view/full' )} {$object.id}</span></button>
    <button type="button" class="a4-copy" data-a4-copied="{'Copied to the clipboard'|i18n( 'design/admin/node/view/full' )|wash}" data-a4-copy-failed="{'Could not copy'|i18n( 'design/admin/node/view/full' )|wash}" data-a4-copy="{$object.remote_id|wash}" title="{'Click to copy the remote ID'|i18n( 'design/admin/node/view/full' )|wash}"><span class="a4-copy-label">{'Remote ID'|i18n( 'design/admin/node/view/full' )}</span></button>
    {if gt( $locations, 1 )}<span>{$locations} {'locations'|i18n( 'design/admin/node/view/full' )}</span>{/if}
    {* every language of the site with its state for this object: main (filled), present (outlined, a mark when
       older than the main language), missing (dashed; Translate into ... for who may); click a present one to
       switch to it (parts/language_state_badge.tpl, expContentLanguageStates) *}
    <span class="a4-node-langs">
    {foreach $a4_translations as $language}
        {include uri='design:parts/language_state_badge.tpl' state_entry=$language state_main_name=$a4_main_language_name
                 state_object_id=$object.id state_current=eq( $language.locale, $object.current_language )
                 state_href=concat( 'content/view/full/', $node.node_id, '/(language)/', $language.locale )|ezurl( 'no' )}
    {/foreach}
    </span>
</p>
</div>

{* ---- The tabs (unchanged template: extensions add theirs) ---- *}
<div id="window-controls" class="tab-block a4-node-tabs">
{include uri='design:window_controls.tpl'}
{include uri='design:parts/a4_tabs_height.tpl'}
</div>

{undef $js_class_languages $disable_another_language $disabled_sub_menu $has_content_class $object $section
       $locations $a4_translations $a4_main_language_name $site_url}
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
