---
title: Mehrere Web-Knoten
audience: hosting, admin
category: hosting
order: 40
summary: Was sich ändert, wenn mehr als ein Webserver eine Exponential-Website ausliefert - die Datenbank, gemeinsame Dateien, Sitzungen, die Caches auf jedem Knoten, vertrauenswürdige Proxys, Cron, Ausrollen, Wartungsmodus und Logs, mit einer Checkliste.
keywords: Load Balancer, mehrere Server, Web-Knoten, Skalierung, horizontale Skalierung, Sitzungen, Sticky Sessions, TrustedProxies, X-Forwarded-For, Replikate, UseSlaveServer, Valkey, Redis, Cluster, load balancer, several servers, web nodes, scaling, horizontal scaling, sessions, sticky sessions, replicas, cluster
type: help
---
# Mehrere Web-Knoten

Diese Seite ist für Systemadministratoren und Produktmanager, die ein Angebot planen, bei dem mehrere Webserver („Knoten“) eine
Exponential-Website hinter einem Load Balancer ausliefern. Sie beantwortet: welche Teile einer Website zwischen den Knoten geteilt
werden müssen, welche auf jedem lokal bleiben und was ein Betreiber bei Cron, Ausrollen, Wartung und Logs anders machen muss.

Eine einzelne Maschine mit genug CPU und Speicher ist für die meisten Websites die einfachere Wahl; lesen Sie zuerst
[Speicher, CPU und Festplatte dimensionieren](../grundlagen/speicher-cpu-und-festplatte-dimensionieren.md) und die Seiten zum Caching. Nutzen Sie
mehrere Knoten, wenn eine Maschine nicht reicht oder wenn das Angebot verspricht, dass ein Knoten ausfallen kann, ohne die Website
mitzureißen.

## Inhalt

