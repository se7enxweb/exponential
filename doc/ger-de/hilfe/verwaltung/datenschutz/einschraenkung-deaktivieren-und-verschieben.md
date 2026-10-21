---
title: Einschränkung und „Deaktivieren und verschieben“
audience: government, admin
category: public-sector
order: 180
summary: Für öffentliche Stellen, die die Verarbeitung einschränken oder Konten stilllegen müssen, ohne sie zu löschen - die Funktionen Deaktivieren, „Deaktivieren und verschieben“ (Opt-in, umkehrbar, verborgene Gruppe ohne Rollen) und „Benutzerkonten prüfen“ von Exponential, mit dem, was jede ändert und was bleibt.
keywords: Einschränkung der Verarbeitung, DSGVO Artikel 18, Konto deaktivieren, Deaktivieren und verschieben, DeactivateAndMove, Reaktivieren und zurückverschieben, verborgene Gruppe, user/deactivate, user/hygiene, ausscheidende Beschäftigte, umkehrbar, Konten stilllegen, öffentlicher Sektor, restriction of processing, disable account, Deactivate and move, leavers
type: help
---
# Einschränkung und „Deaktivieren und verschieben“

Diese Seite richtet sich an Administratoren. Manchmal müssen Daten erhalten bleiben, ihre Nutzung aber aufhören: ein
strittiges Konto, eine laufende Untersuchung, ausscheidende Beschäftigte, deren Inhalte und Verlauf aufbewahrt werden
müssen. Exponential bietet das Deaktivieren und, als Opt-in, **Deaktivieren und verschieben**
([Benutzerkonten prüfen](../../../../eng-us/features/6.0/user-hygiene.md), [Datenschutzstandards](../../../../eng-us/standards/privacy.md#gdpr)).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Drei Werkzeuge](#drei-werkzeuge)
2. [Deaktivieren und verschieben einschalten](#deaktivieren-und-verschieben-einschalten)
3. [Was es tut](#was-es-tut)
4. [Wer es nutzen darf](#wer-es-nutzen-darf)
5. [Verwandte Seiten](#verwandte-seiten)

## Drei Werkzeuge

| Werkzeug | Wirkung | Umkehrbar |
|---|---|---|
| **Deaktivieren** (Benutzer > Benutzerkonten prüfen oder die Kontoeinstellungen) | das Konto kann sich nicht anmelden, seine Sitzungen enden; Inhalte, Gruppen und Rollen bleiben | ja, **Aktivieren** |
| **Deaktivieren und verschieben** (Opt-in) | deaktiviert, und alle Platzierungen werden in eine verborgene Gruppe ohne Rollen gebracht; wo es war, wird vermerkt | ja, **Reaktivieren und zurückverschieben** |
| **Entfernen** | das Benutzerobjekt ist endgültig weg (kein Papierkorb) | nein |

## Deaktivieren und verschieben einschalten

```ini
# settings/override/site.ini.append.php, or the admin siteaccess's site.ini.append.php
[UserSettings]
DeactivateAndMove=enabled
# optional: a group of your own instead of the one found by its remote id
DeactivatedUserGroupNodeID=
```

Standardmäßig aus; wenn es aus ist, wird nichts angezeigt. Die Gruppe („Deaktivierte Benutzer“, Remote-ID
`exp_deactivated_users_group`) wird bei der ersten Nutzung unterhalb des Knotens Benutzer angelegt, oder vorab mit
`php bin/php/ezcreatedeactivatedusergroup.php -s <admin siteaccess> --commit`.

## Was es tut

Für jedes Konto in einer Transaktion: Es vermerkt seine Platzierungen; deaktiviert es (Sitzungen enden); entfernt die
anderen Platzierungen; verschiebt die Hauptplatzierung in die verborgene Gruppe. Inhalte, Versionen, dem Konto selbst
zugewiesene Rollen und Relationen bleiben erhalten. Die Gruppe hat keine Rollen, und das Zuweisen einer Rolle an sie
wird abgelehnt. Sie ist verborgen, sodass die Konten weder auf der Website noch in ihren Benutzerlisten erscheinen.
Aufgezeichnet als `access.user.deactivate_move`; die Umkehrung als `access.user.reactivate_move`. Die vermerkten
Platzierungen werden nur als Knoten-IDs in `ezsite_data` aufbewahrt.

## Wer es nutzen darf

Die Funktion `user/deactivate` (in einer frischen Installation nur Administrator; außerdem jede Rolle mit `user/*`,
prüfen Sie diese also zuerst), dazu `user/hygiene` für die Massenaktionen, sowie die Inhaltsrechte an jedem Konto. Der
anonyme Benutzer, der Ersteller der Benutzer, das Administratorkonto und Ihr eigenes Konto werden nie angetastet.

## Verwandte Seiten

- [Benutzerkonten prüfen: Deaktivieren und verschieben](../../../../eng-us/features/6.0/user-hygiene.md#deactivate-and-move)
- [Das Recht auf Löschung](recht-auf-loeschung.md)
- [Rollen, Richtlinien und nicht aktivierte Benutzer](../../../anleitungen/rollen-und-richtlinien.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
