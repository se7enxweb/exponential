---
title: "Thema: ein Assistent, der nur Entwürfe schreibt"
audience: press
category: press-media
order: 55
summary: Ein Themenbeitrag über eine Designentscheidung von Exponential Intelligence - der Assistent schreibt Entwürfe und veröffentlicht nie von sich aus - mit den Schutzmechanismen darum herum (Rechte, Freigabe-Workflow, Audit, Hauptschalter) und den Fakten und Quellen zum Drucken.
keywords: Thema, Exponential Intelligence, expai, nur Entwürfe, Mensch in der Schleife, Freigabe-Workflow, Rechte, Audit, KI-Sicherheit, Presse, story, drafts only, human in the loop, approval workflow, permissions, audit, AI safety, press
type: help
---
# Thema: ein Assistent, der nur Entwürfe schreibt

Diese Seite ist für Journalisten, die über den verantwortungsvollen Einsatz von KI im Publizieren schreiben. Sie
konzentriert sich auf eine Entscheidung bei Exponential Intelligence: Der Assistent schreibt Entwürfe, und ein Mensch
entscheidet, was veröffentlicht wird. Das umfassendere Thema ist [Thema: Exponential Intelligence](thema-exponential-intelligence.md).

## Inhalt

1. [Das Thema in einem Absatz](#das-thema-in-einem-absatz)
2. [Was neu ist, und seit wann](#was-neu-ist-und-seit-wann)
3. [Die Schutzmechanismen, einer nach dem anderen](#die-schutzmechanismen-einer-nach-dem-anderen)
4. [Fakten, die Sie drucken können](#fakten-die-sie-drucken-können)
5. [Was Sie nicht behaupten sollten](#was-sie-nicht-behaupten-sollten)
6. [Blickwinkel und Ideen für Überschriften](#blickwinkel-und-ideen-für-überschriften)
7. [Wen Sie fragen](#wen-sie-fragen)
8. [Verwandte Seiten](#verwandte-seiten)

## Das Thema in einem Absatz

Viele KI-Werkzeuge veröffentlichen für Sie. Exponential Intelligence tut das nicht: „Drafts only. It never publishes on
its own. 'Send for approval' runs the normal publish operation as the editor, so an approval workflow holds the version
for the approver“ ([README, Safety](https://github.com/se7enxweb/exponential/blob/main/README.md#safety)) – nur
Entwürfe, nie eine Veröffentlichung von sich aus, und „Send for approval“ führt den normalen Veröffentlichungsvorgang
als der Redakteur aus, sodass ein Freigabe-Workflow die Version für den Freigebenden zurückhält. Der Assistent läuft mit
den Inhaltsrechten der Person, die ihn gefragt hat, jede von ihm geschriebene Version trägt die Kennzeichnung
„Exponential Intelligence“, und jede Anfrage ist ein Audit-Ereignis. Die Entscheidung bewahrt die Regel der Redaktion:
Ein Mensch ist verantwortlich für das, was online geht.

## Was neu ist, und seit wann

Der Assistent kam am 12. Oktober 2026 in die Distribution von Exponential, in der Entwicklungslinie 6.0.15, die nicht
erschienen ist ([Changelog von 6.0.15](../../../changelogs/6.0/6.0.15.md#12-october-2026-exponential-intelligence-in-the-distribution)).
Die Regel „nur Entwürfe“ ist ab diesem Zeitpunkt auf der Funktionsseite der Erweiterung und in der README beschrieben.

## Die Schutzmechanismen, einer nach dem anderen

| Schutzmechanismus | Was er bedeutet | Quelle |
|---|---|---|
| Nur Entwürfe | Der Assistent schreibt Entwürfe; er veröffentlicht nie von sich aus. | [expai, Kurz gesagt](../../../eng-us/features/6.0/extensions/expai.md#in-short) |
| Die Rechte des Redakteurs | Jede Abfrage und jeder Entwurf läuft als der angemeldete Redakteur, der damit nicht mehr tun kann als ohne. | [README, Safety](https://github.com/se7enxweb/exponential/blob/main/README.md#safety) |
| Die Freigabe gilt weiter | „Send for approval“ führt den normalen Veröffentlichungsvorgang aus, sodass ein Freigabe-Workflow die Version zurückhält. | [expai, Für Redakteure](../../../eng-us/features/6.0/extensions/expai.md#for-editors) |
| Eine sichtbare Kennzeichnung | „Exponential Intelligence“ an jeder Version, die er geschrieben hat: Meine Entwürfe, Versionsansichten, Bearbeitungsformulare, Freigabeelemente. | [README, What it does for editors](https://github.com/se7enxweb/exponential/blob/main/README.md#what-it-does-for-editors) |
| Prüfung vor dem Schreiben | Im Arbeitsbereich Vector eine schrittweise Prüfung jeder Änderung pro Feld, bevor etwas geschrieben wird, und „What changed“ mit Rückgängigmachen. | ebenda |
| Eigene Richtlinien | `expai/write`, `expai/vector` und andere, in einer neuen Installation nur der Rolle Administrator zugewiesen. | [README, Safety](https://github.com/se7enxweb/exponential/blob/main/README.md#safety) |
| Audit | Jede Anfrage, jeder Entwurf und jede Zustandsänderung ist ein Audit-Ereignis; nie ein Prompt, eine Antwort oder ein Schlüssel. | ebenda |
| Ein Schalter | `[AISettings] Enabled` schaltet ihn überall oder pro Siteaccess aus; ein Veto-Hook kann eine Anfrage mit Begründung ablehnen. | ebenda |

## Fakten, die Sie drucken können

| Fakt | Quelle |
|---|---|
| „It never publishes on its own.“ (Er veröffentlicht nie von sich aus.) | [expai, Kurz gesagt](../../../eng-us/features/6.0/extensions/expai.md#in-short) |
| Vorschläge im Bearbeitungsformular ändern nichts, bis der Redakteur sie annimmt und den Entwurf speichert. | [Inhalte anlegen und bearbeiten](../inhalte-bearbeiten.md#exponential-intelligence) |
| Ohne einen Schlüssel für einen Anbieter sendet er nichts irgendwohin. | [README, How to enable it](https://github.com/se7enxweb/exponential/blob/main/README.md#how-to-enable-it) |
| Nur Modelle, die einen Live-Test zum Aufrufen von Werkzeugen bestanden haben (oder per Konfiguration als vertrauenswürdig gelten), dürfen Entwürfe schreiben. | [README, Providers](https://github.com/se7enxweb/exponential/blob/main/README.md#providers-hosted-in-the-eu-or-on-your-own-server) |

## Was Sie nicht behaupten sollten

- „Von KI erzeugte Inhalte können unmöglich versehentlich veröffentlicht werden.“ Ein Mensch kann immer noch einen
  schlechten Entwurf veröffentlichen; die Regel ist, dass ein Mensch entscheidet.
- „Es ist sicher“ in allgemeinen Worten. Beschreiben Sie stattdessen die Schutzmechanismen.
- „Er ersetzt Redakteure.“ Die Quellen beschreiben einen Assistenten für Redakteure.
- Alles darüber, wer rechtlich für KI-Texte verantwortlich ist: Das ist Sache jeder einzelnen Organisation.

## Blickwinkel und Ideen für Überschriften

```text
"The AI that is not allowed to press Publish"
"Human in the loop by design: Exponential's assistant writes drafts only"
"Your approval workflow still applies when the AI wrote the draft"
```

Auf Deutsch zum Beispiel: „Die KI, die nicht auf ‚Veröffentlichen‘ drücken darf“, „Der Mensch bleibt in der Schleife:
Der Assistent von Exponential schreibt nur Entwürfe“, „Ihr Freigabe-Workflow gilt auch, wenn die KI den Entwurf
geschrieben hat“.

## Wen Sie fragen

| Frage | Fragen Sie | Wo |
|---|---|---|
| Warum nur Entwürfe, und wie das durchgesetzt wird | 7x | info@se7enx.com oder <https://exponential.earth/about/contact-us> |
| Die vollständige Sicherheitsdokumentation | das Repository der Erweiterung | <https://github.com/se7enxweb/expai> |

## Verwandte Seiten

- [Thema: Exponential Intelligence](thema-exponential-intelligence.md)
- [Gesprächspunkte: Exponential Intelligence](../fuersprecher/gespraechspunkte-exponential-intelligence.md#punkte-für-skeptiker)
- [Thema: das Prüfprotokoll](thema-audit-trail.md)
- [Funktionsthemen: Übersicht](themenbeitraege-uebersicht.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
