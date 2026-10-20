---
title: Die Aufbewahrung des Audits verlängern
audience: government, admin
category: public-sector
order: 240
summary: Schritt für Schritt - die Audit-Archive von Exponential länger als die mitgelieferten 730 Tage aufbewahren, wenn Ihr Aufbewahrungsplan es verlangt, je Kanal, mit einem Archivverzeichnis auf einmal beschreibbarem Speicher, und das Ergebnis prüfen.
keywords: Aufbewahrung des Audits, ArchiveDays, LiveDays, ArchiveFormat, ArchiveDir, einmal beschreibbar, WORM, exp:ini set, Aufbewahrungsplan, Beweismittel, purge dry-run, öffentlicher Sektor, audit retention, write-once, records schedule, evidence, public sector
type: help
---
# Die Aufbewahrung des Audits verlängern

Für Administratoren, auf Entscheidung der Verantwortlichen für das Schriftgut. Die mitgelieferte Aufbewahrung beträgt je
Kanal 90 Tage live und 730 Tage archiviert; die mitgelieferten Kanalblöcke setzen beide Werte, also ändern Sie sie je
Kanal ([Aufbewahrung des Audits](../../../../../eng-us/help/government/privacy/audit-retention.md)).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Der beschlossene Zeitraum je Kanal (zum Beispiel `access` und `system` 3650 Tage).
- Speicherplatz: Prüfen Sie die Größe der Archive unter **Setup > Audit** > Archive; `xz` komprimiert stärker als das
  voreingestellte gzip.
- Optional ein einmal beschreibbarer Mount für die Archive.

## Schritte

1. Sehen Sie sich die Werte in der Vorschau an und setzen Sie sie:

   ```bash
   ./console exp:ini set audit.ini/AuditChannel_access/ArchiveDays 3650 global --dry-run --allow-root-user
   ./console exp:ini set audit.ini/AuditChannel_access/ArchiveDays 3650 global --allow-root-user
   ./console exp:ini set audit.ini/AuditChannel_access/ArchiveFormat xz global --allow-root-user
   ```

   Wiederholen Sie das für die anderen betroffenen Kanäle.
2. Verlegen Sie die Archive optional auf einen eigenen Mount, in `settings/override/audit.ini.append.php`:

   ```ini
   [AuditArchiveSettings]
   ArchiveDir=/srv/worm/exponential-audit
   ```

   (legen Sie ihn zuerst an, im Besitz des Benutzers der Site, Modus 0750).
3. Leeren Sie den INI-Cache; laden Sie PHP-FPM neu; starten Sie Velocity neu.

## Prüfen, ob es funktioniert hat

- `./console exp:audit purge --dry-run --allow-root-user` listet keine Archive mehr, die jünger als der neue Zeitraum
  sind.
- **Setup > Audit** (`/audit/settings`) zeigt die Werte und die Datei, aus der sie stammen.
- Bewahren Sie die Signaturschlüssel für den ganzen Zeitraum auf (siehe „Den Signaturschlüssel des Audits wechseln“).

## Verwandte Seiten

- [Aufbewahrung des Audits](../../../../../eng-us/help/government/privacy/audit-retention.md)
- [Langfristige Aufbewahrung des Audits](../../../../../eng-us/help/government/records-and-archiving/long-term-audit-retention.md)
- [Anleitung zum Audit, 4.3 Aufbewahrung](../../../../../eng-us/bc/6.0/audit.md#43-retention)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
