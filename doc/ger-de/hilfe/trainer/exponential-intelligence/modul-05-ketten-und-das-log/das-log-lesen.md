---
title: "Lektion 5.2: Das Log lesen"
audience: trainer, editor, admin
category: training
order: 52
summary: Die Teilnehmenden öffnen EI: Log, lesen die nach Durchgängen gruppierten Anfragen, filtern nach Datum, Aktion, Modell, Anbieter, Region und Fehlern, wechseln zu Versions written und exportieren die gefilterten Anfragen als CSV. 30 Minuten.
keywords: exponential intelligence, log, ei log, requests and usage, versions written, durchgänge, filter, aktion, modell, anbieter, region, errors only, export csv, kosten, tokens, turns, action, model, provider, cost
type: help
---
# Lektion 5.2: Das Log lesen

Jede Anfrage des Assistenten wird festgehalten: wer gefragt hat, welches Modell geantwortet hat, wie lange es gedauert
hat, was es gekostet hat und welche Versionen es geschrieben hat. Unter **EI: Log** prüfen Redakteure ihre eigene
Nutzung und Administratoren die der Website. Diese Lektion liest es.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Exponential Intelligence](../README.md) |
| Modul | [Modul 5: Ketten und das Log](README.md) |
| Dauer | 30 Minuten (10 Präsentation, 20 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Redakteure; Administratoren mit `expai/log` |
| Voraussetzungen | [Lektion 5.1](ki-ketten.md): Die Person hat in diesem Kurs Anfragen gestellt |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- **EI: Log** öffnen und zwischen **Requests and usage** und **Versions written** wechseln;
- einen Durchgang und seine Anfragen öffnen;
- nach **From**, **To**, **Action**, **Model**, **Provider**, **Region** und **Errors only** filtern und **Reset**
  verwenden;
- die gefilterten Anfragen mit **Export CSV** exportieren;
- sagen, was das Log nie festhält.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: die Seiten zum Dashboard und zur Nutzung unter **EI: Help** (`extension/expai/doc/dashboard.md`,
`extension/expai/doc/usage-tracking.md`).

1. **Zwei Ansichten** (2 Minuten). **Requests and usage** und **Versions written**. Redakteure sehen ihre eigenen; mit
   `expai/log` die aller (die Seite sagt *Everyone's activity.*), mit den Filtern **User ID** und **Node ID**.
2. **Durchgänge** (3 Minuten). Anfragen werden zu Durchgängen gruppiert: eine Nachricht im Bereich, eine Feldaktion,
   ein Fill the fields, mit jeder Anfrage, zu der es geführt hat. Ein Durchgang öffnet sich zu seinen Anfragen und
   Werkzeugaufrufen. Darüber stehen Summen, pro Tag und (mit `expai/log`) pro Benutzer.
3. **Filter** (3 Minuten). **Action** (`panel`, `fill`, `field:write`, `field:rewrite`, `field:shorten`,
   `field:translate`, `field:suggest` und für Administratoren `probe` und `test`), **Model**, **Provider**, **Region**,
   **Errors only**; **Filter** und **Reset**. **Export CSV** liefert die gefilterten Anfragen ohne Anfragetexte, mit
   Benutzern nur als IDs.
4. **Datenschutz** (2 Minuten). Prompts, Antworten und Schlüssel sind standardmäßig nicht Teil der
   Nutzungsaufzeichnungen; das Audit-Protokoll hält sie nie fest.

### Häufige Fragen

**Warum haben manche Anfragen keinen Preis?** Das Modell hat keinen Preis in der Tabelle der Website; die Seite sagt,
wie viele es sind, und verlinkt auf sie.

**Kann ich sehen, was ich eingegeben habe?** Das Entwurfslog bewahrt eine kurze Zusammenfassung der Anfrage für eine
gewisse Zeit auf; vollständige Anfragen werden nur dort protokolliert, wo ein Administrator das eingeschaltet hat.

**Wer sieht meine Anfragen?** Sie und Inhaber von `expai/log`.

### Stolperfallen

- **Personenbezogene Daten exportieren.** Die CSV enthält Benutzer-IDs; behandeln Sie sie als personenbezogene Daten.
- **Eine Anfrage als Kosten eines Durchgangs lesen.** Ein Durchgang kann mehrere Anfragen haben; öffnen Sie ihn.

## Praktische Übung

Verwenden Sie die Schulungsinstallation und Ihr Schulungskonto.

1. Öffnen Sie **EI: Log** in der linken Spalte des Reiters Dashboard.
2. Lesen Sie die Summen oben. Notieren Sie Ihre Anfragen, Tokens und geschätzten Kosten in diesem Kurs bis jetzt.
3. Öffnen Sie Ihren neuesten Durchgang und lesen Sie seine Anfragen: Modell, Zeit, Tokens.
4. Setzen Sie **Action** auf `field:shorten` (oder eine andere Aktion, die Sie verwendet haben) und klicken Sie auf
   **Filter**. Dann **Reset**.
5. Kreuzen Sie **Errors only** an und klicken Sie auf **Filter**. Gibt es welche? Lesen Sie gegebenenfalls eine.
6. Wechseln Sie zu **Versions written** und suchen Sie die Entwürfe und Versionen, die der Assistent für Sie
   geschrieben hat.
7. Klicken Sie mit einem Datumsfilter für heute auf **Export CSV** und öffnen Sie die Datei in einer
   Tabellenkalkulation. Prüfen Sie, dass sie keine Anfragetexte enthält.

## Erwartetes Ergebnis

- Die Notizen enthalten die eigenen Summen und die Einzelheiten eines Durchgangs.
- Die CSV ließ sich öffnen und enthielt Anfragen ohne Anfragetexte.
- Der Trainer prüft das, indem er eine Person bittet, eine fehlgeschlagene Anfrage zu finden (oder zu erklären, warum
  es keine gibt).

## Quiz

1. Nennen Sie die zwei Ansichten von **EI: Log**.
2. Was ist ein Durchgang?
3. Welche Richtlinie zeigt die Anfragen aller?
   A. `expai/write` B. `expai/log` C. `expai/summary` D. `expai/vector`
4. Was lässt **Export CSV** weg?
5. Richtig oder falsch: Das Audit-Protokoll hält Prompts und Antworten fest.

<details>
<summary>Antworten</summary>

1. **Requests and usage** und **Versions written**.
2. Eine Nachricht im Bereich, eine Feldaktion oder ein Fill the fields, mit jeder Anfrage, zu der es geführt hat.
3. **B.**
4. Die Anfragetexte; Benutzer erscheinen nur als IDs.
5. **Falsch.** Prompts, Antworten und Schlüssel werden dort nie festgehalten
   ([expai](../../../../../eng-us/features/6.0/extensions/expai.md#for-administrators)).

</details>

## Weiterführende Informationen

- [expai: Für Administratoren](../../../../../eng-us/features/6.0/extensions/expai.md#for-administrators): Nutzung, Kosten und
  Audit.
- Die Seiten zur Nutzungserfassung und zum Audit-Protokoll unter **EI: Help**.

## Verwandte Seiten

- [Modul 5: Ketten und das Log](README.md)
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
