---
title: "Bilder zum Teilen und für Metadaten"
audience: theme-designer
category: theme-design
order: 28
summary: Was Theme-Designer über Bild und Text wissen müssen, die eine Seite zeigt, wenn sie geteilt wird - Open-Graph- und Twitter-Tags im Kopf der Seite, wie die Erweiterung xrowmetadata das Bild wählt und skaliert, ein Standardbild pro Klasse, sichere Bereiche und wie Sie eine Vorschau beim Teilen testen.
keywords: Theme-Designer, Teilen in sozialen Medien, Open Graph, og:image, Twitter Card, Bild zum Teilen, Metadaten, xrowmetadata, Standardbild, sicherer Bereich, Vorschaukarte, theme designer, social sharing, open graph, twitter card, share image, metadata, default image, safe area, preview card
type: help
---
# Bilder zum Teilen und für Metadaten

Diese Seite ist für Theme-Designer, die möchten, dass geteilte Links so aussehen, wie sie gestaltet wurden. Wenn jemand
eine Seite teilt, liest das soziale Netzwerk oder der Messenger Tags im Kopf der Seite (Open Graph `og:*` und
`twitter:*`) und baut aus Titel, Beschreibung und Bild eine Vorschaukarte. Das Theme gibt diese Tags aus; die Inhalte
liefern die Werte. Wie man sie ausgibt, behandelt für Website-Ersteller
[Metadaten und Teilen in sozialen Medien](../ersteller/website-ersteller/metadaten-und-teilen-in-sozialen-medien.md); diese Seite
handelt davon, was ein Designer entscheidet.

## Inhalt

