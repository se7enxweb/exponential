{* One object found by the content finder: what found it, its metadata, versions, locations, trash entries and the
   actions the user may take. Variables: result (expContentFinder::describe()), form_token, token_field,
   type_names. Everything in result is what the user may see; the actions are checked again when they are sent. *}
{def $status_names = hash( 'draft', 'Draft'|i18n( 'kernel/contentfinder' ),
                           'published', 'Published'|i18n( 'kernel/contentfinder' ),
                           'pending', 'Pending'|i18n( 'kernel/contentfinder' ),
                           'archived', 'Archived'|i18n( 'kernel/contentfinder' ),
                           'rejected', 'Rejected'|i18n( 'kernel/contentfinder' ),
                           'internal_draft', 'Internal draft'|i18n( 'kernel/contentfinder' ),
                           'repeat', 'Repeat'|i18n( 'kernel/contentfinder' ),
                           'queued', 'Queued'|i18n( 'kernel/contentfinder' ) )
     $action_url = 'contentfinder/action'|ezurl( 'no' )}
<section class="a4-card a4-contentfinder-result" id="cf-object-{$result.object_id}" aria-labelledby="cf-object-title-{$result.object_id}">
<h2 id="cf-object-title-{$result.object_id}">
    {if $result.main_node_id}<a href={concat( 'content/view/full/', $result.main_node_id )|ezurl}>{$result.name|wash}</a>{else}{$result.name|wash}{/if}
    <span class="a4-badge a4-contentfinder-badge {$result.status|wash}">{$status_names[$result.status]|wash}</span>
    {if $result.in_trash}<span class="a4-badge a4-contentfinder-badge trash">{'In the trash'|i18n( 'kernel/contentfinder' )}</span>{/if}
</h2>

{foreach $result.matches as $hit}
<div class="a4-contentfinder-match">
    <strong>{'Found by'|i18n( 'kernel/contentfinder' )}:</strong>
    {switch match=$hit.via}
    {case match='object'}{'object id %id'|i18n( 'kernel/contentfinder',, hash( '%id', $hit.value ) )}{/case}
    {case match='object_remote_id'}{'the remote id of the object'|i18n( 'kernel/contentfinder' )}{/case}
    {case match='node'}{'node id %id'|i18n( 'kernel/contentfinder',, hash( '%id', $hit.node_id ) )}{/case}
    {case match='node_remote_id'}{'the remote id of node %id'|i18n( 'kernel/contentfinder',, hash( '%id', $hit.node_id ) )}{/case}
    {case match='trash_node'}{'node %id, which is in the trash'|i18n( 'kernel/contentfinder',, hash( '%id', $hit.node_id ) )}{/case}
    {case match='urlalias'}{'the URL alias %alias (node %id)'|i18n( 'kernel/contentfinder',, hash( '%alias', $hit.value, '%id', $hit.node_id ) )|wash}{if $hit.prefix} {'with the path prefix %prefix of siteaccess %siteaccess'|i18n( 'kernel/contentfinder',, hash( '%prefix', $hit.prefix, '%siteaccess', $hit.siteaccess ) )|wash}{elseif $hit.siteaccess} {'as a page of siteaccess %siteaccess'|i18n( 'kernel/contentfinder',, hash( '%siteaccess', $hit.siteaccess ) )|wash}{/if}{/case}
    {case match='urlalias_part'}{'an alias containing “%part” (%alias, node %id)'|i18n( 'kernel/contentfinder',, hash( '%part', $hit.part, '%alias', $hit.value, '%id', $hit.node_id ) )|wash}{if $hit.history} {'(an old address, kept as history)'|i18n( 'kernel/contentfinder' )}{/if}{/case}
    {case match='urlalias_tail'}{'the last part of the URL alias %alias (node %id)'|i18n( 'kernel/contentfinder',, hash( '%alias', $hit.value, '%id', $hit.node_id ) )|wash}{if $hit.history} {'(an old address, kept as history)'|i18n( 'kernel/contentfinder' )}{/if}{/case}
    {case match='version'}{'version id %id (version %version)'|i18n( 'kernel/contentfinder',, hash( '%id', $hit.value, '%version', $hit.version ) )}{/case}
    {case match='attribute'}{'attribute id %id'|i18n( 'kernel/contentfinder',, hash( '%id', $hit.value ) )}{/case}
    {case match='file'}{'the file path %path'|i18n( 'kernel/contentfinder',, hash( '%path', $hit.value ) )|wash}{/case}
    {case}{$hit.via|wash}{/case}
    {/switch}
    {if $hit.rows|count}
    <ul>
    {foreach $hit.rows as $row}
        <li>{'Attribute %id (%identifier, %datatype), version %version, %language'|i18n( 'kernel/contentfinder',, hash( '%id', $row.attribute_id, '%identifier', $row.identifier, '%datatype', $row.datatype, '%version', $row.version, '%language', $row.language ) )|wash}
        {foreach $row.files as $file}<br /><code>{$file|wash}</code>{/foreach}</li>
    {/foreach}
    </ul>
    {/if}
