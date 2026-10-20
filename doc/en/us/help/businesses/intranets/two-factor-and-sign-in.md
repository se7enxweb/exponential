---
title: Two-factor and sign-in
audience: businesses, admin, hosting
category: business-solutions
order: 70
summary: What an organization decides about how staff sign in to an Exponential intranet - password rules, lockout after failed sign-ins, password expiry, two-step sign-in with sevenx_authentication_2fa, social login, sessions and the sign-in records.
keywords: sign-in, login, two-factor, 2FA, two-step sign-in, TOTP, authenticator, e-mail code, sevenx_authentication_2fa, Enforce2FA, social login, password rules, MaxNumberOfFailedLogin, ezmbpaex, password expiry, sessions, auth.log
type: help
---
# Two-factor and sign-in

This page is for intranet owners and IT managers who decide how staff prove who they are. A stolen password is the
most common way into a staff portal; this page lists the protections Exponential offers, which are on by default and
which you switch on, and links to the guides for the work. It is an overview, not legal or audit advice.

## Contents

1. [The layers](#the-layers)
2. [Password rules](#password-rules)
3. [Locking an account after failed sign-ins](#locking-an-account-after-failed-sign-ins)
4. [Two-step sign-in](#two-step-sign-in)
5. [Social login](#social-login)
6. [Password expiry](#password-expiry)
7. [Sessions and signing out](#sessions-and-signing-out)
8. [Records of sign-ins](#records-of-sign-ins)
9. [Decisions for the intranet](#decisions-for-the-intranet)
10. [Related pages](#related-pages)

## The layers

| Layer | On by default | Where it is configured | Guide |
|---|---|---|---|
| Minimum password length 10 and a weak password check | yes (`MinPasswordLength=10`, `WeakPasswordCheck=enabled` in `settings/site.ini`) | `[UserSettings]` | [Changing your password](../../../features/6.0/modern-password-change.md) |
| Further password rules (upper case, digit, symbol ...) | no, each rule is off by default | `site.ini [PasswordSettings]` | same |
| Other sessions signed out after a password change | yes (`EndOtherSessions=enabled`) | `[PasswordSettings]` | same |
| Lock after N failed sign-ins | no (`MaxNumberOfFailedLogin=0`) | `[UserSettings]` | [Security hardening](../../security-hardening.md#brute-force-and-credential-stuffing-on-the-sign-in) |
| Two-step sign-in | extension, opt-in | `sevenxauthentication2fa.ini` | [Two-step sign-in](../../../guides/two-factor-login.md) |
| Password expiry | extension `ezmbpaex` | its own settings | [ezmbpaex](../../../features/6.0/extensions/ezmbpaex.md) |
| Network limits | web server | Apache, nginx, a VPN | [Restricting the administration](../../hosting/security/restricting-the-administration.md#network-restrictions) |

## Password rules

Since 6.0.15 the same rules apply to every form that sets a password: registration, editing a user account, the
password change page and the console command `bin/php/resetuserpassword.php`. A password change sends the user a
"Your password was changed" mail without the password, and signs out the user's other sessions on their next request
([Changing your password](../../../features/6.0/modern-password-change.md#in-short)).

`site.ini [PasswordSettings]` adds rules, all off by default so an upgraded site keeps the rules it had:
`RequireLowercase`, `RequireUppercase`, `RequireDigit`, `RequireSymbol`, `MinCharacterClasses`, `ForbidLogin`,
`ForbidCurrentPassword`. Choose them with your security policy; longer passwords usually help staff more than
complicated ones.

## Locking an account after failed sign-ins

`site.ini [UserSettings] MaxNumberOfFailedLogin` locks an account after that many failed attempts. It ships as `0`,
which means off. `TrustedIPList[]` can exempt office addresses. Locked accounts are listed and unlocked in bulk on
**Users > Check user accounts** ([Check user accounts](../../../features/6.0/user-hygiene.md#the-filters)).

> **Warning**
> On an intranet reachable from the internet, set `MaxNumberOfFailedLogin` to a value that fits your policy. The
> sign-in page is the most attacked address of any site
> ([Restricting the administration](../../hosting/security/restricting-the-administration.md)).

## Two-step sign-in

The extension `sevenx_authentication_2fa` (listed in `composer.json`) adds a second step after the password: a
6-digit code from an authenticator app (TOTP, RFC 6238) or a code sent by e-mail
([sevenx_authentication_2fa](../../../features/6.0/extensions/sevenx_authentication_2fa.md)).

What staff see ([Two-step sign-in](../../../guides/two-factor-login.md#what-a-user-sees)):

1. Sign in with user name and password as always.
2. Enter the code from the app, or from the mail.
3. Set it up once under **My account > Two-step sign-in** (`/user2fa/setup`) by scanning a QR code.

What the organization decides:

| Setting (`[General]`) | Effect |
|---|---|
| `Enforce2FA=disabled` (default) | staff opt in |
| `Enforce2FA=enabled`, `DefaultMethod=totp` | everyone without a second step sets up the app right after the password |
| `Enforce2FA=enabled`, `DefaultMethod=email` | everyone without a second step gets e-mail codes at once |

Every role that signs in needs the policies `user2fa/setup` and `user2fa/verify`. A key in
`settings/override/sevenxauthentication2fa.ini.append.php` encrypts the authenticator secrets; losing it means
everyone sets the app up again ([Two-step sign-in, Turning it on](../../../guides/two-factor-login.md#turning-it-on)).

Lost phones: an administrator sets the user's *Two-step sign-in* field to e-mail codes or Disabled and publishes;
the user then sets the app up again. Plan who on the help desk may do this.

On the demonstration server two-step sign-in has been on since 6 October 2026 as opt-in
([Two-step sign-in, On the demonstration server](../../../guides/two-factor-login.md#on-the-demonstration-server)).

## Social login

The same extension contains social login handlers for Google, Facebook, Twitter/X, Instagram, Meta and ID.me,
described as skeletons ready to customize, plus a command line builder for more OAuth providers. Social login is off
until `[SocialLogin] Enabled=enabled`; a user signing in this way still passes their own second step
([sevenx_authentication_2fa, Use it](../../../features/6.0/extensions/sevenx_authentication_2fa.md#use-it)). For a
staff intranet a company directory is usually the better source of accounts
([Directory sign-in and LDAP](directory-sign-in-and-ldap.md)).

## Password expiry

`ezmbpaex` sets how long a password is valid, can warn by mail before it expires, and can check new passwords
against a regular expression ([ezmbpaex](../../../features/6.0/extensions/ezmbpaex.md)). Whether forced expiry fits
your policy is a decision for your security officer.

## Sessions and signing out

A session ends `site.ini [Session] SessionTimeout` seconds after its last use; the default is 259200, three days.
Administrators can see who signed in recently and, with the database session handler, remove sessions one by one
([Sessions](../../../guides/sessions.md)). Shorter timeouts suit shared computers.

## Records of sign-ins

The audit trail records sign-ins, failed sign-ins, account locks, role assignments and password resets in its
`access` channel ([The audit trail](../../../features/6.0/audit-trail.md#what-is-recorded)). Two-step sign-in writes
every step to `var/log/auth.log`, never a code or a secret.

## Decisions for the intranet

- [ ] Password rules chosen in `[PasswordSettings]`.
- [ ] `MaxNumberOfFailedLogin` set; office addresses in `TrustedIPList[]` if wanted.
- [ ] Two-step sign-in: opt-in or required; administrators enrolled first.
- [ ] Help desk procedure for lost phones.
- [ ] Encryption key for the authenticator secrets stored safely and backed up.
- [ ] Session timeout decided.
- [ ] Network restriction decided (office only, VPN, or internet with sign-in).

## Related pages

- [Two-step sign-in (guide)](../../../guides/two-factor-login.md)
- [Two-step sign-in for hosts](../../hosting/security/two-factor-sign-in.md)
- [Your account and signing in](../../accounts-and-sign-in.md)
- [User accounts and passwords](../security/user-accounts-and-passwords.md)
- [Directory sign-in and LDAP](directory-sign-in-and-ldap.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
