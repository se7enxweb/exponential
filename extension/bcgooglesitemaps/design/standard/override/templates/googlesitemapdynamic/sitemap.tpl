{* Get all needed values from bcgooglesitemapdynamic.ini *}
{if not( is_set( $node ) )}
    {def $node=fetch( 'content', 'node', hash( 'node_id', $start_node_id ) )}
{/if}
{def $depth=$node.depth|int()
     $class_filter=ezini( 'Classes', 'ClassFilterArray', 'bcgooglesitemapdynamic.ini' )
     $class_filter_type=ezini( 'Classes', 'ClassFilterType', 'bcgooglesitemapdynamic.ini' )
     $main_node_only=ezini( 'NodeSettings', 'MainNodeOnly', 'bcgooglesitemapdynamic.ini' )
     $show_untranslated_objects=ezini( 'RegionalSettings', 'ShowUntranslatedObjects', 'site.ini' )

     $change_freq=ezini( 'NodeChangeFreqSettings', 'NodeDepthChangefreq', 'bcgooglesitemapdynamic.ini' )
     $standard_change_freq=ezini( 'StandardSettings', 'StandardChangefreq', 'bcgooglesitemapdynamic.ini' )
     $node_change_freq_list=ezini( 'NodeChangeFreqSettings', 'NodeIndividualChangefreq', 'bcgooglesitemapdynamic.ini' )
     $subtree_change_freq_list=ezini( 'SubtreeChangeFreqSettings', 'SubtreeChangefreq', 'bcgooglesitemapdynamic.ini' )
     $folder_change_freq_modified=ezini( 'FolderChangeFreqSettings', 'FolderChangefreqModified', 'bcgooglesitemapdynamic.ini' )
     $subtree_change_freq_modified=ezini( 'SubtreeChangeFreqSettings', 'SubtreeChangefreqModified', 'bcgooglesitemapdynamic.ini' )

     $priority=ezini( 'NodePrioritySettings', 'NodeDepthPriority', 'bcgooglesitemapdynamic.ini' )
     $standard_priority=ezini( 'StandardSettings', 'StandardPriority', 'bcgooglesitemapdynamic.ini' )
     $node_priority_list=ezini( 'NodePrioritySettings', 'NodeIndividualPriority', 'bcgooglesitemapdynamic.ini' )
     $subtree_priority_list=ezini( 'SubtreePrioritySettings', 'SubtreePriority', 'bcgooglesitemapdynamic.ini' )
     $folder_priority_modified=ezini( 'FolderPrioritySettings', 'FolderPriorityModified', 'bcgooglesitemapdynamic.ini' )
     $subtree_priority_modified=ezini( 'SubtreePrioritySettings', 'SubtreePriorityModified', 'bcgooglesitemapdynamic.ini' )

     $node_visibility_list=ezini( 'NodeVisibilitySettings', 'NodeIndividualVisibility', 'bcgooglesitemapdynamic.ini' )
     $subtree_visibility_list=ezini( 'SubtreeVisibilitySettings', 'SubtreeVisibility', 'bcgooglesitemapdynamic.ini' )

     $cur_timestamp=currentdate()
     $diff_last_modified=sub( $cur_timestamp, $node.object.modified )
     $request_uri='REQUEST_URI'|getenv
     $http_host='HTTP_HOST'|getenv
     $request_uri_list=$request_uri|explode( 'layout' )}
{if not( is_set( $sitemap_siteurl ) )}
    {def $sitemap_siteurl=concat( $http_host, $request_uri_list[0] )}
{/if}
{* Get the value for <changefreq> *}
{if is_set( $subtree_change_freq_list[$node.node_id] )}
    {def $cur_subtree_change_freq=$subtree_change_freq_list[$node.node_id]
         $output_change_freq=$subtree_change_freq_list[$node.node_id]}
{elseif and( is_set( $cur_subtree_change_freq ) , ne( $cur_subtree_change_freq, null ) )}
    {def $output_change_freq=$cur_subtree_change_freq}
{elseif is_set( $subtree_change_freq_modified[$node.node_id] )}
    {def $output_change_freq=$standard_change_freq
         $change_freq_modified_set_info=$subtree_change_freq_modified[$node.node_id]|explode( ';' )
         $change_freq_modified_set=$change_freq_modified_set_info[0]
         $change_freq_modified_set_depth=$change_freq_modified_set_info[1]}
    {if eq( $change_freq_modified_set_depth, 0 )}
        {def $change_freq_tree_node_list=fetch( 'content', 'tree',
                                                 hash( 'parent_node_id', $node.node_id ) )}
    {else}
        {def $change_freq_tree_node_list=fetch( 'content', 'tree',
                                                 hash( 'parent_node_id', $node.node_id,
                                                       'depth', $change_freq_modified_set_depth ) )}
    {/if}
    {def $change_freq_min_last_modified=0}
    {foreach $change_freq_tree_node_list as $change_freq_tree_node}
        {if eq( $change_freq_min_last_modified, 0 )}
            {set $change_freq_min_last_modified=$change_freq_tree_node.object.modified}
        {else}
            {if lt( $change_freq_tree_node.object.modified, $change_freq_min_last_modified )}
                {set $change_freq_min_last_modified=$change_freq_tree_node.object.modified}
            {/if}
        {/if}
    {/foreach}
    {def $change_freq_min_last_modified_intervall=sub( $cur_timestamp, $change_freq_min_last_modified )
         $subtree_change_freq_modified_set_list=ezini( 'SubtreeChangeFreqSettings', $change_freq_modified_set, 'bcgooglesitemapdynamic.ini' )}
    {foreach $subtree_change_freq_modified_set_list as $subtree_change_freq_modified_set}
        {def $subtree_change_freq_modified_set_info=$subtree_change_freq_modified_set|explode( ';' )
             $change_freq_time_intervall=$subtree_change_freq_modified_set_info[0]
             $change_freq_modified=$subtree_change_freq_modified_set_info[1]}

             {if le( $change_freq_min_last_modified_intervall, $change_freq_time_intervall )}
                 {def $output_change_freq=$change_freq_modified}
                 {break}
             {/if}
    {/foreach}
    {def $cur_subtree_change_freq_modified=$output_change_freq}
{elseif and( is_set( $cur_subtree_change_freq_modified ) , ne( $cur_subtree_change_freq_modified, null ) )}
    {def $output_change_freq=$cur_subtree_change_freq_modified}
{/if}
{if not( is_set( $output_change_freq ) )}
    {if is_set( $change_freq[$depth] )}
        {def $output_change_freq=$change_freq[$depth]}
    {else}
        {def $output_change_freq=$standard_change_freq}
    {/if}
{/if}
{if is_set( $folder_change_freq_modified[$node.parent_node_id] )}
    {def $output_change_freq=$standard_change_freq
         $change_freq_folder_min_last_modified_intervall=sub( $cur_timestamp, $node.object.modified )
         $folder_change_freq_modified_set_list=ezini( 'FolderChangeFreqSettings', $folder_change_freq_modified[$node.parent_node_id], 'bcgooglesitemapdynamic.ini' )}

    {foreach $folder_change_freq_modified_set_list as $folder_change_freq_modified_set}
        {def $folder_change_freq_modified_set_info=$folder_change_freq_modified_set|explode( ';' )
             $change_freq_folder_time_intervall=$folder_change_freq_modified_set_info[0]
             $folder_change_freq_modified=$folder_change_freq_modified_set_info[1]}

             {if le( $change_freq_folder_min_last_modified_intervall, $change_freq_folder_time_intervall )}
                 {def $output_change_freq=$folder_change_freq_modified}
                 {break}
             {/if}
    {/foreach}
{/if}
{if is_set( $node_change_freq_list[$node.node_id] )}
    {def $output_change_freq=$node_change_freq_list[$node.node_id]}
{/if}
{* Get value for <priority> *}
{if is_set( $subtree_priority_list[$node.node_id] )}
    {def $cur_subtree_priority=$subtree_priority_list[$node.node_id]
         $output_priority=$subtree_priority_list[$node.node_id]}
{elseif and( is_set( $cur_subtree_priority ) , ne( $cur_subtree_priority, null ) )}
    {def $output_priority=$cur_subtree_priority}
{elseif is_set( $subtree_priority_modified[$node.node_id] )}
    {def $output_priority=$standard_priority
         $modified_set_info=$subtree_priority_modified[$node.node_id]|explode( ';' )
         $modified_set=$modified_set_info[0]
         $modified_set_depth=$modified_set_info[1]}
    {if eq( $modified_set_depth, 0 )}
        {def $tree_node_list=fetch( 'content', 'tree',
                                     hash( 'parent_node_id', $node.node_id ) )}
    {else}
        {def $tree_node_list=fetch( 'content', 'tree',
                                     hash( 'parent_node_id', $node.node_id,
                                           'depth', $modified_set_depth ) )}
    {/if}
    {def $min_last_modified=0}
    {foreach $tree_node_list as $tree_node}
           {if eq( $min_last_modified, 0 )}
            {set $min_last_modified=$tree_node.object.modified}
        {else}
            {if lt( $tree_node.object.modified, $min_last_modified )}
                {set $min_last_modified=$tree_node.object.modified}
            {/if}
        {/if}
    {/foreach}
    {def $min_last_modified_intervall=sub( $cur_timestamp, $min_last_modified )
         $subtree_priority_modified_set_list=ezini( 'SubtreePrioritySettings', $modified_set, 'bcgooglesitemapdynamic.ini' )}
    {foreach $subtree_priority_modified_set_list as $subtree_priority_modified_set}
        {def $subtree_priority_modified_set_info=$subtree_priority_modified_set|explode( ';' )
             $time_intervall=$subtree_priority_modified_set_info[0]
             $priority_modified=$subtree_priority_modified_set_info[1]}

             {if le( $min_last_modified_intervall, $time_intervall )}
                 {def $output_priority=$priority_modified}
                 {break}
             {/if}
    {/foreach}
    {def $cur_subtree_priority_modified=$output_priority}
{elseif and( is_set( $cur_subtree_priority_modified ) , ne( $cur_subtree_priority_modified, null ) )}
    {def $output_priority=$cur_subtree_priority_modified}
{/if}
{if not( is_set( $output_priority ) )}
    {if is_set( $priority[$depth] )}
        {def $output_priority=$priority[$depth]}
    {else}
        {def $output_priority=$standard_priority}
    {/if}
{/if}
{if is_set( $folder_priority_modified[$node.parent_node_id] )}
    {def $output_priority=$standard_priority
         $folder_min_last_modified_intervall=sub( $cur_timestamp, $node.object.modified )
         $folder_priority_modified_set_list=ezini( 'FolderPrioritySettings', $folder_priority_modified[$node.parent_node_id], 'bcgooglesitemapdynamic.ini' )}

    {foreach $folder_priority_modified_set_list as $folder_priority_modified_set}
        {def $folder_priority_modified_set_info=$folder_priority_modified_set|explode( ';' )
             $folder_time_intervall=$folder_priority_modified_set_info[0]
             $folder_priority_modified=$folder_priority_modified_set_info[1]}

             {if le( $folder_min_last_modified_intervall, $folder_time_intervall )}
                 {def $output_priority=$folder_priority_modified}
                 {break}
             {/if}
    {/foreach}
{/if}
{if is_set( $node_priority_list[$node.node_id] )}
    {def $output_priority=$node_priority_list[$node.node_id]}
{/if}
{* Get visibility fpr current Node *}
{if is_set( $subtree_visibility_list[$node.node_id] )}
    {def $cur_subtree_visibility=$subtree_visibility_list[$node.node_id]
         $output_visibility=$subtree_visibility_list[$node.node_id]}
{elseif and( is_set( $cur_subtree_visibility ) , ne( $cur_subtree_visibility, null ) )}
    {def $output_visibility=$cur_subtree_visibility}
{else}
    {def $output_visibility='show'}
{/if}

