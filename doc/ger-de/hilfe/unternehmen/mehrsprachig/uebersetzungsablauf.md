---
title: Übersetzungsablauf
audience: businesses, editor, project-manager
category: business-solutions
order: 30
summary: Wie ein Unternehmen die Übersetzung in Exponential organisiert – Übersetzer, die auf ihre Sprache beschränkt sind, Übersetzen im Editor, Freigabe je Sprache, Übersetzungen aktuell halten, Inhalte zwischen Sprachen verschieben und was Inhalte löscht.
keywords: Übersetzungsablauf, Übersetzer, Sprachbeschränkung, Freigabe, Ereignis Freigeben, betroffene Sprachen, Hauptsprache, immer verfügbar, Übersetzung entfernen, Unternehmen, translation workflow, translators, content/translate, language limitation, approval, Approve event, affected languages, main language, always available, remove translation, business
type: help
---
# Übersetzungsablauf

Diese Seite richtet sich an Inhaltsverantwortliche, Sprachverantwortliche und Führungskräfte, die Übersetzungsarbeit
organisieren. Sie beschreibt, wie das Übersetzen in Exponential funktioniert, wie Sie Übersetzern genau die Rechte
geben, die sie brauchen, wie Sie Übersetzungen freigeben lassen, bevor sie online gehen, und welche Aktionen sich nicht
rückgängig machen lassen. Die schrittweise Referenz für Redakteure und Administratoren ist
[Inhaltssprachen](../../../anleitungen/inhaltssprachen.md).

## Inhalt

