{* The content step of the content object export wizard (package/create, creator ezcontentobject): the nodes and
   subtrees the package carries. Add node and Add subtree open the content browser (the shared browse page) and come
   back here with the choice; Remove selected takes the ticked rows out. The POST names are those of before:
   AddNode, AddSubtree, RemoveSelected, DeleteIDArray[], read by eZContentObjectPackageCreator.
   The same file is in design/admin and design/admin4. Guide: doc/features/6.0/package-wizard.md *}
{def $k_nodes = 'Selected nodes'|i18n( 'kernel/package' )
     $errs = hash()}
{foreach $error_list as $e}{set $errs = $errs|merge( hash( $e.field, $e.description ) )}{/foreach}
<div id="package" class="create">
<div id="sid-{$current_step.id|wash}" class="pc-{$creator.id|wash}">
<form name="objectlist" method="post" action={'package/create'|ezurl} class="a4-page a4-standalone a4-package-wizard">
<div class="context-block">
{include uri="design:package/create/wizard_head.tpl"}
<div class="box-bc"><div class="box-ml"><div class="box-content">
{include uri="design:package/create/wizard_progress.tpl"
         intro='Choose the content to export: a single node, or a node with everything below it (a subtree). The next step sets what comes with it.'|i18n( 'design/admin/package' )
         field_ids=hash( $k_nodes, 'a4-package-nodes' )}

<section class="a4-card is-flush a4-package-pick" id="a4-package-nodes" tabindex="-1" aria-labelledby="a4-package-nodes-title"{if is_set( $errs[$k_nodes] )} aria-describedby="a4-package-nodes-error"{/if}>
    <div class="a4-card-head">
        <h3 class="a4-h3" id="a4-package-nodes-title">{'Selected nodes'|i18n( 'design/admin/package' )}</h3>
        <p>{'%count chosen'|i18n( 'design/admin/package',, hash( '%count', $node_list|count ) )|wash}</p>
        <div class="a4-card-actions">
            <button class="a4-btn" type="submit" name="AddNode" value="1"><span class="a4-icon a4-bi-plus-lg is-small" aria-hidden="true"></span> {'Add node'|i18n( 'design/admin/package' )}</button>
            <button class="a4-btn" type="submit" name="AddSubtree" value="1"><span class="a4-icon a4-bi-diagram-3 is-small" aria-hidden="true"></span> {'Add subtree'|i18n( 'design/admin/package' )}</button>
        </div>
    </div>
    {if is_set( $errs[$k_nodes] )}<p class="a4-field-error a4-package-pick-error" id="a4-package-nodes-error">{$errs[$k_nodes]|wash}</p>{/if}
    {if $node_list|count}
    <div class="a4-table-wrap">
    <table class="a4-table a4-package-pick-table">
        <thead>
        <tr>
            <th scope="col" class="a4-package-pick-check"><span class="a4-sr-only">{'Select for removal'|i18n( 'design/admin/package' )}</span></th>
            <th scope="col">{'Node'|i18n( 'design/admin/package' )}</th>
            <th scope="col">{'Export type'|i18n( 'design/admin/package' )}</th>
        </tr>
        </thead>
        <tbody>
        {foreach $node_list as $item}
        {def $node = fetch( 'content', 'node', hash( 'node_id', $item.id ) )}
        <tr>
            <td class="a4-package-pick-check"><input type="checkbox" name="DeleteIDArray[]" id="a4-package-node-{$item.id|wash}" value="{$item.id|wash}" /></td>
            <td><label class="a4-inline-label" for="a4-package-node-{$item.id|wash}">{if $node}{$node.name|wash}{else}{$item.id|wash}{/if}</label>
                {if $node}<span class="a4-meta a4-package-node-path">{$node.class_name|wash} &middot; /{$node.url_alias|wash}</span>{/if}</td>
            <td>{if $item.type|eq( 'subtree' )}<span class="a4-tag">{'Subtree'|i18n( 'design/admin/package' )}</span>{else}<span class="a4-tag">{'Node'|i18n( 'design/admin/package' )}</span>{/if}</td>
        </tr>
        {undef $node}
        {/foreach}
        </tbody>
    </table>
    </div>
    <div class="a4-card-foot">
        <button class="a4-btn a4-btn-outline-danger" type="submit" name="RemoveSelected" value="1"><span class="a4-icon a4-bi-trash is-small" aria-hidden="true"></span> {'Remove selected'|i18n( 'design/admin/package' )}</button>
    </div>
    {else}
    <div class="a4-empty">
        <p>{'There are currently no objects selected for export'|i18n( 'design/admin/package' )}</p>
        <p class="a4-help">{'Add a node or a subtree to start.'|i18n( 'design/admin/package' )}</p>
    </div>
    {/if}
</section>

{include uri="design:package/create/wizard_actions.tpl"}
</div></div></div>
</div>
</form>
</div>
</div>
{undef $k_nodes $errs}
