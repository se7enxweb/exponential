---
title: Überwachung
audience: customer, businesses, hosting
category: customer-success
order: 11
summary: Was ein Betreiber von der Überwachung einer Exponential-Website erwarten sollte - Prüfungen von außen und auf dem Server, die Seiten, die eine Person jede Woche liest, und wer einen Alarm erhält - mit den Fragen an Ihren Hoster.
keywords: Überwachung, Monitoring, Verfügbarkeit, Statusprüfung, Alarme, Systeminformationen, Zustand der Logs, Fehlerübersicht, Alarme des Prüfprotokolls, Velocity-Status, Bereitschaft, monitoring, uptime, health check, alerts, system information, log health, error digest, audit alerts, on call
type: help
---
# Überwachung

Überwachung bedeutet, dass jemand ein Problem bemerkt, bevor Ihre Kunden es Ihnen sagen. Diese Seite beschreibt, wie
eine gute Überwachung einer Exponential-Website aus Sicht des Betreibers aussieht und was Sie den Hoster oder Betreiber
fragen sollten, der sie einrichtet. Die technischen Einzelheiten stehen unter
[Statusprüfungen und Überwachung](../../../../en/us/help/businesses/reliability/health-checks-and-monitoring.md) und, für Hoster, unter
[Überblick über die Überwachung](../../hosting/betrieb/ueberblick-ueber-die-ueberwachung.md).

## Keine einzelne Statusadresse

Das Installationshandbuch ist deutlich: „Es gibt keine einzelne Status-URL; kombinieren Sie eine Prüfung von außen mit
den Statusbefehlen innen“ ([Statusprüfungen und Überwachung](../../../../en/us/help/businesses/reliability/health-checks-and-monitoring.md)).
Fordern Sie beide Hälften an.

## Von außen: was Kunden sehen

Ein externer Verfügbarkeitsdienst prüft zwei Adressen
([Statusprüfungen und Überwachung](../../../../en/us/help/businesses/reliability/health-checks-and-monitoring.md)):

| Prüfung | Gesunde Antwort | Warum |
|---|---|---|
| Die Startseite über HTTPS | `200`, in einer Zeit, die Sie kennen | Was Besucher zuerst erreichen |
| Die Anmeldeseite der Administration | `200` | Sie läuft durch die Anwendung, während die Startseite möglicherweise aus einem Cache kommt |

Während einer geplanten Wartung antwortet die Website mit `503` und einem Header `Retry-After`; die Überwachung sollte
für das Zeitfenster pausiert werden oder diese Antwort akzeptieren ([Wartungsmodus und geplante Arbeiten](wartungsmodus-und-geplante-arbeiten.md)).

## Auf dem Server: warum es ausfällt

Der Hoster lässt Statusbefehle nach Plan laufen und schlägt Alarm, wenn einer fehlschlägt: die Statusprüfung der
Plattform, der Cache-Status, der Velocity-Status, wo Velocity die Website ausliefert, die Verifizierung des
Prüfprotokolls, Cron und freier Speicherplatz. Jeder liefert eine klare Antwort, gesund oder nicht gesund
([Statusprüfungen und Überwachung](../../../../en/us/help/businesses/reliability/health-checks-and-monitoring.md)). Sie müssen sie nicht
selbst ausführen; bitten Sie darum, dass sie eingeplant sind und dass ein Fehlschlag eine Person erreicht.

## Seiten, die eine Person jede Woche liest

| Seite | Was sie zeigt |
|---|---|
| **Setup > Systeminformationen** | Welcher Server geantwortet hat, Statusprüfungen mit dem, was zu tun ist, Versionen, Datenbank, Cron, E-Mail |
| **Setup > Logdateien**, Zustand der Logs und Fehlerübersicht | Von Logs belegter Speicherplatz, die verschiedenen Fehler der letzten 24 Stunden |
| Der Reiter **Audit** | Der Zustand jedes Kanals des Prüfprotokolls, neue Ereignisse, Alarme |
| **Setup > Cronjobs** | Eingeplante Jobs, letzte Läufe, Jobs, die Aufmerksamkeit brauchen |

Quelle: [Statusprüfungen und Überwachung](../../../../en/us/help/businesses/reliability/health-checks-and-monitoring.md). Wenn niemand in
Ihrem Team sie liest, bitten Sie Ihren Hoster oder Partner stattdessen um eine kurze wöchentliche oder monatliche
Zusammenfassung ([Monatlicher Betrieb](../../hosting/checklisten/checkliste-fuer-den-monatlichen-betrieb.md) enthält einen Bericht an den Kunden).

## Wer geweckt wird

„Eine Prüfung, die niemand liest, ist keine Überwachung“ (dieselbe Seite). Entscheiden und notieren Sie:

- [ ] Wer während der Bürozeiten einen Alarm erhält und wer außerhalb; wer die Vertretung ist.
- [ ] Wie schnell erwartet wird, dass sie nachsehen ([Verträge und SLAs](../partner-und-hoster/vertraege-und-slas.md)).
- [ ] Wohin Alarme gehen: E-Mail, ein Chat-Kanal, ein Telefon.
- [ ] Wer die eigenen Alarm-E-Mails des Prüfprotokolls erhält (Brute-Force-Anmeldungen, eine vergebene Rolle
      Administrator, ein abgeschaltetes Prüfprotokoll); der Hoster kann die Empfänger auflisten ([Das Prüfprotokoll](../../../../en/us/features/6.0/audit-trail.md)).
- [ ] Wann der Betreiber informiert wird, und von wem.

## Fragen an Ihren Hoster

1. Welche Adressen prüfen Sie von außen, wie oft und von wo?
2. Welche Statusbefehle laufen auf dem Server, und was geschieht, wenn einer fehlschlägt?
3. Wer erhält Alarme außerhalb der Bürozeiten?
4. Wo Velocity die Website ausliefert: Ist sein Watchdog installiert, der es neu startet, wenn es nicht läuft
   ([Statusprüfungen und Überwachung](../../../../en/us/help/businesses/reliability/health-checks-and-monitoring.md))?
5. Können wir jeden Monat die Verfügbarkeitszahlen sehen?
6. Wird ein fehlgeschlagener Backup-Job wie eine fehlgeschlagene Statusprüfung gemeldet ([Backups](backups.md))?

## Verwandte Seiten

- [Logs für Betreiber](logs-fuer-betreiber.md)
- [Wenn die Website nicht erreichbar ist](wenn-die-website-nicht-erreichbar-ist.md)
- [Statusprüfungen und Verfügbarkeit (für Hoster)](../../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md)
- [Verfügbarkeit in der Praxis](../../../../en/us/help/businesses/reliability/uptime-practice.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
