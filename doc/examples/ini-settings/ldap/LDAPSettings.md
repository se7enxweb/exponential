---
title: ldap.ini [LDAPSettings]
audience: developer, admin
category: examples
summary: "The settings of [LDAPSettings] in ldap.ini: LDAPDebugTrace, LDAPVersion, LDAPFollowReferrals, LDAPEnabled, LDAPServer, LDAPPort, LDAPBaseDn, LDAPBindUser ..."
keywords: ldap.ini, LDAPSettings, INI, setting, LDAPDebugTrace, LDAPVersion, LDAPFollowReferrals, LDAPEnabled, LDAPServer, LDAPPort, LDAPBaseDn, LDAPBindUser, LDAPBindPassword, LDAPSearchScope, LDAPEqualSign, LDAPSearchFilters, LDAPLoginAttribute, LDAPUserGroupType, LDAPUserGroup, LDAPGroupRootNodeId, LDAPGroupMappingType, LDAPCreateMissingGroups, LDAPGroupBaseDN, LDAPGroupClass, LDAPGroupNameAttribute, LDAPGroupMemberAttribute, LDAPGroupDescriptionAttribute, LDAPUserGroupMap, LDAPUserGroupAttributeType, LDAPUserGroupAttribute, LDAPFirstNameAttribute, LDAPFirstNameIsCommonName, LDAPLastNameAttribute, LDAPEmailAttribute
type: generated
generated: exp:help:examples
---
# ldap.ini [LDAPSettings]

The block `[LDAPSettings]` of `settings/ldap.ini`, with 33 setting(s).

Never edit `settings/ldap.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/ldap.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/ldap.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/ldap.ini.append.php` in an extension (read when the extension is active).

## LDAPDebugTrace

> Enable tracing the the ldap login, outputs extensive debug info for use during setup  
> NOTE: Do not keep this enabled on production setup as login name and passwords will be  
> logged to logfiles or outputted if DebugOutput settings are enabled.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPDebugTrace=disabled
*/ ?>
```

## LDAPVersion

> Set LDAP version number  

Default: `2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPVersion=2
*/ ?>
```

## LDAPFollowReferrals

> Determines whether the LDAP library automatically follows referrals returned by LDAP servers or not.  
> set to 1 to enable  

Default: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPFollowReferrals=0
*/ ?>
```

## LDAPEnabled

> Set to true if use LDAP server  

Default: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPEnabled=true
*/ ?>
```

## LDAPServer

> LDAP host  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPServer=
*/ ?>
```

## LDAPPort

> Port nr for LDAP, default is 389  

Default: `389`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPPort=389
*/ ?>
```

## LDAPBaseDn

> Specifies the base DN for the directory.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPBaseDn=
*/ ?>
```

## LDAPBindUser

> If the server does not allow anonymous bind, specify the user name for the bind here.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPBindUser=
*/ ?>
```

## LDAPBindPassword

> If the server does not allow anonymous bind, specify the password for the bind here.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPBindPassword=
*/ ?>
```

## LDAPSearchScope

> Could be sub, one, base.  

Default: `sub`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPSearchScope=sub
*/ ?>
```

## LDAPEqualSign

> Use the equla sign to replace "=" when specify LDAPBaseDn or LDAPSearchFilters  

Default: `--`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPEqualSign=--
*/ ?>
```

## LDAPSearchFilters

> Add extra search requirment. Uncomment it if you don't need it.  
> Example LDAPSearchFilters\[\]=objectClass--inetOrgPerson  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
# an empty "LDAPSearchFilters[]" first replaces the shipped list; without it the items are added
LDAPSearchFilters[]
*/ ?>
```

## LDAPLoginAttribute

> LDAP attribute for login. Normally, uid  

Default: `uid`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPLoginAttribute=uid
*/ ?>
```

## LDAPUserGroupType

> Could be id or name  

Default: `id`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPUserGroupType=id
*/ ?>
```

## LDAPUserGroup

> Default place to store LDAP users. Could be content object id or group name for LDAP user group,  
> depends on LDAPUserGroupType.  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
# an empty "LDAPUserGroup[]" first replaces the shipped list; without it the items are added
LDAPUserGroup[]
*/ ?>
```

## LDAPGroupRootNodeId