1. [Woraus die Vorschaukarte besteht](#woraus-die-vorschaukarte-besteht)
2. [Woher die Werte kommen](#woher-die-werte-kommen)
3. [Das Bild zum Teilen](#das-bild-zum-teilen)
4. [Ein Standardbild](#ein-standardbild)
5. [Längen von Titel und Beschreibung](#längen-von-titel-und-beschreibung)
6. [Eine Vorschau testen](#eine-vorschau-testen)

## Woraus die Vorschaukarte besteht

| Tag | Inhalt | Frage des Designs |
|---|---|---|
| `og:title` | der Titel der Seite | Länge; Markenzusatz oder nicht |
| `og:description` | eine kurze Zusammenfassung | Länge, Ton |
| `og:image` | eine absolute Bildadresse, mit Breite, Höhe und Alternativtext | das Bild selbst |
| `og:site_name` | der Name der Website | einheitlich mit dem Kopfbereich |
| `twitter:*` | dieselben Werte für dieses Netzwerk | |

Das Aussehen der Karte (ihr Layout, ihre Schriften, ihre Farben) gehört dem Netzwerk, nicht Ihnen. Sie bestimmen das
Bild und die Worte.

## Woher die Werte kommen

Die mitgelieferten Themes unterscheiden sich:

- das Theme **simple** gibt in seinem `page_head.tpl` `og:title`, `og:description` und `og:type` aus, aus den Metadaten
  der Seite, wenn es sie gibt, und sonst aus Titel und Beschreibung der Website;
- das Theme **media** bindet aus seinem Template für den Seitenkopf das Template `xrowmetadata/opengraph_image.tpl` der
  Erweiterung xrowmetadata ein, das `og:image` (mit Breite, Höhe, Typ und Alternativtext) und `twitter:image` ausgibt.

Mit der Erweiterung **xrowmetadata** können Redakteure bei Objekten der Klassen, die ein Metadatenfeld haben, dieses
ausfüllen: Titel, Beschreibung, Schlüsselwörter und ein Open-Graph-Bild aus der Medienbibliothek. Ihre Einstellungen
(`xrowmetadata.ini`, Gruppe `[OpenGraph]`) nennen die Bildklassen, die Redakteure wählen dürfen, die Attribute, die nach
einem Bild durchsucht werden (standardmäßig `site_opengraph_image`, `image`, `site_logo`, `file`), und den Bild-Alias
für das Tag, standardmäßig `opengraph`. Die Erweiterung definiert diesen Alias als "auf 1200 mal 1200 verkleinern". Hat
das Objekt keine eigene Wahl, wird ein Standard für die Klasse verwendet, und gibt es beides nicht, wird kein Bild-Tag
ausgegeben.

Prüfen Sie, was davon Ihre Installation verwendet, bevor Sie gestalten; eine Website ohne xrowmetadata braucht eigene
Felder und Templates ([Metadaten und Teilen in sozialen Medien](../ersteller/website-ersteller/metadaten-und-teilen-in-sozialen-medien.md#mit-eigenen-feldern)).

## Das Bild zum Teilen

Weil der mitgelieferte Alias verkleinert, ohne zuzuschneiden, behält das Bild zum Teilen die Proportionen des Bildes,
das Redakteure gewählt haben. Die Netzwerke schneiden es dann in ihre eigenen Kartenformen zu, die sich zwischen
Netzwerken unterscheiden und mit der Zeit ändern. Praktische Regeln:

- gestalten Sie Bilder zum Teilen im Querformat; ein Hochformatfoto wird hart beschnitten;
- halten Sie das Wichtige (Gesichter, das Logo, jeden Text) in einem **sicheren Bereich** in der Mitte, weg von allen
  Rändern;
- vermeiden Sie kleinen Text in Bildern zum Teilen; er ist in einer Messenger-Vorschau unlesbar und lässt sich nicht
  übersetzen;
- sorgen Sie dafür, dass das Bild auf hellem und dunklem Hintergrund funktioniert, denn Karten erscheinen in beiden.

Möchte der Kunde ein festes Format zum Teilen für jede Seite, ist das ein eigenes Bildfeld und ein eigener Alias mit
Zuschnitt; vereinbaren Sie es mit dem Entwickler ([Bildzuschnitte und Art Direction gestalten](bildzuschnitte-und-art-direction-gestalten.md)).

## Ein Standardbild

Viele Seiten haben überhaupt kein Bild. Gestalten Sie ein Standardbild zum Teilen pro Website (und pro Marke in einer
Multisite-Installation): das Logo oder ein Markenmuster auf der Markenfarbe, innerhalb des sicheren Bereichs. Mit
xrowmetadata lässt es sich als Standard der Klasse festlegen; ohne die Erweiterung greift das Template für den
Seitenkopf darauf zurück. Das Bild ist eine normale Datei des Designs oder ein Objekt in der Medienbibliothek, was mit
dem Entwickler entschieden wird.

## Längen von Titel und Beschreibung

Das Bearbeitungsformular von xrowmetadata begrenzt, was Redakteure schreiben: standardmäßig 65 Zeichen für den Titel und
150 für die Beschreibung (`[EditorInputSettings] MaxTitle` und `MaxDescription`). Schreiben Sie Ihre Beispiele in diesen
Grenzen, und gestalten Sie den sichtbaren Seitentitel getrennt: Der Titel zum Teilen kann kürzer sein als die
Schlagzeile.

## Eine Vorschau testen

1. Öffnen Sie den Quelltext der Seite und prüfen Sie, dass die Tags vorhanden sind, mit einer absoluten
   `https://`-Bildadresse.
2. Öffnen Sie die Bildadresse für sich allein: Sie muss ohne Anmeldung laden.
3. Fügen Sie die Seitenadresse in das Vorschau- oder Debugging-Werkzeug der Netzwerke ein, die dem Kunden wichtig sind,
   und in einen Messenger. Netzwerke cachen Vorschauen; ihre Debugging-Werkzeuge können sie auffrischen.
4. Prüfen Sie eine Seite mit eigenem Bild, eine Seite nur mit dem Standard der Klasse und eine Seite ohne beides.

## Verwandte Seiten

- [Metadaten und Teilen in sozialen Medien](../ersteller/website-ersteller/metadaten-und-teilen-in-sozialen-medien.md)
- [Favicons und Touch-Icons](../designer/theme-design/favicons-und-touch-icons.md)
- [Für mehrere Marken im Multisite-Betrieb gestalten](fuer-mehrere-marken-in-multisite-gestalten.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
