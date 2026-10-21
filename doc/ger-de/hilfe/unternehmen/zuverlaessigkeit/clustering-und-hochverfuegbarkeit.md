---
title: Clustering und Hochverfügbarkeit
audience: businesses, project-manager, hosting, admin
category: business-solutions
order: 140
summary: Wann ein Unternehmen für eine Exponential-Website mehr als einen Webserver braucht, was ein Aufbau mit mehreren Servern voraussetzt (einen Datenbankserver, gemeinsamen Speicher, Sitzungen, einen Load Balancer), was er im Betrieb kostet und welche einfacheren Optionen Sie zuerst ausprobieren sollten.
keywords: Cluster, Clustering, Hochverfügbarkeit, Load Balancer, mehrere Web-Knoten, gemeinsamer Speicher, Sitzungen, Ausfallsicherheit, Redundanz, Velocity-Instanzen, cluster, clustering, high availability, load balancer, several web nodes, DFS, NFS, shared storage, sessions, failover, redundancy, Velocity instances
type: help
---
# Clustering und Hochverfügbarkeit

Diese Seite richtet sich an Inhaber und Führungskräfte, die sich fragen, ob ihre Website mehrere Server braucht, und an
die Projektleitung, die die Kosten erklären muss. Sie beschreibt, was „Hochverfügbarkeit“ in der Praxis für eine
Exponential-Website bedeutet, was ein Aufbau mit mehreren Servern braucht, was er dem Betrieb abverlangt und was Sie
zuerst ausprobieren sollten. Die technischen Seiten sind [Mehrere Web-Knoten](../../hosting/betrieb/mehrere-web-knoten.md)
und [Clustering und gemeinsamer Speicher](../../hosting/betrieb/clustering-und-gemeinsamer-speicher.md).

## Inhalt

