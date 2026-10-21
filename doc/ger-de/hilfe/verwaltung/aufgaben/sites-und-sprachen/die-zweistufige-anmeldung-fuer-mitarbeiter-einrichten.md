---
title: Die zweistufige Anmeldung für Mitarbeiter einrichten
audience: government, admin
category: public-sector
order: 210
summary: Aufgabe für Administratoren - die zweistufige Anmeldung (Authenticator-App oder E-Mail-Code) mit sevenx_authentication_2fa einschalten, sie für Mitarbeiter verlangen, die Geheimnisse schützen und sie testen.
keywords: zweistufige Anmeldung, Zwei-Faktor, Authenticator-App, Mitarbeiter, Sicherheit, two-step sign-in, 2FA, MFA, authenticator app, Enforce2FA, sevenxUser2fa, user2fa, staff, security
type: help
---
# Die zweistufige Anmeldung für Mitarbeiter einrichten

Diese Aufgabe richtet sich an Administratoren. Hintergrund: [Zweistufige Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md)
und [Zweistufige Anmeldung für Mitarbeiter](../../aufbauen/intranets/zweistufige-anmeldung-fuer-mitarbeiter.md).

## Schritte

1. Aktivieren Sie `sevenx_authentication_2fa` und setzen Sie seinen Handler an die erste Stelle:

   ```ini
   # settings/override/site.ini.append.php
   [UserSettings]
   LoginHandler[]
   LoginHandler[]=sevenxUser2fa
   LoginHandler[]=standard
   ```

2. Fügen Sie der Benutzerklasse ein Feld des Datentyps *7x 2FA Configuration* hinzu.
3. Geben Sie jeder Rolle, die sich anmeldet, die Richtlinien `user2fa/setup` und `user2fa/verify`.
4. Setzen Sie `[TOTPSettings] SecretKey` in `settings/override/sevenxauthentication2fa.ini.append.php` (außerhalb der
   Versionsverwaltung).
5. Verlangen Sie sie: `[General] Enforce2FA=enabled`, `DefaultMethod=totp`.
6. Leeren Sie die Caches; mit Velocity `./console exp:velocity deploy`.

Probieren Sie es zuerst auf einem Test-Siteaccess aus, wie die Anleitung empfiehlt.

## Prüfen

Melden Sie sich mit einem Testkonto für Mitarbeiter an: Nach dem Passwort ist die Einrichtung der App verpflichtend;
nach der Einrichtung wird bei jeder Anmeldung ein Code abgefragt. Fünf falsche Codes beenden die Anmeldung.

## Probleme

| Problem | Lösung |
|---|---|
| Ein Benutzer hat sein Telefon verloren | öffnen Sie das Benutzerobjekt, setzen Sie **Zweistufige Anmeldung** auf *E-mail one-time code* oder *Deaktiviert*, veröffentlichen Sie; prüfen Sie zuerst die Identität |
| Benutzer einer Klasse können keinen zweiten Schritt behalten | der Klasse fehlt das 2FA-Feld |

## Verwandte Seiten

- [Anmeldeoptionen für Mitarbeiter](../../aufbauen/intranets/anmeldeoptionen-fuer-mitarbeiter.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
