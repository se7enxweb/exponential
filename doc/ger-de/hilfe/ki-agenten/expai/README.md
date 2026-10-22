---
title: Exponential Intelligence für KI-Nutzer
audience: ai-agents, editor, admin, developer
category: ai-agents
order: 20
summary: Der Teil des Abschnitts zu KI, Agenten und agentischem Arbeiten über Exponential Intelligence (expai), den Schreibassistenten der Administrationsoberfläche - was er tut, welche Anbieter er unterstützt, wie Entwürfe und Prüfung funktionieren, Hilfe bei Übersetzung und Alternativtext, Vector und Ketten, Berechtigungen und der Hauptschalter - mit jeder Seite in einer Tabelle.
keywords: Exponential Intelligence, expai, Schreibassistent, KI-Entwürfe, Anbieter, lokales Modell, Übersetzung, Alternativtext, Vector, Ketten, Berechtigungen, Hauptschalter, Veto, writing assistant, AI drafts, providers, local model, translation, alt text, chains, permissions, master switch
type: help
---
# Exponential Intelligence für KI-Nutzer

Dieser Teil richtet sich an Redakteure, Website-Verantwortliche und Administratoren, die Exponential Intelligence verwenden oder
darüber entscheiden: den in die Administrationsoberfläche eingebauten Schreibassistenten. Er erklärt in einfacher Sprache, was der
Assistent mit Ihren Inhalten und Ihrem Geld tut, und verlinkt für jede Einstellung auf die eigene Dokumentation der Erweiterung.

## Inhalt

