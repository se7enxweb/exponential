{def $section         = fetch( 'section', 'object', hash( 'section_id', $node.object.section_id ) )
    $visible_columns  = ezini('SubItems', 'VisibleColumns', 'admininterface.ini')
    $locales          = fetch( 'content', 'translation_list' ) }

{literal}
<script type="text/javascript">
(function() {
{/literal}

{* The names of the "Edit in" menu: the content languages' names, as the row menus, the history and the drafts
   show them, not the locale's international name *}
var availableLanguages = {ldelim}{*
    *}{foreach fetch( 'content', 'prioritized_languages' ) as $language}{*
        *}'{$language.locale|wash(javascript)}': '{$language.name|wash(javascript)}'{*
        *}{delimiter},{/delimiter}{*
    *}{/foreach}{*
*}{rdelim};

{* The short code of each language for the Translations column (DE, EN: the language, never the region; the name tells eng-GB and eng-US apart). *}
var languageBadges = {ldelim}{*
    *}{foreach $locales as $locale}{*
        *}'{$locale.locale_code|wash(javascript)}': '{include uri='design:parts/language_badge.tpl' locale=$locale.locale_code badge_plain=true()}'{*
        *}{delimiter},{/delimiter}{*
    *}{/foreach}{*
*}{rdelim};

var vcols = {ldelim}

{foreach $visible_columns as $index => $object}
    {$index} : '{$object}'.split(';'){delimiter},

{/delimiter}
{/foreach}

{rdelim};

var confObj = {ldelim}


{switch match=$node.sort_field}
{case match='2'}    sortKey: "published_date",{/case}
{case match='3'}    sortKey: "modified_date",{/case}
{case match='4'}    sortKey: "section",{/case}
{case match='7'}    sortKey: "class_name",{/case}
{case match='8'}    sortKey: "priority",{/case}
{case match='9'}    sortKey: "name",{/case}
{case}    sortKey: "published_date",{/case}
{/switch}

    dataSourceURL: "{concat('ezjscore/call/ezjscnode::subtree::', $node.node_id)|ezurl('no')}",
    nodeID: {$node.node_id|int()},
    {* Reordering the rows by drag and drop (subitems_priority_drag.js): only where the children are sorted by
       priority and the user may change the priorities - the condition of the classic priority fields
       (children.tpl $priority). parentObjectID: the object whose cache is cleared, as content/action does. *}
    priorityDrag: {if and( eq( $node.sort_array[0][0], 'priority' ), $node.can_edit )}true{else}false{/if},
    parentObjectID: {$node.contentobject_id|int()},
    {* The subitems column registry: expsubitems::columns / ::rows / ::savepreference, and the CSV export.
       Without the server functions the table stays the classic one. *}
    subitemsServer: {if ezini_hasvariable( 'ezjscServer_expsubitems', 'Class', 'ezjscore.ini' )}true{else}false{/if},
    exportURL: "{concat('content/subitemsexport/', $node.node_id)|ezurl('no')}",
    editPrefixURL: {'/content/edit/'|ezurl},
    rowsPrPage: {$number_of_items},
    sortOrder: {$node.sort_order},
    nameFilter: '{$view_parameters.namefilter|wash(javascript)}',
    defaultShownColumns: vcols,
    navigationPart: "{$section.navigation_part_identifier}",
    cookieName: "eZSubitemColumns",
    cookieSecure: false,
    cookieDomain: "{ezsys(hostname)}",
    languages: availableLanguages,
    classesString: {$node.classes_js_array},
    languageBadges: languageBadges,
    editInLanguage: "{'Edit in <%language_name>.'|i18n( 'design/admin/node/view/full',, hash( '%language_name', '%language_name' ) )|wash('javascript')}",
    {* The state of each badge in the Translations column (main, present, missing) as its title and accessible name.
       The missing languages only on a site with up to 6 languages (expContentLanguageStates::LIST_MISSING_LIMIT):
       with more, a row's badges would wrap and bury the ones it has; the Missing translations column lists them. *}
    languageStates: {ldelim}
        main: "{'%language – main language'|i18n( 'design/admin/node/view/full',, hash( '%language', '%language' ) )|wash('javascript')}",
        present: "{'%language – translation exists'|i18n( 'design/admin/node/view/full',, hash( '%language', '%language' ) )|wash('javascript')}",
        missing: "{'%language – missing'|i18n( 'design/admin/node/view/full',, hash( '%language', '%language' ) )|wash('javascript')}"
    {rdelim},
    showMissingLanguages: {if le( fetch( 'content', 'prioritized_languages' )|count, 6 )}true{else}false{/if}

{rdelim}


{* Where "Edit selected" sends the selection. Built with ezurl so it carries
   the siteaccess and index file this installation actually uses, rather than
   a path hardcoded in the javascript. *}
{* On window: this script runs inside a function, and a plain var here was
   that function's own, so the datatable script never saw it and sent the
   selection to a bare /content/multiedit - the public siteaccess. *}
window.eZExpMultiEditURL = "{'content/multiedit'|ezurl('no')}";
var labelsObj = {ldelim}


    DATA_TABLE: {ldelim}

                        msg_loading: "{'Loading ...'|i18n( 'design/admin/node/view/full' )|wash('javascript')}"
                    {rdelim},

    DATA_TABLE_COLS: {ldelim}

                        thumbnail: "{'Thumbnail'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        name: "{'Name'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        visibility: "{'Visibility'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        type: "{'Type'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        modifier: "{'Modifier'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        modified: "{'Modified'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        published: "{'Published'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        section: "{'Section'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        translations: "{'Translations'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        priority: "{'Priority'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        nodeid: "{'Node ID'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        objectid: "{'Object ID'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        noderemoteid: "{'Node remote ID'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        objectremoteid: "{'Object remote ID'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        objectstate: "{'Object state'|i18n( 'design/admin/node/view/full' )|wash('javascript')}"
                    {rdelim},

    {* The status marks before each name: a node's visibility, a user account's state (components.css .a4-mark) *}
    STATUS: {ldelim}

                        visible: "{'Visible'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        hidden: "{'Hidden'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        hidden_by_parent: "{'Hidden by a parent'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        visible_title: "{'Visible: visitors of the site can see it.'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        hidden_title: "{'Hidden: visitors of the site do not see it.'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        hidden_by_parent_title: "{'An item above it is hidden, so visitors of the site do not see it.'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        disabled: "{'Disabled'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        disabled_title: "{'Account disabled: this user cannot log in.'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        locked: "{'Locked'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        locked_title: "{'Locked after too many failed logins.'|i18n( 'design/admin/node/view/full' )|wash('javascript')}"
                    {rdelim},

    TABLE_OPTIONS: {ldelim}

                        header: "{'Table options'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        header_noipp: "{'Number of items per page:'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        header_vtc: "{'Visible table columns:'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        button_close: "{'Close'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        custom_label: "{'Custom'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        custom_placeholder: "{'Enter number'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        custom_invalid: "{'Please enter a valid number between 1 and 10000'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        filter_label: "{'Find a column:'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        filter_placeholder: "{'Column name'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        filter_none: "{'No columns match.'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        group_other: "{'Other'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        order_legend: "{'Order of the visible columns:'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        order_hint: "{'Drag a column to move it, or use its arrow buttons.'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        order_up: "{'Move %name up'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        order_down: "{'Move %name down'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        presets_legend: "{'Column presets:'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        presets_choose: "{'Preset'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        presets_none: "{'None'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        presets_name: "{'Name of the new preset'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        presets_save_as: "{'Save current as...'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        presets_delete: "{'Delete preset'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        export_csv: "{'Export CSV'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        export_csv_title: "{'Download the items with the visible columns as a CSV file'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        copy_title: "{'Click to copy'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        copied: "{'Copied'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        copy_failed: "{'Not copied'|i18n( 'design/admin/node/view/full' )|wash('javascript')}"
                   {rdelim},

    {* Reordering the rows by drag and drop (subitems_priority_drag.js); %name, %position, %count and %error are
       filled in by the script *}
    DRAG: {ldelim}

                        column: "{'Order'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        grip: "{'Move %name'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        grip_title: "{'Drag to change the order'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        hint: "{'Drag the grip to change the order. With the keyboard: Space picks the row up, the arrow keys move it, Space drops it, Escape cancels. Alt+Up and Alt+Down move it by one place at once.'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        picked: "{'Picked up %name. Position %position of %count.'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        position: "{'Position %position of %count'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        moved: "{'Moved to position %position of %count'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        dropped_same: "{'Dropped. The order is unchanged.'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        cancelled: "{'Move cancelled. %name is back at position %position of %count.'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        saved: "{'Order saved'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        undo: "{'Undo'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        restored: "{'Previous order restored'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        error: "{'The order was not saved: %error'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        undo_error: "{'The previous order was not restored: %error'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        error_unknown: "{'unknown error'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        conflict: "{'Someone else changed the order in the meantime. Nothing was saved; the list shows the current order now.'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        unsaved_edit: "{'Save the priority you are editing (Enter) or cancel it (Escape) before you move a row.'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        busy: "{'Please wait until the order is saved.'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        wait_edit: "{'Please wait until the priority is saved.'|i18n( 'design/admin/node/view/full' )|wash('javascript')}"
                   {rdelim},

    ACTION_BUTTONS: {ldelim}

                        select: "{'Select'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        select_sav: "{'Select all visible'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        select_sn: "{'Select none'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        select_inv: "{'Invert selection'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        create_new: "{'Create new'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        more_actions: "{'More actions'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        more_actions_rs: "{'Remove selected'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        more_actions_ms: "{'Move selected'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        more_actions_cp: "{'Copy selected'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        more_actions_hs: "{'Hide selected'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        more_actions_us: "{'Unhide selected'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        create_multiple: "{'Create multiple new'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        more_actions_me: "{'Edit selected'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        more_actions_al: "{'Add a location'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        more_actions_no: "{'Use the checkboxes to select one or more items.'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        table_options: "{'Table options'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        first_page: "&laquo;&nbsp;{'first'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        previous_page: "&lsaquo;&nbsp;{'prev'|i18n( 'design/admin/node/view/full' )|wash('javascript')}",
                        next_page: "{'next'|i18n( 'design/admin/node/view/full' )|wash('javascript')}&nbsp;&rsaquo;",
                        last_page: "{'last'|i18n( 'design/admin/node/view/full' )|wash('javascript')}&nbsp;&raquo;"
                    {rdelim}


{rdelim};

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

    var createGroups = [

    {foreach $can_create_classes as $group}
        "{$group.group_name|wash(javascript)}"
        {delimiter}
        ,
        {/delimiter}
    {/foreach}

    ];

    var createOptions = [

    {foreach $can_create_classes as $group}
        [
        {foreach $group.items as $can_create_class}
            {if $can_create_class.can_instantiate_languages}
            {ldelim} text: "{$can_create_class.name|wash()}", value: {$can_create_class.id} {rdelim}

            {/if}
            {delimiter}{if $can_create_class.can_instantiate_languages},{/if}{/delimiter}
        {/foreach}
        ]
        {delimiter}
        ,
        {/delimiter}
    {/foreach}
    ];

{else}
    var createGroups = [];
    var createOptions = [];
{/if}

{literal}
// The sub-items table: Exponential UI's exp::datatable (ezajaxsubitems_expdatatable.js)
if ( window.Exp && Exp.$ && Exp.$.fn.expDataTable && window.eZAjaxSubitemsExpDataTable ) {
    Exp.ready( function() {
        eZAjaxSubitemsExpDataTable.init(confObj, labelsObj, createGroups, createOptions);
    } );
}

})();
{/literal}
{undef $section $visible_columns $locales}
</script>

<div id="action-controls-container">
    <div id="action-controls"></div>
    <div id="tpg"></div>
</div>
<div id="content-sub-items-list" class="content-navigation-childlist"></div>
<div id="bpg"></div>

<div id="to-dialog-container"></div>

{if ezini( 'ExtensionSettings', 'ActiveExtensions' )|contains( 'expui' )}
    {* the texts Exponential UI's table shows itself (page links, sorting, editing), translated *}
    {exp_config( hash( 'strings', array( 'Loading...', 'No records found.', 'Data error.', 'Click to sort ascending', 'Click to sort descending',
                                         'Pages', 'Page %page', 'Page %page of %pages', 'First page', 'Previous page', 'Next page', 'Last page',
                                         'Table actions', 'Select %name', 'Press Enter to edit', 'Sorted by %column, ascending', 'Sorted by %column, descending' ) ) )}
{/if}
