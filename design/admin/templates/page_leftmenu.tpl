{* The left menu. It collapses and expands with its show/hide link (Exponential UI's exp::collapse, remembered in
   the preference admin_left_menu_collapsed; without JavaScript the link sets the preference and reloads), and its
   width is dragged with the handle in the separator (leftmenu_widthcontrol.js, saved in admin_left_menu_size).
   $collapse_left_menu comes from pagelayout.tpl, which also sets the collapsed widths before the page shows. *}
{def $left_collapsed = first_set( $collapse_left_menu, false() )}
<div id="leftmenu"{if $left_collapsed} class="collapsed"{/if}>
<a id="leftmenu-showhide" class="show-hide-control" title="{'Hide / Show leftmenu'|i18n( 'design/admin/pagelayout/leftmenu' )}" href={concat( '/user/preferences/set/admin_left_menu_collapsed/', cond( $left_collapsed, '0', '1' ) )|ezurl}></a>
<div id="leftmenu-design">

{if is_set( $module_result.left_menu )}
    {include uri=$module_result.left_menu}
{else}
    {*
        Get navigationpart identifier variable depends if the call is an contenobject
        or a custom module
    *}
    {def $navigation_part_name = $navigation_part.identifier}
    {if $navigation_part_name|eq('')}
        {set $navigation_part_name = $module_result.navigation_part}
    {/if}
    {*
        Include automatically the menu template for the $navigation_part_name
        ez $part_name navigationpart =>  parts/$part_name/menu.tpl
    *}
    {def $extract_length = sub( count_chars( $navigation_part_name ), '14' )
         $part_name = $navigation_part_name|extract( '2', $extract_length )}

    {* a kernel navigation part with the exp prefix: exp<name>navigationpart => parts/<name>/menu.tpl *}
    {if $navigation_part_name|begins_with( 'exp' )}{set $part_name = $navigation_part_name|explode( 'navigationpart' )[0]|extract( 3 )}{/if}
    {include uri=concat( 'design:parts/', $part_name, '/menu.tpl' )}

    {undef $extract_length $part_name $navigation_part_name}
{/if}

</div>
</div>

{ezscript( 'leftmenu_widthcontrol.js' )}
<script type="text/javascript">
{literal}
if ( window.Exp && window.Exp.collapse ) {
    Exp.ready(function () {
        var $ = Exp.$, strip = 22;   // what stays of the menu when it is collapsed: the show/hide link
        var width = function () { return $( '#leftmenu' ).outerWidth(); };
        Exp.collapse({
            link: '#leftmenu-showhide',
            collapsed: $( '#leftmenu' ).hasClass( 'collapsed' ) ? 1 : 0,
            elements: [{
                selector: '#leftmenu',
                duration: 0.4,
                fullStyle: { marginLeft: '0px' },
                collapsedStyle: { marginLeft: function () { return ( strip - width() ) + 'px'; } }
            },{
                selector: '#maincontent',
                duration: 0.4,
                fullStyle: { marginLeft: function () { return ( width() + 10 ) + 'px'; } },
                collapsedStyle: { marginLeft: ( strip + 10 ) + 'px' }
            },{
                selector: '#left-panels-separator',
                duration: 0.4,
                fullStyle: { left: function () { return ( $( '#leftmenu' ).innerWidth() - ( parseInt( $( '#leftmenu-design' ).css( 'marginRight' ), 10 ) || 0 ) ) + 'px'; } },
                collapsedStyle: { left: '0px' }
            }],
            callback: function () {
                $( '#leftmenu' ).toggleClass( 'collapsed', !!this.conf.collapsed );
                $( '#columns' ).toggleClass( 'leftmenu-collapsed', !!this.conf.collapsed );
            },
            pref: { name: 'admin_left_menu_collapsed', values: [0, 1] }
        });
    });
}
{/literal}
</script>

<hr class="hide" />
