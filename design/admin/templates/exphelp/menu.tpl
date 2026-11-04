{* The left menu of the help views (menu.ini [Leftmenu_help]), drawn instead of the Setup menu while the Setup tab
   stays marked; each link is shown only to a user who can open it. The names are the help's own (kernel/exphelp).
   The pages the menu does not name mark the link they belong to, passed on as uri_string (parts/ini_menu.tpl marks
   the link that address starts with): an extension's page in the viewer (exphelp/viewer/ext/...) marks Extensions,
   any other page of the viewer, a topic, a manual and the page tree (exphelp/navigation) mark Contents, an audience's
   landing page marks Help Start. *}
{def $help_menu_uri = first_set( $uri_string, '' )|trim( '/' )}
{if $help_menu_uri|begins_with( 'exphelp/viewer/ext/' )}
    {set $help_menu_uri = 'exphelp/extensions'}
{elseif or( $help_menu_uri|begins_with( 'exphelp/viewer' ), $help_menu_uri|begins_with( 'exphelp/topic' ),
            $help_menu_uri|begins_with( 'exphelp/manual' ), $help_menu_uri|begins_with( 'exphelp/navigation' ) )}
    {set $help_menu_uri = 'exphelp/contents'}
{elseif $help_menu_uri|begins_with( 'exphelp/audience' )}
    {set $help_menu_uri = 'exphelp/dashboard'}
{/if}
{include uri='design:parts/ini_menu.tpl' ini_section='Leftmenu_help' uri_string=$help_menu_uri
         i18n_hash=hash(
    'help',       'Help'|i18n( 'kernel/exphelp' ),
    'dashboard',  'Help Start'|i18n( 'kernel/exphelp' ),
    'contents',   'Contents'|i18n( 'kernel/exphelp' ),
    'search',     'Search the help'|i18n( 'kernel/exphelp' ),
    'extensions', 'Extensions'|i18n( 'kernel/exphelp' ),
    'report',     'Report an error'|i18n( 'kernel/exphelp' ),
)}
{undef $help_menu_uri}
