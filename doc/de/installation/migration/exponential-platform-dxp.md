---
title: Exponential 6 und Exponential Platform DXP
audience: admin, developer, project-manager
category: install-upgrade
order: 22
summary: Eine Exponential-6-Site in Exponential Platform DXP 1.x (das 4.6-Skeleton) oder 2.x (das v5-Skeleton) überführen und zurück – was DXP enthält und was nicht, der Weg Generation für Generation über die gemeinsame Datenbank, XmlText zu RichText, Templates, Benutzer, URLs, Prüfung, Rückweg und Checkliste.
keywords: Exponential Platform DXP, DXP 1.x, DXP 2.x, Skeleton, Projektgerüst, Migration, Umstieg, exponential-platform-dxp, v4.6, v5, Symfony 7.4, RichText, XmlText, ezxmltext:convert-to-richtext, ibexa_ tables, Twig, migration
type: install
---
# Exponential 6 und Exponential Platform DXP

**Exponential Platform DXP** ist die reine Symfony-Plattform der Produktfamilie. „DXP 1.x“ und „DXP 2.x“ sind ihre
beiden Projekt-Skeletons:

| Name | Repository | Neuestes Release | Generation | Symfony | PHP |
|---|---|---|---|---|---|
| DXP 1.x | `se7enxweb/exponential-platform-v4x-dxp-skeleton` | `v1.0.0.2` | 4.6 (`exponential-platform-dxp 4.6.x-dev`) | 5.4 | `^7.4` bis `^8.5` |
| DXP 2.x | `se7enxweb/exponential-platform-dxp-skeleton` | `v2.0.0.2` | v5 (`exponential-platform-dxp dev-master`) | 7.4 | `>=8.3` |

Beide setzen das Metapaket `se7enxweb/exponential-platform-dxp` voraus (Releases `0.0.0.1`, `v0.0.0.2`), dessen Kernel
`se7enxweb/exponential-platform-dxp-core` ist (`v5.0.9` als neuestes Release; `v4.6.x`-Tags für die Linie 4.6). Eine
sofort lauffähige Variante ist `se7enxweb/exponential-platform-dxp-starter` (`1.0.0.0`).

[Übersicht Migration](README.md) · [Checkliste](checkliste.md) · [DXP-Skeleton](../../../en/us/features/6.0/platform-dxp-skeleton.md) ·
[Paketübersicht der Plattform](../../spezifikationen/6.0/platform-paketzuordnung.md)

## Inhalt

