---
title: Erweitern, ohne zu forken
audience: developer, designer, app-creator
category: extension-development
order: 10
summary: Ändern Sie aus Ihrer eigenen Erweiterung heraus, wie sich der Kernel oder die Erweiterung eines anderen verhält - Einstellungen, Template-Overrides, Design-Reihenfolge, Handler, Events, Ersetzen von Modulen - damit deren Updates Sie weiterhin erreichen.
keywords: erweitern, ohne Fork, Override, Template-Override, Einstellungen, settings append, Design-Erweiterung, Handler, Event, Filter, Modul-Override, Ladereihenfolge der Erweiterungen, extend, without forking, override, template override, design extension, handler, event, filter, module override, extension load order
type: help
---
# Erweitern, ohne zu forken

Teil 5, Kapitel 1. Bevor Sie die Erweiterung eines anderen kopieren und ändern, suchen Sie nach einem Weg, ihr
Verhalten aus Ihrer eigenen Erweiterung heraus zu ändern. Exponential wurde dafür gebaut: Fast alles, was der Kernel
und gut geschriebene Erweiterungen tun, lässt sich von außen anpassen. Dieses Kapitel listet die Wege auf, vom
leichtesten zum schwersten, damit Sie den leichtesten wählen können, der funktioniert.

## Warum das wichtig ist

Wenn Sie die Dateien eines anderen ändern, gehören Ihnen diese Änderungen für immer. Jedes Update des Originals muss
von Hand mit Ihren Änderungen zusammengeführt werden. Wenn Sie das Verhalten aus Ihrer eigenen Erweiterung heraus
ändern, bleibt das Original unberührt: Sie aktualisieren es mit `composer update`, und Ihre Erweiterung erledigt
weiter ihre Aufgabe.

## Die Wege, vom leichten zum schweren

| Weg | Was er ändert | Kapitel |
|---|---|---|
| 1. Eine Einstellung in `settings/*.ini.append.php` Ihrer Erweiterung | alles, was das Original aus seiner INI-Datei liest | [Einstellungen in einer Erweiterung](../teil-3-erweiterungsentwickler/einstellungen-in-einer-erweiterung.md) |
| 2. Ein Template in Ihrem Design | wie eine Seite oder ein Teil aussieht | [Die Design-Kette](../teil-2-builder/die-design-kette.md) |
| 3. Eine Template-Override-Regel (`override.ini`) | ein Template für eine Klasse, einen Knoten, eine Sektion oder einen View-Modus | [Template-Overrides](../teil-2-builder/grundlagen-der-template-overrides.md) |
| 4. Ein eigener Template-Operator oder eine eigene Fetch-Funktion | was Templates berechnen oder laden können | [Template-Operatoren](../teil-3-erweiterungsentwickler/template-operatoren.md) |
| 5. Ein Event-Listener oder Filter | was vor oder nach Aktionen des Kernels geschieht | [Events, Listener und Filter](../teil-3-erweiterungsentwickler/ereignisse-und-filter.md) |
| 6. Ein Handler, der über eine Einstellung benannt wird | ein ganzer Dienst (Mail-Transport, Bild-Handler, Suche) | [Handler, die über Einstellungen benannt werden](../teil-3-erweiterungsentwickler/handler-ueber-einstellungen-benennen.md) |
| 7. Eine Unterklasse der ursprünglichen Klasse, registriert dort, wo das Original genannt wird | das Verhalten einer Klasse | siehe unten |
| 8. Ein gleichnamiges Modul in Ihrer Erweiterung | ein ganzes Modul und seine Views | [Extension module override](../../../../eng-us/features/6.0/extension-module-override.md) (auf Englisch) |
| 9. Ein Kernel-Override | eine Kernel-Klasse | [Kernel-Overrides: das letzte Mittel](../teil-3-erweiterungsentwickler/kernel-overrides-der-letzte-ausweg.md) |

Jeder Schritt weiter unten in der Liste bindet Sie enger an die Interna des Originals, sodass ein Update des Originals
eher eine Änderung auf Ihrer Seite erfordert.

## Wer gewinnt: die Ladereihenfolge

