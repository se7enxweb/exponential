---
title: Zwei-Faktor und Anmeldung
audience: businesses, admin, hosting
category: business-solutions
order: 70
summary: Was eine Organisation darüber entscheidet, wie sich Mitarbeiter in einem Exponential-Intranet anmelden - Passwortregeln, Sperre nach fehlgeschlagenen Anmeldungen, Ablauf von Passwörtern, zweistufige Anmeldung mit sevenx_authentication_2fa, Social Login, Sitzungen und die Aufzeichnung der Anmeldungen.
keywords: Anmeldung, Login, Zwei-Faktor, 2FA, zweistufige Anmeldung, TOTP, Authenticator, E-Mail-Code, sevenx_authentication_2fa, Enforce2FA, Social Login, Passwortregeln, MaxNumberOfFailedLogin, ezmbpaex, Ablauf von Passwörtern, Sitzungen, auth.log, sign-in, login, two-factor, two-step sign-in, authenticator, e-mail code, social login, password rules, password expiry, sessions
type: help
---
# Zwei-Faktor und Anmeldung

Diese Seite ist für Intranetverantwortliche und IT-Verantwortliche, die entscheiden, wie Mitarbeiter nachweisen, wer
sie sind. Ein gestohlenes Passwort ist der häufigste Weg in ein Mitarbeiterportal; diese Seite listet die Schutzmaßnahmen
auf, die Exponential bietet, welche standardmäßig eingeschaltet sind und welche Sie einschalten, und verlinkt die
Anleitungen für die Arbeit. Sie ist ein Überblick, keine Rechts- oder Prüfungsberatung.

## Inhalt

