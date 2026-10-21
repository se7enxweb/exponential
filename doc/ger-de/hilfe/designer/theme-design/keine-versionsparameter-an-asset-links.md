---
title: Keine Versionsabfragen an Asset-Links
audience: designer
category: theme-design
order: 250
summary: Warum Designs von Exponential nie ?v= oder ähnliche Versionsabfragen an Links zu Stylesheets und Skripten hängen, wie neue Dateien stattdessen die Browser erreichen und was zu tun ist, wenn ein Browser noch eine alte Datei zeigt.
keywords: Versionsabfrage, Cache Busting, ?v=, Query-String, Stylesheet-Link, Skript-Link, Browser-Cache, hartes Neuladen, AppendLastModifiedTime, veraltetes CSS, version query, cache busting, query string, stylesheet link, script link, browser cache, hard reload, stale css
type: help
---
# Keine Versionsabfragen an Asset-Links

Diese Seite ist für Designer, die an „Cache Busting“ mit `style.css?v=3` gewöhnt sind. In Exponential-Designs wird das nicht gemacht und
ist auch nicht nötig.

## Inhalt

1. [Die Regel](#die-regel)
2. [Wie neue Dateien die Browser erreichen](#wie-neue-dateien-die-browser-erreichen)
3. [Warum Sie trotzdem keine Abfrage anhängen](#warum-sie-trotzdem-keine-abfrage-anhängen)
4. [Ein Browser zeigt noch die alte Datei](#ein-browser-zeigt-noch-die-alte-datei)

## Die Regel

Hängen Sie in Templates kein `?v=`, `?ver=`, kein Datum und keinen Hash als Query-String an URLs von `<link>` oder `<script>`, und schlagen
Sie es in Reviews nicht vor. Das ist eine Regel des Projekts für jedes Design.

## Wie neue Dateien die Browser erreichen

Stylesheets und Skripte laufen über den Packer, der jede gepackte Datei nach der Liste ihrer Quellen und der **jüngsten Änderungszeit**
dieser Quellen benennt (`ezjscore.ini [Packer] AppendLastModifiedTime=enabled`). Bearbeiten Sie eine Datei, und die nächste Seite verlinkt
eine Datei mit anderem Namen. Browser haben sie nie gesehen und holen sie daher. Siehe [Der Packer](der-packer.md).

Bilder und Schriften, auf die CSS verweist, werden ebenfalls über ihre URL geholt; geben Sie einem ersetzten Bild einen neuen Dateinamen,
wenn es sich sofort ändern muss.

## Warum Sie trotzdem keine Abfrage anhängen

- Sie verdoppelt, was der Packer schon tut, und veraltet, wenn jemand vergisst, sie zu erhöhen.
- Von Hand geschriebene Links mit Abfragen umgehen den Packer und die Design-Kaskade.
- Manche Caches und Proxys behandeln URLs mit Abfragen anders.

## Ein Browser zeigt noch die alte Datei

1. Prüfen Sie, ob die Seite die neue gepackte Datei verlinkt (sehen Sie sich den Quelltext an). Verlinkt sie die alte, hält noch ein
   zwischengespeichertes Template den Link: Leeren Sie `template-block` (und `content` für Vollansichten); siehe
   [Der Packer-Cache und Deploys](packer-cache-und-deployment.md).
2. Ist der Link neu, der Stil aber alt, laden Sie den Browser hart neu.
3. Auf Websites mit einem Service Worker (das Design media registriert einen) kann der Worker eine ältere Seite ausliefern; laden Sie
   zweimal neu oder löschen Sie die Website-Daten in den Entwicklerwerkzeugen des Browsers.

## Verwandte Seiten

- [Der Packer](der-packer.md)
- [Stylesheets laden](stylesheets-laden.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
