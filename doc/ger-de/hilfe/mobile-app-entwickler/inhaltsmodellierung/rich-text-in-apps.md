---
title: Rich Text in Apps
audience: mobile-app-developer, app-creator, developer, designer
category: mobile-apps
order: 440
summary: Artikeltexte (Rich Text vom Typ ezxmltext) in einer App anzeigen - warum der REST-Wert reiner Text ist, die drei Möglichkeiten (reiner Text, gerendertes XHTML in einer Web View, ein strukturierter Export von einem Serverendpunkt), Links und eingebettete Bilder im Rich Text und wie Sie wählen.
keywords: Rich Text, ezxmltext, XML-Text, reiner Text, strip_tags, OutputFormat, xhtml, renderedOutput, Web View, HTML, Links, eingebettete Bilder, strukturierter Export, Artikeltext, Mobile App, rich text, XML text, plain text, web view, links, embedded images, structured export, article body, mobile app
type: help
---
# Rich Text in Apps

Diese Seite richtet sich an App-Entwickler, die Artikel, Nachrichten oder andere Inhalte mit formatiertem Fließtext
zeigen. Rich-Text-Felder (der Datentyp `ezxmltext`) sind der Teil des Inhaltsmodells, der vor dem Bauen eine
Entscheidung braucht.

## Inhalt

1. [Was der REST-Wert ist](#was-der-rest-wert-ist)
2. [Drei Möglichkeiten](#drei-möglichkeiten)
3. [Links und eingebettete Inhalte](#links-und-eingebettete-inhalte)
4. [Auswählen](#auswählen)

## Was der REST-Wert ist

Bei einem `ezxmltext`-Feld ist der `value` der REST-Antwort das gerenderte HTML des Felds, aus dem jedes Tag entfernt
wurde (`strip_tags()` in `ezpRestContentModel::attributeOutputData()`). Absatzwechsel, Überschriften, Listen, fetter
Text, Links und eingebettete Bilder sind verschwunden. Für eine Teaserzeile reicht das, für einen Artikeltext nicht.

## Drei Möglichkeiten

| Möglichkeit | Wie | Liefert | Kostet |
|---|---|---|---|
| Reiner Text | der Feldwert aus REST | nur Text | nichts; verliert jede Formatierung |
| Gerendertes XHTML | `?OutputFormat=xhtml` bei einem Knoten-Lesezugriff, mit `rest`-Templates auf dem Server | `renderedOutput`, HTML des ganzen Knotens | das Website-Team pflegt zwei Templates; die App braucht eine Web View ([Gerenderte XHTML-Ausgabe](../rest-api/gerenderte-xhtml-ausgabe.md)) |
| Ein strukturierter Export | ein für die App geschriebener Serverendpunkt, der das Feld in eine Liste von Blöcken (Absatz, Überschrift, Bild, Link) umwandelt | native Darstellung, volle Kontrolle | PHP-Arbeit auf dem Server ([ezjscore-Serverfunktionen für Ihre App](../json-endpunkte/ezjscore-serverfunktionen-fuer-ihre-app.md)) |

Auch die Remote-Dienste exportieren Attributwerte (`expnode::dataMap`, `expnode::attribute`); prüfen Sie auf einer
Testinstallation, was sie für Ihre Rich-Text-Felder liefern, bevor Sie sie für Artikeltexte wählen.

## Links und eingebettete Inhalte

Rich Text kann auf andere Inhalte verlinken und Bilder und Objekte einbetten. In gerendertem HTML zeigen diese Links auf
Adressen der Website. Eine App, die sie nativ öffnen möchte, löst den Pfad zu einem Knoten auf
([Knoten-IDs, Remote-IDs und Deep Links](knoten-ids-remote-ids-und-deep-links.md#einen-url-pfad-auflösen)); „ordnen Sie sie
über URL-Alias oder Knoten-ID den Routen Ihres Frontends zu“ lautet der Rat von
[Headless-Exponential](../../ersteller/anwendungsentwickler/headless-exponential.md#rich-text-bilder-und-links).
Eingebettete Bilder in gerendertem HTML verwenden die Bildaliase der Templates der Website.

## Auswählen

- **Teaser und kurze Texte:** reiner Text, und bitten Sie um ein eigenes einfaches Teaserfeld
  ([Inhaltsklassen für Apps](inhaltsklassen-fuer-apps.md#felder-die-apps-helfen)).
- **Eine Nachrichten- oder Magazin-App mit langen Artikeln:** Gerendertes XHTML ist der schnellste Weg, wenn das
  Website-Team die `rest`-Templates übernehmen kann; ein strukturierter Export, wenn die App nativ darstellen muss
  (Einstellungen der Bedienungshilfen, dunkler Modus, Schriften).
- **Rich Text aus der App schreiben:** Das Anlegen über REST reicht Rich Text durch den Eingabeparser des Online-Editors,
  daher wird einfaches HTML wie `<p>Text</p>` akzeptiert ([Inhalte anlegen und entfernen](../rest-api/inhalte-anlegen-und-entfernen.md)).

## Verwandte Seiten

- [Felder und Feldwerte](../rest-api/felder-und-feldwerte.md)
- [Gerenderte XHTML-Ausgabe](../rest-api/gerenderte-xhtml-ausgabe.md)
- [Inhaltsmodellierung für Apps](README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
