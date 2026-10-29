---
title: bookmark.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von bookmark.ini.
keywords: Beispiele, Referenz, bookmark.ini, examples, reference
type: generated
generated: exp:help:examples
---
# bookmark.ini

Die ausgelieferten Einstellungen von `settings/bookmark.ini`: 2 Blöcke, 6 Einstellungen.

> Exponential-Konfigurationsdatei für Lesezeichen von Systemseiten.  
>
> HINWEIS: Es wird nicht empfohlen, diese Datei direkt zu bearbeiten; legen Sie stattdessen  
>       eine Datei in override an, um die Werte zu setzen, die Ihre Site braucht. Legen Sie  
>       dazu eine Datei settings/override/bookmark.ini.append an, oder  
>       settings/override/bookmark.ini.append.php für mehr Sicherheit  
>       ohne virtuelle Hosts (die .php-Datei ist vielleicht schon vorhanden  
>       und kann dafür benutzt werden).  
>
> Ein Lesezeichen kann auf einen Knoten oder auf eine Systemseite zeigen: die Seite einer Modulansicht mit ihren Parametern  
> (setup/info, role/view/3, audit/console/(user)/14, content/search?SearchText=...). Die Adresse wird ohne  
> Host und Siteaccess gespeichert, so öffnet dasselbe Lesezeichen auf jedem Host und in jedem Administrations-Siteaccess,  
> den der Benutzer benutzen darf. Anleitung: doc/eng-us/features/6.0/bookmarks-of-system-pages.md  

| Block | Einstellungen |
|---|---|
| [ViewBookmarkSettings](ViewBookmarkSettings.md) | 4 |
| [BookmarkTypeSettings](BookmarkTypeSettings.md) | 2 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/bookmark.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
