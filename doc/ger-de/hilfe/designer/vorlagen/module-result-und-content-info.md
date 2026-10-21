---
title: Module result und content info
audience: designer, developer
category: templates
order: 660
summary: Was $module_result enthält, nachdem eine View gelaufen ist (content, path, title_path, uri, content_info, view_parameters, errorType und errorNumber), und wie das Pagelayout content_info nutzt, um Klasse, Knoten, Sektion, Ansichtsmodus und Sprache der Seite ohne Fetch zu kennen.
keywords: module_result, content_info, path, title_path, view_parameters, node_id, class_identifier, viewmode, errorType, errorNumber, Daten im Pagelayout, Fakten der Seite, pagelayout data, page facts
type: help
---
# Module result und content info

Diese Seite ist für Designer, die Pagelayouts und ihre Teile schreiben. `$module_result` ist alles, was die View eines
Moduls über die Seite meldet, die sie erzeugt hat. Die vollständige Liste der Schlüssel steht in
[Pagelayout-Variablen](../theme-design/pagelayout-variablen.md); diese Seite zeigt, wie Sie sie verwenden.

## Inhalt

1. [Inhaltsseiten und andere Seiten](#inhaltsseiten-und-andere-seiten)
2. [Fakten ohne Fetch](#fakten-ohne-fetch)
3. [Der Pfad](#der-pfad)
4. [Fehlerseiten](#fehlerseiten)

## Inhaltsseiten und andere Seiten

`content_info` gibt es nur, wenn die View eine Inhaltsansicht ist. Auf Seiten der Suche, der Benutzer, auf Fehlerseiten und
Seiten anderer Module ist es kein Array. Sichern Sie immer ab:

```tpl
{def $ci = cond( is_array( $module_result.content_info ), $module_result.content_info, false() )}
```

## Fakten ohne Fetch

| Bedarf | Lesen Sie |
|---|---|
| die Klasse der Seite | `$ci.class_identifier` |
| den Knoten, seinen Elternknoten, seine Tiefe | `$ci.node_id`, `$ci.parent_node_id`, `$ci.node_depth` |
| den Ansichtsmodus | `$ci.viewmode` (das Design media prüft auf `payload` und `layout_preview`) |
| die Sektion | `$ci.section_id`, `$ci.section_identifier` |
| den URL-Alias und den kanonischen | `$ci.url_alias`, `$ci.main_node_url_alias` |
| die angezeigte Sprache | `$ci.current_language` |
| den Seitenversatz | `$ci.offset` |
| was die View übergeben hat | `$ci.persistent_variable` ([Die persistent variable](die-persistent-variable.md)) |

Jeder davon ist kostenlos: keine Datenbankabfrage.

## Der Pfad

`$module_result.path` ist eine Liste von Hashes (`text`, `url`, `url_alias`, `node_id`), von der Spitze des Baums bis zur
aktuellen Seite, deren letzter Eintrag `url` auf false hat. Inhaltsansichten füllen sie aus dem Pfad des Knotens; andere
Module setzen ihren eigenen (zum Beispiel „Search“). `title_path` ist dasselbe für den `<title>`. Brotkrumen und Titel werden
daraus gebaut ([Brotkrumennavigation](brotkrumen.md)).

## Fehlerseiten

Scheitert eine View (kein Zugriff, nicht gefunden), stellt der Kernel ein Fehler-Template dar, und `$module_result` trägt
`errorType` (zum Beispiel `kernel`) und `errorNumber`. Die `pagelayout/head/title.tpl` des Designs media verwendet sie, um
Fehlerseiten einen lesbaren Titel zu geben ([Fehler-Templates](fehler-templates.md)).

## Verwandte Seiten

- [Pagelayout-Variablen](../theme-design/pagelayout-variablen.md)
- [Das Pagelayout](../theme-design/das-pagelayout.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