> Group mapping settings:  
> Root node id where LDAP groups are created, node id: 5 is used if blank  

Default: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPGroupRootNodeId=5
*/ ?>
```

## LDAPGroupMappingType

> Possible values: UseGroupAttribute (old style group assigning using LDAPUserGroupAttribute setting),  
> SimpleMapping (using LDAPUserGroupMap array for name-to-name group mapping), and GetGroupsTree  
> (automatic name-to-name group mapping, creating groups as needed)  

Default: `UseGroupAttribute`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPGroupMappingType=UseGroupAttribute
*/ ?>
```

## LDAPCreateMissingGroups

> When 'LDAPGroupMappingType' is set to 'UseGroupAttribute', this setting decides whether groups that  
> are not found in Exponential, should be created.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPCreateMissingGroups=disabled
*/ ?>
```

## LDAPGroupBaseDN

> Base LDAP dn which should be used to fetch user group objects from LDAP  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPGroupBaseDN=
*/ ?>
```

## LDAPGroupClass

> LDAP user group class  

Default: `exampleGroupDAClassName`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPGroupClass=exampleGroupDAClassName
*/ ?>
```

## LDAPGroupNameAttribute

> Attribute which should be used to obtain name of an LDAP group  
> Required then 'LDAPGroupMappingType' is set to 'GetGroupsTree' or 'SimpleMapping'  
> and when 'LDAPGroupMappingType' is set to 'UseGroupAttribute' and 'LDAPUserGroupAttributeType' is 'dn'  

Default: `cn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPGroupNameAttribute=cn
*/ ?>
```

## LDAPGroupMemberAttribute

> Attribute of LDAP user which should be used to obtain groups which user(group) belongs to.  
> Required then 'LDAPGroupMappingType' is set to 'GetGroupsTree' or 'SimpleMapping'  

Default: `member`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPGroupMemberAttribute=member
*/ ?>
```

## LDAPGroupDescriptionAttribute

> Attribute which contain description of LDAP group, optional  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPGroupDescriptionAttribute=
*/ ?>
```

## LDAPUserGroupMap

> Group names map (from LDAP to exponential user-groups),  
> used then 'LDAPGroupMappingType' is set to 'SimpleMapping'  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
# an empty "LDAPUserGroupMap[]" first replaces the shipped list; without it the items are added
LDAPUserGroupMap[]
*/ ?>
```

## LDAPUserGroupAttributeType

> LDAP attribute type for user group. Could be name, id or dn  

Default: `name`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPUserGroupAttributeType=name
*/ ?>
```

## LDAPUserGroupAttribute

> LDAP attribute for user group. For example, employeetype. If specified, LDAP users  
> will be saved under the same group as in LDAP server.  
> Depends on the value of LDAPUserGroupAttributeType:  
> name: attribute holding name of group  
> id: attribute holding nummeric id of group  
> dn: attribute holding DN of group  

Default: `employeetype`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPUserGroupAttribute=employeetype
*/ ?>
```

## LDAPFirstNameAttribute

> LDAP attribute for First name. Normally, givenname  

Default: `givenname`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPFirstNameAttribute=givenname
*/ ?>
```

## LDAPFirstNameIsCommonName

> If cn (common name) is used for first name, sn (Last name) will be removed from first name  

Default: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPFirstNameIsCommonName=false
*/ ?>
```

## LDAPLastNameAttribute

> LDAP attribute for Last name. Normally, sn  

Default: `sn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPLastNameAttribute=sn
*/ ?>
```

## LDAPEmailAttribute

> LDAP attribute for email. Normally, mail  

Default: `mail`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPEmailAttribute=mail
*/ ?>
```

## LDAPEmailEmptyAttributeSuffix

> For use if LDAP does not return mail, creates one using login name + email suffix, like '@example.com'  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPEmailEmptyAttributeSuffix=
*/ ?>
```

## Utf8Encoding

> LDAP encoding is utf-8 or not  

Default: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
Utf8Encoding=false
*/ ?>
```

## KeepGroupAssignment

> if 'enabled' you can move LDAP users to a different group and they will not  
> be automatically moved back (to the group they are configured to be placed in)  
> when the user logs in again.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
KeepGroupAssignment=disabled
*/ ?>
```

## Related

- [All blocks of ldap.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/ldap.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
