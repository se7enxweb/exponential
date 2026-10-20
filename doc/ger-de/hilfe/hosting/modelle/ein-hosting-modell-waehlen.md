---
title: Ein Hosting-Modell wählen
audience: hosting
category: hosting
order: 20
summary: Ein Vergleich von Shared Hosting, VPS, dedizierten Servern, Containern und Kubernetes für Exponential, aufgebaut aus dem, was die Quellen des Projekts sagen, mit den Fragen, die entscheiden, welches Modell ein Hosting-Angebot nutzen sollte.
keywords: Hosting-Modell, Vergleich, Shared Hosting, VPS, dedizierter Server, Container, Docker, Podman, Kubernetes, Velocity, PHP-FPM, SQLite, PostgreSQL, Abschottung, Shell-Zugang, lang laufender Prozess, Entscheidung, hosting model, comparison, shared hosting, dedicated server, containers, isolation, shell access, long-running process, decision
featured: true
type: help
---
# Ein Hosting-Modell wählen

Diese Seite ist für Produktmanager und Systemadministratoren bei Hosting-Anbietern, die entscheiden müssen, welche
Hosting-Modelle sie für Exponential anbieten. Sie beantwortet: wie sich Shared Hosting, VPS, dedizierte Server, Container
und Kubernetes für eine Exponential-Website vergleichen, welche Fakten in den Quellen des Projekts den Vergleich bestimmen
und welche Fragen das Modell für ein Angebot oder einen Kunden entscheiden. Sie nennt keine Preise und keine Zahlen zu
Websites pro Server: Die Quellen nennen keine, und die richtigen Zahlen ergeben sich aus der Messung Ihrer eigenen Maschinen.

## Inhalt

