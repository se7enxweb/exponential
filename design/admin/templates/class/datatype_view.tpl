{* One datatype (class/datatype/view/<datatype string>), read only.

   Its meta data (PHP class, file, provider, flags), the settings columns of a class attribute it reads, its
   templates in the administration and the default site, the classes and class attributes that use it with their
   objects, the content using it (paged, sortable, by class), the tables its code names and its help page.

   Variables (kernel/private/classes/views/class/datatype.php): datatype (a row of expDataTypeInventory::build()),
   datatype_source (settings, content, tables), datatype_tables, datatype_attributes, datatype_classes,
   datatype_templates, datatype_nodes (a page of expDataTypeInventory::nodeRows()), datatype_node_total,
   datatype_node_params, datatype_node_sort_links, datatype_node_size_links, datatype_node_page_uri, view_parameters,
   datatype_help_page, datatype_counted, datatype_current_access, module.
   The same file is in design/admin and design/admin4. Guide: doc/eng-us/guides/datatypes.md *}

{def $dt = $datatype}

<div class="context-block a4-page exp-classgroups exp-datatypes">

<div class="box-header"><div class="box-ml">
<div class="a4-title-row">
<h1 class="context-title"><code>{$dt.string|wash}</code>{if $dt.name|ne( '' )} {$dt.name|wash}{/if}</h1>
{switch match=$dt.status}
{case match='orphan'}<span class="a4-tag is-bad">{'Not registered'|i18n( 'design/admin/class/datatype' )}</span>{/case}
{case match='missing'}<span class="a4-tag is-warn">{'Listed, file missing'|i18n( 'design/admin/class/datatype' )}</span>{/case}
{case match='unused'}<span class="a4-tag">{'Not used'|i18n( 'design/admin/class/datatype' )}</span>{/case}
{case}<span class="a4-tag is-ok">{'In use'|i18n( 'design/admin/class/datatype' )}</span>{/case}
{/switch}
</div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<div class="a4-toolbar">
    <p class="a4-meta">{'Counted %time'|i18n( 'design/admin/class/datatype',, hash( '%time', $datatype_counted|l10n( shortdatetime ) ) )|wash}</p>
    <div class="a4-actions">
        <a class="a4-btn" href={'class/datatype/list'|ezurl}>{'All datatypes'|i18n( 'design/admin/class/datatype' )}</a>
        {if $datatype_help_page}<a class="a4-btn" href={concat( 'exphelp/viewer/', $datatype_help_page )|ezurl}>{'Help page'|i18n( 'design/admin/class/datatype' )}</a>{/if}
        <a class="a4-btn" href={concat( 'class/datatype/view/', $dt.string, '/(refresh)/1' )|ezurl}>{'Count again'|i18n( 'design/admin/class/datatype' )}</a>
    </div>
</div>

{if $dt.status|eq( 'orphan' )}
<div class="a4-notice is-bad" role="alert">
    <p class="a4-notice-title"><strong>{'This datatype is used but not registered'|i18n( 'design/admin/class/datatype' )}</strong></p>
    <p>{'The objects of the classes below cannot be shown, edited or published while no extension registers the datatype. Activate the extension that provides it and add it to content.ini [DataTypeSettings] AvailableDataTypes[], or change the classes.'|i18n( 'design/admin/class/datatype' )}</p>
</div>
{elseif $dt.status|eq( 'missing' )}
<div class="a4-notice is-warn" role="status">
    <p>{'content.ini lists this datatype, but no datatype directory has its file. Nothing uses it; remove it from AvailableDataTypes[] or activate its extension.'|i18n( 'design/admin/class/datatype' )}</p>
</div>
{/if}

<ul class="a4-figures">
    <li class="a4-figure"><strong>{$dt.classes}</strong><span>{'Classes'|i18n( 'design/admin/class/datatype' )}</span></li>
    <li class="a4-figure"><strong>{$dt.class_attributes}</strong><span>{'Class attributes'|i18n( 'design/admin/class/datatype' )}</span></li>
    <li class="a4-figure"><strong>{$dt.objects}</strong><span>{'Objects'|i18n( 'design/admin/class/datatype' )}</span></li>
    <li class="a4-figure"><strong>{$dt.object_attributes}</strong><span>{'Object attributes'|i18n( 'design/admin/class/datatype' )}</span></li>
