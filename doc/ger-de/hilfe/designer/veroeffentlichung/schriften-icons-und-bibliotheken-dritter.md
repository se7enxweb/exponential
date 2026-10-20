---
title: "Schriften, Icons und Bibliotheken Dritter"
audience: designer
category: theme-distribution
order: 280
summary: Die Lizenz jeder Schrift, jedes Icon-Satzes und jeder JavaScript- oder CSS-Bibliothek, die ein Theme mitliefert, einzeln prüfen - worauf Sie achten, wohin die Hinweise gehören und warum Selbsthosting CDNs vorzuziehen ist. Keine Rechtsberatung.
keywords: Dritte, Schriften, Icons, Bibliotheken, jQuery, Lizenzvereinbarkeit, OFL, MIT, Hinweise, CDN, Selbsthosting, NOTICE, third-party, fonts, icons, libraries, license compatibility, notices, self-hosting
type: help
---
# Schriften, Icons und Bibliotheken Dritter

Diese Seite ist für Designer, deren Theme Arbeit anderer enthält. Sie dürfen sie nur mitliefern, wenn ihre Lizenz das
erlaubt, und müssen deren Bedingungen befolgen. Prüfen Sie jeden Bestandteil einzeln; eine allgemeine Regel ersetzt nie
das Lesen der Lizenz. Dies ist keine Rechtsberatung.

## Was Sie bei jedem Bestandteil prüfen

| Frage | Warum |
|---|---|
| Darf er weitergegeben werden? | Manche Schriften dürfen auf Ihrer Website genutzt, aber nicht an andere weitergegeben werden |
| Ist er mit Ihrer Code-Lizenz vereinbar (bei einem GPL-Theme: GPL-kompatibel)? | Eine gemeinsame Verbreitung muss beiden gehorchen |
| Welcher Hinweis muss ihn begleiten? | Die meisten Lizenzen verlangen ihren Text und die Copyright-Zeile |
| Darf er geändert werden (eine Schrift reduzieren, ein Icon umfärben)? | Manche Lizenzen verbieten Änderungen oder verlangen eine umbenannte Schrift |

## Typische Fälle

| Bestandteil | Was Sie meist finden | Was zu tun ist |
|---|---|---|
| Quelloffene Schriften | Oft die SIL Open Font License | Die Lizenzdatei neben die Schriftdateien legen |
| Icon-Sätze | Unterschiedliche Lizenzen: MIT, CC BY oder eigene Bedingungen des Anbieters | Die Lizenz des Satzes lesen; seinen Hinweis behalten |
| JavaScript-Bibliotheken | Oft MIT oder BSD | Den Kopfkommentar und die Lizenzdatei behalten |
| jQuery | Exponential liefert es bereits über ezjscore mit (jQuery 4) | Das mitgelieferte statt einer zweiten Kopie verwenden |

## Selbst hosten statt aus einem CDN laden

Laden Sie Schriften, Icons und Skripte aus dem eigenen Design-Ordner des Themes. Ein CDN bringt bei jedem Besuch einen
Dritten ins Spiel (eine Datenschutzfrage für Besucher) und braucht zusätzliche Einträge in einer Content Security
Policy; die Richtlinie der Administration von Exponential erlaubt standardmäßig nur `'self'`. Siehe
[Externe Ressourcen und CDNs](../../../../eng-us/help/designers/theme-design/external-resources-and-cdns.md).

## Schritt für Schritt

1. Listen Sie jede Datei auf, die Sie nicht selbst erstellt haben: `fonts/`, `images/`, `javascript/`, `stylesheets/`.
2. Halten Sie zu jeder Name, Version, Quell-URL, Lizenz und Rechteinhaber in einer `THIRD-PARTY.md` (oder
   `CREDITS.md`) fest.
3. Legen Sie jeden Lizenztext neben seine Dateien (`design/mytheme/fonts/LICENSE-OFL.txt`).
4. Entfernen Sie alles, was Sie nicht eindeutig lizenzieren können.
5. Wiederholen Sie diese Prüfung, wann immer Sie eine Bibliothek aktualisieren ([Abhängigkeiten aktuell halten](../lebenszyklus/abhaengigkeiten-aktualisieren.md)).

## Verwandte Seiten

- [Creative Commons für Bilder](creative-commons-fuer-bilder.md)
- [Schriften in einem Design](../../../../eng-us/help/designers/theme-design/fonts-in-a-design.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
