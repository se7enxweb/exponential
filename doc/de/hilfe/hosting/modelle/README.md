---
title: Hosting-Modelle: Überblick
audience: hosting
category: hosting
order: 10
summary: Der Überblick über den Bereich Hosting-Modelle - welche Arten, Exponential zu hosten, die Quellen abdecken (Shared, VPS, dediziert, Container, Kubernetes, Platform as a Service, verwaltet), und eine Zeile zu jeder Seite.
keywords: Hosting-Modelle, Shared Hosting, VPS, virtueller Server, dedizierter Server, Container, Docker, Podman, Kubernetes, Platform as a Service, PaaS, Managed Hosting, Überblick, hosting models, shared hosting, virtual server, dedicated server, containers, platform as a service, managed hosting, overview
type: help
---
# Hosting-Modelle: Überblick

Dieser Bereich ist für Produktmanager und Systemadministratoren bei Hosting-Anbietern, die entscheiden, **in welcher Form**
sie Exponential anbieten: auf einem geteilten Server, auf einer virtuellen oder dedizierten Maschine pro Kunde, in
Containern, auf einer Platform as a Service oder als verwalteten Dienst. Er beantwortet, was jedes Modell für eine
Exponential-Website bedeutet, was die eigenen Quellen des Projekts dazu sagen und welche Fragen zu klären sind, bevor ein
Modell zum Produkt wird.

Zurück zu [Hosting-Anbieter: hier beginnen](../hosting-anbieter-hier-beginnen.md).

## Inhalt

