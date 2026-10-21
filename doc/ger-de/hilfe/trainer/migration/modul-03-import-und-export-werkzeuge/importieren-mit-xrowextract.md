---
title: "Lektion 3.2: Importieren mit xrowextract: Probeläufe und wiederholbare Importe"
audience: trainer, admin, developer
category: training
order: 32
summary: Die Teilnehmenden exportieren mit xrowextract eine Stichprobe einer Klasse, um ihr Dateiformat kennenzulernen, führen den Importer als Probelauf aus, lesen seinen Bericht und sehen, wie der Abgleich über Remote-IDs einen Import wiederholbar macht, statt zu duplizieren. 60 Minuten.
keywords: migration zu exponential, import, probelauf, abgleich, wiederholbarer import, spaltensatz für die migration, migrating to exponential, xrowextract, csv, dry run, apply, match, remote_id, manifest, repeatable import, migration column set
type: help
---
# Lektion 3.2: Importieren mit xrowextract: Probeläufe und wiederholbare Importe

xrowextract ist der vollständigste Importer, den Exponential hat. Diese Lektion vermittelt die Arbeitsweise, die die
Werkzeugseite empfiehlt: eine Stichprobe der Zielklasse exportieren, um das Format richtig zu treffen, die Quelle in
dieses Format umwandeln und vor `--apply` immer einen Probelauf ausführen. Die Teilnehmenden sehen außerdem, warum
dieselbe Datei zweimal importiert werden kann, ohne Duplikate zu erzeugen.

