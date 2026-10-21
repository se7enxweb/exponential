---
title: Einem Auftragnehmer Zugang geben
audience: government, admin
category: public-sector
order: 40
summary: Schritt für Schritt für Administratoren - ein persönliches Konto für einen Mitarbeiter eines Auftragnehmers in der richtigen externen Benutzergruppe anlegen, ohne Selbstregistrierung, mit festgehaltenem Enddatum, zweistufiger Anmeldung, falls verlangt, und einer Prüfung bei der ersten Anmeldung.
keywords: Konto für Auftragnehmer, externer Benutzer, Benutzer anlegen, Benutzergruppe, Einarbeitung, Enddatum, zweistufige Anmeldung, Passwort zurücksetzen, Extranet-Zugang, contractor account, external user, create user, user group, onboarding, end date, two-step sign-in, password reset, extranet access
type: help
---
# Einem Auftragnehmer Zugang geben

Diese Seite richtet sich an Administratoren, die einer Person, die für einen Auftragnehmer oder Partner arbeitet,
Zugang zum Extranet geben. Sie setzt voraus, dass Benutzergruppe und Sektion der Partei existieren (siehe
„Benutzergruppen und Sektionen für Extranets“).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Das Konto anlegen](#das-konto-anlegen)
3. [Erste Anmeldung](#erste-anmeldung)
4. [Festhalten](#festhalten)

## Bevor Sie beginnen

- Ein Antrag des verantwortlichen Projektleiters, der die Person, die Partei, den Umfang und das Enddatum nennt.
- Ein Konto je Person; nie ein gemeinsames Konto für ein Unternehmen.
- Die dienstliche Adresse beim Auftragnehmer, keine private.

## Das Konto anlegen

1. Öffnen Sie unter **Benutzer** die Benutzergruppe der Partei (zum Beispiel *Extern > Auftragnehmer A*).
2. Drücken Sie unter **Unterelemente** **Neu erstellen** und wählen Sie die Benutzerklasse.
3. Füllen Sie Namen, die E-Mail-Adresse und einen Benutzernamen aus; das Passwort überlassen Sie der Person (unten).
4. **Zur Veröffentlichung schicken**.

Die Registrierung über `user/register` legt neue Benutzer in `[UserSettings] DefaultUserPlacement` ab, der Gruppe der
öffentlichen Mitglieder; sie darf nie der Weg sein, auf dem Auftragnehmer Zugang erhalten.

## Erste Anmeldung

Lassen Sie die Person ihr eigenes Passwort mit **Passwort vergessen** (`user/forgotpassword`) setzen, dessen Schlüssel
ablaufen ([Benutzerpasswort zurücksetzen](../../../../../eng-us/features/6.0/reset-user-password.md)). Ist die zweistufige
Anmeldung verlangt, richtet sie sie direkt nach dem Passwort ein (siehe „Zweistufige Anmeldung für Mitarbeiter“ im
Bereich Intranets). Prüfen Sie mit ihr, dass sie ihr Projekt sieht und sonst nichts.

## Festhalten

Notieren Sie in Ihrem Zugangsregister: die Person, die Partei, die Gruppe, das Datum und das geplante Enddatum.
Exponential hat kein Ablaufdatum für Konten, also sagt Ihnen dieses Register, wann der Zugang zu beenden ist (siehe
„Den Zugang beenden“).

## Verwandte Seiten

- [Konten und Anmeldung](../../../konten-und-anmeldung.md)
- [Den Zugang beenden](den-zugang-beenden.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
