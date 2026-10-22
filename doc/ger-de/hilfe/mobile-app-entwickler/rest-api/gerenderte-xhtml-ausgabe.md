---
title: Gerenderte XHTML-Ausgabe
audience: mobile-app-developer, app-creator, developer, designer
category: mobile-apps
order: 170
summary: Was OutputFormat=xhtml in der REST-API bewirkt - der Knoten, im View-Modus "rest" gerendert, innerhalb des in rest.ini genannten Templates, das mitgelieferte Platzhalter-Template, die zwei Templates, die eine Site bereitstellen muss, und wie eine App renderedOutput in einer Web-View anzeigt.
keywords: OutputFormat, xhtml, renderedOutput, rest_pagelayout.tpl, View-Modus rest, node/view/rest.tpl, Template, Web-View, Rich Text, REST, Mobile App, view mode rest, template, web view, rich text, mobile app
type: help
---
# Gerenderte XHTML-Ausgabe

Diese Seite richtet sich an App-Entwickler, die formatierte Inhalte möchten (Artikel mit Überschriften, Links und
Bildern), und an die Designer oder Entwickler, die den Server darauf vorbereiten. Sie erklärt `OutputFormat=xhtml`,
was mitgeliefert wird und was eine Site ergänzen muss.

## Inhalt

1. [Was der Parameter bewirkt](#was-der-parameter-bewirkt)
2. [Das mitgelieferte Template ist ein Platzhalter](#das-mitgelieferte-template-ist-ein-platzhalter)
3. [Den Server vorbereiten](#den-server-vorbereiten)
4. [Anzeige in der App](#anzeige-in-der-app)
5. [Wann Sie es verwenden](#wann-sie-es-verwenden)

## Was der Parameter bewirkt

```bash
curl -s -H "Authorization: Bearer $EXP_API_KEY" \
     "https://example.com/api/ezp/v2/content/node/<node id>?OutputFormat=xhtml"
```

Die Antwort erhält zusätzlich `renderedOutput`, einen String. Der Renderer des Kernels (`ezpContentXHTMLRenderer`,
registriert in `rest.ini [OutputSettings] RendererClass[xhtml]`) rendert den Hauptknoten des Knotens im
**View-Modus `rest`** und übergibt das Ergebnis als `module_result` an das Template, das in
`rest.ini [ezp7xRestContentController_viewContent_OutputSettings] Template` genannt ist (Voreinstellung
`rest_pagelayout.tpl`).

Der Leitfaden weist darauf hin, dass `renderedOutput` bei Lesezugriffen über `ezp7xRestContentController` bis
`ezprestapi` 1.2.5 leer war und seit 1.2.6 mit dem Kernel 6.0.15 wieder gefüllt wird
([Konfiguration](../../../anleitungen/rest-api-v1-und-v2.md#5-konfiguration)).

## Das mitgelieferte Template ist ein Platzhalter

`design/standard/templates/rest_pagelayout.tpl` enthält eine kurze HTML-Seite, die dazu auffordert, in der
REST-Dokumentation nachzulesen, wie die Templates für `OutputFormat` konfiguriert werden. Den Knoten gibt sie nicht
aus. Solange die Site keine eigenen Templates bereitstellt, ist `renderedOutput` diese Platzhalterseite.

## Den Server vorbereiten

Das ist serverseitige Arbeit für Designer oder Anwendungsentwickler:

1. Ein Seitenlayout für die REST-Ausgabe unter dem Namen aus der Einstellung `Template`, das
   `{$module_result.content}` ausgibt und nichts, was die App nicht braucht (keine Menüs, keine Skripte).
2. Ein Knoten-View-Template für den View-Modus `rest` (`node/view/rest.tpl` oder Override-Regeln pro Klasse), das die
   Felder rendert, die die App anzeigt.
3. Leeren Sie die Template-Caches und deployen Sie unter Velocity wie gewohnt.

Wie Overrides funktionieren, steht in [Template-Overrides](../../ersteller/website-ersteller/template-overrides.md); die
Einstellungen in [REST-API v1 und v2: Konfiguration](../../../anleitungen/rest-api-v1-und-v2.md#5-konfiguration).

## Anzeige in der App

- Laden Sie `renderedOutput` mit der Basis-URL der Site in eine Web-View, damit relative Links und Bildadressen
  aufgelöst werden.
- Geben Sie der Web-View eine strenge Inhaltsrichtlinie: keine Skripte, sofern Sie sie nicht brauchen, und öffnen Sie
  externe Links im System-Browser.
- Gestalten Sie die Ausgabe mit einem kleinen Stylesheet, das mit der App ausgeliefert wird, oder lassen Sie die
  `rest`-Templates Klassen setzen, die Ihre App gestaltet.

## Wann Sie es verwenden

| Verwenden Sie es, wenn | Wählen Sie etwas anderes, wenn |
|---|---|
| Artikel Überschriften, Listen, Links und eingebettete Bilder behalten müssen | Die App native Textansichten braucht: Fragen Sie dann nach einem strukturierten Export aus einer Serverfunktion |
| Das Site-Team die `rest`-Templates verantworten kann | Niemand Templates pflegen kann: Zeigen Sie dann reinen Text aus dem Feldwert |
| Dieselbe Formatierung auf der Site und in der App erscheinen soll | Die App Daten braucht, kein Markup |

## Verwandte Seiten

- [Rich Text in Apps](../inhaltsmodellierung/rich-text-in-apps.md)
- [Felder und Feldwerte](felder-und-feldwerte.md)
- [Headless Exponential: Rich Text, Bilder und Links](../../ersteller/anwendungsentwickler/headless-exponential.md#rich-text-bilder-und-links)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
