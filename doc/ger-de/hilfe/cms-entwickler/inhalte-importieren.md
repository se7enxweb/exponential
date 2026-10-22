---
title: Inhalte importieren
audience: cms-developer
category: cms-development
order: 27
summary: Für CMS-Entwickler, die Inhalte in eine Website laden, die vorhandenen Importwerkzeuge und welches zu welcher Quelle passt. Die Seite behandelt xrowextract, ezcsvimport des Kernels, Pakete, den ZIP-Importer, Office-Dokumente, RSS-Import, Übertragungsdateien für Layouts und die HTTP-Schnittstellen und wann ein eigenes Skript die richtige Antwort ist.
keywords: Import, Inhaltsimport, CSV-Import, xrowextract, ezcsvimport, Pakete, ezpkg, ezmultiupload, ZIP-Import, ezodf, RSS-Import, Layouts exportieren und importieren, REST, expservices, Migration, Remote-ID, Probelauf, import, content import, CSV import, packages, ZIP import, RSS import, layouts export import, remote id, dry run
type: help
---
# Inhalte importieren

Diese Seite richtet sich an Entwickler, die eine Website mit Inhalten von anderswo füllen müssen: aus einer Tabelle,
einem anderen CMS, einer alten Exponential-Website, einem Ordner mit Fotos. Verwenden Sie zuerst die vorhandenen
Werkzeuge und schreiben Sie ein Skript nur für das, was sie nicht können. Jedes Werkzeug unten ist ausführlich unter
[Import- und Exportwerkzeuge von Exponential](../../installation/migration/exponential-tools.md) beschrieben; diese Seite
hilft Ihnen bei der Wahl.

## Inhalt

