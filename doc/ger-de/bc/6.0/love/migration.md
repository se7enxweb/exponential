# Eine Website von LoveStack umziehen

**Die meisten Websites müssen nichts tun.** Wenn Sie Exponential betreiben und LoveStack nie betrieben haben, betrifft
Sie nichts auf dieser Seite. Die Funktionen, die Exponential von LoveStack übernommen hat, sind Teil von 6.0.15 und
dort ausgeschaltet, wo sie das Verhalten einer vorhandenen Website ändern könnten.

Diese Seite ist für alle, die **LoveStack** betreiben (die Distribution von Mugo Web des Legacy-Kernels,
`mugoweb/ezpublish-legacy`) und eine Website auf Exponential 6 umziehen möchten, und für Entwickler, die in ihrem
eigenen Code eine API verwendet haben, die es nur in LoveStack gibt. Eine LoveStack-Datenbank ist der von Exponential
sehr ähnlich, daher ist der Umzug meist ein gewöhnliches Upgrade mit zwei zusätzlichen Befehlen. Die Anleitung zum
Upgrade beschreibt denselben Weg in Kürze: [Umstieg von LoveStack](../../../anleitungen/aktualisieren.md#umstieg-von-lovestack).

## Schritt 1 — Müssen Sie etwas tun?

Führen Sie dies im Wurzelverzeichnis Ihrer Installation aus:

```sh
grep -l -i "lovestack" lib/version.php composer.json composer.lock 2>/dev/null
```

| Was es ausgibt | Was es bedeutet | Was zu tun ist |
|---|---|---|
| **Nichts** | diese Installation ist kein LoveStack | nichts; Sie sind fertig |
| `lib/version.php` (und vielleicht `composer.*`) | diese Installation betreibt LoveStack | lesen Sie weiter |

Um statt des Codes eine Datenbank zu prüfen (zum Beispiel eine Kopie, die Sie erhalten haben):

```sql
SELECT name, value FROM ezsite_data
 WHERE name IN ( 'ezpublish-version', 'db-schema-version' );
```

`lovestack` und eine Zeile `db-schema-version` bedeuten eine LoveStack-Datenbank; eine Zahl wie `6.0.15stable`
bedeutet Exponential. Sobald Exponential installiert ist (Schritt 3), erledigt ein Befehl beide Prüfungen und führt
außerdem die Einstellungen und Erweiterungen auf, die Aufmerksamkeit brauchen:

```sh
./console exp:lovestack check
```

## Schritt 2 — Bevor Sie beginnen

1. **Sichern Sie** den Code, die Datenbank und das Speicherverzeichnis:

   ```sh
   tar czf ../site-code-backup.tar.gz --exclude=var/cache --exclude='var/*/cache' .
   mysqldump --single-transaction --routines mydatabase > ../site-db-backup.sql
   tar czf ../site-storage-backup.tar.gz var/storage var/*/storage
   ```

   Ersetzen Sie `mydatabase` (verwenden Sie `pg_dump` für PostgreSQL). Bewahren Sie die Backups auf, bis die
   umgezogene Website eine Weile gelaufen ist.

2. **Prüfen Sie PHP.** Exponential 6 läuft unter PHP 8.1 bis 8.5. Die `composer.json` von LoveStack erlaubt PHP
   7.3–7.4 und 8.1–8.3; eine Website unter PHP 7 zieht also gleichzeitig auf PHP 8 um: `php -v`.

3. **Erfassen Sie Ihren eigenen Code.** Alles, was Sie oder Ihre Agentur geschrieben haben, liegt in Ihren eigenen
   Erweiterungen, in `settings/override/` und in `settings/siteaccess/`. Das ist es, was Sie mitnehmen; der Kernel,
   `extension/ezjscore`, `extension/ezoe` und `extension/ezformtoken` kommen mit Exponential.

   ```sh
   ls extension/ settings/override/ settings/siteaccess/
   ```

## Schritt 3 — Exponential neben der alten Website installieren

Installieren Sie Exponential in einem **neuen** Verzeichnis, wie es die Installationsanleitung beschreibt, zum
Beispiel:

```sh
cd /var/www
composer create-project se7enxweb/exponential exponential
```

Entpacken Sie Exponential nicht über das LoveStack-Verzeichnis: Dateien, die LoveStack verschoben hat (zum Beispiel
der Datentyp `ezenum`, den LoveStack in `extension/legacy_2018` hält), lägen sonst an zwei Stellen. Kopieren Sie dann
Ihre eigenen Teile hinüber:

```sh
OLD=/var/www/lovestack-site
NEW=/var/www/exponential
cp -a $OLD/extension/myextension     $NEW/extension/       # once per own extension
cp -a $OLD/settings/override/.        $NEW/settings/override/
cp -a $OLD/settings/siteaccess/.      $NEW/settings/siteaccess/
cp -a $OLD/var/storage                $NEW/var/            # or var/<site>/storage
```

Führen Sie jetzt die Prüfung im neuen Verzeichnis aus, gegen die kopierten Einstellungen und die alte Datenbank:

```sh
./console exp:lovestack check
```

Jede Zeile ist markiert mit `FOUND` (etwas zu tun; die Zeile darunter sagt, was), `info` (vorhanden, und Exponential
funktioniert damit, wie es ist), `clear` (nicht vorhanden) oder `unknown` (die Engine gibt keine Auskunft). Sie endet
mit `OK` (Exit-Code 0) oder `NEEDS ATTENTION` (Exit-Code 1); `--json` liefert einen maschinenlesbaren Bericht. Sie
schreibt nichts.

## Schritt 4 — Einstellungen

### 4.1 `legacy_2018` entfernen

LoveStack hat den Datentyp `ezenum` in eine Erweiterung namens `legacy_2018` verschoben. Exponential behält `ezenum`
im Kernel, daher darf die Erweiterung nicht aktiv sein (zwei Klassen namens `eZEnumType` würden kollidieren):

```sh
grep -rn "legacy_2018" settings/override settings/siteaccess
```

Entfernen Sie jede Zeile `ActiveExtensions[]=legacy_2018` und `ActiveAccessExtensions[]=legacy_2018`, die der Befehl
ausgibt. Inhalte, die `ezenum` verwenden, funktionieren weiter. Kopieren Sie das Verzeichnis der Erweiterung nicht.

### 4.2 Einstellungen, die es nur in LoveStack gibt, umbenennen oder entfernen

`exp:lovestack check` führt sie auf, mit dem zu verwendenden Namen in Exponential. In Kürze:

| Einstellung in LoveStack | In Exponential |
|---|---|
| `site.ini [Session] SameSite=` | `site.ini [Session] CookieSameSite=` (Standard `Lax`; `None` braucht `Secure`). Bis Sie sie umbenennen, liest Exponential `SameSite` und protokolliert einen Deprecation-Hinweis |
| `site.ini [HTTPHeaderSettings] SetContentLengthHeader=` | entfernen: Velocity, FrankenPHP und der HTTP-Cache senden `Content-Length` selbst |
| `site.ini [DebugSettings] UsedTemplateEditUrl=` | gleicher Name und gleiche Bedeutung ([L10](../lovestack.md#l10--bearbeitungslinks-in-der-liste-der-verwendeten-templates)) |
| `site.ini [Cache_ezjscore] Maxage=` | entfernen: gepackte Dateien werden von `./console exp:cache` geleert |
| `image.ini [ValidUploadFormats] MIMEList[]` | `image.ini [ImageUploadSettings] AllowedMIMETypes[]` ([L05](../lovestack.md#l05--hochgeladene-bilder-werden-anhand-ihres-inhalts-geprüft)) |
| `error.ini [HTTPError-<code>] HeaderList[]` | gleicher Name und gleiche Bedeutung ([L29](../lovestack.md#l29--antwort-header-für-fehlerseiten)) |
| `ezfind.ini [SearchSettings] SortBy[]` (Suche von ezjscore) | gleicher Name und gleiche Bedeutung ([L30](../lovestack.md#l30--die-reihenfolge-der-suchergebnisse-in-den-dialogen-der-redakteure)) |
| `ezjscore.ini [eZJSCore] LocalScriptBasePath`, `[YUI3] LoaderOptions` | entfernen: Das ezjscore von Exponential liefert kein YUI aus |
| `ezjscore.ini [ezjscServer_ezjscnode] Functions[]` | dasselbe; um die Richtlinie zu verlangen, setzen Sie außerdem `RequirePolicy=enabled` ([L01](../lovestack.md#l01--knotendaten-aufrufe-können-eine-richtlinie-verlangen)) |
| `menu.ini [Leftmenu_my] PolicyList_edit_profile` | entfernen: vom Admin-Menü von Exponential nicht gelesen |
| `content.ini [CustomTagSettings] IsHidden[]` | gleicher Name und gleiche Bedeutung ([L22](../lovestack.md#l22--versteckte-custom-tags)) |
| `[Include path/*.ini]` in einer INI-Datei | gleiche Syntax ([L28](../lovestack.md#l28--include--in-einstellungsdateien)) |
| `ezoe.ini [EditorSettings] Plugins[]=custom_tags_controls`, `design.ini EditorCSSFileList[]=mugo_editor.css`, `EditorDialogCSSFileList[]=mugo_editor_dialog.css` (falls Ihre eigenen Einstellungen sie wiederholen) | entfernen; setzen Sie stattdessen `ezoe.ini [EditorSettings] CustomTagControls=enabled` ([L23](../lovestack.md#l23--bedienelemente-für-custom-tags-im-rich-text-editor)) |

### 4.3 Standardwerte für Berechtigungen

LoveStack verlangt für zwei Dinge eine Richtlinie, die eine aktualisierte Exponential-Website offen lässt. Um das
Verhalten von LoveStack zu behalten, setzen Sie beide in Ihren Overrides:

- **Registrierung.** Fügen Sie zu `settings/override/site.ini.append.php` hinzu:

  ```ini
  [RoleSettings]
  PolicyOmitListExclude[]=user/register
  ```

  und geben Sie der Rolle Anonymous *User / Register*, wenn Besucher sich weiterhin registrieren sollen
  ([L02](../lovestack.md#l02--die-registrierung-kann-eine-richtlinie-verlangen)).
- **Knotendaten-Aufrufe.** Fügen Sie zu `settings/override/ezjscore.ini.append.php` hinzu:

  ```ini
  [ezjscServer_ezjscnode]
  RequirePolicy=enabled
  ```

  Rollen, die unter LoveStack *ezjscore / call* mit `ezjscnode` hatten, funktionieren weiter
  ([L01](../lovestack.md#l01--knotendaten-aufrufe-können-eine-richtlinie-verlangen)).

## Schritt 5 — Datenbank

1. **Führen Sie das Upgrade-SQL von Exponential** für Ihre Engine aus, der Reihe nach. Das Schema von LoveStack geht
   vom Schema 5.4 des Legacy-Kernels aus, daher gelten beide Dateien von 6.0:

   ```sh
   mysql mydatabase < update/database/mysql/6.0/dbupdate-5.4.0-6.0.0.sql
   mysql mydatabase < update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql
   ```

   Für PostgreSQL:

   ```sh
   psql mydatabase < update/database/postgresql/6.0/dbupdate-5.4-to-6.0.sql
   psql mydatabase < update/database/postgresql/6.0/dbupdate-6.0.0-6.0.15.sql
   ```

   Die Spalten, die LoveStack hinzugefügt hat (`ezuser.password_hash` mit 255 Zeichen,
   `ezcontentobject_trash.trashed`), sind in Exponential dieselben und werden nicht angetastet.

2. **Tragen Sie die Datenbank als Datenbank von Exponential ein:**

   ```sh
   ./console exp:lovestack migrate            # dry run: prints what it would write
   ./console exp:lovestack migrate --apply
   ```

   Der Befehl setzt in `ezsite_data` `ezpublish-version` von `lovestack` auf die Version dieses Release
   (`6.0.15stable`), die die Upgrade-Prüfung und die Seite Über von Exponential lesen, und trägt die Schemaversion von
   LoveStack als `lovestack-schema-version` ein. Die eigene Zeile `db-schema-version` von LoveStack bleibt als
   Nachweis erhalten. Er ändert nie Inhalte, Benutzer oder Einstellungen; ein zweiter Lauf ändert nichts; eine
   Datenbank, die nicht von LoveStack stammt, wird abgelehnt. Unter den Zeilen gibt er die Einstellungen und
   Erweiterungen aus, die noch zu ändern sind, aus Schritt 4.

3. **Behalten Sie, was der Schemaschritt 4 von LoveStack hinzugefügt hat.** Der eindeutige Index auf `ezuser.login`
   bleibt: Exponential funktioniert damit, und neue Exponential-Installationen haben ihn ebenfalls
   ([L04](../lovestack.md#l04--ein-konto-pro-anmeldename)). Die Spalte
   `ezcontentobject_attribute.data_text NOT NULL DEFAULT ''` kann ebenfalls bleiben: Exponential speichert dort eine
   leere Zeichenkette, nie NULL; dieselben Inhaltsoperationen wurden auf einer solchen Datenbank und auf einer von
   Exponential mit denselben Ergebnissen getestet.

## Schritt 6 — Caches leeren und prüfen

```sh
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all
./console exp:lovestack check
```

Die Prüfung sollte jetzt mit `OK` enden. Öffnen Sie dann die Website und die Administration und prüfen Sie:

- die Startseite, einige Inhaltsseiten und eine Suche;
- An- und Abmelden in der Administration; ein Objekt mit einem Bild bearbeiten und veröffentlichen;
- `var/log/error.log` und `var/<site>/log/error.log` bleiben ruhig.

## Schritt 7 — Ihr eigener PHP-Code

Rufen Ihre Erweiterungen eine API auf, die es nur in LoveStack gibt, ist dies die von Exponential:

| API in LoveStack | API in Exponential |
|---|---|
| `eZURI::build( $url, $full )` | dieselbe, dazu `$htmlEscape` ([L25](../lovestack.md#l25--ezuribuild-baut-eine-site-url-in-einem-aufruf)) |
| `eZMailTransport::send( $mail, $transportType )` | dieselbe ([L26](../lovestack.md#l26--eine-nachricht-mit-einem-anderen-mail-transport)) |
| `eZTemplateDesignResource::setKeyValue( $key, $value, $override )` | dieselbe ([L31](../lovestack.md#l31--templates-können-layout-ansichten-unterscheiden)) |
| `eZSiteData::create()`, `fetchByName()`, `createNew()` | dieselben |
| `fetch( 'content', 'site_data', … )` in Templates | `fetch( 'content', 'site_data', hash( 'name', … ) )` für Namen, die in `site.ini [SiteDataSettings] TemplateReadable[]` aufgeführt sind ([L14](../lovestack.md#l14--site-daten-in-templates-und-in-der-administration)) |
| `eZDebug::writeDeprecated()`, `eZDebug::LEVEL_DEPRECATED` | dieselben ([L07](../lovestack.md#l07--php-deprecations-erhalten-ein-eigenes-log)) |
| `{MyResolver}` in einem Namensmuster einer Klasse, `eZNamePatternResolver::instance()` | dasselbe Muster; die Klasse implementiert `expNamePatternResolverInterface` und ist in `content.ini [NamePatternSettings] ResolverClasses[]` aufgeführt; Exponential behält den Konstruktor und fügt `eZNamePatternResolver::resolve()` hinzu ([L18](../lovestack.md#l18--von-einer-php-klasse-erzeugte-namen)) |
| Ganzzahl-Grenzen von `eZDBInterface` | `MAX_INT`, `MIN_INT` pro Treiber und `$db->clampInteger()` ([L34](../lovestack.md#l34--sehr-große-zahlen-halten-den-suchindex-nicht-mehr-an)) |
| Template-Operatoren `rawurlencode` / `rawurldecode` | dieselben ([L27](../lovestack.md#l27--die-template-operatoren-rawurlencode-und-rawurldecode)) |

Aufrufe in Ihrem Code finden:

```sh
grep -rnE "eZURI::build|setKeyValue\(|writeDeprecated|LEVEL_DEPRECATED|MAX_INT|MIN_INT|'site_data'|rawurl(en|de)code|eZNamePatternResolver::instance" \
     extension/myextension design/mydesign --include=*.php --include=*.tpl
```

## Über LoveStack

LoveStack wird von Mugo Web entwickelt und unter <https://github.com/mugoweb/ezpublish-legacy> sowie auf Packagist als
[`mugoweb/ezpublish-legacy`](https://packagist.org/packages/mugoweb/ezpublish-legacy) veröffentlicht. Seine eigenen
Hinweise zum Upgrade stehen in seiner
[`update/README.md`](https://github.com/mugoweb/ezpublish-legacy/blob/e61e9fdf0ca464197b79935ef784ed459721aa87/update/README.md).
Dank an Mugo Web, dessen dokumentierte Schemaschritte (`update/database/mysql/lovestack/1.sql` bis `4.sql`) es möglich
gemacht haben, eine LoveStack-Datenbank so genau zu erkennen.

Der Umzug in die andere Richtung oder der Betrieb von beiden ist nicht beschrieben: Die beiden Projekte teilen ihren
Ursprung, aber nicht ihre Release-Linien.
