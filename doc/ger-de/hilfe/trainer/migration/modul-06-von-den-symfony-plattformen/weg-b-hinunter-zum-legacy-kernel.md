---
title: "Lektion 6.3: Weg B: hinunter zum Legacy-Kernel von Exponential 6"
audience: trainer, admin, developer
category: training
order: 63
summary: Die Teilnehmenden planen den Umzug einer Datenbank von eZ Platform oder Ibexa hinunter zum Legacy-Kernel von Exponential 6 - was beide gemeinsam haben, was verloren geht und was hinzukommt, Feldtypen ohne Datentyp, die sechs Datenbankschritte mit ezsqldiff in der richtigen Richtung und das Weiterbetreiben eines Symfony-Stacks daneben mit der Legacy-Bridge. 60 Minuten.
keywords: migration zu exponential, weg b, legacy-kernel, legacy-bridge, feldtypen, versionszeilen, tabellennamen, datenbank-übersetzer, migrating to exponential, path B, legacy kernel, Exponential Platform Legacy, legacy bridge, ezsqldiff, db_schema.dba, field types, ezrichtext, ezimageasset, ezlandingpage, version rows, ibexa table names, database translator
type: help
---
# Lektion 6.3: Weg B: hinunter zum Legacy-Kernel von Exponential 6

Das Inhaltsmodell jeder Plattformgeneration ist das des Legacy-Kernels, deshalb kann eine Symfony-basierte Website
hinunter zu Exponential 6 wechseln. Weg B schreibt die Darstellung und den eigenen Code neu; die Inhaltstabellen
werden gemeinsam genutzt. Diese Lektion vermittelt, was beide gemeinsam haben, was verloren geht und was hinzukommt,
welche Feldtypen eine Entscheidung brauchen, und die sechs Datenbankschritte aus Kapitel 16.6.4, mit dem einen Befehl,
bei dem man die Richtung leicht verwechselt.

> **Hinweis**
> Jeder Datenbankschritt dieser Lektion wird auf einer Kopie geprobt. Die Teilnehmenden lesen und planen; die einzigen
> Befehle, die sie ausführen, lesen nur.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Migration zu Exponential](../README.md) |
| Modul | [Modul 6: Von den Symfony-basierten Plattformen](README.md) |
| Dauer | 60 Minuten (25 Präsentation, 35 Übung) |
| Niveau | Experten |
| Zielgruppe | Entwickler und Administratoren |
| Voraussetzungen | [Lektion 6.1](zwei-ziele-und-die-wahl.md); [Modul 4](../modul-04-von-der-4x-linie/README.md) für den Hintergrund zur Legacy-Datenbank |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- erklären, welche Tabellen die Plattformgenerationen mit dem Legacy-Kernel teilen und welche sie entfernt oder
  umbenannt haben;
