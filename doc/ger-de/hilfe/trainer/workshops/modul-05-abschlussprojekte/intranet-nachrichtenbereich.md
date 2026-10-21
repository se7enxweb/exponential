---
title: "Projekt 5.2: Ein Intranet-Nachrichtenbereich"
audience: trainer, admin, editor
category: training
order: 52
summary: Eine Aufgabenstellung für ein Abschlussprojekt für Administratoren, ein Intranet-Nachrichtenbereich mit eigener Sektion, einer Newsroom-Gruppe, einer eingeschränkten Redakteursrolle, einem Freigabeweg und einem Test jedes Kontos, in vier Stunden auf einer Schulungsinstallation gebaut, mit einem Bewertungsraster.
keywords: abschlussprojekt, projektaufgabe, intranet, nachrichtenbereich, administratoren, rollen, sektionen, benutzergruppen, zugriffskontrolle, bewertungsraster, capstone, project brief, news section, administrators, roles, sections, user groups, access control, rubric
type: help
---
# Projekt 5.2: Ein Intranet-Nachrichtenbereich

Administratoren beweisen ihr Können, indem sie Zugriffe korrekt einrichten und testen. Diese Aufgabe passt an das
Ende des [Administratoren-Bootcamps](../modul-03-dreitaegige-bootcamps/administratoren-bootcamp.md) oder des
[Administratorentags](../modul-02-eintaegige-programme/tag-fuer-administratoren.md) (gekürzt).

## Auf einen Blick

| | |
|---|---|
| Kurs | [Workshops und Bootcamps](../README.md) |
| Modul | [Modul 5: Abschlussprojekte](README.md) |
| Dauer | 4 Stunden |
| Niveau | Fortgeschrittene |
| Zielgruppe | Website-Administratoren, allein oder zu zweit |
| Voraussetzungen | Benutzer, Rollen, Richtlinien und Sektionen ([Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md), [Sektionen](../../../../anleitungen/sektionen.md)) |

## Lernziele

Die Teilnehmenden zeigen, dass sie:

- eine Sektion erstellen und einem Teilbaum zuweisen können;
- Benutzergruppen und Benutzer mit Testadressen erstellen können;
- Rollen mit eingeschränkten Richtlinien erstellen und korrekt zuweisen können;
- jedes Konto testen und die Einrichtung dokumentieren können.

## Die Aufgabenstellung

> Das Intranet des erfundenen „Northfield Council“ braucht einen Bereich News. Drei Newsroom-Autoren dürfen
> Nachrichten nur in News erstellen und bearbeiten. Ein Nachrichtenredakteur darf dort außerdem veröffentlichen und
> entfernen. Alle angemeldeten Benutzer dürfen Nachrichten lesen. Niemand im Newsroom darf Benutzer, Rollen oder
> Einstellungen ändern.

| Teil | Was zu bauen ist |
|---|---|
| Sektion | „Intranet news“, dem Ordner News zugewiesen |
| Gruppen | „Newsroom writers“, „Newsroom editors“ |
| Benutzer | Drei Autoren, ein Redakteur, alle unter `example.invalid` |
| Rollen | „News writer“ (Erstellen und Bearbeiten im Teilbaum oder in der Sektion News), „News editor“ (zusätzlich Entfernen), den Gruppen zugewiesen |
| Dokumentation | Eine einseitige Zugriffsnotiz: wer was wo tun darf |

## Hinweise für Trainer

- **Lesen Sie Rollen als Sätze** auf der Rollenseite; die Seite beschreibt jede Richtlinie in Worten, was die
  Prüfung erleichtert.
- **Testen Sie mit den Konten**, nicht durch Lesen: Melden Sie sich als Autor an und versuchen Sie, außerhalb von News
  zu bearbeiten.
- **Niemals auf einer Live-Website.** Eine Schulungsinstallation pro Teilnehmer.

## Praktische Übung

1. Erstellen Sie den Ordner News (falls er fehlt) und die Sektion „Intranet news“; weisen Sie die Sektion News zu.
2. Erstellen Sie die zwei Gruppen und die vier Benutzer.
3. Erstellen Sie die zwei Rollen mit eingeschränkten Richtlinien; weisen Sie jede ihrer Gruppe zu.
4. Melden Sie sich als Autor an: Erstellen Sie eine Nachricht in News; versuchen Sie, ein Element an anderer Stelle zu
   bearbeiten; versuchen Sie, den Bereich Setup zu öffnen.
5. Melden Sie sich als Redakteur an: Entfernen Sie eine Nachricht und stellen Sie sie aus dem Papierkorb wieder her.
6. Schreiben Sie die Zugriffsnotiz.

## Erwartetes Ergebnis

Bewertungsraster (jedes Kriterium 0, 1 oder 2 Punkte):

| Kriterium | 0 | 1 | 2 |
|---|---|---|---|
| Sektion | Fehlt | Erstellt, nicht zugewiesen | News zugewiesen |
| Gruppen und Benutzer | Fehlen | Erstellt, einige echte Adressen | Vollständig, alle unter `example.invalid` |
| Autorenrolle | Zu weit gefasst oder fehlt | Funktioniert, Einschränkung unklar | Auf News eingeschränkt, getestet |
| Redakteursrolle | Fehlt | Funktioniert | Auf News eingeschränkt, Entfernen getestet und wiederhergestellt |
| Keine Rechteausweitung | Der Newsroom kann Rollen oder Einstellungen ändern | | Der Newsroom erreicht weder Rollen noch Benutzer noch Einstellungen |
| Zugriffsnotiz | Fehlt | Unvollständig | Wer, was, wo, für jede Gruppe |

Bestanden ist mit 8 von 12 Punkten; das Bewertungsraster ist das eigene des Trainers und beansprucht keinen
offiziellen Status.

## Quiz

1. Warum dürfen die Newsroom-Rollen keine Richtlinien für das Modul `role` enthalten?
2. Was ist der Unterschied zwischen einer Einschränkung nach Sektion und nach Teilbaum?
3. Wie prüfen Sie, ob die Rolle korrekt eingeschränkt ist?

<details>
<summary>Antworten</summary>

1. Eine Richtlinie für das Modul role erlaubt Benutzern, Rollen zu ändern und sich so jeden beliebigen Zugriff zu
   geben ([Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md#kurz-gesagt)).
2. Eine Sektion ist eine Kennzeichnung, die an Inhalten überall stehen kann; ein Teilbaum ist eine Stelle im Baum mit
   allem, was darunter liegt.
3. Melden Sie sich als die Benutzer an und probieren Sie Aktionen innerhalb und außerhalb der Einschränkung aus.

</details>

## Weiterführende Informationen

- [Rollen, Richtlinien und nicht aktivierte Benutzer](../../../../anleitungen/rollen-und-richtlinien.md)
- [Sektionen](../../../../anleitungen/sektionen.md)
- [Sicherheit und Audit](../../../../anleitungen/sicherheit-und-audit.md)

## Verwandte Seiten

- [Modul 5: Abschlussprojekte](README.md)
- [Workshops und Bootcamps: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
