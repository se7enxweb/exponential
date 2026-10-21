---
title: "Lektion 2.5: Konten und Rollen der Teilnehmenden"
audience: trainer, admin
category: training
order: 25
summary: Legen Sie Konten der Teilnehmenden mit der passenden Rolle für den Kurs an, Redakteure in der Gruppe Editors, Administratoren in Administrator users, Test-E-Mail-Adressen unter example.invalid, und prüfen Sie vor dem Kurs, was ein Konto darf. 45 Minuten.
keywords: benutzerkonten, teilnehmende, rollen, rolle editor, administrator users, gruppe editors, example.invalid, testbenutzer, benutzer anlegen, rollenzuweisung, übungsumgebung, user accounts, learners, roles, Editor role, test users, create user, role assignment
type: help
---
# Lektion 2.5: Konten und Rollen der Teilnehmenden

Ein Kurs für Redakteure, der mit Administratorkonten unterrichtet wird, vermittelt das Falsche: Die Teilnehmenden
sehen Menüs, die ihre echten Konten nie zeigen werden. Eine gemeinsame Schulungs-Website braucht ein Konto pro
Teilnehmer, mit der Rolle, die zum Kurs passt. Diese Lektion zeigt, wie Sie solche Konten in der Administration
anlegen und wie Sie prüfen, was sie dürfen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Train the Trainer](../README.md) |
| Modul | [Modul 2: Die Übungsumgebung aufbauen](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Trainer, die eine gemeinsame Schulungs-Website vorbereiten |
| Voraussetzungen | [Lektion 2.4](die-websites-der-uebungsumgebung-bereitstellen.md); ein Administratorkonto auf der Schulungs-Website |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- in der Administration einen Benutzer in einer Benutzergruppe anlegen;
- die Gruppe und damit die Rollen wählen, die zum Kurs passen;
- für jedes Testkonto Adressen unter `example.invalid` verwenden, damit keine E-Mail eine echte Person erreicht;
- die Seite einer Rolle lesen, um zu sehen, welchen Gruppen sie zugewiesen ist und was sie erlaubt.

## Hinweise für Trainer

### So präsentieren Sie es

1. Öffnen Sie **Benutzer** im oberen Menü (3 Minuten). Die linke Spalte listet die Benutzergruppen auf, zum Beispiel
   **Administrator users**, **Editors**, **Members** und **Anonymous Users**, und unter **Zugangskontrolle** die
   Einträge **Rollen und Richtlinien**, **Nicht aktivierte Benutzer** und **Benutzerkonten prüfen**.
2. Erklären Sie das Modell (5 Minuten): Eine Rolle ist eine Menge von Richtlinien; eine Rolle bewirkt nichts, bis sie
   zugewiesen ist; weisen Sie Rollen Gruppen zu, nicht einzelnen Benutzern, damit jedes Mitglied, jetzt und später,
   die Rolle bekommt.
3. Öffnen Sie **Rollen und Richtlinien**, dann die Rolle **Editor**, und zeigen Sie die Gruppen, denen sie zugewiesen
   ist (3 Minuten).
4. Erklären Sie `example.invalid` (2 Minuten): Die Domain `.invalid` ist reserviert und kann nie E-Mails empfangen,
   sodass ein Testkonto nie eine E-Mail an einen Fremden schicken kann.

### Häufige Fragen

**Warum nicht für alle das Administratorkonto verwenden?** Ein gemeinsames Konto bedeutet ein gemeinsames Passwort,
eine gemeinsame Sammlung von Einstellungen und keine Möglichkeit, im Audit-Protokoll zu sehen, wer was getan hat.
Teilen Sie niemals das Administratorkonto.

**Können Teilnehmende eine Rolle nur in einem Teil des Baums haben?** Ja: Auf der Seite einer Rolle wendet
**Mit Einschränkungen zuordnen** die Rolle nur in einem Teilbaum oder einer Sektion an. Lektion 2.7 verwendet das für
gemeinsame Schulungsserver.

**Welche Felder hat ein Benutzer?** Der Inhaltstyp User hat **First name**, **Last name** und **User account** (mit
**Benutzername**, **E-Mail**, **Passwort** und **Passwort bestätigen**), dazu optionale Felder wie **Signature** und
**Image** auf einer Website, die aus den Seitenpaketen von 7x installiert wurde.

### Stolperfallen

- **Schwache Passwörter.** Die Website erzwingt eine Mindestlänge (`site.ini [UserSettings] MinPasswordLength`, 10 in
  den mitgelieferten Einstellungen). Bereiten Sie Passwörter vor, die sie erfüllen.
- **Echte Adressen.** Tragen Sie niemals die private E-Mail-Adresse einer Teilnehmerin oder eines Teilnehmers in eine
  Schulungs-Website ein. Verwenden Sie `example.invalid`.
- **Übrig gebliebene Konten.** Entfernen Sie Schulungskonten nach dem Kurs (Lektion 2.6).

## Praktische Übung

Auf Ihrer Schulungsinstallation, als Administrator angemeldet:

1. Öffnen Sie **Benutzer** im oberen Menü und dann die Gruppe **Editors** in der linken Spalte.
2. Klicken Sie unter **Unterelemente** auf **Neu erstellen** und wählen Sie **User**.
3. Füllen Sie **First name** `Learner`, **Last name** `One`, **Benutzername** `learner01`, **E-Mail**
   `learner01@example.invalid` und ein **Passwort** mit mindestens 10 Zeichen (zweimal) aus.
4. Klicken Sie auf **Zur Veröffentlichung schicken**. Der neue Benutzer erscheint unter den Unterelementen der Gruppe.
5. Öffnen Sie **Benutzer > Rollen und Richtlinien** und öffnen Sie die Rolle **Editor**. Suchen Sie die Gruppen, denen
   sie zugewiesen ist, und prüfen Sie, dass **Editors** darunter ist. (Wenn nicht, weist Ihr Seitenpaket Rollen anders
   zu: Notieren Sie, welche Rolle die Gruppe Editors hat.)
6. Melden Sie sich ab und als `learner01` in der Administration an. Notieren Sie, welche Einträge des oberen Menüs im
   Vergleich zum Administrator fehlen.
7. Melden Sie sich ab und wieder als Administrator an. Wiederholen Sie die Schritte 2 bis 4 für `learner02`, wenn Sie
   Zeit haben.

## Erwartetes Ergebnis

- `learner01` existiert in der Gruppe Editors mit einer Adresse unter `example.invalid`.
- Als `learner01` angemeldet, zeigt die Administration weniger Menüs als für den Administrator (der Bereich Setup zum
  Beispiel steht der Rolle Editor nicht zur Verfügung).
- Der Trainer prüft die Benutzerliste: keine echte E-Mail-Adresse, keine gemeinsamen Konten.

## Quiz

1. Warum weist man Rollen Gruppen zu statt einzelnen Benutzern?
2. Warum verwendet man `example.invalid` für Testkonten?
3. Welche Felder enthält der Teil User account eines Benutzers?
4. Wo sehen Sie, welchen Gruppen eine Rolle zugewiesen ist?
5. Richtig oder falsch: Ein gemeinsames Administratorkonto ist für einen Kurs in Ordnung.

<details>
<summary>Antworten</summary>

1. Jedes Mitglied der Gruppe, jetzt und später, bekommt die Rolle ([Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md#5-die-rolle-zuweisen)).
2. Die Domain `.invalid` kann keine E-Mails empfangen, sodass keine Nachricht eine echte Person erreichen kann.
3. **Benutzername**, **E-Mail**, **Passwort** und **Passwort bestätigen** (und **E-Mail-Adresse bestätigen**, wenn die
   Website es verlangt).
4. Auf der Seite der Rolle unter **Benutzer > Rollen und Richtlinien**.
5. **Falsch.** Gemeinsame Zugangsdaten verbergen, wer was getan hat, und teilen eine Sammlung von Einstellungen.

</details>

## Weiterführende Literatur

- [Rollen, Richtlinien und nicht aktivierte Benutzer](../../../../anleitungen/rollen-und-richtlinien.md): die vollständige
  Anleitung.
- [Ihr Konto und die Anmeldung](../../../konten-und-anmeldung.md): was die Teilnehmenden bei der Anmeldung sehen.
- [Anmelden im Editor](../../../anmelden-im-editor.md): der Siteaccess des Editors.

## Verwandte Seiten

- [Modul 2: Die Übungsumgebung aufbauen](README.md)
- [Train the Trainer: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
