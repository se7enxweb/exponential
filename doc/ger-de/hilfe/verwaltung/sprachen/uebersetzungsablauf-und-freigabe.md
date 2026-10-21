---
title: Übersetzungsablauf und Freigabe
audience: government, admin, project-manager, editor
category: public-sector
order: 200
summary: Wie eine öffentliche Stelle die Übersetzung in Exponential organisiert - ein Rückstand, Übersetzer pro Sprache, die Freigabe von Übersetzungen mit dem Ereignis Freigeben, beschränkt auf betroffene Sprachen, das getrennte Veröffentlichen jeder Sprache und welche Werkzeuge (Übersetzungsspeicher, maschinelle Übersetzung) es gibt und welche nicht.
keywords: Übersetzungsablauf, Freigabe, Ereignis Freigeben, Betroffene Sprachen, Übersetzungsrückstand, Durchsicht, maschinelle Übersetzung, expai, Übersetzungsspeicher, Veröffentlichung pro Sprache, translation workflow, approval, Approve event, Affected languages, translation backlog, review, machine translation, translation memory
type: help
---
# Übersetzungsablauf und Freigabe

Diese Seite richtet sich an Führungskräfte und Administratoren, die die Übersetzung einer mehrsprachigen Website
organisieren. Exponential versioniert jede Übersetzung, veröffentlicht jede Sprache für sich und kann eine Übersetzung
mit einem Workflow zur Freigabe zurückhalten ([Workflows](../../../anleitungen/workflows.md)).

## Inhalt

1. [Ein einfacher Ablauf](#ein-einfacher-ablauf)
2. [Finden, was übersetzt werden muss](#finden-was-übersetzt-werden-muss)
3. [Freigabe pro Sprache](#freigabe-pro-sprache)
4. [Jede Sprache für sich veröffentlichen](#jede-sprache-für-sich-veröffentlichen)
5. [Werkzeuge, die es gibt, und solche, die es nicht gibt](#werkzeuge-die-es-gibt-und-solche-die-es-nicht-gibt)

## Ein einfacher Ablauf

1. Die Autorin veröffentlicht in der Ausgangssprache.
2. Ein Übersetzer fügt die Übersetzung hinzu (siehe „Einen Eintrag Schritt für Schritt übersetzen“).
3. Eine prüfende Person für diese Sprache gibt sie frei.
4. Sie wird in dieser Sprache veröffentlicht; die anderen Sprachen bleiben unberührt.

## Finden, was übersetzt werden muss

- Die Seite einer Sprache unter **Setup > Sprachen** zählt die Objekte mit einer Übersetzung in dieser Sprache und
  listet die neuesten auf.
- Die Sprachkennzeichen eines Eintrags zeigen fehlende Übersetzungen gestrichelt an
  ([Sprachen (für Redakteure)](../../sprachen.md)).
- Eine als Inhalt gepflegte Liste oder eine Objektzustandsgruppe „Übersetzung“ (Benötigt, In Arbeit, Fertig), die
  Übersetzer setzen, ergibt einen Rückstand ([Objektzustände](../../../anleitungen/objektzustaende.md)).
- Benachrichtigungen: Übersetzer können einer Sektion folgen und eine E-Mail erhalten, wenn sich Inhalte darin ändern
  ([Benachrichtigungen](../../../../eng-us/features/6.0/notifications.md)).

## Freigabe pro Sprache

Das Ereignis *Freigeben* hat **Betroffene Sprachen**: Nur Versionen, deren Ausgangssprache eine davon ist, brauchen
eine Freigabe. Eine Version, die als Übersetzung ins Französische angelegt wurde, hat Französisch als Sprache, sodass
ein Workflow mit **Betroffene Sprachen** Französisch und **Gruppen, die Content freigeben** „Prüfende Französisch“ nur
französische Übersetzungen zurückhält.

1. **Setup > Workflows**: ein Workflow pro Sprache, jeder mit einem Ereignis *Freigeben* für diese Sprache und ihre
   prüfenden Personen.
2. **Setup > Trigger**: ein Workflow pro Trigger, kombinieren Sie sie also mit einem Ereignis **Multiplexer** in dem
   Workflow, der mit **Vor dem Veröffentlichen von Inhalten** verbunden ist.
3. Die Cronjob-Gruppe `frequent` muss laufen.

## Jede Sprache für sich veröffentlichen

Jede Übersetzung wird für sich veröffentlicht: Das Veröffentlichen des deutschen Textes ändert den englischen nicht.
Eine Korrektur in der Ausgangssprache markiert Übersetzungen nicht als veraltet; das muss Ihr Ablauf leisten (ein
Zustand, ein Hinweis an die Übersetzer).

## Werkzeuge, die es gibt, und solche, die es nicht gibt

| Werkzeug | In Exponential |
|---|---|
| Übersetzungsspeicher, Terminologiedatenbank | nicht im Kernel; nutzen Sie die Werkzeuge Ihres Sprachdienstes und fügen Sie das Ergebnis ein |
| Maschinelle Übersetzung | nicht im Kernel; der optionale Assistent `expai` hat im Bearbeitungsformular eine Aktion **Translate** pro Feld, die mit dem eingerichteten Anbieter einen Vorschlag zur Durchsicht in das Formular schreibt ([expai](../../../../eng-us/features/6.0/extensions/expai.md)) |
| Export für Übersetzungsagenturen | `xrowextract` kann eine Zeile pro Übersetzung als CSV, JSON oder XML exportieren und Dateien mit einem Probelauf wieder importieren ([xrowextract](../../../../eng-us/features/6.0/extensions/xrowextract.md)) |
| Bearbeiten nebeneinander | **Übersetzung basiert auf** kopiert die Quelle in das Formular der neuen Übersetzung |

Maschinelle Übersetzung sendet Text an den Anbieter, den Sie einrichten; prüfen Sie das unter „Zu prüfende Dienste
von Drittanbietern“ im Bereich Souveränität, und lassen Sie jeden maschinellen Vorschlag vor dem Veröffentlichen von
einem Menschen durchsehen.

## Verwandte Seiten

- [Workflows](../../../anleitungen/workflows.md)
- [Rollen und Rechte für Übersetzer](rollen-und-rechte-fuer-uebersetzer.md)
- [Begriffsglossare](begriffsglossare.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
