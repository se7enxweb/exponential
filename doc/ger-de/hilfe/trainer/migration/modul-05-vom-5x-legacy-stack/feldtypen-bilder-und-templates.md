---
title: "Lektion 5.3: Feldtypen, Bildvarianten und Twig-Templates"
audience: trainer, developer
category: training
order: 53
summary: Entwickler vergleichen die Feldtypen einer 5.x-Website mit den Datentypen von Exponential, entscheiden, was mit RichText geschieht, machen aus YAML-Bildvarianten Aliase in image.ini und schreiben Twig-Templates und View-Regeln als TPL-Templates und Override-Regeln neu. 60 Minuten.
keywords: migration zu exponential, feldtypen, datentypen, bildvarianten, twig zu tpl, migrating to exponential, 5.x, field types, datatypes, ezrichtext, DocBook, ezxmltext, image_variations, image aliases, image.ini, Twig to TPL, override.ini, location_view, attribute_view_gui
type: help
---
# Lektion 5.3: Feldtypen, Bildvarianten und Twig-Templates

Die Inhalte einer 5.x-Website werden übernommen, aber drei Dinge drumherum brauchen Arbeit: Feldtypen, die es nur auf
der Symfony-Seite gab, Bildvarianten, die die Bridge aus YAML übernahm, und die Twig-Templates, die oft das gesamte
öffentliche Frontend bildeten. Diese Lektion behandelt jedes davon mit den Tabellen aus Kapitel 15.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Migration zu Exponential](../README.md) |
| Modul | [Modul 5: Vom 5.x-Legacy-Stack](README.md) |
| Dauer | 60 Minuten (25 Präsentation, 35 Übung) |
| Niveau | Experten |
| Zielgruppe | Entwickler und Theme-Designer |
| Voraussetzungen | [Lektion 5.2](legacy-herausloesen-und-ini-schreiben.md); Grundlagen von Twig und TPL; Shell-Zugang zu einer Schulungsinstallation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Datentypen auflisten, die eine Website verwendet, und sie mit den Datentypen von Exponential vergleichen;
- pro `ezrichtext`-Attribut zwischen Neueingabe und skriptgesteuerter Umwandlung wählen;
- eine YAML-Bildvariante als Alias in `image.ini` schreiben;
- eine Twig-View-Regel als `override.ini`-Block und fünf Twig-Ausdrücke als TPL neu schreiben.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Kapitel 15.8](../../../../installation/15-migration-von-5x-legacy.md#158-feldtypen-und-datentypen),
[15.10](../../../../installation/15-migration-von-5x-legacy.md#1510-bildvariationen-und-bild-aliase) und
[15.12](../../../../installation/15-migration-von-5x-legacy.md#1512-templates-von-twig-zu-tpl).

1. **Dieselben Bezeichner** (4 Minuten). Die Symfony-Feldtypen von 5.x implementierten die Legacy-Datentypen über
   dieselben Zeilen, mit denselben Bezeichnern in `data_type_string`. Zeigen Sie die Tabelle aus 15.8: `ezxmltext`,
   `ezpage`, `ezsrrating`, `ezgmaplocation`, `ezcomcomments` existieren alle; `ezrichtext` hat keinen Datentyp.
2. **RichText zurück zu XmlText** (6 Minuten). Exponential liefert keinen Konverter von `ezrichtext` zu `ezxmltext`.
   Pro Attribut: die Inhalte in einem `ezxmltext`-Attribut einer Klassenkopie neu eingeben oder das gespeicherte
   DocBook mit einem Skript umwandeln und das Ergebnis über den Kernel speichern. Zeigen Sie die Tabelle der
   Elementzuordnung (`<para>` zu `<paragraph>`, `<emphasis role="strong">` zu `<strong>`, Links mit `ezlocation://`
   zu `node_id`). Prüfen Sie jeden umgewandelten Wert im Online-Editor.
3. **Bildvarianten** (5 Minuten). Die Bridge setzte `AliasList` auf genau die Namen der YAML-Varianten; Aliase, die nur
   in `image.ini` existierten, waren auf der alten Website nicht wirksam. Schreiben Sie jede Variante als INI-Alias mit
   `Reference` und `Filters[]`.
4. **Twig zu TPL** (10 Minuten). Die `.tpl`-Templates der Legacy-Designs funktionieren weiter; nur Twig muss neu
   geschrieben werden. Zeigen Sie die Tabelle aus 15.12: Eine `location_view`-Regel wird zu einem `override.ini`-Block;
   `ez_render_field` wird zu `attribute_view_gui`; `path(location)` wird zu `$node.url_alias|ezurl`; `|escape` wird zu
   `|wash`.

### Häufige Fragen

**Können wir während der Übergangszeit Twig aus TPL heraus rendern?** `ngsymfonytools` kann das, aber nur innerhalb
einer Symfony-Anwendung über die Bridge; einer eigenständigen Website hilft es nicht (15.12).

**Wohin kommen eigene Tags?** Sie bleiben Custom Tags von `ezxmltext`, mit jedem Typ aus `ezoe_attributes.ini` (15.8).

**Wie finden wir heraus, welches Template ein bestimmtes Markup geschrieben hat?** Mit Kommentaren zu Template-Pfaden;
siehe die weiterführenden Informationen.

### Stolperfallen

- **RichText ohne Prüfung umwandeln.** Öffnen Sie jedes umgewandelte Objekt im Online-Editor und veröffentlichen Sie
  es einmal.
- **`|wash` vergessen.** Twig hat standardmäßig maskiert; eine TPL-Ausgabe ohne `|wash` ist roh. Jede Ausgabe, die aus
  Inhalten oder von Besuchern stammt, wird mit `|wash` behandelt.
- **Annehmen, die alten `image.ini`-Aliase seien aktiv gewesen.** Auf der alten Website waren nur die YAML-Varianten
  wirksam.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation; die Teile 1 und 2 lesen nur, Teil 3 schreiben Sie in Ihre Notizen.

1. Listen Sie die Datentypen auf, die die Klassen Ihrer Installation verwenden:

   ```bash
   sqlite3 -readonly var/storage/sqlite3/exponential.db "SELECT data_type_string, COUNT(DISTINCT contentclass_id) FROM ezcontentclass_attribute WHERE version = 0 GROUP BY data_type_string ORDER BY data_type_string;"
   ```

2. Vergleichen Sie sie mit den Datentypen, die Exponential hat:

   ```bash
   php bin/php/console exp:ini get content.ini/DataTypeSettings/AvailableDataTypes
   ```

   Notieren Sie jeden Bezeichner aus Schritt 1, der in Schritt 2 fehlt (auf einer Schulungswebsite meist keiner:
   Datentypen von Erweiterungen werden von ihren Erweiterungen registriert).
3. Schreiben Sie diese YAML-Variante als INI für `image.ini`, nach dem Beispiel aus Kapitel 15.10:

   ```yaml
   image_variations:
       teaser:
           reference: ~
           filters:
               - { name: geometry/scaledownonly, params: [400, 300] }
   ```

4. Schreiben Sie diese View-Regel anhand von Kapitel 15.12 als Block in `override.ini.append.php` für den Siteaccess
   `site`: eine Vollansicht von Orten, deren Inhaltstyp `article` ist, mit dem Template `full/article.tpl`.
5. Schreiben Sie diese Twig-Ausdrücke in TPL neu:
   - `{{ ez_content_name(content) }}`
   - `{{ ez_render_field(content, 'body') }}`
   - `{{ path(location) }}`
   - `{% if not ez_is_field_empty(content, 'image') %}`
   - `{{ 'Read more'|trans }}`
6. Wandeln Sie dieses DocBook-Fragment mit der Zuordnungstabelle aus Kapitel 15.8 in `ezxmltext` um:
   `<para>See <emphasis role="strong">our offer</emphasis> on <link xlink:href="ezlocation://42">this page</link>.</para>`

## Erwartetes Ergebnis

- Schritte 1 und 2: Jeder verwendete Datentyp ist verfügbar.
- Schritt 3: `[AliasSettings] AliasList[]=teaser` und ein Block `[teaser]` mit `Reference=` und einer Zeile
  `Filters[]` für `geometry/scaledownonly=400;300`.
- Schritt 4: ein Block mit `Source=node/view/full.tpl`, `MatchFile=full/article.tpl`, `Subdir=templates` und
  `Match[class_identifier]=article`.
- Schritt 5: `{$node.name|wash}`, `{attribute_view_gui attribute=$node.data_map.body}`, `{$node.url_alias|ezurl}`,
  `{if $node.data_map.image.has_content}`, `{'Read more'|i18n('context')}`.
- Schritt 6: `<paragraph>See <strong>our offer</strong> on <link node_id="42">this page</link>.</paragraph>`.
- Der Trainer prüft die Schritte 4 und 6.

## Quiz

1. Warum brauchen die meisten 5.x-Feldtypen keine Umwandlung?
2. Welcher Datentyp hat keine Entsprechung und braucht pro Attribut eine Entscheidung?
   A. `ezxmltext` B. `ezpage` C. `ezrichtext` D. `ezsrrating`
3. Welche Bildvarianten waren auf der alten 5.x-Website tatsächlich wirksam?
4. Was ist das TPL-Gegenstück zu `|escape` in Twig?
5. Richtig oder falsch: Exponential liefert einen Konverter von `ezrichtext` zu `ezxmltext`.

<details>
<summary>Antworten</summary>

1. Sie implementierten die Legacy-Datentypen über dieselben Zeilen, mit denselben Bezeichnern
   ([Kapitel 15.8](../../../../installation/15-migration-von-5x-legacy.md#158-feldtypen-und-datentypen)).
2. **C** (Kapitel 15.8).
3. Genau die YAML-`image_variations`; die Bridge ersetzte die Legacy-Liste
   ([Kapitel 15.10](../../../../installation/15-migration-von-5x-legacy.md#1510-bildvariationen-und-bild-aliase)).
4. `|wash` ([Kapitel 15.12](../../../../installation/15-migration-von-5x-legacy.md#1512-templates-von-twig-zu-tpl)).
5. **Falsch.** Geben Sie die Inhalte neu ein oder wandeln Sie sie mit einem eigenen Skript um (Kapitel 15.8).

</details>

## Weiterführende Informationen

- [Kapitel 15.8: Feldtypen gegenüber Datentypen](../../../../installation/15-migration-von-5x-legacy.md#158-feldtypen-und-datentypen):
  die Zuordnungstabelle für DocBook.
- [Kapitel 17.5: Templates, TPL und Twig](../../../../installation/17-migrationsreferenz.md#175-templates-tpl-und-twig):
  die vollständige Nachschlagetabelle.
- [Templates und Design](../../../../anleitungen/templates-und-design.md): wie Templates gefunden, überschrieben und
  untersucht werden.

## Verwandte Seiten

- [Modul 5: Vom 5.x-Legacy-Stack](README.md)
- [Migration zu Exponential: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
