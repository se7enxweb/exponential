---
title: "Einwand: „Was ist mit den KI-Funktionen?“"
audience: advocate
category: advocacy
order: 36
summary: Wie Sie beide Arten von KI-Einwänden beantworten - „KI ruiniert unsere Inhalte oder gibt unsere Daten preis“ und „wir brauchen mehr KI“ - mit den dokumentierten Fakten zu Exponential Intelligence, den Steuerungsmöglichkeiten der Administratoren und dem, was ein Fürsprecher nicht versprechen darf.
keywords: Einwand, KI, künstliche Intelligenz, Exponential Intelligence, expai, Datenschutz, Privatsphäre, Entwürfe, Hauptschalter, Anbieter, lokales Modell, Kosten, Halluzination, redaktionelle Kontrolle, objection, AI, artificial intelligence, data protection, privacy, drafts, master switch, providers, local model, costs, hallucination, editorial control
type: help
---
# Einwand: „Was ist mit den KI-Funktionen?“

Diese Seite ist für Fürsprecher, die einer der beiden Seiten der KI-Debatte begegnen. Manche wenden etwas ein, **weil**
Exponential einen Schreibassistenten hat; andere wenden ein, dass es mit KI **nicht genug** tut. Beide verdienen
dasselbe: die genauen Fakten über [Exponential Intelligence](gespraechspunkte-exponential-intelligence.md) und die
Steuerungsmöglichkeiten, die es gibt.

## Inhalt

1. [Einwände von KI-Skeptikern](#einwände-von-ki-skeptikern)
2. [Einwände von KI-Begeisterten](#einwände-von-ki-begeisterten)
3. [Die Steuerungsmöglichkeiten der Administratoren](#die-steuerungsmöglichkeiten-der-administratoren)
4. [Antwortvorlagen](#antwortvorlagen)
5. [Sagen Sie nicht](#sagen-sie-nicht)

## Einwände von KI-Skeptikern

| Sie sagen | Antwort | Quelle |
|---|---|---|
| „Die KI veröffentlicht falsche Inhalte.“ | Sie schreibt nur Entwürfe und veröffentlicht nie selbst; ein Mensch veröffentlicht, und Workflows gelten weiterhin | [expai, Kurz gesagt](../../../en/us/features/6.0/extensions/expai.md#in-short), [Für Redakteure](../../../en/us/features/6.0/extensions/expai.md#for-editors) |
| „Sie gibt unsere Daten preis.“ | Sie sendet nichts, bis ein Anbieterschlüssel hinzugefügt wird; Sie wählen die Region (USA, EU) oder ein lokales Modell | [Exponential Intelligence](../../../en/us/help/who-is-the-exponential-foundation.md#exponential-intelligence), [Für Administratoren](../../../en/us/features/6.0/extensions/expai.md#for-administrators) |
| „Wir wollen überhaupt keine KI.“ | Es gibt einen Hauptschalter, auch pro Siteaccess | [Für Administratoren](../../../en/us/features/6.0/extensions/expai.md#for-administrators) |
| „Die Kosten laufen aus dem Ruder.“ | Die Nutzung wird protokolliert, mit Grenzen pro Benutzer, Tag, Monat, Website und Anbieter sowie mit Budgets | ebenda |
| „Niemand wird wissen, was die KI getan hat.“ | Jede Anfrage, jeder Entwurf und jede Zustandsänderung eines Entwurfs ist ein Ereignis im Prüfprotokoll; Prompts, Antworten und Schlüssel werden nie aufgezeichnet | ebenda |
| „Unser Datenschutzbeauftragter braucht Details.“ | **EI: Summary** ist eine druckbare Seite mit Region, Aufbewahrung und Datum der Bedingungen jedes Anbieters | ebenda |

## Einwände von KI-Begeisterten

| Sie sagen | Antwort | Quelle |
|---|---|---|
| „Kann sie ganze Seiten schreiben?“ | Sie kann Felder aus einer Vorgabe füllen und Inhalte entwerfen; eine redaktionelle Person prüft und veröffentlicht | [Für Redakteure](../../../en/us/features/6.0/extensions/expai.md#for-editors) |
| „Kann sie übersetzen?“ | Ja, als Aktion pro Feld und als Ketten über die Job-Warteschlange | ebenda |
| „Kann sie bei der Barrierefreiheit helfen?“ | Sie schlägt Alternativtext vor und meldet Bilder, denen er fehlt | ebenda |
| „Kann ich mein eigenes Modell nutzen?“ | Ein lokales Modell mit offenen Gewichten über Ollama wird unterstützt | [expai, Kurz gesagt](../../../en/us/features/6.0/extensions/expai.md#in-short) |
| „Warum veröffentlicht sie nicht automatisch?“ | Absichtlich: nur Entwürfe, ein Mensch entscheidet | ebenda |

## Die Steuerungsmöglichkeiten der Administratoren

Aus [expai, Für Administratoren](../../../en/us/features/6.0/extensions/expai.md#for-administrators):

- Anbieter und Modelle, mit einer Probe, damit nur getestete Modelle Entwürfe schreiben dürfen;
- eine Datenrichtlinie: Region und Aufbewahrung pro Anbieter, Regeln pro Sektion und Klasse;
- Nutzung, Kosten, Grenzen und Budgets;
- ein Hauptschalter und ein Veto-Hook;
- Ereignisse im Prüfprotokoll;
- eine Übersichtsseite für Datenschutzbeauftragte.

## Antwortvorlagen

```text
TO A SKEPTIC
Fair worry. Exponential Intelligence only writes drafts - it never publishes - and it works with the
editor's own permissions. Nothing is sent anywhere until an admin adds a provider key, you can pick a
region or run a local model, and there's a master switch to turn it off. Details: (link to expai page)

TO AN ENTHUSIAST
It does quite a lot: fill fields from a brief, rewrite and translate per field, alt text, chains through
the job queue. It always stops at a draft so a human publishes. (link)
```

## Sagen Sie nicht

- „Es ist sicher für personenbezogene Daten“ oder „DSGVO-konform“. Das hängt vom Anbieter, der Region und der eigenen
  Bewertung der Organisation ab.
- „Die Nutzung ist kostenlos.“ Gehostete Anbieter können Gebühren verlangen.
- „Die KI hat immer recht.“
- „Exponential AI“: Der Name ist Exponential Intelligence ([Kurzkarte zu den Namensregeln](kurzkarte-namensregeln.md)).

## Verwandte Seiten

- [Gesprächspunkte: Exponential Intelligence](gespraechspunkte-exponential-intelligence.md)
- [Mit Einwänden umgehen, Exponential Intelligence und Datenschutz](../../../en/us/help/business/objection-handling.md#exponential-intelligence-and-data-protection)
- [Einwände als Fürsprecher beantworten](einwaende-beantworten.md)
- [Freie Dokumentation](../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
