---
title: Übersicht der Kontoeinstellungen
audience: government, admin
category: public-sector
order: 90
summary: Jede in den Anleitungen zu Bürgerkonten genannte Einstellung in einer Tabelle - Registrierung, Anmeldung, Sitzungen, Passwörter, Sperre, zweistufige Anmeldung, Deaktivierung und Ablauf von Passwörtern - mit Datei, Block, mitgeliefertem Wert und einer Empfehlung für den öffentlichen Sektor.
keywords: Übersicht der Einstellungen, Kontoeinstellungen, Registrierung, Sitzungen, Passwörter, Sperre, zweistufige Anmeldung, settings reference, site.ini, UserSettings, PasswordSettings, Session, sevenxauthentication2fa.ini, mbpaex.ini, MinPasswordLength, MaxNumberOfFailedLogin, Enforce2FA, DeactivateAndMove, VerifyUserType
type: reference
---
# Übersicht der Kontoeinstellungen

Diese Seite richtet sich an Administratoren, die Konten auf einer Site für den öffentlichen Sektor konfigurieren. Sie
sammelt die Einstellungen der Anleitungen zu Bürgerkonten an einem Ort. Als „mitgeliefert“ markierte Werte sind die der
mitgelieferten Einstellungen oder einer neuen Installation, wie in den verlinkten Seiten dokumentiert; Empfehlungen
sind Ausgangspunkte (Beispielannahmen) zum Anpassen. Legen Sie Änderungen in `settings/override/` oder in
`settings/siteaccess/<name>/` eines Siteaccess ab, nie in den mitgelieferten Dateien, und leeren Sie danach den
INI-Cache: `php bin/php/ezcache.php --clear-tag=ini`.

## Inhalt

