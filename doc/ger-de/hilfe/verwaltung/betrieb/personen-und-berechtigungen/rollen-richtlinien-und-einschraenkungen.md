---
title: Rollen, Richtlinien und Einschränkungen
audience: government, admin, project-manager
category: public-sector
order: 20
summary: Wie Exponential entscheidet, wer was tun darf - Rollen aus Richtlinien, Richtlinien eingeschränkt nach Sektion, Teilbaum, Klasse, Eigentümer, Sprache, Siteaccess oder Zustand, und Rollen, die Gruppen zugewiesen werden - erklärt für Web-Teams im öffentlichen Sektor, die jedes Zugriffsrecht dokumentieren und begründen müssen.
keywords: Rollen, Richtlinien, Einschränkungen, Berechtigungen, Zugriffskontrolle, geringste Rechte, Benutzergruppen, Rollenzuweisung, Sektionseinschränkung, Teilbaum-Einschränkung, PreventPrivilegeEscalation, Zugriffsrechte, Autorisierung, roles, policies, limitations, permissions, access control, least privilege, user groups, role assignment, section limitation, subtree limitation, access rights, authorization
type: help
---
# Rollen, Richtlinien und Einschränkungen

Diese Seite richtet sich an den Administrator, der den Zugang der Beschäftigten einrichtet, und an die Projektleitung, die ihn einem Prüfer
oder einer oder einem Informationssicherheitsbeauftragten erklären muss. Sie beschreibt das Berechtigungsmodell von Exponential so, wie der
Leitfaden [Rollen, Richtlinien und nicht aktivierte Benutzer](../../../../anleitungen/rollen-und-richtlinien.md) und die Moduldefinitionen in
`kernel/<module>/module.php` es beschreiben, und ergänzt die Dokumentationsgewohnheiten, die öffentliche Stellen üblicherweise brauchen.

