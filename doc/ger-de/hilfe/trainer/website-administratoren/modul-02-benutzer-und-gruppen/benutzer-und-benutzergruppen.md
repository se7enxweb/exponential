---
title: "Lektion 2.1: Benutzer und Benutzergruppen"
audience: trainer, admin
category: training
order: 21
summary: Die Teilnehmenden erklären, dass Benutzer und Benutzergruppen Inhalte im Benutzerbaum sind, lesen die Gruppen und Rollen eines Benutzers und legen eine Benutzergruppe für ein Team an. 35 Minuten.
keywords: website-administration, benutzer, benutzergruppen, benutzerkonten, anonymer benutzer, administratoren, redakteure, mitglieder, reiter rollen, benutzerbaum, site administration, users, user groups, user accounts, anonymous user, roles tab, user tree
type: help
---
# Lektion 2.1: Benutzer und Benutzergruppen

In Exponential sind Benutzer und Benutzergruppen Inhalte: Sie liegen in einem eigenen Teil des Inhaltsbaums, unter
**Benutzer**, und werden wie andere Elemente angelegt, verschoben und bearbeitet. Wo ein Benutzer in diesem Baum
liegt, entscheidet, zu welchen Gruppen er gehört, und die Gruppen entscheiden über das meiste, was er tun darf. Diese
Lektion baut dieses Bild auf, bevor die Teilnehmenden Konten anlegen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Website-Administration](../README.md) |
| Modul | [Modul 2: Benutzer und Gruppen](README.md) |
| Dauer | 35 Minuten (15 Präsentation, 20 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Website-Administratoren |
| Voraussetzungen | [Modul 1](../modul-01-orientierung/README.md); ein Administratorkonto auf der Schulungsinstallation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- den Benutzerbaum öffnen und die Benutzergruppen einer neuen Installation benennen;
- erklären, dass ein Benutzer zu jeder Gruppe gehört, in der er einen Ort hat;
- die Reiter **Rollen** und **Richtlinien** einer Gruppe lesen;
- die besondere Stellung des anonymen Benutzers und des Kontos `admin` erklären;
- eine Benutzergruppe für ein Team anlegen.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Rollen, Richtlinien und nicht aktivierte Benutzer](../../../../anleitungen/rollen-und-richtlinien.md) und
[Ihr Konto und die Anmeldung](../../../konten-und-anmeldung.md).

1. **Der Benutzerbaum** (4 Minuten). Klicken Sie im oberen Menü auf **Benutzer**. Die linke Spalte zeigt
   **Benutzerkonten** mit der obersten Gruppe **Users** und ihren Gruppen (auf einer Website, die aus den
   7x-Seitenpaketen installiert wurde, zum Beispiel **Members**, **Administrator users**, **Editors** und
   **Anonymous Users**) und unter **Zugangskontrolle** die Einträge **Rollen und Richtlinien**,
   **Nicht aktivierte Benutzer** und **Benutzerkonten prüfen**.
2. **Eine Gruppe ist ein Element** (3 Minuten). Öffnen Sie **Administrator users**. Die Gruppe hat die Reiter jedes
   Elements (**Ansehen**, **Details**, **Orte**, **Verknüpfungen**) und zusätzlich **Rollen** und **Richtlinien**:
   die dieser Gruppe zugewiesenen Rollen und die Richtlinien, die sie vergeben.
3. **Mitgliedschaft ist ein Ort** (4 Minuten). Ein Benutzer ist Mitglied der Gruppe, in der er platziert ist. Ein
   Benutzer mit zwei Orten (in **Editors** und in einer weiteren Gruppe) hat die Rollen beider. Wird ein Benutzer in
   eine andere Gruppe verschoben, ändern sich seine Rechte sofort.
4. **Zwei besondere Konten** (4 Minuten). Der anonyme Benutzer (in **Anonymous Users**) ist jeder Besucher, bevor er
   sich anmeldet: Seine Rollen entscheiden, was die Öffentlichkeit sehen darf. Das Konto `admin` ist der erste
   Administrator; es sollte niemandes Konto für den Alltag sein (Lektion 2.3).

### Häufige Fragen

**Kann ein Benutzer in keiner Gruppe sein?** Jedes Benutzerobjekt braucht mindestens einen Ort, und dieser Ort liegt
unterhalb einer Gruppe. Ein Benutzerkonto ohne Ort wird unter **Benutzerkonten prüfen** als *Verwaist* gemeldet
(Lektion 2.4).

**Warum gibt es unter Benutzer einen Eintrag Papierkorb?** Die linke Spalte zeigt den Papierkorb, aber
Benutzerobjekte landen nie im Papierkorb: Das Entfernen eines Benutzers ist endgültig. Lektion 2.4 zeigt, wie Sie
Konten stattdessen stilllegen.

