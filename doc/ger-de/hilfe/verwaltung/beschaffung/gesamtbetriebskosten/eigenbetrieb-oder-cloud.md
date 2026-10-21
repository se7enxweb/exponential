---
title: Eigenbetrieb oder Cloud-Hosting für eine Exponential-Website
audience: government, project-manager, hosting, admin
category: public-sector
order: 100
summary: Ein Vergleich, ob Sie eine Exponential-Website im eigenen Rechenzentrum, in einer privaten oder öffentlichen Cloud oder bei einem Managed Host betreiben - was jedes Modell von der Software braucht, welche Kosten sich wohin verschieben, Datenstandort und Kontrolle, und eine Entscheidungstabelle. Keine Preise.
keywords: Eigenbetrieb, On-Premise, Cloud, Hosting-Modell, private Cloud, öffentliche Cloud, Managed Hosting, Rechenzentrum, Datenstandort, Souveränität, Container, Kubernetes, Vergleich, TCO, on-premise, hosting model, private cloud, public cloud, managed hosting, data center, data location, sovereignty, containers, comparison
type: help
---
# Eigenbetrieb oder Cloud-Hosting für eine Exponential-Website

Diese Seite richtet sich an Entscheidungsträger, die vergleichen, ob sie eine Exponential-Website auf ihrer eigenen
Infrastruktur betreiben oder sie bei einer Cloud oder einem Managed Host mieten. Weil Exponential freie Software ist,
die auf gewöhnlichen Linux-Servern mit PHP läuft, stehen Ihnen alle Modelle offen; die Wahl verschiebt Kosten und
Kontrolle. Die technischen Fakten stammen aus [Ein Hosting-Modell wählen](../../../hosting/modelle/ein-hosting-modell-waehlen.md)
und dem Abschnitt zum Hosting.

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt
1. [Die Modelle](#die-modelle)
2. [Was die Software in jedem Modell braucht](#was-die-software-in-jedem-modell-braucht)
3. [Wohin sich Kosten verschieben](#wohin-sich-kosten-verschieben)
4. [Kontrolle und Datenstandort](#kontrolle-und-datenstandort)
5. [Eine Entscheidungstabelle](#eine-entscheidungstabelle)
6. [Fallstricke](#fallstricke)

## Die Modelle

| Modell | Sie betreiben | Der Anbieter betreibt | Dokumentation |
|---|---|---|---|
| Eigenbetrieb | alles: Hardware, Betriebssystem, PHP, Datenbank, Sicherungen | nichts | [Dedizierte Server](../../../hosting/modelle/dedizierte-server.md) |
| Infrastruktur in einer Cloud (virtuelle Maschinen) | ab dem Betriebssystem aufwärts | Hardware, Netzwerk | [VPS-Hosting](../../../hosting/modelle/vps-hosting.md) |
| Container oder Kubernetes | Images, Volumes, Manifeste | die Cluster-Plattform (wenn gemietet) | [Container](../../../hosting/modelle/container-docker-und-podman.md), [Kubernetes](../../../hosting/modelle/kubernetes.md) |
| Platform as a Service | die Anwendung | Laufzeitumgebung, Skalierung | [Platform as a Service](../../../hosting/modelle/platform-as-a-service.md) |
| Managed Hosting | Inhalte und Entscheidungen zur Website | Server, Updates, Sicherungen, Überwachung | [Managed Hosting](../../../hosting/modelle/managed-hosting.md) |

Die Quellen ordnen jedes Modell in die Stufenskala des Projekts ein: Container Stufe 2, Kubernetes und Platform as a
Service Stufe 3, „eine begründete Erwartung, kein Testergebnis“. Planen Sie einen Test, bevor Sie sich auf ein Modell
der Stufe 3 festlegen.

## Was die Software in jedem Modell braucht

- PHP 8.0 oder neuer (Velocity eingeschlossen) und eine unterstützte Datenbank ([Anforderungen](../../../../installation/02-voraussetzungen.md)).
- Dauerhaften Speicher für `var/` (Uploads, Protokolle, Audit) - wichtig auf Plattformen mit flüchtigen Dateisystemen.
- Cronjobs für Hintergrundarbeit ([Cronjobs](../../../hosting/betrieb/cronjobs.md)).
- Für mehrere Webknoten: einen Datenbankserver und ein gemeinsames `var/storage`; SQLite passt nur zu einem schreibenden
  Rechner.
- Sicherungen von Datenbank und Dateien zusammen sowie der generierten Schlüssel in `settings/override/`
  ([Sicherungen: was zu sichern ist](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md)).

## Wohin sich Kosten verschieben

| Kosten | Eigenbetrieb | Cloud-Infrastruktur | Managed Hosting |
|---|---|---|---|
| Hardware | Ihre, vorab gekauft | in der Gebühr des Anbieters | in der Gebühr des Anbieters |
| Betriebspersonal | Ihres | Ihres (ab dem Betriebssystem) | überwiegend das des Anbieters |
| Sicherheitspatches | Ihre | Ihre oberhalb der Plattform | Anbieter, wie vertraglich vereinbart |
| Skalierung für Spitzen | für die Spitze kaufen | nach Nutzung bezahlen | wie vertraglich vereinbart |
| Ausstieg | Sie halten alles | Volumes und Datenbank exportieren | braucht eine Offboarding-Klausel |

Keines davon ist allgemein günstiger; vergleichen Sie mit Ihren eigenen Angeboten im [TCO-Arbeitsblatt](vorlage-fuer-ein-tco-arbeitsblatt.md).

## Kontrolle und Datenstandort

- Eigenbetrieb und Ihr eigener Cloud-Mandant halten die Daten dort, wo Sie es entscheiden.
- Fragen Sie bei einem Anbieter, wo Daten, Sicherungen und Protokolle gespeichert werden, wer darauf zugreifen kann und
  unter welcher Vereinbarung. In der EU braucht ein Anbieter, der personenbezogene Daten in Ihrem Auftrag verarbeitet,
  einen Auftragsverarbeitungsvertrag nach der [DSGVO](https://eur-lex.europa.eu/eli/reg/2016/679/oj).
- Die Administration von Exponential lädt standardmäßig keine Skripte oder Schriften von anderen Hosts, die
  Administration selbst sendet also keine Daten an Dritte ([Datenschutzstandards](../../../../standards/datenschutz.md)).
- Sicherheitspflichten können Sie unabhängig vom gewählten Modell treffen; in der EU siehe die
  [NIS2-Richtlinie](https://eur-lex.europa.eu/eli/dir/2022/2555/oj) für Einrichtungen in ihrem Anwendungsbereich.

## Eine Entscheidungstabelle

| Wenn | Neigen Sie zu |
|---|---|
| Sie Betriebspersonal und Kapazität im Rechenzentrum haben | Eigenbetrieb oder private Cloud |
| Sie schnelle Skalierung brauchen und Cloud-Kompetenz haben | Cloud-Infrastruktur oder Container |
| Sie kein Betriebspersonal haben | Managed Hosting mit starken Offboarding-Bedingungen |
| Daten in einem benannten Land bleiben müssen | jedes Modell, dessen Anbieter das schriftlich garantiert |
| Verfügbarkeit über einen Server hinaus verlangt ist | mehrere Knoten mit einem Datenbankserver und gemeinsamem Speicher |

## Fallstricke

- Eine Plattform mit flüchtigem Dateisystem ohne dauerhaftes `var/` wählen.
- Einen Preis für Managed Hosting mit einem Preis für Eigenbetrieb vergleichen, der die Personalzeit weglässt.
- Keine Offboarding-Bedingungen mit einem Managed Host ([Kunden-Offboarding](../../../hosting/checklisten/checkliste-fuer-das-offboarding-eines-kunden.md)).

## Verwandte Seiten
- [Gesamtbetriebskosten: Übersicht](README.md)
- [Hostingkosten](hostingkosten.md)
- [Hosting-Unternehmen: hier beginnen](../../../hosting/hosting-anbieter-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