1. [Zwei Gründe für mehr Server](#zwei-gründe-für-mehr-server)
2. [Probieren Sie zuerst Folgendes](#probieren-sie-zuerst-folgendes)
3. [Was mehrere Webserver brauchen](#was-mehrere-webserver-brauchen)
4. [Was es dem Betrieb abverlangt](#was-es-dem-betrieb-abverlangt)
5. [Die Teile, die trotzdem ausfallen können](#die-teile-die-trotzdem-ausfallen-können)
6. [Fragen an Ihren Hosting-Partner](#fragen-an-ihren-hosting-partner)
7. [Checkliste für die Entscheidung](#checkliste-für-die-entscheidung)

## Zwei Gründe für mehr Server

Die Hosting-Dokumentation nennt zwei Gründe: „Setzen Sie mehrere Knoten ein, wenn ein Rechner nicht ausreicht oder wenn
das Angebot verspricht, dass ein Knoten ausfallen kann, ohne die Website lahmzulegen“
([Mehrere Web-Knoten](../../hosting/betrieb/mehrere-web-knoten.md)).

| Grund | Zu stellende Frage |
|---|---|
| Kapazität | Haben wir gemessen, dass ein Rechner mit eingeschalteten Caches unsere Spitzenlast nicht tragen kann? |
| Redundanz | Sind die Kosten eines Ausfalls, während ein Server repariert wird, höher als die Kosten für Betrieb und Wartung mehrerer Server? |

Lautet die Antwort auf beide Fragen „nein“ oder „wissen wir nicht“, ist ein Aufbau mit mehreren Servern wahrscheinlich
verfrüht. Beantworten Sie die zweite Frage mit dem [Arbeitsblatt: Kosten eines Ausfalls](../kosten/arbeitsblatt-kosten-eines-ausfalls.md)
und Ihren eigenen Zahlen.

## Probieren Sie zuerst Folgendes

Dieselbe Seite rät: „Ein einzelner Rechner mit genügend CPU und Arbeitsspeicher ist für die meisten Websites die
einfachere Wahl.“ Bevor Sie Server hinzufügen:

1. **Caches eingeschaltet und vorgewärmt** ([Caching für Verfügbarkeit](caching-fuer-verfuegbarkeit.md)).
2. **Messen** Sie mit `exp:benchmark`, um zu wissen, wo die Zeit verloren geht
   ([Lastspitzen planen](../../../../eng-us/help/businesses/reliability/peak-traffic-planning.md#measure-before-you-guess)).
3. **Velocity** erhöht dort, wo das Hosting es erlaubt, was ein Rechner ausliefern kann
   ([Velocity für Unternehmen](../../../../eng-us/help/businesses/reliability/velocity-for-businesses.md)). Mehrere Velocity-Instanzen können sich auf einem Rechner
   einen Port teilen; `graceful` lädt sie nacheinander neu, „damit der Port weiter antwortet“
   ([Velocity-Engines](../../../../eng-us/bc/6.0/velocity-engines.md#several-qbix-servers-on-one-port-instances)).
4. **Ein größerer Rechner**: siehe
   [Speicher, CPU und Festplatte dimensionieren](../../hosting/grundlagen/speicher-cpu-und-festplatte-dimensionieren.md).
5. **Eine schnelle Wiederherstellung**: Für viele kleine Unternehmen ist eine getestete Wiederherstellung auf einen
   Bereitschaftsserver eine günstigere Form der Verfügbarkeit als ein Cluster
   ([Wiederherstellungsübungen](../../../../eng-us/help/businesses/reliability/restore-drills.md)).

> **Hinweis**
> „Ein einzelner Server braucht keinen Cluster-File-Handler, ganz gleich, wie groß er ist. Clustering ist für eine
> Website, die mehrere Rechner gleichzeitig ausliefern. Es bringt einen Mount und eine Datenbank mit sich, die
> betrieben, gesichert und überwacht werden müssen“
> ([Clustering und gemeinsamer Speicher](../../hosting/betrieb/clustering-und-gemeinsamer-speicher.md#zwei-datei-handler)).

## Was mehrere Webserver brauchen

Aus [Mehrere Web-Knoten, was geteilt werden muss](../../hosting/betrieb/mehrere-web-knoten.md#was-geteilt-werden-muss-was-lokal-bleibt):

| Teil der Website | Über die Server hinweg |
|---|---|
| Datenbank | Geteilt: ein Datenbankserver, den alle Server erreichen. SQLite ist nur für einen Rechner gedacht |
| Hochgeladene Dateien (`var/storage`) | Geteilt, über den DFS-Cluster-File-Handler und einen NFS-Mount |
| Code, Einstellungen, Erweiterungen, Designs | Auf jedem Server identisch, auf dieselbe Weise ausgerollt |
| Sitzungen | Geteilt über den Datenbank-Session-Handler, oder jeder Besucher bleibt auf einem Server |
| Caches | Lokal je Server, im Gleichschritt gehalten |
| Logs | Lokal je Server; für eine Gesamtansicht zentral sammeln |
| Cronjobs | Nur auf einem Server |

Jede unterstützte Datenbank außer SQLite unterstützt mehrere Webserver an einer Datenbank
([Mehrere Web-Knoten, die Datenbank](../../hosting/betrieb/mehrere-web-knoten.md#die-datenbank)). Unter MySQL und
MariaDB lassen sich Leseabfragen an Replikate schicken (`UseSlaveServer`, ab Werk `disabled`). Ein vorgeschalteter Load
Balancer braucht eine Zustandsprüfung, die einen Server herausnimmt, wenn er keine Seite rendern kann
([Zustandsprüfungen und Verfügbarkeit, Zustandsprüfungen für Load Balancer](../../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md#zustandsprüfungen-des-load-balancers)).

## Was es dem Betrieb abverlangt

Jede Routineaufgabe findet nun auf mehreren Rechnern statt
([Mehrere Web-Knoten, Cron, Deployments, Wartung und Logs](../../hosting/betrieb/mehrere-web-knoten.md#cron-ausrollen-wartung-und-logs)):

- **Deployments auf jedem Server**, damit alle den neuen Code ausführen; nehmen Sie bei Bedarf einen Server aus dem
  Load Balancer, während er aktualisiert wird.
- **Wartungsmodus auf jedem Server**: Der Zustand ist eine Datei im Verzeichnis `var/` jedes Servers.
- **Cron nur auf einem Server**, sonst laufen dieselben Jobs doppelt.
- **Logs je Server**: Die Fehlerreferenz eines Kunden kann im Log eines anderen Servers stehen.
- **Monitoring je Server** sowie der gemeinsamen Adresse.
- **Mehr zu sichern**: der Datenbankserver, der gemeinsame Speicher und die Metadaten des Clusters.

Planen Sie dafür Personalzeit ein, nicht nur Hardware; siehe [Kostentreiber beim Hosting](../kosten/kostentreiber-beim-hosting.md).

## Die Teile, die trotzdem ausfallen können

Mehrere Webserver schützen vor dem Verlust eines Webservers. Von sich aus schützen sie nicht davor, dass

- der **Datenbankserver** ausfällt, es sei denn, die Datenbank hat eigene Replikation und Ausfallsicherung;
- der **gemeinsame Speicher** (der NFS-Export) ausfällt;
- der **Load Balancer** selbst ausfällt;
- ein **fehlerhaftes Deployment** auf alle Server gleichzeitig gelangt;
- ein **inhaltlicher Fehler** passiert, der sofort überallhin repliziert wird.

Jeder dieser Punkte braucht eine eigene Antwort, von Ihrem Hosting-Partner und aus Ihren eigenen Gewohnheiten
([Unterbrechungsfreie Deployments](../../../../eng-us/help/businesses/reliability/graceful-deploys.md), [Backups, die sich wiederherstellen lassen](backups-die-sich-wiederherstellen-lassen.md)).

## Fragen an Ihren Hosting-Partner

- [ ] Welche Teile sind redundant: Webserver, Datenbank, Speicher, Load Balancer, Netzwerk?
- [ ] Wie laufen Deployments und Wartungsfenster auf jedem Server ab?
- [ ] Welcher Server führt die Cronjobs aus, und was passiert, wenn er ausfällt?
- [ ] Wie werden Logs gesammelt, damit sich eine Fehlerreferenz auf jedem Server finden lässt?
- [ ] Wie werden Datenbank und gemeinsamer Speicher zusammen gesichert?
- [ ] Was wurde getestet: Wurde ein Server absichtlich herausgenommen, um zu sehen, dass die Website weiterläuft?

## Checkliste für die Entscheidung

- [ ] Wir haben gemessen, dass ein Rechner mit eingeschalteten Caches unsere Spitzenlast nicht tragen kann, **oder**
      unsere Ausfallkosten rechtfertigen Redundanz.
- [ ] Wir haben zuerst die einfacheren Optionen ausprobiert.
- [ ] Unsere Datenbank unterstützt mehrere Webserver.
- [ ] Unser Team oder Partner kann Deployments, Wartung, Cron und Logs über mehrere Server hinweg betreiben.
- [ ] Die zusätzlichen Betriebskosten sind im Budget.

## Verwandte Seiten

- [Mehrere Web-Knoten](../../hosting/betrieb/mehrere-web-knoten.md)
- [Datenbankwahl für Zuverlässigkeit](../../../../eng-us/help/businesses/reliability/database-choice-for-reliability.md)
- [Grundlagen zu Verträgen und SLAs](../auswahl/grundlagen-zu-vertraegen-und-slas.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
