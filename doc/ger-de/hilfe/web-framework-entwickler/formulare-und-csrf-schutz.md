---
title: Formulare und CSRF-Schutz
audience: web-framework-developer, developer
category: web-frameworks
order: 260
summary: Wie Formulare in Exponential funktionieren, für Entwickler, die Formular-Komponenten, Form Requests und CSRF-Middleware gewohnt sind - Templates, die das Formular schreiben, Modul-Aktionen, die es lesen, eZHTTPTool für Eingaben, das automatische Token von ezformtoken für angemeldete Benutzer, der Header X-CSRF-Token für Skripte, POST-Redirect-GET, Uploads, Formulare für Besucher und was keine direkte Entsprechung hat.
keywords: Formulare, forms, CSRF, Formular-Token, form token, ezformtoken, ezxFormToken, ezxform_token, X-CSRF-Token, HTMLForms Secret, eZHTTPTool, postVariable, Modul-Aktionen, module actions, single_post_actions, POST redirect GET, eZHTTPFile, Uploads, Informationssammler, information collector, Symfony Form, Django forms, Web-Framework-Entwickler, web framework developer
type: help
---
# Formulare und CSRF-Schutz

Diese Seite richtet sich an Entwickler, die Symfony-Formulartypen, Laravel Form Requests, Django-Formulare oder die
Formular-Helfer von Rails gewohnt sind. Exponential hat keine Formular-Komponente, die ein Formularobjekt aufbaut,
bindet und validiert. Ein Formular ist HTML in einem Template; die View liest die gesendeten Werte über die Hilfsmittel
des Kernels und **Modul-Aktionen**; und die Erweiterung `ezformtoken` schützt POST-Requests vor Cross-Site Request
Forgery, ohne dass Ihre Formulare dafür Code brauchen.

## Inhalt

