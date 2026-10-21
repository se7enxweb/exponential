---
title: Das Audit-Protokoll verifizieren
audience: government, admin, project-manager
category: public-sector
order: 180
summary: Schritt für Schritt - mit exp:audit status und verify (Live-Dateien und Archive) oder mit Jetzt prüfen auf dem Reiter Audit prüfen, dass das Audit-Protokoll vollständig und unverändert ist, und was mit dem Ergebnis zu tun ist.
keywords: Audit verifizieren, Hash-Kette, Integrität, exp:audit status, exp:audit verify, Archive, Jetzt prüfen, Nachweis, manipulationssicher, gebrochene Kette, audit verify, hash chain, integrity, archives, Verify now, evidence, tamper-evident, broken chain
type: help
---
# Das Audit-Protokoll verifizieren

Diese Aufgabe ist für Administratoren und Verantwortliche für das Audit, wöchentlich, nach einem Vorfall und bevor
Einträge an irgendjemanden übergeben werden. Am Ende haben Sie den Nachweis, dass das Protokoll weder bearbeitet noch
gekürzt wurde. Hintergrund: [Verifikation des Audit-Protokolls](../../../../../eng-us/help/government/operations/monitoring/audit-trail-verification.md).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Das Ergebnis lesen](#das-ergebnis-lesen)
4. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Shell-Zugang oder `audit/manage` für **Jetzt prüfen** im Browser.

## Schritte

```bash
./console exp:audit status --allow-root-user
./console exp:audit verify --allow-root-user
./console exp:audit verify --archives --allow-root-user     # also every archived day
```

`status` listet jeden Kanal (`content`, `access`, `system`, `commerce`, `read`) mit Dateien, Bytes, Einträgen von heute
und dem Zustand der Kette. `verify` gibt je Kanal `INTACT` aus; Exit-Code 0 unversehrt, 1 gebrochen, 2 Fehler. Im
Browser: Reiter **Audit**, Dashboard, **Jetzt prüfen**.

Für einen Tag eines Kanals: `--channel=access --date=2026-10-01`.

## Das Ergebnis lesen

| Ergebnis | Tun |
|---|---|
| Jeder Kanal `INTACT` | die Ausgabe mit Ihrer wöchentlichen Notiz oder dem Nachweispaket aufbewahren |
| `BROKEN` | nichts bearbeiten oder entfernen; Datei, Zeile und Art notieren; das Audit-Verzeichnis an einen sicheren Ort kopieren; mit Kopien aus Syslog oder Webhooks vergleichen; als möglichen Vorfall behandeln ([Vermutete Kompromittierung eines Kontos](../../../../../eng-us/help/government/operations/incidents/suspected-account-compromise.md)) |
| Fehler (Exit 2) | das Audit-Verzeichnis oder die Schlüssel lassen sich nicht lesen; Berechtigungen und `settings/override/audit.ini.append.php` prüfen |

Ein Absturz oder ein voller Datenträger kann die letzte Zeile einer Datei beschädigen; die Anleitung zum Audit erklärt
jede Art von Bruch ([Anleitung zum Audit, Abschnitt 4.4](../../../../bc/6.0/audit.md#44-prüfen-und-wiederherstellen)).

## Verwandte Seiten
- [Audit-Einträge für einen Prüfer exportieren](audit-eintraege-fuer-einen-pruefer-exportieren.md)
- [Prüfen, wer Audit-Warnungen erhält](pruefen-wer-audit-warnungen-erhaelt.md)
- [Archive und Sicherungen des Audit-Protokolls](../../../../../eng-us/help/government/operations/backups-and-recovery/audit-trail-archives-and-backups.md)
- [Audit-Protokoll (Hosting)](../../../hosting/sicherheit/pruefprotokoll.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
