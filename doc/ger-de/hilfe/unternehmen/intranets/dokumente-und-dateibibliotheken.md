---
title: Dokumente und Dateibibliotheken
audience: businesses, admin, editor
category: business-solutions
order: 30
summary: Wie ein Intranet Richtlinien, Handbücher, Formulare und andere Dokumente in Exponential aufbewahrt - Dateien als Inhaltsobjekte, Downloads mit Berechtigungsprüfung, Versionen, Massen-Upload, Volltextsuche in Dateien, PDF-Exporte und die Option WebDAV.
keywords: Dokumentenbibliothek, Dateibibliothek, Dateien, Downloads, content/download, ezbinaryfile, Richtlinien, Handbücher, Formulare, Versionen, ezmultiupload, ezodf, WebDAV, PDF-Export, binaryfile.ini, Dokumente im Intranet, document library, file library, files, downloads, policies, handbooks, forms, versions, PDF export, intranet documents
type: help
---
# Dokumente und Dateibibliotheken

Diese Seite ist für Intranetverantwortliche, die einen vertrauenswürdigen Ort für Dokumente möchten: die aktuelle
Personalrichtlinie, das Reisekostenformular, das Markenhandbuch. Sie erklärt, wie Exponential Dateien speichert, warum
ein Download aus einem geschützten Bereich sicher ist, was Redakteuren hilft, Dokumente aktuell zu halten, und was Sie
prüfen sollten, bevor Sie ein gemeinsames Laufwerk ins Intranet verlagern.

![Die Medien-Bibliothek mit ihren Ordnern](../../../../eng-us/help/images/help/bu-files.webp)

## Inhalt

