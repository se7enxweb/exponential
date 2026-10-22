---
title: Skalierung und Clustering
audience: software-engineer, admin
category: software-engineering
order: 350
summary: Wie eine Exponential-Website skaliert - nach oben auf einem Rechner mit Velocity und Caches, und nach außen auf mehrere Webknoten - mit dem, was geteilt werden muss (Datenbank, Dateien, Sessions), was je Knoten bleibt (Caches, Logs, Worker), dem DFS-Cluster-Datei-Handler, Lese-Repliken, vertrauenswürdigen Proxys, Cron auf einem Knoten, Bereitstellungen über Knoten hinweg und den Grenzen jedes Ansatzes.
keywords: Skalierung, horizontale Skalierung, vertikale Skalierung, Cluster, mehrere Webknoten, Load Balancer, eZDFSFileHandler, NFS, Sessions, Lese-Repliken, UseSlaveServer, Valkey, Redis, vertrauenswürdige Proxys, Cron, Softwareingenieur, scaling, horizontal scaling, vertical scaling, several web nodes, load balancer, read replicas, trusted proxies
type: help
---
# Skalierung und Clustering

Diese Seite richtet sich an Ingenieure, die Kapazität oder eine hochverfügbare Einrichtung planen. Die Anleitung für
Betreiber mit einer Checkliste ist [Mehrere Webknoten](../hosting/betrieb/mehrere-web-knoten.md); der
Cluster-Datei-Handler ist unter [Clustering und gemeinsamer Speicher](../hosting/betrieb/clustering-und-gemeinsamer-speicher.md)
beschrieben.

## Inhalt

