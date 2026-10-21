---
title: Kubernetes
audience: hosting, admin
category: hosting
order: 70
summary: Was die Quellen über den Betrieb von Exponential auf Kubernetes sagen (Stufe 3, funktioniert erwartungsgemäß) und was mehrere Replikate von der Datenbank, dem gemeinsamen Dateispeicher, den Sitzungen, Cron, Proxys und dem Ausrollen von Code verlangen, geschrieben als Planungsliste statt als Manifeste.
keywords: Kubernetes, Pods, Replikate, persistentes Volume, gemeinsamer Speicher, ReadWriteMany, PostgreSQL, SQLite, Sitzungen, ezpSessionHandlerDB, Cluster-Datei-Handler, eZDFSFileHandler, CronJob, Ingress, vertrauenswürdige Proxys, Velocity, pods, replicas, persistent volume, shared storage, sessions, cluster file handler, ingress, trusted proxies
type: help
---
# Kubernetes

Diese Seite ist für Plattform-Teams bei Hosting-Anbietern, die Exponential auf Kubernetes betreiben möchten. Sie beantwortet,
was die Quellen des Projekts zu Kubernetes sagen und was sich ändert, wenn eine Website in mehr als einem Replikat läuft: die
Datenbank, gemeinsame Dateien, Sitzungen, Cron, der Proxy davor und das Ausrollen von Code. Sie liefert keine Manifeste: Die
Quellen liefern keine mit, und jeder Cluster ist anders. Betrachten Sie sie als Liste der Entscheidungen, die Ihre Manifeste
abbilden müssen.

## Inhalt

