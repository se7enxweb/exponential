{if is_set( $custom_root_node )}
    {def $root_node    = $custom_root_node
         $root_node_id = $root_node.node_id}
{elseif is_set( $custom_root_node_id )}
    {def $root_node_id = $custom_root_node_id
         $root_node    = fetch( 'content', 'node', hash( 'node_id', $root_node_id ) )}
{else}
    {def $root_node_id = ezini('TreeMenu','RootNodeID','contentstructuremenu.ini')
         $root_node    = fetch( 'content', 'node', hash( 'node_id', $root_node_id ) )}
{/if}
{if is_unset( $menu_persistence )}
    {def $menu_persistence = ezini('TreeMenu','MenuPersistence','contentstructuremenu.ini')|eq('enabled')}
{/if}
{if is_unset( $hide_node_list )}
    {def $hide_node_list = array()}
{/if}
{if and( is_set( $search_subtree_array[0] ), $search_subtree_array[0]|ne( '1' ) )}
    {def $search_node = fetch( 'content', 'node', hash( 'node_id', $search_subtree_array[0] ))}
    {if is_set( $search_node.path_array[1] )}
        {set $root_node_id = $search_node.path_array[1]
             $root_node    = fetch( 'content', 'node', hash( 'node_id', $root_node_id ) )}
    {/if}
    {undef $search_node}
{/if}
{def $user_class_group_id  = ezini('ClassGroupIDs', 'Users', 'content.ini')
     $setup_class_group_id = ezini('ClassGroupIDs', 'Setup', 'content.ini')
     $user_root_node_id    = ezini('NodeSettings', 'UserRootNode', 'content.ini')
}
{if $root_node_id|gt( 1 )}
    {def $filter_type          = cond( $root_node.path_array|contains( $user_root_node_id ), 'include', 'exclude')
         $filter_groups        = cond( $root_node.path_array|contains( $user_root_node_id ), array( $user_class_group_id ), array( $user_class_group_id, $setup_class_group_id ))}
{else}
    {def $filter_type          = 'exclude'
         $filter_groups        = array()}
{/if}

{def $click_action = ezini('TreeMenu','ItemClickAction','contentstructuremenu.ini')}
{if and( is_set( $csm_menu_item_click_action ), $click_action|not )}
    {set $click_action = $csm_menu_item_click_action}
{/if}

{if $click_action}
    {set $click_action = $click_action|ezurl(no)}
{/if}

{* The tree is drawn by contentstructuremenu_dynamic.js from the data of this element (no inline script, so it works
   under a Content Security Policy without 'unsafe-inline', doc/features/6.0/content-security-policy.md):
   data-tree-root the top node, data-tree-params the icons, classes, languages and texts (cached), data-tree-request
   what depends on the request and the user. Each is JSON. *}