</ul>

<section class="a4-card" aria-labelledby="datatype-meta-title">
<div class="a4-card-head"><h2 class="a4-h2" id="datatype-meta-title">{'About this datatype'|i18n( 'design/admin/class/datatype' )}</h2></div>
<div class="a4-card-body">
<dl class="a4-facts">
    <div><dt>{'Identifier'|i18n( 'design/admin/class/datatype' )}</dt><dd><code>{$dt.string|wash}</code></dd></div>
    <div><dt>{'Name'|i18n( 'design/admin/class/datatype' )}</dt><dd>{if $dt.name|ne( '' )}{$dt.name|wash}{else}<span class="a4-muted">{'none'|i18n( 'design/admin/class/datatype' )}</span>{/if}</dd></div>
    <div><dt>{'Provided by'|i18n( 'design/admin/class/datatype' )}</dt><dd>{if $dt.provider|eq( 'kernel' )}{'Kernel'|i18n( 'design/admin/class/datatype' )}{elseif $dt.provider|ne( '' )}{'Extension %name'|i18n( 'design/admin/class/datatype',, hash( '%name', $dt.provider ) )|wash}{else}<span class="a4-muted">{'nobody'|i18n( 'design/admin/class/datatype' )}</span>{/if}</dd></div>
    <div><dt>{'PHP class'|i18n( 'design/admin/class/datatype' )}</dt><dd>{if $dt.class|ne( '' )}<code>{$dt.class|wash}</code>{else}<span class="a4-muted">{'none'|i18n( 'design/admin/class/datatype' )}</span>{/if}</dd></div>
    <div class="a4-field-wide"><dt>{'File'|i18n( 'design/admin/class/datatype' )}</dt><dd>{if $dt.file|ne( '' )}<code>{$dt.file|wash}</code>{else}<span class="a4-muted">{'none'|i18n( 'design/admin/class/datatype' )}</span>{/if}</dd></div>
    <div><dt>{'In AvailableDataTypes[]'|i18n( 'design/admin/class/datatype' )}</dt><dd>{if $dt.listed}{'yes'|i18n( 'design/admin/class/datatype' )}{else}<strong class="a4-bad-text">{'no'|i18n( 'design/admin/class/datatype' )}</strong>{/if}</dd></div>
    <div class="a4-field-wide"><dt>{'Flags'|i18n( 'design/admin/class/datatype' )}</dt><dd>{include uri='design:class/datatype_flags.tpl' flags=$dt.flags}</dd></div>
    {if $datatype_source.content}<div class="a4-field-wide"><dt>{'Columns of its value'|i18n( 'design/admin/class/datatype' )}</dt><dd>{foreach $datatype_source.content as $column}<code class="a4-kbd">{$column|wash}</code> {/foreach}</dd></div>{/if}
    {if $datatype_source.settings}<div class="a4-field-wide"><dt>{'Columns of its class attribute settings'|i18n( 'design/admin/class/datatype' )}</dt><dd>{foreach $datatype_source.settings as $column}<code class="a4-kbd">{$column|wash}</code> {/foreach}</dd></div>{/if}
    <div class="a4-field-wide"><dt>{'Tables'|i18n( 'design/admin/class/datatype' )}</dt><dd><code class="a4-kbd">ezcontentclass_attribute</code> <code class="a4-kbd">ezcontentobject_attribute</code> {foreach $datatype_tables as $table}<code class="a4-kbd">{$table|wash}</code> {/foreach}</dd></div>
</dl>
<p class="a4-help">{'The columns and tables are found by reading the code of the datatype: a guide, not a promise.'|i18n( 'design/admin/class/datatype' )}</p>
</div>
</section>

