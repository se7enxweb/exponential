---
title: "KI, Agenten und agentisches Arbeiten: häufige Fragen"
audience: ai-agents, developer, app-creator, admin, editor
category: ai-agents
order: 3
summary: Kurze Antworten auf die Fragen, die Menschen zu KI in Exponential stellen - veröffentlicht sie, welche Anbieter, verlassen Inhalte den Server, was kostet es, lässt es sich ausschalten, wie verbinden und authentifizieren sich Agenten und wie wird mit Prompt-Injection umgegangen - jeweils mit der Seite, die die vollständige Antwort enthält.
keywords: FAQ, häufige Fragen, Fragen, KI, Exponential Intelligence, expai, Anbieter, lokales Modell, Datenschutz, Kosten, ausschalten, Agenten, REST-API, expservices, Prompt-Injection, questions, AI, providers, local model, privacy, cost, switch off, agents, REST API, prompt injection
type: help
---
# KI, Agenten und agentisches Arbeiten: häufige Fragen

Diese Seite richtet sich an alle mit einer schnellen Frage zu KI oder Agenten in Exponential. Jede Antwort ist kurz und verlinkt
auf die Seite mit den Details.

## Inhalt

1. [Exponential Intelligence](#exponential-intelligence)
2. [Datenschutz und Kosten](#datenschutz-und-kosten)
3. [Agenten](#agenten)

## Exponential Intelligence

**Veröffentlicht die KI Inhalte selbst?**
Nein. Sie schreibt Entwürfe als der angemeldete Redakteur. Veröffentlicht wird nur, wenn ein Redakteur auf "Send for approval"
drückt, über die normale Veröffentlichungsoperation, sodass Freigabe-Workflows greifen
([Entwürfe und menschliche Prüfung](expai/entwuerfe-und-menschliche-pruefung.md)).

**Ist sie auf meiner Website installiert?**
Sie wird vom Haupt-`composer.json` verlangt, liegt also in `extension/expai`, und eine neue Installation aktiviert sie für die
Administrations-Siteaccesses. Sie tut nichts, bis ein Anbieterschlüssel oder ein lokales Modell konfiguriert ist
([Was es heute gibt](was-es-heute-gibt.md)).

**Welche KI-Anbieter kann sie verwenden?**
Anthropic, jeden OpenAI-kompatiblen Dienst über einen Adapter, ein lokales Open-Weight-Modell über Ollama oder eine eigene
Klasse. Anthropic und ein lokales Modell sind live erprobt; mehrere andere bisher nur gegen Testserver
([Anbieter und Modelle](expai/anbieter-und-modelle.md)).

**Wessen Berechtigungen verwendet sie?**
Die des angemeldeten Redakteurs, für jede Abfrage und jeden Entwurf. Es gibt kein Dienstkonto
([Berechtigungen für KI-Funktionen](expai/berechtigungen-fuer-ki-funktionen.md)).

**Kann sie übersetzen?**
Ja: eine Feldaktion Translate, übersetzte Entwürfe auf Anfrage im Reiter Intelligence und einen Übersetzungsabgleich, der
Übersetzungen mit ihrer Quelle im Gleichschritt hält, immer als Entwürfe ([Übersetzungshilfe](expai/uebersetzungshilfe.md)).

**Kann sie Bilder hinzufügen?**
Sie kann keine Bilder oder Dateien hochladen und keine Relationen setzen. Mit einem Vision-Modell kann sie Alternativtext für
vorhandene Bilder vorschlagen ([Alternativtext für Bilder](expai/alternativtext-fuer-bilder.md)).

**Kann sie das Web lesen?**
Nur mit der Richtlinie `expai/web` und nur, wenn konfiguriert: Seiten von Websites auf einer Erlaubnisliste abrufen, eine
Websuche und die eigene Commit-Geschichte des Projekts ([Web- und Verlaufswerkzeuge](expai/web-und-verlaufswerkzeuge.md)).

**Kann jedes Modell Entwürfe schreiben?**
Nein. Nur Modelle mit nativem Tool-Calling, die eine Live-Probe bestanden haben oder per Konfiguration als vertrauenswürdig
gelten ([Das Schreibtor und die Probe](expai/das-schreibtor-und-die-probe.md)).

## Datenschutz und Kosten

**Verlassen unsere Inhalte den Server?**
Nur zu den Anbietern, die Sie konfigurieren, und nur, was die Datenrichtlinie erlaubt. Mit einem lokalen Modell verlässt nichts
den Server ([Datenschutz und Daten, die die Website verlassen](steuerung/datenschutz-und-daten-die-die-website-verlassen.md)).

**Sieht der Browser jemals den API-Schlüssel?**
Nein. Schlüssel liegen in `settings/override`, und nur der Server ruft die Anbieter auf
([Einen Anbieter konfigurieren](expai/einen-anbieter-konfigurieren.md)).

**Was kostet es?**
Die Software ist frei; gehostete Anbieter rechnen je Anfrage ab. Der Assistent schätzt die Kosten jeder Anfrage, zeigt sie an und
hält an den Grenzen und Budgets an, die Sie setzen ([Kostenkontrolle](steuerung/kostenkontrolle.md),
[Das Dashboard und das Protokoll](expai/dashboard-und-protokoll.md)).

**Können wir sie ausschalten?**
Ja, überall oder je Siteaccess, mit `[AISettings] Enabled` und `DisabledSiteAccesses[]`; ein Veto-Hook kann einzelne Anfragen
ablehnen ([Der Hauptschalter und der Veto-Hook](expai/hauptschalter-und-veto.md)).

**Wird es festgehalten?**
Jede Anfrage, jede Ablehnung und jeder Entwurf ist ein Ereignis des Audit-Kanals `ai`, ohne Prompts, Antworten oder Schlüssel
([Der Audit-Trail für KI](steuerung/audit-trail-fuer-ki.md)).

## Agenten

**Wie verbindet sich ein Agent mit Exponential?**
Über die REST-API oder expservices mit seinem eigenen Konto oder, auf dem Server selbst, über die Konsole und die Hilfedateien
([Eine Schnittstelle für einen Agenten wählen](agentische-ablaeufe/eine-schnittstelle-waehlen.md)). Ein MCP-Server ist heute nicht Teil
von Exponential.

**Wie sollte sich ein Agent authentifizieren?**
Mit seinem eigenen Konto und den engsten Zugangsdaten: einem persönlichen API-Schlüssel mit Geltungsbereichen für REST oder einem
persönlichen Token mit kurzer Laufzeit für expservices ([Zugangsdaten für Agenten](agentische-ablaeufe/zugangsdaten-fuer-agenten.md)).

**Werden die Schreibzugriffe eines Agenten geprüft?**
Schreibzugriffe über REST und expservices wirken als eigene Aktionen des Kontos. Für eine Prüfung schreiben Sie in eine Sektion
mit einem Freigabe-Workflow, die Besucher nicht sehen können
([Einen Menschen beteiligt halten](agentische-ablaeufe/einen-menschen-beteiligt-halten.md)).

**Was ist mit Prompt-Injection?**
Der Assistent kennzeichnet Inhalte als Daten, begrenzt, was ein irregeleitetes Modell tun kann, und hört nach Webtext auf zu
schreiben; Ihr eigener Agent braucht dieselbe Disziplin
([Nicht vertrauenswürdige Daten und Prompt-Injection](agentische-ablaeufe/nicht-vertrauenswuerdige-daten-und-prompt-injection.md)).

**Kann ein Agent die Dokumentation lesen?**
Ja: `./console exp:help search` und `show` oder die Markdown-Dateien in `doc/`
([Agentenfreundliche Dokumentation](agentische-ablaeufe/agentenfreundliche-dokumentation.md)).

**Was, wenn etwas schiefgeht?**
Schalten Sie den Assistenten aus, deaktivieren Sie das Konto des Agenten, widerrufen Sie seine Schlüssel und lesen Sie den
Audit-Trail ([Reaktion auf KI-Vorfälle](steuerung/reaktion-auf-ki-vorfaelle.md)).

## Verwandte Seiten

- [KI, Agenten und agentisches Arbeiten: hier beginnen](ki-und-agenten-hier-beginnen.md)
- [Glossar zu KI, Agenten und agentischem Arbeiten](glossar-zu-ki-und-agenten.md)
- [Einwand: "Was ist mit den KI-Funktionen?"](../fuersprecher/einwand-ki-funktionen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
