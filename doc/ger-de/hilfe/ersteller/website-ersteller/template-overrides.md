---
title: Template-Overrides
audience: website-creator, developer
category: website-creation
order: 120
summary: Geben Sie einer Klasse, einer Sektion, einem Knoten oder einem Ansichtsmodus mit einer Override-Regel ein eigenes Template - per Klickpfad im Template-Editor oder als Dateien in Ihrer Design-Erweiterung - und die Bedingungsschlüssel, die Sie verwenden können.
keywords: Override, Template-Override, Bedingung, Template-Editor, Vollansicht, Zeilenansicht, Website-Ersteller, override, override.ini, template override, Match, class_identifier, section, node, viewmode, MatchFile, Source, template editor, full view, line view, website creator
type: help
---
# Template-Overrides

Diese Seite richtet sich an Website-Ersteller, die ein Template für eine bestimmte Art von Seite wollen: Artikel sehen
auf eine Weise aus, Produkte auf eine andere, die Startseite auf eine dritte. Ein **Override** ist eine Regel in
`override.ini`, die sagt: "Für Seiten, die diese Bedingungen erfüllen, verwende diese Datei statt des Standard-Templates."
Die Schritt-für-Schritt-Anleitung ist
[4. Eine Änderung mit einem Override vornehmen](../../../anleitungen/templates-und-design.md#4-eine-änderung-mit-einem-override-vornehmen);
diese Seite ist das Nachschlagewerk für Website-Ersteller.

## Inhalt

1. [Die Bestandteile einer Regel](#die-bestandteile-einer-regel)
2. [Bedingungsschlüssel](#bedingungsschlüssel)
3. [Ein Voll- und ein Zeilen-Template je Klasse](#ein-voll--und-ein-zeilen-template-je-klasse)
4. [Klickpfad oder Dateien](#klickpfad-oder-dateien)
5. [Reihenfolge: welche Regel gewinnt](#reihenfolge-welche-regel-gewinnt)
6. [Checkliste](#checkliste)

## Die Bestandteile einer Regel

```ini
# settings/siteaccess/<site>/override.ini.append.php
[full_article]
Source=node/view/full.tpl
MatchFile=full/article.tpl
Subdir=templates
Match[class_identifier]=article
```

| Schlüssel | Bedeutung |
|---|---|
| `[full_article]` | der Blockname; ein beliebiger eindeutiger Name |
| `Source` | das Template, das ersetzt wird |
| `MatchFile` | Ihre Datei, relativ zu `design/<design>/override/templates/` |
| `Subdir` | immer `templates` |
| `Match[...]` | die Bedingungen; alle müssen erfüllt sein |
| `Priority` | optional; Regeln mit einer Priorität kommen zuerst, die niedrigste Zahl zuerst |

## Bedingungsschlüssel

Für Knotenansichten bietet der Kernel diese Schlüssel an (`kernel/classes/eznodeviewfunctions.php`):

| Schlüssel | Beispiel | Verwenden Sie ihn für |
|---|---|---|
| `class_identifier` | `article` | ein Template je Klasse (am häufigsten) |
| `class` | `2` | dasselbe über die Klassen-ID |
| `node` | `2` | eine Platzierung (die Startseite) |
| `object` | `1234` | ein Objekt, an allen seinen Platzierungen |
| `parent_node` | `58` | alles direkt in einem Ordner |
| `section` / `section_identifier` | `members` | alles in einer Sektion |
| `state` / `state_identifier` | ein Objektzustand | Inhalte in einer Workflow-Stufe |
| `depth` | `2` | Seiten der obersten Ebene |
| `url_alias` | `About-us` | eine Adresse |
| `remote_id`, `node_remote_id` | | durch Importe angelegte Inhalte, stabil über Installationen hinweg |
| `viewmode` | `line` | ein Ansichtsmodus, für Templates, die nicht über `Source` angesprochen werden |
| `class_group` | | alle Klassen einer Klassengruppe |
| `navigation_part_identifier` | | Navigationsbereiche der Administration |

Bevorzugen Sie `class_identifier`, `section_identifier`, `state_identifier` und `node_remote_id` gegenüber IDs:
Bezeichner bedeuten auf jeder Installation dasselbe, IDs nicht.

## Ein Voll- und ein Zeilen-Template je Klasse

Die meisten Websites brauchen zwei Templates je Klasse: **full** (die Seite des Elements) und **line** (das Element in
einer Liste).

```ini
[full_event]
Source=node/view/full.tpl
MatchFile=full/event.tpl
Subdir=templates
Match[class_identifier]=event

[line_event]
Source=node/view/line.tpl
MatchFile=line/event.tpl
Subdir=templates
Match[class_identifier]=event
```

`design/<design>/override/templates/full/event.tpl`:

```text
<article class="event">
  <h1>{$node.name|wash}</h1>
  {attribute_view_gui attribute=$node.data_map.start_date}
  {attribute_view_gui attribute=$node.data_map.description}
</article>
```

`design/<design>/override/templates/line/event.tpl`:

```text
<li><a href={$node.url_alias|ezurl}>{$node.name|wash}</a></li>
```

Das Voll-Template eines Ordners zeigt seine Unterelemente mit `{node_view_gui content_node=$child view='line'}`, sodass
der Zeilen-Override überall greift, wo Veranstaltungen aufgelistet werden.

## Klickpfad oder Dateien

| | Template-Editor (Klickpfad) | Dateien in Ihrer Design-Erweiterung |
|---|---|---|
| Wo | **Design > Template-Editor** | `override/templates/` und `settings/siteaccess/<site>/override.ini.append.php` |
| Schreibt | die Regel in die `override.ini.append.php` des Siteaccess und die Template-Datei | was Sie schreiben |
| Gut für | Ausprobieren, einmalige Änderungen | alles, was Sie behalten: Es liegt in Git und wird mit der Erweiterung ausgeliefert |
| Reihenfolge | Drag-and-drop | die Reihenfolge der Blöcke oder `Priority` |

Die Funktionen des Template-Editors (Standardkopie, Bedingung auf ein einzelnes Objekt, Entfernen mit Aufräumen) sind
unter [Template-Editor](../../../funktionen/6.0/template-editor-ueberschreibungen.md) beschrieben.

## Reihenfolge: welche Regel gewinnt

Wenn mehrere Regeln passen, gewinnt die erste. Regeln mit einer `Priority` kommen vor Regeln ohne; unter diesen die
niedrigste Zahl zuerst. Regeln in Erweiterungen werden nach der Reihenfolge der Erweiterungen eingeordnet. Stellen Sie
spezifische Regeln (ein Knoten) vor allgemeine (eine Klasse).
[Template-Editor: neu anordnen](../../../funktionen/6.0/template-editor-ueberschreibungen.md#per-drag-and-drop-umsortieren-september-2026),
[Ladereihenfolge der Erweiterungen](../../../funktionen/6.0/ladereihenfolge-der-extensions.md).

## Checkliste

- [ ] Das Template besteht `php bin/php/eztemplatecheck.php -s<site> <file>`.
- [ ] Nach einer neuen Regel oder Datei: `--clear-id=template-override`, `--clear-tag=template` und `--clear-tag=content`.
- [ ] Jeder vom Benutzer eingegebene Wert wird mit `|wash` ausgegeben.
- [ ] Keine Inline-Blöcke `<style>` oder `<script>`: Stylesheets und Skripte laufen über die Listen des Designs
      ([Stylesheets und Skripte](stylesheets-und-skripte.md)).
- [ ] Mit eingeschalteten Kommentaren zu Template-Pfaden zeigt der Seitenquelltext Ihre Datei für die Seite.

## Verwandte Seiten

- [Designs und die Fallback-Kette](designs-und-die-fallback-kette.md)
- [Das Pagelayout](das-pagelayout.md)
- [Templates und Design](../../../anleitungen/templates-und-design.md)
- [Modul-Override in Erweiterungen](../../../funktionen/6.0/modulueberschreibung-durch-extensions.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
