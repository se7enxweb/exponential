{* admin4: the remembered choices of the frame, as attributes of <html> for window.a4Pref (the first script of
   page_head_style.tpl). For a signed-in user they are eZPreferences, saved through user/preferences/set_and_exit
   (data-a4-pref-url) and written here on every page, outside any cache-block, so the page is drawn with them
   (no flash) in every browser the user signs in with. An anonymous page gets none of these attributes and keeps
   the choices in the browser (localStorage) only.
     admin_color_mode     light | dark | system (empty: light)
     admin_left_sidebar   1 | 0, the left column on a wide window (empty: open)
     admin_right_sidebar  1 | 0, the right column on a wide window (empty: open)
     admin_right_drawer   1 | 0, the right column as a drawer on a tablet-sized window, 576 to 1279px (empty: closed)
     admin_navgroups      the closed groups of the left menus, comma separated ids (empty or "-": none)
     admin_node_preview_height  the height of the node view's tab box in pixels (empty or "-": the default; admin4.js)
   Used by the page layouts of admin4 and admin4l. No output for anonymous pages; no line breaks (it sits in a tag). *}
{if fetch( 'user', 'current_user' ).is_logged_in} data-a4-pref-url="{'/user/preferences/set_and_exit'|ezurl( 'no' )|wash}"{foreach array( 'admin_color_mode', 'admin_left_sidebar', 'admin_right_sidebar', 'admin_right_drawer', 'admin_navgroups', 'admin_node_preview_height' ) as $a4_pref} data-a4-pref-{$a4_pref|explode( '_' )|implode( '-' )}="{ezpreference( $a4_pref )|wash}"{/foreach}{/if}
