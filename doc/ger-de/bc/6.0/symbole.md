# Icons aus Erweiterungen (6.0.15)

Lesen Sie diese Seite, wenn Ihre Website `bciconextensions` oder `bciconextensions_share_icons` verwendet oder wenn Sie
ein eigenes Icon-Theme in einer Erweiterung ausliefern möchten. Die beiden Erweiterungen wurden in den Kernel von
Exponential 6.0.15 übernommen, sodass ein Icon-Theme jetzt in jeder beliebigen Erweiterung liegen kann und die
Erweiterungen selbst ausgeschaltet werden können.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | `eZWordToImageOperator` sucht Icon-Themes in Erweiterungen (`icon.ini [ExtensionSettings] IconExtensions[]`) vor `share/icons/`, folgt einer Fallback-Kette von Themes und fällt auf das Icon `Default` zurück statt auf ein kaputtes `<img>`. Ein Klassen-Icon `job` wird im Theme `crystal-admin` mitgeliefert. |
| Wer betroffen ist | Websites, die `bciconextensions` oder `bciconextensions_share_icons` aktivieren oder ein Kernel-Override von `kernel/common/ezwordtoimageoperator.php` behalten. Eigene vhosts, die `/extension/*/icons/*` an `index.php` senden. |
| Wie Sie es prüfen | `grep -n "IconExtensions\|StandardTheme" settings/icon.ini` |
| Wie Sie es beheben | Entfernen Sie die beiden Erweiterungen aus `ActiveExtensions[]` und das Kernel-Override; siehe [Upgrade von bciconextensions](#upgrade-von-bciconextensions). |

Ein Klassen-Override im Kernel und eine Einrichtung von `EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE` sind nicht mehr nötig.

## Upgrade von bciconextensions

1. Entfernen Sie `ActiveExtensions[]=bciconextensions` und `ActiveExtensions[]=bciconextensions_share_icons` aus Ihren
   Overrides.
2. Entfernen Sie jedes Kernel-Override von `kernel/common/ezwordtoimageoperator.php`.
3. Wenn Sie `job.png` von Hand nach `share/icons/crystal-admin/` kopiert haben: Der Kernel liefert es jetzt mit.
   Behalten Sie Ihre Kopie nur, wenn Sie sie geändert haben.
4. Erzeugen Sie die Autoloads neu und leeren Sie die Caches:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all
   ```

## Wie Icons gefunden werden

- **Kette der Themes**: das aktuelle Theme (`Theme` oder das Theme des aktuellen Designs, `DesignTheme[<SiteDesign>]`,
  gefolgt von `Theme`), dann jedes Theme aus `AdditionalThemeList[]`, dann `StandardTheme`.
- **Kette der Repositories**: für jedes Theme das Verzeichnis `icons/` jeder Erweiterung in `IconExtensions[]` (in der
  aufgeführten Reihenfolge), dann das Standard-Repository `share/icons/`.
- **Standard-Icon**: Passt nichts, wird das Icon `Default` des Themes oder des Overrides verwendet.
- **Statische Dateien**: Icons in Erweiterungen werden als gewöhnliche statische Dateien ausgeliefert; pro Bild läuft
  kein PHP.

## Einstellungen

Alle Einstellungen stehen in `settings/icon.ini` und lassen sich in `settings/override/` und pro Siteaccess
überschreiben.

| Datei | Block | Schlüssel | Standard | Geltungsbereich |
|---|---|---|---|---|
| `icon.ini` | `[IconSettings]` | `Repository` | `share/icons` | global oder Siteaccess |
| `icon.ini` | `[IconSettings]` | `Theme` | `crystal` | global oder Siteaccess |
| `icon.ini` | `[IconSettings]` | `StandardTheme` | `crystal` (letzter Fallback) | global oder Siteaccess |
| `icon.ini` | `[IconSettings]` | `AdditionalThemeList[]` | leer (vor `StandardTheme` durchsucht) | global oder Siteaccess |
| `icon.ini` | `[IconSettings]` | `DesignTheme[<design>]` | `bootstrap-icons` für `admin4` und `admin4l` (ersetzt `Theme`, solange das Design das `SiteDesign` ist; leer: aus) | global oder Siteaccess |
| `icon.ini` | `[ExtensionSettings]` | `IconExtensions[]` | leer (vor `Repository` durchsucht, die Reihenfolge zählt) | global oder Siteaccess |

```ini
[IconSettings]
Repository=share/icons
Theme=crystal
StandardTheme=crystal
#AdditionalThemeList[]
#AdditionalThemeList[]=crystal-admin

[ExtensionSettings]
IconExtensions[]
#IconExtensions[]=mythemeextension
```

Ein Siteaccess, der das Theme `crystal-admin` zusammen mit dem Theme einer Erweiterung verwendet:

```ini
# settings/siteaccess/sevenx_site_admin/icon.ini.append.php
[IconSettings]
Theme=crystal-admin
StandardTheme=crystal
AdditionalThemeList[]=crystal-admin

[ExtensionSettings]
IconExtensions[]=mycompany_icons
```

## Beispiel: ein Icon-Theme in einer Erweiterung ausliefern

1. Legen Sie die Verzeichnisstruktur an:

   ```
   extension/mycompany_icons/
     icons/
       crystal-admin/
         32x32/
           apps/
             job.png
         16x16_indexed/
           apps/
             job.png
         icon.ini
   ```

2. Legen Sie `extension/mycompany_icons/icons/crystal-admin/icon.ini` an:

   ```ini
   #?ini charset="utf-8"?
   [IconSettings]
   Sizes[]
   Sizes[normal]=32x32
   Sizes[small]=16x16_indexed

   [ClassIcons]
   # Default icon if no class identifier matches
   Default=mimetypes/empty.png
   ClassMap[job]=apps/job.png
   ```

3. Legen Sie `extension/mycompany_icons/settings/icon.ini.append.php` an:

   ```ini
   <?php /* #?ini charset="utf-8"?

   [ExtensionSettings]
   IconExtensions[]=mycompany_icons

   [IconSettings]
   Theme=crystal-admin
   StandardTheme=crystal
   AdditionalThemeList[]=crystal-admin

   */ ?>
   ```

4. Aktivieren Sie die Erweiterung in `settings/override/site.ini.append.php`:

   ```ini
   [ExtensionSettings]
   ActiveExtensions[]=mycompany_icons
   ```

5. Erzeugen Sie die Autoloads neu und leeren Sie die Caches:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all
   ```

Der Eintrag in `IconExtensions[]` lässt die Icon-Engine in `extension/mycompany_icons/icons/` vor `share/icons/`
suchen.

## Das Theme bootstrap-icons

`share/icons/bootstrap-icons/` ist ein zweites Icon-Theme der Administration neben `crystal-admin`, gemacht für das
Design admin4: Bootstrap Icons 1.13.2 (MIT, `LICENSE` daneben) als SVG, eine Datei für jede Größe
(`Sizes[normal]=32x32;svg`, der Teil nach dem Semikolon ist das Unterverzeichnis). Seine `icon.ini` ordnet dieselben
Klassen, MIME-Typen, Klassengruppen und anderen Icons zu wie `crystal-admin`, und mehr. Es folgt dem Design:
`settings/icon.ini` setzt

```ini
[IconSettings]
DesignTheme[]
DesignTheme[admin4]=bootstrap-icons
DesignTheme[admin4l]=bootstrap-icons
```

Solange das `SiteDesign` des Siteaccess (`site.ini [DesignSettings]`) ein Design mit einem Eintrag ist, ersetzt dessen
Theme `Theme`, und `Theme` wird direkt danach durchsucht. Ein Admin-Siteaccess mit `Theme=crystal-admin` zeichnet in
admin4 daher Bootstrap Icons, und `class_icon`, `mimetype_icon`, `classgroup_icon` und `icon` liefern für einen
Schlüssel, den das Theme nicht zuordnet, weiterhin das Icon von `crystal-admin`; ein Benutzer, der zu admin3 wechselt,
erhält wieder `crystal-admin`. Das eigene Theme eines Typs (`[ClassIcons] Theme` usw.) gewinnt weiterhin.
`DesignTheme[admin4]=` (leer) in der `icon.ini.append.php` eines Siteaccess schaltet es für admin4 aus, ein leeres
`DesignTheme[]` für jedes Design; ein eigenes, auf admin4 aufbauendes Design braucht einen eigenen Eintrag.

Früher in der Linie 6.0.15 war das Theme pro Admin-Siteaccess optional (`Theme=bootstrap-icons`,
`AdditionalThemeList[]=crystal-admin`). Diese Einstellung funktioniert weiterhin und liefert dieselben Icons; sie kann
bleiben oder entfallen. Das `icon_info` des Inhaltsbaums nicht: Es liest das erste gefundene Theme (Abbruch beim ersten
Theme-Verzeichnis) und liefert für eine Klasse, die es nicht zuordnet, das `Default` dieses Themes; eine eigene Klasse
gehört daher in `ClassMap`. Siehe
[das Design admin4: Icons](../../../eng-us/features/6.0/admin4-design.md#icons).

## Template-Operatoren

Die Operatoren sind dieselben wie bisher. Sie durchsuchen jetzt auch die Icon-Themes von Erweiterungen.

| Operator | Rückgabe |
|---|---|
| `{$mime_type\|mimetype_icon}` | Icon des MIME-Typs |
| `{$class_identifier\|class_icon}` | Icon der Klasse |
| `{$class_group_identifier\|classgroup_icon}` | Icon der Klassengruppe |
| `{$action_identifier\|action_icon}` | Icon der Aktion |
| `{$icon_identifier\|icon}` | allgemeines Icon |
| `{$country_code\|flag_icon}` | Flaggen-Icon eines Landes |
| `{icon_info('class')}` | Metadaten über das aufgelöste Icon-Theme |

```smarty
{* URL of the class icon of the current content class *}
<img src="{$node.class_identifier|class_icon('normal',,true())}" alt="">

{* The same icon as a full <img> tag *}
{$node.class_identifier|class_icon('normal')}

{* Small job icon from an extension theme *}
{'job'|class_icon('small')}
```

## Das mitgelieferte Icon job

Das Icon `job.png` von `bciconextensions_share_icons` ist jetzt Teil des Themes `crystal-admin`:

- `share/icons/crystal-admin/32x32/apps/job.png`
- `share/icons/crystal-admin/16x16_indexed/apps/job.png`
- `share/icons/crystal-admin/icon.ini` enthält `ClassMap[job]=apps/job.png`

Um es zu verwenden, machen Sie `crystal-admin` zum Theme des Siteaccess oder fügen es zu `AdditionalThemeList[]`
hinzu. Das Standard-Theme `crystal` enthält es nicht.

## Regeln für den Webserver

Icons unter `extension/<name>/icons/` sind statische Dateien, und die Icon-Operatoren verweisen den Browser direkt
darauf. Die Webserver-Regeln senden nur eine feste Liste von Asset-Pfaden als Datei und alles andere an `index.php`;
die Liste braucht also eine Regel dafür, sonst ist jedes solche Icon ein kaputtes Bild. Seit 6.0.15 (20. Oktober
2026) haben die mitgelieferten Regeln sie: `.htaccess_root`, `.htaccess_root_static`, jedes Beispiel unter
[Webserver-Beispiele](../../beispiele/webserver/README.md) und die Liste der statischen Pfade von Velocity,
FrankenPHP und dem eingebauten PHP-Server (`expVelocity::STATIC_PATHS`).

Die Regel nimmt nur SVG-, PNG- und GIF-Bilder, deren Name **genau eine Dateiendung** hat: ein Name wie `a.php.svg`
könnte sonst über ein `AddHandler`-Mapping von Apache an PHP gehen. Behalten Sie diese Einschränkung bei, wenn Sie
die Regel für einen anderen Server schreiben.

Apache `.htaccess` (Verzeichniskontext, die Zeile aus `.htaccess_root`), vor `RewriteRule .* index.php`:

```apache
RewriteRule ^extension/[^/]+/icons/[A-Za-z0-9_/-]+\.(svg|png|gif)$ - [L]
```

Virtueller Host von Apache (Serverkontext, der Pfad beginnt mit `/`; LiteSpeed nimmt `^/?`):

```apache
RewriteRule ^/extension/[^/]+/icons/[A-Za-z0-9_/-]+\.(svg|png|gif)$ - [L]
```

nginx, neben den anderen Asset-Locations:

```nginx
location ~ ^/extension/[^/]+/icons/[A-Za-z0-9_/-]+\.(svg|png|gif)$ { try_files $uri =404; }
```

Caddy, FrankenPHP und IIS: `extension/[^/]+/icons/[A-Za-z0-9_/-]+\.(svg|png|gif)$` als weitere Alternative des
Ausdrucks der statischen Pfade, wie es die Beispiele zeigen.

**Upgrade**: Eine installierte `.htaccess` oder eine eigene Serverkonfiguration wird von einem Upgrade nie
umgeschrieben. Kopieren Sie die Regel hinein, vor die Regel des Front-Controllers, wenn eine Erweiterung der Site
Icons mitbringt. Velocity und die erzeugte `Caddyfile` von FrankenPHP übernehmen sie aus dem Code beim nächsten
Neustart (`exp:velocity deploy`).

## In zwei Minuten prüfen

So rendern Sie das mitgelieferte Icon `job` auf der Kommandozeile mit einem temporären Template.

1. Legen Sie das Template an:

   ```bash
   cat > design/standard/templates/test_icon.tpl <<'TPL'
   {'job'|class_icon('normal',,true())}
   TPL
   ```

2. Rendern Sie es aus dem Document Root von Exponential. Das Snippet erzwingt das Theme `crystal-admin`, weil dieses
   Theme `job.png` mitliefert:

   ```bash
   php -r '
   $GLOBALS["eZCurrentAccess"] = array( "name" => "sevenx_site_admin" );
   require "autoload.php";

   $ini = eZINI::instance( "icon.ini" );
   $ini->setVariable( "IconSettings", "Theme", "crystal-admin" );
   $ini->setVariable( "IconSettings", "AdditionalThemeList", array( "crystal-admin" ) );

   $tpl = eZTemplate::factory();
   echo $tpl->fetch( "design:test_icon.tpl" ) . PHP_EOL;
   '
   ```

   Erwartete Ausgabe:

   ```text
   /share/icons/crystal-admin/32x32/apps/job.png
   ```

3. Für den vollständigen `<img>`-Tag ändern Sie das Template in `{'job'|class_icon('normal')}` und führen das Snippet
   erneut aus. Erwartete Ausgabe:

   ```html
   <img class="transparent-png-icon" src="/share/icons/crystal-admin/32x32/apps/job.png" width="32" height="32" alt="job" title="job" />
   ```

4. Entfernen Sie das temporäre Template:

   ```bash
   rm design/standard/templates/test_icon.tpl
   ```

Hat Ihre Website ein Objekt `job`, sehen Sie das Icon auch, wenn Sie dieses Objekt im Siteaccess `sevenx_site_admin`
ansehen.

## Dateien

| Datei | Änderung |
|---|---|
| `kernel/common/ezwordtoimageoperator.php` | Suche in Erweiterungen und Themes, Behandlung von Fallbacks, `IconExtensions[]` |
| `settings/icon.ini` | `StandardTheme`, `AdditionalThemeList[]`, `[ExtensionSettings] IconExtensions[]` |
| `share/icons/crystal-admin/icon.ini` | `ClassMap[job]=apps/job.png` |
| `share/icons/crystal-admin/16x16_indexed/apps/job.png` | neues Icon |
| `share/icons/crystal-admin/32x32/apps/job.png` | neues Icon |

Übernommene Upstream-Projekte: [bciconextensions](https://github.com/brookinsconsulting/bciconextensions) und
[bciconextensions_share_icons](https://github.com/brookinsconsulting/bciconextensions_share_icons).

## Verwandte Seiten

- [Icon-Themes in Erweiterungen](../../../eng-us/features/6.0/icon-themes-in-extensions.md)
- [Erweiterungen: Verhaltensänderungen](verhaltensaenderungen-von-erweiterungen.md)
- [Anleitung zum Upgrade](../../anleitungen/aktualisieren.md)
