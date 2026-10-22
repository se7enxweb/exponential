---
title: Glossar zu KI, Agenten und agentischem Arbeiten
audience: ai-agents, developer, app-creator, admin, editor
category: ai-agents
order: 4
summary: Die Begriffe des Abschnitts zu KI, Agenten und agentischem Arbeiten an einem Ort erklärt - Agent, agentischer Ablauf, Datenrichtlinie, Entwurf, expai, expservices, Lane, Modell, Probe, Prompt-Injection, Anbieter, Geltungsbereich, Dienstwerkzeug, gestufte Prüfung, Token, Werkzeug, Vector, Schreibtor und mehr - jeweils mit der Seite, die ihn erklärt.
keywords: Glossar, Begriffe, Definitionen, KI-Begriffe, Agent, agentisch, Werkzeug, Anbieter, Modell, Probe, Schreibtor, Datenrichtlinie, Prompt-Injection, Lane, Vector, expai, expservices, API-Schlüssel, Geltungsbereich, glossary, terms, definitions, tool, provider, model, probe, write gate, data policy, prompt injection, API key, scope
type: help
---
# Glossar zu KI, Agenten und agentischem Arbeiten

Diese Seite richtet sich an Leser, die in diesem Abschnitt auf einen unbekannten Begriff stoßen. Das allgemeine Glossar von
Exponential ist das [Glossar](../../glossar.md). Die Einträge sind nach dem englischen Begriff geordnet, der deutsche steht in
Klammern.

## Inhalt

