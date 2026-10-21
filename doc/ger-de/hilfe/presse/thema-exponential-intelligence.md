---
title: "Thema: Exponential Intelligence"
audience: press
category: press-media
order: 54
summary: Ein Themenbeitrag über Exponential Intelligence (expai), den Schreibassistenten in der Administration von Exponential - was er tut, Anbieter in den USA, in der EU oder auf dem eigenen Server, Datenrichtlinie, Kosten und Audit - mit druckbaren Fakten, Quellen und den Aussagen, die Sie vermeiden sollten.
keywords: Thema, Exponential Intelligence, expai, KI, Schreibassistent, Anbieter, Datenrichtlinie, lokales Modell, Audit, Kosten, Presse, story, AI, writing assistant, providers, data policy, local model, audit, costs, press
type: help
---
# Thema: Exponential Intelligence

Diese Seite ist für Journalisten und Creator, die über KI im Content-Management berichten. Exponential Intelligence lässt
sich leicht falsch beschreiben; diese Seite gibt den genauen Wortlaut der Quellen, die Daten und die Aussagen, die Sie
nicht machen dürfen. Das nächste Thema, [ein Assistent, der nur Entwürfe schreibt](thema-ein-assistent-der-nur-entwuerfe-schreibt.md),
führt einen Blickwinkel weiter.

## Inhalt

