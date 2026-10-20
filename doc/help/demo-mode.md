---
title: Demo mode
audience: admin, hosting, developer
category: administration
order: 33
summary: What changes on an installation that runs as a public demo (the environment variable EXP_DEMO_MODE=1) - administrator passwords cannot be changed or reset, the installers accept the published demo password, a banner shows in the administration - and why it must never be on for a real site.
keywords: demo mode, EXP_DEMO_MODE, public demo, demonstration site, administrator password, password reset, forgot password, banner, environment variable, well-known password, security.log, system.demo.mode
type: help
---
# Demo mode

This page is for administrators of an Exponential site, and for whoever wonders why the administration shows a
**Demo mode** banner. A public demonstration site publishes its administrator sign-in so that anyone can try the
administration. **Demo mode** keeps such a demo usable for the next visitor: nobody can change or reset the password
of an administrator. It is switched on by the server, with the environment variable `EXP_DEMO_MODE=1`, and never from
the administration.

> **Warning:** Demo mode means that the administrator password is known to everyone. If you see the banner on a site
> with real content, real users or real customers, tell the people who run the server at once: the variable must be
> removed there.

## Contents

1. [How to tell that demo mode is on](#how-to-tell-that-demo-mode-is-on)
2. [What you cannot do in demo mode](#what-you-cannot-do-in-demo-mode)
3. [What still works](#what-still-works)
4. [Who counts as an administrator](#who-counts-as-an-administrator)
5. [Switching it on or off](#switching-it-on-or-off)
6. [Related pages](#related-pages)

## How to tell that demo mode is on

- Every page of the administration (admin4 and the classic admin) shows a yellow notice at the top: **Demo mode.**
  "This installation is a public demo: anyone may know its administrator password, and administrator passwords cannot
  be changed or reset. Do not enter anything private here."
- The **Change password** page of an administrator says that the password cannot be changed.
- **Setup > Log files** shows `demo/mode` lines in `security.log`, and the audit has `system.demo.mode` events.

## What you cannot do in demo mode

For an administrator account, demo mode refuses every way of setting a new password:

- **Change password** (in the user menu): the form shows "This installation is a public demo: the passwords of
  administrators cannot be changed or reset." and nothing is changed.
- **Forgot your password?**: no reset mail is sent for an administrator. The page tells every visitor that
  administrator passwords cannot be reset, and gives every address the same answer, so it does not reveal which
  address belongs to an administrator.
- **Edit** the user (the user account field): a new password for an administrator is refused with the same message;
  the name, the e-mail address and the other fields can still be changed.
- The console command `exp:resetuserpassword`, the web services and imports: refused or ignored for an administrator.

Each refusal is recorded in the audit (**Setup > Audit**) with the reason `demo_mode`.

## What still works

- Signing in with the published password.
- Everything else an administrator may do. Demo mode does not make the site read-only: a visitor can edit content,
  create users, change roles and settings. That is why a demo is rebuilt from scratch on a schedule.
- Other accounts: an editor or a registered member changes and resets their own password as usual, with all the usual
  password rules.

## Who counts as an administrator

An account whose roles give it unlimited access to everything: the account `admin`, everybody in **Administrator
users**, and any account given the **Administrator** role directly. An account with limited access (an editor, a
section administrator) is not protected by demo mode.

## Switching it on or off

Demo mode is not a setting. There is no switch in the administration, in the settings files or in the address bar, so
nobody can turn it on by accident or by mistake. The people who run the server set or remove the environment variable
`EXP_DEMO_MODE=1` for the demo's PHP processes; only the exact value `1` turns it on. How, for each kind of server, is
on [Demo mode for hosts](hosting/security/demo-mode-for-hosts.md).

During the installation of a demo, demo mode also lets the installer accept the published demo password, which it
would otherwise refuse as too well-known or too short (`publish` or `publishing`). The details are in the upgrade note
[Demo mode: EXP_DEMO_MODE=1](../bc/6.0/demo-mode.md).

## Related pages

- [Demo mode for hosts](hosting/security/demo-mode-for-hosts.md), [Demo mode FAQ](demo-mode-faq.md),
  [Demo mode problems](hosting/runbooks/demo-mode-problems.md)
- [Demo mode for developers](extension-development/part-8-reference/demo-mode-for-developers.md)
- [Demo mode: the upgrade note](../bc/6.0/demo-mode.md)
- [The default administrator password](default-administrator-password.md),
  [Change the administrator password](change-the-admin-password.md)
- [Setting up a demo installation](press/setting-up-a-demo-installation.md),
  [Live demo safety](advocates/live-demo-safety.md)
- [Free documentation](free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
