---
title: Datenschutz-Folgenabschätzung für eine Website
audience: government, project-manager
category: public-sector
order: 290
summary: Für öffentliche Stellen, die entscheiden, ob und wie sie die Datenschutzrisiken einer mit Exponential gebauten Website bewerten - wann eine DSFA üblicherweise in Betracht kommt, welche Fakten das Produkt für die Beschreibung und die Risikoanalyse liefert, und welche Maßnahmen zur Verringerung der Risiken verfügbar sind.
keywords: DSFA, Datenschutz-Folgenabschätzung, DSGVO Artikel 35, Risikobewertung, Datenschutzrisiken, Maßnahmen, Beschreibung der Verarbeitung, Erforderlichkeit, Verhältnismäßigkeit, öffentlicher Sektor, Website, Mitgliederbereich, KI, DPIA, data protection impact assessment, GDPR, AI
type: help
---
# Datenschutz-Folgenabschätzung für eine Website

Diese Seite richtet sich an Datenschutzbeauftragte und Projektleitungen. Eine DSFA (Art. 35 DSGVO) ist erforderlich,
wo eine Verarbeitung voraussichtlich ein hohes Risiko zur Folge hat; die Listen Ihrer Aufsichtsbehörde sagen, wann.
Eine reine Informationswebsite braucht oft keine; eine Website mit Bürgerkonten, sensiblen Formularen, Profilbildung
oder KI-Funktionen möglicherweise schon. Exponential entscheidet das nicht für Sie; es liefert Fakten und Maßnahmen.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Wann sie üblicherweise in Betracht kommt](#wann-sie-üblicherweise-in-betracht-kommt)
2. [Die Verarbeitung beschreiben](#die-verarbeitung-beschreiben)
3. [Risiken und Maßnahmen](#risiken-und-maßnahmen)
4. [Verwandte Seiten](#verwandte-seiten)

## Wann sie üblicherweise in Betracht kommt

- Konten für Bürgerinnen und Bürger mit persönlichen Profilen oder Mitgliederbereiche mit sensiblen Themen.
- Formulare, die besondere Kategorien von Daten (Gesundheit, soziale Lage) oder Daten über Kinder erheben.
- Überwachung von Beschäftigten über das Prüfprotokoll über Sicherheitszwecke hinaus.
- Webanalyse oder Tracking über Dienste hinweg.
- KI-Verarbeitung von Inhalten oder Bildern, die personenbezogene Daten enthalten (die optionale Erweiterung
  Exponential Intelligence sendet Inhalte nach ihrer Datenrichtlinie an Anbieter).

## Die Verarbeitung beschreiben

Nutzen Sie die Seiten dieses Bereichs:

| Teil der DSFA | Quelle |
|---|---|
| Daten und wo sie aufbewahrt werden | „Wo personenbezogene Daten in Exponential liegen“ |
| Aufbewahrung | „Aufbewahrung personenbezogener Daten im Überblick“ |
| Empfänger | „Verantwortlicher, Auftragsverarbeiter und die Software“, „Erweiterungen von Drittanbietern und Datenschutz“ |
| Rechte der betroffenen Personen | „Das Auskunftsrecht“, „Das Recht auf Löschung“, „Einschränkung und ‚Deaktivieren und verschieben‘“ |
| Technische Maßnahmen | der Sicherheitsbereich dieser Hilfe |

## Risiken und Maßnahmen

| Risiko | In Exponential verfügbare Maßnahmen |
|---|---|
| Unbefugter Zugriff auf Konten | zweistufige Anmeldung, Passwortrichtlinie, Kontosperre, Netzwerkbeschränkung der Administration |
| Übermäßiger Zugriff durch Beschäftigte | Rollen begrenzt auf Sektion oder Teilbaum; `infocollector/read`, `audit/read`, `mailpreferences/export` nur an wenige vergeben |
| Zu lange aufbewahrte Daten | Aufbewahrungseinstellungen für Audit, Protokolle und Einwilligungen; Routinen zum Entfernen bei Formularen |
| Datenabfluss über Protokolle | Maskierung in Anfrageprotokollen und im Protokollbetrachter |
| Daten an Drittanbieter | Erweiterungen standardmäßig inaktiv; CSP beschränkt Hosts; Einwilligung vor dem Laden |
| Unentdeckter Missbrauch | Prüfprotokoll standardmäßig an, Warnregeln, Weiterleitung an ein SIEM |
| Versehentliche Veröffentlichung | Versionen, Vorschau, Freigabe-Workflows, Papierkorb ([Workflows](../../../anleitungen/workflows.md)) |

## Verwandte Seiten

- [Die DSGVO und Exponential](dsgvo-zuordnung.md)
- [Checkliste Datenschutz durch Technikgestaltung](checkliste-datenschutz-durch-technikgestaltung.md)
- [Datenschutzstandards](../../../standards/datenschutz.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
