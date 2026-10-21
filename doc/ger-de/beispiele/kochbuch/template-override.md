---
title: Ein Template-Override
audience: developer
category: examples
order: 30
summary: Der Vollansicht einer Inhaltsklasse mit einer Override-Regel in override.ini.append.php ein eigenes Template geben, abgelegt im Design einer Erweiterung, mit den Bedingungen, der Priorität und den Cache-Leerungen, die dafür nötig sind.
keywords: Template-Override, Override-Regel, Vollansicht, Inhaltsklasse, Design, Priorität, Cache, template override, override.ini, override.ini.append.php, Source, MatchFile, Subdir, Match, class_identifier, node, viewmode, Priority, design, DesignExtensions, template-override cache, eztemplatecheck
type: example
---
# Ein Template-Override

Ein Override ersetzt ein Template durch ein anderes, wenn Bedingungen erfüllt sind: *die Vollansicht einer Notiz*,
*die Zeilenansicht unterhalb von Knoten 42*, *alles in Sektion 3*. Sie geben der Klasse **Note** (aus
[Eine Inhaltsklasse anlegen](inhaltsklasse.md)) eine eigene Vollansicht, abgelegt in der Beispielerweiterung, damit sie
unter Versionskontrolle steht und ein Upgrade übersteht.

Voraussetzungen: das Erweiterungsgerüst aus [Ein Erweiterungsgerüst](erweiterungsgeruest.md), aktiviert, mit
`DesignExtensions[]=expexample` in seiner `design.ini.append.php`; der Name des Designs Ihrer Website (hier `mysite`,
der Siteaccess `site`).

## 1. Wie das Template ausgewählt wird

Für `node/view/full.tpl` sammelt der Kernel Zuordnungsschlüssel aus der angezeigten Seite und probiert die
Override-Regeln des Siteaccess der Reihe nach durch. Die Schlüssel, die eine Knotenansicht bietet:

| Schlüssel | Wert |
|---|---|
| `object`, `node`, `parent_node` | IDs des Objekts, des Knotens und seines Elternknotens |
| `class_identifier`, `parent_class_identifier` | die Klasse des Objekts und die des Objekts des Elternknotens |
| `viewmode` | `full`, `line`, `embed` ... |
| `section`, `state`, `state_identifier` | die Sektions-ID, die IDs der Objektzustände und die Bezeichner `<group>/<state>` |
| `remote_id`, `node_remote_id`, `parent_node_remote_id` | Remote-IDs |
| `depth`, `url_alias`, `class_group`, `navigation_part_identifier` | der Rest |

Jedes `Match[...]` einer Regel muss zutreffen. Die erste passende Regel gewinnt; Regeln mit `Priority` kommen zuerst,
die niedrigste Zahl zuerst.

## 2. Das Template

Legen Sie es im Override-Verzeichnis des Designs an, innerhalb der Erweiterung:

```bash
mkdir -p extension/expexample/design/mysite/override/templates/full
```

`extension/expexample/design/mysite/override/templates/full/note.tpl`:

```tpl
{* The full view of a Note. $node is the node shown. *}
<article class="content-view-full class-note">
    <h1>{$node.data_map.title.content|wash}</h1>

    {if $node.data_map.body.has_content}
        <div class="note-body">
            {attribute_view_gui attribute=$node.data_map.body}
        </div>
    {/if}

    <p class="note-meta">
        {'Last changed %date'|i18n( 'extension/expexample', '', hash( '%date', $node.object.modified|l10n( 'shortdatetime' ) ) )}
    </p>
</article>
```

Kein `<style>`-Block und kein `style=""`-Attribut: nur Klassen, gestaltet in den Stylesheets des Designs (ein
Template der Administration muss den Gestaltungsregeln der Administration folgen, und die Content Security Policy der
Administration lehnt Inline-Skripte ab).

## 3. Die Regel

`extension/expexample/settings/siteaccess/site/override.ini.append.php` enthält Regeln nur für den Siteaccess `site`,
was eine Website-Erweiterung will. Für jeden Siteaccess verwenden Sie
`extension/expexample/settings/override.ini.append.php`; für eine Regel einer einzelnen Installation, die nicht in
einer Erweiterung steht, `settings/siteaccess/site/override.ini.append.php`.

```ini
<?php /* #?ini charset="utf-8"?

[full_note]
Source=node/view/full.tpl
MatchFile=full/note.tpl
Subdir=templates
Match[class_identifier]=note

*/ ?>
```

- Der Blockname (`full_note`) muss unter allen Regeln des Siteaccess eindeutig sein; eine später gelesene Datei mit
  demselben Blocknamen ersetzt die Regel.
