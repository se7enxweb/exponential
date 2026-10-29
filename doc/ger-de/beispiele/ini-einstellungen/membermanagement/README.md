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

> Mitgliederverwaltung: welche Benutzergruppen die Benutzerkonten welcher anderen Gruppen verwalten dürfen, mit welchen Richtlinien, und  
> welche Gruppen niemand über eine solche Regel verwaltet. Gelesen vom Rollen-Schritt des Setup-Assistenten und vom Kickstarter (eine neue  
> Installation), von bin/php/addmembermanagement.php --apply (eine bestehende Installation; ohne --apply zeigt es nur,  
> was es ändern würde) und von der Zugriffsprüfung, die die Konten der geschützten Gruppen außer Reichweite hält  
> (doc/eng-us/bc/6.0/member-management.md).  
>
> Die Standardwerte geben der Gruppe Editors die Rolle "Member management": die Konten unterhalb von Members anlegen, bearbeiten (Name, E-Mail, Passwort), aktivieren und  
> deaktivieren, verschieben und entfernen. Die Konten von Administrator users und Editors bleiben bei den  
> Administratoren, und niemand erhält dadurch Rollen, Sektionen oder Setup.  
>
> Gruppen werden in einer dieser Formen benannt, damit dieselbe Datei in jede Installation passt:  
>   remote:<remote id>   die remote id des Gruppenobjekts oder seines Haupt-Knotens (die Standardgruppen behalten ihre überall)  
>   node:<node id>       der Knoten der Gruppe  
>   name:<name>          die Benutzergruppe der obersten Ebene mit diesem Namen unterhalb des Benutzer-Roots  
> Regeln hinzufügen oder ändern in settings/override/membermanagement.ini.append.php oder in den Einstellungen einer Erweiterung.  

| Block | Einstellungen |
|---|---|
| [MemberManagementSettings](MemberManagementSettings.md) | 3 |
| [MemberGuardSettings](MemberGuardSettings.md) | 4 |
| [MemberRule_editors_manage_members](MemberRule_editors_manage_members.md) | 5 |
| [MemberPolicySet_member_management](MemberPolicySet_member_management.md) | 1 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/membermanagement.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
