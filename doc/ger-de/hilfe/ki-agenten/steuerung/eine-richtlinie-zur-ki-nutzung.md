---
title: Eine Richtlinie zur KI-Nutzung für Ihre Organisation
audience: ai-agents, admin, businesses, project-manager, government
category: ai-agents
order: 85
summary: Eine Vorlage für eine interne Richtlinie zur KI-Nutzung auf einer Exponential-Installation, Regel für Regel, mit der Einstellung oder Funktion, die jede Regel durchsetzt - wer KI verwenden darf, welche Inhalte wohin gehen dürfen, menschliche Prüfung, Verantwortung für veröffentlichten Text, Kosten, Agenten und ihre Konten, Aufzeichnungen und wie Sie sie stoppen.
keywords: Richtlinie zur KI-Nutzung, Vorlage für eine KI-Richtlinie, zulässige Nutzung, Steuerung, Regeln, menschliche Prüfung, Verantwortung, Datenrichtlinie, Budgets, Agenten, Aufzeichnungen, ausschalten, Richtlinie der Organisation, AI use policy, AI policy template, acceptable use, governance, rules, human review, accountability, data policy, agents, records, switch off
type: help
---
# Eine Richtlinie zur KI-Nutzung für Ihre Organisation

Diese Seite richtet sich an Führungskräfte und Administratoren, die aufschreiben, wie ihre Organisation KI mit Exponential
verwendet. Sie ist eine Vorlage: Passen Sie die Formulierung an, streichen Sie, was nicht zutrifft, und behalten Sie die rechte
Spalte, die die Einstellung oder Funktion nennt, die jede Regel zu mehr als einem Versprechen macht. Sie ist keine Rechtsberatung.

## Inhalt

