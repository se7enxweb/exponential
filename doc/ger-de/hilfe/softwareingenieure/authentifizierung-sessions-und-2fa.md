---
title: Authentifizierung, Sessions und Zwei-Faktor-Anmeldung
audience: software-engineer, admin, developer
category: software-engineering
order: 720
summary: Wie sich Menschen und Maschinen bei Exponential authentifizieren - Speicherung und Richtlinie von Passwörtern, die Liste bekannter Passwörter, Sperre, Session-Handler und Cookies, Zwei-Faktor-Anmeldung mit sevenx_authentication_2fa, OAuth 2.0 und persönliche API-Schlüssel für die REST-API - und die technischen Entscheidungen, die jedes davon mit sich bringt.
keywords: Authentifizierung, Anmeldung, Login, password_hash, MinPasswordLength, schwache Passwörter, Sperre, Sessions, ezpSessionHandlerDB, Cookies, SameSite, HttpOnly, Zwei-Faktor, 2FA, TOTP, sevenx_authentication_2fa, OAuth, API-Schlüssel, Softwareingenieur, authentication, sign-in, weak passwords, lockout, two-factor, API keys
type: help
---
# Authentifizierung, Sessions und Zwei-Faktor-Anmeldung

Diese Seite richtet sich an Ingenieure, die die Anmeldung für eine Website entwerfen, Maschinen über die API anbinden oder
die Kontosicherheit prüfen. Sie behandelt Menschen (Passwörter, zwei Faktoren, Sessions) und Maschinen (OAuth-Tokens,
API-Schlüssel).

## Inhalt

