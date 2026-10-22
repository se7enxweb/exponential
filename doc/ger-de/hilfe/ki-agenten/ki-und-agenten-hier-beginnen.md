---
title: "KI, Agenten und agentisches Arbeiten: hier beginnen"
audience: ai-agents, developer, app-creator, admin
category: ai-agents
order: 1
summary: Die Einstiegsseite für Menschen, die KI mit Exponential verwenden, und für Menschen, die Agenten und agentische Abläufe dagegen bauen. Was heute wirklich ausgeliefert wird (Exponential Intelligence, die REST-API, expservices, ezjscore-JSON, der Audit-Trail und die Hilfe auf der Kommandozeile), wofür jedes da ist, die Regeln, die KI auf einer Website sicher halten, und eine Übersicht aller Seiten dieses Abschnitts.
keywords: KI, künstliche Intelligenz, Agent, agentisch, agentischer Ablauf, LLM, großes Sprachmodell, Exponential Intelligence, expai, Claude, Anthropic, OpenAI-kompatibel, Ollama, lokales Modell, REST-API, expservices, ezjscore, exp:help, Prompt-Injection, Datenrichtlinie, Audit, hier beginnen, Überblick, AI, artificial intelligence, agent, agentic, agentic workflow, large language model, local model, REST API, prompt injection, data policy, start here, overview
featured: true
type: help
---
# KI, Agenten und agentisches Arbeiten: hier beginnen

Dieser Abschnitt richtet sich an zwei Gruppen von Lesern. Die erste verwendet KI **mit** Exponential: Redakteure,
Website-Verantwortliche und Administratoren, die Hilfe beim Schreiben, Umschreiben und Übersetzen von Inhalten möchten und
wissen müssen, wohin diese Inhalte gehen. Die zweite baut **gegen** Exponential: Entwickler und Anwendungsentwickler, die einen
KI-Agenten mit einer Installation verbinden oder agentische Abläufe betreiben, die Inhalte selbstständig lesen und ändern. Beide
Gruppen brauchen dieselbe Grundlage: ein ehrliches Bild davon, was ausgeliefert wird, was es tut und was es nicht tun wird.

> **Hinweis**
> Jede Aussage in diesem Abschnitt wurde am Code, an den Einstellungen und an der Dokumentation der beschriebenen Teile geprüft:
> der Erweiterung expai (Exponential Intelligence), dem Kernel, der Erweiterung expservices und der Dokumentation in
> `doc/eng-us/`. Wo sich etwas nicht prüfen ließ, sagt die Seite weniger statt mehr. Modellnamen, Preise und Bedingungen der
> Anbieter ändern sich; maßgeblich sind die Einstellungen Ihrer Installation.

## Inhalt

