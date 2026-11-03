{* The datatypes of the installation (class/datatype/list), the companion of class/grouplist.

   Top down: what a datatype is, the figures, a warning for the datatypes the database uses but nothing registers,
   the filters (view parameters /(filter)/<used|unused|orphans> and /(group)/<provider>, no query string), a search
   and the sorting of the tables (page/class_datatypes.js and page/exp_list_script.js, both CSP safe), then one table
   per provider: the kernel, each extension, the datatypes nothing registers. Read only.

   Variables (kernel/private/classes/views/class/datatype.php): datatype_groups (the filtered groups),
   datatype_all_groups, datatype_summary, datatype_filter, datatype_group, datatype_counted, datatype_cache_ttl,
   view_parameters, module. The same file is in design/admin and design/admin4.
   Guide: doc/eng-us/guides/datatypes.md *}

{def $summary = $datatype_summary
     $group_suffix = cond( $datatype_group|ne( '' ), concat( '/(group)/', $datatype_group ), '' )
     $filter_suffix = cond( $datatype_filter|ne( 'all' ), concat( '/(filter)/', $datatype_filter ), '' )}

<div class="context-block a4-page exp-classgroups exp-datatypes">

<div class="box-header"><div class="box-ml">
<div class="a4-title-row">
<h1 class="context-title">{'Datatypes (%count)'|i18n( 'design/admin/class/datatype',, hash( '%count', $summary.datatypes ) )|wash}</h1>
<span class="a4-meta">{'Counted %time, again after %minutes minutes'|i18n( 'design/admin/class/datatype',, hash( '%time', $datatype_counted|l10n( shortdatetime ), '%minutes', div( $datatype_cache_ttl, 60 )|floor ) )|wash}</span>
</div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="a4-intro">{'A datatype stores and edits one kind of value of a class attribute: a text line, an image, a relation. The kernel brings most of them, extensions add more. Here are all of them, who provides them and how much of the content uses them. A datatype that the database uses but nothing registers any more breaks the content that has it.'|i18n( 'design/admin/class/datatype' )}</p>

<div class="a4-toolbar">
    <div class="a4-actions">
        <a class="a4-btn" href={'class/grouplist'|ezurl}>{'Class groups'|i18n( 'design/admin/class/datatype' )}</a>
        <a class="a4-btn" href={concat( 'class/datatype/list', $filter_suffix, $group_suffix, '/(refresh)/1' )|ezurl} title="{'Count again now instead of using the counts of the last minutes.'|i18n( 'design/admin/class/datatype' )|wash}">{'Count again'|i18n( 'design/admin/class/datatype' )}</a>
    </div>
</div>

<section aria-labelledby="datatypes-overview-title">
<h2 class="a4-sr-only" id="datatypes-overview-title">{'Overview'|i18n( 'design/admin/class/datatype' )}</h2>
<ul class="a4-figures">
    <li class="a4-figure"><strong>{$summary.registered}</strong><span>{'Registered'|i18n( 'design/admin/class/datatype' )}</span></li>
    <li class="a4-figure is-ok"><strong>{$summary.used}</strong><span>{'In use'|i18n( 'design/admin/class/datatype' )}</span></li>
    <li class="a4-figure"><strong>{$summary.unused}</strong><span>{'Not used'|i18n( 'design/admin/class/datatype' )}</span></li>
    <li class="a4-figure"><strong>{$summary.extensions}</strong><span>{'Extensions providing datatypes'|i18n( 'design/admin/class/datatype' )}</span></li>
    <li class="a4-figure"><strong>{$summary.class_attributes}</strong><span>{'Class attributes'|i18n( 'design/admin/class/datatype' )}</span></li>
    <li class="a4-figure"><strong>{$summary.object_attributes}</strong><span>{'Object attributes'|i18n( 'design/admin/class/datatype' )}</span></li>
    {if $summary.orphans|gt( 0 )}<li class="a4-figure is-bad"><strong>{$summary.orphans}</strong><span>{'Not registered'|i18n( 'design/admin/class/datatype' )}</span></li>{/if}
    {if $summary.missing|gt( 0 )}<li class="a4-figure is-warn"><strong>{$summary.missing}</strong><span>{'Listed, file missing'|i18n( 'design/admin/class/datatype' )}</span></li>{/if}
</ul>
</section>

