---
title: Audit-Einträge für einen Prüfer exportieren
audience: government, admin, project-manager
category: public-sector
order: 200
summary: Schritt für Schritt - Einträge des Audit-Protokolls für einen internen oder externen Prüfer als CSV, JSON Lines oder JSON über Audit > Exportieren oder mit exp:audit export exportieren, mit beigefügtem Ergebnis der Verifikation und den Export als personenbezogene Daten behandeln.
keywords: Audit-Export, Prüfer, Beweismittel, CSV, JSON, exp:audit export, Audit Exportieren, Jetzt herunterladen, Im Hintergrund exportieren, Verifikation, personenbezogene Daten, audit export, auditor, evidence, Download now, Export in the background, verification, personal data
type: help
---
# Audit-Einträge für einen Prüfer exportieren

Diese Aufgabe ist für Administratoren und Verantwortliche für das Audit, die einem Prüfer, einem Ermittler oder einem
Verantwortlichen für das Schriftgut Nachweise übergeben. Am Ende hat der Prüfer die angefragten Einträge, mit dem
Nachweis, dass das Protokoll unversehrt war.

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [In der Administration](#in-der-administration)
3. [In der Shell](#in-der-shell)
4. [Das Nachweispaket](#das-nachweispaket)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Die Anfrage: Zeitraum, Kanäle (`access`, `content`, `system`, `commerce`), Benutzer oder Objekte, Format.
- `audit/read` für die Kanäle (die Seite **Exportieren**) oder Shell-Zugang.

## In der Administration

1. Reiter **Audit**, **Konsole**: Setzen Sie den Filter (Kanal, Namen der Ereignisse, Benutzer, Zeitraum, Text).
2. **Exportieren** (`/audit/export`): der Filter der Konsole als CSV, JSON Lines oder JSON.
3. **Jetzt herunterladen** streamt die Datei sofort; **Im Hintergrund exportieren** führt für einen großen Filter einen
   Inhaltsauftrag aus, und die Datei wird nach Abschluss auf der Exportseite angeboten, nur dem Benutzer, der sie
   angefordert hat.

## In der Shell

```bash
./console exp:audit export --channel=access --from=2026-09-01 --to=2026-10-01 --format=csv --out=var/tmp/evidence/access.csv --allow-root-user
./console exp:audit export --name='access.*' --from=2026-09-01 --format=bundle --out=var/tmp/evidence/bundle --allow-root-user
./console exp:audit verify --allow-root-user > var/tmp/evidence/verify-result.txt
```

Jeder Export gibt die Zahl der Einträge und den SHA-256 der Datei aus. Das Format `bundle` schreibt `records.jsonl` mit
einem signierten `manifest.json`, was zu Nachweisen passt, die später geprüft werden müssen. `export` nimmt dieselben
Filter wie `search` (`--channel`, `--name`, `--user`, `--login`, `--object`, `--result`, `--from`, `--to`, `--query` ...)
([die Anleitung zum Audit, Abschnitt 3.3](../../../../bc/6.0/audit.md#33-der-befehl-expaudit)). Für ein
Auskunftsersuchen einer betroffenen Person liefert `--subject-user=<id>` jeden Eintrag von oder über diesen Benutzer.

## Das Nachweispaket

| Bestandteil | Warum |
|---|---|
| Die Exportdatei | die Einträge |
| Der verwendete Filter, das Datum und wer exportiert hat | Nachvollziehbarkeit |
| Die Ausgabe von `verify` vom selben Tag | Integrität |
| Ein Hinweis darauf, was nicht aufgezeichnet wird (Lesezugriffe nur, wenn eingeschaltet; maskierte Adressen) | Auslegung |

Exporte enthalten Benutzernamen und können andere personenbezogene Daten enthalten: Senden Sie sie über einen
geschützten Kanal, bewahren Sie sie nur so lange wie nötig auf und halten Sie die Übergabe fest. Der Export selbst ist
ein Audit-Ereignis.

## Verwandte Seiten
- [Das Audit-Protokoll verifizieren](das-audit-protokoll-verifizieren.md)
- [Rollengestaltung: Prüfer](../../betrieb/personen-und-berechtigungen/rollenentwurf-pruefende.md)
- [Einem Prüfer Lesezugriff auf das Audit-Protokoll geben](../personen-und-berechtigungen/einem-pruefer-lesezugriff-auf-das-audit-protokoll-geben.md)
- [Die Funktion Audit-Protokoll](../../../../funktionen/6.0/pruefprotokoll.md#exporte-und-die-kanäle-von-extensions)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
