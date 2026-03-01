<!DOCTYPE html>
<html lang="{$site.http_equiv.Content-language|wash}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
{def $basket = fetch( shop, basket )
     $basket_qty        = 0
     $basket_is_empty   = cond( $current_user.is_logged_in, fetch( shop, basket ).is_empty, 1 )
     $user_hash         = concat( $current_user.role_id_list|implode( ',' ), ',', $current_user.limited_assignment_value_list|implode( ',' ) )
     $path_array        = array()
     $path_normalized   = ''
     $indexpage         = ezini( 'NodeSettings', 'RootNode', 'content.ini' )
     $pagerootdepth    = ezini( 'SiteSettings', 'RootNodeDepth', 'site.ini' )
}
{foreach $basket.items as $cart_item}{set $basket_qty = $basket_qty|sum( $cart_item.item_count )}{/foreach}
{include uri='design:page_head_displaystyles.tpl'}

{if is_set( $extra_cache_key )|not}
    {def $extra_cache_key = ''}
{/if}

{cache-block keys=array( $module_result.uri, $basket_is_empty, $current_user.contentobject_id, $extra_cache_key )}
{def $pagedata         = ezpagedata()
     $pagestyle        = $pagedata.css_classes
     $locales          = fetch( 'content', 'translation_list' )
     $pagedesign       = $pagedata.template_look
     $pagedesign_class = fetch( 'content', 'class', hash( 'class_id', 'template_look' ) )
     $current_node_id  = $pagedata.node_id}
{if $pagedesign_class.object_count|eq( 0 )|not}{set $pagedesign = $pagedesign_class.object_list[1]}{/if}{if is_set( $module_result.content_info )}{set $content_info = $module_result.content_info}{/if}

    {include uri='design:page_head.tpl'}
    <!-- google fonts  -->
    <link rel="preconnect" href="https://fonts.googleapis.com" >
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin >
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap"
      rel="stylesheet"
    >
    {*<!-- splide -->
    <link rel="stylesheet" href={"javascript/splide/css/splide.min.css"|ezdesign()} >*}
    <!-- custom styles -->
    {include uri='design:page_head_style.tpl'}
    {include uri='design:page_head_script.tpl'}
</head>
{* To simplify IE css targeting. IE9 conforms, so threat as rest *}
<!--[if lt IE 7 ]><body class="ie6"><![endif]-->
<!--[if IE 7 ]>   <body class="ie7"><![endif]-->
<!--[if IE 8 ]>   <body class="ie8"><![endif]-->
<!--[if (gt IE 8)|!(IE)]><!--><body><!--<![endif]-->

  <!-- Header area: START -->
    {include uri='design:page_header.tpl'}
  <!-- Header area: END -->

<script>
<!--
    var ezpRoot = "{'/'|ezurl( no )|rtrim( '/' )}";
    var ezpFilesRoot = "{'/'|ezroot( no )|rtrim( '/' )}";
    var ezpCurrentURL = {cond( is_set( $module_result.node_id ), concat( '"', $module_result.uri|wash( 'javascript' ), '"' ), 'false' )};
    var ezpReturnURL = {cond( is_set( $module_result.node_id ), $module_result.uri, '/' )|ezurl( , 'full' )};
// -->
</script>

    <!-- main starts -->
  <main class="main-sections">
    <!-- Complete page area: START -->

<!-- Change between "sidemenu"/"nosidemenu" and "extrainfo"/"noextrainfo" to switch display of side columns on or off  -->
{*<div id="page" class="{$pagestyle}">*}

  {if and( is_set( $pagedata.persistent_variable.extra_template_list ),
             $pagedata.persistent_variable.extra_template_list|count() )}
    {foreach $pagedata.persistent_variable.extra_template_list as $extra_template}
      {include uri=concat('design:extra/', $extra_template)}
    {/foreach}
  {/if}

  {cache-block keys=array( $module_result.uri, $user_hash, $extra_cache_key )}

  {*<!-- Top menu area: START -->
  {if $pagedata.top_menu}
    {include uri='design:page_topmenu.tpl'}
  {/if}
  <!-- Top menu area: END -->*}

 {* <!-- Path area: START -->
  {if $pagedata.show_path}
    {include uri='design:page_toppath.tpl'}
  {/if}
  <!-- Path area: END -->*}

  <!-- Toolbar area: START -->
  {if and( $pagedata.website_toolbar, $pagedata.is_edit|not)}
    {include uri='design:page_toolbar.tpl'}
  {/if}
  <!-- Toolbar area: END -->

  <!-- Columns area: START -->
  {*<div id="columns-position">
  <div id="columns" class="float-break"> *}

    {*<!-- Side menu area: START -->
    {if $pagedata.left_menu}
        {include uri='design:page_leftmenu.tpl'}
    {/if}
    <!-- Side menu area: END -->*}

  {/cache-block}

{/cache-block}

    <div class="container">
    {foreach $module_result.path as $index => $path}
        {if $index|ge($pagerootdepth)}
            {set $path_array = $path_array|append($path)}
        {/if}
        {if is_set($path.node_id)}
            {set $path_normalized = $path_normalized|append( concat('subtree_level_', $index, '_node_id_', $path.node_id, ' ' ))}
        {/if}
    {/foreach}

        <!-- Path area: START -->
        <div id="path">
    {include uri='design:parts/path.tpl'}
        </div>
        <!-- Path area: END -->
    <!-- Main area: START -->
    {*include uri='design:page_mainarea.tpl'*}
          <!-- Main area content: START -->
          {$module_result.content}
    </div>
    <!-- Main area content: END -->
    <!-- Main area: END -->
  </main>
    <!-- main ends -->
{cache-block keys=array( $module_result.uri, $user_hash, $access_type.name, $extra_cache_key )}

    {* if is_unset($pagedesign)}
        {def $pagedata   = ezpagedata()
             $pagedesign = $pagedata.template_look}
    {/if *}

    {*<!-- Extra area: START -->
    {if $pagedata.extra_menu}
        {include uri='design:page_extramenu.tpl'}
    {/if}
    <!-- Extra area: END -->*}

{*  </div>
  </div>*}
  <!-- Columns area: END -->

  <!-- Footer area: START -->
   {include uri='design:page_footer.tpl'}
   {*include uri="design:page_footer_secondary.tpl"*}

    <!-- scroll to top starts -->
    <div class="scroll-to-top-box fixed left-0 bottom-8 xl:bottom-12 w-full pointer-events-none">
      <div class="scroll-to-top-container lg:container mx-auto relative">
        <button
          type="button"
          id="scroll-to-top"
          class="scroll-to-top inline-flex items-center justify-center border border-solid border-orange-500 bg-orange-500 text-base text-white font-medium p-2 transition duration-500 hover:bg-orange-600 hover:text-white w-10 h-10 absolute bottom-0 right-4 z-30 pointer-events-auto"
        >
          <i class="fa-solid fa-arrow-up-long"></i>
        </button>
      </div>
    </div>
    <!-- scroll to top ends -->

    <!-- Google tag (gtag.js) -->
    {'false'|bc_ga_urchin()}
  <!-- Footer area: END -->
{/cache-block}
    {* This comment will be replaced with actual debug report (if debug is on). *}
    <!--DEBUG_REPORT-->
  </body>
</html>
