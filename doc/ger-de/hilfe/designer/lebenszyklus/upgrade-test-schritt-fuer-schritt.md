---
title: "Upgrade-Test Schritt für Schritt"
audience: designer, developer
category: theme-lifecycle
order: 100
featured: true
summary: Die Routine, um ein Theme gegen ein neues Release von Exponential zu testen - die Testinstallation aktualisieren, Caches leeren, die Seitenliste durchgehen, Overrides vergleichen, das Fehlerlog prüfen, Prüfungen durchführen und das Ergebnis festhalten.
keywords: Upgrade-Test, Routine, neues Release, Regression, Seitenliste, Fehlerlog, Caches, Ergebnisse festhalten, upgrade test, routine, new release, regression, page list, error log, caches, record results
type: help
---
# Upgrade-Test Schritt für Schritt

Diese Seite ist für Designer, die prüfen, ob ein Theme noch funktioniert, nachdem sich Exponential (oder PHP oder eine
Bibliothek) geändert hat. Führen Sie sie in Ihrer [Upgrade-Testumgebung](upgrade-testumgebung.md) aus.

## Bevor Sie beginnen

- Lesen Sie das Changelog nach Punkten, die Themes betreffen ([Ein Changelog als Designer lesen](ein-changelog-als-designer-lesen.md)).
- Führen Sie im Repository des Themes eine Seitenliste: die URLs der Demoseiten, die jedes Template zeigen.

## Schritt für Schritt

1. **Aktualisieren** Sie die Testinstallation auf das neue Release (`composer update` für die betroffenen Pakete) und
   folgen Sie [Upgraden](../../../anleitungen/aktualisieren.md) für alles, was das Release verlangt.
2. **Neu erzeugen und leeren**:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all --allow-root-user
   ```

   Laden Sie den PHP-Dienst neu, wenn die Website auf PHP-FPM oder Exponential Velocity läuft.
3. **Gehen Sie die Seitenliste durch**, bei 1440 px: Jede Seite wird dargestellt, nichts fehlt, nichts springt im Layout.
4. **Prüfen Sie die Logs**: `var/log/error.log` und die Debug-Ausgabe der Website zeigen keine neuen Fehler oder Warnungen
   aus Ihrem Theme.
5. **Vergleichen Sie die Overrides** mit den neuen Standard-Templates ([Ihre Overrides vergleichen](overrides-mit-dem-standard-vergleichen.md)).
6. **Prüfen** Sie: Barrierefreiheit, CSP, Optik und Leistung ([Barrierefreiheit](barrierefreiheitspruefung-pro-release.md),
   [CSP](csp-pruefung-pro-release.md), [Visuelle Regression](visuelle-regressionspruefungen.md), [Leistung](leistungspruefung-pro-release.md)).
7. **Bearbeiten und veröffentlichen** Sie als Redakteur einen Inhalt; das Theme zeigt ihn.
8. **Halten Sie** das Ergebnis in der Kompatibilitätsmatrix **fest** und öffnen Sie Issues für das, was gescheitert ist
   ([Eine Kompatibilitätsmatrix](kompatibilitaetsmatrix.md)).
9. **Beheben und veröffentlichen** Sie ([Die Version vor dem Tag erhöhen](../veroeffentlichung/version-vor-dem-tag-erhoehen.md)).

## Verwandte Seiten

- [Checkliste: ein Wartungszyklus](checkliste-ein-wartungszyklus.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
