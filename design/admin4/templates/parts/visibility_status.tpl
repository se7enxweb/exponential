{* A node's visibility as a status mark (components.css .a4-status): a filled disc for visible, a ring for hidden,
   a ring with a bar for hidden because a node above it is hidden (is_invisible without is_hidden). The shape tells
   the state as well as the colour; the title explains it.

   Parameters (prefixed status_: an include sees the variables of the template that includes it):
     status_node    the node (required)
     status_badge   true: a badge with the mark and its name (the node view's header); otherwise the mark alone,
                    named for screen readers (role="img", aria-label) unless it is visible
     status_quiet   true: nothing for a visible node (lists that only point out what differs, such as the tree)

   The sub-items table and the dynamic content tree draw the same marks in their scripts
   (ezajaxsubitems_expdatatable.js, contentstructuremenu_dynamic.js).
*}{def $a4_vis_state = cond( $status_node.is_hidden, 'hidden', $status_node.is_invisible, 'hidden-by-parent', 'visible' )
     $a4_vis_name = ''
     $a4_vis_title = ''}{*
*}{switch match=$a4_vis_state}{*
    *}{case match='hidden'}{set $a4_vis_name = 'Hidden'|i18n( 'design/admin/node/view/full' )
                                $a4_vis_title = 'Hidden: visitors of the site do not see it.'|i18n( 'design/admin/node/view/full' )}{/case}{*
    *}{case match='hidden-by-parent'}{set $a4_vis_name = 'Hidden by a parent'|i18n( 'design/admin/node/view/full' )
                                          $a4_vis_title = 'An item above it is hidden, so visitors of the site do not see it.'|i18n( 'design/admin/node/view/full' )}{/case}{*
    *}{case}{set $a4_vis_name = 'Visible'|i18n( 'design/admin/node/view/full' )
                 $a4_vis_title = 'Visible: visitors of the site can see it.'|i18n( 'design/admin/node/view/full' )}{/case}{*
*}{/switch}{*
*}{if and( is_set( $status_quiet ), $status_quiet, eq( $a4_vis_state, 'visible' ) )}{*
*}{elseif and( is_set( $status_badge ), $status_badge )}<span class="a4-badge {if eq( $a4_vis_state, 'visible' )}is-ok{else}is-bad{/if} a4-visibility" title="{$a4_vis_title|wash}"><span class="a4-status is-{$a4_vis_state}" aria-hidden="true"></span>{$a4_vis_name|wash}</span>{*
*}{elseif eq( $a4_vis_state, 'visible' )}<span class="a4-status is-visible" title="{$a4_vis_title|wash}" aria-hidden="true"></span>{*
*}{else}<span class="a4-status is-{$a4_vis_state}" role="img" aria-label="{$a4_vis_name|wash}" title="{$a4_vis_title|wash}"></span>{/if}{*
*}{undef $a4_vis_state $a4_vis_name $a4_vis_title}