> **Hinweis**
> Verwenden Sie eine Schulungsinstallation, niemals eine Live-Website. Der Importer schreibt ohne `--apply` nichts; in
> dieser Lektion wenden die Teilnehmenden den Import nur einmal an, und zwar auf ihren eigenen Kursordner.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Migration zu Exponential](../README.md) |
| Modul | [Modul 3: Import- und Exportwerkzeuge](README.md) |
| Dauer | 60 Minuten (15 Präsentation, 45 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler und Administratoren |
| Voraussetzungen | [Lektion 3.1](das-richtige-werkzeug-waehlen.md); ein eigener Kursordner auf der Schulungsinstallation und dessen Knoten-ID |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- eine Stichprobe einer Klasse mit dem Spaltensatz für die Migration exportieren, um das Dateiformat kennenzulernen;
- einen Import als Probelauf ausführen und seinen Bericht lesen;
- die drei Werte von `--match` erklären und sagen, was jeder bei einem zweiten Lauf bewirkt;
- einen Import in einen gegebenen Elternknoten anwenden und das Ergebnis in der Administration prüfen.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Die Import- und Exportwerkzeuge von Exponential, xrowextract](../../../../installation/migration/exponential-tools.md#xrowextract-import-von-csv-xml-und-json-se7enxwebxrowextract).

1. **Das Format kommt aus einem Export** (4 Minuten). Am einfachsten trifft man das Format, indem man ein
   Beispielobjekt der Zielklasse exportiert und seine Spalten übernimmt. Jeder Export trägt ein typisiertes
   Spaltenmanifest (`<file>.manifest.json`), sodass eine Datei mit Manifest exakt zurück abgebildet wird.
2. **Zuerst der Probelauf** (3 Minuten). Ohne `--apply` wird nichts geschrieben. Zeigen Sie den Bericht des
   Probelaufs am Beamer.
3. **Abgleich** (5 Minuten). Die eigene Hilfe des Importers: `--match` ist `remote_id` (der Standard, wenn die Datei
   eine Spalte mit der Remote-ID hat), `object_id` oder `none` (immer anlegen). Mit dem Standard aktualisiert ein
   zweiter Lauf derselben Datei dieselben Objekte. Das ist der „wiederholbare Import“ der Checkliste.
4. **Fremde Dateien** (3 Minuten). Eine Datei aus einem anderen System ordnet ihre Spalten mit
   `--map=column=attribute,...` zu. Große Dateien laufen mit `--background` und werden mit `--resume-from`
   fortgesetzt.

### Häufige Fragen

**Können Redakteure in der Administration importieren?** Ja: Die Schaltfläche **Import/Export** der Knotenansicht,
*Import into this item*, führt denselben Importer aus (Werkzeugseite). Prüfen Sie die Schaltfläche auf Ihrer eigenen
Installation, bevor Sie sie zeigen.

**Was ist mit Bildern?** Bild- und Dateispalten nennen Dateien; kopieren Sie den Speicher oder die Dateien der Quelle
mit dem Export.

**Beachtet der Import die Berechtigungen?** Die Werkzeugseite sagt, dass er die Berechtigungen des Benutzers prüft.

### Stolperfallen

- **`--match=none` mit `--apply` auf einer bereits importierten Datei.** Das erzeugt Duplikate. Die Teilnehmenden
  verwenden `none` in dieser Lektion nur in einem Probelauf.
- **Falscher Elternknoten.** Die Teilnehmenden schreiben die Knoten-ID ihres Kursordners auf Papier, bevor sie
  beginnen.
- **Die CSV-Datei in einem Tabellenkalkulationsprogramm bearbeiten, das das Trennzeichen oder die Kodierung ändert.**
  Verwenden Sie für die Übung einen einfachen Texteditor.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation mit einem Kursordner, der mindestens einen Artikel enthält (aus einem
früheren Kurs, oder legen Sie einen in der Administration an). Arbeiten Sie in `var/tmp/import/` unterhalb des
Wurzelverzeichnisses der Installation.

1. Notieren Sie die Knoten-ID Ihres Kursordners (auf seiner Seite in der Administration angezeigt). Im Folgenden heißt
   sie `<folder>`.
2. Legen Sie das Arbeitsverzeichnis an:

   ```bash
   mkdir -p var/tmp/import
   ```

3. Exportieren Sie die Artikel unterhalb Ihres Ordners mit dem Spaltensatz für die Migration:

   ```bash
   php bin/php/console ext:xrowextract:csv --class=article --node=<folder> --sets=migration --output=var/tmp/import/articles.csv
   ```

4. Sehen Sie sich die Datei und ihr Manifest an:

   ```bash
   head -3 var/tmp/import/articles.csv
   ls var/tmp/import/
   ```

5. Führen Sie den Import derselben Datei als Probelauf aus:

   ```bash
   php bin/php/console ext:xrowextract:import --file=var/tmp/import/articles.csv --class=article --parent=<folder>
   ```

   Lesen Sie den Bericht: Wie viele Zeilen würden angelegt, wie viele aktualisiert?
6. Führen Sie denselben Probelauf mit zusätzlichem `--match=none` aus. Vergleichen Sie den Bericht.
7. Ändern Sie in `articles.csv` in einem Texteditor den Titel eines Artikels (lassen Sie die Remote-ID unverändert).
   Speichern Sie.
8. Wenden Sie den Import mit dem Standardabgleich an:

   ```bash
   php bin/php/console ext:xrowextract:import --file=var/tmp/import/articles.csv --class=article --parent=<folder> --apply
   ```

9. Öffnen Sie in der Administration Ihren Ordner. Prüfen Sie den geänderten Titel und die Anzahl der Artikel.
10. Öffnen Sie die Seite **Versionen verwalten** des Artikels (oder seinen Versionsverlauf) und notieren Sie die neue
    Version.

## Erwartetes Ergebnis

- Schritt 3 hat `articles.csv` geschrieben; Schritt 4 zeigt eine Kopfzeile mit den Migrationsspalten und neben der
  Datei eine `.manifest.json`.
- Schritt 5: Der Probelauf meldet Aktualisierungen für die vorhandenen Artikel (über die Remote-ID abgeglichen) und
  schreibt nichts.
- Schritt 6: Mit `--match=none` meldet der Probelauf für jede Zeile ein Anlegen.
- Nach Schritt 8 enthält der Ordner dieselbe Anzahl an Artikeln wie zuvor; der geänderte Artikel zeigt den neuen
  Titel als neue Version.
- Der Trainer prüft den Ordner einer Person: keine Duplikate, ein geänderter Titel.

## Quiz

1. Was macht der Importer, wenn Sie `--apply` weglassen?
2. Welcher Wert von `--match` legt immer neue Objekte an?
   A. `remote_id` B. `object_id` C. `none` D. `apply`
3. Warum exportiert man ein Beispielobjekt der Zielklasse, bevor man eine fremde Datei umwandelt?
4. Was enthält die Manifestdatei neben einem Export?
5. Eine Datei mit 400 000 Zeilen ist bei Zeile 40 000 stehen geblieben. Welche Option setzt den Import fort?

<details>
<summary>Antworten</summary>

1. Er führt einen Probelauf aus und schreibt nichts
   ([Werkzeugseite, xrowextract](../../../../installation/migration/exponential-tools.md#xrowextract-import-von-csv-xml-und-json-se7enxwebxrowextract)).
2. **C.** `none` legt immer an; `remote_id` ist der Standard, wenn die Datei eine Spalte mit der Remote-ID hat (der
   Hilfetext des Importers, `extension/xrowextract/bin/php/import.php`).
3. Seine Spalten zeigen genau das Format, das der Importer erwartet (Werkzeugseite, xrowextract).
4. Ein typisiertes Spaltenmanifest: pro Spalte Datentyp, Format, Sprache und Importziel (Werkzeugseite, xrowextract).
5. `--resume-from=40001` (mit `--apply` und bei einer großen Datei mit `--background`) (Werkzeugseite, xrowextract).

</details>

## Weiterführende Informationen

- [Die Import- und Exportwerkzeuge von Exponential](../../../../installation/migration/exponential-tools.md): Import und
  Export mit xrowextract sowie Pakete.
- [Die gemeinsame Migrations-Checkliste, Abschnitt 6](../../../../installation/migration/checkliste.md#6-den-import-bauen-und-proben):
  was ein wiederholbarer Import ist.
- [Veröffentlichen, Versionen und der Papierkorb](../../../veroeffentlichen-und-versionen.md): was eine neue Version für
  Redakteure bedeutet.

## Verwandte Seiten

- [Modul 3: Import- und Exportwerkzeuge](README.md)
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
