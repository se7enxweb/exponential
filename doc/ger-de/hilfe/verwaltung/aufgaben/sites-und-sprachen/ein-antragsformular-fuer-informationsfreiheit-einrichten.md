---
title: Ein Antragsformular für Informationsfreiheit einrichten
audience: government, website-creator, admin
category: public-sector
order: 280
summary: Aufgabe für Site-Ersteller - ein Online-Antragsformular mit dem Information Collector bauen, jeden Antrag an das Postfach des Teams für Informationsfreiheit senden, auf eine Dankesseite weiterleiten und es testen.
keywords: Antragsformular, Informationsfreiheit, Information Collector, Feedback-Formular, Empfänger, Weiterleitung, Formular-Token, Datenschutztext, request form, FOI, information collector, feedback form, recipient, collect.ini, redirect, form token, privacy text
type: help
---
# Ein Antragsformular für Informationsfreiheit einrichten

Diese Aufgabe richtet sich an Site-Ersteller und Administratoren. Hintergrund:
[Ein Antragsformular mit dem Informationssammler](../../transparenz-und-informationsfreiheit/ein-antragsformular-mit-dem-informationssammler.md).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Schritte

1. **Setup > Klassen**: Legen Sie `information_request` an (oder passen Sie **Feedback form** an), mit sammelnden
   Attributen: Name, E-Mail für die Antwort, Beschreibung der gewünschten Information, bevorzugtes Format; und Feldern
   für Redakteure: Einleitung, Empfänger (das Postfach des Teams).
2. Legen Sie ein Objekt der Klasse auf der Seite „Informationen beantragen“ an; schreiben Sie die Einleitung (was
   gefragt werden kann, Antwortfristen, andere Kanäle, Datenschutz).
3. Leiten Sie Besucher nach dem Senden auf eine Dankesseite, damit ein Neuladen den Antrag nicht erneut sendet. Der
   Kernel liest dafür zwei optionale Attribute des Formularobjekts
   (`collect.ini [DisplaySettings] DisplayAttribute=collection_display`,
   `RedirectURLAttribute=collection_redirect`, gelesen von `eZInformationCollection::displayHandling()` und
   `redirectURL()`): Fügen Sie der Klasse Textzeilen-Attribute mit den Bezeichnern `collection_display` und
   `collection_redirect` hinzu (keine sammelnden), und füllen Sie sie im Formularobjekt mit `redirect` und der Adresse
   der Dankesseite. Alternativ setzen Sie für den Formulartyp `DisplayList[<type>]=redirect` und
   `RedirectURLList[<type>]` in `settings/override/collect.ini.append.php`, was jedes Formular dieses Typs betrifft.
4. Lassen Sie `ezformtoken` aktiv.
5. Geben Sie `infocollector/read` nur der Rolle des Teams für Informationsfreiheit.

## Prüfen

Senden Sie abgemeldet einen Antrag mit einer Testadresse auf Ihrer eigenen Domain: Die Mail erreicht das Postfach des
Teams (oder mit dem Datei-Transport das Dateiverzeichnis), die Einsendung erscheint unter **Setup > Gesammelte
Informationen**. Entfernen Sie die Testeinsendung.

## Probleme

| Problem | Lösung |
|---|---|
| Keine Mail | `[EmailSettings] SendEmail` für den Typ, der Mail-Transport oder das Empfängerfeld |
| Doppelte Einsendungen | verwenden Sie die Anzeige mit Weiterleitung |

## Verwandte Seiten

- [Formulare mit dem Information Collector](../../../ersteller/website-ersteller/formulare-mit-dem-informationssammler.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
