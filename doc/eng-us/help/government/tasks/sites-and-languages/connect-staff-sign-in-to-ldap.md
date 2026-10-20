---
title: Connect staff sign-in to LDAP
audience: government, admin
category: public-sector
order: 420
summary: Task for administrators: let intranet staff sign in with their directory account through the kernel's LDAP login handler, configured in ldap.ini, tested on a test installation first because no 6.x guide exists.
keywords: LDAP, directory, login handler, ldap.ini, LDAPServer, LDAPBaseDn, LDAPLoginAttribute, group mapping, staff sign-in
type: help
---
# Connect staff sign-in to LDAP

This task is for administrators. Background: [LDAP sign-in](../../building/intranets/ldap-sign-in.md).

## Before you start

- PHP's `ldap` extension in the PHP that serves the site.
- The directory's server, base DN, login attribute and a search account from your directory team.
- A test installation.

## Steps

1. In `settings/override/ldap.ini.append.php`, `[LDAPSettings]`: `LDAPServer`, `LDAPPort`, `LDAPVersion=3` if your
   directory needs it, `LDAPBaseDn`, `LDAPBindUser`, `LDAPBindPassword`, `LDAPLoginAttribute`, the name and e-mail
   attributes, `LDAPUserGroupType` and `LDAPUserGroup[]` for the default group, and the group mapping
   (`LDAPGroupMappingType`).
2. In the intranet siteaccess (or the override), chain the handlers:

   ```ini
   [UserSettings]
   LoginHandler[]
   LoginHandler[]=standard
   LoginHandler[]=LDAP
   ```

3. Clear the INI cache; with Velocity deploy it.
4. Turn `LDAPDebugTrace=enabled` on while testing; off afterwards.

## Check

- A test directory account signs in; a user is created with the right names, e-mail and groups.
- A wrong password and a disabled directory account fail.
- A local administrator can still sign in (the `standard` handler first).

## Problems

| Problem | Fix |
|---|---|
| Nothing happens, no error | the PHP `ldap` extension is missing |
| Users land in the wrong group | the group mapping settings; check `LDAPGroupMappingType` and the mapping |

## Related pages

- [Sign-in options for staff](../../building/intranets/sign-in-options-for-staff.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
