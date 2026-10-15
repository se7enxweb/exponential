{* The classes of one class group (class/classlist/<group id>).

   The group with its ID, last change and actions (Edit, Remove, back to the class groups), an overview, a search,
   then the classes in a table: name, identifier, ID, published objects, the other groups the class is in, the last
   change, and View, Edit and Copy. New class creates one in this group in the language chosen; Remove selected
   leads to the class/removeclass confirmation, which says what goes with each class.

   Every field and button name is the one the view has always read (DeleteIDArray[], RemoveButton, NewButton,
   ClassLanguageCode, CurrentGroupID, CurrentGroupName; the group form posts EditGroupID, EditGroupButton,
   RemoveGroupButton and DeleteIDArray[] to class/grouplist), and every template variable is still used (group,
   group_modifier, groupclasses, class_count, limit, view_parameters, GroupID, module). Copy posts the form to
   class/copy/<id>, so a copy is never made by following a link. The same file is in design/admin and design/admin4.
   Guide: doc/guides/class-groups.md *}

{def $languages = fetch( 'content', 'prioritized_languages' )
     $objects = 0
     $containers = 0}
{foreach $groupclasses as $c}{set $objects = sum( $objects, $c.object_count )}{if $c.is_container|eq( 1 )}{set $containers = inc( $containers )}{/if}{/foreach}

<div class="context-block a4-page exp-classgroups">

<div class="box-header"><div class="box-ml">
<div class="a4-title-row">
<h1 class="context-title">{$group.name|wash|classgroup_icon( 'normal', $group.name|wash )}&nbsp;{'%group_name [Class group]'|i18n( 'design/admin/class/classlist',, hash( '%group_name', $group.name ) )|wash}</h1>
<span class="a4-meta">{'ID %id'|i18n( 'design/admin/class/grouplist',, hash( '%id', $group.id ) )}</span>
</div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="a4-intro">{'The classes listed in this group. A class can be in several groups; removing a class here asks first and says how many objects go with it, while a class that is also in another group only leaves this one.'|i18n( 'design/admin/class/classlist' )}</p>

<form action={'class/grouplist'|ezurl} method="post" name="GroupList" class="a4-toolbar">
    <p class="a4-meta">{'Last modified'|i18n( 'design/admin/class/classlist' )}: {$group.modified|l10n( shortdatetime )}{if $group_modifier}, {$group_modifier.name|wash}{/if}</p>
    <div class="a4-actions">
        <input type="hidden" name="DeleteIDArray[]" value="{$group.id}" />
        <input type="hidden" name="EditGroupID" value="{$group.id}" />
        <a class="a4-btn" href={'/class/grouplist'|ezurl}>{'Back to class groups.'|i18n( 'design/admin/class/classlist' )}</a>
        <button class="a4-btn" type="submit" name="EditGroupButton" value="1" title="{'Edit this class group.'|i18n( 'design/admin/class/classlist' )}">{'Edit'|i18n( 'design/admin/class/classlist' )}</button>
        <button class="a4-btn a4-btn-outline-danger" type="submit" name="RemoveGroupButton" value="1" title="{'Remove this class group.'|i18n( 'design/admin/class/classlist' )}">{'Remove'|i18n( 'design/admin/class/classlist' )}</button>
    </div>
</form>

<ul class="a4-figures">
    <li class="a4-figure"><strong>{$class_count}</strong><span>{'Classes'|i18n( 'design/admin/class/grouplist' )}</span></li>
    <li class="a4-figure"><strong>{$objects}</strong><span>{if $class_count|gt( $limit )}{'Published objects on this page'|i18n( 'design/admin/class/classlist' )}{else}{'Published objects'|i18n( 'design/admin/class/grouplist' )}{/if}</span></li>
    <li class="a4-figure"><strong>{$containers}</strong><span>{'Containers'|i18n( 'design/admin/class/classlist' )}</span></li>
</ul>

<form action={concat( 'class/classlist/', $GroupID )|ezurl} method="post" name="ClassList">
<input type="hidden" name="CurrentGroupID" value="{$GroupID|wash}" />
<input type="hidden" name="CurrentGroupName" value="{$group.name|wash}" />

