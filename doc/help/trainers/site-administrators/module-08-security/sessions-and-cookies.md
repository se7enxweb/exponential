---
title: "Lesson 8.4: Sessions and cookies"
audience: trainer, admin
category: training
order: 84
summary: Learners explain what a session is and how long it lasts, read Setup > Sessions with the default handler, switch a training installation to database sessions, sign a test user's browser out safely and choose secure cookie settings. 35 minutes.
keywords: site administration, sessions, Setup Sessions, session handler, ezpSessionHandlerDB, SessionTimeout, cookies, CookieSecure, CookieSameSite, sign out, Remove timed out sessions
type: help
---
# Lesson 8.4: Sessions and cookies

A session keeps one browser signed in. An administrator needs to know how long sessions last, how to see who is
signed in, how to sign a browser out after an incident, and which cookie settings keep a session from being stolen.
This lesson covers **Setup > Sessions** with both kinds of session handler and the cookie settings of chapter 13.

> **Note**
> Switching the session handler signs everybody out once. Do it only on your own training installation.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Module | [Module 8: Security](README.md) |
| Duration | 35 minutes (15 presenting, 20 exercise) |
| Level | Intermediate |
| Audience | Site administrators |
| Prerequisites | [Lesson 8.2](passwords-and-sign-in-policy.md); an administrator account, a test user and a shell |

## Learning objectives

After this lesson, learners can:

- say how long a session lasts by default and which setting changes it;
- explain why **Setup > Sessions** cannot list sessions with the default handler, and what it shows instead;
- switch to database sessions and remove one user's sessions through the confirmation;
- explain why removing a session is not enough to keep an attacker out;
- choose cookie settings for a site served only over HTTPS.

## Trainer's notes

### How to present it

Sources: [Sessions: who is signed in, and signing browsers out](../../../../guides/sessions.md) and
[Chapter 13, 13.7 Sessions and cookies](../../../../install/13-security-hardening.md#137-sessions-and-cookies).

1. **What a session is** (2 minutes). Opened at the first request that needs it; ends `site.ini [Session]
   SessionTimeout` seconds after its last use (259200, three days, by default).
2. **Two handlers** (4 minutes). `Handler=` empty (the default) means PHP keeps sessions in its own storage: the page
   **Session administration** shows who signed in recently (**Recently signed in**, with **the last 1 h**, **the last
   day**, **the session lifetime** and **Find a user**), but cannot list or remove sessions. With
   `Handler=ezpSessionHandlerDB` sessions are in the table `ezsession` and the page lists and removes them.
3. **Removing safely** (3 minutes). **Remove timed out / old sessions** signs nobody out. **Remove all sessions**
   signs everybody out, you included, after a confirmation. Your own current session is never removed from a selection.
4. **Not enough on its own** (2 minutes). Removing a session signs a browser out; it does not block the user. After a
   takeover, change the password (with `EndOtherSessions`, lesson 8.2) or disable the account as well.
5. **Cookies** (4 minutes). `CookieSecure` (`auto`, or `true` for HTTPS-only sites), `CookieHttponly=true`,
   `CookieSameSite=Lax`, and a shorter `SessionTimeout` to limit how long a stolen session is useful. Separate
   session cookies per siteaccess keep a public sign-in from being an administration sign-in.

### Common questions

**Should every site switch to database sessions?** Only if you need to see and remove sessions. `ForceStart=enabled`
also writes a row for every anonymous visit; set it only if anonymous visitors must be counted.

**Users behind an HTTPS proxy are signed out on every page.** Typically `CookieSecure=true` while the kernel thinks
the request is plain HTTP: the proxy is not trusted or does not send `X-Forwarded-Proto` (chapter 13, 13.10).

**Why not `CookieSameSite=Strict`?** It drops the session on the first click from an e-mail or another site into a
signed-in page, which users see as being signed out.

### Pitfalls

- **Signing the class out.** If several learners share one installation, **Remove all sessions** signs everyone out.
  On one installation per learner it only signs out the learner.
- **Forgetting the restart.** On Velocity, a handler change needs a deploy so the workers read it.
- **Time skew.** *Time skew detected* in **Last activity** means the clocks of web servers differ or `SessionTimeout`
  was lowered.

## Hands-on exercise

Use your own training installation, in a terminal at the installation root and in two browsers (or a normal and a
private window).

1. As administrator, open **Setup > Sessions**. Read the notice about the handler and the **Recently signed in**
   list. Try the windows **the last 1 h** and **the session lifetime**.
2. Read the session settings in effect:

   ```bash
   php bin/php/console exp:ini get site.ini/Session/Handler
   php bin/php/console exp:ini get site.ini/Session/SessionTimeout
   ```

3. Switch to database sessions:

   ```bash
   php bin/php/console exp:ini set site.ini/Session/Handler ezpSessionHandlerDB global --dry-run
   php bin/php/console exp:ini set site.ini/Session/Handler ezpSessionHandlerDB global
   php bin/php/ezcache.php --clear-tag=ini
   ```

   On Velocity, deploy as your trainer shows.
4. Sign in again as administrator (everyone is signed out once). In the private window, sign in as the test user.
5. Reload **Setup > Sessions**. Read the figures and find the test user's card.
6. Press **Sessions** on the test user's card to see that user's sessions one by one.
7. Tick the test user's session, remove it and read the confirmation before you confirm.
8. In the private window, reload a page: the test user is signed out.
9. Press **Remove timed out / old sessions** and read the message.
10. On paper, write the `[Session]` settings you would use for a site served only over HTTPS, and compare them with
    chapter 13, section 13.7.

## Expected result

- Step 1 shows the notice that sessions are kept by PHP and cannot be counted or removed there.
- Step 5 shows figures for sessions in all and signed-in users, and a card for the test user.
- Step 8: the test user's browser is signed out; the administrator's session stayed.
- Step 10 includes `CookieSecure=true`, `CookieHttponly=true`, `CookieSameSite=Lax` and a chosen `SessionTimeout`.
- The trainer checks by asking one learner to explain what else they would do after a real account takeover.

## Quiz

1. How long does a session last by default after its last use?
2. Why can **Setup > Sessions** not remove sessions on a default installation?
3. True or false: removing a user's sessions stops that user from signing in again.
4. Which button signs nobody out?
   A. **Remove all sessions** B. **Remove timed out / old sessions** C. Remove selected D. All of them
5. What does `CookieSecure=true` guarantee?

<details>
<summary>Answers</summary>

1. 259200 seconds, three days (`site.ini [Session] SessionTimeout`).
2. The default handler keeps sessions in PHP's own storage, which the page cannot list
   ([Sessions, section 1](../../../../guides/sessions.md#1-the-page-without-a-session-table-the-default)).
3. **False.** It signs browsers out; disable the user or change the password as well.
4. **B.** It removes expired rows only.
5. The session cookie is never sent over plain HTTP.

</details>

## Further reading

- [Sessions: who is signed in, and signing browsers out](../../../../guides/sessions.md): the page and its problems.
- [Chapter 13: Security hardening for production](../../../../install/13-security-hardening.md): sections 13.7 and
  13.10.
- [TLS and HTTPS for hosts](../../../hosting/security/tls-and-https.md).

## Related pages

- [Module 8: Security](README.md)
- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
