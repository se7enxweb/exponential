---
title: "Lektion 1.3: Wer es nutzen darf, und wie"
audience: trainer, editor, admin
category: training
order: 13
summary: Die Teilnehmenden nennen die wichtigsten expai-Richtlinien, erklären, dass jedes Nachschlagen und Schreiben mit den eigenen Inhaltsberechtigungen des Redakteurs läuft, und entwerfen Rollen für einen Redakteur, einen erfahrenen Nutzer und einen Datenschutzbeauftragten. 30 Minuten.
keywords: exponential intelligence, expai, berechtigungen, richtlinien, expai/write, expai/log, expai/vector, expai/chain, expai/summary, expai/model_use, ezjscore call, rollen, datenschutzbeauftragter, permissions, policies, roles, data protection officer
type: help
---
# Lektion 1.3: Wer es nutzen darf, und wie

Was der Assistent darf, ist nie mehr, als der Redakteur darf. Darüber hinaus entscheidet die Website mit Richtlinien,
wer den Assistenten überhaupt nutzen darf und welche seiner Teile. Diese Lektion erklärt beide Ebenen und entwirft drei
Rollen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Exponential Intelligence](../README.md) |
| Modul | [Modul 1: Exponential Intelligence kennenlernen](README.md) |
| Dauer | 30 Minuten (15 Präsentation, 15 Übung) |
| Niveau | Einsteiger |
| Zielgruppe | Redakteure und die Administratoren, die ihnen Zugang geben |
| Voraussetzungen | [Lektion 1.2](das-exponential-intelligence-dashboard.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- erklären, dass jedes Nachschlagen und Schreiben als der Redakteur läuft, mit dessen Inhaltsberechtigungen;
- die Richtlinien `expai/write`, `expai/log`, `expai/vector`, `expai/chain`, `expai/model_use` und `expai/summary`
  nennen und sagen, was jede öffnet;
- erklären, warum auch `ezjscore/call` nötig ist;
- eine Rolle für einen Redakteur, einen erfahrenen Nutzer und einen Datenschutzbeauftragten entwerfen.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: die Seite zu den Berechtigungen der Erweiterung (`extension/expai/doc/permissions.md`, unter **EI: Help**).

1. **Als der Redakteur** (4 Minuten). Das Lesen läuft über die Prüfungen des Kernels für den aktuellen Benutzer. Das
   Anlegen eines Entwurfs prüft `content/create` auf dem übergeordneten Element für diese Klasse und Sprache, mit jeder
   Einschränkung der Rolle. Das Ändern prüft `content/edit`. Der Assistent verwendet nie ein anderes Konto und wechselt
   nie den Benutzer.
2. **Die wichtigsten Richtlinien** (6 Minuten):

   | Richtlinie | Öffnet |
   |---|---|
   | `expai/write` | Den Tab Intelligence, die Bedienelemente im Bearbeitungsformular, die Dashboard-Seite und den Block, das Log der eigenen Anfragen, den Bericht „Missing alt text“, die Hilfeseiten |
   | `expai/log` | Die Anfragen und Versionen aller; die Ansicht **Site** des Dashboards |
   | `expai/vector` | Vector (mit den Einschränkungen Subtree, Section, Scope, Review) |
   | `expai/chain` | KI-Ketten (Einschränkung Chain) |
   | `expai/model_use` | Modelle von Anbietern außerhalb der offenen Liste (Einschränkung Provider) |
   | `expai/summary` | Die druckbare Seite **EI: Summary**, ohne jeden Inhaltszugriff |

   Weitere Richtlinien gibt es für die Modellverwaltung, Einstellungen, Web-Werkzeuge, MCP-Werkzeuge,
   Dienstwerkzeuge, Übersetzungsabgleich und eine Überholspur; der Trainer nennt sie nur für Administratoren.
3. **ezjscore** (2 Minuten). Der Browser erreicht den Assistenten über `ezjscore/call`; die Rolle braucht es, ohne
   Einschränkung oder mit der Funktionsliste `expai`. Viele Installationen geben es allen angemeldeten Benutzern.
4. **Nicht angezeigt** (1 Minute). Ohne `expai/write` werden der Tab, der Menüeintrag und der Dashboard-Block gar nicht
   angezeigt.

### Häufige Fragen

**Kann ein Redakteur mit dem Assistenten Inhalte anlegen, wo er es nicht darf?** Nein. Anlegen und Ändern laufen über
die eigenen Inhaltsberechtigungen des Redakteurs.

**Lässt sich Vector auf einen Teil der Website beschränken?** Ja: Die Richtlinie `expai/vector` hat die
Einschränkungen Subtree, Section, Scope (Knoten, Teilbaum, Klasse) und Review (gestuft, direkt).

**Wer sollte `expai/summary` bekommen?** Ein Datenschutzbeauftragter: Es braucht keine andere expai-Richtlinie und
keinen Inhaltszugriff.

### Stolperfallen

- **`expai/log` allen geben.** Es zeigt die Anfragen aller; Redakteure brauchen nur ihre eigenen.
- **`ezjscore/call` vergessen.** Der Tab erscheint, aber nichts funktioniert.
- **Direkte Prüfung bei Massenarbeit.** Lassen Sie Vector für die meisten Redakteure gestuft (Lektion 4.2).

## Praktische Übung

Verwenden Sie Papier und am Beamer die Administratorsitzung des Trainers auf der Schulungsinstallation.

1. Der Trainer öffnet unter **Benutzer** > **Rollen und Richtlinien** die Rolle, die für diesen Kurs verwendet wird,
   und zeigt ihre `expai`-Richtlinien.
2. Notieren Sie, welche der sechs wichtigsten Richtlinien die Kursrolle hat.
3. Entwerfen Sie zu zweit auf Papier:
   - eine Rolle „Redakteur mit Assistent“: `expai/write`, `ezjscore/call` (Funktionsliste `expai`) und die
     Inhaltsrichtlinien, die die Redakteure bereits haben;
   - eine Rolle „Erfahrener Redakteur“: zusätzlich `expai/vector` mit Review **staged** und einer
     Teilbaum-Einschränkung, dazu `expai/chain`;
   - eine Rolle „Datenschutzbeauftragter“: nur `expai/summary`.
4. Schreiben Sie zu jeder Rolle einen Satz dazu, was die Person tun kann und was nicht.
5. Der Trainer vergleicht die Entwürfe von zwei Paaren mit der Tabelle.

## Erwartetes Ergebnis

- Jedes Paar hat drei Rollenentwürfe, die zur Tabelle passen.
- Alle Teilnehmenden können erklären, warum der Assistent nicht mehr tun kann als der Redakteur.
- Der Trainer prüft zwei Entwürfe am Beamer.

## Quiz

1. Wessen Inhaltsberechtigungen gelten, wenn der Assistent einen Entwurf anlegt?
2. Welche Richtlinie zeigt den Tab Intelligence und die Dashboard-Seite?
   A. `expai/log` B. `expai/write` C. `expai/summary` D. `expai/vector`
3. Warum braucht eine Rolle auch `ezjscore/call`?
4. Was gibt `expai/summary` einem Datenschutzbeauftragten?
5. Nennen Sie zwei Einschränkungen von `expai/vector`.

<details>
<summary>Antworten</summary>

1. Die des angemeldeten Redakteurs.
2. **B.**
3. Der Browser erreicht den Assistenten über ezjscore-Aufrufe der Funktionsliste `expai`.
4. Die druckbare Seite **EI: Summary**, ohne eine andere expai-Richtlinie oder Inhaltszugriff.
5. Zwei beliebige aus Subtree, Section, Scope, Review.

</details>

## Weiterführende Informationen

- [Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md): der Rolleneditor.
- Die Seite zu den Berechtigungen unter **EI: Help** (`extension/expai/doc/permissions.md`).

## Verwandte Seiten

- [Modul 1: Exponential Intelligence kennenlernen](README.md)
- [Exponential Intelligence: Kursübersicht](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
