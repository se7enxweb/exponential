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

> Aktiviert die Verfolgung der LDAP-Anmeldung, gibt ausführliche Debug-Informationen für die Einrichtung aus  
> HINWEIS: Lassen Sie dies auf einem Produktivsystem nicht aktiviert, weil Anmeldenamen und Passwörter  
> in Logdateien protokolliert oder bei aktivierter DebugOutput ausgegeben werden.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPDebugTrace=disabled
*/ ?>
```

## LDAPVersion

> Die LDAP-Versionsnummer setzen  

Standard: `2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPVersion=2
*/ ?>
```

## LDAPFollowReferrals

> Legt fest, ob die LDAP-Bibliothek von LDAP-Servern zurückgegebenen Verweisen automatisch folgt.  
> zum Aktivieren auf 1 setzen  

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPFollowReferrals=0
*/ ?>
```

## LDAPEnabled

> Auf true setzen, wenn ein LDAP-Server verwendet wird  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPEnabled=true
*/ ?>
```

## LDAPServer

> LDAP-Host  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPServer=
*/ ?>
```

## LDAPPort

> Portnummer für LDAP, Standard ist 389  

Standard: `389`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPPort=389
*/ ?>
```

## LDAPBaseDn

> Gibt die Basis-DN des Verzeichnisses an.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPBaseDn=
*/ ?>
```

## LDAPBindUser

> Erlaubt der Server keinen anonymen Bind, geben Sie hier den Benutzernamen für den Bind an.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPBindUser=
*/ ?>
```

## LDAPBindPassword

> Erlaubt der Server keinen anonymen Bind, geben Sie hier das Passwort für den Bind an.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPBindPassword=
*/ ?>
```

## LDAPSearchScope

> Kann sub, one oder base sein.  

Standard: `sub`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPSearchScope=sub
*/ ?>
```

## LDAPEqualSign

> Mit diesem Gleichheitszeichen "=" ersetzen, wenn LDAPBaseDn oder LDAPSearchFilters angegeben werden  

Standard: `--`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPEqualSign=--
*/ ?>
```

## LDAPSearchFilters

> Zusätzliche Suchbedingung hinzufügen. Kommentieren Sie sie aus, wenn Sie sie nicht brauchen.  
> Beispiel LDAPSearchFilters\[\]=objectClass--inetOrgPerson  

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

> LDAP-Attribut für das Login. Normalerweise uid  

Standard: `uid`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPLoginAttribute=uid
*/ ?>
```

## LDAPUserGroupType

> Kann id oder name sein  

Standard: `id`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPUserGroupType=id
*/ ?>
```

## LDAPUserGroup

> Standardort für LDAP-Benutzer. Kann die Inhaltsobjekt-ID oder der Gruppenname der LDAP-Benutzergruppe sein,  
> je nach LDAPUserGroupType.  

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

> Einstellungen der Gruppenzuordnung:  
> ID des Wurzelknotens, unter dem LDAP-Gruppen angelegt werden; wenn leer, wird Knoten-ID 5 verwendet  

Standard: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPGroupRootNodeId=5
*/ ?>
```

## LDAPGroupMappingType

> Mögliche Werte: UseGroupAttribute (alte Zuweisung der Gruppe über die Einstellung LDAPUserGroupAttribute),  
> SimpleMapping (über das Array LDAPUserGroupMap, Gruppe für Gruppe nach Namen zugeordnet) und GetGroupsTree  
> (automatische Zuordnung nach Namen, legt Gruppen bei Bedarf an)  

Standard: `UseGroupAttribute`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPGroupMappingType=UseGroupAttribute
*/ ?>
```

## LDAPCreateMissingGroups

> Wenn 'LDAPGroupMappingType' auf 'UseGroupAttribute' steht, entscheidet diese Einstellung, ob Gruppen, die  
> in Exponential nicht gefunden werden, angelegt werden.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPCreateMissingGroups=disabled
*/ ?>
```

## LDAPGroupBaseDN

> Basis-DN in LDAP, unter der die Objekte der Benutzergruppen aus LDAP geholt werden  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPGroupBaseDN=
*/ ?>
```

