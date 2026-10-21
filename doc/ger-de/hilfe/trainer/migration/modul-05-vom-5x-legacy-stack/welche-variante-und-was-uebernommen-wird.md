---
title: "Lektion 5.1: Welche 5.x-Variante und was übernommen wird"
audience: trainer, admin, developer
category: training
order: 51
summary: Die Teilnehmenden erkennen, welche der drei Varianten der 5.x-Ära eine alte Website betreibt (den Stack mit zwei Kerneln, die reine Legacy-Linie 5.90 oder den Legacy-Kernel ohne Symfony), erklären, warum die Datenbank ohne Umwandlung übernommen wird, und planen die Migration mit einer Bestandsaufnahme und einem Zeitplan. 45 Minuten.
keywords: migration zu exponential, zwei kernel, gemeinsame datenbank, bestandsaufnahme, zeitplan, herauslösen, migrating to exponential, 5.x, dual kernel, ezpublish_legacy, legacy bridge, 5.90, community 2014.11, shared database, ezsqldiff, inventory, timeline, extraction
type: help
---
# Lektion 5.1: Welche 5.x-Variante und was übernommen wird

Websites der 5.x-Ära betrieben zwei Kernel nebeneinander: einen Symfony-„New Stack“ und den Legacy-Kernel, verbunden
durch eine Bridge. Exponential führt den Legacy-Kernel fort, deshalb ist der Umzug ein Herauslösen und kein
Neuschreiben. Diese Lektion bringt den Teilnehmenden bei, die Variante zu erkennen, zu sehen, warum die Inhalte keine
Umwandlung brauchen, und die Arbeit rund um den Symfony-Code zu planen, denn der ist es, der wirklich Zeit kostet.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Migration zu Exponential](../README.md) |
| Modul | [Modul 5: Vom 5.x-Legacy-Stack](README.md) |
| Dauer | 45 Minuten (20 Präsentation, 25 Übung) |
| Niveau | Experten |
| Zielgruppe | Administratoren und Entwickler |
| Voraussetzungen | [Modul 4](../modul-04-von-der-4x-linie/README.md) (die Datenbankkette); Shell-Zugang zu einer Schulungsinstallation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die drei Varianten der 5.x-Ära an den Dateien im Wurzelverzeichnis der alten Installation unterscheiden;
- erklären, warum der New Stack von 5.x keine eigenen Tabellen hatte und was das für die Migration bedeutet;
- den Feldtyp nennen, für den es in Exponential keinen Datentyp gibt und der eine Entscheidung braucht;
- die Bestandsaufnahme aus Kapitel 15.4 schreiben und die Phasen ihres Zeitplans ordnen.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Kapitel 15.1](../../../../installation/15-migration-von-5x-legacy.md#151-was-sich-ändert-und-was-die-anleitung-des-herstellers-sagt),
[15.2](../../../../installation/15-migration-von-5x-legacy.md#152-welche-variante-betreiben-sie),
[15.3](../../../../installation/15-migration-von-5x-legacy.md#153-was-übernommen-wird-die-gemeinsame-datenbank) und
[15.4](../../../../installation/15-migration-von-5x-legacy.md#154-planung-und-zeitplan).

1. **Die andere Richtung** (4 Minuten). Der Hersteller hat diese Migration in Richtung seines reinen
   Symfony-Produkts dokumentiert, das den Legacy-Kernel aufgibt. Exponential geht den anderen Weg: Es behält den
   Legacy-Kernel und gibt die Symfony-Seite auf. Deshalb gelten viele Schritte des Herstellers nicht oder in
   umgekehrter Richtung. Zeigen Sie drei Zeilen der Tabelle in 15.1: XmlText wird **nicht** in RichText umgewandelt,
   ezflow-Seiten bleiben, Legacy-Datentypen und Templates funktionieren weiter.
2. **Drei Varianten** (5 Minuten). Zeigen Sie die Tabelle aus 15.2: `ezpublish/`, `ezpublish_legacy/`, `web/` und
   `src/` bedeuten den Stack mit zwei Kerneln; nur der Legacy-Baum mit `VERSION_MAJOR = 5` und `VERSION_MINOR = 90`
   bedeutet die Linie 5.90 (die Kapitel 11 folgt); ein Baum mit zwei Kerneln, dessen Document Root auf
   `ezpublish_legacy/` zeigt, ist der Legacy-Kernel ohne Symfony.
3. **Die gemeinsame Datenbank** (5 Minuten). Die Persistenzschicht des New Stack las und schrieb direkt das
   Legacy-Schema; Inhalte, die über die Public API angelegt wurden, bestehen aus denselben Zeilen. Das Schema von
   Exponential ist dieses Legacy-Schema plus zehn Tabellen, die alle von den Update-Schritten angelegt werden.
   Tabellen, die Exponential nicht kennt, werden ignoriert.
4. **Planen** (6 Minuten). Die Dauer hängt von der Menge des Symfony-Codes ab, nicht von der Menge der Inhalte. Gehen
   Sie die Liste der Bestandsaufnahme und die sieben Phasen aus 15.4 durch: Bestandsaufnahme, Probeinstallation,
   Neuschreiben, Generalprobe, Einfrieren, Umstellung, Beobachten.

### Häufige Fragen

**Wandeln wir XmlText in RichText um, wie die Anleitung des Herstellers sagt?** Nein. XmlText (`ezxmltext`) ist der
Rich-Text-Datentyp des Kernels; es wird nichts umgewandelt (15.1).

**Jemand hat bereits die eZ-Platform-Update-Datei `dbupdate-5.4.0-to-6.13.0.sql` ausgeführt. Ist die Datenbank
verloren?** Nein. Keine ihrer Änderungen hält Exponential auf; setzen Sie die Versionszeile so, wie Kapitel 15.7 es
beschreibt, damit die Update-Dateien greifen (15.3).

**Was bestimmt, wie lange es dauert?** Die Menge des Symfony-Codes: Bundles, Twig-Templates, Controller, Listener
(15.4).

### Stolperfallen

- **Exponential in das alte Projekt installieren.** Die alte `composer.json` setzt `symfony-app-dir`; legen Sie
  stattdessen ein neues Verzeichnis an (Lektion 5.2).
- **`ezrichtext` übersehen.** Klassen damit sind auf einer 5.x-Website selten, aber jede braucht eine Entscheidung
  (Lektion 5.3).
- **Vergessen, was die Symfony-Seite in Legacy eingespeist hat.** Einstellungen, die zur Laufzeit überschrieben
  wurden, sind in den Legacy-INI-Dateien oft leer (Lektion 5.2).

## Praktische Übung

Teil 1 liest nur auf Ihrer Schulungsinstallation, die für die herausgelöste Website steht; Teil 2 ist eine
Planungsübung.

1. Lesen Sie im Wurzelverzeichnis der Installation die Version des Codes:

   ```bash
   grep -n "const VERSION_" lib/version.php
   ```

2. Zählen Sie die Tabellen Ihrer Schulungsdatenbank:

   ```bash
   sqlite3 -readonly var/storage/sqlite3/exponential.db "SELECT COUNT(*) FROM sqlite_master WHERE type = 'table';"
   ```

3. Listen Sie die Tabellen auf, die Exponential dem Legacy-Schema hinzugefügt hat:

   ```bash
   sqlite3 -readonly var/storage/sqlite3/exponential.db "SELECT name FROM sqlite_master WHERE type = 'table' AND (name LIKE 'exp%' OR name = 'ezrss_export_opml_item') ORDER BY name;"
   ```

   Vergleichen Sie die Liste mit den zehn Tabellen, die Kapitel 15.3 nennt.
4. Planung: Das Wurzelverzeichnis einer alten Website enthält `ezpublish/EzPublishKernel.php`,
   `ezpublish/config/ezpublish.yml`, `ezpublish_legacy/`, `web/` und `src/`, und ihre `composer.json` setzt
   `"symfony-app-dir": "ezpublish"`. Welche Variante ist das, und welchem Kapitel folgen Sie?
5. Dieselbe Website hat drei Symfony-Bundles in `src/`: Eines rendert die öffentlichen Seiten in Twig, eines fügt eine
   REST-Route für eine mobile App hinzu, eines lauscht auf Veröffentlichungsereignisse und schickt eine
   Benachrichtigung an einen Chat-Dienst. Schreiben Sie für jedes die Zeile der Bestandsaufnahme aus Kapitel 15.4, mit
   einer Entscheidung: als Erweiterung neu schreiben, weglassen oder auf einer Bridge behalten.
6. Schreiben Sie für diese Website die sieben Phasen des Zeitplans aus Kapitel 15.4 auf, jeweils mit einer Zeile
   „erledigt, wenn“.

## Erwartetes Ergebnis

- Schritt 1 zeigt die Versionskonstanten von Exponential 6.0 (Major 6, Minor 0).
- Schritt 3 listet die Tabellen `expaudit_*`, `expbookmark_folder`, `expmail_*` und `ezrss_export_opml_item` auf,
  passend zu Kapitel 15.3.
- Schritt 4: der Stack mit zwei Kerneln (5.0 bis 5.4 oder Community 2013.x und 2014.x); Kapitel 15.
- Schritt 5: Jedes Bundle hat einen Verantwortlichen und eine Entscheidung; das Twig-Frontend ist als größter Posten
  markiert.
- Der Trainer prüft die Entscheidungen aus Schritt 5 und bittet eine Person zu erklären, warum die Datenbank keine
  Umwandlung braucht.

## Quiz

1. Welche Dateien im alten Wurzelverzeichnis zeigen Ihnen, dass es sich um den Stack mit zwei Kerneln handelt?
2. Die Inhalte einer 5.x-Website wurden über die Public API angelegt. Kann Exponential sie lesen?
   A. Nein, sie müssen umgewandelt werden B. Ja, es sind dieselben Zeilen des Legacy-Schemas C. Erst nach einer
   Umwandlung in RichText D. Nur über REST
3. Was bestimmt laut Kapitel 15.4 die Dauer einer 5.x-Migration?
4. Richtig oder falsch: Exponential wandelt bei dieser Migration XmlText-Felder in RichText um.
5. Für welchen Feldtyp des New Stack gibt es in Exponential keinen Datentyp?

<details>
<summary>Antworten</summary>

1. `ezpublish/` (mit `EzPublishKernel.php`, `config/ezpublish.yml`), `ezpublish_legacy/`, `web/`, `src/`
   ([Kapitel 15.2](../../../../installation/15-migration-von-5x-legacy.md#152-welche-variante-betreiben-sie)).
2. **B** ([Kapitel 15.3](../../../../installation/15-migration-von-5x-legacy.md#153-was-übernommen-wird-die-gemeinsame-datenbank)).
3. Die Menge des Symfony-Codes, nicht die Menge der Inhalte
   ([Kapitel 15.4](../../../../installation/15-migration-von-5x-legacy.md#154-planung-und-zeitplan)).
4. **Falsch.** XmlText ist der Rich-Text-Datentyp des Kernels; es wird nichts umgewandelt
   ([Kapitel 15.1](../../../../installation/15-migration-von-5x-legacy.md#151-was-sich-ändert-und-was-die-anleitung-des-herstellers-sagt)).
5. `ezrichtext` (RichText)
   ([Kapitel 15.8](../../../../installation/15-migration-von-5x-legacy.md#158-feldtypen-und-datentypen)).

</details>

## Weiterführende Informationen

- [Kapitel 15: Migration vom 5.x-Legacy-Stack](../../../../installation/15-migration-von-5x-legacy.md): das vollständige
  Kapitel.
- [Kapitel 11.5: Von 5.4 oder 5.90 auf 6.0.0](../../../../installation/11-aktualisieren.md#115-von-54-oder-590-auf-600): der
  Datenbankweg der Linie 5.90.
- [Ältere Versionen dieses Kernels](../../../../installation/migration/aeltere-versionen.md): die Tabelle der
  Ausgangspunkte.

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