Die meisten dieser Wege funktionieren, weil der Kernel die aktiven Erweiterungen in einer Reihenfolge liest und die
letzte Einstellung bzw. das erste gefundene Template gewinnt. Ihre Erweiterung muss daher an der richtigen Stelle
relativ zu der geladen werden, die sie ändert. Nennen Sie das Original in Ihrer `extension.xml`:

```xml
<dependencies>
    <requires>
        <extension name="theoriginal" />
    </requires>
</dependencies>
```

Die Einzelheiten, auch wie Sie prüfen, welche Reihenfolge Sie bekommen haben, stehen in
[Ladereihenfolge der Erweiterungen](../teil-3-erweiterungsentwickler/ladereihenfolge-der-erweiterungen.md).

## Beispiel: ein anderes Template für einen Block

Die ursprüngliche Erweiterung stellt einen Block mit `design/standard/templates/theoriginal/block.tpl` dar. Sie
möchten anderes Markup. In Ihrer Erweiterung:

```text
extension/mysite/design/mysite/templates/theoriginal/block.tpl
```

wobei Ihr Design (`mysite`) in der Design-Liste des Siteaccess vor `standard` steht. Die Datei des Originals bleibt,
wie sie ist; Ihre wird zuerst gefunden. Wenn Sie es nur für eine Inhaltsklasse brauchen, verwenden Sie stattdessen eine
Override-Regel, damit das Template des Originals weiterhin für die anderen dient.

## Beispiel: eine geänderte Einstellung

Das Original liest `theoriginal.ini [ListSettings] Limit=10`. In Ihrer Erweiterung:

```ini
<?php /* #?ini charset="utf-8"?

[ListSettings]
Limit=25

*/ ?>
```

gespeichert als `extension/mysite/settings/theoriginal.ini.append.php`. Wenn Ihre Erweiterung in der Ladereihenfolge
nach dem Original steht, ist `Limit` 25.

## Beispiel: eine Unterklasse

Wenn das Original seine Klassen in Einstellungen nennt (das tun viele: Handler, Blocktypen, Konverter), registrieren
Sie dort eine eigene Unterklasse:

```php
<?php
class mySiteListHandler extends theOriginalListHandler
{
    public function title()
    {
        return 'Latest from ' . parent::title();
    }
}
```

Überschreiben Sie nur die Methoden, die Sie brauchen, und rufen Sie für den Rest `parent::` auf. Generieren Sie nach
dem Hinzufügen der Klasse die Autoloads neu (`php bin/php/ezpgenerateautoloads.php -e`). Wenn das Original die Klasse
direkt mit `new` erzeugt und sie nirgends nennt, kann eine Unterklasse sie nicht ersetzen, und Sie haben einen guten
Grund gefunden, den Autor um eine Einstellung zu bitten ([Gute Issues schreiben](gute-issues-schreiben.md)).

## Wenn nichts davon funktioniert

Dann müssen Sie den Code des Originals ändern. Es gibt immer noch zwei Wege, die keinen Fork bedeuten:

1. **Bitten Sie um einen Erweiterungspunkt.** Eröffnen Sie ein Issue, das erklärt, was Sie ändern möchten, und eine
   Einstellung, ein Event oder eine Template-Variable vorschlägt. Besser: Schicken Sie es als Pull Request
   ([Mit Pull Requests zurückgeben](mit-pull-requests-zurueckgeben.md)). Sobald er übernommen ist, erweitern Sie, ohne
   zu forken.
2. **Warten Sie in einem Branch Ihres Forks darauf**, solange der Pull Request offen ist, und kehren Sie zum Original
   zurück, sobald es veröffentlicht ist ([Wann Sie forken sollten und wann nicht](wann-forken.md)).

## Verwandte Seiten

- Zurück: [Teil 5: Erweitern, fair forken und zurückgeben](README.md)
- Weiter: [Wann Sie forken sollten und wann nicht](wann-forken.md)
- [RAD extension points](../../../bc/6.0/rad-erweiterungspunkte.md) (auf Englisch)
- [Ladereihenfolge der Erweiterungen](../teil-3-erweiterungsentwickler/ladereihenfolge-der-erweiterungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
