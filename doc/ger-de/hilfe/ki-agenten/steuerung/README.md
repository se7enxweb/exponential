---
title: Steuerung von KI und Agenten
audience: ai-agents, admin, project-manager, businesses
category: ai-agents
order: 80
summary: Der Teil des Abschnitts zu KI, Agenten und agentischem Arbeiten für die Menschen, die die Regeln festlegen - Datenschutz und Daten, die die Website verlassen, Kostenkontrolle, der Audit-Trail für KI, Rollen und minimale Rechte für Assistenten und Agenten und eine Vorlage für eine Richtlinie zur KI-Nutzung.
keywords: Steuerung, Governance, KI-Richtlinie, Datenschutz, Kostenkontrolle, Budgets, Audit-Trail, Rollen, minimale Rechte, Verantwortlichkeit, Datenschutzbeauftragter, Compliance, governance, AI policy, privacy, data protection, cost control, audit trail, roles, least privilege, accountability, data protection officer
type: help
---
# Steuerung von KI und Agenten

Dieser Teil richtet sich an Führungskräfte, Administratoren und Datenschutzbeauftragte, die entscheiden, wie KI auf einer
Exponential-Installation verwendet wird, und die danach dafür einstehen müssen. Er erklärt die Kontrollen, die Ihnen die
Software gibt, und wie Sie daraus Regeln machen, denen Ihre Organisation folgen kann.

## Inhalt

1. [Jede Seite dieses Teils](#jede-seite-dieses-teils)
2. [Die Fragen, die Steuerung beantworten muss](#die-fragen-die-steuerung-beantworten-muss)

## Jede Seite dieses Teils

| Seite | Was sie beantwortet |
|---|---|
| [Datenschutz und Daten, die die Website verlassen](datenschutz-und-daten-die-die-website-verlassen.md) | Was an einen KI-Anbieter geht, wie die Datenrichtlinie es begrenzt, was behalten wird, und die Seite EI: Summary |
| [Kostenkontrolle](kostenkontrolle.md) | Geschätzte Kosten je Anfrage, Grenzen, Budgets für eingereihte Arbeit, Lanes und Stapel |
| [Der Audit-Trail für KI](audit-trail-fuer-ki.md) | Der Audit-Kanal `ai`, Ereignisse zu API-Schlüsseln und Tokens und wie Sie einem Prüfer genau das geben |
| [Rollen und minimale Rechte](rollen-und-minimale-rechte.md) | Konten und Rollen für Assistenten und Agenten und wie Sie sie prüfen |
| [Eine Richtlinie zur KI-Nutzung für Ihre Organisation](eine-richtlinie-zur-ki-nutzung.md) | Eine anzupassende Vorlage mit den Einstellungen, die jede Regel durchsetzen |
| [Reaktion auf KI-Vorfälle](reaktion-auf-ki-vorfaelle.md) | Was Sie zuerst stoppen, wo Sie herausfinden, was geschehen ist, und wie Sie sich erholen |

## Die Fragen, die Steuerung beantworten muss

| Frage | Woher die Antwort kommt |
|---|---|
| Wer darf KI verwenden, und wofür? | Rollen mit den Richtlinien `expai/*` ([Berechtigungen für KI-Funktionen](../expai/berechtigungen-fuer-ki-funktionen.md)) |
| Welche Inhalte dürfen an welchen Anbieter gehen? | Die Datenrichtlinie ([Datenschutz und Daten, die die Website verlassen](datenschutz-und-daten-die-die-website-verlassen.md)) |
| Wer gibt KI-geschriebene Inhalte frei? | Ein Freigabe-Workflow ([Entwürfe und menschliche Prüfung](../expai/entwuerfe-und-menschliche-pruefung.md)) |
| Wie viel darf es kosten? | Grenzen und Budgets ([Kostenkontrolle](kostenkontrolle.md)) |
| Was ist geschehen, und wer hat es getan? | Der Audit-Trail ([Der Audit-Trail für KI](audit-trail-fuer-ki.md)) |
| Welche Agenten dürfen auf dem System handeln, als wer? | Konten und Zugangsdaten ([Rollen und minimale Rechte](rollen-und-minimale-rechte.md)) |
| Wie stoppen wir es? | Der Hauptschalter und der Veto-Hook ([Der Hauptschalter und der Veto-Hook](../expai/hauptschalter-und-veto.md)) |

Die Software hält fest und setzt durch; sie bewertet nicht. Ob die Bedingungen eines Anbieters Ihre rechtlichen Pflichten
erfüllen, entscheiden Ihre Organisation und ihre Berater.

## Verwandte Seiten

- [KI, Agenten und agentisches Arbeiten: hier beginnen](../ki-und-agenten-hier-beginnen.md)
- [Sicherheitsgrundlagen für Unternehmen](../../unternehmen/sicherheit/sicherheitsgrundlagen-fuer-unternehmen.md)
- [DSGVO-Grundlagen](../../unternehmen/sicherheit/dsgvo-grundlagen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
