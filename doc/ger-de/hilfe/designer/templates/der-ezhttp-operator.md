---
title: Der Operator ezhttp
audience: designer, developer
category: templates
order: 280
summary: Werte der Anfrage (GET, POST, Sitzung, Cookie) in Templates mit ezhttp und ezhttp_hasvariable lesen, warum jeder solche Wert nicht vertrauenswürdig ist und maskiert werden muss und warum View-Parameter und Fetch-Ergebnisse meist die bessere Quelle sind.
keywords: ezhttp, ezhttp_hasvariable, GET-Parameter, POST-Parameter, Query-String, Sitzung, Cookie, Werte der Anfrage, nicht vertrauenswürdige Eingabe, XSS, wash, get parameter, post parameter, query string, session, cookie, request values, untrusted input, xss
type: help
---
# Der Operator ezhttp

Diese Seite ist für Designer, die in einem Template einen Wert aus der Anfrage brauchen, zum Beispiel einen Suchbegriff, den
sie wieder ausgeben.

## Inhalt

1. [Der Aufruf](#der-aufruf)
2. [Nicht vertrauenswürdige Eingaben](#nicht-vertrauenswürdige-eingaben)
3. [Bessere Quellen](#bessere-quellen)
4. [Caching](#caching)

## Der Aufruf

```tpl
{if ezhttp_hasvariable( 'SearchText', 'get' )}
    <p>{'You searched for %term'|i18n( 'design/mytheme/search',, hash( '%term', ezhttp( 'SearchText', 'get' )|wash ) )}</p>
{/if}
```

Parameter (`kernel/common/ezurloperator.php`): der Variablenname und die Methode `post` (Standard), `get`, `session` oder
`cookie`. Prüfen Sie zuerst mit `ezhttp_hasvariable`; eine fehlende Variable zu lesen, ist eine Warnung.

## Nicht vertrauenswürdige Eingaben

Alles in der Anfrage stammt vom Besucher oder von einem Angreifer in einem präparierten Link:

- **maskieren Sie es immer**, bevor Sie es ausgeben ([Wash und Maskierung](wash-und-maskierung.md));
- wandeln Sie Zahlen um: `ezhttp( 'page', 'get' )|int`;
- bauen Sie damit nie die Klassenliste oder das Sortierfeld eines Fetch, ohne es gegen eine Liste erlaubter Werte zu prüfen;
- geben Sie nie Sitzungs- oder Cookie-Werte aus, die etwas Privates enthalten könnten.

## Bessere Quellen

| Sie brauchen | Besser als ezhttp |
|---|---|
| Versatz beim Blättern, Filter in der URL | View-Parameter: `/(offset)/20` kommt im View-Template als `$view_parameters.offset` an |
| die aktuelle Seite | `$module_result`, `$node` |
| den angemeldeten Benutzer | `$current_user`, `fetch( 'user', 'current_user' )` |
| Suchergebnisse | die eigenen Template-Variablen des Suchmoduls |

## Caching

Ein Wert aus der Anfrage in einem Template im View-Cache oder in einem Cache-Block wird in den Cache eingefroren.
View-Parameter sind Teil des Schlüssels des View-Cache; beliebige GET-Werte nicht. Halten Sie die Ausgabe von `ezhttp`
außerhalb gecachter Teile.

## Verwandte Seiten

- [Der Operator ezini](der-ezini-operator.md)
- [Suchergebnis-Template](suchergebnis-template.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
