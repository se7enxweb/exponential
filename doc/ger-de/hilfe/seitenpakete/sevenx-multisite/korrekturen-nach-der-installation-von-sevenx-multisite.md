---
title: Korrekturen nach der Installation von sevenx_multisite
audience: developer, admin
category: install-upgrade
order: 69
summary: Was install-scripts.php und post-install-fix.php tun - die Hooks, die das Setup aufruft, und jede Reparatur, die die Multisite-Installation ausführt, nachdem die Inhalte da sind - und warum jede nötig ist.
keywords: post-install-fix.php, install-scripts.php, eZSitePreInstall, eZSitePostInstall, sevenxFixPackageNodesAndExplayouts, sevenxFixMenuINIFiles, sevenxRegenerateURLAliases, URL-Aliase, PathPrefix, eztags, Relationsliste, Listen von Klassennamen, Sprachmasken, URL aliases, relation list, class name lists, language masks
type: help
---
# Korrekturen nach der Installation von sevenx_multisite

Diese Seite ist für Entwickler, die den Installer von `sevenx_multisite` pflegen oder kopieren, und für Administratoren, die eine Zeile aus
dem Log der Installation verstehen wollen. Sie beantwortet: was die zwei Hook-Dateien tun und wofür jede Reparatur nach dem Import der
Inhalte da ist. Die Schrittnummern beziehen sich auf die Tabelle in
[Das Skript des Multisite-Installers](das-skript-des-multisite-installers.md#phase-4-die-schritte-der-nachinstallation).

## Inhalt

1. [install-scripts.php](#install-scriptsphp)
2. [post-install-fix.php](#post-install-fixphp)
3. [Reparaturen der importierten Daten](#reparaturen-der-importierten-daten)
4. [Knoten, Layouts, Menüs und Tags](#knoten-layouts-menüs-und-tags)
5. [URL-Aliase und Präfixe der Websites](#url-aliase-und-präfixe-der-websites)
6. [Klassennamen](#klassennamen)
7. [Das Log lesen](#das-log-lesen)

## install-scripts.php

`settings/install-scripts.php` definiert die zwei Hooks, die `CreateSites` (`kernel/setup/steps/ezstep_create_sites.php`) rund um die
Installation der Pakete aufruft:

```php
function eZSitePreInstall( )
{
    $installer = new sevenxMultiSiteInstaller( array( 'var_dir' => 'var/site' ) );
    $installer->preInstall();
}

function eZSitePostInstall( &$parameters )
{
    $installer = new sevenxMultiSiteInstaller( $parameters );
    $installer->postInstall();
}
```

`CreateSites` ruft `eZSitePreInstall( $siteType )` auf, nachdem es die Einstellungsdateien eingebunden hat und bevor es die verlangten
Pakete installiert, und `eZSitePostInstall( $parameters )` am Ende, mit der Sprache der Inhalte vorübergehend auf die Sprache der sauberen
Daten gesetzt (in der Hauptsprache kam jedes `dataMap()` eines Objekts in eng-US leer zurück). Nach der Nachinstallation prüft es
`eZSiteInstaller::abortedStep()` und meldet `EZSW-080`, wenn ein Schritt die Kette angehalten hat. Was `preInstall()` tut, steht in
[Das Skript des Multisite-Installers](das-skript-des-multisite-installers.md#phase-2-die-vorinstallation).

## post-install-fix.php

`settings/post-install-fix.php` (1050 Zeilen) wird vom Installer mit `require_once` geladen; es ist kein `<settings-file>`. Sein Kopf sagt,
warum es getrennt ist: Alle nicht interaktiven Normalisierungen der Datenbank sollen hier stehen, damit der Installer und build_reference
dieselbe Logik teilen können. Jede Funktion ist in `if ( !function_exists(...) )` eingeschlossen.

| Funktion | Genutzt von Schritt | Zweck |
|---|---|---|
| `sevenxSitePackageName()` | | der Ordnername über `settings/`: `sevenx_multisite` oder `sevenx_multisite_clean` |
| `sevenxDemocontentPackageName()` | | das verlangte Paket, dessen Name mit `sevenx_multisite_democontent` beginnt, aus der `package.xml` des Seitenpakets gelesen |
| `sevenxDemocontentObjectDir()` | | der Ordner `ezcontentobject/` dieses Pakets im Repository |
| `sevenxFixPackageNodesAndExplayouts()` | 35 | Knoten, Verweise der Layouts, Menüs, Tags, Einbettungen, Cache |
| `sevenxSetSiteInfoMenuNodeIDs()` | | ein Platzhalter, der `true` zurückgibt |
| `sevenxFixMenuINIFiles()` | 35 | `menu.ini.append.php` jedes Siteaccess einer Website |
| `sevenxClearLinksToContentNotInstalled()` | 40 | Links in Blöcken auf fehlende Seiten |
| `sevenxCleanStaleUrlTextAttributes()` | 37 | veraltete Werte `url_text` |
| `sevenxRegenerateURLAliases()` | 38 | URL-Aliase jedes Knotens |
| `sevenxFixEmbeddedObjectIDs()` | 35 | IDs von `<embed>` und `<embed-node>` im Rich Text |
| `sevenxFixEzTagsFromPackage()` | 35 | Tag-Attribute |
| `sevenxRepairClassNameLists()` | 45 | nach Nummer geschlüsselte Listen von Klassennamen |

Der einzige Unterschied zwischen den beiden Installationen ist das Inhaltspaket, das das Seitenpaket verlangt; deshalb dient dieselbe Datei
`sevenx_multisite` und `sevenx_multisite_clean`.

## Reparaturen der importierten Daten

Diese Schritte laufen früh, weil sie nur die Objekte brauchen und mehrere spätere Schritte empfindlich sind:

| Schritt | Reparatur |
|---|---|
| 3 `postInstallFixContentObjectNameLanguages` | bringt die Sprach-ID jeder Zeile `ezcontentobject_name` und `ezcontentobject_attribute` mit ihrem Locale in Einklang, behält dabei das Bit „immer verfügbar“ und baut dann die Sprachmasken von Versionen und Objekten aus ihren Attributen neu. Ohne sie verfehlen Abfragen, die die Namenstabelle verbinden, ihr Ziel, und `executeSteps` bricht den Rest der Installation ab |
| 4 `postInstallResolveRelationListIds` | Einträge von Relationslisten kommen als `<relation-item contentobject-remote-id="...">` an; jedes Attribut wird über `fromString()` des Datentyps mit echten Objekt-IDs neu gebaut, und nicht installierte Ziele werden weggelassen |
| 5 `postInstallRekeyStarRatings` | verschiebt die eingespielte Zeile `ezstarrating` auf das installierte „Test Product“ (Remote-ID `af35e1174dab340acaddcff36cea57d3`) und stellt seinen Durchschnitt von 4,3 wieder her |
| 41 `postInstallPlaceOrphanedPackageObjects` | gibt jedem Paketobjekt, das ohne Knoten veröffentlicht wurde, einen Knoten, liest das Elternelement aus der Objektdatei, in zwei Durchgängen |

## Knoten, Layouts, Menüs und Tags

Schritt 35 ruft `sevenxFixPackageNodesAndExplayouts()` auf, das:

1. Die Knotenzuweisungen jeder `object-media-o-*.xml` des Inhaltspakets liest und die noch fehlenden Knoten anlegt, Elternelemente über die
   Remote-ID auflöst, in bis zu 50 Durchgängen. Das baut die Abbildung von Paket-IDs für Knoten und Objekte auf die IDs dieser Installation.
2. Die Verweise in `explayouts_*` und `eztags_attribute_link` abbildet und die entfernt, die auf Inhalte zeigen, die das Paket nicht
   mitbringt (siehe [Layoutdaten und share-DBA-Dateien](layoutdaten-und-share-dba-dateien.md#die-zeilen-an-die-neue-installation-anpassen)).
3. `sevenxFixMenuINIFiles()`: schreibt `settings/siteaccess/<sa>/menu.ini.append.php` für `site`, `eng`, `sevenx_site_user` (Fit & Healthy)
   und `bold`, `bold_ger` (Bold Agency), mit `[SiteInfo]` `RemoteID` (`media-o-site-info` oder `media-o-site-info-bold`), `MainMenuID[]`,
   `NexusMainMenuID[]`, `FooterMenuID[]`, `NexusFooterMenuID[]`, `PrivacyPolicyID` und `CookiePolicyID`. Jede Liste beginnt mit einer reinen
   Zeile `Key[]`, sodass sie die Liste des Themes ersetzt, statt an sie angehängt zu werden; eine Seite, die das Paket nicht installiert
   hat, wird weggelassen. Die IDs werden in die Siteaccess-Einstellungen des Projekts geschrieben, nicht in die Theme-Erweiterung.
4. `sevenxFixEzTagsFromPackage()`: speichert jedes Attribut `eztags` erneut aus den Zeichenketten in der Objektdatei und erhöht die Priorität
   von Regel 50 auf 175.
5. `sevenxFixEmbeddedObjectIDs()`: schreibt numerische `object_id` und `node_id` von Einbettungen in Feldern `ezxmltext` um.
6. Leert alle Caches mit `eZCache::clearAll()`, weil die ersten Seitenaufrufe aus Caches ausgeliefert wurden, die vor diesen Korrekturen
   gebaut waren.

Die Methode des Installers leert dann den Cache des Layout-Resolvers.

## URL-Aliase und Präfixe der Websites

Jede Website liegt unter ihrem eigenen Startknoten und verbirgt ihn mit `PathPrefix`, sodass ihre URLs Aliase brauchen, die mit dem Präfix
beginnen:

| Schritt | Was er tut |
|---|---|
| 37 `postInstallCleanUrlText` | leert Werte `url_text`, die nicht zum Namen des Objekts passen (Showcase-Seiten behalten ihre) |
| 38 `postInstallRegenerateURLAliases` | ruft `updateSubTreePath()` für jeden Knoten außer Knoten 1 auf, ohne die Alias-Tabellen vorher zu leeren: Sie zu leeren entfernt die Wurzelelemente, an denen der Inhaltsbaum hängt |
| 39 `postInstallCreateSitePrefixAliases` | speichert den Pfad mit Präfix jedes Knotens unter `media-n-939` und `media-n-940`, pro Sprache des Startknotens, ohne je das eigene Element des Startknotens anzufassen |
| 42 `postInstallSetSiteHomeAndPrefix` | schreibt `ForceVirtualHost=true`, `PathPrefix`, `IndexPage`, `DefaultPage` und `RootNodeDepth` für `site`, `bold`, `bold_ger` und die Übersetzungen, kopiert die Menüs in die Übersetzungen und richtet den leeren Pfad-Alias auf die Startseite von Fit & Healthy |

## Klassennamen

| Schritt | Reparatur |
|---|---|
| 44 `postInstallResyncContentClassNames` | baut `ezcontentclass.serialized_name_list` aus den Zeilen `ezcontentclass_name` neu für Klassen, deren Liste keinen Namen enthält, sodass Klassenlisten und Menüs zum Anlegen keine Lücke mehr zeigen |
| 45 `postInstallRepairClassNameLists` | verschiebt Namen und Beschreibungen, die unter dem Schlüssel `0` gespeichert sind (geschrieben, als noch keine Inhaltssprache existierte), auf die Sprache der Klasse; idempotent |

## Das Log lesen

Die Korrekturen protokollieren über `eZDebug::writeNotice()`, je eine zusammenfassende Zeile, zum Beispiel
`Created N missing tree nodes in N pass(es)`, `References to content ... does not ship, removed: ...`,
`Regenerated URL aliases for N nodes, N changed`, `Stored N prefixed URL alias path(s)`,
`Switched off N block link(s) to pages this installation does not have`. Bei einer Clean-Installation sind die Zählungen der entfernten
Verweise zu erwarten; bei einer vollständigen Installation sollten sie null sein.

## Verwandte Seiten

- [sevenx_multisite](README.md)
- [Das Skript des Multisite-Installers](das-skript-des-multisite-installers.md)
- [Installationsskripte in einem Seitenpaket](../grundlagen/installationsskripte-in-einem-seitenpaket.md)
- [Das Installerskript ändern](../eigene/das-installerskript-aendern.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
