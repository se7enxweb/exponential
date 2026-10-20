---
title: Audit-Einträge über eine Person exportieren
audience: government, admin
category: public-sector
order: 260
summary: Schritt für Schritt - für ein Auskunftsersuchen jeden Audit-Eintrag exportieren, der von oder über einen Benutzer erstellt wurde (pseudonymisierte eingeschlossen), mit exp:audit export --subject-user, ihn auf Daten anderer Personen prüfen und übergeben.
keywords: Audit-Export, subject-user, Auskunftsersuchen, DSAR, DSGVO Artikel 15, pseudonymisiert, jsonl, csv, Prüfung, Schwärzung, exp:audit, öffentlicher Sektor, audit export, access request, GDPR article 15, pseudonymized, review, redaction, public sector
type: help
---
# Audit-Einträge über eine Person exportieren

Für Administratoren, die ein Auskunftsersuchen einer Mitarbeiterin, eines Mitarbeiters oder eines registrierten
Benutzers beantworten. Das Audit hat dafür einen eigenen Export
([Anleitung zum Audit, 4.12 Aufgaben zu Datenschutz und DSGVO](../../../../../eng-us/bc/6.0/audit.md#412-privacy-and-gdpr-tasks)).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, bevor Sie senden](#prüfen-bevor-sie-senden)
4. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Die Benutzer-ID der Person (Benutzer > Benutzerkonten prüfen oder der Knoten des Benutzers).
- Shell-Zugang; ein privates Arbeitsverzeichnis `var/tmp/<case>/`.
- Einträge, die älter als 90 Tage sind, werden aus dem Index gefunden, solange er sie behält (730 Tage); für die
  vollständigen Werte älterer archivierter Tage stellen Sie diese zuerst wieder her („Einen archivierten Audit-Tag
  wiederherstellen“).

## Schritte

```bash
./console exp:audit export --subject-user=<id> --format=jsonl --out=var/tmp/<case>/audit-<id>.jsonl --allow-root-user
./console exp:audit export --subject-user=<id> --format=csv --out=var/tmp/<case>/audit-<id>.csv --allow-root-user
```

Der Befehl gibt die Zahl der Einträge und den sha256 aus. Pseudonymisierte Einträge sind enthalten. Der Export wird
selbst aufgezeichnet (`system.audit.export`).

## Prüfen, bevor Sie senden

- Die Warnung der Anleitung: Die Datei „enthält auch Einträge über Handlungen anderer Personen an diesem Benutzer“ (zum
  Beispiel eines Administrators, der das Konto geändert hat). Entfernen oder schwärzen Sie die Daten anderer Personen,
  wie Ihr Recht es verlangt.
- Erklären Sie die Felder in einer kurzen Notiz (Zeit in UTC, Name des Ereignisses, Ergebnis, gekürzte Netzwerkadresse).
- Einträge werden während ihrer Aufbewahrung nicht auf Antrag gelöscht; sagen Sie, bis wann sie aufbewahrt werden.
- Löschen Sie die Arbeitsdateien nach der Übergabe; halten Sie fest, was gesendet wurde.

## Verwandte Seiten

- [Das Auskunftsrecht](../../../../../eng-us/help/government/privacy/right-of-access.md)
- [Datenschutz im Audit-Protokoll](../../../../../eng-us/help/government/privacy/audit-trail-privacy.md)
- [Ein Auskunftsersuchen zu E-Mail-Einstellungen beantworten](ein-auskunftsersuchen-zu-e-mail-einstellungen-beantworten.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
