---
title: Zweistufige Anmeldung
audience: hosting, admin
category: hosting
order: 80
summary: Zweistufige Anmeldung für die Exponential-Websites, die ein Hoster betreibt - was die Erweiterung sevenx_authentication_2fa tut, wie Sie sie pro Website oder Siteaccess einschalten, wie Sie sie für Administratoren verlangen, der Schlüssel für die Geheimnisse der Authenticator-Apps, verlorene Telefone, auth.log und wie ein Hoster sie anbieten kann.
keywords: Zwei-Faktor, zweistufige Anmeldung, 2FA, TOTP, Authenticator-App, E-Mail-Code, sevenx_authentication_2fa, sevenxUser2fa, LoginHandler, Enforce2FA, DefaultMethod, SecretKey, user2fa/setup, user2fa/verify, auth.log, verlorenes Telefon, Social Login, two-factor, two-step sign-in, authenticator app, e-mail code, lost phone, social login
type: help
---
# Zweistufige Anmeldung

Diese Seite ist für Systemadministratoren und Support-Mitarbeitende bei einem Hoster. Mit der zweistufigen Anmeldung gibt ein Benutzer
nach dem Passwort einen Code aus einer Authenticator-App oder aus einer E-Mail ein, sodass ein gestohlenes oder erratenes Passwort allein
nutzlos ist. Sie ist der stärkste einzelne Schutz gegen Credential Stuffing auf der Anmeldeseite. Die Referenz ist
[Zweistufige Anmeldung](../../../../en/us/guides/two-factor-login.md); die Erweiterung ist `sevenx_authentication_2fa`, die `composer.json` verlangt
und die Installer aktivieren.

## Inhalt

