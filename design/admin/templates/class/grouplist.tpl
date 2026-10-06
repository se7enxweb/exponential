{* The class groups (class/grouplist).

   What a class group is, an overview of the groups, classes and objects, a search, then one card per group with
   its classes and their published objects, when the group or one of its classes last changed, and what removing
   the group would remove. Remove selected leads to the confirmation of class/removegroup, as before. Below, the
   recently modified classes.

   The same file is in design/admin and design/admin4. The extra data comes from the view (group_overview,
   group_summary); without it (an older view class) the cards show what the group rows hold and nothing is lost.
   Every variable the page had before is still set: groups, group_count, limit, view_parameters, module. Field
   names (DeleteIDArray[], RemoveGroupButton, NewGroupButton) are unchanged. Guide: doc/guides/class-groups.md *}
{include uri='design:class/exp_style.tpl'}

{def $overview = first_set( $group_overview, hash() )
     $summary = first_set( $group_summary, false() )}

<form name="GroupList" method="post" action={'class/grouplist'|ezurl}>

<div class="context-block exp-lists exp-classgroups">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Class groups (%group_count)'|i18n( 'design/admin/class/grouplist',, hash( '%group_count', $group_count ) )|wash}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'A class defines a kind of content: its fields, how its objects are named and where they may be created. Classes are kept in groups so that they are easy to find; a class can belong to more than one group. Removing a group removes the classes that are in no other group, and with them all their objects.'|i18n( 'design/admin/class/grouplist' )}</p>

{if $summary}
<section aria-labelledby="classgroups-overview-title">
<h2 class="exp-sr" id="classgroups-overview-title">{'Overview'|i18n( 'design/admin/class/grouplist' )}</h2>
<ul class="exp-figures">
    <li class="exp-figure"><strong>{$summary.groups}</strong><span>{'Groups'|i18n( 'design/admin/class/grouplist' )}</span></li>
    <li class="exp-figure"><strong>{$summary.classes}</strong><span>{'Classes'|i18n( 'design/admin/class/grouplist' )}</span></li>
    <li class="exp-figure"><strong>{$summary.objects}</strong><span>{'Published objects'|i18n( 'design/admin/class/grouplist' )}</span></li>
    <li class="exp-figure"><strong>{$summary.shared}</strong><span>{'In more than one group'|i18n( 'design/admin/class/grouplist' )}</span></li>
    {if $summary.ungrouped|gt( 0 )}<li class="exp-figure is-attention"><strong>{$summary.ungrouped}</strong><span>{'In no group'|i18n( 'design/admin/class/grouplist' )}</span></li>{/if}
</ul>
</section>
{/if}

<div class="exp-toolbar">
    <div class="exp-field exp-js-only" hidden>
        <label for="classgroups-search">{'Find a group or class'|i18n( 'design/admin/class/grouplist' )}</label>
        <input type="search" id="classgroups-search" autocomplete="off" spellcheck="false" aria-controls="classgroups-list" aria-describedby="classgroups-filter-count classgroups-search-help" />
        <span class="exp-help" id="classgroups-search-help">{'Group name, class name or identifier.'|i18n( 'design/admin/class/grouplist' )}</span>
    </div>
    <div class="exp-field">
        <span class="exp-help">{'Open a group to create classes in it, copy or remove them.'|i18n( 'design/admin/class/grouplist' )}</span>
        <div class="exp-actions">
            <button type="submit" class="exp-btn exp-btn-primary" name="NewGroupButton" value="1" title="{'Create a new class group.'|i18n( 'design/admin/class/grouplist' )}"><svg width="14" height="14" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M7 2h2v5h5v2H9v5H7V9H2V7h5z"/></svg>{'New class group'|i18n( 'design/admin/class/grouplist' )}</button>
        </div>
    </div>
    <p class="exp-filter-count exp-js-only" id="classgroups-filter-count" aria-live="polite" hidden></p>
</div>

<section class="exp-section" aria-labelledby="classgroups-list-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="classgroups-list-title">{'All class groups'|i18n( 'design/admin/class/grouplist' )}</h2>
    {if $group_count|gt( $limit )}
    <span class="exp-meta">{'%from to %to of %count'|i18n( 'design/admin/class/grouplist',, hash( '%from', sum( $view_parameters.offset, 1 ), '%to', min( sum( $view_parameters.offset, $limit ), $group_count ), '%count', $group_count ) )}</span>
    {/if}
    {if $groups|count}<label class="exp-meta exp-js-only" hidden><input type="checkbox" id="classgroups-select-all" data-select-all="DeleteIDArray[]" /> {'Select all on this page'|i18n( 'design/admin/class/grouplist' )}</label>{/if}
</div>

