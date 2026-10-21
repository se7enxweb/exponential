---
title: Prüfen, wer Audit-Warnungen erhält
audience: government, admin, project-manager
category: public-sector
order: 190
summary: Schritt für Schritt - die Regeln der Audit-Warnungen und die Adressen auflisten, an die jede schreibt, die Empfänger so korrigieren, dass Warnungen ein Team-Postfach statt eines Platzhalters oder einer einzelnen Person erreichen, und eine Regel gegen einen vergangenen Tag testen.
keywords: Audit-Warnungen, Empfänger, AdminEmail, Warnregeln, exp:audit alerts, Sicherheitsteam, Postfach, AuditAlertSettings, Wiederholung, Test, audit alerts, recipients, alert rules, security team, mailbox, replay, test
type: help
---
# Prüfen, wer Audit-Warnungen erhält

Diese Aufgabe ist für Administratoren und Sicherheitsbeauftragte, einmal beim Go-live und nach Personalwechseln. Am Ende
erreicht jede Warnung die Menschen, die darauf reagieren müssen. Hintergrund:
[Audit-Warnungen](../../../../../eng-us/help/government/operations/monitoring/audit-alerts.md).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Die Empfänger ändern](#die-empfänger-ändern)
4. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Shell-Zugang; die Änderung der Einstellungen läuft über Ihren Änderungsprozess.

## Schritte

```bash
./console exp:audit alerts list --allow-root-user
./console exp:audit alerts recipients --allow-root-user
./console exp:audit alerts recipients --rule=admin_role_granted --allow-root-user
```

`recipients` listet jede Adresse mit den Einträgen, aus denen sie entstanden ist, und etwaige Probleme (eine unbekannte
Gruppe, ein deaktivierter Benutzer, eine fehlende Rolle). Ohne Konfiguration gehen die E-Mails an
`site.ini [MailSettings] AdminEmail`, das auf vielen Installationen noch ein Platzhalter ist.

## Die Empfänger ändern

In `settings/override/audit.ini.append.php`, für alle Regeln oder je Regel:

```ini
[AuditAlertSettings]
Recipients[]
Recipients[]=address:security-team@example.gov

[AlertRule_admin_role_granted]
Recipients[]
Recipients[]=group:security
```

Empfänger können `address:`, `group:<name>` (eine Liste `[AlertRecipients_<name>]`), `user:<id>`, `login:<login>`,
`usergroup:<id>` oder `role:<name>` sein. Bevorzugen Sie ein Team-Postfach oder eine Rolle, damit Abwesenheiten und
Personalwechsel die Warnungen nicht unterbrechen. Leeren Sie danach den INI-Cache
(`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`).

## Prüfen, ob es funktioniert hat

- `./console exp:audit alerts recipients --allow-root-user` zeigt die neuen Adressen und keine Probleme.
- Spielen Sie eine Regel gegen einen vergangenen Tag ab, um zu sehen, was sie gesendet hätte:

  ```bash
  ./console exp:audit alerts test mass_delete --replay=2026-09-01 --allow-root-user
  ```

- Die E-Mails werden vom Teil `audit` der Cronjobs gesendet; stellen Sie sicher, dass er läuft
  ([Prüfen, ob die Cronjobs laufen](pruefen-ob-die-cronjobs-laufen.md)).

## Verwandte Seiten
- [Das Audit-Protokoll verifizieren](das-audit-protokoll-verifizieren.md)
- [Audit-Warnungen](../../../../../eng-us/help/government/operations/monitoring/audit-alerts.md)
- [Probleme mit E-Mails](../../../../../eng-us/help/government/operations/incidents/mail-problems.md)
- [Anleitung zum Audit, Abschnitt 4.5](../../../../bc/6.0/audit.md#45-alarme-und-ihre-empfänger)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
