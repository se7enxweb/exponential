---
title: Einen Menschen beteiligt halten
audience: ai-agents, developer, app-creator, admin, project-manager
category: ai-agents
order: 67
summary: Wie Sie sicherstellen, dass ein Mensch prüft, was ein KI-Agent schreibt, bevor Besucher es sehen - Entwürfe von Exponential Intelligence, eine verborgene Sektion mit einem Freigabe-Workflow für Schreibzugriffe über REST und expservices, Berichte statt Schreibzugriffen und der Audit-Trail als Aufzeichnung, wer was entschieden hat.
keywords: Mensch in der Schleife, menschliche Prüfung, Freigabe-Workflow, verborgene Sektion, Staging-Sektion, Entwürfe, Schreibzugriffe von Agenten, REST create, Prüfung vor der Veröffentlichung, Verantwortung, human in the loop, human review, approval workflow, hidden section, staging section, drafts, agent writes, review before publish, accountability
type: help
---
# Einen Menschen beteiligt halten

Diese Seite richtet sich an alle, die einen KI-Agenten Inhalte erzeugen lassen und möchten, dass ein Mensch entscheidet, was
veröffentlicht wird. Der Assistent in der Administration tut das von Haus aus; ein eigener Agent braucht eine Einrichtung, die
dasselbe leistet.

## Inhalt

1. [Warum es wichtig ist](#warum-es-wichtig-ist)
2. [Muster 1: den Assistenten Entwürfe schreiben lassen](#muster-1-den-assistenten-entwürfe-schreiben-lassen)
3. [Muster 2: eine verborgene Sektion mit einem Freigabe-Workflow](#muster-2-eine-verborgene-sektion-mit-einem-freigabe-workflow)
4. [Muster 3: Berichte statt Schreibzugriffen](#muster-3-berichte-statt-schreibzugriffen)
5. [Wer was entschieden hat](#wer-was-entschieden-hat)

## Warum es wichtig ist

Ein REST-v2-create **legt an und veröffentlicht**, und Schreibzugriffe über expservices wirken, sobald sie erfolgen. Ein Agent, der
durch Text in einem Feed irregeleitet wird oder einfach falsch liegt, würde seinen Fehler sofort veröffentlichen. Ein Mensch
dazwischen fängt ihn ab und ist derjenige, der für das verantwortlich ist, was öffentlich wird.

## Muster 1: den Assistenten Entwürfe schreiben lassen

Wenn der Inhalt ohnehin für Redakteure geschrieben wird, verwenden Sie Exponential Intelligence: Es schreibt Entwürfe als der
Redakteur, veröffentlicht nie, und "Send for approval" führt die normale Veröffentlichungsoperation aus, sodass ein
Freigabe-Workflow greift ([Entwürfe und menschliche Prüfung](../expai/entwuerfe-und-menschliche-pruefung.md)). Bei größeren Aufgaben lässt die
gestufte Prüfung von Vector den Redakteur jede Änderung Feld für Feld annehmen
([Vector und gestufte Prüfung](../expai/vektorsuche-und-gestufte-pruefung.md)).

## Muster 2: eine verborgene Sektion mit einem Freigabe-Workflow

Für einen eigenen Agenten, der Inhalte anlegt:

1. Legen Sie einen Ordner für die Ausgabe des Agenten an, in einer **Sektion, die Besucher nicht lesen können** (die Rolle Anonymous
   hat dafür kein `content/read`).
2. Geben Sie dem Konto des Agenten `content/create` nur dort ([Rollen und minimale Rechte](../steuerung/rollen-und-minimale-rechte.md)).
3. Fügen Sie für diese Sektion einen Freigabe-Workflow auf `content / publish / before` mit Redakteuren als Freigebenden hinzu, wenn
   Elemente freigegeben werden müssen, bevor sie auch nur intern existieren ([Workflows](../../../anleitungen/workflows.md)).
4. Redakteure prüfen die Elemente, bearbeiten sie und verschieben oder kopieren freigegebene an ihren öffentlichen Platz.
5. Prüfen Sie die Rechte mit `./console exp:access:check --user=anonymous --node=<agent folder>`: Die Ausgabe muss mit DENIED enden.

## Muster 3: Berichte statt Schreibzugriffen

Oft muss der Agent gar nicht schreiben. Lassen Sie ihn über einen nur lesenden Schlüssel lesen und einen Bericht, eine Liste
vorgeschlagener Änderungen oder einen Entwurfstext in einer Datei erzeugen, und lassen Sie einen Redakteur das umsetzen
([Ein nur lesender Inhaltsbericht](ablauf-nur-lesender-inhaltsbericht.md)). Das entfernt die Schreibberechtigung vollständig.

## Wer was entschieden hat

Der Audit-Trail hält die Schreibzugriffe des Agentenkontos und die Aktionen der Redakteure zum Veröffentlichen, Verschieben und
Freigeben fest, jeweils unter dem Konto, das sie vorgenommen hat ([Der Audit-Trail für KI](../steuerung/audit-trail-fuer-ki.md)).
Mit einem eigenen Konto je Agent hat die Frage "Wer hat das veröffentlicht?" immer den Namen eines Menschen als Antwort.

## Verwandte Seiten

- [Eine Schnittstelle für einen Agenten wählen](eine-schnittstelle-waehlen.md)
- [Ein Ablauf für den Inhaltsimport](ablauf-inhaltsimport.md)
- [Eine Richtlinie zur KI-Nutzung für Ihre Organisation](../steuerung/eine-richtlinie-zur-ki-nutzung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