{if $summary.orphans|gt( 0 )}
<div class="a4-notice is-bad" role="alert">
    <p class="a4-notice-title"><strong>{'%count datatypes are used but not registered'|i18n( 'design/admin/class/datatype',, hash( '%count', $summary.orphans ) )|wash}</strong></p>
    <p>{'%class_attributes class attributes and %object_attributes object attributes have a datatype that no extension or setting registers. Their objects cannot be shown, edited or published. Activate the extension that provides the datatype, or change the class.'|i18n( 'design/admin/class/datatype',, hash( '%class_attributes', $summary.orphan_class_attributes, '%object_attributes', $summary.orphan_object_attributes ) )|wash}</p>
    {if $datatype_filter|ne( 'orphans' )}<p><a href={'class/datatype/list/(filter)/orphans'|ezurl}>{'Show only these'|i18n( 'design/admin/class/datatype' )}</a></p>{/if}
</div>
{/if}

<div class="a4-filterbar">
    <div class="a4-field exp-js-only" hidden>
        <label for="datatypes-search">{'Find a datatype'|i18n( 'design/admin/class/datatype' )}</label>
        <input type="search" id="datatypes-search" autocomplete="off" spellcheck="false" aria-describedby="datatypes-filter-count datatypes-search-help" />
        <span class="a4-help" id="datatypes-search-help">{'Identifier, name, PHP class or extension.'|i18n( 'design/admin/class/datatype' )}</span>
    </div>
    <div class="a4-field">
        <span id="datatypes-filter-label"><strong>{'Show'|i18n( 'design/admin/class/datatype' )}</strong></span>
        <ul class="a4-tabs" aria-labelledby="datatypes-filter-label">
        {foreach array( hash( 'key', 'all', 'text', 'All'|i18n( 'design/admin/class/datatype' ) ),
                        hash( 'key', 'used', 'text', 'In use'|i18n( 'design/admin/class/datatype' ) ),
                        hash( 'key', 'unused', 'text', 'Not used'|i18n( 'design/admin/class/datatype' ) ),
                        hash( 'key', 'orphans', 'text', 'Not registered'|i18n( 'design/admin/class/datatype' ) ) ) as $tab}
            <li>{if eq( $tab.key, $datatype_filter )}<span class="current" aria-current="true">{$tab.text|wash}</span>{else}<a href={concat( 'class/datatype/list', cond( $tab.key|ne( 'all' ), concat( '/(filter)/', $tab.key ), '' ), $group_suffix )|ezurl}>{$tab.text|wash}</a>{/if}</li>
        {/foreach}
        </ul>
    </div>
    <p class="a4-filter-count exp-js-only" id="datatypes-filter-count" aria-live="polite" hidden></p>
</div>

<div class="a4-filterbar">
    <div class="a4-field a4-field-wide">
        <span id="datatypes-group-label"><strong>{'Provided by'|i18n( 'design/admin/class/datatype' )}</strong></span>
        <ul class="a4-tabs" aria-labelledby="datatypes-group-label">
            <li>{if $datatype_group|eq( '' )}<span class="current" aria-current="true">{'Everyone'|i18n( 'design/admin/class/datatype' )}</span>{else}<a href={concat( 'class/datatype/list', $filter_suffix )|ezurl}>{'Everyone'|i18n( 'design/admin/class/datatype' )}</a>{/if}</li>
        {foreach $datatype_all_groups as $key => $group}
            {def $label = cond( $group.kind|eq( 'kernel' ), 'Kernel'|i18n( 'design/admin/class/datatype' ), $group.kind|eq( 'orphans' ), 'Not registered'|i18n( 'design/admin/class/datatype' ), $key )}
            <li>{if $datatype_group|eq( $key )}<span class="current" aria-current="true">{$label|wash} ({$group.count})</span>{else}<a href={concat( 'class/datatype/list', $filter_suffix, '/(group)/', $key )|ezurl}>{$label|wash} <span class="a4-muted">{$group.count}</span></a>{/if}</li>
            {undef $label}
        {/foreach}
        </ul>
    </div>
</div>

{if $datatype_groups|count|eq( 0 )}
<p class="a4-empty">{'No datatype matches this filter.'|i18n( 'design/admin/class/datatype' )} <a href={'class/datatype/list'|ezurl}>{'Show all'|i18n( 'design/admin/class/datatype' )}</a></p>
{/if}

{foreach $datatype_groups as $key => $group}
{def $label = cond( $group.kind|eq( 'kernel' ), 'Kernel'|i18n( 'design/admin/class/datatype' ), $group.kind|eq( 'orphans' ), 'Not registered'|i18n( 'design/admin/class/datatype' ), $key )
     $section_id = concat( 'datatypes-', $key|explode( '_' )|implode( '-' ) )}