1. [Was er ist](#was-er-ist)
2. [Jede Seite dieses Teils](#jede-seite-dieses-teils)
3. [Empfohlene Lesereihenfolge](#empfohlene-lesereihenfolge)
4. [Wo die vollständige Dokumentation liegt](#wo-die-vollständige-dokumentation-liegt)

## Was er ist

Exponential Intelligence ist die Erweiterung `expai`. Redakteure bitten in einfacher Sprache um neue Inhalte oder um Änderungen;
der Assistent schlägt nach, was er braucht, und schreibt **Entwürfe**, als der angemeldete Redakteur und mit dessen eigenen
Berechtigungen. Nichts wird von selbst veröffentlicht ([README](../../../../../README.md#exponential-intelligence-new),
[Funktionsseite zu expai](../../../funktionen/6.0/erweiterungen/expai.md)).

Er erscheint an diesen Stellen der Administration:

| Ort | Was er bietet |
|---|---|
| Der Reiter **Intelligence** der Vollansicht eines Knotens | Ein Gespräch je Benutzer und Knoten; Abfragen und Entwürfe als Schritte gezeigt; Links zum Bearbeiten, Ansehen und Verwerfen jedes Entwurfs |
| Das Bearbeitungsformular | "Fill the fields" aus einer Vorgabe, Aktionen je Feld (Write, Rewrite, Shorten, Translate, Suggest), Vorlagen, das Menü **Improve**, Alternativtext für Bilder |
| **Vector** (`expai/vector`) | Threads mit Geltungsbereich, Plan- und Schreibmodus, gestufte Prüfung jeder Änderung |
| Dashboard > Exponential Intelligence | Geschätzte Kosten, Tokens, Anfragen, Entwürfe, Grenzen und "Start writing" |
| Setup > EI: Summary | Eine druckbare Seite für einen Datenschutzbeauftragten |

## Jede Seite dieses Teils

| Seite | Was sie beantwortet |
|---|---|
| [Was Exponential Intelligence tut](was-exponential-intelligence-tut.md) | Die Funktionen für Redakteure, Schritt für Schritt, und was an das Modell gesendet wird |
| [Entwürfe und menschliche Prüfung](entwuerfe-und-menschliche-pruefung.md) | Warum nichts von selbst veröffentlicht wird, was "Send for approval" tut und die Kennzeichnung KI-geschriebener Versionen |
| [Anbieter und Modelle](anbieter-und-modelle.md) | Anthropic, OpenAI-kompatible Dienste und lokale Modelle; was mit welchem erprobt ist |
| [Einen Anbieter konfigurieren](einen-anbieter-konfigurieren.md) | Die Einstellungen eines Anbieters und eines Modells, wohin Schlüssel gehören und wie Sie sie prüfen |
| [Lokale Modelle](lokale-modelle.md) | Ein Modell auf Ihrem eigenen Server betreiben, sodass nichts ihn verlässt |
| [Das Schreibtor und die Probe](das-schreibtor-und-die-probe.md) | Welche Modelle Entwürfe schreiben dürfen und wie ein Modell sich das verdient |
| [Übersetzungshilfe](uebersetzungshilfe.md) | Die Feldaktion Translate, Übersetzen im Panel und der Übersetzungsabgleich |
| [Alternativtext für Bilder](alternativtext-fuer-bilder.md) | Vorschläge eines Vision-Modells und die Wahl "dekorativ", die beim Redakteur bleibt |
| [Vector und gestufte Prüfung](vektorsuche-und-gestufte-pruefung.md) | Massenarbeit mit einem Plan, einer Kostenschätzung und Prüfung je Feld, bevor etwas geschrieben wird |
| [Ketten und eingereihte Arbeit](ketten-und-eingereihte-arbeit.md) | Mehrstufiges Schreiben mit einem menschlichen Kontrollpunkt, über die Job-Warteschlange |
| [Berechtigungen für KI-Funktionen](berechtigungen-fuer-ki-funktionen.md) | Die Richtlinien `expai/*` und wie sie mit Inhaltsberechtigungen zusammenwirken |
| [Der Hauptschalter und der Veto-Hook](hauptschalter-und-veto.md) | Den Assistenten überall oder je Siteaccess ausschalten und Anfragen nach Regeln ablehnen |
| [Web- und Verlaufswerkzeuge](web-und-verlaufswerkzeuge.md) | Seiten von der Erlaubnisliste abrufen, eine Websuche und die Projektgeschichte, mit ihren Schutzmaßnahmen |
| [Dienstwerkzeuge](dienstwerkzeuge.md) | Lesedienste von expservices, die der Assistent als der Redakteur aufrufen darf |
| [Das Dashboard und das Protokoll](dashboard-und-protokoll.md) | Nutzung, Kosten, Entwürfe und Grenzen; jede Anfrage im EI: Log |
| [Den Assistenten erweitern](den-assistenten-erweitern.md) | Feldaktionen, Werkzeuge, Anbieter und Panel-Abschnitte, die eine Erweiterung hinzufügen kann |

## Empfohlene Lesereihenfolge

| Sie sind | Lesen Sie |
|---|---|
| Ein Redakteur | Was er tut, Entwürfe und menschliche Prüfung, Übersetzungshilfe, Alternativtext, dann die [Prüfcheckliste](../checklisten/ki-entwuerfe-pruefen.md) |
| Ein Administrator | Anbieter und Modelle, Einen Anbieter konfigurieren, Das Schreibtor, Berechtigungen, Der Hauptschalter, dann die [Checkliste vor dem Einschalten](../checklisten/bevor-sie-exponential-intelligence-einschalten.md) |
| Ein Datenschutzbeauftragter | Anbieter und Modelle, dann [Datenschutz und Daten, die die Website verlassen](../steuerung/datenschutz-und-daten-die-die-website-verlassen.md) und [Der Audit-Trail für KI](../steuerung/audit-trail-fuer-ki.md) |
| Ein Entwickler | Dienstwerkzeuge, Den Assistenten erweitern, dann [Exponential Intelligence erweitern](../../ersteller/anwendungsentwickler/exponential-intelligence-erweitern.md) |

## Wo die vollständige Dokumentation liegt

- **Im Hilfe-Center**: Ist expai aktiv, öffnet Dashboard > EI: Help seine Dokumentation, einschließlich seines eigenen
  Benutzerhandbuchs, unter der Dokumentation der Erweiterungen (braucht `exphelp/read`).
- **Auf GitHub**: das [expai-Repository](https://github.com/se7enxweb/expai) mit
  [INSTALL.md](https://github.com/se7enxweb/expai/blob/main/INSTALL.md),
  [USAGE.md](https://github.com/se7enxweb/expai/blob/main/USAGE.md) und dem
  [Dokumentationsindex](https://github.com/se7enxweb/expai/blob/main/doc/index.md).
- **Für Trainer**: der [Kurs zu Exponential Intelligence](../../trainer/exponential-intelligence/README.md).

## Verwandte Seiten

- [KI, Agenten und agentisches Arbeiten: hier beginnen](../ki-und-agenten-hier-beginnen.md)
- [Exponential Intelligence (expai) für Anwendungen](../../ersteller/anwendungsentwickler/exponential-intelligence.md)
- [Argumente: Exponential Intelligence](../../fuersprecher/gespraechspunkte-exponential-intelligence.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
