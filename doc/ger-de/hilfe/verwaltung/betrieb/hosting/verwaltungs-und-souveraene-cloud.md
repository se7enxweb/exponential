---
title: Hosting in der Behörden- und souveränen Cloud
audience: government, hosting, admin, project-manager
category: public-sector
order: 30
summary: Für öffentliche Stellen, die Exponential in einer Behörden-Cloud, einer souveränen Cloud oder auf virtuellen Maschinen eines nationalen Anbieters betreiben müssen oder wollen. Was die Software von einer solchen Plattform braucht, welche Hosting-Modelle die Quellen abdecken und was Sie den Anbieter fragen sollten.
keywords: Behörden-Cloud, souveräne Cloud, öffentliche Cloud, nationale Cloud, Cloud-Anbieter, virtuelle Maschinen, IaaS, PaaS, verwaltetes Hosting, Datenresidenz, Souveränität, Beschaffung, Cloud-Fragebogen, government cloud, sovereign cloud, public cloud, national cloud, cloud provider, virtual machines, managed hosting, data residency, sovereignty, procurement, cloud questionnaire
type: help
---
# Hosting in der Behörden- und souveränen Cloud

Diese Seite richtet sich an IT-Architektinnen und -Architekten sowie Projektleitungen in öffentlichen Stellen, deren
Websites in einer Behörden-Cloud, in einer nach nationalen Regeln angebotenen „souveränen“ Cloud oder auf den
virtuellen Maschinen eines zugelassenen Anbieters laufen müssen. Sie beantwortet, was eine solche Plattform für
Exponential bieten muss, welche Hosting-Modelle die Quellen des Projekts beschreiben und welche Fragen Sie dem Anbieter
vor der Unterschrift stellen sollten. Sie beschreibt die Software; welche Cloud Sie nutzen dürfen, entscheiden Ihre
eigenen Richtlinien.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Exponential braucht keine bestimmte Cloud](#exponential-braucht-keine-bestimmte-cloud)
2. [Welches Hosting-Modell passt](#welches-hosting-modell-passt)
3. [Was die Plattform bieten muss](#was-die-plattform-bieten-muss)
4. [Fragen an den Anbieter](#fragen-an-den-anbieter)
5. [Die Möglichkeit zum Wechsel erhalten](#die-möglichkeit-zum-wechsel-erhalten)
6. [Checkliste](#checkliste)

## Exponential braucht keine bestimmte Cloud

Exponential ist eine PHP-Anwendung. Sie nimmt keinen Kontakt zum Hersteller auf, braucht keinen Lizenzserver und hat
keinen eigenen Cloud-Dienst, von dem eine Website abhängt. Die Administration liefert ihre Skripte lokal aus: jQuery
und die anderen Skripte der Administration werden aus der Installation geladen, nicht aus einem CDN
(`ezjscore.ini [eZJSCore] LoadFromCDN=disabled`), und das Design admin4 lädt keine Webschriften oder Skripte von
anderen Hosts ([Datenschutzstandards](../../../../../eng-us/standards/privacy.md)). Virtuelle Maschinen in der Cloud sind
„gewöhnliche Linux-Maschinen, in der Stufe ihrer Distribution“
([Betriebssysteme für Hosts](../../../hosting/grundlagen/betriebssysteme-fuer-hoster.md)).

Einige Erweiterungen, die mit dem Code ausgeliefert werden, nehmen Kontakt zu Dritten auf, sobald Sie sie aktivieren
(Statistiken, Captchas, Anmeldung über soziale Netzwerke). Sie sind ausgeschaltet, bis eine Website sie einschaltet;
die Liste steht in den [Datenschutzstandards](../../../../../eng-us/standards/privacy.md). Prüfen Sie `ActiveExtensions[]` vor
dem Livegang.

## Welches Hosting-Modell passt

Der Bereich Hosting vergleicht die Modelle anhand dessen, was die Quellen sagen. Jedes davon gilt auch innerhalb einer
Behörden-Cloud:

| Modell | Wann es zu einer öffentlichen Stelle passt | Seite |
|---|---|---|
| Virtuelle Maschinen (IaaS) | die Standardwahl; Ihr Team betreibt PHP, die Datenbank und die Engine | [VPS-Hosting](../../../hosting/modelle/vps-hosting.md) |
| Container | Ihr Plattformteam standardisiert auf Docker oder Podman | [Container und Orchestrierung](container-und-orchestrierung.md) |
| Kubernetes | das verwaltete Kubernetes der Cloud ist der Standard Ihrer Organisation | [Kubernetes](../../../hosting/modelle/kubernetes.md) (Stufe 3) |
| Platform as a Service | der Anbieter betreibt PHP für Sie | [Platform as a Service](../../../hosting/modelle/platform-as-a-service.md) (Stufe 3) |
| Verwaltetes Hosting | ein externer Dienstleister betreibt die Website für Sie | [Verwaltetes Hosting](../../../hosting/modelle/managed-hosting.md) |

Eine einzelne virtuelle Maschine mit der Standarddatenbank (SQLite 3) ist der einfachste Aufbau. Mehrere Maschinen
brauchen einen Datenbankserver und gemeinsamen Speicher
([Übersicht zur Hochverfügbarkeit](../verfuegbarkeit/hochverfuegbarkeit-im-ueberblick.md)).

## Was die Plattform bieten muss

| Bedarf | Warum | Einzelheiten |
|---|---|---|
| PHP 8 (8.0 oder neuer, Velocity eingeschlossen), Kommandozeile und Web | die Anwendung und ihre Cronjobs | [PHP-Versionen und -Erweiterungen](../../../hosting/grundlagen/php-versionen-und-erweiterungen.md) |
| Dauerhafter Speicher für `var/` und `settings/override` | hochgeladene Dateien, Logs, der Audit-Trail, die SQLite-Datenbank | [Aufbau des Speichers](../../../hosting/betrieb/aufbau-des-speichers.md) |
| Eine Datenbank, wenn nicht SQLite | eine der unterstützten Engines; ein verwalteter Datenbankdienst ist in Ordnung, wenn er eine davon ist | [Datenbankwahl in der offiziellen Reihenfolge](datenbankwahl-in-der-offiziellen-reihenfolge.md) |
| Ein Zeitplaner | `runcronjobs.php` alle paar Minuten oder der eigene Zeitplaner von Velocity | [Cronjobs](../../../hosting/betrieb/cronjobs.md) |
| Ausgehende E-Mail | Passwort- und Benachrichtigungs-E-Mails | [E-Mail-Zustellung](../../../hosting/betrieb/mailzustellung.md) |
| Snapshots oder Sicherungsspeicher an einem zweiten Standort | Kontinuität | [Datensicherung und Wiederherstellung](../../../../../eng-us/help/government/operations/backups-and-recovery/README.md) |

Eine Plattform mit schreibgeschütztem oder flüchtigem Dateisystem braucht `var/` auf dauerhaftem Speicher; siehe
[Platform as a Service](../../../hosting/modelle/platform-as-a-service.md).

## Fragen an den Anbieter

Nehmen Sie diese in Ihre Beschaffungsunterlagen oder in den Sicherheitsfragebogen des Anbieters auf. Keine davon ist
spezifisch für Exponential; sie stellen sicher, dass die Plattform die an anderer Stelle in diesem Bereich
beschriebenen Pflichten tragen kann.

| Frage | Warum sie wichtig ist |
|---|---|
| In welchen Ländern und Rechenzentren befinden sich die Maschinen, der Speicher und die Datensicherungen? | wo Inhalte, Konten und Logs liegen (siehe [Speicherort der Daten und was wo gespeichert wird](datenstandort-und-was-wo-gespeichert-wird.md)) |
| Wer beim Anbieter kann die Festplatten und Snapshots lesen? | `settings/override/` enthält Zugangsdaten der Datenbank und die Audit-Schlüssel |
| Welche PHP-Versionen und -Erweiterungen können wir installieren, und wie schnell werden Sicherheitsupdates eingespielt? | die Software braucht PHP 8 mit festgelegten Erweiterungen |
| Können wir einen langlebigen Prozess (Velocity) betreiben und Ports binden? | entscheidet zwischen Velocity und einem herkömmlichen Webserver ([Wahl des Webservers](wahl-des-webservers.md)) |
| Wird ein verwaltetes PostgreSQL, MariaDB oder MySQL angeboten, in welchen Versionen? | die unterstützten Engines und ihre Mindestversionen stehen in [Datenbankunterstützung](../../../datenbankunterstuetzung.md) |
| Wie werden Snapshots aufbewahrt, wie lange und an einem zweiten Standort? | Ihre Entscheidungen zur Wiederherstellung ([RPO und RTO als Entscheidungen](../../../../../eng-us/help/government/operations/backups-and-recovery/rpo-and-rto-as-decisions.md)) |
| Können wir am Vertragsende alles (Festplatten, Datenbank-Dumps) in Standardformaten exportieren? | Ausstiegsstrategie |

## Die Möglichkeit zum Wechsel erhalten

Exponential speichert seine Daten in einer Standarddatenbank und in einfachen Dateien; eine Website umzuziehen ist
daher eine Kopie der Datenbank und der Dateien plus ein frischer Checkout des Codes
([Eine Website zwischen Hosts umziehen](../../../hosting/migrationen/eine-website-zwischen-hostern-umziehen.md)).
Sorgen Sie dafür, dass das so bleibt:

- vermeiden Sie anbieterspezifische Dienste, die die Software nicht braucht;
- halten Sie Code und Einstellungen in Ihrer eigenen Versionsverwaltung ([Konfiguration unter Versionsverwaltung](konfiguration-unter-versionskontrolle.md));
- testen Sie mindestens einmal eine Wiederherstellung Ihrer Datensicherungen auf einer Maschine außerhalb des Anbieters.

## Checkliste

- [ ] Die Antworten des Anbieters auf die Fragen oben liegen vor.
- [ ] Dauerhafter Speicher enthält `var/` und `settings/override`.
- [ ] Aktivierte Erweiterungen, die Kontakt zu Dritten aufnehmen, sind bekannt und begründet.
- [ ] Datensicherungen werden an einem zweiten Standort aufbewahrt, den Sie kontrollieren.
- [ ] Ein Export und eine Wiederherstellung außerhalb des Anbieters wurden getestet.

## Verwandte Seiten

- [Hosting](README.md): Übersicht dieses Ordners
- [Hosting im eigenen Rechenzentrum](hosting-im-eigenen-rechenzentrum.md)
- [Ein Hosting-Modell wählen](../../../hosting/modelle/ein-hosting-modell-waehlen.md)
- [Compliance und DSGVO für Hoster](../../../hosting/sicherheit/compliance-und-dsgvo.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
