---
title: Exponential Intelligence
audience: customer, editor, admin
category: customer-success
order: 12
summary: Die Sicht des Betreibers auf Exponential Intelligence, den in die Administration eingebauten Schreibassistenten - was er für die Redaktion tut, die Regel „nur Entwürfe“, Anbieter und wohin Texte gehen, Kosten und Grenzen, wer Zugriff erhält und die Entscheidungen, bevor Sie ihn einschalten.
keywords: Exponential Intelligence, expai, Schreibassistent, KI, Entwürfe, Anbieter, Datenrichtlinie, Kosten, Grenzen, Vector, Alternativtext, Übersetzungsabgleich, writing assistant, AI, drafts, providers, data policy, costs, limits, alt text, translation sync
type: help
---
# Exponential Intelligence

Diese Seite ist für den Betreiber, der entscheidet, ob und wie das Team den Schreibassistenten nutzt. Exponential
Intelligence ist in die Administration eingebaut: Redakteure bitten in normaler Sprache um neue Inhalte oder
Änderungen, und der Assistent schlägt nach, was er braucht, und schreibt **Entwürfe**, als der angemeldete Redakteur
und mit dessen Berechtigungen (`README.md`, „Exponential Intelligence“). Wie man ihn gut nutzt, steht unter
[KI-Entwürfe verantwortungsvoll nutzen](ki-entwuerfe-verantwortungsvoll-nutzen.md).

## Inhalt

