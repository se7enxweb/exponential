{def $page_limit=30
     $col_count=2
     $sub_children=0
     $child_url_alias=''
     $children=fetch('content','list',hash('parent_node_id', $node.node_id,
                                           'limit', $page_limit,
                                           'offset', $view_parameters.offset,
                                           'sort_by', $node.sort_array,
					   'class_filter_type',  'exclude',
                                           'class_filter_array', array( 'comment' )
					 ) 
			)}
<div class="border-box">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc float-break">

<div class="content-view-sitemap">

<div class="attribute-header">
	<h1 class="long">{"Sitemap"|i18n("design/ezwebin/view/sitemap")}{* $node.name|wash *}</h1>
</div>

<div class="">
{foreach $children as $key => $child}
{set $child_url_alias=$child.url_alias}
{if $child.class_identifier|eq( 'link' )}
  {set $child_url_alias=$child.data_map.location.content}
{/if}
<div class="">
<h2><a href={$child_url_alias|ezurl}>{$child.name}</a></h2>
    {* if ne( $child.node_id, 180 ) *}
    {* if $child.class_identifier|eq( 'event_calendar' )}
        {set $sub_children=fetch('content','list',hash( 'parent_node_id', $child.node_id, 
                                                        'limit', $page_limit,
                                                        'sort_by', array( 'attribute', false(), 'event/from_time' ),
							'class_filter_type',  'exclude',
							'class_filter_array', array( 'link', 'comment' )  
							)
				 )}
    {else *}
        {set $sub_children=fetch('content','list',hash( 'parent_node_id', $child.node_id,
                                                        'limit', $page_limit,
                                                        'sort_by', $child.sort_array))}
    {* /if *}
    <ul>
    {foreach $sub_children as $sub_child}
    <li><a href={$sub_child.url_alias|ezurl}>{$sub_child.name}</a></li>
    {/foreach}
    </ul>
    {/if}
    </div>
{/foreach}
</div>
</div>

</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>
