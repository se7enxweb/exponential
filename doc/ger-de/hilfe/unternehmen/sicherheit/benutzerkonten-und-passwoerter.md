---
title: Benutzerkonten und Passwörter
audience: businesses, admin, editor
category: business-solutions
order: 50
summary: Die Richtlinie für Konten und Passwörter, die ein Unternehmen in Exponential festlegen sollte - Passwortregeln, Ablehnung gängiger Passwörter, zweistufige Anmeldung, was bei einer Passwortänderung geschieht, Links zum Zurücksetzen, Sperren und eine aufgeräumte Kontenliste - mit den Einstellungen und Seiten für jeden Punkt.
keywords: Benutzerkonten, Passwörter, Passwortrichtlinie, zweistufige Anmeldung, Passwortänderung, Zurücksetzen, Sperre, Kontenpflege, Benutzerkonten prüfen, Administrator, user accounts, passwords, password policy, MinPasswordLength, WeakPasswordCheck, two-step sign-in, 2FA, two-factor, Enforce2FA, password change, reset, lockout, account hygiene, check user accounts
type: help
---
# Benutzerkonten und Passwörter

Diese Seite richtet sich an Inhaber und Administratoren, die entscheiden, wie sich Mitarbeiter, Redakteure, Mitglieder
und Kunden anmelden. Gestohlene und erratene Passwörter sind der häufigste Weg in jede Website; ein paar Entscheidungen
schließen diese Tür größtenteils. Jeder Abschnitt sagt, was Exponential bereits tut und was Sie entscheiden sollten.

> **Hinweis**
> Grundlagen, keine Rechtsberatung und keine vollständige Sicherheitsbewertung. Wenn Verträge oder Vorschriften in Ihrer
> Branche Regeln für die Anmeldung festlegen, haben diese Vorrang.

## Inhalt

