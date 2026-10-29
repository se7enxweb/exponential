---
title: Archive des Prüfprotokolls und Datensicherung
audience: government, admin, hosting
category: public-sector
order: 120
summary: Für Sicherheitsbeauftragte, Verantwortliche für das Schriftgut und Betriebsteams in öffentlichen Stellen. Wie das Prüfprotokoll von Exponential Live-Dateien und signierte Archive führt, wie lange standardmäßig, wie Sie Nachweise jahrelang auf Speicher aufbewahren, den der Webserver nicht ändern kann, wie Sie ihn sichern und prüfen und wie Sie einen archivierten Tag lesen.
keywords: Prüfprotokoll, Audit-Archive, signierte Manifeste, Hash-Kette, ArchiveDir, LiveDays, ArchiveDays, WORM, Nachweis, exp:audit verify, exp:audit restore, Aufbewahrung, Schriftgut, Prüfpunkte, audit trail, audit archives, signed manifests, hash chain, evidence, retention, records, checkpoints
type: help
---
# Archive des Prüfprotokolls und Datensicherung

Diese Seite richtet sich an Sicherheitsbeauftragte, Verantwortliche für das Schriftgut und Betriebsteams in
öffentlichen Stellen, die das Prüfprotokoll einer Website als Nachweis aufbewahren müssen. Sie beantwortet, wie das
Prüfprotokoll seine Einträge speichert, wie lange es sie standardmäßig aufbewahrt, wie Sie sie länger auf Speicher
aufbewahren, den der Webserver nicht ändern kann, wie Sie sie sichern und ihre Unversehrtheit nachweisen und wie Sie
einen alten Tag lesen. Die Referenzen sind [Das Prüfprotokoll](../../../../funktionen/6.0/pruefprotokoll.md) und der
[Audit-Leitfaden](../../../../bc/6.0/audit.md).

## Inhalt

1. [Wie Einträge aufbewahrt werden](#wie-einträge-aufbewahrt-werden)
2. [Standardaufbewahrung](#standardaufbewahrung)
3. [Nachweise länger aufbewahren](#nachweise-länger-aufbewahren)
4. [Es sichern](#es-sichern)
5. [Alte Einträge prüfen und lesen](#alte-einträge-prüfen-und-lesen)
6. [Checkliste](#checkliste)

## Wie Einträge aufbewahrt werden

- Jeder Kanal (`content`, `access`, `system`, `commerce`, `read`) schreibt tägliche Dateien unter
  `var/<site>/log/audit/`.
- Jeder Eintrag trägt den Hash des vorherigen, eine Hash-Kette je Kanal.
- Die tägliche Aufgabe des Cronjob-Teils `audit` (in der Gruppe `frequent`) archiviert abgeschlossene Tage, die älter
  als `LiveDays` sind: geprüft, komprimiert, zurückgelesen und verglichen und mit einem signierten Manifest versehen.
  Erst dann wird die Live-Datei entfernt
  ([Audit-Leitfaden, Rotation und Archive](../../../../bc/6.0/audit.md#42-rotation-und-archive)).
- Archive gehen nach `[AuditArchiveSettings] ArchiveDir`, standardmäßig `log/audit/archive` unter dem var-Verzeichnis,
  mit dem Dateimodus `0440` und dem Verzeichnismodus `0750`.

## Standardaufbewahrung

| Kanal | `LiveDays` | `ArchiveDays` |
|---|---|---|
| content, access, system, commerce | 90 | 730 |
| read (nur wenn Lesezugriffe eingeschaltet sind) | 30 | 90 |

Der durchsuchbare Index behält Zeilen für `[AuditIndexSettings] KeepDays` (730). Die Aufbewahrungsregel entfernt nie
einen Signaturschlüssel.

## Nachweise länger aufbewahren

Öffentliche Stellen brauchen Audit-Einträge unter Umständen länger als zwei Jahre. Der Audit-Leitfaden gibt eine
Referenzkonfiguration für lange Aufbewahrung an
([Compliance: lange Aufbewahrung](../../../../bc/6.0/audit.md#66-compliance-lange-aufbewahrung)): `ArchiveDays=3650` je
Kanal, Archive auf einem Mount, dem der Webserver Dateien hinzufügen, den er aber nicht ändern kann (einmal
beschreibbarer Speicher oder ein Sicherungsziel), und die täglichen signierten Prüfpunkte, die über die
Webhook-Senke vom Server weggeschickt werden. In `settings/override/audit.ini.append.php`:

```ini
[AuditChannel_content]
ArchiveDays=3650

[AuditArchiveSettings]
ArchiveDir=/srv/worm/exponential-audit
```

Der Pfad ist ein Beispiel; stimmen Sie den Zeitraum mit Ihren Verantwortlichen für das Schriftgut und Ihrer
Rechtsberatung ab.

## Es sichern

- Schließen Sie das Live-Verzeichnis und das Archivverzeichnis in die Dateisicherung ein
  ([Sicherung von Dateien und Einstellungen](sicherung-von-dateien-und-einstellungen.md)).
- Schließen Sie `settings/override/audit.ini.append.php` ein: Ohne die Signaturschlüssel lassen sich Archive nicht
  prüfen ([Schlüssel und Geheimnisse in Datensicherungen](schluessel-und-geheimnisse-in-datensicherungen.md)).
- Behalten Sie die Aufbewahrung der Einträge selbst im Blick: Eine Datensicherung, die länger als `ArchiveDays`
  aufbewahrt wird, bewahrt auch die Einträge länger auf.
- Mehrere Webserver müssen sich eine Schlüsseldatei teilen.

## Alte Einträge prüfen und lesen

```bash
./console exp:audit verify --allow-root-user                # live files; exit 1 when a chain is broken
./console exp:audit verify --archives --allow-root-user     # also every archive and its signed manifest
./console exp:audit restore --apply --channel=content --date=2026-06-02 --allow-root-user
```

`restore` entpackt einen archivierten Tag zum Lesen nach `<LogDir>/restored/` und prüft ihn gegen das Manifest; es
fügt ihn nie wieder in die Live-Kette ein. Prüfen Sie, bevor Sie Einträge an irgendjemanden übergeben. Wenn eine
Prüfung BROKEN meldet: Bearbeiten oder entfernen Sie nichts, kopieren Sie das Verzeichnis an einen sicheren Ort und
vergleichen Sie mit Kopien, die den Server verlassen haben (Syslog, Webhook-Empfänger, Archive)
([Audit-Leitfaden, Prüfen und Wiederherstellen](../../../../bc/6.0/audit.md#44-prüfen-und-wiederherstellen)).

Zur Übergabe von Einträgen an Prüfende siehe den Export des Prüfprotokolls, der ein signiertes Paket schreibt.

## Checkliste

- [ ] Die Cronjob-Gruppe `frequent` läuft, sodass Archive täglich erstellt und geprüft werden.
- [ ] `ArchiveDays` und `ArchiveDir` entsprechen der vereinbarten Aufbewahrung und dem vereinbarten Speicher.
- [ ] Audit-Verzeichnisse und die Schlüsseldatei sind in der Datensicherung enthalten.
- [ ] `exp:audit verify --archives` läuft planmäßig und alarmiert bei einem Fehler.
- [ ] Das Verfahren bei einem BROKEN-Ergebnis ist bekannt.

## Verwandte Seiten

- [Datensicherung und Wiederherstellung](README.md): Übersicht dieses Ordners
- [Prüfprotokoll für Hoster](../../../hosting/sicherheit/pruefprotokoll.md)
- [Sicherheit und Audit](../../../../anleitungen/sicherheit-und-audit.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
