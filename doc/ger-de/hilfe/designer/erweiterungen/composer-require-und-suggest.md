---
title: "require und suggest: wovon ein Theme abhängt"
audience: designer, developer
category: theme-extensions
order: 320
summary: Entscheiden, was in der composer.json eines Themes unter require und was unter suggest steht - PHP-Versionen, das Installer-Plugin, Layout- oder Site-API-Erweiterungen - mit den echten Entscheidungen von sevenx_themes_media und ezwebin und Versionsbedingungen, die Nutzer nicht in Schwierigkeiten bringen.
keywords: composer require, composer suggest, Abhängigkeiten, Versionsbedingungen, PHP-Version, Tilde, Caret, sevenx_themes_media, ezwebin, dependencies, version constraints, php version, tilde, caret
type: help
---
# require und suggest: wovon ein Theme abhängt

Diese Seite ist für Designer, die entscheiden, was die `composer.json` ihres Themes mitbringen soll.

## Die Faustregel

| Setzen Sie es in `require`, wenn | Setzen Sie es in `suggest`, wenn |
|---|---|
| Das Theme ohne es scheitert (ein fataler Fehler, eine leere Seite) | Das Theme funktioniert, ohne es aber eine Funktion fehlt |
| Es das Installer-Plugin ist, das das Theme ablegt | Es Inhalte oder Daten sind, die der Nutzer vielleicht schon hat |
| Es der PHP-Versionsbereich ist, den Sie testen | Es eine Alternative ist, die der Nutzer vorziehen könnte |

## Echte Entscheidungen

- `sevenx_themes_media` verlangt nur PHP (`^8.0 || ^8.1 || ^8.2 || ^8.3 || ^8.4`) und **schlägt vor**
  `se7enxweb/explayouts` (stellt die Layoutstruktur dar, für die seine Templates geschrieben sind),
  `se7enxweb/expsite-data-media` (das Inhaltsmodell und die Daten, die es darstellt) und `se7enxweb/expsite-api`.
- `ezwebin` **verlangt** `se7enxweb/ezpublish-legacy-installer` und `se7enxweb/expui` und schlägt drei kleinere
  Erweiterungen mit der Bedingung `~5.3` vor.

Beides ist vernünftig: Das Media-Theme überlässt die Wahl der Daten der Website; ezwebin kann ohne expui nicht zeichnen.

## Versionsbedingungen

| Bedingung | Erlaubt | Verwenden für |
|---|---|---|
| `^1.2` | `>=1.2.0 <2.0.0` | Bibliotheken, die semantischer Versionierung folgen |
| `~1.2.3` | `>=1.2.3 <1.3.0` | Pakete, die in der letzten Stelle nur Korrekturen hinzufügen |
| `~0.0.4.9` | `>=0.0.4.9 <0.0.5.0` (also ist `0.0.4.10` enthalten) | Vierstellige Versionen, bei denen sich nur die letzte Stelle bewegt |
| `*` | alles | Nur für das Installer-Plugin, wenn überhaupt |

Die Tilde lässt die zuletzt angegebene Stelle frei, sodass eine Reihe, die `.9`, `.10`, `.11` zählt, darin bleibt
([Nach .9 kommt .10](../veroeffentlichung/nur-die-letzte-stelle-zaehlt.md)).

## Schritt für Schritt

1. Listen Sie auf, was Ihre Templates und Operatoren aufrufen, das nicht im Kernel ist.
2. Ordnen Sie jedes mit der Tabelle oben `require` oder `suggest` zu. Der Text eines `suggest`-Eintrags sagt, was er
   hinzufügt.
3. Schreiben Sie den engsten PHP-Bereich, den Sie tatsächlich testen.
4. Führen Sie `composer validate` aus und installieren Sie das Theme in eine saubere Testinstallation, um zu sehen, dass
   `require` genügt.
5. Deklarieren Sie in `extension.xml` die Erweiterungen, die Sie überschreiben oder auf die Sie sich für die
   Ladereihenfolge verlassen ([Abhängigkeiten deklarieren](abhaengigkeiten-in-extension-xml.md)).

## Verwandte Seiten

- [composer.json für ein Theme](composer-json-fuer-ein-theme.md)
- [Versionsbedingungen Ihrer Nutzer](../veroeffentlichung/versionsbedingungen-fuer-nutzer.md)
- [PHP-Versionskompatibilität](../lebenszyklus/php-versionskompatibilitaet.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
