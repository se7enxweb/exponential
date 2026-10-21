---
title: Verfügbarkeit für Websites des öffentlichen Sektors
audience: government, hosting, admin, project-manager
category: public-sector
order: 10
summary: Übersicht der Seiten zur Verfügbarkeit für öffentliche Stellen: Hochverfügbarkeit, mehrere Webserver, gemeinsamer Speicher, Datenbankverfügbarkeit und Lese-Replikate, die Caches vor PHP, Cache-Aufwärmen, Wartungsmodus, Sitzungen, Kapazitätsplanung und Service-Level.
keywords: Verfügbarkeit, Hochverfügbarkeit, Kontinuität, Load Balancer, Cluster, DFS, Datenbank, Replikate, statischer Cache, Antwort-Cache, HTTP-Cache, Cache-Aufwärmen, Wartungsmodus, Sitzungen, Kapazität, Service-Level, availability, high availability, continuity, load balancer, cluster, DFS, database, replicas, static cache, response cache, HTTP cache, cache warming, maintenance mode, sessions, capacity, service level
type: help
---
# Verfügbarkeit für Websites des öffentlichen Sektors

Dieser Ordner richtet sich an IT-Teams, Architektinnen und Architekten sowie Verantwortliche für Dienste in
öffentlichen Stellen, die eine Website erreichbar halten müssen: bei Lastspitzen, während geplanter Wartung und wenn
eine Maschine ausfällt. Er behandelt, was ein einzelner Server leistet, was mehrere Webserver erfordern, die
Datenbankverfügbarkeit, die Caches vor PHP, Wartungsfenster, Sitzungen, Kapazitätsplanung und das Festlegen von
Verfügbarkeitszielen. Die ausführlichen Serverabläufe stehen im Hosting-Bereich für Hoster, insbesondere
[Mehrere Webknoten](../../../hosting/betrieb/mehrere-web-knoten.md) und
[Clustering und gemeinsamer Speicher](../../../hosting/betrieb/clustering-und-gemeinsamer-speicher.md); diese Seiten
ergänzen die Sicht des öffentlichen Sektors: Pflichten zur Aufrechterhaltung des Betriebs, zentrale Netzwerkteams,
Änderungskalender und Entscheidungen, die bei der Organisation liegen.

## Seiten

| Seite | Beantwortet |
|---|---|
| [Überblick über Hochverfügbarkeit](hochverfuegbarkeit-im-ueberblick.md) | Was ein Server leistet, was mehrere erfordern und welche einzelnen Ausfallpunkte bleiben. |
| [Mehrere Webserver hinter einem Load Balancer](mehrere-webserver-hinter-einem-load-balancer.md) | Was gemeinsam genutzt wird, vertrauenswürdige Proxys, Cronjobs auf einem Knoten, Bereitstellungen und Cache-Leerungen auf jedem Knoten. |
| [Gemeinsamer Speicher und der DFS-Cluster](gemeinsamer-speicher-und-der-dfs-cluster.md) | Wie mehrere Server hochgeladene Dateien teilen und was die Speicher- und Datenbankteams bereitstellen. |
| [Hochverfügbarkeit der Datenbank](hochverfuegbarkeit-der-datenbank.md) | Was Exponential für die Datenbankverfügbarkeit leistet und wie Sie auf einen Bereitschaftsserver umschalten. |
| [Lese-Replikate](lese-replikate.md) | Wie MySQL- und MariaDB-Websites Lesezugriffe an Replikate senden und wann das hilft. |
| [Der statische Cache](statischer-cache.md) | Warum er ausgeschaltet ausgeliefert wird und in welchen seltenen Fällen er noch passt. |
| [Velocity-Antwort-Cache und HTTP-Cache](velocity-antwortcache-und-http-cache.md) | Die Caches, die vor dem Rendern antworten, ihre Aktualität und wie Sie sie leeren. |
| [Cache-Aufwärmen nach Änderungen](caches-nach-aenderungen-vorwaermen.md) | Wie Sie die Website nach einer Bereitstellung, einer Wiederherstellung oder vor einer Ankündigung aufwärmen. |
| [Wartungsmodus und geplante Ausfallzeiten](wartungsmodus-und-geplante-ausfallzeiten.md) | Was Besucher und Überwachungssysteme sehen, wer hineinkommt und wie Sie ein Wartungsfenster planen. |
| [Sitzungen über mehrere Server](sitzungen-ueber-mehrere-server.md) | Wie Redakteurinnen und Redakteure über mehrere Server angemeldet bleiben und was ein ausgefallener Knoten bedeutet. |
| [Kapazitätsplanung](kapazitaetsplanung.md) | Die Zahlen, die das Repository liefert, und wie Sie Ihre eigene Website messen. |
| [Verfügbarkeitsziele und Service-Level](verfuegbarkeitsziele-und-service-level.md) | Welche Zusagen Sie festlegen können, wovon sie abhängen und wie Sie sie messen. |

## Lesereihenfolge

1. Entscheiden Sie, was Sie brauchen: [Verfügbarkeitsziele und Service-Level](verfuegbarkeitsziele-und-service-level.md),
   dann [Überblick über Hochverfügbarkeit](hochverfuegbarkeit-im-ueberblick.md).
2. Halten Sie einen einzelnen Server schnell und berechenbar: die [Caches](velocity-antwortcache-und-http-cache.md),
   [Cache-Aufwärmen](caches-nach-aenderungen-vorwaermen.md), [Kapazitätsplanung](kapazitaetsplanung.md) und
   [Wartungsmodus](wartungsmodus-und-geplante-ausfallzeiten.md).
3. Nur wenn die Ziele es verlangen, mehrere Server: [Load Balancer](mehrere-webserver-hinter-einem-load-balancer.md),
   [gemeinsamer Speicher](gemeinsamer-speicher-und-der-dfs-cluster.md), [Sitzungen](sitzungen-ueber-mehrere-server.md),
   [Datenbankverfügbarkeit](hochverfuegbarkeit-der-datenbank.md) und [Lese-Replikate](lese-replikate.md).
4. Lesen Sie [Der statische Cache](statischer-cache.md) nur, wenn jemand danach fragt.

Weiter mit [Datensicherung und Wiederherstellung](../datensicherung-und-wiederherstellung/README.md); Entscheidungen zum Hosting stehen unter [Hosting](../hosting/README.md).

## Verwandte Seiten

- Nach oben: [Betrieb](../README.md)
- Anfang des Bereichs: [Behörden: hier beginnen](../../verwaltungen-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
