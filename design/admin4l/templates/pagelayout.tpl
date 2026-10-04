<!DOCTYPE html>
<html lang="{$site.http_equiv.Content-language|wash}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {* Do some uncacheable left + right menu stuff before cache-block's *}
    {* $hide_left_menu and $hide_right_menu as the admin design defines them: on content edit pages (edit, history,
       version view ...) the page's own template brings the left column (Object information) and the content tree
       is not drawn; a template can ask for it with persistent_variable left_menu / extra_menu. admin3 had lost the
       two definitions but kept their {if}s, so the tree and the template's column were both drawn, fixed at the
       same place, one over the other. *}
    {def $ui_context_edit      = eq( $ui_context, 'edit' )
     $content_edit         = and( $ui_context_edit, eq( $ui_component, 'content' ) )
     $hide_left_menu       = first_set( $module_result.content_info.persistent_variable.left_menu, $content_edit|not )|not
     $hide_right_menu      = first_set( $module_result.content_info.persistent_variable.extra_menu, $ui_context_edit|not )|not
     $admin_left_size      = ezpreference( 'admin_left_menu_size' )
     $admin_theme          = ezpreference( 'admin_theme' )
     $left_size_hash       = 0
     $search_hash          = array( cond( ezhttp_hasvariable( 'SectionID', 'get' ), ezhttp( 'SectionID', 'get' ) ) )
     $user_hash = concat( $current_user.role_id_list|implode( ',' ), ',', $current_user.limited_assignment_value_list|implode( ',' ) )
     $uri_cache_key        = $module_result.uri
}
    {* admin4l: the admin layout for this page. The safety switch is explayouts.ini [AdminLayoutSettings]
       Enabled=enabled|disabled: anything but "enabled" gives the plain admin4 page. When no layout resolves, or
       the resolved layout has no blocks, the page is admin4's too, and a zone that renders nothing is drawn
       from admin4's own template (the zone fallback below). *}
    {def $admin_layout      = false()
         $admin_layout_id   = 0
         $admin_layout_type = ''
         $admin_mp          = module_params()
         $zone_html         = ''}
    {if and( ezini_hasvariable( 'AdminLayoutSettings', 'Enabled', 'explayouts.ini' ), eq( ezini( 'AdminLayoutSettings', 'Enabled', 'explayouts.ini' ), 'enabled' ) )}
        {set $admin_layout = fetch( 'explayouts', 'resolve_admin_layout', hash( 'module', first_set( $admin_mp.module_name, '' ), 'view', first_set( $admin_mp.function_name, '' ) ) )}
        {if and( is_array( $admin_layout ), is_set( $admin_layout.block_count ), $admin_layout.block_count|gt( 0 ) )}
            {set $admin_layout_id   = $admin_layout.id
                 $admin_layout_type = $admin_layout.layout_type}
            {* the layout type decides which side columns the page has *}
            {if $admin_layout_type|eq( 'admin_full' )}
                {set $hide_left_menu = true()
                     $hide_right_menu = true()}
            {elseif $admin_layout_type|eq( 'admin_2col' )}
                {set $hide_right_menu = true()}
            {/if}
        {else}
            {set $admin_layout = false()}
        {/if}
    {/if}

    {* Error pages can arrive with the same URI while their title and path name the error: key them
       by error type and number too (every other page keeps the plain URI key) *}
    {if is_set( $module_result.errorType )}
    {set $uri_cache_key = concat( $module_result.uri, '|error|', $module_result.errorType, '|', first_set( $module_result.errorNumber, '' ) )}
    {/if}

    {* Pr uri header cache
 Need navigation part for cases like content/browse where node id is taken from caller params *}
    {* siteaccess_url() here too: this block holds the header block below, whose
       site link depends on the scheme, host and port of the request. *}
    {cache-block keys=array( $uri_cache_key, $user_hash, $admin_theme, $admin_left_size, $access_type, first_set( $module_result.navigation_part, $navigation_part.identifier ), $search_hash, siteaccess_url(), $admin_layout_id ) ignore_content_expiry}

    {include uri='design:page_head.tpl'}

    {include uri='design:page_head_style.tpl'}
    {include uri='design:page_head_script.tpl'}

    {* Set CSS variables for left sidebar width and font size based on user preference *}
    {def $left_sidebar_width = '16rem'}
    {def $left_sidebar_font_size = '0.8225rem'}
    {if eq( $admin_left_size, 'medium' )}
        {set $left_sidebar_width = '22rem'}
        {set $left_sidebar_font_size = '1rem'}
    {else}
        {if eq( $admin_left_size, 'large' )}
            {set $left_sidebar_width = '30rem'}
            {set $left_sidebar_font_size = '1.305rem'}
        {else}
            {* Custom pixel values from drag-resize are passed through as-is *}
            {if and( $admin_left_size|ne( '' ), $admin_left_size|ne( 'small' ), $admin_left_size|ne( 'medium' ), $admin_left_size|ne( 'large' ) )}
                {set $left_sidebar_width = $admin_left_size|wash}
            {/if}
        {/if}
    {/if}
    <style>
        :root {ldelim} 
        --left-sidebar-width: {$left_sidebar_width};
        --left-sidebar-font-size: {$left_sidebar_font_size};
        {rdelim}
    </style>
    {undef $left_sidebar_width}
    {undef $left_sidebar_font_size}

</head>

<body>

    <div id="page" class="{$navigation_part.identifier} section_id_{first_set( $module_result.section_id, 0 )}">

        <div id="header">
            <div id="header-design" class="float-break">
                {* Pr tab header cache *}
                {* siteaccess_url(): the header links to the site with the scheme, host and port
                   of the request, and Apache and Velocity share this cache. *}
                {cache-block keys=array( $ui_context, $ui_component, $user_hash, $access_type, first_set( $module_result.navigation_part, $navigation_part.identifier ), siteaccess_url(), $admin_layout_id ) ignore_content_expiry}

                {* HEADER ( SEARCH, LOGO AND USERMENU ): the navbar. The header zone fills it with the logo,
                   search, theme switch and sidebar toggles, the topmenu zone with the tab menu. Either zone
                   empty (or no admin layout): admin4's own navbar. *}
                {def $header_html  = ''
                     $topmenu_html = ''}
                {if $admin_layout}
                    {set-block variable=$header_html}{include uri='design:explayouts/admin_zone.tpl' layout=$admin_layout zone_identifier='header'}{/set-block}
                    {set-block variable=$topmenu_html}{include uri='design:explayouts/admin_zone.tpl' layout=$admin_layout zone_identifier='topmenu'}{/set-block}
                {/if}
                {if and( $header_html|trim|ne( '' ), $topmenu_html|trim|ne( '' ) )}
                <div id="navbar" class="navbar-main">
                    {$header_html}
                    <div class="navbar-menu {if $ui_context_edit}disabled{/if}">
                        <ul class="navbar-bar">
                            {$topmenu_html}
                        </ul>
                    </div>
                </div>
                {else}
                    {include uri='design:page_header.tpl'}
                {/if}
                {undef $header_html $topmenu_html}
            </div>
        </div>
        {/cache-block}{* /Pr tab cache *}

        {/cache-block}{* /Pr uri cache *}

        <div id="columns" {if $hide_right_menu} class="hide-rightmenu" {/if}>
            <div class="dashboard-flex">
                <!-- begin::Left Sidebar -->
                {if $hide_left_menu}
                {else}
                    {if $admin_layout}{set-block variable=$zone_html}{include uri='design:explayouts/admin_zone.tpl' layout=$admin_layout zone_identifier='left'}{/set-block}{else}{set $zone_html = ''}{/if}
                    {if $zone_html|trim|ne( '' )}
                    <div id="leftmenu" class="sidebar left">
                        <div id="leftmenu-resize-handle" class="leftmenu-resize-handle" aria-hidden="true"></div>
                        <div id="leftmenu-design">
                            {$zone_html}
                        </div>
                    </div>
                    {else}
                    {include uri='design:page_leftmenu.tpl'}
                    {/if}
                {/if}
                <!-- end::Left Sidebar -->

                <!-- begin::Main Section -->
                {if $hide_left_menu}
                    {if $admin_layout}{include uri='design:explayouts/admin_zone.tpl' layout=$admin_layout zone_identifier='main'}{else}{include uri='design:page_mainarea.tpl'}{/if}
                {else}
                    <div id="maincolumn" class="content-wrapper">
                        {* Pr uri Path/Left menu cache (dosn't use ignore_content_expiry because of content structure menu  ) *}
                        {cache-block keys=array( $uri_cache_key, $user_hash, $left_size_hash, $access_type, first_set( $module_result.navigation_part, $navigation_part.identifier ) )}
                        {/cache-block}{* /Pr uri cache *}
                        {* Main area START *}
                        <div id="maincontent">
                            <div id="maincontent-design" class="float-break">
                                <div id="fix">

                                    <div id="path">
                                        <div id="path-design">
                                            {if $admin_layout}{set-block variable=$zone_html}{include uri='design:explayouts/admin_zone.tpl' layout=$admin_layout zone_identifier='main_top'}{/set-block}{else}{set $zone_html = ''}{/if}
                                            {if $zone_html|trim|ne( '' )}{$zone_html}{else}{include uri='design:page_toppath.tpl'}{/if}
                                        </div>
                                    </div>

                                    <!-- Maincontent START -->
                                    {if $admin_layout}{include uri='design:explayouts/admin_zone.tpl' layout=$admin_layout zone_identifier='main'}{else}{include uri='design:page_mainarea.tpl'}{/if}
                                    <!-- Maincontent END -->
                                    {if $admin_layout}{include uri='design:explayouts/admin_zone.tpl' layout=$admin_layout zone_identifier='main_bottom'}{/if}
                                </div>
                                <div class="break"></div>
                            </div>
                        </div>
                    </div>
                {/if}
                <!-- end::Main Section -->

                <!-- begin::Right Sidebar -->
                <div id="rightmenu" class="sidebar right">
                    <div id="rightmenu-design">
                        {if $admin_layout}{set-block variable=$zone_html}{include uri='design:explayouts/admin_zone.tpl' layout=$admin_layout zone_identifier='right'}{/set-block}{else}{set $zone_html = ''}{/if}
                        {if $zone_html|trim|ne( '' )}{$zone_html}{else}
                        {tool_bar name='admin_right' view='full'}
                        {tool_bar name='admin_developer' view='full'}
                        {/if}
                    </div>
                </div>
                <!-- end::Right Sidebar -->
            </div>
            <div class="break"></div>
        </div>

        {* keyed by the user's roles too: the context menu below shows entries by policy *}
        {cache-block keys=array( $access_type, $user_hash, $admin_layout_id ) ignore_content_expiry}
        <div id="footer" class="float-break">
            <div id="footer-design">
                {if $admin_layout}{set-block variable=$zone_html}{include uri='design:explayouts/admin_zone.tpl' layout=$admin_layout zone_identifier='footer'}{/set-block}{else}{set $zone_html = ''}{/if}
                {if $zone_html|trim|ne( '' )}{$zone_html}{else}{include uri='design:page_copyright.tpl'}{/if}
            </div>
        </div>

        <div class="break"></div>

        {* The popup menu include must be outside all divs. It is hidden by default. *}
        {include uri='design:popupmenu/popup_menu.tpl'}
        {/cache-block}
    </div>
    {* This comment will be replaced with actual debug report (if debug is on). *}
    <!--DEBUG_REPORT-->

    {* modal window and AJAX stuff *}
    <div id="overlay-mask" style="display:none;"></div>
    <img src={'2/loader.gif'|ezimage()} id="ajaxuploader-loader" style="display:none;"
        alt="{'Loading...'|i18n( 'design/admin/pagelayout' )}" />

</body>

</html>