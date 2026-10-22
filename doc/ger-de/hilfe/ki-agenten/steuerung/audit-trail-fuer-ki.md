---
title: Der Audit-Trail für KI
audience: ai-agents, admin, businesses, government
category: ai-agents
order: 83
summary: Wie die KI-Nutzung im manipulationssicheren Audit-Trail von Exponential festgehalten wird - der Kanal ai von Exponential Intelligence mit seinen Ereignissen zu Anfragen, Ablehnungen, Entwürfen, Ketten, Modellen und Budgets (Aktionen und Metadaten, nie Prompts, Antworten oder Schlüssel), die Aufzeichnungen dessen, was Agentenkonten über REST und expservices tun, Ereignisse zu API-Schlüsseln und Tokens und wie Sie einem Prüfer genau den KI-Kanal geben.
keywords: Audit-Trail, KI-Audit, Kanal ai, system.expai.request, content.expai_draft, Dienstereignisse, access.apikey, access.expservices.token, exp:audit, Prüferrolle, Einschränkung Channel, Hash-Kette, SIEM, CSV-Export, audit trail, AI audit, channel ai, service events, auditor role, Channel limitation, hash chain, export CSV
type: help
---
# Der Audit-Trail für KI

Diese Seite richtet sich an Administratoren, Prüfer und Datenschutzbeauftragte, die zeigen müssen, was KI und Agenten auf einer
Installation getan haben. Exponential hat einen manipulationssicheren Audit-Trail: kanonische JSON-Zeilen mit einer Hash-Kette, die
sich prüfen, archivieren, exportieren und weiterleiten lassen ([Der Audit-Trail](../../../funktionen/6.0/pruefprotokoll.md)). Die
KI-Nutzung wird dort festgehalten, als Aktionen und Metadaten.

## Inhalt

1. [Was nie festgehalten wird](#was-nie-festgehalten-wird)
2. [Exponential Intelligence: der Kanal ai](#exponential-intelligence-der-kanal-ai)
3. [Was Agentenkonten tun](#was-agentenkonten-tun)
4. [Schlüssel und Tokens von Agenten](#schlüssel-und-tokens-von-agenten)
5. [Ihn lesen](#ihn-lesen)
6. [Ein Prüfer nur für KI](#ein-prüfer-nur-für-ki)

## Was nie festgehalten wird

Der Audit-Trail hält fest, wer was wann mit welchem Ergebnis getan hat. Für den Assistenten enthält er **nie** einen Prompt, eine
Antwort, den Text eines Entwurfs, den Fehlertext eines Anbieters oder einen Schlüssel. Personenbezogene Daten über die Benutzer-ID
hinaus folgen den Datenschutzeinstellungen des Kernel-Audits.

## Exponential Intelligence: der Kanal ai

Aus der [Audit-Seite](https://github.com/se7enxweb/expai/blob/main/doc/audit-trail.md) der Erweiterung, geschrieben in den Kanal
`ai` (`var/log/audit/ai-<day>.jsonl`):

| Ereignis | Wann |
|---|---|
| `system.expai.request` | Jede an ein Modell gesendete Anfrage: Anbieter, Modell, Region, Aufbewahrung, Zweck, Lane, Tokenzahlen, Latenz, Status, geschätzte Kosten |
| `system.expai.request.failed` | Die Anfrage ist gescheitert oder das Modell hat abgelehnt |
| `system.expai.request.refused` | Das Veto hat sie abgelehnt, oder der Hauptschalter ist aus |
| `content.expai_draft.write` | Eine vom Assistenten geschriebene Version, aus beliebiger Quelle |
| `content.expai_draft.keep`, `.submit`, `.publish`, `.discard`, `.reopen` | Der Zustand des Entwurfs hat sich geändert |
| `content.expai_chain.start`, `.approve`, `.changes`, `.stop`, `.resume` | Ein Redakteur hat an einer Kette gehandelt |
| `system.expai.model.action` | Ein lokales Modell geladen oder entladen, ein Modelltest eingereiht |
| `system.expai.budget.hold`, `.resume` | Ein eingereihter Job von einem Budget angehalten oder fortgesetzt |

Änderungen an den Einstellungen des Assistenten (der Hauptschalter, die Datenrichtlinie), geschrieben über `exp:ini` oder die
Einstellungsansichten, hält der Kernel als `system.setting.write` fest; Rollenänderungen als `access.role.*`.

Der Umfang beträgt etwa eine Zeile von 1 KB je Anfrage und je Entwurf. Um nur Fehler, Ablehnungen und Entwürfe festzuhalten,
schalten Sie `system.expai.request` in Ihren Audit-Einstellungen aus.

## Was Agentenkonten tun

Ein Agent, der über die REST-API oder expservices arbeitet, handelt als sein eigenes Konto, seine Aktionen werden also wie die jedes
Benutzers festgehalten:

- Jeder Schreibzugriff über expservices hält `service.<domain>.<method>` im Audit-Trail fest
  ([Entfernte Dienste und Apps](../../../anleitungen/entfernte-dienste-und-apps.md));
- über den Kernel vorgenommene Inhaltsänderungen werden im Inhaltskanal unter dem Konto festgehalten, das sie vorgenommen hat.

Deshalb sollte jeder Agent ein eigenes Konto haben: Der Audit-Trail beantwortet dann die Frage "Was hat der Agent getan?" direkt
([Rollen und minimale Rechte](rollen-und-minimale-rechte.md)).

## Schlüssel und Tokens von Agenten

| Ereignis | Hält fest |
|---|---|
| `access.apikey.*` | Erstellen, Widerrufen, erste Verwendung und Ablehnungen persönlicher API-Schlüssel |
| `access.expservices.token.create`, `.revoke`, `.failed` | Tokens von expservices |

## Ihn lesen

```bash
./console exp:audit tail --channel=ai --allow-root-user
./console exp:audit search --name=system.expai.request.failed --allow-root-user
./console exp:audit verify --channel=ai --allow-root-user
./console exp:audit tail --channel=access --name='access.expservices.*' --lines=5 --allow-root-user
```

In der Administration hat die Audit-Konsole bei aktivem Assistenten eine Quelle **Exponential Intelligence**: ein Dashboard-Feld
(Anfragen, Fehler, Ablehnungen, Entwürfe, geschätzte Kosten von 7 Tagen, Benutzer und Modelle mit der höchsten Nutzung), ein
Diagramm je Tag, eine Karte je Ereignis mit Links zur Anfrage im EI: Log und eine Alarmregel für viele gescheiterte Anfragen an ein
Modell in kurzer Zeit. Der CSV-Export des Kanals `ai` fügt Spalten wie Anbieter, Modell, Region, Zweck, Tokens und Kosten hinzu.

## Ein Prüfer nur für KI

Die Einschränkung `Channel` von `audit/read` bietet "Exponential Intelligence" (`ai`) an. Eine Rolle mit `audit/read`,
beschränkt auf diesen Kanal, sieht die KI-Ereignisse und nichts von den Kanälen für Inhalte, Zugriff oder System. Fügen Sie
`expai/log` hinzu, wenn der Prüfer auch das Nutzungsprotokoll lesen soll.

## Verwandte Seiten

- [Sicherheit und Audit](../../../anleitungen/sicherheit-und-audit.md)
- [Kostenkontrolle](kostenkontrolle.md)
- [Reaktion auf KI-Vorfälle](reaktion-auf-ki-vorfaelle.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
