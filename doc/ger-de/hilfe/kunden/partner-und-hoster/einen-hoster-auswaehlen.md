---
title: Einen Hoster auswählen
audience: customer, hosting, businesses
category: customer-success
order: 4
summary: Wie der Betreiber einer laufenden Exponential-Website einen neuen Hoster auswählt - was die Website braucht, die Fragen, die über das Hosting-Modell entscheiden, was über die Technik hinaus zu prüfen ist, wie man Angebote vergleicht und wie der Umzug abläuft.
keywords: Hoster auswählen, Hosting, Hosting-Modell, VPS, Shared Hosting, Managed Hosting, Velocity, PHP, Datenbank, Umzug, Migration, choose a host, hosting model, shared hosting, managed hosting, database, move, migration
type: help
---
# Einen Hoster auswählen

Diese Seite ist für einen Betreiber, der bereits eine Exponential-Website betreibt und sie umziehen möchte, oder der
einen Hoster für eine Website braucht, die ein Partner gerade baut. Der vollständige Leitfaden für Unternehmen ist
[Hosting auswählen](../../../../eng-us/help/businesses/choosing/choosing-hosting.md), und die Hosting-Modelle werden unter
[Hosting-Modelle im Vergleich](../../../../eng-us/help/businesses/choosing/hosting-models-compared.md) verglichen. Diese Seite ergänzt,
was zählt, wenn eine Website, ihre Daten und ihre Geschichte bereits existieren.

## Was die Website braucht

Aus [Hosting auswählen](../../../../eng-us/help/businesses/choosing/choosing-hosting.md):

| Bedarf | Mindestens |
|---|---|
| PHP | 8.0; empfohlen wird das neueste 8.x, das das System anbietet; Velocity läuft ebenfalls unter 8.0 (ab Release 0.0.4.54) |
| Datenbank | Eine der unterstützten Engines; das Projekt listet sie in einer offiziellen Reihenfolge |
| Webserver | Keiner, wenn Velocity die Website ausliefert; oder Apache, nginx und andere |
| Composer | 2.x |
| Cron | Ein Job alle paar Minuten |
| E-Mail | Ausgehende E-Mail |
| HTTPS | Für jede öffentliche Website |
| Backups | Datenbank, hochgeladene Dateien, Einstellungen, Schlüssel, Prüfprotokoll |

Notieren Sie, was Ihre Website heute verwendet: **Setup > Systeminformationen** zeigt die PHP-Version, die Datenbank,
die Engine, die geantwortet hat, und mehr ([Systeminformationen](../../../anleitungen/systeminformationen.md)). Ein neuer
Hoster sollte mindestens dasselbe bieten, oder einen geplanten Upgrade-Pfad.

## Die Fragen, die über das Modell entscheiden

Aus [Hosting auswählen](../../../../eng-us/help/businesses/choosing/choosing-hosting.md):

| Frage | Wenn ja |
|---|---|
| Muss mehr als ein Rechner in die Datenbank schreiben? | Ein Datenbankserver; nicht SQLite |
| Ist das Hosting ohne dauerhaft laufende Prozesse (typisches Shared Hosting)? | Apache oder nginx mit PHP-FPM; Velocity meist nicht |
| Ist das PHP des Rechners 8.0? | Jeder Webserver; Velocity läuft ab seinem Release 0.0.4.54 unter 8.0 |
| Gibt es keinen Shell-Zugriff? | Der Einrichtungsassistent im Browser, und weniger Möglichkeiten für Updates |

> **Tipp**
> „Die richtige Frage ist nicht nur ‚Welche Technik ist die beste‘, sondern ‚Wer kann sie betreiben, sichern und
> wiederherstellen‘“ ([Hosting auswählen](../../../../eng-us/help/businesses/choosing/choosing-hosting.md)).

## Über die Technik hinaus

Verwenden Sie die Fragen unter [Was Sie Ihren Hoster fragen sollten](../gesund-halten/was-sie-ihren-hoster-fragen-sollten.md). Diese
entscheiden am häufigsten:

- [ ] Wer spielt Updates für Exponential, PHP und das System ein, und wie schnell nach einem Sicherheits-Release?
- [ ] Backups: was, wie oft, wo, und wann war der letzte Wiederherstellungstest?
- [ ] Überwachung von außen, und wer wird nachts alarmiert?
- [ ] Support-Zeiten und erste Reaktionszeiten, schriftlich.
- [ ] Wo die Server und Backups stehen (für den Datenschutz).
- [ ] Hat der Hoster schon Exponential-Websites betrieben? Hosting-Partner werden vom Projekt aufgelistet
      ([Hosting-Partner](../../partner/hosting-partner.md)).
- [ ] Wie Sie wieder gehen: Exportformat, Dauer, Löschung ([Ausscheiden eines Kunden](../../hosting/checklisten/checkliste-fuer-das-offboarding-eines-kunden.md)).

## Angebote fair vergleichen

| Vergleichen Sie | Nicht |
|---|---|
| Was enthalten ist: Updates, Backups, Wiederherstellungen, Überwachung, Support-Zeiten | Nur den Monatspreis |
| Schriftliche Reaktions- und Wiederherstellungszeiten | „Schnellen Support“ |
| Die Ausstiegsbedingungen | Nur die Startbedingungen |
| Ihre Antwort auf „Wer stellt wieder her, und wie lange hat es beim letzten Mal gedauert?“ | Eine Liste von Funktionen |

Die Kostenseite steht unter [Kostentreiber beim Hosting](../../../../eng-us/help/businesses/costs/hosting-cost-drivers.md).

## Der Umzug

Ein Umzug ohne Versionswechsel folgt [Eine Website zwischen Hostern umziehen](../../hosting/migrationen/eine-website-zwischen-hostern-umziehen.md):
was zu kopieren ist, was neu zu holen ist, die Einstellungen, die den alten Server nennen, und die Prüfungen danach.
Ihr Teil:

- [ ] Vereinbaren Sie einen Termin außerhalb geschäftiger Zeiten, mit einem Wartungsfenster
      ([Wartungsmodus und geplante Arbeiten](../gesund-halten/wartungsmodus-und-geplante-arbeiten.md)).
- [ ] Bitten Sie den alten Hoster um einen vollständigen Export (Datenbank, Dateien, Einstellungen, Versionen) und
      bewahren Sie selbst eine Kopie auf.
- [ ] Lassen Sie das Datenbankpasswort beim neuen Hoster ändern; die Einstellungen, die es enthalten, reisen mit dem
      Export ([Ausscheiden eines Kunden](../../hosting/checklisten/checkliste-fuer-das-offboarding-eines-kunden.md)).
- [ ] Gehen Sie nach dem Umzug Ihre Testliste durch ([Checkliste für den Upgrade-Tag](../gesund-halten/checkliste-upgrade-tag.md)).
- [ ] Behalten Sie die alte Website, bis die neue bestätigt ist, und bitten Sie dann um Löschung und Bestätigung.

## Verwandte Seiten

- [Einen Partner auswählen](einen-partner-auswaehlen.md)
- [Verträge und SLAs](vertraege-und-slas.md)
- [Kunden auf Ihr Hosting umziehen (für Hoster)](../../hosting/migrationen/kunden-zu-ihrem-hosting-migrieren.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
