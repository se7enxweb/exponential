---
title: exp:makestaticcache
audience: developer, admin
category: examples
summary: Durchläuft die öffentlichen Siteaccesses und erzeugt statische Seiten (--site, --max-pages, --max-depth).
keywords: Konsole, Befehl, exp:makestaticcache, makestaticcache, console, command, CLI, makestaticcache.php
type: generated
generated: exp:help:examples
---
# exp:makestaticcache

Durchläuft die öffentlichen Siteaccesses und erzeugt statische Seiten (--site, --max-pages, --max-depth).

Skript `bin/php/makestaticcache.php`, Code in `kernel/private/classes/commands/makestaticcache.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:makestaticcache --help
php bin/php/makestaticcache.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `-f` |  |  |
| `--force` |  | Zur Kompatibilität akzeptiert; ein Lauf ersetzt immer, was er erzeugt. |
| `--site` | erforderlich | Nur diesen Siteaccess erzeugen. Für mehrere wiederholen; weglassen für jeden öffentlichen. |
| `--max-pages` | erforderlich | Nach so vielen Seiten pro Website aufhören. Standard 2500. |
| `--max-depth` | erforderlich | Links so viele Schritte von der Wurzel der Website aus folgen. Standard 12. |
| `--keep` |  | Zum bereits Gespeicherten hinzufügen, statt es zu ersetzen. |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/makestaticcache.php`, `kernel/private/classes/commands/makestaticcache.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
