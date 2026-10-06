{* The versions of an object (content/history/<object id>).

   An introduction to versions and to making a new draft from one, the messages of the last action, the figures
   (versions by status, translations, the user's own drafts, newer drafts), the filters by status, translation and
   creator, the order, then one card per version with its status, translations, creator and dates and the actions
   the user has on it; an action that is not offered says why. Below the list: comparing two versions, removing the
   ticked versions (confirmed in place) and the Back button. A comparison shows its differences above the list.

   The same file is in design/admin, design/admin3 and design/admin4. The list, the figures and the actions come from
   the view (history_rows, history_overview, history_choices, history_filters ...), worked out by
   expContentHistoryList; who may open the page and whose content they see by History::canOpen() and
   History::canSeeVersionContent() (content_versions, can_read, refused). Every POST name of the page before is kept
   (DeleteIDArray[], RemoveButton, HistoryCopyVersionButton[n], CopyVersionLanguage[n], HistoryEditButton[n],
   DoNotEditAfterCopy, DiffButton, FromVersion, ToVersion, Language, BackButton, RedirectURI); CompareButton[n] is new.
   Everything works without javascript; the script only adds Select all, the count of ticked versions and the
   switch between the views of the differences.
   Guide: doc/guides/content-history.md *}
{include uri='design:content/history_exp_style.tpl'}

{def $rows = first_set( $history_rows, array() )
     $overview = first_set( $history_overview, false() )
     $choices = first_set( $history_choices, hash( 'creators', hash(), 'languages', hash() ) )
     $filters = first_set( $history_filters, hash( 'status', '', 'language', '', 'creator', 0, 'sort', 'newest' ) )
     $suffix = first_set( $history_suffix, '' )
     $links = first_set( $history_links, hash( 'status', hash( 'all', '' ), 'language', hash( 'all', '' ), 'creator', hash( 'all', '' ), 'sort', hash() ) )
     $status_names = first_set( $history_status_names, array( 'draft', 'published', 'pending', 'archived', 'rejected', 'untouched', 'repeat', 'queued' ) )
     $count = first_set( $history_count, $rows|count )
     $page_limit = first_set( $history_limit, 25 )
     $feedback = first_set( $history_feedback, false() )
     $base = concat( '/content/history/', $object.id, '/', $object.current_version )
     $seen = first_set( $content_versions, array() )
     $show_diff = and( is_set( $diff ), is_set( $oldVersion ), is_set( $newVersion ) )
     $status_text = hash( 'draft', 'Draft'|i18n( 'design/admin/content/history' ),
                          'published', 'Published'|i18n( 'design/admin/content/history' ),
                          'pending', 'Pending'|i18n( 'design/admin/content/history' ),
                          'archived', 'Archived'|i18n( 'design/admin/content/history' ),
                          'rejected', 'Rejected'|i18n( 'design/admin/content/history' ),
                          'untouched', 'Untouched draft'|i18n( 'design/admin/content/history' ),
                          'repeat', 'Repeat'|i18n( 'design/standard/content/history' ),
                          'queued', 'Queued'|i18n( 'design/standard/content/history' ) )
     $status_class = hash( 'draft', 'is-info', 'published', 'is-ok', 'pending', 'is-warn', 'archived', '', 'rejected', 'is-bad',
                           'untouched', '', 'repeat', 'is-warn', 'queued', 'is-warn' )
     $why_text = hash( 'versionread', 'Viewing it needs the policy content/versionread for this version.'|i18n( 'design/admin/content/history' ),
                       'no_edit', 'You may not edit this object.'|i18n( 'design/admin/content/history' ),
                       'not_draft', 'Only drafts are edited: make a new draft from this version to change it.'|i18n( 'design/admin/content/history' ),
                       'not_own', 'This draft belongs to someone else: make a new draft from it to change it.'|i18n( 'design/admin/content/history' ),
                       'untouched', 'An untouched draft has nothing to copy.'|i18n( 'design/admin/content/history' ),
                       'not_readable', 'You may not read this version, so it is not copied or compared.'|i18n( 'design/admin/content/history' ),
                       'no_language', 'You may not edit any translation of this version.'|i18n( 'design/admin/content/history' ),
                       'published', 'The published version is not removed.'|i18n( 'design/admin/content/history' ),
                       'workflow', 'This version is in a workflow that is still running, so it is not removed.'|i18n( 'design/admin/content/history' ),
                       'no_remove', 'You may not remove this version.'|i18n( 'design/admin/content/history' ) )}

<div id="leftmenu" class="sidebar left">
<div id="leftmenu-design">
    {include uri="design:content/parts/object_information.tpl" object=$object manage_version_button=false()}
</div>
</div>

<div id="maincontent"><div id="maincontent-design" class="float-break"><div id="fix">
<!-- Maincontent START -->

<div class="context-block exp-history">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Versions of “%name” (%count)'|i18n( 'design/admin/content/history',, hash( '%name', $object.name, '%count', first_set( $overview.total, $versions|count ) ) )|wash}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'Each save of an object is a version. A draft becomes the published version when it is published, and the version before it is archived. To change an older version or someone else’s draft, make a new draft from it: the new draft is yours, in the translation you choose, and you edit and publish it as usual. When the object already has as many versions as the version history limit of content.ini allows, the oldest archived version is removed to make room.'|i18n( 'design/admin/content/history' )}</p>

{switch match=$edit_warning}
{case match=1}
<div class="exp-feedback is-warn" role="alert">
    <p><strong>{'Version is not a draft'|i18n( 'design/admin/content/history' )}</strong></p>
    <p>{'Version %1 is not available for editing anymore. Only drafts can be edited.'|i18n( 'design/admin/content/history',, array( $edit_version ) )|wash} {'To edit this version, first create a copy of it.'|i18n( 'design/admin/content/history' )}</p>
</div>
{/case}
{case match=2}
<div class="exp-feedback is-warn" role="alert">
    <p><strong>{'Version is not yours'|i18n( 'design/admin/content/history' )}</strong></p>
    <p>{'Version %1 was not created by you. You can only edit your own drafts.'|i18n( 'design/admin/content/history',, array( $edit_version ) )|wash} {'To edit this version, first create a copy of it.'|i18n( 'design/admin/content/history' )}</p>
</div>
{/case}
{case match=3}
<div class="exp-feedback is-bad" role="alert">
    <p><strong>{'Unable to create new version'|i18n( 'design/admin/content/history' )}</strong></p>
    <p>{'Version history limit has been exceeded and no archived version can be removed by the system.'|i18n( 'design/admin/content/history' )} {'You can change your version history settings in content.ini, remove draft versions or edit existing drafts.'|i18n( 'design/admin/content/history' )}</p>
</div>
{/case}
{case}
{/case}
{/switch}

{include uri='design:content/history_access_messages.tpl' can_read=$can_read refused=$refused}

{if $feedback}
    {if eq( $feedback.type, 'removed' )}
<div class="exp-feedback is-ok" role="status">{'Removed: version %versions.'|i18n( 'design/admin/content/history',, hash( '%versions', $feedback.versions|implode( ', ' ) ) )|wash}</div>
    {elseif eq( $feedback.type, 'copied' )}
<div class="exp-feedback is-ok" role="status">{'Version %from was copied to your new draft %to (%language). Edit it from the list below.'|i18n( 'design/admin/content/history',, hash( '%from', $feedback.from, '%to', $feedback.to, '%language', $feedback.language ) )|wash}</div>
    {/if}
{/if}

{if $show_diff}
<section class="exp-section exp-diff" id="history-diff" aria-labelledby="history-diff-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="history-diff-title">{'Differences between versions %oldVersion and %newVersion'|i18n( 'design/admin/content/history',, hash( '%oldVersion', $oldVersion, '%newVersion', $newVersion ) )|wash}</h2>
    <a class="exp-btn exp-btn-small" href={concat( $base, $suffix )|ezurl}>{'Close the comparison'|i18n( 'design/admin/content/history' )}</a>
    <p>{'Removed text is struck through, added text is underlined. Choose how the changes are shown:'|i18n( 'design/admin/content/history' )}</p>
</div>
<ul class="exp-tabs exp-js-only" id="history-diff-modes" hidden>
    <li><button type="button" class="exp-tab is-current" data-mode="inlinechanges" aria-pressed="true">{'Inline changes'|i18n( 'design/admin/content/history' )}</button></li>
    <li><button type="button" class="exp-tab" data-mode="blockchanges" aria-pressed="false">{'Block changes'|i18n( 'design/admin/content/history' )}</button></li>
    <li><button type="button" class="exp-tab" data-mode="previous" aria-pressed="false">{'Old version'|i18n( 'design/admin/content/history' )}</button></li>
    <li><button type="button" class="exp-tab" data-mode="latest" aria-pressed="false">{'New version'|i18n( 'design/admin/content/history' )}</button></li>
</ul>
<div id="diffview" class="inlinechanges">
<ul class="exp-cards">
{foreach $object.data_map as $attr}
    <li class="exp-card">
        <h3>{$attr.contentclass_attribute.name|wash}</h3>
        <div class="attribute-view-diff">
            {attribute_diff_gui view=diff attribute=$attr old=$oldVersion new=$newVersion diff=$diff[$attr.contentclassattribute_id]}
        </div>
    </li>
{/foreach}
</ul>
</div>
</section>
{/if}

{if $overview}
<section aria-labelledby="history-overview-title">
<h2 class="exp-sr" id="history-overview-title">{'Overview'|i18n( 'design/admin/content/history' )}</h2>
<ul class="exp-figures">
    <li class="exp-figure{if eq( $filters.status, '' )} is-current{/if}"><a href={$base|ezurl}><strong>{$overview.total}</strong> <span>{'Versions'|i18n( 'design/admin/content/history' )}</span></a></li>
    {foreach $overview.statuses as $status_name => $status_count}
    <li class="exp-figure{if eq( $filters.status, $status_name )} is-current{/if}"><a href={concat( $base, '/(status)/', $status_name )|ezurl}><strong>{$status_count}</strong> <span>{$status_text[$status_name]|wash}</span></a></li>
    {/foreach}
    <li class="exp-figure"><strong>{$overview.translations}</strong> <span>{'Translations'|i18n( 'design/admin/content/history' )}</span></li>
    <li class="exp-figure"><a href={concat( $base, '/(creator)/', $user_id )|ezurl}><strong>{$overview.own_drafts}</strong> <span>{'Your drafts'|i18n( 'design/admin/content/history' )}</span></a></li>
</ul>
</section>

<section aria-labelledby="history-find-title">
<h2 class="exp-sr" id="history-find-title">{'Filter and order'|i18n( 'design/admin/content/history' )}</h2>
<div class="exp-toolbar">
    <div class="exp-field">
        <span class="exp-field-label" id="history-status-label"><strong>{'Status'|i18n( 'design/admin/content/history' )}</strong></span>
        <ul class="exp-tabs" aria-labelledby="history-status-label">
            <li>{if eq( $filters.status, '' )}<span class="current" aria-current="true">{'All'|i18n( 'design/admin/content/history' )}</span>{else}<a href={concat( $base, $links.status.all )|ezurl}>{'All'|i18n( 'design/admin/content/history' )}</a>{/if}</li>
        {foreach $overview.statuses as $status_name => $status_count}
            <li>{if eq( $filters.status, $status_name )}<span class="current" aria-current="true">{$status_text[$status_name]|wash} <span class="exp-count">{$status_count}</span></span>{else}<a href={concat( $base, $links.status[$status_name] )|ezurl}>{$status_text[$status_name]|wash} <span class="exp-count">{$status_count}</span></a>{/if}</li>
        {/foreach}
        </ul>
    </div>
    {if or( $choices.languages|count|gt( 1 ), $filters.language|ne( '' ) )}
    <div class="exp-field">
        <span class="exp-field-label" id="history-language-label"><strong>{'Translation'|i18n( 'design/admin/content/history' )}</strong></span>
        <ul class="exp-tabs" aria-labelledby="history-language-label">
            <li>{if eq( $filters.language, '' )}<span class="current" aria-current="true">{'All'|i18n( 'design/admin/content/history' )}</span>{else}<a href={concat( $base, $links.language.all )|ezurl}>{'All'|i18n( 'design/admin/content/history' )}</a>{/if}</li>
        {foreach $choices.languages as $locale => $language_name}
            <li>{if eq( $filters.language, $locale )}<span class="current" aria-current="true">{$language_name|wash}</span>{else}<a href={concat( $base, $links.language[$locale] )|ezurl}>{$language_name|wash}</a>{/if}</li>
        {/foreach}
        </ul>
    </div>
    {/if}
    {if or( $choices.creators|count|gt( 1 ), $filters.creator|gt( 0 ) )}
    <div class="exp-field">
        <span class="exp-field-label" id="history-creator-label"><strong>{'Creator'|i18n( 'design/admin/content/history' )}</strong></span>
        <ul class="exp-tabs" aria-labelledby="history-creator-label">
            <li>{if eq( $filters.creator, 0 )}<span class="current" aria-current="true">{'Anyone'|i18n( 'design/admin/content/history' )}</span>{else}<a href={concat( $base, $links.creator.all )|ezurl}>{'Anyone'|i18n( 'design/admin/content/history' )}</a>{/if}</li>
        {foreach $choices.creators as $creator_id => $creator_name}
            <li>{if eq( $filters.creator, $creator_id )}<span class="current" aria-current="true">{$creator_name|wash}</span>{else}<a href={concat( $base, $links.creator[$creator_id] )|ezurl}>{$creator_name|wash}</a>{/if}</li>
        {/foreach}
        </ul>
    </div>
    {/if}
    <div class="exp-field">
        <span class="exp-field-label" id="history-sort-label"><strong>{'Order'|i18n( 'design/admin/content/history' )}</strong></span>
        <ul class="exp-tabs" aria-labelledby="history-sort-label">
        {foreach array( hash( 'sort', 'newest', 'text', 'Newest first'|i18n( 'design/admin/content/history' ) ),
                        hash( 'sort', 'oldest', 'text', 'Oldest first'|i18n( 'design/admin/content/history' ) ),
                        hash( 'sort', 'modified', 'text', 'Last modified'|i18n( 'design/admin/content/history' ) ) ) as $tab}
            <li>{if eq( $tab.sort, $filters.sort )}<span class="current" aria-current="true">{$tab.text|wash}</span>{else}<a href={concat( $base, $links.sort[$tab.sort] )|ezurl}>{$tab.text|wash}</a>{/if}</li>
        {/foreach}
        </ul>
    </div>
</div>
</section>
{/if}

<form name="versionsform" method="post" action={concat( $base, $suffix, cond( $view_parameters.offset|gt( 0 ), concat( '/(offset)/', $view_parameters.offset ), '' ) )|ezurl}>
<input type="hidden" name="ObjectID" value="{$object.id}" />

{* What no version on this page allows is said once above the list, not on every card *}
{def $viewable = 0
     $removable = 0}
{foreach $rows as $row}{if $row.actions.view.allowed}{set $viewable = inc( $viewable )}{/if}{if $row.actions.remove.allowed}{set $removable = inc( $removable )}{/if}{/foreach}
<section class="exp-section" aria-labelledby="history-list-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="history-list-title">{'Versions'|i18n( 'design/admin/content/history' )}</h2>
    {if $count|gt( 0 )}
    <span class="exp-meta">{'%from to %to of %count'|i18n( 'design/admin/content/history',, hash( '%from', sum( $view_parameters.offset, 1 ), '%to', min( sum( $view_parameters.offset, $page_limit ), $count ), '%count', $count ) )}</span>
    {if $removable|gt( 0 )}<label class="exp-meta exp-js-only" hidden><input type="checkbox" id="history-select-all" /> {'Select all on this page'|i18n( 'design/admin/content/history' )}</label>{/if}
    {/if}
    {if and( $rows|count|gt( 0 ), or( $viewable|eq( 0 ), and( $can_edit, $removable|eq( 0 ) ) ) )}
    <ul class="exp-why exp-why-page">
        {if $viewable|eq( 0 )}<li>{'Opening the versions needs the policy content/versionread; you see what each version is, and compare and copy those you may read.'|i18n( 'design/admin/content/history' )}</li>{/if}
        {if and( $can_edit, $removable|eq( 0 ) )}<li>{'No version on this page can be removed by you: removing needs the policy content/versionremove, and the published version and versions in a running workflow are never removed.'|i18n( 'design/admin/content/history' )}</li>{/if}
    </ul>
    {/if}
</div>

{if $rows|count|eq( 0 )}
<p class="exp-empty">{if $suffix|ne( '' )}{'No version matches these filters.'|i18n( 'design/admin/content/history' )} <a href={$base|ezurl}>{'Show all versions'|i18n( 'design/admin/content/history' )}</a>{else}{'This object does not have any versions.'|i18n( 'design/admin/content/history' )}{/if}</p>
{else}
<ul class="exp-cards" id="history-list">
{foreach $rows as $row}
    {def $card_id = concat( 'history-version-', $row.version )
         $actions = $row.actions
         $why = array()}
    {if and( $actions.view.allowed|not, $viewable|gt( 0 ) )}{set $why = $why|append( $why_text[$actions.view.reason] )}{/if}
    {if and( $actions.edit.allowed|not, $actions.edit.reason|ne( 'not_draft' ) )}{set $why = $why|append( $why_text[$actions.edit.reason] )}{/if}
    {if $actions.copy.allowed|not}{set $why = $why|append( $why_text[$actions.copy.reason] )}{/if}
    {if and( $actions.remove.allowed|not, $actions.remove.reason|ne( 'published' ), $removable|gt( 0 ) )}{set $why = $why|append( $why_text[$actions.remove.reason] )}{/if}
    {set $why = $why|unique}
<li class="exp-card{if eq( $row.status, 1 )} is-current{/if}" id="{$card_id}">
    <div class="exp-card-head">
        <div class="exp-card-title">
            <label class="exp-select" title="{if $actions.remove.allowed}{'Select version %number for removal.'|i18n( 'design/admin/content/history',, hash( '%number', $row.version ) )}{else}{$why_text[$actions.remove.reason]|wash}{/if}">
                {if $actions.remove.allowed}
                <input type="checkbox" name="DeleteIDArray[]" value="{$row.id}" data-version="{$row.version}" aria-label="{'Select version %number for removal'|i18n( 'design/admin/content/history',, hash( '%number', $row.version ) )}" />
                {else}
                <input type="checkbox" disabled="disabled" aria-label="{$why_text[$actions.remove.reason]}" />
                {/if}
            </label>
            <h3 id="{$card_id}-title">{if $actions.view.allowed}<a href={concat( '/content/versionview/', $object.id, '/', $row.version, '/', $row.language, '/' )|ezurl} title="{'View the contents of version #%version_number. Translation: %translation.'|i18n( 'design/admin/content/history',, hash( '%version_number', $row.version, '%translation', $row.language_name ) )}">{'Version %number'|i18n( 'design/admin/content/history',, hash( '%number', $row.version ) )}</a>{else}{'Version %number'|i18n( 'design/admin/content/history',, hash( '%number', $row.version ) )}{/if}</h3>
            <ul class="exp-badges">
                <li class="exp-badge {$status_class[$row.status_name]}">{$status_text[$row.status_name]|wash}</li>
                {if eq( $row.version, $object.current_version )}<li class="exp-badge is-info">{'Current version'|i18n( 'design/admin/content/history' )}</li>{/if}
                {if eq( $row.creator_id, $user_id )}<li class="exp-badge">{'Yours'|i18n( 'design/admin/content/history' )}</li>{/if}
            </ul>
        </div>
        <div class="exp-actions">
            {if $actions.view.allowed}
            <a class="exp-btn exp-btn-small" href={concat( '/content/versionview/', $object.id, '/', $row.version, '/', $row.language, '/' )|ezurl} aria-describedby="{$card_id}-title">{'View'|i18n( 'design/admin/content/history' )}</a>
            {/if}
            {if $actions.edit.allowed}
            <button type="submit" class="exp-btn exp-btn-small exp-btn-primary" name="HistoryEditButton[{$row.version}]" value="1" aria-describedby="{$card_id}-title">{'Edit'|i18n( 'design/admin/content/history' )}</button>
            {/if}
            {if and( $actions.compare.allowed, $object.can_diff, $seen|count|gt( 1 ) )}
            <button type="submit" class="exp-btn exp-btn-small" name="CompareButton[{$row.version}]" value="1" aria-describedby="{$card_id}-title" title="{if eq( $row.version, $object.current_version )}{'Compare with the newest other version you may read.'|i18n( 'design/admin/content/history' )}{else}{'Compare with the current version.'|i18n( 'design/admin/content/history' )}{/if}">{'Compare'|i18n( 'design/admin/content/history' )}</button>
            {/if}
        </div>
    </div>
    <dl class="exp-facts">
        <div>
            <dt>{'Translations'|i18n( 'design/admin/content/history' )}</dt>
            <dd>{foreach $row.languages as $locale => $language_name}{delimiter}, {/delimiter}<img src="{$locale|flag_icon}" width="18" height="12" alt="" /> {$language_name|wash}{if eq( $locale, $row.language )} <span class="exp-muted">({'modified'|i18n( 'design/admin/content/history' )})</span>{/if}{/foreach}</dd>
        </div>
        <div>
            <dt>{'Creator'|i18n( 'design/admin/content/history' )}</dt>
            <dd>{$row.creator_name|wash}</dd>
        </div>
        <div>
            <dt>{'Created'|i18n( 'design/admin/content/history' )}</dt>
            <dd>{$row.created|l10n( shortdatetime )}</dd>
        </div>
        <div>
            <dt>{'Modified'|i18n( 'design/admin/content/history' )}</dt>
            <dd>{$row.modified|l10n( shortdatetime )}</dd>
        </div>
    </dl>
    {if $actions.copy.allowed}
    <div class="exp-copy">
        <label for="{$card_id}-language">{'New draft from this version, in'|i18n( 'design/admin/content/history' )}</label>
        {if $actions.copy_languages|count|gt( 1 )}
        <select id="{$card_id}-language" name="CopyVersionLanguage[{$row.version}]">
            {foreach $actions.copy_languages as $locale => $language_name}
            <option value="{$locale|wash}"{if eq( $locale, $row.language )} selected="selected"{/if}>{$language_name|wash}</option>
            {/foreach}
        </select>
        {else}
            {foreach $actions.copy_languages as $locale => $language_name}
        <input type="hidden" name="CopyVersionLanguage[{$row.version}]" value="{$locale|wash}" />
        <span class="exp-copy-language" id="{$card_id}-language">{$language_name|wash}</span>
            {/foreach}
        {/if}
        <button type="submit" class="exp-btn exp-btn-small" name="HistoryCopyVersionButton[{$row.version}]" value="1" aria-describedby="{$card_id}-title">{'Make a new draft'|i18n( 'design/admin/content/history' )}</button>
    </div>
    {/if}
    {if $why|count}
    <ul class="exp-why">
        {foreach $why as $reason}<li>{$reason|wash}</li>{/foreach}
    </ul>
    {/if}
</li>
    {undef $card_id $actions $why}
{/foreach}
</ul>
{/if}

<div class="exp-listfoot">
    <p class="exp-sizes">
        <span>{'Per page'|i18n( 'design/admin/content/history' )}:</span>
    {foreach first_set( $history_limit_choices, array( 10, 25, 50 ) ) as $limit_index => $limit_option}
        {if eq( $limit_option, $page_limit )}
        <span class="current" aria-current="true">{$limit_option}</span>
        {else}
        <a href={concat( '/user/preferences/set/admin_history_list_limit/', $limit_index|inc )|ezurl} title="{'Show %count versions per page.'|i18n( 'design/admin/content/history',, hash( '%count', $limit_option ) )}">{$limit_option}</a>
        {/if}
    {/foreach}
    </p>
    <div class="exp-pager">
    {include name=navigator
             uri='design:navigator/google.tpl'
             page_uri=$base
             page_uri_suffix=$suffix
             item_count=$count
             view_parameters=$view_parameters
             item_limit=$page_limit}
    </div>
</div>
</section>

{if and( $object.can_diff, $seen|count|gt( 1 ) )}
<section class="exp-section" aria-labelledby="history-compare-title">
<div class="exp-panel">
    <h2 class="exp-h2" id="history-compare-title">{'Compare two versions'|i18n( 'design/admin/content/history' )}</h2>
    <p class="exp-help">{'Only the versions whose content you may read are offered.'|i18n( 'design/admin/content/history' )}</p>
    <div class="exp-compare">
        <div class="exp-field">
            <label for="history-from">{'Older version'|i18n( 'design/admin/content/history' )}</label>
            <select id="history-from" name="FromVersion">
            {foreach $object.versions as $ver}{if $seen|contains( $ver.version )}
                <option value="{$ver.version}"{if eq( $ver.version, $selectOldVersion )} selected="selected"{/if}>{'Version %number'|i18n( 'design/admin/content/history',, hash( '%number', $ver.version ) )} &ndash; {$status_text[first_set( $status_names[$ver.status], 'draft' )]|wash}</option>
            {/if}{/foreach}
            </select>
        </div>
        <div class="exp-field">
            <label for="history-to">{'Newer version'|i18n( 'design/admin/content/history' )}</label>
            <select id="history-to" name="ToVersion">
            {foreach $object.versions as $ver}{if $seen|contains( $ver.version )}
                <option value="{$ver.version}"{if eq( $ver.version, $selectNewVersion )} selected="selected"{/if}>{'Version %number'|i18n( 'design/admin/content/history',, hash( '%number', $ver.version ) )} &ndash; {$status_text[first_set( $status_names[$ver.status], 'draft' )]|wash}</option>
            {/if}{/foreach}
            </select>
        </div>
        <div class="exp-field">
            <label for="history-diff-language">{'Translation'|i18n( 'design/admin/content/history' )}</label>
            <select id="history-diff-language" name="Language">
            {foreach $object.languages as $lang}
                <option value="{$lang.locale|wash}"{if and( is_set( $diff_language ), eq( $diff_language, $lang.locale ) )} selected="selected"{/if}>{$lang.name|wash}</option>
            {/foreach}
            </select>
        </div>
        <div class="exp-field">
            <button type="submit" class="exp-btn exp-btn-primary" name="DiffButton" value="1">{'Show differences'|i18n( 'design/admin/content/history' )}</button>
        </div>
    </div>
</div>
</section>
{/if}

<div class="exp-bottombar">
    <div class="exp-actions">
        {if is_set( $redirect_uri )}<input type="hidden" name="RedirectURI" value="{$redirect_uri|wash}" />{/if}
        <button type="submit" class="exp-btn" name="BackButton" value="1">{'Back'|i18n( 'design/admin/content/history' )}</button>
        {if and( $can_edit, $removable|gt( 0 ) )}
        <details class="exp-confirm" id="history-remove">
            <summary>{'Remove selected versions'|i18n( 'design/admin/content/history' )}</summary>
            <div>
                <p><span id="history-remove-list">{'The ticked versions are removed for good, with all their translations.'|i18n( 'design/admin/content/history' )}</span> {'The published version and versions in a running workflow are never removed.'|i18n( 'design/admin/content/history' )}</p>
                <button type="submit" class="exp-btn exp-btn-danger" name="RemoveButton" value="1" id="history-remove-button">{'Remove for good'|i18n( 'design/admin/content/history' )}</button>
            </div>
        </details>
        {/if}
    </div>
    {if $can_edit}
    <label class="exp-check exp-meta"><input type="checkbox" name="DoNotEditAfterCopy" value="1" checked="checked" /> <span>{'Stay on this page after making a new draft (untick to open the new draft in the editor at once).'|i18n( 'design/admin/content/history' )}</span></label>
    {/if}
    <p class="exp-meta" id="history-selected-count" aria-live="polite"></p>
</div>

</form>

</div></div></div>
</div>

<!-- Maincontent END -->
</div></div><div class="break"></div></div>

<script type="text/javascript">
var expHistoryText = {ldelim}
    selected: '{'%count selected.'|i18n( 'design/admin/content/history' )|wash( javascript )}',
    removeList: '{'Versions %list are removed for good, with all their translations.'|i18n( 'design/admin/content/history' )|wash( javascript )}',
    removeNone: '{'Tick the versions to remove in the list first.'|i18n( 'design/admin/content/history' )|wash( javascript )}'
{rdelim};
{literal}
(function () {
    var nodes = document.querySelectorAll( '.exp-history .exp-js-only' ), i;
    for ( i = 0; i < nodes.length; i++ ) nodes[i].hidden = false;
    var modes = document.getElementById( 'history-diff-modes' ), view = document.getElementById( 'diffview' );
    if ( modes && view ) modes.addEventListener( 'click', function ( e ) {
        var b = e.target.closest( 'button[data-mode]' ); if ( !b ) return;
        view.className = b.getAttribute( 'data-mode' );
        var all = modes.querySelectorAll( 'button' ), j;
        for ( j = 0; j < all.length; j++ ) { all[j].classList.toggle( 'is-current', all[j] === b ); all[j].setAttribute( 'aria-pressed', all[j] === b ? 'true' : 'false' ); }
    } );
    var list = document.getElementById( 'history-list' );
    if ( !list ) return;
    var selectAll = document.getElementById( 'history-select-all' );
    var countEl = document.getElementById( 'history-selected-count' );
    var removeList = document.getElementById( 'history-remove-list' );
    var removeButton = document.getElementById( 'history-remove-button' );
    function boxes() { return list.querySelectorAll( 'input[name="DeleteIDArray[]"]' ); }
    function update() {
        var all = boxes(), ticked = [], j;
        for ( j = 0; j < all.length; j++ ) {
            var card = all[j].closest( '.exp-card' );
            if ( card ) card.classList.toggle( 'is-selected', all[j].checked );
            if ( all[j].checked ) ticked.push( all[j].getAttribute( 'data-version' ) );
        }
        if ( countEl ) countEl.textContent = ticked.length ? expHistoryText.selected.split( '%count' ).join( ticked.length ) : '';
        if ( removeList ) removeList.textContent = ticked.length ? expHistoryText.removeList.split( '%list' ).join( ticked.join( ', ' ) ) : expHistoryText.removeNone;
        if ( removeButton ) removeButton.disabled = !ticked.length;
        if ( selectAll ) { selectAll.checked = ticked.length > 0 && ticked.length === all.length; selectAll.indeterminate = ticked.length > 0 && ticked.length < all.length; }
    }
    list.addEventListener( 'change', update );
    if ( selectAll ) selectAll.addEventListener( 'change', function () {
        var all = boxes(), j;
        for ( j = 0; j < all.length; j++ ) all[j].checked = selectAll.checked;
        update();
    } );
    update();
})();
{/literal}
</script>
