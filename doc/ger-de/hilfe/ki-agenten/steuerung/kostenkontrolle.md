---
title: Kostenkontrolle
audience: ai-agents, admin, businesses, project-manager
category: ai-agents
order: 82
summary: Wie Exponential Intelligence KI-Kosten sichtbar und gedeckelt hält - geschätzte Kosten für jede Anfrage aus einer Preistabelle (oder die vom Anbieter gemeldeten Kosten), Dashboard und Protokoll, Grenzen je Gespräch, Session, Benutzer, Tag, Monat, Website und Anbieter, Budgets mit Reservierungen für eingereihte Arbeit, Lanes und Stapel für günstigere Massenarbeit und die Kosten von Agenten außerhalb davon.
keywords: Kostenkontrolle, KI-Kosten, Tokens, Preistabelle, geschätzte Kosten, Grenzen, MaxCostPerUserPerDay, MaxCostPerSitePerMonth, ProviderMaxCostPerMonth, Budgets, SiteMonthly, angehaltene Jobs, Lanes, Batch, Flex, Prompt-Caching, Dashboard, cost control, AI cost, price table, estimated cost, limits, held jobs, lanes, prompt caching
type: help
---
# Kostenkontrolle

Diese Seite richtet sich an Administratoren und Führungskräfte, die für die KI-Nutzung bezahlen. Gehostete Anbieter rechnen je
Anfrage ab, nach den gesendeten und empfangenen Tokens. Exponential Intelligence schätzt die Kosten jeder Anfrage, zeigt sie an und
stoppt Anfragen an den Grenzen, die Sie setzen. Ein lokales Modell kostet keine Gebühren, aber Serverkapazität.

## Inhalt