1. [Die Schichten](#die-schichten)
2. [Passwortregeln](#passwortregeln)
3. [Ein Konto nach fehlgeschlagenen Anmeldungen sperren](#ein-konto-nach-fehlgeschlagenen-anmeldungen-sperren)
4. [Zweistufige Anmeldung](#zweistufige-anmeldung)
5. [Social Login](#social-login)
6. [Ablauf von Passwörtern](#ablauf-von-passwörtern)
7. [Sitzungen und Abmelden](#sitzungen-und-abmelden)
8. [Aufzeichnungen der Anmeldungen](#aufzeichnungen-der-anmeldungen)
9. [Entscheidungen für das Intranet](#entscheidungen-für-das-intranet)
10. [Verwandte Seiten](#verwandte-seiten)

## Die Schichten

| Schicht | Standardmäßig an | Wo sie eingestellt wird | Anleitung |
|---|---|---|---|
| Mindestlänge des Passworts 10 und eine Prüfung auf schwache Passwörter | ja (`MinPasswordLength=10`, `WeakPasswordCheck=enabled` in `settings/site.ini`) | `[UserSettings]` | [Ihr Passwort ändern](../../../../eng-us/features/6.0/modern-password-change.md) |
| Weitere Passwortregeln (Großbuchstabe, Ziffer, Sonderzeichen ...) | nein, jede Regel ist standardmäßig aus | `site.ini [PasswordSettings]` | dieselbe |
| Andere Sitzungen nach einer Passwortänderung abgemeldet | ja (`EndOtherSessions=enabled`) | `[PasswordSettings]` | dieselbe |
| Sperre nach N fehlgeschlagenen Anmeldungen | nein (`MaxNumberOfFailedLogin=0`) | `[UserSettings]` | [Sicherheitshärtung](../../sicherheitshaertung.md#brute-force-und-credential-stuffing-bei-der-anmeldung) |
| Zweistufige Anmeldung | Erweiterung, freiwillig | `sevenxauthentication2fa.ini` | [Zweistufige Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md) |
| Ablauf von Passwörtern | Erweiterung `ezmbpaex` | ihre eigenen Einstellungen | [ezmbpaex](../../../../eng-us/features/6.0/extensions/ezmbpaex.md) |
| Netzwerkbeschränkungen | Webserver | Apache, nginx, ein VPN | [Die Administration einschränken](../../hosting/sicherheit/die-administration-einschraenken.md#einschränkungen-im-netzwerk) |

## Passwortregeln

Seit 6.0.15 gelten dieselben Regeln für jedes Formular, das ein Passwort setzt: Registrierung, Bearbeiten eines
Benutzerkontos, die Seite zum Ändern des Passworts und der Konsolenbefehl `bin/php/resetuserpassword.php`. Eine
Passwortänderung schickt dem Benutzer eine Mail „Your password was changed“ ohne das Passwort und meldet die anderen
Sitzungen des Benutzers bei ihrer nächsten Anfrage ab
([Ihr Passwort ändern](../../../../eng-us/features/6.0/modern-password-change.md#in-short)).

`site.ini [PasswordSettings]` fügt Regeln hinzu, alle standardmäßig aus, damit eine aktualisierte Website die Regeln
behält, die sie hatte: `RequireLowercase`, `RequireUppercase`, `RequireDigit`, `RequireSymbol`, `MinCharacterClasses`,
`ForbidLogin`, `ForbidCurrentPassword`. Wählen Sie sie mit Ihrer Sicherheitsrichtlinie; längere Passwörter helfen
Mitarbeitern meist mehr als komplizierte.

## Ein Konto nach fehlgeschlagenen Anmeldungen sperren

`site.ini [UserSettings] MaxNumberOfFailedLogin` sperrt ein Konto nach so vielen fehlgeschlagenen Versuchen. Es wird
als `0` ausgeliefert, was aus bedeutet. `TrustedIPList[]` kann Büroadressen ausnehmen. Gesperrte Konten werden unter
**Benutzer > Benutzerkonten prüfen** aufgelistet und gesammelt entsperrt
([Benutzerkonten prüfen](../../../../eng-us/features/6.0/user-hygiene.md#the-filters)).

> **Warnung**
> Setzen Sie `MaxNumberOfFailedLogin` in einem Intranet, das aus dem Internet erreichbar ist, auf einen Wert, der zu
> Ihrer Richtlinie passt. Die Anmeldeseite ist die am häufigsten angegriffene Adresse jeder Website
> ([Die Administration einschränken](../../hosting/sicherheit/die-administration-einschraenken.md)).

## Zweistufige Anmeldung

Die Erweiterung `sevenx_authentication_2fa` (in `composer.json` aufgeführt) fügt nach dem Passwort einen zweiten
Schritt hinzu: einen sechsstelligen Code aus einer Authenticator-App (TOTP, RFC 6238) oder einen per E-Mail
verschickten Code ([sevenx_authentication_2fa](../../../../eng-us/features/6.0/extensions/sevenx_authentication_2fa.md)).

Was Mitarbeiter sehen ([Zweistufige Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md#was-ein-benutzer-sieht)):

1. Wie immer mit Benutzername und Passwort anmelden.
2. Den Code aus der App oder aus der Mail eingeben.
3. Die Einrichtung einmalig unter **Mein Konto > Zweistufige Anmeldung** (`/user2fa/setup`) durch Scannen eines
   QR-Codes.

Was die Organisation entscheidet:

| Einstellung (`[General]`) | Wirkung |
|---|---|
| `Enforce2FA=disabled` (Standard) | Mitarbeiter entscheiden sich selbst dafür |
| `Enforce2FA=enabled`, `DefaultMethod=totp` | jeder ohne zweiten Schritt richtet gleich nach dem Passwort die App ein |
| `Enforce2FA=enabled`, `DefaultMethod=email` | jeder ohne zweiten Schritt bekommt sofort E-Mail-Codes |

Jede Rolle, die sich anmeldet, braucht die Richtlinien `user2fa/setup` und `user2fa/verify`. Ein Schlüssel in
`settings/override/sevenxauthentication2fa.ini.append.php` verschlüsselt die Geheimnisse der Authenticator-Apps; geht
er verloren, richten alle die App neu ein
([Zweistufige Anmeldung, Turning it on](../../../anleitungen/zwei-faktor-anmeldung.md#einschalten)).

Verlorene Telefone: Ein Administrator setzt das Feld *Zweistufige Anmeldung* des Benutzers auf E-Mail-Codes oder
Deaktiviert und veröffentlicht; der Benutzer richtet die App dann neu ein. Planen Sie, wer im Helpdesk das tun darf.

Auf dem Demonstrationsserver ist die zweistufige Anmeldung seit dem 6. Oktober 2026 als freiwillige Option eingeschaltet
([Zweistufige Anmeldung, On the demonstration server](../../../anleitungen/zwei-faktor-anmeldung.md#auf-dem-demonstrationsserver)).

## Social Login

Dieselbe Erweiterung enthält Social-Login-Handler für Google, Facebook, Twitter/X, Instagram, Meta und ID.me,
beschrieben als Gerüste, die zum Anpassen bereitstehen, sowie einen Kommandozeilen-Generator für weitere
OAuth-Anbieter. Social Login ist aus, bis `[SocialLogin] Enabled=enabled` gesetzt ist; ein Benutzer, der sich so
anmeldet, durchläuft trotzdem seinen eigenen zweiten Schritt
([sevenx_authentication_2fa, Use it](../../../../eng-us/features/6.0/extensions/sevenx_authentication_2fa.md#use-it)). Für ein
Mitarbeiter-Intranet ist ein Firmenverzeichnis meist die bessere Quelle für Konten
([Verzeichnisanmeldung und LDAP](verzeichnisanmeldung-und-ldap.md)).

## Ablauf von Passwörtern

`ezmbpaex` legt fest, wie lange ein Passwort gültig ist, kann vor dem Ablauf per Mail warnen und neue Passwörter gegen
einen regulären Ausdruck prüfen ([ezmbpaex](../../../../eng-us/features/6.0/extensions/ezmbpaex.md)). Ob ein erzwungener Ablauf
zu Ihrer Richtlinie passt, entscheidet Ihr Sicherheitsbeauftragter.

## Sitzungen und Abmelden

Eine Sitzung endet `site.ini [Session] SessionTimeout` Sekunden nach ihrer letzten Nutzung; der Standard ist 259200,
drei Tage. Administratoren können sehen, wer sich kürzlich angemeldet hat, und mit dem Datenbank-Sitzungshandler
Sitzungen einzeln entfernen ([Sitzungen](../../../anleitungen/sitzungen.md)). Kürzere Zeitlimits passen zu gemeinsam
genutzten Computern.

## Aufzeichnungen der Anmeldungen

Der Audit-Trail zeichnet in seinem Kanal `access` Anmeldungen, fehlgeschlagene Anmeldungen, Kontosperren,
Rollenzuordnungen und das Zurücksetzen von Passwörtern auf
([Der Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md#what-is-recorded)). Die zweistufige Anmeldung schreibt jeden
Schritt in `var/log/auth.log`, nie einen Code oder ein Geheimnis.

## Entscheidungen für das Intranet

- [ ] Passwortregeln in `[PasswordSettings]` gewählt.
- [ ] `MaxNumberOfFailedLogin` gesetzt; Büroadressen bei Bedarf in `TrustedIPList[]`.
- [ ] Zweistufige Anmeldung: freiwillig oder vorgeschrieben; Administratoren zuerst eingerichtet.
- [ ] Verfahren des Helpdesks für verlorene Telefone.
- [ ] Schlüssel für die Geheimnisse der Authenticator-Apps sicher aufbewahrt und gesichert.
- [ ] Zeitlimit der Sitzungen entschieden.
- [ ] Netzwerkbeschränkung entschieden (nur Büro, VPN oder Internet mit Anmeldung).

## Verwandte Seiten

- [Zweistufige Anmeldung (Anleitung)](../../../anleitungen/zwei-faktor-anmeldung.md)
- [Zweistufige Anmeldung für Hoster](../../hosting/sicherheit/zweistufige-anmeldung.md)
- [Ihr Konto und die Anmeldung](../../konten-und-anmeldung.md)
- [Benutzerkonten und Passwörter](../sicherheit/benutzerkonten-und-passwoerter.md)
- [Verzeichnisanmeldung und LDAP](verzeichnisanmeldung-und-ldap.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
