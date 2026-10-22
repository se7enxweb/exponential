---
title: Welcher Weg zu Ihrer Aufgabe passt
audience: ai-agents, developer, app-creator, admin, editor
category: ai-agents
order: 6
summary: Eine kurze Entscheidungshilfe - beantworten Sie einige Fragen dazu, was eine KI mit Exponential tun soll, und erhalten Sie den zu verwendenden Teil (Exponential Intelligence, die REST-API, expservices, ezjscore oder die Hilfe auf der Kommandozeile), das Konto, unter dem er laufen soll, und die nächste zu lesende Seite.
keywords: Entscheidung, welches Werkzeug, Weg, wählen, Assistent oder Agent, REST oder expservices, expai, expservices, REST-API, nur lesend, schreiben, agentischer Ablauf, decision, which tool, path, choose, assistant or agent, REST or expservices, read only, write, agentic workflow
type: help
---
# Welcher Weg zu Ihrer Aufgabe passt

Diese Seite richtet sich an alle, die wissen, was eine KI tun soll, aber nicht, welchen Teil von Exponential sie verwenden sollen.
Beantworten Sie die Fragen der Reihe nach; jede Antwort verweist auf einen Teil und auf die nächste zu lesende Seite.

## Inhalt

1. [Frage 1: Wer arbeitet, ein Mensch oder ein Programm?](#frage-1-wer-arbeitet-ein-mensch-oder-ein-programm)
2. [Frage 2: Liest es nur oder schreibt es auch?](#frage-2-liest-es-nur-oder-schreibt-es-auch)
3. [Frage 3: Wo läuft der Agent?](#frage-3-wo-läuft-der-agent)
4. [Die Antworten in einer Tabelle](#die-antworten-in-einer-tabelle)
5. [Kombinationen, die gut funktionieren](#kombinationen-die-gut-funktionieren)
6. [Was keiner der Wege Ihnen gibt](#was-keiner-der-wege-ihnen-gibt)

## Frage 1: Wer arbeitet, ein Mensch oder ein Programm?

- **Ein Mensch in der Administrationsoberfläche**, der Hilfe beim Schreiben, Umschreiben, Kürzen, Übersetzen oder Ausfüllen von
  Feldern möchte: Verwenden Sie **Exponential Intelligence**. Es arbeitet als dieser Mensch, in den Bildschirmen, die er schon
  verwendet, und jedes Ergebnis ist ein Entwurf, den er prüft ([Exponential Intelligence](expai/README.md)).
- **Ein Programm oder ein KI-Agent**, das selbstständig oder für einen Entwickler arbeitet: weiter zu Frage 2.

## Frage 2: Liest es nur oder schreibt es auch?

| Der Agent | Verwenden Sie |
|---|---|
| Liest nur veröffentlichte Inhalte, für eine App, einen Bericht oder eine andere Website | Die REST-API mit einem Schlüssel, der nur den Geltungsbereich `read` hat ([Ein nur lesender API-Schlüssel, Schritt für Schritt](agentische-ablaeufe/ein-nur-lesender-api-schluessel-schritt-fuer-schritt.md)) |
| Liest Daten der Administration über Inhalte hinaus (Benutzer, Caches, Cronjobs, Tags, das Audit) | expservices mit dem Token eines Kontos, das auf diese Dienste beschränkt ist ([Agenten und expservices](agentische-ablaeufe/agenten-und-expservices.md)) |
| Legt im Rahmen eines Ablaufs Inhalte an oder ändert sie | Die REST-API v2 oder expservices mit einem eigenen Konto, idealerweise in eine geprüfte Sektion ([Einen Menschen beteiligt halten](agentische-ablaeufe/einen-menschen-beteiligt-halten.md)) |
| Beantwortet Fragen dazu, wie die Installation konfiguriert ist | Die Konsole auf dem Server und die Dokumentation ([Ein Diagnoseablauf mit der Konsole](agentische-ablaeufe/ablauf-diagnose-mit-der-konsole.md)) |

## Frage 3: Wo läuft der Agent?

| Der Agent läuft | Folge |
|---|---|
| Auf demselben Server, von Ihnen gestartet (ein Coding-Assistent in einem Terminal) | Er kann die Konsole verwenden (`./console exp:help`, `exp:ini get`, `exp:audit`) und die Dateien lesen, als der Systembenutzer, unter dem Sie ihn starten |
| Auf einer anderen Maschine, über das Netzwerk | Die REST-API oder expservices über HTTPS mit einem Token |
| In der Administration, für den angemeldeten Redakteur | Exponential Intelligence mit seinen eigenen Werkzeugen ([Web- und Verlaufswerkzeuge](expai/web-und-verlaufswerkzeuge.md), [Dienstwerkzeuge](expai/dienstwerkzeuge.md)) |

## Die Antworten in einer Tabelle

| Aufgabe | Teil | Läuft als | Als Nächstes lesen |
|---|---|---|---|
| Einen Artikel aus einer Vorgabe entwerfen | Exponential Intelligence | der Redakteur | [Was Exponential Intelligence tut](expai/was-exponential-intelligence-tut.md) |
| Übersetzungen mit der Ausgangssprache im Gleichschritt halten | Exponential Intelligence, Übersetzungsabgleich | der Redakteur, der freigibt | [Übersetzungshilfe](expai/uebersetzungshilfe.md) |
| Alternativtext für Bilder vorschlagen | Exponential Intelligence | der Redakteur | [Alternativtext für Bilder](expai/alternativtext-fuer-bilder.md) |
| Einen Bericht über veröffentlichte Inhalte erstellen | REST-API, Geltungsbereich `read` | ein eigenes Lesekonto | [Ein nur lesender Inhaltsbericht](agentische-ablaeufe/ablauf-nur-lesender-inhaltsbericht.md) |
| Herausfinden, warum eine Einstellung oder ein Logeintrag so ist, wie er ist | Konsole und Hilfe auf dem Server | der Systembenutzer | [Ein Diagnoseablauf mit der Konsole](agentische-ablaeufe/ablauf-diagnose-mit-der-konsole.md) |
| Inhalte aus einem Feed importieren | REST v2 oder expservices | ein eigenes Importkonto | [Ein Ablauf für den Inhaltsimport](agentische-ablaeufe/ablauf-inhaltsimport.md) |
| In der Dokumentation nachschlagen | `./console exp:help` oder die Markdown-Dateien | jeder mit den Dateien | [Agentenfreundliche Dokumentation](agentische-ablaeufe/agentenfreundliche-dokumentation.md) |

## Kombinationen, die gut funktionieren

- **Ein Coding-Assistent in einem Projekt**: `exp:help` für die Dokumentation plus die Konsole für Laufzeitfakten
  ([Coding-Agenten in einem Exponential-Projekt](agentische-ablaeufe/coding-agenten-in-einem-exponential-projekt.md)).
- **Ein Redaktionsteam**: Exponential Intelligence mit einem Freigabe-Workflow, einer Datenrichtlinie und monatlichen Grenzen
  ([Steuerung](steuerung/README.md)).
- **Eine Importstrecke**: ein eigenes Konto mit `content/create`, auf einen Teilbaum beschränkt, zuerst ein Testlauf und danach
  der Audit-Trail zur Kontrolle ([Rollen und minimale Rechte](steuerung/rollen-und-minimale-rechte.md)).

## Was keiner der Wege Ihnen gibt

- Kein Weg lässt eine KI hinter dem Rücken der Redakteure veröffentlichen: Der Assistent schreibt nur Entwürfe, und jeder andere
  Weg handelt als ein Konto, dessen Berechtigungen Sie festlegen.
- Kein Weg wählt für Sie einen KI-Anbieter: Exponential sendet nichts an einen Anbieter, bis ein Administrator einen konfiguriert.
- Kein Weg macht Inhalte zu Anweisungen: Inhalte, die an einen Agenten zurückgegeben werden, sind Daten
  ([Nicht vertrauenswürdige Daten und Prompt-Injection](agentische-ablaeufe/nicht-vertrauenswuerdige-daten-und-prompt-injection.md)).

## Verwandte Seiten

- [Was es heute gibt](was-es-heute-gibt.md)
- [Eine Schnittstelle für einen Agenten wählen](agentische-ablaeufe/eine-schnittstelle-waehlen.md)
- [KI, Agenten und agentisches Arbeiten: häufige Fragen](haeufige-fragen-zu-ki-und-agenten.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
