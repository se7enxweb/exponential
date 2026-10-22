---
title: Single Sign-on und Social Login
audience: integrator, developer, admin
category: integration
order: 230
summary: Was Exponential für Single Sign-on bietet - den Hook SingleSignOnHandlerArray, Social Login als OAuth-2.0-Client in sevenx_authentication_2fa, einen SAML- oder OpenID-Connect-Proxy vor der Website - und was es nicht bietet, mit einem ausgearbeiteten SSO-Handler auf Basis eines Headers und seinen Sicherheitsbedingungen.
keywords: Single Sign-on, SSO, Social Login, Anmeldung mit sozialen Konten, Identitätsanbieter, vertrauenswürdiger Header, single sign-on, SingleSignOnHandlerArray, SSOHandler, handleSSOLogin, social login, Google, Facebook, ID.me, OAuth client, PKCE, OpenID Connect, SAML, identity-aware proxy, trusted header, sevenx_authentication_2fa
type: help
---
# Single Sign-on und Social Login

Diese Seite richtet sich an Integratoren, die Benutzer sich "einmal anmelden" lassen sollen, mit einem
Identitätsanbieter des Unternehmens oder mit einem sozialen Konto. Exponential hat einen Hook für Single Sign-on und
eine Erweiterung für Social Login; es hat **keine SAML-Umsetzung und keinen OpenID-Connect-Anbieter**. Diese Seite
erklärt, was vorhanden ist, die übliche Architektur, die die Lücke füllt, und wie Sie sie sicher bauen.

## Inhalt