1. [Passwörter](#passwörter)
2. [Sessions](#sessions)
3. [Zwei-Faktor-Anmeldung](#zwei-faktor-anmeldung)
4. [Maschinen: OAuth und API-Schlüssel](#maschinen-oauth-und-api-schlüssel)
5. [Login-Handler](#login-handler)
6. [Zu treffende Entscheidungen](#zu-treffende-entscheidungen)

## Passwörter

| Aspekt | Mitgeliefertes Verhalten | Einstellung oder Quelle |
|---|---|---|
| Speicherung | PHPs `password_hash()` (heute bcrypt), ältere Hashes werden bei der Anmeldung aktualisiert | `site.ini [UserSettings] HashType=php_default` |
| Mindestlänge | 10 | `[UserSettings] MinPasswordLength` |
| Bekannte Passwörter | überall abgelehnt, wo ein Passwort gesetzt wird, mit einfachen Varianten | `expWeakPasswordList` ([Schwache Passwörter und Gültigkeit von Rücksetzschlüsseln](../../bc/6.0/schwache-passwoerter-und-gueltigkeit-von-reset-links.md)) |
| Link "Passwort vergessen" | läuft ab | dieselbe Seite |
| Sperre nach fehlgeschlagenen Anmeldungen | aus (`0`), bis Sie eine Zahl setzen | `[UserSettings] MaxNumberOfFailedLogin` |
| Andere Sessions bei Passwortänderung | beendet | `[PasswordSettings] EndOtherSessions=enabled` |

Unbekannte Logins und falsche Passwörter kosten gleich viel Zeit, und Antworten auf "Passwort vergessen" sind einheitlich,
sodass ein Angreifer keine Konten auflisten kann ([Sicherheitsstandards](../../standards/sicherheit.md)). Seit August 2026
kann sich ein Konto ohne Zeile in `ezuser_setting` nicht anmelden: Die Anmeldung schlägt sicher fehl.

## Sessions

- `site.ini [Session] SessionTimeout` (Standard drei Tage) beendet eine Session nach ihrer letzten Verwendung.
- `[Session] Handler` leer (der Standard) speichert Sessions in PHPs eigenem Speicher; `ezpSessionHandlerDB` speichert sie
  in der Tabelle `ezsession`, die mehrere Webknoten teilen können und die die Administration auflisten und beenden kann
  ([Sessions](../../anleitungen/sitzungen.md)).
- Session-Cookies sind seit September 2026 standardmäßig `HttpOnly` und `SameSite` und über HTTPS `Secure`
  (`CookieSecure=auto`); die Session-ID wird bei der Anmeldung neu erzeugt.
- Unter Velocity wird `$_SESSION` nach jeder Anfrage geschrieben und geleert.

## Zwei-Faktor-Anmeldung

Die Erweiterung `sevenx_authentication_2fa`, die `composer.json` verlangt und die Installer aktivieren, fügt nach dem
Passwort einen zweiten Schritt hinzu: einen TOTP-Code aus einer Authenticator-App (RFC 6238, nur PHP-Bordmittel) oder einen
Einmalcode per E-Mail. Ein Datentyp an der Benutzerklasse speichert Methode und Geheimnis jedes Benutzers; ein Login-Handler
prüft das Passwort und schließt dann die Anmeldung ab oder leitet zur Abfrage weiter. Offene Abfragen sind JSON-Dateien
unter `var/`, es wird also kein Redis oder Valkey gebraucht. Dieselbe Erweiterung enthält Handler für Social Login als
Gerüste zum Anpassen ([Zweistufige Anmeldung](../../anleitungen/zwei-faktor-anmeldung.md),
[sevenx_authentication_2fa](../../funktionen/6.0/erweiterungen/sevenx_authentication_2fa.md)).

## Maschinen: OAuth und API-Schlüssel

| Mechanismus | Verwendung |
|---|---|
| OAuth-2.0-Tokens | `POST /api/auth/v1/oauth/token` mit `authorization_code` oder `refresh_token`, für Anwendungen, die für einen Benutzer handeln ([OAuth und API-Schlüssel](../ersteller/anwendungsentwickler/oauth-und-api-schluessel.md), [Richtlinie für OAuth authorize](../../bc/6.0/oauth-autorisierungsrichtlinie.md)) |
| Persönliche API-Schlüssel | `Authorization: Bearer expk_...`; von einem Benutzer mit `apikey/create` erzeugt, mit Geltungsbereich, ablaufend, widerrufbar, einmal angezeigt ([Persönliche API-Schlüssel](../../anleitungen/api-schluessel.md)) |
| `expservices`-Tokens | entfernte Dienste und Apps ([Entfernte Dienste und Apps](../../anleitungen/entfernte-dienste-und-apps.md)) |

Was auch immer eine REST-Anfrage authentifiziert: Jedes Lesen und Schreiben prüft die Richtlinien des Benutzers
([REST-API v1 und v2](../../anleitungen/rest-api-v1-und-v2.md)).

## Login-Handler

`site.ini [UserSettings] LoginHandler[]` listet die Handler, die der Reihe nach versucht werden; die Zwei-Faktor-Erweiterung
fügt ihren eigenen vor `standard` ein. Verzeichnisdienste (LDAP und andere) werden auf dieselbe Weise eingebunden
([Standards für APIs und Protokolle](../../standards/apis-und-protokolle.md)).

## Zu treffende Entscheidungen

- [ ] Ist die Zwei-Faktor-Anmeldung für Administratoren und Redakteure Pflicht?
- [ ] Ist eine Sperrzahl gesetzt, und stimmt `TrustedProxies[]`, sodass die echten Client-Adressen gezählt werden?
- [ ] Welcher Session-Handler: PHPs Speicher auf einem Knoten oder die Datenbank für mehrere Knoten und Session-Kontrolle?
- [ ] Welche Maschinen erhalten API-Schlüssel, mit welchen Geltungsbereichen und Laufzeiten, und wer erneuert sie?
- [ ] Werden Anmeldeereignisse überwacht (die Ereignisse `access.session.*` des Audit-Trails und die Alarmregel für
      Brute-Force)?

## Verwandte Seiten

- [Sicherheitstechnik im Überblick](sicherheitstechnik-im-ueberblick.md)
- [eZUser und Zugriff](../erweiterungsentwicklung/teil-8-referenz/ezuser-und-zugriff.md)
- [Benutzerhygiene](../../funktionen/6.0/benutzerpflege.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
