# Die REST-Schreibzugriffe über den Kernel, nxc_powercontent nicht mehr nötig (6.0.15, ezprestapi 1.2.9)

Lesen Sie diese Seite, bevor Sie eine Installation aktualisieren, die Inhalte über die REST API anlegt, ändert oder
entfernt, die `nxc_powercontent` installiert hat oder die Inhalte aus eigenem PHP-Code schreibt. Ab Exponential 6.0.15
schreibt der Kernel Inhalte aus einfachen Werten selbst, mit [expContentWriter](../../funktionen/6.0/inhalte-schreiben.md),
und `ezprestapi` 1.2.9 schreibt über ihn statt über `nxc_powercontent`.

## Kurz gesagt

| | Vorher (Kernel bis 6.0.14, ezprestapi bis 1.2.8) | Seither (Kernel 6.0.15, ezprestapi 1.2.9) |
|---|---|---|
| Wer REST-Inhalte schreibt | `nxc_powercontent` (`nxcPowerContent`), von `ezprestapi` verlangt | `expContentWriter` des Kernels; `ezprestapi` verlangt keine andere Extension |
| Ändern über REST | antwortete `501` (nicht implementiert) | `POST` oder `PUT /api/ezp/v2/content/node/<Knoten-ID>` veröffentlicht eine neue Version; `200`, oder `202`, wenn ein Workflow sie hält |
| Request-Body eines Schreibzugriffs | Formularfelder | Formularfelder oder ein JSON-Objekt (`Content-Type: application/json`) |
| Dateiwert | ein Pfad auf dem Server oder eine beliebige URL (Weiterleitungen verfolgt) | nur eine öffentliche `http(s)`-Adresse, ohne Weiterleitungen, höchstens 50 MB; ein Serverpfad antwortet mit `400` |
| Unbekannter Attributbezeichner | übergangen | `400 invalid_request` mit den Bezeichnern |
| Wert, der kein Text ist | geschrieben als das, was daraus wurde | `400 invalid_request` |
| Anlegen, das ein Workflow hält | `201` | `202` mit `"message":"Waiting for a workflow"` |
| Antwort beim Anlegen | `{"message","objectId","nodeId"}` | dieselbe plus `version` |
| Ein fehlgeschlagener Schreibzugriff | `500` | `500` für einen Fehler beim Schreiben (zurückgerollt), `400` für Eingaben, die sich nicht schreiben lassen |
| Ein Schreibzugriff auf einem älteren Kernel | | `501 not_implemented` (kein `expContentWriter`); Lesezugriffe funktionieren wie bisher |
| Eine Adresse, die keinen Provider nennt, ohne `ezprestapi` | Schleife fataler Fehler, `500` | `404` |
| PHP-Code, der Inhalte anlegt | von Hand, `eZContentFunctions` oder eine Extension | auch `expContentWriter::create()`, `update()`, `remove()` |

Unverändert: die Routen, die Lesezugriffe unter v1 und v2 und ihre Antworten, die Authentifizierung mit OAuth2-Tokens
und persönlichen API-Schlüsseln, die Berechtigungsprüfungen von 1.2.5, das Löschen (es entfernt jede Platzierung und
alles darunter, nicht in den Papierkorb, wie bisher).

## Was beim Aktualisieren zu tun ist

1. **Aktualisieren Sie zuerst den Kernel auf 6.0.15**, dann `ezprestapi`. Mit `ezprestapi` 1.2.9 auf einem älteren
   Kernel antworten die Schreibzugriffe mit `501`; Lesezugriffe funktionieren weiter.

   ```bash
   composer update se7enxweb/exponential se7enxweb/ezprestapi
   ```

   Die Wurzel-`composer.json` von 6.0.15 verlangt `se7enxweb/ezprestapi ~1.2.9`; eine eigene `composer.json` einer
   Website mit einer älteren Einschränkung (`~1.2.8`) muss zuerst angehoben werden.

