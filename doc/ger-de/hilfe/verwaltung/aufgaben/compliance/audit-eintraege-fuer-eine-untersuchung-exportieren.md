---
title: Audit-Einträge für eine Untersuchung exportieren
audience: government, admin
category: public-sector
order: 160
summary: Schritt für Schritt - die Audit-Einträge auswählen, die eine Untersuchung braucht, die Ketten verifizieren, sie als signiertes Paket oder JSON Lines mit ihrem sha256 exportieren und die Übergabe dokumentieren, damit der Export als Beweismittel dienen kann.
keywords: Audit-Export, Untersuchung, Beweismittel, signiertes Paket, jsonl, sha256, exp:audit export, verifizieren, Beweiskette, Export im Hintergrund, MaxExportRecords, audit/read, öffentlicher Sektor, audit export, investigation, evidence, signed bundle, verify, chain of custody, background export, public sector
type: help
---
# Audit-Einträge für eine Untersuchung exportieren

Für Administratoren, die von Rechtsabteilung, Personalabteilung, Innenrevision oder Polizei um Aufzeichnungen darüber
gebeten werden, was auf der Site geschehen ist. Die Methode steht in
[Audit-Einträge als Beweismittel](../../schriftgut-und-archivierung/audit-eintraege-als-nachweis.md); diese Seite ist der Ablauf.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Eine schriftliche Anfrage, die den Umfang nennt (Personen, Objekte, Zeitraum) und wer den Export erhält.
- Shell-Zugang oder `audit/read` für den Export im Web.
- Ein privates Arbeitsverzeichnis, zum Beispiel `var/tmp/<case>/`, das nach der Übergabe entfernt wird.

## Schritte

1. Wenn der Zeitraum älter als 90 Tage ist, stellen Sie diese Tage je Kanal wieder her:
   `./console exp:audit restore --channel=<channel> --date=<YYYY-MM-DD> --allow-root-user`.
2. Verifizieren Sie: `./console exp:audit verify --allow-root-user` (fügen Sie `--archives` für archivierte Tage hinzu).
   Speichern Sie die Ausgabe.
3. Probieren Sie die Auswahl zuerst mit `search` aus, dann exportieren Sie:

   ```bash
   ./console exp:audit search --user=<id> --from=<YYYY-MM-DD> --to=<YYYY-MM-DD> --limit=20 --allow-root-user
   ./console exp:audit export --user=<id> --from=<YYYY-MM-DD> --to=<YYYY-MM-DD> --format=bundle --out=var/tmp/<case>/audit-bundle --allow-root-user
   ./console exp:audit export --object=node:<node id> --format=jsonl --out=var/tmp/<case>/node.jsonl --allow-root-user
   ```

   Jeder Export gibt die Zahl der Einträge und den sha256 der Datei aus.
4. Im Browser: Audit > **Exportieren** bietet den aktuellen Filter der Konsole als CSV, JSON Lines oder JSON an;
   **Im Hintergrund exportieren** für große Auswahlen (ein Inhaltsauftrag; die Datei erscheint auf der Exportseite für
   den Benutzer, der sie angefordert hat).
5. Schreiben Sie die Übergabenotiz: Anfrage, Umfang, Befehle, sha256-Werte, Ausgabe der Verifikation, Datum, Empfänger.
6. Übergeben Sie auf vereinbarten Datenträgern; entfernen Sie das Arbeitsverzeichnis.

## Prüfen, ob es funktioniert hat

- Berechnen Sie den sha256 der übergebenen Datei neu (`sha256sum <file>`) und vergleichen Sie ihn mit dem ausgegebenen
  Wert.
- Das Audit zeichnet den Export selbst als `system.audit.export` mit demselben sha256 auf.

## Verwandte Seiten

- [Audit-Einträge als Beweismittel](../../schriftgut-und-archivierung/audit-eintraege-als-nachweis.md)
- [Anleitung zum Audit: Exportieren](../../../../bc/6.0/audit.md#exportieren)
- [Das Audit-Protokoll verifizieren](das-audit-protokoll-verifizieren.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
