---
title: REST- und JSON-Aufrufe
audience: developer
category: examples
order: 90
summary: Inhalte auf drei Wegen als JSON lesen – mit einer eigenen ezjscore-Serverfunktion, aufgerufen aus einem CSP-sicheren Skript des Designs, mit dem mitgelieferten expservices-Katalog und einem persönlichen API-Token und mit der REST-API unterhalb von /api/ – samt den Richtlinien, die jeder Weg prüft.
keywords: REST, JSON, ezjscore, Serverfunktion, JavaScript, CSP, API-Token, Richtlinie, server function, ezjscServerFunctions, ezjscore.ini, ezjscServer, FunctionList, RequirePolicy, ezjscore/call, fetch, expservices, API token, X-CSRF-Token, ezxform_token, index_rest.php, ezprestapi, /api/ezp/v2
type: example
---
# REST- und JSON-Aufrufe

Drei Wege, Exponential über HTTP zu lesen, vom kleinsten bis zum vollständigsten:

| Weg | Adresse | Am besten für |
|---|---|---|
| Eine eigene **ezjscore-Serverfunktion** | `/ezjscore/call/<name>::<function>::<argument>` | eine Antwort, die eine Seite Ihres Designs braucht, abgerufen von ihrem eigenen Skript |
| Der mitgelieferte **expservices**-Katalog | `/ezjscore/call/exp<domain>::<method>` | Skripte und Apps, die einen großen Teil der Administration brauchen, mit persönlichen API-Tokens |
| Die **REST-API** | `/api/ezp/v1/...`, `/api/ezp/v2/...` | Inhaltsclients (mobile Apps), die Knoten, Objekte und Felder lesen; braucht die Erweiterung `ezprestapi` |

Voraussetzungen: die Erweiterung aus [Ein Erweiterungsgerüst](erweiterungsgeruest.md), aktiviert; `ezjscore` wird
mitgeliefert und ist standardmäßig aktiv.

## 1. Eine eigene Serverfunktion

Sie fügen `expexample::count::<node id>` hinzu: die Anzahl der Kinder eines Knotens, die der Aufrufer lesen darf.

### Die Klasse

`extension/expexample/classes/expexampleserverfunctions.php` (gefunden über das Autoload-Array):

```php
<?php
/**
 * The ezjscore server functions of expexample: /ezjscore/call/expexample::count::<node id>.
 * Registered in ezjscore.ini [ezjscServer_expexample]; found through the autoload array.
 *
 * @copyright Copyright (C) 2026 Example Company
 * @license GNU General Public License v2.0 (or any later version)
 * @package expexample
 */

class expExampleServerFunctions extends ezjscServerFunctions
{
    /**
     * The number of children of a node the current user may read.
     *
     * @param array $args the arguments after the function name: array( <node id> )
     * @return int
     * @throws InvalidArgumentException for a node that does not exist or may not be read
     */
    public static function count( $args )
    {
        $nodeID = isset( $args[0] ) ? (int)$args[0] : 0;
        $node = eZContentObjectTreeNode::fetch( $nodeID );
        if ( !$node instanceof eZContentObjectTreeNode || !$node->canRead() )
            throw new InvalidArgumentException( 'Node not found' );
        return (int)$node->subTreeCount( array( 'Depth' => 1, 'DepthOperator' => 'eq' ) );
    }
}
```

`ezjscore/call` prüft selbst keine Richtlinie (es steht in `site.ini [RoleSettings] PolicyOmitList[]`): Jede Funktion
schützt sich selbst. Der Router lehnt eine Klasse ohne Block `[ezjscServer_<name>]` ab und prüft `Functions[]` gegen
die Richtlinie `ezjscore/call` des Benutzers. Die Funktion oben prüft zusätzlich `canRead()`, damit sie einem Besucher
nie etwas über einen Knoten verrät, den er nicht sehen darf. Eine Exception wird zum `error_text` der Antwort.

### Die Registrierung

`extension/expexample/settings/ezjscore.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[ezjscServer]
# The permission function list of ezjscore/call: a role grants ezjscore/call with the limitation
# "FunctionList: expexample"
FunctionList[]=expexample

[ezjscServer_expexample]
Class=expExampleServerFunctions
# The user needs ezjscore/call with FunctionList expexample
Functions[]=expexample
# Refuse a request whose calls are all refused with 403 Forbidden
RequirePolicy=enabled

*/ ?>
```

