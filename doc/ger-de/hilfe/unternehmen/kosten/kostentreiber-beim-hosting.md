---
title: Kostentreiber beim Hosting
audience: businesses, project-manager, hosting, admin
category: business-solutions
order: 80
summary: Welche technischen Entscheidungen das Hosting einer Exponential-Website billiger oder teurer machen - Hosting-Modell, Datenbank, Engine, Anzahl von Websites und Servern, Speicher, Caching, optionale Dienste, Backups und Arbeitszeit - mit den Fakten aus der Hosting-Dokumentation und einer durchgerechneten Monatsschätzung aus Beispielannahmen.
keywords: Hostingkosten, Kostentreiber, Hosting-Modell, Shared Hosting, VPS, dedizierter Server, Managed Hosting, SQLite, PostgreSQL, Velocity, Speicher, Backups, Solr, Valkey, Multisite, Dimensionierung, Budget, hosting cost, cost drivers, hosting model, shared hosting, dedicated, managed hosting, storage, backups, multisite, sizing, budget
type: help
---
# Kostentreiber beim Hosting

Diese Seite ist für Inhaber und Führungskräfte, die Hosting-Angebote vergleichen, und für die Projektleitung, die
erklären muss, warum ein Angebot mehr kostet als ein anderes. Sie nennt die Entscheidungen, die die Hostingrechnung
bewegen, jeweils mit den Fakten aus der Hosting-Dokumentation, und zeigt eine durchgerechnete Monatsschätzung. Die
technischen Einzelheiten stehen in der [Hilfe für Hoster](../../hosting/hosting-anbieter-hier-beginnen.md).

> **Hinweis**
> Das Projekt veröffentlicht keine Hostingpreise und keine Angaben zu Websites pro Server
> ([Ein Hosting-Modell wählen](../../hosting/modelle/ein-hosting-modell-waehlen.md) sagt das ausdrücklich). Die Schätzung
> unten verwendet nur **Beispielannahmen** in einer neutralen Währungseinheit. Verwenden Sie für Ihr Budget echte
> Angebote.

## Inhalt

