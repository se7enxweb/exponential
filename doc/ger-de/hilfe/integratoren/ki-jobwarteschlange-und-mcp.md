---
title: KI-Anbieter, die Jobwarteschlange und MCP
audience: integrator, developer, admin
category: integration
order: 260
summary: Was Exponential für die Anbindung von KI-Anbietern und für Hintergrundarbeit bietet - expai (Exponential Intelligence) mit Anthropic, OpenAI-kompatiblen Diensten und lokalen Modellen, expqueue als Jobwarteschlange für Integrationen, die Dienste, die eine KI aufrufen darf, und der Stand des Model Context Protocol.
keywords: KI, künstliche Intelligenz, expai, Exponential Intelligence, Anbieter, Anthropic, OpenAI-kompatibel, Ollama, lokales Modell, Datenrichtlinie, Region, expqueue, Jobwarteschlange, Worker, Wiederholungen, Idempotenzschlüssel, MCP, Model Context Protocol, Werkzeuge, AI, artificial intelligence, provider, data policy, job queue, retries, idempotency key, tools
type: help
---
# KI-Anbieter, die Jobwarteschlange und MCP

Diese Seite ist für Integratoren, die Exponential an KI-Dienste anbinden oder Integrationsarbeit mit Wiederholungen im
Hintergrund ausführen sollen. Zwei Erweiterungen, die die `composer.json` im Wurzelverzeichnis verlangt, decken das ab:
**expai** (Exponential Intelligence, `~0.0.1.16`) und **expqueue** (die Jobwarteschlange, `~0.1.5`). Beide werden mit
der Distribution installiert und bleiben inaktiv, bis sie aktiviert werden.

## Inhalt

