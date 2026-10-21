---
title: Cookie-Banner und Einwilligung
audience: government, project-manager, developer, designer
category: public-sector
order: 200
summary: Für öffentliche Stellen, die entscheiden, ob ihre Website ein Cookie-Einwilligungsbanner braucht - Exponential liefert weder Banner noch Consent-Manager mit, der Kernel braucht für seine funktionalen Cookies keines, und was zu tun ist, wenn Sie Webanalyse, Einbettungen oder andere nicht notwendige Speicherung hinzufügen.
keywords: Cookie-Banner, Consent-Manager, Cookie-Einwilligung, ePrivacy, Artikel 5 Absatz 3, unbedingt erforderlich, Einwilligung für Webanalyse, Einbettungen, Zwei-Klick-Lösung, Barrierefreiheit von Bannern, CSP, öffentlicher Sektor, cookie banner, consent manager, cookie consent, strictly necessary
type: help
---
# Cookie-Banner und Einwilligung

Diese Seite richtet sich an Projektleitungen, Datenschutzbeauftragte, Designer und Entwickler. Exponential liefert
**weder ein Cookie-Einwilligungsbanner noch einen Consent-Manager** im Kernel oder in den mitgelieferten Designs aus;
laut der Aussage des Projekts wurde in `kernel/`, `design/` oder `settings/` keines gefunden
([Datenschutzstandards: ePrivacy-Richtlinie](../../../standards/datenschutz.md#eprivacy-richtlinie-cookies)). Die eigenen
Cookies des Kernels sind funktional. Ein Banner wird zu Ihrer Aufgabe, wenn Sie Speicherung oder Dienste hinzufügen,
die eine Einwilligung erfordern. ePrivacy-Richtlinie: <https://eur-lex.europa.eu/eli/dir/2002/58/oj>.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Brauchen Sie eines](#brauchen-sie-eines)
2. [Wenn Sie eines brauchen](#wenn-sie-eines-brauchen)
3. [Alternativen, die es vermeiden](#alternativen-die-es-vermeiden)
4. [Verwandte Seiten](#verwandte-seiten)

## Brauchen Sie eines

| Ihre Website hat | Übliche Einschätzung (mit Ihrer Rechtsberatung zu bestätigen) |
|---|---|
| nur die Cookies des Kernels (Sitzung, `is_logged_in`) | für unbedingt erforderliche Cookies ist keine Einwilligung nötig; beschreiben Sie sie in der Datenschutzerklärung |
| Webanalyse (`bcwebsitestatistics` oder ein anderer Dienst) | in der Regel ist eine Einwilligung nötig; die Erweiterung hat keine eigene Einwilligungsabfrage |
| ein CAPTCHA (`recaptcha`, `hcaptcha`) | eine Einwilligung ist „in den meisten Lesarten von Art. 5 Abs. 3“ nötig, sagt die Aussage des Projekts |
| eingebettete Karten oder Videos anderer Dienste | in der Regel eine Einwilligung, weil der Dienst eigene Speicherung setzt |
| Schaltflächen für Social Login | der Benutzer wählt den Anbieter; nennen Sie ihn in der Datenschutzerklärung |

## Wenn Sie eines brauchen

- Laden Sie das Skript des Drittanbieters **erst nach** der Einwilligung; ein Banner, das nichts blockiert, ist keine
  Einwilligung.
- Bauen Sie es in Ihr Design ein: ein Template-Block und eine Skriptdatei. Unter einer Content Security Policy muss das
  Skript eine Datei sein (kein Inline-Code), und die Hosts des Drittanbieters dürfen nur dort erlaubt sein, wo sie
  genutzt werden.
- Machen Sie das Banner barrierefrei: per Tastatur bedienbar, mit gesteuertem Fokus, ohne das fokussierte Element zu
  verdecken, bei 200 % Zoom lesbar, mit klaren, gleich gewichteten Schaltflächen für Zustimmen und Ablehnen.
- Zeichnen Sie Einwilligungsentscheidungen so auf, wie Ihre Rechtsberatung es verlangt; das Einwilligungsprotokoll von
  Exponential deckt E-Mails ab, keine Cookies.
- Wenn Sie ein Consent-Manager-Produkt einsetzen, ist es ein Drittanbieter: Prüfen Sie seine eigene Datenverarbeitung.

## Alternativen, die es vermeiden

- Nutzen Sie die betrieblichen Statistiken, die Exponential führt (Suchstatistiken, Aufrufzähler), wo sie die Frage
  beantworten ([Besucherstatistiken und Webanalyse](../../ersteller/website-ersteller/besucherstatistik-und-analyse.md)).
- Selbst betriebene Webanalyse ohne Cookies, wo Ihre Rechtsberatung zustimmt.
- Einbettungen mit zwei Klicks: Zeigen Sie einen Platzhalter mit einem Link; laden Sie die Karte oder das Video erst
  beim Klick.
- Serverseitiger Spamschutz (das Formular-Token von `ezformtoken`, Prüfungen in Ihrem eigenen Code) statt eines
  CAPTCHAs.

## Verwandte Seiten

- [Von Exponential gesetzte Cookies](cookies.md)
- [Erweiterungen von Drittanbietern](erweiterungen-von-drittanbietern.md)
- [Besucherstatistiken und Webanalyse](../../ersteller/website-ersteller/besucherstatistik-und-analyse.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