1. [Was Benutzer sehen](#was-benutzer-sehen)
2. [Einschalten](#einschalten)
3. [Verlangen](#verlangen)
4. [Der Schlüssel für die Geheimnisse](#der-schlüssel-für-die-geheimnisse)
5. [Verlorene Telefone und Support](#verlorene-telefone-und-support)
6. [Was sie schützt](#was-sie-schützt)
7. [Sie Kunden anbieten](#sie-kunden-anbieten)
8. [Checkliste](#checkliste)

## Was Benutzer sehen

1. Anmelden mit Benutzername und Passwort wie immer.
2. Eine Seite fragt nach dem sechsstelligen Code: aus der Authenticator-App oder aus der E-Mail (auch in deren Betreff). Jeder Code
   funktioniert einmal; nach fünf falschen Codes beginnt die Anmeldung wieder mit dem Passwort.
3. Einrichtung unter *Mein Konto* > *Zweistufige Anmeldung* (`/user2fa/setup`): *Authenticator app* wählen, den QR-Code scannen, den Code
   eingeben. Nichts ändert sich, bis dieser Code eingegeben ist.
4. Der Wechsel zu E-Mail-Codes oder das Ausschalten verlangt zuerst einen aktuellen Code.

## Einschalten

Pro Installation in `settings/override/site.ini.append.php` (oder pro Siteaccess für einen Versuch):

```ini
[ExtensionSettings]
ActiveExtensions[]=sevenx_authentication_2fa

[UserSettings]
LoginHandler[]
LoginHandler[]=sevenxUser2fa
LoginHandler[]=standard
```

Dann:

1. Fügen Sie der Benutzerklasse ein Klassenattribut vom Datentyp *7x 2FA Configuration* hinzu (Bezeichner zum Beispiel `two_factor`).
   Benutzer einer Klasse ohne dieses Attribut können keinen zweiten Schritt behalten.
2. Geben Sie jeder Rolle, die sich anmeldet, die Richtlinien `user2fa/setup` und `user2fa/verify`. Für anonyme Besucher ist nichts nötig.
3. Leeren Sie die Caches und rollen Sie unter Velocity aus: `./console exp:velocity deploy --allow-root-user`.

> **Tipp**
> Probieren Sie es zuerst auf einem Siteaccess aus: Aktivieren Sie die Erweiterung als Zugriffserweiterung eines Test-Siteaccess und
> setzen Sie die Zeilen `LoginHandler` in dessen `site.ini.append.php`. Denken Sie daran, dass `settings/override` Vorrang vor einem
> Siteaccess hat.

## Verlangen

| `[General] Enforce2FA` | `DefaultMethod` | Wirkung |
|---|---|---|
| `disabled` (Standard) | beliebig | Benutzer entscheiden sich auf der Einrichtungsseite selbst dafür |
| `enabled` | `totp` | ein Benutzer ohne zweiten Schritt richtet die App direkt nach dem Passwort ein |
| `enabled` | `email` | ein Benutzer ohne zweiten Schritt bekommt sofort E-Mail-Codes; die Adresse des Kontos muss echt sein |

```ini
# settings/override/sevenxauthentication2fa.ini.append.php
[General]
Enforce2FA=enabled
DefaultMethod=totp
```

Leeren Sie den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini`) und rollen Sie aus. Richten Sie zuerst die Administratoren ein, damit
niemand ohne Telefon überrascht wird. Eine später angelegte Rolle braucht ebenfalls `user2fa/setup` und `user2fa/verify`. Sie mindestens
für Administratoren zu verlangen, ist die Empfehlung von
[Sicherheitshärtung](../../../../en/us/help/security-hardening.md#brute-force-and-credential-stuffing-on-the-sign-in).

## Der Schlüssel für die Geheimnisse

Empfohlen: ein Schlüssel, der die Geheimnisse der Authenticator-Apps in der Datenbank verschlüsselt.

```ini
# settings/override/sevenxauthentication2fa.ini.append.php
[TOTPSettings]
SecretKey=<output of: openssl rand -base64 48>
```

Halten Sie ihn aus der Versionsverwaltung heraus und sichern Sie ihn mit dem Rest von `settings/override/` als Geheimnis. Geht er verloren,
richtet jeder Benutzer die App neu ein. Erzeugen Sie einen Schlüssel pro Website; nutzen Sie nie einen Schlüssel für mehrere Kunden.

## Verlorene Telefone und Support

Ein Administrator der Website öffnet das Objekt des Benutzers in der Administration, setzt das Feld *Zweistufige Anmeldung* auf
*E-mail one-time code* oder *Deaktiviert* und veröffentlicht; der Benutzer meldet sich an und richtet die App neu ein
([Zweistufige Anmeldung und ein verlorenes Telefon](../../../../en/us/help/change-the-admin-password.md#two-step-sign-in-and-a-lost-phone)).

Für den Support-Desk eines Hosters heißt das:

- Prüfen Sie die Identität der anrufenden Person nach dem eigenen Verfahren des Kunden, bevor jemand einen zweiten Schritt zurücksetzt;
  eine Anfrage zum Zurücksetzen ist ein klassischer Weg für Social Engineering.
- Lassen Sie das Zurücksetzen bevorzugt den eigenen Administrator des Kunden tun; die Mitarbeitenden des Hosters tun es nur auf eine
  dokumentierte Anfrage hin.
- Prüfen Sie danach `var/log/auth.log` und das Prüfprotokoll.

## Was sie schützt

Aus [Was sie schützt](../../../../en/us/guides/two-factor-login.md#what-protects-it):

- Das Passwort allein meldet nie an; der Benutzer wird erst nach dem richtigen Code angemeldet, mit einer neuen Sitzungs-ID.
- Falsche Codes zählen als gescheiterte Anmeldungen des Kontos (`[UserSettings] MaxNumberOfFailedLogin` sperrt es).
- E-Mail-Codes werden gehasht gespeichert; ein neuer kann einmal pro Minute angefordert werden, dreimal pro Anmeldung.
- Der QR-Code wird auf dem Server gezeichnet; Seiten mit einem Schlüssel oder einer ausstehenden Anmeldung werden nicht
  zwischengespeichert.
- Jeder Schritt wird nach `var/log/auth.log` geschrieben, nie ein Code oder ein Geheimnis.

## Sie Kunden anbieten

Möglichkeiten, die ein Hoster wählen kann:

- auf jeder neuen Website als freiwillige Option eingeschaltet (Login-Handler, Klassenattribut und Richtlinien von Ihrer Bereitstellung
  gesetzt);
- für Administratorrollen verlangt, als Teil eines verwalteten oder auf Sicherheit ausgerichteten Tarifs;
- ein dokumentiertes Verfahren zum Zurücksetzen als Teil der Support-Stufe ([Support-Stufen](../geschaeft/support-stufen-fuer-exponential-hosting.md)).

## Checkliste

- [ ] Erweiterung aktiv; Reihenfolge von `LoginHandler`: `sevenxUser2fa`, dann `standard`.
- [ ] Die Benutzerklasse hat das Attribut *7x 2FA Configuration*.
- [ ] `user2fa/setup` und `user2fa/verify` auf jeder Rolle, die sich anmeldet.
- [ ] `[TOTPSettings] SecretKey` gesetzt, eindeutig pro Website, als Geheimnis gesichert.
- [ ] `Enforce2FA=enabled` mindestens dort, wo Administratoren sich anmelden, nachdem sie eingerichtet sind.
- [ ] Verfahren zum Zurücksetzen bei verlorenen Telefonen aufgeschrieben, mit Prüfung der Identität.

## Verwandte Seiten

- [Sicherheit für Hoster: Überblick](README.md)
- [Zweistufige Anmeldung (Anleitung)](../../../../en/us/guides/two-factor-login.md)
- [Sicherheit und Audit: absichern](../../../../en/us/guides/security-and-audit.md)
- [Die Administration einschränken](die-administration-einschraenken.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
