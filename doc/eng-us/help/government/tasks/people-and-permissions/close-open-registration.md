---
title: Close open registration
audience: government, admin
category: public-sector
order: 280
summary: Step by step - stop visitors from creating accounts on a public-sector site where nobody should self-register, on a new installation (no policy) and on an upgraded one (PolicyOmitListExclude), and clean up unactivated registrations.
keywords: registration, user/register, self-registration, close registration, PolicyOmitList, PolicyOmitListExclude, Anonymous role, unactivated users, spam accounts
type: help
---
# Close open registration

This task is for administrators of public websites where only staff have accounts. At the end, `user/register` is
refused for visitors, the sign-in page shows no registration link, and leftover registrations are removed. The rules
are in [Roles guide, section 8](../../../../guides/roles-and-policies.md#8-decide-who-may-register-and-who-may-read-node-data).

## Contents
1. [Before you start](#before-you-start)
2. [New installation](#new-installation)
3. [Upgraded installation](#upgraded-installation)
4. [Clean up old registrations](#clean-up-old-registrations)
5. [Check it worked](#check-it-worked)
6. [Related pages](#related-pages)

## Before you start

- Shell access (for an upgraded site), and an account with the `role` module.
- Make sure no service relies on self-registration (newsletter sign-up is separate from accounts in most setups;
  check yours).

## New installation

On a new installation, registering needs the `user/register` policy. Check that the **Anonymous** role does not have
*User / Register*: **Users > Roles and policies**, Anonymous. If it has it, **Edit**, remove the policy, **Save**.

## Upgraded installation

An upgraded site keeps registration open, because `user/register` stays in `site.ini [RoleSettings]
PolicyOmitList[]`. To close it, add to `settings/override/site.ini.append.php`:

```ini
[RoleSettings]
PolicyOmitListExclude[]=user/register
```

then clear the INI cache:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

`PolicyOmitListExclude[]` takes single entries out of the omit list after all settings files are read, so the
entries extensions add stay. Then make sure the Anonymous role has no *User / Register* policy.

## Clean up old registrations

**Users > Unactivated users** lists registrations never activated; those older than 30 days are marked **Old**. Use
**Remove all unactivated users** (or remove selected ones). The anonymous user, `admin` and you are never removed; the
removal is recorded as `access.user.remove`.

## Check it worked

- In a private window, open the sign-in page: no *Register new account* link.
- Open `/user/register` directly: refused.

## Related pages
- [User account hygiene](../../operations/people-and-permissions/user-account-hygiene.md)
- [Spam protection for forms](../../../creators/website-creators/spam-protection-for-forms.md)
- [Roles and policies guide](../../../../guides/roles-and-policies.md)
- [Security hardening](../../../security-hardening.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
