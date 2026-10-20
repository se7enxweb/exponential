---
title: Vorschaubilder und Screenshots
audience: developer, designer, partner
category: install-upgrade
order: 56
summary: Die Bilder eines Seitenpakets, die Dateilisten für Vorschaubild und Bild in package.xml, wohin die Dateien im Archiv gehören, wie der Setup-Assistent zwischen dem eigenen Vorschaubild eines Pakets, der thumbnail_url der index.xml und dem Standardbild wählt und was die ausgelieferten Pakete und der Paketserver mitbringen.
keywords: Vorschaubild, thumbnail.png, thumbnail_url, logo.png, Bild, file-list, files/default, Sammlung default, md5sum, Screenshot, Setup-Assistent, Schritt der Seitentypen, Vorschaubilder des Paketservers, thumbnail, image, collection default, screenshot, setup wizard, site type step, package server thumbnails
type: help
---
# Vorschaubilder und Screenshots

Diese Seite ist für Entwickler und Designer, die einem Seitenpaket sein Bild geben. Das Vorschaubild ist das Erste, was ein Administrator von
einem Seitenpaket sieht: Der Setup-Assistent zeigt es neben jedem Seitenpaket, das er anbietet, und die Paketseite zeigt es neben den
Details. **Kein Vorschaubild ist erzwungen**; ohne eines zeigt der Assistent ein Standardbild. Jedes ausgelieferte Seitenpaket hat eines,
und der Paketserver hat eine Kopie für den Index.

## Inhalt

