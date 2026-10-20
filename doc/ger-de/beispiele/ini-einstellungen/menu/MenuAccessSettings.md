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

> Beispiel eines eigenen Navigationsbereichs  
> Wie Sie dies mit einem neuen Hauptmenüeintrag kombinieren,  
> siehe TopMenuAdminMenu  
> Part\[ezcustomnavigationpart\]=Custom navigation  

Bearbeiten Sie `settings/menu.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/menu.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/menu.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/menu.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## CheckViewAccess

> Zeigt einen Reiter des Hauptmenüs (\[Topmenu\_\*\] URL) oder einen Link des linken Menüs (\[Leftmenu\_\*\] Links\[\]) nur einem Benutzer, der  
> die Adresse öffnen kann, auf die er zeigt, geprüft wie der Kernel die Anfrage prüft (expViewAccess):  
> der URL-Alias, die Richtlinienfunktionen der Ansicht mit ihren Einschränkungen (eine Ansicht ohne Funktionen braucht  
> das Modul), die \[SiteAccessRules\] des Siteaccess, RequireUserLogin, die Einschränkung SiteAccess von user/login  
> und der Knoten oder das Objekt von content/view und content/edit. Es kommt zu den  
> Einstellungen PolicyList unten hinzu, die für das bleiben, was eine Adresse nicht sagen kann (ein lesbarer Knoten, eine  
> Richtlinie, nach der die Ansicht selbst fragt). Templates fragen dasselbe: fetch( 'user', 'can\_open', hash( 'uri', ... ) ).  
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

> Was aus einem Link des linken Menüs wird, den der Benutzer nicht öffnen kann:  
>   hidden    der Link entfällt (und ein Menü ohne übrig gebliebenen Link entfällt)  
>   disabled  sein Name wird ohne Link angezeigt (das Verhalten der Releases 4.x)  

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
