---
title: rest.ini [OAuthSettings]
audience: developer, admin
category: examples
summary: "The settings of [OAuthSettings] in rest.ini: TokenTTL, RefreshTokenTTL, AllowTokenInQuery, AllowTokenInBody, AuthorizeRequiresPolicy, RequirePkceForPublicClients, RequirePkceForConfidentialClients, AllowPlainPkce"
keywords: rest.ini, OAuthSettings, INI, setting, TokenTTL, RefreshTokenTTL, AllowTokenInQuery, AllowTokenInBody, AuthorizeRequiresPolicy, RequirePkceForPublicClients, RequirePkceForConfidentialClients, AllowPlainPkce
type: generated
generated: exp:help:examples
---
# rest.ini [OAuthSettings]

The block `[OAuthSettings]` of `settings/rest.ini`, with 8 setting(s).

Never edit `settings/rest.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/rest.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/rest.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/rest.ini.append.php` in an extension (read when the extension is active).

## TokenTTL

> OAuth token/authcode time to live, in seconds  
> Default is one hour  

Default: `3600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[OAuthSettings]
TokenTTL=3600
*/ ?>
```

## RefreshTokenTTL

> Refresh token time to live, in seconds; 0 = refresh tokens do not expire (as in earlier versions).  
> Default is 30 days. Every refresh issues a new refresh token, so the lifetime counts from the last  
> refresh. The issue time is derived from the access token's expiry and TokenTTL: changing TokenTTL  
> shifts the deadline of refresh tokens issued before by the difference.  

Default: `2592000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[OAuthSettings]
RefreshTokenTTL=2592000
*/ ?>
```

## AllowTokenInQuery

> Where an access token is taken from. The Authorization header ("Bearer <token>" or "OAuth <token>")  
> is always read and is the recommended way.  
> AllowTokenInQuery: enabled or disabled (the default). Enabled: the oauth\_token query parameter  
> (?oauth\_token=...) is accepted as well, as in earlier versions. A token in the address ends up in  
> access logs, the browser history and Referer headers (RFC 6750, section 2.3); enable it only for  
> old clients that cannot send the header.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[OAuthSettings]
AllowTokenInQuery=disabled
*/ ?>
```

## AllowTokenInBody

> AllowTokenInBody: enabled (the default) or disabled. Enabled: the oauth\_token parameter of a POST  
> form body is accepted when there is no Authorization header (RFC 6750, section 2.2).  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[OAuthSettings]
AllowTokenInBody=enabled
*/ ?>
```

## AuthorizeRequiresPolicy

> enabled or disabled. Enabled (the default): a signed in user may authorize an OAuth client at oauth/authorize only  
> with the policy oauth/authorize (optionally limited by siteaccess); without it the client gets error=access\_denied.  
> disabled: every signed in user may authorize clients, as before 6.0.15. Use it only for a short time during an  
> upgrade, until "php bin/php/addoauthauthorizepolicy.php" has granted the policy (doc/eng-us/bc/6.0/oauth-authorize-policy.md).  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[OAuthSettings]
AuthorizeRequiresPolicy=enabled
*/ ?>
```

## RequirePkceForPublicClients

> PKCE (RFC 7636) for the authorization code flow. A client sends code\_challenge and code\_challenge\_method to  
> oauth/authorize and the matching code\_verifier to the token end point (/api/oauth/token); the code is refused  
> there without it. doc/eng-us/bc/6.0/oauth-pkce.md  
> enabled (the default) or disabled. Enabled: a public client (one stored without a client secret, such as a mobile  
> or browser application) must use PKCE; oauth/authorize answers error=invalid\_request without a code\_challenge.  
> A public client authenticates with its client\_id alone, so without PKCE anyone who sees its code can redeem it.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[OAuthSettings]
RequirePkceForPublicClients=enabled
*/ ?>
```

## RequirePkceForConfidentialClients

> enabled or disabled (the default). Enabled: every client must use PKCE, also one with a client secret.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[OAuthSettings]
RequirePkceForConfidentialClients=disabled
*/ ?>
```

## AllowPlainPkce

> enabled or disabled (the default). The challenge method "plain" sends the verifier itself and protects nothing  
> against an attacker who can read the authorization request; only S256 is accepted unless this is enabled.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[OAuthSettings]
AllowPlainPkce=disabled
*/ ?>
```

## Related

- [All blocks of rest.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/rest.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