<div class="a4-filterbar">
    <div class="a4-field exp-js-only" hidden>
        <label for="classlist-search">{'Find a class'|i18n( 'design/admin/class/classlist' )}</label>
        <input type="search" id="classlist-search" autocomplete="off" spellcheck="false" aria-controls="classlist-table" aria-describedby="classlist-filter-count{if $class_count|gt( $limit )} classlist-search-page{/if}" />
        {if $class_count|gt( $limit )}<span class="a4-help" id="classlist-search-page">{'Only this page is searched: %from to %to of %count.'|i18n( 'design/admin/class/classlist',, hash( '%from', sum( $view_parameters.offset, 1 ), '%to', min( sum( $view_parameters.offset, $limit ), $class_count ), '%count', $class_count ) )}</span>{/if}
    </div>
    <div class="a4-field">
        {if gt( $languages|count, 1 )}
        <label for="ClassLanguageCode">{'Language of the new class'|i18n( 'design/admin/class/classlist' )}</label>
        <div class="a4-searchrow">
            <select name="ClassLanguageCode" id="ClassLanguageCode">
            {foreach $languages as $language}
                <option value="{$language.locale|wash}">{$language.name|wash}</option>
            {/foreach}
            </select>
            <button class="a4-btn a4-btn-primary" type="submit" name="NewButton" id="NewButtonTop" value="1" title="{'Create a new class within the <%class_group_name> class group.'|i18n( 'design/admin/class/classlist',, hash( '%class_group_name', $group.name ) )|wash}"><span class="a4-icon a4-bi-plus-lg" aria-hidden="true"></span>{'New class'|i18n( 'design/admin/class/classlist' )}</button>
        </div>
        {else}
        <input type="hidden" name="ClassLanguageCode" value="{$languages[0].locale|wash}" />
        <span class="a4-help">{'A new class starts with a name, an identifier and no attributes.'|i18n( 'design/admin/class/classlist' )}</span>
        <div class="a4-actions"><button class="a4-btn a4-btn-primary" type="submit" name="NewButton" id="NewButtonTop" value="1"><span class="a4-icon a4-bi-plus-lg" aria-hidden="true"></span>{'New class'|i18n( 'design/admin/class/classlist' )}</button></div>
        {/if}
    </div>
    <p class="a4-filter-count exp-js-only" id="classlist-filter-count" aria-live="polite" hidden></p>
</div>

<section class="a4-section" aria-labelledby="classlist-title">
<div class="a4-section-head">
    <h2 class="a4-h2" id="classlist-title">{'Classes inside <%group_name> (%class_count)'|i18n( 'design/admin/class/classlist',, hash( '%group_name', $group.name, '%class_count', $class_count ) )|wash}</h2>
    {if $class_count|gt( $limit )}<span class="a4-meta">{'%from to %to of %count'|i18n( 'design/admin/class/grouplist',, hash( '%from', sum( $view_parameters.offset, 1 ), '%to', min( sum( $view_parameters.offset, $limit ), $class_count ), '%count', $class_count ) )}</span>{/if}
    {if $class_count}<label class="a4-meta exp-js-only" hidden><input type="checkbox" id="classlist-select-all" data-select-all="DeleteIDArray[]" /> {'Select all on this page'|i18n( 'design/admin/class/grouplist' )}</label>{/if}
</div>

{if $class_count|eq( 0 )}
<p class="a4-empty">{'There are no classes in this group.'|i18n( 'design/admin/class/classlist' )} {'Create one with New class, or add an existing class to this group from its page.'|i18n( 'design/admin/class/classlist' )}</p>
{else}
<div class="a4-table-wrap">
<table class="a4-table" id="classlist-table" summary="{'List of classes inside %group_name class group (%class_count)'|i18n( 'design/admin/class/classlist',, hash( '%group_name', $group.name, '%class_count', $class_count ) )|wash}">
<thead><tr>
    <th scope="col"><span class="a4-sr-only">{'Select'|i18n( 'design/admin/class/classlist' )}</span></th>
    <th scope="col">{'Name'|i18n( 'design/admin/class/classlist' )}</th>
    <th scope="col" class="a4-num">{'Objects'|i18n( 'design/admin/class/classlist' )}</th>
    <th scope="col">{'Also in'|i18n( 'design/admin/class/classlist' )}</th>
    <th scope="col">{'Modified'|i18n( 'design/admin/class/classlist' )}</th>
    <th scope="col"><span class="a4-sr-only">{'Actions'|i18n( 'design/admin/class/classlist' )}</span></th>
