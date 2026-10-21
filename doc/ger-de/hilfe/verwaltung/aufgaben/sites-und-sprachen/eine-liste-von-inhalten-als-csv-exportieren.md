---
title: Eine Liste von Inhalten als CSV exportieren
audience: government, admin, editor
category: public-sector
order: 250
summary: Aufgabe für Administratoren und Redakteure - die Objekte einer Klasse unterhalb eines Ordners (ein Register, eine Liste von Servicestellen) als CSV exportieren, mit xrowextract in der Administration oder exp:ezcsvexport des Kernels, und vor dem Veröffentlichen prüfen.
keywords: CSV-Export, Register exportieren, Tabellenkalkulation, offene Daten, eine Klasse, CSV export, xrowextract, ezcsvexport, exp:ezcsvexport, register export, spreadsheet, open data, one class
type: help
---
# Eine Liste von Inhalten als CSV exportieren

Diese Aufgabe richtet sich an Administratoren und Redakteure. Hintergrund:
[Inhalte als CSV, JSON oder XML exportieren](../../offene-daten/inhalte-als-csv-json-xml-exportieren.md).

## Mit xrowextract (wenn aktiv)

1. **Export** im oberen Menü (`/xrowextract/csv`), **One class of content export**.
2. Wählen Sie die Klasse und den Startknoten; wählen Sie CSV; fügen Sie bei Bedarf Filter hinzu (nur sichtbare, ein
   Datumsbereich).
3. **Vorschau**; prüfen Sie die Spalten und entfernen Sie Spalten mit personenbezogenen Daten.
4. Laden Sie die Datei herunter oder speichern Sie die Einstellungen als Vorlage für das nächste Mal.

Entsprechung auf der Kommandozeile:

```bash
php extension/xrowextract/bin/php/csv.php --class=<class> --node=<node id> --visibility=visible --output=var/export/list.csv
```

## Mit dem Befehl des Kernels

```bash
./console exp:ezcsvexport --storage-dir=var/export/csv <node id>
```

Eine CSV-Datei je Klasse unterhalb des Knotens; keine Filter und keine Auswahl der Spalten.

## Prüfen

- Öffnen Sie die Datei in einer Tabellenkalkulation und in einem Texteditor: UTF-8, Kopfzeile, die erwartete Zahl von
  Zeilen.
- Keine Spalten mit Eigentümern, Erstellern, E-Mail-Adressen oder internen Notizen, sofern nicht beabsichtigt.

## Probleme

| Problem | Lösung |
|---|---|
| Kein Reiter **Export** | `xrowextract` ist nicht aktiv, oder Ihrer Rolle fehlen seine Richtlinien |
| Akzente in der Tabellenkalkulation verstümmelt | importieren Sie die Datei als UTF-8 |

## Verwandte Seiten

- [xrowextract](../../../../funktionen/6.0/erweiterungen/xrowextract.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