1. [Was die Quellen sagen](#was-die-quellen-sagen)
2. [Ein Replikat oder mehrere](#ein-replikat-oder-mehrere)
3. [Die Datenbank](#die-datenbank)
4. [Gemeinsame Dateien](#gemeinsame-dateien)
5. [Sitzungen](#sitzungen)
6. [Cron](#cron)
7. [Ingress und vertrauenswürdige Proxys](#ingress-und-vertrauenswürdige-proxys)
8. [Code ausrollen](#code-ausrollen)
9. [Checkliste](#checkliste)

## Was die Quellen sagen

- **Kubernetes ist Stufe 3**, funktioniert erwartungsgemäß: Es führt die oben beschriebenen Container aus, mit dem Hinweis
  auf einen einzigen Schreiber für SQLite ([Stufe 3](../../betriebssystemunterstuetzung.md#stufe-3-voraussichtlich-lauffähig)). Stufe 3 ist eine
  begründete Erwartung, kein Testergebnis.
- Mehrere Replikate brauchen einen Datenbankserver (zuerst PostgreSQL) und gemeinsamen Speicher für `var/storage`
  ([Container, Cloud und Hosting-Panels](../../betriebssystemunterstuetzung.md#container-cloud-und-hosting-panels)).
- Alles auf [Container: Docker und Podman](container-docker-und-podman.md) gilt: Images, Volumes, der Entry Point,
  Geheimnisse.

> **Hinweis**
> Wenn Sie Exponential produktiv auf Kubernetes betreiben, teilen Sie es dem Projekt in seinem Tracker mit; die Seite zu
> den Betriebssystemen bittet genau darum, damit ein Eintrag der Stufe 3 aufrücken kann
> ([Die vier Stufen](../../betriebssystemunterstuetzung.md#die-vier-stufen)).

## Ein Replikat oder mehrere

| | Ein Replikat | Mehrere Replikate |
|---|---|---|
| Datenbank | jede der sechs, einschließlich 1. SQLite 3 | ein Datenbankserver |
| `var/` | ein persistentes Volume | gemeinsamer Speicher für `var/storage`; Caches pro Replikat oder gemeinsam, nach Entscheidung |
| Sitzungen | die Standarddateien von PHP funktionieren | gemeinsamer Sitzungsspeicher erforderlich |
| Cron | im Pod oder ein CronJob | genau ein Planer, nicht einer pro Replikat |
| Rollende Updates | eine kurze Lücke oder eine Strategie mit Neuerstellung | möglich; achten Sie auf Änderungen am Datenbankschema |

Ein einzelnes Replikat mit SQLite ist die einfachste Bereitstellung auf Kubernetes und bleibt innerhalb der Regel eines
einzigen Schreibers. Planen Sie den Schritt zu mehreren Replikaten als eigenes Projekt.

## Die Datenbank

In der offiziellen Reihenfolge: 1. SQLite 3 (der Standard) · 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL.
Microsoft SQL Server ist derzeit bekanntermaßen mit zusätzlicher Arbeit möglich.

Mit mehreren Replikaten scheidet SQLite aus: Das WAL von SQLite braucht gemeinsamen Speicher auf einem Host, sodass mehrere
Replikate keine Datenbankdatei über ein Netzwerk-Volume teilen können. Die Quellen empfehlen zuerst **2. PostgreSQL**; jede
Server-Datenbank der Liste hat „Mehrere Webserver an einer Datenbank: ja“
([Auf einen Blick](../../datenbankunterstuetzung.md#auf-einen-blick)). Unter Velocity hält jeder Worker seine eigene Verbindung, sodass
die Datenbank Worker mal Replikate Verbindungen annehmen muss.

## Gemeinsame Dateien

`var/storage` enthält Uploads und Bildvarianten; jedes Replikat muss dieselben Dateien sehen. In den Quellen gibt es zwei
Ansätze:

1. **Ein gemeinsames Dateisystem, das in jedem Replikat eingehängt ist** (ein Volume, in das mehrere Pods schreiben können),
   mit dem Standard-Datei-Handler `eZFSFileHandler` (`settings/file.ini [ClusteringSettings] FileHandler`).
2. **Der Cluster-Datei-Handler** `eZDFSFileHandler`, in `file.ini` beschrieben als Handler für ein verteiltes Dateisystem
   mit einer Datenbankschicht, erforderlich für Architekturen auf NFS-Basis; seine `[eZDFSClusteringSettings]` nennen den
   Einhängepunkt (`MountPointPath`) und ein Datenbank-Backend für die Metadaten der Dateien.

Clustering wird in [Clustering und gemeinsamer Speicher](../betrieb/clustering-und-gemeinsamer-speicher.md) und
[Mehrere Web-Knoten](../betrieb/mehrere-web-knoten.md) behandelt.

Caches (`var/cache`, `var/<site>/cache`) können gemeinsam oder lokal pro Replikat sein; ein lokaler Cache bedeutet, dass
ein Leeren des Caches jedes Replikat erreichen muss. Pfade für Logs und Caches pro Website (`LogVarDir`, `CacheVarDir`)
erlauben es, Logs und Caches auf andere Dateisysteme als den Speicher zu legen
([Logs, INI-Cache und Ablaufzeitstempel pro Website](../../../../eng-us/features/6.0/multi-site-log-and-cache-paths.md)).

## Sitzungen

Standardmäßig ist `site.ini [Session] Handler` leer, was `ezpSessionHandlerPHP` bedeutet: PHP hält sie in seinem eigenen
Speicher (Dateien) ([Sitzungen](../../../anleitungen/sitzungen.md)). Dateien auf einem Pod sind für den nächsten unsichtbar; ein
Besucher, der auf einem Replikat angemeldet ist, ist auf einem anderen ein Fremder. Wählen Sie eines:

- `Handler=ezpSessionHandlerDB`, das Sitzungen in der Tabelle `ezsession` der gemeinsamen Datenbank hält; oder
- einen Session-Handler von PHP, den jedes Replikat teilt (in PHP konfiguriert, außerhalb von Exponential); oder
- Sticky Sessions am Ingress, was das Problem verdeckt, bis ein Pod ersetzt wird.

```ini
# settings/override/site.ini.append.php
[Session]
Handler=ezpSessionHandlerDB
```

## Cron

`runcronjobs.php` muss für die Website nach einem Zeitplan laufen, nicht einmal pro Replikat: Mehrere Replikate, die den
Teil `frequent` in derselben Minute ausführen, würden Benachrichtigungen doppelt verschicken und Workflows doppelt
ausführen. Ein Kubernetes-CronJob, der einen Container aus demselben Image startet, mit denselben Volumes,
Datenbankeinstellungen und demselben Benutzer, entspricht der dokumentierten Crontab
([Kapitel 10, Cronjobs](../../../installation/10-nach-der-installation.md#103-cronjobs)):

```text
*/5   * * * *   php runcronjobs.php -q -s site frequent
*/15  * * * *   php runcronjobs.php -q -s site
17    * * * *   php runcronjobs.php -q -s site infrequent
```

Der eigene Planer von Velocity ersetzt den System-Cron für diese Teile nicht von Haus aus.

## Ingress und vertrauenswürdige Proxys

Hinter einem Ingress-Controller oder Load Balancer muss Exponential wissen, welche Adressen ihm die Adresse und das Protokoll
des Besuchers mitteilen dürfen:

- Unter PHP-FPM führen Sie die Proxys in `site.ini [HTTPHeaderSettings] TrustedProxies[]` auf (mitgeliefert: `127.0.0.1` und
  `::1`); setzen Sie `ClientIpByCustomHTTPHeader` nur zusammen mit dieser Liste
  ([13.10 Hinter einem Proxy](../../../installation/13-sicherheitshaertung.md#1310-hinter-einem-proxy-vertrauenswürdige-header)).
- Unter Velocity konfigurieren Sie die vertrauenswürdigen Proxys in der eigenen Liste von Velocity,
  `Q.webserver.proxy.trusted`; Proxys vor Velocity werden **dort** konfiguriert
  ([Vertrauenswürdige Proxys](../../../../eng-us/bc/6.0/trusted-proxies.md)).
- Stellen Sie sicher, dass der Anwendungsserver nicht an ihnen vorbei erreichbar ist.

> **Warnung**
> Eine falsche Liste vertrauenswürdiger Proxys lässt Besucher entweder ihre Adresse und ihren HTTPS-Zustand fälschen, oder
> sie lässt jeden Besucher wie der Ingress aussehen. Testen Sie mit der curl-Prüfung aus
> [8.8 Hinter einem Reverse Proxy](../../../installation/08-die-website-ausliefern.md#88-hinter-einem-reverse-proxy).

## Code ausrollen

Die Worker von Velocity halten die Anwendung im Speicher; eine Codeänderung erreicht sie erst nach einem Neustart. In
Kubernetes ist die natürliche Einheit ein neues Image, das auf neue Pods ausgerollt wird. Zwei Vorsichtsmaßnahmen aus den
Quellen:

- Schemaänderungen eines Upgrades (die Update-Dateien in `update/database/`, ein Verzeichnis pro Engine) laufen einmal gegen
  die gemeinsame Datenbank, nicht pro Pod ([Kunden-Websites aktualisieren](../betrieb/kunden-websites-aktualisieren.md)).
- Leeren Sie nach dem Ausrollen die Caches, die sich die Replikate teilen
  ([Caches leeren und vorwärmen](../leistung/caches-leeren-und-vorwaermen.md)).

## Checkliste

- [ ] Ein Replikat mit SQLite, oder mehrere mit einem Datenbankserver (zuerst PostgreSQL).
- [ ] `var/storage` von jedem Replikat geteilt, oder der Cluster-Datei-Handler konfiguriert.
- [ ] Sitzungen geteilt (`ezpSessionHandlerDB` oder ein gemeinsamer Handler von PHP).
- [ ] Genau ein Planer für `runcronjobs.php`.
- [ ] Vertrauenswürdige Proxys für den Ingress gesetzt, in `site.ini` oder in der Liste von Velocity.
- [ ] Das Verfahren zum Ausrollen deckt Schema-Updates und das Leeren der Caches ab.
- [ ] Eine Testinstallation auf dem Cluster vor jeder Kunden-Website, weil dies Stufe 3 ist.

## Verwandte Seiten

- [Container: Docker und Podman](container-docker-und-podman.md)
- [Mehrere Web-Knoten](../betrieb/mehrere-web-knoten.md), [Clustering und gemeinsamer Speicher](../betrieb/clustering-und-gemeinsamer-speicher.md)
- [Varnish und Reverse Proxys](../leistung/varnish-und-reverse-proxys.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
