---
title: Exponential Intelligence (expai) für Anwendungen
audience: app-creator, developer, admin
category: app-creation
order: 390
summary: Was die Erweiterung expai einer Anwendung bietet - Entwürfe im Namen der Redaktion, Anbieter und Datenrichtlinie, die Job-Warteschlange, Verbrauch und Budgets, Audit - wie Sie sie einschalten und wie sich eine Anwendung einklinkt.
keywords: Exponential Intelligence, KI, Assistent, Entwürfe, Anbieter, Datenrichtlinie, Budgets, Audit, Anwendungsentwickler, expai, AI, assistant, drafts, providers, Anthropic, OpenAI-compatible, Ollama, data policy, budgets, audit, expqueue, app creator
type: help
---
# Exponential Intelligence (expai) für Anwendungen

Diese Seite ist für Anwendungsentwickler, die KI-Unterstützung in ihrer Anwendung möchten oder wissen wollen, wie sie
sich einfügt. Exponential Intelligence ist die Erweiterung `expai`: ein Schreibassistent in der Administration, der
nachschlägt, was er braucht, und **Entwürfe** im Namen der angemeldeten Redakteurin oder des angemeldeten Redakteurs
schreibt, ohne jemals selbst zu veröffentlichen ([expai](../../../../eng-us/features/6.0/extensions/expai.md)). Die vollständige
Dokumentation liegt in ihrem Repository und unter `extension/expai/doc/`.

## Inhalt

1. [Was sie tut](#was-sie-tut)
2. [Voraussetzungen und Einschalten](#voraussetzungen-und-einschalten)
3. [Datenrichtlinie, Verbrauch und Audit](#datenrichtlinie-verbrauch-und-audit)
4. [Wie sich Anwendungen einklinken](#wie-sich-anwendungen-einklinken)
5. [Gestaltungsregeln für KI-Funktionen](#gestaltungsregeln-für-ki-funktionen)

## Was sie tut

- **Der Reiter Intelligenz** eines Knotens: eine Unterhaltung pro Benutzer und Knoten; jedes Nachschlagen und jeder
  Entwurf ist ein Schritt.
- **Das Bearbeitungsformular**: Felder aus einer Kurzbeschreibung füllen, Feldaktionen (schreiben, umschreiben, kürzen,
  übersetzen, vorschlagen), Voreinstellungen pro Klasse, ein Menü Verbessern, Alternativtexte für Bilder aus einem
  Bildmodell.
- **Vector**: Threads mit Planungs- und Schreibmodus, gestufte Prüfung, Checkpoints, Zurücksetzen.
- **Ketten** und die Übersetzungssynchronisierung laufen über die Job-Warteschlange.

## Voraussetzungen und Einschalten

Laut Funktionsseite: Exponential 6.0.14 oder neuer, PHP 8.0+ mit `curl`, `dom`, `json`, `mbstring`; ezjscore und
ezformtoken; `expqueue` für eingereihte Arbeit; ein Anbieterschlüssel oder ein lokales Modell (Anthropic,
OpenAI-kompatible Dienste oder Ollama).

1. Aktivieren Sie sie in jedem Admin-Siteaccess (`ActiveAccessExtensions[]=expai`) oder überall.
2. Legen Sie Schlüssel ausschließlich in `settings/override/expai.ini.append.php` ab (Modus 0640, nie committet).
3. Aktivieren und betreiben Sie `expqueue` für eingereihte Arbeit ([Warteschlangen mit expqueue](warteschlangen-mit-expqueue.md)).

## Datenrichtlinie, Verbrauch und Audit

- Ein Hauptschalter (`[AISettings] Enabled`) und ein Veto-Hook.
- Pro Anbieter: eine Region (`us`, `eu`, `local`) und eine Aufbewahrungsdauer; Regeln pro Sektion und Klasse.
- Jede Anfrage in einem Verbrauchsprotokoll (nur Benutzer-IDs), geschätzte Kosten, Limits und Budgets.
- Jede Anfrage, jeder Entwurf und jede Zustandsänderung eines Entwurfs im Kanal `ai` des Audit-Trails; Prompts,
  Antworten und Schlüssel werden nie aufgezeichnet. **EI: Übersicht** gibt einer oder einem Datenschutzbeauftragten
  eine druckbare Seite.

## Wie sich Anwendungen einklinken

| Bedarf | Mechanismus |
|---|---|
| Eine Feldaktion für Ihre Klasse | nur Einstellungen (`[EditViewSettings] FieldActions[]`) |
| Der Assistent liest Ihre Daten | eine Werkzeugklasse oder ein Lesedienst von expservices mit `'ai' => true` |
| Ein anderer KI-Dienst | eine Anbieterklasse oder eine Anbietergruppe in den Einstellungen |
| Oberfläche im Panel | ein Template für einen Panel-Abschnitt und JavaScript-Hooks |

Details und ausgearbeitete Beispiele: [Exponential Intelligence erweitern](exponential-intelligence-erweitern.md).

## Gestaltungsregeln für KI-Funktionen

- KI schreibt Entwürfe; Menschen veröffentlichen. Halten Sie das in Ihren eigenen Funktionen genauso.
- Laufen Sie als der Benutzer, mit dessen Rechten.
- Halten Sie Geheimnisse in den Einstellungen, nie in Prompts oder Nutzdaten.
- Machen Sie Kosten sichtbar und begrenzt.

## Verwandte Seiten

- [Exponential Intelligence erweitern](exponential-intelligence-erweitern.md)
- [Warteschlangen mit expqueue](warteschlangen-mit-expqueue.md)
- [Datenschutzstandard](../../../../eng-us/standards/privacy.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
