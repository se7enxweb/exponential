---
title: "extension.xml: Metadaten und Abhängigkeiten"
audience: developer, app-creator
category: extension-development
order: 30
summary: Die Datei extension.xml - die Metadaten, die vor ezinfo.php gelesen werden, und die Abhängigkeiten requires, uses und extends, die die Seite Extensions anzeigt und die über die Ladereihenfolge entscheiden.
keywords: Metadaten, Abhängigkeiten, Ladereihenfolge, benötigt, verwendet, erweitert, extension.xml, metadata, dependencies, requires, uses, extends, loading order, ExtensionOrdering, xmllint, software, info_url
type: help
---
# extension.xml: Metadaten und Abhängigkeiten

Teil 3, Kapitel 3. `extension.xml` erfüllt zwei Aufgaben: Sie trägt dieselben Metadaten wie `ezinfo.php` (und wird
zuerst gelesen), und sie deklariert **Abhängigkeiten** von anderen Erweiterungen, die die Seite Extensions anzeigt und
die über die Ladereihenfolge entscheiden können.

## Eine vollständige Datei

```xml
<?xml version="1.0" encoding="utf-8" ?>
<software>
    <metadata>
        <name>My Site Hello</name>
        <version>1.0.0</version>
        <copyright>Copyright (C) 2026 Your Organization</copyright>
        <license>GNU General Public License v2.0 (or any later version)</license>
        <info_url>https://github.com/your-organization/mysite_hello</info_url>
    </metadata>
    <dependencies>
        <requires>
            <extension name="ezjscore" />
        </requires>
        <uses>
            <extension name="eztags" />
        </uses>
        <extends>
            <extension name="ezwebin" />
        </extends>
    </dependencies>
</software>
```

Prüfen Sie sie:

```bash
xmllint --noout extension/mysite_hello/extension.xml
```

Keine Ausgabe bedeutet, dass sie wohlgeformt ist. Eine `extension.xml`, die sich nicht parsen lässt, wird ignoriert
(ihre Fehler gehen in die Debug-Ausgabe), und stattdessen wird `ezinfo.php` verwendet.

## Der Metadatenblock

`<name>`, `<version>`, `<copyright>`, `<license>`, `<info_url>` und optional `<description>`, `<summary>` und
`<author>`. Es gelten die Regeln von [ezinfo.php](ezinfo-php.md): ein lesbarer Name, die Version ohne `v`, der volle
Lizenzname, die Projektseite als `info_url`. Niemals `//autogentag//` oder `dev-master` als Version.

Eine ältere flache Form, `<extension><name>..</name><version>..</version></extension>` ohne Block `<metadata>`, wird
auf dieselbe Weise von der obersten Ebene gelesen.

## Abhängigkeiten

| Element | Bedeutet | Auf der Seite Extensions angezeigt als | Ladereihenfolge mit `ExtensionOrdering=enabled` |
|---|---|---|---|
| `<requires>` | Ihre funktioniert nicht ohne sie | Benötigt; ein Problem, wenn sie nicht aktiv ist | die benötigte Erweiterung wird **vor** Ihrer geladen |
| `<uses>` | Ihre verwendet sie, wenn sie vorhanden ist | Verwendet | die verwendete Erweiterung wird **vor** Ihrer geladen |
| `<extends>` | Ihre baut auf ihr auf und ändert sie | Erweitert | Ihre wird **vor** der erweiterten geladen |

Die Reihenfolge stammt aus `ezpExtension::getLoadingOrder()` (`kernel/private/classes/ezpextension.php`), das
`requires` und `uses` zu "danach" und `extends` zu "davor" macht, und aus `eZExtension::extensionOrdering()`, das die
aktiven Erweiterungen topologisch sortiert. Ein Test mit dem Sortierer des Kernels zeigt die Richtung: Eine
Erweiterung `a`, die `b` benötigt, kommt als `b,a` heraus.

Warum das wichtig ist: Eine Erweiterung, die in der Ladereihenfolge **weiter vorn** steht, gewinnt bei Einstellungen
und Design-Templates. Also:

- deklarieren Sie `<extends>`, wenn Ihre Erweiterung Templates oder Einstellungen einer anderen überschreibt: Ihre wird
  zuerst geladen und gewinnt;
- deklarieren Sie `<requires>` für eine Bibliothek, die Sie brauchen: Sie wird zuerst geladen, und die Seite Extensions
  warnt, wenn sie fehlt.

`site.ini [ExtensionSettings] ExtensionOrdering=enabled` ist die Vorgabe. Die Seite Extensions zeigt auf jeder Karte,
an welcher Stelle eine Erweiterung tatsächlich geladen wird, wenn das von ihrer geschriebenen Position abweicht
([Die Seite Extensions](../../../anleitungen/extensions-seite.md)).

## Abhängigkeiten im Code lesen

`expExtensionCatalogue::dependenciesFromXml( $xml )` (`kernel/classes/expextensioncatalogue.php`) zerlegt einen String
mit einer `extension.xml` in die Listen `requires`, `uses` und `extends`, genauso wie die Ladereihenfolge sie liest:

```php
<?php
$xml = file_get_contents( 'extension/ezwebin/extension.xml' );
print_r( expExtensionCatalogue::dependenciesFromXml( $xml ) );
```

## Bei jedem Release

`<version>` muss der Release-Version und der `Version` in `ezinfo.php` entsprechen, und zwar in dem Commit, auf den der
Tag zeigt. Siehe
[ezinfo.php und extension.xml hochzählen](../teil-7-veroeffentlichen/ezinfo-und-extension-xml-anheben.md).

## Verwandte Seiten

- Zurück: [ezinfo.php: wer sie gemacht hat und welche Version](ezinfo-php.md)
- Weiter: [Eine Erweiterung aktivieren](eine-erweiterung-aktivieren.md)
- [Metadaten von Erweiterungen (Spezifikation)](../../../spezifikationen/6.0/extension-metadaten.md)
- [Ladereihenfolge der Erweiterungen](ladereihenfolge-der-erweiterungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->

## Urheberrecht und Copyleft

Seit dem 29. Oktober 2026 halten die Exponential Foundation und 7x das Copyright an Exponential gemeinsam, die
Foundation zuerst genannt: „Exponential Copyright © 1998-2026 Exponential Foundation and 7x“, in Dateien
„Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.“ Wer es hält und warum, was die
GPL Ihnen erlaubt, welche Hinweise Dritter bleiben und wie der Hinweis in neue Dateien und Erweiterungen kommt:
[Urheberrecht und Copyleft](../../urheberrecht-und-copyleft.md).
