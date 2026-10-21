---
title: Einen archivierten Audit-Tag wiederherstellen
audience: government, admin
category: public-sector
order: 190
summary: Schritt für Schritt - Audit-Einträge lesen, die älter als die 90 Live-Tage sind, indem Sie den archivierten Tag mit exp:audit restore wiederherstellen, ihn gegen sein signiertes Manifest prüfen, ihn durchsuchen und wissen, was mit der wiederhergestellten Kopie geschieht.
keywords: Audit-Archive, wiederherstellen, exp:audit restore, archivierter Tag, Manifest, sha256, search --files, LiveDays, ArchiveDays, alte Einträge, Untersuchung, öffentlicher Sektor, audit archives, restore, archived day, manifest, old records, investigation, public sector
type: help
---
# Einen archivierten Audit-Tag wiederherstellen

Für Administratoren. Audit-Tage, die älter als `LiveDays` (90) sind, werden in Archive mit einem signierten Manifest
komprimiert; der Index der Konsole behält zwei Jahre an Zeilen. Um die Einträge eines archivierten Tages vollständig zu
lesen, stellen Sie ihn wieder her
([Anleitung zum Audit, 4.4 Verifizieren und wiederherstellen](../../../../bc/6.0/audit.md#44-prüfen-und-wiederherstellen)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Shell-Zugang als Benutzer der Site (oder als root mit `--allow-root-user`; die Dateien werden dann für den Benutzer
  der Site geschrieben).
- Der Kanal (`content`, `access`, `system`, `commerce`, `read`) und das Datum.
- Der Tag liegt noch innerhalb von `ArchiveDays` (standardmäßig 730); danach wurde er bereinigt.
- Das Wiederherstellen ist eine Änderung und wird als Audit-Ereignis aufgezeichnet; es wird verweigert, solange das
  Audit nicht schreiben kann, wenn `OnWriteFailure=refuse` gesetzt ist.

## Schritte

1. Sehen Sie nach, was archiviert ist: **Setup > Audit** > Archive (`/audit/archives`, Richtlinie `audit/manage`), oder
   listen Sie das Archivverzeichnis auf.
2. Stellen Sie wieder her:

   ```bash
   ./console exp:audit restore --channel=access --date=<YYYY-MM-DD> --allow-root-user
   ```

   Der Tag wird nach `<LogDir>/restored/` entpackt und gegen den sha256 des Manifests geprüft. `--to=<dir>` schreibt ihn
   an einen anderen Ort.
3. Lesen Sie ihn:

   ```bash
   ./console exp:audit search --files --channel=access --from=<YYYY-MM-DD> --to=<YYYY-MM-DD> --user=<id> --allow-root-user
   ```

## Prüfen, ob es funktioniert hat

- Der Befehl zum Wiederherstellen meldet die Datei und die Prüfung des sha256.
- `./console exp:audit verify --archives --channel=access --date=<YYYY-MM-DD> --allow-root-user` meldet den Tag als
  unversehrt.
- Entfernen Sie eine mit `--to` geschriebene Kopie, wenn die Aufgabe erledigt ist; sie enthält personenbezogene Daten.

## Verwandte Seiten

- [Aufbewahrung des Audits](../../datenschutz/aufbewahrung-von-audit-eintraegen.md)
- [Langfristige Aufbewahrung des Audits](../../schriftgut-und-archivierung/langfristige-aufbewahrung-von-audit-eintraegen.md)
- [Audit-Einträge für eine Untersuchung exportieren](audit-eintraege-fuer-eine-untersuchung-exportieren.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
