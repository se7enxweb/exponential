# Aktualisierung auf 6.0.15: auf Abruf erzeugte Bild-Aliase

Exponential 6.0.15 erzeugt Bild-Aliase, wenn der Browser nach ihnen fragt, nicht beim Erzeugen der Seite. Diese Seite
sagt, was sich für eine bestehende Installation ändert, die auf die Linie 6.0.15 wechselt, was Sie selbst tun müssen
und wie Sie zurückgehen.

Referenz der Funktion: [Auf Abruf erzeugte Bild-Aliase](../../funktionen/6.0/bild-aliase-auf-abruf.md) ·
Webserver-Regeln: [Webserver-Rezepte für auf Abruf erzeugte Bild-Aliase](../../beispiele/webserver/bild-aliase-auf-abruf.md)
· Hilfe: [Bild-Aliase](../../hilfe/bild-aliase.md)

## Kurz gesagt

- **Auf Abruf ist nach der Aktualisierung der Standard.** `settings/image.ini [AliasSettings]
  AliasGeneration=on-demand`: Ein Template-Aufruf berechnet einen fehlenden Alias (URL, Größe), statt ihn
  umzuwandeln; der Alias wird erzeugt, wenn der Browser nach ihm fragt. Templates brauchen keine Änderung, die
  Datenbank keine Migration.
- **Zurückgehen ist eine Zeile**: `AliasGeneration=render` in `settings/override/image.ini.append.php`, dann den
  INI-Cache leeren. Es lässt sich jederzeit in beide Richtungen umschalten.
- **Ergänzen Sie die Rewrite-Regeln**, die ein fehlendes Bild der Ablage und `content/image` an das neue
  Einstiegsskript `index_image.php` geben (Apache: `.htaccess_root` erneut kopieren oder die vier Zeilen in den
  virtuellen Host setzen; nginx, LiteSpeed, Caddy: die Rezepte). Ohne sie funktioniert die Website, aber langsamer.
- **Nehmen Sie `index_image.php`** in jede Liste erlaubter Skripte im Wurzelverzeichnis auf, und
  `AddType image/avif .avif` dort, wo der Server AVIF nicht kennt.
- **Request Shield**: Geben Sie `content/image`, `index_image.php` und Fehltreffern in der Ablage ein eigenes Budget,
  sonst bekommen Seiten mit vielen neuen Bildern Antworten 429.
- **Themes mit eigenem Pagelayout** laden `exp_image_swap.js`, sonst bleiben Platzhalter bis zum Neuladen.
- Neu: Setup > Bild-Aliase mit den Richtlinien `setup/aliases` und `setup/managealiases`, der Befehl
  `exp:image:alias`, drei Jobtypen für expqueue, das Log `imagealias.log`, das Geheimnis `image_alias_signing`.

## Inhalt