- aufzählen, was Weg B verliert und gewinnt;
- entscheiden, was mit fünf Feldtypen geschieht, die keinen Legacy-Datentyp haben;
- die sechs Datenbankschritte ordnen und `ezsqldiff.php` mit dem Referenzschema an erster Stelle ausführen;
- beschreiben, wie ein Symfony-Stack neben dem Legacy-Kernel weiterlaufen kann.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Kapitel 16.6](../../../../installation/16-migration-von-ez-platform-und-ibexa.md#166-weg-b-hinunter-zum-legacy-kernel-von-exponential-60).

1. **Was beide gemeinsam haben** (4 Minuten). Inhalte, Klassen, Orte, Versionen, URL-Aliase, Rollen, Sektionen,
   Zustände, Sprachen und Benutzer sind dieselben Tabellen mit derselben Bedeutung. Shop-, Workflow-,
   Zusammenarbeits- und andere Legacy-Tabellen hat der Hersteller mit dem Schritt zu 3.0 entfernt (etwa 80 Tabellen);
   5.0 hat die Kerntabellen in `ibexa_*` umbenannt (16.6.1).
2. **Verlieren und gewinnen** (4 Minuten). Zeigen Sie die Tabelle aus 16.6.2: Verloren gehen die
   Symfony-Administration, Twig, REST v2 und GraphQL, der Page Builder; hinzu kommen die Legacy-Administration, TPL mit
   Overrides, Legacy-Workflows, Informationssammlung, Shop, Newsletter, Exponential Layouts, Velocity, SQLite in der
   Produktion.
3. **Feldtypen** (5 Minuten). Aus 16.6.3: `ezrichtext` (umwandeln oder behalten und die Ausgabe anpassen); `ezmatrix`
   ab 2.5 hat ein anderes Format; `ezimageasset` wird zu `ezobjectrelation` (beide speichern die Ziel-ID in
   `data_int`); `ezcontentquery` wird zu einem Fetch im Template; `ezlandingpage` und `ezform` werden mit Exponential
   Layouts und der Informationssammlung neu aufgebaut.
4. **Sechs Datenbankschritte** (8 Minuten). Sichern und den Symfony-Stack anhalten; bei 5.0 die Tabellennamen
   zurückholen (umgekehrte Umbenennungen oder die Erweiterung für den Datenbank-Übersetzer); das Schema mit
   `ezsqldiff.php` zum Schema von 6.0.15 machen; `ezkeyword_attribute_link.version` einen Standardwert geben; die
   Versionszeilen einfügen; Einstellungen, Speicher mit demselben `VarDir`, Caches und ein sauberer Suchindex.
5. **Die Richtungsfalle** (2 Minuten). `ezsqldiff.php` gibt das SQL aus, das das **zweite** Argument in das **erste**
   überführt. Das Referenzschema zuerst, Ihre Datenbank als zweites. Plausibilitätsprüfung: Zeilen mit `DROP TABLE`
   dürfen nur Tabellen der Symfony-Seite nennen; wenn sie `ezbasket` oder `ezworkflow` aufführen, sind die Argumente
   vertauscht.
6. **Beide Stacks** (2 Minuten). Platform Legacy und die älteren Nexus-Linien betreiben beide Kernel über die
   Legacy-Bridge auf einer Datenbank (16.6.8). Entfernen Sie die `DROP`-Zeilen, solange der Symfony-Stack noch
   gebraucht werden könnte.

### Häufige Fragen

**Können wir die alte Zeile `ezpublish-version` einer 2.5-Datenbank als Legacy-Version lesen?** Nein. Die
Update-Dateien des Symfony-Kernels schreiben ihre eigene Kernel-Version hinein; beginnen Sie die Legacy-Kette aus
Kapitel 11 nicht damit (16.6.4, Schritt 5).

**Funktioniert SQLite als Quelle?** `ezsqldiff.php` spricht nur MySQL und PostgreSQL; wandeln Sie zuerst um oder
ziehen Sie die Inhalte auf Anwendungsebene um (16.6.4).

**Was ist mit Emojis in den Inhalten?** Der Standardzeichensatz der Verbindung des Legacy-Kernels unter MySQL ist das
Drei-Byte-`utf8`, das keine Zeichen außerhalb der Basic Multilingual Plane aufnehmen kann. Prüfen Sie die Inhalte vor
dem Umzug darauf (16.6.4).

### Stolperfallen

- **Die Ausgabe von `ezsqldiff.php` ungelesen ausführen.** Sie enthält `DROP`-Anweisungen; lesen Sie sie, behalten Sie,
  was Sie brauchen, und führen Sie sie dann ohne `--force` aus.
- **Den Standardwert für die Keyword-Verknüpfung vergessen.** Die Keyword-Inserts des Legacy-Kernels scheitern dann im
  strikten SQL-Modus.
- **Nichts neu aufbauen.** Leeren Sie die Caches und bauen Sie den Suchindex mit `--clean` neu auf; das ist billiger
  als Nachprüfen.

## Praktische Übung

Teil 1 liest nur auf Ihrer Schulungsinstallation; Teil 2 ist eine Planungsübung mit geöffnetem Kapitel 16.6.

1. Zeigen Sie im Wurzelverzeichnis der Installation die Hilfe des Schemavergleichs an und suchen Sie die Optionen für
   die beiden Schemas und für `--check-only`:

   ```bash
   php bin/php/ezsqldiff.php --help
   ```

   (Auf einem Rechner, auf dem Sie als root arbeiten, verlangt das Skript `--allow-root-user`.)
2. Zählen Sie die Tabelleneinträge in der Referenzschema-Datei:

   ```bash
   grep -c "^  '[a-z_0-9]*' => *$" share/db_schema.dba
   ```

   Die Datei hat einen Eintrag pro Tabelle plus einen Eintrag `_info`; Kapitel 16.6.4 nennt 128 Tabellen.
3. Zeigen Sie die Versionszeilen Ihrer Schulungsdatenbank an:

   ```bash
   sqlite3 -readonly var/storage/sqlite3/exponential.db "SELECT name, value FROM ezsite_data WHERE name LIKE 'ezpublish-%';"
   ```

4. Planung: Eine Ibexa-4.6-Website unter MySQL 8 hat `ezrichtext` in vier Inhaltstypen, 120 Felder vom Typ
   `ezimageasset`, drei Landing Pages (`ezlandingpage`) und verwendet Schlüsselwörter. Schreiben Sie für jeden Feldtyp
   die Entscheidung aus [16.6.3](../../../../installation/16-migration-von-ez-platform-und-ibexa.md#1663-feldtypen) auf.
5. Schreiben Sie die sechs Datenbankschritte aus
   [16.6.4](../../../../installation/16-migration-von-ez-platform-und-ibexa.md#1664-die-datenbank-schritt-für-schritt) für diese
   Website auf, mit dem Befehl `ezsqldiff.php` (erfundene Werte für Host, Benutzer und Passwort) und den beiden
   `grep`-Plausibilitätsprüfungen.
6. Schreiben Sie die drei `ezsite_data`-Anweisungen aus Schritt 5 und sagen Sie, warum die alte Zeile zuerst gelöscht
   wird.
7. Die Redakteure möchten die Symfony-Administration noch drei Monate behalten. Schreiben Sie auf, welche Produktlinie
   für die 4.6-Generation beide Kernel betreibt und welchen Bridge-Branch sie verwendet
   ([16.6.8](../../../../installation/16-migration-von-ez-platform-und-ibexa.md#1668-einen-symfony-stack-neben-dem-legacy-kernel-behalten)).

## Erwartetes Ergebnis

- Schritt 1 zeigt die Optionen; Schritt 3 zeigt auf einer aktuellen Schulungsinstallation `6.0.15stable` und `1`.
- Schritt 4: RichText umgewandelt (oder behalten und die Ausgabe angepasst); `ezimageasset` in `ezobjectrelation`
  geändert; Landing Pages mit Exponential Layouts neu aufgebaut und das Feld entfernt; Schlüsselwörter brauchen den
  Standardwert für `ezkeyword_attribute_link.version`.
- Schritt 5: zuerst `share/db_schema.dba`, als zweites die Datenbank; `grep -c '^CREATE TABLE'` groß,
  `grep '^DROP TABLE'` nur Tabellen der Symfony-Seite, die entfernt werden, solange der Symfony-Stack gebraucht werden
  könnte.
- Schritt 7: Platform Legacy `v4.6.23.x` (oder Nexus `1.2.0.x`), Bridge-Branch `4.x`.
- Der Trainer prüft auf jedem Blatt die Reihenfolge in Schritt 5 und die Richtung des Befehls `ezsqldiff.php`.

## Quiz

1. `ezsqldiff.php A B` gibt SQL aus, das welches Schema in welches überführt?
2. Ihre Diff-Ausgabe enthält `DROP TABLE ezbasket;`. Was ist schiefgelaufen?
   A. Nichts B. Die Argumente sind vertauscht C. Die Datenbank ist SQLite D. Die Schemadatei ist alt
3. Welcher Feldtyp behält seinen Wert, wenn er in `ezobjectrelation` geändert wird, und warum?
4. Was muss die Spalte `ezkeyword_attribute_link.version` nach dem MySQL-Skript von 2.5 zu 3.0 erhalten, und warum?
5. Nennen Sie zwei Dinge, die Weg B gewinnt, und zwei, die er verliert.

<details>
<summary>Antworten</summary>

1. Es überführt das zweite (`B`) in das erste (`A`)
   ([Kapitel 16.6.4](../../../../installation/16-migration-von-ez-platform-und-ibexa.md#1664-die-datenbank-schritt-für-schritt)).
2. **B.** Zeilen mit `DROP TABLE` dürfen nur Tabellen der Symfony-Seite aufführen (Kapitel 16.6.4).
3. `ezimageasset`: Beide speichern die ID des Ziel-Inhaltselements in `data_int`
   ([Kapitel 16.6.3](../../../../installation/16-migration-von-ez-platform-und-ibexa.md#1663-feldtypen)).
4. Einen Standardwert von 0, weil der Legacy-Kernel Keyword-Verknüpfungen ohne ihn einfügt und der Insert im strikten
   SQL-Modus scheitert (Kapitel 16.6.1 und 16.6.4, Schritt 4).
5. Gewinne, zwei beliebige aus: die Legacy-Administration, TPL mit Overrides, Legacy-Workflows,
   Informationssammlung, Shop, Newsletter, Exponential Layouts, Velocity, SQLite in der Produktion. Verluste, zwei
   beliebige aus: die Symfony-Administration, Twig, REST v2 und GraphQL, Page Builder, Form Builder
   ([Kapitel 16.6.2](../../../../installation/16-migration-von-ez-platform-und-ibexa.md#1662-was-sie-verlieren-und-was-sie-gewinnen)).

</details>

## Weiterführende Informationen

- [Kapitel 16.6: Weg B](../../../../installation/16-migration-von-ez-platform-und-ibexa.md#166-weg-b-hinunter-zum-legacy-kernel-von-exponential-60):
  der vollständige Weg mit Konfiguration und Templates.
- [Exponential Platform Legacy](../../../../installation/migration/exponential-platform-legacy.md): der Hybrid, in beide
  Richtungen.
- [Legacy-Bridge](../../../../funktionen/6.0/legacy-bridge.md): beide Kernel auf einer Datenbank betreiben.

## Verwandte Seiten

- [Modul 6: Von den Symfony-basierten Plattformen](README.md)
- [Migration zu Exponential: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