## LDAPGroupClass

> LDAP-Klasse für Benutzergruppen  

Standard: `exampleGroupDAClassName`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPGroupClass=exampleGroupDAClassName
*/ ?>
```

## LDAPGroupNameAttribute

> Attribut, über das der Name einer LDAP-Gruppe ermittelt wird  
> Erforderlich, wenn 'LDAPGroupMappingType' auf 'GetGroupsTree' oder 'SimpleMapping' steht  
> und wenn 'LDAPGroupMappingType' auf 'UseGroupAttribute' und 'LDAPUserGroupAttributeType' auf 'dn' steht  

Standard: `cn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPGroupNameAttribute=cn
*/ ?>
```

## LDAPGroupMemberAttribute

> Attribut des LDAP-Benutzers, über das die Gruppen ermittelt werden, zu denen der Benutzer (die Gruppe) gehört.  
> Erforderlich, wenn 'LDAPGroupMappingType' auf 'GetGroupsTree' oder 'SimpleMapping' steht  

Standard: `member`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPGroupMemberAttribute=member
*/ ?>
```

## LDAPGroupDescriptionAttribute

> Attribut mit der Beschreibung der LDAP-Gruppe, optional  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPGroupDescriptionAttribute=
*/ ?>
```

## LDAPUserGroupMap

> Zuordnung der Gruppennamen (von LDAP zu den Benutzergruppen von Exponential),  
> verwendet, wenn 'LDAPGroupMappingType' auf 'SimpleMapping' steht  

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

> LDAP-Attributtyp für die Benutzergruppe. Kann name, id oder dn sein  

Standard: `name`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPUserGroupAttributeType=name
*/ ?>
```

## LDAPUserGroupAttribute

> LDAP-Attribut für die Benutzergruppe. Zum Beispiel employeetype. Wenn angegeben, werden LDAP-Benutzer  
> in derselben Gruppe wie auf dem LDAP-Server gespeichert.  
> Hängt vom Wert von LDAPUserGroupAttributeType ab:  
> name: Attribut mit dem Namen der Gruppe  
> id: Attribut mit der numerischen ID der Gruppe  
> dn: Attribut mit der DN der Gruppe  

Standard: `employeetype`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPUserGroupAttribute=employeetype
*/ ?>
```

## LDAPFirstNameAttribute

> LDAP-Attribut für den Vornamen. Normalerweise givenname  

Standard: `givenname`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPFirstNameAttribute=givenname
*/ ?>
```

## LDAPFirstNameIsCommonName

> Wird cn (common name) als Vorname verwendet, wird sn (Nachname) aus dem Vornamen entfernt  

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPFirstNameIsCommonName=false
*/ ?>
```

## LDAPLastNameAttribute

> LDAP-Attribut für den Nachnamen. Normalerweise sn  

Standard: `sn`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPLastNameAttribute=sn
*/ ?>
```

## LDAPEmailAttribute

> LDAP-Attribut für die E-Mail. Normalerweise mail  

Standard: `mail`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPEmailAttribute=mail
*/ ?>
```

## LDAPEmailEmptyAttributeSuffix

> Falls LDAP keine Mail zurückgibt, wird eine aus Anmeldename + E-Mail-Suffix gebildet, etwa '@example.com'  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
LDAPEmailEmptyAttributeSuffix=
*/ ?>
```

## Utf8Encoding

> Ob die LDAP-Kodierung utf-8 ist  

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ldap.ini.append.php
[LDAPSettings]
Utf8Encoding=false
*/ ?>
```

## KeepGroupAssignment

> Wenn 'enabled', können Sie LDAP-Benutzer in eine andere Gruppe verschieben, und sie werden  
> bei der nächsten Anmeldung nicht automatisch zurückverschoben (in die Gruppe, in die sie  
> laut Konfiguration gehören).  

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
