---
title: "Swark-Operatoren: Anfrage und System"
audience: developer, designer, app-creator
category: development
order: 432
summary: Die Anfrage- und Systemoperatoren der Erweiterung swark - charset, current_siteaccess, current_layout, is_post_request, server, cookie, user_id_by_login, variable_names, debug, debug_attributes, redirect, return und clear_object_cache - mit Parametern, Rückgabewerten, welche davon die Anfrage beenden oder Zustand ändern, und Beispielen, die auf alpha gerendert wurden.
keywords: swark, charset, current_siteaccess, current_layout, is_post_request, server, cookie, user_id_by_login, variable_names, debug, debug_attributes, redirect, return, clear_object_cache, Anfrage, System, Weiterleitung, Debug-Ausgabe, Nebenwirkungen, request, side effects
type: help
---
# Swark-Operatoren: Anfrage und System

Diese swark-Operatoren lesen die Anfrage und das System, schreiben in die Debug-Ausgabe oder beenden die Anfrage. Die
Klassen liegen in `extension/swark/autoloads/classes/`; wie sie registriert werden, steht unter
[Die swark-Operatoren](swark-operatoren.md).

## Inhalt

1. [Lesen](#lesen): [charset](#charset), [current_siteaccess](#current_siteaccess),
   [current_layout](#current_layout), [is_post_request](#is_post_request), [server](#server), [cookie](#cookie),
   [user_id_by_login](#user_id_by_login), [variable_names](#variable_names)
2. [Debug-Ausgabe](#debug-ausgabe): [debug](#debug), [debug_attributes](#debug_attributes)
3. [Operatoren mit Nebenwirkungen](#operatoren-mit-nebenwirkungen): [redirect](#redirect), [return](#return),
   [clear_object_cache](#clear_object_cache)

## Lesen

### charset

Keine Parameter, keine Eingabe. Gibt den HTTP-Zeichensatz zurück (`eZTextCodec::httpCharset()`).

### current_siteaccess

Keine Parameter. Gibt den Namen des aktuellen Siteaccess zurück (`$GLOBALS['eZCurrentAccess']['name']`).

### current_layout

Keine Parameter. Gibt den Schlüssel `layout` der Design-Ressource zurück (gesetzt von der View `layout/set`) oder
`false`. Auf einer gewöhnlichen Seite leer.

### is_post_request

Keine Parameter. `true`, wenn `$_SERVER['REQUEST_METHOD']` den Wert `POST` hat.

### server

| Parameter | Erforderlich |
|---|---|
| `variable_name` | ja |

Gibt `$_SERVER[<Name in Großbuchstaben>]` zurück oder `false` mit einer Debug-Warnung, wenn die Variable nicht gesetzt
ist. Der Wert ist unbearbeitet: Wenden Sie `wash` an, und geben Sie Servervariablen nie auf einer öffentlichen Seite aus.

### cookie

| Parameter | Erforderlich |
|---|---|
| `name` | ja |

Liest `$_COOKIE[name]` oder gibt `false` mit einer Debug-Warnung zurück. Er setzt keine Cookies. Der Wert kommt vom
Besucher: Wenden Sie `wash` an. Im Beispiel nicht gezeigt, weil die Kommandozeile keine Cookies hat.

### user_id_by_login

| Parameter | Erforderlich |
|---|---|
| `login` | ja |

Gibt die Inhaltsobjekt-ID des Benutzers mit diesem Login zurück (maskiertes SQL auf `ezuser`) oder `false`. Ist
`login` leer, wird die Eingabe verwendet; weil der Parameter aber erforderlich ist, protokolliert die Pipe-Form
Warnungen "Parameter 'login' missing": Rufen Sie ihn als Funktion auf.

### variable_names

| Parameter | Erforderlich | Standardwert |
|---|---|---|
| `in_debug` | nein | `1` |

Listet die Namen der Template-Variablen aller Namensräume auf (`$name` oder `$namespace:name`). Mit dem Standardwert
geht die Liste als Hinweis in die Debug-Ausgabe; mit `0` wird sie zurückgegeben.

```tpl
{def $title = 'x' $count = 3}
1 charset: {charset()}
2 current_siteaccess: {current_siteaccess()}
3 current_layout: [{current_layout()}]
4 is_post_request: {is_post_request()|choose( 'no', 'yes' )}
5 server SCRIPT_NAME: {server( 'script_name' )}
6 user_id_by_login: admin={user_id_by_login( 'admin' )}, anonymous={user_id_by_login( 'anonymous' )}, nobody={user_id_by_login( 'nobody-here' )|get_type}
7 variable_names: {variable_names( 0 )}
```

Ausgabe auf alpha:

```text
1 charset: utf-8
2 current_siteaccess: site
3 current_layout: []
4 is_post_request: no
5 server SCRIPT_NAME: bin/php/ezexec.php
6 user_id_by_login: admin=14, anonymous=10, nobody=boolean[false]
7 variable_names: $title, $count
```

## Debug-Ausgabe

### debug

| Parameter | Erforderlich | Standardwert |
|---|---|---|
| `header` | nein | `Debug operator` |

Schreibt die Eingabe als Hinweis in die Debug-Ausgabe (`eZDebug::writeNotice()`) und gibt nichts zurück.

### debug_attributes

| Parameter | Erforderlich | Standardwert |
|---|---|---|
| `header` | nein | `Debug attributes operator` |
| `depth` | nein | `2` |

Schreibt den Attributbaum der Eingabe, wie der Kernel-Operator `attribute`, in die Debug-Ausgabe. Eine Ganzzahl als
erster Parameter wird als Tiefe verstanden.

Keiner der beiden wird in einem gerenderten Beispiel gezeigt: Ihre Ausgabe geht nur in die Debug-Ausgabe (die
angezeigt wird, wenn die Debug-Ausgabe für die Anfrage aktiviert ist), nicht in die Seite.

## Operatoren mit Nebenwirkungen

Nicht auf alpha ausgeführt: Diese Operatoren beenden die Anfrage, senden Header oder leeren Caches, was ein
Beispiel, das nur liest, nicht tun darf. Ihr Verhalten ist anhand des Codes beschrieben.

### redirect

| Parameter | Erforderlich | Standardwert |
|---|---|---|
| `url` | ja | |
| `status` | nein | `302` |

Leitet mit `eZHTTPTool::redirect()` weiter und beendet die Anfrage mit `eZExecution::cleanExit()`. Einer URL ohne
Schema wird das Index-Verzeichnis vorangestellt. Ist `url` gleich `null`, wird die Eingabe verwendet.

```tpl
{redirect( '/content/view/full/89', 301 )}
```

### return

| Parameter | Erforderlich | Standardwert |
|---|---|---|
| `content_type` | nein | leer |

Sendet die Eingabe als vollständige Antwort: setzt `Content-Type` (wenn angegeben) und `Content-Length`, verwirft alle
Ausgabepuffer, gibt die Eingabe aus und beendet die Anfrage. Wird verwendet, um kleine JSON- oder Textanfragen aus
einem Template zu beantworten.

```tpl
{json_encode( hash( 'ok', true() ) )|return( 'application/json' )}
```

### clear_object_cache

Keine Parameter. Ruft `eZContentObject::clearCache()` auf, was den Speicher-Cache der Inhaltsobjekte dieser Anfrage
leert (den View-Cache auf der Festplatte berührt er nicht). Nützlich in langen Templates, die viele Objekte laden.

## Verwandte Seiten

- [Die swark-Operatoren](swark-operatoren.md)
- [Swark-Operatoren: Arrays, Zeichenketten und URLs](swark-operatoren-arrays-und-zeichenketten.md)
- [URL-, Design-, INI- und HTTP-Operatoren](url-operatoren.md) (`ezhttp`, `ezsys`)
- [Fehlersuche in Templates](../../../eng-us/help/designers/templates/template-debugging.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