1. [Dateien sind Inhalte](#dateien-sind-inhalte)
2. [Downloads beachten Berechtigungen](#downloads-beachten-berechtigungen)
3. [Eine Bibliothek strukturieren](#eine-bibliothek-strukturieren)
4. [Dokumente aktuell halten](#dokumente-aktuell-halten)
5. [Viele Dateien hineinbekommen](#viele-dateien-hineinbekommen)
6. [Wörter in Dateien finden](#wörter-in-dateien-finden)
7. [Handbücher als PDF und Office-Dokumente](#handbücher-als-pdf-und-office-dokumente)
8. [WebDAV](#webdav)
9. [Checkliste](#checkliste)
10. [Verwandte Seiten](#verwandte-seiten)

## Dateien sind Inhalte

In Exponential ist ein Dokument ein Inhaltsobjekt der Klasse **File**, deren Feld `file` den Datentyp `ezbinaryfile`
verwendet. Uploads von Dateien, die keine Bilder sind, werden zu File-Objekten, weil `settings/upload.ini`
`DefaultClass=file` setzt
([Dateien und Downloads](../../../../eng-us/help/creators/website-creators/files-and-downloads.md#how-a-download-works)).

Für das Unternehmen bedeutet das: Ein Dokument hat alles, was andere Inhalte haben: einen Titel, einen Platz im Baum,
eine Sektion, einen Eigentümer, Versionen, Übersetzungen und eine URL. Es kann mit den Seiten verknüpft werden, die es
erwähnen ([Relationen und Wiederverwendung](../../../../eng-us/help/creators/website-creators/relations-and-reuse.md)).

## Downloads beachten Berechtigungen

Jeder Download läuft über die Ansicht `content/download/<object ID>/<attribute ID>`. Die Ansicht prüft, ob die Person
das Objekt lesen darf, bevor sie die Datei schickt (`kernel/content/module.php`, Ansicht `download`, Funktion `read`).
Eine Datei in einer Sektion nur für die Personalabteilung kann daher nur von Personen heruntergeladen werden, die diese
Sektion lesen dürfen, selbst wenn jemand den Link per Mail herumschickt.

> **Warnung**
> Verlinken Sie Dokumente nicht direkt aus dem Speicherverzeichnis (`var/.../storage/`). Das umgeht die
> Berechtigungsprüfung
> ([Dateien und Downloads, Protected downloads](../../../../eng-us/help/creators/website-creators/files-and-downloads.md#protected-downloads)).

## Eine Bibliothek strukturieren

| Bedarf | Wie in Exponential |
|---|---|
| Dokumente für alle Mitarbeiter | ein Ordner in der Mitarbeitersektion |
| Dokumente für eine Abteilung | ein Ordner, der der Sektion dieser Abteilung zugewiesen ist ([Abteilungen, Rollen und Sektionen](abteilungen-rollen-und-sektionen.md)) |
| Ein Dokument, das auf mehrere Seiten gehört | ein File-Objekt, mit den Seiten verknüpft oder mit einem zweiten Ort |
| Ausgemusterte Dokumente, zum Nachschlagen aufbewahrt | in eine Archivsektion verschieben, die nur die Verantwortlichen lesen |
| Themen über Abteilungen hinweg | Tags mit `eztags` ([Verschlagworten mit eztags](../../../../eng-us/help/creators/website-creators/tagging-with-eztags.md)) |

Benennen Sie Ordner danach, was Mitarbeiter suchen („Reisen und Spesen“), nicht nach der Abteilung, die sie
verantwortet; die Zuständigkeit drückt die Sektion aus.

## Dokumente aktuell halten

- **Versionen.** Jedes Speichern eines Objekts ist eine Version; die vorherige veröffentlichte Version wird archiviert
  und kann auf der Versionsseite (`/content/history/<object id>`) verglichen und wiederhergestellt werden
  ([Versionen eines Objekts](../../../anleitungen/inhaltsverlauf.md)). Eine Datei in einem vorhandenen File-Objekt zu
  ersetzen, behält Adresse und Verlauf; eine zweite Kopie hochzuladen, stiftet Verwirrung.
- **Eigentümer.** Der Eigentümer jedes Objekts wird aufgezeichnet und kann geändert werden
  ([Eigentümer ändern](../../../anleitungen/eigentuemer-aendern.md)).
- **Freigabe.** Richtlinien und Handbücher können eine Freigabe brauchen, bevor eine neue Version live geht
  ([Freigabe-Workflows](freigabe-workflows.md)).
- **Folgende.** Mitarbeiter, die dem Bibliotheksordner folgen, bekommen eine Mail, wenn darunter ein neues Dokument
  veröffentlicht wird ([Benachrichtigungen für Mitarbeiter](benachrichtigungen-fuer-mitarbeiter.md)).
- **Upload-Grenzen.** `upload_max_filesize` und `post_max_size` von PHP begrenzen, was Redakteure hochladen können;
  vereinbaren Sie die größte Dateigröße mit der Person, die den Server betreibt
  ([Dateien und Downloads, Large files](../../../../eng-us/help/creators/website-creators/files-and-downloads.md#large-files)).

## Viele Dateien hineinbekommen

| Werkzeug | Was es tut | Quelle |
|---|---|---|
| **Mehrere Dateien hochladen** (`ezmultiupload`) | mehrere Dateien wählen oder ablegen; jede wird unter dem aktuellen Ordner zu einem Objekt der richtigen Klasse | [ezmultiupload](../../../../eng-us/features/6.0/extensions/ezmultiupload.md) |
| Upload im Editor | eine Datei nach der anderen über den Editor oder die Medien-Bibliothek | [Medien und Uploads](../../medien-und-uploads.md) |
| Skripte und Importe | Entwickler können für eine Migration Inhalte im Code anlegen | [Inhalte im Code anlegen](../../../../eng-us/help/creators/app-creators/creating-content-in-code.md) |

Hochgeladene Bilder werden seit dem 12. Oktober 2026 anhand ihres Inhalts geprüft, nicht nur anhand ihrer Endung
([Verhaltensänderungen 2026-10](../../../../eng-us/bc/6.0/behaviour-changes-2026-10.md)).

## Wörter in Dateien finden

Die eingebaute Suche indiziert die durchsuchbaren Felder jeder Klasse. Für Text in Dateien nennt
`settings/binaryfile.ini` Extraktoren pro Typ: `ezpdf` für PDF (mit dem externen Programm `pstotext`), `ezword` für
Word-Dokumente (mit `wvWare`) und `ezplaintext` für reinen Text. Die externen Programme müssen auf dem Server
installiert sein. Mit Solr über `ezfind` können auch Dateiinhalte indiziert werden
([Suche im Intranet](suche-im-intranet.md)).

## Handbücher als PDF und Office-Dokumente

- **PDF-Exporte.** Setup > PDF-Export (`/pdf/list`) erzeugt aus einem Teil des Inhaltsbaums ein PDF-Dokument, zum
  Beispiel ein Handbuch, das als Seiten gepflegt wird ([PDF-Exporte](../../../anleitungen/pdf-exporte.md)).
- **OpenDocument.** Die Erweiterung `ezodf` importiert eine Datei `.odt` aus OpenOffice oder LibreOffice Writer als
  Inhaltsobjekt und exportiert Objekte zurück in Writer-Dokumente; sie braucht OpenOffice oder LibreOffice 2.4 oder
  neuer auf dem Server ([ezodf](../../../../eng-us/features/6.0/extensions/ezodf.md)).

## WebDAV

Der Kernel enthält einen WebDAV-Einstiegspunkt (`webdav.php`) mit seinen Einstellungen in `settings/webdav.ini`. Er ist
standardmäßig ausgeschaltet (`EnableWebDAV=false`) und beginnt bei Knoten 2. Uploads über WebDAV durchlaufen dieselben
Bildprüfungen wie andere Uploads (Changelog 6.0.15). Diese Hilfe hat keine Anleitung Schritt für Schritt dafür; wenn
Mitarbeiter die Bibliothek als Netzlaufwerk einbinden möchten, bitten Sie Ihren Partner, es auf Ihrer Installation zu
testen, bevor Sie es zusagen.

## Checkliste

- [ ] Jeder Dokumentenbereich gehört zu einer Sektion, die nur die richtigen Gruppen lesen dürfen.
- [ ] Jedes Dokument hat einen benannten Eigentümer.
- [ ] Download-Links zeigen Format und Größe.
- [ ] Geschützte Downloads getestet, angemeldet als jemand ohne Zugriff.
- [ ] Alte Kopien nach der Migration entfernt oder in eine Archivsektion verschoben.
- [ ] Upload-Grenzen passen zur größten Datei.
- [ ] Entschieden, ob Text in Dateien durchsuchbar sein muss, und die Werkzeuge installiert.

## Verwandte Seiten

- [Dateien und Downloads](../../../../eng-us/help/creators/website-creators/files-and-downloads.md)
- [Die Medien-Bibliothek ordnen](../../../../eng-us/help/creators/website-creators/organizing-the-media-library.md)
- [Dateien mit Partnern teilen](../extranets/dateien-mit-partnern-teilen.md)
- [Suche im Intranet](suche-im-intranet.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
