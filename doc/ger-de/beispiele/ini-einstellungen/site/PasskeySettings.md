---
title: site.ini [PasskeySettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [PasskeySettings] in site.ini: Passkeys, SignInForm, RelyingPartyID, RelyingPartyName, AllowedOrigins, UserVerification, Algorithms, ChallengeSeconds ..."
keywords: Einstellung, site.ini, PasskeySettings, INI, setting, Passkeys, SignInForm, RelyingPartyID, RelyingPartyName, AllowedOrigins, UserVerification, Algorithms, ChallengeSeconds, MaxPerUser, Attestation, RelyingPartyIDMap, AuthenticatorNames, Policy, PasswordSignIn, GroupPolicy, GroupPasswordSignIn, UserPolicy, UserPasswordSignIn, PolicyHandlers, ExemptViews, ConsoleSlots
type: generated
generated: exp:help:examples
---
# site.ini [PasskeySettings]

Der Block `[PasskeySettings]` von `settings/site.ini`, mit 21 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Passkeys

> Passkeys (WebAuthn): ein Benutzer registriert Passkeys unter user/passkeys (Mein Konto > Passkeys)  
> und meldet sich mit einem im Web-Anmeldeformular an, neben dem Passwort; ein Passkey  
> beantwortet auch "Bestätigen Sie, dass Sie es sind" (\[ReauthSettings\]). Nur die Webseiten nutzen Passkeys: REST,  
> SOAP, WebDAV und die expservices-Anmeldung ändern sich nicht.  
> Anleitung: doc/eng-us/guides/passkeys.md  
>
> enabled oder disabled. Disabled: kein Passkey kann hinzugefügt oder verwendet werden; die gespeicherten bleiben.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
Passkeys=enabled
*/ ?>
```

## SignInForm

> "Mit einem Passkey anmelden" im Anmeldeformular anbieten (und die Passkey-Autofill-Funktion des Browsers).  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
SignInForm=enabled
*/ ?>
```

## RelyingPartyID

> Die Relying-Party-ID: ein Hostname, an den die Passkeys gebunden sind. Leer: der Hostname der  
> Anfrage. Eine gemeinsame übergeordnete Domain (example.com) lässt die Passkeys von www.example.com  
> auch auf admin.example.com funktionieren; sie muss der Host oder eine übergeordnete Domain davon sein.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
RelyingPartyID=
*/ ?>
```

## RelyingPartyName

> Der Name, den der Browser anzeigt. Leer: \[SiteSettings\] SiteName.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
RelyingPartyName=
*/ ?>
```

## AllowedOrigins

> Die Origins (scheme://host\[:port\]), von denen Antworten kommen dürfen. Leer: der Origin der  
> Anfrage. Alle Hosts auflisten, auf denen die Passkeys genutzt werden, wenn RelyingPartyID eine übergeordnete Domain ist.  

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

> required (Standard; der Authenticator prüft eine PIN, einen Fingerabdruck oder ein Gesicht), preferred oder  
> discouraged. Die Anmeldung mit einem Passkey verlangt es immer; dies gilt für die erneute Authentifizierung.  

Standard: `required`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
UserVerification=required
*/ ?>
```

## Algorithms

> Angebotene COSE-Algorithmen, der bevorzugte zuerst: -7 ES256, -257 RS256, -8 Ed25519 (braucht  
> die sodium-Erweiterung von PHP), -35 ES384, -36 ES512.  

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

> Sekunden, in denen eine Challenge beantwortet werden darf.  

Standard: `300`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
ChallengeSeconds=300
*/ ?>
```

## MaxPerUser

> Passkeys, die ein Konto haben darf.  

Standard: `20`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
MaxPerUser=20
*/ ?>
```

## Attestation

> Die Attestierung, um die beim Hinzufügen eines Passkeys gebeten wird: none (Standard), indirect oder direct.  
> Mit indirect behält jeder Browser die Modellkennung des Authentifikators (AAGUID), die ihn in  
> der Liste benennt („iCloud Keychain“, „YubiKey 5 Series“); der Nachweis selbst wird nie herangezogen.  

Standard: `none`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
Attestation=none
*/ ?>
```

## RelyingPartyIDMap

> Eine Installation mit mehreren Hosts: die Relying-Party-ID je Host, RelyingPartyIDMap\[<host>\]=<id>.  
> Die ID muss der Host oder eine übergeordnete Domain davon sein; ein nicht genannter Host benutzt RelyingPartyID.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
# ein leeres "RelyingPartyIDMap[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RelyingPartyIDMap[]
*/ ?>
```

## AuthenticatorNames

> Namen von Authentifikatormodellen, die die eingebaute Liste nicht kennt oder anders nennt:  
> AuthenticatorNames\[<aaguid>\]=<Name>  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
# ein leeres "AuthenticatorNames[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AuthenticatorNames[]
*/ ?>
```

## Policy

> Die Passkey-Richtlinie (doc/ger-de/anleitungen/passkeys.md, „Richtlinie“). Nur die Webseiten wenden sie an;  
> REST, SOAP, WebDAV und die expservices-Anmeldung behalten ihre eigene Anmeldung.  
> optional (Standard): Benutzer dürfen Passkeys hinzufügen. preferred: Ein Benutzer ohne Passkey wird  
> einmal je Sitzung gebeten, einen hinzuzufügen, und darf weitermachen. required: Ein Benutzer ohne Passkey fügt  
> vor jeder anderen Seite einen hinzu.  

Standard: `optional`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
Policy=optional
*/ ?>
```

## PasswordSignIn

> enabled (Standard) oder disabled: disabled lehnt im Anmeldeformular das Passwort eines  
> Kontos ab, das einen Passkey hat (beantwortet wie ein falsches Passwort). Ein Konto ohne Passkey  
> meldet sich weiter mit seinem Passwort an, niemand wird ausgesperrt.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
PasswordSignIn=enabled
*/ ?>
```

## GroupPolicy

> Je Benutzergruppe (Content-Objekt-ID): Die strengste Angabe der Gruppen des Benutzers und Policy gilt;  
> disabled in irgendeiner Gruppe gilt. GroupPolicy\[13\]=required, GroupPasswordSignIn\[13\]=disabled  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
# ein leeres "GroupPolicy[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
GroupPolicy[]
*/ ?>
```

## GroupPasswordSignIn

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
# ein leeres "GroupPasswordSignIn[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
GroupPasswordSignIn[]
*/ ?>
```

## UserPolicy

> Je Konto (Content-Objekt-ID), vor den Gruppen.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
# ein leeres "UserPolicy[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
UserPolicy[]
*/ ?>
```

## UserPasswordSignIn

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
# ein leeres "UserPasswordSignIn[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
UserPasswordSignIn[]
*/ ?>
```

## PolicyHandlers

> Klassen, die expPasskeyPolicyHandler umsetzen: Erweiterungen ändern die Richtlinie eines Kontos.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
# ein leeres "PolicyHandlers[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyHandlers[]
*/ ?>
```

## ExemptViews

> Die Ansichten, die offen bleiben, solange ein Passkey verlangt ist („modul“ oder „modul/ansicht“).  

Standard: eine Liste mit 7: `user/passkeys`, `user/logout`, `user/login`, `user/password`, `user2fa`, `ezjscore`, `user/preferences`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
# ein leeres "ExemptViews[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
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

> Templates unter der Liste der Passkeys auf user/passkeys (ein eigener Abschnitt einer Erweiterung):  
> ConsoleSlots\[\]=design:myextension/passkeys\_slot.tpl  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasskeySettings]
# ein leeres "ConsoleSlots[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ConsoleSlots[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
