---
title: "Lesson 8.3: Two-step sign-in"
audience: trainer, admin
category: training
order: 83
summary: Learners turn on two-step sign-in on a training installation, give roles the two policies it needs, set up an authenticator app or e-mail codes for a test user, sign in with a code and help a user who lost their phone. 45 minutes.
keywords: site administration, two-step sign-in, two-factor, 2FA, sevenx_authentication_2fa, authenticator app, TOTP, e-mail code, user2fa/setup, user2fa/verify, Enforce2FA, LoginHandler, auth.log
type: help
---
# Lesson 8.3: Two-step sign-in

With two-step sign-in, a password alone no longer signs anyone in: after the password comes a code from an
authenticator app or from an e-mail. A stolen or guessed password is then useless on its own. This lesson shows
administrators how to turn it on, how users set it up, and what to do when a user loses their phone.

> **Note**
> Never enroll a real administrator account while trying it out. Use a test user with an address at
> `example.invalid`, and keep the training installation's mail transport on `file`.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Module | [Module 8: Security](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Site administrators |
| Prerequisites | [Lesson 8.2](passwords-and-sign-in-policy.md); an administrator account, a test user and a shell; an authenticator app on a phone (optional) |

## Learning objectives

After this lesson, learners can:

- explain what the second step protects against and what it does not;
- turn on two-step sign-in: the extension, the login handler, the user class field and the two policies;
- set up an authenticator app or e-mail codes for a test user and sign in with a code;
- reset the second step for a user who lost their phone;
- choose between opt-in and `Enforce2FA=enabled`.

## Trainer's notes

### How to present it

Source: [Two-step sign-in](../../../../guides/two-factor-login.md). The extension's own documentation is in
`extension/sevenx_authentication_2fa/README.md` and `INSTALL.md`.

1. **What a user sees** (4 minutes). Sign in as always; a page asks for the 6-digit code; each code works once;
   after five wrong codes the sign-in ends. Setup is under *My account* > *Two-step sign-in* (`/user2fa/setup`).
   Changing or turning it off asks for a current code.
2. **Turning it on** (5 minutes). Four parts: the extension `sevenx_authentication_2fa` active; the login handler
   `sevenxUser2fa` before `standard` in `[UserSettings] LoginHandler[]`; a field of the datatype *7x 2FA
   Configuration* in the user class; and the policies `user2fa/setup` and `user2fa/verify` for every role that signs
   in. Recommended: a `SecretKey` that encrypts the authenticator secrets.
3. **Opt-in or required** (2 minutes). `[General] Enforce2FA=disabled` (default) lets users opt in. `enabled` with
   `DefaultMethod=totp` makes a user without a second step set up the app right after the password. Enroll the
   administrators first.
4. **Lost phone** (2 minutes). An administrator opens the user's object, sets the *Two-step sign-in* field to
   *E-mail one-time code* or *Disabled*, and publishes.
5. **What protects it** (2 minutes). A new session id after the code; codes limited per sign-in; e-mail codes kept
   hashed; every step written to `var/log/auth.log`, never a code or a secret.

### Common questions

**Does the extension come with a new installation?** The guide says the installers activate it. Check
**Setup > Extensions** on your training installation before class; the user class field and the login handler may
still need to be set.

**What if the encryption key is lost?** Everyone sets the app up again. Keep `SecretKey` out of version control and in
the backup (Module 9).

**Does social login skip the second step?** No. A user found through a provider still passes their own second step.

### Pitfalls

- **Clock skew.** Authenticator codes are only valid within about a minute. A training machine with a wrong clock
  refuses every code.
- **No field in the user class.** Users of a class without the field cannot keep a second step; the setup page says
  so.
- **`settings/override` outranks a siteaccess.** Mail sent from a test siteaccess still uses the global transport.

## Hands-on exercise

Use your own training installation. If the trainer has already prepared steps 1 to 4, start at step 5.

1. Open **Setup > Extensions** and check that `sevenx_authentication_2fa` is active. If not, activate it as your
   trainer shows.
2. In `settings/override/site.ini.append.php`, set the login handlers (edit the file; keep a copy first):

   ```ini
   [UserSettings]
   LoginHandler[]
   LoginHandler[]=sevenxUser2fa
   LoginHandler[]=standard
   ```

3. Open the user class (**Setup > Classes**, group *Users*, class *User*) and check for a field of the datatype
   *7x 2FA Configuration*. If there is none, add one with the identifier `two_factor` and store the class.
4. Open **Users > Roles and policies**, open the role of your test user (for example *Member* or *Editor*) and add
   the policies `user2fa/setup` and `user2fa/verify`.
5. Clear the caches: `php bin/php/ezcache.php --clear-tag=ini` and, if the site runs on Velocity,
   `php bin/php/console exp:velocity deploy` as your trainer shows.
6. In a private window, sign in as the test user and open `/user2fa/setup`.
7. Choose *Authenticator app*, scan the QR code with an app on your phone, and enter the code it shows. Without a
   phone, choose e-mail codes instead.
8. Sign out and sign in again. Enter the code from the app, or, for e-mail codes, find the newest file in
   `var/log/mail` and read the code in it.
9. Sign out, sign in, and type a wrong code twice before the right one.
10. As administrator, open **Setup > Log files** and read the newest lines of `auth.log`.
11. Reset the test user: open the user's object, set the *Two-step sign-in* field to *Disabled* and publish.

## Expected result

- Step 7 ends with the second step active for the test user; the key is not shown again.
- Step 8: the sign-in asks for the code after the password, and the user is signed in only after the right code.
- Step 10 lists the steps of the sign-ins, including the wrong codes, and no code or secret.
- After step 11 the test user signs in with the password alone.
- The trainer checks one learner's `auth.log` view on the projector.

## Quiz

1. Which two policies must every role that signs in hold?
2. In which order must the login handlers be listed?
   A. `standard`, then `sevenxUser2fa` B. `sevenxUser2fa`, then `standard` C. Order does not matter D. Only
   `sevenxUser2fa`
3. A user lost their phone. What does the administrator do?
4. What does `Enforce2FA=enabled` with `DefaultMethod=totp` do for a user without a second step?
5. Which log records every step of two-step sign-in?

<details>
<summary>Answers</summary>

1. `user2fa/setup` and `user2fa/verify` ([Two-step sign-in](../../../../guides/two-factor-login.md#turning-it-on)).
2. **B.** The two-step handler comes before `standard`.
3. Opens the user's object, sets the *Two-step sign-in* field to *E-mail one-time code* or *Disabled*, and publishes;
   the user then sets the app up again.
4. The user must set up an authenticator app right after the password, before being signed in.
5. `var/log/auth.log`, without codes or secrets.

</details>

## Further reading

- [Two-step sign-in](../../../../guides/two-factor-login.md): users, administrators and what protects it.
- [Security and audit, section 5](../../../../guides/security-and-audit.md#5-two-factor-sign-in-optional-10-minutes):
  the short walk-through.
- [Two-factor sign-in for hosts](../../../hosting/security/two-factor-sign-in.md).

## Related pages

- [Module 8: Security](README.md)
- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
