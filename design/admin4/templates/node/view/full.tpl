{* admin4 node view: a header card (class icon with its class menu, title, badges, meta chips, translations), the
   actions (the same content/action form as before, with View on site, Preview, versions and the parent beside it),
   a strip of figures, the tabs (window_controls.tpl, untouched, so extensions' tabs keep working) and the
   children. Every id, class and form field the scripts and extensions use is kept: .content-view-full,
   .context-block, h1.context-title, #window-controls, .controlbar and its form, #content-view-children.
   The top node (node 1) has no content object: it gets a view of its own, see node/top_node_full.tpl. *}
{if eq( $node.contentobject_id, 0 )}
{include uri='design:node/top_node_full.tpl'}
{else}
{include uri='design:node/view/a4_full_body.tpl'}
{/if}
