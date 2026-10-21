---
title: Login handlers - LDAP, textfile and your own
audience: integrator, developer, admin
category: integration
order: 220
summary: Letting people sign in to Exponential with an account that lives in a directory or another system - how the chain of login handlers works, the LDAP handler and its group mapping, the textfile handler, writing a login handler of your own, and the security points.
keywords: login handler, LoginHandler, LDAP, ldap.ini, eZLDAPUser, LDAPVersion, group mapping, textfile, textfile.ini, eZTextFileUser, custom login handler, ExtensionDirectory, login_handler, directory, Active Directory, identity provider, authentication
type: help
---
# Login handlers - LDAP, textfile and your own

This page is for integrators who connect Exponential to a company directory or another user store, so that staff or
members sign in with the account they already have. When someone signs in, Exponential asks the **login handlers**
named in `site.ini [UserSettings] LoginHandler[]`, in order; the first handler that accepts the login and password
signs the user in. Each external user gets a local Exponential user, placed in a user group, so roles and policies
work as for everyone else.

## Contents

1. [The chain](#the-chain)
2. [LDAP](#ldap)
3. [Textfile](#textfile)
4. [A login handler of your own](#a-login-handler-of-your-own)
5. [Security points](#security-points)
6. [Checklist](#checklist)

## The chain

```ini
# settings/override/site.ini.append.php
[UserSettings]
LoginHandler[]
LoginHandler[]=LDAP
LoginHandler[]=standard
```

`standard` is the local password check. The shipped default is `standard` alone; `LDAP` and `textfile` are listed
commented out. Keep `standard` in the chain, after the external handler, so a local emergency administrator can still
sign in when the directory is down. The two-step sign-in of `sevenx_authentication_2fa` is also a login handler
(`sevenxUser2fa`), placed first ([Two-step sign-in](../../guides/two-factor-login.md)).

The kernel finds a handler `<name>` as `kernel/classes/datatypes/ezuser/ez<name>user.php` with the class
`eZ<name>User`, or in an extension directory listed in `[UserSettings] ExtensionDirectory[]`, as
`extension/<ext>/login_handler/ez<name>user.php` (`eZUserLoginHandler`, `kernel/classes/datatypes/ezuser/ezuserloginhandler.php`).

## LDAP

The handler is `eZLDAPUser` (`kernel/classes/datatypes/ezuser/ezldapuser.php`) on PHP's `ldap` extension. Settings in
`settings/override/ldap.ini.append.php`:

```ini
[LDAPSettings]
LDAPEnabled=true
LDAPVersion=3
LDAPServer=ldaps://ldap.example.com
LDAPPort=636
LDAPBaseDn=ou--people,dc--example,dc--com
LDAPBindUser=cn--exponential,ou--services,dc--example,dc--com
LDAPBindPassword=<the service account's password>
LDAPLoginAttribute=uid
LDAPSearchScope=sub
LDAPFirstNameAttribute=givenname
LDAPLastNameAttribute=sn
LDAPEmailAttribute=mail
Utf8Encoding=true
LDAPUserGroupType=name
LDAPUserGroup[]
LDAPUserGroup[]=Staff
```

| Setting | Meaning |
|---|---|
| `LDAPEqualSign=--` | In `LDAPBaseDn` and `LDAPSearchFilters[]`, `--` stands for `=` (an INI value cannot hold `=`) |
| `LDAPSearchFilters[]` | Extra filters, for example `objectClass--inetOrgPerson` |
| `LDAPUserGroupType`, `LDAPUserGroup[]` | The default group for new users, by `id` or `name` |
| `LDAPGroupMappingType` | `UseGroupAttribute` (from `LDAPUserGroupAttribute`), `SimpleMapping` (`LDAPUserGroupMap[]`, name to name) or `GetGroupsTree` |
| `LDAPCreateMissingGroups` | Create groups that do not exist yet |
| `KeepGroupAssignment` | `enabled`: a user moved to another group by hand stays there |
| `LDAPDebugTrace` | Logs logins **and passwords**: never in production |

Known gaps ([API and protocol standards, LDAP](../../standards/apis-and-protocols.md#ldap-rfc-4511)): the default
`LDAPVersion=2` must be set to `3` for any current directory; StartTLS is not called, so encryption depends on an
`ldaps://` address; whether `ldaps://` works with the separate `LDAPPort` setting is unverified, so test it with your
directory. The login is escaped before it goes into a filter.

## Textfile

`eZTextFileUser` reads users from a delimited text file (`settings/textfile.ini [TextFileSettings]`): `FileName`,
`FilePath` (`root` = the installation root), `FileFieldSeparator` (default `tab`), the column numbers of login
(`LoginAttribute`), password (`PasswordAttribute`), first name, last name and e-mail, and the default group. It is a
simple way to let a nightly export of another system define who may sign in.

> **Note**
> The file holds passwords as the other system exported them. Keep it outside the web root, readable only by the
> site user, and prefer LDAP or a handler of your own that asks the other system, so passwords never sit in a file.

## A login handler of your own

For an identity provider that offers a password check over an API, write a handler in your extension:

```ini
# extension/myintegration/settings/site.ini.append.php
[UserSettings]
ExtensionDirectory[]=myintegration
```

```php
<?php
// extension/myintegration/login_handler/ezmyidpuser.php, used as LoginHandler[]=myidp
class eZmyidpUser extends eZUser
{
    static function loginUser( $login, $password, $authenticationMatch = false )
    {
        $client = new myIntegrationIdpClient();             // your API client, configured from myintegration.ini
        $profile = $client->checkPassword( $login, $password );
        if ( !$profile )
        {
            return false;                                   // the next handler in the chain is asked
        }
        $user = eZUser::fetchByName( $login );
        if ( !$user instanceof eZUser )
        {
            $user = myIntegrationUserProvisioning::create( $login, $profile );   // a user object in the right group
        }
        if ( !$user instanceof eZUser || !$user->isEnabled() )
        {
            return false;
        }
        eZUser::setCurrentlyLoggedInUser( $user, $user->attribute( 'contentobject_id' ) );
        return $user;
    }
}
```

The class name is `eZ` plus the handler name exactly as written in `LoginHandler[]`, plus `User`; the file name is
the same in lower case. Read `eZLDAPUser::loginUser()` for the complete pattern of creating and updating the local user
and its group assignment.

## Security points

- **HTTPS everywhere** a password is typed or sent onwards.
- **A dedicated bind account** for LDAP with read access to the people branch only.
- **Disabled at the source means disabled here.** Decide what happens when a person leaves: the directory refuses the
  password, but the local user still exists; disable it with a scheduled job or by hand ([User hygiene](../../features/6.0/user-hygiene.md)).
- **Failed logins count.** Failed sign-ins are recorded in the audit (`access.session.login.failed`) and alert rules
  watch them.
- **Keep the bind password in `settings/override`**, never in Git.

## Checklist

- [ ] Handler chain decided; `standard` kept as fallback with a local emergency administrator.
- [ ] LDAP: `LDAPVersion=3`, encrypted connection tested, bind account with least rights, `LDAPDebugTrace=disabled`.
- [ ] Group mapping tested with users of each group.
- [ ] Leaver process agreed with the customer.
- [ ] Sign-in tested on every siteaccess that should accept external users.

## Related pages

- [Single sign-on and social login](single-sign-on-and-social-login.md)
- [Two-step sign-in](../../guides/two-factor-login.md)
- [eZUser and access](../extension-development/part-8-reference/ezuser-and-access.md)
- [Service accounts, roles and policies](service-accounts-roles-and-policies.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