1. [Registrierung](#registrierung)
2. [Anmeldung und Sitzungen](#anmeldung-und-sitzungen)
3. [Passwörter](#passwörter)
4. [Zweistufige Anmeldung](#zweistufige-anmeldung)
5. [Deaktivierung und Ablauf](#deaktivierung-und-ablauf)

## Registrierung

| Datei | Block | Schlüssel | Mitgeliefert | Empfehlung | Lesen |
|---|---|---|---|---|---|
| `site.ini` | `UserSettings` | `DefaultUserPlacement` | `12` | der Knoten Ihrer Gruppe für öffentliche Konten | [Registrierung](registrierung.md) |
| `site.ini` | `UserSettings` | `VerifyUserType` | `email` | beibehalten | [Registrierung](registrierung.md) |
| `site.ini` | `UserSettings` | `RegistrationFeedback` | `email` | beibehalten; setzen Sie `RegistrationEmail` auf ein gemeinsames Postfach | [Rollen und Richtlinien, 7](../../../../anleitungen/rollen-und-richtlinien.md#7-nicht-aktivierte-benutzer-aufräumen) |
| `site.ini` | `UserSettings` | `RegistrationAdminLinks` | `enabled` | beibehalten | dasselbe |
| `site.ini` | `UserSettings` | `RegistrationActionTTL` | `604800` | kürzer, wenn Freigaben schnell erfolgen | dasselbe |
| `site.ini` | `UserSettings` | `PasswordInRegistrationEmail` | `disabled` | beibehalten | [Registrierung von Mitgliedern](../../../../../eng-us/help/creators/website-creators/member-registration-and-areas.md) |

## Anmeldung und Sitzungen

| Datei | Block | Schlüssel | Mitgeliefert | Empfehlung | Lesen |
|---|---|---|---|---|---|
| `site.ini` | `Session` | `SessionTimeout` | `259200` | kürzer für gemeinsam genutzte Computer | [Sitzungen](../../../../anleitungen/sitzungen.md) |
| `site.ini` | `Session` | `ActivityTimeout` | `3600` | beibehalten | dasselbe |
| `site.ini` | `Session` | `Handler` | leer | `ezpSessionHandlerDB`, um Sitzungen aufzulisten und zu entfernen | dasselbe |
| `site.ini` | `Session` | `CookieSecure` | nicht gesetzt (Verhalten `auto`, gemäß den Standards zur Sicherheit) | beibehalten; liefern Sie alles über HTTPS aus | [Standards zur Sicherheit](../../../../../eng-us/standards/security.md) |
| `site.ini` | `UserSettings` | `MaxNumberOfFailedLogin` | `0` (aus) | ein Wert mit `TrustedIPList[]`, oder verlassen Sie sich auf 2FA und Warnungen | [Sicherheitshärtung](../../../sicherheitshaertung.md#brute-force-und-credential-stuffing-bei-der-anmeldung) |
| `site.ini` | `UserSettings` | `TrustedIPList[]` | leer | Ihre Büronetze | dasselbe |

## Passwörter

| Datei | Block | Schlüssel | Mitgeliefert | Empfehlung | Lesen |
|---|---|---|---|---|---|
| `site.ini` | `UserSettings` | `MinPasswordLength` | `10` | 12 für die Öffentlichkeit, 15 für Mitarbeiter | [Passwortregeln](passwortregeln.md) |
| `site.ini` | `UserSettings` | `WeakPasswordCheck` | `enabled` | beibehalten | dasselbe |
| `site.ini` | `PasswordSettings` | `ForbidLogin` | aus | `enabled` | dasselbe |
| `site.ini` | `PasswordSettings` | `ForbidCurrentPassword` | aus | `enabled` für Mitarbeiter | dasselbe |
| `site.ini` | `PasswordSettings` | `MinCharacterClasses` | `0` | wie Ihre Richtlinie es vorsieht | dasselbe |
| `site.ini` | `PasswordSettings` | `EndOtherSessions` | `enabled` | beibehalten | [Das eigene Passwort ändern](../../../../../eng-us/features/6.0/modern-password-change.md) |

## Zweistufige Anmeldung

| Datei | Block | Schlüssel | Mitgeliefert | Empfehlung | Lesen |
|---|---|---|---|---|---|
| `site.ini` | `UserSettings` | `LoginHandler[]` | `sevenxUser2fa`, `standard` (vom Installer gesetzt) | beibehalten | [Zweistufige Anmeldung](zweistufige-anmeldung.md) |
| `sevenxauthentication2fa.ini` | `General` | `Enforce2FA` | `disabled` | `enabled` für den Siteaccess der Administration | dasselbe |
| `sevenxauthentication2fa.ini` | `General` | `DefaultMethod` | `totp` | beibehalten | dasselbe |
| `sevenxauthentication2fa.ini` | `TOTPSettings` | `SecretKey` | vom Installer erzeugt | geheim halten, sichern | [Anleitung zur zweistufigen Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md) |

## Deaktivierung und Ablauf

| Datei | Block | Schlüssel | Mitgeliefert | Empfehlung | Lesen |
|---|---|---|---|---|---|
| `site.ini` | `UserSettings` | `DeactivateAndMove` | `disabled` | `enabled` | [Deaktivierung und Entfernung](deaktivierung-und-entfernung.md) |
| `site.ini` | `UserSettings` | `DeactivatedUserGroupNodeID` | leer | leer lassen, sofern Sie keine eigene Gruppe haben | dasselbe |
| `mbpaex.ini` | `mbpaexSettings` | `DefaultPasswordLifeTime` | `3` | vor dem Aktivieren von ezmbpaex entscheiden; `0` schaltet den Ablauf aus | [ezmbpaex](../../../../../eng-us/features/6.0/extensions/ezmbpaex.md) |
| `mbpaex.ini` | `mbpaexSettings` | `PasswordValidationRegexp` | leer | nur, wenn sich eine Regel nicht anders ausdrücken lässt | dasselbe |

Prüfen Sie den auf Ihrer Installation geltenden Wert mit **Setup > Ini Einstellungen** oder `./console exp:ini`
([Der Befehl exp:ini](../../../../../eng-us/features/6.0/exp-ini-command.md)).

## Verwandte Seiten

- [Bürgerkonten](README.md)
- [Die Seite der Einstellungen](../../../../anleitungen/einstellungsseite.md)
- [13. Sicherheitshärtung](../../../../installation/13-sicherheitshaertung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
