---
title: "Lektion 11.3: Ein eigener REST-Provider"
audience: trainer, developer
category: training
order: 113
summary: Die Teilnehmenden lesen, wie ein REST-Provider versionierte Routen, einen View-Controller und einen Controller mit do-Aktionen deklariert, schreiben einen kleinen Provider, der die gespeicherten Grüße unter einem eigenen Präfix als JSON zurückgibt, registrieren ihn in rest.ini und vergleichen ihn mit dem, was der RAD-Assistent für REST-Provider schreibt. 60 Minuten.
keywords: entwickler, REST-provider, ezpRestProviderInterface, getRoutes, getViewController, ezpRestVersionedRoute, ezpMvcRailsRoute, ezpRestMvcController, ezpRestMvcResult, ezpRestJsonView, rest.ini ProviderClass, präfix, RAD-assistent REST-provider, developers course, REST provider, prefix, RAD REST provider wizard
type: help
---
# Lektion 11.3: Ein eigener REST-Provider

Wenn eine App Daten braucht, die die Inhaltsrouten nicht bieten, fügt eine Erweiterung einen **REST-Provider** hinzu:
ein eigenes Präfix, eigene Routen und Controller, mit der Authentifizierung, den Versionen und der Ausgabe des
Kernels. In dieser Lektion lesen die Teilnehmenden, wie der Provider von `ezprestapi` aufgebaut ist, schreiben einen
kleinen Provider, der die Grüße aus Lektion 9.3 zurückgibt, und vergleichen ihren Code mit dem des RAD-Assistenten.

