---
title: Überblick über Hochverfügbarkeit
audience: government, hosting, admin, project-manager
category: public-sector
order: 20
summary: Für öffentliche Stellen mit Pflichten zur Aufrechterhaltung des Betriebs, deren Exponential-Website den Ausfall einer Maschine überstehen muss. Was ein einzelner Server leisten kann und was nicht, was mehrere Webserver erfordern (Datenbankserver, gemeinsamer Speicher, Sitzungen, ein Cronjob) und wie Sie entscheiden, wie viel Verfügbarkeit Sie brauchen.
keywords: Hochverfügbarkeit, HA, Kontinuität, Redundanz, einzelner Ausfallpunkt, mehrere Webserver, Load Balancer, Cluster, DFS, Datenbankserver, Ausfallumschaltung, Betriebskontinuität, Verfügbarkeit öffentlicher Dienste, high availability, continuity, redundancy, single point of failure, several web servers, cluster, database server, failover, business continuity, public service availability
type: help
---
# Überblick über Hochverfügbarkeit

Diese Seite richtet sich an IT-Architektinnen und -Architekten sowie Kontinuitätsplanende in öffentlichen Stellen,
deren Websites erreichbar bleiben müssen, wenn eine Maschine ausfällt, zum Beispiel weil sie Notfallinformationen
oder gesetzlich vorgeschriebene Veröffentlichungen tragen. Sie beantwortet, was ein einzelner Exponential-Server
leistet, was vorhanden sein muss, bevor mehrere Server eine Website ausliefern können, und wie Sie entscheiden, wie
weit Sie gehen. Die Einzelheiten stehen auf den Hosting-Seiten
[Mehrere Webknoten](../../../hosting/betrieb/mehrere-web-knoten.md) und
[Clustering und gemeinsamer Speicher](../../../hosting/betrieb/clustering-und-gemeinsamer-speicher.md).

## Inhalt

