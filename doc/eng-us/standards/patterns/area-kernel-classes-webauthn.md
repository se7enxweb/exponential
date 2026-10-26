---
title: Patterns in kernel/classes/webauthn
audience: developer
category: standards
order: 523
summary: Every class, view, fetch function, operator and plugin point of kernel/classes/webauthn with its design patterns.
keywords: kernel/classes/webauthn, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/classes/webauthn

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/classes/webauthn` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (4)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expWebAuthn` | class | [Utility (static helper)](utility.md) | `kernel/classes/webauthn/expwebauthn.php:34` | The server side of WebAuthn (W3C Web Authentication, Level 2/3) for passkeys: the options a browser is given, and the checks of what it sends back wh… |
| `expWebAuthnCbor` | class | [Utility (static helper)](utility.md) | `kernel/classes/webauthn/expwebauthncbor.php:20` | The part of CBOR (RFC 8949) that WebAuthn needs: decoding the attestation object of a new passkey and the COSE public key inside its authenticator da… |
| `expWebAuthnChallenge` | class | [Utility (static helper)](utility.md) | `kernel/classes/webauthn/expwebauthnchallenge.php:19` | The WebAuthn challenges a session was given: each request for options gets a new random challenge, kept on the server (in the session) with what it i… |
| `expWebAuthnCose` | class | [Utility (static helper)](utility.md) | `kernel/classes/webauthn/expwebauthncose.php:26` | The public key of a passkey as COSE (RFC 9052/9053) and the check of a signature made with it. |
