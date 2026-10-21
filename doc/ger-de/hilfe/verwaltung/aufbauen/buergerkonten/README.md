---
title: Bürgerkonten
audience: government, admin, website-creator, project-manager
category: public-sector
order: 10
summary: Index der Anleitungen für Konten, die Bürgerinnen und Bürger auf einer Exponential-Site haben - Registrierung, Anmeldung und Sitzungen, zweistufige Anmeldung, Passwortregeln, Kontenhygiene, Deaktivierung und Entfernung, Benutzerdokumentation und eine Übersicht der Einstellungen.
keywords: Bürgerkonten, Benutzerkonten, Registrierung, Anmeldung, Sitzungen, zweistufige Anmeldung, Passwortregeln, Kontenhygiene, Deaktivierung, Entfernen von Konten, Benutzerdokumentation, citizen accounts, user accounts, registration, sign-in, sessions, two-factor, 2FA, password rules, account hygiene, deactivation, account removal, user documentation
type: help
---
# Bürgerkonten

Dieser Ordner richtet sich an Administratoren, Site-Ersteller und Projektleiter, die Bürgerinnen und Bürgern Konten auf
einer Site für den öffentlichen Sektor geben: um Inhalten zu folgen und Benachrichtigungen zu erhalten, um Newsletter
zu abonnieren, um zu kommentieren, um einen Mitgliederbereich zu erreichen oder als Partner der Organisation. Er deckt
das ganze Leben eines Kontos ab, von der Registrierung bis zur Entfernung, mit den Einstellungen und Seiten, die
Exponential bietet. Konten von Mitarbeitern verwenden dieselben Mechanismen; wo sie sich unterscheiden, sagen es die
Seiten.

Konten sind optional. Viele öffentliche Sites brauchen keine: Die Informationen sind öffentlich, und Verfahren laufen
in anderen Systemen. Bieten Sie Konten nur für einen Zweck an, den Sie benennen können, denn jedes Konto sind
personenbezogene Daten, um die Sie sich kümmern müssen.

## Inhalt

1. [Die Seiten](#die-seiten)
2. [Lesereihenfolge](#lesereihenfolge)
3. [Entscheidungen, bevor Sie Konten anbieten](#entscheidungen-bevor-sie-konten-anbieten)

## Die Seiten

| Seite | Die Antwort in einer Zeile |
|---|---|
| [Registrierung](registrierung.md) | `user/register`, Bestätigung der E-Mail, wohin neue Benutzer kommen, Freigabe durch Mitarbeiter, nicht aktivierte Benutzer |
| [Anmeldung und Sitzungen](anmeldung-und-sitzungen.md) | Anmeldeseiten, Lebensdauer von Sitzungen, andere Sitzungen abmelden, die Seite **Sitzungen** |
| [Zweistufige Anmeldung](zweistufige-anmeldung.md) | sevenx_authentication_2fa für Öffentlichkeit und Mitarbeiter: freiwillig oder verpflichtend, Wiederherstellung |
| [Passwortregeln](passwortregeln.md) | Mindestlänge, Prüfung auf schwache Passwörter, Zeichenregeln, Ablauf mit ezmbpaex, Links zum Zurücksetzen |
| [Kontenhygiene](kontenhygiene.md) | **Benutzer > Benutzerkonten prüfen**: nie angemeldet, nicht gesehen, gesperrt, doppelte Adressen |
| [Deaktivierung und Entfernung](deaktivierung-und-entfernung.md) | Deaktivieren, deaktivieren und verschieben, endgültig entfernen, und was mit den Daten geschieht |
| [Benutzerdokumentation und Support](benutzerdokumentation-und-support.md) | Was Sie Kontoinhabern sagen, und wie Support-Mitarbeiter helfen, ohne Passwörter zu sehen |
| [Übersicht der Einstellungen](uebersicht-der-kontoeinstellungen.md) | Jede auf diesen Seiten genannte Kontoeinstellung in einer Tabelle |

## Lesereihenfolge

1. **Registrierung** und **Anmeldung und Sitzungen**.
2. **Passwortregeln** und **Zweistufige Anmeldung**.
3. **Kontenhygiene** und **Deaktivierung und Entfernung**: Planen Sie sie, bevor das erste Konto existiert.
4. **Benutzerdokumentation und Support**.
5. **Übersicht der Einstellungen** als Checkliste.

Nach oben: [Eine Site für den öffentlichen Sektor aufbauen](../README.md), der Index aller Anleitungen zum Aufbauen,
und [Behörden: hier beginnen](../../verwaltungen-hier-beginnen.md), der Anfang des Bereichs für Behörden.

## Entscheidungen, bevor Sie Konten anbieten

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

- [ ] Was ist der Zweck eines Kontos, und was erlaubt es?
- [ ] Welche Daten erhebt die Registrierung? (Die Benutzerklasse von Exponential braucht einen Benutzernamen, eine
  E-Mail-Adresse und ein Passwort; alles andere entscheiden Sie.)
- [ ] Ist die Registrierung für alle offen, oder wird sie von Mitarbeitern freigegeben?
- [ ] Wie lange wird ein ungenutztes Konto aufbewahrt, bevor es deaktiviert und entfernt wird?
- [ ] Nutzt die Öffentlichkeit denselben Siteaccess wie die Mitarbeiter? (Mitarbeiter sollten sich in der
  Administration anmelden, die Öffentlichkeit auf der Site.)

Allgemeine Anleitungen: [Registrierung von Mitgliedern und Bereiche nur für Mitglieder](../../../../../eng-us/help/creators/website-creators/member-registration-and-areas.md),
[Ihr Konto und die Anmeldung](../../../konten-und-anmeldung.md), [Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md).

## Verwandte Seiten

- [Formulare aufbauen](../formulare/README.md)
- [Standards zum Datenschutz](../../../../../eng-us/standards/privacy.md)
- [Standards zur Sicherheit](../../../../../eng-us/standards/security.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
