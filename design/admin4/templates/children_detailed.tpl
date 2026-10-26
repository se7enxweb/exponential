{def $section         = fetch( 'section', 'object', hash( 'section_id', $node.object.section_id ) )
    $visible_columns  = ezini('SubItems', 'VisibleColumns', 'admininterface.ini')
    $locales          = fetch( 'content', 'translation_list' )
    $subitems_languages = hash()
    $subitems_badges  = hash()
    $subitems_badge   = ''
    $subitems_sort_key = 'published_date'
    $subitems_create_groups  = array()
    $subitems_create_options = array()
    $subitems_group_options  = array()}

{* The sub-items table's settings and texts: data attributes of #subitems-config, which page/children_detailed.js
   hands to the table script (ezajaxsubitems_expdatatable.js). No inline script, so the page runs under a Content
   Security Policy without 'unsafe-inline'. The values are JSON (json_encode), HTML-escaped for the attribute. *}

{* The names of the "Edit in" menu: the content languages' names, as the row menus, the history and the drafts
   show them, not the locale's international name *}
{foreach fetch( 'content', 'prioritized_languages' ) as $language}{set $subitems_languages = $subitems_languages|merge( hash( $language.locale, $language.name ) )}{/foreach}

{* The short code of each language for the Translations column (DE, EN: the language, never the region; the name tells eng-GB and eng-US apart). *}
{foreach $locales as $locale}{*
    *}{set-block variable=subitems_badge}{include uri='design:parts/language_badge.tpl' locale=$locale.locale_code badge_plain=true()}{/set-block}{*
    *}{set $subitems_badges = $subitems_badges|merge( hash( $locale.locale_code, $subitems_badge ) )}{*
*}{/foreach}

{switch match=$node.sort_field}
{case match='2'}{set $subitems_sort_key = 'published_date'}{/case}
{case match='3'}{set $subitems_sort_key = 'modified_date'}{/case}
{case match='4'}{set $subitems_sort_key = 'section'}{/case}
{case match='7'}{set $subitems_sort_key = 'class_name'}{/case}
{case match='8'}{set $subitems_sort_key = 'priority'}{/case}
{case match='9'}{set $subitems_sort_key = 'name'}{/case}
{case}{set $subitems_sort_key = 'published_date'}{/case}
{/switch}

{if and( $node.is_container,  $node.can_create)}
    {if $node.path_array|contains( ezini( 'NodeSettings', 'MediaRootNode', 'content.ini' ) )}
        {def $group_id = array( ezini( 'ClassGroupIDs', 'Users', 'content.ini' ),
                                ezini( 'ClassGroupIDs', 'Setup', 'content.ini' ) )}
    {elseif $node.path_array|contains( ezini( 'NodeSettings', 'UserRootNode', 'content.ini' ) )}
        {def $group_id = array( ezini( 'ClassGroupIDs', 'Setup', 'content.ini' ),
                                ezini( 'ClassGroupIDs', 'Content', 'content.ini' ),
                                ezini( 'ClassGroupIDs', 'Media', 'content.ini' ) )}
    {else}
        {def $group_id = false()}
    {/if}

    {def $can_create_classes = fetch( 'content', 'can_instantiate_class_list', hash( 'parent_node', $node,
                                                                                     'filter_type', 'exclude',
                                                                                     'group_id', $group_id,
                                                                                     'group_by_class_group', true() ) )}

    {* The class group of the tree the node is in comes first (Users under the users tree, Media under the media tree,
       Content elsewhere), the others follow in their order: under Users, User and User group were the last of some
       sixty classes of the other groups. *}
    {def $create_lead_group = cond( $node.path_array|contains( ezini( 'NodeSettings', 'UserRootNode', 'content.ini' ) ), ezini( 'ClassGroupIDs', 'Users', 'content.ini' ),
                                    $node.path_array|contains( ezini( 'NodeSettings', 'MediaRootNode', 'content.ini' ) ), ezini( 'ClassGroupIDs', 'Media', 'content.ini' ),
                                    ezini( 'ClassGroupIDs', 'Content', 'content.ini' ) )
         $create_ordered_groups = array()}
    {foreach $can_create_classes as $create_group_id => $create_group}
        {if eq( $create_group_id, $create_lead_group )}
            {set $create_ordered_groups = $create_ordered_groups|prepend( $create_group )}
        {else}
            {set $create_ordered_groups = $create_ordered_groups|append( $create_group )}
        {/if}
    {/foreach}
    {set $can_create_classes = $create_ordered_groups}
    {undef $create_lead_group $create_ordered_groups}

    {* The "Create new" menu: one group per class group, its classes (names HTML-escaped: the menu shows them as markup) *}
    {foreach $can_create_classes as $group}
        {set $subitems_create_groups = $subitems_create_groups|append( $group.group_name )}
        {set $subitems_group_options = array()}
        {foreach $group.items as $can_create_class}
            {if $can_create_class.can_instantiate_languages}
                {set $subitems_group_options = $subitems_group_options|append( hash( 'text', $can_create_class.name|wash(), 'value', $can_create_class.id|int() ) )}
            {/if}
        {/foreach}
        {set $subitems_create_options = $subitems_create_options|append( $subitems_group_options )}
    {/foreach}
{/if}

{* priorityDrag - reordering the rows by drag and drop (subitems_priority_drag.js): only where the children are
   sorted by priority and the user may change the priorities - the condition of the classic priority fields
   (children.tpl $priority). parentObjectID: the object whose cache is cleared, as content/action does.
   subitemsServer - the subitems column registry: expsubitems::columns / ::rows / ::savepreference, and the CSV
   export. Without the server functions the table stays the classic one.
   defaultShownColumns - the columns of each navigation part, separated by ; (split by the page script).
   classesString - the classes that may be created here (node.classes_js_array: JSON, or '' for none).
   languageStates - the state of each badge in the Translations column (main, present, missing) as its title and
   accessible name. The missing languages only on a site with up to 6 languages
   (expContentLanguageStates::LIST_MISSING_LIMIT): with more, a row's badges would wrap and bury the ones it has;
   the Missing translations column lists them. *}
{* data-no-table: the note shown in the place of the table when Exponential UI is not active or not loaded.
   data-multiedit-url: where "Edit selected" sends the selection. Built with ezurl so it carries the siteaccess and
   index file this installation actually uses, rather than a path hardcoded in the javascript. The page script sets
   it as window.eZExpMultiEditURL, where the datatable script looks for it; without it the selection went to a bare
   /content/multiedit - the public siteaccess. *}
{* The labels: STATUS - the status marks before each name: a node's visibility, a user account's state
   (components.css .a4-mark). DRAG - reordering the rows by drag and drop (subitems_priority_drag.js); %name,
   %position, %count and %error are filled in by the script. *}
<div id="subitems-config" hidden
     data-conf="{json_encode( hash(
        'sortKey', $subitems_sort_key,
        'dataSourceURL', concat('ezjscore/call/ezjscnode::subtree::', $node.node_id)|ezurl('no'),
        'nodeID', $node.node_id|int(),
        'priorityDrag', and( eq( $node.sort_array[0][0], 'priority' ), $node.can_edit ),
        'parentObjectID', $node.contentobject_id|int(),
        'subitemsServer', ezini_hasvariable( 'ezjscServer_expsubitems', 'Class', 'ezjscore.ini' ),
        'exportURL', concat('content/subitemsexport/', $node.node_id)|ezurl('no'),
        'editPrefixURL', '/content/edit/'|ezurl('no'),
        'rowsPrPage', $number_of_items|int(),
        'sortOrder', $node.sort_order|int(),
        'nameFilter', $view_parameters.namefilter,
        'defaultShownColumns', $visible_columns,
        'navigationPart', $section.navigation_part_identifier,
        'cookieName', 'eZSubitemColumns',
        'cookieSecure', false(),
        'cookieDomain', ezsys( 'hostname' ),
        'languages', $subitems_languages,
        'classesString', $node.classes_js_array,
        'languageBadges', $subitems_badges,
        'editInLanguage', 'Edit in <%language_name>.'|i18n( 'design/admin/node/view/full',, hash( '%language_name', '%language_name' ) ),
        'languageStates', hash(
            'main', '%language – main language'|i18n( 'design/admin/node/view/full',, hash( '%language', '%language' ) ),
            'present', '%language – translation exists'|i18n( 'design/admin/node/view/full',, hash( '%language', '%language' ) ),
            'missing', '%language – missing'|i18n( 'design/admin/node/view/full',, hash( '%language', '%language' ) )
        ),
        'showMissingLanguages', le( fetch( 'content', 'prioritized_languages' )|count, 6 )
     ) )|wash}"
     data-labels="{json_encode( hash(
        'DATA_TABLE', hash(
            'msg_loading', 'Loading ...'|i18n( 'design/admin/node/view/full' )
        ),
        'DATA_TABLE_COLS', hash(
            'thumbnail', 'Thumbnail'|i18n( 'design/admin/node/view/full' ),
            'name', 'Name'|i18n( 'design/admin/node/view/full' ),
            'visibility', 'Visibility'|i18n( 'design/admin/node/view/full' ),
            'type', 'Type'|i18n( 'design/admin/node/view/full' ),
            'modifier', 'Modifier'|i18n( 'design/admin/node/view/full' ),
            'modified', 'Modified'|i18n( 'design/admin/node/view/full' ),
            'published', 'Published'|i18n( 'design/admin/node/view/full' ),
            'section', 'Section'|i18n( 'design/admin/node/view/full' ),
            'translations', 'Translations'|i18n( 'design/admin/node/view/full' ),
            'priority', 'Priority'|i18n( 'design/admin/node/view/full' ),
            'nodeid', 'Node ID'|i18n( 'design/admin/node/view/full' ),
            'objectid', 'Object ID'|i18n( 'design/admin/node/view/full' ),
            'noderemoteid', 'Node remote ID'|i18n( 'design/admin/node/view/full' ),
            'objectremoteid', 'Object remote ID'|i18n( 'design/admin/node/view/full' ),
            'objectstate', 'Object state'|i18n( 'design/admin/node/view/full' )
        ),
        'STATUS', hash(
            'visible', 'Visible'|i18n( 'design/admin/node/view/full' ),
            'hidden', 'Hidden'|i18n( 'design/admin/node/view/full' ),
            'hidden_by_parent', 'Hidden by a parent'|i18n( 'design/admin/node/view/full' ),
            'visible_title', 'Visible: visitors of the site can see it.'|i18n( 'design/admin/node/view/full' ),
            'hidden_title', 'Hidden: visitors of the site do not see it.'|i18n( 'design/admin/node/view/full' ),
            'hidden_by_parent_title', 'An item above it is hidden, so visitors of the site do not see it.'|i18n( 'design/admin/node/view/full' ),
            'disabled', 'Disabled'|i18n( 'design/admin/node/view/full' ),
            'disabled_title', 'Account disabled: this user cannot log in.'|i18n( 'design/admin/node/view/full' ),
            'locked', 'Locked'|i18n( 'design/admin/node/view/full' ),
            'locked_title', 'Locked after too many failed logins.'|i18n( 'design/admin/node/view/full' )
        ),
        'TABLE_OPTIONS', hash(
            'header', 'Table options'|i18n( 'design/admin/node/view/full' ),
            'header_noipp', 'Number of items per page:'|i18n( 'design/admin/node/view/full' ),
            'header_vtc', 'Visible table columns:'|i18n( 'design/admin/node/view/full' ),
            'button_close', 'Close'|i18n( 'design/admin/node/view/full' ),
            'custom_label', 'Custom'|i18n( 'design/admin/node/view/full' ),
            'custom_placeholder', 'Enter number'|i18n( 'design/admin/node/view/full' ),
            'custom_invalid', 'Please enter a valid number between 1 and 10000'|i18n( 'design/admin/node/view/full' ),
            'filter_label', 'Find a column:'|i18n( 'design/admin/node/view/full' ),
            'filter_placeholder', 'Column name'|i18n( 'design/admin/node/view/full' ),
            'filter_none', 'No columns match.'|i18n( 'design/admin/node/view/full' ),
            'group_other', 'Other'|i18n( 'design/admin/node/view/full' ),
            'order_legend', 'Order of the visible columns:'|i18n( 'design/admin/node/view/full' ),
            'order_hint', 'Drag a column to move it, or use its arrow buttons.'|i18n( 'design/admin/node/view/full' ),
            'order_up', 'Move %name up'|i18n( 'design/admin/node/view/full' ),
            'order_down', 'Move %name down'|i18n( 'design/admin/node/view/full' ),
            'presets_legend', 'Column presets:'|i18n( 'design/admin/node/view/full' ),
            'presets_choose', 'Preset'|i18n( 'design/admin/node/view/full' ),
            'presets_none', 'None'|i18n( 'design/admin/node/view/full' ),
            'presets_name', 'Name of the new preset'|i18n( 'design/admin/node/view/full' ),
            'presets_save_as', 'Save current as...'|i18n( 'design/admin/node/view/full' ),
            'presets_delete', 'Delete preset'|i18n( 'design/admin/node/view/full' ),
            'export_csv', 'Export CSV'|i18n( 'design/admin/node/view/full' ),
            'export_csv_title', 'Download the items with the visible columns as a CSV file'|i18n( 'design/admin/node/view/full' ),
            'copy_title', 'Click to copy'|i18n( 'design/admin/node/view/full' ),
            'copied', 'Copied'|i18n( 'design/admin/node/view/full' ),
            'copy_failed', 'Not copied'|i18n( 'design/admin/node/view/full' ),
            'save_failed', 'Not saved: %error'|i18n( 'design/admin/node/view/full' )
        ),
        'DRAG', hash(
            'column', 'Order'|i18n( 'design/admin/node/view/full' ),
            'grip', 'Move %name'|i18n( 'design/admin/node/view/full' ),
            'grip_title', 'Drag to change the order'|i18n( 'design/admin/node/view/full' ),
            'hint', 'Drag the grip to change the order. With the keyboard: Space picks the row up, the arrow keys move it, Space drops it, Escape cancels. Alt+Up and Alt+Down move it by one place at once.'|i18n( 'design/admin/node/view/full' ),
            'picked', 'Picked up %name. Position %position of %count.'|i18n( 'design/admin/node/view/full' ),
            'position', 'Position %position of %count'|i18n( 'design/admin/node/view/full' ),
            'moved', 'Moved to position %position of %count'|i18n( 'design/admin/node/view/full' ),
            'dropped_same', 'Dropped. The order is unchanged.'|i18n( 'design/admin/node/view/full' ),
            'cancelled', 'Move canceled. %name is back at position %position of %count.'|i18n( 'design/admin/node/view/full' ),
            'saved', 'Order saved'|i18n( 'design/admin/node/view/full' ),
            'undo', 'Undo'|i18n( 'design/admin/node/view/full' ),
            'restored', 'Previous order restored'|i18n( 'design/admin/node/view/full' ),
            'error', 'The order was not saved: %error'|i18n( 'design/admin/node/view/full' ),
            'undo_error', 'The previous order was not restored: %error'|i18n( 'design/admin/node/view/full' ),
            'error_unknown', 'unknown error'|i18n( 'design/admin/node/view/full' ),
            'conflict', 'Someone else changed the order in the meantime. Nothing was saved; the list shows the current order now.'|i18n( 'design/admin/node/view/full' ),
            'unsaved_edit', 'Save the priority you are editing (Enter) or cancel it (Escape) before you move a row.'|i18n( 'design/admin/node/view/full' ),
            'busy', 'Please wait until the order is saved.'|i18n( 'design/admin/node/view/full' ),
            'wait_edit', 'Please wait until the priority is saved.'|i18n( 'design/admin/node/view/full' )
        ),
        'ACTION_BUTTONS', hash(
            'select', 'Select'|i18n( 'design/admin/node/view/full' ),
            'select_sav', 'Select all visible'|i18n( 'design/admin/node/view/full' ),
            'select_sn', 'Select none'|i18n( 'design/admin/node/view/full' ),
            'select_inv', 'Invert selection'|i18n( 'design/admin/node/view/full' ),
            'create_new', 'Create new'|i18n( 'design/admin/node/view/full' ),
            'more_actions', 'More actions'|i18n( 'design/admin/node/view/full' ),
            'more_actions_rs', 'Remove selected'|i18n( 'design/admin/node/view/full' ),
            'more_actions_ms', 'Move selected'|i18n( 'design/admin/node/view/full' ),
            'more_actions_cp', 'Copy selected'|i18n( 'design/admin/node/view/full' ),
            'more_actions_hs', 'Hide selected'|i18n( 'design/admin/node/view/full' ),
            'more_actions_us', 'Unhide selected'|i18n( 'design/admin/node/view/full' ),
            'create_multiple', 'Create multiple new'|i18n( 'design/admin/node/view/full' ),
            'more_actions_me', 'Edit selected'|i18n( 'design/admin/node/view/full' ),
            'more_actions_al', 'Add a location'|i18n( 'design/admin/node/view/full' ),
            'more_actions_no', 'Use the checkboxes to select one or more items.'|i18n( 'design/admin/node/view/full' ),
            'table_options', 'Table options'|i18n( 'design/admin/node/view/full' ),
            'first_page', concat( '&laquo;&nbsp;', 'first'|i18n( 'design/admin/node/view/full' ) ),
            'previous_page', concat( '&lsaquo;&nbsp;', 'prev'|i18n( 'design/admin/node/view/full' ) ),
            'next_page', concat( 'next'|i18n( 'design/admin/node/view/full' ), '&nbsp;&rsaquo;' ),
            'last_page', concat( 'last'|i18n( 'design/admin/node/view/full' ), '&nbsp;&raquo;' )
        )
     ) )|wash}"
     data-create-groups="{json_encode( $subitems_create_groups )|wash}"
     data-create-options="{json_encode( $subitems_create_options )|wash}"
     data-multiedit-url="{'content/multiedit'|ezurl('no')|wash}"
     data-no-table="{'The sub items cannot be listed: Exponential UI (the extension expui) is not active or not loaded on this siteaccess.'|i18n( 'design/admin/node/view/full' )|wash}"></div>
{* page/children_detailed.js starts the table on Exponential UI (Exp.ready, exp::datatable): requested here as well
   as in children.tpl, so the page script never stands without it *}
{ezscript_require( array( 'exp::core::shared', 'exp::io', 'exp::datatable' ) )}
{ezscript( array( 'page/children_detailed.js' ) )}
{undef $section $visible_columns $locales $subitems_languages $subitems_badges $subitems_badge $subitems_sort_key
       $subitems_create_groups $subitems_create_options $subitems_group_options}

