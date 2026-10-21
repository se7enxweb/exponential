---
title: "FAQ: KI-Funktionen"
audience: government, project-manager, editor, admin
category: public-sector
order: 210
summary: Antworten auf die Fragen öffentlicher Stellen zu Exponential Intelligence, dem optionalen KI-Schreibassistenten - was er tut, ob er veröffentlicht, welche Anbieter er nutzt, wohin Daten gehen, wer ihn nutzen darf, Kosten und verantwortungsvoller Einsatz.
keywords: FAQ KI, Exponential Intelligence, expai, künstliche Intelligenz, Schreibassistent, Entwürfe, Anbieter, lokales Modell, Ollama, Datenschutz, Rechte, verantwortungsvoller Einsatz, öffentlicher Sektor, AI FAQ, artificial intelligence, writing assistant, drafts, providers, local model, data protection, permissions, responsible use, public sector
type: help
---
# FAQ: KI-Funktionen

Diese Seite beantwortet die Fragen öffentlicher Stellen zu Exponential Intelligence (expai), dem optionalen
Schreibassistenten der Administration. Die Antworten stammen aus
[Exponential Intelligence](../../../funktionen/6.0/erweiterungen/expai.md) und dem
[Kurs Exponential Intelligence](../../trainer/exponential-intelligence/README.md).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Die Regeln für den
> Einsatz von KI im öffentlichen Sektor hängen von Ihrer Rechtsordnung und Organisation ab; prüfen Sie sie mit Ihrer
> Rechtsberatung.

## Inhalt

1. [Ist KI in Exponential eingebaut?](#ist-ki-in-exponential-eingebaut)
2. [Was tut sie?](#was-tut-sie)
3. [Kann sie selbstständig veröffentlichen?](#kann-sie-selbstständig-veröffentlichen)
4. [Welche KI-Anbieter nutzt sie?](#welche-ki-anbieter-nutzt-sie)
5. [Wer darf sie nutzen?](#wer-darf-sie-nutzen)
6. [Was kostet sie?](#was-kostet-sie)
7. [Wie setzen wir sie verantwortungsvoll ein?](#wie-setzen-wir-sie-verantwortungsvoll-ein)

## Ist KI in Exponential eingebaut?

Sie ist eine Erweiterung, `se7enxweb/expai`, die ab 6.0.15 in Installationen vorhanden, aber **bis zur Aktivierung
inaktiv** ist. Eine Website, die sie nicht aktiviert, sendet nichts an einen KI-Anbieter
([Exponential Intelligence](../../../funktionen/6.0/erweiterungen/expai.md)).

## Was tut sie?

Redakteurinnen und Redakteure bitten in natürlicher Sprache um neue Inhalte oder Änderungen; der Assistent sucht
heraus, was er braucht, und schreibt Entwürfe. Er erscheint im Reiter Intelligence eines Knotens, im
Bearbeitungsformular (Feldaktionen, Voreinstellungen, ein Menü zum Verbessern, Alternativtext) und in Vector-Threads
([Exponential Intelligence](../../../funktionen/6.0/erweiterungen/expai.md#kurz-gesagt)).

## Kann sie selbstständig veröffentlichen?

Nein. Sie schreibt Entwürfe, als die angemeldete Person und mit deren Rechten, und veröffentlicht nie selbstständig;
ein Mensch prüft und veröffentlicht ([Exponential Intelligence](../../../funktionen/6.0/erweiterungen/expai.md#kurz-gesagt)). Der
Kurs nennt das ihre drei Versprechen: nur Entwürfe, als die redigierende Person, nie veröffentlichen
([Modul 1](../../trainer/exponential-intelligence/modul-01-exponential-intelligence-kennenlernen/README.md)).

## Welche KI-Anbieter nutzt sie?

Den, den Sie einrichten: Anthropic, einen OpenAI-kompatiblen Dienst (die Funktionsseite nennt Beispiele, darunter einen
in der EU) oder ein lokales Open-Weight-Modell über Ollama, das die Anfragen auf Ihrer eigenen Infrastruktur hält. Ihre
Wahl entscheidet, wohin die Anfragen und Inhalte der Redaktion gehen; treffen Sie sie mit Ihren
Datenschutzbeauftragten und halten Sie sie fest.

## Wer darf sie nutzen?

Nur Benutzer, deren Rollen ihre Richtlinien enthalten (zum Beispiel `expai/write`); Administratoren richten sie ein
([Modul 1](../../trainer/exponential-intelligence/modul-01-exponential-intelligence-kennenlernen/README.md),
[Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)).

## Was kostet sie?

Die Erweiterung ist freie Software. Ein gehosteter Anbieter berechnet die Nutzung nach seinen eigenen Bedingungen; ein
lokales Modell braucht Ihre eigene Hardware. Die Administration hat eine Seite für Budgets, um die Nutzung zu begrenzen
(**Setup > EI: Budgets**).

## Wie setzen wir sie verantwortungsvoll ein?

Der Kurs hat ein Modul zum verantwortungsvollen Einsatz
([Modul 6: verantwortungsvoller Einsatz](../../trainer/exponential-intelligence/modul-06-verantwortungsvoller-einsatz/README.md)).
Gängige Praxis für öffentliche Stellen: eine schriftliche Richtlinie, wofür sie eingesetzt werden darf, menschliche
Prüfung jedes Entwurfs, keine personenbezogenen Daten in Eingaben, sofern Ihre Bewertung es nicht erlaubt, und eine
Aufzeichnung des verwendeten Anbieters und Modells.

## Verwandte Seiten

- [FAQ für Verwaltungen](README.md)
- [Exponential Intelligence](../../../funktionen/6.0/erweiterungen/expai.md)
- [FAQ: Datenschutz](faq-datenschutz.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
