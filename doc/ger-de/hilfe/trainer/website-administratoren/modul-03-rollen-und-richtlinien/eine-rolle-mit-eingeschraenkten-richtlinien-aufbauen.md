---
title: "Lektion 3.2: Eine Rolle mit eingeschränkten Richtlinien bauen"
audience: trainer, admin
category: training
order: 32
summary: Die Teilnehmenden legen mit dem Richtlinien-Assistenten eine Rolle an, fügen eingeschränkte Richtlinien zum Lesen, Erstellen und Bearbeiten von Inhalten hinzu, bringen die Richtlinien in eine Reihenfolge, speichern den Entwurf und kopieren eine Rolle. 45 Minuten.
keywords: website-administration, neue rolle, richtlinien-assistent, modul, funktion, einschränkungen, eingeschränkten zugriff gewähren, rolleneditor, entwurf, speichern, abbrechen, reihenfolge der richtlinien, rolle kopieren, site administration, new role, policy wizard, grant limited access, role editor, copy role
type: help
---
# Lektion 3.2: Eine Rolle mit eingeschränkten Richtlinien bauen

Die meisten Organisationen brauchen ein paar eigene Rollen: Redakteure, die nur Nachrichten anfassen dürfen,
Mitarbeitende, die erstellen, aber nicht veröffentlichen dürfen, ein Team, das seinen eigenen Teilbaum verwaltet.
Diese Lektion baut eine solche Rolle Schritt für Schritt im Rolleneditor und seinem dreistufigen
Richtlinien-Assistenten.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Website-Administration](../README.md) |
| Modul | [Modul 3: Rollen und Richtlinien](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Website-Administratoren |
| Voraussetzungen | [Lektion 3.1](rollen-richtlinien-und-einschraenkungen.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- eine Rolle anlegen und mit den Schritten Modul, Funktion und Einschränkungen des Assistenten Richtlinien hinzufügen;
- für eine Funktion zwischen vollem und eingeschränktem Zugriff wählen;
- die Einschränkungen einer Richtlinie später ändern;
- Richtlinien umsortieren und erklären, warum die Reihenfolge den Zugriff nicht ändert;
- einen Rollenentwurf speichern oder abbrechen und eine Rolle kopieren.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Eine Rolle mit eingeschränkten Richtlinien anlegen](../../../../anleitungen/rollen-und-richtlinien.md#3-eine-rolle-mit-eingeschränkten-richtlinien-anlegen)
und [Rollen: Richtlinien-IDs, Sortierung und Schaltflächen für die Reihenfolge](../../../../funktionen/6.0/reihenfolge-der-richtlinien-einer-rolle.md).

1. **Der Entwurf** (2 Minuten). Jede Änderung im Rolleneditor geschieht in einem Entwurf. Für niemanden ändert sich
   etwas bis **Speichern**; **Abbrechen** verwirft den Entwurf. Solange es Änderungen gibt, zeigt der Editor
   **Nicht gespeicherte Änderungen**.
2. **Der Assistent** (6 Minuten). Klicken Sie in der Rollenliste auf **Neue Rolle**, geben Sie einen Namen ein und
   dann **Neue Richtlinie**:
   - Schritt 1, Modul: Wählen Sie `content`. *Zugriff auf alle Funktionen gewähren* fügt das ganze Modul auf einmal
     hinzu.
   - Schritt 2, Funktion: Wählen Sie `read`. *Vollen Zugriff gewähren* fügt sie ohne Einschränkungen hinzu;
     *Eingeschränkten Zugriff gewähren* führt weiter.
   - Schritt 3, Einschränkungen: Wählen Sie Werte (Strg oder Cmd für mehrere); *Beliebig* lässt eine Einschränkung
     weg; Knoten und Teilbäume werden im Inhaltsbrowser gewählt. **OK**.
3. **Was eine typische Redakteursrolle braucht** (4 Minuten). Neben den Funktionen von `content` muss sich ein
   Redakteur an der Administration anmelden können (`user/login` mit der Einschränkung SiteAccess) und liest meist
   mehr, als er bearbeitet. Vergleichen Sie mit der ausgelieferten Rolle **Editor**, statt alles neu zu erfinden.
4. **Reihenfolge und Kopie** (3 Minuten). Richtlinien lassen sich an ihrem Griff ziehen oder mit den Pfeiltasten oder
   den Schaltflächen **hinauf** und **runter** verschieben. Die Reihenfolge hilft nur den Lesenden: Jede Richtlinie
   gewährt für sich. **Kopieren** erzeugt „Copy of <name>“ mit denselben Richtlinien und ohne Zuweisungen.

### Häufige Fragen

**Welche Einschränkungen bietet jede Funktion an?** Der Assistent bietet nur die Einschränkungen an, die die Funktion
unterstützt. Zum Beispiel akzeptiert `content/read` Section, Class, Owner, Node, Subtree, State und weitere;
`user/login` akzeptiert SiteAccess.

**Ich habe eine Richtlinie hinzugefügt, die ich nicht vergeben darf, und sie ist verschwunden.** Mit
`PreventPrivilegeEscalation=enabled` wird eine Richtlinie, die Sie selbst nicht haben, wieder herausgenommen, und die
Seite meldet *Die Richtlinie wurde nicht hinzugefügt*. Administratoren dürfen alles vergeben.

**Ist es besser, die ausgelieferte Rolle Editor zu bearbeiten oder sie zu kopieren?** Kopieren Sie sie. Ein Upgrade
oder ein Seitenpaket erwartet die ausgelieferten Rollen womöglich unverändert, und eine Kopie lässt sich ohne Schaden
entfernen.

### Stolperfallen

- **Speichern vergessen.** Eine Teilnehmerin oder ein Teilnehmer schließt den Tab mit
  **Nicht gespeicherte Änderungen**; nichts wurde gespeichert. Weisen Sie auf den Hinweis hin.
- **Versehentlich alle Module.** *Alle Module* mit allen Funktionen zu wählen bedeutet vollen Zugriff; die Seite warnt
  vorher. Lesen Sie die Warnung laut vor.
- **Keine Anmelderichtlinie.** Eine Rolle für Benutzer der Administration ohne `user/login` auf dem Siteaccess der
  Administration kann sich dort nicht anmelden. Lektion 3.4 zeigt das Symptom.

## Praktische Übung

Auf Ihrer Schulungsinstallation, als Administrator angemeldet:

1. Öffnen Sie **Benutzer > Rollen und Richtlinien** und klicken Sie auf **Neue Rolle**.
2. Geben Sie den Namen `News editors` ein.
3. Klicken Sie auf **Neue Richtlinie**. Wählen Sie in Schritt 1 das Modul `content`, in Schritt 2 die Funktion `read`
   und dann **Eingeschränkten Zugriff gewähren**.
4. Wählen Sie in Schritt 3 die Sektion **Standard** und klicken Sie auf **OK**. Lesen Sie den Satz, den der Editor
   anzeigt.
5. Fügen Sie eine zweite Richtlinie hinzu: `content` / `create`, eingeschränkt auf die Klassen **Article** und
   **Folder**.
6. Fügen Sie eine dritte Richtlinie hinzu: `content` / `edit`, eingeschränkt auf den Eigentümer *Self* (nur die eigenen
   Inhalte), wenn Ihre Version das anbietet; andernfalls auf die Klasse **Article**.
7. Fügen Sie eine vierte Richtlinie hinzu: `user` / `login`, eingeschränkt auf den Siteaccess `admin`.
8. Beachten Sie den Hinweis **Nicht gespeicherte Änderungen** und klicken Sie dann auf **Speichern**.
9. Lesen Sie auf der Rollenseite die vier Sätze. Sortieren Sie sie **Nach Modul**.
10. Klicken Sie auf **Bearbeiten**, verschieben Sie die Richtlinie `user / login` nach ganz oben und klicken Sie auf
    **Speichern**.
11. Klicken Sie in der Rollenliste bei `News editors` auf **Kopieren**, lesen Sie die Seite und erstellen Sie die
    Kopie. Entfernen Sie die Kopie dann wieder über die Entfernen-Auswahl der Liste und bestätigen Sie.

## Erwartetes Ergebnis

- Eine Rolle `News editors` mit vier Richtlinien, noch nicht zugewiesen (Abzeichen *Nicht zugewiesen* in der Liste).
- Die Rollenseite zeigt Sätze wie „Darf Inhalte lesen, in der Sektion Standard“ und „Darf sich anmelden, auf dem
  Siteaccess admin“.
- Nach Schritt 11 bleibt nur die ursprüngliche Rolle übrig.
- Der Trainer prüft die Rollenseite auf dem Bildschirm einer Person und fragt, warum die Rolle noch niemandem etwas
  gibt.

![Die Seite einer Rolle: ihre Richtlinien mit Modul, Funktion und Einschränkungen](../../../../../eng-us/help/images/trainers/sa-editor-role.webp)

## Quiz

1. Was sind die drei Schritte des Richtlinien-Assistenten?
2. Was geschieht mit Ihren Änderungen im Rolleneditor, wenn Sie den Browser vor **Speichern** schließen?
3. Bekommt eine Richtlinie Vorrang, wenn Sie sie nach oben verschieben? Warum?
4. Was kopiert **Kopieren**, und was lässt es weg?
5. Welche Wahl in Schritt 1 und Schritt 2 zusammen ergibt vollen Zugriff?
   A. `content` mit allen Funktionen B. *Alle Module* mit allen Funktionen C. `user` / `login` D. `role` / `assign`

<details>
<summary>Antworten</summary>

1. Modul, Funktion, Einschränkungen.
2. Sie gehen verloren: Änderungen geschehen in einem Entwurf, den nur **Speichern** behält.
3. Nein. Jede Richtlinie gewährt für sich; die Reihenfolge hilft nur Menschen, die Rolle zu lesen
   ([Die Richtlinien in eine Reihenfolge bringen](../../../../anleitungen/rollen-und-richtlinien.md#4-die-richtlinien-in-eine-reihenfolge-bringen)).
4. Die Richtlinien; die Kopie hat keine Zuweisungen.
5. **B.** Die Seite warnt, bevor sie sie hinzufügt.

</details>

## Weiterführende Literatur

- [Rollen, Richtlinien und nicht aktivierte Benutzer](../../../../anleitungen/rollen-und-richtlinien.md): Editor und Assistent
  im Detail.
- [Rollen: Richtlinien-IDs, Sortierung und Schaltflächen für die Reihenfolge](../../../../funktionen/6.0/reihenfolge-der-richtlinien-einer-rolle.md):
  Reihenfolge und Sortierung.
- [Rollen- und Richtlinienprüfungen in Templates](../../../../funktionen/6.0/template-operatoren-fuer-rollen-und-richtlinien.md): wie
  Designs nach Berechtigungen fragen.

## Verwandte Seiten

- [Modul 3: Rollen und Richtlinien](README.md)
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
