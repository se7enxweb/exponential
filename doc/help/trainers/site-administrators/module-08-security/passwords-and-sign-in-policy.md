---
title: "Lesson 8.2: Passwords and sign-in policy"
audience: trainer, admin
category: training
order: 82
summary: Learners read and set the password policy of an installation, see the weak password check refuse a common password, lock and unlock a test account after failed sign-ins, and explain why a password change signs out other sessions. 40 minutes.
keywords: site administration, password policy, MinPasswordLength, WeakPasswordCheck, PasswordSettings, EndOtherSessions, MaxNumberOfFailedLogin, locked account, Check user accounts, Unlock, exp:ini
type: help
---
# Lesson 8.2: Passwords and sign-in policy

The sign-in is the most attacked address of any site. Exponential stores passwords as bcrypt hashes, refuses
well-known passwords, does not reveal which accounts exist and can lock an account after failed attempts. This lesson
shows administrators the settings behind these defenses, how to choose a sensible policy and how to help a user who
has been locked out.

> **Note**
> Change settings only on your own training installation. Every test account uses an address at `example.invalid`.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Module | [Module 8: Security](README.md) |
| Duration | 40 minutes (15 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Site administrators |
| Prerequisites | [Lesson 8.1](thinking-like-an-attacker.md); an administrator account and a test user on the training installation |

## Learning objectives

After this lesson, learners can:

- read the password settings in effect with `exp:ini`;
- set a minimum password length in an override and explain why length beats composition rules;
- show the weak password check refusing a common password;
- set a lockout after failed sign-ins and unlock an account on **Check user accounts**;
- explain what `EndOtherSessions` does after a password change.

## Trainer's notes

### How to present it

Source: [Chapter 13, 13.6 Passwords and sign-in](../../../../install/13-security-hardening.md#136-passwords-and-sign-in).

1. **The settings** (4 minutes). `site.ini [UserSettings]`: `MinPasswordLength` (10 bytes shipped),
   `GeneratePasswordLength`, `WeakPasswordCheck`, `ForgotPasswordKeyLifetime` (one hour), `HashType=php_default`
   (bcrypt). `[PasswordSettings]`: composition rules, all off by default; `ForbidLogin`, `ForbidCurrentPassword`,
   `EndOtherSessions`, `ChangeNotificationMail`, the strength meter and the **Generate a strong password** link.
2. **A reasonable policy** (3 minutes). The chapter suggests `MinPasswordLength=15` with `ForbidLogin` and
   `ForbidCurrentPassword`, following current guidance that favors length over composition rules. Raising the
   minimum locks nobody out: it applies when a password is next set.
3. **Sessions after a change** (3 minutes). With `EndOtherSessions=enabled`, every other session of the user is
   signed out on its next request, and the session that changed the password gets a new id. That is what makes a
   password change useful after an account was taken over.
4. **Failed sign-ins** (3 minutes). `MaxNumberOfFailedLogin=0` means no lockout. A value such as 10 locks an account
   until an administrator unlocks it. Weigh it: anyone can then lock a known account. The audit's brute force alerts
   report attacks either way.
5. **Unlocking** (2 minutes). **Users > Check user accounts**, filter **Locked**, tick, **Unlock the ticked
   accounts**, confirm.

### Common questions

**Why not require a digit and a symbol?** Composition rules lead to predictable passwords such as `Summer2026!`. The
`Require*` rules exist for organizations whose own policy prescribes them.

**Does the password change mail contain the password?** No. It tells the account owner that the password changed,
so they learn of a change they did not make.

**A user forgot their password.** **Forgot your password?** answers the same for every address and its link works once
and for one hour by default. An administrator can also set a new password; see
[Change the administrator password](../../../change-the-admin-password.md) for every way.

### Pitfalls

- **Editing `settings/site.ini`.** Settings go into `settings/override/site.ini.append.php`. Use `exp:ini set` with
  `--dry-run` first.
- **Locking the administrator.** Test the lockout with a test user, never with your only administrator account.
- **Caches and Velocity.** After a settings change, clear the INI cache; on Velocity, restart or deploy so the workers
  read it.

## Hands-on exercise

Use your own training installation, in a terminal at the installation root and in two browser windows.

1. Read the settings in effect:

   ```bash
   php bin/php/console exp:ini get site.ini/UserSettings/MinPasswordLength
   php bin/php/console exp:ini get site.ini/UserSettings/WeakPasswordCheck
   php bin/php/console exp:ini get site.ini/UserSettings/MaxNumberOfFailedLogin
   ```

2. Preview raising the minimum length, then write it:

   ```bash
   php bin/php/console exp:ini set site.ini/UserSettings/MinPasswordLength 15 global --dry-run
   php bin/php/console exp:ini set site.ini/UserSettings/MinPasswordLength 15 global
   ```

3. Set a lockout after five failed sign-ins the same way (`site.ini/UserSettings/MaxNumberOfFailedLogin`, value `5`).
4. Clear the INI cache: `php bin/php/ezcache.php --clear-tag=ini`. If the site runs on Velocity, restart it as your
   trainer shows.
5. In a private window, sign in as the test user and open the password change page of the account. Try a short
   password, then a common one such as `password1234567`. Read both messages.
6. Set a long password that **Generate a strong password** proposes, or a long passphrase of your own, and save it.
7. Sign out. Sign in as the test user with a wrong password six times.
8. As administrator, open **Users > Check user accounts**, choose the **Locked** filter and press **Show**. Find the
   test user.
9. Tick it, press **Unlock the ticked accounts** and confirm.
10. Sign in as the test user with the right password.

## Expected result

- Step 1 prints the shipped values (10, enabled, 0) unless your installation overrides them.
- Step 5: the form refuses the short password because of its length and the common one as well known.
- Step 8 lists the test user as locked; after step 9 the filter no longer lists it and step 10 works.
- The trainer checks one learner's `settings/override/site.ini.append.php`: the two new settings are there, nothing
  in `settings/site.ini` changed.

## Quiz

1. Which setting decides how many failed sign-ins lock an account, and what does `0` mean?
2. Why does current guidance prefer a long minimum length over "one digit, one symbol"?
3. True or false: raising `MinPasswordLength` locks out users whose passwords are shorter.
4. A user's account was taken over. They change their password. What happens to the attacker's session?
5. Where do you unlock a locked account in the administration?
   A. **Setup > Sessions** B. **Users > Check user accounts** C. **Setup > Ini settings** D. **Setup > Log files**

<details>
<summary>Answers</summary>

1. `site.ini [UserSettings] MaxNumberOfFailedLogin`; `0` means no lockout.
2. Composition rules lead to predictable passwords; length makes guessing expensive
   ([Chapter 13, 13.6](../../../../install/13-security-hardening.md#136-passwords-and-sign-in)).
3. **False.** Existing passwords keep working; the rule applies when a password is next set.
4. With `EndOtherSessions=enabled` it is signed out on its next request.
5. **B.** With the **Locked** filter and **Unlock the ticked accounts**
   ([Check user accounts](../../../../features/6.0/user-hygiene.md)).

</details>

## Further reading

- [Chapter 13: Security hardening for production](../../../../install/13-security-hardening.md): sections 13.6 and
  13.7.
- [Changing your password](../../../../features/6.0/modern-password-change.md): what users see.
- [Check user accounts](../../../../features/6.0/user-hygiene.md): every filter and action.
- [First sign-in and default passwords for hosts](../../../hosting/security/first-sign-in-and-default-passwords.md).

## Related pages

- [Module 8: Security](README.md)
- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
