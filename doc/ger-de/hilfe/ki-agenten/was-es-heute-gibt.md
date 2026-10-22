---
title: Was es heute gibt
audience: ai-agents, developer, app-creator, admin
category: ai-agents
order: 5
summary: Eine ehrliche Bestandsaufnahme der KI- und Agentenfunktionen von Exponential 6 - Exponential Intelligence (expai), die REST-API, expservices, ezjscore, der Audit-Trail und die Hilfe auf der Kommandozeile - mit ihrer Herkunft, ob sie mit dem Hauptrepository ausgeliefert werden, was sie brauchen, bevor sie etwas tun, und was heute nicht Teil von Exponential ist.
keywords: Bestandsaufnahme, was es gibt, expai, REST-API, expservices, ezjscore, exp:help, Audit-Trail, Standarddistribution, composer.json, Stand, nicht enthalten, inventory, what exists, REST API, audit trail, default distribution, status, not included
type: help
---
# Was es heute gibt

Diese Seite richtet sich an alle, die vor einer Entscheidung die Fakten brauchen: welche KI- und Agentenfunktionen Exponential 6
wirklich hat, woher jede kommt und was geschehen muss, bevor sie etwas tut. Lesen Sie sie, bevor Sie einem Kollegen oder einem
Kunden eine Funktion versprechen.

## Inhalt