1. [Welches Werkzeug für welche Quelle](#welches-werkzeug-für-welche-quelle)
2. [xrowextract: der wichtigste Importer](#xrowextract-der-wichtigste-importer)
3. [ezcsvimport: der einfache Importer des Kernels](#ezcsvimport-der-einfache-importer-des-kernels)
4. [Pakete: zwischen Exponential-Installationen](#pakete-zwischen-exponential-installationen)
5. [Medien, Dokumente und Feeds](#medien-dokumente-und-feeds)
6. [Layouts](#layouts)
7. [Über HTTP](#über-http)
8. [Ein eigenes Skript](#ein-eigenes-skript)
9. [Regeln für jeden Import](#regeln-für-jeden-import)

## Welches Werkzeug für welche Quelle

| Quelle | Erste Wahl | Hinweise |
|---|---|---|
| Eine Tabelle oder CSV-Datei einer Art von Element | xrowextract mit `--map` | standardmäßig ein Probelauf |
| Ein Export eines anderen CMS (WordPress, Drupal, Joomla) | in CSV, XML oder JSON umwandeln, dann xrowextract | kein Werkzeug liest diese Exporte direkt |
| Eine andere Exponential-Website | Pakete oder eine Kopie der Datenbank, wenn die ganze Website umzieht | |
| Ein Ordner mit Bildern und Dateien | der ZIP-Importer der Upload-Seite | behält auf Wunsch die Ordnerstruktur |
| Office-Dokumente | `ezodf` | braucht LibreOffice oder OpenOffice auf dem Server |
| Ein News-Feed während eines Übergangs | RSS-Import | nur die neuesten Elemente des Feeds |
| Layouts und ihre Regeln | `exp:layouts export` und `import` | zwischen Exponential-Installationen |
| Ein System, das nur über HTTP erreichbar ist | ein eigenes Skript mit der Kernel-API | |

Die vollständige Tabelle: [Welches Werkzeug für welche Aufgabe](../../installation/migration/exponential-tools.md#5-welches-werkzeug-für-welche-aufgabe).

## xrowextract: der wichtigste Importer

`xrowextract` (`se7enxweb/xrowextract`, von der `composer.json` im Hauptverzeichnis verlangt) liest CSV, XML oder JSON
und legt Objekte einer Klasse unter einem übergeordneten Knoten an oder aktualisiert sie. Ohne `--apply` schreibt es
nichts, es kann vorhandene Objekte für Aktualisierungen zuordnen, ordnet fremde Spalten Attributen zu, verarbeitet
große Dateien im Hintergrund und setzt nach einer Unterbrechung fort:

```bash
./console ext:xrowextract:import --file=var/tmp/import/events.csv --class=exp_event --parent=<node id> \
    --map=title=title,start=start_date
./console ext:xrowextract:import --file=var/tmp/import/events.csv --class=exp_event --parent=<node id> \
    --map=title=title,start=start_date --apply
```

Am einfachsten treffen Sie das Format, indem Sie ein Beispielobjekt der Zielklasse mit xrowextract exportieren und
seine Spalten übernehmen ([xrowextract](../../installation/migration/exponential-tools.md#xrowextract-import-von-csv-xml-und-json-se7enxwebxrowextract),
[Importieren mit xrowextract](../trainer/migration/modul-03-import-und-export-werkzeuge/importieren-mit-xrowextract.md)).

## ezcsvimport: der einfache Importer des Kernels

```bash
./console exp:ezcsvimport --class=exp_event --creator=<user object id> --storage-dir=var/tmp/import/files <node id> var/tmp/import/events.csv
```

Die Datei verwendet `;` als Trennzeichen und hat keine Kopfzeile: Spalte *n* geht über `fromString()` an das *n*-te
Attribut der Klasse. Es legt nur an, ein zweiter Lauf erzeugt also Duplikate; verwenden Sie es für ein erstes Laden
einer einfachen Klasse.

## Pakete: zwischen Exponential-Installationen

Pakete (`.ezpkg`) übertragen Klassen und Inhaltsobjekte mit ihren Dateien von einer Exponential-Installation in eine
andere und sind der Weg, Klassen mit Ihrem Projekt auszuliefern ([Pakete](../../anleitungen/pakete.md),
[Seitenpakete und Installer](seitenpakete-und-installer.md)). Sie sind kein Format, das andere Systeme erzeugen.

## Medien, Dokumente und Feeds

- **ZIP-Importer** (`ezmultiupload`): Jede Datei wird je nach Typ zu einem Bild- oder Dateiobjekt; *Keep the folder
  structure* legt Ordner an. Die Grenzen stehen in `ezmultiupload.ini [ZipSettings]`. Bilder, deren Klasse einen
  Alternativtext verlangt, werden übersprungen, weil ein ZIP keinen hat ([Medien und Uploads](../medien-und-uploads.md)).
- **Office-Dokumente** (`ezodf`): Writer-Dokumente zu Inhaltsobjekten und zurück.
- **RSS-Import** (**Setup** > **RSS**): Neue Elemente eines Feeds werden zu Objekten einer gewählten Klasse; der
  Cronjob `rssimport.php` führt ihn aus ([RSS-Feeds](../../anleitungen/rss-feeds.md)).

## Layouts

```bash
./console exp:layouts export --all --output=var/tmp/layouts.zip
./console exp:layouts import var/tmp/layouts.zip --dry-run
```

Layouts, gemeinsame Layouts und ihre Zuordnungsregeln werden als eine ZIP-Datei übertragen
([Layouts exportieren und importieren](../zwei-wege-zum-bauen/layouts-exportieren-und-importieren.md)).

## Über HTTP

Die REST-Schnittstelle kann ab v2 Knoten anlegen und entfernen (Aktualisierungen antworten mit 501, noch nicht
implementiert), und `expservices` bietet die Funktionen der Administration als HTTP-Dienste an
([Die REST-API](die-rest-api.md), [Entfernte Dienste und Apps](../../anleitungen/entfernte-dienste-und-apps.md)). Verwenden Sie
sie, wenn der Importer auf einem anderen Rechner laufen muss; bei großen Importen ist ein Skript auf dem Server
schneller.

## Ein eigenes Skript

Passt kein Werkzeug, schreiben Sie ein Skript mit der Kernel-API: Lesen Sie die Quelle, ordnen Sie jedes Element zu und
legen Sie es dann über `eZContentFunctions` mit einer Remote-ID je Element an oder aktualisieren Sie es, damit Läufe
wiederholt werden können ([Inhalte aus PHP](inhalte-aus-php.md) und das Gerüst unter
[Ein eigenes Importskript](../../installation/migration/exponential-tools.md#4-ein-eigenes-importskript-das-grundgerüst)).

## Regeln für jeden Import

1. **Zuerst das Modell.** Legen Sie die Klassen und den Ordnerbaum vor dem Import an
   ([Ein Inhaltsmodell entwerfen](ein-inhaltsmodell-entwerfen.md)).
2. **Remote-IDs aus der Quelle** (`wp-1234`, `crm-42`) an jedem Objekt: Aktualisierungen statt Duplikaten, und
   Relationen lassen sich in einem zweiten Durchgang auflösen.
3. **Zuerst Medien**, dann Behälter in der Reihenfolge der übergeordneten Knoten, dann Elemente, dann Relationen und
   innere Links.
4. **Probelauf, dann eine Kopie, dann live.** Halten Sie die Quelldateien außerhalb des Web-Roots (`var/tmp/` ist auf
   einem Entwicklungsrechner in Ordnung).
5. **Danach**: Bauen Sie den Suchindex neu auf (`php bin/php/updatesearchindex.php --clean`), prüfen Sie die URL-Aliase,
   leeren Sie die Inhalts-Caches ([Suche](suche.md), [URL-Aliase und Routing](url-aliase-und-routing.md)).
6. **Keine personenbezogenen Daten, die Sie nicht brauchen.** Importieren Sie Benutzerkonten nur mit Rechtsgrundlage,
   und testen Sie mit Adressen unter `example.invalid`.

## Verwandte Seiten

- [Inhalte auf Exponential abbilden](../../installation/migration/inhaltsmodell-zuordnen.md)
- [Checkliste für die Migration](../../installation/migration/checkliste.md)
- [Das richtige Import- oder Exportwerkzeug wählen](../trainer/migration/modul-03-import-und-export-werkzeuge/das-richtige-werkzeug-waehlen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
