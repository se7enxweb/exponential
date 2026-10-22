---
title: Anbieter und Modelle
audience: ai-agents, admin, developer
category: ai-agents
order: 23
summary: Welche KI-Anbieter Exponential Intelligence verwenden kann - Anthropic, jeden OpenAI-kompatiblen Dienst über einen Adapter, ein lokales Open-Weight-Modell oder eine eigene Klasse - was live erprobt ist und was nur gegen Testserver, wie die Modellauswahl funktioniert und was passiert, wenn ein gewähltes Modell nicht antworten kann.
keywords: Anbieter, Modelle, Anthropic, Claude, OpenAI-kompatibel, openai_chat, OpenRouter, Hugging Face, Mistral, Melious, Ollama, lokales Modell, eigener Anbieter, Modellauswahl, Rückfall, Anbieterunterstützung, Region, Aufbewahrung, providers, models, OpenAI-compatible, local model, custom provider, model picker, fallback, provider support, region, retention
type: help
---
# Anbieter und Modelle

Diese Seite richtet sich an Administratoren, die wählen, wohin die Anfragen des Assistenten gehen, und an alle, die fragen "Welche
KI verwendet er?". Die kurze Antwort: die, die Sie konfigurieren. Der Assistent spricht über Adapter mit Anbietern, und der
Administrator entscheidet, welche Anbieter und Modelle aktiv sind und wer sie verwenden darf.

## Inhalt

