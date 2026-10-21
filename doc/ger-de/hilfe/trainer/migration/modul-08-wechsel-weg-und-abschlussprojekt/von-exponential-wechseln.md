---
title: "Lektion 8.1: Migration weg von Exponential"
audience: trainer, admin, developer
category: training
order: 81
summary: Die Teilnehmenden erstellen einen vollständigen, selbstbeschreibenden Export einer Exponential-Website - das Website-Archiv von xrowextract, den Speicher, einen Datenbank-Dump, Layouts, Formulardaten -, wandeln Rich Text in HTML um, bauen die Weiterleitungstabelle aus den URL-Aliasen auf und behandeln Benutzer und personenbezogene Daten nach vereinbarten Regeln. 45 Minuten.
keywords: migration zu exponential, exponential verlassen, export, website-archiv, manifest, speicher, datenbank-dump, layout-export, xmltext zu html, weiterleitungstabelle, personenbezogene daten, ihre inhalte gehören ihnen, migrating to exponential, leaving exponential, xrowextract archive, site archive, storage, database dump, layouts export, XmlText to HTML, redirect table, personal data, your content is yours
type: help
---
# Lektion 8.1: Migration weg von Exponential

Ein Migrationskurs ist nur ehrlich, wenn er auch den Weg hinaus lehrt. Die Dokumentation sagt es klar: Ihre Inhalte
gehören Ihnen. Exponential speichert sie in einem offenen Datenbankschema und in gewöhnlichen Dateien und hat
Werkzeuge, die sie in einfachen Formaten ausgeben. Diese Lektion vermittelt den vollständigen Export und was das
Zielsystem davon braucht.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Migration zu Exponential](../README.md) |
| Modul | [Modul 8: Exponential verlassen und das Abschlussprojekt](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Administratoren und Entwickler |
| Voraussetzungen | [Modul 3](../modul-03-import-und-export-werkzeuge/README.md); Shell-Zugang zu einer Schulungsinstallation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- aufzählen, was von einer Exponential-Website exportiert wird, und das Werkzeug für jeden Teil nennen;
- mit xrowextract ein Website-Archiv erstellen und es mit den Zählungen der Administration abgleichen;
- zwischen dem Rendern und dem Transformieren von XmlText in HTML wählen;
- aus URL-Aliasen und Wildcards eine Weiterleitungstabelle aufbauen;
- erklären, warum das Website-Archiv nie Passwort-Hashes enthält und was das für die Benutzer bedeutet.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Migration weg von Exponential](../../../../installation/migration/exponential-verlassen.md) und die Exporttabelle
der [Werkzeugseite, Abschnitt 2](../../../../installation/migration/exponential-tools.md#2-export-aus-exponential).

1. **Was exportiert wird** (4 Minuten). Zeigen Sie die Tabelle aus Abschnitt 1: das Website-Archiv von xrowextract
   (pro Klasse eine CSV-Datei mit IDs, Remote-ID, Knoten, URL-Alias, Datumsangaben und jedem Attribut, eine
   `manifest.json`, eine `README.txt`), einzelne Klassen, Pakete für ein anderes Exponential, Layouts, das
   Speicherverzeichnis, den Datenbank-Dump, Formulareinsendungen, Einwilligungsnachweise, das Audit-Log.
2. **Die sieben Schritte** (4 Minuten). Einfrieren oder die Uhrzeit notieren; Website-Archiv; Speicher;
   Datenbank-Dump; Layouts; Adressliste; das Archiv mit den Zählungen der Administration abgleichen.
3. **Rich Text** (3 Minuten). Rendern (die Templates der Website oder REST v2 mit `OutputFormat=xhtml`) oder das XML
   in einem Skript transformieren; Embeds werden zu Bild-Tags oder Links auf die exportierten Dateien.
4. **Benutzer** (4 Minuten). Das Archiv enthält nie Passwort-Hashes. Die Benutzer setzen auf dem Zielsystem neue
   Passwörter, es sei denn, das Zielsystem kann Standardwerte von PHP `password_hash()` prüfen; entscheiden Sie das
   mit der Datenschutzberatung. Exportieren Sie nur die personenbezogenen Daten, die das Zielsystem braucht.

### Häufige Fragen

**Gibt es eine Anleitung pro Zielsystem?** Ja: WordPress, Drupal, Joomla, Shopify, Contentful, Website-Baukästen und
die Produkte der Familie; die Seite zum Verlassen verlinkt sie.

**Können wir ein statisches Archiv der alten Website behalten?** `exp:makestaticcache` schreibt statische Seiten, oder
ein spiegelnder Crawl der Live-Website; behalten Sie den Speicher für die Originalbilder (Anleitung für statisches
HTML, Abschnitt 5).

**Was bleibt auf dem alten System?** Lassen Sie es nur lesbar, bis das neue abgenommen ist (Seite zum Verlassen,
Checkliste).

### Stolperfallen

- **Ohne den Speicher exportieren.** Das Archiv verweist auf Dateien; kopieren Sie `var/<site>/storage/` mit.
- **Personenbezogene Daten ohne Regeln archivieren.** Bewahren Sie sie mit Zugriffskontrollen auf oder löschen Sie
  sie, wie es die Aufbewahrungsregeln sagen.
- **Andere Aliase vergessen.** Das Archiv enthält den Hauptalias jedes Knotens; eigene Aliase, andere Sprachen und
  Wildcards liegen in `ezurlalias_ml` und `ezurlwildcard`.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation. Der Export schreibt Dateien nur nach `var/tmp/export/`.

1. Legen Sie das Arbeitsverzeichnis an:

   ```bash
   mkdir -p var/tmp/export
   ```

2. Erstellen Sie ein Website-Archiv von allem, als ZIP:

   ```bash
   php bin/php/console ext:xrowextract:archive --set=everything --format=zip --output=var/tmp/export/
   ```

3. Listen Sie das Archiv auf und öffnen Sie seine `manifest.json` und `README.txt`:

   ```bash
   ls -l var/tmp/export/
   unzip -l var/tmp/export/*.zip | head -30
   ```

4. Wählen Sie eine Klasse aus dem Archiv und zählen Sie ihre Zeilen; vergleichen Sie mit der Anzahl der Objekte
   dieser Klasse in der Administration (**Setup > Klassen**, die Seite der Klasse).
5. Prüfen Sie, dass kein Passwort-Hash im Archiv ist: Durchsuchen Sie die CSV-Datei der Benutzerklasse nach den
   Spaltennamen von `password_hash`. Notieren Sie, was Sie finden.
6. Exportieren Sie die Layouts, wenn Ihre Installation Exponential Layouts hat:

   ```bash
   php bin/php/console exp:layouts export --all --output=var/tmp/export/layouts.zip
   ```

7. Schreiben Sie den Weiterleitungsplan: Welche Spalte des Archivs liefert die Hauptadresse jedes Knotens, und welche
   zwei Tabellen enthalten die anderen Aliase und die Wildcards?
8. Schreiben Sie zwei Sätze für Benutzer über die Passwörter auf dem Zielsystem und verwenden Sie für jede Adresse
   `example.invalid`.
9. Entfernen Sie `var/tmp/export/`, wenn Sie fertig sind, nachdem der Trainer Schritt 4 geprüft hat.

## Erwartetes Ergebnis

- Schritt 2 hat ein Archiv mit einer CSV-Datei pro Klasse, `manifest.json` und `README.txt` geschrieben.
- Schritt 4: Die Zeilenzahl der Klasse stimmt mit der Anzahl der Objekte in der Administration überein (eine Zeile pro
  Objekt und Sprache).
- Schritt 5: keine Spalte mit Passwort-Hashes.
- Schritt 6 hat `layouts.zip` geschrieben, oder die Person hat notiert, dass die Installation keine Layouts hat.
- Schritt 7: die URL-Alias-Spalte des Archivs sowie `ezurlalias_ml` und `ezurlwildcard`.
- Der Trainer prüft Schritt 4 auf einem Bildschirm.

## Quiz

1. Welches Werkzeug erstellt einen vollständigen, selbstbeschreibenden Export einer Website?
   A. `php ezpm.php export` B. Das Website-Archiv von xrowextract C. `exp:layouts export` D. `sevenx_dse`
2. Was enthält das Website-Archiv absichtlich nie?
3. Nennen Sie die beiden Wege, XmlText für das Zielsystem in HTML umzuwandeln.
4. Wo liegen eigene URL-Aliase und Wildcards, die die Hauptalias-Spalte des Archivs nicht abdeckt?
5. Richtig oder falsch: Die alte Website kann abgeschaltet werden, sobald der Export abgeschlossen ist.

<details>
<summary>Antworten</summary>

1. **B** ([Seite zum Verlassen, Abschnitt 1](../../../../installation/migration/exponential-verlassen.md#1-was-sie-exportieren-sollten)).
2. Passwort-Hashes (Abschnitt 1 und [Abschnitt 5](../../../../installation/migration/exponential-verlassen.md#5-benutzer-und-personenbezogene-daten)).
3. Rendern (Templates oder REST v2 mit `OutputFormat=xhtml`) oder das XML in einem Skript transformieren
   ([Abschnitt 3](../../../../installation/migration/exponential-verlassen.md#3-rich-text)).
4. In `ezurlalias_ml` und `ezurlwildcard`
   ([Abschnitt 4](../../../../installation/migration/exponential-verlassen.md#4-adressen-für-die-weiterleitungen)).
5. **Falsch.** Lassen Sie sie nur lesbar, bis die neue abgenommen ist
   ([Abschnitt 6](../../../../installation/migration/exponential-verlassen.md#6-checkliste)).

</details>

## Weiterführende Informationen

- [Migration weg von Exponential](../../../../installation/migration/exponential-verlassen.md): jeder Export und der Plan.
- [Die Import- und Exportwerkzeuge von Exponential, Abschnitt 2](../../../../installation/migration/exponential-tools.md#2-export-aus-exponential):
  die Exportwerkzeuge mit Befehlen.
- [WordPress und Exponential, Abschnitt 5](../../../../installation/migration/wordpress.md#5-von-exponential-zu-wordpress):
  ein Zielsystem im Detail.

## Verwandte Seiten

- [Modul 8: Exponential verlassen und das Abschlussprojekt](README.md)
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