Erteilen Sie die Berechtigung: **Benutzer > Rollen und Richtlinien**, eine Rolle der Benutzer (für Besucher die Rolle
*Anonymous*), **Neue Richtlinie**, Modul *ezjscore*, Funktion *call*, Einschränkung *FunctionList*: `expexample`.

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
curl -s -H 'Accept: application/json' https://example.com/ezjscore/call/expexample::count::2
```

Erwartet: `{"error_text":"","content":<number>}`. Ohne die Richtlinie `403` (mit `RequirePolicy=enabled`).
`?ContentType=json` in der Adresse bewirkt dasselbe wie der `Accept`-Header, was im Browser hilft.

### Aufruf aus einer Seite, innerhalb einer Content Security Policy

Die Administration erzwingt eine Content Security Policy (`script-src 'self'` und eine Nonce), und eine Website kann
das ebenfalls tun: kein Inline-`<script>`, kein `onclick=""`. Das Template markiert nur, wohin die Antwort kommt, mit
der Adresse in einem `data-`-Attribut:

`extension/expexample/design/standard/templates/expexample/count.tpl`:

```tpl
{* Include where the number of children of $node should appear; expexample.js fills it in *}
<span class="expexample-count"
      data-expexample-url={concat( 'ezjscore/call/expexample::count::', $node.node_id )|ezurl}>…</span>
```

`ezurl` fügt den Siteaccess-Pfad und die Anführungszeichen hinzu. Das Skript ist eine Datei des Designs,
`extension/expexample/design/standard/javascript/expexample.js`:

```js
/*
 * expexample.js: fills every element with a data-expexample-url attribute with the answer of that ezjscore
 * server function call. No inline script: the file is loaded by the pagelayout from design.ini
 * [JavaScriptSettings] FrontendJavaScriptList[], so a Content Security Policy with script-src 'self' allows it.
 */
