---
title: Inhalte als CSV, JSON oder XML exportieren
audience: government, admin, editor, developer
category: public-sector
order: 120
summary: Wie Sie Inhalte, die in Exponential liegen (Register, Listen von Servicestellen, als Inhaltsobjekte geführte Verzeichnisse), in Dateien mit offenen Daten verwandeln - der Reiter Export von xrowextract und seine Befehle, das exp:ezcsvexport des Kernels und die Exporte gesammelter Informationen und Benutzerlisten.
keywords: CSV-Export, JSON-Export, XML-Export, xrowextract, ezcsvexport, exp:ezcsvexport, Reiter Export, Register, Verzeichnis, Massenexport, zeitgesteuerter Export, offene Daten, CSV export, JSON export, XML export, export tab, register, directory, bulk export, scheduled export, open data
type: help
---
# Inhalte als CSV, JSON oder XML exportieren

Diese Seite richtet sich an Administratoren und Redaktionen, deren Daten bereits als Inhalt in Exponential liegen: eine
Liste von Servicestellen, ein Beschlussregister, ein Verzeichnis von Einrichtungen. Statt eine eigene Tabelle zu
pflegen, exportieren Sie die Inhalte selbst als Datensatzdatei. Die Werkzeuge sind die Erweiterung `xrowextract`
([xrowextract](../../../../eng-us/features/6.0/extensions/xrowextract.md)) und der Befehl des Kernels
[exp:ezcsvexport](../../../../eng-us/examples/commands/ezcsvexport.md).

## Inhalt

1. [Welches Werkzeug](#welches-werkzeug)
2. [Der Reiter Export von xrowextract](#der-reiter-export-von-xrowextract)
3. [Von der Kommandozeile](#von-der-kommandozeile)
4. [Der Befehl exp:ezcsvexport des Kernels](#der-befehl-expezcsvexport-des-kernels)
5. [Die exportierte Datei veröffentlichen](#die-exportierte-datei-veröffentlichen)
6. [Bevor Sie einen Export veröffentlichen](#bevor-sie-einen-export-veröffentlichen)

## Welches Werkzeug

| Bedarf | Werkzeug |
|---|---|
| Eine Klasse als CSV, JSON oder XML, mit Vorschau, Filtern und gespeicherten Voreinstellungen | `xrowextract`, **Export > One class of content export** |
| Jede Klasse unterhalb eines Knotens als ein Archiv | `xrowextract`, **Multi class of content export** |
| Ein regelmäßiger Export, der in einen Ordner, per SFTP oder nach S3 geliefert wird | `xrowextract`, **Schedules** |
| Eine schnelle CSV-Datei pro Klasse von der Kommandozeile, ohne Erweiterung | `exp:ezcsvexport` |
| Formulareinsendungen | **Setup > Gesammelte Informationen**, CSV-Export pro Formular ([Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md)) |

`xrowextract` wird mit Exponential installiert, muss aber auf Ihrer Website aktiv sein (`ActiveExtensions[]`); fragen
Sie Ihre Administration. Seine Seiten brauchen die Richtlinien der Erweiterung
([Richtlinien von xrowextract](../../../../eng-us/features/6.0/extensions/xrowextract.md#policies)).

## Der Reiter Export von xrowextract

1. Öffnen Sie **Export** im oberen Menü (`/xrowextract/csv`), dann **One class of content export**.
2. Wählen Sie die Klasse (zum Beispiel Ihre Klasse `service_point`) und den Startknoten.
3. Wählen Sie das Format: CSV, JSON oder XML. Wählen Sie die Sprachen, wenn die Inhalte übersetzt sind: eine Zeile pro
   Übersetzung.
4. Fügen Sie bei Bedarf Filter hinzu (Datum, Sektion, Zustand, Sichtbarkeit, Attributwerte).
5. Klicken Sie auf **Preview**, um die ersten Zeilen so zu sehen, wie eine Tabellenkalkulation sie zeigen wird.
6. Laden Sie die Datei herunter oder speichern Sie die Einstellungen als Voreinstellung, um den Export später zu
   wiederholen.

Große Exporte können im Hintergrund laufen und nach Abschluss im Reiter **Jobs** heruntergeladen werden.

## Von der Kommandozeile

```bash
# one class below a node, as CSV
php extension/xrowextract/bin/php/csv.php --class=<class identifier> --node=<node id> --output=var/export/service-points.csv

# the same as JSON, visible content only
php extension/xrowextract/bin/php/csv.php --class=<class identifier> --node=<node id> --format=json \
  --visibility=visible --output=var/export/service-points.json

# which columns a class offers
php extension/xrowextract/bin/php/csv.php --list-columns --class=<class identifier>
```

Zeitpläne (`ext:xrowextract:schedule`) führen Exporte regelmäßig aus und können die Datei per SFTP, FTP/FTPS, in einen
NAS-Ordner, nach S3, per WebDAV oder an einen HTTP-Endpunkt liefern.

## Der Befehl exp:ezcsvexport des Kernels

Ohne die Erweiterung exportiert der Kernel den Teilbaum unterhalb eines Knotens in eine CSV-Datei pro Inhaltsklasse:

```bash
./console exp:ezcsvexport --storage-dir=var/export/csv <node id>
php bin/php/ezcsvexport.php --storage-dir=var/export/csv <node id>
```

Er hat keine Filter und keine Spaltenauswahl; prüfen Sie die Spalten vor dem Veröffentlichen.

## Die exportierte Datei veröffentlichen

Der Export ist eine Datei auf dem Server oder in Ihren Downloads. Um sie als offene Daten zu veröffentlichen, laden Sie
sie als Distribution des Datensatzes hoch (siehe „Datensatzdateien veröffentlichen“), mit dem Datum des Exports im Feld
„Zuletzt geändert“ des Datensatzes. Bei einem regelmäßigen Export ersetzen Sie die Datei jedes Mal in derselben
Distribution, damit die Seitenadresse gleich bleibt.

## Bevor Sie einen Export veröffentlichen

- [ ] Entfernen Sie Spalten mit personenbezogenen Daten: Eigentümer, Ersteller, Bearbeiter, E-Mail-Adressen, interne
  Vermerke. Exporte können solche Spalten enthalten, und Benutzerkonten können von Benutzern mit der passenden
  Richtlinie als besondere Spalten exportiert werden.
- [ ] Entfernen Sie interne Kennungen, die Nachnutzer nicht brauchen, oder behalten Sie sie bewusst als stabile
  Schlüssel.
- [ ] Prüfen Sie Zeichenkodierung (UTF-8) und Trennzeichen in einer Tabellenkalkulation und einem Texteditor.
- [ ] Prüfen Sie eine Zeile gegen die veröffentlichte Seite.
- [ ] Halten Sie fest, welcher Export (Voreinstellung oder Befehl) die Datei erzeugt hat, damit die nächste
  Aktualisierung gleich ist.

## Verwandte Seiten

- [xrowextract](../../../../eng-us/features/6.0/extensions/xrowextract.md)
- [Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md)
- [exp:ezcsvexport](../../../../eng-us/examples/commands/ezcsvexport.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