{if $groups|count|eq( 0 )}
<p class="exp-empty">{'There are no class groups. Create one with New class group; every class needs a group to be listed in.'|i18n( 'design/admin/class/grouplist' )}</p>
{else}
<ul class="exp-secs" id="classgroups-list" data-list="1">
{foreach $groups as $group}
    {def $info = first_set( $overview[$group.id], false() )
         $card_id = concat( 'classgroup-', $group.id )}
<li class="exp-sec" id="{$card_id}" data-search="{if $info}{$info.search|wash}{else}{$group.name|downcase|wash}{/if}">
    <div class="exp-sec-head">
        <div class="exp-sec-title">
            <label class="exp-select" title="{'Select class group for removal.'|i18n( 'design/admin/class/grouplist' )}">
                <input type="checkbox" name="DeleteIDArray[]" value="{$group.id}" aria-label="{'Select %name for removal'|i18n( 'design/admin/class/grouplist',, hash( '%name', $group.name ) )|wash}" />
            </label>
            <h3 id="{$card_id}-title">{$group.name|wash|classgroup_icon( small, $group.name|wash )}&nbsp;<a href={concat( $module.functions.classlist.uri, '/', $group.id )|ezurl}>{$group.name|wash}</a></h3>
            <span class="exp-meta">{'ID %id'|i18n( 'design/admin/class/grouplist',, hash( '%id', $group.id ) )}</span>
            {if $info}
            <ul class="exp-badges">
                {if $info.class_count|eq( 0 )}<li class="exp-badge">{'Empty'|i18n( 'design/admin/class/grouplist' )}</li>{else}<li class="exp-badge is-info">{'%count classes'|i18n( 'design/admin/class/grouplist',, hash( '%count', $info.class_count ) )}</li>
                <li class="exp-badge">{'%count objects'|i18n( 'design/admin/class/grouplist',, hash( '%count', $info.objects ) )}</li>{/if}
            </ul>
            {/if}
        </div>
        <div class="exp-actions">
            <a class="exp-btn exp-btn-small" href={concat( $module.functions.classlist.uri, '/', $group.id )|ezurl} aria-describedby="{$card_id}-title">{'Open'|i18n( 'design/admin/class/grouplist' )}</a>
            <a class="exp-btn exp-btn-small" href={concat( $module.functions.groupedit.uri, '/', $group.id )|ezurl} aria-describedby="{$card_id}-title" title="{'Edit the <%class_group_name> class group.'|i18n( 'design/admin/class/grouplist',, hash( '%class_group_name', $group.name ) )|wash}">{'Edit'|i18n( 'design/admin/class/grouplist' )}</a>
        </div>
    </div>
    {if and( $info, $info.class_count|gt( 0 ) )}
    <p style="margin-top: 12px;">
    {foreach $info.classes as $class}<a class="exp-fn" href={concat( 'class/view/', $class.id )|ezurl} title="{$class.identifier|wash}">{$class.name|wash} <span class="exp-muted">{$class.objects}</span></a>{/foreach}
    {if $info.more|gt( 0 )}<a href={concat( $module.functions.classlist.uri, '/', $group.id )|ezurl}>{'and %count more'|i18n( 'design/admin/class/grouplist',, hash( '%count', $info.more ) )}</a>{/if}
    </p>
    {/if}
    <dl class="exp-facts">
        <div>
            <dt>{'Last change'|i18n( 'design/admin/class/grouplist' )}</dt>
            <dd>{if $info}{$info.last_modified|l10n( shortdatetime )}{if $info.last_class}<span class="exp-meta">{'class %name'|i18n( 'design/admin/class/grouplist',, hash( '%name', $info.last_class.name|wash ) )}</span>{/if}{else}{$group.modified|l10n( shortdatetime )}{/if}</dd>
        </div>
        <div>
            <dt>{'Group modified'|i18n( 'design/admin/class/grouplist' )}</dt>
            <dd>{$group.modified|l10n( shortdatetime )}{if $group.modifier.contentobject}<span class="exp-meta">{'by %name'|i18n( 'design/admin/class/grouplist',, hash( '%name', $group.modifier.contentobject.name|wash ) )}</span>{/if}</dd>
        </div>
        {if $info}
        <div style="grid-column: 1 / -1;">
            <dt>{'Removing it'|i18n( 'design/admin/class/grouplist' )}</dt>
            <dd>{if $info.removes|eq( 0 )}{'Removes only the group.'|i18n( 'design/admin/class/grouplist' )}{if $info.shared|gt( 0 )} {'Its %count classes stay in their other groups.'|i18n( 'design/admin/class/grouplist',, hash( '%count', $info.shared ) )}{/if}
                {else}<strong{if $info.removes_objects|gt( 0 )} style="color: var(--sc-bad);"{/if}>{'Removes %classes classes and their %objects objects.'|i18n( 'design/admin/class/grouplist',, hash( '%classes', $info.removes, '%objects', $info.removes_objects ) )}</strong>{if $info.shared|gt( 0 )} {'%count classes stay in their other groups.'|i18n( 'design/admin/class/grouplist',, hash( '%count', $info.shared ) )}{/if}{/if}</dd>
        </div>
        {/if}
    </dl>
