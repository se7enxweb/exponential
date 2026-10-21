---
title: Eine Unter-Site für eine Abteilung einrichten
audience: government, admin
category: public-sector
order: 140
summary: Aufgabe für Administratoren - einer bestehenden Installation eine Site für eine Abteilung hinzufügen, mit eigenem Ordner, Siteaccess, Host und Link zur Administration.
keywords: Abteilungs-Site, Unter-Site, Siteaccess, Multisite, neue Site, department site, subsite, siteaccess, RootNode, IndexPage, HostMatchMapItems, multisite, new site
type: help
---
# Eine Unter-Site für eine Abteilung einrichten

Diese Aufgabe richtet sich an Administratoren. Hintergrund:
[Eine Abteilungs-Site hinzufügen](../../aufbauen/multisite/eine-abteilungs-site-hinzufuegen.md).

## Bevor Sie beginnen

- Der Hostname der Abteilung (zum Beispiel `dept.example.gov`) im DNS und im Zertifikat, oder ein Pfad.
- Ein Design dafür, oder das gemeinsame.

## Schritte

1. Legen Sie den Ordner der Abteilung unter der Wurzel an; notieren Sie seine Knoten-ID.
2. Kopieren Sie `settings/siteaccess/site` nach `settings/siteaccess/dept_example`; setzen Sie `SiteName`, `SiteURL`,
   `IndexPage` und `DefaultPage` (`/content/view/full/<node id>`), `SiteDesign` und
   `content.ini [NodeSettings] RootNode=<node id>`.
3. In `settings/override/site.ini.append.php`: `AvailableSiteAccessList[]=dept_example`,
   `RelatedSiteAccessList[]=dept_example` und `HostMatchMapItems[]=dept.example.gov;dept_example` (bestehende
   Einträge behalten).
4. Fügen Sie `dept_example` der `[SiteSettings] SiteList[]` des Administrations-Siteaccess hinzu.
5. Fügen Sie den Host dem Webserver oder Velocity hinzu.
6. `php bin/php/ezcache.php --clear-tag=ini`.

## Prüfen

`curl -sI https://dept.example.gov/ | head -1` antwortet mit 200; die Startseite ist der Ordner der Abteilung.

## Probleme

| Problem | Lösung |
|---|---|
| Der Host zeigt die Hauptsite | `HostMatchMapItems[]` fehlt, oder `MatchOrder` enthält `host` nicht |
| Links zeigen auf die falsche Site | siehe „Von einer Abteilungs-Site auf eine andere verlinken“ |

## Verwandte Seiten

- [Mehrere Sites einrichten](../../../../../eng-us/help/creators/website-creators/multisite-setup.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