1. [Was er für die Redaktion tut](#was-er-für-die-redaktion-tut)
2. [Die Regel, die Sie schützt](#die-regel-die-sie-schützt)
3. [Wohin Texte gehen](#wohin-texte-gehen)
4. [Kosten und Grenzen](#kosten-und-grenzen)
5. [Wer Zugriff erhält](#wer-zugriff-erhält)
6. [Entscheidungen, bevor Sie ihn einschalten](#entscheidungen-bevor-sie-ihn-einschalten)

## Was er für die Redaktion tut

Aus `README.md` und [expai: Exponential Intelligence](../../../../eng-us/features/6.0/extensions/expai.md):

| Wo | Was |
|---|---|
| Der Reiter **Intelligence** eines Elements | eine Unterhaltung pro Benutzer und Element; jedes Nachschlagen und jeder Entwurf ist ein Schritt; die geschriebenen Entwürfe sind mit Links zum Bearbeiten, Ansehen und Verwerfen aufgelistet |
| Das Bearbeitungsformular | „Fill the fields“ aus einem Briefing; Write, Rewrite, Shorten, Translate und Suggest pro Feld; Voreinstellungen für Titel, Zusammenfassung, Meta-Beschreibung, Schlagwörter; ein Menü **Improve**; Werte gehen zur Prüfung ins Formular |
| Bilder | Vorschläge für Alternativtexte von einem Bildmodell, und ein Bericht „Missing alt text“ für einen Teilbaum |
| **Vector** | ein Arbeitsbereich für größere Aufgaben: ein Plan mit Kostenschätzung, Prüfung jeder Änderung pro Feld, bevor etwas geschrieben wird, **What changed** mit Zurücknehmen |
| Ketten und Übersetzungsabgleich | Schreiben, Prüfen, Überarbeiten und Übersetzen in Schritten mit einem menschlichen Prüfpunkt; Übersetzungen werden auf Stand gebracht, nachdem sich die Quelle geändert hat |
| Ein Dashboard | Kosten, Tokens, Anfragen, Entwürfe und Grenzen |

## Die Regel, die Sie schützt

- **Nur Entwürfe.** Er veröffentlicht nie von sich aus. „Zur Freigabe senden“ führt den normalen
  Veröffentlichungsvorgang als der Redakteur aus, sodass ein Freigabe-Workflow die Version für die freigebende Person
  zurückhält (`README.md`).
- **Die eigenen Berechtigungen des Redakteurs.** Jedes Nachschlagen und jeder Entwurf läuft mit den
  Inhaltsberechtigungen des Redakteurs.
- **Gekennzeichnet.** Jede Version, die er geschrieben hat, trägt die Kennzeichnung „Exponential Intelligence“ in
  Meine Entwürfe, in Versionsansichten, Bearbeitungsformularen und Freigabeelementen.
- **Protokolliert.** Jede Anfrage, jeder Entwurf und jede Zustandsänderung ist ein Ereignis des Prüfprotokoll-Kanals
  `ai`; Prompts, Antworten und Schlüssel werden nie aufgezeichnet.

## Wohin Texte gehen

| Anbieter | Region |
|---|---|
| Anthropic (Claude) | wie vom Anbieter angegeben |
| Ein OpenAI-kompatibler Dienst (Melious.ai in der EU-Region, Hugging Face, OpenRouter, Mistral und andere) | wie vom Anbieter angegeben |
| Ein lokales Open-Weight-Modell über Ollama | auf Ihrem eigenen Server; die Daten verlassen ihn nie |

Jeder Anbieter gibt seine Region (`us`, `eu`, `local`) und seine Aufbewahrung an; Regeln pro Sektion und Inhaltsklasse
entscheiden, welche Inhalte wohin gehen dürfen, und jede Anfrage hält fest, wohin sie ging. **EI: Summary** ist eine
druckbare Seite für einen Datenschutzbeauftragten (`README.md`). Nur Modelle, die einen Live-Test des Tool-Callings
bestanden haben oder per Konfiguration als vertrauenswürdig gelten, dürfen Entwürfe schreiben.

## Kosten und Grenzen

Ein Nutzungsprotokoll hält die geschätzten Kosten jeder Anfrage fest; Grenzen lassen sich pro Benutzer, Tag, Monat,
Website und Anbieter setzen, dazu Budgets für Arbeit in der Warteschlange (`README.md`). Bitten Sie Ihren
Administrator, die Grenzen zu setzen, **bevor** das Team beginnt, und sehen Sie sich das Dashboard monatlich an. Die
Preise der Anbieter legen die Anbieter fest, nicht Exponential.

## Wer Zugriff erhält

Die Funktionen des Assistenten sind eigene Richtlinien (`expai/write`, `expai/vector`, ...). Auf einer neuen
Installation hat nur die Rolle Administrator diese (`README.md`). Geben Sie `expai/write` (und `ezjscore/call`) den
Redakteuren, die ihn nutzen sollen; lassen Sie Vector und die Verwaltung der Anbieter bei weniger Personen. Der
Schalter `[AISettings] Enabled` schaltet ihn überall auf einmal oder pro Siteaccess ab.

> **Hinweis**
> Eine neue Installation aktiviert die Erweiterung für die Siteaccesses der Administration, aber sie **sendet nichts
> irgendwohin, bis ein Schlüssel eines Anbieters hinzugefügt wird** (`README.md`). Der Schlüssel gehört in eine
> Override-Einstellungsdatei, nie in eine eingecheckte Datei. Die Cron-Teile `expqueue` und `expai` führen seine Arbeit
> aus der Warteschlange aus.

## Entscheidungen, bevor Sie ihn einschalten

- [ ] Welcher Anbieter, in welcher Region, mit welcher Aufbewahrung: mit Ihrem Datenschutzbeauftragten entschieden.
      Dies ist keine Rechtsberatung.
- [ ] Welche Sektionen oder Klassen nie an einen externen Anbieter gesendet werden dürfen.
- [ ] Wer `expai/write` erhält, wer mehr.
- [ ] Monatliche Grenzen und Grenzen pro Benutzer.
- [ ] Dass Freigaben dort bestehen bleiben, wo sie waren, auch für KI-Entwürfe ([Freigabe und Prüfung](../alltag/freigabe-und-pruefung.md)).
- [ ] Die Regeln des Teams: [KI-Entwürfe verantwortungsvoll nutzen](ki-entwuerfe-verantwortungsvoll-nutzen.md).

## Verwandte Seiten

- [KI-Entwürfe verantwortungsvoll nutzen](ki-entwuerfe-verantwortungsvoll-nutzen.md)
- [Unterstützte Übersetzung mit Exponential Intelligence](../../unternehmen/mehrsprachig/unterstuetzte-uebersetzung-mit-exponential-intelligence.md)
- [expai: Exponential Intelligence](../../../../eng-us/features/6.0/extensions/expai.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