- `Source` ist das ersetzte Template, `MatchFile` das neue relativ zu `override/templates/`, `Subdir` ist immer
  `templates`.
- Grenzen Sie sie mit weiteren Bedingungen ein: `Match[section]=1` (und die Klasse ist Note), `Match[parent_node]=42`.
- Eine zweite Regel für die Zeilenansicht: `Source=node/view/line.tpl`, `MatchFile=line/note.tpl`.

Die Einstellungen einer Erweiterung je Siteaccess in `settings/siteaccess/<name>/` werden gelesen, wenn die
Erweiterung in `ActiveExtensions[]` steht; siehe
[Einstellungen je Website in Erweiterungen](../../funktionen/6.0/ini-ueberschreibungen-fuer-mehrere-websites.md).

## 4. Die Caches leeren und prüfen

Ein neuer Template-Pfad oder eine neue Regel erfordert einmal das Leeren des Override- und des Template-Caches:

```bash
php bin/php/eztemplatecheck.php -ssite extension/expexample/design/mysite/override/templates/full/note.tpl --allow-root-user
php bin/php/ezcache.php --clear-id=template-override --allow-root-user
php bin/php/ezcache.php --clear-tag=template --allow-root-user
php bin/php/ezcache.php --clear-tag=content --allow-root-user
```

Erwartet vom ersten Befehl: `Template file valid: ...`. Öffnen Sie dann eine Notiz auf der Website. Mit
`site.ini [TemplateSettings] ShowUsedTemplates=enabled` (oder den Kommentaren mit den Template-Pfaden) nennt die Liste
der verwendeten Templates `full/note.tpl` für `node/view/full.tpl`. Spätere Änderungen an derselben Datei brauchen nur
`--clear-tag=template` (oder gar nichts, solange der Template-Cache ausgeschaltet ist). Unter Velocity leeren Sie
außerdem dessen Response-Cache: `./console exp:velocity cache clear`.

Für diese Seite geprüft (2026-10-18): `bin/php/eztemplatecheck.php` meldet das Template als gültig (Syntax); die
Zuordnungsschlüssel sind die, die `eZNodeviewfunctions::generateNodeViewData()` setzt.

## 5. Dasselbe ohne Dateien

**Design > Template-Editor** legt dieselbe Regel und Datei für Sie an: Öffnen Sie `node/view/full.tpl`, klicken Sie
auf **Neues Überschreib-Template** und wählen Sie die Klasse. Der Editor schreibt
`settings/siteaccess/<siteaccess>/override.ini.append.php` (nicht in Ihre Erweiterung) und legt vor dem Schreiben eine
Kopie an. Siehe [Template-Editor](../../funktionen/6.0/template-editor-ueberschreibungen.md).

## Häufige Fehler

| Symptom | Ursache |
|---|---|
| Die Seite zeigt das alte Template | der Cache `template-override`; oder eine andere, früher in der Reihenfolge stehende Regel passt zuerst |
| Die Seite ist leer, mit dem Layout drumherum | `MatchFile` nennt eine Datei, die nicht in `override/templates/` eines Designs liegt, das der Siteaccess verwendet |
| Die Regel wirkt für jeden Siteaccess, auch die Administration | sie steht in `settings/override.ini.append.php` der Erweiterung statt in `settings/siteaccess/<name>/` |
| Die Regeln zweier Erweiterungen kommen sich in die Quere | derselbe Blockname; oder die Reihenfolge von `ActiveExtensions[]` entscheidet: [Ladereihenfolge der Erweiterungen](../../funktionen/6.0/ladereihenfolge-der-extensions.md) |

## Abgeleitet aus

- `kernel/classes/eznodeviewfunctions.php` (die Zuordnungsschlüssel einer Knotenansicht)
- `doc/eng-us/guides/templates-and-design.md`, Abschnitte 4 und 5
- `design/standard/templates/node/view/full.tpl`

## Verwandte Seiten

- [Kochbuch](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md), [Template-Editor](../../funktionen/6.0/template-editor-ueberschreibungen.md)
- [Template-Operatoren](../template-operatoren/README.md) und [Fetch-Funktionen](../fetch-funktionen/README.md)
- [Ein eigener Template-Operator](eigener-template-operator.md), [Caching](caching.md)
- Die Programmierregeln und der Musterkatalog: [die Standards](../../standards/README.md)
- [Freie Dokumentation](../../hilfe/freie-dokumentation.md): Diese Seite darf frei kopiert, übersetzt und verbessert
  werden.