</li>
    {undef $info $card_id}
{/foreach}
</ul>
<p class="exp-empty exp-no-match" hidden>{'No group on this page matches. Clear the search.'|i18n( 'design/admin/class/grouplist' )}</p>
{/if}

{if $group_count|gt( $limit )}
<div class="exp-listfoot"><div class="exp-pager">
{include name=GroupNavigator
         uri='design:navigator/google.tpl'
         page_uri='/class/grouplist'
         item_count=$group_count
         view_parameters=$view_parameters
         item_limit=$limit}
</div></div>
{/if}
</section>

<div class="exp-bottombar">
    <div class="exp-actions">
        <button type="submit" class="exp-btn exp-btn-outline-danger" name="RemoveGroupButton" value="1" aria-describedby="classgroups-remove-help" title="{'Remove the selected class groups. This will also remove all classes that only exist within the selected groups.'|i18n( 'design/admin/class/grouplist' )}"{if $groups|count|eq( 0 )} disabled="disabled"{/if}>{'Remove selected'|i18n( 'design/admin/class/grouplist' )}</button>
        <button type="submit" class="exp-btn" name="NewGroupButton" value="1" title="{'Create a new class group.'|i18n( 'design/admin/class/grouplist' )}">{'New class group'|i18n( 'design/admin/class/grouplist' )}</button>
    </div>
    <p class="exp-meta" id="classgroups-remove-help">{'Remove selected asks for confirmation first and lists the classes and objects that would go.'|i18n( 'design/admin/class/grouplist' )} <span class="exp-selected-count" data-for="DeleteIDArray[]" aria-live="polite"></span></p>
</div>

{* The classes changed last, in every group. *}
{def $latest_classes = fetch( class, latest_list, hash( limit, 10 ) )}
{if $latest_classes}
<section class="exp-section" style="margin-top: 28px;" aria-labelledby="classgroups-latest-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="classgroups-latest-title">{'Recently modified classes'|i18n( 'design/admin/class/grouplist' )}</h2>
</div>
<div class="exp-table-wrap">
<table class="exp-table" summary="{'List of recently modified classes'|i18n( 'design/admin/class/grouplist' )}">
<thead><tr>
    <th scope="col">{'Name'|i18n( 'design/admin/class/grouplist' )}</th>
    <th scope="col" class="exp-num">{'ID'|i18n( 'design/admin/class/grouplist' )}</th>
    <th scope="col">{'Identifier'|i18n( 'design/admin/class/grouplist' )}</th>
    <th scope="col">{'Modifier'|i18n( 'design/admin/class/grouplist' )}</th>
    <th scope="col">{'Modified'|i18n( 'design/admin/class/grouplist' )}</th>
    <th scope="col" class="exp-num">{'Objects'|i18n( 'design/admin/class/grouplist' )}</th>
    <th scope="col"><span class="exp-sr">{'Edit'|i18n( 'design/admin/class/grouplist' )}</span></th>
</tr></thead>
<tbody>
{foreach $latest_classes as $latest}
<tr>
    <td>{$latest.identifier|class_icon( small, $latest.name|wash )}&nbsp;<a href={concat( '/class/view/', $latest.id )|ezurl}>{$latest.name|wash}</a></td>
    <td class="exp-num">{$latest.id}</td>
    <td><code>{$latest.identifier|wash}</code></td>
    <td>{if $latest.modifier.contentobject}{$latest.modifier.contentobject.name|wash}{/if}</td>
    <td>{$latest.modified|l10n( shortdatetime )}</td>
    <td class="exp-num">{$latest.object_count}</td>
    <td><a class="exp-btn exp-btn-small" href={concat( 'class/edit/', $latest.id, '/(language)/', $latest.top_priority_language_locale )|ezurl} title="{'Edit the <%class_name> class.'|i18n( 'design/admin/class/grouplist',, hash( '%class_name', $latest.name ) )|wash}">{'Edit'|i18n( 'design/admin/class/grouplist' )}</a></td>
</tr>
{/foreach}
</tbody>
</table>
</div>
</section>
{/if}
{undef $latest_classes}

</div></div></div>
</div>

</form>

{undef $overview $summary}
{include uri='design:class/exp_list_script.tpl' text_shown='%shown of %count groups on this page shown'|i18n( 'design/admin/class/grouplist' ) text_all='Groups on this page: %count'|i18n( 'design/admin/class/grouplist' ) text_selected='%count selected.'|i18n( 'design/admin/class/grouplist' )}