1. [Die Bestandteile eines Formulars](#die-bestandteile-eines-formulars)
2. [CSRF-Schutz mit ezformtoken](#csrf-schutz-mit-ezformtoken)
3. [Eingaben lesen](#eingaben-lesen)
4. [POST, Weiterleitung, GET](#post-weiterleitung-get)
5. [Uploads](#uploads)
6. [Formulare für Besucher](#formulare-für-besucher)
7. [Im Vergleich mit anderen Frameworks](#im-vergleich-mit-anderen-frameworks)

## Die Bestandteile eines Formulars

| Bestandteil | Wo |
|---|---|
| Das HTML-Formular | Ihr Template, das an Ihre View sendet: `<form method="post" action={'expdemo/edit/42'\|ezurl}>` |
| Schaltflächen als Aktionen | `single_post_actions` in `module.php`: `array( 'StoreButton' => 'Store', 'CancelButton' => 'Cancel' )` |
| Werte je Aktion | `post_action_parameters`: `array( 'Store' => array( 'Title' => 'ItemTitle' ) )` |
| Lesen | `$Module->isCurrentAction( 'Store' )`, `$Module->actionParameter( 'Title' )` oder `eZHTTPTool` |
| Validierung und Fehler | Ihr Code; Fehler werden an das Template zurückgegeben ([Validierung](validierung.md)) |

Ein ausgearbeitetes Formular finden Sie in [Aufgabe: ein kleines CRUD-Modul bauen](ihr-erstes-modul-schritt-fuer-schritt.md).

## CSRF-Schutz mit ezformtoken

`ezformtoken` registriert zwei Listener ([Middleware](middleware-und-request-regeln.md#eingabe--und-ausgabe-listener)):

- `response/output`: fügt jedem Formular mit `method="post"` auf der Seite ein verstecktes Token-Feld hinzu, und zwar für
  einen **angemeldeten Benutzer mit Session**. Ihre Templates brauchen dafür nichts.
- `request/input`: weist einen POST eines solchen Benutzers ohne das richtige Token zurück, das aus dem Formularfeld oder
  aus dem Header `X-CSRF-Token` gelesen wird. Die Antwort ist eine 403-Seite „Dieses Formular ist abgelaufen“ (oder ein
  JSON-Body bei XHR- und JSON-Requests) und eine Zeile in `var/log/warning.log`, niemals das Token selbst
  ([Seite für abgelaufene Formulare](../../funktionen/6.0/seite-abgelaufenes-formular.md)).

Legen Sie pro Installation ein eigenes Geheimnis in `settings/override/site.ini.append.php` fest:

```ini
[HTMLForms]
Secret=<a long random value of your own>
```

JavaScript, das mit `fetch()` sendet, schickt das Token im Header `X-CSRF-Token`; lesen Sie es aus der Seite, statt die
Prüfung abzuschalten ([Formulare, CSRF-Schutz und HTTP-Eingaben](../ersteller/anwendungsentwickler/formulare-csrf-und-http-eingaben.md#formular-tokens)).

Anonyme Besucher haben keine Session, an die ein Token gebunden werden könnte, daher prüft `ezformtoken` ihre POSTs
nicht. Öffentliche Formulare werden mit anderen Mitteln geschützt: einem CAPTCHA aus der Erweiterung `recaptcha` oder
`hcaptcha` und serverseitiger Validierung
([Spamschutz für Formulare](../ersteller/website-ersteller/spamschutz-fuer-formulare.md)).

## Eingaben lesen

```php
$http = eZHTTPTool::instance();
if ( $http->hasPostVariable( 'Title' ) )
    $title = trim( (string)$http->postVariable( 'Title' ) );
$page = $http->hasGetVariable( 'page' ) ? max( 1, (int)$http->getVariable( 'page' ) ) : 1;
```

Lesen Sie `$_POST` oder `$_GET` nie direkt: Unter Velocity bereitet der Kernel sie für jeden Request auf.

## POST, Weiterleitung, GET

```php
if ( $Module->isCurrentAction( 'Store' ) && $errors === array() )
{
    $item->store();
    return $Module->redirectTo( '/expdemo/item/' . (int)$item->attribute( 'id' ) );
}
```

Weiterleitungsziele, die aus dem Request stammen, müssen auf der Site bleiben
([Sichere Weiterleitungen](../../funktionen/6.0/sichere-weiterleitungen.md)).

## Uploads

```php
if ( eZHTTPFile::canFetch( 'File' ) )
{
    $file = eZHTTPFile::fetch( 'File' );
    // check $file->attribute( 'mime_type' ) and size against an allow list before storing
}
```

Für Dateien, die zu Inhalten gehören, legen Sie stattdessen ein Objekt der Klasse `file` oder `image` an; es kümmert
sich um Speicherung, Clustering und die Bereinigung von SVG-Dateien
([Inhalte im Code anlegen und aktualisieren](../ersteller/anwendungsentwickler/inhalte-im-code-anlegen.md)).

## Formulare für Besucher

Kontaktformulare, Umfragen und Feedback brauchen meist kein PHP: Eine Inhaltsklasse mit Attributen als
**Informationssammler** sammelt die Antworten, und die Administrationsoberfläche listet sie auf
([Formulare mit dem Informationssammler](../ersteller/website-ersteller/formulare-mit-dem-informationssammler.md),
[Ein Kontaktformular bauen](../ersteller/website-ersteller/ein-kontaktformular-bauen.md)).

## Im Vergleich mit anderen Frameworks

| Framework | Konzept | Exponential |
|---|---|---|
| Symfony | Formulartypen, `handleRequest()`, CSRF-Option | Template plus Modul-Aktionen; `ezformtoken` |
| Laravel | Form Requests, `@csrf` | Ihre Validierung in der View; Token wird für angemeldete Benutzer automatisch hinzugefügt |
| Django | `Form`-Klassen, `{% csrf_token %}` | dieselbe Aufteilung; Informationssammler für einfache öffentliche Formulare |
| Rails | `form_with`, Strong Parameters | Aktionsparameter, die in `module.php` deklariert sind |

## Verwandte Seiten

- [Formulare, CSRF-Schutz und HTTP-Eingaben](../ersteller/anwendungsentwickler/formulare-csrf-und-http-eingaben.md)
- [Formulare in Templates](../designer/vorlagen/formulare-in-templates.md)
- [Responses, Weiterleitungen, Fehler und JSON](responses-weiterleitungen-und-fehler.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
