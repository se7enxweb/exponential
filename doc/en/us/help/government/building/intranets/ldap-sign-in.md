---
title: LDAP sign-in
audience: government, admin
category: public-sector
order: 60
summary: What the kernel's LDAP login handler does (staff sign in with their directory account; an Exponential user is created and placed in groups), the settings in settings/ldap.ini, the PHP ldap extension it needs, and the testing to do because no 6.x guide exists.
keywords: LDAP, Active Directory, directory, login handler, ldap.ini, LDAPServer, LDAPBaseDn, LDAPLoginAttribute, LDAPGroupMappingType, single sign-in, staff accounts
type: help
---
# LDAP sign-in

This page is for administrators who connect an intranet to the organization's directory. The kernel ships an LDAP
login handler (`kernel/classes/datatypes/ezuser/ezldapuser.php`) configured in `settings/ldap.ini`. The 6.x
documentation has no guide for it; the settings file's comments and the older feature notes
(`doc/features/4.3/ldap_login_handler.txt`, `doc/features/3.8/ldap_user_binding.txt`) are the reference. Test it with
your directory before relying on it.

## Contents

1. [How it works](#how-it-works)
2. [Requirements](#requirements)
3. [Settings](#settings)
4. [Groups](#groups)
5. [Testing](#testing)
6. [Security notes](#security-notes)

## How it works

1. `site.ini [UserSettings] LoginHandler[]` lists `LDAP` (after `standard`, see "Sign-in options for staff").
2. A user signs in with directory credentials; the handler searches the directory and binds as the user.
3. On the first successful sign-in an Exponential user is created with names and e-mail from directory attributes,
   and placed in user groups according to the group mapping.
4. Such accounts have no password of their own in Exponential.

## Requirements

- The PHP `ldap` extension in the PHP that serves the site; the handler checks for `ldap_connect`.
- Network access from the web server to the directory. The handler has no StartTLS step; for an encrypted connection
  test an `ldaps://` server address with your PHP build, or protect the network path.
- A service account for searching if the directory does not allow anonymous search.

## Settings

`settings/override/ldap.ini.append.php`, `[LDAPSettings]` (shipped values in brackets):

| Key | Meaning |
|---|---|
| `LDAPServer`, `LDAPPort` (389) | the directory server |
| `LDAPVersion` (2) | protocol version; directories today usually need 3 |
| `LDAPBaseDn` | where users are searched |
| `LDAPBindUser`, `LDAPBindPassword` | the search account |
| `LDAPSearchScope` (sub), `LDAPSearchFilters[]` | how users are found; `LDAPEqualSign` (`--`) replaces `=` in these values |
| `LDAPLoginAttribute` (uid) | the attribute matched against the login name |
| `LDAPFirstNameAttribute`, `LDAPLastNameAttribute`, `LDAPEmailAttribute` | the attributes copied to the user |
| `LDAPUserGroup[]`, `LDAPUserGroupType` | the default group for new users |

Keep the bind password in `settings/override/`, never in version control.

## Groups

`LDAPGroupMappingType`:

| Value | Meaning |
|---|---|
| `UseGroupAttribute` (shipped) | a user attribute names the group(s); `LDAPCreateMissingGroups` decides whether missing groups are created |
| `SimpleMapping` | `LDAPUserGroupMap[]` maps directory group names to Exponential groups |
| `GetGroupsTree` | groups mapped name to name, created as needed below `LDAPGroupRootNodeId` |

Roles are then assigned to those groups (see "Sections and roles for intranet content").

## Testing

1. On a test installation, set the server, base DN and login attribute; turn on `LDAPDebugTrace` while testing.
2. Sign in with a test directory account; check the created user, its names, e-mail and groups.
3. Sign in with a wrong password and with a disabled directory account: both must fail.
4. Turn `LDAPDebugTrace` off again.

## Security notes

- A first LDAP sign-in refuses a login another account already has ([Security and audit](../../../../guides/security-and-audit.md#7-one-account-per-login-2-minutes)).
- Leavers: disabling the directory account stops new sign-ins; the Exponential user remains and can be disabled in
  **Users > Check user accounts**.
- Sign-ins are recorded in the audit trail's `access` channel.

## Related pages

- [Sign-in options for staff](sign-in-options-for-staff.md)
- [Security hardening](../../../security-hardening.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