(function () {
    'use strict';

    function callServer(url) {
        // ezjscore/call answers JSON when the request asks for it: {"error_text": "", "content": ...}
        return fetch(url, {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' }
        }).then(function (response) {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            return response.json();
        }).then(function (answer) {
            if (answer.error_text) {
                throw new Error(answer.error_text);
            }
            return answer.content;
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-expexample-url]').forEach(function (element) {
            callServer(element.getAttribute('data-expexample-url')).then(function (content) {
                element.textContent = String(content);
            }).catch(function () {
                element.textContent = '?';
            });
        });
    });
}());
```

Es wird über `design.ini [JavaScriptSettings] FrontendJavaScriptList[]=expexample.js` geladen (siehe
[Ein Erweiterungsgerüst](erweiterungsgeruest.md)), das das Pagelayout eines Website-Designs mit `ezscript_load` packt;
in einem Administrationsdesign verwenden Sie `BackendJavaScriptList[]`. Ein Design, das die Liste nicht liest, lädt die
Datei mit `{ezscript_require( 'expexample.js' )}` in seinem Pagelayout.

**Schreibzugriffe.** Eine Funktion, die etwas ändert, nimmt nur POST an und muss das Formular-Token prüfen: Senden Sie
es als Header `X-CSRF-Token` (oder als Feld `ezxform_token`); die Erweiterung `ezformtoken` lehnt einen POST eines
angemeldeten Benutzers ohne Token ab. Lesen Sie das Token aus der Seite (das Pagelayout der Administration enthält es)
oder über `expsession::token`.

## 2. Der expservices-Katalog

`expservices` (mitgeliefert) stellt den größten Teil der Administration als ezjscore-Serverfunktionen bereit, mit
einem einheitlichen Antwortformat, `{"ok": true, "data": ..., "meta": ...}`, Seitenaufteilung und Zugriffsprüfungen
je Dienst. Ermitteln Sie, was es gibt, und rufen Sie es dann auf:

```bash
curl -s 'https://example.com/ezjscore/call/expservices::domains?ContentType=json'
curl -s 'https://example.com/ezjscore/call/expservices::catalog::system?ContentType=json'
```

Skripte melden sich mit einem persönlichen API-Token an (einmal im angemeldeten Zustand mit `expsession::tokenCreate`
erzeugt) und senden es als `Authorization: Bearer expt_...` oder, wo ein Proxy diesen Header verwirft, als
`X-Exp-Token: expt_...`:

```bash
export EXP_TOKEN='expt_...'
curl -s -H "X-Exp-Token: $EXP_TOKEN" 'https://example.com/ezjscore/call/expsystem::version?ContentType=json'
```

Eine Anfrage mit Token braucht für Schreibzugriffe POST, aber kein Formular-Token. Ein eigener Dienst erweitert
`expServiceBase` und meldet sich in `public static $services` an; siehe
[Backend-Dienste über ezjscore](../../../eng-us/bc/6.0/backend_ezjscore_services.md#writing-a-service).

## 3. Die REST-API

`/api/` wird von jeder ausgelieferten Webserver-Konfiguration an `index_rest.php` geleitet. Die Inhaltsrouten stammen
aus der Erweiterung `ezprestapi` (separat installiert, `ActiveExtensions[]=ezprestapi`): Lesen über `v1` und `v2`,
Schreiben über `v2`, jeder Aufruf geprüft gegen die `content/*`-Richtlinien des Benutzers.

```bash
export EXP_API_KEY='expk_...'     # a personal API key (My account > API access)
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v2/content/node/2
curl -s -H "Authorization: Bearer $EXP_API_KEY" \
     "https://example.com/api/ezp/v1/content/node/2/list/offset/0/limit/20?ResponseGroups=Fields"
```

Routen, Authentifizierung (OAuth2 oder persönliche API-Schlüssel) und Konfiguration: [REST-API v1 und v2](../../anleitungen/rest-api-v1-und-v2.md).

## Prüfen

Für diese Seite geprüft (2026-10-18): `php -l` auf die Klasse der Serverfunktion und die Einstellungsdatei;
`bin/php/eztemplatecheck.php` meldet `count.tpl` als gültig (Syntax). Das Antwortformat, die Prüfung der Richtlinie
und `RequirePolicy` sind die aus `extension/ezjscore/classes/runnable/views/ezjscore/call.php` und
`extension/ezjscore/settings/ezjscore.ini`; der Header des Formular-Tokens ist der, den
`extension/ezformtoken/event/ezxformtoken.php` liest. Die Aufrufe wurden für diese Seite nicht gegen eine Website
ausgeführt.

## Häufige Fehler

| Symptom | Ursache |
|---|---|
| `Not a valid ezjscServerRouter argument` | kein Block `[ezjscServer_<name>]`, ein Tippfehler im Namen, oder der INI-Cache |
| Die Antwort ist HTML, nicht JSON | kein Header `Accept: application/json` und kein `?ContentType=json` |
| `403` bei jedem Aufruf | der Rolle fehlt `ezjscore/call` mit der Einschränkung `FunctionList` der Funktion |
| Das Skript läuft in der Administration nie | ein Inline-`<script>` oder ein `on...=`-Attribut, von der Content Security Policy abgelehnt; laden Sie eine Datei |
| `Authorization` kommt nie bei PHP an | Apache mit PHP-FPM verwirft den Header, sofern nicht konfiguriert (`CGIPassAuth On`); verwenden Sie für expservices `X-Exp-Token` |

## Abgeleitet aus

- `extension/ezjscore/settings/ezjscore.ini` (`[ezjscServer]`, `[ezjscServer_<name>]`, `RequirePolicy`)
- `extension/ezjscore/classes/runnable/views/ezjscore/call.php`, `extension/ezjscore/classes/ezjscajaxcontent.php`
- `extension/ezjscore/classes/ezjscserverfunctionsjs.php`, `extension/ezjscore/settings/site.ini.append.php`
- `extension/ezformtoken/event/ezxformtoken.php`; `doc/eng-us/bc/6.0/backend_ezjscore_services.md`;
  `doc/eng-us/guides/rest-api-v1-and-v2.md`

## Verwandte Seiten

- [Kochbuch](README.md)
- [Entfernte Dienste: die ganze Administration als HTTP-API](../../../eng-us/features/6.0/remote-services-expservices.md)
- [REST-API v1 und v2](../../anleitungen/rest-api-v1-und-v2.md), [Persönliche API-Schlüssel](../../anleitungen/api-schluessel.md)
- [Content Security Policy](../../../eng-us/features/6.0/content-security-policy.md)
- [Fetch-Funktionen](../fetch-funktionen/README.md) (dieselben Daten in Templates) und [Richtlinienfunktionen](../richtlinien/README.md)
- Die Programmierregeln und der Musterkatalog: [die Standards](../../../eng-us/standards/README.md)
- [Freie Dokumentation](../../hilfe/freie-dokumentation.md): Diese Seite darf frei kopiert, übersetzt und verbessert
  werden.
