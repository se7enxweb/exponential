---
title: Suchbegriffe
audience: government, admin
category: public-sector
order: 270
summary: Für Administratoren öffentlicher Websites - die Suchstatistiken von Exponential speichern, was Besucher in die Suche eingeben, und das kann Namen oder Aktenzeichen enthalten; sie sind standardmäßig aus, werden pro Siteaccess eingeschaltet und sollten zurückgesetzt werden, wenn sie nicht mehr gebraucht werden.
keywords: Suchstatistiken, Suchbegriffe, LogSearchStats, Setup Suchstatistiken, Statistik zurücksetzen, personenbezogene Daten, Datenminimierung, Aufbewahrung, öffentlicher Sektor, search statistics, search phrases, reset statistics, personal data
type: help
---
# Suchbegriffe

Diese Seite richtet sich an Administratoren. Suchstatistiken zeigen Ihnen, wonach Besucher suchen und was sie nicht
finden, und das ist für eine öffentliche Website wertvoll. Sie speichern aber auch genau das, was Besucher eingegeben
haben, und Menschen geben Namen, Adressen und Aktenzeichen in Suchfelder ein
([URLs und Suchstatistiken](../../../anleitungen/urls-und-aliase.md#5-suchstatistiken)).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Standardmäßig aus](#standardmäßig-aus)
2. [Einschalten](#einschalten)
3. [Nutzen und zurücksetzen](#nutzen-und-zurücksetzen)
4. [Verwandte Seiten](#verwandte-seiten)

## Standardmäßig aus

`site.ini [SearchSettings] LogSearchStats=disabled`. Die Seite **Setup > Suchstatistiken** sagt, welche Siteaccesses
Suchen aufzeichnen, oder warnt, dass keiner es tut. Suchbegriffe werden ohne Benutzer oder Adresse gespeichert.

## Einschalten

Nur auf dem öffentlichen Siteaccess, wo Sie sie brauchen:

```ini
# settings/siteaccess/<public siteaccess>/site.ini.append.php
[SearchSettings]
LogSearchStats=enabled
```

Erwähnen Sie in der Datenschutzerklärung, dass Suchbegriffe zur Verbesserung der Website aufgezeichnet werden.

## Nutzen und zurücksetzen

- Die Seite zeigt die Suchbegriffe, wie oft nach jedem gesucht wurde, und die durchschnittliche Zahl der Treffer;
  **Anzeigen** filtert die Suchbegriffe, die nichts gefunden haben.
- Handeln Sie danach: Ergänzen Sie fehlende Inhalte oder die Wörter, die die Menschen benutzen.
- **Statistik zurücksetzen** entfernt nach einer Bestätigung an Ort und Stelle alle Suchbegriffe endgültig. Setzen Sie
  nach Plan zurück (zum Beispiel vierteljährlich, nachdem Sie Ihre Schlüsse gezogen haben) und immer dann, wenn Sie
  personenbezogene Daten in der Liste bemerken.
- Geben Sie Zugriff auf die Seite nur den Personen, die ihn brauchen.

## Verwandte Seiten

- [Links, URL-Aliase, Platzhalter und Suchstatistiken](../../../anleitungen/urls-und-aliase.md)
- [Datenminimierung](datenminimierung.md)
- [Suche auf der Website](../../ersteller/website-ersteller/suche-auf-der-website.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