<div id="action-controls-container">
    <div id="action-controls"></div>
    <div id="tpg"></div>
</div>
<div id="content-sub-items-list" class="content-navigation-childlist"></div>
{* The table's place while its rows load (admin4.css .a4-subitems-reserve): the toolbar and the header, one line per
   row of the first page (the rows per page, or the sub-items when there are fewer) and the pager, so the page does
   not move down when the script draws the table. Shown only while the list is empty or loading. *}
{def $subitems_reserve_rows = min( $children_count, $number_of_items )}
<div class="a4-subitems-reserve" aria-hidden="true"><span class="a4-subitems-reserve-head"></span>{if gt( $subitems_reserve_rows, 0 )}{for 1 to $subitems_reserve_rows as $subitems_reserve_row}<span></span>{/for}<span class="a4-subitems-reserve-pager"></span>{/if}</div>
{undef $subitems_reserve_rows}
<div id="bpg"></div>

<div id="to-dialog-container"></div>

{if ezini( 'ExtensionSettings', 'ActiveExtensions' )|contains( 'expui' )}
    {* the texts Exponential UI's table shows itself (page links, sorting, editing), translated *}
    {exp_config( hash( 'strings', array( 'Loading...', 'No records found.', 'Data error.', 'Click to sort ascending', 'Click to sort descending',
                                         'Pages', 'Page %page', 'Page %page of %pages', 'First page', 'Previous page', 'Next page', 'Last page',
                                         'Table actions', 'Select %name', 'Press Enter to edit', 'Sorted by %column, ascending', 'Sorted by %column, descending' ) ) )}
{/if}
