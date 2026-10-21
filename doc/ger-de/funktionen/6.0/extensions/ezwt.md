# ezwt: die Website-Werkzeugleiste

Diese Seite ist für Designer, die einem öffentlichen Website-Design das Bearbeiten hinzufügen. `ezwt` („eZ Website
Toolbar LS“) ist die Bearbeitungsleiste, die ein angemeldeter Redakteur auf der öffentlichen Website sieht: bearbeiten,
verschieben, entfernen, hier anlegen, Unterelemente sortieren, den Zustand ändern, ein Dokument hochladen oder
exportieren. Sie benötigt `ezjscore`. [ezwebin](ezwebin.md), [ezflow](ezflow.md), [ezdemo](ezdemo.md) und das
[Simple-Theme](sevenx_themes_simple.md) bauen alle auf ihr auf.

## In Ihr Design einbauen

1. Umschließen Sie in `pagelayout.tpl` die Werkzeugleiste mit einem Cache-Block, dessen Schlüssel die Rollen des
   Benutzers sind, damit sie kein SQL hinzufügt:

```
{def $user_hash = concat( $current_user.role_id_list|implode( '_' ), '_', $current_user.limited_assignment_value_list|implode( '_' ) )}
{cache-block keys=array( $uri_string, $user_hash )}
    {include uri='design:parts/website_toolbar.tpl' current_node_id=$module_result.node_id}
{/cache-block}
```

2. Laden Sie `stylesheets/websitetoolbar.css` mit `ezdesign`.
3. Binden Sie in `content/edit.tpl` `design:parts/website_toolbar_edit.tpl` ein; im Template der Versionsansicht binden
   Sie den passenden Teil ein.
4. Melden Sie sich als Redakteur an und öffnen Sie eine Seite: Die Werkzeugleiste erscheint über dem Inhalt.

Das README der Extension enthält den vollständigen Text.

## Einstellungen

| Datei | Block | Schlüssel | Standard | Bedeutung |
|---|---|---|---|---|
| `websitetoolbar.ini` | `WebsiteToolbarSettings` | `ODFDisplayClasses[]` | documentation_page, blog, blog_post, folder, article, article_mainpage, article_subpage, event | Klassen, für die die Schaltflächen für Import und Export von OpenDocument gezeigt werden |
| `websitetoolbar.ini` | `WebsiteToolbarSettings` | `HideODFContainerClasses[]` | article | Container-Klassen ohne die OpenDocument-Schaltflächen |
| `websitetoolbar.ini` | `WebsiteToolbarSettings` | `HiddenContentClasses[]` | banner, common_ini_settings | Klassen, die die Werkzeugleiste nicht zum Anlegen anbietet |
| `websitetoolbar.ini` | `CustomTemplateSettings` | `CustomTemplateList[]`, `IncludeInView[<name>]` | object_states, link (Vollansicht) | Zusätzliche Templates der Werkzeugleiste |

## Was sich in den Releases von Exponential 6 geändert hat

| Version | Änderung |
|---|---|
| 6.0.3 | HTML5-Bereinigung der Templates der Werkzeugleiste (leere Elemente) und des Stylesheets (Herstellerpräfixe für `box-shadow`, `border-radius` und `transition` entfernt). |
| 6.0.5 | Jeder sichtbare Text ist ein Übersetzungstext, mit Deutsch. |
| 6.0.7 | Die Datums- und Datum/Zeit-Felder des Demo-Designs verwenden den Kalender von Exponential UI, `exp::datepicker`; die Extension benötigt `se7enxweb/expui ^1.0.0.1`. |
| 6.0.8 | **YUI entfernt.** Die Sortierseite (`websitetoolbar/sort`, Drag-and-drop von Unterelementen) läuft auf jQuery mit nativen Drag-Ereignissen: dieselben Prioritäten, dieselbe automatische Aktualisierung über `ezwt::updatepriority` (`ezjsc::jqueryio`). Siehe [Entfernung von YUI](../../../../eng-us/bc/6.0/yui-removal.md). |
| 6.0.9 | Kommandozeilenskripte, Cronjob-Teile und Modulansichten sind Klassen, die die Dateien aufrufen; die Copyright-Hinweise nennen zuerst 1998 - 2026 7x & Exponential Foundation ([Abstraktionen für Kommandozeile, Cronjobs und Ansichten](../../../../eng-us/bc/6.0/cli_cronjob_view_abstractions.md)). |

## Verwandte Seiten

- [ezwebin](ezwebin.md), [ezflow](ezflow.md), [ezodf](ezodf.md), [ezmultiupload](ezmultiupload.md)
- [Chronik](../../../../eng-us/history/extensions/ezwt.md) und [Versionshinweise](../../../../changelogs/extensions/ezwt.md)
- [Änderungsverzeichnis](../../../../eng-us/history/ledger/ezwt.md)
- [Verhaltensänderungen der Extensions](../../../../eng-us/bc/6.0/extensions-behaviour-changes.md)
- Monate: [2026-07](../../../../eng-us/history/extensions/months/2026-07.md), [2026-09](../../../../eng-us/history/extensions/months/2026-09.md), [2026-10](../../../../eng-us/history/extensions/months/2026-10.md) (alle Extensions)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
