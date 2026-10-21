---
title: Zweistufige Anmeldung für Mitarbeiter
audience: government, admin
category: public-sector
order: 70
summary: Wie Sie mit der Erweiterung sevenx_authentication_2fa für Mitarbeiter einen zweiten Schritt bei der Anmeldung verlangen (Authenticator-App oder E-Mail-Code) - einschalten, verpflichtend machen, die Geheimnisse schützen, verlorene Telefone und die Kombination mit anderen Login-Handlern.
keywords: zweistufige Anmeldung, Zwei-Faktor, Authenticator-App, E-Mail-Code, Sicherheit der Mitarbeiter, two-step sign-in, two-factor, 2FA, MFA, TOTP, authenticator app, e-mail code, Enforce2FA, sevenx_authentication_2fa, user2fa, staff security
type: help
---
# Zweistufige Anmeldung für Mitarbeiter

Diese Seite richtet sich an Administratoren. Nach dem Passwort macht ein Code aus einer Authenticator-App oder ein per
E-Mail gesendeter Code das Passwort allein für andere nutzlos. Bereitgestellt wird das von
`sevenx_authentication_2fa`, das `composer.json` verlangt
([Zweistufige Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md)).

## Inhalt

1. [Einschalten](#einschalten)
2. [Verpflichtend machen](#verpflichtend-machen)
3. [Die Geheimnisse schützen](#die-geheimnisse-schützen)
4. [Verlorene Telefone](#verlorene-telefone)
5. [Mit anderen Login-Handlern](#mit-anderen-login-handlern)

## Einschalten

1. Aktivieren Sie die Erweiterung und setzen Sie ihren Login-Handler vor `standard`:

   ```ini
   [UserSettings]
   LoginHandler[]
   LoginHandler[]=sevenxUser2fa
   LoginHandler[]=standard
   ```

2. Fügen Sie der Benutzerklasse ein Feld des Datentyps *7x 2FA Configuration* hinzu.
3. Geben Sie jeder Rolle, die sich anmeldet, die Richtlinien `user2fa/setup` und `user2fa/verify`.
4. Leeren Sie die Caches; mit Velocity `./console exp:velocity deploy`.

Benutzer richten sie unter **Mein Konto** > **Zweistufige Anmeldung** (`/user2fa/setup`) ein.

## Verpflichtend machen

`[General] Enforce2FA=enabled` mit `DefaultMethod=totp` lässt einen Benutzer ohne zweiten Schritt die App direkt nach
dem Passwort einrichten; mit `email` erhält er sofort E-Mail-Codes (die Adresse des Kontos muss echt sein). Für
Mitarbeiter mit administrativen Rechten machen Sie sie verpflichtend.

## Die Geheimnisse schützen

Setzen Sie `[TOTPSettings] SecretKey` in `settings/override/sevenxauthentication2fa.ini.append.php`, um die Geheimnisse
der Authenticator-Apps in der Datenbank zu verschlüsseln; halten Sie ihn aus der Versionsverwaltung heraus. Geht er
verloren, muss jeder die App neu einrichten.

## Verlorene Telefone

Ein Administrator öffnet das Objekt des Benutzers, setzt das Feld *Zweistufige Anmeldung* auf *E-mail one-time code*
oder *Deaktiviert* und veröffentlicht; der Benutzer meldet sich an und richtet die App neu ein. Prüfen Sie zuerst die
Identität der Person, nach Ihrem eigenen Verfahren.

## Mit anderen Login-Handlern

Die dokumentierte Einrichtung verkettet den 2FA-Handler mit `standard`. Ob er für Konten gilt, die der LDAP-Handler
angelegt hat, ist nicht dokumentiert; testen Sie die Kombination, bevor Sie sich darauf verlassen (siehe „Anmeldung
über LDAP“).

## Verwandte Seiten

- [Zweistufige Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md)
- [Sicherheit und Audit](../../../../anleitungen/sicherheit-und-audit.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