1. [Entscheidungen zur Richtlinie auf einen Blick](#entscheidungen-zur-richtlinie-auf-einen-blick)
2. [Passwortregeln](#passwortregeln)
3. [Zweistufige Anmeldung](#zweistufige-anmeldung)
4. [Wenn ein Passwort geändert oder vergessen wird](#wenn-ein-passwort-geändert-oder-vergessen-wird)
5. [Administratorkonten](#administratorkonten)
6. [Die Kontenliste aufgeräumt halten](#die-kontenliste-aufgeräumt-halten)
7. [Ein Richtlinientext zum Anpassen](#ein-richtlinientext-zum-anpassen)

## Entscheidungen zur Richtlinie auf einen Blick

| Entscheidung | Standard | Empfehlung zur Überlegung | Quelle |
|---|---|---|---|
| Mindestlänge des Passworts | 10 in einer neuen Installation | länger, zum Beispiel 15, wie der Härtungsleitfaden vorschlägt | [Ihr Passwort ändern](../../../../eng-us/features/6.0/modern-password-change.md) |
| Gängige Passwörter ablehnen | an (`WeakPasswordCheck=enabled`) | eingeschaltet lassen | dieselbe |
| Weitere Regeln (Ziffern, Sonderzeichen, nicht der Benutzername) | aus | `ForbidLogin` und `ForbidCurrentPassword` einschalten | dieselbe |
| Zweistufige Anmeldung | Benutzer entscheiden sich selbst dafür | für Administratoren und Redakteure verlangen | [Zweistufige Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md) |
| Sperre nach fehlgeschlagenen Versuchen | aus (`MaxNumberOfFailedLogin` 0) | optional; eine Sperre lässt auch jeden ein bekanntes Konto sperren | [Sicherheitshärtung](../../sicherheitshaertung.md#brute-force-und-credential-stuffing-bei-der-anmeldung) |
| Gültigkeit des Links zum Zurücksetzen | funktioniert einmal, eine Stunde lang | beibehalten | dieselbe |

## Passwortregeln

- Die Längenregel ist `site.ini [UserSettings] MinPasswordLength`: 10 in einer neuen Installation.
- Die optionalen Regeln in `site.ini [PasswordSettings]` (Kleinbuchstaben, Großbuchstaben, Ziffer, Sonderzeichen,
  Zeichenklassen, nicht der Benutzername, nicht das aktuelle Passwort) sind standardmäßig alle aus, sodass ein Upgrade
  die Regeln nie von selbst verschärft.
- `WeakPasswordCheck` ist seit 6.0.15 eingeschaltet: Neue Passwörter, die bekannt oder gängig sind, oder einfache
  Varianten davon, werden abgelehnt. Bei der Anmeldung wird niemand abgewiesen.
- Dieselben Regeln gelten für jedes Formular, das ein Passwort setzt: Registrierung, Bearbeitung des Kontos, die Seite
  zum Ändern und die Kommandozeile.

Quelle: [Ihr Passwort ändern](../../../../eng-us/features/6.0/modern-password-change.md#the-rules). Passwörter werden als
bcrypt-Hashes gespeichert ([Sicherheitshärtung](../../sicherheitshaertung.md#brute-force-und-credential-stuffing-bei-der-anmeldung)).

## Zweistufige Anmeldung

Nach dem Passwort ein sechsstelliger Code aus einer Authenticator-App oder einer E-Mail: Das Passwort allein ist dann
für niemand anderen nützlich. Bereitgestellt wird sie von `sevenx_authentication_2fa`, das `composer.json` verlangt und
die Installer aktivieren ([Zweistufige Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md)).

| Sie entscheiden | Einstellung |
|---|---|
| Jeder wählt selbst | `[General] Enforce2FA=disabled` (der Standard) |
| Pflicht, mit einer Authenticator-App | `Enforce2FA=enabled`, `DefaultMethod=totp` |

Was das Unternehmen wissen muss:

- Das Einschalten dauert etwa 15 Minuten; jeder Benutzer braucht etwa 2 Minuten für die Einrichtung (dieselbe Quelle).
- Das Ändern oder Ausschalten verlangt einen aktuellen Code, sodass jemand an einem unbeaufsichtigten Computer ein Konto
  nicht schwächen kann.
- Ein verlorenes Telefon behandelt ein Administrator, der das Feld des Benutzers auf E-Mail-Codes oder deaktiviert
  setzt; der Benutzer richtet die App dann neu ein.
- Bewahren Sie den Verschlüsselungsschlüssel (`SecretKey`) sicher und außerhalb der Versionsverwaltung auf: Geht er
  verloren, muss jeder die App neu einrichten.

Die Sicht für Intranets steht in [Zweistufige Anmeldung für Intranets](../intranets/zwei-faktor-und-anmeldung.md).

## Wenn ein Passwort geändert oder vergessen wird

Aus [Ihr Passwort ändern](../../../../eng-us/features/6.0/modern-password-change.md):

- Die Seite zum Ändern listet die Anforderungen auf und meldet jedes Problem neben seinem Feld.
- Nach einer Änderung bleibt der Benutzer mit einer neuen Session-ID angemeldet; andere Sessions auf anderen Geräten
  werden bei ihrer nächsten Anfrage abgemeldet. Das geschieht auch, wenn ein Administrator das Passwort ändert.
- Eine E-Mail „Ihr Passwort wurde geändert“ geht an die Adresse des Kontos. Sie enthält nie das Passwort, und niemand
  kann sie ausschalten.
- Jede Änderung und jede abgelehnte Änderung wird im Audit-Trail festgehalten, nie das Passwort selbst.
- „Passwort vergessen?“ antwortet für jede Adresse gleich, verrät also nicht, welche Konten existieren
  ([Sicherheitshärtung](../../sicherheitshaertung.md#brute-force-und-credential-stuffing-bei-der-anmeldung)).

Ein Administrator, der keinen Zugang mehr hat, kann auf der Kommandozeile ein neues Passwort setzen
([Ein Benutzerpasswort zurücksetzen](../../../../eng-us/features/6.0/reset-user-password.md)).

## Administratorkonten

- Kein Installer von Exponential 6 lässt das alte Standardpasswort bestehen
  ([Das Standard-Administratorpasswort](../../standard-administratorpasswort.md)).
- Geben Sie jedem Administrator ein persönliches Konto und stellen Sie die gemeinsam genutzte Anmeldung `admin` ein,
  damit der Audit-Trail zeigt, wer was getan hat ([Das Administratorpasswort ändern](../../administratorpasswort-aendern.md#kontenhygiene)).
- Halten Sie die Zahl der Administratoren klein; geben Sie anderen Rollen mit nur dem, was sie brauchen
  ([Zugriffsprüfungen](zugriffspruefungen.md)).
- Erwägen Sie einen eigenen Hostnamen oder eine Netzwerkbeschränkung für die Administration
  ([Die Administration einschränken](../../hosting/sicherheit/die-administration-einschraenken.md)).

## Die Kontenliste aufgeräumt halten

**Benutzer > Benutzerkonten prüfen** (`user/hygiene`) listet Konten auf, die sich nie angemeldet haben, seit einer Zahl
von Tagen (standardmäßig 180) nicht gesehen wurden, deaktiviert, gesperrt oder verwaist sind oder einen Benutzernamen
oder eine E-Mail-Adresse teilen. Ausgewählte Konten lassen sich in einem Schritt deaktivieren, aktivieren oder
entsperren, und die Liste lässt sich als CSV exportieren. Nichts auf der Seite entfernt ein Konto; eine optionale
Einstellung **Deaktivieren und verschieben** verschiebt deaktivierte Konten in eine verborgene Gruppe ohne Rollen
([Benutzerkonten prüfen](../../../../eng-us/features/6.0/user-hygiene.md)).

Tun Sie das mindestens einmal im Jahr und jedes Mal, wenn jemand geht: [Zugriffsprüfungen](zugriffspruefungen.md) enthält
die Schritte.

## Ein Richtlinientext zum Anpassen

```text
ACCOUNT AND PASSWORD POLICY - [Organization name]   Version [x], [date]

1. Every person has their own account. Shared accounts are not allowed.
2. Passwords have at least [15] characters. The site refuses common passwords.
3. Two-step sign-in is required for [administrators and editors / everyone].
4. Administrator rights are held by at most [number] named people: [names or roles].
5. When someone leaves, [role] disables their account on their last working day.
6. Accounts not used for [180] days are reviewed and disabled if not needed.
7. A lost phone for two-step sign-in is reported to [contact]; identity is checked by [method].
8. Suspected misuse is reported to [contact] at once.
Approved by: [name, role]   Next review: [date]
```

## Verwandte Seiten

- [Ihr Konto und die Anmeldung](../../konten-und-anmeldung.md)
- [Zweistufige Anmeldung für Hoster](../../hosting/sicherheit/zweistufige-anmeldung.md)
- [Leitfaden Sicherheit und Audit](../../../anleitungen/sicherheit-und-audit.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
