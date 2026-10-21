---
title: Registrierung
audience: government, admin, website-creator
category: public-sector
order: 20
summary: Wie sich Bürgerinnen und Bürger auf einer Exponential-Site registrieren - die Seite user/register, die Bestätigung der E-Mail, die Gruppe, in die neue Benutzer kommen, die Freigabe durch Mitarbeiter, die Mail „Neuer Benutzer registriert“ mit signierten Links und das Aufräumen nicht aktivierter Registrierungen.
keywords: Registrierung, Aktivierung, nicht aktivierte Benutzer, Freigabe, signierte Links, Einwilligung zu E-Mail-Einstellungen, registration, user/register, VerifyUserType, DefaultUserPlacement, RegistrationEmail, RegistrationFeedback, activation, unactivated users, approval, signed links, mail preferences consent
type: help
---
# Registrierung

Diese Seite richtet sich an Administratoren und Site-Ersteller, die die Registrierung für die Öffentlichkeit öffnen.
Sie zeigt die beteiligten Seiten, Einstellungen und Bildschirme der Administration. Quellen:
[Registrierung von Mitgliedern und Bereiche nur für Mitglieder](../../../../../eng-us/help/creators/website-creators/member-registration-and-areas.md)
und [Rollen und Richtlinien, 7](../../../../anleitungen/rollen-und-richtlinien.md#7-nicht-aktivierte-benutzer-aufräumen).

## Inhalt

1. [Die Seiten](#die-seiten)
2. [Einstellungen](#einstellungen)
3. [Was das Registrierungsformular fragt](#was-das-registrierungsformular-fragt)
4. [Freigabe durch Mitarbeiter](#freigabe-durch-mitarbeiter)
5. [Nicht aktivierte Registrierungen](#nicht-aktivierte-registrierungen)

## Die Seiten

| Seite | View | Für Besucher geöffnet durch |
|---|---|---|
| Registrieren | `user/register` | `site.ini [SiteAccessSettings] AnonymousAccessList[]` |
| Registrierung abgeschlossen | `user/success` | dasselbe |
| Aktivieren (Link in der Mail) | `user/activate` | dasselbe |
| Passwort vergessen | `user/forgotpassword` | dasselbe |
| Anmelden, abmelden | `user/login`, `user/logout` | immer |

Überschreiben Sie ihre Templates in Ihrem öffentlichen Design, damit sie zur Site passen.

## Einstellungen

`site.ini [UserSettings]`, in `settings/override/site.ini.append.php` oder im öffentlichen Siteaccess:

| Schlüssel | Verwendung |
|---|---|
| `DefaultUserPlacement` | der Knoten der Benutzergruppe, in die neue Konten kommen (mitgeliefert `12`); lassen Sie ihn auf eine Gruppe „Mitglieder“ oder „Öffentliche Konten“ zeigen, nie auf eine Gruppe von Mitarbeitern |
| `VerifyUserType=email` | das Konto funktioniert erst, nachdem der Link in der Mail angeklickt wurde |
| `RegistrationEmail` | wer über neue Registrierungen informiert wird |
| `RegistrationFeedback=email` | die Mail an den Administrator mit signierten Aktionslinks (unten) |
| `PasswordInRegistrationEmail=disabled` | das Passwort nie per Mail senden |
| `MinPasswordLength`, `WeakPasswordCheck` | Passwortregeln ([Passwortregeln](passwortregeln.md)) |

Die Rolle der Gruppe (zum Beispiel die mitgelieferte Rolle Member) entscheidet, was Konten dürfen; geben Sie ihr nur,
was der Zweck braucht ([Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md)).

## Was das Registrierungsformular fragt

Die Benutzerklasse entscheidet über die Felder: Vor- und Nachname, Benutzername, E-Mail und Passwort in der
Standardklasse, dazu Felder, die Sie hinzufügen. Fragen Sie nichts, was Sie nicht brauchen. Seit 6.0 kann die
Registrierung **E-Mails von uns (optional)** zeigen: Auswahlmöglichkeiten zur Einwilligung in Mail-Kategorien, die mit
dem angezeigten Text in einem Einwilligungsprotokoll festgehalten werden
([E-Mail-Einstellungen](../../../../../eng-us/features/6.0/mail-preferences.md),
[Ihr Konto und die Anmeldung](../../../konten-und-anmeldung.md#ein-konto-registrieren)).

Schützen Sie das Formular gegen automatisierte Registrierungen: Das Formular-Token ist automatisch; die Bestätigung der
E-Mail hilft; ein CAPTCHA in der Benutzerklasse ist möglich, mit den Folgen für Drittanbieter, die in
[Spamschutz und Drittanbieter](../formulare/spamschutz-und-drittanbieter.md) beschrieben sind.

## Freigabe durch Mitarbeiter

Für Konten, die Zugang zu mehr als öffentlichen Informationen geben (ein Partnerbereich), geben Mitarbeiter jede
Registrierung frei:

| Weg | Wie |
|---|---|
| Die Mail „Neuer Benutzer registriert“ | mit `RegistrationFeedback=email` erhält der Administrator eine Mail mit signierten Links: **Konto ansehen**, **Konto aktivieren** (oder **Freigeben**), **Ablehnen und entfernen**; jeder Link öffnet eine Bestätigung in der Administration, funktioniert einmal und läuft ab (`RegistrationActionTTL`, 7 Tage) ([Signierte Links](../../../../../eng-us/features/6.0/registration-admin-links.md)) |
| Ein Workflow | ein Freigabe-Ereignis bei der Registrierung, entschieden im Posteingang der Zusammenarbeit |
| **Benutzer > Nicht aktivierte Benutzer** | **Ausgewählte Benutzer aktivieren** für wartende Registrierungen |

## Nicht aktivierte Registrierungen

**Benutzer > Nicht aktivierte Benutzer** listet Personen, die sich registriert, aber nie auf den Aktivierungslink
geklickt haben: Suche, Sortierung nach Datum, Registrierungen älter als 30 Tage als **Alt** markiert. Aktionen:
**Ausgewählte Benutzer aktivieren**, **Aktivierungsmail erneut senden**, **Ausgewählte Benutzer entfernen** und **Alle
unaktivierten Benutzer entfernen** (in Stapeln zu 50, im Audit als `access.user.remove` festgehalten). Führen Sie das
monatlich aus: Eine nie bestätigte Registrierung sind personenbezogene Daten ohne Zweck.

## Verwandte Seiten

- [Bürgerkonten](README.md)
- [Anmeldung und Sitzungen](anmeldung-und-sitzungen.md)
- [Links für Administratoren bei der Registrierung](../../../../../eng-us/features/6.0/registration-admin-links.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
