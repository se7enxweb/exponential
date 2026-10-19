---
title: Directory sign-in and LDAP
audience: businesses, admin, developer
category: business-solutions
order: 80
summary: What Exponential really offers for signing staff in with company directory accounts - the kernel's LDAP and text file login handlers, how users and groups are created, single sign-on and custom handlers as extension points, and what is not built in.
keywords: LDAP, directory, Active Directory, single sign-on, SSO, login handler, LoginHandler, ldap.ini, textfile.ini, eZLDAPUser, eZTextFileUser, SingleSignOnHandlerArray, group mapping, LDAPGroupMappingType, SAML, OpenID Connect, intranet sign-in
type: help
---
# Directory sign-in and LDAP

This page is for IT managers who want staff to sign in to the intranet with the account they already have. It states
exactly what the Exponential kernel contains for this, verified in the code, and what it does not contain, so that a
project is planned on facts. The settings are in `settings/ldap.ini`, `settings/textfile.ini` and `settings/site.ini`;
the code is in `kernel/classes/datatypes/ezuser/`.

## Contents

1. [What exists, in one table](#what-exists-in-one-table)
2. [How login handlers work](#how-login-handlers-work)
3. [The LDAP login handler](#the-ldap-login-handler)
4. [Groups from the directory](#groups-from-the-directory)
5. [The text file login handler](#the-text-file-login-handler)
6. [Single sign-on and custom handlers](#single-sign-on-and-custom-handlers)
7. [What is not built in](#what-is-not-built-in)
8. [Points to settle with your partner](#points-to-settle-with-your-partner)
9. [Related pages](#related-pages)

## What exists, in one table

| Way to sign in | In the kernel | Default | Source |
|---|---|---|---|
| Local Exponential accounts (`standard`) | yes | on | `site.ini [UserSettings] LoginHandler[]=standard` |
| LDAP directory (`LDAP`) | yes, `eZLDAPUser` | off (commented out) | `kernel/classes/datatypes/ezuser/ezldapuser.php`, `settings/ldap.ini` |
| A delimited text file (`textfile`) | yes, `eZTextFileUser` | off (commented out) | `kernel/classes/datatypes/ezuser/eztextfileuser.php`, `settings/textfile.ini` |
| Automatic single sign-on | an extension point (`SingleSignOnHandlerArray[]`), no handler shipped | empty | `kernel/classes/datatypes/ezuser/ezuser.php`, `settings/site.ini` |
| Two-step sign-in and social login | extension `sevenx_authentication_2fa` | opt-in | [Two-factor and sign-in](two-factor-and-sign-in.md) |
| Your own login handler | an extension point (`ExtensionDirectory[]`) | none | `settings/site.ini` |

## How login handlers work

`site.ini [UserSettings] LoginHandler[]` lists the handlers that are tried, in order, when someone signs in
(`kernel/classes/datatypes/ezuser/ezuserloginhandler.php`). The shipped file contains:

```ini
LoginHandler[]=standard
#LoginHandler[]=LDAP
#LoginHandler[]=textfile
```

To use the directory, a partner adds `LDAP` to the list in `settings/override/site.ini.append.php` and fills in
`ldap.ini`. Keeping `standard` in the list lets local accounts, such as the emergency administrator, still sign in.

## The LDAP login handler

What `eZLDAPUser::loginUser()` does, read from the code:

1. It connects to `LDAPServer` on `LDAPPort` (default 389) with `LDAPVersion` (shipped `2`) and binds anonymously, or
   with `LDAPBindUser` and `LDAPBindPassword` when the directory does not allow anonymous binds.
2. It searches below `LDAPBaseDn` for the login in the attribute `LDAPLoginAttribute` (shipped `uid`), with optional
   extra `LDAPSearchFilters[]` and the scope `LDAPSearchScope` (`sub`, `one` or `base`). The login is escaped before
   it is put in the filter.
3. It binds as the found entry with the password the person typed. If that fails, the sign-in fails.
4. It creates or updates the matching Exponential user account with the first name, last name and e-mail from
   `LDAPFirstNameAttribute` (`givenname`), `LDAPLastNameAttribute` (`sn`) and `LDAPEmailAttribute` (`mail`). If the
   directory has no address, `LDAPEmailEmptyAttributeSuffix` can build one from the login.
5. The account it creates has an empty local password hash (`ezldapuser.php`): the password stays in the directory.

Consequences for the business:

- Staff use one password; leaving the company in the directory stops their sign-in.
- Password rules of Exponential do not apply to directory accounts; accounts without a password of their own (LDAP,
  SSO) are not checked by `EndOtherSessions` either (`settings/site.ini [PasswordSettings]`).
- A first LDAP sign-in may not take a login another account already has
  ([Security and audit](../../../guides/security-and-audit.md)).
- PHP needs its `ldap` extension. The `standard` Velocity binary includes LDAP
  ([Velocity packages and binaries](../../../features/6.0/velocity-packages-and-binaries.md)).

> **Warning**
> `LDAPDebugTrace=enabled` writes login names and passwords to log files, as its comment in `ldap.ini` says. Use it
> only while setting up, never in production.

## Groups from the directory

New directory users are placed in Exponential user groups, which is what gives them roles. `ldap.ini` offers:

| Setting | Meaning |
|---|---|
| `LDAPUserGroup[]`, `LDAPUserGroupType` | default group(s), by object ID or by name |
| `LDAPGroupMappingType=UseGroupAttribute` | (shipped) the group named in a user attribute, `LDAPUserGroupAttribute` (shipped `employeetype`) |
| `LDAPGroupMappingType=SimpleMapping` | directory group names mapped to Exponential group names with `LDAPUserGroupMap[]` |
| `LDAPGroupMappingType=GetGroupsTree` | groups mapped by name automatically, created as needed under `LDAPGroupRootNodeId` |
| `LDAPCreateMissingGroups` | with `UseGroupAttribute`: create groups that do not exist |
| `KeepGroupAssignment` | `enabled`: users an administrator moved are not moved back at their next sign-in |

With a mapping, department membership in the directory can decide department access on the intranet
([Departments, roles and sections](departments-roles-and-sections.md)).

## The text file login handler

`eZTextFileUser` reads a delimited file (`textfile.ini [TextFileSettings]`: `FileName`, `FilePath`,
`FileFieldSeparator`) whose columns hold login, e-mail, password, first name and last name, and places new users in
`DefaultUserGroup` (shipped `11`). The code compares the typed password with the password column as plain text.

> **Warning**
> The text file therefore contains readable passwords. Use it only for short-lived tests or migrations, keep the file
> outside the web root with tight file permissions, and remove it afterwards.

## Single sign-on and custom handlers

- **Automatic single sign-on.** For a visitor who is not signed in, `eZUser` calls every class
  `eZ<name>SSOHandler` listed in `SingleSignOnHandlerArray[]` and signs in the user its `handleSSOLogin()` returns.
  The repository ships no such handler; one is written as an extension, for example to trust a header set by a
  company sign-on proxy.
- **Your own login handler.** `ExtensionDirectory[]` names extensions that hold
  `login_handler/<name>user.php` with a class `eZ<name>User`; `sevenx_authentication_2fa` is built this way.

## What is not built in

The repository, searched on 19 October 2026, has no SAML handler, no OpenID Connect handler for staff sign-in, no
dedicated Active Directory integration beyond LDAP, and no automatic user provisioning from a directory (users are
created at their first sign-in). Any of these is an extension project; ask a partner to estimate it
(Choosing a partner).

## Points to settle with your partner

- [ ] Which directory, which base DN, which attribute holds the login.
- [ ] A read-only bind account, its password kept in `settings/override/` and out of version control.
- [ ] An encrypted connection to the directory; `ldap.ini` has no StartTLS setting, so test what your server offers.
- [ ] The group mapping and which groups receive which roles.
- [ ] A local emergency administrator kept with `standard`.
- [ ] What happens to the Exponential account when someone leaves
  ([Check user accounts](../../../features/6.0/user-hygiene.md)).

## Related pages

- [Two-factor and sign-in](two-factor-and-sign-in.md)
- [LDAP settings reference](../../../examples/ini-settings/ldap/LDAPSettings.md)
- [Extension points in ldap.ini](../../../examples/extension-points/ldap.md)
- [Security and audit](../../../guides/security-and-audit.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
