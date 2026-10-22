---
title: Multisite-Angebote
audience: reseller, partner, hosting
category: reselling
order: 220
summary: Wie Sie mehrere Websites auf einer Exponential-Installation verkaufen - Siteaccesses, die nach URL, Host oder Port zugeordnet werden, das Seitenpaket sevenx_multisite, Logs und Caches je Website, wann getrennte Installationen besser sind, wie Sie „eine Website“ definieren und bepreisen, und eine Checkliste für das Angebot.
keywords: Multisite, mehrere Websites, Siteaccess, Mehrmarken, Host-Zuordnung, HostMatchMapItems, sevenx_multisite, Einstellungen je Website, Logs je Website, Preis je Website, eine Installation, multisite, multi-site, siteaccess, several sites, multi-brand, host matching, per-site settings, logs per site, pricing per site, one installation
type: help
---
# Multisite-Angebote

Diese Seite ist für Wiederverkäufer, die Angebote mit mehreren Websites verkaufen: eine Gruppe von Marken, ein
Unternehmen mit Länder-Websites, eine Schule mit Websites für ihre Abteilungen. Exponential liefert über
**Siteaccesses** viele Websites aus einer Installation aus; das ist ein starkes Verkaufsargument und eine Frage der
Preisgestaltung.

## Inhalt

1. [Wie Multisite funktioniert](#wie-multisite-funktioniert)
2. [Eine Installation oder mehrere](#eine-installation-oder-mehrere)
3. [Eine Website definieren und bepreisen](#eine-website-definieren-und-bepreisen)
4. [Was Sie dem Kunden sagen](#was-sie-dem-kunden-sagen)
5. [Checkliste für das Angebot](#checkliste-für-das-angebot)

## Wie Multisite funktioniert

| Begriff | Was es ist | Quelle |
|---|---|---|
| Siteaccess | „ein Weg in die Installation: ein Name, ein Satz Einstellungen in `settings/siteaccess/<name>/` und eine Regel, auf welche Anfragen er antwortet“ | [Multisite auf einer Installation](../../hosting/bereitstellung/mehrere-websites-in-einer-installation.md) |
| Zuordnung | `site.ini [SiteAccessSettings]`, `MatchOrder` ist im Auslieferungszustand `uri;host;port`; nach URL (`example.com/admin`), Host (`admin.example.com`) oder Port | ebenda |
| Host-Zuordnung nach Anfang, Ende oder Teil eines Hostnamens | `HostMatchMethod` (`strict` voreingestellt, `start`, `end`, `part`) | [Siteaccess nach Host-Präfix](../../../funktionen/6.0/siteaccess-zuordnung-nach-host-und-sprache.md) |
| Ein fertiger Ausgangspunkt | Seitenpaket `sevenx_multisite` („7x MultiSite Default Installation“) oder `sevenx_multisite_clean` ohne Demo-Inhalte | [Seitenpakete für Hoster](../../hosting/bereitstellung/seitenpakete-fuer-hoster.md) |
| Logs und Caches je Website | `site.ini [FileSettings]` `UseGlobalLogDir`, `LogVarDir`, `CacheVarDir`, `INICacheDir` | [Logs, INI-Cache und Ablaufzeitstempel je Website](../../../funktionen/6.0/log-und-cache-pfade-fuer-mehrere-websites.md) |
| Links zwischen Websites | Website-übergreifende Links | [Website-übergreifende Links](../../../funktionen/6.0/websiteuebergreifende-links-im-multisite.md) |

Alle Websites auf einer Installation teilen sich ein Inhalts-Repository und einen Benutzerbestand, sodass Inhalte
über Marken hinweg geteilt werden können ([Inhalte über Marken hinweg teilen](../../unternehmen/multisite/inhalte-zwischen-marken-teilen.md)).

## Eine Installation oder mehrere

| | Eine Installation, mehrere Siteaccesses | Eine Installation je Website |
|---|---|---|
| Gemeinsame Inhalte und Benutzer | Ja | Nein |
| Ein Update für alle Websites | Ja | Eines je Website |
| Isolation auf Betriebssystemebene zwischen Websites | Nein | Ja |
| Die Last einer Website betrifft die anderen | Ja | Nein, mit getrennten Pools |
| Datensicherungen und Wiederherstellungen | Gemeinsam | Je Website |

Die Faustregel des Hosting-Bereichs: mehrere Siteaccesses auf einer Installation für **einen Kunden** mit mehreren
Websites; getrennte Installationen für **verschiedene Kunden**
([Multisite auf einer Installation](../../hosting/bereitstellung/mehrere-websites-in-einer-installation.md#eine-installation-oder-mehrere)).

> **Hinweis**
> Legen Sie nie zwei verschiedene Kunden auf eine Installation, um Geld zu sparen. Sie würden sich Benutzer,
> Inhalts-Repository und Updates teilen, und keiner könnte ohne den anderen gehen.

## Eine Website definieren und bepreisen

Preise „je Website“ brauchen eine Definition. Wählen Sie eine und schreiben Sie sie in das Angebot:

| Definition | Klar, weil | Achten Sie auf |
|---|---|---|
| Ein öffentlicher Siteaccess | Leicht in `settings/siteaccess/` zu zählen | Siteaccesses für Sprachen vervielfachen die Zahl |
| Ein Hostname mit eigenem Design | Entspricht dem, was der Kunde sieht | Eine Marke mit zwei Sprachen kann eine „Website“ sein |
| Eine Marke (Design + Hostnamen + Sprachen) | Entspricht dem Geschäft des Kunden | Braucht eine schriftliche Liste |

Bepreisen Sie die Einrichtung je Website (Hostnamen, Zertifikate, Design, Tests) und die Betriebskosten je
Installation, dazu einen kleinen Betrag je Website für den Support. Weitere Möglichkeiten:
[Optionen für Abrechnung und Messung](../../hosting/geschaeft/abrechnungs-und-messoptionen.md).

## Was Sie dem Kunden sagen

```text
All your sites run on one Exponential installation. They share content and user accounts where you want,
each has its own address, design and settings, and one update keeps all of them current.
```

Und die ehrlichen Grenzen: Eine Installation wird als Ganzes aktualisiert und wiederhergestellt, und eine hohe Last
auf einer Website spüren die anderen, es sei denn, Sie planen die Kapazität dafür.

## Checkliste für das Angebot

- [ ] Anzahl der Websites, Hostnamen und Sprachen aufgelistet.
- [ ] Definition von „einer Website“ im Angebot festgehalten.
- [ ] Jeder Hostname in DNS, im Webserver oder in Velocity und im Zertifikat.
- [ ] Jeder Siteaccess hat seine eigene `[SiteSettings] SiteURL` und wurde nach dem Leeren des INI-Caches getestet
      (`php bin/php/ezcache.php --clear-tag=ini`).
- [ ] Logs und Caches je Website entschieden.
- [ ] Rollen der Redakteure, wo nötig, auf ihre eigene Website beschränkt.

## Verwandte Seiten

- [Multisite auf einer Installation](../../hosting/bereitstellung/mehrere-websites-in-einer-installation.md)
- [Eine Installation, viele Websites](../../unternehmen/multisite/eine-installation-viele-websites.md)
- [Planung für mehrere Marken](../../unternehmen/multisite/mehrere-marken-planen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