1. [A bis F](#a-bis-f)
2. [G bis O](#g-bis-o)
3. [P bis Z](#p-bis-z)

## A bis F

- **Agent**: ein Programm, in dem ein KI-Modell entscheidet, welche Aktion als Nächstes kommt, meist durch Aufruf von Werkzeugen.
  [Agentische Abläufe](agentische-ablaeufe/README.md).
- **Agentic workflow (agentischer Ablauf)**: eine Aufgabe, die ein Agent über mehrere Schritte erledigt, etwa ein Bericht oder ein
  Import. [Ein Ablauf für den Inhaltsimport](agentische-ablaeufe/ablauf-inhaltsimport.md).
- **Audit trail (Audit-Trail)**: die manipulationssichere Aufzeichnung des Kernels, wer was getan hat; der Assistent schreibt in den
  Kanal `ai`. [Der Audit-Trail für KI](steuerung/audit-trail-fuer-ki.md).
- **Chain (KI-Kette)**: mehrstufiges Schreiben über die Job-Warteschlange mit einem menschlichen Kontrollpunkt.
  [Ketten und eingereihte Arbeit](expai/ketten-und-eingereihte-arbeit.md).
- **Data policy (Datenrichtlinie)**: Regeln, die entscheiden, welche Inhalte an welchen Anbieter gehen dürfen, nach Region und
  Aufbewahrung. [Datenschutz und Daten, die die Website verlassen](steuerung/datenschutz-und-daten-die-die-website-verlassen.md).
- **Draft (Entwurf)**: eine unveröffentlichte Version; alles, was Exponential Intelligence schreibt, ist eine.
  [Entwürfe und menschliche Prüfung](expai/entwuerfe-und-menschliche-pruefung.md).
- **EI: Summary**: die druckbare Seite für einen Datenschutzbeauftragten (`expai/summary`).
- **expai**: der Erweiterungsname von Exponential Intelligence. [Exponential Intelligence für KI-Nutzer](expai/README.md).
- **expservices**: die Erweiterung, die Funktionen der Administration als benannte Dienste anbietet.
  [Agenten und expservices](agentische-ablaeufe/agenten-und-expservices.md).
- **Exponential Intelligence**: der Schreibassistent der Administrationsoberfläche.
- **Fallback (Rückfall)**: Kann ein gewähltes Modell nicht antworten, antwortet stattdessen der Standard der Website.
  [Anbieter und Modelle](expai/anbieter-und-modelle.md).

## G bis O

- **Label (Kennzeichnung)**: die Markierung "Exponential Intelligence" an jeder Version, die der Assistent geschrieben hat.
- **Lane**: eine Art, eine Anfrage zu senden (standard, flex, batch, fast), mit eigenem Preis und eigener Geschwindigkeit.
  [Kostenkontrolle](steuerung/kostenkontrolle.md#lanes-und-stapel).
- **Local model (lokales Modell)**: ein Open-Weight-Modell, das auf Ihrem eigenen Server läuft; Region `local`.
  [Lokale Modelle](expai/lokale-modelle.md).
- **Model (Modell)**: das KI-System, das Text schreibt; über einen Anbieter erreicht.
- **OAuth2 token (OAuth2-Token)**: ein REST-Zugang für eine Anwendung, die für Benutzer handelt, die sich selbst anmelden.

## P bis Z

- **Personal API key (persönlicher API-Schlüssel)**: ein REST-Zugang `expk_...` mit Geltungsbereichen, der als sein Eigentümer
  handelt. [Ein nur lesender API-Schlüssel, Schritt für Schritt](agentische-ablaeufe/ein-nur-lesender-api-schluessel-schritt-fuer-schritt.md).
- **Personal token (persönliches Token, expservices)**: ein Zugang `expt_...` für expservices, der als sein Benutzer handelt.
- **Plan mode (Planmodus)**: der Modus von Vector, in dem das Modell vor Massenarbeit einen Plan mit Kostenschätzung vorschlägt.
  [Vector und gestufte Prüfung](expai/vektorsuche-und-gestufte-pruefung.md).
- **Probe**: 15 vorgegebene Aufgaben, die ein Modell bestehen muss, bevor es Entwürfe schreiben darf.
  [Das Schreibtor und die Probe](expai/das-schreibtor-und-die-probe.md).
- **Prompt injection (Prompt-Injection)**: Text in Inhalten, der geschrieben wurde, um ein Modell zu steuern.
  [Nicht vertrauenswürdige Daten und Prompt-Injection](agentische-ablaeufe/nicht-vertrauenswuerdige-daten-und-prompt-injection.md).
- **Provider (Anbieter)**: der Dienst (oder lokale Server), der ein Modell betreibt. [Anbieter und Modelle](expai/anbieter-und-modelle.md).
- **Region / retention (Region / Aufbewahrung)**: wo ein Anbieter Anfragen verarbeitet (`us`, `eu`, `local`) und wie lange er sie
  behält (`none`, `zdr`, `30d`, `unknown`).
- **Scope (Geltungsbereich, API-Schlüssel)**: wofür ein Schlüssel verwendet werden darf (`read`, `publish`, `edit`, `remove`),
  jeder steht für eine Richtlinie seines Eigentümers.
- **Scope (Geltungsbereich, Vector)**: die Knoten, Teilbäume, Klassen und Sprachen, die ein Thread berühren darf.
- **Service tool (Dienstwerkzeug)**: ein Lesedienst von expservices, den der Assistent aufrufen darf. [Dienstwerkzeuge](expai/dienstwerkzeuge.md).
- **Staged review (gestufte Prüfung)**: Vorschläge, die je Feld und Teil geprüft werden, bevor sie in Entwürfe geschrieben werden.
- **Token (Abrechnung)**: die Einheit, die Anbieter zählen und abrechnen. **Token (Zugang)**: ein Geheimnis, das authentifiziert.
- **Tool (Werkzeug)**: eine benannte Fähigkeit, die ein Modell aufrufen kann, mit einer Beschreibung und einem Eingabeschema.
- **Vector**: der Arbeitsbereich mit Threads von Exponential Intelligence für größere Aufgaben.
- **Veto hook (Veto-Hook)**: Regeln oder ein Listener, die einzelne KI-Anfragen mit einer Begründung ablehnen.
  [Der Hauptschalter und der Veto-Hook](expai/hauptschalter-und-veto.md).
- **Web tools (Webwerkzeuge)**: `web_fetch`, `web_search` und `project_history` des Assistenten.
  [Web- und Verlaufswerkzeuge](expai/web-und-verlaufswerkzeuge.md).
- **Write gate (Schreibtor)**: die Regel, die entscheidet, welche Modelle Entwürfe schreiben dürfen.

## Verwandte Seiten

- [KI, Agenten und agentisches Arbeiten: häufige Fragen](haeufige-fragen-zu-ki-und-agenten.md)
- [Glossar](../../glossar.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
