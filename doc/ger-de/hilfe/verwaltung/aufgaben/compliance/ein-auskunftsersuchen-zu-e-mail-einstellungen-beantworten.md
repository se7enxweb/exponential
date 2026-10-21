---
title: Ein Auskunftsersuchen zu E-Mail-Einstellungen beantworten
audience: government, admin
category: public-sector
order: 250
summary: Schritt für Schritt - einer Person ihre E-Mail-Einstellungen und ihre vollständige Einwilligungshistorie aus Exponential als JSON oder CSV geben, aus der Administration oder der Konsole, die Datei prüfen und sie sicher übergeben.
keywords: Auskunftsersuchen, Auskunftsrecht, DSAR, E-Mail-Einstellungen, Einwilligungshistorie, Export, JSON, CSV, exp:mail:preferences export, mailpreferences/export, DSGVO Artikel 15, öffentlicher Sektor, access request, subject access request, mail preferences, consent history, export, GDPR article 15, public sector
type: help
---
# Ein Auskunftsersuchen zu E-Mail-Einstellungen beantworten

Für Administratoren, die Datenschutzanfragen bearbeiten. Die E-Mail-Einstellungen und das Einwilligungsprotokoll sind
der Teil der Daten einer Person, den Exponential direkt exportiert ([Das Auskunftsrecht](../../datenschutz/auskunftsrecht.md)).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte in der Administration](#schritte-in-der-administration)
3. [Schritte auf der Konsole](#schritte-auf-der-konsole)
4. [Übergabe](#übergabe)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Die Identität der Person ist so geprüft, wie Ihr Verfahren es vorschreibt.
- Die Richtlinie `mailpreferences/export` (für Downloads) und Zugang zu Setup > E-Mail-Einstellungen.
- Sagen Sie der Person, dass sie dieselben Daten selbst auf ihrer Einstellungsseite herunterladen kann, ohne auf Sie zu
  warten.

## Schritte in der Administration

1. **Setup > E-Mail-Einstellungen** > **Ein Benutzer**: Finden Sie den Benutzer nach Name, Login, Adresse oder ID und
   öffnen Sie ihn.
2. Sie sehen seine Seite so, wie er sie sieht, mit seiner Einwilligungshistorie.
3. **Als JSON herunterladen** oder **Als CSV herunterladen**.

## Schritte auf der Konsole

Auch für eine Adresse ohne Konto:

```bash
./console exp:mail:preferences show --email=<address> --allow-root-user
./console exp:mail:preferences export --email=<address> --format=json --output=var/tmp/<case>/mail.json --allow-root-user
```

Die Datei wird mit dem Modus 0600 geschrieben. Die Ausgabe der Konsole verbirgt Adressen, sofern Sie nicht
`--addresses` hinzufügen.

## Übergabe

- Prüfen Sie die Datei: Sie sollte nur die Daten dieser Person enthalten.
- Senden Sie sie über einen Kanal, den Ihr Verfahren erlaubt; JSON für die Übertragbarkeit, sonst CSV oder eine lesbare
  Zusammenfassung.
- Löschen Sie die Arbeitsdatei, wenn die Anfrage abgeschlossen ist, und halten Sie fest, was Sie wann gesendet haben.
- Andere Daten (Konto, Formulareinsendungen, Audit) werden getrennt gesammelt; siehe „Audit-Einträge über eine Person
  exportieren“.

## Verwandte Seiten

- [Das Auskunftsrecht](../../datenschutz/auskunftsrecht.md)
- [E-Mail-Einstellungen für Administratoren: Anfragen](../../../../anleitungen/mail-einstellungen-administrator.md#6-anfragen-nach-dem-datenschutzrecht)
- [Einwilligungsnachweise für E-Mails](../../datenschutz/einwilligungsnachweise-fuer-e-mail.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
