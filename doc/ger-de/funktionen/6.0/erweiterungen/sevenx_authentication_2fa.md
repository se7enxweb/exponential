# sevenx_authentication_2fa: Zwei-Faktor- und Social-Login

Diese Seite ist für Administratoren, die eine stärkere Anmeldung möchten, und für Website-Entwickler, die „Mit Google
anmelden“ und Ähnliches anbieten. `sevenx_authentication_2fa` fügt Exponential 6 **Zwei-Faktor-Authentifizierung** und
**Social Login über OAuth** hinzu. Sie wurde am 21. Juli 2026 importiert (1.0.0) und hat minimale Abhängigkeiten: Sie ist
mit PHP 8.5.8 kompatibel, braucht für TOTP nur die Erweiterung `hash` und für OAuth `curl` oder `allow_url_fopen`.

## Was sie bietet

- **TOTP** (RFC 6238) nur mit eingebauten Mitteln von PHP: Google Authenticator, Authy, Microsoft Authenticator und jede
  RFC-konforme App.
- **Einmalcode per E-Mail**, wenn TOTP nicht verfügbar ist oder die gewählte Methode ist, gesendet mit `eZMail`, mit
  einem **fortsetzbaren Link** `/user2fa/verify/code/<code>`, der die Anmeldung beim Anklicken abschließt, auch in einem
  anderen Browser.
- Einen **Datentyp** `sevenxauthentication2fa` an der Klasse `user`, sodass Benutzer ihre eigene Methode und ihr
  Geheimnis einrichten.
- Einen **Login-Handler** `sevenxUser2fa`, der das Passwort prüft und dann die Anmeldung abschließt oder zur Abfrage
  weiterleitet.
- Eine Oberfläche zum **Zurücksetzen der 2FA**, eine Zurück-Schaltfläche und Templates für das Bearbeiten von Benutzern,
  die Registrierung und den Fall der erzwungenen Einrichtung (ein Benutzer ohne eingerichtete Methode, der eine
  einrichten muss, bevor er die Website verwendet).
- **Handler für Social Login** (Gerüste, bereit zum Anpassen) für Google, Facebook, Twitter/X, Instagram, Meta und ID.me,
  eine Basisklasse und einen Kommandozeilen-Generator für weitere Anbieter.
- **Kein Redis oder Valkey nötig**: Offene Abfragen sind JSON-Dateien in `var/site/cache/sevenx_2fa_pending/`, mit
  Benutzer-ID und Hash des Codes als Schlüssel, entfernt bei Erfolg und vom Aufräumskript.

## Einrichten

1. Aktivieren Sie die Extension und ihren Login-Handler in `settings/override/site.ini.append.php`:

```ini
[ExtensionSettings]
ActiveExtensions[]=sevenx_authentication_2fa
[UserSettings]
ExtensionDirectory[]=sevenx_authentication_2fa
LoginHandler[user2fa]=sevenxUser2fa
LoginHandler[]=standard
```

   Der Standard-Handler meldet den Benutzer an, bevor die Abfrage laufen kann, daher muss er **nach** `sevenxUser2fa`
   stehen. Entfernen oder kommentieren Sie ein `LoginHandler[]=standard` in `settings/site.ini` aus, damit die
   Reihenfolge der Überschreibung wirkt.

2. Erzeugen Sie die Autoloads neu und leeren Sie die Caches:

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all --allow-root-user
```

3. Gewähren Sie die Richtlinien (unten).
4. Melden Sie sich als Testbenutzer an und öffnen Sie `/user2fa/setup`, um TOTP oder E-Mail einzuschalten.

### Rollenrichtlinien

Das Modul `user2fa` hat vier Funktionen: `setup`, `verify`, `oauth` und `callback`. Gewähren Sie sie nur über **Rollen
und Richtlinien**:

| Ansicht | Gewähren |
|---|---|
| `/user2fa/oauth/<provider>` | `user2fa/oauth` der Rolle **Anonymous** |
| `/user2fa/callback/<provider>` | `user2fa/callback` der Rolle **Anonymous** |
| `/user2fa/verify`, `/user2fa/verify/code/<code>` | `user2fa/verify` der Rolle **Anonymous** und jeder Rolle, die 2FA verwendet |
| `/user2fa/setup` | `user2fa/setup` den Rollen, die ihre eigene 2FA verwalten (Member, Editor, Partner, Administrator, eigene Rollen) |

Die Ansichten prüfen ihren eigenen Zustand: `verify` verarbeitet eine Anfrage nur, wenn eine gültige offene Abfrage
existiert.

## Verwenden

- **Zwei-Faktor:** Benutzer öffnen `/user2fa/setup`, um TOTP- oder E-Mail-2FA einzuschalten. Die Seite zeigt das
  Geheimnis, eine URI `otpauth://` und einen QR-Code. Das QR-Bild wird über die Google Chart API erzeugt; wenn Sie keinen
  externen Dienst wollen, verwenden Sie die URI oder das einfache Geheimnis.
