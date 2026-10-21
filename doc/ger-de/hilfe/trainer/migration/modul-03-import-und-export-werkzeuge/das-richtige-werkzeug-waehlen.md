---
title: "Lektion 3.1: Das richtige Import- oder Exportwerkzeug wählen"
audience: trainer, admin, developer
category: training
order: 31
summary: Die Teilnehmenden kennen die Import- und Exportwerkzeuge von Exponential (xrowextract, ezcsvimport, Pakete, den ZIP-Importer, ezodf, RSS-Import, Layout-Transfer, REST) und wählen das richtige für eine Quelle, bevor sie ein Skript schreiben. 45 Minuten.
keywords: migration zu exponential, importwerkzeuge, exportwerkzeuge, pakete, zip-import, rss-import, welches werkzeug, migrating to exponential, import tools, export tools, xrowextract, ezcsvimport, packages, ezpkg, ezmultiupload, zip import, ezodf, rss import, exp:layouts, rest api, which tool
type: help
---
# Lektion 3.1: Das richtige Import- oder Exportwerkzeug wählen

Die Regel der Dokumentation ist einfach: Verwenden Sie zuerst die vorhandenen Werkzeuge und schreiben Sie ein Skript
nur für das, was sie nicht können. Diese Lektion gibt den Teilnehmenden eine Übersicht über diese Werkzeuge, was
jedes liest und schreibt, und die Tabelle, die einer Quelle ihre erste Wahl zuordnet.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Migration zu Exponential](../README.md) |
| Modul | [Modul 3: Import- und Exportwerkzeuge](README.md) |
| Dauer | 45 Minuten (20 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler und Administratoren |
| Voraussetzungen | [Modul 2](../modul-02-inhalte-zuordnen/README.md); eine Schulungsinstallation mit Shell-Zugang und einem Administratorkonto |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Importwerkzeuge von Exponential nennen und sagen, was jedes liest;
- die Exportwerkzeuge nennen und sagen, was jedes schreibt;
- für eine gegebene Quelle anhand der Tabelle „Welches Werkzeug für welche Aufgabe“ das erste Werkzeug wählen;
- mit xrowextract die Klassen und Exportspalten ihrer Installation auflisten, ohne etwas zu schreiben.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Die Import- und Exportwerkzeuge von Exponential](../../../../installation/migration/exponential-tools.md),
Abschnitte 1, 2 und 5.

1. **Importwerkzeuge** (8 Minuten). Jeweils in einem Satz:
   - **xrowextract**: der vollständigste Importer; liest CSV, XML oder JSON, legt Objekte einer Klasse unterhalb
     eines Elternknotens an oder aktualisiert sie; standardmäßig ein Probelauf; Hintergrundjobs und Fortsetzen.
   - **ezcsvimport** (Kernel): einfach; Trennzeichen `;`, keine Kopfzeile, nur Anlegen.
   - **Pakete** (`.ezpkg`): Klassen und Inhalte zwischen Exponential-Installationen.
   - **Der ZIP-Importer** der Upload-Seite (ezmultiupload): eine Medienbibliothek in einem Schwung.
   - **ezodf**: Office-Dokumente.
   - **RSS-Import**: holt während einer Übergangszeit laufend einen Feed herein; keine vollständige Migration.
   - **exp:layouts**: Layouts und Regeln als ZIP-Transferdatei zwischen Exponential-Installationen.
   - **REST und expservices**: wenn der Importer auf einem anderen Rechner läuft.
2. **Exportwerkzeuge** (5 Minuten). Zeigen Sie die Tabelle aus Abschnitt 2: CSV und Website-Archiv von xrowextract,
   Pakete, der CSV-Export des Kernels, Layout-Export, bccie für Formulardaten, der Datenbank-Dump.
3. **Welches Werkzeug für welche Aufgabe** (4 Minuten). Zeigen Sie Abschnitt 5. Eine Tabelle geht mit `--map` an
   xrowextract; nur ein System mit einer REST-API braucht ein eigenes Skript (Lektion 3.3).
4. **Ein ehrlicher Satz** (3 Minuten). Kein Werkzeug liest Exporte von WordPress, Drupal oder Joomla direkt; die
   Anleitungen zu den Wegen wandeln sie in ein Format um, das xrowextract liest, oder verwenden das Skriptgerüst.

### Häufige Fragen

**Gehört xrowextract zu jeder Installation?** Es wird von der `composer.json` im Wurzelverzeichnis von Exponential
6.0.15 verlangt, wie die anderen Erweiterungen, die die Werkzeugseite nennt. Die Werkzeuge einer Erweiterung sind
verfügbar, wenn sie installiert und aktiv ist.

**Warum nicht über REST importieren?** Die Werkzeugseite: Für große Importe ist ein Skript auf dem Server schneller
und einfacher. Aktualisieren über REST antwortet mit 501 (noch nicht implementiert).

**Können Pakete Inhalte aus WordPress transportieren?** Nein. Pakete sind das Werkzeug für den Umzug zwischen
Exponential-Installationen; sie sind kein Format, das andere Systeme erzeugen.