1. [Die Adapter](#die-adapter)
2. [Was mit welchem Anbieter erprobt ist](#was-mit-welchem-anbieter-erprobt-ist)
3. [Die Modellauswahl](#die-modellauswahl)
4. [Wenn ein Modell nicht antworten kann](#wenn-ein-modell-nicht-antworten-kann)
5. [Werkzeugmodi](#werkzeugmodi)
6. [Einen Anbieter wählen](#einen-anbieter-wählen)

## Die Adapter

| Typ | Für |
|---|---|
| `anthropic` | Die Anthropic Messages API (Claude) und Anthropic-kompatible Server |
| `openai_chat` | OpenAI Chat Completions und jeden kompatiblen Server: Hugging Face Inference Providers (Router), OpenRouter, Mistral, Melious, xAI, Ollama, llama.cpp `llama-server`, vLLM, LM Studio, Azure OpenAI |
| `custom` | Eine eigene Klasse, die die Anbieterschnittstelle implementiert ([Exponential Intelligence erweitern](../../ersteller/anwendungsentwickler/exponential-intelligence-erweitern.md)) |

Intern verwendet der Assistent überall ein Format (die Form der Anthropic Messages API); jeder Adapter übersetzt in beide
Richtungen. Die Erweiterung liefert Beispielgruppen von Anbietern für Hugging Face, OpenRouter, Mistral, Melious und ein lokales
Ollama; sie bleiben inaktiv, bis sie in `[ProviderSettings] Providers[]` aufgeführt sind
([Dokumentation der Erweiterung: Anbieter](https://github.com/se7enxweb/expai/blob/main/doc/providers.md)).

## Was mit welchem Anbieter erprobt ist

Die Erweiterung sagt klar, was betrieben wurde und wie
([Anbieterunterstützung](https://github.com/se7enxweb/expai/blob/main/doc/provider-support.md)):

| Anbieter | Stand, wie ihn die Erweiterung festhält |
|---|---|
| Anthropic (Claude) | Live erprobt: das Panel, Werkzeuge, Entwürfe, Feldaktionen, Nutzung und Kosten, Ratenlimits, Message Batches |
| Ein lokales Open-Weight-Modell (Ollama, über `openai_chat`) | Live erprobt: die Schrittschleife, native Werkzeugaufrufe, die Probe, ein Entwurf, eine Feldaktion; langsam auf einem geteilten CPU-Host (Minuten je Schritt) |
| Hugging-Face-Router, OpenRouter | Gegen Testserver erprobt, die aus der Dokumentation der Anbieter gebaut wurden, plus eine Live-Anfrage ohne Schlüssel |
| OpenAI, Gemini, Mistral und andere OpenAI-kompatible Dienste | Derselbe Adapter, nur gegen Testserver erprobt |

Melious (ein EU-Anbieter von Open-Weight-Modellen) ist in der Erweiterung ausführlich dokumentiert, einschließlich einer Live-Probe
von vier seiner Modelle. Lesen Sie die Seite zur Anbieterunterstützung Ihrer installierten Version, bevor Sie sich auf einen
Anbieter verlassen: Ein Anbieter, der nur gegen einen Testserver getestet wurde, kann sich mit einem echten Schlüssel dennoch anders
verhalten.

## Die Modellauswahl

Dashboard, Panel und Bearbeitungsformular teilen eine Auswahl. Sie gruppiert die Modelle nach Anbieter und zeigt für jedes:

- die **Region** (`us`, `eu`, `local`);
- die **relativen Kosten** (`free`, `$`, `$$`, `$$$`, nach dem Ausgabepreis);
- "slow" für ein lokales Modell, "prompted tools" oder "no tools", wo es zutrifft, und "read only" für ein Modell, das keine
  Entwürfe schreiben darf;
- seinen Zustand: Ein angehaltener lokaler Server oder ein nicht erreichbarer Anbieter lässt sich nicht wählen.

Welche Modelle ein Benutzer sieht: den Standard der Website, die Modelle der Anbieter in `[ProviderSettings] ProvidersWithoutPolicy[]`
(standardmäßig Anthropic) und die Modelle der Anbieter, die die Richtlinie `expai/model_use` erlaubt
([Berechtigungen für KI-Funktionen](berechtigungen-fuer-ki-funktionen.md)). Ein Modell, das nicht angeboten wurde, wird beim Absenden
abgelehnt.

## Wenn ein Modell nicht antworten kann

Mit `[AISettings] FallbackToDefaultModel=enabled` (dem Standard) geht eine Anfrage, deren Modell angehalten, nicht erreichbar, in
ein Timeout gelaufen, nicht mehr erlaubt, entfernt oder von der Datenrichtlinie ferngehalten ist, stattdessen an den **Standard der
Website**. Der Redakteur sieht eine Zeile, die das sagt, zum Beispiel "Claude ... answered instead: Qwen ... (local) is stopped".
Darf auch der Standard die Inhalte nicht erhalten, antwortet das erste erlaubte Modell, das darf; kann keines, erhält der Redakteur
eine Meldung, die Modell und Grund nennt. Das Nutzungsprotokoll hält das angefragte Modell und den Grund fest.

Ein Gespräch gehört zu seinem Modell: Nach einem Modellwechsel geht es in einem frischen Fenster mit den früheren Austauschen als
reiner Text weiter, nachdem die Datenrichtlinie geprüft hat, dass der neue Anbieter sehen darf, was gelesen wurde.

## Werkzeugmodi

| `ToolUse` eines Modells | Was es kann |
|---|---|
| `native` | Das Tool-Calling der API; darf Entwürfe schreiben, sobald es die Probe bestanden hat oder als vertrauenswürdig gilt |
| `prompted` | Werkzeuge in den Anweisungen beschrieben, als JSON beantwortet; nur Lesewerkzeuge, nie Entwürfe |
| `none` | Keine Werkzeuge; ein nur lesender Schnappschuss des Knotens in den Anweisungen |

Details: [Das Schreibtor und die Probe](das-schreibtor-und-die-probe.md).

## Einen Anbieter wählen

| Was am meisten zählt | Worauf Sie achten |
|---|---|
| Inhalte dürfen Ihren Server nicht verlassen | Ein lokales Modell, `Region=local` ([Lokale Modelle](lokale-modelle.md)) |
| Verarbeitung in der EU | Ein Anbieter mit `Region=eu`; prüfen Sie seine Bedingungen und tragen Sie den Tag in `TermsCheckedAt` ein |
| Der Anbieter behält nichts | Ein Anbieter mit vereinbarter Zero Data Retention (`Retention=zdr`); lokale Modelle behalten nichts (`none`) |
| Qualität der Werkzeugnutzung und der Entwürfe | Ein Modell, das die Live-Probe bestanden hat; prüfen Sie Setup > EI: Models |
| Kosten | Der Preis je Million Tokens jedes Modells und Lanes für Massenarbeit ([Kostenkontrolle](../steuerung/kostenkontrolle.md)) |

Die für die Beispielanbieter mitgelieferten Werte sind ein Ausgangspunkt, keine rechtliche Aussage. Lesen Sie die Bedingungen des
Anbieters für Ihren Vertrag.

## Verwandte Seiten

- [Einen Anbieter konfigurieren](einen-anbieter-konfigurieren.md)
- [Lokale Modelle](lokale-modelle.md)
- [Datenschutz und Daten, die die Website verlassen](../steuerung/datenschutz-und-daten-die-die-website-verlassen.md)
- [Exponential Intelligence (expai) für Anwendungen](../../ersteller/anwendungsentwickler/exponential-intelligence.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
