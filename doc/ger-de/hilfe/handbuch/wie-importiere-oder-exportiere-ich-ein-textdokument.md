---
title: Wie importiere oder exportiere ich ein Textverarbeitungsdokument?
audience: editor
category: manual
order: 497
summary: Der Eintrag OpenOffice.org im Baummenü eines Elements importiert einen OpenDocument-Text (.odt) als neues veröffentlichtes Element darunter, ersetzt den Inhalt des Elements durch ein Dokument als neue Version oder exportiert das Element als .odt (und, mit einem Konverter auf dem Server, als Word oder PDF).
keywords: ezodf, OpenDocument, odt, LibreOffice, OpenOffice, Word, Textverarbeitung, Dokument importieren, ersetzen, exportieren, PDF exportieren, Word exportieren, Bereiche, import, replace, export, export PDF, export Word, sections, document import
context: ezodf/import
type: help
---
# Wie importiere oder exportiere ich ein Textverarbeitungsdokument?

Mit der Erweiterung *ezodf* kann ein in LibreOffice oder OpenOffice geschriebener Text zu Inhalt werden, und Inhalte
lassen sich wieder als Dokument ausgeben. Überschriften, Absätze, Listen, Tabellen und Bilder werden übernommen. Ein
Import wird sofort veröffentlicht; prüfen Sie das Dokument also, bevor Sie es importieren.

## Wer das tun kann

Redakteure, deren Rolle die Richtlinien `ezodf/import` und `ezodf/export` hat, dazu das Recht, unter dem gewählten
Element Inhalte anzulegen (für einen Import) oder das Element zu bearbeiten (für ein Ersetzen).

## Schritte

### Ein Dokument als neues Element importieren

1. Speichern Sie das Dokument im OpenDocument-Format (`.odt`).
2. Öffnen Sie das Baummenü des Ordners, in den das neue Element gehört, und wählen Sie **OpenOffice.org** >
   **Import OpenOffice**.
3. Wählen Sie die Datei und, falls gefragt, die Sprache unter **Create or update the translation in:**.
4. Klicken Sie auf **Import document**.
   Die Seite meldet, dass das Dokument importiert wurde und als welche Klasse. Öffnen Sie das neue Element und prüfen
   Sie es.

### Den Inhalt eines Elements durch ein Dokument ersetzen

1. Öffnen Sie das Baummenü des Elements und wählen Sie **OpenOffice.org** > **Replace OpenOffice**.
2. Wählen Sie die Datei und die Sprache und klicken Sie auf **Import document**.
   Das Dokument wird zu einer neuen veröffentlichten Version des Elements; der alte Inhalt bleibt in seinem
   Versionsverlauf.

### Ein Element exportieren

1. Öffnen Sie das Baummenü des Elements und wählen Sie **OpenOffice.org** > **Export OpenOffice**.
   Ihr Browser lädt eine Datei `.odt` herunter.
2. **Export Word** und **Export PDF** funktionieren genauso, auf Websites, die einen Dokumentkonverter betreiben.

Auf Websites mit der Website-Werkzeugleiste sind dieselben Funktionen deren Schaltflächen **Importieren**,
**Replace** und **Exportieren**.

## Welche Klasse ein Import wird

Ein Dokument wird standardmäßig zu einem **Artikel**: Der Dateiname ohne Endung wird zum Titel und der gesamte Text zum
Haupttext. Um andere Felder zu füllen oder eine andere Klasse anzulegen, teilen Sie das Dokument in der
Textverarbeitung in benannte **Bereiche** (**Einfügen** > **Bereich**) und benennen Sie jeden Bereich nach einem Feld,
zum Beispiel `title`, `intro` und `body`. Wenn die Bereichsnamen zu allen Feldern passen, die Ihre Website für eine
Klasse aufführt, wird das Dokument zu dieser Klasse. Fragen Sie einen Administrator, welche Klassen und Namen Ihre
Website annimmt.

## Was schiefgehen kann

- **Fehler „Filetype“.** Nur `.odt` wird angenommen, es sei denn, die Website betreibt einen Konverter für
  Word-Dateien (`.doc`). Speichern Sie das Dokument als `.odt`.
- **Achtung: Ein Import veröffentlicht sofort.** Er hält nicht bei einem Entwurf an (außer ein Freigabe-Workflow hält
  ihn auf), und ein Ersetzen prüft nicht, ob jemand einen offenen Entwurf des Elements hat. Bereiten Sie das Dokument
  zuerst vor und sagen Sie Kollegen Bescheid, die das Element bearbeiten.
- **Einige Felder bleiben leer.** Importe füllen nur Text-, Rich-Text-, Datums-, Datums- und Zeit-, Bild- und
  Matrixfelder. Füllen Sie Schlüsselwörter, Kontrollkästchen und Relationen von Hand aus.
- **Die Formatierung geht verloren.** Schriften, Farben und Seitenlayout werden nicht übernommen; das Design der
  Website bestimmt das Aussehen.
- **Bilder brauchen Alternativtexte.** Fügen Sie sie nach dem Import hinzu.

## Verwandte Seiten

- [OpenDocument-Import und -Export](../verwaltung/aufbauen/dokumente-und-publikationen/opendocument-import-und-export.md), die
  Einstellungen
- [Wie bearbeite ich Inhalte direkt auf der Website?](wie-bearbeite-ich-inhalte-direkt-auf-der-website.md)
- [Benutzerhandbuch](README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
