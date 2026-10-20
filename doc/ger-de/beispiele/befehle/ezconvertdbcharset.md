---
title: exp:ezconvertdbcharset
audience: developer, admin
category: examples
summary: Stellt alle Datenbanktabellen von Exponential auf den Zeichensatz UTF-8 um.
keywords: Konsole, Befehl, exp:ezconvertdbcharset, ezconvertdbcharset, console, command, CLI, ezconvertdbcharset.php
type: generated
generated: exp:help:examples
---
# exp:ezconvertdbcharset

Stellt alle Datenbanktabellen von Exponential auf den Zeichensatz UTF-8 um.

Skript `bin/php/ezconvertdbcharset.php`, Code in `kernel/private/classes/commands/ezconvertdbcharset.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:ezconvertdbcharset --help
php bin/php/ezconvertdbcharset.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--extra-xml-attributes` | erforderlich | eigene Attribute angeben, die ihre Daten in XML speichern. Verwendung: <datatype_string>[.<table>.<field>][,<datatype_string>.<table>.<field>...]. Standardtabelle ist 'ezcontentobject_attribute', Standard-Datenfeld ist 'data_text' Hinweis: Ihre eigene Tabelle muss die Felder 'id', 'version' und 'data_type_string' haben. |
| `--extra-xml-data` | erforderlich | eigene XML-Daten angeben. Verwendung: <table>.<field>[,<table>.<field>...]. Hinweis: Ihre eigene Tabelle muss das Feld 'id' haben. |
| `--extra-serialized-data` | erforderlich | eigene serialisierte Daten angeben. Verwendung: <table>.<field>;<key_field1>[.<key_field2>....][,<table>.<field>...]. Beispiel: mytable.data_text;id.version,mytable2.data;id |
| `--collation` | erforderlich | die Kollation der umgewandelten Datenbank angeben. Standard ist 'utf8_general_ci' |
| `--skip-class-translations` |  | Übersetzungen von Inhaltsklassen kamen mit Exponential 3.9 hinzu. Verwenden Sie diese Option beim Upgrade von einer früheren Version. |
| `--iconv-character-set` | erforderlich | Diese Einstellung wird verwendet, wenn Zeichen mit iconv() umgewandelt werden. Sie wird typischerweise gebraucht, wenn ein Zeichensatz in der Datenbank und in iconv nicht gleich heißt. Ein Beispiel ist "windows-1252" (iconv) und "iso-8859-1" (mysql 5) |
| `--log-filename` | erforderlich | Eine Datei angeben, in die die Umwandlungen durch iconv protokolliert werden |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/ezconvertdbcharset.php`, `kernel/private/classes/commands/ezconvertdbcharset.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
