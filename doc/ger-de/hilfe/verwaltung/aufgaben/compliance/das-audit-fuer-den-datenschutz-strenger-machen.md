---
title: Das Audit für den Datenschutz strenger machen
audience: government, admin
category: public-sector
order: 210
summary: Schritt für Schritt - das Profil für strengen Datenschutz aus der Anleitung zum Audit anwenden (gehashte Logins und Adressen, keine User Agents, nur Schlüssel bei Vorher- und Nachher-Werten, 30 Tage bis zur Pseudonymisierung, kürzere Aufbewahrung), wo Ihr Datenschutzbeauftragter weniger personenbezogene Daten im Audit verlangt.
keywords: Datenschutz im Audit, strenger Datenschutz, AuditPrivacySettings, Field, hash, off, truncate, BeforeAfter keys, PseudonymiseAfterDays, LiveDays, ArchiveDays, KeepDays, Datenminimierung, öffentlicher Sektor, audit privacy, strict privacy, data minimization, public sector
type: help
---
# Das Audit für den Datenschutz strenger machen

Für Administratoren, mit der Entscheidung des Datenschutzbeauftragten. Das mitgelieferte Audit minimiert bereits
(Adressen gekürzt, Sitzungen gehasht, keine Inhaltswerte). Das Profil für strengen Datenschutz aus der Anleitung behält
noch weniger ([Datenschutz im Audit-Protokoll](../../../../../eng-us/help/government/privacy/audit-trail-privacy.md)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Wägen Sie Untersuchungen ab: Mit gehashten Logins können Sie einen bekannten Login weiterhin zuordnen, aber nicht
  allein aus dem Eintrag ablesen, wer gehandelt hat; mit `BeforeAfter=keys` sehen Sie, welche Felder sich geändert
  haben, nicht deren Werte.
- Die Einstellungen gelten für neue Einträge; bestehende Dateien werden nicht umgeschrieben (das würde die Kette
  brechen).
- Sichern Sie zuerst `settings/override/audit.ini.append.php`.

## Schritte

1. In `settings/override/audit.ini.append.php`:

   ```ini
   [AuditPrivacySettings]
   Field[actor.login]=hash
   Field[actor.ip]=hash
   Field[actor.ua]=off
   Field[actor.cli.os_user]=hash
   Field[request.url]=truncate
   Field[request.host]=hash
   Field[object.name]=truncate
   PseudonymiseAfterDays=30

   [AuditRecordSettings]
   BeforeAfter=keys

   [AuditChannel_access]
   LiveDays=30
   ArchiveDays=180

   [AuditIndexSettings]
   KeepDays=180
   ```

   Wiederholen Sie den Kanalblock für `content`, `system` und `commerce` mit der Aufbewahrung, die Ihr
   Datenschutzbeauftragter gewählt hat.
2. Leeren Sie den INI-Cache, laden Sie PHP-FPM neu, starten Sie Velocity neu.
3. Sehen Sie sich die Pseudonymisierung bestehender Indexzeilen in der Vorschau an:
   `./console exp:audit pseudonymise --dry-run --allow-root-user`.

## Prüfen, ob es funktioniert hat

- **Setup > Audit** (`/audit/settings`) zeigt die Werte und ihre Datei.
- Eine neue Anmeldung erscheint in `./console exp:audit tail --channel=access --lines=3 --allow-root-user` mit einem
  gehashten Login (`h:...`) und ohne User Agent.
- Die Warnungen bei Brute Force funktionieren weiterhin: Sie gruppieren nach der gehashten Adresse.

## Verwandte Seiten

- [Anleitung zum Audit, 6.4 Strenger Datenschutz](../../../../../eng-us/bc/6.0/audit.md#64-strict-privacy)
- [Datenschutz im Audit-Protokoll](../../../../../eng-us/help/government/privacy/audit-trail-privacy.md)
- [Aufbewahrung des Audits](../../../../../eng-us/help/government/privacy/audit-retention.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
