---
title: Verwaltungscloud und Hostingmodelle
audience: government, admin, project-manager
category: public-sector
order: 40
summary: Für welche Hostingmodelle Exponential dokumentiert ist (Shared Hosting, VPS, dedizierte Server, Container, Kubernetes, Platform as a Service, Managed Hosting), mit den Stufen des Projekts, und die Fragen, die eine öffentliche Stelle stellt, bevor sie es in einer Verwaltungscloud oder einer kommerziellen Cloud betreibt.
keywords: Verwaltungscloud, Hostingmodell, Cloud, VPS, dedizierter Server, Container, Docker, Podman, Kubernetes, PaaS, Managed Hosting, Stufen, Souveränität, government cloud, hosting model, dedicated server, managed hosting, tiers, sovereignty
type: help
---
# Verwaltungscloud und Hostingmodelle

Diese Seite richtet sich an IT-Verantwortliche und Architekten, die entscheiden, wo Exponential läuft: in einer
Verwaltungscloud, in einer Region einer kommerziellen Cloud oder auf gemieteten Servern. Exponential hängt von keinem
einzelnen Anbieter ab; die Hilfe zum Hosting beschreibt jedes Modell und bewertet es nach der eigenen Stufenskala des
Projekts ([Ein Hostingmodell wählen](../../hosting/modelle/ein-hosting-modell-waehlen.md)).

## Inhalt

1. [Die Stufen des Projekts](#die-stufen-des-projekts)
2. [Die Modelle](#die-modelle)
3. [Fragen an ein Cloud-Angebot](#fragen-an-ein-cloud-angebot)
4. [Mehrere Webknoten](#mehrere-webknoten)
5. [Entscheiden und dokumentieren](#entscheiden-und-dokumentieren)

## Die Stufen des Projekts

Die Hilfe zum Hosting verwendet vier Stufen: 1 hier getestet, 2 bekanntermaßen funktionsfähig, 3 voraussichtlich
funktionsfähig, 4 heute nicht möglich. Ein Eintrag der Stufe 3 ist „eine begründete Erwartung, kein Testergebnis“
([Die vier Stufen](../../betriebssystemunterstuetzung.md#the-four-tiers)). Für eine öffentliche Stelle heißt ein Modell der Stufe 3:
Planen Sie eine Testinstallation und eine Abnahmeprüfung, bevor Sie sich festlegen.

## Die Modelle

| Modell | Seite | Hinweis für öffentliche Stellen |
|---|---|---|
| Shared Hosting | [Shared Hosting](../../hosting/modelle/shared-hosting.md) | für amtliche Websites selten geeignet: wenig Isolation und Kontrolle |
| Virtueller privater Server | [VPS-Hosting](../../hosting/modelle/vps-hosting.md) | üblich für eine einzelne Website; Sie oder der Anbieter betreiben das Betriebssystem |
| Dedizierter Server | [Dedizierte Server](../../hosting/modelle/dedizierte-server.md) | für große Websites oder viele Websites mit Isolation |
| Container (Docker, Podman) | [Container](../../hosting/modelle/container-docker-und-podman.md) | in den Quellen Stufe 2; achten Sie auf persistente Volumes |
| Kubernetes | [Kubernetes](../../hosting/modelle/kubernetes.md) | Stufe 3; Replikate brauchen gemeinsamen Speicher, gemeinsame Sitzungen und einen einzigen Cron |
| Platform as a Service | [Platform as a Service](../../hosting/modelle/platform-as-a-service.md) | Stufe 3; `var/` muss auf persistentem Speicher liegen |
| Managed Hosting | [Managed Hosting](../../hosting/modelle/managed-hosting.md) | ein Anbieter betreibt es; regeln Sie vertraglich, wer was tut |

## Fragen an ein Cloud-Angebot

| Frage | Warum |
|---|---|
| In welchem Land und welcher Region liegen Server, Speicher und Datensicherungen? | Regeln zum Datenstandort (siehe „Datenstandort“) |
| Wer kann auf die Systeme zugreifen, von wo aus und nach welchem Recht? | Zugriff durch Mitarbeitende des Anbieters und durch Behörden |
| Ist die Datenbank ein verwalteter Dienst, und welche Datenbank in welcher Version? | Es muss eine der unterstützten Datenbanken sein |
| Ist der Dateispeicher persistent und zwischen den Knoten geteilt? | Inhaltsdateien liegen unter `var/` |
| Können Cronjobs zuverlässig laufen, einmal pro Installation? | Workflows, Benachrichtigungen und das Audit brauchen sie |
| Was decken die Datensicherungen ab, wie lange werden sie aufbewahrt, wie wird eine Wiederherstellung getestet? | Ihre Pflichten zu Schriftgut und Kontinuität |
| Wie bekommen Sie am Ende alle Daten und Dateien heraus? | Ausstieg (siehe „Ausstiegsplanung“) |

## Mehrere Webknoten

Mehr als ein Webserver für eine Website braucht gemeinsamen Dateispeicher, eine gemeinsame Datenbank, Sitzungen, die
alle Knoten sehen, Cron auf nur einem Knoten und ein Deployment, das alle Knoten gemeinsam aktualisiert
([Mehrere Webknoten](../../hosting/betrieb/mehrere-web-knoten.md),
[Clustering und gemeinsamer Speicher](../../hosting/betrieb/clustering-und-gemeinsamer-speicher.md)).

## Entscheiden und dokumentieren

Halten Sie das Modell, den Anbieter, die Regionen, die Datenbank, den Ort der Datensicherungen und den Betreiber jeder
Ebene schriftlich fest. Überprüfen Sie es, wenn der Vertrag verlängert wird.

## Verwandte Seiten

- [Ein Hostingmodell wählen](../../hosting/modelle/ein-hosting-modell-waehlen.md)
- [Betriebssystemunterstützung](../../betriebssystemunterstuetzung.md)
- [Selbst betreiben im eigenen Haus](selbst-betreiben-im-eigenen-haus.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
