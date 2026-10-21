---
title: Die Sperrliste
audience: government, admin
category: public-sector
order: 140
summary: Für Administratoren - wie die E-Mail-Sperrliste von Exponential optionale E-Mails an harte Unzustellbarkeiten, Beschwerden, Anträge „alles stoppen“ und rechtliche Anträge blockiert und dabei nur einen gesalzenen Hash jeder Adresse speichert, wie Sie Einträge hinzufügen, prüfen und aufheben, und ihre Aufbewahrung.
keywords: Sperrliste, keine E-Mail senden, harte Unzustellbarkeit, Hard Bounce, Beschwerde, rechtlicher Antrag, gesalzener Hash, exp:mail:suppression, Bounce-Leser, Blacklist, Newsletter, Aufbewahrung, alle E-Mails stoppen, öffentlicher Sektor, suppression list, do not mail, hard bounce, complaint, legal request
type: help
---
# Die Sperrliste

Diese Seite richtet sich an Administratoren, die E-Mail-Anträge bearbeiten. An eine Adresse auf der Sperrliste geht
keine optionale E-Mail: harte Unzustellbarkeiten, Beschwerden (als Spam markiert), Anträge „alle optionalen E-Mails
stoppen“, rechtliche Anträge, von einem Administrator angelegte Einträge und die Newsletter-Blacklist. Notwendige
E-Mails gehen weiterhin hinaus
([E-Mail-Einstellungen für Administratoren: Die Sperrliste](../../../anleitungen/mail-einstellungen-administrator.md#7-die-sperrliste)).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Datenschutz durch Technikgestaltung](#datenschutz-durch-technikgestaltung)
2. [Einträge verwalten](#einträge-verwalten)
3. [Unzustellbarkeiten](#unzustellbarkeiten)
4. [Aufbewahrung](#aufbewahrung)
5. [Verwandte Seiten](#verwandte-seiten)

## Datenschutz durch Technikgestaltung

Von jeder Adresse wird nur ein gesalzener Hash gespeichert, sodass die Liste nicht gelesen werden kann; einen Eintrag
finden Sie, indem Sie die Adresse eingeben. So kann eine öffentliche Stelle dem Wunsch „schreiben Sie mir nie wieder“
nachkommen, ohne eine lesbare Liste der Menschen zu führen, die darum gebeten haben.

## Einträge verwalten

**Setup > E-Mail-Einstellungen > Sperrliste**: **Eine Adresse prüfen**, **Eine Adresse hinzufügen** (mit einem Grund
und einer Notiz ohne personenbezogene Daten), einen Eintrag **Aufheben**. Auf der Konsole:

```bash
./console exp:mail:suppression check --email=<address> --allow-root-user
./console exp:mail:suppression add --email=<address> --reason=legal --note="<case reference, no personal data>" --allow-root-user
./console exp:mail:suppression list --reason=legal --limit=5 --allow-root-user
./console exp:mail:suppression lift --email=<address> --allow-root-user
```

Eine Person, die selbst alle E-Mails gestoppt hat, hebt ihren eigenen Eintrag auf, indem sie optionale E-Mails wieder
einschaltet. Einträge für Unzustellbarkeiten, Beschwerden und rechtliche Anträge bleiben, bis ein Administrator sie
aufhebt, und sie stoppen auch E-Mails „Link zusenden“ und Bestätigungs-E-Mails.

## Unzustellbarkeiten

Der Bounce-Leser liest ein in `[BounceSettings]` eingestelltes Postfach für Unzustellbarkeitsmeldungen und fügt harte
Unzustellbarkeiten automatisch hinzu; er tut nichts, solange sein Postfach nicht eingetragen ist, und die Statusseite
zeigt seinen letzten Lesevorgang. Ohne ihn fügen Sie harte Unzustellbarkeiten von Hand mit dem Grund `bounce` hinzu.
Die Newsletter-Erweiterung (cjw_newsletter 4.2) nutzt denselben Leser.

## Aufbewahrung

Einträge werden standardmäßig unbegrenzt aufbewahrt (`[SuppressionSettings] RetentionDays=0`), denn einen Eintrag zu
vergessen hieße, wieder jemandem zu schreiben, der darum gebeten hat, nicht angeschrieben zu werden. Wenn Ihr
Aufbewahrungsplan eine Grenze verlangt, setzen Sie `RetentionDays`; der Cronjob-Teil `mailpreferences` wendet sie an.

## Verwandte Seiten

- [E-Mail-Einstellungen für Administratoren](../../../anleitungen/mail-einstellungen-administrator.md)
- [Abmelden mit einem Klick](abmelden-mit-einem-klick.md)
- [Das Recht auf Löschung](recht-auf-loeschung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