1. [Die Teile auf einen Blick](#die-teile-auf-einen-blick)
2. [Exponential Intelligence (expai)](#exponential-intelligence-expai)
3. [Die REST-API](#die-rest-api)
4. [expservices und ezjscore](#expservices-und-ezjscore)
5. [Der Audit-Trail](#der-audit-trail)
6. [Das Hilfesystem für Agenten](#das-hilfesystem-für-agenten)
7. [Was heute nicht Teil von Exponential ist](#was-heute-nicht-teil-von-exponential-ist)
8. [Wie Sie Ihre eigene Installation prüfen](#wie-sie-ihre-eigene-installation-prüfen)

## Die Teile auf einen Blick

| Teil | Mit dem Hauptrepository ausgeliefert | Nach einer neuen Installation aktiv | Sendet etwas an einen KI-Anbieter |
|---|---|---|---|
| Exponential Intelligence (`expai`) | Ja: `composer.json` verlangt `se7enxweb/expai` | Ja, für die Administrations-Siteaccesses, aber untätig, bis ein Anbieterschlüssel oder ein lokales Modell konfiguriert ist | Erst nachdem Sie einen Anbieter konfiguriert haben |
| REST-API v1 und v2 | Ja: Kernel | Ja, standardmäßig mit erforderlicher Authentifizierung | Nein |
| expservices | Ja: Teil der Standarddistribution | Prüfen Sie es: Der Aufruf `whoami` antwortet, und `expservices.ini [Services] Enabled` ist sein Hauptschalter | Nein |
| ezjscore | Ja | Ja | Nein |
| Audit-Trail | Ja: Kernel | Ja | Nein |
| Hilfesystem und `exp:help` | Ja: Kernel | Ja, für Rollen mit `exphelp/read` | Nein |

Die Faustregel: **Nichts in Exponential spricht mit einem KI-Anbieter, bis ein Administrator einen konfiguriert.** Die anderen
Teile sind Schnittstellen, die ein KI-Agent Ihrer Wahl verwenden kann, mit den Berechtigungen eines Kontos, das Sie wählen.

## Exponential Intelligence (expai)

Ein Schreibassistent in der Administrationsoberfläche. Redakteure fragen in einfacher Sprache; der Assistent liest, was er braucht
(Klassendefinitionen, den aktuellen Inhalt, den Baum ringsum), und schreibt **Entwürfe** als der angemeldete Redakteur. Er
veröffentlicht nie selbst ([README](../../../../README.md#exponential-intelligence-new),
[Funktionsseite zu expai](../../funktionen/6.0/erweiterungen/expai.md)).

- **Wo er liegt**: `extension/expai`, das Composer-Paket `se7enxweb/expai`, vom Haupt-`composer.json` verlangt.
- **Was eine neue Installation tut**: Der Installer aktiviert ihn für die Administrations-Siteaccesses, legt seine Tabellen an und
  gibt seine Richtlinien nur der Rolle Administrator.
- **Was Ihnen bleibt**: ein Anbieterschlüssel in `settings/override/expai.ini.append.php` (oder ein lokales Modell), die
  Cron-Teile `expqueue` und `expai` und die Richtlinie `expai/write` für die Redakteure, die ihn nutzen sollen.
- **Anbieter**: Anthropic, jeder OpenAI-kompatible Dienst über einen Adapter oder ein lokales Open-Weight-Modell über Ollama
  ([Anbieter und Modelle](expai/anbieter-und-modelle.md)).

Seine eigene Dokumentation mit jeder Einstellung liegt im [expai-Repository](https://github.com/se7enxweb/expai) und im
Hilfe-Center unter Extensions, wenn die Erweiterung aktiv ist.

## Die REST-API

`/api/ezp/v1` liest Knoten, Kinder, Felder und Objekte; `/api/ezp/v2` fügt das Anlegen und Entfernen eines Knotens hinzu. Jede
Anfrage läuft als Benutzer und prüft die Richtlinien dieses Benutzers. Ein Agent authentifiziert sich mit einem OAuth2-Token oder
einem persönlichen API-Schlüssel (`expk_...`), der durch Geltungsbereiche begrenzt ist
([REST-API für Anwendungen](../ersteller/anwendungsentwickler/rest-api.md), [Agenten und die REST-API](agentische-ablaeufe/agenten-und-die-rest-api.md)).

## expservices und ezjscore

ezjscore ruft registrierte PHP-Funktionen über HTTP auf und antwortet in JSON
([JSON-Endpunkte mit ezjscore](../ersteller/anwendungsentwickler/json-endpunkte-mit-ezjscore.md)). expservices baut darauf auf und macht
aus den Funktionen der Administration über tausend benannte Dienste, jeder mit einer Zugriffsregel und einem Merkmal `write`,
aufrufbar mit einem persönlichen Token ([Entfernte Dienste mit expservices](../ersteller/anwendungsentwickler/entfernte-dienste-expservices.md),
[Agenten und expservices](agentische-ablaeufe/agenten-und-expservices.md)).

Ein Lesedienst kann sich auch dafür entscheiden, ein Werkzeug des Assistenten zu sein ([Dienstwerkzeuge](expai/dienstwerkzeuge.md)).

## Der Audit-Trail

Der manipulationssichere Audit-Trail des Kernels hält fest, was Konten tun, einschließlich jeder Anfrage, jeder Ablehnung und
jedes Entwurfs des Assistenten in seinem eigenen Kanal `ai` und jedes Ereignisses zu API-Schlüsseln und Tokens
([Der Audit-Trail für KI](steuerung/audit-trail-fuer-ki.md)).

## Das Hilfesystem für Agenten

Jede von Git verfolgte Seite unter `doc/` ist eine Hilfeseite, in Markdown geschrieben. Ein Agent kann sie auf der Kommandozeile
mit `./console exp:help search` und `./console exp:help show` durchsuchen und lesen oder die Markdown-Dateien direkt lesen
([Agentenfreundliche Dokumentation](agentische-ablaeufe/agentenfreundliche-dokumentation.md)).

## Was heute nicht Teil von Exponential ist

- Ein MCP-Server für Agenten ist heute nicht Teil von Exponential. Agenten verwenden stattdessen die REST-API, expservices und die
  Kommandozeile.
- Es ist kein KI-Modell eingebaut; der Assistent ruft die Anbieter auf, die Sie konfigurieren.
- Der Assistent ist keine API für andere Programme; er arbeitet in der Administration für angemeldete Redakteure.

## Wie Sie Ihre eigene Installation prüfen

| Frage | Wie |
|---|---|
| Ist expai vorhanden und aktiv? | Dashboard > Exponential Intelligence in der Administration oder die Seite Extensions unter Setup |
| Ist ein Anbieter konfiguriert? | Setup > EI: Models (`expai/models`) zeigt "key present" oder "No key", nie den Schlüssel |
| Welche expservices gibt es? | Setup > RAD (`/setup/rad`) oder der Befehl `catalog` des Shell-Clients |
| Antwortet die REST-API? | Ein `GET /api/ezp/v1/content/node/2` mit einem Schlüssel ([Agenten und die REST-API](agentische-ablaeufe/agenten-und-die-rest-api.md)) |
| Welche Hilfeseiten erwähnen ein Thema? | `./console exp:help search "<words>"` |

## Verwandte Seiten

- [KI, Agenten und agentisches Arbeiten: hier beginnen](ki-und-agenten-hier-beginnen.md)
- [Welcher Weg zu Ihrer Aufgabe passt](welcher-weg-zu-ihrer-aufgabe-passt.md)
- [Was KI in Exponential nicht tut](was-ki-in-exponential-nicht-tut.md)
- [Womit eine neue Installation kommt](../../funktionen/6.0/standard-extension-auslieferung.md)
- [Erweiterungen, Themes und Pakete](../../funktionen/6.0/erweiterungen/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