**Kann eine Gruppe in einer anderen Gruppe liegen?** Ja. Ein Mitglied einer inneren Gruppe bekommt auch die Rollen,
die den Gruppen darüber zugewiesen sind.

### Stolperfallen

- **Die Rollen der Gruppe Anonymous Users bearbeiten.** Eine falsche Änderung dort öffnet oder schließt die gesamte
  öffentliche Website. In dieser Lektion wird nur angesehen, nicht geändert.
- **Eine Gruppe mit Benutzern darin entfernen.** Die Benutzer werden mit ihr entfernt, endgültig. Entfernen Sie im
  Kurs nie eine Gruppe, ohne ihre Unterelemente zu prüfen.

## Praktische Übung

Auf Ihrer Schulungsinstallation, als Administrator angemeldet:

1. Klicken Sie im oberen Menü auf **Benutzer**. Schreiben Sie die Gruppen auf, die unter **Benutzerkonten** aufgelistet
   sind.
2. Öffnen Sie **Administrator users** und dort den Reiter **Rollen**. Notieren Sie die zugewiesene Rolle und ob sie
   eine Einschränkung hat.
3. Öffnen Sie den Reiter **Richtlinien** derselben Gruppe und lesen Sie die aufgelisteten Richtlinien.
4. Öffnen Sie **Anonymous Users**, dann den Reiter **Rollen**. Notieren Sie die Rolle.
5. Öffnen Sie die oberste Gruppe **Users**. Klicken Sie unter **Unterelemente** auf **Neu erstellen** und wählen Sie
   **Benutzergruppe**.
6. Nennen Sie die Gruppe `Training team` und geben Sie ihr eine kurze Beschreibung.
7. Klicken Sie auf **Zur Veröffentlichung schicken**.
8. Öffnen Sie den Reiter **Rollen** der neuen Gruppe. Er ist leer: Es ist noch keine Rolle zugewiesen.
9. Öffnen Sie Ihr eigenes Konto (das Benutzermenü mit Ihrem Namen in der Kopfzeile führt dorthin, oder Sie finden es
   in **Administrator users**) und öffnen Sie den Reiter **Orte**. In welcher Gruppe liegt es?

## Erwartetes Ergebnis

- Die Teilnehmenden nennen vier oder mehr Gruppen und die Rolle von **Administrator users** und
  **Anonymous Users**.
- Eine Gruppe `Training team` liegt direkt unter **Users**, ohne Rollen.
- Schritt 9: Das Konto der Teilnehmenden liegt in **Administrator users**.
- Der Trainer prüft das, indem er auf dem Bildschirm einer Person **Benutzer** und den Reiter **Rollen** der neuen
  Gruppe öffnet.

![Der Reiter Benutzer: Benutzergruppen als Ordner, mit den Konten darin](../../../../../eng-us/help/images/trainers/sa-users-groups.webp)

## Quiz

1. Was entscheidet, zu welchen Gruppen ein Benutzer gehört?
   A. Ein Feld am Benutzer B. Seine Orte im Benutzerbaum C. Die Rollenliste D. Der Siteaccess
2. Welches Konto ist jeder Besucher, bevor er sich anmeldet?
3. Nennen Sie die zwei Reiter, die eine Benutzergruppe hat, ein gewöhnlicher Ordner aber nicht.
4. Ein Benutzer liegt in einer inneren Gruppe. Bekommt er die Rollen der Gruppen darüber?
5. Richtig oder falsch: Ein entfernter Benutzer lässt sich aus dem Papierkorb wiederherstellen.

<details>
<summary>Antworten</summary>

1. **B.** Die Mitgliedschaft ist der Ort des Benutzers.
2. Der anonyme Benutzer, in **Anonymous Users**; seine Rollen entscheiden, was die Öffentlichkeit sehen darf.
3. **Rollen** und **Richtlinien**.
4. Ja, die Rollen der Gruppen darüber gelten ebenfalls.
5. **Falsch.** Benutzerobjekte landen nie im Papierkorb; das Entfernen eines Benutzers ist endgültig
   ([Benutzerkonten prüfen](../../../../../eng-us/features/6.0/user-hygiene.md)).

</details>

## Weiterführende Literatur

- [Rollen, Richtlinien und nicht aktivierte Benutzer](../../../../anleitungen/rollen-und-richtlinien.md): Rollen und wie sie
  bei den Benutzern ankommen.
- [Ihr Konto und die Anmeldung](../../../konten-und-anmeldung.md): was Benutzer von ihrem eigenen Konto sehen.
- [Benutzerkonten prüfen](../../../../../eng-us/features/6.0/user-hygiene.md): die Kontoprüfungen aus Lektion 2.4.

## Verwandte Seiten

- [Modul 2: Benutzer und Gruppen](README.md)
- [Website-Administration: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
