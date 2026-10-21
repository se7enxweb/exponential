---
title: Container und Orchestrierung
audience: government, hosting, developer
category: public-sector
order: 40
summary: Für Plattformteams in öffentlichen Stellen, die Exponential in Docker, Podman oder Kubernetes betreiben. Was die Quellen über Container sagen, was auf einem dauerhaften Volume liegen muss, die Regel eines einzigen Schreibers für SQLite und wie eine Container-Plattform zu einem Änderungsbeirat passt.
keywords: Container, Docker, Podman, Kubernetes, Orchestrierung, Images, ghcr.io, Volumes, dauerhafter Speicher, SQLite ein Schreiber, Replikate, Plattformteam, Behörden-Cloud, containers, orchestration, images, volumes, persistent storage, SQLite one writer, replicas, platform team, government cloud
type: help
---
# Container und Orchestrierung

Diese Seite richtet sich an Plattform- und DevOps-Teams in öffentlichen Stellen, die ihre Anwendungen in Containern
betreiben. Sie beantwortet, was die Quellen von Exponential über Docker, Podman und Kubernetes sagen, was außerhalb des
Images liegen muss, wie viele Container in die Standarddatenbank schreiben dürfen und wie Image-Tags und die Freigabe
von Änderungen zusammenpassen. Die technischen Einzelheiten stehen auf den Hosting-Seiten
[Container: Docker und Podman](../../../hosting/modelle/container-docker-und-podman.md) und
[Kubernetes](../../../hosting/modelle/kubernetes.md); diese Seite fasst sie zusammen und ergänzt die Sicht des
öffentlichen Sektors.

## Inhalt