<section class="a4-section" aria-labelledby="datatype-templates-title">
<div class="a4-section-head"><h2 class="a4-h2" id="datatype-templates-title">{'Templates'|i18n( 'design/admin/class/datatype' )}</h2></div>
<div class="a4-table-wrap">
<table class="a4-table">
<thead><tr>
    <th scope="col">{'Siteaccess'|i18n( 'design/admin/class/datatype' )}</th>
    <th scope="col">{'Template'|i18n( 'design/admin/class/datatype' )}</th>
    <th scope="col">{'Found in'|i18n( 'design/admin/class/datatype' )}</th>
</tr></thead>
<tbody>
{foreach $datatype_templates as $set}
{foreach $set.templates as $kind => $template}
<tr>
    <td>{if $set.siteaccess|eq( '' )}{$datatype_current_access|wash} <span class="a4-meta">{'this administration'|i18n( 'design/admin/class/datatype' )}</span>{else}{$set.siteaccess|wash} <span class="a4-meta">{'default site'|i18n( 'design/admin/class/datatype' )}</span>{/if}</td>
    <td><code>{$template.template|wash}</code></td>
    <td>{if $template.file}<code>{$template.file|wash}</code>{else}<span class="a4-muted">{'none'|i18n( 'design/admin/class/datatype' )}</span>{/if}</td>
</tr>
{/foreach}
{/foreach}
</tbody>
</table>
</div>
<p class="a4-help">{'Without overrides: an override template matched by class or attribute wins over these.'|i18n( 'design/admin/class/datatype' )}</p>
</section>

<section class="a4-section" aria-labelledby="datatype-classes-title">
<div class="a4-section-head"><h2 class="a4-h2" id="datatype-classes-title">{'Classes using it (%count)'|i18n( 'design/admin/class/datatype',, hash( '%count', $datatype_classes|count ) )|wash}</h2></div>
{if $datatype_classes|count|eq( 0 )}
<p class="a4-empty">{'No class has an attribute of this datatype.'|i18n( 'design/admin/class/datatype' )}</p>
{else}
<div class="a4-table-wrap">
<table class="a4-table" data-datatype-sort="1">
<thead><tr>
    <th scope="col" data-sort="text">{'Class'|i18n( 'design/admin/class/datatype' )}</th>
    <th scope="col" data-sort="text">{'Identifier'|i18n( 'design/admin/class/datatype' )}</th>
    <th scope="col" class="a4-num" data-sort="number">{'Attributes'|i18n( 'design/admin/class/datatype' )}</th>
    <th scope="col" class="a4-num" data-sort="number">{'Objects'|i18n( 'design/admin/class/datatype' )}</th>
    <th scope="col" class="a4-num" data-sort="number">{'Object attributes'|i18n( 'design/admin/class/datatype' )}</th>
</tr></thead>
<tbody>
{foreach $datatype_classes as $class}
<tr>
    <td data-value="{$class.name|wash}">{$class.identifier|class_icon( small, $class.name|wash )}&nbsp;<a href={concat( 'class/view/', $class.id )|ezurl}>{$class.name|wash}</a></td>
    <td data-value="{$class.identifier|wash}"><code>{$class.identifier|wash}</code></td>
    <td class="a4-num" data-value="{$class.attributes}">{$class.attributes}</td>
    <td class="a4-num" data-value="{$class.objects}">{$class.objects}</td>
    <td class="a4-num" data-value="{$class.object_attributes}">{$class.object_attributes}</td>
</tr>
{/foreach}
</tbody>
</table>
</div>
{/if}
</section>

{if $datatype_attributes|count}
<section class="a4-section" aria-labelledby="datatype-attributes-title">
<div class="a4-section-head"><h2 class="a4-h2" id="datatype-attributes-title">{'Class attributes (%count)'|i18n( 'design/admin/class/datatype',, hash( '%count', $datatype_attributes|count ) )|wash}</h2></div>
<div class="a4-table-wrap">
<table class="a4-table" data-datatype-sort="1">
<thead><tr>
    <th scope="col" data-sort="text">{'Attribute'|i18n( 'design/admin/class/datatype' )}</th>
    <th scope="col" data-sort="text">{'Class'|i18n( 'design/admin/class/datatype' )}</th>
    <th scope="col">{'Flags'|i18n( 'design/admin/class/datatype' )}</th>
    <th scope="col">{'Settings'|i18n( 'design/admin/class/datatype' )}</th>
    <th scope="col" class="a4-num" data-sort="number">{'Objects'|i18n( 'design/admin/class/datatype' )}</th>
    <th scope="col" class="a4-num" data-sort="number">{'Object attributes'|i18n( 'design/admin/class/datatype' )}</th>
