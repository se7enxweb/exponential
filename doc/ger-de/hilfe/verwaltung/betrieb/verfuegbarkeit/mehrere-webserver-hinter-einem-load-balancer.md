---
title: Mehrere Webserver hinter einem Load Balancer
audience: government, hosting, admin
category: public-sector
order: 30
summary: Für IT-Teams in öffentlichen Stellen, die eine Exponential-Website auf mehreren Webservern hinter einem zentralen Load Balancer oder Reverse Proxy betreiben. Was gemeinsam genutzt wird und was lokal bleibt, vertrauenswürdige Proxys, Cronjobs auf einem Knoten sowie Bereitstellungen, Cache-Leerungen und Wartung auf jedem Knoten.
keywords: Load Balancer, Reverse Proxy, mehrere Webserver, Webknoten, TrustedProxies, X-Forwarded-For, X-Forwarded-Proto, ClientIpByCustomHTTPHeader, Cronjobs auf einem Knoten, Bereitstellung auf jedem Knoten, zentraler Proxy, Behördennetz, load balancer, reverse proxy, several web servers, web nodes, cron on one node, deploy every node, central proxy, government network
type: help
---
# Mehrere Webserver hinter einem Load Balancer

Diese Seite richtet sich an Infrastrukturteams in öffentlichen Stellen, die eine Exponential-Website auf mehr als
einem Webserver betreiben, oft hinter einem Load Balancer, den ein zentrales Netzwerkteam betreibt. Sie beantwortet,
was zwischen den Servern gemeinsam genutzt werden muss, was lokal bleibt, wie Exponential dem Load Balancer vertraut
und was sich für Cronjobs, Bereitstellungen, Cache-Leerungen und Wartung ändert. Die vollständige Hosting-Seite ist
[Mehrere Webknoten](../../../hosting/betrieb/mehrere-web-knoten.md).

## Inhalt