## Inhalt
1. [Das Modell in vier Sätzen](#das-modell-in-vier-sätzen)
2. [Richtlinien: Modul, Funktion, Einschränkungen](#richtlinien-modul-funktion-einschränkungen)
3. [Einschränkungen, die Sie verwenden werden](#einschränkungen-die-sie-verwenden-werden)
4. [Rollen zuweisen](#rollen-zuweisen)
5. [Eingebaute Schutzmechanismen](#eingebaute-schutzmechanismen)
6. [Zugriff für ein Audit dokumentieren](#zugriff-für-ein-audit-dokumentieren)
7. [Verwandte Seiten](#verwandte-seiten)

## Das Modell in vier Sätzen

- Eine **Rolle** ist eine Menge von **Richtlinien**; eine Rolle bewirkt nichts, bis sie Benutzern oder Benutzergruppen **zugewiesen** wird.
- Eine **Richtlinie** erlaubt ihren Inhabern, ein Modul oder eine Funktion eines Moduls zu nutzen, gegebenenfalls nur innerhalb von
  **Einschränkungen**.
- Ein Benutzer hat jede Richtlinie jeder Rolle, die ihm oder einer Gruppe über ihm zugewiesen ist: Richtlinien **fügen** Zugriff immer nur
  **hinzu**. Es gibt keine Verbotsrichtlinie.
- Niemand erhält etwas, das ihm keine Rolle gibt, daher ist der Ausgangspunkt jedes Entwurfs „kein Zugriff“.

Alle Rollenseiten liegen unter **Benutzer > Rollen und Richtlinien** (`/role/list`). Jede Änderung im Rolleneditor ist ein Entwurf, bis Sie
auf **Speichern** klicken ([der Leitfaden, Abschnitt 3](../../../../anleitungen/rollen-und-richtlinien.md#3-eine-rolle-mit-eingeschränkten-richtlinien-anlegen)).

## Richtlinien: Modul, Funktion, Einschränkungen

Eine Richtlinie nennt ein Modul (`content`, `user`, `setup`, `section`, `state`, `audit` ...) und entweder alle seine Funktionen oder eine
Funktion. Die für Beschäftigte wichtigsten Funktionen, wie im Kernel definiert:

| Modul | Funktionen (Auswahl) |
|---|---|
| `content` | `read`, `create`, `edit`, `publish`, `remove`, `hide`, `move`, `manage_locations`, `translate`, `versionread`, `versionremove`, `diff`, `pdf`, `dashboard`, `pendinglist`, `restore`, `cleantrash`, `jobs`, `publish_without_notification` |
| `user` | `login`, `password`, `preferences`, `selfedit`, `register`, `activation`, `hygiene`, `deactivate` |
| `setup` | `administrate`, `logs`, `managelogs`, `unmasklogs`, `managecache`, `managecronjobs`, `system_info`, `setup`, `preload`, `install` |
| `section` | `view`, `edit`, `assign` |
| `state` | `administrate`, `assign` |
| `audit` | `read`, `manage` |

Die Module `role` und `workflow` haben keine getrennten Funktionen: Eine Richtlinie über sie gibt das ganze Modul. Eine Richtlinie über das
Modul `role` lässt ihre Inhaber Rollen ändern, was die Rollenliste gelb als *Kann Rollen ändern* markiert; eine Richtlinie über alle Module
ist *Voller Zugriff*, rot markiert.

Für Freigaben zählt eine Tatsache: Im Bearbeitungsformular entscheidet `content/edit`, ob ein Benutzer einen Entwurf veröffentlichen darf
([Ohne Benachrichtigung veröffentlichen](../../../../funktionen/6.0/veroeffentlichen-ohne-benachrichtigung.md)). Eine Freigabe vor dem Veröffentlichen
wird daher durch einen Workflow durchgesetzt, nicht durch das Vorenthalten eines Veröffentlichungsrechts; siehe
[Vier-Augen-Prinzip beim Veröffentlichen](../redaktionelle-workflows/vier-augen-prinzip-beim-veroeffentlichen.md).

## Einschränkungen, die Sie verwenden werden

| Einschränkung | Angeboten von | Bedeutet |
|---|---|---|
| Sektion | den meisten `content`-Funktionen, `section/assign`, `state/assign` | nur Objekte in diesen Sektionen |
| Teilbaum | den meisten `content`-Funktionen | nur Objekte an oder unterhalb dieser Platzierungen |
| Knoten | den meisten `content`-Funktionen | nur genau diese Platzierungen |
| Klasse | den meisten `content`-Funktionen | nur Objekte dieser Inhaltsklassen |
| Eigentümer / Gruppe | `read`, `edit`, `remove` ... | nur Objekte, die dem Benutzer (oder seiner Gruppe) gehören |
| Sprache | `create`, `edit`, `publish`, `translate`, `hide` | nur diese Übersetzungen |
| SiteAccess | `user/login` | darf sich nur in diesen Siteaccesses anmelden |
| `StateGroup_<group>` | `read`, `edit`, `publish`, `manage_locations`, `remove`, `state/assign` | nur Objekte in diesen Objektzuständen |
| NewState, NewSection | `state/assign`, `section/assign` | welche Zustände oder Sektionen der Benutzer setzen darf |

Mehrere Einschränkungen einer Richtlinie müssen alle erfüllt sein; mehrere Werte einer Einschränkung sind Alternativen
([Leitfaden, Abschnitt 3](../../../../anleitungen/rollen-und-richtlinien.md#3-eine-rolle-mit-eingeschränkten-richtlinien-anlegen)). Sektionen werden unter
[Sektionen und Teilbaum-Einschränkungen](sektionen-und-teilbaum-einschraenkungen.md) erklärt, Zustände unter
[Objektzustände in Berechtigungen](objektzustaende-in-berechtigungen.md).

## Rollen zuweisen

Auf der Seite einer Rolle gibt **Zuweisen** die Rolle Benutzern oder Gruppen; **Mit Einschränkungen zuordnen** gibt sie nur innerhalb eines
Teilbaums oder einer Sektion. Bevorzugen Sie Gruppen: Ein neues Mitglied erhält die Rolle sofort, und wer austritt, verliert sie, wenn er aus
der Gruppe verschoben wird. Eine Rolle kann mehrmals mit verschiedenen Einschränkungen zugewiesen werden, zum Beispiel dieselbe Rolle
„Fachbereichsredaktion“ an die Gruppe jedes Fachbereichs, eingeschränkt auf den Teilbaum dieses Fachbereichs
([Leitfaden, Abschnitt 5](../../../../anleitungen/rollen-und-richtlinien.md#5-die-rolle-zuweisen)).

## Eingebaute Schutzmechanismen

- **Keine Rechteausweitung.** Mit `site.ini [RoleSettings] PreventPrivilegeEscalation=enabled` (Standard) kann ein Benutzer, der Rollen
  bearbeiten darf, nur vergeben, was seine eigenen Rollen abdecken; eine weitergehende Richtlinie wird mit *Die Richtlinie wurde nicht
  hinzugefügt* oder *Die Rolle wurde nicht gespeichert* abgewiesen ([Leitfaden, „Wer das tun darf“](../../../../anleitungen/rollen-und-richtlinien.md#wer-das-darf)).
- **Alles wird erfasst.** Rollenzuweisungen und Änderungen an Berechtigungen sind Ereignisse des Kanals `access` des
  [Prüfprotokolls](../../../../funktionen/6.0/pruefprotokoll.md), und die Vergabe einer Administratorrolle löst die Alarmregel
  `admin_role_granted` aus (`settings/audit.ini`).
- **Links folgen den Berechtigungen.** Die Administration verbirgt Links, die ein Benutzer nicht öffnen könnte
  ([Administrationslinks folgen den Berechtigungen](../../../../funktionen/6.0/admin-links-folgen-den-berechtigungen.md)).

## Zugriff für ein Audit dokumentieren

Öffentliche Stellen werden üblicherweise gebeten zu zeigen, wer was tun darf und warum. Ein praktischer Nachweis je Rolle:

| Feld | Beispiel |
|---|---|
| Name und Zweck der Rolle | „Fachbereichsredaktion: bearbeitet und veröffentlicht die Seiten eines Fachbereichs“ |
| Verantwortlich für die Rolle | die Leitung des Web-Teams (eine Funktion, nicht der Name einer Person) |
| Richtlinien | kopiert von der Rollenseite, die jede Richtlinie als Satz schreibt |
| Zugewiesen an | Benutzergruppen, mit Einschränkungen |
| Überprüfungstermin | wann die nächste [Überprüfung der Administrationszugänge](ueberpruefung-administrativer-zugaenge.md) sie betrachtet |

Die Rollenseite zeigt die Richtlinien als Sätze (*Darf Inhalte lesen, in der Sektion Standard, der Klassen Artikel und Ordner*) und zählt
die Benutzer, die die Rolle erreicht, was sie zu einer guten Quelle für diesen Nachweis macht.

## Verwandte Seiten
- [Grundsätze der geringsten Rechte](prinzip-der-geringsten-rechte.md)
- [Funktionstrennung](funktionstrennung.md)
- [Prüfen, was eine Rolle erlaubt](../../aufgaben/personen-und-berechtigungen/pruefen-was-eine-rolle-erlaubt.md)
- [Rollen, Richtlinien und nicht aktivierte Benutzer](../../../../anleitungen/rollen-und-richtlinien.md)
- [Reihenfolge der Richtlinien einer Rolle](../../../../funktionen/6.0/reihenfolge-der-richtlinien-einer-rolle.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
