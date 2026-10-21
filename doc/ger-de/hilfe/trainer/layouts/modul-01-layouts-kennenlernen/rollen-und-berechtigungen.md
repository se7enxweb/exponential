---
title: "Lektion 1.3: Wer was mit Layouts tun darf"
audience: trainer, editor, website-creator, admin
category: training
order: 13
summary: Die Teilnehmenden nennen die Richtlinien der drei Layouts-Module, entscheiden, welche Rolle ein Redakteur, ein Seitenbauer und ein Administrator brauchen, und vereinbaren Teamregeln für das Veröffentlichen von Layouts. 30 Minuten.
keywords: exponential layouts, berechtigungen, richtlinien, rollen, explayouts read, explayouts edit, explayouts_ui, explayouts_ui_api, transfer export, transfer import, teamregeln, permissions, policies, roles, team rules
type: help
---
# Lektion 1.3: Wer was mit Layouts tun darf

Ein Layout kann mit einem Klick auf **Layout veröffentlichen** das Aussehen von Hunderten Seiten ändern. Bevor die
Gruppe etwas baut, erklärt der Trainer, wer Layouts lesen, bearbeiten und veröffentlichen darf, und das Team
vereinbart Regeln. Diese Lektion ist kurz und besteht vor allem aus Gespräch; sie erspart später Ärger.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Exponential Layouts](../README.md) |
| Modul | [Modul 1: Exponential Layouts kennenlernen](README.md) |
| Dauer | 30 Minuten (15 Präsentation, 15 Übung) |
| Niveau | Einsteiger |
| Zielgruppe | Redakteure, Website-Ersteller und die Administratoren, die ihnen Zugang geben |
| Voraussetzungen | [Lektion 1.2](sich-zurechtfinden.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die drei Layouts-Module und ihre Richtlinien **read** und **edit** nennen;
- erklären, was ein Konto mit reinem Leserecht tun kann und was nicht;
- eine Rolle für einen Seitenbauer und eine für einen Redakteur vorschlagen, der nur bestehende Layouts füllt;
- drei Teamregeln für Entwürfe und das Veröffentlichen vereinbaren.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Exponential Layouts: Berechtigungen](../../../../bc/6.0/layouts.md#berechtigungen) und
[explayouts_ui: Einstellungen](../../../../funktionen/6.0/erweiterungen/explayouts_ui.md#einstellungen).

1. **Drei Module** (4 Minuten). Die Layouts-Suite hat drei Module, jedes mit Richtlinien im Rolleneditor:
   - `explayouts` (der Kern): **read** und **edit**;
   - `explayouts_ui` (der Reiter Layouts und seine Bildschirme): **read**, **edit** sowie **transfer_export** und
     **transfer_import**, um Layouts zwischen Installationen zu übertragen;
   - `explayouts_ui_api` (der visuelle Editor und seine JSON-API): **read** und **edit**.
2. **Read und edit** (3 Minuten). Read öffnet das Dashboard, die Listen und die Vorschauen. Edit ist nötig, um
   Layouts anzulegen, Blöcke zu ändern, zu veröffentlichen und Zuordnungen zu ändern. Wenn der Editor lädt, aber jede
   Liste leer ist, prüfen Sie die Richtlinien, bevor Sie die Daten verdächtigen.
3. **Typische Rollen** (4 Minuten):

   | Person | Braucht |
   |---|---|
   | Redakteur, der nur ansieht | read auf den drei Modulen |
   | Seitenbauer | read und edit auf den drei Modulen |
   | Administrator | alles, einschließlich transfer export und import |

4. **Teamregeln** (4 Minuten). Entwürfe gehören zum Layout, nicht zu einer Person: Zwei Personen, die gleichzeitig
   dasselbe Layout bearbeiten, überschreiben sich gegenseitig. Vereinbaren Sie, wer welches Layout bearbeitet, wer
   veröffentlicht, und dass Änderungen an gemeinsamen Layouts angekündigt werden (sie betreffen viele Seiten).

### Häufige Fragen

**Kann ein Redakteur auf einige Layouts beschränkt werden?** Die Übergabe-Checkliste des Leitfadens für
Website-Ersteller empfiehlt, Redakteuren nur die Layouts zu geben, für die sie zuständig sind; wie fein eine
Einschränkung sein kann, hängt von den Richtlinien der Installation ab. Prüfen Sie es im Rolleneditor Ihrer
Installation.

**Warum meldet der visuelle Editor „You don't have permission to edit this layout.“?** Das Konto hat read, aber nicht
edit auf den Layouts-Modulen.

**Wer darf Zuordnungen ändern?** Für die Zuordnungsbildschirme ist edit auf `explayouts_ui` nötig.

### Stolperfallen

- **Seitenbauern Administratorrechte geben.** Das ist einfacher und gefährlich. Geben Sie stattdessen die
  Layouts-Richtlinien.
- **Zwei Seitenbauer, ein Layout.** Die Arbeit der zweiten Person ersetzt stillschweigend die der ersten. Die Teamregel
  verhindert das.
- **Den Editor-Siteaccess vergessen.** Redakteure, die nur dort arbeiten, erreichen Layouts überhaupt nicht.

## Praktische Übung

Verwenden Sie die Schulungsinstallation. Der Trainer meldet sich am Beamer als Administrator an; die Teilnehmenden
arbeiten auf Papier.

1. Der Trainer öffnet **Benutzer** > **Rollen und Richtlinien** und die Rolle, die für diesen Kurs verwendet wird. Die
   Gruppe liest ab, welche Layouts-Richtlinien sie hat.
2. Die Teilnehmenden notieren für jedes der drei Module, ob die Rolle **read**, **edit** oder beides hat.
3. Die Teilnehmenden melden sich mit ihrem eigenen Schulungskonto an und öffnen den Reiter **Layouts**. Prüfen Sie,
   dass **Neues Layout** angeboten wird.
4. Entwerfen Sie zu zweit auf Papier eine Rolle „Seitenbauer“ und eine Rolle „Layout-Betrachter“ mit den Richtlinien
   aus der Tabelle oben.
5. Vereinbaren Sie in der Gruppe drei Teamregeln für diesen Kurs, zum Beispiel: „Jede Person bearbeitet nur ihre
   eigenen Layouts“; „Gemeinsame Layouts ändert nur der Trainer“; „Veröffentlicht wird erst nach einer Vorschau“.
6. Der Trainer schreibt die Regeln für den Rest des Kurses an die Tafel.

## Erwartetes Ergebnis

- Alle Teilnehmenden können den Reiter **Layouts** öffnen und sehen **Neues Layout**.
- Jedes Paar hat zwei Rollenentwürfe, die zur Tabelle passen.
- Drei Teamregeln stehen an der Tafel.
- Der Trainer prüft die Rollenentwürfe von zwei Paaren.

![Die Rollen, darunter diejenigen, die Layouts bearbeiten dürfen](../../../../../eng-us/help/images/trainers/ly-roles.webp)

## Quiz

1. Nennen Sie die drei Layouts-Module.
2. Welche Richtlinie ist nötig, um Blöcke zu ändern und Layouts zu veröffentlichen?
   A. read B. edit C. transfer_export D. dashboard
3. Mit welchen Richtlinien werden Layouts zwischen Installationen übertragen?
4. Was geschieht, wenn zwei Personen gleichzeitig dasselbe Layout bearbeiten?
5. Eine Person sieht den visuellen Editor, aber jede Liste ist leer. Was prüfen Sie zuerst?

<details>
<summary>Antworten</summary>

1. `explayouts`, `explayouts_ui`, `explayouts_ui_api`.
2. **B.**
3. **transfer_export** und **transfer_import** von `explayouts_ui`.
4. Sie überschreiben sich gegenseitig: Entwürfe gehören zum Layout, nicht zu einer Person
   ([Landingpages mit dem Layout-Editor](../../../ersteller/website-ersteller/landingpages-mit-dem-layouts-editor.md#entwürfe-und-veröffentlichen)).
5. Die Layouts-Richtlinien des Kontos ([Exponential Layouts](../../../../bc/6.0/layouts.md#berechtigungen)).

</details>

## Weiterführende Informationen

- [Exponential Layouts: Berechtigungen](../../../../bc/6.0/layouts.md#berechtigungen)
- [Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md): der Rolleneditor.
- [Landingpages mit dem Layout-Editor](../../../ersteller/website-ersteller/landingpages-mit-dem-layouts-editor.md#übergabe-an-die-redaktion):
  die Übergabe-Checkliste.

## Verwandte Seiten

- [Modul 1: Exponential Layouts kennenlernen](README.md)
- [Exponential Layouts: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
