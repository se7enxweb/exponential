---
title: Einwilligungsnachweise für E-Mail
audience: government, admin, project-manager
category: public-sector
order: 110
summary: Für öffentliche Stellen, die Newsletter und Mitteilungen per E-Mail versenden - wie Exponential jede Änderung einer Einwilligung aufzeichnet (Zeit, Kategorie, alter und neuer Zustand, Quelle, genauer Wortlaut, handelnder Benutzer), wie Sie das Einwilligungsprotokoll lesen, filtern und exportieren, wer das darf und wie lange es aufbewahrt wird.
keywords: Einwilligungsprotokoll, Einwilligungsnachweise, DSGVO Artikel 7 Absatz 1, expmail_consent_log, E-Mail-Einstellungen, Wortlaut, Quelle, CSV-Export, exp:mail:consent, mailpreferences/export, Aufbewahrung, RetentionDays, Nachweis der Einwilligung, öffentlicher Sektor, consent log, records of consent, GDPR, proof of consent
type: help
---
# Einwilligungsnachweise für E-Mail

Diese Seite richtet sich an Administratoren und Datenschutzbeauftragte öffentlicher Stellen, die optionale E-Mails
versenden (Newsletter, Warnmeldungen, Beteiligungsverfahren). Wo die Einwilligung die Rechtsgrundlage ist, müssen Sie
nachweisen können, dass sie erteilt wurde. Exponential führt ein Einwilligungsprotokoll über jede Änderung der
E-Mail-Einstellungen einer Person
([E-Mail-Einstellungen für Administratoren: Das Einwilligungsprotokoll](../../../anleitungen/mail-einstellungen-administrator.md#8-das-einwilligungsprotokoll)).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Was aufgezeichnet wird](#was-aufgezeichnet-wird)
2. [Das Protokoll lesen](#das-protokoll-lesen)
3. [Exportieren](#exportieren)
4. [Wer das darf](#wer-das-darf)
5. [Aufbewahrung](#aufbewahrung)
6. [Verwandte Seiten](#verwandte-seiten)

## Was aufgezeichnet wird

Tabelle `expmail_consent_log`, eine Zeile pro Änderung ([Datenschutzstandards](../../../standards/datenschutz.md#dsgvo)):

| Feld | Beispiel |
|---|---|
| Zeit | wann die Änderung stattfand |
| Person | Adresse oder Benutzer |
| Kategorie | `newsletter`, `marketing`, `content` ... oder der Hauptschalter |
| Änderung | `off -> on`, `off -> pending`, `on -> off` |
| Quelle | die Person, ein Bestätigungslink, ein Administrator, ein Import, eine Abmeldung |
| Wortlaut | der genaue Text, der bei der Entscheidung angezeigt wurde |
| handelnder Benutzer, Siteaccess, IP-Adresse | wer die Änderung vorgenommen hat und wo |

Jede Änderung ist zugleich ein Audit-Ereignis, `access.user.consent.change`. Eine Änderung, die ein Administrator für
jemanden vornimmt, wird mit der Quelle „Von einem Administrator geändert“ und der Benutzer-ID des Administrators
aufgezeichnet.

## Das Protokoll lesen

**Setup > E-Mail-Einstellungen > Einwilligungsprotokoll**: neueste Einträge zuerst, filterbar nach Person (Adresse
oder Benutzer-ID), Kategorie, Quelle und Zeitraum; die Filter stehen in der Adresse, sodass sich eine Ansicht als
Lesezeichen speichern lässt. Auf der Konsole:

```bash
./console exp:mail:consent list --email=<address> --limit=10 --allow-root-user
```

Adressen sind in der Konsolenausgabe verborgen, sofern Sie nicht `--addresses` angeben.

## Exportieren

**Als CSV exportieren** exportiert genau das, was der Filter anzeigt. Die CSV-Datei enthält Adressen und IP-Adressen:
Speichern Sie sie wie alle personenbezogenen Daten und löschen Sie sie, sobald der Antrag, den sie beantwortet,
erledigt ist. Zellen, die mit `=`, `+`, `-` oder `@` beginnen, erhalten einen vorangestellten Apostroph, damit eine
Tabellenkalkulation sie nicht ausführt.

## Wer das darf

Zum Exportieren ist die Richtlinie `mailpreferences/export` nötig; geben Sie sie nur wenigen Personen. Zum Anzeigen der
Administrationsseiten ist `mailpreferences/administrate` nötig
([13.5 Der Administrations-Siteaccess](../../../installation/13-sicherheitshaertung.md#135-der-administrations-siteaccess)).

## Aufbewahrung

Der Cronjob-Teil `mailpreferences` (Gruppe `infrequent`) entfernt Zeilen des Einwilligungsprotokolls von Personen, die
nicht mehr da sind (ein anonymisierter Datensatz, ein entferntes Konto, eine Adresse ohne verbleibende Einstellung),
sobald sie älter sind als `mailpreferences.ini [ConsentSettings] RetentionDays` (1095 Tage: die Lebensdauer des Kontos
plus drei Jahre). Vorschau: `./console exp:mail:consent cleanup --dry-run --allow-root-user`. Zeilen von Personen, die
weiterhin E-Mails erhalten, bleiben als Nachweis ihrer aktuellen Einwilligung erhalten.

## Verwandte Seiten

- [E-Mail-Einstellungen für Administratoren](../../../anleitungen/mail-einstellungen-administrator.md)
- [E-Mail-Kategorien und Opt-in](e-mail-kategorien-und-opt-in.md)
- [Die Rechts-Checkliste für E-Mail-Einstellungen](../../../spezifikationen/6.0/mail-einstellungen-compliance.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