1. [Was es gibt](#was-es-gibt)
2. [Der Hook für Single Sign-on](#der-hook-für-single-sign-on)
3. [Beispiel: ein SSO-Handler hinter einem identitätsbewussten Proxy](#beispiel-ein-sso-handler-hinter-einem-identitätsbewussten-proxy)
4. [Social Login](#social-login)
5. [Einen Ansatz wählen](#einen-ansatz-wählen)

## Was es gibt

| Bedarf | Vorhanden | Wo |
|---|---|---|
| Anmeldung mit einem Verzeichnispasswort | Ja | Login-Handler: LDAP, Textdatei, ein eigener ([Seite](login-handler-ldap-und-textdatei.md)) |
| Einen bereits angemeldeten Benutzer ohne Anmeldeformular erkennen | Ein Hook | `site.ini [UserSettings] SingleSignOnHandlerArray[]` |
| Anmeldung mit Google, Facebook, Meta, Instagram, Twitter, ID.me | Ja, als OAuth-2.0-Client mit `state` und PKCE | `sevenx_authentication_2fa` (`login_handler/`) |
| SAML-2.0-Service-Provider | Nein | Ein identitätsbewusster Proxy davor oder eigene Entwicklung |
| OpenID-Connect-Client mit Prüfung des ID-Tokens | Nein (in den sozialen Handlern nur Userinfo) | Proxy oder eigene Entwicklung |
| Exponential als Identitätsanbieter für andere Anwendungen | Nur als OAuth-2.0-Autorisierungsserver für seine REST-API | [OAuth-Anwendungen](oauth-anwendungen-fuer-integrationen.md) |

## Der Hook für Single Sign-on

Bei jeder Anfrage eines **anonymen** Besuchers, der sich nicht gerade abgemeldet hat, fragt `eZUser::currentUser()`
jeden Handler, der in `[UserSettings] SingleSignOnHandlerArray[]` aufgeführt ist. Für einen Namen `<name>` erzeugt er
die Klasse `eZ<name>SSOHandler` (über die Autoloads gefunden) und ruft `handleSSOLogin()` auf. Ein Handler gibt einen
`eZUser` zurück, um diesen Benutzer anzumelden, oder `false`. Wird ein Benutzer zurückgegeben, meldet der Kernel die
Sitzung an, aktualisiert den letzten Besuch und leitet auf dieselbe Adresse um
(`kernel/classes/datatypes/ezuser/ezuser.php`).

Der Kernel liefert keinen Handler mit; der Hook ist für Ihren.

## Beispiel: ein SSO-Handler hinter einem identitätsbewussten Proxy

Eine übliche Architektur: Ein Reverse Proxy (oder ein Webserver-Modul) spricht SAML oder OpenID Connect mit dem
Identitätsanbieter und übergibt den authentifizierten Login in einem Anfrage-Header oder einer Servervariable an
Exponential. Der Handler ordnet ihn einem lokalen Benutzer zu.

```ini
# extension/myintegration/settings/site.ini.append.php
[UserSettings]
SingleSignOnHandlerArray[]=myproxy
```

```php
<?php
// extension/myintegration/classes/ezmyproxyssohandler.php
class eZmyproxySSOHandler
{
    /** @return eZUser|false */
    public function handleSSOLogin()
    {
        // set by the proxy; the web server must strip this header from every request that does not come through it
        $login = isset( $_SERVER['HTTP_X_AUTH_USER'] ) ? trim( (string)$_SERVER['HTTP_X_AUTH_USER'] ) : '';
        if ( $login === '' || !preg_match( '/^[A-Za-z0-9._@-]{1,150}$/', $login ) )
        {
            return false;
        }
        $user = eZUser::fetchByName( $login );
        if ( !$user instanceof eZUser || !$user->isEnabled() )
        {
            return false;   // provision users with a job of your own; never create administrators here
        }
        return $user;
    }
}
```

Erzeugen Sie die Autoloads neu (`php bin/php/ezpgenerateautoloads.php -e`), damit `class_exists()` die Klasse findet.

Bedingungen, die das sicher machen:

- [ ] Der Header wird **nur** vom Proxy gesetzt, und der Proxy entfernt jeden eingehenden Header gleichen Namens.
- [ ] Der Anwendungsserver nimmt Anfragen nur vom Proxy an (Firewall, Bind-Adresse); sonst könnte jeder den Header
  senden. Konfigurieren Sie vertrauenswürdige Proxys wie unter
  [Vertrauenswürdige Proxys](../../bc/6.0/vertrauenswuerdige-proxys.md) beschrieben.
- [ ] Administratoren melden sich über einen separaten Siteaccess an, der den Handler nicht verwendet, mit
  zweistufiger Anmeldung.
- [ ] Die Abmeldung wird auch am Proxy behandelt, sonst meldet die nächste Anfrage den Benutzer wieder an.
- [ ] Getestet: Eine Anfrage ohne den Header ist anonym; eine Anfrage mit einem unbekannten Login ist anonym.

## Social Login

`sevenx_authentication_2fa` (erforderlich als `~1.0.14`) ergänzt auf den Anmeldeseiten Schaltflächen
**Continue with ...** für jeden Anbieter, der in `sevenxauthentication2fa.ini` aktiviert ist. Es ist ein
OAuth-2.0-Client mit dem Authorization-Code-Grant, einem einmaligen, an den Anbieter gebundenen `state`
(600 Sekunden), PKCE (`S256`), wo konfiguriert, und es lehnt E-Mail-Adressen ab, die der Anbieter als nicht bestätigt
kennzeichnet. Ein so gefundener Benutzer durchläuft trotzdem seinen eigenen zweiten Schritt. Die Schritte je Anbieter
stehen in der `INSTALL.md` der Erweiterung; die Übersicht ist
[Zweistufige Anmeldung, Social Login](../../anleitungen/zwei-faktor-anmeldung.md#social-login).

Grenzen: ID-Tokens werden nicht geprüft (die Identität stammt aus der Userinfo-Antwort des Anbieters), und es gibt
keine OpenID-Connect-Discovery; Endpunkte werden je Anbieter konfiguriert.

## Einen Ansatz wählen

| Situation | Ansatz |
|---|---|
| Mitarbeiter mit einem Verzeichnis, ein auf der Website eingegebenes Passwort ist akzeptabel | LDAP-Login-Handler |
| SSO des Unternehmens (SAML, Entra ID, Okta, Keycloak) verlangt, kein Passwort auf der Website | Identitätsbewusster Proxy plus SSO-Handler |
| Öffentliche Mitglieder mit sozialen Konten | Social Login von `sevenx_authentication_2fa` |
| Partnerportal, in dem eine andere Anwendung für Benutzer handelt | OAuth-Anwendung von Exponential |
| Anbieter mit einer API zur Passwortprüfung | Ein eigener Login-Handler |

Gleich welcher Ansatz: Das Anlegen von Benutzern samt Gruppenzuweisung und das Deaktivieren ausscheidender Benutzer
gehören zur Integration; schätzen Sie sie gesondert.

## Verwandte Seiten

- [Login-Handler - LDAP, Textdatei und eigene](login-handler-ldap-und-textdatei.md)
- [Zweistufige Anmeldung](../../anleitungen/zwei-faktor-anmeldung.md)
- [Standards für APIs und Protokolle, OpenID Connect](../../standards/apis-und-protokolle.md#openid-connect-und-oauth-20-als-client-social-login)
- [FAQ zu Integrationen für öffentliche Stellen](../verwaltung/faq/faq-integrationen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
