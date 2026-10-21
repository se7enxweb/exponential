---
title: "JavaScript-Bibliotheken aktualisieren (jQuery 4, kein YUI)"
audience: designer, developer
category: theme-lifecycle
order: 120
summary: Das JavaScript eines Themes auf die Bibliotheken bringen, die Exponential mitliefert - jQuery 4.0.0 mit Migrate und jQuery UI 1.14.2 über ezjscore, YUI entfernt - wonach Sie suchen, was Sie ersetzen und wie Sie testen.
keywords: jQuery 4, Entfernung von YUI, ezjscore, ezjsc::jquery, jQuery Migrate, jQuery UI, JavaScript-Upgrade, .on, .prop, YUI removal, JavaScript upgrade
type: help
---
# JavaScript-Bibliotheken aktualisieren (jQuery 4, kein YUI)

Diese Seite ist für Designer, deren Theme JavaScript hat. Am 1. und 2. Oktober 2026 ist die Administration auf jQuery 4
umgestiegen, und YUI wurde aus den Designs der Administration, dem Design `standard` und dem mitgelieferten ezjscore
entfernt. Die Einzelheiten stehen in [jQuery 4 in der Administration, und YUI ist weg](../../../../eng-us/features/6.0/jquery4-and-yui-removal.md)
und [Entfernung von YUI](../../../../eng-us/bc/6.0/yui-removal.md).

## Was ezjscore mitliefert

`ezjsc::jquery` und `ezjsc::jqueryUI` liefern **jQuery 4.0.0 mit jQuery Migrate 4.0.2 und jQuery UI 1.14.2**. Migrate ist
standardmäßig die stille Fassung, sodass älterer Code weiter funktioniert, ohne die Konsole zu füllen. Die älteren
Dateien von jQuery 3.7.1, Migrate 3.4.1 und jQuery UI 1.10.3 wurden am 14. Oktober 2026 entfernt.

## Schritt für Schritt

1. **Suchen Sie nach YUI**; es lädt nichts mehr:

   ```bash
   grep -rn "ezjsc::yui\|YUI(" extension/mytheme
   ```

   Keine Ausgabe bedeutet, dass nichts zu portieren ist. Sonst folgen Sie [Entfernung von YUI](../../../../eng-us/bc/6.0/yui-removal.md).
2. **Suchen Sie nach alten jQuery-Mustern**:

   ```bash
   grep -rn -E "\.(click|change|submit|bind|unbind)\(|jQuery\.trim|\.attr\(['\"](checked|disabled|selected)" extension/mytheme/design
   ```

3. **Ersetzen** Sie sie, wie es die Administration getan hat: Event-Kurzformen und `.bind()` durch `.on()`, `.unbind()`
   durch `.off()`, boolesches `.attr()` durch `.prop()`, `jQuery.trim()` durch `String.prototype.trim()`.
4. **Laden Sie jQuery über ezjscore**, nicht als eigene Kopie, damit die Version zum System passt.
5. **Testen** Sie jeden interaktiven Teil mit geöffneter Browserkonsole: keine Fehler, keine Warnungen von Migrate, wenn die
   meldende Fassung eingeschaltet ist.

## Verwandte Seiten

- [jQuery in Designs](../theme-design/jquery-in-designs.md)
- [Abhängigkeiten aktuell halten](abhaengigkeiten-aktualisieren.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
