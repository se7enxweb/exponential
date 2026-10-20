---
title: Demo mode for developers
audience: developer
category: extension-development
order: 92
summary: The demo mode API for extension and installer authors - expDemoMode::enabled(), protectsPassword(), refusalText(), refused(), installing() and the store guard in eZUser::store(), the template operator exp_demo_mode(), and how to make code of your own respect EXP_DEMO_MODE=1 in persistent workers.
keywords: demo mode, EXP_DEMO_MODE, expDemoMode, protectsPassword, refusalText, installing, guardStore, eZUser::store, exp_demo_mode, template operator, password change, extension, installer, Velocity, persistent worker
type: help
---
# Demo mode for developers

This page is for developers of extensions, installers and integrations that set or change user passwords. On a public
demo the environment variable `EXP_DEMO_MODE=1` switches on **demo mode**: the password of an administrator cannot be
changed or reset by any path, and the installers accept the published demo password. The kernel enforces it at the
last line, `eZUser::store()`, so code of yours cannot break a demo by accident; this page shows how to make it give
the user the reason instead of failing quietly.

## Contents

1. [The API](#the-api)
2. [Refusing a password change of your own](#refusing-a-password-change-of-your-own)
3. [The store guard](#the-store-guard)
4. [Installers that set the administrator password](#installers-that-set-the-administrator-password)
5. [Templates](#templates)
6. [Persistent workers](#persistent-workers)
7. [Testing](#testing)
8. [Related pages](#related-pages)

## The API

`expDemoMode` (`kernel/classes/expdemomode.php`), all static:

| Method | Returns or does |
|---|---|
| `enabled()` | `true` while `EXP_DEMO_MODE` is exactly `1` (`getenv()`, then `$_SERVER`); read on every call |
| `isAdministrator( $user )` | the account (`eZUser` or content object id) has unlimited access to every module and function; `true` when its roles cannot be read |
| `protectsPassword( $user )` | `enabled()` and `isAdministrator()`: the account's password must not change |
| `refusalText()` | the translated message: "This installation is a public demo: the passwords of administrators cannot be changed or reset." |
| `refused( $user, $path, $event )` | records a refusal: the audit event (`access.user.password.change.failed` by default, or `access.user.password.reset.failed`) with the reason `demo_mode` and `$path`, and a debug warning |
| `installing( $callable )` | runs `$callable` with the store guard open (see below); returns what it returns |
| `guardStore( eZUser $user )` | the store guard, called by `eZUser::store()` |

There is no setting to read and none to write: the mode belongs to the server's environment, not to the
installation.

## Refusing a password change of your own

Ask before you change a password, and show the reason:

```php
if ( class_exists( 'expDemoMode' ) && expDemoMode::protectsPassword( $user ) )
{
    expDemoMode::refused( $user, 'myextension/password' );
    $errors[] = expDemoMode::refusalText();
    return;
}
eZUserOperationCollection::password( $user->id(), $newPassword );
```

A web service answers HTTP 403 with `expDemoMode::refusalText()`, as the kernel's `expaccount` and `expuser` services
do. A form that sends a reset mail sends none for an administrator and gives the same answer as for any address, so
the form does not reveal which address belongs to an administrator.

The kernel's operations `user/password` and `user/forgotpassword` already refuse: they return
`array( 'status' => false, 'reason' => 'demo_mode' )`.

## The store guard

`eZUser::store()` calls `expDemoMode::guardStore()` while demo mode is on. When the stored row of an existing
administrator has another `password_hash` or `password_hash_type` than the object being stored, the guard puts the
stored values back, the rest of the row is stored, and the refusal is recorded. A new account, an unchanged password
and every account that is not an administrator pass.

So a CSV import, a package or an extension that sets `password_hash` cannot change an administrator's password on a
demo. Code that writes `ezuser.password_hash` with its own SQL bypasses the guard: always store users through
`eZUser`. (The kernel's own SQL write of the hash, the hash upgrade at sign-in, stores the same password with a newer
method and is not a change.)

## Installers that set the administrator password

The kernel's installation steps set the administrator password inside `expDemoMode::installing()`. An installer of
your own that sets it after them (a site package's post-install, a multi-site installer) must do the same, or its
change is refused in demo mode:

```php
$set = function () use ( $admin, $password ) {
    $admin->setInformation( $admin->id(), $admin->attribute( 'login' ), $admin->attribute( 'email' ), $password, $password );
    $admin->store();
};
class_exists( 'expDemoMode' ) ? expDemoMode::installing( $set ) : $set();
```

The scope ends when the callable returns or throws, so it never reaches the next request of a persistent worker.
Never call `installing()` from code a visitor can reach.

To accept the demo password, ask `eZStepSiteAdmin::isWeakDefaultPassword()`, `isTooShortAdminPassword()` and
`kickstartPasswordProblem()`, as the kernel's installers do: in demo mode they accept a well-known password of any
length (never an empty one). To store a password shorter than `MinPasswordLength`, set it inside both
`expDemoMode::installing()` and `expDemoMode::settingInstallationAdministratorPassword()`, as `eZStepCreateSites`
does: only there does `eZUser::validatePassword()` accept it, and only while demo mode is on.

## Templates

- `exp_demo_mode()` (`expDemoModeOperator`) returns whether demo mode is on, worked out each time the template runs.
- `{include uri='design:parts/exp_demo_mode_banner.tpl'}` draws the administration's banner (admin, admin4 and
  admin4l), with the shared component `.a4-notice.is-warn`. Include it outside every `{cache-block}`, or the cached
  page shows the mode of the request that filled the cache.
- `user/password.tpl` gets `demo_mode_locked` and `demo_mode_text`, `user/forgotpassword.tpl` `demo_mode_text` and
  `demo_mode_refused`, `setup/init/site_admin.tpl` `demo_mode`. All are optional: use `first_set()`.

## Persistent workers

Exponential Velocity and FrankenPHP keep PHP processes for many requests. `expDemoMode` caches nothing, so it always
answers for the environment of the process now. In code that may run in a worker started before the class existed,
check `class_exists( 'expDemoMode' )` first, as the kernel does. Do not cache the answer in a static variable or an
object that lives longer than the request.

## Testing

Set and unset the variable in the test, and restore it afterwards:

```php
putenv( 'EXP_DEMO_MODE=1' );   // on
putenv( 'EXP_DEMO_MODE' );     // unset
```

`tests/tests/kernel/classes/user/expDemoModeTest.php` shows the kernel's tests without a database: an `eZUser` with a
fixed access array stands in for an administrator, and `expDemoMode::storeAllowed()` is the store guard's decision
as a pure function.

## Related pages

- [Demo mode: the upgrade note](../../../bc/6.0/demo-mode.md)
- [Demo mode](../../demo-mode.md) (administrators), [Demo mode for hosts](../../hosting/security/demo-mode-for-hosts.md)
- [eZUser and access checks](ezuser-and-access.md)
- [Security in extensions](../part-3-extension-developer/security-in-extensions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
