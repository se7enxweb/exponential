---
title: rest.ini [OAuthSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [OAuthSettings] in rest.ini: TokenTTL, RefreshTokenTTL, AllowTokenInQuery, AllowTokenInBody, AuthorizeRequiresPolicy, RequirePkceForPublicClients, RequirePkceForConfidentialClients, AllowPlainPkce"
keywords: Einstellung, rest.ini, OAuthSettings, INI, setting, TokenTTL, RefreshTokenTTL, AllowTokenInQuery, AllowTokenInBody, AuthorizeRequiresPolicy, RequirePkceForPublicClients, RequirePkceForConfidentialClients, AllowPlainPkce
type: generated
generated: exp:help:examples
---
# rest.ini [OAuthSettings]

Der Block `[OAuthSettings]` von `settings/rest.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/rest.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/rest.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/rest.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/rest.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## TokenTTL

> Lebensdauer von OAuth-Token/Autorisierungscode, in Sekunden  
> Standard ist eine Stunde  

Standard: `3600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[OAuthSettings]
TokenTTL=3600
*/ ?>
```

## RefreshTokenTTL

> Lebensdauer eines Refresh-Tokens, in Sekunden; 0 = Refresh-Tokens laufen nicht ab (wie in früheren Versionen).  
> Standard sind 30 Tage. Jede Erneuerung stellt ein neues Refresh-Token aus, die Lebensdauer zählt also ab der letzten  
> Erneuerung. Die Ausstellungszeit wird aus dem Ablauf des Zugriffstokens und TokenTTL abgeleitet: Eine Änderung von TokenTTL  
> verschiebt die Frist früher ausgestellter Refresh-Tokens um die Differenz.  

Standard: `2592000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[OAuthSettings]
RefreshTokenTTL=2592000
*/ ?>
```

## AllowTokenInQuery

> Woher ein Zugriffstoken genommen wird. Der Header Authorization ("Bearer <token>" oder "OAuth <token>")  
> wird immer gelesen und ist der empfohlene Weg.  
> AllowTokenInQuery: enabled oder disabled (der Standard). enabled: Der Query-Parameter oauth\_token  
> (?oauth\_token=...) wird ebenfalls angenommen, wie in früheren Versionen. Ein Token in der Adresse landet in  
> Zugriffslogs, im Browserverlauf und in Referer-Headern (RFC 6750, Abschnitt 2.3); schalten Sie es nur für  
> alte Clients ein, die den Header nicht senden können.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[OAuthSettings]
AllowTokenInQuery=disabled
*/ ?>
```

## AllowTokenInBody

> AllowTokenInBody: enabled (der Standard) oder disabled. enabled: Der Parameter oauth\_token eines POST-Formulars  
> wird angenommen, wenn es keinen Header Authorization gibt (RFC 6750, Abschnitt 2.2).  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[OAuthSettings]
AllowTokenInBody=enabled
*/ ?>
```

## AuthorizeRequiresPolicy

> enabled oder disabled. Enabled (der Standard): Ein angemeldeter Benutzer darf einen OAuth-Client unter oauth/authorize nur  
> mit der Richtlinie oauth/authorize (optional nach Siteaccess eingeschränkt) autorisieren; ohne sie erhält der Client error=access\_denied.  
> disabled: Jeder angemeldete Benutzer darf Clients autorisieren, wie vor 6.0.15. Nutzen Sie es nur kurz während eines  
> Upgrades, bis "php bin/php/addoauthauthorizepolicy.php --apply" die Richtlinie gewährt hat (doc/eng-us/bc/6.0/oauth-authorize-policy.md).  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[OAuthSettings]
AuthorizeRequiresPolicy=enabled
*/ ?>
```

## RequirePkceForPublicClients

> PKCE (RFC 7636) für den Authorization-Code-Flow. Ein Client sendet code\_challenge und code\_challenge\_method an  
> oauth/authorize und den passenden code\_verifier an den Token-Endpunkt (/api/oauth/token); ohne ihn wird der Code  
> dort abgelehnt. doc/eng-us/bc/6.0/oauth-pkce.md  
> enabled (der Standard) oder disabled. enabled: Ein öffentlicher Client (einer, der ohne Client-Secret gespeichert ist, etwa eine mobile  
> oder Browser-Anwendung) muss PKCE verwenden; oauth/authorize antwortet ohne code\_challenge mit error=invalid\_request.  
> Ein öffentlicher Client authentifiziert sich allein mit seiner client\_id; ohne PKCE kann also jeder, der seinen Code sieht, ihn einlösen.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[OAuthSettings]
RequirePkceForPublicClients=enabled
*/ ?>
```

## RequirePkceForConfidentialClients

> enabled oder disabled (der Standard). enabled: Jeder Client muss PKCE verwenden, auch einer mit einem Client-Secret.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[OAuthSettings]
RequirePkceForConfidentialClients=disabled
*/ ?>
```

## AllowPlainPkce

> enabled oder disabled (der Standard). Die Challenge-Methode "plain" sendet den Verifier selbst und schützt nicht  
> gegen einen Angreifer, der die Autorisierungsanfrage lesen kann; nur S256 wird akzeptiert, solange dies nicht aktiviert ist.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[OAuthSettings]
AllowPlainPkce=disabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von rest.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/rest.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
