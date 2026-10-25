---
title: site.ini [PasskeySettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [PasskeySettings] in site.ini: Passkeys, SignInForm, RelyingPartyID, RelyingPartyName, AllowedOrigins, UserVerification, Algorithms, ChallengeSeconds ..."
keywords: Einstellung, site.ini, PasskeySettings, INI, setting, Passkeys, SignInForm, RelyingPartyID, RelyingPartyName, AllowedOrigins, UserVerification, Algorithms, ChallengeSeconds, MaxPerUser
type: generated
generated: exp:help:examples
---
# site.ini [PasskeySettings]

Der Block `[PasskeySettings]` von `settings/site.ini`, mit 9 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Passkeys

> Passkeys (WebAuthn): a user registers passkeys on user/passkeys (My account > Passkeys)  
> and signs in with one on the web sign-in form, next to the password; a passkey also  
> answers "Verify it's you" (\[ReauthSettings\]). Only the web pages use passkeys: REST,  
> SOAP, WebDAV and the expservices login do not change.  
> Guide: doc/eng-us/guides/passkeys.md  
>
> enabled or disabled. Disabled: no passkey can be added or used; the stored ones stay.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
Passkeys=enabled
*/ ?>
```

## SignInForm

> Offer "Sign in with a passkey" on the sign-in form (and the browser's passkey autofill).  

Standard: `enabled`.

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

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
RelyingPartyID=
*/ ?>
```

## RelyingPartyName

> The name the browser shows. Empty: \[SiteSettings\] SiteName.  

Standard: leer.

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

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
# ein leeres "AllowedOrigins[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AllowedOrigins[]
*/ ?>
```

## UserVerification

> required (default; the authenticator checks a PIN, fingerprint or face), preferred or  
> discouraged. Signing in with a passkey always requires it; this applies to re-authentication.  

Standard: `required`.

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

Standard: eine Liste mit 5: `-7`, `-257`, `-8`, `-35`, `-36`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
# ein leeres "Algorithms[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
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

Standard: `300`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
ChallengeSeconds=300
*/ ?>
```

## MaxPerUser

> Passkeys one account may have.  

Standard: `20`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
MaxPerUser=20
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
