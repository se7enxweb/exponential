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

> Exponential configuration file for bookmarks of system pages.  
>
> NOTE: It is not recommended to edit this files directly, instead  
>       a file in override should be created for setting the  
>       values that is required for your site. Either create  
>       a file called settings/override/bookmark.ini.append or  
>       settings/override/bookmark.ini.append.php for more security  
>       in non-virtualhost modes (the .php file may already be present  
>       and can be used for this purpose).  
>
> A bookmark can point to a node or to a system page: the page of a module view with its parameters  
> (setup/info, role/view/3, audit/console/(user)/14, content/search/(text)/...). The address is stored without  
> the host and the siteaccess, so the same bookmark opens on every host and in every administration siteaccess  
> the user can use. Guide: doc/eng-us/features/6.0/bookmarks-of-system-pages.md  

| Block | Einstellungen |
|---|---|
| [ViewBookmarkSettings](ViewBookmarkSettings.md) | 4 |
| [BookmarkTypeSettings](BookmarkTypeSettings.md) | 2 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/bookmark.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
