<!DOCTYPE html>
{* The left column's width (the admin_left_menu_size preference): a preset name, drawn by the stylesheets, or a
   width dragged by hand, set as --left-sidebar-width by the pre-paint script in the head. No inline CSS. *}
{def $page_left_size = ezpreference( 'admin_left_menu_size' )}
<html lang="{$site.http_equiv.Content-language|wash}"{if $page_left_size|ne( '' )}{if or( eq( $page_left_size, 'small' ), eq( $page_left_size, 'medium' ), eq( $page_left_size, 'large' ) )} data-a4-left-size="{$page_left_size}"{else} data-a4-left-width="{$page_left_size|wash}"{/if}{/if}>
{undef $page_left_size}

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {* Do some uncacheable left + right menu stuff before cache-block's *}
    {def $ui_context_edit      = eq( $ui_context, 'edit' )
     $content_edit         = and( $ui_context_edit, eq( $ui_component, 'content' ) )
     $admin_left_size      = ezpreference( 'admin_left_menu_size' )
     $admin_theme          = ezpreference( 'admin_theme' )
     $left_size_hash       = 0
     $search_hash          = array( cond( ezhttp_hasvariable( 'SectionID', 'get' ), ezhttp( 'SectionID', 'get' ) ) )
     $user_hash = concat( $current_user.role_id_list|implode( ',' ), ',', $current_user.limited_assignment_value_list|implode( ',' ), ',', $current_user.is_logged_in|choose( 'anonymous', 'user' ) )
     $uri_cache_key        = $module_result.uri
}
    {* Error pages can arrive with the same URI while their title and path name the error: key them
       by error type and number too (every other page keeps the plain URI key) *}
    {if is_set( $module_result.errorType )}
    {set $uri_cache_key = concat( $module_result.uri, '|error|', $module_result.errorType, '|', first_set( $module_result.errorNumber, '' ) )}
    {/if}

    {* Pr uri header cache
 Need navigation part for cases like content/browse where node id is taken from caller params *}
    {* siteaccess_url() here too: this block holds the header block below, whose
       site link depends on the scheme, host and port of the request. *}
    {cache-block keys=array( $uri_cache_key, $user_hash, $admin_theme, $admin_left_size, $access_type, first_set( $module_result.navigation_part, $navigation_part.identifier ), $search_hash, siteaccess_url() ) ignore_content_expiry}

    {include uri='design:page_head.tpl'}

    {include uri='design:page_head_style.tpl'}
    {include uri='design:page_head_script.tpl'}

    {* the hand-dragged width of the left column, before anything is drawn (data-a4-left-width on <html>) *}
    {literal}<script>(function () { var r = document.documentElement, w = r.getAttribute('data-a4-left-width'); if (w && /^\d{2,4}(\.\d+)?px$/.test(w)) r.style.setProperty('--left-sidebar-width', w); })();</script>{/literal}

</head>

<body>

    <div id="page" class="{$navigation_part.identifier} section_id_{first_set( $module_result.section_id, 0 )}">

        <div id="header">
            <div id="header-design" class="float-break">
                {* Pr tab header cache *}
                {* siteaccess_url(): the header links to the site with the scheme, host and port
                   of the request, and Apache and Velocity share this cache. *}
                {cache-block keys=array( $ui_context, $ui_component, $user_hash, $access_type, first_set( $module_result.navigation_part, $navigation_part.identifier ), siteaccess_url() ) ignore_content_expiry}

                {* HEADER ( SEARCH, LOGO AND USERMENU ) *}
                {include uri='design:page_header.tpl'}
            </div>
        </div>
        {/cache-block}{* /Pr tab cache *}

        {/cache-block}{* /Pr uri cache *}

        <div id="columns" {if $hide_right_menu} class="hide-rightmenu" {/if}>
            <div class="dashboard-flex">
                <!-- begin::Left Sidebar -->
                {if $hide_left_menu}
                {else}
                    {include uri='design:page_leftmenu.tpl'}
                {/if}
                <!-- end::Left Sidebar -->

                <!-- begin::Main Section -->
                {if $hide_left_menu}
                    {include uri='design:page_mainarea.tpl'}
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
                                            {include uri='design:page_toppath.tpl'}
                                        </div>
                                    </div>

                                    <!-- Maincontent START -->
                                    {include uri='design:page_mainarea.tpl'}
                                    <!-- Maincontent END -->
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
                        {tool_bar name='admin_right' view='full'}
                        {tool_bar name='admin_developer' view='full'}
                    </div>
                </div>
                <!-- end::Right Sidebar -->
            </div>
            <div class="break"></div>
        </div>

        {* keyed by the user's roles too: the context menu below shows entries by policy *}
        {cache-block keys=array( $access_type, $user_hash ) ignore_content_expiry}
        <div id="footer" class="float-break">
            <div id="footer-design">
                {include uri='design:page_copyright.tpl'}
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
    <div id="overlay-mask"></div>
    <img src={'2/loader.gif'|ezimage()} id="ajaxuploader-loader"
        alt="{'Loading...'|i18n( 'design/admin/pagelayout' )}" />

</body>

</html>