1. [Was geteilt werden muss, was lokal bleibt](#was-geteilt-werden-muss-was-lokal-bleibt)
2. [Die Datenbank](#die-datenbank)
3. [Dateien](#dateien)
4. [Sitzungen](#sitzungen)
5. [Caches auf jedem Knoten](#caches-auf-jedem-knoten)
6. [Der Load Balancer und vertrauenswürdige Proxys](#der-load-balancer-und-vertrauenswürdige-proxys)
7. [Cron, Ausrollen, Wartung und Logs](#cron-ausrollen-wartung-und-logs)
8. [Checkliste](#checkliste)

## Was geteilt werden muss, was lokal bleibt

| Teil der Website | Geteilt oder lokal | Wie |
|---|---|---|
| Datenbank | geteilt | ein Datenbankserver, den alle Knoten erreichen |
| Hochgeladene Dateien (`var/storage`) | geteilt | der DFS-Cluster-Datei-Handler und ein NFS-Mount |
| Einstellungen, Code, Erweiterungen, Designs | auf jedem Knoten gleich | dasselbe Ausrollen auf jedem Knoten |
| Sitzungen | geteilt, oder der Benutzer bleibt auf einem Knoten | der Session-Handler für die Datenbank oder Sticky Sessions |
| Caches (`var/cache`, View-Cache, HTTP-Cache) | lokal pro Knoten, im Gleichschritt gehalten | der Cluster-Handler oder ein gemeinsames Cache-Backend |
| Logs (`var/log`) | lokal pro Knoten | sammeln Sie sie zentral, wenn Sie eine Gesamtsicht brauchen |
| Cronjobs | ein Knoten | führen Sie die Crontab nur auf einem Knoten aus |

## Die Datenbank

Eine Website mit mehreren Knoten braucht einen Datenbankserver. Von den unterstützten Datenbanken in der offiziellen Reihenfolge
(1. SQLite 3, der Standard · 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL; Microsoft SQL Server ist derzeit
bekanntermaßen mit zusätzlicher Arbeit möglich, ohne Treiber in 6.0.x) unterstützt jede Engine außer SQLite mehrere Webserver an
einer Datenbank ([Datenbankunterstützung, auf einen Blick](../../datenbankunterstuetzung.md#auf-einen-blick)). Der WAL-Modus von SQLite nutzt
gemeinsamen Speicher, alle Prozesse müssen also auf einer Maschine sein; mehrere Webserver, die sich eine Datenbankdatei über NFS
teilen, werden nicht unterstützt ([Kapitel 9](../../../installation/09-datenbanken.md#wal-leser-und-schreiber-nebeneinander)).

Für leselastige Websites auf MySQL oder MariaDB schickt `site.ini [DatabaseSettings] UseSlaveServer` lesende Abfragen an Replikate
(`SlaveServerArray[]` und die zugehörigen Schlüssel); der mitgelieferte Wert ist `disabled`
([Leistung und Skalierung](../../datenbankunterstuetzung.md#performance-und-skalierung)).

## Dateien

Hochgeladene Bilder und Dateien müssen für jeden Knoten sichtbar sein. Der Weg des Kernels ist der DFS-Cluster-Datei-Handler: die
Dateien auf einem gemeinsamen NFS-Export, ihre Metadaten in einer Datenbanktabelle, Cache-Dateien auf jeden Knoten kopiert. Jeder
Knoten behält sein eigenes Verzeichnis `var/`; nur der Mount wird geteilt. Alles darüber steht auf
[Clustering und gemeinsamer Speicher](clustering-und-gemeinsamer-speicher.md).

> **Warnung**
> Legen Sie nicht einfach das ganze Verzeichnis `var/` für alle Knoten auf eine Netzwerkfreigabe. Die Dokumentation zu eZDFS sagt,
> dass die var-Verzeichnisse auf KEINEN Fall zwischen Instanzen geteilt werden sollten; der Cluster-Handler hält sie selbst im
> Gleichschritt.

## Sitzungen

`site.ini [Session] Handler` entscheidet, wo Sitzungen gehalten werden ([Sitzungen](../../../anleitungen/sitzungen.md)):

| Handler | Wo | Mehrere Knoten |
|---|---|---|
| leer (Standard): `ezpSessionHandlerPHP` | der eigene Sitzungsspeicher von PHP, meist Dateien auf dem Knoten | jeder Knoten kennt nur seine eigenen Sitzungen |
| `ezpSessionHandlerDB` | die Tabelle `ezsession` der Datenbank der Website | jeder Knoten sieht jede Sitzung |

Zwei Wege, damit die Anmeldung über Knoten hinweg funktioniert:

- **Sitzungen in der Datenbank.** Setzen Sie `Handler=ezpSessionHandlerDB`; jeder Knoten liest dieselbe Tabelle. Die Seite
  **Setup > Sitzungen** der Administration kann dann auch Sitzungen auflisten und entfernen.
- **Jeden Benutzer auf einem Knoten halten.** Ein Load Balancer, der nach dem Sitzungscookie routet, hält einen Benutzer auf dem
  Knoten, der die Sitzung hat. `[Session] CookieAlwaysAddToHttpResponse=enabled` sendet das Sitzungscookie mit jeder Antwort,
  sobald eine Sitzung läuft, für einen solchen Balancer; diese Antworten gehen mit `Cache-Control: private` hinaus. Ein Knoten,
  der ausfällt, meldet dann seine Benutzer ab.

## Caches auf jedem Knoten

Caches beantworten die meisten Anfragen, also hat jeder Knoten seine eigenen. Was ein Hoster planen muss, ist, dass das Leeren eines
Caches auf einem Knoten die anderen erreicht:

- **Mit dem DFS-Cluster** sind Cache-Dateien und `expiry.php` (die Zeitstempel, die Caches ungültig machen) Cluster-Dateien, sodass
  ein Leeren auf einem Knoten von allen gesehen wird ([Cluster (DFS)](../../../../eng-us/features/6.0/multi-site-log-and-cache-paths.md#cluster-dfs)).
- **Die Caches vor PHP** sind lokal: der HTTP-Cache in `var/<site>/cache/exphttpcache` und der Antwort-Cache von Velocity. Leeren
  Sie sie nach einer Änderung auf jedem Knoten (`./console exp:velocity cache clear` für Velocity;
  [Caches](../../../anleitungen/caches.md)).
- **Ein gemeinsames Cache-Backend.** Die Erweiterung `sevenx_valkey_cache` hält Template-Blöcke, den Content-View-Cache und den
  kompilierten INI-Cache in Redis oder Valkey, was INI-Caches für Cluster sicher macht
  ([sevenx_valkey_cache](../../../../eng-us/features/6.0/extensions/sevenx_valkey_cache.md)). Siehe
  [Valkey und Redis](../leistung/valkey-und-redis.md).

Ein Reverse Proxy oder CDN vor allen Knoten ist eine weitere, gemeinsame Cache-Schicht; siehe
[Varnish und Reverse Proxys](../leistung/varnish-und-reverse-proxys.md).

## Der Load Balancer und vertrauenswürdige Proxys

Hinter einem Load Balancer kommt jede Anfrage von der Adresse des Balancers. Exponential glaubt weitergeleiteten Headern nur von
den Proxys, denen es vertrauen soll ([8.8 Hinter einem Reverse Proxy](../../../installation/08-die-website-ausliefern.md#88-hinter-einem-reverse-proxy),
[Vertrauenswürdige Proxys](../../../../eng-us/bc/6.0/trusted-proxies.md)):

```ini
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
# replace the shipped list (127.0.0.1 and ::1) with the balancer's addresses
TrustedProxies[]
TrustedProxies[]=192.0.2.10
# read the visitor's address from X-Forwarded-For, from a trusted proxy only
ClientIpByCustomHTTPHeader=X-Forwarded-For
```

- Der Balancer muss `X-Forwarded-Proto: https` setzen, wenn er TLS beendet, damit erzeugte Links und das Merkmal `Secure` des
  Sitzungscookies stimmen.
- Er sollte den ursprünglichen `Host` (oder `X-Forwarded-Host`) weitergeben.
- Velocity hat eine eigene Liste vertrauenswürdiger Proxys, `Q.webserver.proxy.trusted` in der Konfiguration der Engine, weil es
  entscheidet, bevor der Kernel läuft; setzen Sie sie ebenfalls, wenn Velocity die Knoten ausliefert.

Die Adresse `192.0.2.10` oben ist eine Adresse für Dokumentation; verwenden Sie die Ihres Balancers.

## Cron, Ausrollen, Wartung und Logs

- **Cron von einem Knoten.** Richten Sie die Crontab nur auf einem Knoten ein, sonst führen mehrere Knoten dieselben Jobs aus. Jedes
  Skript nimmt eine Sperre (`eZMutex`), aber diese Sperre ist eine gesperrte Datei im eigenen Cache-Verzeichnis des Knotens und hält
  daher keinen anderen Knoten auf. Um Teile für alle Knoten anzuhalten, richten Sie `cronjob.ini [PauseSettings] Directory` auf ein
  gemeinsames Verzeichnis ([Cronjob-Teile anhalten](../../../../eng-us/features/6.0/cronjob-pause.md)).
- **Ausrollen auf jedem Knoten.** Führen Sie das Ausrollen (`./console exp:velocity deploy`, siehe
  [Änderungen ausrollen](aenderungen-ausrollen.md)) auf jedem Knoten aus, damit alle den neuen Code ausführen und ihre Caches neu
  aufbauen. Nehmen Sie einen Knoten aus dem Balancer, während auf ihn ausgerollt wird, wenn die Änderung nicht halb ausgeliefert
  werden darf.
- **Wartung auf jedem Knoten.** Der Wartungszustand ist die Datei `var/maintenance.json` einer Installation. Mit einem Verzeichnis
  `var/` pro Knoten schalten Sie sie auf jedem Knoten ein und aus ([Wartungsmodus](wartungsmodus.md)).
- **Logs pro Knoten.** Jeder Knoten schreibt sein eigenes `var/log`. Die Fehlerreferenz eines Besuchers steht vielleicht im Log
  eines anderen Knotens als dem, auf den Sie schauen
  ([Einen Fehler über seine Referenz finden](../../../anleitungen/fehler-anhand-der-referenz-finden.md)). Sammeln Sie Logs zentral,
  oder leiten Sie das Prüfprotokoll mit einer Senke weiter (siehe [Sicherheitslog](sicherheitslog.md)).
- **Überwachung pro Knoten.** Prüfen Sie jeden Knoten direkt und auch die ausbalancierte Adresse
  ([Zustandsprüfungen und Verfügbarkeit](zustandspruefungen-und-verfuegbarkeit.md)).

## Checkliste

- [ ] Die Datenbank der Website ist ein Server (nicht SQLite), den jeder Knoten erreicht.
- [ ] Hochgeladene Dateien werden über den DFS-Cluster geteilt; jeder Knoten hat sein eigenes `var/`.
- [ ] Sitzungen liegen in der Datenbank, oder der Balancer hält jeden Benutzer auf einem Knoten.
- [ ] `TrustedProxies[]` nennt den Balancer, und `ClientIpByCustomHTTPHeader` ist gesetzt, wenn die Adresse des Besuchers zählt.
- [ ] Die Crontab läuft auf einem Knoten; das Verzeichnis für Pausen ist geteilt.
- [ ] Runbooks für Ausrollen, Leeren der Caches und Wartung nennen jeden Knoten.
- [ ] Logs werden gesammelt, oder das Support-Team weiß, wie es jeden Knoten durchsucht.

## Verwandte Seiten

- [Clustering und gemeinsamer Speicher](clustering-und-gemeinsamer-speicher.md)
- [Ein Hosting-Modell wählen](../modelle/ein-hosting-modell-waehlen.md), [Kubernetes](../modelle/kubernetes.md)
- [Velocity neben Apache oder nginx](../server/velocity-neben-apache-oder-nginx.md)
- [Cache-Schichten](../leistung/cache-schichten.md)
- [Datenbankunterstützung](../../datenbankunterstuetzung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
