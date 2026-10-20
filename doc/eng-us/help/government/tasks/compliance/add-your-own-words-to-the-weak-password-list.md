---
title: Add your own words to the weak password list
audience: government, admin
category: public-sector
order: 30
summary: Step by step - make Exponential refuse passwords built from your organization's own words (its name, the site, the city) on top of the shipped list of well-known passwords, with WeakPasswordList[] or WeakPasswordFile.
keywords: weak passwords, WeakPasswordCheck, WeakPasswordList, WeakPasswordFile, forbidden words, password policy, ForbidLogin, ForbidCurrentPassword, public sector
type: help
---
# Add your own words to the weak password list

For administrators. Attackers try an organization's own name and city first. Exponential refuses well-known and common
passwords by default (`[UserSettings] WeakPasswordCheck=enabled`) and lets you add words of your own
([13.6 Passwords and sign-in](../../../../install/13-security-hardening.md#136-passwords-and-sign-in)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check that it worked](#check-that-it-worked)
4. [Related pages](#related-pages)

## Before you start

- Shell access, or edit rights on `settings/override/site.ini.append.php`.
- A short list of words: the organization's name and abbreviation, the site's name, the city or region, common
  department names.

## Steps

1. Add the words in `settings/override/site.ini.append.php`:

   ```ini
   [UserSettings]
   WeakPasswordCheck=enabled
   WeakPasswordList[]=exampletown
   WeakPasswordList[]=departmentofexample
   ```

   or, for a longer list, a file with one word per line, readable by the site's user:

   ```ini
   [UserSettings]
   WeakPasswordFile=settings/override/weak-passwords.txt
   ```

2. Also forbid the login name and reuse of the current password:

   ```ini
   [PasswordSettings]
   ForbidLogin=enabled
   ForbidCurrentPassword=enabled
   ```

3. `php bin/php/ezcache.php --clear-tag=ini --allow-root-user` and reload PHP (or deploy on Velocity).

## Check that it worked

As a test user, try one of the words as a new password: it is refused. Try the login name: refused. Existing passwords
keep working until they are changed.

## Related pages

- [Password policy](../../security/password-policy.md)
- [Set the minimum password length to 12](set-the-minimum-password-length-to-12.md)
- [Changing your password](../../../../features/6.0/modern-password-change.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