1. [Die Seiten dieses Bereichs](#die-seiten-dieses-bereichs)
2. [Was in jedem Modell gleich ist](#was-in-jedem-modell-gleich-ist)
3. [Was sich zwischen den Modellen ändert](#was-sich-zwischen-den-modellen-ändert)
4. [Wo Sie weiterlesen](#wo-sie-weiterlesen)

## Die Seiten dieses Bereichs

| Seite | Was sie beantwortet |
|---|---|
| [Ein Hosting-Modell wählen](ein-hosting-modell-waehlen.md) | Eine Tabelle, die Shared, VPS, dediziert, Container und Kubernetes vergleicht, mit den Entscheidungen dahinter |
| [Shared Hosting](shared-hosting.md) | Viele Kunden-Websites auf einer Maschine betreiben, ohne Shell-Zugang oder lang laufende Prozesse für den Kunden |
| [VPS-Hosting](vps-hosting.md) | Eine virtuelle Maschine pro Kunde oder pro kleiner Gruppe von Websites, mit root-Zugang |
| [Dedizierte Server](dedizierte-server.md) | Eine ganze physische Maschine für einen Kunden oder eine große Website, und wann sich das lohnt |
| [Container: Docker und Podman](container-docker-und-podman.md) | Images, Volumes und die Regel eines einzigen Schreibers für SQLite |
| [Kubernetes](kubernetes.md) | Mehrere Replikate, gemeinsamer Speicher, Sitzungen und ein Datenbankserver |
| [Platform as a Service](platform-as-a-service.md) | Angebote mit fester Laufzeitumgebung, und was ein schreibgeschütztes oder flüchtiges Dateisystem bedeutet |
| [Managed Hosting](managed-hosting.md) | Updates, Backups, Überwachung und Sicherheit für den Kunden übernehmen |

## Was in jedem Modell gleich ist

Welches Modell auch immer: Eine Exponential-Website braucht dieselben wenigen Dinge
([Kapitel 2: Anforderungen](../../../../en/us/install/02-requirements.md)):

- **PHP 8.0 oder später** (8.1 oder später für Exponential Velocity) mit den erforderlichen Erweiterungen;
- **eine Datenbank** der sechs unterstützten, in der offiziellen Reihenfolge: 1. SQLite 3 (der Standard) · 2. PostgreSQL ·
  3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server ist derzeit bekanntermaßen mit zusätzlicher Arbeit
  möglich; in 6.0.x hat es keinen Treiber ([Datenbankunterstützung](../../../../en/us/help/database-support.md));
- **beschreibbaren Speicher** für `var/` und die Verzeichnisse unter `settings/`, die das Setup schreibt;
- **einen Weg, HTTP auszuliefern**: Exponential Velocity, FrankenPHP über Velocity oder einen klassischen Webserver wie
  Apache oder nginx mit PHP-FPM ([Kapitel 8](../../../../en/us/install/08-serving-the-site.md));
- **Cron** oder einen anderen Planer für `runcronjobs.php`
  ([Kapitel 10, Cronjobs](../../../../en/us/install/10-after-installing.md#103-cronjobs)).

Ein vollständiges Backup besteht in jedem Modell aus der Datenbank **und** den Dateien `var/storage`, `settings/override`
und `settings/siteaccess` ([Backup und Wiederherstellung](../../../../en/us/help/database-support.md#backup-and-restore)).

## Was sich zwischen den Modellen ändert

| Frage | Warum sie für Exponential zählt |
|---|---|
| Darf der Kunde einen lang laufenden Prozess ausführen? | Velocity und FrankenPHP brauchen einen; Apache oder nginx mit PHP-FPM nicht. Shared Hosting erlaubt es meist nicht (`README.md`). |
| Gibt es Shell-Zugang? | Ohne ihn ist der Setup-Assistent im Browser der Installer ([Kapitel 4](../../../../en/us/install/04-choosing-an-install-method.md#45-which-method-for-which-situation)). |
| Ist das Dateisystem persistent? | `var/` enthält Uploads, Caches, Logs und, mit SQLite, die Datenbank selbst. |
| Liefern mehrere Maschinen eine Website aus? | Dann scheidet SQLite aus, und ein Datenbankserver, gemeinsamer Speicher für `var/storage` und gemeinsame Sitzungen sind nötig ([Container, Cloud und Hosting-Panels](../../../../en/us/help/os-support.md#containers-cloud-and-hosting-panels)). |
| Müssen Kunden voneinander abgeschottet sein? | Ein PHP-FPM-Pool oder eine Velocity-Instanz pro Website, jeweils als eigener Benutzer des Betriebssystems ([Kapitel 8](../../../../en/us/install/08-serving-the-site.md#81-the-choice-in-one-table)). |
| Wer führt Updates und Backups aus? | Sie, der Kunde oder beide; das macht aus Hosting Managed Hosting. |

> **Hinweis**
> Die Quellen ordnen die Hosting-Modelle über das Betriebssystem und das Panel in Stufen ein, nicht über das Modell
> selbst: Virtuelle Maschinen in der Cloud sind gewöhnliche Linux-Maschinen, Stufe 2 oder 3 nach ihrer Distribution;
> Kubernetes und Angebote als Platform as a Service sind Stufe 3, eine begründete Erwartung, kein Testergebnis
> ([Die vier Stufen](../../../../en/us/help/os-support.md#the-four-tiers)).

## Wo Sie weiterlesen

- [Grundlagen des Hostings](../grundlagen/README.md): Anforderungen, PHP, die Datenbanken in Reihenfolge, Berechtigungen,
  Dimensionierung.
- [Control Panels](../control-panels/README.md): Plesk, cPanel, DirectAdmin, ISPConfig, Webmin und die Aufgaben im Panel.
- [Webserver und Engines](../server/README.md): Velocity, FrankenPHP, Apache, nginx und andere.
- [Sicherheit](../sicherheit/README.md), besonders [Trennung von Mandanten](../sicherheit/trennung-von-mandanten.md).

## Verwandte Seiten

- [Hosting-Anbieter: hier beginnen](../hosting-anbieter-hier-beginnen.md)
- [Hosting, Datenbank und Betriebssystem wählen](../../../../en/us/help/leadership/choosing-hosting-database-and-os.md): dieselbe
  Entscheidung aus Sicht der Projektleitung.
- [Betriebssystemunterstützung](../../../../en/us/help/os-support.md) und [Datenbankunterstützung](../../../../en/us/help/database-support.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