1. [Was eine Anfrage kostet](#was-eine-anfrage-kostet)
2. [Wo Sie es sehen](#wo-sie-es-sehen)
3. [Grenzen](#grenzen)
4. [Budgets für eingereihte Arbeit](#budgets-für-eingereihte-arbeit)
5. [Lanes und Stapel](#lanes-und-stapel)
6. [Gewohnheiten, die Geld sparen](#gewohnheiten-die-geld-sparen)
7. [Agenten außerhalb des Assistenten](#agenten-außerhalb-des-assistenten)

## Was eine Anfrage kostet

Die Kosten einer Anfrage sind, in dieser Reihenfolge: die vom Anbieter gemeldeten Kosten (manche Anbieter melden sie mit jeder
Antwort), die Preise ihres Modells in den Einstellungen (`InputPerMTok`, `OutputPerMTok` und die Cache-Preise, je Million Tokens)
oder eine Preistabelle nach Modell-ID. Ein Modell eines anderen Anbieters ohne Preise hat **unbekannte** Kosten statt geschätzter;
ein lokales Modell ist kostenlos. Preise in einer anderen Währung werden mit dem EZB-Referenzkurs des Tages umgerechnet.

Das sind **Schätzungen** aus Listenpreisen. Maßgeblich ist die Rechnung des Anbieters.

Jedes Bearbeitungsformular zeigt eine Zeile "Defaults in use" mit Modell, Region und geschätzten Kosten eines Klicks, und der
Hinweis zum Alternativtext nennt die Kosten jedes Vorschlags.

## Wo Sie es sehen

| Ort | Zeigt | Braucht |
|---|---|---|
| Dashboard > Exponential Intelligence | Geschätzte Kosten, Tokens, Anfragen und Entwürfe mit Trends, ein Diagramm je Tag, die Grenzen | `expai/write` (eigene Nutzung); `expai/log` für die ganze Website und die Benutzer mit der höchsten Nutzung |
| EI: Log (`expai/log`) | Jede Anfrage mit Modell, Tokens, Kosten, Zeit, Werkzeugen und Fehlern, filterbar | `expai/write` (eigene), `expai/log` (alle) |
| EI: Budgets (`expai/budgets`) | Das Budget der Website, Job-Gruppen, Lanes, Stapel, angehaltene Jobs | `expai/log` |
| Audit > Dashboard | Anfragen und die geschätzten Kosten von 7 Tagen | `audit/read` auf den Kanal `ai` |
| EI: Summary | Anfragen, Tokens und Kosten jedes Anbieters in diesem Monat, jede gesetzte Grenze | `expai/summary` |

## Grenzen

Setzen Sie beliebige davon in `[Limits]` Ihrer expai-Einstellungen; leer bedeutet keine Grenze:

| Grenze | Wird zurückgesetzt |
|---|---|
| `MaxTokensPerConversation` | Mit "New conversation" |
| `MaxTokensPerSession` | Wenn sich der Benutzer neu anmeldet |
| `MaxTokensPerUserPerDay`, `MaxCostPerUserPerDay` | Um Mitternacht |
| `MaxTokensPerUserPerMonth`, `MaxCostPerUserPerMonth` | Am 1. |
| `MaxTokensPerSitePerMonth`, `MaxCostPerSitePerMonth` | Am 1., alle Benutzer zusammen |
| `MaxRequestsPerTurn` | Die meisten Modellaufrufe, zu denen eine Nachricht führen darf |
| `ProviderMaxTokensPerMonth[<provider>]`, `ProviderMaxCostPerMonth[<provider>]` | Am 1.; nur Anfragen an diesen Anbieter werden gestoppt |

Eine von einer Grenze gestoppte Anfrage wird nicht gesendet; der Redakteur sieht die Grenze und wann sie zurückgesetzt wird.
Solange eine Grenze gesetzt ist, reserviert jede Anfrage ihre Schätzung, bevor sie gesendet wird, und rechnet danach mit den echten
Zahlen ab, sodass parallele Anfragen sie nicht gemeinsam überschreiten können.

## Budgets für eingereihte Arbeit

Eingereihte Arbeit (Ketten, Übersetzungsabgleich, Berichte zum Alternativtext, Hintergrundläufe von Vector) gibt Geld aus, während
niemand zusieht. Zwei Budgets halten sie in Grenzen
([Dokumentation der Erweiterung: Budgets](https://github.com/se7enxweb/expai/blob/main/doc/budgets.md)):

| Budget | Gesetzt in | Zählt |
|---|---|---|
| Monatliches Budget der Website | `[Budgets] SiteMonthly` (sonst `[Limits] MaxCostPerSitePerMonth`) | Jede Anfrage des Monats plus die Reservierungen eingereihter Anfragen |
| Budget einer Job-Gruppe | Je Job-Gruppe oder `[Budgets] DefaultGroupBudget` | Die abgerechneten Kosten der Gruppe plus ihre Reservierungen |

Ein eingereihter Job reserviert seine Schätzung mal einem Sicherheitsfaktor. Passt die Reservierung nicht, wird der Job
**angehalten**, mit einer Meldung, die das Budget und die Zahlen nennt, und direkt vor seinem Aufruf erneut geprüft. Angehaltene
Jobs laufen weiter, wenn sie wieder passen: über den Cron-Teil `expai`, `./console ext:expai:queue resume` oder die Schaltfläche
"Resume held jobs" unter EI: Budgets (Richtlinie `expai/settings`). Ein neuer Monat oder ein erhöhtes Budget gibt das Geld frei.

## Lanes und Stapel

Eine Lane ist eine Art, eine Anfrage an einen Anbieter zu senden. Die Erweiterung hält je Anbieter fest, was sie in seiner
Dokumentation gefunden hat:

| Lane | Preis, typischerweise | Geschwindigkeit |
|---|---|---|
| `standard` | Listenpreis | Sekunden |
| `flex` | Etwa die Hälfte, wo angeboten | Minuten; kann abgelehnt werden, wenn der Anbieter ausgelastet ist |
| `batch` | Etwa die Hälfte | Innerhalb von Stunden (bei den aufgeführten Anbietern höchstens 24 Stunden) |
| `fast` | Ein Aufschlag | Schneller; braucht eine eigene Richtlinie `expai/lane_fast` |

Das Panel und die Feldaktionen verwenden immer die Standard-Lane. Eingereihte Anfragen nehmen die Lane, die die Lane-Richtlinie
wählt, sodass Massenarbeit über eine Batch-Lane gehen kann. **Die Erweiterung verwendet nie eine Lane, deren Preis sie raten
müsste**: Eine Lane ohne bestätigten Preis wird ausgeschaltet ausgeliefert. Der Planmodus in Vector zeigt vor Beginn der
Massenarbeit eine Kostenschätzung.

## Gewohnheiten, die Geld sparen

- Setzen Sie vom ersten Tag an ein monatliches Budget der Website und eine tägliche Grenze je Benutzer; erhöhen Sie sie, wenn Sie
  die tatsächliche Nutzung kennen.
- Verwenden Sie ein kleineres Modell als Standard der Website für kurze Feldaktionen und erlauben Sie ein größeres nur dort, wo es
  hilft.
- Verwenden Sie Planmodus und Batch-Lane für Massenarbeit.
- Lassen Sie Prompt-Caching eingeschaltet (`[AISettings] PromptCaching`, standardmäßig eingeschaltet): Es macht wiederholten
  Kontext günstiger.
- Prüfen Sie monatlich das Dashboard sowie die Benutzer und Modelle mit der höchsten Nutzung.

## Agenten außerhalb des Assistenten

Die REST-API und expservices kosten selbst nichts je Aufruf; der eigene Anbieter des Agenten rechnet die Nutzung des Agenten ab.
Grenzen dafür liegen in der Agentenplattform, nicht in Exponential. Lassen Sie einen Agenten Listen mit kleinen Grenzen
seitenweise abrufen (REST `/limit/<n>`, das Argument `limit` der Listen von expservices), damit er keine großen Antworten in seinen
Kontext zieht.

## Verwandte Seiten

- [Anbieter und Modelle](../expai/anbieter-und-modelle.md)
- [Ketten und eingereihte Arbeit](../expai/ketten-und-eingereihte-arbeit.md)
- [Gesamtbetriebskosten](../../unternehmen/kosten/gesamtbetriebskosten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
