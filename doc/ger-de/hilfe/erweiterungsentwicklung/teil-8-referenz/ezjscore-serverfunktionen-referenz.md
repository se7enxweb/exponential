---
title: Referenz der ezjscore-Serverfunktionen
audience: developer
category: extension-development
order: 230
summary: Die Einstellungen eines Blocks für ezjscore-Serverfunktionen, Aufrufadresse und Antwort, die Richtlinie ezjscore/call, die Serverfunktionen, die der Kernel mitbringt, und die JavaScript-Clients.
keywords: Serverfunktion, Ajax, Richtlinie, JSON, Aufrufadresse, ezjscore, server function, ezjscServer, ezjscServerFunctions, ezjscore/call, Class, Functions, TemplateFunction, RequirePolicy, HardLimit, ezjscnode, Exp.io.call, $.ez, JSON
type: help
---
# Referenz der ezjscore-Serverfunktionen

Teil 8, Referenz 19. Eine Serverfunktion ist eine PHP-Methode, die der Browser über `/ezjscore/call/...` aufrufen kann
und die JSON zurückgibt. Wie Sie eine bauen, erklären [Ajax mit ezjscore-Serverfunktionen](../teil-3-erweiterungsentwickler/ajax-mit-ezjscore-serverfunktionen.md)
und das Tutorial [Eine Ajax-Serverfunktion, von Hand](../teil-3-erweiterungsentwickler/tutorials/von-hand-ajax-serverfunktion.md).
Diese Seite führt die Einstellungen, die eingebauten Funktionen und die Clients auf.

## Der Einstellungsblock

In `settings/ezjscore.ini.append.php` Ihrer Erweiterung, ein Block je Servername:

| Schlüssel | Bedeutung |
|---|---|
| `[ezjscServer_<name>]` | der Block; `<name>` ist der Servername, der in Aufrufen verwendet wird |
| `Class` | die Klasse, deren öffentliche statische Methoden die Funktionen sind (erweitert `ezjscServerFunctions`) |
| `File` | die Datei der Klasse, wenn sie nicht per Autoload geladen wird |
| `TemplateFunction=true` | die Funktionen sind Templates, `templates/<name>/<function>.tpl`, statt PHP-Methoden |
| `Functions[]` | die Namen der Funktionslisten, zu denen dieser Block gehört, verwendet von der Einschränkung der Richtlinie `ezjscore/call` |
| `RequirePolicy` | `enabled`: Aufrufer brauchen für diese Funktionsliste zusätzlich die Richtlinie `ezjscore/call` |
| `HardLimit` | (nur `ezjscnode`) die höchste Zahl an Elementen, die ein Teilbaum-Aufruf zurückgibt |

## Die Adresse

```text
<root>/ezjscore/call/<server>::<function>[::<arg1>[::<arg2> ...]]
```

Die Teile nach dem Funktionsnamen kommen in `$args` an. POST-Daten liest die Methode mit `eZHTTPTool`. Die Antwort
lautet mit einem Header `Accept: application/json` `{"error_text":"...","content":...}`.

## Zugriff

- Jeder Aufruf braucht die Richtlinie `ezjscore/call`; ihre Einschränkung **FunctionList** nennt die Funktionslisten,
  die eine Rolle aufrufen darf.
- Jede Funktion muss trotzdem prüfen, was sie tut: Eine Funktion, die Inhalte liest, prüft, ob der Benutzer sie lesen
  darf; eine, die Daten ändert, verlangt POST und das Formular-Token ([ezformtoken und CSRF](ezformtoken-und-csrf.md)).
- Die Kernel-Funktionen von `ezjscnode` sind für jeden lesbar, der den Knoten lesen darf, sofern nicht
  `RequirePolicy=enabled` gesetzt ist; [Backend-Dienste über ezjscore](../../../bc/6.0/backend-dienste-ueber-ezjscore.md)
  erklärt den Schalter.

## Serverfunktionen, die der Kernel mitbringt

| Aufruf | Gibt zurück |
|---|---|
| `ezjsc::time` | die Serverzeit (ein schneller Test) |
| `ezjscnode::subtree::<nodeID>[::<limit>::<offset>...]` | eine Knotenliste als JSON |
| `ezjsctemplate::<template>` | ein Template, als Antwort gerendert |
| `expsubitems::columns::<nodeID>`, `expsubitems::rows::...` | die Daten der Tabelle der Unterelemente in der Administration |
| `expdebugbar::...` | die Funktionen der Debug-Leiste (Einstellungen, Caches, Log) |

Die Kommentare in `extension/ezjscore/settings/ezjscore.ini` nennen für jeden Block die Testadresse.

## Clients

| Client | Geladen mit | Aufruf |
|---|---|---|
| Exponential UI | `exp::io` (auf jeder Administrationsseite) | `Exp.io.call( 'myserver::myfunction', [ arg1 ], { data: { ... } } )` gibt ein Promise zurück; standardmäßig per POST |
| jQuery | `ezjsc::jquery`, `ezjsc::jqueryio` | `$.ez( 'myserver::myfunction::arg1', postData, callback )` |

Beide senden bei POST-Requests von Seiten, die das Formular-Token tragen, dieses mit.

## Verwandte Seiten

- Zurück: [Inhaltsfunktionen aus PHP](inhaltsfunktionen-aus-php.md)
- Weiter: [ezformtoken und CSRF-Schutz](ezformtoken-und-csrf.md)
- [ezjscore](../../../funktionen/6.0/erweiterungen/ezjscore.md)
- [Die JavaScript-API von expui](expui-javascript-api.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
