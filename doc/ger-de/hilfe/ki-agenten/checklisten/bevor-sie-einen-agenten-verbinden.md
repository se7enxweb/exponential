---
title: Bevor Sie einen Agenten verbinden
audience: ai-agents, developer, admin, app-creator
category: ai-agents
order: 92
summary: Eine Checkliste, bevor ein KI-Agent über die REST-API, expservices oder die Konsole Zugang zu einer Exponential-Installation erhält - Zweck, Konto und Rolle, Zugangsdaten, Gestaltung der Werkzeuge, Daten, die den Anbieter des Agenten erreichen dürfen, Schreibzugriffe, nicht vertrauenswürdige Eingaben, Aufzeichnungen und Widerruf.
keywords: Checkliste, Agent verbinden, REST-API-Schlüssel, expservices-Token, Konsole, Agentenkonto, minimale Rechte, Geltungsbereiche, Prompt-Injection, Audit, widerrufen, checklist, connect agent, REST API key, expservices token, console, agent account, least privilege, scopes, prompt injection, revoke
type: help
---
# Bevor Sie einen Agenten verbinden

Diese Checkliste richtet sich an Entwickler und Administratoren, die einem KI-Agenten gleich Zugang zu einer Installation geben.
Gehen Sie sie für jeden Agenten durch; jeder Punkt verlinkt auf die Seite, die ihn erklärt.

## Inhalt

1. [Zweck](#zweck)
2. [Konto und Zugangsdaten](#konto-und-zugangsdaten)
3. [Gestaltung der Werkzeuge](#gestaltung-der-werkzeuge)
4. [Daten](#daten)
5. [Schreibzugriffe](#schreibzugriffe)
6. [Aufzeichnungen und Widerruf](#aufzeichnungen-und-widerruf)

## Zweck

- [ ] Die Aufgabe des Agenten ist aufgeschrieben, mit dem, was er lesen und was er ändern muss.
- [ ] Die Schnittstelle ist für diese Aufgabe gewählt ([Eine Schnittstelle für einen Agenten wählen](../agentische-ablaeufe/eine-schnittstelle-waehlen.md)).
- [ ] Der Agent arbeitet zuerst auf einer Entwicklungs- oder Testinstallation.

## Konto und Zugangsdaten

- [ ] Ein eigenes Konto in einer Gruppe für Agenten; kein Konto einer Person, kein Administrator
  ([Rollen und minimale Rechte](../steuerung/rollen-und-minimale-rechte.md)).
- [ ] Seine Rolle ist nach Teilbaum, Sektion, Klasse und Sprache so weit beschränkt, wie die Aufgabe es erlaubt.
- [ ] Wirksame Rechte mit `./console exp:access:check` oder einer Testanmeldung geprüft.
- [ ] Die Zugangsdaten sind die engsten: ein Schlüssel nur mit den nötigen Geltungsbereichen, ein Token mit kurzer Laufzeit
  ([Ein nur lesender API-Schlüssel, Schritt für Schritt](../agentische-ablaeufe/ein-nur-lesender-api-schluessel-schritt-fuer-schritt.md)).
- [ ] Die Zugangsdaten liegen in einem Geheimnisspeicher oder einer Umgebungsvariablen, nie in einem Prompt, einem Repository
  oder einer geteilten Konfigurationsdatei ([Zugangsdaten für Agenten](../agentische-ablaeufe/zugangsdaten-fuer-agenten.md)).

## Gestaltung der Werkzeuge

- [ ] Ein Werkzeug je erlaubter REST-Route oder expservices-Dienst in der Definition des Agenten, kein allgemeines HTTP- oder
  Shell-Werkzeug ([Agenten und die REST-API](../agentische-ablaeufe/agenten-und-die-rest-api.md#die-routen-einem-modell-beschreiben)).
- [ ] Eingaben im Code geprüft; der Schlüssel vom Code hinzugefügt, dem Modell nie gezeigt.
- [ ] Listen mit kleinen Grenzen seitenweise abgerufen.
- [ ] Für einen Coding-Agenten auf dem Server: nur lesende Befehle, auf einer Entwicklungskopie
  ([Coding-Agenten in einem Exponential-Projekt](../agentische-ablaeufe/coding-agenten-in-einem-exponential-projekt.md)).

## Daten

- [ ] Alles, was das Konto des Agenten lesen kann, darf an den Modellanbieter des Agenten gehen
  ([Datenschutz und Daten, die die Website verlassen](../steuerung/datenschutz-und-daten-die-die-website-verlassen.md#agenten-die-inhalte-lesen)).
- [ ] Personenbezogene Daten (Konten, Formulareinsendungen, Bestellungen) liegen außerhalb der Rolle des Kontos, sofern die
  Aufgabe sie nicht braucht.
- [ ] Die Transkripte der Agentenplattform sind von Ihren Aufbewahrungsregeln abgedeckt.

## Schreibzugriffe

- [ ] Der Agent beginnt nur lesend.
- [ ] Wenn er schreibt: Geltungsbereich oder Richtlinie zum Schreiben bewusst vergeben, für einen Teilbaum und eine Klasse, auf
  einer Testinstallation geprobt ([Ein Ablauf für den Inhaltsimport](../agentische-ablaeufe/ablauf-inhaltsimport.md)).
- [ ] Schreibzugriffe, die eine menschliche Prüfung brauchen, gehen in eine Sektion mit einem Freigabe-Workflow, nicht direkt zu
  den Besuchern ([Einen Menschen beteiligt halten](../agentische-ablaeufe/einen-menschen-beteiligt-halten.md)).
- [ ] Nicht vertrauenswürdige Eingaben (Feeds, Benutzerinhalte, Webseiten) werden als Daten behandelt
  ([Nicht vertrauenswürdige Daten und Prompt-Injection](../agentische-ablaeufe/nicht-vertrauenswuerdige-daten-und-prompt-injection.md)).

## Aufzeichnungen und Widerruf

- [ ] Sie wissen, wo seine Aktionen festgehalten werden: im Audit-Trail unter seinem eigenen Konto
  ([Der Audit-Trail für KI](../steuerung/audit-trail-fuer-ki.md)).
- [ ] Jemand liest sie nach jedem automatisierten Lauf.
- [ ] Sie wissen, wie Sie ihn sofort stoppen: das Konto deaktivieren, den Schlüssel oder das Token widerrufen
  ([Reaktion auf KI-Vorfälle](../steuerung/reaktion-auf-ki-vorfaelle.md)).

## Verwandte Seiten

- [Checklisten zu KI und Agenten](README.md)
- [Agentische Abläufe](../agentische-ablaeufe/README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
