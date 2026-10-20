---
title: menu.ini [MenuAccessSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [MenuAccessSettings] in menu.ini: CheckViewAccess, NoAccessLinks"
keywords: Einstellung, menu.ini, MenuAccessSettings, INI, setting, CheckViewAccess, NoAccessLinks
type: generated
generated: exp:help:examples
---
# menu.ini [MenuAccessSettings]

Der Block `[MenuAccessSettings]` von `settings/menu.ini`, mit 2 Einstellung(en).

> Example of a custom navigation part  
> See TopMenuAdminMenu for how you can combine this with a  
> new top menu  
> Part\[ezcustomnavigationpart\]=Custom navigation  

Bearbeiten Sie `settings/menu.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/menu.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/menu.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/menu.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## CheckViewAccess

> Shows a top tab (\[Topmenu\_\*\] URL) or a left menu link (\[Leftmenu\_\*\] Links\[\]) only to a user who  
> can open the address it points to, checked the way the kernel checks the request (expViewAccess):  
> the URL alias, the view's policy functions with their limitations (a view without functions needs  
> the module), the siteaccess's \[SiteAccessRules\], RequireUserLogin, the user/login SiteAccess  
> limitation, and the node or object of content/view and content/edit. It comes on top of the  
> PolicyList settings below, which stay for what an address cannot tell (a node to be readable, a  
> policy the view itself asks for). Templates ask the same: fetch( 'user', 'can\_open', hash( 'uri', ... ) ).  
> enabled | disabled  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[MenuAccessSettings]
CheckViewAccess=enabled
*/ ?>
```

## NoAccessLinks

> What becomes of a left menu link the user cannot open:  
>   hidden    the link is left out (and a menu with no link left is left out)  
>   disabled  its name is shown without a link (the behaviour of the 4.x releases)  

Standard: `hidden`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[MenuAccessSettings]
NoAccessLinks=hidden
*/ ?>
```

## Siehe auch

- [Alle Blöcke von menu.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/menu.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
