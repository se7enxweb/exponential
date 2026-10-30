---
title: site.ini [PasskeySettings]
audience: developer, admin
category: examples
summary: "The settings of [PasskeySettings] in site.ini: Passkeys, SignInForm, RelyingPartyID, RelyingPartyName, AllowedOrigins, UserVerification, Algorithms, ChallengeSeconds ..."
keywords: site.ini, PasskeySettings, INI, setting, Passkeys, SignInForm, RelyingPartyID, RelyingPartyName, AllowedOrigins, UserVerification, Algorithms, ChallengeSeconds, MaxPerUser, Attestation, RelyingPartyIDMap, AuthenticatorNames, Policy, PasswordSignIn, GroupPolicy, GroupPasswordSignIn, UserPolicy, UserPasswordSignIn, PolicyHandlers, ExemptViews, ConsoleSlots
type: generated
generated: exp:help:examples
---
# site.ini [PasskeySettings]

The block `[PasskeySettings]` of `settings/site.ini`, with 21 setting(s).

Never edit `settings/site.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/site.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in an extension (read when the extension is active).

## Passkeys

> Passkeys (WebAuthn): a user registers passkeys on user/passkeys (My account > Passkeys)  
> and signs in with one on the web sign-in form, next to the password; a passkey also  
> answers "Verify it's you" (\[ReauthSettings\]). Only the web pages use passkeys: REST,  
> SOAP, WebDAV and the expservices login do not change.  
> Guide: doc/eng-us/guides/passkeys.md  
>
> enabled or disabled. Disabled: no passkey can be added or used; the stored ones stay.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
Passkeys=enabled
*/ ?>
```

## SignInForm

> Offer "Sign in with a passkey" on the sign-in form (and the browser's passkey autofill).  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
SignInForm=enabled
*/ ?>
```

## RelyingPartyID

> The relying party id: a host name, the passkeys are bound to it. Empty: the host name of  
> the request. A shared parent domain (example.com) lets the passkeys of www.example.com  
> work on admin.example.com too; it must be the host or a parent of it.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
RelyingPartyID=
*/ ?>
```

## RelyingPartyName

> The name the browser shows. Empty: \[SiteSettings\] SiteName.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
RelyingPartyName=
*/ ?>
```

## AllowedOrigins

> The origins (scheme://host\[:port\]) answers may come from. Empty: the origin of the  
> request. List every host the passkeys are used on when RelyingPartyID is a parent domain.  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
# an empty "AllowedOrigins[]" first replaces the shipped list; without it the items are added
AllowedOrigins[]
*/ ?>
```

## UserVerification

> required (default; the authenticator checks a PIN, fingerprint or face), preferred or  
> discouraged. Signing in with a passkey always requires it; this applies to re-authentication.  

Default: `required`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
UserVerification=required
*/ ?>
```

## Algorithms

> COSE algorithms offered, most preferred first: -7 ES256, -257 RS256, -8 Ed25519 (needs  
> PHP's sodium extension), -35 ES384, -36 ES512.  

Default: a list of 5: `-7`, `-257`, `-8`, `-35`, `-36`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
# an empty "Algorithms[]" first replaces the shipped list; without it the items are added
Algorithms[]
Algorithms[]=-7
Algorithms[]=-257
Algorithms[]=-8
Algorithms[]=-35
Algorithms[]=-36
*/ ?>
```

## ChallengeSeconds

> Seconds a challenge may be answered.  

Default: `300`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
ChallengeSeconds=300
*/ ?>
```

## MaxPerUser

> Passkeys one account may have.  

Default: `20`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
MaxPerUser=20
*/ ?>
```

## Attestation

> The attestation asked for when a passkey is added: none (default), indirect or direct.  
> With indirect every browser keeps the authenticator's model id (AAGUID), which names it in  
> the list ("iCloud Keychain", "YubiKey 5 Series"); the statement itself is never relied on.  

Default: `none`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
Attestation=none
*/ ?>
```

## RelyingPartyIDMap

> A multi-host installation: the relying party id of each host, RelyingPartyIDMap\[<host>\]=<id>.  
> The id must be the host or a parent domain of it; a host not listed uses RelyingPartyID.  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
# an empty "RelyingPartyIDMap[]" first replaces the shipped list; without it the items are added
RelyingPartyIDMap[]
*/ ?>
```

## AuthenticatorNames

> Names of authenticator models the built-in list lacks or names otherwise:  
> AuthenticatorNames\[<aaguid>\]=<name>  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
# an empty "AuthenticatorNames[]" first replaces the shipped list; without it the items are added
AuthenticatorNames[]
*/ ?>
```

## Policy

> The passkey policy (doc/eng-us/guides/passkeys.md, "Policy"). Only the web pages apply it;  
> REST, SOAP, WebDAV and the expservices login keep their own sign-in.  
> optional (default): users may add passkeys. preferred: a user without a passkey is asked  
> once per session to add one and may go on. required: a user without a passkey adds one  
> before any other page.  

Default: `optional`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
Policy=optional
*/ ?>
```

## PasswordSignIn

> enabled (default) or disabled: disabled refuses the password on the sign-in form for an  
> account that has a passkey (answered like a wrong password). An account without a passkey  
> still signs in with its password, so nobody is locked out.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
PasswordSignIn=enabled
*/ ?>
```

## GroupPolicy

> Per user group (content object id): the strictest of the user's groups and Policy wins;  
> disabled in any group wins. GroupPolicy\[13\]=required, GroupPasswordSignIn\[13\]=disabled  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
# an empty "GroupPolicy[]" first replaces the shipped list; without it the items are added
GroupPolicy[]
*/ ?>
```

## GroupPasswordSignIn

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
# an empty "GroupPasswordSignIn[]" first replaces the shipped list; without it the items are added
GroupPasswordSignIn[]
*/ ?>
```

## UserPolicy

> Per account (content object id), winning over the groups.  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
# an empty "UserPolicy[]" first replaces the shipped list; without it the items are added
UserPolicy[]
*/ ?>
```

## UserPasswordSignIn

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
# an empty "UserPasswordSignIn[]" first replaces the shipped list; without it the items are added
UserPasswordSignIn[]
*/ ?>
```

## PolicyHandlers

> Classes implementing expPasskeyPolicyHandler: extensions change the policy of an account.  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
# an empty "PolicyHandlers[]" first replaces the shipped list; without it the items are added
PolicyHandlers[]
*/ ?>
```

## ExemptViews

> The views that stay open while a passkey is required ("module" or "module/view").  

Default: a list of 7: `user/passkeys`, `user/logout`, `user/login`, `user/password`, `user2fa`, `ezjscore`, `user/preferences`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
# an empty "ExemptViews[]" first replaces the shipped list; without it the items are added
ExemptViews[]
ExemptViews[]=user/passkeys
ExemptViews[]=user/logout
ExemptViews[]=user/login
ExemptViews[]=user/password
ExemptViews[]=user2fa
ExemptViews[]=ezjscore
ExemptViews[]=user/preferences
*/ ?>
```

## ConsoleSlots

> Templates added below the list of passkeys on user/passkeys (an extension's own section):  
> ConsoleSlots\[\]=design:myextension/passkeys\_slot.tpl  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
# an empty "ConsoleSlots[]" first replaces the shipped list; without it the items are added
ConsoleSlots[]
*/ ?>
```

## Related

- [All blocks of site.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/site.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