- **Social Login:** Legen Sie beim Anbieter eine OAuth-Anwendung an, füllen Sie den Block des Anbieters aus, setzen Sie
  `[SocialLogin] Enabled=enabled` in `sevenxauthentication2fa.ini` (der Hauptschalter, Standard `disabled`) und
  verlinken Sie auf `/user2fa/oauth/<provider>`. Der Anbieter kehrt zu `/user2fa/callback/<provider>` zurück.
- **Einen Anbieter hinzufügen:** Das legt den Handler an und hängt seinen INI-Block an; bearbeiten Sie dann
  `normalizeUserInfo()`:

```bash
php extension/sevenx_authentication_2fa/bin/php/sevenx2fabuild.php --provider=MyProvider --client-id=... --client-secret=... --authorization-url=... --token-url=... --userinfo-url=... --scope="email profile"
```

- **Die Zugangsdaten eines Anbieters setzen, ohne die INI-Datei zu bearbeiten:** Das schreibt die passende
  `sevenxauthentication2fa.ini.append.php` in `settings/override` (oder in den Siteaccess-Ordner) und leert den
  INI-Cache. Es ändert Einstellungen: Führen Sie es nur aus, wenn Sie das wollen.

```bash
php extension/sevenx_authentication_2fa/bin/php/sevenx2faconfig.php --provider=Google --siteaccess=<siteaccess> --client-id=... --client-secret=...
```

- **Aufräumen:** entfernt abgelaufene Abfragen (auch als Cronjob-Skript verwendbar):

```bash
php extension/sevenx_authentication_2fa/bin/php/sevenx2facleanup.php
```

## Einstellungen

Alle Schlüssel stehen in `sevenxauthentication2fa.ini`.

| Block | Schlüssel | Standard | Bedeutung |
|---|---|---|---|
| `General` | `Enabled` | `enabled` | Zwei-Faktor ein oder aus |
| `General` | `Issuer` | `Exponential` | In der Authenticator-App angezeigter Name |
| `General` | `DefaultMethod` | `disabled` | `totp`, `email` oder `disabled` |
| `General` | `Enforce2FA` | `disabled` | Benutzer ohne Methode zwingen, eine einzurichten |
| `General` | `AllowEmailFallback` | `enabled` | Den Code per E-Mail als Rückfall erlauben |
| `CodeSettings` | `Length`, `TimeStep`, `Window`, `Algorithm` | `6`, `30`, `1`, `SHA1` | TOTP-Parameter |
| `CodeSettings` | `EmailTTL` | `600` | Sekunden, die ein Code per E-Mail gilt |
| `EmailSettings` | `Subject`, `Sender` | `Your login verification code is {code}`, leer | Betreff und Absender der E-Mail |
| `EmailSettings` | `Body` | leer | Optionaler Text mit `{code}`, `{expires}`, `{site_url}`, `{verify_url}`; leer verwendet das Template |
| `TOTPSettings` | `SecretLength` | `20` | Länge des Geheimnisses |
| `SocialLogin` | `Enabled`, `AutoCreateUser` | `disabled`, `disabled` | Hauptschalter des Social Login; unbekannte Benutzer anlegen |
| `SocialLogin` | `DefaultUserGroupNodeID`, `AuthenticationMatch` | `12`, `email` | Gruppe automatisch angelegter Benutzer; Konten nach E-Mail zuordnen |
| `<Provider>` (Google, Facebook, TwitterX, Instagram, ...) | `Enabled`, `ClientID`, `ClientSecret`, `Scope`, `AuthorizationURL`, `TokenURL`, `UserInfoURL` | aus, leer | Ein Block pro Anbieter |

Der Text der E-Mail ist das Template `design/standard/templates/mail/2fa_code.tpl` (Variablen `$code`, `$expires`,
`$site_url`, `$verify_url`); kopieren Sie es in Ihr Website-Design, um es mit Ihrer Marke zu versehen. Halten Sie
Client-Geheimnisse in `settings/override/`, nie in der eigenen Einstellungsdatei der Extension unter
Versionsverwaltung.

## Verwandte Seiten

- [Das Passwort eines Benutzers zurücksetzen](../benutzerpasswort-zuruecksetzen.md)
- [Prüfprotokoll](../pruefprotokoll.md)
- [Chronik](../../../geschichte/erweiterungen/sevenx_authentication_2fa.md) und [Versionshinweise](../../../../changelogs/extensions/sevenx_authentication_2fa.md)
- [Änderungsverzeichnis](../../../geschichte/aenderungsverzeichnis/sevenx_authentication_2fa.md)
- [Verhaltensänderungen der Extensions](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
- [Monat: 2026-07 (alle Extensions)](../../../geschichte/erweiterungen/monate/2026-07.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
