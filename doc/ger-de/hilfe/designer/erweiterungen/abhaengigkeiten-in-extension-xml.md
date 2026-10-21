---
title: "Abhängigkeiten in extension.xml deklarieren"
audience: designer, developer
category: theme-extensions
order: 190
summary: Der Block dependencies in extension.xml - extends, requires und uses -, was jeder mit der Ladereihenfolge macht, ein echtes Beispiel und wie er mit require von Composer zusammenhängt.
keywords: Abhängigkeiten, extension.xml, extends, requires, uses, Ladereihenfolge, ExtensionOrdering, composer require, dependencies, loading order
type: help
---
# Abhängigkeiten in extension.xml deklarieren

Diese Seite ist für Designer, deren Theme eine andere Erweiterung braucht oder darauf aufbaut.

## Der Block

`cjw_newsletter` deklariert, dass es `ezwt` erweitert:

```xml
<software>
    <metadata>...</metadata>

    <dependencies>
        <extends>
            <extension name="ezwt" />
        </extends>
    </dependencies>
</software>
```

## Die drei Arten

Der Kernel liest den Block in `ezpExtension::getLoadingOrder()` (`kernel/private/classes/ezpextension.php`):

| Element | Lädt Ihre Erweiterung | Verwenden Sie es für |
|---|---|---|
| `<extends>` | vor der genannten (Ihre gewinnt) | Ein Theme, das Templates oder Einstellungen eines Basis-Themes überschreibt |
| `<requires>` | nach der genannten | Etwas, ohne das Ihr Theme nicht funktioniert, zum Beispiel eine Layout-Engine |
| `<uses>` | nach der genannten | Etwas, das Ihr Theme verwendet, wenn es vorhanden ist |

Die Reihenfolge wird angewendet, wenn `site.ini [ExtensionSettings] ExtensionOrdering=enabled` gilt. Sie ordnet nur
aktive Erweiterungen: `<requires>` zu deklarieren, aktiviert die andere Erweiterung nicht, und der Kernel lädt Ihre auch
dann, wenn die andere fehlt. Sagen Sie in Ihrem README, was installiert und aktiv sein muss.

## Schritt für Schritt

1. Entscheiden Sie für jede andere Erweiterung: Überschreibt Ihr Theme sie (`extends`) oder verlässt es sich auf sie
   (`requires`, `uses`)?
2. Fügen Sie den Block in `extension.xml` nach `<metadata>` ein.
3. Prüfen Sie die Datei: `xmllint --noout extension/mytheme/extension.xml` (keine Ausgabe erwartet).
4. Leeren Sie den INI-Cache und sehen Sie sich **Setup > Extensions** an: Die Karte zeigt die tatsächliche Ladeposition.
5. Fügen Sie dieselben Abhängigkeiten in `composer.json` hinzu (`require` oder `suggest`), damit Composer sie installiert
   ([require und suggest](composer-require-und-suggest.md)).

Eine fehlerhafte `extension.xml` wird in der Debug-Ausgabe gemeldet, und ihre Ladereihenfolge wird ignoriert; deshalb ist
Schritt 3 wichtig.

## Verwandte Seiten

- [Ladereihenfolge und welches Theme gewinnt](ladereihenfolge-von-themes.md)
- [extension.xml: die paketierte Beschreibung](extension-xml.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