1. [Wie Sie diese Vorlage verwenden](#wie-sie-diese-vorlage-verwenden)
2. [Die Vorlage](#die-vorlage)
3. [Regeln, die die Software nicht durchsetzen kann](#regeln-die-die-software-nicht-durchsetzen-kann)
4. [Die Richtlinie lebendig halten](#die-richtlinie-lebendig-halten)

## Wie Sie diese Vorlage verwenden

1. Lesen Sie [Was es heute gibt](../was-es-heute-gibt.md), damit die Richtlinie nur Funktionen nennt, die Sie haben.
2. Füllen Sie die Klammern aus.
3. Konfigurieren Sie jede durchsetzende Einstellung gleichzeitig mit der Freigabe der Richtlinie.
4. Veröffentlichen Sie die Richtlinie dort, wo Redakteure sie finden, zum Beispiel als Seite Ihres Intranets.

## Die Vorlage

| Regel | Durchgesetzt durch |
|---|---|
| **Geltungsbereich.** KI-Schreibhilfe wird in [Sektionen] von [Gruppen] für [Zwecke] verwendet. | Rollen mit `expai/write`, mit Teilbaum-Einschränkungen zugewiesen ([Berechtigungen für KI-Funktionen](../expai/berechtigungen-fuer-ki-funktionen.md)) |
| **Anbieter.** Anfragen gehen nur an [Anbieter], geprüft am [Datum]. | `[ProviderSettings] Providers[]`, `TermsCheckedAt` ([Einen Anbieter konfigurieren](../expai/einen-anbieter-konfigurieren.md)) |
| **Sensible Inhalte.** [Klassen/Sektionen] gehen nur an EU- oder lokale Anbieter; [Klassen/Sektionen] gehen nie an einen Anbieter. | Regeln in `[DataPolicy]`; Veto-Regeln ([Datenschutz und Daten, die die Website verlassen](datenschutz-und-daten-die-die-website-verlassen.md)) |
| **Menschliche Prüfung.** KI-Ausgabe ist ein Entwurf; ein Mensch prüft ihn vor der Veröffentlichung; [Sektionen] brauchen die Freigabe durch [Rolle]. | Nur Entwürfe; ein Freigabe-Workflow ([Entwürfe und menschliche Prüfung](../expai/entwuerfe-und-menschliche-pruefung.md)) |
| **Verantwortung.** Wer einen Text veröffentlicht, ist dafür verantwortlich, wie für jeden Text. | Die Veröffentlichungsoperation läuft als diese Person; die Kennzeichnung markiert KI-geschriebene Versionen |
| **Barrierefreiheit.** KI-Alternativtext wird vom Redakteur geprüft; dekorative Bilder sind die Wahl des Redakteurs. | Die Schaltflächen für Alternativtext ([Alternativtext für Bilder](../expai/alternativtext-fuer-bilder.md)) |
| **Übersetzungen.** Maschinelle Übersetzungen werden von jemandem geprüft, der die Sprache kann. | Entwürfe des Übersetzungsabgleichs; `expai/translation_sync` |
| **Kosten.** KI kostet höchstens [Betrag] im Monat, [Betrag] je Benutzer und Tag. | `[Limits]`, `[Budgets] SiteMonthly` ([Kostenkontrolle](kostenkontrolle.md)) |
| **Agenten.** Jeder Agent hat ein eigenes Konto und eine eigene Rolle, freigegeben von [Rolle]; Agenten beginnen nur lesend. | Eigene Konten; API-Schlüssel nur mit dem Geltungsbereich `read` ([Rollen und minimale Rechte](rollen-und-minimale-rechte.md)) |
| **Zugangsdaten.** Schlüssel und Tokens liegen in [Geheimnisspeicher]; nie in Prompts oder Repositorys. | `settings/override`; Schlüsselwarnungen unter EI: Models ([Zugangsdaten für Agenten](../agentische-ablaeufe/zugangsdaten-fuer-agenten.md)) |
| **Aufzeichnungen.** Die KI-Nutzung wird für [Zeitraum] festgehalten; [Rolle] prüft sie [monatlich]. | Der Audit-Kanal `ai`, seine Aufbewahrung; das Nutzungsprotokoll ([Der Audit-Trail für KI](audit-trail-fuer-ki.md)) |
| **Datenschutz.** [Kontakt] beantwortet Fragen zur Verarbeitung. | `[SummarySettings] Contact`, EI: Summary |
| **Stoppen.** [Rolle] darf KI sofort ausschalten, auf allen oder einigen Siteaccesses. | `[AISettings] Enabled`, `DisabledSiteAccesses[]` ([Der Hauptschalter und der Veto-Hook](../expai/hauptschalter-und-veto.md)) |

## Regeln, die die Software nicht durchsetzen kann

Manche Regeln hängen von Menschen ab. Schreiben Sie sie trotzdem auf und schulen Sie sie:

- Fügen Sie kein vertrauliches Material in eine Anfrage ein, die die Datenrichtlinie nicht abdecken würde (zum Beispiel Text aus
  einem anderen System).
- Geben Sie KI-geschriebenen Text nicht als Aussage einer Person aus, wo das wichtig ist (Zitate, gezeichnete Meinungen).
- Prüfen Sie Fakten, Namen, Zahlen und Links in jedem KI-Entwurf ([KI-Entwürfe prüfen](../checklisten/ki-entwuerfe-pruefen.md)).
- Melden Sie seltsames Verhalten (ein Entwurf, der in Inhalten gefundenen Anweisungen folgt, unerwartete Kosten) an [Kontakt].

## Die Richtlinie lebendig halten

- Überarbeiten Sie sie, wenn Sie einen Anbieter, ein Modell oder einen Agenten hinzufügen.
- Vergleichen Sie sie zweimal im Jahr mit EI: Summary; die Zusammenfassung wird aus den laufenden Einstellungen gelesen.
- Bewahren Sie die Versionsgeschichte der Richtlinie mit den Daten der Einstellungsänderungen auf, die sie ausgelöst hat.

## Verwandte Seiten

- [Steuerung von KI und Agenten](README.md)
- [Bevor Sie Exponential Intelligence einschalten](../checklisten/bevor-sie-exponential-intelligence-einschalten.md)
- [Kurs zu Exponential Intelligence für Trainer](../../trainer/exponential-intelligence/README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