</div>
{/foreach}

<dl class="a4-contentfinder-meta">
    <dt>{'Object id'|i18n( 'kernel/contentfinder' )}</dt><dd>{$result.object_id}</dd>
    <dt>{'Class'|i18n( 'kernel/contentfinder' )}</dt><dd>{$result.class_name|wash} (<code>{$result.class_identifier|wash}</code>)</dd>
    <dt>{'Owner'|i18n( 'kernel/contentfinder' )}</dt><dd>{$result.owner_name|wash}</dd>
    <dt>{'Section'|i18n( 'kernel/contentfinder' )}</dt><dd>{$result.section_name|wash}</dd>
    <dt>{'Languages'|i18n( 'kernel/contentfinder' )}</dt><dd>{$result.languages|implode( ', ' )|wash} ({'initial: %language'|i18n( 'kernel/contentfinder',, hash( '%language', $result.initial_language ) )|wash})</dd>
    {if $result.states|count}<dt>{'States'|i18n( 'kernel/contentfinder' )}</dt><dd>{$result.states|implode( ', ' )|wash}</dd>{/if}
    <dt>{'Remote id'|i18n( 'kernel/contentfinder' )}</dt><dd><code>{$result.remote_id|wash}</code></dd>
    <dt>{'Created'|i18n( 'kernel/contentfinder' )}</dt><dd>{if $result.published}{$result.published|l10n( 'shortdatetime' )}{else}-{/if}</dd>
    <dt>{'Modified'|i18n( 'kernel/contentfinder' )}</dt><dd>{if $result.modified}{$result.modified|l10n( 'shortdatetime' )}{else}-{/if}</dd>
    <dt>{'Current version'|i18n( 'kernel/contentfinder' )}</dt><dd>{$result.current_version}</dd>
</dl>

<h3>{'Locations'|i18n( 'kernel/contentfinder' )}</h3>
{if $result.locations|count}
<div class="a4-contentfinder-table"><table class="list" cellspacing="0">
<tr><th scope="col">{'Node id'|i18n( 'kernel/contentfinder' )}</th><th scope="col">{'Path'|i18n( 'kernel/contentfinder' )}</th><th scope="col">{'URL alias'|i18n( 'kernel/contentfinder' )}</th><th scope="col">{'State'|i18n( 'kernel/contentfinder' )}</th></tr>
{foreach $result.locations as $location sequence array( 'bglight', 'bgdark' ) as $style}
<tr class="{$style}">
    <td data-label="{'Node id'|i18n( 'kernel/contentfinder' )|wash}"><a href={concat( 'content/view/full/', $location.node_id )|ezurl}>{$location.node_id}</a></td>
    <td data-label="{'Path'|i18n( 'kernel/contentfinder' )|wash}"><code>{$location.path|wash}</code></td>
    <td data-label="{'URL alias'|i18n( 'kernel/contentfinder' )|wash}"><code>{$location.url_alias|wash}</code></td>
    <td data-label="{'State'|i18n( 'kernel/contentfinder' )|wash}">{if $location.is_main}<span class="a4-badge a4-contentfinder-badge main">{'Main'|i18n( 'kernel/contentfinder' )}</span> {/if}{if $location.is_hidden}<span class="a4-badge a4-contentfinder-badge">{'Hidden'|i18n( 'kernel/contentfinder' )}</span> {elseif $location.is_invisible}<span class="a4-badge a4-contentfinder-badge">{'Hidden by its parent'|i18n( 'kernel/contentfinder' )}</span> {/if}</td>
</tr>
{/foreach}
</table></div>
{else}
<p>{if $result.in_trash}{'None: the object is in the trash.'|i18n( 'kernel/contentfinder' )}{else}{'No location you may read.'|i18n( 'kernel/contentfinder' )}{/if}</p>
{/if}

