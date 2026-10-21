---
title: Offene Formate und Datenportabilität
audience: government, admin, developer, project-manager
category: public-sector
order: 120
summary: In welchen Formaten Exponential Inhalte, Einstellungen und Dateien speichert und exportiert, welche davon offen und dokumentiert sind und wie Sie Ihre eigenen Inhalte in Formaten halten, die ein anderes System lesen kann.
keywords: offene Formate, Datenportabilität, XML, Rich Text, ezxmltext, CSV, JSON, INI, UTF-8, ezpkg, Dateien, Speicherung, Export, Migration, open formats, data portability, rich text, files, storage, export, migration
type: help
---
# Offene Formate und Datenportabilität

Diese Seite richtet sich an Administratoren, Entwicklerinnen und Entwickler sowie Führungskräfte, die zeigen müssen,
dass die Inhalte der Stelle auch ohne Exponential lesbar sind. Sie listet auf, wo welche Art von Daten liegt, in
welchem Format, und worauf Sie achten sollten.

## Inhalt

1. [Wo was gespeichert wird](#wo-was-gespeichert-wird)
2. [Exportformate](#exportformate)
3. [Rich Text](#rich-text)
4. [Zeichenkodierung](#zeichenkodierung)
5. [Portabilität bewahren](#portabilität-bewahren)

## Wo was gespeichert wird

| Daten | Gespeichert als | Ohne Exponential lesbar |
|---|---|---|
| Inhaltsfelder | Zeilen in der Datenbank (eine von sechs Datenbanken) | ja, mit Datenbankwerkzeugen; das Schema liegt im Repository |
| Rich Text | XML in einer Datenbankspalte | ja, es ist XML |
| Hochgeladene Dateien und Bilder | Dateien unter `var/<site>/storage/` | ja, als Dateien; ihre Namen sind in der Datenbank Objekten zugeordnet |
| Einstellungen | INI-Dateien in `settings/` und `extension/*/settings/` | ja, reiner Text |
| Templates | `.tpl`-Textdateien | ja, reiner Text |
| Übersetzungen der Oberfläche | Qt-Linguist-XML (`.ts`) | ja ([Standards zur Internationalisierung](../../../../eng-us/standards/internationalization.md)) |
| Audit-Einträge | Dateien mit JSON-Zeilen, mit Manifesten archiviert | ja ([Prüfprotokoll](../../../../eng-us/features/6.0/audit-trail.md)) |

## Exportformate

| Format | Woher | Dokumentation |
|---|---|---|
| CSV, JSON, XML pro Klasse | `xrowextract` | [xrowextract](../../../../eng-us/features/6.0/extensions/xrowextract.md) |
| CSV pro Klasse von der Kommandozeile | `exp:ezcsvexport` | [exp:ezcsvexport](../../../../eng-us/examples/commands/ezcsvexport.md) |
| JSON über HTTP | REST-API | [REST-API v1 und v2](../../../anleitungen/rest-api-v1-und-v2.md) |
| `.ezpkg`-Pakete (Klassen, Inhalte, Erweiterungen) | Setup > Pakete | [Pakete](../../../anleitungen/pakete.md) |
| `.dba`-Datenbank-Dumps, unabhängig von der Datenbank | `bin/php/ezsqldumpschema.php` | [Datenbankunterstützung](../../datenbankunterstuetzung.md#eine-site-auf-eine-andere-datenbank-umziehen) |
| RSS, Atom | Setup > RSS | [RSS-Feeds](../../../anleitungen/rss-feeds.md) |
| PDF | Setup > PDF-Export | [PDF-Exporte](../../../anleitungen/pdf-exporte.md) |
| CSV von Formulareinsendungen, Benutzerlisten | Gesammelte Informationen, Benutzerkonten prüfen | [Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md) |

## Rich Text

Der Datentyp für Rich Text speichert XML nach einem eigenen Schema, kein HTML. Ein anderes System liest es über einen
Export, der es rendert (der `OutputFormat=xhtml` der REST-API füllt die gerenderte Ausgabe), oder über eine
Konvertierung, die Sie schreiben. Planen Sie diese Konvertierung bei einer Migration früh ein; sie ist meist die
größte Einzelaufgabe.

## Zeichenkodierung

Der interne Zeichensatz ist UTF-8 (`settings/i18n.ini [CharacterSettings] Charset=utf-8`). Unter MySQL und MariaDB
verwendet eine Standardverbindung das 3-Byte-`utf8`; Zeichen oberhalb von U+FFFF brauchen `utf8mb4`, das die
Administration einrichtet
([Standards zur Internationalisierung](../../../../eng-us/standards/internationalization.md#4-byte-characters-on-mysql-and-mariadb)).
SQLite und PostgreSQL speichern vollständiges Unicode.

## Portabilität bewahren

- Laden Sie Dokumente in offenen oder breit dokumentierten Formaten hoch, mit einer CSV-Datei neben jeder Tabelle.
- Speichern Sie Inhalte nicht nur in Templates; halten Sie sie in Inhaltsobjekten.
- Halten Sie die Bezeichner von Klassen und Attributen aussagekräftig; sie werden in Exporten zu Spaltennamen.
- Exportieren Sie Ihre wichtigsten Klassen einmal im Jahr und prüfen Sie, ob sich die Dateien in gängigen Werkzeugen
  öffnen lassen.

## Verwandte Seiten

- [Inhalte und Pakete exportieren](inhalte-und-pakete-exportieren.md)
- [Datenbankunterstützung](../../datenbankunterstuetzung.md)
- [Standards zur Internationalisierung](../../../../eng-us/standards/internationalization.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