2. **Lassen Sie Composer nxc_powercontent entfernen, oder behalten Sie es bewusst.** `ezprestapi` 1.2.9 verlangt
   `se7enxweb/nxc_powercontent` nicht mehr. Wenn nichts anderes es verlangt, entfernt das Aktualisieren oben es und mit
   ihm den Ordner `extension/nxc_powercontent`. Prüfen Sie vorher:

   ```bash
   composer why se7enxweb/nxc_powercontent
   grep -rn "nxc_powercontent\|nxcPowerContent" settings/ extension/*/classes extension/*/modules extension/*/bin 2>/dev/null | grep -v "^extension/nxc_powercontent/"
   ```

   - `composer why` nennt nur `se7enxweb/ezprestapi`, und `grep` findet nichts: Nichts von Ihnen verwendet es. Lassen
     Sie das Aktualisieren es entfernen.
   - Ihre eigene `composer.json` verlangt es: Es bleibt. Entfernen Sie es mit `composer remove se7enxweb/nxc_powercontent`,
     wenn Sie es nicht mehr brauchen.
   - Ihr eigener Code ruft `nxcPowerContent` auf, oder ein Template verwendet `powercontent_create_gui`: Behalten Sie es
     (verlangen Sie es in Ihrer eigenen `composer.json`) oder stellen Sie den Code auf `expContentWriter` um
     ([Code auf expContentWriter umstellen](#code-auf-expcontentwriter-umstellen)).

   `nxc_powercontent` ist nicht dieselbe Extension wie `powercontent` (`se7enxweb/powercontent`, die Werkzeuge zum
   Anlegen von Inhalten aus Templates). Die Wurzel-`composer.json` verlangt `powercontent` weiterhin; nichts auf dieser
   Seite ändert das.

3. **Nehmen Sie nxc_powercontent aus den Einstellungen**, wenn es weg ist. Entfernen Sie
   `ActiveExtensions[]=nxc_powercontent` (und ein etwaiges `ActiveAccessExtensions[]=nxc_powercontent`) aus
   `settings/override/site.ini.append.php` und den Siteaccesses. Solange es aktiv war, ersetzte es das Modul `content`
   des Kernels durch seine eigene Kopie (`ModuleSettings ExtensionRepositories[]=nxc_powercontent`,
   `ModuleList[]=content`); ohne es läuft das eigene Modul `content` des Kernels, das jede View dieser Kopie hat.

4. **Erzeugen Sie die Autoloads neu, leeren Sie die Caches und laden Sie PHP neu.** Der Autoload-Generator erfasst die
   Klassen jedes Ordners unter `extension/`, aktiv oder nicht, und `nxc_powercontent` enthält in seinem Ordner
   `modules/content` Kopien von Kernel-Klassen. Wenn der Ordner weg ist, erzeugen Sie neu, damit
   `var/autoload/ezp_override.php` keine davon mehr nennt:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e -o
   ./console exp:velocity deploy --allow-root-user     # INI- und Template-Caches, PHP-FPM neu laden, Velocity neu starten
   ```

   Ohne den Deploy-Befehl: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`, das PHP-FPM neu laden, das die
   Website ausliefert, und Velocity neu starten, wenn es läuft.

5. **Prüfen Sie Ihre REST-Clients** anhand der Tabelle oben. Was ein Client ändern muss:
   - ein Dateiwert, der einen Pfad auf dem Server nennt: Geben Sie stattdessen eine öffentliche `https`-Adresse der
     Datei an;
   - Felder, die keine Attribute der Klasse sind: Lassen Sie sie weg. Jedes Formularfeld eines Anlegens oder Änderns
     außer `parentNodeID`, `classIdentifier`, `languageLocale` und `_method` wird als Attributwert genommen; ein Client,
     der sein Token als Formularfeld `oauth_token` sendet, bekommt deshalb `400`; senden Sie das Token im Header
     `Authorization`;
   - ein Anlegen kann mit `202` antworten, wenn ein Workflow das neue Objekt hält: Behandeln Sie das als angenommen, nicht
     als Fehler;
   - ein Client, der das fehlende Ändern umgangen hat (entfernen und neu anlegen, oder ein Schreibzugriff über
     expservices), kann jetzt an Ort und Stelle ändern; das behält die Objekt-ID, die Knoten-ID, die URL und die Historie.

6. **Geben Sie Schlüsseln zum Ändern den Scope `edit`.** Ein persönlicher API-Schlüssel braucht für ein Ändern den Scope
   `edit` (`rest.ini [ApiKeySettings] RouteScopes[ezp7xRestContentController_UpdateContentNode]=edit`, ausgeliefert), und
   der Benutzer des Schlüssels braucht `content/edit` in der geschriebenen Sprache.

7. **Testen Sie einen Schreibzugriff.** Mit einem Schlüssel oder Token eines Testbenutzers:

   ```bash
   curl -s -X POST -H "Authorization: Bearer $EXP_API_KEY" -H "Content-Type: application/json" \
        -d '{"parentNodeID":2,"classIdentifier":"folder","languageLocale":"eng-US","name":"REST test"}' \
        https://www.example.com/api/ezp/v2/content/node/create
   # {"message":"Created","objectId":<object id>,"nodeId":<node id>,"version":1}
   curl -s -X PUT -H "Authorization: Bearer $EXP_API_KEY" -H "Content-Type: application/json" \
        -d '{"name":"REST test, updated"}' https://www.example.com/api/ezp/v2/content/node/<node id>
   # {"message":"Updated","objectId":<object id>,"nodeId":<node id>,"version":2,"languageLocale":"eng-US"}
   curl -s -X DELETE -H "Authorization: Bearer $EXP_API_KEY" \
        https://www.example.com/api/ezp/v2/content/node/delete/<node id>
   # {"message":"Removed","nodeId":<node id>,"objectId":<object id>}
   ```

   Ein `501` bedeutet, dass der Kernel älter als 6.0.15 ist; ein `403`, dass dem Schlüssel der Scope oder seinem
   Benutzer die Richtlinie fehlt.

## Code auf expContentWriter umstellen

| nxc_powercontent | expContentWriter |
|---|---|
| `$pc = new nxcPowerContent(); $pc->createObject( array( 'parentNode' => $node, 'class' => $class, 'languageLocale' => 'eng-US', 'attributes' => $values ) )` | `expContentWriter::create( array( 'parentNodeID' => $nodeID, 'classIdentifier' => 'article', 'languageLocale' => 'eng-US', 'attributes' => $values ) )` |
| gibt das Objekt oder `false` zurück | gibt `status`, `message`, `object`, `version` zurück |
| `$pc->updateObject( array( 'object' => $object, 'attributes' => $values ) )`: ändert die aktuelle Version an Ort und Stelle | `expContentWriter::update( $object, $values, 'eng-US' )`: veröffentlicht eine neue Version, die alte bleibt in der Historie |
| `$pc->removeObject( $object )` | `expContentWriter::remove( $object )` (oder `remove( $object, true )` für den Papierkorb) |
| Dateiwerte: ein Serverpfad oder eine beliebige URL | eine öffentliche `http(s)`-Adresse; für Dateien auf dem Server `eZContentFunctions` mit `storage_dir` |
| Rich Text als HTML | Rich Text als HTML (der Parser des Online Editors; Links und eingebettete Objekte werden zu Relationen) |
| `parentNode` beim Ändern verschiebt das Objekt; `additionalParentNodeIDs` setzt weitere Platzierungen; `visibility` verbirgt | nicht Teil der Klasse: `eZContentObjectTreeNodeOperations::move()`, `eZContentOperationCollection::addAssignment()`, `eZContentObjectTreeNode::hideSubTree()` |
| Debug-Ausgabe auf der Kommandozeile | die `message` der Antwort; Fehler im Debug-Log, gehaltene Versionen in `var/log/contentwriter.log` |

Die Anleitung enthält vollständige Beispiele: [Inhalte aus PHP schreiben mit expContentWriter](../../anleitungen/inhalte-aus-php-schreiben.md).

## Code und Tests

- `kernel/classes/contentwriter/expcontentwriter.php` (Kernel-Commit "Added: expContentWriter creates, updates and
  removes content from plain values", 12. November 2026); Tests ohne Datenbank in
  `tests/tests/kernel/classes/contentwriter/expContentWriterTest.php` (CW-01 bis CW-06).
- `extension/ezprestapi/classes/controllers/content.php` (`doCreateContentNode`, `doUpdateContentNode`,
  `doDeleteContentNode`) und `classes/rest_provider.php` (die Route `ezpNodeUpdate`, `POST` und `PUT`), ezprestapi 1.2.9.
- `kernel/private/rest/classes/router.php`: Eine Adresse ohne Provider erreicht die Fehlerroute, wenn der Standard-Provider
  `ezp` fehlt; Tests des Routen-Matchings RM-08 und RM-12 in
  `tests/tests/kernel/private/restapi/ezpRestRouteMatchingTest.php`.

## Verwandte Seiten

- [Inhalte aus einfachen Werten: expContentWriter](../../funktionen/6.0/inhalte-schreiben.md)
- [Neu in 6.0.15: Die REST API ändert Inhalte](../../hilfe/neu-in-6-0-15-rest-api.md)
- [REST API v1 und v2](../../anleitungen/rest-api-v1-und-v2.md), [Persönliche API-Schlüssel](../../anleitungen/api-schluessel.md)
- [ezprestapi](../../funktionen/6.0/erweiterungen/ezprestapi.md), [nxc_powercontent](../../funktionen/6.0/erweiterungen/nxc_powercontent.md)
- [Verhaltensänderungen der Erweiterungen](verhaltensaenderungen-von-erweiterungen.md)
- [Aktualisieren](../../anleitungen/aktualisieren.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
