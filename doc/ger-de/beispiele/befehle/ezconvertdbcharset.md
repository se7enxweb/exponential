---
title: exp:ezconvertdbcharset
audience: developer, admin
category: examples
summary: Convert all Exponential database tables to use UTF-8 character set.
keywords: Konsole, Befehl, exp:ezconvertdbcharset, ezconvertdbcharset, console, command, CLI, ezconvertdbcharset.php
type: generated
generated: exp:help:examples
---
# exp:ezconvertdbcharset

Convert all Exponential database tables to use UTF-8 character set.

Skript `bin/php/ezconvertdbcharset.php`, Code in `kernel/private/classes/commands/ezconvertdbcharset.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:ezconvertdbcharset --help
php bin/php/ezconvertdbcharset.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--extra-xml-attributes` | erforderlich | specify custom attributes which store its data in xml. usage: <datatype_string>[.<table>.<field>][,<datatype_string>.<table>.<field>...]. default table is 'ezcontentobject_attribute', default data field is 'data_text' note: your custom table must have 'id', 'version' and 'data_type_string' fields. |
| `--extra-xml-data` | erforderlich | specify custom xml data. usage: <table>.<field>[,<table>.<field>...]. note: your custom table must have 'id' field. |
| `--extra-serialized-data` | erforderlich | specify custom serialized data. usage: <table>.<field>;<key_field1>[.<key_field2>....][,<table>.<field>...]. ex: mytable.data_text;id.version,mytable2.data;id |
| `--collation` | erforderlich | specify collation for converted db. default is 'utf8_general_ci' |
| `--skip-class-translations` |  | Content class translations were added in Exponential 3.9. Use this options if upgrading from early version. |
| `--iconv-character-set` | erforderlich | This setting is used when characters are converted by iconv(). This settings is typically needed when a character set is not called the same by the database and iconv. An example is "windows-1252" (iconv) and "iso-8859-1" (mysql 5) |
| `--log-filename` | erforderlich | Specify a file where iconv conversions will be logged to |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/ezconvertdbcharset.php`, `kernel/private/classes/commands/ezconvertdbcharset.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