{if is_set( $node_visibility_list[$node.node_id] )}
    {def $output_visibility=$node_visibility_list[$node.node_id]}
{/if}

{* Generate XML-output for one node *}
{if eq( $output_visibility, 'show' )}
    <url>
        <loc>{*concat( 'http://', $sitemap_siteurl, $node.url_alias )*}{concat( 'http://', $http_host, '/', $node.url_alias )|ezurl(no)}</loc>
        <lastmod>{$node.object.modified|datetime( 'custom', '%Y-%m-%d' )}</lastmod>
        <changefreq>{$output_change_freq|wash()}</changefreq>
        <priority>{$output_priority|wash()}</priority>
    </url>
{/if}
{* Getting Node-list for going through it recursively *}
{if eq( $show_untranslated_objects, 'disabled' )}
    {def $only_translated='true()'}
{else}
    {def $only_translated='false()'}
{/if}
{def $sitemap_node_list=fetch( 'content', 'list',
                               hash( 'parent_node_id',     $node.node_id,
                                     'sort_by',            $node.sort_array,
                                     'class_filter_type',  $class_filter_type,
                                     'class_filter_array', $class_filter,
                                     'main_node_only',     $main_node_only,
                                     'only_translated',    $only_translated ) )}
{foreach $sitemap_node_list as $sitemap_node_item}
    {if not( is_set( $cur_subtree_change_freq ) )}
        {def $cur_subtree_change_freq=null}
    {/if}

    {if not( is_set( $cur_subtree_change_freq_modified ) )}
        {def $cur_subtree_change_freq_modified=null}
    {/if}

    {if not( is_set( $cur_subtree_priority ) )}
        {def $cur_subtree_priority=null}
    {/if}

    {if not( is_set( $cur_subtree_priority_modified ) )}
        {def $cur_subtree_priority_modified=null}
    {/if}

    {if not( is_set( $cur_subtree_visibility ) )}
        {def $cur_subtree_visibility=null}
    {/if}

    {node_view_gui view=googlesitemap content_node=$sitemap_node_item sitemap_siteurl=$sitemap_siteurl cur_subtree_change_freq=$cur_subtree_change_freq cur_subtree_change_freq_modified=$cur_subtree_change_freq_modified cur_subtree_priority=$cur_subtree_priority cur_subtree_priority_modified=$cur_subtree_priority_modified cur_subtree_visibility=$cur_subtree_visibility}
{/foreach}
