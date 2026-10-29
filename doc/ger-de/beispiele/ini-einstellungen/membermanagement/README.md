---
title: membermanagement.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von membermanagement.ini.
keywords: Beispiele, Referenz, membermanagement.ini, examples, reference
type: generated
generated: exp:help:examples
---
# membermanagement.ini

Die ausgelieferten Einstellungen von `settings/membermanagement.ini`: 4 Blöcke, 13 Einstellungen.

> Member management: which user groups may manage the user accounts of which other groups, with which policies, and  
> which groups nobody manages through such a rule. Read by the setup wizard's role step and the kickstarter (a new  
> installation), by bin/php/addmembermanagement.php --apply (an installation that exists; without --apply it only  
> shows what it would change), and by the access check that keeps the protected groups' accounts out of reach  
> (doc/eng-us/bc/6.0/member-management.md).  
>
> The defaults give the Editors group the role "Member management": create, edit (name, e-mail, password), enable and  
> disable, move and remove the accounts below Members. The accounts of Administrator users and Editors stay with the  
> administrators, and nobody gets roles, sections or setup from it.  
>
> Groups are named in one of these forms, so the same file fits every installation:  
>   remote:<remote id>   the group object's or its main node's remote id (the stock groups keep theirs everywhere)  
>   node:<node id>       the group's node  
>   name:<name>          the top-level user group of that name below the user root  
> Add or change rules in settings/override/membermanagement.ini.append.php or in an extension's settings.  

| Block | Einstellungen |
|---|---|
| [MemberManagementSettings](MemberManagementSettings.md) | 3 |
| [MemberGuardSettings](MemberGuardSettings.md) | 4 |
| [MemberRule_editors_manage_members](MemberRule_editors_manage_members.md) | 5 |
| [MemberPolicySet_member_management](MemberPolicySet_member_management.md) | 1 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/membermanagement.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
