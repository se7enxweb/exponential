---
title: "Modul 5: Freigaben und Zusammenarbeit"
audience: trainer, editor
category: training
order: 50
summary: Das fünfte Modul von Redakteure 201. Die Teilnehmenden sehen eine Version auf ihre Freigabe warten, geben zu zweit im Posteingang Zusammenarbeit frei und lehnen ab und gehen als Team mit Bearbeitungskonflikten und Übergaben um. 2 Stunden 10 Minuten.
keywords: redakteure 201, modul 5, freigabe, workflow, ausstehend, zusammenarbeit, freigeben, ablehnen, bearbeitungskonflikt, team, benachrichtigungen, editors 201, module 5, approval, pending, collaboration, approve, deny, edit conflict, notifications
type: help
---
# Modul 5: Freigaben und Zusammenarbeit

Redaktionsteams teilen sich Inhalte und Verantwortung. Dieses Modul behandelt den Freigabe-Workflow von beiden Seiten
und die Gewohnheiten, die ein Team davor bewahren, die Arbeit der anderen zu überschreiben. Die meisten Übungen werden
zu zweit durchgeführt.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Redakteure 201](../README.md) |
| Dauer | 2 Stunden 10 Minuten (mit 10 Minuten Pause) |
| Lektionen | 3 |
| Niveau | Fortgeschrittene |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [Lektion 5.1: Wenn Veröffentlichen eine Freigabe braucht](wenn-veroeffentlichen-eine-freigabe-braucht.md) | 30 Minuten | Einen Artikel in die Freigabe schicken und ihn unter **Meine Warteliste** lesen |
| [Lektion 5.2: Freigeben und ablehnen](freigeben-und-ablehnen.md) | 45 Minuten | Mit Kommentar ablehnen, korrigieren, freigeben; die Rollen tauschen |
| [Lektion 5.3: Im Team arbeiten](im-team-arbeiten.md) | 45 Minuten | Einen Bearbeitungskonflikt lösen, dem Ordner einer Kollegin folgen, Teamregeln schreiben |

## Dieses Modul vorbereiten

- Ein Freigabe-Workflow auf der Schulungsinstallation, der die Autorenkonten der Teilnehmenden abdeckt, verbunden mit
  „vor dem Veröffentlichen von Inhalten“, mit Freigabekonten, die keine Autoren sind. Zwei Wege:
  - Folgen Sie [dem durchgearbeiteten Beispiel](../../../../anleitungen/workflows.md#3-durchgerechnetes-beispiel-freigabe-vor-dem-veröffentlichen); oder
  - legen Sie die Beispielfreigaben an und entfernen Sie sie nach dem Kurs:

    ```bash
    ./console exp:collaborationsampledata --dry-run --allow-root-user
    ./console exp:collaborationsampledata --allow-root-user
    ./console exp:collaborationsampledata --remove --allow-root-user
    ```

- Der Workflow-Cronjob muss laufen (prüfen Sie **Setup** > **Workflow-Prozesse**: Die Statusleiste sagt, wann er das
  nächste Mal läuft), oder seien Sie bereit, nach jeder Freigabe den Teil frequent selbst auszuführen.
- Freigabekonten brauchen Zugang zu **Zusammenarbeit** in dem Siteaccess, den sie verwenden; prüfen Sie das, indem Sie
  sich mit einem davon anmelden.
- Kontenpaare für Lektion 5.3 und Artikel in den Kursordnern der Teilnehmenden.

## Zeitplan

| Teil | Minuten |
|---|---|
| Lektion 5.1 | 30 |
| Lektion 5.2 | 45 |
| Pause | 10 |
| Lektion 5.3 | 45 |
| **Gesamt** | **130** |

## Verwandte Seiten

- [Redakteure 201: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
