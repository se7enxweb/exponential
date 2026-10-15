{* Setup > Overview (setup/menu, the Setup tab's first page): every function of the Setup menu as a tile with a
   Bootstrap icon, its name and one line on what it does, in the groups of the left menu.

   Everything comes from menu.ini [Leftmenu_setup], the Setup menu's own section (parts/setup/menu.tpl,
   parts/ini_menu.tpl): the links, their names, groups and access checks, and per link LinkIcons[],
   LinkDescriptions[] and LinkFigureTemplates[]. A function the user may not open has no tile; an extension's link
   shows under "More", with the icon, description and figure its menu.ini.append.php gives it.
   Guide: doc/features/6.0/admin4-design.md ("Setup overview") *}
{def $setup_menu_layout = 'tiles'}

<div class="a4-page a4-setup-overview">

    <div class="a4-pagehead">
        <div class="a4-pagehead-main">
            <h1 class="a4-pagehead-title">{'Setup'|i18n( 'design/admin/setup/setupmenu' )}</h1>
            <p class="a4-pagehead-sub">{'Configure the installation: content model, site and URLs, workflows, users and the system.'|i18n( 'design/admin/setup/setupmenu' )}</p>
        </div>
    </div>

    {include uri='design:parts/setup/menu.tpl'}

</div>
{undef $setup_menu_layout}
