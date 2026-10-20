---
title: Alle optionalen E-Mails an eine Adresse stoppen
audience: government, admin
category: public-sector
order: 290
summary: Schritt für Schritt - einer Bitte „Schreiben Sie mir nicht mehr“ in Exponential mit einem Eintrag in der Sperrliste (Grund legal) nachkommen, der jede optionale E-Mail und Bestätigung an diese Adresse blockiert und nur einen Hash speichert, und ihn prüfen.
keywords: E-Mails stoppen, nicht kontaktieren, Sperrliste, exp:mail:suppression add, Grund legal, Widerspruch, Opt-out, alles abbestellen, notwendige E-Mails, Hash, öffentlicher Sektor, stop mail, do not contact, suppression list, objection, opt out, unsubscribe all, essential mail, public sector
type: help
---
# Alle optionalen E-Mails an eine Adresse stoppen

Für Administratoren, wenn jemand per Telefon, Brief oder E-Mail bittet, nichts mehr zu erhalten. Die Sperrliste
blockiert optionale E-Mails, Bestätigungen und E-Mails vom Typ „Schicken Sie mir einen Link“ an die Adresse; notwendige
E-Mails (Zurücksetzen des Passworts, Bestellungen) gehen weiterhin hinaus
([Die Sperrliste](../../../../../eng-us/help/government/privacy/suppression-list.md)).

## Inhalt

1. [Schritte](#schritte)
2. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
3. [Rückgängig machen](#rückgängig-machen)
4. [Verwandte Seiten](#verwandte-seiten)

## Schritte

In der Administration: **Setup > E-Mail-Einstellungen** > **Sperrliste** > **Eine Adresse hinzufügen**, Grund legal,
eine Notiz ohne personenbezogene Daten (ein Aktenzeichen).

Oder auf der Konsole:

```bash
./console exp:mail:suppression add --email=<address> --reason=legal --note="<case reference>" --allow-root-user
```

Wenn die Person auch die Löschung ihrer Daten wünscht, siehe „E-Mail-Daten zu einer Adresse löschen“. Wenn die
Newsletter-Erweiterung eine eigene Blacklist führt, ist sie Teil der Sperrliste, wie die Anleitung es beschreibt.

## Prüfen, ob es funktioniert hat

```bash
./console exp:mail:suppression check --email=<address> --allow-root-user
```

antwortet `suppressed: legal`. Einträge bleiben, bis ein Administrator sie aufhebt (`[SuppressionSettings]
RetentionDays=0` als Voreinstellung).

## Rückgängig machen

Nur auf Wunsch der Person: **Aufheben** auf der Seite der Sperrliste oder
`./console exp:mail:suppression lift --email=<address> --allow-root-user`.

## Verwandte Seiten

- [Die Sperrliste](../../../../../eng-us/help/government/privacy/suppression-list.md)
- [Abbestellen mit einem Klick](../../../../../eng-us/help/government/privacy/one-click-unsubscribe.md)
- [E-Mail-Einstellungen für Administratoren: die Sperrliste](../../../../anleitungen/mail-einstellungen-administrator.md#7-die-sperrliste)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
