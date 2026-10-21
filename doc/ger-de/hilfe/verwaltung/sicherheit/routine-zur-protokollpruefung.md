---
title: Routine zur Protokollprüfung
audience: government, admin, hosting
category: public-sector
order: 340
summary: Eine wöchentliche und monatliche Routine zur Protokollprüfung für die Administration einer öffentlichen Website - welche Protokolle und Audit-Ansichten von Exponential Sie lesen, worauf Sie achten, welche Befehle Sie verwenden und wie Sie die Prüfung als Nachweis festhalten.
keywords: Protokollprüfung, Überwachung, wöchentliche Prüfung, Audit-Konsole, Fehlerübersicht, Protokollansicht, Nachweis, Routine, öffentlicher Sektor, log review, monitoring, weekly review, security.log, audit console, error digest, csp.log, auth.log, log viewer, exp:logs, exp:audit, evidence, routine, public sector
type: help
---
# Routine zur Protokollprüfung

Diese Seite richtet sich an Administratorinnen und Administratoren sowie Hosting-Teams. „Härtung ohne Beobachtung
verschiebt nur den Moment, in dem Sie von einem Angriff erfahren“
([Sicherheitshärtung: Beobachten](../../sicherheitshaertung.md#beobachten-logs-der-request-klassifizierer-und-das-audit-protokoll)).
Unten finden Sie eine Routine, die in eine halbe Stunde pro Woche passt, mit den Stellen in Exponential, an denen Sie
nachsehen.

## Inhalt

1. [Wöchentlich](#wöchentlich)
2. [Monatlich](#monatlich)
3. [Wo Sie nachsehen](#wo-sie-nachsehen)
4. [Die Prüfung festhalten](#die-prüfung-festhalten)
5. [Verwandte Seiten](#verwandte-seiten)

## Wöchentlich

| Prüfung | Wo | Worauf achten |
|---|---|---|
| Zustand des Audits | Reiter Audit, Dashboard (`/audit/dashboard`) | Ketten intakt, Cronjob-Teil innerhalb der letzten Stunde gelaufen, Alarme |
| Ausgelöste Alarme | **Alarme** (`/audit/alerts`) | jeder Alarm erklärt |
| Fehlgeschlagene Anmeldungen | Karte *Sicherheit* im Dashboard; `/audit/console/(name)/access.session.login.failed` | Netze oder Konten mit vielen Fehlschlägen |
| Rollenvergaben | `/audit/console/(name)/access.role.*` | Vergaben, die Sie nicht erwartet haben |
| Schreibvorgänge an Einstellungen | `/audit/console/(name)/system.setting.*` | Änderungen außerhalb von Änderungsanträgen |
| Sicherheitsprotokoll | **Setup > Logdateien** > „Sicherheit (Anfrageklassifizierung)“ | ein plötzlicher Anstieg einer Regel; Injection oder Traversal mit Status 200 oder 500 |
| Fehler | **Setup > Logdateien**, Fehlerübersicht | neue Fehler nach einer Änderung |
| CSP-Berichte | **Setup > Logdateien** > „Berichte der Content Security Policy“ | neue Quellen; Verstöße auf Seiten, die sauber sein sollten |
| Zweistufige Anmeldung | `var/log/auth.log` in der Protokollansicht | wiederholte Fehlschläge für ein Konto |

## Monatlich

- `./console exp:audit verify --archives --allow-root-user` und die Ausgabe aufbewahren.
- Leichte Kontenprüfung: Benutzer > Benutzerkonten prüfen, **Seit N Tagen nicht gesehen** und **Gesperrt**.
- `composer audit` und `composer outdated` (siehe „Patch-Rhythmus“).
- Seite „Zustand der Logs“ in der Protokollansicht: Wachstum und Rotation der Protokolle
  ([Protokollansicht](../../../funktionen/6.0/log-betrachter.md#zustand-der-logs)).

## Wo Sie nachsehen

Entsprechungen auf der Kommandozeile:

```bash
./console exp:audit status --allow-root-user
./console exp:audit search --name='access.session.login.failed' --from=<YYYY-MM-DD> --limit=50 --allow-root-user
./console exp:audit tail --channel=system --lines=50 --allow-root-user
./console exp:logs --archives --allow-root-user
```

Die Protokollansicht maskiert E-Mail- und IP-Adressen sowie Geheimnisse standardmäßig; lassen Sie das für Screenshots
und Berichte so. Geben Sie `audit/read` und die Protokollansicht (`setup/logs`) nur Administratorinnen und
Administratoren.

## Die Prüfung festhalten

Führen Sie je Prüfung einen kurzen Eintrag: Datum, prüfende Person, was geprüft wurde, Befunde und Maßnahmen, mit Links
zu Filtern der Audit-Konsole (das sind URLs, die sich als Lesezeichen speichern lassen). Über ein Jahr ergibt das den
Nachweis, den Prüferinnen und Prüfer bei Anforderungen an Protokollierung und Überwachung verlangen.

## Verwandte Seiten

- [Sicherheitshärtung: Beobachten](../../sicherheitshaertung.md#beobachten-logs-der-request-klassifizierer-und-das-audit-protokoll)
- [Anfrageprotokolle und das Sicherheitsprotokoll](anfrageprotokolle-und-das-sicherheitsprotokoll.md)
- [Die Protokollansicht](../../../funktionen/6.0/log-betrachter.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
