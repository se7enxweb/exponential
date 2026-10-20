---
title: Die Werkzeuge, die Sie brauchen
audience: user, developer, designer, app-creator
category: extension-development
order: 30
summary: Die kurze Liste der Werkzeuge für die Erweiterungsentwicklung mit Exponential - ein Terminal, PHP 8, Composer 2, Git, ein Texteditor, ein Browser mit Entwicklerwerkzeugen, xmllint und PHPUnit - mit dem Befehl, der jedes davon prüft.
keywords: Werkzeuge, Terminal, Editor, IDE, Browser, Entwicklerwerkzeuge, Anforderungen, tools, terminal, php, composer, git, editor, ide, browser, developer tools, xmllint, phpunit, php -l, requirements
type: help
---
# Die Werkzeuge, die Sie brauchen

Teil 1, Kapitel 3. Für die Erweiterungsentwicklung brauchen Sie sehr wenig. Dieses Kapitel listet die Werkzeuge auf,
sagt, wofür jedes da ist, und nennt den einen Befehl, der Ihnen sagt, ob Sie es haben. Nichts davon kostet Geld.

## Die Liste

| Werkzeug | Wofür | Prüfen | Sie brauchen mindestens |
|---|---|---|---|
| Ein Terminal | Jeden Befehl in diesem Buch | eines öffnen | eine beliebige Shell (bash, zsh) |
| PHP (Kommandozeile) | Die Konsole, die Installer, `php -l` ausführen | `php -v` | 8.0 (die `composer.json` des Kernels akzeptiert 8.0 und spätere Versionen 8.x; 8.5 wird unterstützt) |
| Composer | Exponential und Erweiterungen als Pakete installieren | `composer --version` | 2.x |
| Git | Historie, Branches, Ihre Arbeit teilen | `git --version` | 2.28 (siehe [Mit Git entwickeln](../../mit-git-entwickeln.md)) |
| Ein Texteditor | PHP, Templates und Einstellungen schreiben | | alles, was reines UTF-8 ohne Byte Order Mark speichert |
| Ein Browser mit Entwicklerwerkzeugen | Das HTML, CSS, die Netzwerkanfragen und Konsolenfehler ansehen, die Ihre Seiten erzeugen | F12 drücken | jeder aktuelle Browser |
| `xmllint` | `extension.xml` vor einem Release prüfen | `xmllint --version` | Teil von libxml2 |
| PHPUnit | Tests ausführen (die des Kernels und Ihre eigenen) | von Composer als Entwicklungsabhängigkeit installiert | die `composer.json` des Kernels akzeptiert 9.6 bis 13.x |

Die Anforderungen von Exponential selbst (PHP-Erweiterungen, Datenbanken, Webserver) stehen unter
[Anforderungen](../../../installation/02-voraussetzungen.md). In diesem Kapitel geht es nur darum, was *Sie* zusätzlich brauchen.

## PHP auf der Kommandozeile

Sie führen PHP auf der Kommandozeile viel häufiger aus, als Sie denken: die Konsole (`./console`), die Skripte unter
`bin/php/` und die Syntaxprüfung, die Sie nach jeder Änderung verwenden werden:

```bash
php -l extension/mysite_hello/ezinfo.php
```

Erwartet: `No syntax errors detected in extension/mysite_hello/ezinfo.php`. Machen Sie diese Prüfung zur Gewohnheit;
sie kostet eine Sekunde und fängt den Fehler ab, der sonst eine ganze Seite lahmlegen würde.

> **Hinweis**
> Das PHP der Kommandozeile und das PHP, das Ihre Webseiten ausliefert, können verschiedene Programme mit
> verschiedenen Einstellungen sein. Wenn etwas auf der Kommandozeile funktioniert, aber nicht im Browser (oder
> umgekehrt), vergleichen Sie die beiden: **Setup > Systeminformationen** zeigt das PHP des Webservers.

## Composer

Exponential und seine Erweiterungen sind Composer-Pakete. Sie brauchen Composer, um sie zu installieren und um die
Entwicklungswerkzeuge (PHPUnit) zu installieren. Richten Sie es einmal so ein, dass jedes Paket `se7enxweb/*` als
Git-Klon kommt, den Sie lesen und ändern können:

```bash
composer config --global 'preferred-install.se7enxweb/*' source
```

