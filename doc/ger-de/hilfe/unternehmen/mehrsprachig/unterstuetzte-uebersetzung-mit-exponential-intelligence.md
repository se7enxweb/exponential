---
title: Unterstützte Übersetzung mit Exponential Intelligence
audience: businesses, editor, project-manager
category: business-solutions
order: 60
summary: Wie Exponential Intelligence einem Unternehmen beim Übersetzen hilft – Übersetzungsentwürfe eines Sprachmodells, die ein Mensch prüft, der Übersetzungsabgleich, der Übersetzungen mit geänderten Quelltexten Schritt halten lässt, Anbieter und Datenregionen, Kosten, die Sie deckeln können, und die Regel, dass der Assistent nichts veröffentlicht.
keywords: Exponential Intelligence, expai, maschinelle Übersetzung, unterstützte Übersetzung, Übersetzungsentwürfe, Übersetzungsabgleich, Prüfung, Freigabe, Datenrichtlinie, Anbieter, EU, lokales Modell, Kosten, Unternehmen, machine translation, assisted translation, translation drafts, translation sync, review, approval, data policy, providers, local model, costs, business
type: help
---
# Unterstützte Übersetzung mit Exponential Intelligence

Diese Seite richtet sich an Führungskräfte und Sprachverantwortliche, die sich von einem Sprachmodell beim Übersetzen
von Inhalten helfen lassen möchten, ohne die Kontrolle darüber abzugeben, was veröffentlicht wird. Exponential
Intelligence, der Schreibassistent der Administration, kann Übersetzungen als **Entwürfe** vorbereiten, die ein Mensch
prüft. Diese Seite erklärt, was er tut, was er nie tut, was Sie über Daten und Kosten entscheiden und wie er sich in
einen Übersetzungsablauf einfügt. Sie ist keine Rechtsberatung zum Datenschutz; beziehen Sie Ihren
Datenschutzbeauftragten ein, bevor Sie ihn einschalten.

## Inhalt

