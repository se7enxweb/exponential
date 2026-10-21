---
title: Karten und Datenschutz
audience: government, admin, project-manager
category: public-sector
order: 60
summary: Was mit den Daten von Besuchern und Redakteuren geschieht, wenn eine öffentliche Site über ezgmaplocation Google-Karten zeigt, wie Sie entscheiden, was in die Datenschutzerklärung und die Content Security Policy gehört und wie die Standortfunktionen ohne jeden Drittanbieter laufen.
keywords: Datenschutz bei Karten, Google Maps, Drittanbieter, API-Schlüssel, Einwilligung, Datenschutzerklärung, ohne Karte, Standortbestimmung, maps privacy, third party, API key, Content Security Policy, consent, privacy notice, data protection, without map, geolocation
type: help
---
# Karten und Datenschutz

Diese Seite richtet sich an Administratoren, Datenschutzbeauftragte und Führungskräfte. Mit einem API-Schlüssel lädt
`ezgmaplocation` die Google Maps JavaScript API von `maps.googleapis.com` im Bearbeitungsformular und auf Seiten, die
einen Standort zeigen (die Bearbeitungs- und View-Templates der Erweiterung). Google erhält dann Anfragen aus den
Browsern von Redakteuren und Besuchern.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Was den Browser verlässt](#was-den-browser-verlässt)
2. [Entscheiden](#entscheiden)
3. [Wenn Sie Google-Karten verwenden](#wenn-sie-google-karten-verwenden)
4. [Ohne jeden Drittanbieter](#ohne-jeden-drittanbieter)

## Was den Browser verlässt

| Wann | Wer | An Google gesendete Daten |
|---|---|---|
| Ein Besucher öffnet eine Seite mit einem Standort | Besucher | die Anfrage nach dem Kartenskript und den Kacheln, mit den üblichen Browserdaten |
| Ein Redakteur bearbeitet einen Standort | Redakteur | die Kartenanfragen und die für **Find address** eingegebene Adresse |
| Ein Redakteur verwendet **My current location** | Redakteur | der Browser teilt die Position des Redakteurs mit der Seite, die sie in der Karte von Google verwendet |

## Entscheiden

Prüfen Sie es wie jeden Dienst eines Drittanbieters (siehe „Zu prüfende Dienste von Drittanbietern“ im Bereich
Souveränität): Zweck, Daten, Rechtsgrundlage und Einwilligung, Vertrag mit dem Anbieter, Alternativen. Exponential
liefert kein Cookie- oder Einwilligungsbanner mit ([Standards zum Datenschutz](../../../../standards/datenschutz.md)).

## Wenn Sie Google-Karten verwenden

- Nennen Sie den Dienst in der Datenschutzerklärung.
- Laden Sie die Karte nur dort, wo sie gebraucht wird (Seiten mit einem Standort), was die Erweiterung bereits tut.
- Erlauben Sie die Hosts von Google in der Content Security Policy, wie im README der Erweiterung aufgeführt.
- Erwägen Sie in Ihrem Override des View-Templates einen Platzhalter, der erst auf Klick lädt, damit das Kartenskript
  nur geladen wird, wenn der Besucher es verlangt.

## Ohne jeden Drittanbieter

Lassen Sie `[GMapSettings] ApiKey` leer: Es wird kein Skript von Google geladen, die Koordinaten und die Adresse werden
ohne Karte gezeigt, und der Entfernungsfilter funktioniert weiterhin. Fügen Sie bei Bedarf einen Link zu einem
Kartendienst nach Wahl des Besuchers hinzu.

## Verwandte Seiten

- [Was es für Karten gibt](was-es-fuer-karten-gibt.md)
- [Standards zum Datenschutz](../../../../standards/datenschutz.md)
- [Content Security Policy](../../../../funktionen/6.0/content-security-policy.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
