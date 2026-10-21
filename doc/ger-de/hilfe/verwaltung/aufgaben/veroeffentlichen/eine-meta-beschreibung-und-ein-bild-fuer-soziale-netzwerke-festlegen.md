---
title: Eine Meta-Beschreibung und ein Bild für soziale Netzwerke festlegen
audience: government, editor
category: public-sector
order: 550
summary: Für Redakteure - das Feld Meta Data einer Seite ausfüllen (Titel, Beschreibung, Open-Graph-Bild), damit Suchergebnisse und Linkvorschauen den richtigen Text und das richtige Bild zeigen, und prüfen, wie ein geteilter Link aussehen wird.
keywords: Meta-Beschreibung, Meta Data, xrowmetadata, Suchergebnisse, Linkvorschau, Open Graph, Bild für soziale Netzwerke, Seitentitel, Canonical, Suchmaschinenoptimierung, SEO, meta description, search results, link preview, social image, page title, canonical
type: help
---
# Eine Meta-Beschreibung und ein Bild für soziale Netzwerke festlegen

Diese Seite ist für Redakteure, die möchten, dass eine Seite in Suchergebnissen und beim Teilen richtig aussieht. Viele
Klassen haben ein Feld **Meta Data** (der Datentyp `xrowmetadata`) mit einem Titel, Schlüsselwörtern, einer
Beschreibung, einem Canonical-Link und, seit 1.3.6, einem Open-Graph-Bild
([xrowmetadata](../../../../../eng-us/features/6.0/extensions/xrowmetadata.md),
[Metadaten und Teilen in sozialen Netzwerken](../../aufbauen/nachrichten-und-warnmeldungen/metadaten-und-teilen-in-sozialen-netzwerken.md)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Das Ergebnis prüfen](#das-ergebnis-prüfen)
4. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Die Klasse der Seite hat ein Feld **Meta Data** (Event, Folder, Frontpage, Feedback form und die Klassen `ng_*`
  haben es; die Grundklasse Article einer neuen Installation nicht, sofern Ihr Web-Team es nicht hinzugefügt hat).
- Der eigene Titel und die Zusammenfassung der Seite sind endgültig.

## Schritte

1. **Bearbeiten** Sie die Seite; suchen Sie das Feld **Meta Data** (oft auf einem späteren Reiter des Formulars).
2. **Title**: der Titel für Suchergebnisse, unter etwa 60 Zeichen, die wichtigen Wörter zuerst.
3. **Description**: ein oder zwei vollständige Sätze (unter etwa 160 Zeichen), die sagen, was die Seite bietet.
4. **Image** (Open Graph): ein Bild, das die Seite auch klein gut darstellt; mit Alternativtext in seinem Objekt;
   ohne versehentlich sichtbare personenbezogene Daten.
5. Lassen Sie **Canonical** leer, sofern die Seite kein Duplikat einer anderen Adresse ist.
6. **Zur Veröffentlichung schicken**.

## Das Ergebnis prüfen

- [ ] Sehen Sie sich den Quelltext der Seite an (abgemeldet): `<title>` und `<meta name="description">` zeigen Ihren
  Text, wenn Ihr Design sie ausgibt.
- [ ] Ein Werkzeug für Linkvorschauen oder ein Messenger zeigt Titel, Beschreibung und Bild (sie können alte Vorschauen
  eine Weile im Cache halten).

## Fallstricke

| Fallstrick | So vermeiden Sie ihn |
|---|---|
| Den Titel in die Beschreibung kopieren | die Beschreibung fügt Informationen hinzu |
| Schlüsselwörter anhäufen | Suchmaschinen ignorieren das Feld für Schlüsselwörter weitgehend |
| Eine andere Botschaft als die Seite | die Beschreibung muss zur Seite passen |
| Ein Bild mit zu kleinem Text | Linkvorschauen sind klein |

## Verwandte Seiten

- [Aufgaben zum Veröffentlichen](README.md)
- [Grundlagen der Suchmaschinenoptimierung](../../../../../eng-us/help/creators/website-creators/seo-basics.md)
- [Eine Zusammenfassung in einfacher Sprache schreiben](eine-zusammenfassung-in-einfacher-sprache-schreiben.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