1. [Die Regel: nur Entwürfe](#die-regel-nur-entwürfe)
2. [Wie er beim Übersetzen hilft](#wie-er-beim-übersetzen-hilft)
3. [Der Übersetzungsabgleich](#der-übersetzungsabgleich)
4. [Wohin Ihr Text geht](#wohin-ihr-text-geht)
5. [Kosten und Grenzen](#kosten-und-grenzen)
6. [Einschalten](#einschalten)
7. [Ein Prüfablauf](#ein-prüfablauf)

## Die Regel: nur Entwürfe

Aus der README des Projekts und der [Funktionsseite zu expai](../../../../eng-us/features/6.0/extensions/expai.md):

- Der Assistent schreibt **Entwürfe**, als der angemeldete Redakteur und mit dessen eigenen Berechtigungen. „Er
  veröffentlicht nie von sich aus.“
- **„Send for approval“** führt den normalen Veröffentlichungsvorgang als der Redakteur aus, sodass ein
  Freigabe-Workflow die Version für den Freigebenden zurückhält
  ([Übersetzungsablauf](uebersetzungsablauf.md#freigabe-je-sprache)).
- Jede Version, die er geschrieben hat, trägt in Meine Entwürfe, in Versionsansichten, Bearbeitungsformularen und
  Freigabeelementen die Kennzeichnung „Exponential Intelligence“, damit die Prüfenden wissen, was sie vor sich haben.

Für ein Unternehmen heißt das: Maschinell vorbereiteter Text kann Kunden nur erreichen, wenn eine Person mit
Veröffentlichungsrechten ihn durchlässt.

## Wie er beim Übersetzen hilft

| Wo | Was er tut | Quelle |
|---|---|---|
| Das Bearbeitungsformular | Eine Aktion **Übersetzen** je Feld, neben „Write“, „Rewrite“, „Shorten“ und „Suggest“; die Werte landen zur Prüfung im Formular | [expai](../../../../eng-us/features/6.0/extensions/expai.md#for-editors) |
| Vector, der Arbeitsbereich für größere Aufträge | Pläne mit Kostenschätzung, schrittweise Prüfung jeder Änderung je Feld, bevor etwas geschrieben wird, und „What changed“ mit Rückgängigmachen | ebenda |
| KI-Ketten | Schreiben, prüfen, überarbeiten und übersetzen in Schritten, mit einem menschlichen Kontrollpunkt | ebenda |
| Übersetzungsabgleich | Hält bestehende Übersetzungen auf Stand, wenn sich der Quelltext ändert | unten |

## Der Übersetzungsabgleich

Wenn sich die Quellsprache eines Objekts ändert, sind seine Übersetzungen veraltet. Der Übersetzungsabgleich (translation
sync), beschrieben in der eigenen [Anleitung zum Übersetzungsabgleich](https://github.com/se7enxweb/expai/blob/main/doc/translation-sync.md)
der Erweiterung:

1. findet, was sich in der Quelle geändert hat, seit die Übersetzung erstellt wurde;
2. entscheidet je Übersetzung, ob nichts zu tun ist, ob ein **Patch** der geänderten Passagen genügt oder ob eine
   **vollständige** Übersetzung nötig ist;
3. stellt kleine, günstige Patches sofort in die Warteschlange und lässt einen Redakteur die übrigen freigeben;
4. schreibt das Ergebnis als **Entwurf** der Übersetzung, gekennzeichnet mit „Exponential Intelligence“. „Es wird
   nichts veröffentlicht.“

Bei einem Patch behalten unveränderte Passagen die bestehende Übersetzung exakt bei, und Links, Hervorhebungen und
eingebettete Objekte werden als Platzhalter mitgeführt, sodass sie unverändert bleiben. Die Anleitung führt auf, was
dazu führt, dass das Ergebnis abgelehnt statt geschrieben wird (zum Beispiel eine Antwort, die eine Einbettung
weglässt oder Zahlen verändert).

Er kann nach jeder Veröffentlichung durch ein Workflow-Ereignis gestartet werden, aus dem Abschnitt **Übersetzungen**
des Intelligence-Tabs eines Knotens (Schaltflächen „Check translations“, **Freigeben**, „Translate in full“ und
**Überspringen**) oder von der Kommandozeile aus. Die Schaltflächen erfordern die Richtlinie `expai/translation_sync`
und das Recht, das Objekt in dieser Sprache zu bearbeiten.

> **Tipp**
> Patches erhalten menschliche Korrekturen in unveränderten Passagen. Deshalb passt der Abgleich gut zu einem
> Unternehmen, das seine wichtigen Seiten von Menschen übersetzen lässt und nur bei späteren Änderungen Hilfe möchte.

## Wohin Ihr Text geht

Die in der README aufgeführten Anbieter: Anthropic; jeder OpenAI-kompatible Dienst (Melious.ai in der EU-Region,
Hugging Face, OpenRouter, Mistral und andere); und lokale Open-Weight-Modelle über Ollama, bei denen „die Daten den
Server nie verlassen“. Schutzmaßnahmen aus derselben Quelle und der
[Funktionsseite](../../../../eng-us/features/6.0/extensions/expai.md#for-administrators):

- Jeder Anbieter nennt seine **Region** (`us`, `eu`, `local`) und Aufbewahrungsdauer; Regeln je Sektion und
  Inhaltsklasse legen fest, welche Inhalte wohin gehen dürfen, und jede Anfrage hält fest, wohin sie ging.
- **„EI: Summary“** ist eine druckbare Seite für einen Datenschutzbeauftragten mit Region, Aufbewahrungsdauer, Datum der
  Bedingungen und Schlüsselquelle jedes Anbieters (nie dem Schlüssel selbst).
- Jede Anfrage, jeder Entwurf und jede Statusänderung eines Entwurfs ist ein Ereignis des **Audit-Trails**; Prompts,
  Antworten und Schlüssel werden nie aufgezeichnet.
- Nur Modelle, die einen Live-Test des Tool-Callings bestanden haben oder per Konfiguration als vertrauenswürdig gelten,
  dürfen Entwürfe schreiben.

Entscheiden Sie mit Ihrem Datenschutzbeauftragten, welche Inhalte an welchen Anbieter gesendet werden dürfen, bevor
jemand ihn nutzt ([Sicherheit](../sicherheit/README.md)).

## Kosten und Grenzen

Jede Anfrage steht mit ihren aus einer Preistabelle geschätzten Kosten in einem Nutzungsprotokoll; Grenzen lassen sich
je Benutzer, Tag, Monat, Website und Anbieter festlegen, und Budgets deckeln Arbeit in der Warteschlange
([expai](../../../../eng-us/features/6.0/extensions/expai.md#for-administrators)). Der Übersetzungsabgleich schätzt die Kosten
jedes Auftrags, bevor er in die Warteschlange kommt, und ein Auftrag, den das Monatsbudget nicht mehr aufnehmen kann,
wird zurückgehalten.

Das Projekt veröffentlicht keine Preisliste für Anbieter, und diese Seite nennt auch keine: Die Kosten hängen von
Ihrem Anbieter, Modell und Volumen ab. Nutzen Sie für Ihr Budget die Zahlen des Dashboards aus einem Pilotprojekt.

## Einschalten

Die README beschreibt zwei Fälle:

- **Eine neue Installation**: Exponential Intelligence ist für die Siteaccesses der Administration bereits aktiv,
  seine Tabellen existieren und die Rolle Administrator hat seine Richtlinien. Es „bleibt untätig und sendet nichts“,
  bis ein Anbieterschlüssel hinzugefügt wird, und die Cron-Teile für die Auftragswarteschlange und den Assistenten
  müssen eingeplant werden.
- **Eine ältere Installation**: Aktivieren Sie die Erweiterung, fügen Sie den Schlüssel hinzu, legen Sie ihre Tabellen
  an, geben Sie Redakteuren die Richtlinie `expai/write` und fügen Sie die Cron-Teile hinzu.

Die [Funktionsseite](../../../../eng-us/features/6.0/extensions/expai.md#switch-it-on) enthält die Schritte. Der Hauptschalter
`[AISettings] Enabled` schaltet alles auf einmal aus, oder je Siteaccess.

## Ein Prüfablauf

- [ ] Nur Personen, die die Zielsprache lesen, prüfen und veröffentlichen deren Entwürfe.
- [ ] Die Prüfenden kontrollieren Namen, Zahlen, Preise, rechtliche Formulierungen und Tonfall, nicht nur die
      Grammatik.
- [ ] Maschinell vorbereitete Rechtstexte (AGB, Datenschutzhinweise) werden immer von einer qualifizierten Person
      geprüft.
- [ ] Der Freigabe-Workflow deckt die Sprachen ab, die Entwürfe des Assistenten verwenden.
- [ ] Das Nutzungs-Dashboard wird monatlich mit Ihrem Budget abgeglichen.
- [ ] Ihre Terminologieliste (Produktnamen, Markenbegriffe, die nicht übersetzt werden dürfen) liegt den Prüfenden vor.

## Verwandte Seiten

- [Übersetzungsablauf](uebersetzungsablauf.md)
- [Checkliste Mehrsprachigkeit](checkliste-mehrsprachigkeit.md)
- [expai: Exponential Intelligence](../../../../eng-us/features/6.0/extensions/expai.md)
- [Exponential Intelligence für Anwendungen](../../../../eng-us/help/creators/app-creators/exponential-intelligence.md)
- [Inhaltssprachen](../../../anleitungen/inhaltssprachen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
