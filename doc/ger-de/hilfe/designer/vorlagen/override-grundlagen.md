---
title: Override-Grundlagen
audience: designer, developer
category: templates
order: 570
summary: Wie Template-Overrides funktionieren - eine Regel in override.ini nennt ein Quell-Template, das ersetzende Template und die Bedingungen; wo Regeldateien liegen (Siteaccess, Erweiterung, Siteaccess einer Erweiterung); wie der Kernel den Ersatz unter override/templates findet; der visuelle Template-Editor; und die Caches.
keywords: Override, override.ini, Template-Override, Source, MatchFile, Subdir, Match, Priority, Override-Regel, override/templates, Template-Editor, Override-Cache, override, template override, override rule, template editor, override cache
featured: true
type: help
---
# Override-Grundlagen

Diese Seite ist für Designer, die für manche Inhalte ein anderes Template wollen: Artikel, eine besondere Seite, alles in einer
Sektion. Ein **Override** ist eine Regel: „Wird das Template `Source` für Inhalte verwendet, die diese Bedingungen erfüllen,
verwende stattdessen `MatchFile`.“

## Inhalt

1. [Eine Regel](#eine-regel)
2. [Wo Regeln liegen](#wo-regeln-liegen)
3. [Wie der Ersatz gefunden wird](#wie-der-ersatz-gefunden-wird)
4. [Wenn mehrere Regeln zutreffen](#wenn-mehrere-regeln-zutreffen)
5. [Overrides in der Administration verwalten](#overrides-in-der-administration-verwalten)
6. [Caches](#caches)

## Eine Regel

```ini
[mytheme_full_article]
Source=node/view/full.tpl
MatchFile=full/article.tpl
Subdir=templates
Match[class_identifier]=article
Priority=10
```

| Schlüssel | Bedeutung |
|---|---|
| Abschnittsname | eindeutiger Name der Regel; stellen Sie ihm Ihr Theme voran |
| `Source` | das zu ersetzende Template, ein Pfad unter `templates/` |
| `MatchFile` | der Ersatz, ein Pfad unter `override/templates/` |
| `Subdir` | `templates` (die Art des Ordners; in Designs immer dieser Wert) |
| `Match[<key>]` | Bedingungen; alle müssen wahr sein ([Override-Match-Schlüssel](override-match-schluessel.md)); keine bedeutet immer |
| `Priority` | Reihenfolge der Auswertung, niedrigste zuerst; Regeln ohne kommen zuletzt ([Override-Reihenfolge](override-reihenfolge.md)) |

Die `settings/override.ini` des Kernels zeigt die Syntax als auskommentiertes Beispiel und enthält selbst keine Regeln.

## Wo Regeln liegen

Regeln werden für den aktuellen Siteaccess aus `override.ini` mit all ihren Append-Dateien gelesen:

- `settings/siteaccess/<siteaccess>/override.ini.append.php` (die eigene des Siteaccess);
- die `settings/override.ini.append.php` einer Erweiterung (jeder Siteaccess, auf dem die Erweiterung aktiv ist);
- die `settings/siteaccess/<siteaccess>/override.ini.append.php` einer Erweiterung (nur dieser Siteaccess; so liefern die
  Themes media und starter ihre aus).

Bevorzugen Sie für ein Theme die Siteaccess-Datei der Erweiterung: Ihre Regeln berühren dann nie die Administration oder
andere Websites.

## Wie der Ersatz gefunden wird

Wird `Source` angefordert, prüft der Kernel die Regeln dafür der Reihe nach; die erste, deren Bedingungen alle zutreffen,
gewinnt. Ihr `MatchFile` wird unter `override/templates/` in jeder Design-Basis der Kaskade gesucht
(`eZTemplateDesignResource::overrideArray()` in `kernel/common/eztemplatedesignresource.php`). Ein `MatchFile`, das in keinem
Design gefunden wird, wird im Fehlerlog gemeldet („Custom match file: path ... not found in any resource“).

## Wenn mehrere Regeln zutreffen

Nur die erste Regel in der Reihenfolge zählt. Geben Sie spezifischen Regeln (ein Knoten) eine niedrigere `Priority` als
allgemeinen (eine Klasse), damit sie zuerst geprüft werden. Siehe [Override-Reihenfolge](override-reihenfolge.md).

## Overrides in der Administration verwalten

**Design > Template-Editor** (`/visual/templatelist`) listet die Templates eines Siteaccess mit ihren Overrides, legt neue an
(mit `Priority=0` geschrieben), ordnet sie per Drag-and-drop und bearbeitet die Dateien
([Template-Editor: Overrides anlegen, ordnen und bearbeiten](../../../funktionen/6.0/template-editor-ueberschreibungen.md)). Bei einem
Theme in einer Erweiterung halten Sie die Regeln in den Dateien der Erweiterung, unter Versionsverwaltung.

## Caches

Nachdem Sie eine Regel oder ein Override-Template hinzugefügt haben: `php bin/php/ezcache.php --clear-tag=ini` und
`--clear-id=template-override`. Dann `--clear-id=content` für schon gecachte Vollansichten.

## Verwandte Seiten

- [Override-Match-Schlüssel](override-match-schluessel.md)
- [Knoten-Templates nach Klasse](knoten-templates-nach-klasse.md)
- [Tutorial: eine Vollansicht überschreiben](../theme-design/tutorial-eine-vollansicht-ueberschreiben.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