> **Hinweis**
> Dies ist die anspruchsvollste Lektion des Kurses. Proben Sie sie auf Ihrer eigenen Installation, bevor Sie sie
> unterrichten; halten Sie die fertigen Dateien zum Austeilen bereit.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 11: Die REST-API](README.md) |
| Dauer | 60 Minuten (20 Präsentation, 40 Übung) |
| Niveau | Experten |
| Zielgruppe | Entwickler |
| Voraussetzungen | [Lektion 11.2](die-api-mit-einem-schluessel-aufrufen.md) und [Lektion 9.3](../modul-09-die-konsole-und-persistente-objekte/persistente-objekte.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die drei Teile eines Providers beschreiben: Routen, einen View-Controller und Controller;
- mit `ezpRestVersionedRoute` und `ezpMvcRailsRoute` eine versionierte Route deklarieren;
- eine Controller-Aktion `do<Action>()` schreiben, die ein `ezpRestMvcResult` zurückgibt;
- mit `rest.ini [ApiProvider] ProviderClass[<prefix>]` einen Provider für ein Präfix registrieren;
- in einem Controller Berechtigungen prüfen und den RAD-Assistenten **REST-Provider** finden.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: `kernel/private/rest/classes/interfaces/rest_provider.php`, `extension/ezprestapi/classes/rest_provider.php`,
`extension/ezprestapi/classes/view_controller.php`, `extension/ezprestapi/classes/controllers/content.php` und
[REST-API v1 und v2, Abschnitt 5](../../../../anleitungen/rest-api-v1-und-v2.md#5-konfiguration).

1. **Die Schnittstelle** (4 Minuten). `ezpRestProviderInterface` hat zwei Methoden: `getRoutes()` gibt benannte,
   versionierte Routen zurück; `getViewController()` gibt das Objekt zurück, das für ein Ergebnis eine Ansicht
   (JSON, Atom ...) wählt.
2. **Eine Route** (4 Minuten). `new ezpRestVersionedRoute( new ezpMvcRailsRoute( '/content/node/:nodeId',
   'ezp7xRestContentController', array( 'http-get' => 'viewContent' ) ), <versions> )`: ein Pfad mit Platzhaltern,
   die Controller-Klasse, eine Aktion pro HTTP-Methode und die Versionen, unter denen sie antwortet.
3. **Ein Controller** (4 Minuten). Eine Klasse, die `ezpRestMvcController` erweitert; die Aktion `viewContent` ist die
   Methode `doViewContent()`, die ein `ezpRestMvcResult` baut und `$result->variables` füllt. Platzhalter wie
   `:nodeId` stehen als Eigenschaften zur Verfügung.
4. **Registrierung** (3 Minuten). `ProviderClass[mysite]=mysiteRestProvider` in `rest.ini` lässt
   `/api/mysite/v1/...` über Ihren Provider antworten, mit der Authentifizierung des Kernels.
5. **Berechtigungen** (3 Minuten). Das Framework authentifiziert; was der Benutzer sehen darf, entscheidet der
   Controller. Der Inhalts-Controller lehnt mit 403 ab, wenn eine Richtlinie fehlt; tun Sie dasselbe mit
   `eZUser::currentUser()` und `hasAccessTo()`.
6. **Der Assistent** (2 Minuten). **Setup** > **RAD** listet **REST-Provider** (`/setup/handlerextension/restprovider`).

### Häufige Fragen

**Warum ein eigenes Präfix und keine Route in `ezp`?** Die Routen von `ezp` gehören `ezprestapi`; ein eigenes Präfix
hält Ihre API unabhängig von deren Releases.

**Wie werden Antworten gecacht?** Das Framework hat einen Antwort-Cache mit Einstellungen pro Controller-Aktion;
siehe `settings/rest.ini`.

**Kann ich etwas anderes als JSON zurückgeben?** Das entscheidet der View-Controller; `ezprestapi` gibt für seinen
Feed-Controller Atom zurück und sonst JSON.

### Stolperfallen

- **Keine Berechtigungsprüfung.** Authentifizierung ist keine Autorisierung. Prüfen Sie in jeder Aktion die
  Richtlinien des Benutzers.
- **Personenbezogene Daten in Antworten.** Geben Sie nur zurück, was der Client braucht; niemals E-Mail-Adressen
  anderer Benutzer.
- **Klasse nicht gefunden.** Provider und Controller sind Klassen: Führen Sie `ezpgenerateautoloads.php -e` aus.

## Praktische Übung

Auf Ihrer Schulungsinstallation, in `extension/mysite_hello`, mit der Klasse `mysiteGreeting` aus Lektion 9.3 und
einem API-Schlüssel aus Lektion 11.2 (erzeugen Sie einen neuen, falls Sie ihn widerrufen haben).

1. Lesen Sie die Routentabelle und den View-Controller von `ezprestapi`:
   `sed -n 1,60p extension/ezprestapi/classes/rest_provider.php` und `cat extension/ezprestapi/classes/view_controller.php`.
2. Legen Sie `classes/rest/mysiterestprovider.php` an:

   ```php
   <?php
   /**
    * REST provider of mysite_hello: GET /api/mysite/v1/greetings
    */
   class mysiteRestProvider implements ezpRestProviderInterface
   {
       public function getRoutes()
       {
           return array(
               'mysiteGreetings' => new ezpRestVersionedRoute(
                   new ezpMvcRailsRoute( '/greetings', 'mysiteRestController', array( 'http-get' => 'list' ) ),
                   1
               ),
           );
       }

       public function getViewController()
       {
           return new mysiteRestViewController();
       }
   }
   ```

3. Legen Sie `classes/rest/mysiterestviewcontroller.php` an:

   ```php
   <?php
   class mysiteRestViewController implements ezpRestViewControllerInterface
   {
       public function loadView( ezcMvcRoutingInformation $routeInfo, ezcMvcRequest $request, ezcMvcResult $result )
       {
           return new ezpRestJsonView( $request, $result );
       }
   }
   ```

4. Legen Sie `classes/rest/mysiterestcontroller.php` an:

   ```php
   <?php
   class mysiteRestController extends ezpRestMvcController
   {
       public function doList()
       {
           $result = new ezpRestMvcResult();
           $access = eZUser::currentUser()->hasAccessTo( 'hello', 'greet' );
           if ( $access['accessWord'] === 'no' )
           {
               $result->status = new ezpRestHttpResponse( 403, 'Forbidden' );
               return $result;
           }
           $greetings = array();
           foreach ( mysiteGreeting::fetchByName( 'Ada', 20 ) as $greeting )
           {
               $greetings[] = array( 'id' => (int)$greeting->attribute( 'id' ),
                                     'created' => (int)$greeting->attribute( 'created' ) );
           }
           $result->variables['greetings'] = $greetings;
           return $result;
       }
   }
   ```

5. Prüfen Sie die drei Dateien mit `php -l` und führen Sie dann `php bin/php/ezpgenerateautoloads.php -e` aus.
6. Registrieren Sie den Provider:
   `php bin/php/console exp:ini set "rest.ini/ApiProvider/ProviderClass[mysite]" mysiteRestProvider extension:mysite_hello`,
   und leeren Sie den `ini`-Cache.
7. Rufen Sie ihn auf: `curl -s -H "Authorization: Bearer $EXP_API_KEY" http://127.0.0.1:8087/api/mysite/v1/greetings`.
8. Rufen Sie ihn ohne den Schlüssel auf und lesen Sie den Status.
9. Öffnen Sie **Setup** > **RAD**, wählen Sie **REST-Provider**, tragen Sie einen Testnamen ein, klicken Sie auf die
   Vorschau und vergleichen Sie die Dateien, die er schreiben würde, mit Ihren (schreiben Sie sie nicht).
10. Committen Sie die drei Klassen und die Einstellungen.

## Erwartetes Ergebnis

- Schritt 7: JSON mit einer Liste `greetings` der gespeicherten Grüße mit dem Namen Ada (der Benutzer braucht die
  Richtlinie `hello/greet` aus Lektion 6.3, sonst 403).
- Schritt 8: `401`.
- Schritt 9: Der Assistent listet eine Provider-Klasse, einen Controller und die Registrierung in `rest.ini`, wie in
  der Übung.
- Der Trainer ruft am Projektor die Route einer Person mit deren Schlüssel auf.

## Quiz

1. Welche zwei Methoden verlangt `ezpRestProviderInterface`?
2. Welche Methode des Controllers beantwortet die Aktion `list`?
   A. `list()` B. `doList()` C. `actionList()` D. `getList()`
3. Welche Einstellung registriert einen Provider für das Präfix `mysite`?
4. Wer entscheidet, was der Benutzer sehen darf: das Framework oder der Controller?
5. Warum verwendet eine Erweiterung ein eigenes Präfix, statt Routen zu `ezp` hinzuzufügen?

<details>
<summary>Antworten</summary>

1. `getRoutes()` und `getViewController()`.
2. **B.** `doList()`.
3. `rest.ini [ApiProvider] ProviderClass[mysite]=mysiteRestProvider`.
4. Der Controller: Das Framework authentifiziert, der Controller prüft die Richtlinien.
5. Die Routen von `ezp` gehören `ezprestapi`; ein eigenes Präfix hält die API unabhängig von den Releases dieser
   Erweiterung.

</details>

## Weiterführende Literatur

- [REST-API v1 und v2](../../../../anleitungen/rest-api-v1-und-v2.md).
- [RAD-Werkzeuge](../../../../../eng-us/features/6.0/rad-tools.md) und [Erweiterungspunkte](../../../../../eng-us/bc/6.0/rad-extension-points.md).
- [API- und Protokollstandards](../../../../../eng-us/standards/apis-and-protocols.md).

## Verwandte Seiten

- [Modul 11: Die REST-API](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
