---
title: Aktivitäten von Auftragnehmern und das Audit-Protokoll
audience: government, admin
category: public-sector
order: 80
summary: Wie Sie in Exponential sehen, was externe Benutzer getan haben - Anmeldungen, verweigerte Aufrufe, Inhaltsänderungen und, falls eingeschaltet, Downloads, gefiltert nach Login oder Benutzer in der Audit-Konsole oder mit exp:audit, und wie Sie es für eine Vertragsüberprüfung oder einen Vorfall exportieren.
keywords: Audit-Protokoll, Aktivitäten von Auftragnehmern, externe Benutzer, Anmeldeeinträge, Kanal access, Kanal content, Kanal read, Downloads, Export, Vorfall, audit trail, contractor activity, external users, sign-in records, access channel, content channel, read channel, downloads, exp:audit, export, incident
type: help
---
# Aktivitäten von Auftragnehmern und das Audit-Protokoll

Diese Seite richtet sich an Administratoren und Sicherheitsbeauftragte. Das Audit-Protokoll zeichnet
sicherheitsrelevante Ereignisse auf, voreingestellt eingeschaltet, in einem manipulationssicheren Protokoll
([Audit-Protokoll](../../../../../eng-us/features/6.0/audit-trail.md)).

## Inhalt

1. [Was für externe Benutzer aufgezeichnet wird](#was-für-externe-benutzer-aufgezeichnet-wird)
2. [Die Aktivitäten einer Person finden](#die-aktivitäten-einer-person-finden)
3. [Downloads](#downloads)
4. [Exportieren](#exportieren)

## Was für externe Benutzer aufgezeichnet wird

| Kanal | Beispiele |
|---|---|
| `access` | Anmeldung, fehlgeschlagene Anmeldung, verweigerter Aufruf, Kontosperre, Rollenzuweisung, Zurücksetzen des Passworts |
| `content` | Veröffentlichen, Verschieben, Entfernen, Verstecken, Versionen, für Inhalte, die sie bearbeiten oder hochladen |
| `read` | stichprobenartige Aufrufe von Knoten, Suchen und Downloads, nur wenn Lesezugriffe eingeschaltet sind |

## Die Aktivitäten einer Person finden

Filtern Sie unter **Audit > Konsole** nach **Login** oder **Benutzer-ID**, **Kanal** und Daten. Auf der
Kommandozeile:

```bash
./console exp:audit search --login=<login> --from=2026-10-01 --to=2026-10-31
./console exp:audit search --user=<user id> --channel=access
```

## Downloads

Downloads werden nur aufgezeichnet, wenn der Kanal `read` eingeschaltet ist, und dann stichprobenartig; seine
voreingestellte Aufbewahrung ist kürzer (30 Tage aktiv, 90 archiviert). Müssen Sie jeden Download einer vertraulichen
Extranet-Datei kennen, schalten Sie für diesen Zeitraum Lesezugriffe ein und prüfen Sie vorher die Einstellungen in der
Anleitung zum Audit ([die Anleitung zum Audit](../../../../bc/6.0/audit.md)).

## Exportieren

`./console exp:audit export` mit denselben Filtern schreibt CSV oder JSON-Zeilen; die Exportseite der Konsole bietet
**Jetzt herunterladen** und Exporte im Hintergrund. Prüfen Sie die Kette, bevor Sie Einträge herausgeben:
`./console exp:audit verify`. Exporte enthalten personenbezogene Daten; behandeln Sie sie nach Ihren Regeln.

## Verwandte Seiten

- [Der Audit-Trail für wer was geändert hat](../../transparenz-und-informationsfreiheit/der-audit-trail-fuer-wer-was-geaendert-hat.md)
- [Den Zugang beenden](den-zugang-beenden.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