1. [Die Vergleichstabelle](#die-vergleichstabelle)
2. [Die fünf Fragen, die entscheiden](#die-fünf-fragen-die-entscheiden)
3. [Webserver und Engine je Modell](#webserver-und-engine-je-modell)
4. [Datenbank je Modell](#datenbank-je-modell)
5. [Abschottung zwischen Kunden](#abschottung-zwischen-kunden)
6. [Modelle mischen](#modelle-mischen)
7. [Ein Entscheidungsprotokoll](#ein-entscheidungsprotokoll)

## Die Vergleichstabelle

| | Shared Hosting | VPS | Dedizierter Server | Container (Docker, Podman) | Kubernetes |
|---|---|---|---|---|---|
| Typischer Zugang des Kunden | Panel, FTP, oft keine Shell | root oder ein Benutzer mit sudo | root | das Image und seine Volumes | Manifeste, Images, Volumes |
| Lang laufender Prozess (Velocity, FrankenPHP) | meist nicht erlaubt | ja | ja | ja, der Hauptprozess des Containers | ja |
| Übliche Art der Auslieferung | Apache oder LiteSpeed mit `.htaccess` | Velocity oder Apache/nginx mit PHP-FPM | Velocity oder Apache/nginx mit PHP-FPM | Velocity im Container | Velocity in den Pods oder PHP-FPM hinter einem Webserver |
| Installationsmethode | der Setup-Assistent im Browser | `exp:install`, der Kickstarter oder der Assistent | wie VPS | `exp:install` im Entry Point | `exp:install` in einem Job oder Entry Point |
| SQLite 3 (die Standarddatenbank) | ja, eine Website pro Datei | ja | ja | nur ein schreibender Container | nur ein schreibendes Replikat |
| Mehrere Web-Knoten für eine Website | nein | nein (eine Maschine) | nein (eine Maschine) | braucht einen Datenbankserver und gemeinsames `var/storage` | braucht einen Datenbankserver und gemeinsames `var/storage` |
| Abschottung zwischen Kunden | pro Konto und PHP-FPM-Pool, wie der Hoster es einrichtet | eine Maschine pro Kunde | eine Maschine pro Kunde | pro Container | pro Namespace und Pod, wie konfiguriert |
| Stufe in den Quellen | über das Panel: Plesk Stufe 1, cPanel und DirectAdmin Stufe 3 | nach Distribution: Stufe 2 oder 3 | nach Distribution: Stufe 1 bis 3 | Stufe 2 (Docker und Podman) | Stufe 3 |

Quellen: [Betriebssystemunterstützung](../../betriebssystemunterstuetzung.md) (Stufen, „Container, Cloud und Hosting-Panels“),
[Kapitel 8](../../../installation/08-die-website-ausliefern.md#81-die-wahl-in-einer-tabelle) („Passt zu Shared Hosting“),
[Kapitel 4](../../../installation/04-installationsweg-waehlen.md#45-welche-methode-für-welche-situation) (Installationsmethoden)
und die `README.md` im Wurzelverzeichnis (wann ein klassischer Server noch die richtige Antwort ist).

> **Hinweis**
> „Stufe“ ist hier die eigene Skala des Projekts: 1 hier getestet, 2 funktioniert bekanntermaßen, 3 funktioniert
> erwartungsgemäß, 4 heute nicht möglich. Ein Eintrag der Stufe 3 ist eine begründete Erwartung, kein Testergebnis
> ([Die vier Stufen](../../betriebssystemunterstuetzung.md#die-vier-stufen)). Planen Sie eine Testinstallation, bevor Sie ein Modell der
> Stufe 3 verkaufen.

## Die fünf Fragen, die entscheiden

| Frage | Wenn die Antwort ... lautet | Dann |
|---|---|---|
| Dürfen Kunden einen lang laufenden Prozess ausführen? | nein | Apache oder LiteSpeed mit `.htaccess` oder nginx mit PHP-FPM; Velocity ist meist nicht möglich ([Shared Hosting](shared-hosting.md)) |
| Braucht die Website mehr als eine Maschine, die in die Datenbank schreibt? | ja | ein Datenbankserver, zuerst PostgreSQL; nicht SQLite ([Datenbankunterstützung](../../datenbankunterstuetzung.md#die-unterstützten-datenbanken-in-ihrer-reihenfolge)) |
| Ist das PHP der Maschine älter als 8.1? | ja, zum Beispiel das mitgelieferte PHP 8.0 von Red Hat Enterprise Linux 9 | ein klassischer Webserver; Velocity braucht 8.1 ([Kapitel 2](../../../installation/02-voraussetzungen.md#22-php)) |
| Müssen Kunden auf Ebene des Betriebssystems abgeschottet sein? | ja | ein PHP-FPM-Pool oder eine Velocity-Instanz pro Website, jeweils als eigener Benutzer ([Abschottung](#abschottung-zwischen-kunden)) |
| Hat der Kunde Shell-Zugang? | nein | der Setup-Assistent im Browser installiert die Website ([Kapitel 4](../../../installation/04-installationsweg-waehlen.md#45-welche-methode-für-welche-situation)) |

## Webserver und Engine je Modell

Das Projekt empfiehlt Exponential Velocity für jede Phase; FrankenPHP ist eine produktionsreife Alternative; Apache und
nginx mit PHP-FPM sind die klassischen Einrichtungen. Kapitel 8 nennt die Fälle, in denen ein klassischer Server die bessere
Antwort ist: wenn Ihr Hosting keine lang laufenden Prozesse erlaubt, wenn Sie bereits ein abgestimmtes Apache oder nginx
betreiben, das Sie behalten möchten, wenn die Maschine nur PHP 8.0 hat oder wenn mehrere Kunden auf einer Maschine
voneinander auf Ebene des Betriebssystems abgeschottet sein müssen
([8.1 Die Wahl in einer Tabelle](../../../installation/08-die-website-ausliefern.md#81-die-wahl-in-einer-tabelle)).

Für einen Hosting-Anbieter heißt das:

- **Shared Hosting**: Apache (oder LiteSpeed) mit `.htaccess` und einem PHP-FPM-Pool pro Konto.
- **VPS und dediziert**: Velocity als Standard; Apache oder nginx, wenn ein Panel die Maschine verwaltet oder der Kunde es
  möchte. Beide können während einer Migration nebeneinander auf denselben Dateien laufen.
- **Container und Kubernetes**: Velocity als Prozess des Containers, da es keinen getrennten Webserver braucht und HTTPS
  selbst ausliefert; oder ein Webserver-Container vor PHP-FPM.

Details: [Einen Webserver wählen](../server/einen-webserver-waehlen.md) und
[Velocity für Hoster](../server/velocity-fuer-hoster.md).

## Datenbank je Modell

Die sechs unterstützten Datenbanken in der offiziellen Reihenfolge: 1. SQLite 3 (der Standard) · 2. PostgreSQL ·
3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server ist derzeit bekanntermaßen mit zusätzlicher Arbeit
möglich.

| Modell | Was passt | Warum |
|---|---|---|
| Shared Hosting | 1. SQLite 3 für kleine Websites; sonst der Datenbankserver, den das Panel anbietet (oft 4. MariaDB oder 6. MySQL) | SQLite braucht keinen Server und kein Konto; es ist eine vollwertige Produktionsdatenbank für eine Website, die von einer Maschine ausgeliefert wird |
| VPS, dediziert | jede der sechs; zuerst 1. SQLite 3 oder 2. PostgreSQL | eine Maschine; der Standard des Kunden oder Ihrer entscheidet |
| Container | 1. SQLite 3 mit einem schreibenden Container oder ein Datenbankserver | das WAL von SQLite braucht gemeinsamen Speicher auf einem Host |
| Kubernetes mit mehreren Replikaten | ein Datenbankserver, zuerst 2. PostgreSQL | mehrere Replikate können keine SQLite-Datei über ein Netzwerk-Volume teilen |

Quellen: [Datenbankunterstützung](../../datenbankunterstuetzung.md) und
[Container, Cloud und Hosting-Panels](../../betriebssystemunterstuetzung.md#container-cloud-und-hosting-panels). Lesen Sie die
[Grenzen von MongoDB](../../datenbankunterstuetzung.md#grenzen-von-mongodb), bevor Sie 3. MongoDB anbieten.

## Abschottung zwischen Kunden

Die `README.md` im Wurzelverzeichnis ist eindeutig: Ein einzelner Serverprozess, der mehrere Websites ausliefert, ist eine
Bequemlichkeit beim Routing, keine Sicherheitsgrenze; der Prozess, der eine Website ausliefert, kann jede Datei lesen, die
die anderen enthalten. Für mehrere Kunden auf einer Maschine empfiehlt sie eine Velocity-Instanz pro Website hinter einem
Proxy, jede als eigener Benutzer des Betriebssystems, oder einen klassischen Server mit einem PHP-FPM-Pool pro Website
([Kapitel 8](../../../installation/08-die-website-ausliefern.md#81-die-wahl-in-einer-tabelle)).

> **Warnung**
> Mehrere Kunden-Websites innerhalb einer Exponential-Installation (eine Installation mit mehreren Websites) teilen eine
> Codebasis, einen Satz von Prozessen und meist eine Datenbank. Das ist eine gute Einrichtung für die Websites einer
> Organisation, keine Grenze zwischen Kunden, die die Daten des jeweils anderen nicht sehen dürfen. Siehe
> [Trennung von Mandanten](../sicherheit/trennung-von-mandanten.md).

## Modelle mischen

Die meisten Hosting-Anbieter bieten am Ende mehr als ein Modell an. Übliche Kombinationen, alle von den Quellen abgedeckt:

- **Shared Hosting für kleine Websites, VPS für Wachstum.** Eine Website umzuziehen heißt, ihre Dateien und ihre Datenbank
  umzuziehen ([Eine Website zwischen Hostern umziehen](../migrationen/eine-website-zwischen-hostern-umziehen.md)).
- **Velocity neben dem Apache eines Panels.** Der eigene Entwicklungsserver des Projekts betreibt das Apache von Plesk mit
  PHP-FPM und Exponential Velocity auf derselben Installation ([Stufe 1](../../betriebssystemunterstuetzung.md#stufe-1-hier-getestet)).
- **Container für Staging, eine VM für die Produktion**, aus denselben Optionen von `exp:install` oder derselben
  `kickstart.ini` gebaut.

## Ein Entscheidungsprotokoll

```text
Hosting model decision for <offer or customer>       date: <date>     decided by: <names>

Model:              <shared | VPS | dedicated | containers | Kubernetes | PaaS | managed>
Operating system:   <name and version>, tier <1-4>
Panel:              <none | Plesk | other>, tier <1-4 or not placed>
PHP:                <version> web, <version> command line
Serving:            <Velocity | FrankenPHP | Apache + PHP-FPM | nginx + PHP-FPM | other>
Database:           <1. SQLite 3 | 2. PostgreSQL | 3. MongoDB | 4. MariaDB | 5. Oracle | 6. MySQL>
Customer access:    <panel | FTP | shell | none>
Isolation:          <one pool per site | one Velocity per site | one machine per customer>
Backups:            database + var/storage + settings/override + settings/siteaccess, restore tested on <date>
Risks accepted:     <for example a tier 3 system>
```

## Verwandte Seiten

- [Hosting-Modelle: Überblick](README.md)
- [Shared Hosting](shared-hosting.md), [VPS-Hosting](vps-hosting.md), [Dedizierte Server](dedizierte-server.md),
  [Container: Docker und Podman](container-docker-und-podman.md), [Kubernetes](kubernetes.md)
- [Hosting, Datenbank und Betriebssystem wählen](../../fuehrung/hosting-datenbank-und-betriebssystem-waehlen.md)
- [Hosting-Tarife gestalten](../geschaeft/hosting-tarife-gestalten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
