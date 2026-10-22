---
title: Bevor Sie Exponential Intelligence einschalten
audience: ai-agents, admin, project-manager
category: ai-agents
order: 91
summary: Eine Checkliste für Administratoren, bevor Redakteure den Schreibassistenten Exponential Intelligence auf einer Live-Installation erhalten - Entscheidungen, Anbieter und Schlüssel, Datenrichtlinie, Rollen, Prüfung und Freigabe, Kosten, die Job-Warteschlange, Aufzeichnungen, Tests und der Weg zurück.
keywords: Checkliste, KI einschalten, Go-live, expai-Einrichtung, Anbieterschlüssel, Datenrichtlinie, Rollen, Freigabe-Workflow, Grenzen, Budgets, Cron-Teile, Audit, Testredakteur, Hauptschalter, checklist, switch on AI, go live, expai setup, provider key, data policy, roles, approval workflow, limits, budgets, cron parts, test editor, master switch
type: help
---
# Bevor Sie Exponential Intelligence einschalten

Diese Checkliste richtet sich an Administratoren, die Redakteure gleich den Assistenten auf einer Live-Installation nutzen lassen.
Arbeiten Sie sie der Reihe nach durch; jeder Punkt verlinkt auf die Seite, die ihn erklärt.

## Inhalt

1. [Entscheidungen](#entscheidungen)
2. [Anbieter und Schlüssel](#anbieter-und-schlüssel)
3. [Datenrichtlinie](#datenrichtlinie)
4. [Rollen](#rollen)
5. [Prüfung und Freigabe](#prüfung-und-freigabe)
6. [Kosten und die Warteschlange](#kosten-und-die-warteschlange)
7. [Aufzeichnungen und Tests](#aufzeichnungen-und-tests)
8. [Der Weg zurück](#der-weg-zurück)

## Entscheidungen

- [ ] Wer ihn verwendet, wofür und in welchen Sektionen ([Eine Richtlinie zur KI-Nutzung](../steuerung/eine-richtlinie-zur-ki-nutzung.md)).
- [ ] Welche Inhalte nie an einen externen Anbieter gehen dürfen.
- [ ] Wer Fragen zur Verarbeitung beantwortet.

## Anbieter und Schlüssel

- [ ] Anbieter gewählt; seine Bedingungen gelesen; `Region`, `Retention`, `TermsCheckedAt` gesetzt
  ([Einen Anbieter konfigurieren](../expai/einen-anbieter-konfigurieren.md)).
- [ ] Schlüssel nur in `settings/override/expai.ini.append.php`, Modus 0640; in keiner committeten Datei.
- [ ] Setup > EI: Models zeigt "key present" und keine Warnung zu Ablageorten von Schlüsseln.
- [ ] Testprompt des Standardmodells beantwortet.
- [ ] Jedes Modell, das Entwürfe schreiben soll, hat die Probe bestanden oder gilt als vertrauenswürdig
  ([Das Schreibtor und die Probe](../expai/das-schreibtor-und-die-probe.md)).

## Datenrichtlinie

- [ ] Regeln in `[DataPolicy]` für sensible Sektionen und Klassen gesetzt
  ([Datenschutz und Daten, die die Website verlassen](../steuerung/datenschutz-und-daten-die-die-website-verlassen.md)).
- [ ] Veto-Regeln für Inhalte, die nie gesendet werden dürfen ([Der Hauptschalter und der Veto-Hook](../expai/hauptschalter-und-veto.md)).
- [ ] Bilder: bei Bedarf strengere Regeln für Bilder gesetzt.
- [ ] `[UsageSettings] LogRequests` ausgeschaltet gelassen.

## Rollen

- [ ] `expai/write` und `ezjscore/call` nur der Rolle der vorgesehenen Redakteure gegeben
  ([Berechtigungen für KI-Funktionen](../expai/berechtigungen-fuer-ki-funktionen.md)).
- [ ] `expai/vector`, `expai/chain`, `expai/web`, `expai/services` nur dort gegeben, wo nötig, mit Einschränkungen.
- [ ] `expai/summary` dem Datenschutzbeauftragten gegeben; `audit/read` auf den Kanal `ai` bei Bedarf einem Prüfer.

## Prüfung und Freigabe

- [ ] Freigabe-Workflow auf `content / publish / before` für die Sektionen, die ihn brauchen; mit einem Testredakteur getestet
  ([Entwürfe und menschliche Prüfung](../expai/entwuerfe-und-menschliche-pruefung.md)).
- [ ] Redakteure wissen, dass "Send for approval" sofort veröffentlicht, wo keine Freigabe greift.
- [ ] Redakteure haben die [Prüfcheckliste](ki-entwuerfe-pruefen.md).

## Kosten und die Warteschlange

- [ ] Monatliches Budget der Website und tägliche Grenze je Benutzer gesetzt ([Kostenkontrolle](../steuerung/kostenkontrolle.md)).
- [ ] Cron-Teile `expqueue` (jede Minute) und `expai` (alle fünf Minuten) laufen, wenn eingereihte Arbeit verwendet wird
  ([Ketten und eingereihte Arbeit](../expai/ketten-und-eingereihte-arbeit.md)).

## Aufzeichnungen und Tests

- [ ] Der Audit-Kanal `ai` wird geschrieben; `./console exp:audit tail --channel=ai` zeigt eine Testanfrage
  ([Der Audit-Trail für KI](../steuerung/audit-trail-fuer-ki.md)).
- [ ] Als Testredakteur angemeldet: Der Reiter Intelligence erscheint, ein Entwurf wird geschrieben, nichts wird von selbst
  veröffentlicht.
- [ ] EI: Summary ausgedruckt und mit der Richtlinie abgelegt.

## Der Weg zurück

- [ ] Alle, die es brauchen könnten, wissen, wie man ihn ausschaltet: `[AISettings] Enabled` oder `DisabledSiteAccesses[]`.

## Verwandte Seiten

- [Checklisten zu KI und Agenten](README.md)
- [Exponential Intelligence für KI-Nutzer](../expai/README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
