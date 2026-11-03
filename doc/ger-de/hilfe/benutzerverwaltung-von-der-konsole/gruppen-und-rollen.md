---
title: "Gruppen und Rollen"
audience: admin, developer, hosting
category: administration
order: 6
summary: exp:user:groups listet, legt an, benennt um, verschiebt und löscht Benutzergruppen; exp:user:roles listet, weist zu und entfernt Rollen eines Benutzers oder einer Gruppe, wahlweise auf einen Teilbaum oder eine Sektion eingeschränkt; exp:user:whoami sagt, als welches Konto die Konsole handelt.
keywords: Benutzergruppen, Gruppe anlegen, Gruppe umbenennen, Gruppe verschieben, Gruppe löschen, Rollenzuweisung, Rolle zuweisen, Rolle entfernen, Teilbaum-Einschränkung, Sektions-Einschränkung, wer bin ich, Administrator, exp:user:groups, exp:user:roles, exp:user:whoami, user groups, assign role, --as, UserCreatorID
type: help
---
# Gruppen und Rollen

Eine Gruppe nennen Sie mit Knoten-ID oder Namen, eine Rolle mit Namen oder ID. Haben zwei Gruppen denselben Namen,
nehmen Sie die Knoten-ID.

## Was Sie am Ende haben

Einen Baum von Benutzergruppen, der zur Website passt, die richtigen Rollen an den richtigen Konten und Gruppen, und
Gewissheit, als welches Konto die Konsole handelt.

## Benutzergruppen

```bash
./console exp:user:groups
./console exp:user:groups list Editors
./console exp:user:groups create --name="Regional editors" --parent=Editors --apply
./console exp:user:groups rename "Regional editors" --name="Local editors" --apply
./console exp:user:groups move "Local editors" --to=Members --apply
./console exp:user:groups delete "Local editors" --apply
```

| Aktion | Was sie tut |
|---|---|
| `list` (Vorgabe) | der Baum der Benutzergruppen unter einer Gruppe (Vorgabe: die Spitze des Benutzerbaums): Knoten-ID, Name, direkte Mitglieder, Untergruppen |
| `create` | eine neue Gruppe mit dem Namen `--name`, unter `--parent` (Vorgabe: die Spitze des Benutzerbaums) |
| `rename <group>` | gibt der Gruppe den Namen `--name` |
| `move <group>` | verschiebt die Gruppe unter die Gruppe `--to` |
| `delete <group>` | entfernt die Gruppe, wie die Administration sie entfernt |

`delete` verweigert die Spitze des Benutzerbaums und eine Gruppe, die noch Mitglieder oder Untergruppen hat.
Verschieben Sie diese zuerst mit `exp:user:edit --add-group --remove-group` oder `exp:user:groups move`.

## Rollen

```bash
./console exp:user:roles list jane
./console exp:user:roles list group:Editors
./console exp:user:roles assign Editor group:"Local editors" --subtree=2345 --apply
./console exp:user:roles assign Anonymous jane --section=standard --apply
./console exp:user:roles unassign Editor group:"Local editors" --subtree=2345 --apply
```

Das Ziel ist ein Benutzer (Anmeldename, E-Mail-Adresse oder ID) oder `group:<Knoten-ID oder Name>`.

| Aktion | Was sie tut |
|---|---|
| `list <target>` | die Rollenzuweisungen des Benutzers oder der Gruppe, jede mit ihrer Einschränkung und ihrer Herkunft (das Konto oder eine Gruppe darüber) |
| `assign <role> <target>` | weist die Rolle zu; `--subtree=<Knoten-ID>` oder `--section=<Sektions-ID oder Bezeichner>` schränkt sie ein |
| `unassign <role> <target>` | entfernt die Rolle; mit `--subtree` oder `--section` nur diese eingeschränkte Zuweisung |

Das Audit hält `access.role.assign` und `access.role.unassign` fest. Die Rollen-Caches werden wie in der
Administration geleert; die Änderung gilt also ab der nächsten Anfrage.

**Hinweis:** Eine Rolle, die einer Gruppe zugewiesen ist, gilt für jedes Mitglied in jeder Tiefe. Nehmen Sie lieber
Gruppen als einzelne Konten: eine Person, die die Aufgabe wechselt, wechselt dann nur die Gruppe.

## Als wer die Konsole handelt

```bash
./console exp:user:whoami
./console exp:user:whoami --as=jane
```

Der Befehl nennt das Konto, als das die Konsole handelt: den Benutzer aus `[UserSettings] UserCreatorID` oder den aus
`--as`. Er listet seine Rollen auf und sagt, ob es alle Rechte hat.

Die Befehle prüfen die Richtlinien dieses Kontos nicht. Wer die Konsole auf dem Server ausführen darf, kann jedes
Konto verwalten; geben Sie Shell-Zugang also nur den Personen, die die Website verwalten. `--as` entscheidet, wessen
Name im Audit steht.

## Verwandte Seiten

- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md): was eine Rolle und eine Einschränkung sind.
- [Einen neuen Redakteur einrichten](einen-neuen-redakteur-einrichten.md): eine Gruppe und eine Rolle für eine neue
  Person.
- [Konten finden, anlegen und ändern](konten-finden-anlegen-und-aendern.md#ein-konto-ändern): Gruppenmitgliedschaften
  eines Kontos hinzufügen und entfernen.
