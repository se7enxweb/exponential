---
title: "Passwords and sign-in checks"
audience: admin, developer, hosting
category: administration
order: 4
summary: exp:user:reset-password sends the reset link or sets a generated or piped password, exp:user:require-password-change makes the next sign-in ask for a new password, and exp:user:check-login tells a script whether a password signs an account in - never with a password on the command line.
keywords: exp:user:reset-password, exp:user:require-password-change, exp:user:check-login, reset password, forgot password, generate password, password from standard input, --password-stdin, --send-link, --generate, --require-change, change password at next sign-in, health check, check credentials
type: help
---
# Passwords and sign-in checks

No command takes a password as an argument. A password comes from standard input, is generated and printed once,
or is chosen by the person through a mail.

## What you'll have at the end

A forgotten password replaced, a new password required at the next sign-in, or a script that knows whether a
service account's password still works.

## Reset a password

```bash
./console exp:user:reset-password jane --send-link --apply
./console exp:user:reset-password jane --generate --require-change --apply
./console exp:user:reset-password ci-bot --password-stdin --apply < /root/secrets/ci-bot-password
```

Choose exactly one of these:

| Option | What happens |
|---|---|
| `--send-link` | the mail of **Forgot password** with its one-time link; the password stays until the link is used, which works for `[UserSettings] ForgotPasswordKeyLifetime` |
| `--generate` | a password that passes the password rules, set and printed once |
| `--password-stdin` | the password read from standard input; it must pass the password rules |

`--require-change`, with `--generate` or `--password-stdin`, makes the person choose a new password at the next
sign-in.

`--generate` and `--password-stdin` end the account's sessions, as a password change in the administration does.
The audit records `access.user.password.change`, or `access.user.password.reset.request` for the link. It never
records the password. In demo mode (`EXP_DEMO_MODE=1`) an administrator's password is never changed.

**Note:** The link is built from the siteaccess the command runs in. Add `--siteaccess=<the public siteaccess>` when
the link must point at the public site.

## Require a new password at the next sign-in

```bash
./console exp:user:require-password-change jane
./console exp:user:require-password-change jane --apply
./console exp:user:require-password-change jane --clear --apply
```

The account is marked. At the next sign-in the person must choose a new password before going on. `--clear` removes
the mark. The current password stays valid until then. This is the box **Change password at next sign-in** of the
administration ([Change password at next sign-in](../../features/6.0/password-change-at-next-sign-in.md)).

Use it after you handed over a temporary password, or when a password may have been seen by someone else.

## Check whether a password signs an account in

```bash
printf '%s\n' "$CI_BOT_PASSWORD" | ./console exp:user:check-login ci-bot --password-stdin
```

`exp:user:check-login` reads the password from standard input and answers whether it signs the account in: the
password is right, the account is enabled and it is not locked.

| Exit code | Meaning |
|---|---|
| 0 | yes, it signs in |
| 1 | no; the output says why |

The check never counts as a failed sign-in, so a wrong password in a health check does not lock the account. The
password is never printed. With `--format=json` the keys `valid` (true or false) and `reason` tell a script the
answer.

Use it in a monitoring job or after a deploy, to find out early that a service account's password was changed.

## Related pages

- [Recover a locked-out administrator](recover-a-locked-out-administrator.md)
- [Sessions and sign-in security](sessions-and-sign-in-security.md): unlock an account, reset two-step sign-in.
- [Your account and signing in](../accounts-and-sign-in.md): what the person sees.