1. [Rollen in einem Übersetzungsteam](#rollen-in-einem-übersetzungsteam)
2. [Wie eine Übersetzung entsteht](#wie-eine-übersetzung-entsteht)
3. [Übersetzer auf ihre Sprache beschränken](#übersetzer-auf-ihre-sprache-beschränken)
4. [Freigabe je Sprache](#freigabe-je-sprache)
5. [Übersetzungen aktuell halten](#übersetzungen-aktuell-halten)
6. [Aktionen, die Inhalte löschen](#aktionen-die-inhalte-löschen)
7. [Eine wöchentliche Routine](#eine-wöchentliche-routine)

## Rollen in einem Übersetzungsteam

| Rolle | Tut | Einzuplanende Rechte |
|---|---|---|
| Autor | Schreibt den Quelltext | Bearbeiten und Anlegen in der Quellsprache |
| Übersetzer | Übersetzt in eine Sprache | Übersetzen oder Bearbeiten sowie Anlegen, beschränkt auf seine Sprache |
| Sprachverantwortlicher | Gibt Übersetzungen frei, verantwortet die Terminologie | Freigebender im Freigabe-Workflow für diese Sprache |
| Inhaltsverantwortlicher | Entscheidet, was wann übersetzt wird | Liest jede Sprache; verwaltet die Liste der offenen Arbeit |

Agenturen und freie Übersetzer passen in die Rolle Übersetzer: Sie bekommen ein auf ihre Sprache beschränktes Konto,
mehr nicht.

## Wie eine Übersetzung entsteht

Aus [7. Inhalte im Editor übersetzen](../../../anleitungen/inhaltssprachen.md#7-inhalte-im-editor-übersetzen):

1. Der Übersetzer öffnet das Objekt und klickt auf **Bearbeiten**. Lässt es sich in mehr als einer Sprache bearbeiten,
   fragt eine Seite, welche Übersetzung angelegt werden soll.
2. Unter **Neue Übersetzung** wählt er die Sprache; unter **Übersetzung basiert auf** die bestehende Übersetzung, von
   der er ausgeht (ihre Felder werden übernommen), oder keine, um leer zu beginnen.
3. Er übersetzt die Felder und klickt auf **Veröffentlichen** (oder schickt sie zur Freigabe, siehe unten).

Das Fenster **Übersetzungen** jedes Objekts führt seine Sprachen auf und kennzeichnet die Hauptsprache. Die Hauptsprache
ist die, in der ein Objekt angelegt wurde, oder eine, die später mit **Als Haupt setzen** gewählt wurde; die
Hauptübersetzung lässt sich nicht entfernen, solange sie die Hauptübersetzung ist
([6. Hauptsprache und immer verfügbar](../../../anleitungen/inhaltssprachen.md#6-hauptsprache-und-immer-verfügbar)).

> **Tipp**
> Übersetzen Sie zuerst die Startseite und die Menüordner. Über sie erreichen Besucher alles andere.

## Übersetzer auf ihre Sprache beschränken

Die Richtlinien zum Bearbeiten (`content/edit`), Übersetzen (`content/translate`) und Anlegen (`content/create`) von
Inhalten lassen sich im Rolleneditor jeweils nach **Sprache** beschränken, sodass ein Übersetzer etwa Deutsch darf und
sonst nichts. Geben Sie ihm außerdem Lesezugriff auf die Inhalte
([Übersetzer](../../../../eng-us/help/creators/website-creators/multilingual-sites.md#translators),
[Rollen, Richtlinien und nicht aktivierte Benutzer](../../../anleitungen/rollen-und-richtlinien.md)).

Kombinieren Sie die Sprachbeschränkung mit einer Teilbaumbeschränkung, wenn ein Übersetzer nur für eine Marke arbeitet
([Teams in ihrer Marke halten](../multisite/inhalte-zwischen-marken-teilen.md#teams-in-ihrer-marke-halten)).

## Freigabe je Sprache

Der Freigabe-Workflow von Exponential kann Inhalte zurückhalten, bis ein Freigebender sie annimmt. Sein Ereignis
**Freigeben** hat ein Feld **Betroffene Sprachen**: „nur Versionen, deren Ausgangssprache eine dieser ist“
([3.2 Das Ereignis Freigeben einrichten](../../../anleitungen/workflows.md#32-das-ereignis-freigeben-einrichten)). Damit wird eine
Freigabe je Sprache möglich:

| Einrichtung | Wirkung |
|---|---|
| Ereignis Freigeben mit der betroffenen Sprache Deutsch, Freigabegruppe „Sprachverantwortliche Deutsch“ | Deutsche Übersetzungen warten auf einen deutschsprachigen Prüfer |
| Ereignis Freigeben mit allen Sprachen, Freigabegruppe „Chefredaktion“ | Jede Änderung wartet auf die Chefredaktion |
| Ausgenommene Benutzergruppe „Sprachverantwortliche“ | Sprachverantwortliche veröffentlichen ihre eigenen Korrekturen, ohne zu warten |

Freigebende entscheiden in ihrem Posteingang unter Zusammenarbeit; danach veröffentlicht der Cronjob des Workflows die
freigegebene Version. Läuft dieser Cronjob nicht, „werden freigegebene Inhalte nie veröffentlicht“
([Workflows: Freigaben, Trigger und die Prozesse, die warten](../../../anleitungen/workflows.md)). Ein Workflow je Trigger:
Um mehrere Freigaberegeln laufen zu lassen, beschreibt die Anleitung ein Multiplexer-Ereignis.

## Übersetzungen aktuell halten

Wenn sich der Quelltext ändert, ändern sich seine Übersetzungen nicht von selbst. Praktische Wege, den Überblick zu
behalten:

- **Setup > Sprachen** zeigt je Inhaltssprache, wie viele Objekte und Klassen eine Übersetzung darin haben, welche
  Siteaccesses sie zeigen, und gibt Hinweise, wenn wenige Objekte sie verwenden oder kein Siteaccess sie aufführt
  ([Inhaltssprachen, Kurz gesagt](../../../anleitungen/inhaltssprachen.md#kurz-gesagt)).
- Eine Regel in Ihrem Redaktionsleitfaden: Wer einen Quelltext ändert, informiert die Sprachverantwortlichen oder leitet
  die Änderung über den Freigabe-Posteingang weiter.
- Ist Exponential Intelligence eingeschaltet, findet der **Übersetzungsabgleich**, was sich in der Quelle geändert hat,
  und bereitet aktualisierte Übersetzungsentwürfe zur Prüfung vor
  ([Unterstützte Übersetzung mit Exponential Intelligence](unterstuetzte-uebersetzung-mit-exponential-intelligence.md)).

## Aktionen, die Inhalte löschen

| Aktion | Wirkung |
|---|---|
| Eine **Übersetzung** eines Objekts entfernen | Löscht diese Übersetzung „endgültig“: alle ihre Felder in dieser Sprache, in jeder Version |
| Eine **Sprache** entfernen | Nur möglich, wenn kein Objekt und keine Klasse sie verwendet, löscht also nie Inhalte |
| Die Standard-Inhaltssprache einer Website mit Inhalten ändern | Kann Objekte verbergen, die es in der neuen Standardsprache nicht gibt |

Quellen: [9. Übersetzungen und Sprachen sicher entfernen](../../../anleitungen/inhaltssprachen.md#9-übersetzungen-und-sprachen-sicher-entfernen)
und [10.14 Mehrsprachige Websites](../../../installation/10-nach-der-installation.md#1014-mehrsprachige-sites).

Inhalte, die in der falschen Sprache angelegt wurden, verschieben Sie, indem Sie sie in die richtige übersetzen, diese
zur Hauptsprache machen und erst dann die falsche Übersetzung entfernen. „Kein mitgelieferter Konsolenbefehl
verschiebt Übersetzungen in großen Mengen“
([8. Inhalte von einer Sprache in eine andere verschieben](../../../anleitungen/inhaltssprachen.md#8-inhalte-von-einer-sprache-in-eine-andere-verschieben)).

> **Warnung**
> Geben Sie das Recht, Übersetzungen zu entfernen, nur Personen, die verstehen, dass es sich nicht rückgängig machen
> lässt. Bewahren Sie Backups auf, die Sie mindestens einmal wiederhergestellt haben
> ([Zuverlässigkeit für Unternehmen](../zuverlaessigkeit/README.md)).

## Eine wöchentliche Routine

- [ ] Prüfen Sie Setup > Sprachen auf Sprachen mit unerwartet wenigen Objekten.
- [ ] Gehen Sie den Freigabe-Posteingang für jede Sprache durch.
- [ ] Listen Sie die in dieser Woche geänderten Quellseiten auf und bestätigen Sie, dass ihre Übersetzungen
      aktualisiert wurden.
- [ ] Prüfen Sie maschinell vorbereitete Entwürfe, falls Sie sie verwenden, bevor sie veröffentlicht werden.
- [ ] Prüfen Sie stichprobenartig eine Seite je Sprache auf der öffentlichen Website, einschließlich des
      Sprachumschalters.

## Verwandte Seiten

- [Mehrsprachigkeit planen](mehrsprachigkeit-planen.md)
- [Unterstützte Übersetzung mit Exponential Intelligence](unterstuetzte-uebersetzung-mit-exponential-intelligence.md)
- [Checkliste Mehrsprachigkeit](checkliste-mehrsprachigkeit.md)
- [Inhaltssprachen](../../../anleitungen/inhaltssprachen.md)
- [Sprachen](../../sprachen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