1. [Exponential und KI in einer Minute](#exponential-und-ki-in-einer-minute)
2. [Für wen dieser Abschnitt ist](#für-wen-dieser-abschnitt-ist)
3. [Fünf Regeln, die überall gelten](#fünf-regeln-die-überall-gelten)
4. [Jeder Teil dieses Abschnitts](#jeder-teil-dieses-abschnitts)
5. [Eine erste Stunde](#eine-erste-stunde)
6. [Andere Abschnitte, die Sie verwenden werden](#andere-abschnitte-die-sie-verwenden-werden)

## Exponential und KI in einer Minute

| Sie möchten | Verwenden Sie | Woher es kommt |
|---|---|---|
| Inhalte in der Administration mit einem KI-Modell schreiben, umschreiben, kürzen oder übersetzen | **Exponential Intelligence** (die Erweiterung `expai`) | Von `composer.json` des Hauptrepositorys verlangt, daher in `extension/expai` jeder Installation; eine neue Installation aktiviert sie für die Administrations-Siteaccesses |
| Ein Programm oder einen Agenten Inhalte über HTTP lesen und anlegen lassen | Die **REST-API** (`/api/ezp/v1`, `/api/ezp/v2`) mit einem OAuth2-Token oder einem persönlichen API-Schlüssel | Der Kernel |
| Ein Programm Funktionen der Administration nach Namen steuern lassen | **expservices** über die ezjscore-Aufrufschnittstelle, mit einem persönlichen Token | Die Erweiterung `expservices`, Teil der Standarddistribution |
| Kleine JSON-Anfragen aus Ihren eigenen Seiten beantworten | **ezjscore**-Serverfunktionen | Die Erweiterung `ezjscore` |
| Wissen, was ein Agent oder der Assistent getan hat | Der **Audit-Trail** | Der Kernel |
| Einem Agenten die Dokumentation als reinen Text geben | Das Hilfesystem und `./console exp:help search` / `show` | Der Kernel |

Details und der genaue Stand jedes Teils: [Was es heute gibt](was-es-heute-gibt.md).

## Für wen dieser Abschnitt ist

| Sie sind | Beginnen Sie mit |
|---|---|
| Ein Redakteur, der wissen möchte, was der KI-Schreibassistent mit Ihrem Text macht | [Was Exponential Intelligence tut](expai/was-exponential-intelligence-tut.md) |
| Ein Administrator, der entscheidet, ob er den Assistenten einschaltet und mit welchem Anbieter | [Bevor Sie Exponential Intelligence einschalten](checklisten/bevor-sie-exponential-intelligence-einschalten.md) |
| Ein Datenschutzbeauftragter oder eine Führungskraft, die beantworten muss "Wohin gehen unsere Inhalte?" | [Datenschutz und Daten, die die Website verlassen](steuerung/datenschutz-und-daten-die-die-website-verlassen.md) |
| Ein Entwickler, der möchte, dass ein Coding-Agent in einem Projekt auf Grundlage von Fakten arbeitet | [Coding-Agenten in einem Exponential-Projekt](agentische-ablaeufe/coding-agenten-in-einem-exponential-projekt.md) |
| Ein Anwendungsentwickler, der einen Agenten baut, der Inhalte liest oder schreibt | [Eine Schnittstelle für einen Agenten wählen](agentische-ablaeufe/eine-schnittstelle-waehlen.md) |
| Jemand, der eine interne Richtlinie zur KI-Nutzung schreibt | [Eine Richtlinie zur KI-Nutzung für Ihre Organisation](steuerung/eine-richtlinie-zur-ki-nutzung.md) |

Nicht sicher, welcher Teil passt? [Welcher Weg zu Ihrer Aufgabe passt](welcher-weg-zu-ihrer-aufgabe-passt.md) stellt einige Fragen.

## Fünf Regeln, die überall gelten

Diese Regeln sind in die Software eingebaut, nicht nur empfohlen. Jede Seite dieses Abschnitts baut auf ihnen auf.

1. **Der Assistent schreibt Entwürfe, nie Veröffentlichungen.** Exponential Intelligence hat keine Einstellung, um selbst zu
   veröffentlichen; veröffentlicht wird nur, wenn ein Redakteur auf "Send for approval" drückt, über die normale
   Veröffentlichungsoperation, sodass Freigabe-Workflows greifen ([Entwürfe und menschliche Prüfung](expai/entwuerfe-und-menschliche-pruefung.md)).
2. **Alles läuft als echtes Konto mit seinen eigenen Berechtigungen.** Der Assistent handelt als der angemeldete Redakteur; REST
   und expservices handeln als Eigentümer des Tokens oder Schlüssels. Keines davon hat ein eigenes Berechtigungsmodell
   ([Rollen und minimale Rechte](steuerung/rollen-und-minimale-rechte.md)).
3. **Enge Zugangsdaten.** API-Schlüssel tragen Geltungsbereiche, Tokens tragen Laufzeiten, und keines gibt mehr, als sein
   Eigentümer hat ([Zugangsdaten für Agenten](agentische-ablaeufe/zugangsdaten-fuer-agenten.md)).
4. **Inhalte sind Daten, keine Anweisungen.** Die Anweisungen und Werkzeugbeschreibungen des Assistenten sagen das dem Modell,
   und nachdem Text von außen aus dem Web in ein Gespräch gelangt ist, schreibt der Assistent keinen Entwurf, solange dieser
   Text im Gespräch ist ([Nicht vertrauenswürdige Daten und Prompt-Injection](agentische-ablaeufe/nicht-vertrauenswuerdige-daten-und-prompt-injection.md)).
5. **Schlüssel verlassen nie den Server.** Schlüssel der Anbieter liegen nur in `settings/override` und erreichen nie den
   Browser, ein Log oder ein Template ([Einen Anbieter konfigurieren](expai/einen-anbieter-konfigurieren.md)).

## Jeder Teil dieses Abschnitts

| Teil | Was er beantwortet |
|---|---|
| [Exponential Intelligence](expai/README.md) | Was der Schreibassistent tut und nicht tut, Anbieter und Modelle, lokale Modelle, Übersetzung, Alternativtext, Vector, Ketten, Web- und Dienstwerkzeuge, das Dashboard, Berechtigungen, der Hauptschalter, ihn erweitern |
| [Agentische Abläufe](agentische-ablaeufe/README.md) | Eine Schnittstelle wählen, Agenten auf REST und expservices, Zugangsdaten, Dokumentation für Agenten, Prompt-Injection, einen Menschen beteiligt halten, durchgearbeitete Abläufe, Coding-Agenten in einem Projekt |
| [Steuerung](steuerung/README.md) | Datenschutz und Daten, die die Website verlassen, Kostenkontrolle, der Audit-Trail, Rollen und minimale Rechte, eine Richtlinie zur KI-Nutzung, Reaktion auf Vorfälle |
| [Checklisten](checklisten/README.md) | Bevor Sie den Assistenten einschalten, bevor Sie einen Agenten verbinden, KI-Entwürfe prüfen |

Kurze Antworten stehen in den [häufigen Fragen](haeufige-fragen-zu-ki-und-agenten.md), die Begriffe im [Glossar](glossar-zu-ki-und-agenten.md) und die
Lesereihenfolge im [Lesepfad](lesepfad-zu-ki-und-agenten.md). [Was KI in Exponential nicht tut](was-ki-in-exponential-nicht-tut.md)
listet die Grenzen an einem Ort.

## Eine erste Stunde

| Minuten | Tun | Lesen |
|---|---|---|
| 10 | Erfahren, was es gibt und was nicht | [Was es heute gibt](was-es-heute-gibt.md), [Was KI in Exponential nicht tut](was-ki-in-exponential-nicht-tut.md) |
| 15 | Ihren Weg wählen | [Welcher Weg zu Ihrer Aufgabe passt](welcher-weg-zu-ihrer-aufgabe-passt.md) |
| 20 | Die zwei oder drei Seiten Ihres Weges lesen | die README des Abschnitts Ihres Weges |
| 15 | Die passende Checkliste durchgehen | [Checklisten](checklisten/README.md) |

## Andere Abschnitte, die Sie verwenden werden

| Abschnitt | Für |
|---|---|
| [Anwendungsentwickler](../ersteller/anwendungsentwickler/anwendungsentwickler-hier-beginnen.md) | Erweiterungen und Integrationen bauen, darunter [Exponential Intelligence für Anwendungen](../ersteller/anwendungsentwickler/exponential-intelligence.md) und [Exponential Intelligence erweitern](../ersteller/anwendungsentwickler/exponential-intelligence-erweitern.md) |
| [Trainer: Kurs zu Exponential Intelligence](../trainer/exponential-intelligence/README.md) | Redakteuren und Administratoren den Umgang mit dem Assistenten beibringen |
| [Fürsprecher: Argumente](../fuersprecher/gespraechspunkte-exponential-intelligence.md) | Den Assistenten öffentlich zutreffend beschreiben |
| [Unternehmen](../unternehmen/unternehmen-hier-beginnen.md) | Entscheider, die Kosten und Risiken abwägen |

## Verwandte Seiten

- [Was es heute gibt](was-es-heute-gibt.md)
- [KI, Agenten und agentisches Arbeiten: Lesepfad](lesepfad-zu-ki-und-agenten.md)
- [KI, Agenten und agentisches Arbeiten: häufige Fragen](haeufige-fragen-zu-ki-und-agenten.md)
- [expai: Exponential Intelligence](../../funktionen/6.0/erweiterungen/expai.md)
- [Das Hilfesystem](../../funktionen/6.0/hilfesystem.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
