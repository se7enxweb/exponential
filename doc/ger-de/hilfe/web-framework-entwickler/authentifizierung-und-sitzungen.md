---
title: Authentifizierung und Sitzungen
audience: web-framework-developer, developer
category: web-frameworks
order: 240
summary: Wie Exponential erkennt, wer anfragt, für Entwickler, die Symfony-Security-Firewalls, Laravel-Guards, Django-Auth und Devise kennen - Benutzer als Inhaltsobjekte, der anonyme Benutzer, Login-Handler, Sitzungen und ihre Handler, Anmeldepflicht pro Siteaccess, Anmeldung in zwei Schritten, API-Schlüssel und OAuth2 für Maschinen sowie der aktuelle Benutzer im Code.
keywords: Authentifizierung, Anmeldung, Sitzungen, Benutzer, anonymer Benutzer, Zwei-Faktor-Anmeldung, API-Schlüssel, authentication, login, sessions, user, eZUser, anonymous user, AnonymousUserID, LoginHandler, ezpSessionHandlerPHP, ezpSessionHandlerDB, SessionTimeout, RequireUserLogin, two-factor, 2FA, API keys, OAuth2, guard, firewall, Devise, Web-Framework-Entwickler, web framework developer
type: help
---
# Authentifizierung und Sitzungen

Diese Seite richtet sich an Entwickler, die eine Security-Firewall, Guards oder ein Authentifizierungs-Backend
konfigurieren. In Exponential sind Benutzer **Inhalte**: Jeder Benutzer ist ein Inhaltsobjekt der Benutzerklasse mit
einem `ezuser`-Feld, das Login, E-Mail-Adresse und Passwort-Hash enthält, und liegt in Benutzergruppen im
Inhaltsbaum. Die Authentifizierung übernehmen das Kernel-Modul `user` und die Login-Handler; die Sitzung hält den
angemeldeten Benutzer fest.

## Inhalt

