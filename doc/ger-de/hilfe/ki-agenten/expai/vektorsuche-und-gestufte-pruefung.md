---
title: Vector und gestufte Prüfung
audience: ai-agents, editor, admin
category: ai-agents
order: 29
summary: Vector, der Arbeitsbereich mit Threads von Exponential Intelligence, für kontrollierte Massen- und agentische Arbeit - ein Geltungsbereich, den der Thread berühren darf, Planmodus mit einer Kostenschätzung vor Massenarbeit, gestufte Prüfung jeder Änderung je Feld und Teil, bevor etwas geschrieben wird, Kontrollpunkte, zu denen Sie zurückkehren können, und die Einschränkungen von expai/vector, die ihn begrenzen.
keywords: Vector, expai/vector, Threads, agentisch, Geltungsbereich, Planmodus, propose_plan, gestufte Prüfung, direkte Prüfung, Reiter Changes, Kontrollpunkte, wiederherstellen, Massenarbeit, parallele Läufe, Lanes, threads, agentic, scope, plan mode, staged review, direct review, Changes tab, checkpoints, restore, bulk work, parallel runs, lanes
type: help
---
# Vector und gestufte Prüfung

Diese Seite richtet sich an Redakteure, die den Assistenten größere Aufgaben erledigen lassen möchten (viele Objekte, mehrere
Sprachen), und an Administratoren, die entscheiden, wie weit er gehen darf. Vector ist der agentischste Teil von Exponential
Intelligence: Ein Thread plant, arbeitet viele Schritte durch und läuft im Hintergrund. Er ist auch der am stärksten kontrollierte.

## Inhalt

