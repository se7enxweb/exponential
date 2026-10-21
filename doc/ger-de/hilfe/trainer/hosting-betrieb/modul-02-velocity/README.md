---
title: "Modul 2: Velocity"
audience: trainer, hosting, admin
category: training
order: 20
summary: Das zweite Modul von Hosting und Betrieb, was Velocity ist, ihn am Laufen halten, Worker und Pools, der Antwort-Cache und HTTPS-Zertifikate. Vier Lektionen, 3 Stunden.
keywords: hosting und betrieb, modul 2, Velocity, qbix, exp:velocity, worker, pool, antwort-cache, HTTPS, zertifikate, watchdog, systemd, hosting and operations, module 2, workers, response cache, certificates
type: help
---
# Modul 2: Velocity

Exponential Velocity ist der empfohlene Weg, eine Exponential-Website auszuliefern, von der Entwicklung bis zum
Produktivbetrieb. Dieses Modul bringt Hosting-Mitarbeitenden bei, es zu betreiben: das Modell aus einem
Elternprozess und persistenten Workern, die Steuerbefehle und eine Unit für den Systemstart, den Worker-Pool und seinen
Speicher sowie die zwei Dinge, die andere Werkzeuge ersetzen, den Antwort-Cache und HTTPS mit den eigenen Zertifikaten
von Velocity.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Hosting und Betrieb](../README.md) |
| Dauer | 3 Stunden (vier Lektionen) |
| Lektionen | 4 |
| Niveau | Fortgeschrittene bis Experten |
| Form | Praxisorientiert; ein Rechner mit Terminal pro Person |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [2.1 Was Velocity ist und ein erster Start](was-velocity-ist.md) | 45 Min. | Die Engine wählen, eine Einstellung ändern, den Server starten und prüfen |
| [2.2 Velocity am Laufen halten](velocity-am-laufen-halten.md) | 45 Min. | graceful und restart verwenden, eine Unit für den Systemstart schreiben, Logs und den Baum lesen |
| [2.3 Worker und Pools](worker-und-pools.md) | 45 Min. | Einen dynamischen Pool einstellen und sehen, wie er wächst und schrumpft |
| [2.4 Der Antwort-Cache und HTTPS-Zertifikate](antwort-cache-und-https.md) | 45 Min. | Einen Cache-Treffer nachweisen, HTTPS mit einem Zertifikatspaar einschalten |

## Dieses Modul vorbereiten

- Eine Schulungsinstallation pro Person mit PHP 8.0 oder neuer und den Erweiterungen `sockets`, `pcntl`, `posix` und
  `openssl`; das Engine-Paket bereits mit Composer eingebunden
  (`composer require se7enxweb/exponential-velocity:~0.0.4.44`).
- root auf den virtuellen Maschinen der Teilnehmenden für die Unit aus Lektion 2.2; ohne root schreiben die
  Teilnehmenden die Unit und lesen sie mit Ihnen.
- Ein Browser auf dem Rechner jeder Person für die Seite `/Q/dashboard` (sie öffnet sich nur vom Rechner selbst aus).
- `openssl` für das Zertifikatspaar aus Lektion 2.4. Im Kurs werden weder öffentliches DNS noch ACME gebraucht.
- Prüfen Sie, dass sich auf einem gemeinsamen Rechner keine zwei Teilnehmenden dieselben Ports teilen.

## Zeitplan

| Zeit | Punkt |
|---|---|
| 0:00 | Lektion 2.1 (45 Min.) |
| 0:45 | Lektion 2.2 (45 Min.) |
| 1:30 | Pause (15 Min.) |
| 1:45 | Lektion 2.3 (45 Min.) |
| 2:30 | Lektion 2.4 (45 Min.) |
| 3:15 | Ende (3 Std. Lektionen plus eine Pause) |

## Verwandte Seiten

- [Hosting und Betrieb: Kursüberblick](../README.md)
- [Velocity für Hoster](../../../hosting/server/velocity-fuer-hoster.md)
- [Velocity neben Apache oder nginx](../../../hosting/server/velocity-neben-apache-oder-nginx.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
