---
title: "Hosting und Betrieb: Kursüberblick"
audience: trainer, hosting, admin
category: training
order: 1
summary: Der Kurs für Systemadministratoren und Hosting-Mitarbeitende, die Exponential-Websites betreiben, die Website ausliefern, Velocity, Datenbanken, Bereitstellung und Cron, Monitoring und Logs, Sicherungen und Wiederherstellung, Leistung und Skalierung. Sieben Module, 28 Lektionen, etwa 20 Stunden Lektionen.
keywords: hosting und betrieb, kursüberblick, lesepfad, hosting, php-fpm, velocity, datenbanken, bereitstellung, cron, monitoring, logs, sicherungen, benchmarking, valkey, clustering, hosting and operations, course overview, reading path, databases, deploying, backups
type: help
---
# Hosting und Betrieb: Kursüberblick

Dieser Kurs richtet sich an die Menschen, die Exponential-Websites auf Servern am Laufen halten: Systemadministratoren,
Mitarbeitende von Hosting-Unternehmen und die Betriebsseite einer Agentur. Er begleitet eine Website durch ihr Leben
auf einem Server: wie Anfragen beantwortet werden, der Webserver Velocity, die Datenbank, das Bereitstellen von
Änderungen und das Ausführen von Cron, das Beobachten der Website und ihrer Logs, ihre Sicherung und Wiederherstellung
und wie sie schnell und wachstumsfähig wird.

## Für wen er gedacht ist

- Systemadministratoren, die eine oder mehrere Exponential-Websites hosten.
- Support- und Betriebsmitarbeitende von Hosting-Unternehmen.
- Entwickler, die für ihre eigenen Produktivserver verantwortlich sind.

**Ergebnis:** Nach dem Kurs können die Teilnehmenden eine Website mit PHP-FPM oder Velocity ausliefern, ihre Datenbank
wählen und sichern, eine Änderung bereitstellen, Cron ausführen, die Website überwachen, sie aus einer Sicherung
wiederherstellen und ihre Leistung messen und verbessern.

## Form und Dauer

| | |
|---|---|
| Dauer | 28 Lektionen, etwa 20 Stunden Lektionen (mit Pausen drei bis vier Tage) |
| Form | Präsenz oder online; praxisorientiert im Terminal |
| Niveau | Fortgeschrittene bis Experten |
| Voraussetzungen | [Website-Administration](../website-administratoren/README.md) (mindestens die Module zu Caches, Cronjobs, Logs und Upgrades); Grundlagen der Linux-Kommandozeile, eines Webservers und von PHP |
| Übungsumgebung | Ein Linux-Rechner oder eine virtuelle Maschine pro Person mit root-Zugang, PHP 8.0 oder neuer, Composer 2 und Git; eine Schulungsinstallation, angelegt mit `php bin/php/console exp:install` und ausgeliefert mit `exp:velocity start`; für die Lektionen 1.2, 1.3 und 3.3 außerdem Apache oder nginx mit PHP-FPM und ein MariaDB- oder PostgreSQL-Server |

## Module

| Modul | Lektionen | Dauer | Ergebnis |
|---|---|---|---|
| [1. Die Website ausliefern](modul-01-die-website-ausliefern/README.md) | 4 | 2 Std. 45 Min. | Liefert eine Website mit Apache oder nginx und PHP-FPM aus, stimmt Pools ab, prüft OPcache und APCu |
| [2. Velocity](modul-02-velocity/README.md) | 4 | 3 Std. | Startet Velocity und hält ihn am Laufen, bemisst seine Pools, nutzt den Antwort-Cache und HTTPS |
| [3. Datenbanken](modul-03-datenbanken/README.md) | 4 | 2 Std. 55 Min. | Wählt eine Datenbank, betreibt SQLite gut, richtet einen Datenbankserver ein, sichert und verschiebt eine Datenbank |
| [4. Bereitstellung und Cron](modul-04-bereitstellung-und-cron/README.md) | 4 | 2 Std. 55 Min. | Stellt eine Änderung bereit, richtet Cron ein, nutzt die Cronjob-Konsole, plant ein Upgrade |
| [5. Monitoring und Logs](modul-05-monitoring-und-logs/README.md) | 4 | 2 Std. 55 Min. | Überwacht eine Website, liest und rotiert Logs, arbeitet die Runbooks für ausgefallene und langsame Websites ab |
| [6. Sicherungen und Wiederherstellung](modul-06-sicherungen-und-wiederherstellung/README.md) | 4 | 2 Std. 45 Min. | Sichert das Richtige, schreibt eine Aufbewahrungsrichtlinie, stellt wieder her und testet eine Wiederherstellung |
| [7. Leistung und Skalierung](modul-07-leistung-und-skalierung/README.md) | 4 | 2 Std. 55 Min. | Misst eine Website, nutzt Cache-Schichten und einen Valkey-Cache, entwirft eine Website auf mehreren Webknoten |