</tr></thead>
<tbody>
{foreach $datatype_attributes as $attribute}
<tr>
    <td data-value="{$attribute.name|wash}">{$attribute.name|wash}<br /><code class="a4-meta">{$attribute.identifier|wash}</code> <span class="a4-meta">{'ID %id'|i18n( 'design/admin/class/datatype',, hash( '%id', $attribute.id ) )}</span></td>
    <td data-value="{$attribute.class_name|wash}"><a href={concat( 'class/view/', $attribute.class_id )|ezurl}>{$attribute.class_name|wash}</a></td>
    <td>
        {if $attribute.required}<span class="a4-tag">{'Required'|i18n( 'design/admin/class/datatype' )}</span>{/if}
        {if $attribute.searchable}<span class="a4-tag">{'Searchable'|i18n( 'design/admin/class/datatype' )}</span>{/if}
        {if $attribute.translatable}<span class="a4-tag">{'Translatable'|i18n( 'design/admin/class/datatype' )}</span>{/if}
        {if $attribute.information_collector}<span class="a4-tag">{'Collects information'|i18n( 'design/admin/class/datatype' )}</span>{/if}
    </td>
    <td>{if $attribute.settings}{foreach $attribute.settings as $column => $value}<code class="a4-kbd" title="{$value|wash}">{$column|wash}={$value|shorten( 40 )|wash}</code> {/foreach}{else}<span class="a4-muted">{'none'|i18n( 'design/admin/class/datatype' )}</span>{/if}</td>
    <td class="a4-num" data-value="{$attribute.objects}">{$attribute.objects}</td>
    <td class="a4-num" data-value="{$attribute.object_attributes}">{$attribute.object_attributes}</td>
</tr>
{/foreach}
</tbody>
</table>
</div>
</section>
{/if}

{if $datatype_classes|count}
{def $np = $datatype_node_params}
<section class="a4-section" aria-labelledby="datatype-nodes-title">
<div class="a4-section-head"><h2 class="a4-h2" id="datatype-nodes-title">{'Content (%count)'|i18n( 'design/admin/class/datatype',, hash( '%count', $datatype_node_total ) )|wash}</h2>
{if $datatype_node_total|gt( 0 )}<span class="a4-meta">{'%from to %to of %count'|i18n( 'design/admin/class/datatype',, hash( '%from', sum( $np.offset, 1 ), '%to', min( sum( $np.offset, $np.limit ), $datatype_node_total ), '%count', $datatype_node_total ) )}</span>{/if}</div>

{if $datatype_classes|count|gt( 1 )}
<form class="a4-filterbar" method="post" action={concat( 'class/datatype/view/', $dt.string )|ezurl} role="search" aria-label="{'Filter the list'|i18n( 'design/admin/class/datatype' )}">
    <input type="hidden" name="sort" value="{$np.sort|wash}" />
    <input type="hidden" name="order" value="{$np.order|wash}" />
    <input type="hidden" name="limit" value="{$np.limit|wash}" />
    <div class="a4-field">
        <label for="datatype-node-class">{'Class'|i18n( 'design/admin/class/datatype' )}</label>
        <select id="datatype-node-class" name="class">
            <option value="0">{'All classes'|i18n( 'design/admin/class/datatype' )}</option>
            {foreach $datatype_classes as $class}<option value="{$class.id}"{if eq( $class.id, $np.class )} selected="selected"{/if}>{$class.name|wash}</option>{/foreach}
        </select>
    </div>
    <div class="a4-field">
        <div class="a4-actions">
            <button type="submit" class="a4-btn a4-btn-primary" name="DataTypeNodeFilterButton" value="1">{'Show'|i18n( 'design/admin/class/datatype' )}</button>
        </div>
    </div>
</form>
{/if}