1. [Die Treiber auf einen Blick](#die-treiber-auf-einen-blick)
2. [Hosting-Modell](#hosting-modell)
3. [Datenbank](#datenbank)
4. [Engine und Webserver](#engine-und-webserver)
5. [Websites, Server und Speicher](#websites-server-und-speicher)
6. [Optionale Dienste](#optionale-dienste)
7. [Menschen: der versteckte Treiber](#menschen-der-versteckte-treiber)
8. [Eine durchgerechnete Monatsschätzung](#eine-durchgerechnete-monatsschätzung)
9. [Fragen an einen Hoster](#fragen-an-einen-hoster)

## Die Treiber auf einen Blick

| Treiber | Meist billiger | Meist teurer |
|---|---|---|
| Hosting-Modell | Shared Hosting, ein kleiner VPS | Managed Hosting, mehrere Server, Kubernetes |
| Datenbank | SQLite 3 in einer Datei, kein Server | Ein Datenbankserver oder ein Cluster davon |
| Engine | Eine Velocity-Instanz oder ein PHP-FPM-Pool | Eine Instanz pro Website zur Isolation, mehrere Web-Knoten |
| Websites | Mehrere Websites in einer Installation | Eine Installation pro Website |
| Speicher | Wenige Mediendateien, vernünftige Aufbewahrung der Backups | Große Medien-Bibliothek, lange Aufbewahrung, Kopien außer Haus |
| Dienste | Eingebaute Suche, Datei-Caches | Solr, Valkey oder Redis, ein CDN |
| Menschen | Selbstbedienung mit dokumentierten Runbooks | Reaktion rund um die Uhr, betreute Aktualisierungen |

## Hosting-Modell

Das Projekt vergleicht Shared Hosting, VPS, dedizierte Server, Container und Kubernetes ohne Preise
([Ein Hosting-Modell wählen](../../hosting/modelle/ein-hosting-modell-waehlen.md)). Für die Kosten zählen diese Fakten:

- Shared Hosting erlaubt meist keinen dauerhaft laufenden Prozess, daher ist Velocity dort meist nicht möglich;
  stattdessen liefern Apache oder LiteSpeed mit `.htaccess` die Website aus (`README.md`, „When a traditional server
  is still the right answer“).
- Ein VPS oder dedizierter Server erlaubt Velocity und gibt Ihnen Root-Rechte, aber Sie oder Ihr Hoster müssen ihn
  betreiben.
- Managed Hosting verlagert die Betriebsarbeit zum Hoster; Sie bezahlen sie im Tarif statt mit Arbeitszeit
  ([Managed Hosting](../../hosting/modelle/managed-hosting.md)).

[Hosting-Modelle im Vergleich](../auswahl/hosting-modelle-im-vergleich.md) fasst das in geschäftlichen Begriffen.

## Datenbank

- SQLite 3 ist der Standard und braucht keinen Datenbankserver (`README.md`, „Requirements“). Es passt zu einer
  Website, die von einer Maschine ausgeliefert wird ([Datenbankunterstützung](../../datenbankunterstuetzung.md)).
- Einen Datenbankserver, zuerst PostgreSQL, brauchen Sie, „when more than one machine must write the database“ (wenn
  mehr als eine Maschine die Datenbank schreiben muss,
  [Hosting, Datenbank und Betriebssystem wählen](../../fuehrung/hosting-datenbank-und-betriebssystem-waehlen.md)). Ein Server
  bringt eine Maschine oder einen Dienst mehr, den Sie bezahlen, sichern und patchen müssen.
- Mehrere Web-Knoten für eine Website brauchen einen Datenbankserver und gemeinsamen Speicher für `var/storage`
  ([Mehrere Web-Knoten](../../hosting/betrieb/mehrere-web-knoten.md)).

## Engine und Webserver

- Velocity „needs nothing installed beyond PHP“ (braucht außer PHP nichts installiert, `README.md`, „Requirements“)
  und liefert HTTPS selbst aus, einschließlich Zertifikaten von Let's Encrypt (`README.md`).
- Ein Velocity-Worker hält etwa 10 MB privaten Speicher, wie im Projekt gemessen
  ([Speicher, CPU und Festplatte dimensionieren](../../hosting/grundlagen/speicher-cpu-und-festplatte-dimensionieren.md)).
- Seiten aus dem Cache kosten wenig CPU: Die README berichtet, dass Seiten aus dem Cache in 0,36 ms beantwortet werden,
  ohne dass ein Worker beteiligt ist (`README.md`, „In short“). Gerenderte Seiten sind es, die CPU kosten; gutes Caching
  lässt also eine kleinere Maschine mehr tragen.
- Starke Isolation zwischen mehreren Kunden bedeutet „one Velocity instance per site behind a proxy, each as its own
  operating-system user“ (eine Velocity-Instanz pro Website hinter einem Proxy, jede als eigener Benutzer des
  Betriebssystems, `README.md`), was mehr Speicher kostet als ein gemeinsamer Prozess.

## Websites, Server und Speicher

- Multisite ist eingebaut: Mehrere Websites können sich eine Installation, einen Satz Aktualisierungen und einen
  Backup-Job teilen ([Eine Installation, viele Websites](../multisite/eine-installation-viele-websites.md)).
- Der Speicherbedarf wächst mit den Medien: Bilder und Dateien in `var/storage`, Bildvarianten, die beim ersten Aufruf
  erzeugt werden, Logs und das Prüfprotokoll
  ([Speicher, CPU und Festplatte dimensionieren](../../hosting/grundlagen/speicher-cpu-und-festplatte-dimensionieren.md)).
- Backups brauchen Platz für die Datenbank, hochgeladene Dateien, Einstellungen, Schlüssel und das Prüfprotokoll
  ([Backups: was gesichert wird](../../hosting/betrieb/backups-was-gesichert-werden-muss.md)); die Aufbewahrungsdauer
  vervielfacht das.

## Optionale Dienste

| Dienst | Wann Sie ihn brauchen | Lesen |
|---|---|---|
| Solr über `ezfind` | Große Websites mit erweiterten Anforderungen an die Suche; die eingebaute Suche ist der Standard | [Such-Backends](../../hosting/grundlagen/such-backends.md) |
| Valkey oder Redis | Gemeinsame Caches über mehrere Web-Knoten (`sevenx_valkey_cache`) | [Valkey und Redis](../../hosting/leistung/valkey-und-redis.md) |
| Ein CDN | Besucher weit weg vom Server, viele Medien | [CDN](../../hosting/leistung/cdn.md) |
| Ein Mail-Zustelldienst | Viele Newsletter oder strenge Anforderungen an die Zustellung | [Mailzustellung](../../hosting/betrieb/mailzustellung.md) |

Jeder davon ist optional. Lassen Sie ihn aus dem Budget heraus, bis ein gemessener Bedarf auftritt.

## Menschen: der versteckte Treiber

Der Tarifpreis ist oft kleiner als die Zeit, die jemand mit dem Betrieb der Website verbringt: Aktualisierungen,
Backups, Wiederherstellungstests, Logs beobachten. Zählen Sie diese Stunden (siehe
[Gesamtbetriebskosten](gesamtbetriebskosten.md)) oder kaufen Sie sie als Teil eines Managed-Tarifs. Die
[monatliche Betriebscheckliste](../vorlagen/monatliche-betriebscheckliste.md) zeigt, worin die Arbeit besteht.

## Eine durchgerechnete Monatsschätzung

Eine einzelne Firmen-Website auf einem VPS mit Velocity und SQLite. Jeder Eingabewert ist eine **Beispielannahme**.

| Eingabewert | Wert |
|---|---|
| Beispielannahme: VPS-Tarif | 40 Währungseinheiten im Monat |
| Beispielannahme: Backup-Speicher außer Haus | 10 Währungseinheiten im Monat |
| Beispielannahme: externes Monitoring der Verfügbarkeit | 5 Währungseinheiten im Monat |
| Beispielannahme: Zeit des Administrators | 3 Stunden im Monat zu 50 Währungseinheiten pro Stunde |
| Datenbankserver | 0 (SQLite 3, Fakt) |
| TLS-Zertifikat | 0, wenn Velocity eines von Let's Encrypt bezieht (Fakt: `README.md`) |

Schritt für Schritt:

1. Infrastruktur: 40 + 10 + 5 = **55**.
2. Arbeitszeit: 3 x 50 = **150**.
3. Summe pro Monat: 55 + 150 = **205**.
4. Summe pro Jahr: 205 x 12 = **2.460**.

Ändern Sie nun einen Treiber: Die Website wächst und braucht zwei Web-Knoten mit einem Datenbankserver.
Beispielannahmen: ein zweiter VPS zu 40, eine verwaltete Datenbank zu 60 und 2 Stunden mehr Administration im Monat
(100). Neue Summe pro Monat: 205 + 40 + 60 + 100 = **405**, etwa das Doppelte. Deshalb sollten Sie Server erst
hinzufügen, wenn Messungen den Bedarf zeigen
([Speicher, CPU und Festplatte dimensionieren](../../hosting/grundlagen/speicher-cpu-und-festplatte-dimensionieren.md#messen-nicht-raten)).

## Fragen an einen Hoster

- [ ] Welche PHP-Versionen kann ich pro Website wählen, und darf ich einen dauerhaft laufenden Prozess für Velocity
      betreiben?
- [ ] Ist SQLite für meinen Tarif in Ordnung, oder verlangen Sie einen Datenbankserver?
- [ ] Was ist enthalten: Backups, Wiederherstellungstests, Monitoring, Aktualisierungen, TLS?
- [ ] Wie lange werden Backups aufbewahrt, und wo?
- [ ] Zu welchen Reaktionszeiten verpflichten Sie sich schriftlich?
- [ ] Was kostet ein Wechsel weg von Ihnen, und in welchem Format bekomme ich meine Daten?

Weitere Fragen: [Fragen an einen Partner](../auswahl/fragen-an-einen-partner.md) und
[Hosting-Anforderungen für eine Website](../../ersteller/website-ersteller/hosting-fuer-eine-website.md#fragen-an-einen-hosting-anbieter).

## Verwandte Seiten

- [Hosting auswählen](../auswahl/hosting-auswaehlen.md)
- [Hosting-Tarife gestalten](../../hosting/geschaeft/hosting-tarife-gestalten.md)
- [Velocity für Unternehmen](../zuverlaessigkeit/velocity-fuer-unternehmen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
