---
title: Das Format index.xml
audience: developer, admin, hosting
category: install-upgrade
order: 34
summary: Der Paketindex eines Release-Ordners, Element für Element und Attribut für Attribut, mit dem, was der Setup-Assistent und ezupdate tatsächlich daraus lesen, den Regeln, die sie anwenden, und einem minimalen eigenen Index.
keywords: index.xml, Paketindex, packages, Element package, vendor, name, type, url, version, summary, description, thumbnail_url, eZStepSiteTypes, retrieveRemotePackagesList, ezupdate, Paketserver, package index, package element, package server
type: help
---
# Das Format index.xml

Diese Seite ist für Entwickler, die Pakete veröffentlichen, einen Spiegel betreiben oder den Schritt **Site Paket** untersuchen. Sie
beschreibt die Datei `index.xml` eines Release-Ordners: ihre Elemente und Attribute, welche davon der Code liest und was geschieht, wenn eines
falsch ist. Die Referenzimplementierung ist `eZStepSiteTypes::retrieveRemotePackagesList()` in `kernel/setup/steps/ezstep_site_types.php`.

## Inhalt

1. [Ein Beispiel](#ein-beispiel)
2. [Das Wurzelelement](#das-wurzelelement)
3. [Das Element package](#das-element-package)
4. [Wie der Setup-Assistent ihn liest](#wie-der-setup-assistent-ihn-liest)
5. [Wie ezupdate ihn liest](#wie-ezupdate-ihn-liest)
6. [Ein minimaler eigener Index](#ein-minimaler-eigener-index)
7. [Einen Index prüfen](#einen-index-prüfen)
8. [Verwandte Seiten](#verwandte-seiten)

## Ein Beispiel

Zwei Einträge des Index von 6.0.15, der erste ein verlangtes Paket, der zweite ein Seitenpaket (in der Datei je eine Zeile, hier
umbrochen):

```xml
<?xml version="1.0"?>
<packages vendor="7x">

<!-- DEPENDENCY PACKAGE LIST -->
  <package name="sevenx_classes" type="contentclass"
           url="https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/sevenx_classes.ezpkg"/>

  <package name="sevenx_multisite" version="6.0-15-stable" type="site"
           summary="7x MultiSite Default Installation"
           description="7x MultiSite Default Installation is a web based CMS solution based on Exponential. ..."
           url="https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/sevenx_multisite.ezpkg"
           thumbnail_url="https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/thumbnails/sevenx_multisite.png"/>
</packages>
```

Die ganze Datei: <https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/index.xml> (44 Einträge).

## Das Wurzelelement

| Element | Erforderlich | Bedeutung |
|---|---|---|
| `<packages>` | ja | Die Wurzel. Der Setup-Assistent weist jede andere Wurzel mit dem Debug-Fehler `Malformed index file.` ab |
| Attribut `vendor` | nein | `7x` auf dem Server. Es dokumentiert den Hersteller; weder der Setup-Assistent noch ezupdate lesen es |

Kommentare und Leerraum zwischen den Einträgen sind erlaubt; alles, was kein Element `<package>` ist, wird übersprungen.

## Das Element package

| Attribut | Genutzt bei | Gelesen von | Bedeutung |
|---|---|---|---|
| `name` | jedem Eintrag | Assistent, ezupdate | Der Paketname, derselbe wie `<name>` in seiner `package.xml`. Einträge werden nach Namen geschlüsselt: Ein zweiter Eintrag gleichen Namens ersetzt den ersten |
| `type` | jedem Eintrag | Assistent, ezupdate | Der Pakettyp. Der Assistent listet nur `type="site"` auf; die anderen Einträge dienen dazu, die URLs verlangter Pakete zu finden |
| `url` | jedem Eintrag | Assistent, ezupdate | Die vollständige Adresse der Datei `.ezpkg`. Sie darf überallhin zeigen, nicht nur in denselben Ordner |
| `version` | Seitenpaketen | Assistent, ezupdate | Gezeigt als „(ver. *version*)“ und mit einer importierten Kopie verglichen. Derselbe Text wie `eZPackage::getVersion()` des Pakets (`<number>-<release>` seiner `package.xml`), sodass eine importierte Kopie als gleich verglichen wird ([Die Version im Index und die Version des Pakets](was-auf-dem-paketserver-liegt.md#die-version-im-index-und-die-version-des-pakets)) |
| `summary` | Seitenpaketen | Assistent, ezupdate | Die Überschrift des Eintrags im Schritt **Site Paket** und der Titel seines Bildes |
| `description` | Seitenpaketen | Assistent | Der Text unter der Überschrift |
| `thumbnail_url` | Seitenpaketen | Assistent | Das Bild, das für ein noch nicht importiertes Seitenpaket gezeigt wird; ohne es wird ein Standardbild gezeigt |

> **Hinweis**
> In 6.0.15 behoben. Vorher schrieb der Schritt **Site Paket** `description` so in die Seite, wie es war, nicht maskiert
> (`design/standard/templates/setup/init/site_types.tpl`), und setzte `thumbnail_url` ungeprüft in eine Bildadresse. Ab 6.0.15 wird jedes
> Feld eines Eintrags im Index maskiert (`description` ist einfacher Text, seine Zeilenumbrüche bleiben erhalten), und
> `eZStepSiteTypes::sanitizeRemotePackageAttributes()` behält eine `url` oder `thumbnail_url` nur, wenn sie eine Adresse `http://` oder
> `https://` ist, und überspringt einen Eintrag, dessen `name` nicht aus Buchstaben, Ziffern, `.`, `-` und `_` besteht. Schreiben Sie
> `description` als einfachen Text (HTML-Tags erscheinen als Text), und richten Sie eine Installation trotzdem nur auf Indexdateien, denen
> Sie vertrauen.

## Wie der Setup-Assistent ihn liest

1. Die Adresse des Index ist `RemotePackagesIndexURL` plus `/index.xml` (ein abschließender Schrägstrich wird nicht verdoppelt).
2. Die Datei wird wie ein Paket heruntergeladen, mit Wiederholungen ([Wenn der Paketserver nicht erreichbar ist](wenn-der-paketserver-nicht-erreichbar-ist.md)),
   nach `<cache>/packages/index.xml`, und nach dem Lesen gelöscht.
3. Scheitert der Download, kopiert der Assistent `index.xml` aus dem System-Repository, `var/storage/packages/7x/index.xml`, wenn es eines
   gibt ([Offline-Installationen und Spiegel](offline-installationen-und-spiegel.md)).
4. Die Datei wird mit `DOMDocument` gelesen. Eine Datei, die sich nicht lesen lässt, protokolliert `Unable to open index file.`, und die
   Liste bleibt leer.
5. Jedes Element `<package>` wird zu einem Array aller seiner Attribute, sodass Attribute, die der Code nicht kennt, behalten, aber ignoriert
   werden.
6. Für die Liste auf dem Bildschirm werden nur Seitenpakete genutzt; für die Abhängigkeiten eines gewählten Pakets wird die ganze Liste
   genutzt, nach `name` nachgeschlagen. Ein verlangtes Paket, das weder importiert noch im Index ist, stoppt den Schritt mit „Der Download
   des Pakets '*name*' ist gescheitert. Sie können das Paket manuell uploaden.“

Das Optionsfeld eines entfernten Eintrags trägt `name|url`. Der Schritt behandelt die Wahl nur dann als Download, wenn der Name aus
Buchstaben, Ziffern und `_` besteht (`/^(\w+)\|(.+)$/` in `processPostData()`); Paketnamen folgen dieser Regel ohnehin
([Pflichtfelder in package.xml](../anforderungen/pflichtfelder-in-package-xml.md)).

## Wie ezupdate ihn liest

Die Seite **Package servers** der Erweiterung ezupdate liest dasselbe Format von jedem Server, den sie kennt
(`extension/ezupdate/classes/ezupdatepackageservers.php`):

- Der Index darf höchstens 1 MiB groß sein und wird ohne Netzwerkzugriff gelesen (`LIBXML_NONET`);
- eine andere Wurzel als `<packages>` ergibt `The server did not answer with a package index.`;
- ein `name` muss `^[A-Za-z0-9][A-Za-z0-9_.-]{0,63}$` entsprechen, andere Einträge werden übersprungen;
- sie zeigt `name`, `type`, `summary` und `version` neben der Version einer importierten Kopie;
- **Fetch** akzeptiert nur eine `url`, die mit `https://` beginnt, und ein Paket von höchstens 256 MiB.

Siehe [Paketserver in ezupdate](../admin-oberflaeche/paketserver-in-ezupdate.md).

## Ein minimaler eigener Index

Für einen Server mit einem Seitenpaket, das ein anderes Paket verlangt:

```xml
<?xml version="1.0"?>
<packages vendor="example">
  <package name="example_classes" type="contentclass"
           url="https://packages.example.com/6.0/6.0.15/example_classes.ezpkg"/>
  <package name="example_site" version="1.0-0" type="site"
           summary="Example site"
           description="The starter site of Example Inc."
           url="https://packages.example.com/6.0/6.0.15/example_site.ezpkg"
           thumbnail_url="https://packages.example.com/6.0/6.0.15/thumbnails/example_site.png"/>
</packages>
```

Jedes Paket, das ein Seitenpaket in `<dependencies><requires>` nennt, muss entweder hier einen Eintrag haben oder auf der Installation
bereits importiert sein. Siehe [Einen eigenen Paketserver betreiben](../eigene/einen-eigenen-paketserver-betreiben.md) und
[Abhängigkeiten angeben](../anforderungen/abhaengigkeiten-angeben.md).

## Einen Index prüfen

```bash
# well formed?
xmllint --noout index.xml

# every url answers 200?
grep -o 'url="[^"]*"' index.xml | cut -d'"' -f2 | while read -r u; do
  printf '%s %s\n' "$(curl -s -o /dev/null -w '%{http_code}' "$u")" "$u"
done
```

Der zweite Befehl prüft sowohl `url` als auch `thumbnail_url`. Führen Sie beide aus, bevor Sie einen Index veröffentlichen und bevor Sie einen
Pull Request für den gemeinsamen Server öffnen ([Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md)).

## Verwandte Seiten

- [Seitenpakete: hier beginnen](../README.md)
- [Der Paketserver](README.md)
- [Was auf dem Paketserver liegt](was-auf-dem-paketserver-liegt.md)
- [Einstellungen des Paketservers](einstellungen-des-paketservers.md)
- [Wie der Setup-Assistent Pakete nutzt](../grundlagen/wie-der-setup-assistent-pakete-nutzt.md)
- [Der Setup-Assistent, 5.3.8 Seitenpaket](../../../installation/05-setup-assistent.md#538-site-paket)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
