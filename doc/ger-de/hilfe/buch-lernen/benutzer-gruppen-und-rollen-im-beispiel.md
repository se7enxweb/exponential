---
title: "Benutzer, Gruppen und Rollen im Beispiel"
audience: admin, builder
category: books
order: 330
summary: Sektionen, Benutzergruppen und Rollendefinitionen für das Vereinsportal, Schritt für Schritt, mit den Einstellungen (Standard-Benutzergruppe, Richtlinien, die die Prüfung überspringen) und dem, was Sie heute prüfen sollten.
keywords: Sektionen, Benutzergruppen, Rollen, anonym, Besucher, Mitglieder, Vorstand, Buch Learning, sections, user groups, roles, anonymous, visitors, members, management, DefaultUserPlacement, PolicyOmitList, Learning book
type: help
---
# Benutzer, Gruppen und Rollen im Beispiel

Dies setzt [Eine Beispielsite planen](eine-beispielsite-planen.md) fort. Berechtigungen lassen sich leichter umsetzen,
wenn Sektionen sie tragen.

## Sektionen

Für das Vereinsportal legt das Buch vier an, weist jede dem obersten Knoten ihres Zweigs zu (neue Unterelemente erben
sie) und prüft dann das Ergebnis:

| Sektion | Enthält | Wer darf was |
|---|---|---|
| Restricted | den Bereich des Vorstands | nur der Vorstand |
| Forums | die Diskussionsforen | alle lesen; Mitglieder schreiben Beiträge |
| Members | Angaben zu den Mitgliedern | Mitglieder lesen gegenseitig; nicht die Öffentlichkeit |
| Visitors | Home, Documents, Calendar, Forum | alle, auch anonyme Besucher |

Unter [Sektionen](../../anleitungen/sektionen.md) erfahren Sie, wie Sie sie anlegen, und finden **Inhalt zuweisen**.

## Benutzergruppen

Es gibt vier Arten von Benutzern, und jede erhält eine Gruppe: **anonyme** (nicht angemeldet; der Benutzer Anonymous),
**Besucher** (registriert, keine Mitglieder), **Mitglieder** (vom Vorstand eingetragen) und der **Vereinsvorstand**.
Ein neu registrierter Benutzer kommt in die Standardgruppe. Diese Gruppe ist eine Einstellung,
`site.ini [UserSettings] DefaultUserPlacement`, die Knoten-ID der Gruppe:

```ini
[UserSettings]
DefaultUserPlacement=12
```

Der mitgelieferte Wert ist eine Knoten-ID der Standardinstallation; ändern Sie ihn in `settings/override/` und setzen
Sie ihn auf die Knoten-ID Ihrer Besuchergruppe.

## Rollen

Jede Gruppe erhält eine Rolle:

- **Anonymous:** `content/read`, eingeschränkt auf die Sektion Visitors, und das Recht, sich anzumelden. Das
  Anmelderecht lässt sich auch außerhalb des Rollensystems vergeben: `site.ini [RoleSettings] PolicyOmitList[]=user/login`
  (die mitgelieferte Einstellung überspringt die Prüfung auch für `user/logout` und `user/register`).
- **Besucher:** dasselbe wie anonyme Besucher, dazu Lesezeichen und Benachrichtigungen.
- **Mitglieder:** Lesen von Members und Forums; Forumsbeiträge und Themen anlegen; Dokumente und Bilder einreichen, die
  der Vorstand vor der Veröffentlichung prüft ([Workflows](workflows-und-trigger.md)).
- **Vorstand:** Lesen, Anlegen, Bearbeiten und Entfernen in allen Sektionen, auch in der geschützten.

[Rollen und Richtlinien, Schritt für Schritt](rollen-und-richtlinien-schritt-fuer-schritt.md) erklärt das Formular für Richtlinien.
Melden Sie sich nach der Einrichtung als Benutzer jeder Gruppe an und gehen Sie durch, was dieser Benutzer tun können
soll und was nicht.

Weiter: die Templates. [Grundsätze der Templates](grundsaetze-der-templates.md) eröffnet das nächste Kapitel.

## Quelle und Lizenz

Bearbeitet nach Kapitel 2, „Content Management with eZ publish“, von *Learning eZ publish: Building Content Management Solutions* von Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding und Ben Pirt (zusätzliches Material von Bruce Morrison), zweite Auflage, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x und Graham
Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren Version, ohne
unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: umgestellt und gekürzt; Einstellungen, Beschriftungen, Datentypen, Tags und URLs gegen Exponential 6 geprüft und korrigiert; Links auf die aktuellen Anleitungen ergänzt.
