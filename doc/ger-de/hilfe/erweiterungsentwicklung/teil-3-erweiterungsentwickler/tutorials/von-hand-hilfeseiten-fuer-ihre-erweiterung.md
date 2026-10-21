---
title: "Von Hand: Hilfeseiten für Ihre Erweiterung"
audience: developer, app-creator, designer
category: extension-development
order: 220
summary: Die Dokumentation einer Erweiterung ins Hilfecenter der Administration bringen - doc/-Seiten mit Front Matter, die Freischaltung über ExtensionDocDirs, Kontexthilfe für ihre Views und die Prüfung mit exp:help.
keywords: Tutorial, von Hand, Hilfeseiten, Hilfecenter, Front Matter, Kontexthilfe, Dokumentation, tutorial, by hand, help pages, help center, exphelp, ExtensionDocDirs, front matter, context help, ContextHelp, Map, ext/, exp:help check, documentation
type: help
---
# Von Hand: Hilfeseiten für Ihre Erweiterung

Tutorial 21. Die Dokumentation Ihrer Erweiterung kann im selben Hilfecenter liegen wie die des Kernels, durchsuchbar und
über die Schaltfläche „?“ auf den Seiten Ihres Moduls verlinkt. Dieses Tutorial fügt `mysite_hello` zwei Seiten hinzu
und verbindet sie. Die Mechanismen stammen aus `settings/exphelp.ini` und den Hilfeklassen in `kernel/classes/help/`.

## 1. Die Seiten schreiben

`extension/mysite_hello/doc/README.md`:

```text
---
title: My Site Hello
audience: admin, developer
category: development
order: 10
summary: What the mysite_hello extension adds: greetings, a fetch function, a rating field, a digest.
keywords: mysite_hello, greeting, rating, digest
type: help
---
# My Site Hello

This extension greets visitors on /hello/world/<name>, lets editors compose greetings ...
```

`extension/mysite_hello/doc/compose.md`, mit einer Überschrift `## Composing a greeting` und den Schritten für
Redakteure.

Die Schlüssel des Front Matter und ihre Bedeutung beschreibt
[Ihre Erweiterung dokumentieren](../ihre-erweiterung-dokumentieren.md).
Nur Markdown: Die Hilfe maskiert rohes HTML.

## 2. Die Seiten committen

Die Hilfe liefert nur Dateien aus, die von Git verfolgt werden (sie liest den Index des Repositorys); committen Sie die
Seiten daher im Repository der Erweiterung:

```bash
git -C extension/mysite_hello add doc
git -C extension/mysite_hello commit -m "Added: Help pages for mysite_hello"
```

## 3. Freischalten

Die Installation entscheidet in ihrer `settings/override/exphelp.ini.append.php` (oder einer Site-Erweiterung), welche
Dokumentation von Erweiterungen sie anzeigt:

```ini
[IndexSettings]
ExtensionDocDirs[]=mysite_hello
```

Die Seiten erhalten die IDs `ext/mysite_hello/README.md` und `ext/mysite_hello/compose.md`.

## 4. Kontexthilfe

Ihre Erweiterung registriert das Ziel der Schaltfläche „?“ ihrer Views in
`extension/mysite_hello/settings/exphelp.ini.append.php`:

```ini
[ContextHelp]
Map[hello]=ext/mysite_hello/README.md
Map[hello/compose]=ext/mysite_hello/compose.md#composing-a-greeting
```

Der Anker ist die Überschrift so, wie der Viewer sie bildet (Kleinbuchstaben, Leerzeichen zu Bindestrichen). Ein
Eintrag, dessen Seite nicht ausgeliefert wird, wird übersprungen, sodass stattdessen der Eintrag des Moduls antwortet.

## 5. Neu aufbauen und prüfen

```bash
php bin/php/ezcache.php --clear-tag=ini
./console exp:help index
./console exp:help check
```

`exp:help check` meldet defekte Links und Bilder in den Seiten der Hilfe. Öffnen Sie dann das Hilfecenter, suchen Sie
nach „mysite_hello“ und drücken Sie „?“ auf `/hello/compose`.

## Verwandte Seiten

- Zurück: [Von Hand: eine Erweiterung übersetzen](von-hand-uebersetzung.md)
- Weiter: [Von Hand: ein PHPUnit-Test für Ihre Erweiterung](von-hand-phpunit-test.md)
- [Das Hilfesystem für Erweiterungen](../../teil-8-referenz/hilfesystem-fuer-erweiterungen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
