---
title: Einrichtung für Privatpersonen
audience: individual, website-creator
category: personal-sites
order: 1630
summary: Die Entscheidungen zur Einrichtung, die eine Person für eine persönliche Website, ein Portfolio, ein Blog, eine Familienseite oder eine Vereinsseite mit Exponential trifft, der Reihe nach - Installationsmethode, Seitenpaket und Theme, ein kleines Budget, Shared Hosting oder ein kleiner Server und der Domainname und HTTPS, die außerhalb von Exponential geregelt werden.
keywords: Einrichtung, Installation, Installationsmethode, Einrichtungsassistent, exp:install, Kickstarter, Seitenpaket, Theme, Budget, SQLite, Velocity, Shared Hosting, VPS, Domain, DNS, HTTPS, Zertifikat, setup, install, install method, setup wizard, site package, certificate
type: help
---
# Einrichtung für Privatpersonen

Dieser Bereich ist für Privatpersonen, die Exponential gleich für eine eigene Website installieren. Er bringt die
Entscheidungen in die Reihenfolge, in der sie Ihnen begegnen, und verweist für Einzelheiten auf das
Installationsbuch ([Installationsbuch](../../../installation/README.md)).

## Inhalt

1. [Die Entscheidungen der Reihe nach](#die-entscheidungen-der-reihe-nach)
2. [Drei übliche Einrichtungen](#drei-übliche-einrichtungen)
3. [Was Sie vor dem Start brauchen](#was-sie-vor-dem-start-brauchen)

## Die Entscheidungen der Reihe nach

| # | Entscheidung | Seite |
|---|---|---|
| 1 | Wie installieren: der Browser-Assistent, die Konsole oder der Kickstarter | [Eine Installationsmethode wählen](eine-installationsmethode-waehlen.md) |
| 2 | Mit welchem Seitenpaket und Theme beginnen | [Ein Seitenpaket und ein Theme wählen](ein-seitenpaket-und-ein-theme-waehlen.md) |
| 3 | Wie Sie Kosten und bewegliche Teile gering halten | [Mit kleinem Budget betreiben](mit-kleinem-budget-betreiben.md) |
| 4 | Wo die Website läuft: Shared Hosting, ein kleiner Server, Ihr eigener Rechner | [Shared Hosting oder ein kleiner Server](shared-hosting-oder-ein-kleiner-server.md) |
| 5 | Die Adresse und das Schloss: Domainname, DNS und HTTPS | [Domainnamen und HTTPS](domainnamen-und-https.md) |

## Drei übliche Einrichtungen

| Einrichtung | Installieren mit | Datenbank | Ausgeliefert durch | Passt zu |
|---|---|---|---|---|
| **Auf dem eigenen Computer ausprobieren** | `exp:install` mit seinen Standardwerten | SQLite | Den eingebauten Server von PHP oder Velocity | Lernen, Bauen vor dem Livegang |
| **Shared Hosting** | Der Einrichtungsassistent im Browser | SQLite oder die Datenbank des Panels | Apache oder LiteSpeed mit `.htaccess` | Geringen monatlichen Kosten, kein Server zu betreuen |
| **Ein kleiner virtueller Server** | `exp:install` oder der Kickstarter | SQLite oder PostgreSQL | Velocity, oder Apache oder nginx mit PHP-FPM | Volle Kontrolle, die Geschwindigkeit und die Zertifikate von Velocity |

Quellen: [Eine Installationsmethode wählen](../../ersteller/website-ersteller/eine-installationsmethode-waehlen.md),
[Die Website ausliefern](../../../installation/08-die-website-ausliefern.md), [Shared Hosting](../../hosting/modelle/shared-hosting.md).

## Was Sie vor dem Start brauchen

- [ ] PHP 8.0 oder neuer (PHP 7.4 ohne Support nutzbar) mit den erforderlichen Erweiterungen, oder einen Hoster, der es anbietet
  ([Anforderungen auf einen Blick](../../hosting/grundlagen/anforderungen-auf-einen-blick.md)).
- [ ] Für eine Installation über die Konsole: eine Shell und Composer. Für den Assistenten: einen Browser und FTP.
- [ ] Eine echte E-Mail-Adresse für den Administrator.
- [ ] Das ausgefüllte [Arbeitsblatt für die Websiteplanung](../checklisten/arbeitsblatt-fuer-die-websiteplanung.md).
- [ ] Ein paar Stunden ohne Unterbrechungen.

## Verwandte Seiten

- [Privatpersonen: hier beginnen](../privatpersonen-hier-beginnen.md)
- [Eine Installationsmethode wählen (Website-Ersteller)](../../ersteller/website-ersteller/eine-installationsmethode-waehlen.md)
- [Hosting für eine Website](../../ersteller/website-ersteller/hosting-fuer-eine-website.md)
- [Erste Schritte](../../erste-schritte.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
