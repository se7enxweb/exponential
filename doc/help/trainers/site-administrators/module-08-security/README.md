---
title: "Module 8: Security"
audience: trainer, admin
category: training
order: 80
summary: The eighth module of Site administration, the attacker's view and the hardening checklist, passwords and sign-in policy, two-step sign-in, sessions and cookies, and the Content Security Policy. Five lessons, 3 hours 20 minutes.
keywords: site administration, module 8, security, hardening, passwords, lockout, two-step sign-in, 2FA, sessions, cookies, Content Security Policy
type: help
---
# Module 8: Security

Exponential 6 is secure by default against most automated attacks; the administrator keeps those defaults in force and
closes what is left. This module starts with the attacker's view and a check every administrator can run, then works
through the sign-in (password policy, lockout, two-step sign-in), sessions and cookies, and the Content Security
Policy that stops injected scripts.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Duration | 3 hours 20 minutes (five lessons) |
| Lessons | 5 |
| Level | Intermediate |
| Format | Hands-on; a training installation with administration and shell access per learner |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [8.1 Thinking like an attacker](thinking-like-an-attacker.md) | 40 min | Run the outside check, find a probe in security.log, mark the checklist |
| [8.2 Passwords and sign-in policy](passwords-and-sign-in-policy.md) | 40 min | Set a password policy and lockout, unlock a test account |
| [8.3 Two-step sign-in](two-step-sign-in.md) | 45 min | Turn on 2FA, enroll a test user, reset it |
| [8.4 Sessions and cookies](sessions-and-cookies.md) | 35 min | Switch to database sessions, sign a browser out, choose cookie settings |
| [8.5 Content Security Policy](content-security-policy.md) | 40 min | Read the header, collect reports, enforce and roll back |

## Preparing this module

- One training installation per learner, served over HTTP (for example with `exp:velocity start`), with
  administration and shell access. Never run the outside check of lesson 8.1 against a site you do not run.
- One test user per learner with an address at `example.invalid`; the mail transport set to `file`.
- For lesson 8.3: check on your own installation whether `sevenx_authentication_2fa` is active and the user class has
  the *7x 2FA Configuration* field; prepare steps 1 to 4 in advance if time is short. Check that the training
  machines' clocks are right. A phone with an authenticator app helps; e-mail codes in files work without one.
- For lesson 8.5: a browser with developer tools.
- A copy of `settings/override/site.ini.append.php` per learner before the module, so every change can be undone.

## Timing plan

| Time | Item |
|---|---|
| 0:00 | Lessons 8.1 and 8.2 (80 min) |
| 1:20 | Break (15 min) |
| 1:35 | Lesson 8.3 (45 min) |
| 2:20 | Break (10 min) |
| 2:30 | Lessons 8.4 and 8.5 (75 min) |
| 3:45 | End (3 h 20 min of lessons plus breaks) |

## Related pages

- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
