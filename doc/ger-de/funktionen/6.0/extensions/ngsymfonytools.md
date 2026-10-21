# ngsymfonytools: Twig und Symfony aus Legacy-Templates

Diese Seite ist für Entwickler einer Website im Übergang von Legacy-Templates zu Symfony. `ngsymfonytools` (Netgen
Symfony Tools) lässt **Legacy-Templates** **Twig-Templates** einbinden und **Symfony-Unteranfragen** ausführen, sodass
Legacy-Seiten Bausteine der Symfony- oder Ibexa-Seite wiederverwenden können.

Es funktioniert nur in einer Einrichtung mit [Legacy Bridge](../legacy-bridge.md), in der der Legacy-Kernel innerhalb
einer Symfony-Anwendung läuft.

## Operatoren

| Operator | Was er tut |
|---|---|
| `symfony_include` | Bindet ein Twig-Template mit Parametern ein. |
| `symfony_path`, `symfony_url` | Eine relative oder absolute URL für eine Route, mit den Argumenten von Twigs `path` und `url`. |
| `symfony_render` | Gibt eine Route als Unteranfrage aus. |

`doc/USAGE.md` der Extension beschreibt sie alle.

## Beispiel

```
{symfony_include(
    'NetgenTestBundle:Test:test.html.twig',
    hash(
        'theAnswer', 42,
        'homepage', fetch( 'content', 'node', hash( 'node_id', 2 ) )
    )
)}
```

Parameter vom Typ `eZContentObject` oder `eZContentObjectTreeNode` werden in die API-Objekte `Content` und `Location`
umgewandelt, bevor sie das Twig-Template erreichen.

## Was sich im Fork geändert hat (März bis April 2026)

- Paketname und Lizenz in `composer.json` sind die von se7enxweb, mit einem Abschnitt `replace`, damit der Fork das
  Original sauber ersetzt.
- `symfony_include` fragte den Container nach dem entfernten Dienst `templating` (seit Symfony 5 weg), was eine
  `ServiceNotFoundException` auslöste. Es verwendet jetzt den Dienst `twig`. Ein Workaround mit
  `Twig\Environment::class` wurde ausprobiert und zurückgenommen, sobald die Legacy Bridge (v5.0.9) den Dienst `twig`
  öffentlich machte.
- Ein Shim mit `class_alias` lädt den API-Inhaltskonverter sowohl mit der alten Schnittstelle
  `eZ\Publish\API\Repository\Repository` als auch mit `Ibexa\Contracts\Core\Repository\Repository` von Ibexa DXP 5.0.

## Verwandte Seiten

- [Legacy Bridge](../legacy-bridge.md)
- [Chronik des Repositorys](../../../../eng-us/history/ecosystem/ngsymfonytools.md) und [Versionshinweise](../../../../changelogs/extensions/ngsymfonytools.md) (zusammen mit den Plattform-Repositorys behandelt)
- [Änderungsverzeichnis](../../../../eng-us/history/ledger/ngsymfonytools.md)
- [Verhaltensänderungen der Extensions](../../../../eng-us/bc/6.0/extensions-behaviour-changes.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
