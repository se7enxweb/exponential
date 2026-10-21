---
title: Die Hostingkosten einer Exponential-Website schätzen
audience: government, project-manager, hosting, admin
category: public-sector
order: 50
summary: Die Faktoren, die die Hostingkosten einer Exponential-Website bestimmen - Hosting-Modell, Engine, Datenbank, Suche, Speicher, Sicherungen, Verfügbarkeit und Umgebungen - mit den Größenangaben, die das Repository nennt, und einer Methode, mit Ihren eigenen Angeboten zu schätzen. Es werden keine Preise genannt.
keywords: Hostingkosten, Infrastrukturkosten, Server-Dimensionierung, Cloud, VPS, dedizierter Server, Container, Datenbankserver, Sicherungen, Verfügbarkeit, Umgebungen, Staging, Schätzung, hosting cost, infrastructure cost, server sizing, cloud, dedicated server, containers, database server, backups, availability, environments, staging, estimate
type: help
---
# Die Hostingkosten einer Exponential-Website schätzen

Diese Seite richtet sich an Projektleitungen, die den Hosting-Anteil der Gesamtbetriebskosten schätzen müssen, und an
das IT-Personal, das ihnen hilft. Sie führt auf, was die Kosten treibt, die Größenangaben, die das Repository
tatsächlich nennt, und eine Methode, aus Angeboten eine Jahreszahl zu machen. Sie enthält keine Preise; verwenden Sie
Angebote Ihrer Hoster oder Ihre eigenen internen Sätze.

