---
title: CAPTCHAs und Datenschutz
audience: government, project-manager, admin
category: public-sector
order: 230
summary: Für öffentliche Stellen, die Formulare vor Spam schützen - was die optionalen Erweiterungen recaptcha (Google reCAPTCHA) und hcaptcha (hCaptcha) tun, welche Daten sie an den Anbieter senden, was das für Einwilligung und Hinweise bedeutet, Bedenken zur Barrierefreiheit, und das Formular-Token als erste Verteidigungslinie.
keywords: CAPTCHA, reCAPTCHA, hCaptcha, Erweiterung recaptcha, Erweiterung hcaptcha, Spam, Formulare, Drittanbieter, Einwilligung, ePrivacy, Barrierefreiheit, ezformtoken, Informationssammlung, Registrierung, öffentlicher Sektor, third party, consent, accessibility
type: help
---
# CAPTCHAs und Datenschutz

Diese Seite richtet sich an Projektleitungen, Administratoren und Datenschutzbeauftragte. Zwei optionale Erweiterungen
fügen Formularen einen CAPTCHA-Datentyp hinzu: `recaptcha` (Google reCAPTCHA, v2, mit einem v3-Template für das
Feedback-Formular) und `hcaptcha`
([recaptcha](../../../funktionen/6.0/erweiterungen/recaptcha.md), [hcaptcha](../../../funktionen/6.0/erweiterungen/hcaptcha.md)).
Beide laden ein Skript des Anbieters, sodass der Browser des Besuchers mit einem Drittanbieter kommuniziert.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Wie sie funktionieren](#wie-sie-funktionieren)
2. [Folgen für den Datenschutz](#folgen-für-den-datenschutz)
3. [Barrierefreiheit](#barrierefreiheit)
4. [Entscheiden](#entscheiden)
5. [Verwandte Seiten](#verwandte-seiten)

## Wie sie funktionieren

1. Aktivieren Sie die Erweiterung; tragen Sie Ihre Schlüssel in `settings/override/recaptcha.ini.append.php` oder
   `hcaptcha.ini.append.php` ein (halten Sie das Geheimnis aus der Versionsverwaltung heraus).
2. Fügen Sie einer Klasse das CAPTCHA-Attribut hinzu: einer Kontaktformular-Klasse oder, für die Registrierung, der
   Benutzerklasse.
3. Das Formular zeigt das Widget des Anbieters; der Server prüft die Antwort beim Anbieter, bevor er das Formular
   annimmt.

`[PublishSettings] NewObjectsOnly=true` prüft nur neue Objekte (bei der Registrierung ja, beim Bearbeiten des Profils
nein) ([Spamschutz für Formulare](../../ersteller/website-ersteller/spamschutz-fuer-formulare.md)).

## Folgen für den Datenschutz

- Die Aussage des Projekts: Eine Einwilligung oder ein Hinweis ist „in den meisten Lesarten von Art. 5 Abs. 3“ nötig;
  nennen Sie den Anbieter in Ihrer Datenschutzerklärung
  ([Datenschutzstandards](../../../standards/datenschutz.md#drittanbieterdienste-in-mitgelieferten-extensions)).
- Der Anbieter erhält Browser- und Interaktionsdaten jedes Besuchers, der ein geschütztes Formular öffnet, auch von
  Menschen, die sich in persönlichen Angelegenheiten an eine öffentliche Stelle wenden.
- Unter einer Content Security Policy müssen die Hosts des Anbieters auf diesem Siteaccess erlaubt sein.

## Barrierefreiheit

CAPTCHAs sind eine bekannte Barriere für Menschen mit Seh-, kognitiven oder motorischen Beeinträchtigungen. Wenn Sie
eines einsetzen, testen Sie es mit der Tastatur und einem Screenreader, und bieten Sie neben dem Formular einen anderen
Kontaktweg an (E-Mail, Telefon). Der Bereich Barrierefreiheit dieser Hilfe behandelt Formulare.

## Entscheiden

| Möglichkeit | Datenschutz | Barrierefreiheit | Spamschutz |
|---|---|---|---|
| Nur das Formular-Token (`ezformtoken`, standardmäßig aktiv) | nichts verlässt die Website | keine Barriere | stoppt gefälschte websiteübergreifende Einsendungen, nicht aber entschlossene Bots |
| Formular-Token plus eigene serverseitige Prüfungen | nichts verlässt die Website | keine Barriere | mittel |
| request-shield davor, mit seiner Browserprüfung für Formularseiten | nichts verlässt die Website (eigene Bibliothek) | unsichtbare Prüfung | gut gegen Anfragefluten |
| hCaptcha oder reCAPTCHA | Drittanbieter | mögliche Barriere | stark |

Beginnen Sie mit den Möglichkeiten, die die Daten auf Ihrer Website halten; fügen Sie ein CAPTCHA nur bei Formularen
mit tatsächlichem Missbrauch hinzu.

## Verwandte Seiten

- [Spamschutz für Formulare](../../ersteller/website-ersteller/spamschutz-fuer-formulare.md)
- [Erweiterungen von Drittanbietern und Datenschutz](erweiterungen-von-drittanbietern.md)
- [Cookie-Banner und Einwilligung](cookie-banner-und-einwilligung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