1. [Zuerst nach oben skalieren](#zuerst-nach-oben-skalieren)
2. [Nach außen skalieren: was geteilt werden muss](#nach-außen-skalieren-was-geteilt-werden-muss)
3. [Dateien: der Cluster-Datei-Handler](#dateien-der-cluster-datei-handler)
4. [Sessions](#sessions)
5. [Datenbankskalierung](#datenbankskalierung)
6. [Der Rand: Load Balancer und Proxys](#der-rand-load-balancer-und-proxys)
7. [Betrieb über Knoten hinweg](#betrieb-über-knoten-hinweg)
8. [Grenzen](#grenzen)

## Zuerst nach oben skalieren

Ein einzelner Rechner mit genug CPU und Speicher ist für die meisten Websites die einfachere Wahl. Bevor Sie Knoten
hinzufügen:

1. Liefern Sie mit Velocity und seinem Antwort-Cache für anonymen Verkehr aus ([Anwendungsserver](anwendungsserver.md)).
2. Schalten Sie die Caches ein, die zum Verkehr passen: den rollenbewussten HTTP-Cache für angemeldete Seiten, den
   SQL-Abfrage-Cache für das, was sich nicht als Ganzes speichern lässt
   ([Caching-Ebenen für Ingenieure](caching-ebenen-fuer-ingenieure.md)).
3. Bemessen Sie den Worker-Pool nach PSS, nicht nach RSS ([Persistente Worker von Velocity](persistente-worker-von-velocity.md)).
4. Messen Sie ([Leistungsmessung und Benchmarks](leistungsmessung-und-benchmarks.md)).

Skalieren Sie nach außen, wenn ein Rechner nicht genügt oder wenn ein Knoten ausfallen können muss, ohne die Website
mitzureißen.

## Nach außen skalieren: was geteilt werden muss

| Teil | Geteilt oder lokal | Wie |
|---|---|---|
| Datenbank | geteilt | ein Datenbankserver; jede unterstützte Engine außer SQLite |
| Hochgeladene Dateien (`var/storage`) | geteilt | der DFS-Cluster-Datei-Handler und ein NFS-Mount |
| Code, Einstellungen, Erweiterungen, Designs | auf jedem Knoten gleich | dieselbe Bereitstellung auf jedem Knoten |
| Sessions | geteilt, oder der Benutzer bleibt auf einem Knoten | der Datenbank-Session-Handler oder Sticky Sessions |
| Caches | lokal je Knoten, abgeglichen | der Cluster-Handler oder Redis bzw. Valkey |
| Logs | lokal je Knoten | zentral sammeln, wenn Sie eine Gesamtsicht brauchen |
| Cronjobs | ein Knoten | die Crontab nur auf einem Knoten ausführen |

Legen Sie nicht das ganze Verzeichnis `var/` für alle Knoten auf eine Netzwerkfreigabe; der Cluster-Handler hält die
Knoten selbst im Gleichschritt.

## Dateien: der Cluster-Datei-Handler

Jede Cache- und Speicherdatei wird über `eZClusterFileHandler` geschrieben. Der mitgelieferte Handler ist
`eZFSFileHandler` (lokale Dateien); für mehrere Knoten ist es `eZDFSFileHandler`
(`kernel/private/classes/clusterfilehandlers/`): Dateien auf einem gemeinsamen NFS-Export, ihre Metadaten in einer
Datenbanktabelle, Cache-Dateien auf jeden Knoten kopiert. `bin/php/clusterize.php` verschiebt die Dateien einer
Installation in den Cluster. Code, der Dateien direkt statt über den Handler schreibt, bricht das Clustering; Reviews
sollten das finden ([Checkliste für Code-Reviews](checkliste-fuer-code-reviews.md)).

## Sessions

`site.ini [Session] Handler` entscheidet, wo Sessions liegen: leer (der Standard, `ezpSessionHandlerPHP`) hält sie im
Speicher von PHP auf jedem Knoten; `ezpSessionHandlerDB` hält sie in der Tabelle `ezsession`, für jeden Knoten sichtbar
([Sessions](../../anleitungen/sitzungen.md)).

## Datenbankskalierung

- SQLite kann nicht mehrere Rechner bedienen: Alle Prozesse müssen auf einem Rechner sein.
- Unter MySQL oder MariaDB schickt `site.ini [DatabaseSettings] UseSlaveServer` Leseabfragen an Repliken (mitgeliefert
  `disabled`) ([Datenbankunterstützung, Leistung und Skalierung](../datenbankunterstuetzung.md#performance-und-skalierung)).
- PostgreSQL bringt eigene Replikation und Wiederherstellung auf einen Zeitpunkt mit.

## Der Rand: Load Balancer und Proxys

Hinter einem Load Balancer sieht der Kernel die Adresse des Balancers. Tragen Sie sie in `site.ini [HTTPHeaderSettings]
TrustedProxies[]` ein, sonst werden weitergeleitete Header (`X-Forwarded-For`, `X-Forwarded-Proto`, `X-Forwarded-Host`)
ignoriert, und die HTTPS-Erkennung, Client-Adressen, die Anmeldesperre und das Audit-Log sind falsch
([Vertrauenswürdige Proxys](../../bc/6.0/vertrauenswuerdige-proxys.md)). Exponential sendet keine Purges an ein CDN oder einen
Caching-Proxy; einem, der Seiten speichert, muss gesagt werden, was er wie lange speichern soll.

## Betrieb über Knoten hinweg

| Aufgabe | Über Knoten hinweg |
|---|---|
| Bereitstellen | `exp:velocity deploy` deckt PHP-FPM und Velocity auf einem Rechner ab; stellen Sie jeden Knoten nacheinander bereit |
| Cache leeren | auf jedem Knoten leeren oder ein gemeinsames Cache-Backend verwenden |
| Wartungsmodus | der Schalter ist eine Datei in `var/`; setzen Sie ihn auf jedem Knoten |
| Logs | einsammeln oder je Knoten im Log-Viewer lesen |

## Grenzen

- Kein eingebauter Purge für externe Proxys.
- Kein Migrations-Runner; eine Schemaänderung wird einmal angewendet, von einem Knoten aus.
- Caches je Knoten können nach einer Änderung kurz uneins sein; planen Sie die Reihenfolge einer Bereitstellung.

## Verwandte Seiten

- [Anwendungen skalieren](../ersteller/anwendungsentwickler/anwendungen-skalieren.md)
- [Clustering und gemeinsamer Speicher, für Anwendungsentwickler](../ersteller/anwendungsentwickler/clustering-und-gemeinsamer-speicher.md)
- [Installationen mit mehreren Websites](installationen-mit-mehreren-websites.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
