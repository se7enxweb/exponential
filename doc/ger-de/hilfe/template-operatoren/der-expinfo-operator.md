---
title: Der Operator expinfo
audience: developer, designer, app-creator
category: development
order: 280
summary: expinfo aus eZTemplateExpInfoOperator - die aktiven Erweiterungen, alle verfügbaren Erweiterungen, die Metadaten einer Erweiterung aus extension.xml und ezinfo.php und die Kernel-Informationen, welche Schlüssel jeder Modus zurückgibt und welche Kernel-Werte nie auf einer öffentlichen Seite ausgegeben werden dürfen; Beispiele, die auf alpha gerendert wurden.
keywords: expinfo, Erweiterungsinformationen, Erweiterungen, Kernel-Informationen, Version, extension info, ezinfo.php, extension.xml, expInfo, kernel info, version, eZTemplateExpInfoOperator
type: help
---
# Der Operator expinfo

`expinfo` beantwortet in einem Template die Fragen "Welche Erweiterungen sind installiert, welche sind aktiv, und welche
Version ist das?". Er wird von `eZTemplateExpInfoOperator` (`lib/eztemplate/classes/eztemplateexpinfooperator.php`)
über `lib/eztemplate/classes/eztemplateautoload.php` registriert und liest alles über die Klasse `expInfo`
(`lib/ezutils/classes/expinfo.php`).

## Inhalt

1. [expinfo](#expinfo)
2. [Eine Erweiterung](#eine-erweiterung)
3. [Listen von Erweiterungen](#listen-von-erweiterungen)
4. [Der Kernel](#der-kernel)
5. [Verwandte Seiten](#verwandte-seiten)

## expinfo

### expinfo

Der Modus ist der erste Parameter oder, wenn es keinen Parameter gibt, die gepipte Zeichenkette:

| Modus | Rückgabe (Methode von `expInfo`) |
|---|---|
| keiner, `''` oder `'active'` | ein Hash der aktiven Erweiterungen, Name auf Info (`activeExtensions()`) |
| `'all'` | jede verfügbare Erweiterung, jeweils mit `active` true oder false (`availableExtensions()`) |
| `'kernel'` | die Kernel-Informationen (`kernelInfo()`) |
| ein Erweiterungsname | die Info dieser Erweiterung mit `active` oder `false`, wenn es sie nicht gibt (`extensionInfo()`) |

| Parameter | Typ | Erforderlich | Standardwert |
|---|---|---|---|
| Modus oder Erweiterungsname | Zeichenkette | nein | aktive Erweiterungen |

Es gibt keine benannten Parameter. Generierte Referenz: [expinfo](../../../eng-us/examples/template-operators/expinfo.md).

## Eine Erweiterung

Die Info einer Erweiterung wird aus ihrer `extension.xml` und ihrer `ezinfo.php` (dem Array `<extension>Info::info()`)
gebildet, ergänzt um `mtime`, `mtime_formatted`, `extension_name` und `active`. Schlüssel, die eine `ezinfo.php`
hinzufügt, etwa die Hinweise zu Software von Drittanbietern bei ezjscore, erscheinen unter ihren eigenen Namen:

```tpl
{def $ext = expinfo( 'ezjscore' )}
{if $ext}keys: {foreach $ext as $key => $value}{$key}{delimiter}, {/delimiter}{/foreach}

ezjscore {$ext.version}, license: {$ext.license|wash}, active: {$ext.active|choose( 'no', 'yes' )}{/if}

expinfo( 'no_such_extension' ) = [{expinfo( 'no_such_extension' )|choose( 'false', 'true' )}]
```

Ausgabe auf alpha:

```text
keys: name, version, copyright, license, info_url, Includes the following third-party software, Includes the following third-party software (2), Includes the following third-party software (3), mtime, mtime_formatted, extension_name, description, summary, author, meta, active
ezjscore 1.8.5, license: GNU General Public License v2.0 (or any later version), active: yes
expinfo( 'no_such_extension' ) = [false]
```

`{if expinfo( 'name' )}` ist ein sicherer Weg, einen Teil eines Templates davon abhängig zu machen, dass eine
Erweiterung installiert ist; prüfen Sie zusätzlich `.active`, wenn die Erweiterung auch eingeschaltet sein muss.

## Listen von Erweiterungen

```tpl
{def $active = expinfo()
     $all = expinfo( 'all' )
     $piped = 'ezjscore'|expinfo
     $inactive = 0}
{foreach $all as $name => $info}{if $info.active|not}{set $inactive = $inactive|inc}{/if}{/foreach}
expinfo() returns {$active|count} active extensions; expinfo( 'all' ) returns {$all|count}, of which {$inactive} are not active.
'ezjscore'|expinfo works too: {$piped.name|wash}
```

Ausgabe auf alpha:

```text
expinfo() returns 62 active extensions; expinfo( 'all' ) returns 87, of which 25 are not active.
'ezjscore'|expinfo works too: eZ JSCore LS
```

Der Zugriff auf Attribute des Ergebnisses funktioniert bei einer Variablen (`$piped.name`), aber nicht direkt nach
einem Operator, der auf ein Literal angewendet wird: `{'ezjscore'|expinfo.name}` ist ein Parserfehler ("Extra
characters found in expression"). Weisen Sie das Ergebnis zuerst zu, wie oben.

## Der Kernel

`expinfo( 'kernel' )` gibt einen großen Hash zurück, der einmal pro Anfrage gebildet wird (`buildKernelInfo()`):

```tpl
{def $k = expinfo( 'kernel' )}
sections: {foreach $k as $section => $value}{$section}{delimiter}, {/delimiter}{/foreach}

version keys: {foreach $k.version as $key => $value}{$key}{delimiter}, {/delimiter}{/foreach}

PHP sapi in this run: {$k.php.sapi}
```

Ausgabe auf alpha:

```text
sections: version, php, memory, server, database, ini, user, extensions, cache, filesystem, timestamps, git, composer
version keys: major, minor, release, state, development, alias, edition, full, full_alias
PHP sapi in this run: cli
```

Der Abschnitt `version` ist für Templates gedacht: `$k.version.full` ist das Release als Zeichenkette. Die anderen
Abschnitte beschreiben den Server: `server` enthält den Hostnamen, das Document Root und die Installationspfade, `php`
enthält `uname` und die geladenen Erweiterungen, `database` enthält Datenbankname, Server und Benutzer (das Passwort
wird durch `***` ersetzt), und `git` und `composer` den Zustand des Checkouts. **Geben Sie diese Abschnitte nie auf
einer Seite aus, die Besucher sehen können**; sie sind für Verwaltungsseiten hinter einer Anmeldung bestimmt. Die
Beispiele auf dieser Seite geben nur Abschnitts- und Schlüsselnamen sowie die PHP-SAPI des Kommandozeilenlaufs aus.

Alle Beispiele lieferten auf alpha interpretiert und kompiliert dieselbe Ausgabe.

## Verwandte Seiten

- [Aufbau einer Erweiterung](../../../eng-us/help/creators/app-creators/extension-anatomy.md): `ezinfo.php` und `extension.xml`
- [Was eine Erweiterung ist](../../../eng-us/help/extension-development/part-1-from-user-to-developer/what-an-extension-is.md)
- [Die Erweiterungen Ihrer Website finden](../../../eng-us/help/extension-development/part-1-from-user-to-developer/finding-the-extensions-on-your-site.md)
- [Alle Template-Operatoren (generiert)](../../../eng-us/examples/template-operators/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
