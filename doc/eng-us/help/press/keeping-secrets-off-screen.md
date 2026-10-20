---
title: Keeping secrets off screen
audience: press
category: press-media
order: 163
summary: The complete list of what must never appear in a video, stream, screenshot or podcast about Exponential - passwords, the generated administrator password, provider keys, settings files, tokens, server addresses - where each one shows up in the product, how to prepare the machine, and what to do when one leaks.
keywords: secrets, passwords, API keys, provider keys, tokens, settings override, initial admin password, screen recording, leak, rotation, revoke, blur, creator
type: help
---
# Keeping secrets off screen

This page is for every creator who records or shares a screen. One frame with a password or a key is enough for
someone to use it, and recordings, clips and screenshots last for years. The safest secret is one that is not on the
machine you show. This page lists where Exponential's secrets show up, so you can keep them out of the picture.

## Contents

1. [Never on screen](#never-on-screen)
2. [Where secrets show up in Exponential](#where-secrets-show-up-in-exponential)
3. [Prepare the machine](#prepare-the-machine)
4. [During recording](#during-recording)
5. [If something leaks](#if-something-leaks)

## Never on screen

- Passwords of any account, including the one you type into the sign-in form.
- The generated administrator password and the file `var/log/initial-admin-password`.
- Provider keys (for example for Exponential Intelligence), API keys, OAuth client secrets, tokens, session cookies.
- The contents of `settings/override/` and any file with database credentials.
- Control panel passwords and dashboard tokens of Velocity.
- Private keys of certificates.
- Server addresses, database host names and internal paths of a real server.
- Two-step sign-in QR codes, secrets and recovery codes.
- Other people's data: see [Privacy on camera](privacy-on-camera.md).

## Where secrets show up in Exponential

| Place | What it shows | What to do | Source |
|---|---|---|---|
| The summary of `exp:install` | The administrator password, twice | Cut or blur it; change the password after recording | [The console install](../../install/07-console-install.md#710-what-you-see-at-the-end) |
| `var/log/initial-admin-password` | The generated password | Never open it on camera; delete it after the first sign-in | [The default administrator password](../default-administrator-password.md) |
| `--password=`, `--db-password=` on the command line | Passwords in the terminal and its history | Use `EXP_INSTALL_DB_PASSWORD` for the database; clear the history | [Install in one command](../../features/6.0/install-in-one-command.md#choose-a-database) |
| `settings/override/site.ini.append.php` | Database settings | Never open it on camera | [Live demo safety](../advocates/live-demo-safety.md#what-must-never-be-on-screen) |
| `settings/override/expai.ini.append.php` | The provider key of Exponential Intelligence | Never open it; revoke the key after recording | [Exponential Intelligence](../../features/6.0/extensions/expai.md#switch-it-on) |
| Velocity control panel sign-in, `qbixctl panel:password --generate` | The panel password | Set it off camera | [Velocity control panel](../../features/6.0/velocity-control-panel.md#first-sign-in) |
| A dashboard address with `?token=` | The dashboard token | Do not show the address bar | [Velocity control panel](../../features/6.0/velocity-control-panel.md) |
| Two-step sign-in setup (`/user2fa/setup`) | The QR code and secret; recovery codes | Never finish the setup on camera | [Two-step sign-in](../../guides/two-factor-login.md) |
| **Setup > System information** | Paths, host names, database details | Show the top only, or blur | [System information](../../guides/system-information.md) |
| **Setup > Log files** with **Show unmasked** | E-mail and IP addresses, secrets as written | Never switch it on while recording | [Log viewer](../../features/6.0/log-viewer.md) |
| Error pages and terminal errors | Paths, sometimes settings | Read before you show | [Live demo safety](../advocates/live-demo-safety.md#during-the-demo) |

## Prepare the machine

```text
[ ] A demo installation that holds no real secret: its own passwords, a key made only for the recording
[ ] A separate browser profile: no saved passwords, no autocomplete, bookmarks bar hidden
[ ] One window captured, never the whole screen
[ ] Notifications off: system, chat, e-mail, phone
[ ] Terminal: large font, a prompt without real host or user names, history cleared or off for the session
[ ] Password manager locked; sign in before recording, not during
[ ] Desktop and file manager: no file names with customers or projects
```

## During recording

- If a sign-in form appears, pause the recording or the screen share, type, resume.
- Show documentation pages instead of settings files: they explain the same thing and contain no secrets.
- Before you show an error page or a long terminal output, read it.
- Keep the address bar out of the frame when an address carries a token.

## If something leaks

| What leaked | Do at once | Then |
|---|---|---|
| A password | Change it | Sign out other sessions |
| A provider or API key | Revoke it at the provider | Create a new key; check the provider's usage for misuse |
| The control panel password or dashboard token | Set a new password; changing it ends every session | Check the panel's sign-ins |
| A two-step secret or recovery code | Set up two-step sign-in again for that account | |
| A real server's address or path | Tell the server's owner | Review what is exposed |
| A recording with any of the above | Do not publish it, or take it down | Edit the part out, or record again |

Blurring after the fact helps viewers who come later; it does not help against people who already saw or downloaded
the original. Treat a secret that was on screen as burned. If you find that a secret leaks because of a flaw in the
software, report it privately to security@se7enx.com, as `SECURITY.md` asks, and do not publish details until a fix
is out.

## Related pages

- [Live demo safety](../advocates/live-demo-safety.md)
- [Privacy on camera](privacy-on-camera.md)
- [Live stream guide](live-stream-guide.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