### Stolperfallen

- **Am ersten Tag ein Skript schreiben.** Prüfen Sie zuerst die Werkzeuge; eine CSV-Datei und `--map` reichen oft
  aus.
- **ezcsvimport für eine Aktualisierung verwenden.** Es legt nur an. Ein zweiter Lauf dupliziert.
- **Die Dateien vergessen.** Exporte verweisen auf Bilder und Dateien im Speicherverzeichnis; kopieren Sie es mit.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation. Jeder Befehl in dieser Übung liest nur.

1. Listen Sie im Wurzelverzeichnis der Installation die Klassen auf, die Objekte unterhalb des Wurzelknotens haben:

   ```bash
   php bin/php/console ext:xrowextract:csv --list-classes --node=2
   ```

2. Listen Sie die Spalten auf, die xrowextract für die Klasse `article` (oder eine Klasse aus Schritt 1) exportieren
   kann:

   ```bash
   php bin/php/console ext:xrowextract:csv --list-columns --class=article
   ```

3. Zeigen Sie eine Vorschau der ersten Zeilen eines Exports mit dem Spaltensatz für die Migration an, ohne eine Datei
   zu schreiben:

   ```bash
   php bin/php/console ext:xrowextract:csv --class=article --node=2 --sets=migration --preview=3
   ```

4. Notieren Sie die Spalten, die jede Zeile identifizieren (Objekt-ID, Remote-ID), und die, die sie im Baum
   platzieren (Remote-ID des Elternelements, Priorität).
5. Öffnen Sie in der Administration **Setup > Pakete** und notieren Sie, welche Pakete Ihre Installation hat.
6. Schreiben Sie für jede der folgenden Quellen das erste und das zweite Werkzeug aus
   [Abschnitt 5 der Werkzeugseite](../../../../installation/migration/exponential-tools.md#5-welches-werkzeug-für-welche-aufgabe) auf:
   eine Tabelle mit 400 Produkten; ein Ordner mit 2 000 Fotos; eine andere Exponential-6-Website; ein Nachrichten-Feed;
   ein System mit nur einer REST-API.

## Erwartetes Ergebnis

- Schritt 1 listet Klassen mit der Anzahl ihrer Objekte auf; Schritt 2 listet Attributbezeichner und Sonderspalten
  auf.
- Schritt 3 gibt drei Zeilen aus, deren Spalten die Objekt-ID, die Remote-ID, den Klassenbezeichner, die Sprache, die
  Remote-ID des Elternelements und jedes Attribut enthalten.
- Schritt 6: xrowextract mit `--map`; der ZIP-Importer; Pakete (oder eine Kopie der Datenbank, wenn die ganze Website
  umzieht); RSS-Import; ein eigenes Skript mit der Kernel-API.
- Der Trainer prüft Schritt 3 auf einem Bildschirm und die fünf Antworten aus Schritt 6.

## Quiz

1. Welches Werkzeug importiert eine CSV-Datei, deren Spalten Sie mit `--map` benennen, standardmäßig als Probelauf?
   A. ezcsvimport B. xrowextract C. ezodf D. RSS-Import
2. Was macht ezcsvimport aus dem Kernel bei einem zweiten Lauf mit einem bestehenden Objekt?
3. Welches Werkzeug überträgt Layouts und ihre Regeln zwischen Exponential-Installationen?
4. Richtig oder falsch: Pakete sind ein gutes Format für Inhalte, die aus WordPress kommen.
5. Warum ist ein Skript auf dem Server bei einem großen Import meist besser als REST?

<details>
<summary>Antworten</summary>

1. **B** ([Werkzeugseite, Abschnitt 1](../../../../installation/migration/exponential-tools.md#1-import-in-exponential)).
2. Nichts: Es legt nur an, deshalb erzeugt ein zweiter Lauf ein Duplikat (Werkzeugseite, Abschnitt 1,
   `ezcsvimport`).
3. `exp:layouts export` und `exp:layouts import`
   ([Werkzeugseite, Abschnitt 1](../../../../installation/migration/exponential-tools.md#layouts-se7enxwebexplayouts)).
4. **Falsch.** Pakete übertragen Inhalte zwischen Exponential-Installationen; andere Systeme erzeugen sie nicht
   (Werkzeugseite, Abschnitt 1).
5. Es ist schneller und einfacher, und Aktualisierungen über REST antworten mit 501 (Werkzeugseite, Abschnitt 1, „The
   REST API and expservices“).

</details>

## Weiterführende Informationen

- [Die Import- und Exportwerkzeuge von Exponential](../../../../installation/migration/exponential-tools.md): jedes
  Werkzeug mit seinen tatsächlichen Optionen.
- [Pakete](../../../../anleitungen/pakete.md): Pakete in der Administration und in der Shell.
- [Remote-Dienste und Apps](../../../../anleitungen/entfernte-dienste-und-apps.md): die HTTP-Dienste für Importer auf einem
  anderen Rechner.

## Verwandte Seiten

- [Modul 3: Import- und Exportwerkzeuge](README.md)
- [Migration zu Exponential: Kursübersicht](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
