---
title: View-Ergebnisse, der Pfad und das Pagelayout
audience: developer, app-creator, designer
category: extension-development
order: 120
summary: Was eine View in $Result zurückgibt - Inhalt, Pfad, Navigationsteil, ein anderes oder kein Pagelayout -, wie das Pagelayout es als $module_result erhält und wie Sie statt mit einer Seite mit JSON oder einer Datei antworten.
keywords: Ergebnis, Modulergebnis, Inhalt, Pfad, Navigationsteil, Pagelayout, Brotkrumen, Popup, JSON, Result, module result, module_result, content, path, navigation_part, pagelayout, pagelayout false, content_info, persistent_variable, cleanExit, popup, breadcrumb
type: help
---
# View-Ergebnisse, der Pfad und das Pagelayout

Teil 3, Kapitel 12. Eine View gibt keine Seite aus. Sie gibt ein **Ergebnis** zurück, und der Kernel bettet es in das
**Pagelayout** ein, das Template des Rahmens der Website (Kopfbereich, Menü, Fußzeile). Dieses Kapitel führt auf, was ein
Ergebnis enthalten kann und wie das Pagelayout es verwendet. Der Code ist `ezpKernelWeb`
(`kernel/private/classes/ezpkernelweb.php`).

## Das Ergebnis-Array

| Schlüssel | Bedeutung |
|---|---|
| `content` | das HTML des Seiteninhalts, meist `$tpl->fetch( 'design:...' )` |
| `path` | die Brotkrumennavigation: eine Liste von `array( 'text' => ..., 'url' => ... )`, `url` ist `false` für die aktuelle Seite |
| `navigation_part` | welcher obere Reiter der Administration aktiv ist (sonst das `default_navigation_part` der View) |
| `pagelayout` | `false` für keinen Rahmen; ein Template-Name, um einen anderen Rahmen zu verwenden |
| `content_info` | bei Inhalts-Views Informationen über den angezeigten Knoten (Klasse, Sektion, Knoten-ID ...) |

```php
<?php
$Result = array();
$Result['content'] = $tpl->fetch( 'design:hello/world.tpl' );
$Result['path'] = array(
    array( 'text' => ezpI18n::tr( 'mysite_hello', 'Hello' ), 'url' => '/hello/list' ),
    array( 'text' => $name, 'url' => false )
);
```

Eine als Klasse geschriebene View gibt dasselbe Array aus `run()` zurück.

## Im Pagelayout: $module_result

Das Pagelayout erhält das Ergebnis als `$module_result`, mit Schlüsseln, die der Kernel hinzufügt: `uri`, `ui_context`,
`ui_component`, `is_mobile_device`. Es gibt den Inhalt so aus:

```text
{$module_result.content}
```

(ohne `wash`: Es ist HTML, das die View erzeugt hat), und die Brotkrumen aus `$module_result.path`. Das Pagelayout
eines Designs ist `templates/pagelayout.tpl`; die Designs der Administration haben ihr eigenes.

## Ein anderer Rahmen oder keiner

```php
<?php
$Result['pagelayout'] = false;                    // only the content: a fragment, a popup body
$Result['pagelayout'] = 'popup_pagelayout.tpl';   // another frame, found as design:popup_pagelayout.tpl
```

Ein Wert mit `:` wird als vollständige Template-Ressource verwendet. Das Menü des Inhaltsbaums im Kernel selbst gibt
`$Result['pagelayout'] = false` zurück (`kernel/private/classes/views/content/treemenu.php`).

## Mit JSON oder einer Datei antworten

Für Daten statt einer Seite schreiben Sie die Ausgabe und beenden den Request:

```php
<?php
header( 'Content-Type: application/json; charset=utf-8' );
echo json_encode( array( 'name' => $name, 'count' => $count ) );
eZExecution::cleanExit();
```

`eZExecution::cleanExit()` führt das Herunterfahren des Kernels ordnungsgemäß aus. Unter Velocity beendet es den
Request, indem es eine Ausnahme wirft; **umschließen Sie einen Download und `cleanExit()` nie mit
`try { } catch ( Exception $e )`**, sonst wird die Seite an Ihre Ausgabe angehängt. Für Ajax-Aufrufe ist meist eine
ezjscore-Serverfunktion das bessere Werkzeug:
[Ajax mit ezjscore-Serverfunktionen](ajax-mit-ezjscore-serverfunktionen.md).

## Titel und Metadaten

Den Seitentitel im Browser bildet das Pagelayout aus dem Pfad (in manchen Designs `title_path`) und dem Namen der
Website. Eine View, die einen bestimmten Titel möchte, setzt ihn in das letzte Pfadelement.

## Inhalts-Views und persistente Variablen

Inhalts-Views (`node/view/full.tpl` und Overrides) können mit
`{set scope=global persistent_variable=hash( 'left_menu', false() )}` Werte für das Pagelayout setzen; sie kommen in
`$module_result.content_info.persistent_variable` an. Modul-Views legen das, was das Pagelayout braucht, einfach in das
Ergebnis oder in Template-Variablen.

## Verwandte Seiten

- Zurück: [Richtlinienfunktionen und Zugriffsprüfungen](richtlinienfunktionen-und-zugriff.md)
- Weiter: [Template-Operatoren](template-operatoren.md)
- [eZModule und Modulergebnisse](../teil-8-referenz/ezmodule.md)
- [Fehlercodes und Modulergebnisse](../teil-8-referenz/fehlercodes-und-modulergebnisse.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