<section class="a4-section" aria-labelledby="{$section_id}-title">
<div class="a4-section-head">
    <h2 class="a4-h2" id="{$section_id}-title">{$label|wash}</h2>
    {if $group.kind|eq( 'extension' )}<span class="a4-tag is-info">{'Extension'|i18n( 'design/admin/class/datatype' )}</span>{/if}
    <span class="a4-meta">{'%shown of %count datatypes, %used in use'|i18n( 'design/admin/class/datatype',, hash( '%shown', $group.shown, '%count', $group.count, '%used', $group.used ) )|wash}</span>
</div>
{if $group.kind|eq( 'orphans' )}<p class="a4-help">{'The database has class or object attributes of these datatypes, but content.ini lists none of them, or its file is missing.'|i18n( 'design/admin/class/datatype' )}</p>{/if}
<div class="a4-table-wrap">
<table class="a4-table" data-datatype-sort="1">
<caption class="a4-sr-only">{'Datatypes of %provider'|i18n( 'design/admin/class/datatype',, hash( '%provider', $label ) )|wash}</caption>
<thead><tr>
    <th scope="col" data-sort="text">{'Datatype'|i18n( 'design/admin/class/datatype' )}</th>
    <th scope="col" data-sort="text">{'PHP class and file'|i18n( 'design/admin/class/datatype' )}</th>
    <th scope="col" class="a4-num" data-sort="number">{'Classes'|i18n( 'design/admin/class/datatype' )}</th>
    <th scope="col" class="a4-num" data-sort="number">{'Class attributes'|i18n( 'design/admin/class/datatype' )}</th>
    <th scope="col" class="a4-num" data-sort="number">{'Objects'|i18n( 'design/admin/class/datatype' )}</th>
    <th scope="col" class="a4-num" data-sort="number">{'Object attributes'|i18n( 'design/admin/class/datatype' )}</th>
    <th scope="col">{'Flags'|i18n( 'design/admin/class/datatype' )}</th>
</tr></thead>
<tbody data-list="1">
{foreach $group.rows as $row}
<tr data-search="{$row.search|wash}">
    <td data-value="{$row.string|wash}">
        <a href={concat( 'class/datatype/view/', $row.string )|ezurl}><code>{$row.string|wash}</code></a>
        {if $row.name|ne( '' )}<br /><span class="a4-meta">{$row.name|wash}</span>{/if}
        {switch match=$row.status}
        {case match='orphan'}<br /><span class="a4-tag is-bad">{'Not registered'|i18n( 'design/admin/class/datatype' )}</span>{/case}
        {case match='missing'}<br /><span class="a4-tag is-warn">{'Listed, file missing'|i18n( 'design/admin/class/datatype' )}</span>{/case}
        {case match='unused'}<br /><span class="a4-tag">{'Not used'|i18n( 'design/admin/class/datatype' )}</span>{/case}
        {case}{/case}
        {/switch}
    </td>
    <td data-value="{$row.class|wash}">{if $row.class|ne( '' )}<code title="{$row.file|wash}">{$row.class|wash}</code>{else}<span class="a4-muted">{'none'|i18n( 'design/admin/class/datatype' )}</span>{/if}</td>
    <td class="a4-num" data-value="{$row.classes}">{$row.classes}</td>
    <td class="a4-num" data-value="{$row.class_attributes}">{$row.class_attributes}</td>
    <td class="a4-num" data-value="{$row.objects}">{$row.objects}</td>
    <td class="a4-num" data-value="{$row.object_attributes}">{$row.object_attributes}</td>
    <td>{include uri='design:class/datatype_flags.tpl' flags=$row.flags}</td>
</tr>
{/foreach}
</tbody>
</table>
</div>
</section>
{undef $label $section_id}
{/foreach}
<p class="a4-empty exp-no-match" data-global="1" hidden>{'No datatype matches the search. Clear the search.'|i18n( 'design/admin/class/datatype' )}</p>

</div></div></div>
</div>

{include uri='design:class/exp_list_script.tpl' text_shown='%shown of %count datatypes shown'|i18n( 'design/admin/class/datatype' ) text_all='Datatypes shown: %count'|i18n( 'design/admin/class/datatype' ) text_selected=''}
<div hidden data-datatype-sort-text data-text-sort="{'Sort by %column'|i18n( 'design/admin/class/datatype' )|wash}"></div>
{ezscript( array( 'page/class_datatypes.js' ) )}
{undef $summary $group_suffix $filter_suffix}
