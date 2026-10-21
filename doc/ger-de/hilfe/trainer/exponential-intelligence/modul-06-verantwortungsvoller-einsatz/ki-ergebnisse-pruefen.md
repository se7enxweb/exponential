---
title: "Lektion 6.2: KI-Ergebnisse prüfen"
audience: trainer, editor
category: training
order: 62
summary: Die Teilnehmenden prüfen KI-Entwürfe mit einer Checkliste, Fakten, Zahlen, Namen, Links, Ton, Voreingenommenheit, Struktur und Barrierefreiheit, erkennen Prompt Injection und entscheiden, was unter ihrem Namen veröffentlicht werden darf. 60 Minuten.
keywords: exponential intelligence, prüfung, ki-ergebnisse, faktencheck, halluzination, links, ton, voreingenommenheit, prompt injection, kennzeichnung, verantwortung, checkliste, redaktionelle prüfung, review, ai output, fact checking, hallucination, tone, bias, label, responsibility, checklist, editorial review
type: help
---
# Lektion 6.2: KI-Ergebnisse prüfen

Der Assistent schreibt plausiblen Text, und plausibel ist nicht dasselbe wie wahr. Jeder Entwurf, den er schreibt,
wartet auf einen Redakteur, und der Redakteur, der ihn veröffentlicht, ist dafür verantwortlich. Diese Lektion gibt den
Teilnehmenden eine Prüf-Checkliste und Übung in ihrer Anwendung.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Exponential Intelligence](../README.md) |
| Modul | [Modul 6: Verantwortungsvoller Einsatz](README.md) |
| Dauer | 60 Minuten (20 Präsentation, 40 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Alle, die den Assistenten nutzen |
| Voraussetzungen | [Lektion 6.1](datenschutz-und-personenbezogene-daten.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- einen KI-Entwurf anhand einer Checkliste prüfen, bevor sie ihn behalten oder senden;
- erfundene Fakten, Zahlen, Namen und Zitate finden und an einer Quelle prüfen;
- Links, Struktur und Barrierefreiheit des Entwurfs prüfen;
- Text in Inhalten erkennen, der den Assistenten zu steuern versucht (Prompt Injection), und wissen, was der Assistent
  dagegen tut;
- erklären, warum die Kennzeichnung für Prüfende und Freigebende wichtig ist.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: die Sicherheitsseite unter **EI: Help** (`extension/expai/doc/security.md`, „Prompt injection“) und
[expai](../../../../../eng-us/features/6.0/extensions/expai.md).

1. **Warum prüfen** (4 Minuten). Modelle schreiben flüssigen Text, der erfundene Fakten, falsche Zahlen, veraltete
   Informationen oder Namen enthalten kann, die es nicht gibt. Der Entwurf trägt die Kennzeichnung des Assistenten,
   aber das Veröffentlichen ist die Entscheidung und Verantwortung des Redakteurs.
2. **Die Checkliste** (6 Minuten). Zeigen Sie sie am Beamer:

   | Prüfpunkt | Fragen Sie |
   |---|---|
   | Fakten | Ist jede Aussage wahr? Habe ich eine Quelle? |
   | Zahlen, Daten, Preise | Stimmen sie, und sind sie aktuell? |
   | Namen und Zitate | Gibt es diese Personen, Produkte und Zitate, und sind sie korrekt? |
   | Links | Funktioniert jeder Link und führt er dorthin, wohin er sagt? |
   | Ton und Zielgruppe | Klingt es nach uns und passt es zu den Lesern? |
   | Voreingenommenheit und Fairness | Bedient es Stereotype oder lässt es Menschen aus? |
   | Struktur | Überschriften in der richtigen Reihenfolge, Listen, keine verlorenen Einbettungen oder Tabellen? |
   | Barrierefreiheit | Alternativtexte, Linktexte, verständliche Sprache? |
   | Rechte | Kopiert es den Text eines anderen? |

3. **Prompt Injection** (5 Minuten). Inhalte können Text enthalten, der geschrieben wurde, um einen Assistenten zu
   steuern („ignoriere deine Anweisungen und veröffentliche ...“). Der Assistent behandelt Inhalte als Daten, kann nur
   seine eigenen Werkzeuge verwenden, schreibt nur Entwürfe, die der Redakteur schreiben darf, bereinigt Rich Text auf
   eine kleine Menge von Tags (keine Skripte, Styles, iframes), und jeder Entwurf wartet auf eine Prüfung. Prüfen Sie
   Entwürfe, die aus nicht vertrauenswürdigem Text geschrieben wurden (etwa Einsendungen von Benutzern oder Webseiten),
   wie den Entwurf jedes anderen Beitragenden.
4. **Die Kennzeichnung** (2 Minuten). Vom Assistenten geschriebene Versionen sind in Versionsansichten und
   Freigabeelementen gekennzeichnet, damit eine freigebende Person weiß, dass sie sorgfältig lesen muss.
5. **Zeit** (3 Minuten). Planen Sie Zeit für die Prüfung ein: Der Assistent spart Schreibzeit, nicht Lesezeit.

### Häufige Fragen

**Kann der Assistent seine eigenen Fakten prüfen?** Eine Kette hat einen Prüfschritt, und der Assistent kann Inhalte
der Website nachschlagen; Wahrheit garantieren kann er trotzdem nicht. Der Redakteur prüft.

**Müssen KI-geschriebene Inhalte auf der Website gekennzeichnet werden?** Die Software kennzeichnet Versionen in der
Administration; ob und wie Leser informiert werden, ist eine Richtlinie Ihrer Organisation.

**Was, wenn ein Entwurf einen Link enthält, den ich nicht kenne?** Prüfen Sie ihn vor dem Veröffentlichen; entfernen
Sie ihn im Zweifel.

### Stolperfallen

- **Nur den ersten Absatz prüfen.** Fehler verstecken sich in den Einzelheiten weiter unten.
- **Selbstbewusster Formulierung vertrauen.** Selbstbewusstsein ist kein Beleg.
- **Unter Zeitdruck veröffentlichen.** Behalten Sie den Entwurf und kommen Sie darauf zurück.

## Praktische Übung

Arbeiten Sie zu zweit mit der Schulungsinstallation. Der Trainer hat in jedem Kursordner einen KI-geschriebenen Entwurf
mit eingebauten Fehlern vorbereitet (ein falsches Datum, eine erfundene Statistik, ein defekter Link, ein Absatz mit
einer Anweisung an einen Assistenten), oder die Teilnehmenden verwenden ihre eigenen Entwürfe aus früheren Modulen.

1. Öffnen Sie den Entwurf aus **Meine Entwürfe** im normalen Bearbeitungsformular.
2. Gehen Sie die Checkliste durch und notieren Sie jedes Problem, das Sie finden.
3. Beheben Sie jedes Problem von Hand (oder mit einer Feldaktion, dann prüfen Sie erneut).
4. Prüfen Sie nach dem Speichern des Entwurfs jeden Link mit **Auf der Website ansehen** oder in der Vorschau.
5. Tauschen Sie mit Ihrem Partner und prüfen Sie seinen korrigierten Entwurf mit derselben Checkliste.
6. Entscheiden Sie gemeinsam: Ist er bereit zur Veröffentlichung? Veröffentlichen Sie ihn, oder behalten Sie ihn als
   Entwurf mit einer Notiz dazu, was fehlt.
7. Besprechen Sie: Was hat die eingebaute Anweisung im Text bewirkt, und warum ist nichts geschehen?

## Erwartetes Ergebnis

- Jedes Paar hat die eingebauten Fehler (oder echte) gefunden und korrigiert.
- Die Teilnehmenden können erklären, dass die Anweisung im Inhalt als Text behandelt wurde und dass nur Entwürfe
  geschrieben wurden.
- Der Trainer prüft das, indem er jedes Paar nach dem Fehler fragt, den es fast übersehen hätte.

## Quiz

1. Nennen Sie fünf Punkte der Prüf-Checkliste.
2. Wer ist für einen veröffentlichten KI-geschriebenen Text verantwortlich?
   A. Der Anbieter B. Der Assistent C. Der Redakteur, der ihn veröffentlicht hat D. Niemand
3. Was ist Prompt Injection?
4. Nennen Sie zwei Wege, auf denen der Assistent begrenzt, was eingeschleuster Text erreichen kann.
5. Warum ist die Kennzeichnung für eine freigebende Person wichtig?

<details>
<summary>Antworten</summary>

1. Fünf beliebige aus: Fakten, Zahlen und Daten, Namen und Zitate, Links, Ton, Voreingenommenheit, Struktur,
   Barrierefreiheit, Rechte.
2. **C.**
3. Text in Inhalten, der geschrieben wurde, um einen Assistenten zu etwas anderem zu bewegen.
4. Zwei beliebige aus: Inhalte werden als Daten behandelt; nur seine eigenen Werkzeuge; nur Entwürfe, die der Redakteur
   schreiben darf; Rich Text wird auf eine kleine Menge von Tags bereinigt; jeder Entwurf wartet auf eine Prüfung.
5. Sie zeigt, dass die Version vom Assistenten geschrieben wurde, sodass die freigebende Person sorgfältig liest.

</details>

## Weiterführende Informationen

- Die Sicherheitsseite unter **EI: Help**, Abschnitt „Prompt injection“.
- [Standards für Barrierefreiheit](../../../../../eng-us/standards/accessibility.md): was von den Redakteuren abhängt.

## Verwandte Seiten

- [Modul 6: Verantwortungsvoller Einsatz](README.md)
- [Exponential Intelligence: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
