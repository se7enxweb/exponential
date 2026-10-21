---
title: Eine Redakteursrolle einer Abteilung für einen Teilbaum anlegen
audience: government, admin
category: public-sector
order: 20
summary: Schritt für Schritt - eine Rolle „Department editor“ anlegen und der Benutzergruppe einer Abteilung zuweisen, sodass ihre Redakteure nur unterhalb des eigenen Ordners der Abteilung anlegen, bearbeiten und veröffentlichen können.
keywords: Redakteur einer Abteilung, Rolle anlegen, Teilbaum, Mit Einschränkungen zuordnen, Neue Rolle, Neue Richtlinie, Eingeschränkten Zugriff gewähren, Benutzergruppe, Abteilung, Behörde, Redakteure, department editor, create role, subtree, Assign with limitation, New role, New policy, Grant limited access, user group, department, agency, editors
type: help
---
# Eine Redakteursrolle einer Abteilung für einen Teilbaum anlegen

Diese Aufgabe ist für Administratoren. Am Ende können die Redakteure einer Abteilung an den Seiten unterhalb des
Ordners ihrer Abteilung arbeiten und nirgendwo sonst. Die Gestaltung dahinter steht auf
[Rollengestaltung: Redakteure der Abteilungen](../../../../../eng-us/help/government/operations/people-and-permissions/role-design-department-editors.md);
die Seiten sind die von **Benutzer > Rollen und Richtlinien** ([Anleitung zu Rollen](../../../../anleitungen/rollen-und-richtlinien.md)).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Die Rolle anlegen](#die-rolle-anlegen)
3. [Sie der Abteilung zuweisen](#sie-der-abteilung-zuweisen)
4. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
5. [Fallstricke](#fallstricke)
6. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Ein Konto mit dem Modul `role` (normalerweise ein Administrator). Mit `PreventPrivilegeEscalation=enabled` (der
  Voreinstellung) können Sie nur vergeben, was Ihre eigenen Rollen abdecken.
- Der Ordner der Abteilung existiert im Inhaltsbaum, und die Benutzergruppe der Abteilung existiert
  ([Eine Benutzergruppe für eine Abteilung anlegen](eine-benutzergruppe-fuer-eine-abteilung-anlegen.md)).
- Eine Entscheidung, welche Inhaltsklassen die Abteilung anlegen darf.

## Die Rolle anlegen

1. Öffnen Sie **Benutzer > Rollen und Richtlinien** und klicken Sie auf **Neue Rolle**. Nennen Sie sie
   `Department editor`.
2. Klicken Sie auf **Neue Richtlinie**. Schritt 1, Modul: `content`. Schritt 2, Funktion: `read`.
   **Eingeschränkten Zugriff gewähren**. Schritt 3: Sektion `Standard` und `Media`. **OK**.
3. **Neue Richtlinie**: `content` / `create`, **Eingeschränkten Zugriff gewähren**, Klasse: Ihre Klassen für Seiten,
   Artikel, Dateien und Bilder. **OK**.
4. **Neue Richtlinie**: `content` / `edit`, **Eingeschränkten Zugriff gewähren**, Sektion `Standard`, `Media`. **OK**.
5. **Neue Richtlinie**: `content` / `hide` und dann `content` / `remove`, jeweils auf die Sektion `Standard`
   eingeschränkt.
6. **Neue Richtlinie**: `content` / `versionread` und `content` / `diff`, Sektion `Standard`.
7. **Neue Richtlinie**: `content` / `dashboard` (**Vollen Zugriff gewähren** für die Funktion).
8. Der Editor listet die Richtlinien als Sätze und sagt **Nicht gespeicherte Änderungen**. Klicken Sie auf
   **Speichern**.

Die Anmeldung (`user/login`) und `user/password` gehören in eine kleine Rolle für alle Mitarbeiter, ohne Einschränkung
zugewiesen, damit die Anmeldung nie von einer Zuweisung auf einen Teilbaum abhängt.

## Sie der Abteilung zuweisen

1. Wählen Sie auf der Seite der Rolle unter der Liste der Zuweisungen in der Auswahl der Einschränkung **Teilbaum**.
2. Klicken Sie auf **Mit Einschränkungen zuordnen**, wählen Sie im Inhaltsbrowser den obersten Ordner der Abteilung und
   klicken Sie auf **Auswählen**.
3. Wählen Sie die Benutzergruppe der Abteilung und klicken Sie auf **Auswählen**.
4. Die Seite der Rolle listet die Gruppe mit *Nur im Teilbaum ...*.

Wiederholen Sie die Schritte 1 bis 3 für jede weitere Abteilung: eine Rolle, eine eingeschränkte Zuweisung je
Abteilung.

## Prüfen, ob es funktioniert hat

1. Melden Sie sich als Mitglied der Gruppe in einem privaten Browserfenster an.
2. Öffnen Sie eine Seite der Abteilung: **Bearbeiten** wird angeboten. Öffnen Sie eine Seite einer anderen Abteilung:
   nicht.
3. Tippen Sie die Adresse zum Bearbeiten einer Seite einer anderen Abteilung ein: Die Anfrage wird abgelehnt, und das
   Audit-Protokoll zeichnet `access.permission.refused` auf.
4. Auf der Seite der Rolle zählt **Betroffene Benutzer insgesamt** die Mitglieder der Gruppe.

Änderungsnachweis: Name der Rolle, ihre Richtlinien (kopieren Sie die Sätze von der Seite der Rolle), die Gruppe, der
Teilbaum, das Datum und wer den Antrag genehmigt hat.

## Fallstricke

- **Zuweisen** statt **Mit Einschränkungen zuordnen** gibt die Rolle überall.
- Inhalte, die als zusätzlicher Ort in den Ordner der Abteilung verlinkt sind, bleiben außerhalb der Reichweite der
  Redakteure, wenn ihr Hauptort anderswo liegt.
- Wenn Abteilungen nicht ohne Freigabe veröffentlichen dürfen, nehmen Sie die Gruppen der Rolle in den
  Freigabe-Workflow auf, nicht in dessen ausgenommene Gruppen
  ([Einen Freigabe-Workflow für eine Sektion einrichten](einen-freigabe-workflow-fuer-eine-sektion-einrichten.md)).

## Verwandte Seiten
- [Eine Rolle mit einer Einschränkung auf einen Teilbaum zuweisen](eine-rolle-mit-einer-einschraenkung-auf-einen-teilbaum-zuweisen.md)
- [Eine Rolle für eine neue Abteilung kopieren](eine-rolle-fuer-eine-neue-abteilung-kopieren.md)
- [Sektionen und Einschränkungen auf Teilbäume](../../../../../eng-us/help/government/operations/people-and-permissions/sections-and-subtree-limits.md)
- [Prüfen, was eine Rolle erlaubt](pruefen-was-eine-rolle-erlaubt.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