<div class="content-tree-menu"{*
*}{cache-block keys=array( $root_node_id, $access_type ) expiry=0}{*
    *}{def $root_node_url = $root_node.url $class_list = array()}{*
    The top node (1) has no URL alias of its own: it opens its full view, which lists the top-level nodes.
    *}{if $root_node_url|eq('')}{set $root_node_url = concat( 'content/view/full/', $root_node_id )}{/if}{*
    *}{foreach fetch( 'content', 'can_instantiate_class_list', hash( 'parent_node', $root_node, 'filter_type', $filter_type, 'group_id', $filter_groups ) ) as $class}{*
        *}{set $class_list = $class_list|append( $class.id )}{*
    *}{/foreach} data-tree-root="{json_encode( hash(
        'node_id', $root_node_id,
        'object_id', cond( $root_node.object.id, $root_node.object.id, true(), 0 ),
        'class_id', $root_node.object.contentclass_id,
        'has_children', gt( $root_node.children_count, 0),
        'name', $root_node.name|wash,
        'url', $root_node_url|ezurl('no'),
        'modified_subnode', $root_node.modified_subnode,
        'languages', $root_node.object.language_codes,
        'class_list', $class_list
    ) )|wash}"{*
    *}{cache-block keys=array( $root_node_id|gt( 1 ), $access_type ) expiry=86400 ignore_content_expiry}{*
        *}{def $iconInfo       = icon_info('class')
               $classIconsSize = ezini('TreeMenu','ClassIconsSize','contentstructuremenu.ini')
               $tree_languages = hash()
               $tree_classes   = hash()}{*
        the content languages' names for "Edit in", as the other menus and pages show them
        *}{foreach fetch( 'content', 'prioritized_languages' ) as $language}{set $tree_languages = $tree_languages|merge( hash( $language.locale, $language.name ) )}{/foreach}{*
        Using an underscore to prefix the key because of merge not handling numeric keys correctly
        *}{foreach fetch('class','list') as $class}{set $tree_classes = $tree_classes|merge( hash( concat( '_', $class.id ), hash( 'name', $class.name, 'identifier', $class.identifier ) ) )}{/foreach} data-tree-params="{json_encode( hash(
        'contentTreeUrl', concat( "content/treemenu"|ezurl(no), '/' ),
        'wwwDirPrefix', concat( ezsys('wwwdir'), '/', $iconInfo.theme_path, '/', $iconInfo.size_path_list[$classIconsSize], '/' ),
        'icons', $iconInfo.icons,
        'defaultIcon', $iconInfo.default,
        'preloadIcons', ezini('TreeMenu','PreloadClassIcons','contentstructuremenu.ini')|eq('enabled'),
        'languages', $tree_languages,
        'classes', $tree_classes,
        'showTips', ezini('TreeMenu','ToolTips','contentstructuremenu.ini')|eq('enabled'),
        'createHereMenu', ezini('TreeMenu','CreateHereMenu','contentstructuremenu.ini'),
        'autoOpen', ezini('TreeMenu','AutoopenCurrentNode','contentstructuremenu.ini')|eq('enabled'),
        'i18n', hash(
            'expand', 'Click on the icon to display a context-sensitive menu.'|i18n('design/admin/contentstructuremenu'),
            'node_id', 'Node ID'|i18n('design/admin/contentstructuremenu')|wash(xhtml),
            'object_id', 'Object ID'|i18n('design/admin/contentstructuremenu')|wash(xhtml),
            'visibility', 'Visibility'|i18n('design/admin/contentstructuremenu')|wash(xhtml),
            'hidden', 'Hidden'|i18n('design/admin/contentstructuremenu')|wash(xhtml),
            'visible', 'Visible'|i18n('design/admin/contentstructuremenu')|wash(xhtml),
            'hiddenbyparent', 'Hidden by superior'|i18n('design/admin/contentstructuremenu')|wash(xhtml),
            'account_disabled', 'Account disabled'|i18n('design/admin/contentstructuremenu')|wash(xhtml),
            'disabled', 'Dynamic tree menu is disabled for this siteaccess!'|i18n("design/admin/contentstructuremenu"),
            'not_exist', 'Node does not exist'|i18n("design/admin/contentstructuremenu"),
            'internal_error', 'Internal error'|i18n("design/admin/contentstructuremenu"),
            'subitems', 'Items below %name'|i18n("design/admin/contentstructuremenu"),
            'actions', 'Actions for %name'|i18n("design/admin/contentstructuremenu"),
            'tree', 'Content tree'|i18n("design/admin/contentstructuremenu")
        )
    ) )|wash}"{*
        *}{undef $iconInfo $classIconsSize $tree_languages $tree_classes}{*
    *}{/cache-block}{*
*}{/cache-block}{*
   Uncached (per request / user)
*}{def $tree_path = array()}{*
*}{if is_set($module_result.path[0].node_id)}{foreach $module_result.path as $element}{set $tree_path = $tree_path|append( concat( '', first_set( $element.node_id, '' ) ) )}{/foreach}{/if}{*
*}{default current_user=fetch('user','current_user')} data-tree-request="{json_encode( hash(
    'action', $click_action,
    'context', $ui_context,
    'hideNodes', $hide_node_list,
    'maxDepth', ezini('TreeMenu','MaxDepth','contentstructuremenu.ini')|int,
    'expiry', concat( '', fetch('content','content_tree_menu_expiry') ),
    'useCookie', $menu_persistence,
    'path', $tree_path,
    'perm', concat($current_user.role_id_list|implode(','),'|',$current_user.limited_assignment_value_list|implode(','))|md5
) )|wash}"{/default}{undef $tree_path}>
<ul id="content_tree_menu"></ul>
</div>
