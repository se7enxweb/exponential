---
title: Von einer Abteilungs-Site auf eine andere verlinken
audience: government, admin, website-creator
category: public-sector
order: 170
summary: Aufgabe für Administratoren - Links von einer Site auf Inhalte einer anderen Site derselben Installation auf den richtigen Host zeigen lassen, mit den Einstellungen von ezurlmultisite, und sie prüfen.
keywords: Links zwischen Sites, Multisite-Links, Abteilungs-Site, cross-site links, ezurlmultisite, SiteAccessList, RootNodeList, PathPrefix, multisite links
type: help
---
# Von einer Abteilungs-Site auf eine andere verlinken

Diese Aufgabe richtet sich an Administratoren. Hintergrund:
[Links zwischen Abteilungs-Sites](../../aufbauen/multisite/links-zwischen-abteilungs-sites.md).

## Bevor Sie beginnen

Die Sites verwenden `PathPrefix` (jeder Siteaccess blendet den Pfad seines Teilbaums aus), und `ezurlmultisite` ist
installiert (die `composer.json` im Wurzelverzeichnis verlangt es).

## Schritte

1. Legen Sie `settings/override/ezurlmultisite.ini.append.php` an:

   ```ini
   <?php /*
   [MultisiteSettings]
   SiteAccessList[]
   SiteAccessList[]=site
   SiteAccessList[]=dept_example
   RootNodeList[site]=<node id>
   RootNodeList[dept_example]=<node id>
   */ ?>
   ```

2. Leeren Sie den INI- und den Template-Cache; mit Velocity auch dessen Antwort-Cache.

Ohne die Datei nehmen die öffentlichen Siteaccesses mit einem `PathPrefix` mit ihrem `RootNode` oder ihrer `IndexPage`
teil.

## Prüfen

Suchen oder listen Sie auf der Hauptsite eine Seite der Abteilungs-Site; der Link muss auf `dept.example.gov` zeigen
und die Seite öffnen.

## Probleme

| Problem | Lösung |
|---|---|
| Links antworten weiterhin mit 404 | der Siteaccess fehlt in `SiteAccessList[]`, oder ein Cache |
| Links der Administration haben sich geändert | das sollten sie nicht: Siteaccesses der Administration nehmen nie teil |

## Verwandte Seiten

- [Links auf Inhalte einer anderen Site](../../../../funktionen/6.0/websiteuebergreifende-links-im-multisite.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
