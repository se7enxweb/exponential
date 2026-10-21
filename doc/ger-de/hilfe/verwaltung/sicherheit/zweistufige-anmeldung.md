---
title: Zweistufige Anmeldung für Beschäftigte öffentlicher Stellen
audience: government, admin, project-manager
category: public-sector
order: 110
summary: Für öffentliche Stellen, die eine Mehr-Faktor-Authentifizierung verlangen - wie die zweistufige Anmeldung von Exponential funktioniert (Authenticator-App oder Code per E-Mail, Erweiterung sevenx_authentication_2fa), wie Sie sie einschalten und verpflichtend machen, wie Sie sie bei den Beschäftigten einführen und wie Sie mit einem verlorenen Telefon umgehen.
keywords: zweistufige Anmeldung, Zwei-Faktor-Authentifizierung, Mehr-Faktor-Authentifizierung, Authenticator-App, Code per E-Mail, verlorenes Telefon, Einführung, öffentlicher Sektor, two-step sign-in, two-factor authentication, 2FA, MFA, TOTP, authenticator app, e-mail code, sevenx_authentication_2fa, Enforce2FA, DefaultMethod, user2fa, lost phone, rollout, public sector
type: help
---
# Zweistufige Anmeldung für Beschäftigte öffentlicher Stellen

Diese Seite richtet sich an Administratorinnen und Administratoren sowie Projektverantwortliche, die für die
Beschäftigten, die eine öffentliche Website bearbeiten, eine Mehr-Faktor-Authentifizierung einführen müssen. Die
zweistufige Anmeldung von Exponential stellt die Erweiterung `sevenx_authentication_2fa` bereit, die `composer.json`
verlangt und die Installationsprogramme aktivieren; sie ist freiwillig, bis Sie sie verpflichtend machen
([Zweistufige Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md)).

## Inhalt

1. [Was die Beschäftigten erleben](#was-die-beschäftigten-erleben)
2. [Einschalten](#einschalten)
3. [Verpflichtend machen](#verpflichtend-machen)
4. [Einführen](#einführen)
5. [Verlorenes Telefon und andere Supportfälle](#verlorenes-telefon-und-andere-supportfälle)
6. [Was sie schützt](#was-sie-schützt)
7. [Verwandte Seiten](#verwandte-seiten)

## Was die Beschäftigten erleben

1. Anmeldung wie bisher mit Benutzername und Passwort.
2. Eine Seite fragt nach einem 6-stelligen Code: aus einer Authenticator-App oder aus einer E-Mail.
3. Einrichtung unter *Mein Konto* > *Zweistufige Anmeldung* (`/user2fa/setup`): den QR-Code scannen, einen Code zur
   Bestätigung eingeben.
4. Das Ändern der Methode oder das Abschalten verlangt einen aktuellen Code.

## Einschalten

Setzen Sie in `settings/override/site.ini.append.php`, bei aktiver Erweiterung, den Login-Handler vor `standard`:

```ini
[UserSettings]
LoginHandler[]
LoginHandler[]=sevenxUser2fa
LoginHandler[]=standard
```

Dann:

1. Fügen Sie der Benutzerklasse ein Klassenattribut des Datentyps *7x 2FA Configuration* hinzu (zum Beispiel mit dem
   Bezeichner `two_factor`);
2. geben Sie jeder Rolle, die sich anmeldet, die Richtlinien `user2fa/setup` und `user2fa/verify`;
3. setzen Sie einen Verschlüsselungsschlüssel für die Authenticator-Geheimnisse in
   `settings/override/sevenxauthentication2fa.ini.append.php`:
   `[TOTPSettings] SecretKey=<output of: openssl rand -base64 48>` (geht er verloren, muss jeder die App neu
   einrichten);
4. leeren Sie die Caches und stellen Sie bereit: `./console exp:velocity deploy --allow-root-user` unter Velocity.

Probieren Sie es zuerst auf einem Test-Siteaccess aus, wie der Leitfaden beschreibt.

## Verpflichtend machen

| `[General] Enforce2FA` | `DefaultMethod` | Wirkung |
|---|---|---|
| `disabled` (Standard) | beliebig | Benutzer entscheiden selbst |
| `enabled` | `totp` | ein Benutzer ohne zweiten Schritt richtet direkt nach dem Passwort die App ein |
| `enabled` | `email` | ein Benutzer ohne zweiten Schritt erhält sofort Codes per E-Mail |

Setzen Sie das in `settings/override/sevenxauthentication2fa.ini.append.php`, dann
`php bin/php/ezcache.php --clear-tag=ini` und bereitstellen. `Enforce2FA` gilt für jedes Konto, das sich über den
Handler anmeldet.

## Einführen

1. Registrieren Sie zuerst die Administratorinnen und Administratoren, damit niemand ohne Telefon überrascht wird.
2. Kündigen Sie der Redaktion den Termin mit einer einseitigen Anleitung und dem Supportkontakt an.
3. Bieten Sie zwei Wochen lang die freiwillige Nutzung an; setzen Sie dann `Enforce2FA=enabled`.
4. Eine später angelegte Rolle braucht für ihre Benutzer `user2fa/setup` und `user2fa/verify`.
5. Prüfen Sie Konten auf ihrer Kontoseite: Ein Hinweis erscheint, wenn die zweistufige Anmeldung aus ist
   ([Sicherheit und Audit, 6](../../../anleitungen/sicherheit-und-audit.md#6-ein-konto-auf-seiner-kontoseite-prüfen-2-minuten)).

## Verlorenes Telefon und andere Supportfälle

- Eine Administratorin oder ein Administrator öffnet das Objekt des Benutzers, setzt das Feld *Zweistufige Anmeldung*
  auf *E-mail one-time code* (Einmalcode per E-Mail) oder *Deaktiviert* und veröffentlicht; der Benutzer meldet sich an
  und richtet die App neu ein.
- Falsche Codes zählen als fehlgeschlagene Anmeldungen des Kontos; ist `MaxNumberOfFailedLogin` gesetzt, können sie es
  sperren.
- Jeder Schritt wird in `var/log/auth.log` geschrieben (nie ein Code oder ein Geheimnis), lesbar unter
  Setup > Logdateien.

## Was sie schützt

Das Passwort allein meldet nie an; die Sitzung erhält nach dem Code eine neue ID; jeder Code funktioniert einmal; Codes
per E-Mail werden gehasht gespeichert und in der Rate begrenzt; der QR-Code wird auf dem Server gezeichnet, und der
Schlüssel geht an keinen anderen Dienst; Umleitungen durchlaufen die Regeln für sichere Umleitungen
([Zweistufige Anmeldung: Was sie schützt](../../../anleitungen/zwei-faktor-anmeldung.md#was-sie-schützt)). Schaltflächen für
Social Login beziehen, sofern aktiviert, Identitätsanbieter Dritter ein (siehe den Datenschutzbereich dieser Hilfe).

## Verwandte Seiten

- [Zweistufige Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md)
- [Zwei-Faktor-Anmeldung für Hoster](../../hosting/sicherheit/zweistufige-anmeldung.md)
- [sevenx_authentication_2fa](../../../../eng-us/features/6.0/extensions/sevenx_authentication_2fa.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