1. [expai: KI-Anbieter für Redakteure](#expai-ki-anbieter-für-redakteure)
2. [expqueue: eine Jobwarteschlange für Integrationen](#expqueue-eine-jobwarteschlange-für-integrationen)
3. [Das Model Context Protocol](#das-model-context-protocol)
4. [Zu klärende Integrationsfragen](#zu-klärende-integrationsfragen)

## expai: KI-Anbieter für Redakteure

expai ist ein Schreibassistent in der Administration: Redakteure fragen in einfacher Sprache, und der Assistent schlägt
nach und schreibt **Entwürfe** als der angemeldete Redakteur, mit dessen Berechtigungen. Er veröffentlicht nie von
selbst ([expai](../../funktionen/6.0/erweiterungen/expai.md)).

| Integrationspunkt | Was er für einen Integrator bedeutet |
|---|---|
| Anbieter | Anthropic, jeder OpenAI-kompatible Dienst oder ein lokales Open-Weight-Modell über Ollama; ein Rückfall auf den Standard der Website |
| Datenrichtlinie | Eine Region (`us`, `eu`, `local`) und Aufbewahrung pro Anbieter, Regeln pro Sektion und Klasse, pro Anfrage festgehalten |
| Schlüssel | Nur in `settings/override/expai.ini.append.php` (`[AISettings] ApiKey`), nie eingecheckt |
| Hauptschalter | `[AISettings] Enabled`, auch pro Siteaccess abschaltbar, dazu ein Veto-Hook, der eine Anfrage mit Begründung ablehnen kann |
| Werkzeuge | Lesedienste von `expservices`, die sich mit `'ai' => true` freischalten, und externe MCP-Server auf einer Positivliste |
| Nutzung und Kosten | Jede Anfrage in einem Nutzungsprotokoll, nur mit Benutzer-IDs; Grenzen und Budgets pro Benutzer, Website und Anbieter |
| Audit | Anfragen, Entwürfe und Zustandsänderungen von Entwürfen im Audit-Kanal `ai`; Prompts, Antworten und Schlüssel werden nie festgehalten |
| Warteschlange | Arbeit in der Warteschlange (Ketten, Synchronisation von Übersetzungen, Alternativtexte in Masse) läuft in `expqueue` |

Ein Dienst der eigenen `expservices`-Domäne Ihrer Integration lässt sich dem Assistenten als Werkzeug anbieten, indem
Sie ihn in seiner Deklaration als für KI lesbar markieren (`'ai' => true`); so können Redakteure beim Schreiben etwa
nach den ERP-Daten eines Produkts fragen
([Exponential Intelligence erweitern](../ersteller/anwendungsentwickler/exponential-intelligence-erweitern.md)).

## expqueue: eine Jobwarteschlange für Integrationen

expqueue ist eine datenbankgestützte Jobwarteschlange: Erzeuger stellen einen Job mit einem Aufruf ein; Worker führen
ihn über Cron, die Kommandozeile oder einen systemd-Dienst aus, nie innerhalb einer Webanfrage
([expqueue](../../funktionen/6.0/erweiterungen/expqueue.md)).

| Funktion | Warum eine Integration sie möchte |
|---|---|
| Wiederholungen mit Backoff und eine Dead-Letter-Ablage | Ein ausfallendes Fremdsystem verliert keine Arbeit |
| Idempotenzschlüssel | Derselbe Datensatz, zweimal eingestellt, läuft einmal |
| Leases mit einem Reaper | Der Job eines abgestürzten Workers wird erneut aufgenommen |
| Prioritätsklassen, faire Planung, Kontingente, Nebenläufigkeitsgrenzen pro Ressource, die sich an Hinweise zu Ratenlimits anpassen | Eine Massensynchronisation verdrängt keine dringenden Jobs, und das Ratenlimit eines Anbieters wird eingehalten |
| Abhängigkeiten, Frühestzeiten, Jobgruppen mit Fortschritt, Abbrechen, Pausieren und Fortsetzen | Mehrstufige Synchronisationen sind sichtbar und steuerbar |
| Setup > Job queue | Betreiber sehen und steuern Jobs (`expqueue/view`, `expqueue/manage`) |

Einschalten, laut Funktionsseite: global aktivieren, die Tabellen anlegen
(`php bin/php/console ext:expqueue:install-schema`), die Autoloads neu erzeugen, deployen und jede Minute einen
Worker ausführen:

```cron
* * * * * cd /path/to/installation && php runcronjobs.php -q expqueue >> var/log/cronjob-expqueue.log 2>&1
```

Prüfen Sie mit `php bin/php/console ext:expqueue:enqueue expqueue.noop`, `ext:expqueue:work --once` und
`ext:expqueue:status`. Eine Erweiterung registriert ihre Jobtypen und Handler in `expqueue.ini`; die Erzeuger-API steht
in der [Dokumentation von expqueue](https://github.com/se7enxweb/expqueue/blob/main/doc/index.md). Für eine
Integration, die bereits eine Outbox-Tabelle und einen Cronjob-Teil nutzt, ist expqueue der nächste Schritt, wenn
Wiederholungen, Prioritäten und Sichtbarkeit zählen
([Warteschlangen mit expqueue](../ersteller/anwendungsentwickler/warteschlangen-mit-expqueue.md)).

## Das Model Context Protocol

Das Kernel-Repository liefert **keinen MCP-Server** mit. Was es gibt:

- expai kann **externe** MCP-Server aufrufen, die ein Administrator auf eine Positivliste setzt.
- Die Übersicht der Erweiterungspunkte (Setup > RAD tools, `settings/rad.ini`) kann MCP-Werkzeugregister zählen, die
  eine Erweiterung deklariert.
- Velocity veröffentlicht `/.well-known/mcp.json` für seine eigenen Handler, nicht für die Inhalte
  ([Velocity: Discovery, Föderation und Deploy](../../funktionen/6.0/velocity-erkennung-foederation-und-deployment.md)).

Eine Integration, die heute einen externen KI-Agenten Inhalte lesen lassen muss, verwendet die REST-API oder
`expservices` mit einem nur lesenden API-Schlüssel oder Token eines Dienstkontos
([API- und Protokollstandards](../../standards/apis-und-protokolle.md#model-context-protocol-mcp)).

## Zu klärende Integrationsfragen

- [ ] Welcher Anbieter und welche Region die Inhalte des Kunden verarbeiten dürfen, und ob das in den
  Auftragsverarbeitungsverträgen des Kunden steht. (Keine Rechtsberatung.)
- [ ] Welche Sektionen und Klassen von der KI-Verarbeitung ausgeschlossen sind.
- [ ] Wer den Assistenten nutzen darf (`expai/write` und die zugehörigen Richtlinien).
- [ ] Budgets und Grenzen pro Benutzer und pro Monat.
- [ ] Ob für vertrauliche Inhalte ein lokales Modell erforderlich ist.
- [ ] Welche Hintergrundarbeit in expqueue läuft, mit welcher Wiederholungsregel und welcher Behandlung der
  Dead-Letter-Ablage.

## Verwandte Seiten

- [expai](../../funktionen/6.0/erweiterungen/expai.md), [expqueue](../../funktionen/6.0/erweiterungen/expqueue.md)
- [Exponential Intelligence für App-Ersteller](../ersteller/anwendungsentwickler/exponential-intelligence.md)
- [Entfernte Dienste mit expservices](entfernte-dienste-mit-expservices.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
