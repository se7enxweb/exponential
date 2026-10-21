---
title: Das Prüfprotokoll für „wer hat was geändert“
audience: government, admin, project-manager
category: public-sector
order: 150
summary: Wie eine öffentliche Stelle das Prüfprotokoll von Exponential (standardmäßig eingeschaltet, mit Hash-Kette und signierten Archiven) nutzt, um zu beantworten, wer Inhalte veröffentlicht, geändert, verschoben oder entfernt hat, wer Rollen und Einstellungen geändert hat, wie sie beweist, dass die Aufzeichnung unversehrt ist, und wie lange Einträge aufbewahrt werden.
keywords: Prüfprotokoll, Audit, Audit-Protokoll, wer hat was geändert, Rechenschaft, manipulationssicher, Hash-Kette, verify, exp:audit, Audit-Konsole, Aufbewahrung, Kanal content, Kanal access, audit trail, who changed what, accountability, tamper-evident, hash chain, audit console, retention, content channel, access channel
type: help
---
# Das Prüfprotokoll für „wer hat was geändert“

Diese Seite richtet sich an Administratoren, Informationssicherheitsbeauftragte und Führungskräfte, die die Frage „wer
war das?“ beantworten und zeigen müssen, dass man der Antwort trauen kann. Exponential zeichnet sicherheitsrelevante
Ereignisse in einem manipulationssicheren Protokoll auf, standardmäßig eingeschaltet, lesbar in der Administration und
auf der Kommandozeile ([Prüfprotokoll](../../../../eng-us/features/6.0/audit-trail.md); vollständige Referenz:
[der Audit-Leitfaden](../../../../eng-us/bc/6.0/audit.md)).

## Inhalt

1. [Was aufgezeichnet wird](#was-aufgezeichnet-wird)
2. [Herausfinden, wer eine Seite geändert hat](#herausfinden-wer-eine-seite-geändert-hat)
3. [Beweisen, dass die Aufzeichnung unversehrt ist](#beweisen-dass-die-aufzeichnung-unversehrt-ist)
4. [Wie lange Einträge aufbewahrt werden](#wie-lange-einträge-aufbewahrt-werden)
5. [Wer das Audit lesen darf](#wer-das-audit-lesen-darf)
6. [Datenschutz im Audit](#datenschutz-im-audit)

## Was aufgezeichnet wird

| Kanal | Beispiele |
|---|---|
| `content` | Veröffentlichen, Verschieben, Kopieren, Entfernen, Verbergen, Sektionen, Zustände, Papierkorb, Versionen, URL-Aliasse, Klassen, Inhaltsaufträge |
| `access` | Anmeldung, fehlgeschlagene Anmeldung, verweigerte Ansicht, Kontosperre, Rollenzuweisung, Zurücksetzen des Passworts |
| `system` | Schreiben von Einstellungen, Leeren von Caches, Läufe von Cronjobs, Befehle, Paketinstallationen, Wartung, Deployments |
| `commerce` | Bestellungen, Zahlungen (nie Kartendaten), Mehrwertsteuer, Exporte und Importe |
| `read` | stichprobenartige Ansichten, Suchen und Downloads, nur wenn das Aufzeichnen von Lesezugriffen eingeschaltet ist |

Das Entfernen eines Teilbaums ist ein übergeordnetes Ereignis mit einem untergeordneten pro Knoten, sodass „wer hat
diesen Ordner mit allem darin entfernt“ eine einzige Antwort ist.

## Herausfinden, wer eine Seite geändert hat

In der Administration: der Reiter **Audit** (`/audit/dashboard`), dann **Konsole**. Filtern Sie nach **Kanal**
`content`, **Ereignisname** (zum Beispiel `content.node.remove` oder einem Präfix wie `content.*`), **Objekt**, **Von**
und **Bis**, **Login** oder **Benutzer-ID**. Jede Zeile zeigt **Zeit**, **Ereignis**, **Akteur**, **Objekt**,
**Ergebnis** und **Anfrage**.

Auf der Kommandozeile:

```bash
./console exp:audit search --channel=content --name='content.node.*' --from=2026-10-01 --to=2026-10-15
./console exp:audit show <event id>
```

Kombinieren Sie das mit der Versionsgeschichte des Objekts, die den Inhalt vorher und nachher zeigt (siehe
„Versionsgeschichte als Rechenschaft“).

## Beweisen, dass die Aufzeichnung unversehrt ist

Jeder Eintrag trägt den Hash des Eintrags davor; tägliche Dateien werden abgeschlossen, archiviert und in Manifesten
aufgeführt, die mit einem HMAC signiert sind.

```bash
./console exp:audit verify               # live files; exit 1 when a chain is broken
./console exp:audit verify --archives    # everything
```

**Jetzt prüfen** in der Konsole tut dasselbe für die Kanäle, die Sie lesen dürfen. Führen Sie die Prüfung aus, bevor
Sie Einträge an jemanden übergeben (eine Prüferin, ein Gericht, eine Untersuchung). Meldet sie BROKEN: Ändern Sie
nichts, kopieren Sie das Verzeichnis und vergleichen Sie es mit Kopien, die den Server verlassen haben (Syslog,
Webhook-Empfänger). Einträge können auch schon beim Schreiben an ein Syslog oder einen signierten Webhook gehen,
sodass eine Kopie außerhalb des Servers existiert.

## Wie lange Einträge aufbewahrt werden

Ausgeliefert: 90 Tage aktiv und 730 Tage archiviert für content, access, system und commerce; 30 und 90 für read
(`settings/audit.ini`, Blöcke `[AuditChannel_<channel>]`, Schlüssel `LiveDays`, `ArchiveDays`). Wenn Ihr
Aufbewahrungsplan längere Fristen verlangt, setzen Sie die Werte in `settings/override/audit.ini.append.php` oder
exportieren Sie Einträge vor ihrem Ablauf in Ihr Archiv (`./console exp:audit export ...`, CSV oder JSON). Sichern Sie
die Override-Datei: Sie enthält die Signatur- und Pseudonymschlüssel.

## Wer das Audit lesen darf

`audit/read` (wahlweise nach Kanal beschränkt) und `audit/manage`; in einer neuen Installation hat nur Administrator
diese Rechte. Ein verweigerter Versuch wird selbst aufgezeichnet.

## Datenschutz im Audit

Adressen werden gekürzt, Sitzungen gehasht, Passwörter und Token nie aufgezeichnet; Einträge werden nach einer
gewissen Zeit pseudonymisiert. Um ein Auskunftsersuchen einer betroffenen Person zu beantworten, liefert
`./console exp:audit export --subject-user=<id>` die Einträge von oder über einen Benutzer
([Audit-Leitfaden 4.12](../../../../eng-us/bc/6.0/audit.md#412-privacy-and-gdpr-tasks)).

## Verwandte Seiten

- [Prüfprotokoll](../../../../eng-us/features/6.0/audit-trail.md)
- [Sicherheit und Audit](../../../anleitungen/sicherheit-und-audit.md)
- [Versionsgeschichte als Rechenschaft](versionsgeschichte-als-rechenschaft.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