1. [Benutzer und der anonyme Benutzer](#benutzer-und-der-anonyme-benutzer)
2. [Anmelden](#anmelden)
3. [Sitzungen](#sitzungen)
4. [Anmeldung erzwingen](#anmeldung-erzwingen)
5. [Maschinen: API-Schlüssel und OAuth2](#maschinen-api-schlüssel-und-oauth2)
6. [Der aktuelle Benutzer im Code](#der-aktuelle-benutzer-im-code)
7. [Im Vergleich mit anderen Frameworks](#im-vergleich-mit-anderen-frameworks)

## Benutzer und der anonyme Benutzer

| Konzept | In Exponential |
|---|---|
| Benutzermodell | ein Inhaltsobjekt der Benutzerklasse (`site.ini [UserSettings] UserClassID`, bei einer Standardinstallation 4) mit einem `ezuser`-Attribut; PHP-Klasse `eZUser` |
| Gruppen | Benutzergruppen-Objekte im Baum; Rollen werden Gruppen oder Benutzern zugewiesen |
| Gast | ein echter Benutzer, der anonyme Benutzer (`site.ini [UserSettings] AnonymousUserID`, bei einer Standardinstallation 10), mit einer eigenen Rolle |
| Profilfelder | Attribute der Benutzerklasse, die wie jeder andere Inhalt bearbeitet werden |

Weil Benutzer Inhalte sind, erhalten sie Versionen, Übersetzungen ihrer Profilfelder, Berechtigungen und Suche wie
jeder andere Inhalt.

## Anmelden

- Der View `user/login` meldet einen Benutzer an; `user/logout`, `user/register`, `user/forgotpassword` und
  `user/activate` tun, was ihr Name sagt.
- **Login-Handler** prüfen die Zugangsdaten: `site.ini [UserSettings] LoginHandler[]` (`standard` wird mitgeliefert;
  `LDAP` und `textfile` stehen als Alternativen bereit). Der erste Handler, der die Zugangsdaten akzeptiert, meldet
  den Benutzer an. Ein eigener Handler ist ein Erweiterungspunkt
  ([Login-Handler für Benutzer](../../bc/6.0/rad-erweiterungspunkte.md#login-handler-für-benutzer)).
- Die **Anmeldung in zwei Schritten** (Authenticator-App oder Code per E-Mail) stammt aus der Erweiterung
  `sevenx_authentication_2fa`, einem Login-Handler, der vor `standard` eingetragen wird
  ([Anmeldung in zwei Schritten](../../anleitungen/zwei-faktor-anmeldung.md)).
- Im Code prüft `eZUser::loginUser( $login, $password )` die Zugangsdaten und meldet den Benutzer an.

## Sitzungen

- Eine Sitzung hält einen Browser angemeldet. Sie endet `site.ini [Session] SessionTimeout` Sekunden nach ihrer
  letzten Nutzung (mitgeliefert: 259200, also drei Tage).
- Ein leeres `site.ini [Session] Handler` (die Voreinstellung) bedeutet `ezpSessionHandlerPHP`: die
  Sitzungsspeicherung von PHP selbst. `ezpSessionHandlerDB` speichert Sitzungen in der Tabelle `ezsession`, sodass
  **Setup > Sitzungen** sie auflisten und entfernen kann ([Sitzungen](../../anleitungen/sitzungen.md)). Ein Symfony-basierter
  Handler (`ezpSessionHandlerSymfony`) kommt zum Einsatz, wenn der Kernel innerhalb von Symfony läuft.
- Lesen und schreiben Sie Sitzungswerte über `eZHTTPTool::instance()` (`setSessionVariable()`, `sessionVariable()`,
  `hasSessionVariable()`), nicht direkt über `$_SESSION`.
- Sitzungscookies tragen das Präfix aus `[Session] SessionNamePrefix` im Namen.

## Anmeldung erzwingen

| Bedarf | Einstellung |
|---|---|
| Ein ganzer Siteaccess nur für angemeldete Benutzer (die Administrationsoberfläche, ein Intranet) | `site.ini [SiteAccessSettings] RequireUserLogin=true` für diesen Siteaccess |
| Views, die auf einem solchen Siteaccess ohne Anmeldung erreichbar sind | `[SiteAccessSettings] AnonymousAccessList[]` (`user/register`, `user/forgotpassword` ...) |
| Anmeldung vor einem Bereich einer öffentlichen Website | eine Request-Regel mit `Action=login` ([Middleware](middleware-und-request-regeln.md)) |
| Wer einen Siteaccess überhaupt benutzen darf | die Richtlinie `user/login`, eingeschränkt nach Siteaccess ([Autorisierung](autorisierung-rollen-und-richtlinien.md)) |

## Maschinen: API-Schlüssel und OAuth2

Maschinen melden sich nicht über ein Formular an. Die REST-API akzeptiert **persönliche API-Schlüssel**
(`Authorization: Bearer expk_...`) und **OAuth2**-Tokens (`POST /api/auth/v1/oauth/token`); expservices hat eigene
Tokens ([OAuth und API-Schlüssel](../ersteller/anwendungsentwickler/oauth-und-api-schluessel.md),
[Persönliche API-Schlüssel](../../anleitungen/api-schluessel.md)). Unabhängig von der Art der Zugangsdaten wird jeder Aufruf
gegen die Richtlinien des Benutzers geprüft.

## Der aktuelle Benutzer im Code

```php
$user = eZUser::currentUser();
if ( !$user->isRegistered() ) { /* anonymous */ }
$userID = eZUser::currentUserID();
```

Auf der Kommandozeile und in Workern ist der Benutzer anonym, bis der Code einen Benutzer anmeldet, zum Beispiel
einen eigenen Integrationsbenutzer mit `eZUser::setCurrentlyLoggedInUser( $user, $user->id() )`; niemals den
gemeinsam genutzten Administrator ([Zugriffskontrolle im Code](../ersteller/anwendungsentwickler/zugriffskontrolle-im-code.md)).
In Templates: `fetch( 'user', 'current_user' )`.

## Im Vergleich mit anderen Frameworks

| Framework | Konzept | Exponential |
|---|---|---|
| Symfony | User-Provider, Authenticators, Firewalls | die Benutzerklasse, Login-Handler, Anmelderegeln pro Siteaccess |
| Laravel | Guards, Provider, Sanctum-Tokens | Sitzungsanmeldung, Login-Handler, API-Schlüssel und OAuth2 für die REST-API |
| Django | `User`-Modell, Authentifizierungs-Backends | Benutzerobjekte, Login-Handler |
| Rails | Devise, `has_secure_password` | das Benutzermodul und Login-Handler, eingebaut |
| Spring | Filterkette von Spring Security | Login-Handler, Request-Regeln, Richtlinien |

## Verwandte Seiten

- [Konten und Anmeldung](../konten-und-anmeldung.md)
- [Mitgliederregistrierung und Mitgliederbereiche](../ersteller/website-ersteller/mitgliederregistrierung-und-mitgliederbereiche.md)
- [Sicherheit und Audit](../../anleitungen/sicherheit-und-audit.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
