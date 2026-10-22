---
title: "Templates überschreiben"
audience: builder, designer, developer
category: books
order: 440
summary: Wie das Template-System ein Template findet (zuerst die Override-Regeln, dann die Designs in ihrer Reihenfolge), die Syntax von override.ini.append.php und die Abgleichsschlüssel.
keywords: Override, Template-Override, Kaskade, Abgleichsschlüssel, Klasse, Knoten, Sektion, Tiefe, Design, Buch Learning, override, override.ini, template override, cascading, Source, MatchFile, Match, class_identifier, node, section, depth, design, Learning book
type: help
---
# Templates überschreiben

Das Template-System wählt eine Datei in zwei Schritten. Das ist der Gedanke des ganzen Kapitels, und er hat sich nicht
geändert.

1. **Die Override-Regeln** werden zuerst betrachtet (`override.ini`). Passt eine Regel mit ihren Bedingungen auf das
   angeforderte Template, wird die Template-Datei verwendet, die sie nennt.
2. Passt keine Regel, wird die Datei in den **Designs** gesucht, in ihrer Reihenfolge: das Design des Siteaccess, die
   Designs der Erweiterungen, dann `standard`. Die erste gefundene Datei wird verwendet. (Das ist die Kaskade.)

Sie ändern ein Template also, ohne das Original anzufassen: Legen Sie ein spezifischeres in Ihr Design oder fügen Sie
eine Regel hinzu, die eine Klasse, eine Sektion oder einen Knoten an eine eigene Datei schickt.

## Die Syntax

Eine Regel ist ein Block in einer `override.ini.append.php` Ihres Siteaccess oder einer Erweiterung:

```ini
[full_article]
Source=node/view/full.tpl
MatchFile=full/article.tpl
Subdir=templates
Match[class_identifier]=article
```

- Der Blockname (`full_article`) ist ein beliebiger eindeutiger Name.
- `Source` ist das Template, das angefordert wird.
- `MatchFile` ist die Datei, die stattdessen verwendet wird, unterhalb von `<design>/override/templates/` eines Designs, zum Beispiel `extension/mysite_design/design/mysite/override/templates/full/article.tpl` (bei `Subdir=templates`).
- `Match[...]` sind die Bedingungen. Alle müssen erfüllt sein.

Die Schlüssel des Buchs waren die Klasse, die Sektion, der Knoten, das Objekt und die Tiefe. Heute gibt es mehr, die in
[Override-Bedingungen](../erweiterungsentwicklung/teil-2-builder/override-bedingungen.md) aufgeführt sind:
`class_identifier` (verwenden Sie diesen statt des numerischen `class`), `class_group`, `node`, `object`, `parent_node`,
`parent_class_identifier`, `section_identifier` (statt `section`), `state_identifier`, `depth`, `url_alias`, `layout` und
weitere. Wo mehrere Regeln passen, entscheidet eine `Priority`.

## Ein ausgearbeitetes Beispiel

Die Vollansicht jedes Artikels soll sich von der Standardansicht unterscheiden:

1. Kopieren Sie `design/standard/templates/node/view/full.tpl` nach `override/templates/full/article.tpl` in Ihrem Design.
2. Fügen Sie den Block `[full_article]` von oben hinzu.
3. Leeren Sie den Override-Cache: Eine neue Regel oder eine neue Datei braucht `php bin/php/ezcache.php --clear-id=template-override --allow-root-user`
   (siehe [Welchen Cache leere ich?](../handbuch/welchen-cache-leere-ich.md)).
4. Laden Sie eine Artikelseite. `TemplateSettings ShowUsedTemplates=enabled` (nur für Sie, auf einer
   Entwicklungsinstallation) listet das verwendete Template auf und den Override, der es ausgewählt hat.

**Design > Template editor** in der Administration listet die Regeln in der Reihenfolge, in der sie versucht werden, und
lässt Sie sie umordnen.

Mehr: [Template-Overrides: die Grundlagen](../erweiterungsentwicklung/teil-2-builder/grundlagen-der-template-overrides.md) und
[Ressourcen und Overrides](../template-sprache/template-ressourcen-und-overrides.md).

Weiter: [Häufige Aufgaben mit Templates](haeufige-aufgaben-mit-templates.md).

## Quelle und Lizenz

Bearbeitet nach Kapitel 3, „Displaying Content with eZ publish Templates“, von *Learning eZ publish: Building Content Management Solutions* von Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding und Ben Pirt (zusätzliches Material von Bruce Morrison), zweite Auflage, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x und Graham
Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren Version, ohne
unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: umgestellt und gekürzt; Syntax, Operatoren, Einstellungen und Beispiele auf Exponential 6 aktualisiert (foreach, aktuelle Einstellungen, kein Inline-Stil und kein Inline-Skript).
