---
title: Responses, Weiterleitungen, Fehler und JSON
audience: web-framework-developer, developer
category: web-frameworks
order: 90
summary: Wie eine Modul-View antwortet - das $Result-Array statt eines Response-Objekts, das Pagelayout darum herum, Weiterleitungen mit redirectTo und sichere Weiterleitungsziele, Fehlerseiten mit handleError und eZError-Codes, JSON und andere Nicht-HTML-Antworten mit eZExecution::cleanExit sowie Header.
keywords: Response, Result, module_result, Pagelayout, Weiterleitung, redirectTo, redirectToView, eZRedirectManager, handleError, eZError, 404, 403, JSON, cleanExit, Header, Web-Framework-Entwickler, redirect, headers, web framework developer
type: help
---
# Responses, Weiterleitungen, Fehler und JSON

Diese Seite richtet sich an Entwickler, die aus einem Controller `Response`, `JsonResponse`, `redirect()` oder
`abort(404)` zurückgeben. Eine Modul-View gibt ein **Array** zurück, und der Kernel macht daraus die Seite.
Weiterleitungen und Fehler laufen über das Modulobjekt; Nicht-HTML-Antworten werden direkt geschrieben und beenden den
Request.

## Inhalt

1. [Das Ergebnis-Array](#das-ergebnis-array)
2. [Das Pagelayout](#das-pagelayout)
3. [Weiterleitungen](#weiterleitungen)
4. [Fehler](#fehler)
5. [JSON und andere Formate](#json-und-andere-formate)
6. [Header und Statuscodes](#header-und-statuscodes)
7. [Übersetzungstabelle](#übersetzungstabelle)

## Das Ergebnis-Array

| Schlüssel | Bedeutung |
|---|---|
| `content` | das HTML der View, meist `$tpl->fetch( 'design:<path>.tpl' )` |
| `path` | die Brotkrumennavigation: Einträge `array( 'text' => ..., 'url' => ... )`, der letzte mit `'url' => false` |
| `title_path` | der Pfad des Seitentitels, wenn er von `path` abweichen soll |
| `pagelayout` | ein anderes Pagelayout-Template für diese View oder `false` für keines |
| `navigation_part`, `ui_context` | wie die Administrations-Designs die Seite einrahmen |

Die vollständige Liste mit den Schlüsseln, die der Kernel und die Inhalts-Views ergänzen, finden Sie in der
[Referenz des Modulergebnisses](../template-sprache/referenz-des-modulergebnisses.md).

## Das Pagelayout

Die `pagelayout.tpl` des Siteaccess ist das Grundlayout. Es erhält `$module_result` und gibt
`{$module_result.content}` an der Stelle aus, an der das HTML der View steht. Eine View wählt ein anderes Layout, indem
sie dessen Template nennt (die Benutzer-Views setzen `$Result['pagelayout'] = 'loginpagelayout.tpl'`), oder keines
mit `false` ([View-Ergebnisse, Templates und das Pagelayout](../ersteller/anwendungsentwickler/ergebnisse-von-ansichten-und-das-pagelayout.md)).

## Weiterleitungen

| Bedarf | Aufruf |
|---|---|
| Auf eine Adresse weiterleiten | `return $Module->redirectTo( '/expdemo/list' );` |
| Auf eine andere View desselben Moduls weiterleiten | `return $Module->redirectToView( 'list', array() );` |
| Auf eine Adresse aus dem Request weiterleiten | zuerst prüfen: `eZRedirectManager::safeURI( $http->postVariable( 'RedirectURI' ) )` gibt das Ziel oder `false` zurück |
| „Zurück, woher Sie kamen“ | `eZRedirectManager::returnURI( $module, '/expdemo/list', eZRedirectManager::formReturnURIs() )` |

Verwenden Sie nach jedem erfolgreichen Absenden eines Formulars das Muster POST, Weiterleitung, GET. Die Hilfsfunktionen
für sichere Weiterleitungen folgen nur Adressen der eigenen Website
([Sichere Weiterleitungen](../../funktionen/6.0/sichere-weiterleitungen.md)).

## Fehler

```php
return $Module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );       // not found
return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );   // access denied; anonymous users get the login page
```

`eZError` definiert weitere Codes, darunter `KERNEL_NOT_AVAILABLE`, `KERNEL_MOVED`, `KERNEL_MODULE_NOT_FOUND` und
`KERNEL_FORM_TOKEN_REFUSED`. Die Fehlerseite wird vom Modul `error` mit dem Design des Siteaccess gerendert und lässt
sich daher wie jedes andere Template gestalten. Fehler, die Benutzer melden, finden Sie anhand ihrer Referenz in den
Logs ([Einen Fehler anhand seiner Referenz finden](../../anleitungen/fehler-anhand-der-referenz-finden.md)).

Exceptions, die aus einer View entweichen, werden nicht wie in Symfony oder Laravel von einem konfigurierten
Exception-Listener in eine 500-Seite verwandelt: Behandeln Sie erwartete Fehlschläge in der View und geben Sie ein
Fehlerergebnis zurück; protokollieren Sie unerwartete mit `eZDebug::writeError()`
([Debugging, Logging und Fehler](debugging-logging-und-fehler.md)).

## JSON und andere Formate

Für eine kleine JSON-Antwort aus Ihrer eigenen View senden Sie sie und beenden den Request ohne Pagelayout:

```php
header( 'Content-Type: application/json; charset=utf-8' );
echo json_encode( array( 'ok' => true, 'data' => $data ) );
eZExecution::cleanExit();
```

Unter Velocity beendet `cleanExit()` den Request mit einer Exception, die der Server behandelt; umschließen Sie den
Aufruf niemals mit `catch ( Exception $e )`, sonst wird die Seite an Ihre Ausgabe angehängt. Für APIs sollten Sie die
vorhandenen Schichten bevorzugen: ezjscore-Serverfunktionen für das JavaScript Ihrer Seiten, die REST-API für andere
Programme ([REST- und JSON-APIs](rest-und-json-apis.md)).

## Header und Statuscodes

- Setzen Sie Header vor der Ausgabe mit der PHP-Funktion `header()`; lesen Sie Request-Header über `eZSys` oder über
  `$_SERVER`, so wie der Kernel es vorbereitet hat.
- Caching-Header für Seiten anonymer Besucher sind Einstellungen, kein Code: `site.ini [HTTPHeaderSettings]`
  ([HTTP-Caching](http-caching-und-reverse-proxies.md)).
- Request-Regeln können mit „nicht gefunden“, „verboten“, der Anmeldung oder einer Weiterleitung antworten, bevor Ihre
  View läuft ([Middleware](middleware-und-request-regeln.md)).

## Übersetzungstabelle

| Framework | Exponential |
|---|---|
| `return $this->render( 'item.html.twig', [...] )` / `return view( 'item', [...] )` | `$tpl->setVariable(); $Result['content'] = $tpl->fetch( 'design:...' );` |
| `return $this->redirectToRoute( 'list' )` / `return redirect()->route( 'list' )` | `return $Module->redirectTo( '/expdemo/list' );` |
| `throw $this->createNotFoundException()` / `abort( 404 )` / `raise Http404` | `return $Module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );` |
| `throw new AccessDeniedException()` / `abort( 403 )` | `return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );` |
| `return new JsonResponse( $data )` / `response()->json( $data )` | `header(); echo json_encode(); eZExecution::cleanExit();` |

## Verwandte Seiten

- [Controller: Modul-Views](controller-als-modul-views.md)
- [Eigene Module und Views](../ersteller/anwendungsentwickler/eigene-module-und-ansichten.md#weiterleitungen-und-fehler)
- [Formulare und CSRF-Schutz](formulare-und-csrf-schutz.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
