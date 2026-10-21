---
title: Exponential im öffentlichen Sektor hosten
audience: government, hosting, admin, project-manager
category: public-sector
order: 10
summary: Übersicht der Hosting-Seiten für öffentliche Stellen: wo und wie Exponential betrieben wird (eigenes Rechenzentrum, Behörden- oder souveräne Cloud, Container), welche Datenbank und welcher Webserver, getrennte Umgebungen und Administration, Speicherort der Daten, Härtung, Konfigurationskontrolle und eine Entscheidungsdokumentation.
keywords: Hosting, öffentlicher Sektor, Behörden, eigenes Rechenzentrum, souveräne Cloud, Container, Datenbank, Webserver, Velocity, Umgebungen, Siteaccess, Speicherort der Daten, Härtung, Versionsverwaltung, Entscheidungsdokumentation, hosting, public sector, government, on-premise, sovereign cloud, containers, database, web server, environments, siteaccess, data location, hardening, version control, decision record
type: help
---
# Exponential im öffentlichen Sektor hosten

Dieser Ordner richtet sich an interne IT-Teams, Architektinnen und Architekten sowie Projektleitungen in öffentlichen
Stellen, die entscheiden, wo und wie eine Exponential-Website läuft. Er behandelt das Hosting-Modell, die Datenbank und
den Webserver, getrennte Umgebungen, das Fernhalten der Administration vom öffentlichen Internet, den Speicherort der
Daten, die Härtung, die Konfiguration unter Versionsverwaltung und eine Entscheidungsdokumentation, die Sie bei Ihrem
Änderungsbeirat einreichen. Die Schritt-für-Schritt-Seiten zu den Servern wurden für Hoster geschrieben und stehen im
[Bereich Hosting](../../../hosting/hosting-anbieter-hier-beginnen.md); die Seiten hier verlinken darauf und ergänzen,
was für eine öffentliche Stelle anders ist: interne Teams, Behörden-Clouds, Änderungsbeiräte, Vorgaben der Beschaffung
und Pflichten zur Kontinuität.

## Seiten

| Seite | Beantwortet |
|---|---|
| [Hosting im eigenen Rechenzentrum](hosting-im-eigenen-rechenzentrum.md) | Was eine Website von Ihren eigenen Servern braucht, welche Betriebssysteme getestet sind und wer was übernimmt. |
| [Hosting in der Behörden- und souveränen Cloud](verwaltungs-und-souveraene-cloud.md) | Was eine Behörden- oder souveräne Cloud bieten muss und was Sie den Anbieter fragen sollten. |
| [Container und Orchestrierung](container-und-orchestrierung.md) | Wie Sie es in Docker, Podman oder Kubernetes betreiben und warum die Produktion ein festes Image-Tag festlegt. |
| [Eine Datenbank in der offiziellen Reihenfolge wählen](datenbankwahl-in-der-offiziellen-reihenfolge.md) | Die sechs unterstützten Datenbanken und wie Sie eine davon Ihrem Datenbankstandard zuordnen. |
| [Webserver für Websites des öffentlichen Sektors](wahl-des-webservers.md) | Velocity, FrankenPHP oder Apache und nginx mit PHP-FPM sowie Listen zugelassener Software. |
| [Velocity für Teams im öffentlichen Sektor](velocity-fuer-teams-im-oeffentlichen-sektor.md) | Starten, Neustarten, Bereitstellen, Protokollieren und Bewerten der empfohlenen Engine. |
| [Umgebungen für Entwicklung, Test und Produktion](umgebungen-entwicklung-test-produktion.md) | Wie `EXP_ENV` jeder Maschine ihre eigenen Einstellungen gibt und Testkopien still hält. |
| [Die Administration von der öffentlichen Website trennen](administrations-und-oeffentliche-siteaccesses-trennen.md) | Wie Sie der Administration einen eigenen Hostnamen, eine Netzwerkbeschränkung und eigene Anmelderegeln geben. |
| [Speicherort der Daten und was wo gespeichert wird](datenstandort-und-was-wo-gespeichert-wird.md) | Jeder Ort, an dem Daten liegen, welcher personenbezogene Daten enthält und was den Server verlässt. |
| [Den Host härten](den-host-haerten.md) | Die Ebenen der Härtung, die Prüfungen, die sie belegen, und wie Sie sie einer Grundlinie zuordnen. |
| [Konfiguration unter Versionsverwaltung](konfiguration-unter-versionskontrolle.md) | Code und Einstellungen in Git halten und die Administration mit `ManagedByDeployment` sperren. |
| [Eine Entscheidungsdokumentation zum Hosting](hosting-entscheidungsprotokoll.md) | Eine Vorlage, um jede Hosting-Entscheidung für einen Änderungsbeirat oder eine Prüfung zu dokumentieren. |

## Lesereihenfolge

1. Entscheiden Sie das Modell: [Hosting im eigenen Rechenzentrum](hosting-im-eigenen-rechenzentrum.md) oder [Hosting in der Behörden- und souveränen Cloud](verwaltungs-und-souveraene-cloud.md), danach [Container und Orchestrierung](container-und-orchestrierung.md), wenn Ihre Plattform sie nutzt.
2. Wählen Sie die Komponenten: [Datenbank](datenbankwahl-in-der-offiziellen-reihenfolge.md), [Webserver](wahl-des-webservers.md) und, falls Sie sich dafür entscheiden, [Velocity](velocity-fuer-teams-im-oeffentlichen-sektor.md).
3. Richten Sie die Website sicher ein: [Umgebungen](umgebungen-entwicklung-test-produktion.md), [getrennte Administration](administrations-und-oeffentliche-siteaccesses-trennen.md), [Härtung](den-host-haerten.md), [Konfigurationskontrolle](konfiguration-unter-versionskontrolle.md).
4. Dokumentieren Sie es: [Speicherort der Daten](datenstandort-und-was-wo-gespeichert-wird.md) und die [Entscheidungsdokumentation](hosting-entscheidungsprotokoll.md).

Fahren Sie danach mit [Verfügbarkeit](../verfuegbarkeit/README.md) und [Datensicherung und Wiederherstellung](../../../../../eng-us/help/government/operations/backups-and-recovery/README.md) fort.

## Verwandte Seiten

- Eine Ebene höher: [Betrieb](../README.md)
- Anfang des Bereichs: [Behörden: hier beginnen](../../verwaltungen-hier-beginnen.md)
- Für Hoster: [Hoster: hier beginnen](../../../hosting/hosting-anbieter-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
