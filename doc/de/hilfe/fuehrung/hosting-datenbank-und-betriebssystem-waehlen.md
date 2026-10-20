---
title: Hosting, Datenbank und Betriebssystem wählen
audience: project-manager, admin
category: leadership
order: 40
summary: Entscheidungstabellen dafür, wo eine Exponential-Site läuft: die sechs unterstützten Datenbanken in ihrer Reihenfolge, Stufen der Betriebssysteme, Velocity, FrankenPHP, Apache oder nginx, Installationsmethoden und ein Entscheidungsprotokoll.
keywords: Hosting, Datenbank, Betriebssystem, Container, Installationsmethode, Voraussetzungen, Anforderungen, Entscheidung, SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL, Velocity, FrankenPHP, Apache, nginx, PHP-FPM, Plesk, Docker, Kubernetes, database, operating system, containers, install method, requirements, decision
type: help
---
# Hosting, Datenbank und Betriebssystem wählen

Diese Seite ist für Projektleiter, die die Plattformentscheidung treffen oder freigeben müssen, und für Administratoren,
die sie vorbereiten. Sie beantwortet: welche Datenbanken, Betriebssysteme und Webserver Exponential unterstützt und in
welcher Reihenfolge das Projekt sie empfiehlt, welche Installationsmethode zu welcher Situation passt und welche Fragen
die Wahl für Ihr Projekt entscheiden. Die Fakten stammen aus [Datenbankunterstützung](../datenbankunterstuetzung.md),
[Unterstützte Betriebssysteme](../betriebssystemunterstuetzung.md) und den Kapiteln 2, 4 und 8 des Installationshandbuchs; diese Seite
ordnet sie als Entscheidungen an.

## Inhalt