1. [Was sich ändert](#was-sich-ändert)
2. [Was Sie tun](#was-sie-tun)
3. [Zurück zum Modus render](#zurück-zum-modus-render)
4. [Was Sie nach der Aktualisierung prüfen](#was-sie-nach-der-aktualisierung-prüfen)

## Was sich ändert

| Bereich | Vor 6.0.15 | Ab 6.0.15 |
|---|---|---|
| Ein Template fragt nach einem fehlenden Alias | beim Erzeugen der Seite umgewandelt | berechnet; erzeugt, wenn der Browser fragt (`content/image` oder `index_image.php`) |
| Die URL eines fehlenden Alias | sein Pfad in der Ablage | `/content/image/<signed token>/<file name>` (`OnDemandURL=token`); ein vorhandener Alias behält seine URL in der Ablage |
| Ein fehlendes Bild der Ablage | 404 vom Webserver | `index_image.php` erzeugt es (wenn der Pfad einen konfigurierten Alias eines veröffentlichten Bildes nennt) oder antwortet mit einem Platzhalter |
| Ein langsames oder fehlgeschlagenes Bild | ein kaputtes Bild oder eine langsame Seite | ein neutraler SVG-Platzhalter in der richtigen Größe; `exp_image_swap.js` tauscht das echte Bild ein |
| Kommandozeilenskripte | wandeln beim Erzeugen um | unverändert: immer Modus render |
| Velocity | wie PHP-FPM | wie PHP-FPM, auf Abruf (seine Worker folgen der Einstellung) |
| Ein Bild veröffentlichen | kein Alias erzeugt | die Aliase der neuen Bilder werden im Hintergrund erzeugt, wenn expqueue installiert ist (`PreGenerate=enabled`) |
| Warten auf den Alias eines anderen Prozesses | ohne Grenze | höchstens `[ImageConverterSettings] LockTimeout` Sekunden (60), eine Sperre je Alias-Datei statt je Original |
| Parallele Alias-Schreibvorgänge eines Bildes | der letzte Schreiber konnte die Einträge der anderen verwerfen | inzwischen gespeicherte Einträge werden zusammengeführt |
| Ein Objekt entfernen | Aliase anderer Siteaccesses konnten auf der Festplatte bleiben | jeder Alias des Bildes wird entfernt |

Neue Dateien und Namen:

- `index_image.php` im Wurzelverzeichnis der Installation (das leichte Einstiegsskript), die View `content/image`
  (keine Prüfung von Richtlinien, keine Sitzung; in `site.ini [RoleSettings] PolicyOmitList[]` und
  `[SiteAccessSettings] AnonymousAccessList[]`).
- `design/standard/javascript/exp_image_swap.js`, aufgeführt in `design.ini [JavaScriptSettings]
  FrontendJavaScriptList[]`.
- `settings/expqueue.ini` mit den Jobtypen `image.alias.generate`, `image.alias.bulk`, `image.alias.resync`.
- **Setup > Bild-Aliase** (`setup/aliases`, direkt nach Cache im Setup-Menü), der Feed `setup/aliasesfeed`, die
  Richtlinien `setup/aliases` und `setup/managealiases` und **Fortgeschritten > Bild-Aliase** im Kontextmenü des
  Inhaltsbaums.
- `./console exp:image:alias` (`bin/php/expimagealias.php`).
- `imagealias.log` im Log-Verzeichnis der Website.
- Das Geheimnis `image_alias_signing` (Setup > Geheimnisse), angelegt bei der ersten Verwendung;
  `image_alias_signing_previous` nach einem Wechsel.
- Cache-Verzeichnisse `var/<site>/cache/imagealias-locks`, `imagealias-slots`, `imagealias-staging`.
- `config.cluster.php` kann `CLUSTER_IMAGE_ALIAS_ON_DEMAND` definieren (standardmäßig an).
- Neue Einstellungen in `image.ini [AliasSettings]` (OnDemandURL, Delivery, MaxWait, DraftTokenLifetime,
  SigningKeyGrace, PreferredConverter, Negotiate, Placeholder, SwapScript, PreGenerate, PreGenerateAliasList,
  PreGenerateFormats, MaxParallel, BulkNice, CacheMaxAge), `[AliasStaging]`, `[AliasSource_pro]` und `Formats[]` je
  Alias-Block.

## Was Sie tun

1. **Aktualisieren Sie den Code** wie üblich (`./console exp:upgrade --apply --to=v6.0.15`), erzeugen Sie dann die
   Autoloads neu und leeren Sie die Caches; unter Velocity `./console exp:velocity deploy --kernel` (das startet
   Velocity neu, das die neuen Routen schreibt).
2. **Installieren Sie die Rewrite-Regeln** für Ihren Webserver:
   - Apache mit `.htaccess`: Sichern Sie Ihre eigenen Zeilen, dann erneut `cp .htaccess_root .htaccess`.
   - Apache mit `AllowOverride None`: im virtuellen Host vor der Durchreichung der Ablage
     `RewriteRule ^var/([^/]+/)?storage/images(-versioned)?/.* - [L]` ergänzen (in einem `<Directory>`-Block):

     ```apache
     RewriteCond %{REQUEST_FILENAME} !-f
     RewriteRule ^var/([^/]+/)?storage/images(-versioned)?/.* index_image.php [L]
     RewriteRule ^([^/]+/)?content/image/ index_image.php [L]
     RewriteRule ^index_image\.php - [L]
     ```

   - nginx, nginx vor Apache, LiteSpeed/OpenLiteSpeed, ein eigenes Caddyfile, ein Datenbank-Cluster:
     [die Rezepte](../../beispiele/webserver/bild-aliase-auf-abruf.md).
   - Velocity: nichts außer dem Neustart.
3. **Erlauben Sie das Skript**: Ergänzen Sie `_image` in einer Liste erlaubter Skripte im Wurzelverzeichnis (zum
   Beispiel `index(_rest|_treemenu|_cluster|_webdav|_soap|_image)?\.php`), wie für `index_webdav.php`.
4. **AVIF**: `AddType image/avif .avif` in einem virtuellen Host, der kein `.htaccess` liest; `avif` in der Liste der
   Endungen einer statischen nginx-Location vor Apache.
5. **Request Shield**: Übernehmen Sie `rules/exponential.rules` aus dem nächsten Release von `exp_request_shield`
   (Budget `EXP-IMAGES`) oder ergänzen Sie jetzt ein Bildbudget in Ihrer eigenen Regeldatei; dann `request-shield
   check` und `test` ([wie](../../beispiele/webserver/bild-aliase-auf-abruf.md#das-bildbudget-des-request-shield)).
6. **Ihr Theme**: Wenn sein Pagelayout eigene Script-Tags schreibt, laden Sie `exp_image_swap.js` (zum Beispiel
   `{ezscript( array( 'exp_image_swap.js' ) )}` am Ende der Seite). Wenn ein Template-Operator Bild-URLs selbst aus
   der Alias-Liste baut, lassen Sie ihn `$handler->imageAlias( $alias )['url']` nehmen.
7. **expqueue** (empfohlen): Mit laufender Job-Warteschlange erzeugt das Veröffentlichen die neuen Aliase im
   Hintergrund, und langsame Fehltreffer von Besuchern werden dort fertiggestellt. Ohne sie wird alles auf Abruf
   erzeugt.
8. **Stimmen Sie** für Seiten mit vielen Bildern **ab**: `MaxParallel` auf etwa die Hälfte der CPU-Kerne, `Formats[]`
   nur auf großen Aliasen, `PreGenerateAliasList[]`, wenn das Veröffentlichen zu viel Arbeit erzeugt
   ([Leistung und Abstimmung](../../funktionen/6.0/bild-aliase-auf-abruf.md#leistung-und-abstimmung)).
9. Öffnen Sie **Setup > Bild-Aliase** (oder `./console exp:image:alias status`) und sehen Sie sich die Prüfungen an.

## Zurück zum Modus render

```ini
# settings/override/image.ini.append.php
[AliasSettings]
AliasGeneration=render
```

Dann `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`, den Cache der Inhaltsansicht und unter Velocity
`./console exp:velocity deploy` (seine Worker behalten die Einstellungen ihres Warm-ups). Seiten werden wie vor 6.0.15
erzeugt: Der Template-Aufruf wandelt einen fehlenden Alias um. Auf Abruf erzeugte Aliase bleiben und werden genutzt;
WebP-/AVIF-Varianten bleiben auf der Festplatte, bis ihr Alias entfernt wird. Die Rewrite-Regeln dürfen bleiben:
`index_image.php` liefert weiterhin vorhandene Dateien und Platzhalter aus. Schalten Sie zurück, indem Sie die Zeile
entfernen.

## Was Sie nach der Aktualisierung prüfen

- Eine Seite mit Bildern, die nie gezeigt wurden: Jedes Bild erscheint (auf einem langsamen Server zuerst
  Platzhalter), und `imagealias.log` hat Zeilen mit `event=generate`.
- Ein Pfad in der Ablage für einen noch nicht erzeugten Alias antwortet mit dem Bild (die Regel ist vorhanden), eine
  Adresse, die nichts nennt, mit dem SVG-Platzhalter, 404 und `X-Image-Placeholder: 1` (die Befehle stehen unter
  [Prüfen](../../beispiele/webserver/bild-aliase-auf-abruf.md#prüfen)).
- Kein `Set-Cookie` in Bildantworten; kein 429 vom Request Shield auf einer Seite mit vielen Bildern.
- Eine `.avif`-Variante wird als `image/avif` gesendet.
- Ein `settings/override/image.ini.append.php`, das root gehört, lässt sich nicht aus Setup > Bild-Aliase speichern:
  Übergeben Sie es dem Benutzer des Webservers.