Warum und wie, erklärt [Mit Git entwickeln](../../mit-git-entwickeln.md). Die eigene Dokumentation von Composer finden Sie
unter [getcomposer.org](https://getcomposer.org/doc/).

## Git

Git bewahrt die Historie jeder Datei auf. Für einen Entwickler beantwortet es drei Fragen, die Sie jeden Tag stellen
werden: *Was habe ich geändert?* (`git diff`), *Warum ist diese Zeile hier?* (`git log -p`, `git blame`) und *Wie gehe
ich zurück?* (`git restore`, `git revert`). Jede Erweiterung der Organisation se7enxweb ist ein Git-Repository mit
ihrer vollständigen Historie. Die Grundlagen lernen Sie unter [git-scm.com](https://git-scm.com/book/en/v2).

## Ein Texteditor

Verwenden Sie, was Ihnen gefällt. Worauf es ankommt:

- **UTF-8 ohne Byte Order Mark.** Ein Byte Order Mark vor `<?php` wird ausgegeben, bevor die Seite beginnt, und macht
  die Header kaputt. `./console exp:ezcheckphptag` findet Dateien mit Zeichen vor dem PHP-Start-Tag.
- **Zeilenenden `LF`**, nicht `CRLF`.
- **Syntaxhervorhebung** für PHP und, wenn Ihr Editor ihn kennt, für Smarty-ähnliche Templates; die Template-Sprache
  ist Smarty so ähnlich, dass ein Smarty-Modus hilft.
- **Suche über mehrere Dateien.** Sie werden den Kernel oft durchsuchen. Auf der Kommandozeile tut `grep -rn` dasselbe:

  ```bash
  grep -rn "function fetchByNodeID" kernel/classes/
  ```

## Ein Browser mit Entwicklerwerkzeugen

Die Administration und Ihre Website sind Webseiten. Die Entwicklerwerkzeuge (F12) zeigen:

- **Elemente**: das HTML, das Ihre Templates erzeugt haben. Schalten Sie die Template-Pfadkommentare ein (siehe
  [Debug-Ausgabe und Logs für Entwickler](debug-ausgabe-und-logs-fuer-entwickler.md)), und das HTML sagt Ihnen, welches
  Template jeden Teil gezeichnet hat.
- **Konsole**: JavaScript-Fehler und Verstöße gegen die Content Security Policy. Die Administration von Exponential
  erzwingt eine Content Security Policy; ein Inline-Skript in Ihrem Template erscheint hier also als Verstoß, nicht als
  funktionierender Button.
- **Netzwerk**: welche Stylesheets und Skripte geladen wurden und die Antwort eines Ajax-Aufrufs.

## Werkzeuge, die schon in der Installation stecken

Diese müssen Sie nicht installieren; sie kommen mit Exponential:

| Werkzeug | Befehl | Verwendung |
|---|---|---|
| Die Konsole | `./console list` | Jeder Befehl des Kernels und der aktiven Erweiterungen, mit Beschreibung |
| Einstellungswerkzeug | `./console exp:ini` | Einstellungen lesen und ändern, sehen, welche Datei gewinnt |
| Autoload-Generator | `php bin/php/ezpgenerateautoloads.php -e` | Dafür sorgen, dass das System Ihre neuen Klassen findet |
| Cache-Werkzeug | `php bin/php/ezcache.php --clear-all --allow-root-user` | Caches nach einer Änderung leeren |
| Klassenprüfung | `php bin/php/checkclasses.php --allow-root-user` | Jede Klasse laden und diejenigen auflisten, die PHP ablehnt |
| Codestil | `./console exp:checkcodestyle` | Geänderte PHP-Dateien gegen `phpcs.xml.dist` prüfen |
| Mit Bootstrap ausführen | `php bin/php/ezexec.php <script.php>` | Ihr eigenes Skript mit geladenem Kernel ausführen |
| RAD-Werkzeuge | `Setup > RAD` (`/setup/rad`) | Assistenten, die Erweiterungen schreiben, Katalog, Gesundheitscheck |

`--allow-root-user` brauchen Sie nur, wenn Sie einen Befehl als Benutzer root ausführen; als normaler Benutzer lassen
Sie es weg.

## Optional, aber nützlich

- **Ein Datenbank-Client** für die Datenbank Ihrer Installation, um sich beim Lernen Tabellen anzusehen. Lesen Sie,
  schreiben Sie nicht: Die Inhaltstabellen werden vom Kernel gepflegt und gehen kaputt, wenn man sie von Hand bearbeitet.
- **PHPStan und PHP_CodeSniffer**: Die eigenen Qualitätsprüfungen des Kernels verwenden sie (`phpstan.neon.dist`,
  `phpcs.xml.dist` im Wurzelverzeichnis). Siehe [Tests für Erweiterungen](../../../../eng-us/help/extension-development/part-3-extension-developer/tests-for-extensions.md).
- **Ein GitHub-Konto**, sobald Sie veröffentlichen oder beitragen möchten. Für die Installation brauchen Sie keines.

## Alles auf einmal prüfen

```bash
php -v && composer --version && git --version && xmllint --version 2>&1 | head -1
```

Erwartet: vier Versionszeilen. Fehlt eine, installieren Sie das Werkzeug mit dem Paketmanager Ihres Systems, bevor Sie
weitermachen.

## Verwandte Seiten

- Zurück: [Die Leiter: Benutzer, Builder, Erweiterungsentwickler, Theme-Entwickler](die-leiter-vom-benutzer-zum-entwickler.md)
- Weiter: [Eine Entwicklungsinstallation einrichten](eine-entwicklungsinstallation-einrichten.md)
- [Anforderungen](../../../installation/02-voraussetzungen.md)
- [Mit Git entwickeln](../../mit-git-entwickeln.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