1. [Das Vorschaubild in package.xml](#das-vorschaubild-in-packagexml)
2. [Woher der Setup-Assistent das Bild nimmt](#woher-der-setup-assistent-das-bild-nimmt)
3. [Das Vorschaubild auf dem Paketserver](#das-vorschaubild-auf-dem-paketserver)
4. [Die Dateiliste für Bilder](#die-dateiliste-für-bilder)
5. [Was die ausgelieferten Pakete mitbringen](#was-die-ausgelieferten-pakete-mitbringen)
6. [Screenshots](#screenshots)
7. [Checkliste](#checkliste)
8. [Verwandte Seiten](#verwandte-seiten)

## Das Vorschaubild in package.xml

Ein Vorschaubild ist eine Dateiliste vom Typ `thumbnail` in der Dateisammlung namens `default`:

```xml
<files>
  <collection name="default">
    <file-list type="thumbnail" variable-name="thumbnail">
      <file name="thumbnail.png" md5sum="77a7bc0cc6e1e5138ca7a6e39a130307" />
    </file-list>
  </collection>
</files>
```

Die Datei gehört im Archiv nach `files/<collection>/<type>/<name>`, hier also `files/default/thumbnail/thumbnail.png`
(`eZPackage::fileItemPath()` in `kernel/classes/ezpackage.php`). Zwei Details legt der Code fest:

- Die Sammlung muss `default` sein. Das Attribut `thumbnail-list` eines Pakets ist `thumbnailList( 'default' )`, sodass ein Vorschaubild in
  jeder anderen Sammlung nie gezeigt wird.
- Nur das **erste** Vorschaubild wird genutzt, vom Setup-Assistenten wie von der Paketseite.

Das Attribut `md5sum` wird gelesen und zurückgeschrieben, aber nichts, was ein Paket importiert oder installiert, prüft es gegen die Datei.
Halten Sie es trotzdem korrekt: Es ist der einzige Beleg dafür, für welches Bild die Definition geschrieben wurde.

## Woher der Setup-Assistent das Bild nimmt

Der Schritt der Seitentypen (`kernel/setup/steps/ezstep_site_types.php`, Template `design/standard/templates/setup/init/site_types.tpl`)
wählt ein Bild pro Seitenpaket, in dieser Reihenfolge:

| Reihenfolge | Quelle | Wann |
|---|---|---|
| 1 | Das erste eigene Vorschaubild des Pakets (`thumbnail_path`) | Das Paket ist auf dieser Installation bereits importiert |
| 2 | `thumbnail_url` des Eintrags des Pakets in `index.xml` | Das Paket liegt nur auf dem Paketserver, und (ab 6.0.15) die Adresse ist `http://` oder `https://` |
| 3 | `design/standard/images/setup/eZ_setup_template_default.png` | Keines von beidem existiert |

Dasselbe Paket kann also zwei verschiedene Bilder zeigen: das des Servers, bevor es importiert ist, und sein eigenes danach. Das
Quell-Repository von 7x hielt am 02.10.2026 die Korrektur dafür fest:

```text
Updated: The multisite packages carry the Exponential multisite picture as their thumbnail, the one the package server shows for them, so the setup wizard shows the same picture for a package it already has as for one it would download.
```

## Das Vorschaubild auf dem Paketserver

Jeder Eintrag eines Seitenpakets in der `index.xml` von 6.0.15 hat eine `thumbnail_url` der Form
`https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/thumbnails/<name>.png`, zum Beispiel
[thumbnails/sevenx_multisite.png](https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/thumbnails/sevenx_multisite.png).
Der Ordner `thumbnails/` dieses Release enthält 13 PNG-Dateien: eine für jedes der 12 Seitenpakete im Index und `sevenx_site_paypal.png`, das
kein Eintrag des Index nutzt. Das Format des Attributs steht auf [Das Format index.xml](../paketserver/das-format-index-xml.md).

## Die Dateiliste für Bilder

Die Seitenpakete von 7x und CJW bringen ein zweites Bild mit, `logo.png`, als Dateiliste vom Typ `image` in derselben Sammlung, gespeichert
unter `files/default/image/logo.png`:

```xml
<file-list type="image" variable-name="image">
  <file name="logo.png" md5sum="999280c75d36e8e24477c223a3230906" />
</file-list>
```

Keiner der Schritte des Setup-Assistenten in `kernel/setup/steps/` liest es; es ist eine Konvention der ausgelieferten Pakete. Die Paketseite
führt es unter den Dateien des Pakets auf.

## Was die ausgelieferten Pakete mitbringen

Am 18.10.2026 in den Quell-Repositorys und im Repository des Paketservers geprüft:

| Paket | Vorschaubild | Größe | Gleich der Kopie des Servers |
|---|---|---|---|
| `sevenx_multisite`, `sevenx_multisite_clean`, `cjw_multisite` | `thumbnail.png` | PNG 192 x 192 | ja |
| `sevenx_site` | `thumbnail.png` | PNG 120 x 103 | ja |
| `sevenx_site_clean` | `thumbnail.png` | PNG 120 x 103 | nein, die Kopie des Servers weicht ab |
| `sevenx_design_blue`, `sevenx_design_cleangray`, `sevenx_design_gray` | `thumbnail.png` | PNG 120 x 103 | keine Kopie auf dem Server (Seitenstile werden nicht mit einem Vorschaubild aufgeführt) |

Alle fünf Seitenpakete bringen außerdem `logo.png` mit (PNG 176 x 42). In `sevenx_multisite` und `sevenx_multisite_clean` passte die für
`logo.png` geschriebene `md5sum` nicht zur Datei, was zeigt, wie unbemerkt ein solcher Wert veraltet. Am 19.10.2026 behoben: Beide Pakete
nennen nun die Prüfsumme der Datei, und die neu gebauten Pakete liegen auf dem Paketserver.

Die drei Seitenstile `sevenx_design_blue`, `sevenx_design_cleangray` und `sevenx_design_gray` bringen noch veraltete Prüfsummen für ihre
`thumbnail.png`, `site-colors.css` und `classes-colors.css` mit (geprüft durch Importieren am 19.10.2026). In `sevenx_design_gray` sind die
beiden Stylesheets leere Dateien, sodass seine Prüfsummen nicht das Einzige sind, was Sie vor einer Änderung dieses Pakets ansehen sollten.

## Screenshots

Ein Paket hat kein Feld für Screenshots. Das Vorschaubild ist ein kleines Bild zum Auswählen; es ist keine Vorschau der Website. Setzen Sie
Screenshots eines Seitenpakets dorthin, wo Menschen entscheiden, ob sie es nutzen: in das README seines Quell-Repositorys und in den Pull
Request, der es einreicht ([Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md)). Wie Sie sie gut aufnehmen, behandeln
[Screenshots für ein Theme](../../../../en/us/help/designers/distribution/screenshots-for-a-theme.md) und die
[Checkliste für Screenshots](../../../../en/us/help/designers/distribution/screenshot-checklist.md).

## Checkliste

- [ ] Ein PNG-Vorschaubild in der Sammlung `default`, unter `files/default/thumbnail/thumbnail.png`.
- [ ] 192 x 192 wie die Multisite-Pakete oder 120 x 103 wie die klassischen.
- [ ] Die `md5sum` in `package.xml` passt: `md5sum files/default/thumbnail/thumbnail.png`.
- [ ] Dasselbe Bild, Byte für Byte, wie `thumbnails/<name>.png` auf dem Paketserver.
- [ ] `thumbnail_url` im Eintrag des Index zeigt auf diese Datei und antwortet mit 200.
- [ ] Screenshots im README des Quell-Repositorys, nicht im Paket.

```bash
md5sum files/default/thumbnail/thumbnail.png
grep -A2 'type="thumbnail"' package.xml
curl -s -o /dev/null -w '%{http_code}\n' https://packages.example.com/exponential/6.0/6.0.15/thumbnails/example_site.png
```

## Verwandte Seiten

- [Anforderungen an die Dokumentation von Seitenpaketen](README.md)
- [Die Checkliste der Anforderungen](die-checkliste-der-anforderungen.md)
- [Die Datei ezpkg](../grundlagen/die-datei-ezpkg.md)
- [Wie der Setup-Assistent Pakete nutzt](../grundlagen/wie-der-setup-assistent-pakete-nutzt.md)
- [Was auf dem Paketserver liegt](../paketserver/was-auf-dem-paketserver-liegt.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
