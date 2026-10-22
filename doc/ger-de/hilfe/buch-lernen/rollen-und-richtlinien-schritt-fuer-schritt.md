---
title: "Rollen und Richtlinien, Schritt für Schritt"
audience: admin, builder, developer
category: books
order: 240
summary: Wie das Rollensystem entscheidet, wer was tun darf: Eine Rolle ist eine Menge von Richtlinien (Modul, Funktion, Einschränkung), die Benutzern und Gruppen zugewiesen wird, mit den Funktionen des Moduls content und dem, was sich seit dem Buch geändert hat.
keywords: Rolle, Richtlinie, Modul, Funktion, Einschränkung, Zuweisung, Berechtigung, Zugriff, anonym, Buch Learning, role, policy, module, function, limitation, assignment, permission, access, anonymous, content read, content create, Learning book
type: help
---
# Rollen und Richtlinien, Schritt für Schritt

Das Berechtigungssystem beruht auf **Rollen**. Das unterscheidet sich von einem Dateisystem, in dem Berechtigungen an
einzelnen Dateien und Ordnern hängen. Das Rollensystem kann trotzdem so feinkörnig werden, bis hinunter zu einem Knoten
oder einem Knoten mit seinen Unterelementen, und es geht weiter: Es entscheidet auch, welche **Funktionen der Module**
ein Benutzer verwenden darf. Ein normaler Benutzer darf vielleicht Kommentare zu Artikeln hinzufügen, aber keine Artikel
einreichen.

## Die Bestandteile

- Eine **Rolle** ist eine benannte Menge von Richtlinien.
- Eine **Richtlinie** nennt ein **Modul**, eine **Funktion** davon und optionale **Einschränkungen**. Ohne Einschränkung
  gilt die Richtlinie für alles, was die Funktion tut. Die Richtlinie des Administrators lautet „alle Module, alle
  Funktionen“.
- Eine Rolle wird einem Benutzer oder einer Benutzergruppe **zugewiesen**, für die ganze Website oder begrenzt auf eine
  Sektion oder einen Teilbaum.
- Die Rechte eines Benutzers sind die Richtlinien aller Rollen des Benutzers und der Gruppen über ihm. Sobald eine
  Richtlinie eine Aktion erlaubt, ist sie erlaubt.

## Die Funktionen des Moduls content

Das Modul, das ein Builder am häufigsten verwendet, ist `content`. Die Funktionen und wodurch sie eingeschränkt werden
können (das Buch nannte sieben; heute sind es mehr):

| Funktion | Erlaubt einem Benutzer | Eingeschränkt durch |
|---|---|---|
| `read` | Inhalte zu sehen | Klasse, Sektion, Besitzer, Knoten, Teilbaum, Status |
| `create` | Inhalte unter einer Stelle anzulegen | Klasse, Sektion, Elternklasse, Elterntiefe, Knoten, Teilbaum, Sprache |
| `edit` | Inhalte zu ändern | Klasse, Sektion, Besitzer, Knoten, Teilbaum, Status, Sprache |
| `publish` | einen Entwurf zu veröffentlichen | Klasse, Sektion, Besitzer, Knoten, Teilbaum, Status |
| `remove` | Inhalte zu entfernen | Klasse, Sektion, Besitzer, Knoten, Teilbaum, Status |
| `translate` | Übersetzungen hinzuzufügen | Klasse, Sektion, Besitzer, Knoten, Teilbaum |
| `versionread`, `versionremove` | alte Versionen zu sehen oder zu entfernen | Klasse, Sektion, Besitzer, Status, Knoten, Teilbaum |
| `manage_locations`, `hide` | Orte hinzuzufügen und zu entfernen; zu verbergen und anzuzeigen | Klasse, Sektion, Knoten, Teilbaum |
| `bookmark`, `pendinglist`, `urltranslator`, `cleantrash`, `restore` | Lesezeichen zu verwenden; ausstehende Einträge zu sehen; URL-Aliase zu verwalten; den Papierkorb zu leeren und daraus wiederherzustellen | keine |

„Besitzer“ unterscheidet *selbst* von *beliebig*. Der Status bei `versionread` ist Entwurf, veröffentlicht, ausstehend,
abgelehnt oder archiviert. Die Listen der Einschränkungen werden aus `kernel/content/module.php` gelesen; die
Administration zeigt beim Hinzufügen einer Richtlinie genau die Einschränkungen, die die Installation anbietet.

Andere Module haben eigene Funktionen, zum Beispiel `class` (der Klasseneditor), `notification` (verwenden,
administrieren), `package`, `role`, `setup`, `shop`, `user` (anmelden, registrieren, Passwort), `workflow`, `trigger`,
`state`, `section`, `url`, `search`, `ezinfo`, `layout` (das Seitenlayout wechseln, etwa zu einem Drucklayout), `rss`.
Der Zugriff auf ein Kernel-Modul lässt sich, wie das Buch sagte, auch außerhalb des Rollensystems mit Einstellungen
abschalten.

## So gehen Sie vor

Die Schritte stehen in [Rollen, Richtlinien und nicht aktivierte Benutzer](../../anleitungen/rollen-und-richtlinien.md): die
Rollenliste lesen, eine Rolle mit eingeschränkten Richtlinien anlegen, die Richtlinien ordnen, die Rolle zuweisen.
Faustregeln, die auch das Buch nennt:

1. Gehen Sie davon aus, was jede Gruppe tun muss, nicht davon, wer jede Person ist.
2. Geben Sie der Rolle **Anonymous** so wenig wie möglich (Lesen öffentlicher Inhalte und vielleicht Registrieren).
3. Bevorzugen Sie eine Einschränkung auf Sektion oder Teilbaum gegenüber einer Klassenliste, wenn eine Gruppe in einem
   Bereich arbeitet.
4. Melden Sie sich nach dem Ändern einer Rolle als Mitglied der Gruppe an (ein Testkonto) und probieren Sie die Arbeit
   aus.

Siehe auch [Sektionen, Rollen und Zugriff für Builder](../erweiterungsentwicklung/teil-2-builder/sektionen-rollen-und-zugriff-fuer-builder.md)
und, in der Beispielsite, [Benutzer, Gruppen und Rollen](benutzer-gruppen-und-rollen-im-beispiel.md).

Weiter: [Eine Inhaltsklasse anlegen](eine-inhaltsklasse-anlegen.md).

## Quelle und Lizenz

Bearbeitet nach Kapitel 2, „Content Management with eZ publish“, von *Learning eZ publish: Building Content Management Solutions* von Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding und Ben Pirt (zusätzliches Material von Bruce Morrison), zweite Auflage, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x und Graham
Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren Version, ohne
unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: umgestellt und gekürzt; Einstellungen, Beschriftungen, Datentypen, Tags und URLs gegen Exponential 6 geprüft und korrigiert; Links auf die aktuellen Anleitungen ergänzt.
