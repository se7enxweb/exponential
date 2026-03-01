{*
  To be able to define menu entries in ini at the same time as letting i18n
  system translate the names, we use a lookup table for translatable strings.
  If the ini name is not defined here (key), then LinkNames ini value is used.
  For extensions that need to extend this, you either have to override this
  template or let translations use LinkNames as described in menu.ini.
*}

{include uri='design:parts/ini_menu.tpl' ini_section='Leftmenu_bccie_create' i18n_hash=hash(
             'export',  'Export'|i18n( 'design/admin/parts/bccie/menu' ),
             'project', 'Extension project'|i18n( 'design/admin/parts/bccie/menu' ) )}
