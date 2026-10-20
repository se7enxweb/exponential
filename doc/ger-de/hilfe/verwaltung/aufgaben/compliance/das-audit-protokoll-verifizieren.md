---
title: Das Audit-Protokoll verifizieren
audience: government, admin, hosting
category: public-sector
order: 170
summary: Schritt für Schritt - mit exp:audit verify oder Jetzt prüfen kontrollieren, dass kein Audit-Eintrag geändert, entfernt oder eingefügt wurde, die Prüfung über ihren Exit-Code ins Monitoring aufnehmen und wissen, was zu tun ist, wenn ein Kanal gebrochen ist.
keywords: Audit verifizieren, Hash-Kette, unversehrt, gebrochen, exp:audit verify, Jetzt prüfen, Monitoring, Exit-Code, Archive, manipulationssicher, Vorfall, öffentlicher Sektor, audit verify, hash chain, intact, broken, Verify now, monitoring, exit code, archives, tamper evident, incident, public sector
type: help
---
# Das Audit-Protokoll verifizieren

Für Administratoren und Hosting-Teams. Die Hash-Ketten des Audit-Protokolls machen Manipulationen sichtbar, aber nur,
wenn jemand prüft. Der Teil der Cronjobs verifiziert täglich; diese Aufgabe behandelt Prüfungen auf Abruf und im
Monitoring ([Integrität und Verifikation des Audits](../../../../../eng-us/help/government/security/audit-integrity-and-verification.md)).

## Inhalt

1. [Schritte](#schritte)
2. [Im Monitoring](#im-monitoring)
3. [Wenn ein Kanal gebrochen ist](#wenn-ein-kanal-gebrochen-ist)
4. [Verwandte Seiten](#verwandte-seiten)

## Schritte

Auf der Kommandozeile:

```bash
./console exp:audit verify --allow-root-user                  # live files
./console exp:audit verify --archives --allow-root-user       # archives as well
./console exp:audit verify --channel=access --allow-root-user
```

Jeder Kanal gibt **INTACT** mit der Zahl der Einträge und übereinstimmenden Checkpoints aus, oder **BROKEN** mit Datei,
Zeile und Art.

Im Browser (Richtlinie `audit/manage`): Dashboard oder Konsole des Audits, **Jetzt prüfen**. Mit
`[AuditConsoleSettings] ReauthForManage=enabled` wird zuerst Ihr Passwort abgefragt.

## Im Monitoring

Exit-Code 0 = unversehrt, 1 = gebrochen, 2 = Fehler:

```bash
./console exp:audit verify -q --allow-root-user || <your alert command>
```

Führen Sie es täglich aus Ihrem Monitoring-System als Benutzer der Site aus; bewahren Sie die Ausgabe eines monatlichen
Laufs mit `--archives` auf.

## Wenn ein Kanal gebrochen ist

1. Bearbeiten oder entfernen Sie nichts.
2. Notieren Sie Datei, Zeile und Art; kopieren Sie das Audit-Verzeichnis auf schreibgeschützten Speicher.
3. Vergleichen Sie mit Kopien, die den Server verlassen haben (SIEM, Webhook-Empfänger, Checkpoints).
4. Behandeln Sie es als Sicherheitsvorfall ([Reaktion auf Vorfälle](../../../../../eng-us/help/government/security/incident-response.md)).

Eine abgerissene letzte Zeile, gefolgt von einer aufgezeichneten Reparatur, erscheint als `repaired`; das ist kein
Bruch.

## Verwandte Seiten

- [Integrität und Verifikation des Audits](../../../../../eng-us/help/government/security/audit-integrity-and-verification.md)
- [Anleitung zum Audit, 4.4 Verifizieren und wiederherstellen](../../../../../eng-us/bc/6.0/audit.md#44-verify-and-restore)
- [Das Audit-Protokoll an ein SIEM weiterleiten](../../../../../eng-us/help/government/security/forwarding-audit-to-a-siem.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
