{* A help topic, manual or chapter in a list of the public site (doc/eng-us/features/6.0/help-content.md, "Help on the
   site's pages"): its title as a link and its short description, the one sentence every help object has.
   Variables: node. *}
<div class="exphelp-line">
    <a href={$node.url_alias|ezurl}>{$node.name|wash}</a>
    {if is_set( $node.data_map.shortdesc )}{if $node.data_map.shortdesc.has_content}<p class="exphelp-shortdesc">{$node.data_map.shortdesc.content|wash}</p>{/if}{/if}
</div>
