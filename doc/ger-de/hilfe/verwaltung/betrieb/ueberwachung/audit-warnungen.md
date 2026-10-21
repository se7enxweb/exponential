---
title: Audit-Warnungen
audience: government, admin, project-manager
category: public-sector
order: 70
summary: Die sieben Warnregeln, die mit dem Prüfprotokoll von Exponential ausgeliefert werden (Brute Force, eine vergebene Administratorrolle, außerhalb der Dienstzeit geschriebene Einstellungen, Massenlöschungen, ein abgeschaltetes Audit, eine gebrochene Kette), wer sie erhält, wie Sie sie abstimmen und testen und wie eine öffentliche Stelle sie an ihr Sicherheitsteam weiterleitet.
keywords: Audit-Warnungen, Warnregeln, Brute Force, admin_role_granted, Einstellungen außerhalb der Dienstzeit, Massenlöschung, Audit abgeschaltet, gebrochene Kette, Empfänger, Syslog, Webhook, SIEM, Sicherheitsüberwachung, audit alerts, alert rules, brute force, settings out of hours, mass delete, audit disabled, chain broken, recipients, security monitoring
type: help
---
# Audit-Warnungen

Diese Seite richtet sich an Informationssicherheitsbeauftragte und die Administration, die informiert werden müssen,
wenn auf einer öffentlichen Website etwas Riskantes geschieht. Das Prüfprotokoll wertet Warnregeln für seine Einträge aus
und sendet Warnungen an Syslog, per E-Mail oder an einen Webhook. Quellen: `settings/audit.ini` und
[der Audit-Leitfaden, Abschnitt 4.5](../../../../bc/6.0/audit.md#45-alarme-und-ihre-empfänger).

## Inhalt
1. [Die ausgelieferten Regeln](#die-ausgelieferten-regeln)
2. [Wer sie erhält](#wer-sie-erhält)
3. [Wo Sie sie sehen](#wo-sie-sie-sehen)
4. [Abstimmen und testen](#abstimmen-und-testen)
5. [Weiterleitung an Ihr Sicherheitsteam](#weiterleitung-an-ihr-sicherheitsteam)
6. [Verwandte Seiten](#verwandte-seiten)

## Die ausgelieferten Regeln

| Regel | Wird ausgelöst, wenn | Schweregrad | Senken |
|---|---|---|---|
| `brute_force` | 20 gescheiterte Anmeldungen aus einem Netz innerhalb von 300 Sekunden | critical | Syslog, E-Mail |
| `brute_force_user` | 10 gescheiterte Anmeldungen für ein Konto innerhalb von 900 Sekunden | alert | Syslog, E-Mail |
| `admin_role_granted` | eine Rolle mit `*/*`, `setup/*`, `role/*` oder `audit/manage` zugewiesen wird | critical | Syslog, E-Mail |
| `settings_out_of_hours` | eine Einstellung außerhalb der Arbeitstage 1-5, Uhrzeit 7-19 (Zeitzone der Website) geschrieben wird | warning | Syslog |
| `mass_delete` | ein Benutzer innerhalb von 600 Sekunden 500 Knoten entfernt (Unterknoten mitgezählt) | critical | Syslog, E-Mail |
| `audit_disabled` | das Audit abgeschaltet wird | emergency | Syslog |
| `chain_broken` | die Prüfung eine gebrochene Kette findet | alert | Syslog, E-Mail |

Warnungen werden ausgewertet, wenn Einträge geschrieben werden, und im Cronjob-Teil `audit` (häufiger Teil); stellen
Sie sicher, dass dieser Teil läuft ([Überwachung der Cronjobs](ueberwachung-der-cronjobs.md)).

## Wer sie erhält

E-Mails gehen an `Recipients[]` der Regel, sonst an `[AuditAlertSettings] Recipients[]`, sonst an
`[AuditSink_mail] Receivers[]`, sonst an die `AdminEmail` der Website. Empfänger können Adressen, benannte Gruppen
(`group:security`), Benutzer, Benutzergruppen oder eine Rolle (`role:Administrator`) sein. Höchstens eine E-Mail je
Regel und Empfänger innerhalb von 900 Sekunden. Prüfen Sie:

```bash
./console exp:audit alerts recipients --allow-root-user
./console exp:audit alerts list --allow-root-user
```

Verwenden Sie ein Funktionspostfach (zum Beispiel die gemeinsame Adresse des Sicherheitsteams) und nicht die Adresse
einer einzelnen Person, damit Warnungen auch bei Urlaub und Personalwechseln ankommen.

## Wo Sie sie sehen

- Reiter **Audit**, **Alarme** (`/audit/alerts`): ausgelöste Warnungen und die Regeln mit Art, Ereignis, Schwellenwert,
  Senken und Zustand.
- Das Feld Alarme im Audit-Dashboard.
- Syslog: Mit `Transport=local` erreichen die Einträge journald, `journalctl -t exponential`.

## Abstimmen und testen

Spielen Sie eine Regel gegen einen vergangenen Tag durch, bevor Sie sie ändern:

```bash
./console exp:audit alerts test mass_delete --replay=2026-09-01 --allow-root-user
```

Überschreiben Sie Schwellenwerte, Dienstzeiten (`BusinessDays`, `BusinessHours`) oder Empfänger in
`settings/override/audit.ini.append.php`, über Ihren Änderungsprozess. Typische Anpassungen im öffentlichen Sektor:
Dienstzeiten passend zu Ihren Servicezeiten; das Sicherheitsteam als Empfänger von `admin_role_granted` und
`brute_force`; `settings_out_of_hours` zusätzlich per E-Mail, wenn Einstellungen sich nie außerhalb eines Zeitfensters
ändern sollen.

## Weiterleitung an Ihr Sicherheitsteam

Wenn Ihre Organisation ein Security Operations Center oder ein SIEM betreibt, versorgen Sie es über die Syslog-Senke
oder die signierte Webhook-Senke (HMAC-SHA-256 über Zeitstempel und Inhalt, zwischengespeichert mit Wiederholungen)
statt per E-Mail ([Prüfprotokoll, „Sinks, alerts and archives“](../../../../funktionen/6.0/pruefprotokoll.md#sinks-alarme-und-archive)).
Vereinbaren Sie, was jede Warnung für das Team bedeutet und wer wen anruft; die [Seiten zu Vorfällen](../vorfaelle/README.md)
beschreiben die ersten Schritte.

## Verwandte Seiten
- [Prüfung des Prüfprotokolls](pruefung-des-audit-trails.md)
- [Prüfen, wer Audit-Warnungen erhält](../../aufgaben/betrieb/pruefen-wer-audit-warnungen-erhaelt.md)
- [Verdacht auf ein kompromittiertes Konto](../vorfaelle/verdacht-auf-kompromittiertes-konto.md)
- [Prüfprotokoll (Hosting)](../../../hosting/sicherheit/pruefprotokoll.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
