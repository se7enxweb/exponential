---
title: Offene Daten in Exponential auf einen Blick
audience: government, project-manager, admin, editor
category: public-sector
order: 30
summary: Eine Tabelle aller Funktionen von Exponential, die einer öffentlichen Stelle beim Veröffentlichen offener Daten helfen, mit ihrem Ort im Produkt und dem, was sie nicht leisten, damit eine Projektleitung die Arbeit in Minuten abschätzen kann.
keywords: offene Daten, Funktionen, Überblick, Datensätze, Dateien, RSS, Atom, REST-API, JSON, CSV-Export, xrowextract, ezcsvexport, eztags, Versionen, Audit, open data, features, overview, datasets, files, REST API, CSV export, versions
type: help
---
# Offene Daten in Exponential auf einen Blick

Diese Seite richtet sich an Projektleitungen und Administratoren, die eine schnelle Bestandsaufnahme brauchen, was
Exponential für das Veröffentlichen offener Daten bietet. Jede Zeile nennt die Funktion, wo sie sich im Produkt
befindet, und die Dokumentation, die sie belegt. Der letzte Abschnitt führt auf, was fehlt, damit Sie es einplanen
können.

## Inhalt

1. [Veröffentlichen](#veröffentlichen)
2. [Beschreiben](#beschreiben)
3. [Schnittstellen für Maschinen](#schnittstellen-für-maschinen)
4. [Vertrauenswürdig bleiben](#vertrauenswürdig-bleiben)
5. [Nicht enthalten](#nicht-enthalten)
6. [Wie es weitergeht](#wie-es-weitergeht)

## Veröffentlichen

| Bedarf | Funktion | Wo | Dokumentation |
|---|---|---|---|
| Eine Datei zum Herunterladen anbieten | Dateiobjekt mit einem Feld `ezbinaryfile`; Download über `content/download` | Inhaltsstruktur, jeder Ordner | [Dateien und Downloads](../../../../eng-us/help/creators/website-creators/files-and-downloads.md) |
| Viele Dateien auf einmal hochladen | Dateien hochladen (`ezmultiupload`) | Medienbibliothek > Dateien hochladen | [Medien und Uploads](../../medien-und-uploads.md) |
| Lesbare, dauerhafte Adressen | URL-Aliasse aus dem Namen; alte Aliasse leiten nach Umbenennen oder Verschieben weiter | automatisch; Reiter URL-Aliasse eines Knotens | [Weiterleitungen und verschobene Inhalte](../../../../eng-us/help/creators/website-creators/redirects-and-moved-content.md) |
| Ein Dokument aus vielen Seiten | PDF-Export eines Teilbaums | Setup > PDF-Export | [PDF-Exporte](../../../anleitungen/pdf-exporte.md) |
| Eine Liste von Datensätzen | Ein Ordner, dessen Unterobjekte Datensatzseiten sind, als Liste angezeigt | Ihr Design | [Den Inhaltsbaum strukturieren](../../../../eng-us/help/creators/website-creators/structuring-the-content-tree.md) |

## Beschreiben

| Bedarf | Funktion | Dokumentation |
|---|---|---|
| Felder für Titel, Beschreibung, Herausgeber, Lizenz, Daten | Eine eigene Inhaltsklasse aus Datentypen wie Textzeile, Textabschnitt, Datum, Auswahl, URL, Datei | [Datentypen wählen](../../../../eng-us/help/creators/website-creators/choosing-datatypes.md) |
| Themen und Schlagworte | Tags mit der Erweiterung `eztags` | [Verschlagworten mit eztags](../../../../eng-us/help/creators/website-creators/tagging-with-eztags.md) |
| Seitentitel, Beschreibung, kanonischer Link | Metadatenfeld von `xrowmetadata` | [xrowmetadata](../../../../eng-us/features/6.0/extensions/xrowmetadata.md) |
| Sprachen der Beschreibung | Inhaltssprachen und Übersetzungen | [Inhaltssprachen](../../../anleitungen/inhaltssprachen.md) |

## Schnittstellen für Maschinen

| Bedarf | Funktion | Format | Dokumentation |
|---|---|---|---|
| „Was ist neu“ | RSS-Export eines Ordners | RSS 1.0, RSS 2.0, Atom | [RSS-Feeds](../../../anleitungen/rss-feeds.md) |
| Inhalte per Programm lesen | REST-API v1 und v2 (`ezprestapi`) | JSON | [REST-API v1 und v2](../../../anleitungen/rest-api-v1-und-v2.md) |
| Massenexport von Inhalten | Reiter Export von `xrowextract` | CSV, JSON, XML | [xrowextract](../../../../eng-us/features/6.0/extensions/xrowextract.md) |
| Massenexport von der Kommandozeile | `exp:ezcsvexport` | CSV, eine Datei pro Klasse | [exp:ezcsvexport](../../../../eng-us/examples/commands/ezcsvexport.md) |
| Sitemaps für Suchmaschinen | Cronjobs von `xrowmetadata` und `bcgooglesitemaps` | XML-Sitemap | [Sitemaps](../../../../eng-us/help/creators/website-creators/sitemaps.md) |

## Vertrauenswürdig bleiben

| Bedarf | Funktion | Dokumentation |
|---|---|---|
| Sehen, was sich wann geändert hat | Versionen jedes Objekts, mit Vergleich | [Inhaltsgeschichte](../../../anleitungen/inhaltsverlauf.md) |
| Sehen, wer es geändert hat | Der Audit-Trail, standardmäßig eingeschaltet | [Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md) |
| Prüfung vor dem Veröffentlichen | Freigabe-Workflow | [Workflows](../../../anleitungen/workflows.md) |
| Nur das Datenteam darf Datensätze veröffentlichen | Rollen, beschränkt nach Sektion oder Teilbaum | [Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md) |

## Nicht enthalten

Im Repository geprüft; planen Sie diese Punkte selbst ein:

- Keine Ausgabe von DCAT, DCAT-AP oder schema.org Dataset und keine CKAN-API.
- Ab Werk keine Datensatzklasse, kein Lizenzfeld und kein Datenwörterbuch.
- Keine Prüfung der Dateien, die Sie hochladen (wohlgeformtes CSV, Übereinstimmung mit einem Schema).
- Kein Vorschau-Widget für Daten (Tabelle oder Diagramm einer CSV-Datei) in den mitgelieferten Designs.
- Keine API-Schlüssel für die anonyme öffentliche Nutzung: Die REST-API verlangt standardmäßig einen angemeldeten
  Benutzer (`rest.ini [Authentication] RequireAuthentication=enabled`); sie für alle zu öffnen, ist eine bewusste
  Änderung der Einstellungen mit einer eigenen Prüfung.

## Wie es weitergeht

Lesen Sie zuerst „Was offene Daten für eine öffentliche Stelle bedeuten“, wenn die Begriffe neu sind, dann „Eine
Datensatzklasse entwerfen“, „Datensätze lizenzieren“ und „Stabile URLs für Datensätze“ in diesem Bereich.

## Verwandte Seiten

- [Was offene Daten für eine öffentliche Stelle bedeuten](was-offene-daten-bedeuten.md)
- [REST-API v1 und v2](../../../anleitungen/rest-api-v1-und-v2.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