<h3>{'Versions'|i18n( 'kernel/contentfinder' )}</h3>
{if $result.versions|count}
<div class="a4-contentfinder-table"><table class="list" cellspacing="0">
<tr><th scope="col">{'Version'|i18n( 'kernel/contentfinder' )}</th><th scope="col">{'Version id'|i18n( 'kernel/contentfinder' )}</th><th scope="col">{'Status'|i18n( 'kernel/contentfinder' )}</th><th scope="col">{'Language'|i18n( 'kernel/contentfinder' )}</th><th scope="col">{'Creator'|i18n( 'kernel/contentfinder' )}</th><th scope="col">{'Modified'|i18n( 'kernel/contentfinder' )}</th><th scope="col"></th></tr>
{foreach $result.versions as $version sequence array( 'bglight', 'bgdark' ) as $style}
<tr class="{$style}">
    <td data-label="{'Version'|i18n( 'kernel/contentfinder' )|wash}"><a href={concat( 'content/versionview/', $result.object_id, '/', $version.version, '/', $version.language )|ezurl}>{$version.version}</a></td>
    <td data-label="{'Version id'|i18n( 'kernel/contentfinder' )|wash}">{$version.id}</td>
    <td data-label="{'Status'|i18n( 'kernel/contentfinder' )|wash}"><span class="a4-badge a4-contentfinder-badge {$version.status_name|wash}">{$status_names[$version.status_name]|wash}</span></td>
    <td data-label="{'Language'|i18n( 'kernel/contentfinder' )|wash}">{$version.languages|implode( ', ' )|wash}</td>
    <td data-label="{'Creator'|i18n( 'kernel/contentfinder' )|wash}">{$version.creator_name|wash}</td>
    <td data-label="{'Modified'|i18n( 'kernel/contentfinder' )|wash}">{$version.modified|l10n( 'shortdatetime' )}</td>
    <td>{if and( $result.actions.remove_version, $version.can_remove )}
        <form method="post" action="{$action_url|wash}" class="a4-contentfinder-inline">
            <input type="hidden" name="{$token_field|wash}" value="{$form_token|wash}" />
            <input type="hidden" name="Action" value="removeversion" />
            <input type="hidden" name="ObjectID" value="{$result.object_id}" />
            <input type="hidden" name="VersionID" value="{$version.id}" />
            <input type="submit" class="button" value="{'Remove version'|i18n( 'kernel/contentfinder' )}" />
        </form>{/if}</td>
</tr>
{/foreach}
</table></div>
{else}
<p>{'No version you may read (content/versionread).'|i18n( 'kernel/contentfinder' )}</p>
{/if}

{if $result.trash|count}
<h3>{'In the trash'|i18n( 'kernel/contentfinder' )}</h3>
<div class="a4-contentfinder-table"><table class="list" cellspacing="0">
<tr><th scope="col">{'Node id'|i18n( 'kernel/contentfinder' )}</th><th scope="col">{'Original path'|i18n( 'kernel/contentfinder' )}</th><th scope="col">{'Trashed'|i18n( 'kernel/contentfinder' )}</th><th scope="col">{'Trashed by'|i18n( 'kernel/contentfinder' )}</th></tr>
{foreach $result.trash as $entry sequence array( 'bglight', 'bgdark' ) as $style}
<tr class="{$style}">
    <td data-label="{'Node id'|i18n( 'kernel/contentfinder' )|wash}">{$entry.node_id} ({'parent %id'|i18n( 'kernel/contentfinder',, hash( '%id', $entry.parent_node_id ) )})</td>
    <td data-label="{'Original path'|i18n( 'kernel/contentfinder' )|wash}"><code>{$entry.path|wash}</code></td>
    <td data-label="{'Trashed'|i18n( 'kernel/contentfinder' )|wash}">{if $entry.trashed}{$entry.trashed|l10n( 'shortdatetime' )}{else}-{/if}</td>
    <td data-label="{'Trashed by'|i18n( 'kernel/contentfinder' )|wash}">{if $entry.trashed_by_name}{$entry.trashed_by_name|wash}{else}-{/if}{if $entry.trashed_via} ({$entry.trashed_via|wash}){/if}</td>
</tr>
{/foreach}
</table></div>
{/if}

{if or( $result.actions.trash, $result.actions.delete, $result.actions.purge, $result.actions.restore )}
<div class="a4-contentfinder-actions">
    {foreach array( 'trash', 'delete', 'purge' ) as $action}
    {if $result.actions[$action]}
    <form method="post" action="{$action_url|wash}">
        <input type="hidden" name="{$token_field|wash}" value="{$form_token|wash}" />
        <input type="hidden" name="Action" value="{$action}" />
        <input type="hidden" name="ObjectID" value="{$result.object_id}" />
        <input type="submit" class="button{if ne( $action, 'trash' )} a4-contentfinder-danger{/if}" value="{switch match=$action}{case match='trash'}{'Move to trash'|i18n( 'kernel/contentfinder' )}{/case}{case match='delete'}{'Delete permanently'|i18n( 'kernel/contentfinder' )}{/case}{case}{'Purge from the trash'|i18n( 'kernel/contentfinder' )}{/case}{/switch}" />
    </form>
    {/if}
    {/foreach}
    {if $result.actions.restore}<form method="get" action={concat( 'content/restore/', $result.object_id )|ezurl}><input type="submit" class="button" value="{'Restore'|i18n( 'kernel/contentfinder' )}" /></form>{/if}
</div>
{/if}
</section>
{undef $status_names $action_url}
