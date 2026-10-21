---
title: Datensatzdateien veröffentlichen
audience: government, editor
category: public-sector
order: 70
summary: Schritt für Schritt für Redaktionen - eine Datensatzseite anlegen, ihre Dateien als Distributionen hochladen, prüfen, ob der Download für einen anonymen Besucher funktioniert, und eine Datei durch eine neue Fassung ersetzen, ohne die Adresse zu brechen.
keywords: Upload, hochladen, Datensatzdatei, CSV, Download, content/download, Distribution, Klasse Datei, Datei ersetzen, ezbinaryfile, offene Daten, veröffentlichen, upload, dataset file, download, distribution, file class, replace file, open data, publish
type: help
---
# Datensatzdateien veröffentlichen

Diese Seite richtet sich an Redaktionen, die einen Datensatz und seine Dateien veröffentlichen. Sie setzt voraus, dass
eine Datensatzklasse und ein Katalogordner existieren (siehe „Eine Datensatzklasse entwerfen“). Downloads laufen in
Exponential über die Ansicht `content/download`, die prüft, ob der Besucher das Objekt lesen darf, bevor sie die Datei
sendet ([Dateien und Downloads](../../../../eng-us/help/creators/website-creators/files-and-downloads.md)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Die Datensatzseite anlegen](#die-datensatzseite-anlegen)
3. [Die Dateien hinzufügen](#die-dateien-hinzufügen)
4. [Den Download als Besucher prüfen](#den-download-als-besucher-prüfen)
5. [Eine Datei durch eine neue Fassung ersetzen](#eine-datei-durch-eine-neue-fassung-ersetzen)
6. [Formate und Dateinamen](#formate-und-dateinamen)
7. [Probleme](#probleme)

## Bevor Sie beginnen

- Sie können im Katalogordner Objekte der Klassen `dataset` und `distribution` (oder `file`) anlegen. Wenn **Neu
  erstellen** sie nicht anbietet, bitten Sie einen Administrator um die Richtlinie
  ([Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)).
- Die Datei hat die Datenschutzprüfung Ihrer Stelle bestanden (siehe „Datenschutzprüfung vor dem Veröffentlichen von
  Daten“).
- Die Lizenz des Datensatzes ist entschieden (siehe „Datensätze lizenzieren“).

## Die Datensatzseite anlegen

1. Öffnen Sie in der **Inhaltsstruktur** den Katalogordner.
2. Klicken Sie unter **Unterelemente** auf **Neu erstellen** und wählen Sie **Dataset** (oder verwenden Sie **Hier
   erstellen** im Menü des Eintrags im Baum).
3. Füllen Sie die Felder aus: Titel, Beschreibung, Herausgeber, Kontaktpostfach, Lizenz, Daten,
   Aktualisierungshäufigkeit.
4. Klicken Sie auf **Zur Veröffentlichung schicken**. Mit einem Freigabe-Workflow wartet die Version als ausstehend,
   bis eine prüfende Person sie freigibt ([Entwürfe und ausstehende Einträge](../../../anleitungen/entwuerfe-und-ausstehende-objekte.md)).

## Die Dateien hinzufügen

1. Öffnen Sie die neue Datensatzseite; klicken Sie unter **Unterelemente** auf **Neu erstellen** und wählen Sie
   **Distribution** (oder **Datei**).
2. Vergeben Sie einen Titel, der Format und Umfang nennt, zum Beispiel „CSV, alle Jahre“.
3. Wählen Sie im Dateifeld die Datei und veröffentlichen Sie.

Mehrere Dateien auf einmal: **Medienbibliothek > Dateien hochladen** (`ezmultiupload`) lädt viele Dateien in einen
Ordner hoch; verschieben Sie sie danach unter den Datensatz, oder laden Sie sie direkt in den Datensatz hoch, wenn die
Rolle es erlaubt ([Medien und Uploads](../../medien-und-uploads.md)).

Die Grenzen für Uploads kommen aus PHP (`upload_max_filesize`, `post_max_size`) und aus der höchsten Größe, die am
Dateifeld der Klasse eingestellt ist. Eine Datei, die größer ist als diese Grenzen, wird beim Hochladen abgewiesen.

## Den Download als Besucher prüfen

1. Melden Sie sich ab oder öffnen Sie die öffentliche Website in einem privaten Browserfenster.
2. Öffnen Sie die Datensatzseite und klicken Sie auf jede Datei.
3. Prüfen Sie, ob der Browser die Datei mit dem richtigen Namen erhält und ob die Seite Format und Größe zeigt.

Antwortet die Datei für Besucher mit „Zugriff verweigert“, liegt der Datensatz in einer Sektion, die die Rolle Anonymous
nicht lesen darf ([Sektionen](../../../anleitungen/sektionen.md)). Dieser Schutz ist für Entwürfe und interne Daten richtig
und für offene Daten falsch; verschieben Sie den Katalog in eine öffentliche Sektion.

Verlinken Sie nie direkt den gespeicherten Dateipfad unterhalb von `var/.../storage/`. Er umgeht die Rechteprüfung und
ändert sich, wenn sich die Datei ändert
([Dateien und Downloads](../../../../eng-us/help/creators/website-creators/files-and-downloads.md#protected-downloads)).

## Eine Datei durch eine neue Fassung ersetzen

So veröffentlichen Sie neue Daten für dieselbe Distribution:

1. Öffnen Sie die Distribution und klicken Sie auf **Bearbeiten**.
2. Entfernen Sie im Dateifeld die alte Datei und wählen Sie die neue.
3. Aktualisieren Sie auf der Datensatzseite das Datum „Zuletzt geändert“.
4. Veröffentlichen Sie.

Die Distribution behält ihre Objekt-ID, sodass ihre Seitenadresse gleich bleibt. Die Download-Adresse enthält die IDs
von Objekt und Attribut sowie den Dateinamen; wenn die neue Datei einen anderen Namen hat, ändert sich der letzte Teil
der Download-Adresse. Verlinken Sie auf die Seite der Distribution oder behalten Sie den Dateinamen bei, wenn
Nachnutzer Downloads als Lesezeichen speichern (siehe „Stabile URLs für Datensätze“).

Die vorherige Version des Objekts mit der alten Datei bleibt in der Versionsgeschichte, bis die Versionsgrenze sie
entfernt ([Inhaltsgeschichte](../../../anleitungen/inhaltsverlauf.md)).

## Formate und Dateinamen

| Wählen | Vermeiden |
|---|---|
| CSV in UTF-8 mit einer Kopfzeile | Tabellen mit verbundenen Zellen und Farben als Bedeutungsträger |
| JSON oder XML für verschachtelte Daten | Gescannte PDF-Tabellen |
| Dateinamen in Kleinbuchstaben ohne Leerzeichen: `street-lights-2025.csv` | Namen mit Datumsangaben in mehreren Formaten |
| Eine Datei pro logischer Tabelle | Viele Tabellen auf einem Blatt |

## Probleme

| Symptom | Ursache | Lösung |
|---|---|---|
| Der Upload bricht ohne Meldung ab | Upload-Grenze von PHP | Bitten Sie die Administration, `upload_max_filesize` und `post_max_size` anzuheben |
| Der Besucher erhält „Zugriff verweigert“ | Sektion für Anonymous nicht lesbar | Verschieben Sie den Katalog in eine öffentliche Sektion |
| Die alte Datei wird noch heruntergeladen | Ein Cache vor der Website | Warten Sie die Cache-Zeit ab oder bitten Sie die Administration, ihn zu leeren ([Caches](../../../anleitungen/caches.md)) |

## Verwandte Seiten

- [Dateien und Downloads](../../../../eng-us/help/creators/website-creators/files-and-downloads.md)
- [Die Medienbibliothek ordnen](../../../../eng-us/help/creators/website-creators/organizing-the-media-library.md)
- [Inhalte bearbeiten](../../inhalte-bearbeiten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