## Lesepfad

Für Trainer, die diesen Kurs vorbereiten, in dieser Reihenfolge:

1. [Trainer: hier beginnen](../trainer-hier-beginnen.md) und der [Kurskatalog](../kurskatalog.md).
2. [Hosting-Unternehmen: hier beginnen](../../hosting/hosting-anbieter-hier-beginnen.md) und
   [Was das Hosting von Exponential umfasst](../../hosting/grundlagen/was-das-hosting-von-exponential-umfasst.md).
3. [Anforderungen auf einen Blick](../../hosting/grundlagen/anforderungen-auf-einen-blick.md) und
   [PHP-Einstellungen für Exponential](../../hosting/grundlagen/php-einstellungen-fuer-exponential.md).
4. Die Kapitel [7](../../../installation/07-installation-ueber-die-konsole.md), [8](../../../installation/08-die-website-ausliefern.md),
   [9](../../../installation/09-datenbanken.md) und [11](../../../installation/11-aktualisieren.md) des Installationsbuchs.
5. [Velocity für Hoster](../../hosting/server/velocity-fuer-hoster.md) und
   [Velocity-Worker und -Pools](../../hosting/server/velocity-worker-und-pools.md).
6. [Datenbankunterstützung](../../datenbankunterstuetzung.md).
7. [Bereitstellung](../../../anleitungen/bereitstellung.md) und [Cronjobs](../../../anleitungen/cronjobs.md).
8. [Überblick über das Monitoring](../../hosting/betrieb/ueberblick-ueber-die-ueberwachung.md) und die
   [Runbooks](../../hosting/runbooks/README.md).
9. [Sicherungen: was gesichert werden muss](../../hosting/betrieb/backups-was-gesichert-werden-muss.md) und
   [Wiederherstellung und Wiederherstellungstests](../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md).
10. [Cache-Schichten](../../hosting/leistung/cache-schichten.md) und
    [Benchmarking](../../../anleitungen/benchmarking.md).
11. Die Module 1 bis 7 dieses Kurses, der Reihe nach. Proben Sie jede Übung auf einem Rechner der Übungsumgebung, der
    so aufgebaut ist wie die Rechner der Teilnehmenden; bereiten Sie die Fehler für Lektion 5.4 vorab vor.

## Vorgeschlagene Zeitpläne

| Zeitplan | Plan |
|---|---|
| Vier Tage | Tag 1: Module 1 und 2. Tag 2: Module 3 und 4. Tag 3: Module 5 und 6. Tag 4 vormittags: Modul 7 |
| Drei Tage, intensiv | Tag 1: Module 1, 2 und Lektionen 3.1 bis 3.2. Tag 2: Lektionen 3.3 bis 3.4, Module 4 und 5. Tag 3: Module 6 und 7 |
| Zweieinhalb Tage, nur Velocity-Websites | Lektionen 1.2 und 1.3 entfallen. Tag 1: Lektionen 1.1 und 1.4, Module 2 und 3. Tag 2: Module 4, 5 und 6. Tag 3 vormittags: Modul 7 |
| Sieben halbe Tage | Ein Modul pro halbem Tag, der Reihe nach |
| Selbststudium | Ein Modul pro Woche, mit den Übungen auf Ihrer eigenen virtuellen Maschine |

## Teilnehmende beurteilen

Jede Lektion hat ein Quiz und ein erwartetes Ergebnis, das der Trainer prüft. Als abschließende praktische Aufgabe
beschädigt der Trainer die Website der Übungsumgebung einer Person auf vorbereitete Weise (ein gestoppter Server, eine
volle Festplatte, ein fehlender Cron-Eintrag oder eine verlorene Datenbankdatei), und die Person stellt den Dienst mit
den Runbooks der Module 5 und 6 wieder her und schreibt eine kurze Störungsnotiz. Das ist die eigene Prüfung des
Trainers, keine offizielle Zertifizierung; die Zertifizierungen des Partnerprogramms sind davon getrennt
([Zertifizierungen](../../partner/zertifizierungen.md)). Der Kurs Prüfungen und praktische Übungen im Katalog enthält
weiteres Material.

## Verwandte Seiten

- [Trainer: hier beginnen](../trainer-hier-beginnen.md)
- [Kurskatalog](../kurskatalog.md)
- [Hosting-Unternehmen: hier beginnen](../../hosting/hosting-anbieter-hier-beginnen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