1. [Das Thema in einem Absatz](#das-thema-in-einem-absatz)
2. [Was neu ist, und seit wann](#was-neu-ist-und-seit-wann)
3. [Fakten, die Sie drucken können](#fakten-die-sie-drucken-können)
4. [Was Sie nicht behaupten sollten](#was-sie-nicht-behaupten-sollten)
5. [Blickwinkel und Ideen für Überschriften](#blickwinkel-und-ideen-für-überschriften)
6. [Wen Sie fragen](#wen-sie-fragen)
7. [Verwandte Seiten](#verwandte-seiten)

## Das Thema in einem Absatz

„Exponential Intelligence ist ein Schreibassistent, der in die Administration eingebaut ist. Redakteure bitten in
einfacher Sprache um neue Inhalte oder um Änderungen“, und der Assistent „schreibt **Entwürfe**, als der angemeldete
Redakteur und mit dessen eigenen Berechtigungen“ ([README, Exponential Intelligence](https://github.com/se7enxweb/exponential/blob/main/README.md#exponential-intelligence-new)).
Er arbeitet mit gehosteten Anbietern in den USA oder in der EU oder mit einem lokalen Open-Weight-Modell auf demselben
Server; er hält jede Anfrage im Audit-Trail fest, ohne den Prompt oder die Antwort; und er zeigt bei jedem Klick das
Modell, die Region und die Kosten. Er ist die Erweiterung `se7enxweb/expai`, die der Kernel ab der Entwicklungslinie
6.0.15 voraussetzt ([Funktionsseite zu expai](../../../eng-us/features/6.0/extensions/expai.md)).

## Was neu ist, und seit wann

| Was | Wann | Quelle |
|---|---|---|
| Das Repository `expai` wird angelegt | 11. Oktober 2026 | [Wer ist 7x?, Zeitleiste](../wer-ist-7x.md#fast-dreißig-jahre-die-zeitleiste) |
| Lesedienste als Werkzeuge des Assistenten | 11. Oktober 2026 | [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md#11-october-2026-read-services-as-tools-of-the-assistant) |
| `composer.json` setzt `se7enxweb/expai` voraus, ebenso die Job-Warteschlange `expqueue` | 12. Oktober 2026 | [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md#12-october-2026-exponential-intelligence-in-the-distribution) |
| Erstes Release der Erweiterung, 0.0.1.0 | die Quellen nennen kein Datum | [expai, Kurz gesagt](../../../eng-us/features/6.0/extensions/expai.md#in-short) |

Die Linie 6.0.15 ist in Entwicklung und nicht veröffentlicht.

## Fakten, die Sie drucken können

| Fakt | Quelle |
|---|---|
| Er schreibt Entwürfe und veröffentlicht nie von sich aus. | [expai, Kurz gesagt](../../../eng-us/features/6.0/extensions/expai.md#in-short) |
| Er arbeitet als der angemeldete Redakteur, mit dessen Berechtigungen. | ebenda |
| Anbieter: Anthropic, jeder OpenAI-kompatible Dienst (Melious.ai in der EU, Hugging Face, OpenRouter, Mistral und andere) oder ein lokales Modell über Ollama. | ebenda |
| Jeder Anbieter gibt eine Region (`us`, `eu`, `local`) und eine Aufbewahrungsdauer an; Regeln pro Sektion und Inhaltsklasse entscheiden, welche Inhalte wohin gehen dürfen. | [expai, Für Administratoren](../../../eng-us/features/6.0/extensions/expai.md#for-administrators) |
| Prompts, Antworten und Schlüssel werden nie im Audit-Trail festgehalten. | ebenda |
| Nutzungsgrenzen pro Benutzer, Tag, Monat, Website und Anbieter sowie Budgets für Arbeit in der Warteschlange. | ebenda |
| „EI: Summary“ ist eine druckbare Seite für einen Datenschutzbeauftragten. | ebenda |
| Er kann Alternativtexte für Bilder vorschlagen und Bilder melden, denen einer fehlt. | [expai, Für Redakteure](../../../eng-us/features/6.0/extensions/expai.md#for-editors) |
| Auf einer neuen Installation ist er für die Administration aktiv, aber „Ohne Schlüssel bleibt er untätig und sendet nichts.“ | [README, How to enable it](https://github.com/se7enxweb/exponential/blob/main/README.md#how-to-enable-it) |
| Auf einer aktualisierten Installation kommt er inaktiv an, bis ein Administrator ihn aktiviert. | [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md#12-october-2026-exponential-intelligence-in-the-distribution) |

## Was Sie nicht behaupten sollten

- „Die KI veröffentlicht Inhalte“ oder „KI schreibt Ihre Website“. Er schreibt Entwürfe; Menschen veröffentlichen.
- „Er ist DSGVO-konform“ oder irgendeine rechtliche Konformität. Das hängt vom Anbieter, der Region und der Organisation
  ab.
- „Er ist kostenlos.“ Die Software ist frei; ein gehosteter Anbieter kann für die Nutzung Geld verlangen.
- „Exponential AI“ oder „ExpAI“. Der Name ist **Exponential Intelligence**; in technischen Texten `expai`
  ([Kurzkarte zu den Namensregeln](../fuersprecher/kurzkarte-namensregeln.md)).
- Welcher Anbieter „der beste“ ist, oder irgendetwas über die Qualität von Modellen: Die Quellen vergleichen sie nicht.
- Verbreitungszahlen: Die Quellen sagen nicht, wie viele Websites ihn nutzen.

## Blickwinkel und Ideen für Überschriften

- Wahl der Rechtsordnung: die USA, die EU oder der eigene Server, entschieden pro Anbieter.
- Rechenschaft: Jede Anfrage wird protokolliert, aber nie der Text selbst.
- Barrierefreiheit: Vorschläge für Alternativtexte zu Bildern.

```text
„Ein KI-Assistent, der fragt, wohin Ihre Daten dürfen“
„Exponential Intelligence: Entwürfe aus den USA, der EU oder vom eigenen Server“
„Jede Anfrage protokolliert, kein Prompt gespeichert: wie Exponential seine KI prüft“
```

## Wen Sie fragen

| Frage | Fragen Sie | Wo |
|---|---|---|
| Entwurfsentscheidungen, Datenrichtlinie, eine Vorführung | 7x | info@se7enx.com oder <https://exponential.earth/about/contact-us> |
| Die Dokumentation der Erweiterung | ihr Repository | <https://github.com/se7enxweb/expai> |
| Preise und Bedingungen der Anbieter | den Anbieter selbst | seine eigene Website |

## Verwandte Seiten

- [Gesprächspunkte: Exponential Intelligence](../fuersprecher/gespraechspunkte-exponential-intelligence.md)
- [Einwand: „Was ist mit den KI-Funktionen?“](../fuersprecher/einwand-ki-funktionen.md)
- [Thema: ein Assistent, der nur Entwürfe schreibt](thema-ein-assistent-der-nur-entwuerfe-schreibt.md)
- [Themenbeiträge: Übersicht](themenbeitraege-uebersicht.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