{if $datatype_nodes|count}
<div class="a4-table-wrap" role="region" aria-labelledby="datatype-nodes-title" tabindex="0">
<table class="a4-table">
<thead><tr>
    {foreach hash( 'name', 'Name'|i18n( 'design/admin/class/datatype' ), 'class', 'Class'|i18n( 'design/admin/class/datatype' ),
                   'section', 'Section'|i18n( 'design/admin/class/datatype' ), 'languages', 'Languages'|i18n( 'design/admin/class/datatype' ),
                   'owner', 'Owner'|i18n( 'design/admin/class/datatype' ), 'modified', 'Modified'|i18n( 'design/admin/class/datatype' ),
                   'published', 'Published'|i18n( 'design/admin/class/datatype' ) ) as $column => $text}
    {if is_set( $datatype_node_sort_links[$column] )}
    <th scope="col"{if $datatype_node_sort_links[$column].current} aria-sort="{if eq( $np.order, 'asc' )}ascending{else}descending{/if}"{/if}><a href={$datatype_node_sort_links[$column].url|ezurl}{if $datatype_node_sort_links[$column].current} title="{'Reverse the order'|i18n( 'design/admin/class/datatype' )}"{/if}>{$text|wash}{if $datatype_node_sort_links[$column].current} {if eq( $np.order, 'asc' )}&#8593;{else}&#8595;{/if}{/if}</a></th>
    {else}
    <th scope="col">{$text|wash}</th>
    {/if}
    {/foreach}
</tr></thead>
<tbody>
{foreach $datatype_nodes as $node}
<tr>
    <td><a href={$node.url_alias|ezurl}>{$node.name|wash}</a> <span class="a4-meta">{'Node %id'|i18n( 'design/admin/class/datatype',, hash( '%id', $node.node_id ) )}</span></td>
    <td><a href={concat( 'class/view/', $node.class_id )|ezurl}>{$node.class_name|wash}</a></td>
    <td>{if $node.section_name|ne( '' )}<a href={concat( 'section/view/', $node.section_id )|ezurl}>{$node.section_name|wash}</a>{else}{$node.section_id}{/if}</td>
    <td>{foreach $node.languages as $language}<code class="a4-kbd">{$language|wash}</code> {/foreach}</td>
    <td>{if $node.owner_node_id|gt( 0 )}<a href={concat( 'content/view/full/', $node.owner_node_id )|ezurl}>{$node.owner_name|wash}</a>{elseif $node.owner_name|ne( '' )}{$node.owner_name|wash}{else}<span class="a4-muted">{'none'|i18n( 'design/admin/class/datatype' )}</span>{/if}</td>
    <td class="a4-nowrap">{$node.modified|l10n( shortdatetime )}</td>
    <td class="a4-nowrap">{$node.published|l10n( shortdatetime )}</td>
</tr>
{/foreach}
</tbody>
</table>
</div>
{else}
<p class="a4-empty">{'No item'|i18n( 'design/admin/class/datatype' )}</p>
{/if}

<div class="a4-listfoot">
    <p class="a4-sizes">
        <span>{'Per page'|i18n( 'design/admin/class/datatype' )}:</span>
    {foreach $datatype_node_size_links as $size => $size_url}
        {if eq( $size, $np.limit )}<span class="current" aria-current="true">{$size}</span>{else}<a href={$size_url|ezurl} title="{'Show %count items per page.'|i18n( 'design/admin/class/datatype',, hash( '%count', $size ) )}">{$size}</a>{/if}
    {/foreach}
    </p>
    <div class="a4-pager">
    {include name=navigator
             uri='design:navigator/google.tpl'
             page_uri=$datatype_node_page_uri
             item_count=$datatype_node_total
             view_parameters=$view_parameters
             item_limit=$np.limit}
    </div>
</div>
</section>
{undef $np}
{/if}

</div></div></div>
</div>

<div hidden data-datatype-sort-text data-text-sort="{'Sort by %column'|i18n( 'design/admin/class/datatype' )|wash}"></div>
{ezscript( array( 'page/class_datatypes.js' ) )}
{undef $dt}
