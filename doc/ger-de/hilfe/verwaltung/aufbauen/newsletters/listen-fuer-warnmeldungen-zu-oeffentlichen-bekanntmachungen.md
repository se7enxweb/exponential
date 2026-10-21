---
title: Listen für Warnmeldungen zu öffentlichen Bekanntmachungen
audience: government, admin, editor
category: public-sector
order: 30
summary: Wie Sie cjw_newsletter-Listen für öffentliche Warnmeldungen einrichten (Beteiligungsverfahren, Baustellen, Bauleitplanung, Änderungen von Leistungen) - die Struktur, eine Liste je Thema oder Gebiet, eine kurze Ausgabe als Warnmeldung, Übersetzung und wie schnell Warnmeldungen hinausgehen.
keywords: Warnmeldungen, öffentliche Bekanntmachungen, Liste für Warnmeldungen, Beteiligungsverfahren, Baustellen, Bauleitplanung, Themen, Gebiete, Listen, Ausgaben, alerts, public notices, alert list, consultations, road works, planning notices, topics, areas, cjw_newsletter, lists, editions
type: help
---
# Listen für Warnmeldungen zu öffentlichen Bekanntmachungen

Diese Seite richtet sich an Administratoren und Redakteure, die kurze Warnmeldungen per E-Mail senden, wenn etwas
veröffentlicht wird: ein neues Beteiligungsverfahren, Baustellen in einem Stadtteil, eine Bekanntmachung der
Bauleitplanung. Mit `cjw_newsletter` ist jedes Thema der Warnmeldungen eine Liste, die Menschen abonnieren
([Newsletter mit cjw_newsletter](../../../ersteller/website-ersteller/newsletter-mit-cjw-newsletter.md)).

## Inhalt

1. [Struktur](#struktur)
2. [Eine Liste einrichten](#eine-liste-einrichten)
3. [Eine Warnmeldung schreiben](#eine-warnmeldung-schreiben)
4. [Wie schnell Warnmeldungen hinausgehen](#wie-schnell-warnmeldungen-hinausgehen)
5. [Sprachen](#sprachen)

## Struktur

```
Newsletters (folder, RootFolderNodeId)
  Public alerts (newsletter system)
    Consultations (list)
    Road works, North district (list)
    Planning notices (list)
```

Eine Liste je Thema oder Gebiet lässt Menschen genau wählen, was sie möchten; zu viele Listen verwirren. Die Funktion
der Interessen der Erweiterung ist ein weiterer Weg, Inhalte innerhalb einer Liste anzupassen (siehe die Anleitung zur
Darstellung der Erweiterung, verlinkt von [cjw_newsletter](../../../../funktionen/6.0/erweiterungen/cjw_newsletter.md)).

## Eine Liste einrichten

1. Stellen Sie sicher, dass der Newsletter-Ordner existiert und `[NewsletterSettings] RootFolderNodeId` in
   `settings/override/cjw_newsletter.ini.append.php` auf ihn zeigt.
2. Legen Sie darunter ein Newsletter-System „Öffentliche Warnmeldungen“ an, dann eine Liste je Thema.
3. Verlinken Sie das Anmeldeformular (`newsletter/subscribe`) von den Seiten jedes Themas: Der Bereich der
   Beteiligungsverfahren verlinkt die Liste „Beteiligungsverfahren“.
4. Proben Sie mit dem Datei-Transport (siehe „Mit dem Datei-Transport proben“).

## Eine Warnmeldung schreiben

Halten Sie Warnmeldungen kurz: was, wo, wann und ein Link zur Seite mit den Einzelheiten. Legen Sie unter der Liste
eine Ausgabe an, sehen Sie sich die Vorschau an, senden Sie einen Test an die Testgruppe, dann **Send**. Ist die
Freigabe eingeschaltet, wartet die Ausgabe im Posteingang der Zusammenarbeit, bis sie freigegeben ist.

## Wie schnell Warnmeldungen hinausgehen

Versand läuft über Cron: Die dokumentierte Crontab führt den Teil `cjw_newsletter` alle zehn Minuten aus, und
Ratenbegrenzungen verteilen große Listen über die Zeit. Eine Warnmeldung ist daher nicht sofort da; für Notfälle
verwenden Sie die Kanäle, die Ihre Behörde dafür hat, und die Startseite der Website.

## Sprachen

Eine Ausgabe kann in mehreren Sprachen gesendet werden; Abonnenten erhalten ihre Sprache (siehe die Anleitung zur
Darstellung der Erweiterung). Bieten Sie das Anmeldeformular auf jeder Sprach-Site an.

## Verwandte Seiten

- [Newsletter für Behörden](newsletter-fuer-behoerden.md)
- [Newsletter sicher versenden](../../../ersteller/website-ersteller/newsletter-versenden.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