1. [Gemeinsam und lokal](#gemeinsam-und-lokal)
2. [Dem Load Balancer vertrauen](#dem-load-balancer-vertrauen)
3. [Cronjobs auf einem Knoten](#cronjobs-auf-einem-knoten)
4. [Bereitstellungen und Cache-Leerungen auf jedem Knoten](#bereitstellungen-und-cache-leerungen-auf-jedem-knoten)
5. [Wartung und Logs](#wartung-und-logs)
6. [Zusammenarbeit mit einem zentralen Netzwerkteam](#zusammenarbeit-mit-einem-zentralen-netzwerkteam)
7. [Checkliste](#checkliste)

## Gemeinsam und lokal

| Teil der Website | Gemeinsam oder lokal | Wie |
|---|---|---|
| Datenbank | gemeinsam | ein Datenbankserver, den alle Knoten erreichen ([Hochverfügbarkeit der Datenbank](hochverfuegbarkeit-der-datenbank.md)) |
| Hochgeladene Dateien | gemeinsam | der DFS-Cluster und ein NFS-Mount ([Gemeinsamer Speicher und der DFS-Cluster](gemeinsamer-speicher-und-der-dfs-cluster.md)) |
| Code, Einstellungen, Extensions, Designs | identisch auf jedem Knoten | dieselbe Bereitstellung auf jedem |
| Sitzungen | gemeinsam, oder ein Benutzer bleibt auf einem Knoten | [Sitzungen über mehrere Server](sitzungen-ueber-mehrere-server.md) |
| Caches | lokal je Knoten, synchron gehalten | der Cluster-Handler oder ein gemeinsames Cache-Backend |
| Logs | lokal je Knoten | sammeln Sie sie zentral, wenn Sie eine Gesamtsicht brauchen |
| Cronjobs | ein Knoten | die Crontab nur auf einem Knoten |

Legen Sie nicht das gesamte Verzeichnis `var/` für alle Knoten auf eine Netzwerkfreigabe: Die Cluster-Dokumentation
sagt, die var-Verzeichnisse „dürfen in KEINEM FALL zwischen Instanzen geteilt werden“.

## Dem Load Balancer vertrauen

Hinter einem Load Balancer kommt jede Anfrage von der Adresse des Load Balancers. Exponential glaubt weitergeleiteten
Headern nur von Proxys, die Sie aufführen ([Vertrauenswürdige Proxys](../../../../bc/6.0/vertrauenswuerdige-proxys.md)):

```ini
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
TrustedProxies[]
TrustedProxies[]=192.0.2.10
ClientIpByCustomHTTPHeader=X-Forwarded-For
```

Die leere Zeile ersetzt die ausgelieferte Liste (`127.0.0.1` und `::1`); wiederholen Sie diese Einträge, wenn ein
lokaler Proxy sie weiterhin braucht. Die Adresse ist ein Dokumentationsbeispiel; verwenden Sie die Ihres Load
Balancers. Der Load Balancer muss `X-Forwarded-Proto: https` setzen, wenn er TLS terminiert, und den ursprünglichen
Host weitergeben. Velocity hat eine eigene Liste, `Q.webserver.proxy.trusted` in der Engine-Konfiguration, weil es
entscheidet, bevor der Kernel läuft.

Warum das für eine öffentliche Stelle wichtig ist: Ohne diese Einstellung erscheint jeder Besucher im Prüfprotokoll und
in den Anfrage-Logs mit der Adresse des Load Balancers, sodass eine Untersuchung Anmeldungen nicht auseinanderhalten
kann, und HTTPS-Seiten können `http`-Links erzeugen.

## Cronjobs auf einem Knoten

Installieren Sie die Crontab nur auf einem Knoten. Jedes Skript setzt eine Sperre, aber diese Sperre ist eine Datei
im eigenen Cache-Verzeichnis des Knotens und hält daher einen anderen Knoten nicht davon ab, denselben Job
auszuführen. Um Cronjob-Teile für alle Knoten anzuhalten, richten Sie `cronjob.ini [PauseSettings] Directory` auf ein
gemeinsames Verzeichnis ([Cronjob-Teile anhalten](../../../../../eng-us/features/6.0/cronjob-pause.md)). Dokumentieren Sie,
welcher Knoten die Cronjobs ausführt und wie ein anderer Knoten übernimmt, wenn er ausfällt.

## Bereitstellungen und Cache-Leerungen auf jedem Knoten

Führen Sie die Bereitstellung auf jedem Knoten aus, damit alle den neuen Code ausführen und ihre Caches neu aufbauen:

```bash
./console exp:velocity deploy --allow-root-user
```

Nehmen Sie einen Knoten aus dem Load Balancer, während er bereitgestellt wird, wenn die Änderung nicht halb fertig
ausgeliefert werden darf. Die Caches vor PHP (der HTTP-Cache und der Antwort-Cache von Velocity) sind lokal auf jedem
Knoten; leeren Sie sie nach einer Änderung auf jedem Knoten. Mit dem DFS-Cluster sind die eigenen Cache-Dateien des
Kernels und ihre Ablaufzeitstempel Cluster-Dateien, sodass eine Leerung auf einem Knoten die anderen erreicht
([Mehrere Webknoten](../../../hosting/betrieb/mehrere-web-knoten.md)).

## Wartung und Logs

- Der Wartungszustand ist die Datei `var/maintenance.json` einer Installation. Mit einem `var/`-Verzeichnis je Knoten
  schalten Sie ihn auf jedem Knoten ein und aus.
- Jeder Knoten schreibt sein eigenes `var/log`. Eine Fehlerreferenz, die ein Besucher meldet, kann im Log eines
  anderen Knotens stehen; sammeln Sie die Logs zentral oder wissen Sie, wie Sie jeden Knoten durchsuchen.
- Prüfen Sie jeden Knoten direkt sowie die Adresse hinter dem Load Balancer
  ([Zustandsprüfungen und Verfügbarkeit](../../../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md)).

## Zusammenarbeit mit einem zentralen Netzwerkteam

Geben Sie dem Netzwerkteam eine kurze, schriftliche Spezifikation:

| Element | Zu vereinbarender Wert |
|---|---|
| URL der Zustandsprüfung je Knoten | zum Beispiel die Startseite und `/user/login` der Administration |
| Header, die der Load Balancer setzt | `X-Forwarded-For`, `X-Forwarded-Proto`, ursprünglicher `Host` |
| Sitzungsaffinität | nötig oder nicht ([Sitzungen über mehrere Server](sitzungen-ueber-mehrere-server.md)) |
| Adressen des Load Balancers | für `TrustedProxies[]` |
| Wie ein Knoten für eine Bereitstellung aus dem Verkehr genommen wird | das Verfahren und wer es durchführt |

## Checkliste

- [ ] Datenbankserver, gemeinsamer Speicher und Sitzungsbehandlung sind vorhanden.
- [ ] `TrustedProxies[]` führt den Load Balancer auf; `ClientIpByCustomHTTPHeader` ist gesetzt.
- [ ] Die eigene Liste vertrauenswürdiger Proxys von Velocity ist gesetzt, wenn Velocity die Knoten ausliefert.
- [ ] Die Crontab läuft auf einem Knoten; das Pausenverzeichnis wird gemeinsam genutzt.
- [ ] Die Runbooks für Bereitstellung, Cache-Leerung und Wartung nennen jeden Knoten.
- [ ] Das Netzwerkteam hat die schriftliche Spezifikation.

## Verwandte Seiten

- [Verfügbarkeit](README.md): Übersicht dieses Ordners
- [Überblick über Hochverfügbarkeit](hochverfuegbarkeit-im-ueberblick.md)
- [Varnish und Reverse Proxys](../../../hosting/leistung/varnish-und-reverse-proxys.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