1. [Was ein Thread ist](#was-ein-thread-ist)
2. [Geltungsbereich: was ein Thread berühren darf](#geltungsbereich-was-ein-thread-berühren-darf)
3. [Planmodus: zuerst ein Plan und eine Schätzung](#planmodus-zuerst-ein-plan-und-eine-schätzung)
4. [Gestufte Prüfung: nichts wird geschrieben, bis Sie es annehmen](#gestufte-prüfung-nichts-wird-geschrieben-bis-sie-es-annehmen)
5. [Kontrollpunkte](#kontrollpunkte)
6. [Grenzen, die ein Administrator setzt](#grenzen-die-ein-administrator-setzt)

## Was ein Thread ist

Vector (`expai/vector`, im linken Menü des Reiters Dashboard) ist ein Arbeitsbereich mit **Threads**
([Dokumentation der Erweiterung: Vector](https://github.com/se7enxweb/expai/blob/main/doc/vector.md)). Für jeden Thread legen Sie
fest, was er berühren darf, wie er arbeitet (planen oder schreiben, gestufte oder direkte Prüfung), welches Modell und welche Lane
er verwendet; Sie starten ihn, halten ihn an, reihen Folgeaufgaben ein und sehen, was er gekostet hat. Threads sind privat für ihren
Eigentümer; Freigebende einer Version, die ein Thread geschrieben hat, dürfen ihn nur lesend öffnen.

Vector braucht `expai/write` und `expai/vector`.

## Geltungsbereich: was ein Thread berühren darf

Der Geltungsbereich ist eine Menge von Knoten, Teilbäumen (ein Knoten und alles darunter), Klassen und Sprachen. Er wird beim
Speichern und erneut bei jedem Lauf gegen Ihren Lesezugriff und die Einschränkungen von `expai/vector` geprüft. Die
Schreibwerkzeuge schreiben nur innerhalb des Geltungsbereichs.

## Planmodus: zuerst ein Plan und eine Schätzung

**Massenarbeit** ist mehr als ein Objekt oder mehr als eine Sprache oder jeder Teilbaum oder jede Klasse im Geltungsbereich.
Massenarbeit beginnt im **Planmodus** und ist immer **gestuft**:

1. Der Thread hat die Lesewerkzeuge und `propose_plan`, keine Schreibwerkzeuge.
2. Das Modell schlägt einen Plan vor: ein Element je Objekt und Aktion (anlegen, aktualisieren, übersetzen), mit Sprachen, Feldern
   und einer Anweisung je Element, dazu Annahmen, Fragen und Risiken.
3. Jedes Element wird gegen den Geltungsbereich und Ihre Berechtigungen geprüft; abgelehnte Elemente werden durchgestrichen
   gezeigt und laufen nie.
4. Der Reiter **Plan** zeigt die Elemente mit einer Schätzung je Element und den Grenzen, die der Plan berührt. Sie können die
   Anweisung jedes Elements bearbeiten, Elemente abwählen und dann **Approve and run**, **Ask for changes** oder **Reject** wählen.

Ein freigegebener Plan kann seine Elemente nebeneinander ausführen, jedes mit eigenem Fortschritt und eigenen Kosten.

## Gestufte Prüfung: nichts wird geschrieben, bis Sie es annehmen

Mit **Review: Staged** (dem Standard und immer bei Massenarbeit) schreiben die Schreibwerkzeuge keine Entwürfe. Sie stufen
**Vorschläge** ein, je einen pro Objekt, Sprache und Feld. Im Reiter **Changes** nehmen Sie jeden Vorschlag an oder lehnen ihn ab,
Teil für Teil und Feld für Feld. Nur die angenommenen Teile werden in Entwürfe geschrieben, und Ihre Bearbeitungsberechtigung wird
in diesem Moment erneut geprüft.

| Prüfung | Wirkung |
|---|---|
| Staged | Zuerst Vorschläge; Sie nehmen Teile an; dann Entwürfe |
| Direct | Entwürfe werden sofort geschrieben (nie veröffentlicht); nur ein Objekt in einer Sprache und nur dort, wo Ihre Rolle es erlaubt |

Rich-Text-Vorschläge werden beim Einstufen über den Eingabeparser des Kernels umgewandelt, sodass ungültiges Markup vor der Prüfung
erkannt wird. Ein Wert, der dem veröffentlichten gleicht, wird nicht eingestuft.

## Kontrollpunkte

Jeder Thread behält **Kontrollpunkte**: den Zustand zu Beginn jedes Durchgangs, bevor angenommene Änderungen geschrieben werden und
bevor ein freigegebener Plan läuft. Sie können einen Thread auf einen davon **zurücksetzen**, was rückgängig macht, was der Assistent
seitdem getan hat, ohne Entwürfe von Hand aufzuräumen. In keinem Fall wird etwas veröffentlicht.

## Grenzen, die ein Administrator setzt

Die Richtlinie `expai/vector` hat vier Einschränkungen ([Berechtigungen für KI-Funktionen](berechtigungen-fuer-ki-funktionen.md)):

| Einschränkung | Wirkung |
|---|---|
| Subtree | Jeder Knoten und Teilbaum eines Geltungsbereichs muss in einem davon liegen |
| Section | Jeder Knoten und jede Teilbaumwurzel des Geltungsbereichs muss in einer davon sein |
| Scope | Die Arten von Einträgen im Geltungsbereich, die der Benutzer wählen darf: node, subtree, class |
| Review | staged, direct; ohne direct ist jeder Thread gestuft |

Beispiel aus der Erweiterung: eine Redakteursrolle für einen Bereich der Website, nur Arbeit an Knoten, immer geprüft:

```
module expai, function vector: Subtree /1/2/58/, Scope node, Review staged
```

Die Kosten von Hintergrundläufen zählen gegen die Grenzen und Budgets wie jede andere Anfrage
([Kostenkontrolle](../steuerung/kostenkontrolle.md)).

## Verwandte Seiten

- [Ketten und eingereihte Arbeit](ketten-und-eingereihte-arbeit.md)
- [Entwürfe und menschliche Prüfung](entwuerfe-und-menschliche-pruefung.md)
- [Rollen und minimale Rechte](../steuerung/rollen-und-minimale-rechte.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
