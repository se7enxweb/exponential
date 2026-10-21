# Links auf Inhalte einer anderen Website einer Multisite-Installation

Lesen Sie diese Seite, wenn eine Installation mehrere Websites aus einem Inhaltsbaum bedient (die Multisite-Demo: Fit &
Healthy unter `/site` oder `/`, Bold Agency unter `/bold`, ihre Übersetzungen) und eine Seite einer Website Inhalte einer
anderen auflistet: eine Tag-Seite, ein Suchergebnis, ein verwandtes Element. Ohne die Extension `ezurlmultisite` zeigen
diese Links in die falsche Website und antworten mit 404.

## Kurz gesagt

| | |
|---|---|
| Das Problem | Jede Website verbirgt den Pfad ihres Teilbaums mit `PathPrefix`. Ein Knoten einer anderen Website behält in seinem URL-Alias den Pfad dieser Website, und `ezurl` setzt den aktuellen Siteaccess davor: `/bold/fit-healthy/video/...`, was nicht existiert (`kernel (20)`, Modul nicht gefunden). |
| Die Lösung | Die Extension `ezurlmultisite` (`se7enxweb/ezurlmultisite`, verlangt von der `composer.json` im Wurzelverzeichnis). `ezurl` und die eigenen Link-Operatoren des Media-Themes (`exp_path`, `exp_url`, `content_link` ...) liefern die Adresse auf dem Siteaccess, der den Inhalt bedient: `/video/...` (oder `/site/video/...`). |
| Unverändert | Links innerhalb der aktuellen Website, Modul-Links (`user/login`, `content/search`), Dateien und Design-Pfade sowie jeder Link, der auf einem Siteaccess erzeugt wird, der nicht teilnimmt, und dazu gehört jeder Administrations-Siteaccess. |
| Konfiguration | `settings/override/ezurlmultisite.ini.append.php`, `[MultisiteSettings]`: `SiteAccessList[]` (die Websites in der Reihenfolge der Präferenz) und `RootNodeList[<siteaccess>]=<node id>` (der Wurzelknoten des Teilbaums jeder Website). Der Multisite-Installer schreibt sie mit den Knoten-IDs der Installation, die er anlegt. |
| Ohne die Datei | Die öffentlichen Siteaccesses mit einem `PathPrefix` nehmen teil, jeder mit dem Knoten seines `RootNode` aus `content.ini` oder seiner `IndexPage`. |
| Kosten | Keine Datenbankabfrage pro Link; die Siteaccess-Tabelle wird einmal pro Anfrage gelesen (unter Exponential Velocity einmal pro Worker). |

## Wie ein Link aufgelöst wird

1. Der Link wird auf einem Siteaccess aus `SiteAccessList[]` erzeugt; andernfalls ist `ezurl` das des Kernels.
2. Bei einem Knoten oder Objekt ist die Website diejenige, deren Wurzelknoten am tiefsten auf dem Pfad des Knotens liegt
   (`RootNodeList`). Bei einer URL-Alias-Zeichenkette (`$node.url_alias|ezurl`, der übliche Fall) ist es die Website,
   mit deren `PathPrefix` der Alias beginnt, das längste Präfix zuerst.
3. Ist das eine andere Website, wird die Adresse aus der `SiteURL` dieses Siteaccess gebaut, ohne sein `PathPrefix`:
   relativ zum Host auf dem aktuellen Host (`/bold/services`, sodass Schema und Port der Seite erhalten bleiben, zum
   Beispiel auf `:8080` von Velocity), absolut auf einem anderen Host (`https://bold.example.com/services`).
4. Eine Website und ihre Übersetzungen teilen sich einen Teilbaum. Der aktuelle Siteaccess gewinnt, dann einer in der
   aktuellen Locale, dann der erste in `SiteAccessList[]`.

## Auf einer bestehenden Website installieren

```ini
# settings/override/site.ini.append.php
[ExtensionSettings]
ActiveExtensions[]=ezurlmultisite
```

```ini
# settings/override/ezurlmultisite.ini.append.php (the node ids of YOUR installation)
[MultisiteSettings]
SiteAccessList[]
SiteAccessList[]=site
SiteAccessList[]=bold
SiteAccessList[]=bold_ger
RootNodeList[]
RootNodeList[site]=97
RootNodeList[bold]=81
RootNodeList[bold_ger]=81
```

Erzeugen Sie dann die Extension-Autoloads neu und leeren Sie die INI-, Template- und Inhalts-Caches: Kompilierte
Templates enthalten den Code von `ezurl` des Kernels, bis sie neu kompiliert werden.

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
php bin/php/ezcache.php --clear-tag=template --allow-root-user
php bin/php/ezcache.php --clear-id=content --allow-root-user
```

Unter Exponential Velocity starten Sie es neu (`./console exp:velocity deploy` erledigt alles oben Genannte).

Die Knoten-IDs unterscheiden sich zwischen Installationen; kopieren Sie sie nie aus einer anderen. Um Ihre zu finden:
Der Wurzelknoten einer Website ist der Knoten der `IndexPage` ihres Siteaccess (`/content/view/full/<id>`) oder
`content.ini [NodeSettings] RootNode`; der Template-Operator `ezurl_roots()` listet auf, was die Extension verwendet.

## Neue Installationen

Die Multisite-Installer (`sevenx_multisite`, `sevenx_multisite_clean`, `cjw_multisite`) aktivieren die Extension und
führen den Schritt nach der Installation `postInstallWriteMultisiteURLSettings` aus, der die Siteaccess-Dateien liest,
die die Installation gerade geschrieben hat (PathPrefix, `RootNode`, `IndexPage`), und
`settings/override/ezurlmultisite.ini.append.php` schreibt. Administrations-Siteaccesses (Anmeldung erforderlich oder
ein Admin-Design) werden ausgelassen.

## Für Entwickler von Extensions

Code, der Links auf Inhalte selbst baut statt über `ezurl`, stellt der Extension dieselbe Frage:

```php
if ( class_exists( 'eZURLMultisite' ) && eZURLMultisite::isActive() )
{
    $url = eZURLMultisite::crossSiteURL( $nodeOrURLAlias, 'relative' ); // false: keep the local link
}
```

Das README der Extension führt ihre weiteren Einstellungen auf (`[HostMap]`, `[PathMap]`, `[RoleMap]`, klassenbasierte
Links) und die Operatoren `ezurl_path`, `ezurl_siteaccess` und `ezurl_roots`.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
