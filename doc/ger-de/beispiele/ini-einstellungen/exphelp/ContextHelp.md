---
title: exphelp.ini [ContextHelp]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ContextHelp] in exphelp.ini: Providers, CheckPages, MenuTopics, MenuLinks, Map"
keywords: Einstellung, exphelp.ini, ContextHelp, INI, setting, Providers, CheckPages, MenuTopics, MenuLinks, Map
type: generated
generated: exp:help:examples
---
# exphelp.ini [ContextHelp]

Der Block `[ContextHelp]` von `settings/exphelp.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/exphelp.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/exphelp.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Providers

> Kontexthilfe: Der Link „?“ im Kopf der Administration öffnet die Seite, die „Hilfe zu dieser Seite“ beantwortet  
> (expHelpContextRegistry; doc/eng-us/features/6.0/help-system.md, "Context help"). Ein Eintrag pro Schlüssel:  
>   Map\[<module>/<view>\]=<page id>\[#<heading anchor>\]  
>   Map\[<module>\]=...                     jede Ansicht des Moduls ohne eigenen Eintrag  
>   Map\[<design>:<module>/<view>\]=...     nur in diesem Design (admin4l, editor, admin, adminui ...); gewinnt gegen den  
>   Map\[<design>:<module>\]=...            gleichen Schlüssel ohne Design  
> Eine Seiten-ID ist der Pfad unter doc/eng-us/, in jeder Sprache gleich (ext/<extension>/<path> für das doc-Verzeichnis einer Erweiterung, \[IndexSettings\]  
> ExtensionDocDirs\[\]); der Anker ist der Anker der Überschrift, wie ihn der Viewer bildet. Die Suche: Providers\[\], dann  
> design:module/view, module/view, design:module, module, dann das Dashboard der Hilfe. Ein Eintrag, dessen Seite die Hilfe  
> nicht ausliefert (noch nicht geschrieben, nicht von git versioniert, außerhalb von doc/), wird übersprungen, sodass der nächste Schlüssel antwortet.  
> Erweiterungen registrieren die Hilfe ihrer eigenen Module auf dieselbe Weise in ihrer settings/exphelp.ini.append.php.  
> Klassen, die expHelpContextProviderInterface implementieren, vor der Zuordnung gefragt, in dieser Reihenfolge  
> Als Inhalt gepflegte Hilfethemen antworten zuerst für die Schlüssel aus \[ContentSettings\] ContextHelpIds\[\]  

Standard: eine Liste mit 1: `expHelpContentProvider`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContextHelp]
# ein leeres "Providers[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Providers[]
Providers[]=expHelpContentProvider
*/ ?>
```

## CheckPages

> enabled: Ein Eintrag zählt nur, wenn der Index der Hilfe seine Seite ausliefert; disabled: nur Syntaxprüfungen (schneller)  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContextHelp]
CheckPages=enabled
*/ ?>
```

## MenuTopics

> Das Hilfe-Menü im Kopf (die Schaltfläche „?“): wie viele Themen zur Seite es auflistet (die anderen Einträge  
> des Moduls in dieser Zuordnung, dann \[FeaturedSettings\] PageList\[\]), und seine Schnelllinks, Schlüssel => Seiten-ID\[#Anker\] oder eine  
> exphelp-Ansicht. Die Schlüssel des Kernels sind im Template des Menüs benannt; der eigene Schlüssel einer Erweiterung zeigt den Titel der Seite.  

Standard: `6`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContextHelp]
MenuTopics=6
*/ ?>
```

## MenuLinks

Standard: ein Hash mit 5: `getting_started=help/getting-started.md`, `keyboard_shortcuts=help/keyboard-shortcuts.md`, `whats_new=changelogs/6.0/6.0.15.md`, `report=exphelp/report`, `git=install/03-getting-the-code.md#b-git-clone-and-composer-install`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContextHelp]
# ein leeres "MenuLinks[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
MenuLinks[]
MenuLinks[getting_started]=help/getting-started.md
MenuLinks[keyboard_shortcuts]=help/keyboard-shortcuts.md
MenuLinks[whats_new]=changelogs/6.0/6.0.15.md
MenuLinks[report]=exphelp/report
MenuLinks[git]=install/03-getting-the-code.md#b-git-clone-and-composer-install
*/ ?>
```

## Map

> Inhalte  
> Inhaltsmodell  
> Benutzer und Zugriff  
> Setup  
> admin4l: die mit Admin-Layouts gezeichnete Administration  
> Der Siteaccess editor (SiteDesign=editor): die für Redakteure geschriebenen Seiten, vor den Anleitungen für Administratoren  
> Module von Erweiterungen, die ohne eigene Hilfeeinträge ausgeliefert werden (die exphelp.ini.append.php einer Erweiterung gewinnt gegen  
> diese: Sie wird nach dieser Datei gelesen)  
> Der Datenbankmanager von sevenx\_dse (Modul dse)  

Standard: ein Hash mit 131: `content=guides/content-model-and-editing.md`, `content/dashboard=help/admin-tour.md`, `content/view=help/editing-content.md`, `content/edit=help/editing-content.md`, `content/multiedit=guides/content-model-and-editing.md#6-edit-many-items-in-one-form-multi-node-edit`, `content/versionview=guides/content-history.md`, `content/history=guides/content-history.md`, `content/draft=guides/drafts-and-pending.md#1-my-drafts`, `content/pendinglist=guides/drafts-and-pending.md#2-my-pending-items`, `content/trash=guides/content-model-and-editing.md#7-the-trash`, `content/restore=guides/content-model-and-editing.md#7-the-trash`, `content/bookmark=guides/bookmarks.md` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContextHelp]
# ein leeres "Map[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Map[]
Map[content]=guides/content-model-and-editing.md
Map[content/dashboard]=help/admin-tour.md
Map[content/view]=help/editing-content.md
Map[content/edit]=help/editing-content.md
Map[content/multiedit]=guides/content-model-and-editing.md#6-edit-many-items-in-one-form-multi-node-edit
Map[content/versionview]=guides/content-history.md
Map[content/history]=guides/content-history.md
Map[content/draft]=guides/drafts-and-pending.md#1-my-drafts
Map[content/pendinglist]=guides/drafts-and-pending.md#2-my-pending-items
Map[content/trash]=guides/content-model-and-editing.md#7-the-trash
Map[content/restore]=guides/content-model-and-editing.md#7-the-trash
Map[content/bookmark]=guides/bookmarks.md
Map[content/search]=help/searching.md
Map[content/advancedsearch]=help/searching.md#the-advanced-search
Map[content/upload]=help/media-and-uploads.md
Map[content/translations]=guides/content-languages.md
Map[content/translation]=guides/content-languages.md
Map[content/urlalias]=guides/urls-and-aliases.md
Map[content/urltranslator]=guides/urls-and-aliases.md#3-global-url-aliases
Map[content/urlwildcards]=guides/urls-and-aliases.md#4-url-wildcards
# ... 111 weitere in der ausgelieferten Datei
*/ ?>
```

## Siehe auch

- [Alle Blöcke von exphelp.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/exphelp.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