- [1. Was DXP enthält und was nicht](#1-was-dxp-enthält-und-was-nicht)
- [2. Von Exponential 6 zu DXP](#2-von-exponential-6-zu-dxp)
- [3. Inhalte, Templates und Code](#3-inhalte-templates-und-code)
- [4. Von DXP zu Exponential 6](#4-von-dxp-zu-exponential-6)
- [5. Prüfen, Rückweg](#5-prüfen-rückweg)
- [6. Risiken](#6-risiken)
- [7. Checkliste](#7-checkliste)

## 1. Was DXP enthält und was nicht

Laut der `composer.json` von `se7enxweb/exponential-platform-dxp` (`master`, gelesen am 2026-10-18):

| Enthalten | Nicht enthalten |
|---|---|
| der Kern (`exponential-platform-dxp-core`), die Administrationsoberfläche, Inhaltsformulare, REST, GraphQL mit GraphiQL, HTTP-Cache, Solr- und Core-Suche, die Feldtypen RichText, Matrix und Query, Benachrichtigungen, Messenger, Cron, das Standard-Design, Twig-Komponenten, JWT-Authentifizierung | der Legacy-Kernel von Exponential 6 und die Legacy-Bridge |
| `se7enxweb/layouts-core` | die Editor-Oberfläche von Layouts, der Standard-Blocksatz oder die Ibexa-Integration von Layouts (Nexus bringt sie mit) |
| | ein Workflow- oder Scheduler-Paket |
| | die Legacy-Funktionen ohne Gegenstück auf der Plattform: Shop, Legacy-Workflows, Zusammenarbeit, Informationssammlung, Newsletter, RSS-Import/-Export, PDF-Export |

Eine Site, die irgendetwas aus der rechten Spalte nutzt, verliert es also auf DXP oder braucht einen Ersatz. Prüfen
Sie jede Funktion anhand der Paketliste, bevor Sie sie zusagen.

## 2. Von Exponential 6 zu DXP

Es gibt keinen Konverter, der eine Exponential-6-Datenbank direkt in eine v5-Datenbank überführt. Der Weg führt über
die gemeinsamen Inhaltstabellen, eine Plattformgeneration nach der anderen, so wie es auch der eigene Update-Pfad des
Herstellers tut:

1. **Exponential 6 zur Generation 2.5**: Stellen Sie den Symfony-Stack mit
   [Exponential Platform Legacy `v2.5.0.x`](exponential-platform-legacy.md#3-von-exponential-6-zu-platform-legacy)
   (oder [Nexus `1.0.0.x`](exponential-platform-nexus.md)) neben den Legacy-Kernel. Die Inhalte sind dann für den Plattform-Kernel lesbar.
2. **XmlText in RichText umwandeln**, solange die Generation 2.5 in Betrieb ist: `ezxmltext:convert-to-richtext` aus
   `se7enxweb/ezplatform-xmltext-fieldtype` (Branch `1.8`, `bundle/Command/ConvertXmlTextToRichTextCommand.php`).
   Spätere Generationen haben keinen XmlText-Feldtyp. Testen Sie das Ergebnis an Ihren komplexesten Inhalten (Custom
   Tags, Einbettungen, Tabellen) und korrigieren Sie die Quelle dort, wo der Konverter Fehler meldet.
3. **Benutzer**: Plattformgenerationen ab 3.0 akzeptieren nur die Passwort-Hash-Typen 6 und 7. Benutzer mit älteren
   Typen müssen ihr Passwort zurücksetzen oder sich einmal anmelden, solange die Generation 2.5 läuft (der Legacy-Kernel
   erzeugt den Hash bei der Anmeldung neu)
   ([17.7](../17-migrationsreferenz.md#177-passwörter-url-aliase-und-bildspeicherung)).
4. **2.5 zu 3.3**: die Dateien `upgrade/db/<engine>/` im Branch `master` von `se7enxweb/exponential-platform`
   ([Platform Legacy, Abschnitt 4](exponential-platform-legacy.md#4-wechsel-auf-eine-neuere-linie)).
5. **3.3 zu 4.6**: das Datenbank-Update des Herstellers von 3.3 auf 4.0 und die Umbenennungen der Konfiguration zu `ibexa:`
   ([Kapitel 16.5.6](../16-migration-von-ez-platform-und-ibexa.md#1656-die-datenbank-generation-für-generation)).
   An diesem Punkt können Sie **auf DXP 1.x ankommen**: Legen Sie das `v4x`-Skeleton-Projekt an, verbinden Sie es mit
   der Datenbank, ohne den Installer auszuführen, und entfernen Sie die Legacy-Hälfte.
6. **4.6 zu v5**: Das Update von 4.6 auf 5.0 benennt jede Kerntabelle in `ibexa_*` um
   ([16.5.6, „Die Tabellenumbenennungen von 5.0“](../16-migration-von-ez-platform-und-ibexa.md#1656-die-datenbank-generation-für-generation)).
   Danach **kommen Sie auf DXP 2.x an**.
7. **Lassen Sie die reinen Legacy-Tabellen** (Shop, Workflow und andere) in einer Archivkopie; die Plattform nutzt sie nicht.

Jeder Generationsschritt ist ein eigenes Projekt: Führen Sie ihn an einer Kopie aus, prüfen Sie das Ergebnis und gehen
Sie erst dann den nächsten an. Die Seiten des Herstellers zu jedem Schritt sind in [Kapitel 16.3](../16-migration-von-ez-platform-und-ibexa.md#163-die-seiten-des-herstellers-und-wo-dieses-kapitel-sie-abdeckt) aufgeführt.

Eine Alternative für eine kleine Site oder für eine Site, deren Modell Sie ohnehin neu entwerfen möchten: **Legen Sie
ein neues DXP-Projekt an und importieren Sie die Inhalte** über die eigenen APIs der Plattform (ein Symfony-Befehl, der
einen xrowextract-Export liest, siehe [die Werkzeuge von Exponential](exponential-tools.md#2-export-aus-exponential)). Das erspart vier
Schemaschritte, kostet aber das Schreiben des Importers und den Verlust der Versionshistorie.

## 3. Inhalte, Templates und Code

| Exponential 6 | DXP |
|---|---|
| Inhaltsklassen, Attribute | Inhaltstypen, Felder (bis 4.6 dieselben Tabellen) |
| Datentypen | Feldtypen; prüfen Sie jeden Datentyp in [17.3](../17-migrationsreferenz.md#173-datentypen-und-feldtypen) |
| `ezxmltext` | `ezrichtext` / `ibexa_richtext` (umgewandelt, Schritt 2) |
| TPL-Templates, Overrides | Twig-Templates, Konfiguration der Inhaltsansichten in YAML (von Hand neu geschrieben, [17.5](../17-migrationsreferenz.md#175-templates-tpl-und-twig)) |
| INI-Einstellungen | YAML-Konfiguration ([17.4](../17-migrationsreferenz.md#174-einstellungen-ini-und-yaml)) |
| Extensions, Module | Symfony-Bundles, Controller |
| Exponential Layouts | in DXP nicht vorhanden; bauen Sie die Seiten neu auf oder nutzen Sie Nexus für Netgen Layouts |
| URL-Aliase | dieselben Aliase, neu erzeugt durch den Plattform-Befehl (`ibexa:urls:regenerate-aliases` beim Hersteller, [17.9](../17-migrationsreferenz.md#179-werkzeuge-für-migration-und-upgrade)) |
| REST v1/v2 von `ezprestapi` | REST v2 der Plattform (andere Routen und Medientypen); Clients müssen angepasst werden |
| Velocity, SQLite, MongoDB, Oracle | ein Webserver mit PHP-FPM; MySQL/MariaDB oder PostgreSQL (SQLite in den Startern) |

## 4. Von DXP zu Exponential 6

Das ist „Weg B“ aus [Kapitel 16.6](../16-migration-von-ez-platform-und-ibexa.md#166-weg-b-hinunter-zum-legacy-kernel-von-exponential-60),
der genau für diese Richtung geschrieben ist:

1. Erfassen Sie die verwendeten Feldtypen; jeder braucht einen Legacy-Datentyp (16.6.3). RichText bleibt DocBook und
   wird wie dort beschrieben dargestellt oder in XmlText umgewandelt.
2. Datenbank: Machen Sie auf einer v5-Datenbank die Umbenennungen in `ibexa_*` rückgängig (oder nutzen Sie die
   Übersetzer-Extension), legen Sie die reinen Legacy-Tabellen mit `ezsqldiff.php` neu an (zuerst die Referenz), setzen
   Sie die Versionszeilen und spielen Sie die Update-Dateien von Exponential 6 ein (16.6.4).
3. Konfiguration von YAML zu INI (16.6.5), Twig zu TPL (16.6.6).
4. Benutzer behalten ihre Hashes vom Typ 6/7, die Exponential 6 lesen kann.
5. Liefern Sie die Site mit Velocity oder PHP-FPM aus; leiten Sie die nur auf der Plattform vorhandenen Routen um
   (REST v2, Administrationspfade).

Wenn Redakteure die Legacy-Administration brauchen, während die Symfony-Site weiterläuft, nutzen Sie stattdessen
Exponential Platform Legacy derselben Generation (16.6.8).

## 5. Prüfen, Rückweg

- Nach jedem Generationsschritt: Anzahl je Inhaltstyp und Sprache, die Administration, eine Bearbeitung mit
  Veröffentlichung, eine Bildvariante, der neu aufgebaute Suchindex (`exponential:reindex` auf den Exponential-Linien),
  `doctrine:schema:validate`.
- Rich Text: Vergleichen Sie eine Stichprobe der komplexesten Inhalte, dargestellt, vor und nach der Umwandlung.
- Rückweg: die vor jedem Schritt erstellte Sicherung. Die Update-Dateien der Plattform lassen sich nicht durch eine
  weitere SQL-Datei rückgängig machen.

## 6. Risiken

| Risiko | Gegenmaßnahme |
|---|---|
| Funktionen, die die Site nutzt, sind in DXP nicht enthalten | die Paketliste ([1](#1-was-dxp-enthält-und-was-nicht)) vor der Entscheidung prüfen |
| Fehler bei der Umwandlung von XmlText | auf der Generation 2.5 umwandeln, die schwierigsten Inhalte zuerst testen |
| Alte Passwort-Hashes | Zurücksetzen oder eine Anmeldung, solange der Legacy-Kernel noch läuft |
| Vier Schemaschritte auf einmal | eine Generation pro Projektphase, jede geprüft |
| Templates unterschätzt | Neufassung in Twig als eigenes Arbeitspaket geplant |

## 7. Checkliste

- [ ] Jede genutzte Funktion anhand der `composer.json` von DXP geprüft.
- [ ] Weg gewählt: über die Generationen oder ein neues Projekt mit Import.
- [ ] Generation 2.5 in Betrieb (Platform Legacy oder Nexus); XmlText umgewandelt und geprüft.
- [ ] Passwort-Hashes 6/7 für alle aktiven Benutzer.
- [ ] 2.5 zu 3.3 zu 4.6 (DXP 1.x) zu v5 (DXP 2.x), jeweils an einer Kopie geprüft.
- [ ] Templates in Twig neu geschrieben; REST-Clients angepasst.
- [ ] Reine Legacy-Tabellen archiviert; Weiterleitungen für Adressen, die sich ändern.

Diese Seite ist Teil der Exponential-Dokumentation und freie Dokumentation unter der GNU Free Documentation License
1.3 oder einer späteren Version ([die Lizenz dieser Dokumentation](../../../en/us/README-LICENSE.md)).