1. [Die Entscheidung in fünf Fragen](#die-entscheidung-in-fünf-fragen)
2. [Datenbank](#datenbank)
3. [Betriebssystem](#betriebssystem)
4. [Webserver und Engine](#webserver-und-engine)
5. [Hosting-Modell](#hosting-modell)
6. [Installationsmethode](#installationsmethode)
7. [Dimensionierung](#dimensionierung)
8. [Backups und späterer Umzug](#backups-und-späterer-umzug)
9. [Ein Entscheidungsprotokoll zum Kopieren](#ein-entscheidungsprotokoll-zum-kopieren)

## Die Entscheidung in fünf Fragen

| Frage | Wenn die Antwort ... lautet | Dann erwägen Sie |
|---|---|---|
| Muss mehr als eine Maschine in die Datenbank schreiben? | ja | Einen Datenbankserver, zuerst PostgreSQL; nicht SQLite |
| Erlaubt das Hosting lang laufende Prozesse? | nein (typisches Shared Hosting) | Apache oder nginx mit PHP-FPM; Velocity meist nicht |
| Ist das PHP der Maschine älter als 8.1? | ja (zum Beispiel das mitgelieferte PHP 8.0 von RHEL 9) | Einen klassischen Webserver; Velocity braucht 8.1 |
| Hat sich die Organisation auf eine Datenbank festgelegt? | ja | Diese Datenbank, wenn sie zu den sechs gehört; lesen Sie ihre Grenzen |
| Gibt es Shell-Zugang auf dem Server? | nein | Den Setup-Assistenten im Browser |

Jede Zeile wird unten mit ihrer Quelle erläutert.

## Datenbank

> **Unterstützte Datenbanken (Exponential 6.0.x), in der Reihenfolge der Präferenz:** 1. SQLite 3 (die Voreinstellung) ·
> 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server ist derzeit bekanntermaßen mit
> zusätzlichem Aufwand möglich. ([Datenbankunterstützung](../datenbankunterstuetzung.md))

Alle sechs haben einen Treiber, einen Eintrag im Setup-Assistenten und einen Weg in den Installern für die
Kommandozeile ([Datenbankunterstützung](../datenbankunterstuetzung.md#die-unterstützten-datenbanken-in-ihrer-reihenfolge)).

### Entscheidungstabelle

| | 1. SQLite 3 | 2. PostgreSQL | 3. MongoDB | 4. MariaDB | 5. Oracle | 6. MySQL |
|---|---|---|---|---|---|---|
| Braucht einen Datenbankserver | nein (eine Datei) | ja | ja | ja | ja | ja |
| Mehrere Webserver an einer Datenbank | nein | ja | ja | ja | ja | ja |
| In der CI des Projekts getestet | Unit-Tests des Treibers | ja (PostgreSQL 15) | Unit-Tests des Adapters | nein (durch den MySQL-Job abgedeckt, derselbe Treiber) | nein | ja (MySQL 8.0) |
| Praktische Mindestversion | 3.33 | 13 | Server 6.0, PHP 8.2 | ein Release, das Ihr Anbieter unterstützt | 19c | ein Release, das Ihr Anbieter unterstützt |
| Wählen Sie sie, wenn | eine Maschine; einfachster Betrieb | Sie einen Datenbankserver brauchen; empfohlener Server | Ihre Organisation MongoDB betreibt | Sie einen MySQL-kompatiblen Server möchten | Ihre Organisation Oracle betreibt | Sie bereits MySQL betreiben |

Quellen: „At a glance“ und „Versions, checked against the code“ unter [Datenbankunterstützung](../datenbankunterstuetzung.md).

### Was die Quellen zu jeder Wahl sagen

- **SQLite 3** ist die Voreinstellung und „mit dem Treiber, den Exponential mitliefert, eine vollwertige
  Produktivdatenbank für eine Site, die von einer Maschine ausgeliefert wird“. Wählen Sie stattdessen einen Server,
  „wenn mehr als eine Maschine in die Datenbank schreiben muss“, „wenn viele Redakteure den ganzen Tag über gleichzeitig
  große Änderungen veröffentlichen oder wenn Sie Replikation oder Benutzer und Rechte auf Datenbankebene brauchen“
  ([Datenbankunterstützung](../datenbankunterstuetzung.md)).
- **PostgreSQL** ist „der empfohlene Datenbankserver: striktes SQL, Sperren auf Zeilenebene, eigene Replikation und
  Wiederherstellung zu einem Zeitpunkt, und er wird bei jedem Push in der CI getestet“.
- **MongoDB** hat dokumentierte Grenzen: Von Hand geschriebenes SQL, das es nicht übersetzen kann, liefert ein leeres
  Ergebnis, einige Cronjobs sind nicht portiert, `eztags` ist nicht nutzbar, und der Shop-Checkout sowie die Planung von
  `ezflow` sind nicht durchgängig getestet ([Grenzen von MongoDB](../datenbankunterstuetzung.md#grenzen-von-mongodb)). Prüfen Sie
  jede Erweiterung, die Sie verwenden möchten, gegen diese Grenzen.
- **MariaDB** läuft mit demselben Treiber wie MySQL und ist „der MySQL-kompatible Server, den das Projekt bevorzugt“.
- **Oracle** (19c und neuer) wird über die Erweiterung `ezoracle` unterstützt, die in jeder Installation vorhanden ist.
- **MySQL** steht in der Reihenfolge an letzter Stelle, „nicht weil es weniger gut funktioniert, sondern weil das
  Projekt für neue Sites zuerst die anderen empfiehlt“.
- **Microsoft SQL Server** hat in 6.0.x keinen Treiber; nichts davon sollte als Versprechen eines Treibers gelesen
  werden.

> **Tipp**
> Die Frage des Projektleiters lautet nicht „welche Datenbank ist die beste“, sondern „welche Datenbank kann unser Team
> betreiben, sichern und wiederherstellen“. Tragen Sie den Namen der Person, die sie wiederherstellt, in das
> Entscheidungsprotokoll ein.

## Betriebssystem

Exponential läuft auf „fast jedem“ System, „auf dem PHP 8.0 oder neuer läuft“.
[Unterstützte Betriebssysteme](../betriebssystemunterstuetzung.md) ordnet die Systeme in vier Stufen:

| Stufe | Bedeutung | Beispiele aus den Quellen |
|---|---|---|
| 1. Hier getestet | Das Projekt betreibt es täglich, oder die CI läuft bei jedem Push darauf | AlmaLinux 9 (der Entwicklungsserver, mit Plesk), Ubuntu auf GitHub Actions |
| 2. Bekanntermaßen funktionsfähig | Abgedeckt durch Dokumentation, Pakete oder die CI von Velocity | Red Hat Enterprise Linux 9 und 10 und ihre Nachbauten, Debian 12 und 13, Ubuntu 22.04 und 24.04, Alpine, Plesk, FreeBSD 14, macOS, Windows, Docker und Podman |
| 3. Voraussichtlich funktionsfähig | Es gibt ein gepflegtes PHP 8, niemand hat Exponential darauf laufen sehen | Fedora, SUSE, Arch, Hosts mit cPanel und DirectAdmin, Kubernetes und andere |
| 4. Derzeit nicht möglich | Kein PHP 8 für das System | aufgeführt, damit die Frage eine klare Antwort bekommt |

Bevorzugen Sie für ein Produktivprojekt Stufe 1 oder Stufe 2. Ein System der Stufe 3 ist „eine begründete Erwartung,
kein Testergebnis“ ([Die vier Stufen](../betriebssystemunterstuetzung.md#die-vier-stufen)); wenn Sie eines wählen, tragen Sie es in das
Risikoregister ein und planen Sie frühzeitig eine Testinstallation.

Was jedes System bereitstellen muss, steht unter [Unterstützte Betriebssysteme](../betriebssystemunterstuetzung.md) und in
[Kapitel 2](../../installation/02-voraussetzungen.md): PHP 8.0 oder neuer (8.1 für Velocity), die erforderlichen
PHP-Erweiterungen, eine Datenbank, Composer 2, beschreibbare Verzeichnisse, und 64-Bit-PHP wird empfohlen, „für die
Unterstützung von mehr als 30 Sprachen“.

## Webserver und Engine

[Kapitel 8](../../installation/08-die-website-ausliefern.md#81-die-wahl-in-einer-tabelle) vergleicht die Möglichkeiten:

| | Exponential Velocity | FrankenPHP (über Velocity) | Apache + PHP-FPM | nginx + PHP-FPM |
|---|---|---|---|---|
| Rolle | **empfohlen** für jede Stufe | produktionsreife Alternative | klassisch | klassisch |
| PHP | 8.1 oder neuer | das im Binary enthaltene PHP | 8.0 oder neuer | 8.0 oder neuer |
| Separater Webserver nötig | nein | nein | ja | ja |
| Separates Werkzeug für HTTPS oder Zertifikate nötig | nein: eigene Zertifikate, selbst signierter Ersatz, ACME | nein: Ihr Zertifikat oder ein selbst signiertes | ja: `mod_ssl` plus certbot oder ein Hosting-Panel | ja |
| Anwendung im Speicher gehalten | ja (persistente Worker) | nein | nein | nein |
| Geeignet für Shared Hosting | meist nicht | meist nicht | ja | selten |

Wann ein klassischer Server angebracht ist, laut Kapitel 8: „wenn Ihr Hosting keine lang laufenden Prozesse erlaubt,
wenn Sie bereits einen abgestimmten Apache oder nginx betreiben, den Sie behalten möchten, wenn die Maschine nur PHP 8.0
hat ... oder wenn mehrere Kunden auf einer Maschine voneinander auf Betriebssystemebene isoliert sein müssen“. Die
Möglichkeiten schließen sich nicht aus: Velocity kann die Site ausliefern, während Apache oder nginx während einer
Migration auf denselben Dateien bestehen bleibt.

Andere Server (LiteSpeed, Caddy mit PHP-FPM, IIS, lighttpd) gehören zu Stufe 3; der eingebaute Server von PHP ist nur
für die Entwicklung gedacht ([Webserver](../betriebssystemunterstuetzung.md#webserver)).

Mehr zu Velocity: [Der Server mit persistenten Workern](../../../eng-us/features/6.0/velocity-persistent-worker-server.md),
[Der Webserver Velocity](../../../eng-us/features/6.0/velocity-web-server.md),
[Der Antwort-Cache](../../../eng-us/features/6.0/velocity-response-cache.md),
[Das Control Panel](../../../eng-us/features/6.0/velocity-control-panel.md),
[HTTPS-Zertifikate](../../../eng-us/features/6.0/velocity-https-certificates.md). Zu FrankenPHP:
[FrankenPHP](../../../eng-us/bc/6.0/frankenphp.md).

## Hosting-Modell

| Modell | Was die Quellen sagen | Fragen für Ihr Projekt |
|---|---|---|
| Eigener Server oder virtuelle Maschine | Cloud-Maschinen „sind gewöhnliche Linux-Maschinen: je nach Distribution Stufe 2 oder 3“ | Wer spielt die Patches für Betriebssystem, PHP und Datenbank ein? |
| Hosting-Panel | Plesk ist Stufe 1; cPanel und DirectAdmin Stufe 3; „Shared Hosting kann lang laufende Prozesse verbieten, sodass Velocity möglicherweise nicht erlaubt ist“ | Erlaubt das Panel lang laufende Prozesse? Gibt es Shell-Zugang? |
| Container (Docker, Podman, Kubernetes) | `var/` und `settings/override` auf einem persistenten Volume halten; mit SQLite einen schreibenden Container betreiben; mehrere Replikate brauchen einen Datenbankserver und gemeinsamen Speicher für `var/storage` | Wie viele Replikate? Welcher Datenbankserver? |
| Platform as a Service | Funktioniert voraussichtlich mit PHP 8 und einem beschreibbaren, persistenten Datenträger; ein schreibgeschütztes oder flüchtiges Dateisystem erfordert, `var/` zu verlagern, und einen Datenbankserver | Ist der Datenträger persistent? |
| Verwaltetes Hosting durch 7x | 7x bietet „Exponential Website Hosting“ und „Exponential Platform SSD Hosting“ an; Preise und Bedingungen stehen auf der Website von 7x | Fragen Sie 7x direkt; diese Hilfe nennt keine Bedingungen |

Quellen: [Container, Cloud und Hosting-Panels](../betriebssystemunterstuetzung.md#container-cloud-und-hosting-panels),
[Wer ist 7x?](../wer-ist-7x.md#dienstleistungen-und-hosting).

> **Hinweis**
> Exponential ist freie Software und lässt sich auf jedem System installieren, das die Voraussetzungen erfüllt; ein
> Projekt ist also an keinen Hoster gebunden. Eine Site später umzuziehen bedeutet, Dateien und eine Datenbank
> umzuziehen; siehe [Backups und späterer Umzug](#backups-und-späterer-umzug).

## Installationsmethode

[Kapitel 4](../../installation/04-installationsweg-waehlen.md#45-welche-methode-für-welche-situation) bietet einen Installer
mit drei Oberflächen:

| Situation | Empfohlen | Warum |
|---|---|---|
| Ein erster Blick auf einem Laptop | `exp:install` | ein Befehl, SQLite, keine Fragen zu beantworten |
| Erste Produktivinstallation, von Hand durchgeführt | der Setup-Assistent oder `exp:install` mit ausdrücklichen Optionen | der Assistent erklärt jede Entscheidung |
| Shared Hosting ohne Shell-Zugang | der Setup-Assistent | er braucht nur einen Browser und FTP-Zugang |
| Dieselbe Site wird immer wieder neu aufgebaut (Staging, Test, Schulung) | der Kickstarter | die geprüfte `kickstart.ini` ist die Spezifikation der Installation |
| CI-Pipeline, Container-Image, automatisierte Testumgebung | `exp:install` | Optionen oder Umgebungsvariablen, Exit-Status |
| Kein Internetzugang auf dem Server | der Kickstarter oder der Assistent mit lokalen Paketen | die Pakete werden aus dem lokalen Repository genommen |

Für den Bau von Websites empfiehlt das Projekt Git-Klone des Kernels und jeder Erweiterung mit vollständiger Historie
([Mit Git entwickeln](../mit-git-entwickeln.md)). Die Site-Pakete, die der Installer anbietet (zum Beispiel
`sevenx_multisite`, mit oder ohne Demo-Inhalte), sind in
[4.3 Site-Pakete](../../installation/04-installationsweg-waehlen.md#43-site-pakete-was-installiert-wird) aufgeführt.

## Dimensionierung

Das Installationshandbuch sagt offen, dass „die Dokumentation wenige feste Zahlen nennt und dieses Buch keine weiteren
erfindet“. Was es angibt ([2.10 Arbeitsspeicher, CPU und Festplatte](../../installation/02-voraussetzungen.md#210-arbeitsspeicher-cpu-und-festplatte)):

| Punkt | Wert |
|---|---|
| PHP `memory_limit` | mindestens 64M |
| Ein Velocity-Worker | etwa 10 MB privater Arbeitsspeicher (gemessen) |
| Velocity-Pool | `Workers=4` ausgeliefert |
| Eine MongoDB-Installation | RAM mindestens 512 MB, empfohlen 2 GB; Festplatte mindestens 2 GB, empfohlen 10 GB |

Der Platzbedarf auf der Festplatte wächst mit Uploads und Bildvarianten (`var/storage`), Caches und Logs. Der Rat lautet,
„eine laufende Site zu messen, statt zu raten“, mit dem Befehl [benchmark](../../../eng-us/features/6.0/benchmark.md) und dem
[Control Panel](../../../eng-us/features/6.0/velocity-control-panel.md) von Velocity. Planen Sie eine Messung auf der
Testinstallation mit echten Inhalten in den Zeitplan ein, bevor Sie sich auf eine Maschinengröße festlegen.

Stellschrauben für die Performance je Datenbank sind unter
[Performance und Skalierung](../datenbankunterstuetzung.md#performance-und-skalierung) aufgeführt.

## Backups und späterer Umzug

- **Ein vollständiges Backup besteht aus der Datenbank und den Dateien**: `var/storage`, `settings/override` und
  `settings/siteaccess` ([Sicherung und Wiederherstellung](../datenbankunterstuetzung.md#backup-und-wiederherstellung)).
- **Der Wechsel zu einer anderen Datenbank** ist zwischen den SQL-Engines mit dem Schema-Dump und den Insert-Skripten
  möglich, und nach Oracle und MongoDB mit deren eigenen Werkzeugen; planen Sie ein Wartungsfenster ein, denn zwischen
  dem Dump und der Umstellung darf nichts veröffentlicht werden
  ([Eine Site auf eine andere Datenbank umziehen](../datenbankunterstuetzung.md#eine-site-auf-eine-andere-datenbank-umziehen)).
- **Die Wiederherstellung muss getestet sein**: Die Checkliste für die erste Woche verlangt ein Backup, das „erstellt
  **und auf einer anderen Maschine wiederhergestellt**“ wurde
  ([10.15](../../installation/10-nach-der-installation.md#1015-eine-checkliste-für-die-erste-woche)).

## Ein Entscheidungsprotokoll zum Kopieren

```text
Platform decision for <project name>          date: <date>        decided by: <names>

Product:            Exponential 6.0.x  /  <other product of the family>
Database:           <SQLite 3 | PostgreSQL | MongoDB | MariaDB | Oracle | MySQL>, version <x>
  Why:              <one or two sentences, with the row of the decision table>
  Who restores it:  <name>
Operating system:   <name and version>, tier <1-4> per the help's OS support page
PHP:                <version> for the web, <version> for the command line
Web server/engine:  <Velocity | FrankenPHP | Apache + PHP-FPM | nginx + PHP-FPM | other>
Hosting model:      <own server | panel | containers | PaaS | managed>
Install method:     <setup wizard | kickstarter | exp:install | Git clones + ...>
Environments:       development <where>, test <where>, production <where>
Backups:            what <database + var/storage + settings>, where <off the machine>, restore tested on <date>
Risks accepted:     <for example a tier 3 system, MongoDB limits checked against extensions X, Y>
```

## Verwandte Seiten

- [Datenbankunterstützung](../datenbankunterstuetzung.md)
- [Unterstützte Betriebssysteme](../betriebssystemunterstuetzung.md)
- [Kapitel 2: Voraussetzungen](../../installation/02-voraussetzungen.md)
- [Kapitel 4: Eine Installationsmethode wählen](../../installation/04-installationsweg-waehlen.md)
- [Kapitel 8: Die Site ausliefern](../../installation/08-die-website-ausliefern.md)
- [Risikomanagement für Exponential-Projekte](risikomanagement.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
