<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">

<h2 class="context-title"><a href={$node.depth|gt(1)|choose( concat( 'content/view/full/', $node.parent_node_id )|ezurl, $node.parent.url_alias|ezurl )}><span class="a4-icon a4-bi-arrow-90deg-up is-medium" role="img" aria-label="{'Up one level'|i18n( 'design/admin/node/view/full' )|wash}" title="{'Up one level'|i18n( 'design/admin/node/view/full' )|wash}"></span></a> {'Sub items (%children_count)'|i18n( 'design/admin/node/view/full',, hash( '%children_count', $node.children_count ) )}</h2>



{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-content">

<div class="block">
    <p>{'The <%class_name> class is not configured to contain any sub items.'|i18n( 'design/admin/node/view/full',, hash( '%class_name', $node.class_name ) )|wash}</p>
</div>

{* DESIGN: Content END *}</div></div></div>

</div>
