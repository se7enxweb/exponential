---
title: Checkliste zur Inhaltsmigration
audience: adopter, project-manager, developer, editor
category: adoption
order: 44
summary: Eine Checkliste, mit der Anwender Inhalte nach Exponential migrieren - vorher (Bestandsaufnahme, Zuordnung, Sicherungen), bei jeder Probe (messen, Stichproben), bei der Umstellung (Inhaltsstopp, Wartungsmodus, letzter Lauf) und danach (Prüfung, Zeitfenster für den Rückweg).
keywords: Checkliste, Inhaltsmigration, Probe, Bestandsaufnahme, Zuordnung, Sicherung, Inhaltsstopp, Wartungsmodus, Umstellung, Prüfung, Rückweg, checklist, content migration, rehearsal, inventory, mapping, backup, content freeze, maintenance mode, cut-over, verification, rollback
type: help
---
# Checkliste zur Inhaltsmigration

## Inhalt

1. [Vorher](#vorher)
2. [Bei jeder Probe](#bei-jeder-probe)
3. [Umstellung](#umstellung)
4. [Danach](#danach)

## Vorher

```text
[ ] Bestandsaufnahme: Inhaltstypen, Anzahlen, Sprachen, Dateien, Benutzer, Bestellungen
[ ] Entschieden, was NICHT migriert wird
[ ] Zuordnung alt -> neu für Inhaltstypen und Felder aufgeschrieben
[ ] Regeln für URLs entschieden (siehe URLs und Weiterleitungen beim Umzug)
[ ] Sicherung der alten Website angelegt und Wiederherstellung getestet
[ ] Staging-Kopie der neuen Website bereit
```

## Bei jeder Probe

```text
[ ] Vollständiger Lauf auf Staging, jeder Schritt gestoppt
[ ] Anzahlen verglichen: alt gegen neu, pro Inhaltstyp und Sprache
[ ] Stichproben von den Inhaltsverantwortlichen geprüft (Text, Bilder, Verknüpfungen, Daten)
[ ] Suche neu indiziert und ausprobiert
[ ] URL-Aliase für jede Sprache neu aufgebaut; alte Adressen leiten weiter
[ ] Formulare, Shop, Anmeldungen getestet
[ ] Probleme festgehalten und vor der nächsten Probe behoben
```

## Umstellung

```text
[ ] Datum außerhalb der geschäftigsten Zeit; alle informiert
[ ] Inhaltsstopp angekündigt und begonnen
[ ] Wartungsmodus an (siehe Wartungsmodus und geplante Arbeiten)
[ ] Letzter Lauf mit den gemessenen Schritten
[ ] Prüfungen aus der Probe wiederholt
[ ] Wartungsmodus aus; DNS oder Proxy umgestellt
```

## Danach

```text
[ ] Alte Website bis zur Abnahme schreibgeschützt mit Sicherungen behalten
[ ] Plan für den Rückweg im vereinbarten Zeitfenster weiter gültig
[ ] Fehlerlog und 404-Meldungen zwei Wochen lang täglich beobachtet
[ ] Redakteuren gesagt, was sich geändert hat; erste Fragen beantwortet
[ ] Abnahme von den Verantwortlichen unterschrieben
```

Hintergrund: [Zu Exponential wechseln](../../unternehmen/auswahl/zu-exponential-wechseln.md#ein-plan-in-phasen),
[Wartungsmodus und geplante Arbeiten](../../kunden/gesund-halten/wartungsmodus-und-geplante-arbeiten.md),
[Logs für Betreiber](../../kunden/gesund-halten/logs-fuer-betreiber.md).

## Verwandte Seiten

- [Umziehen](README.md)
- [Checkliste zum Start](../starten/checkliste-zum-start.md)
- [Checkliste für den Go-live](../../unternehmen/vorlagen/checkliste-go-live.md)
- [Qualitätsschranken und Abnahme](../../fuehrung/qualitaetsschranken-und-abnahme.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
