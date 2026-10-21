---
title: Die zweistufige Anmeldung für Mitarbeiter einschalten
audience: government, admin
category: public-sector
order: 230
summary: Schritt für Schritt - die 2FA-Erweiterung und den Anmeldehandler prüfen, das Feld zur Benutzerklasse hinzufügen, jeder Mitarbeiterrolle user2fa/setup und user2fa/verify geben, die Geheimnisse verschlüsseln, zuerst die Administratoren einrichten, dann mit Enforce2FA verlangen.
keywords: zweistufige Anmeldung, Zwei-Faktor, 2FA, MFA, TOTP, Authenticator, Enforce2FA, user2fa/setup, user2fa/verify, sevenx_authentication_2fa, Einführung, two-step sign-in, two-factor, authenticator, roll-out
type: help
---
# Die zweistufige Anmeldung für Mitarbeiter einschalten

Diese Aufgabe ist für Administratoren. Am Ende melden sich Mitarbeiter mit einem Passwort und einem Code aus einer
Authenticator-App an. Jede Einstellung steht in [Zweistufige Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md); der Plan
für die Einführung steht auf
[Zweistufige Anmeldung für Mitarbeiter](../../betrieb/personen-und-berechtigungen/zwei-faktor-anmeldung-fuer-beschaeftigte.md).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Die Erweiterung und den Handler prüfen](#die-erweiterung-und-den-handler-prüfen)
3. [Das Feld und die Richtlinien](#das-feld-und-die-richtlinien)
4. [Die Geheimnisse verschlüsseln](#die-geheimnisse-verschlüsseln)
5. [Einrichten, dann verlangen](#einrichten-dann-verlangen)
6. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
7. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Shell-Zugang und ein Administratorkonto; probieren Sie es zuerst auf einer Testkopie aus, niemals mit einem echten
  Administratorkonto.
- Eine fertige Mitteilung an die Mitarbeiter: welche App, was bei einem neuen Telefon zu tun ist, wen man anruft.

## Die Erweiterung und den Handler prüfen

```bash
./console exp:ini get 'site.ini/ExtensionSettings/ActiveExtensions' --allow-root-user
./console exp:ini get 'site.ini/UserSettings/LoginHandler' --allow-root-user
```

`sevenx_authentication_2fa` muss aktiv sein (`composer.json` verlangt sie, und die Installer aktivieren sie), und
`LoginHandler` muss `sevenxUser2fa` vor `standard` listen. Wenn nicht, fügen Sie
`settings/override/site.ini.append.php` hinzu:

```ini
[UserSettings]
LoginHandler[]
LoginHandler[]=sevenxUser2fa
LoginHandler[]=standard
```

## Das Feld und die Richtlinien

1. Die Benutzerklasse braucht ein Attribut vom Datentyp *7x 2FA Configuration* (Bezeichner zum Beispiel `two_factor`).
   Ohne es können Benutzer keinen zweiten Schritt behalten.
2. Geben Sie jeder Rolle, deren Benutzer sich anmelden, die Richtlinien `user2fa/setup` und `user2fa/verify`
   (**Benutzer > Rollen und Richtlinien**, jede Rolle, **Bearbeiten**, **Neue Richtlinie**, Modul `user2fa`).
   Administrator hat sie über vollen Zugriff. Vermerken Sie es in Ihrer Dokumentation der Rollen: Eine später
   angelegte Rolle braucht sie ebenfalls.

## Die Geheimnisse verschlüsseln

In `settings/override/sevenxauthentication2fa.ini.append.php` (halten Sie die Datei aus der Versionsverwaltung heraus
und in Ihrer Sicherung der Geheimnisse):

```ini
[TOTPSettings]
SecretKey=<output of: openssl rand -base64 48>
```

Geht der Schlüssel verloren, richten alle die App neu ein.

## Einrichten, dann verlangen

1. Administratoren und das Web-Team richten zuerst ein: **Mein Konto > Zweistufige Anmeldung** (`/user2fa/setup`), die
   Authenticator-App wählen, den Code scannen, die sechs Ziffern eingeben.
2. Wenn alles bereit ist, verlangen Sie es:

   ```ini
   # settings/override/sevenxauthentication2fa.ini.append.php
   [General]
   Enforce2FA=enabled
   DefaultMethod=totp
   ```

3. Leeren Sie den INI-Cache und rollen Sie aus:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   ./console exp:velocity deploy --allow-root-user
   ```

## Prüfen, ob es funktioniert hat

- Ein Testbenutzer ohne zweiten Schritt wird direkt nach dem Passwort zur Einrichtung der App geschickt.
- Ein falscher Code wird abgelehnt; `var/log/auth.log` zeichnet den Schritt auf (niemals den Code).
- Kontoseiten zeigen für eingerichtete Mitarbeiter keinen Hinweis mehr, dass die zweistufige Anmeldung aus ist.

## Verwandte Seiten
- [Einem Benutzer helfen, der sein Telefon verloren hat](einem-benutzer-helfen-der-sein-telefon-verloren-hat.md)
- [Zweistufige Anmeldung für Mitarbeiter](../../betrieb/personen-und-berechtigungen/zwei-faktor-anmeldung-fuer-beschaeftigte.md)
- [Zwei-Faktor-Anmeldung (Hosting)](../../../hosting/sicherheit/zweistufige-anmeldung.md)
- [Sicherheit und Audit, Schritt 5](../../../../anleitungen/sicherheit-und-audit.md#5-zwei-faktor-anmeldung-optional-10-minuten)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
