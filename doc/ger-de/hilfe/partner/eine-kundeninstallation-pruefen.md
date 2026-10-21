---
title: Eine Kundeninstallation prüfen
audience: partner, hosting
category: partnership
order: 685
summary: Wie ein Partner die Exponential-Installation eines Kunden mit ./console exp:info in wenigen Minuten prüft - was läuft, welche Versionen, welche Erweiterungen, was Aufmerksamkeit braucht - bei einer Übergabe, vor einem Angebot, nach dem Go-live und bei jedem Wartungstermin, und wie Sie das Ergebnis als datierten Nachweis ohne Geheimnisse aufbewahren.
keywords: Partner, Kundeninstallation, prüfen, Prüfung, Audit, Übergabe, Bestandsaufnahme, Systemkarte, Hinweise, Wartung, Angebot, Go-live, customer installation, verify, handover, inventory, exp:info, exp:expfetch, system card, health hints, maintenance, quote, JSON
type: help
---
# Eine Kundeninstallation prüfen

Diese Seite ist für Implementierungs- und Hosting-Partner, die eine Kunden-Website übernehmen, ein Angebot dafür
erstellen oder sie warten. Die Systemkarte (system card) liefert Ihnen mit einem einzigen Befehl ein vollständiges
Bild einer Installation, das keine Geheimnisse enthält. Referenz:
[Die Systemkarte](../../funktionen/6.0/systemkarte.md) (auf Englisch).

## Die Prüfung

```bash
cd /path/to/the/installation
./console exp:info --allow-root-user                       # read it on screen
./console exp:info --json --no-logo > customer-2026-10-20.json   # keep it
```

Worauf Sie achten, in dieser Reihenfolge:

| Abschnitt | Prüfen |
|---|---|
| Exponential | die Version und die Release-Linie; ein Git-Checkout zeigt seinen Branch und Commit (ist es eine veröffentlichte Version?) |
| PHP | ein Branch mit Sicherheitsunterstützung (ein Hinweis nennt, wann sie endet), OPcache eingeschaltet, die empfohlenen Erweiterungen |
| Engine | Velocity oder Apache/nginx mit PHP-FPM; Worker und TLS |
| Datenbank | der Typ und die Version; keine Verbindung ist ein Fehler |
| Site, Siteaccesses | die Siteaccesses und ihre Hosts, Designs und Sprachen; Demomodus und Debug auf einer Live-Website ausgeschaltet |
| Erweiterungen | jede aktive Erweiterung und ihre Version: vergleichen Sie mit dem, was der Vertrag oder die Übergabe aufführt |
| Cronjobs | ein kürzlicher Lauf; die Teile, die die Funktionen der Website brauchen |
| Hinweise | jeder `warn` und jeder `error`: zu jedem gibt es eine einzeilige Lösung |

## Wann Sie sie ausführen

- **Vor einem Angebot oder einer Übernahme**: Die Bestandsaufnahme zeigt Ihnen den Umfang eines Upgrades
  (PHP-Branch, Exponential-Linie, Erweiterungen ohne Version).
- **Beim Go-live**: keine `error`-Hinweise, Demomodus und Debug ausgeschaltet, Cron läuft. Hängen Sie die JSON-Datei
  an das Abnahmeprotokoll an.
- **Bei jedem Wartungstermin**: Führen Sie die Prüfung erneut aus und vergleichen Sie mit der letzten Datei
  (`diff <(jq -S . old.json) <(jq -S . new.json)`): neue Erweiterungen, Versionsänderungen, neue Hinweise.

## Mit dem Kunden oder mit 7x teilen

Die Ausgabe enthält nie ein Passwort, einen Schlüssel, ein Token, einen Datenbankbenutzer oder eine vollständige
Datenbankadresse und auch nicht die Werte der Velocity-envvars. Sie kann daher unverändert in ein Ticket, eine E-Mail
oder einen Bericht übernommen werden. Sie enthält aber Hostnamen und die Liste der Erweiterungen; behandeln Sie sie
als Information des Kunden ([Kundendaten schützen](kundendaten-schuetzen.md)).

## Verwandte Seiten

- [Die Systemkarte](../../funktionen/6.0/systemkarte.md) (auf Englisch)
- [Kunden-Websites aktuell halten](kunden-websites-aktuell-halten.md)
- [Ein Leitfaden für Implementierungsprojekte](leitfaden-fuer-implementierungsprojekte.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