1. [Beginnen Sie mit der Pflicht, nicht mit der Technik](#beginnen-sie-mit-der-pflicht-nicht-mit-der-technik)
2. [Was ein Server leistet](#was-ein-server-leistet)
3. [Was mehrere Webserver erfordern](#was-mehrere-webserver-erfordern)
4. [Die einzelnen Ausfallpunkte, die bleiben](#die-einzelnen-ausfallpunkte-die-bleiben)
5. [Ein schrittweiser Weg](#ein-schrittweiser-weg)
6. [Checkliste](#checkliste)

## Beginnen Sie mit der Pflicht, nicht mit der Technik

Fragen Sie zuerst, was die Organisation leisten muss: wie lange die Website nicht verfügbar sein darf, zu welchen
Zeiten, und wie viel aktueller Inhalt nach einem Ausfall verloren gehen darf. Das sind organisatorische
Entscheidungen, beschrieben unter [Verfügbarkeitsziele und Service-Level](verfuegbarkeitsziele-und-service-level.md)
und [RPO und RTO als Entscheidungen](../datensicherung-und-wiederherstellung/rpo-und-rto-als-entscheidungen.md). Das Projekt veröffentlicht
kein Service-Level und keine Verfügbarkeitszahl für die Software selbst; die Zahlen, die Sie zusagen, stammen aus
Ihrer eigenen Architektur und Ihren eigenen Tests.

## Was ein Server leistet

„Eine einzelne Maschine mit genügend CPU und Arbeitsspeicher ist für die meisten Websites die einfachere Wahl“
([Mehrere Webknoten](../../../hosting/betrieb/mehrere-web-knoten.md)). Auf einer Maschine bietet Exponential:

| Funktion | Hilft bei | Seite |
|---|---|---|
| Der Watchdog von Velocity | startet die Engine nach einem Absturz oder einem Abbruch wegen Speichermangels neu | [Velocity für Teams im öffentlichen Sektor](../hosting/velocity-fuer-teams-im-oeffentlichen-sektor.md) |
| Antwort-Cache und HTTP-Cache | anonyme Seiten werden auch bei Lastspitzen beantwortet | [Velocity-Antwort-Cache und HTTP-Cache](velocity-antwortcache-und-http-cache.md) |
| Wartungsmodus | eine klare 503-Seite mit `Retry-After` während geplanter Arbeiten | [Wartungsmodus und geplante Ausfallzeiten](wartungsmodus-und-geplante-ausfallzeiten.md) |
| Schnelle Wiederherstellung | eine getestete Datensicherung bringt die Website auf einer anderen Maschine zurück | [Wiederherstellung testen](../datensicherung-und-wiederherstellung/wiederherstellungstests.md) |

Was ein Server nicht leistet: das Überstehen eines Ausfalls von Hardware, Festplatte, Rechenzentrum oder Netzwerk.
Eine kalte Reservemaschine mit getesteten Wiederherstellungen reicht oft für eine öffentliche Stelle, deren Pflicht
einige Stunden Ausfallzeit zulässt.

## Was mehrere Webserver erfordern

Aus [Mehrere Webknoten](../../../hosting/betrieb/mehrere-web-knoten.md):

| Teil | Anforderung | Seite |
|---|---|---|
| Datenbank | ein Datenbankserver, den jeder Knoten erreicht; nicht SQLite | [Hochverfügbarkeit der Datenbank](hochverfuegbarkeit-der-datenbank.md) |
| Hochgeladene Dateien | gemeinsam genutzt über den DFS-Cluster-Dateihandler und einen NFS-Mount | [Gemeinsamer Speicher und der DFS-Cluster](gemeinsamer-speicher-und-der-dfs-cluster.md) |
| Sitzungen | in der Datenbank, oder der Load Balancer hält jeden Benutzer auf einem Knoten | [Sitzungen über mehrere Server](sitzungen-ueber-mehrere-server.md) |
| Load Balancer | wird von Exponential für die Adresse des Besuchers und HTTPS vertraut | [Mehrere Webserver hinter einem Load Balancer](mehrere-webserver-hinter-einem-load-balancer.md) |
| Cronjobs | nur auf einem Knoten | [Mehrere Webserver hinter einem Load Balancer](mehrere-webserver-hinter-einem-load-balancer.md) |
| Bereitstellungen, Cache-Leerungen, Wartung | auf jedem Knoten | dieselbe Seite |

## Die einzelnen Ausfallpunkte, die bleiben

Mehrere Webserver beseitigen den Webserver als einzelnen Ausfallpunkt, aber nicht alles:

- **Der Datenbankserver.** Jeder Knoten hängt von ihm ab. Um ihn zu beseitigen, ist seine eigene Replikation oder
  Clusterbildung nötig; siehe [Hochverfügbarkeit der Datenbank](hochverfuegbarkeit-der-datenbank.md).
- **Der gemeinsame Dateispeicher.** „Fällt der NFS-Mount weg, kann kein Server mehr Dateien lesen“
  ([Clustering und gemeinsamer Speicher](../../../hosting/betrieb/clustering-und-gemeinsamer-speicher.md)).
- **Die Metadatendatenbank des Clusters**, die auf dem Weg jedes Dateizugriffs liegt.
- **Der Load Balancer**, sofern er nicht selbst redundant ist.
- **Das Rechenzentrum**, sofern Knoten und Daten nicht in mehr als einem liegen.

Tragen Sie diese Punkte in Ihren Kontinuitätsplan ein, jeweils mit einer verantwortlichen Person und einem
Wiederherstellungsverfahren.

## Ein schrittweiser Weg

| Schritt | Was Sie hinzufügen | Was Sie gewinnen |
|---|---|---|
| 1 | ein Server, getestete Datensicherungen außer Haus, eine dokumentierte Wiederherstellung | Wiederherstellung in der Zeit, die Ihr Wiederherstellungstest gemessen hat |
| 2 | Watchdog von Velocity und externe Überwachung | automatischer Neustart und schnelle Alarmierung |
| 3 | eine für Wiederherstellungen bereitgehaltene Reservemaschine | kürzere Wiederherstellung |
| 4 | ein Datenbankserver mit eigener Replikation | die Datenbank übersteht den Ausfall einer Maschine |
| 5 | mehrere Webknoten, gemeinsamer Speicher, ein Load Balancer | die Webebene übersteht den Ausfall eines Knotens |

Jeder Schritt ist eine eigene Änderung mit einem Test. Der Umstieg von SQLite auf einen Datenbankserver ist der
dokumentierte Weg, wenn ein zweiter Webserver nötig ist
([Die Datenbank-Engine wechseln](../../../hosting/migrationen/die-datenbank-engine-einer-kunden-website-wechseln.md)).

## Checkliste

- [ ] Verfügbarkeits- und Wiederherstellungsziele sind von der Organisation entschieden und schriftlich festgehalten.
- [ ] Die Architektur ist so gewählt, dass sie diese erfüllt, und ihre verbleibenden einzelnen Ausfallpunkte sind
  aufgelistet.
- [ ] Eine Wiederherstellung wurde getestet und ihre Dauer festgehalten.
- [ ] Bei mehreren Knoten: Datenbankserver, gemeinsamer Speicher, Sitzungen, ein Cronjob und vertrauenswürdige Proxys
  sind vorhanden.
- [ ] Der Kontinuitätsplan nennt für jeden verbleibenden einzelnen Ausfallpunkt eine verantwortliche Person.

## Verwandte Seiten

- [Verfügbarkeit](README.md): Übersicht dieses Ordners
- [Notfallwiederherstellungsplan](../datensicherung-und-wiederherstellung/notfallwiederherstellungsplan.md)
- [SLA-Optionen](../../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md) für die Sicht eines Dienstleisters auf Verfügbarkeitszusagen
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
