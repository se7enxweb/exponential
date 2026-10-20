---
title: Retire the shared admin login
audience: government, admin
category: public-sector
order: 50
summary: Step by step - replace the shared admin account with personal administrator accounts so the audit trail names people, then disable admin (after moving UserCreatorID) or make it unusable, and keep a tested break-glass procedure.
keywords: admin account, shared account, personal accounts, accountability, UserCreatorID, disable admin, break-glass, exp:resetuserpassword, audit trail, least privilege, public sector
type: help
---
# Retire the shared admin login

For administrators. The login `admin` exists on every installation and is the first an attacker tries; shared use also
hides who did what. The project's procedure is in
[Change the administrator password: Account hygiene](../../../change-the-admin-password.md#account-hygiene); this page
is its checklist form.

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check that it worked](#check-that-it-worked)
4. [If something goes wrong](#if-something-goes-wrong)
5. [Related pages](#related-pages)

## Before you start

- Every person who uses `admin` today is known.
- Shell access for the `UserCreatorID` change and the break-glass test.

## Steps

1. **Personal accounts**: in **Users > Administrator users**, create one user per administrator with a personal login
   and a real e-mail address. Check under **Roles and policies** > **Administrator** that the group gives the role.
2. **Two-step sign-in** for each of them (see the people-and-permissions tasks of this help).
3. **Each person signs in** with the new account and checks their work.
4. **Move `UserCreatorID`** before disabling `admin`: in `settings/override/site.ini.append.php` set
   `[UserSettings] UserCreatorID=<object id of an enabled administrator or service account>` (the shipped `14` is
   `admin`; command-line tools and self-registration act as that account). Clear the INI cache and deploy.
5. **Retire `admin`**, one of:
   - disable it: open the `admin` user > **Configure user account settings** > clear **Enable user account** > **OK**;
   - keep it enabled but unusable: `./console exp:resetuserpassword --allow-root-user -u admin -g -l 64`, and do not
     keep the password.
6. **Break-glass**: write down the procedure (root shell and `exp:resetuserpassword` for an administrator account) and
   test it once a year on a copy.

## Check that it worked

- Signing in as `admin` fails.
- A command-line tool that acts as `UserCreatorID` (for example a trash purge on a test copy) runs.
- The audit shows actions under personal logins from now on.

## If something goes wrong

If nobody can sign in, use the break-glass procedure; the full set of recovery ways is in
[Change the administrator password](../../../change-the-admin-password.md).

## Related pages

- [Roles and least privilege](../../security/roles-and-least-privilege.md)
- [The default administrator password](../../../default-administrator-password.md)
- [Security hardening: Default and shared credentials](../../../security-hardening.md#default-and-shared-credentials)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