## Inhalt
1. [Was die Hostingkosten treibt](#was-die-hostingkosten-treibt)
2. [Größenangaben aus dem Repository](#größenangaben-aus-dem-repository)
3. [Umgebungen](#umgebungen)
4. [Verfügbarkeit und Sicherungen](#verfügbarkeit-und-sicherungen)
5. [Eine Methode zum Schätzen](#eine-methode-zum-schätzen)
6. [Fragen zu Hosting-Angeboten](#fragen-zu-hosting-angeboten)

## Was die Hostingkosten treibt

| Treiber | Günstige Wahl | Teurere Wahl | Dokumentation |
|---|---|---|---|
| Hosting-Modell | Shared Hosting oder ein VPS | dedizierte Server, mehrere Webknoten, Kubernetes | [Ein Hosting-Modell wählen](../../../hosting/modelle/ein-hosting-modell-waehlen.md) |
| Datenbank | SQLite 3, kein Server | ein Datenbankserver, repliziert | [Anforderungen](../../../../installation/02-voraussetzungen.md), [Datenbanken in der offiziellen Reihenfolge](../../../hosting/grundlagen/datenbanken-in-der-offiziellen-reihenfolge.md) |
| Suche | eingebaute Suche | Solr über ezfind | [Such-Backends](../../../hosting/grundlagen/such-backends.md) |
| Engine | Apache oder nginx mit PHP-FPM, oder Velocity | mehrere Velocity-Instanzen, Load Balancer | [Einen Webserver wählen](../../../hosting/server/einen-webserver-waehlen.md) |
| Speicher | kleine Mediathek | große Mediathek, gemeinsamer Speicher für mehrere Knoten | [Aufbau des Speichers](../../../hosting/betrieb/aufbau-des-speichers.md) |
| Verfügbarkeit | ein Server, Wiederherstellung aus der Sicherung | mehrere Knoten, rund um die Uhr überwacht | [Mehrere Webknoten](../../../hosting/betrieb/mehrere-web-knoten.md) |
| Verwaltung | Ihr eigenes Personal | Managed Hosting | [Managed Hosting](../../../hosting/modelle/managed-hosting.md) |

Mehrere Webknoten für eine Website brauchen einen Datenbankserver und ein gemeinsames `var/storage`; SQLite passt nur
zu einem schreibenden Rechner ([Ein Hosting-Modell wählen](../../../hosting/modelle/ein-hosting-modell-waehlen.md)).

## Größenangaben aus dem Repository

Aus [Speicher, CPU und Festplatte dimensionieren](../../../hosting/grundlagen/speicher-cpu-und-festplatte-dimensionieren.md):

- PHP `memory_limit`: Die Zustandsprüfung schlägt unter 128M fehl und warnt unter 256M.
- Ein Velocity-Worker belegt etwa 10 MB privaten Speicher (gemessen); der mitgelieferte Pool hat 4 Worker.
- OPcache und APCu reservieren gemeinsamen Speicher (jeweils 256 MB in den mitgelieferten Einstellungen von Velocity).
- Freier Festplattenplatz: Die Zustandsprüfung schlägt unter 1 GB oder 5 % fehl und warnt unter 5 GB oder 10 %.
- Seiten aus dem Cache kosten wenig CPU; gerenderte Seiten (angemeldete Redaktion, erste Aufrufe, Suche) sind es, wohin
  die CPU geht.

Der eigene Rat der Seite ist, eine echte Website zu messen, statt zu raten: Führen Sie den Proof of Concept mit Ihren
Inhalten durch und messen Sie mit `exp:benchmark` ([Benchmarking](../../../../anleitungen/benchmarking.md)).

## Umgebungen

| Umgebung | Zweck | Oft dimensioniert als |
|---|---|---|
| Produktion | die öffentliche Website | volle Größe |
| Staging oder Abnahme | Releases und Inhalte vor der Produktion testen | kleinere Kopie |
| Entwicklung | Arbeit des Lieferanten | eigene Rechner des Lieferanten oder klein |
| Schulung | Kurse für die Redaktion mit erfundenen Inhalten | klein; siehe [Lab-Kurzkarte](../../../../../eng-us/help/trainers/lab-quick-card.md) |

Zählen Sie jede Umgebung, die Sie bezahlen. Eine Staging-Kopie darf nie mehr personenbezogene Daten enthalten, als sie
braucht.

## Verfügbarkeit und Sicherungen

- Sicherungen umfassen Datenbank und Dateien zusammen; Einstellungen und generierte Schlüssel müssen darin enthalten
  sein ([Sicherungen: was zu sichern ist](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md)).
- Der Speicher für Sicherungen wächst mit der Aufbewahrung und mit `var/storage`.
- Die Wiederherstellungszeit ist erst nach einem Wiederherstellungstest bekannt ([Wiederherstellung und Wiederherstellungstests](../../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md)).
- Ein Verfügbarkeitsziel über dem, was ein Server leistet, vervielfacht Server, Speicher und Betriebsarbeit.

## Eine Methode zum Schätzen

1. Legen Sie Hosting-Modell und Umgebungen mit Ihrem IT-Team fest.
2. Messen Sie den Proof of Concept mit Ihren Inhalten: Speicher, Festplatte, Renderzeiten der Seiten.
3. Holen Sie mindestens zwei Angebote je Modell ein, mit derselben Spezifikation und demselben Service-Level.
4. Ergänzen Sie interne Kosten: die Zeit Ihres Personals für Überwachung, Updates und Sicherungen, wenn Sie selbst
   betreiben.
5. Multiplizieren Sie die wiederkehrenden Kosten über den Zeithorizont, addieren Sie die einmalige Einrichtung.
6. Tragen Sie das Ergebnis in die [Vorlage für ein TCO-Arbeitsblatt](vorlage-fuer-ein-tco-arbeitsblatt.md) ein.

## Fragen zu Hosting-Angeboten

- Welche PHP-Version, Datenbank und Engine werden die Website betreiben? Werden sie unterstützt
  ([Anforderungen](../../../../installation/02-voraussetzungen.md))?
- Wo werden die Daten gespeichert (Land, Anbieter), und wo die Sicherungen?
- Wie oft werden Sicherungen erstellt, wie lange aufbewahrt, und wann wurde zuletzt eine Wiederherstellung getestet?
- Wie schnell werden veröffentlichte Sicherheitskorrekturen von Exponential eingespielt ([SLA-Optionen](../../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md))?
- Was ist enthalten: TLS-Zertifikate, Überwachung, Staging, Support-Stunden?
- Was übergibt der Hoster am Vertragsende ([Kunden-Offboarding](../../../hosting/checklisten/checkliste-fuer-das-offboarding-eines-kunden.md))?

## Verwandte Seiten
- [Gesamtbetriebskosten: Übersicht](README.md)
- [Eigenbetrieb oder Cloud](eigenbetrieb-oder-cloud.md)
- [Hosting-Unternehmen: hier beginnen](../../../hosting/hosting-anbieter-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