</tr></thead>
<tbody data-list="1">
{foreach $groupclasses as $class}
<tr data-search="{concat( $class.name, ' ', $class.identifier, ' ', $class.id )|downcase|wash}">
    <td><input type="checkbox" name="DeleteIDArray[]" value="{$class.id}" aria-label="{'Select %name for removal'|i18n( 'design/admin/class/grouplist',, hash( '%name', $class.name ) )|wash}" /></td>
    <td>{$class.identifier|class_icon( small, $class.name|wash )}&nbsp;<a href={concat( '/class/view/', $class.id )|ezurl}><strong>{$class.name|wash}</strong></a>
        <span class="a4-meta"><code>{$class.identifier|wash}</code> &middot; {'ID %id'|i18n( 'design/admin/class/grouplist',, hash( '%id', $class.id ) )}{if $class.is_container|eq( 1 )} &middot; {'container'|i18n( 'design/admin/class/classlist' )}{/if}</span></td>
    <td class="a4-num">{$class.object_count}</td>
    <td>{def $others = array()}{foreach $class.ingroup_list as $link}{if ne( $link.group_id, $group.id )}{set $others = $others|append( $link )}{/if}{/foreach}
        {if $others|count}{foreach $others as $link}<a href={concat( '/class/classlist/', $link.group_id )|ezurl}>{$link.group_name|wash}</a>{delimiter}, {/delimiter}{/foreach}{else}<span class="a4-muted">{'only here'|i18n( 'design/admin/class/classlist' )}</span>{/if}{undef $others}</td>
    <td>{$class.modified|l10n( shortdatetime )}{if $class.modifier.contentobject}<span class="a4-meta">{$class.modifier.contentobject.name|wash}</span>{/if}</td>
    <td><div class="a4-actions">
        <a class="a4-btn a4-btn-small" href={concat( '/class/view/', $class.id )|ezurl}>{'View'|i18n( 'design/admin/class/classlist' )}</a>
        <a class="a4-btn a4-btn-small" href={concat( 'class/edit/', $class.id, '/(language)/', $class.top_priority_language_locale )|ezurl} title="{'Edit the <%class_name> class.'|i18n( 'design/admin/class/classlist',, hash( '%class_name', $class.name ) )|wash}">{'Edit'|i18n( 'design/admin/class/classlist' )}</a>
        <button class="a4-btn a4-btn-small" type="submit" formaction={concat( 'class/copy/', $class.id )|ezurl} name="CopyClassButton" value="{$class.id}" title="{'Create a copy of the <%class_name> class.'|i18n( 'design/admin/class/classlist',, hash( '%class_name', $class.name ) )|wash}">{'Copy'|i18n( 'design/admin/class/classlist' )}</button>
    </div></td>
</tr>
{/foreach}
</tbody>
</table>
</div>
<p class="a4-empty exp-no-match" data-global="1" hidden>{'No class on this page matches. Clear the search.'|i18n( 'design/admin/class/classlist' )}</p>
{/if}

{if $class_count|gt( $limit )}
<div class="a4-listfoot"><div class="a4-pager">
{include name=ClassNavigator
         uri='design:navigator/google.tpl'
         page_uri=concat( '/class/classlist/', $GroupID )
         item_count=$class_count
         view_parameters=$view_parameters
         item_limit=$limit}
</div></div>
{/if}
</section>

<div class="a4-toolbar is-bottom">
    <div class="a4-actions">
        <button class="a4-btn a4-btn-outline-danger" type="submit" name="RemoveButton" value="1" aria-describedby="classlist-remove-help" title="{'Remove selected classes from the <%class_group_name> class group.'|i18n( 'design/admin/class/classlist',, hash( '%class_group_name', $group.name ) )|wash}"{if $class_count|eq( 0 )} disabled="disabled"{/if}>{'Remove selected'|i18n( 'design/admin/class/classlist' )}</button>
        <button class="a4-btn" type="submit" name="NewButton" value="1">{'New class'|i18n( 'design/admin/class/classlist' )}</button>
    </div>
    <p class="a4-meta" id="classlist-remove-help">{'Remove selected asks for confirmation first. A class only in this group is removed with all its objects; one that is also in another group only leaves this one. Copy makes a copy of the class in the same groups.'|i18n( 'design/admin/class/classlist' )} <span class="exp-selected-count" data-for="DeleteIDArray[]" aria-live="polite"></span></p>
</div>

</form>

</div></div></div>
</div>
{undef $languages $objects $containers}
{include uri='design:class/exp_list_script.tpl' text_shown='%shown of %count classes on this page shown'|i18n( 'design/admin/class/classlist' ) text_all='Classes on this page: %count'|i18n( 'design/admin/class/classlist' ) text_selected='%count selected.'|i18n( 'design/admin/class/grouplist' )}