1. [Was die Quellen sagen](#was-die-quellen-sagen)
2. [Images und festgelegte Tags](#images-und-festgelegte-tags)
3. [Was dauerhaft bleiben muss](#was-dauerhaft-bleiben-muss)
4. [Ein Container oder mehrere](#ein-container-oder-mehrere)
5. [Container und Ihr Änderungsprozess](#container-und-ihr-änderungsprozess)
6. [Checkliste](#checkliste)

## Was die Quellen sagen

- **Docker und Podman liegen in Stufe 2**, bekanntermaßen funktionsfähig. Velocity veröffentlicht Images unter
  `ghcr.io/se7enxweb/exponential-velocity` für amd64 und arm64, eines pro PHP-Version und Variante
  ([Unterstützte Betriebssysteme](../../../betriebssystemunterstuetzung.md)).
- **Kubernetes liegt in Stufe 3**, voraussichtlich funktionsfähig: eine begründete Erwartung, kein Testergebnis. Das
  Projekt bittet um Nachricht, wenn jemand es in der Produktion betreibt ([Kubernetes](../../../hosting/modelle/kubernetes.md)).
- Die Quellen liefern **keine Kubernetes-Manifeste** mit. Ihr Plattformteam schreibt sie; die Kubernetes-Seite listet
  die Entscheidungen auf, die darin festgehalten werden müssen.
- `exp:install` ist die Installationsmethode für Container-Images und automatisierte Testumgebungen
  ([Eine Installationsmethode wählen](../../../../installation/04-installationsweg-waehlen.md)).

## Images und festgelegte Tags

Die Images von Velocity sind nach Release, PHP-Version und Variante getaggt
([Velocity-Pakete und -Binärdateien](../../../../../eng-us/features/6.0/velocity-packages-and-binaries.md)):

```text
ghcr.io/se7enxweb/exponential-velocity:<release>-php<version>-<variant>   fixed
ghcr.io/se7enxweb/exponential-velocity:php<version>-<variant>             moves with each release
ghcr.io/se7enxweb/exponential-velocity:latest                             newest PHP, standard variant
```

Der Rat des Projekts lautet: „Legen Sie in der Produktion ein festes Tag fest.“ Für eine öffentliche Stelle ist das
auch eine Frage der Steuerung: Ein Änderungsbeirat gibt eine bestimmte Version frei, und nur ein festes Tag garantiert,
dass das freigegebene Image auch das ist, das läuft. Halten Sie das Tag im Änderungsantrag fest.

Welche Basis Sie auch verwenden, führen Sie die Prüfung der Erweiterungen als Schritt der Pipeline im gebauten Image aus:

```bash
php bin/php/console exp:velocity ext check
```

## Was dauerhaft bleiben muss

| Pfad | Enthält | Wo |
|---|---|---|
| `var/` | hochgeladene Dateien (`var/storage`), Caches, Logs, den Audit-Trail, die SQLite-Datenbank | dauerhaftes Volume |
| `settings/override` | die Einstellungen der Installation, Zugangsdaten der Datenbank, Audit-Schlüssel | dauerhaftes Volume oder Secrets |
| `settings/siteaccess` | Siteaccess-Einstellungen, die das Installationsprogramm schreibt | dauerhaftes Volume |
| `kernel/`, `lib/`, `vendor/`, `extension/`, `design/` | Code | im Image |

Die Dateien auf dem Volume müssen dem Benutzer gehören, unter dem das PHP des Containers läuft; legen Sie die
Benutzer-ID einmal für Image und Volume fest
([Container: Docker und Podman](../../../hosting/modelle/container-docker-und-podman.md)). Halten Sie Zugangsdaten aus
dem Image selbst heraus.

## Ein Container oder mehrere

| | Ein Container | Mehrere Replikate |
|---|---|---|
| Datenbank | jede unterstützte Engine, SQLite 3 eingeschlossen | ein Datenbankserver, an erster Stelle PostgreSQL |
| Hochgeladene Dateien | ein Volume | gemeinsamer Speicher für `var/storage` |
| Sitzungen | die Standarddateien funktionieren | gemeinsamer Sitzungsspeicher |
| Cron | im Container oder als geplanter Job | genau ein Zeitplaner |

SQLite „funktioniert in einem Container, wenn ein Container die Datenbank schreibt“: Sein WAL-Modus braucht
gemeinsamen Arbeitsspeicher auf einem Host, daher können mehrere Replikate keine gemeinsame Datenbankdatei über ein
Netzwerk-Volume nutzen. Der Schritt von einem Replikat zu mehreren ist ein eigenes Projekt: siehe
[Übersicht zur Hochverfügbarkeit](../verfuegbarkeit/hochverfuegbarkeit-im-ueberblick.md) und
[Mehrere Webserver hinter einem Load Balancer](../verfuegbarkeit/mehrere-webserver-hinter-einem-load-balancer.md).

## Container und Ihr Änderungsprozess

Container erleichtern manche Pflichten des öffentlichen Sektors, wenn Sie sie dafür nutzen:

- **Reproduzierbare Builds.** Ein festgelegtes Basis-Image, eine `composer.lock` und Ihre eigenen Erweiterungen in der
  Versionsverwaltung ergeben jedes Mal dasselbe Image. Das ist der Nachweis, den ein Änderungsbeirat verlangt.
- **Staging entspricht der Produktion.** Betreiben Sie das freigegebene Image zuerst in einer Testumgebung, mit
  Testeinstellungen, die über `EXP_ENV` ausgewählt werden
  ([Umgebungen für Entwicklung, Test und Produktion](umgebungen-entwicklung-test-produktion.md)).
- **Keine Änderungen in laufenden Containern.** Schalten Sie `ManagedByDeployment` ein, damit die Administration keine
  Einstellungen oder Templates in einen Container schreiben kann, der ersetzt werden wird
  ([Konfiguration unter Versionsverwaltung](konfiguration-unter-versionskontrolle.md)).
- **Datensicherungen sind weiterhin nötig.** Ein Snapshot eines Volumes ist keine getestete Datensicherung; folgen Sie
  [Was zu sichern ist](../datensicherung-und-wiederherstellung/was-gesichert-werden-muss.md).

## Checkliste

- [ ] Die Produktion betreibt ein festes Image-Tag, das im Änderungsantrag festgehalten ist.
- [ ] `exp:velocity ext check` läuft in der Pipeline des Images.
- [ ] `var/`, `settings/override` und `settings/siteaccess` sind dauerhaft; Zugangsdaten liegen nicht im Image.
- [ ] Nur ein Replikat schreibt in eine SQLite-Datenbank; mehrere Replikate nutzen einen Datenbankserver.
- [ ] Ein Zeitplaner führt die Cronjobs aus.
- [ ] `ManagedByDeployment` ist im Siteaccess der Administration eingeschaltet.

## Verwandte Seiten

- [Hosting](README.md): Übersicht dieses Ordners
- [Container: Docker und Podman](../../../hosting/modelle/container-docker-und-podman.md), [Kubernetes](../../../hosting/modelle/kubernetes.md)
- [Hosting in der Behörden- und souveränen Cloud](verwaltungs-und-souveraene-cloud.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
