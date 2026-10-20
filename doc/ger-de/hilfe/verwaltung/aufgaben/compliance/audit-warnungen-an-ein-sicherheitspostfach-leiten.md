---
title: Audit-Warnungen an ein Sicherheitspostfach leiten
audience: government, admin
category: public-sector
order: 180
summary: Schritt für Schritt - die Audit-Warnungen von Exponential (Brute Force, Administratorrolle vergeben, Massenlöschung, Audit ausgeschaltet, gebrochene Kette) an das Postfach Ihres Sicherheitsteams statt an die AdminEmail der Site senden und die Empfänger prüfen.
keywords: Audit-Warnungen, Empfänger der Warnungen, AlertRecipients, Sicherheitspostfach, exp:audit alerts recipients, AdminEmail, brute_force, admin_role_granted, mass_delete, audit_disabled, chain_broken, öffentlicher Sektor, audit alerts, alert recipients, security mailbox, public sector
type: help
---
# Audit-Warnungen an ein Sicherheitspostfach leiten

Für Administratoren. Standardmäßig gehen Warn-E-Mails an `[MailSettings] AdminEmail` der Site, oft eine Adresse des
Web-Teams. Schwerwiegende Ereignisse sollten die Menschen erreichen, die darauf reagieren
([Audit-Warnungen und ihre Empfänger](../../../../../eng-us/help/government/security/audit-alerts-and-recipients.md)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Ein Team-Postfach (zum Beispiel `security-team@example.gov`), das während und außerhalb der Bürozeiten so gelesen
  wird, wie Ihr Verfahren es vorsieht.
- Die Cronjob-Gruppe `frequent` läuft (Warnungen werden vom Teil der Cronjobs ausgewertet und verschickt).
- Der Versand von E-Mails funktioniert auf dem Server (im Zweifel zuerst ein Test mit dem Datei-Transport).

## Schritte

1. Prüfen Sie die aktuellen Empfänger:

   ```bash
   ./console exp:audit alerts recipients --allow-root-user
   ```

2. In `settings/override/audit.ini.append.php`:

   ```ini
   [AuditAlertSettings]
   Recipients[]
   Recipients[]=group:security

   [AlertRecipients_security]
   Addresses[]
   Addresses[]=security-team@example.gov
   Recipients[]
   Recipients[]=role:Administrator
   ```

3. Für nur eine Regel (zum Beispiel `settings_out_of_hours` an das Web-Team) setzen Sie
   `[AlertRule_<rule>] Recipients[]`.
4. Leeren Sie den INI-Cache: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`.

## Prüfen, ob es funktioniert hat

```bash
./console exp:audit alerts recipients --allow-root-user
./console exp:audit alerts recipients --rule=brute_force --allow-root-user
```

listet die Adressen mit den Einträgen, aus denen sie entstanden sind, und etwaige Probleme. Eine E-Mail geht je Regel und
Empfänger höchstens einmal in 900 Sekunden hinaus.

## Verwandte Seiten

- [Audit-Warnungen und ihre Empfänger](../../../../../eng-us/help/government/security/audit-alerts-and-recipients.md)
- [Anleitung zum Audit, 4.5](../../../../../eng-us/bc/6.0/audit.md#45-alerts-and-their-recipients)
- [Das Audit-Protokoll verifizieren](das-audit-protokoll-verifizieren.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
