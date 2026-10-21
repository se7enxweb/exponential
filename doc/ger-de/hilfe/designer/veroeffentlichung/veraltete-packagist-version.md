---
title: "Packagist zeigt eine alte Version"
audience: designer, developer
category: theme-distribution
order: 210
summary: Herausfinden, warum Packagist oder composer require nicht das neueste Release eines Themes anbietet - ein fehlender oder fehlschlagender Hook, ein Tag, den Packagist nicht lesen konnte, ein Fehler in composer.json oder ein Client mit Cache - und jede Ursache beheben.
keywords: veraltetes Packagist, alte Version, nicht aktualisiert, fehlender Webhook, Composer-Cache, Packagist-Update, Version fehlt, stale Packagist, old version, not updated, missing webhook, composer cache, version missing
type: help
---
# Packagist zeigt eine alte Version

Diese Seite ist für Designer, deren neues Release nicht auf Packagist erscheint oder deren Nutzer mit
`composer require` noch eine ältere Version bekommen.

## Der Reihe nach diagnostizieren

1. **Ist der Tag auf dem Remote?** `git ls-remote --tags origin | grep v1.0.25`. Keine Ausgabe: Pushen Sie den Tag.
2. **Kennt Packagist ihn?**

   ```bash
   curl -s https://repo.packagist.org/p2/yourvendor/mytheme.json | grep -o '"version":"[^"]*"' | head -3
   ```

   Die neue Version fehlt: weiter mit Schritt 3. Vorhanden: weiter mit Schritt 5.
3. **Gibt es einen Hook?** `gh api repos/yourvendor/mytheme/hooks --jq '.[].config.url'` listet eine URL auf
   `packagist.org`. Kein Hook ist die häufigste Ursache: Ein Paket kann ohne ihn monatelang auf einer alten Version
   stehen bleiben. Richten Sie ihn ein ([Packagist automatisch aktuell halten](packagist-automatisch-aktualisieren.md)) und drücken Sie
   auf der Paketseite **Update**.
4. **Konnte Packagist den Tag lesen?** Die Paketseite zeigt Fehler vom letzten Lesen. Eine fehlerhafte `composer.json` im
   getaggten Commit oder ein `version`-Feld, das nicht zum Tag passt, lässt Packagist diese Version überspringen.
   Korrigieren Sie es im nächsten Release; verschieben Sie nie den Tag.
5. **Hat der Composer des Nutzers einen Cache oder eine Bedingung?**

   | Prüfung | Befehl |
   |---|---|
   | Was die Bedingung des Nutzers erlaubt | `composer why-not yourvendor/mytheme 1.0.25` |
   | Den Cache von Composer leeren | `composer clear-cache` |
   | Welche Version installiert ist | `composer show yourvendor/mytheme` |

## Eine Tabelle der Ursachen

| Symptom | Ursache | Behebung |
|---|---|---|
| Packagist listet nur `dev-main` | Noch kein Tag | Einen Tag veröffentlichen |
| Packagist Monate im Rückstand | Kein Update-Hook | Den Hook hinzufügen, Update drücken |
| Eine Version fehlt zwischen anderen | `composer.json` in diesem Tag ungültig | Die nächste Version mit korrigierter Datei veröffentlichen |
| Packagist stimmt, Nutzer hat noch die alte | Bedingung oder Cache | `composer why-not`, `composer clear-cache` |

## Verwandte Seiten

- [Versionsbedingungen Ihrer Nutzer](versionsbedingungen-fuer-nutzer.md)
- [Prüfen, was schon veröffentlicht ist](pruefen-was-schon-veroeffentlicht-ist.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
