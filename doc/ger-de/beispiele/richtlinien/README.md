---
title: Richtlinienfunktionen und Einschränkungen
audience: developer, admin
category: examples
order: 0
summary: Die Richtlinienfunktionen und Einschränkungen jedes Moduls, mit den Ansichten, die sie prüfen.
keywords: Beispiele, Referenz, Richtlinienfunktionen und Einschränkungen, examples, reference
type: generated
generated: exp:help:examples
---
# Richtlinienfunktionen und Einschränkungen

Die Richtlinienfunktionen jedes Moduls und die Einschränkungen, die jede tragen kann, aus `$FunctionList` in seiner `module.php`, mit den Ansichten, die sie prüfen, und Beispielen in PHP und Templates.

| Modul | Richtlinienfunktionen |
|---|---|
| [apikey](apikey.md) | `create` |
| [audit](audit.md) | `manage`, `read` |
| [class](class.md) | (keine: `class/*`) |
| [collaboration](collaboration.md) | (keine: `collaboration/*`) |
| [content](content.md) | `bookmark`, `cleantrash`, `create`, `dashboard`, `diff`, `edit`, `hide`, `jobs`, `manage_locations`, `move`, `pdf`, `pendinglist`, `publish`, `publish_without_notification`, `read`, `remove`, `restore`, `reverserelatedlist`, `tipafriend`, `translate`, `translations`, `urltranslator`, `versionread`, `versionremove`, `view_embed`, `view_system_url` |
| [contentcheck](contentcheck.md) | `brokenrelations`, `fixfiles`, `permissions`, `quarantine`, `storage`, `trash`, `unreferenced` |
| [contentfinder](contentfinder.md) | `delete`, `search` |
| [csp](csp.md) | (keine: `csp/*`) |
| [datatable](datatable.md) | `connections`, `manage`, `read`, `sync` |
| [error](error.md) | (keine: `error/*`) |
| [exphelp](exphelp.md) | `context`, `feedback`, `read` |
| [expimageeditor](expimageeditor.md) | `edit` |
| [ezinfo](ezinfo.md) | `read` |
| [ezjscore](ezjscore.md) | `call`, `run` |
| [ezoe](ezoe.md) | `browse`, `disable_editor`, `editor`, `relations`, `search` |
| [infocollector](infocollector.md) | `read` |
| [layout](layout.md) | (keine: `layout/*`) |
| [mailpreferences](mailpreferences.md) | `administrate`, `export` |
| [notification](notification.md) | `administrate`, `use` |
| [oauth](oauth.md) | `authorize` |
| [oauthadmin](oauthadmin.md) | (keine: `oauthadmin/*`) |
| [package](package.md) | `create`, `edit`, `export`, `export_extension`, `import`, `install`, `list`, `read`, `remove` |
| [pdf](pdf.md) | `create`, `edit` |
| [role](role.md) | (keine: `role/*`) |
| [rss](rss.md) | `edit`, `feed` |
| [search](search.md) | (keine: `search/*`) |
| [secrets](secrets.md) | `manage` |
| [section](section.md) | `assign`, `edit`, `view` |
| [settings](settings.md) | (keine: `settings/*`) |
| [setup](setup.md) | `administrate`, `install`, `logs`, `managecache`, `managecronjobs`, `managelogs`, `preload`, `setup`, `system_info`, `unmasklogs` |
| [shop](shop.md) | `administrate`, `buy`, `edit_status`, `setstatus`, `setup` |
| [state](state.md) | `administrate`, `assign` |
| [switchlanguage](switchlanguage.md) | (keine: `switchlanguage/*`) |
| [trigger](trigger.md) | (keine: `trigger/*`) |
| [uiswitch](uiswitch.md) | `design`, `language` |
| [url](url.md) | (keine: `url/*`) |
| [user](user.md) | `activation`, `deactivate`, `hygiene`, `login`, `password`, `preferences`, `register`, `selfedit`, `sessions` |
| [visual](visual.md) | (keine: `visual/*`) |
| [workflow](workflow.md) | (keine: `workflow/*`) |

- [Alle Beispiele](../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/classes/ezrole.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
