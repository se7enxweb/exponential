---
title: ldap.ini [LDAPSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [LDAPSettings] in ldap.ini: LDAPDebugTrace, LDAPVersion, LDAPFollowReferrals, LDAPEnabled, LDAPServer, LDAPPort, LDAPBaseDn, LDAPBindUser ..."
keywords: Einstellung, ldap.ini, LDAPSettings, INI, setting, LDAPDebugTrace, LDAPVersion, LDAPFollowReferrals, LDAPEnabled, LDAPServer, LDAPPort, LDAPBaseDn, LDAPBindUser, LDAPBindPassword, LDAPSearchScope, LDAPEqualSign, LDAPSearchFilters, LDAPLoginAttribute, LDAPUserGroupType, LDAPUserGroup, LDAPGroupRootNodeId, LDAPGroupMappingType, LDAPCreateMissingGroups, LDAPGroupBaseDN, LDAPGroupClass, LDAPGroupNameAttribute, LDAPGroupMemberAttribute, LDAPGroupDescriptionAttribute, LDAPUserGroupMap, LDAPUserGroupAttributeType, LDAPUserGroupAttribute, LDAPFirstNameAttribute, LDAPFirstNameIsCommonName, LDAPLastNameAttribute, LDAPEmailAttribute
type: generated
generated: exp:help:examples
---
# ldap.ini [LDAPSettings]

Der Block `[LDAPSettings]` von `settings/ldap.ini`, mit 33 Einstellung(en).

Bearbeiten Sie `settings/ldap.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/ldap.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/ldap.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/ldap.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## LDAPDebugTrace

> Enable tracing the the ldap login, outputs extensive debug info for use during setup  
> NOTE: Do not keep this enabled on production setup as login name and passwords will be  
> logged to logfiles or outputted if DebugOutput settings are enabled.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPDebugTrace=disabled
*/ ?>
```

## LDAPVersion

> Set LDAP version number  

Standard: `2`.

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

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPFollowReferrals=0
*/ ?>
```

## LDAPEnabled

> Set to true if use LDAP server  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPEnabled=true
*/ ?>
```

## LDAPServer

> LDAP host  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPServer=
*/ ?>
```

## LDAPPort

> Port nr for LDAP, default is 389  

Standard: `389`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPPort=389
*/ ?>
```

## LDAPBaseDn

> Specifies the base DN for the directory.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPBaseDn=
*/ ?>
```

## LDAPBindUser

> If the server does not allow anonymous bind, specify the user name for the bind here.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPBindUser=
*/ ?>
```

## LDAPBindPassword

> If the server does not allow anonymous bind, specify the password for the bind here.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPBindPassword=
*/ ?>
```

## LDAPSearchScope

> Could be sub, one, base.  

Standard: `sub`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPSearchScope=sub
*/ ?>
```

## LDAPEqualSign

> Use the equla sign to replace "=" when specify LDAPBaseDn or LDAPSearchFilters  

Standard: `--`.

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

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
# ein leeres "LDAPSearchFilters[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LDAPSearchFilters[]
*/ ?>
```

## LDAPLoginAttribute

> LDAP attribute for login. Normally, uid  

Standard: `uid`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPLoginAttribute=uid
*/ ?>
```

## LDAPUserGroupType

> Could be id or name  

Standard: `id`.

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

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
# ein leeres "LDAPUserGroup[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LDAPUserGroup[]
*/ ?>
```

## LDAPGroupRootNodeId

> Group mapping settings:  
> Root node id where LDAP groups are created, node id: 5 is used if blank  

Standard: `5`.

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

Standard: `UseGroupAttribute`.

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

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPCreateMissingGroups=disabled
*/ ?>
```

## LDAPGroupBaseDN

> Base LDAP dn which should be used to fetch user group objects from LDAP  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPGroupBaseDN=
*/ ?>
```

## LDAPGroupClass

> LDAP user group class  

Standard: `exampleGroupDAClassName`.

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

Standard: `cn`.

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

Standard: `member`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPGroupMemberAttribute=member
*/ ?>
```

## LDAPGroupDescriptionAttribute

> Attribute which contain description of LDAP group, optional  

Standard: leer.

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

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
# ein leeres "LDAPUserGroupMap[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LDAPUserGroupMap[]
*/ ?>
```

## LDAPUserGroupAttributeType

> LDAP attribute type for user group. Could be name, id or dn  

Standard: `name`.

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

Standard: `employeetype`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPUserGroupAttribute=employeetype
*/ ?>
```

## LDAPFirstNameAttribute

> LDAP attribute for First name. Normally, givenname  

Standard: `givenname`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPFirstNameAttribute=givenname
*/ ?>
```

## LDAPFirstNameIsCommonName

> If cn (common name) is used for first name, sn (Last name) will be removed from first name  

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPFirstNameIsCommonName=false
*/ ?>
```

## LDAPLastNameAttribute

> LDAP attribute for Last name. Normally, sn  

Standard: `sn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPLastNameAttribute=sn
*/ ?>
```

## LDAPEmailAttribute

> LDAP attribute for email. Normally, mail  

Standard: `mail`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPEmailAttribute=mail
*/ ?>
```

## LDAPEmailEmptyAttributeSuffix

> For use if LDAP does not return mail, creates one using login name + email suffix, like '@example.com'  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPEmailEmptyAttributeSuffix=
*/ ?>
```

## Utf8Encoding

> LDAP encoding is utf-8 or not  

Standard: `false`.

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

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
KeepGroupAssignment=disabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von ldap.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/ldap.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
