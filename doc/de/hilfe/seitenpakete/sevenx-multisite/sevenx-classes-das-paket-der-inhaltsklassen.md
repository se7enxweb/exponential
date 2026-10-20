---
title: sevenx_classes, das Paket der Inhaltsklassen
audience: developer, admin, website-creator
category: install-upgrade
order: 62
summary: Jede Inhaltsklasse des Pakets sevenx_classes mit Name, Gruppe und Datentypen, wie das Paket installiert wird und wie der Installer einige Klassen danach ändert.
keywords: sevenx_classes, Inhaltsklassen, Paket contentclass, ezcontentclass, article, folder, product, xrowmetadata, ezsrrating, Remote-ID der Klasse, Klassengruppen, content classes, contentclass package, class remote id, class groups
type: help
---
# sevenx_classes, das Paket der Inhaltsklassen

Diese Seite ist für Entwickler und Administratoren, die wissen müssen, welche allgemeinen Inhaltsklassen eine Installation von
`sevenx_multisite` hat und woher sie kommen. Sie beantwortet: was das Paket `sevenx_classes` enthält, wie es installiert wird und was der
Installer an diesen Klassen danach ändert. Die websitespezifischen Klassen `ng_*` stehen auf
[Die Demo-Inhaltspakete](die-demo-inhaltspakete.md).

## Inhalt

1. [Das Paket](#das-paket)
2. [Die Klassen](#die-klassen)
3. [Wie das Paket installiert wird](#wie-das-paket-installiert-wird)
4. [Was der Installer danach ändert](#was-der-installer-danach-ändert)
5. [Geschichte](#geschichte)

## Das Paket

| Feld | Wert |
|---|---|
| Name | `sevenx_classes` |
| Zusammenfassung | `sevenx content classes` |
| Typ | `contentclass` |
| Installationstyp | `install` (`<package ... install_type="install">`) |
| Version | Nummer `6.0`, Release `10-stable`; `<ezpublish><version>` `6.0.10-stable` |
| Lizenz | `GPL`, Text in `documents/LICENCE` |
| Dateien | `package.xml`, `documents/LICENCE`, 33 Dateien `ezcontentclass/class-<identifier>.xml` |
| Verlangt von | `sevenx_multisite`, `sevenx_multisite_clean`, `sevenx_site`, `sevenx_site_clean` |

Seine Liste `<provides>` hat 34 Einträge, weil `file` zweimal aufgeführt ist; es gibt 33 Klassendateien und 33 Elemente `<install>` und
dieselben 33 Elemente unter `<uninstall>`. Das veröffentlichte Archiv von 6.0.15 ist
[sevenx_classes.ezpkg](https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/sevenx_classes.ezpkg).

## Die Klassen

Gelesen aus dem `<identifier>`, dem Eintrag `eng-US` von `<serialized-name-list>`, den Elementen `<group>`, dem Attribut `is-container` und
den Elementen `<attribute datatype="...">` jeder Klassendatei.

| Bezeichner | Name | Gruppe | Container | Attribute | Datentypen außer ezstring und ezxmltext |
|---|---|---|---|---|---|
| `article` | Article | Content | ja | 13 | ezauthor, ezboolean, ezdatetime, ezkeyword, ezobjectrelationlist, ezsrrating, xrowmetadata |
| `article_mainpage` | Article (main-page) | Content | ja | 13 | ezauthor, ezboolean, ezdatetime, ezimage, ezkeyword, xrowmetadata |
| `article_subpage` | Article (sub-page) | Content | nein | 5 | ezkeyword, xrowmetadata |
| `banner` | Banner | Content | nein | 5 | ezimage, ezkeyword, eztext |
| `blog` | Blog | Content | ja | 4 | ezkeyword, xrowmetadata |
| `blog_post` | Blog post | Content | ja | 7 | ezboolean, ezdatetime, ezkeyword, xrowmetadata |
| `comment` | Comment | Content | nein | 3 | eztext |
| `documentation_page` | Documentation page | Content | ja | 4 | ezboolean, ezkeyword |
| `event` | Event | Content | nein | 7 | ezdatetime, ezkeyword, xrowmetadata |
| `event_calendar` | Event calendar | Content | ja | 4 | ezselection, xrowmetadata |
| `feedback_form` | Feedback form | Content | ja | 8 | ezemail, eztext, xrowmetadata |
| `file` | File | Media | nein | 4 | ezbinaryfile, ezkeyword |
| `flash` | Flash | Media | nein | 4 | ezkeyword, ezmedia |
| `folder` | Folder | Content | ja | 8 | ezboolean, ezdatetime, ezkeyword, xrowmetadata |
| `forum` | Forum | Content | ja | 3 | xrowmetadata |
| `forum_reply` | Forum reply | Content | nein | 2 | eztext |
| `forum_topic` | Forum topic | Content | ja | 4 | ezboolean, ezsubtreesubscription, eztext |
| `forums` | Forums | Content | ja | 2 | (keine) |
| `frontpage` | Frontpage | Content | ja | 8 | ezkeyword, ezobjectrelation, xrowmetadata |
| `gallery` | Gallery | Content | ja | 7 | ezkeyword, ezobjectrelation, ezsrrating, xrowmetadata |
| `geo_article` | Geo Article | Content | ja | 13 | ezauthor, ezboolean, ezdatetime, ezgmaplocation, ezimage, ezkeyword, xrowmetadata |
| `image` | Image | Media | nein | 6 | ezimage, ezkeyword, ezsrrating, xrowmetadata |
| `infobox` | Infobox | Content | nein | 5 | ezimage, ezurl |
| `link` | Link | Content | nein | 5 | ezboolean, ezurl, xrowmetadata |
| `multicalendar` | Multicalendar | Content | nein | 3 | ezobjectrelationlist |
| `poll` | Poll | Content | nein | 3 | ezoption |
| `product` | Product | (keine in der Datei) | ja | 12 | ezkeyword, ezmatrix, ezmultioption, ezobjectrelationlist, ezprice, ezsrrating, xrowmetadata |
| `quicktime` | Quicktime | Media | nein | 4 | ezkeyword, ezmedia |
| `real_video` | Real video | Media | nein | 4 | ezkeyword, ezmedia |
| `review` | Review | Content | nein | 5 | ezboolean, ezinteger, eztext |
| `silverlight` | Silverlight | Media | nein | 3 | ezmedia |
| `video` | Video | Media | nein | 6 | ezkeyword, ezmedia, ezsrrating, xrowmetadata |
| `windows_media` | Windows media | Media | nein | 4 | ezkeyword, ezmedia |

Die Datentypen kommen aus Erweiterungen: `ezsrrating` aus `ezstarrating`, `ezgmaplocation` aus `ezgmaplocation`, `xrowmetadata` aus
`xrowmetadata`. `preInstall()` des Installers registriert die ersten beiden (und `ngenhancedlink`, `eztags`) in
`content.ini [DataTypeSettings]` für den laufenden Prozess, bevor die Pakete installiert werden, und führt alle drei Erweiterungen in
`ActiveExtensions` auf; siehe [Erweiterungen, die es aktiviert](erweiterungen-die-sevenx-multisite-aktiviert.md).

## Wie das Paket installiert wird

Das Setup installiert verlangte Pakete, bevor die Nachinstallation des Seitenpakets läuft (siehe
[Abhängigkeiten und die Installationsreihenfolge](../grundlagen/abhaengigkeiten-und-die-installationsreihenfolge.md)). `CreateSites` installiert sie mit
`'non-interactive' => true` (`kernel/setup/steps/ezstep_create_sites.php`). In diesem Modus überspringt `eZContentClassPackageHandler`
(`kernel/classes/packagehandlers/ezcontentclass/ezcontentclasspackagehandler.php`) eine Klasse, deren Remote-ID bereits existiert: Nicht
interaktive Installationen, etwa über die Kommandozeile, dürfen vorhandene Klassen und ihre Inhaltsobjekte nicht zerstörend ersetzen.

Sechs der 33 Klassen tragen dieselbe Remote-ID wie eine Klasse der Basisdaten des Kernels (`share/db_data.dba`): `article`, `comment`,
`file`, `folder`, `image` und `link`. Auf einer neuen Installation behalten diese sechs die Definition der Basisdaten; die anderen 27 werden
aus dem Paket angelegt.

> **Hinweis**
> Wenn Sie `sevenx_classes` von Hand in eine vorhandene Website importieren, bietet der Klassenhandler die Wahlmöglichkeiten seines
> Konflikts `ERROR_EXISTS` an: ersetzen, überspringen, eine neue Klasse anlegen oder die vorhandene Klasse aktualisieren (Attribute werden
> hinzugefügt und aktualisiert, keines entfernt). Siehe [Importieren oder installieren](../grundlagen/importieren-oder-installieren.md).

## Was der Installer danach ändert

`sevenxMultiSiteInstaller` passt einige dieser Klassen während der Installation an:

| Schritt | Klasse | Änderung |
|---|---|---|
| `preInstall()` | `folder` | fügt `tags` („Tags“, ezkeyword) und `publish_date` („Publish date“, ezdatetime) hinzu, in der Hauptsprache benannt |
| `preInstall()` | `user` (Basisdaten) | fügt `two_factor` (**Zweistufige Anmeldung**, Datentyp `sevenxauthentication2fa`) hinzu, wenn `extension/sevenx_authentication_2fa` existiert |
| Nachinstallation `updateClassAttributes` | `folder` | benennt `short_description` in „Summary“ und `show_children` in „Display sub items“ um |
| Nachinstallation `updateTemplateLookClassAttributes` | `template_look` (Basisdaten) | fügt zwölf Attribute hinzu, etwa `site_map_url`, `footer_text`, `hide_powered_by`, `footer_script` |
| Nachinstallation `removeClassAttribute` | `template_look` | entfernt das Attribut `id` |

Zwei Reparaturschritte bauen dann Listen von Klassennamen neu, die unter einer Nummer statt eines Sprachcodes gespeichert waren
(`postInstallResyncContentClassNames`, `postInstallRepairClassNameLists`); siehe [Korrekturen nach der Installation](korrekturen-nach-der-installation-von-sevenx-multisite.md).

## Geschichte

Die Git-Geschichte von `packages/sevenx_classes` beginnt am 13.01.2024 (Umbenennung der internen Verzeichnisse der wichtigsten Seitenpakete
zu Beginn der Bearbeitung und Verbesserung), als der Ordner `packages/ezwebin_classes` in `packages/sevenx_classes` umbenannt wurde
(`git log --follow` zeigt die Umbenennung). Spätere Commits stellten jede Klasse auf eine Übersetzung `eng-US` um (13.01.2024), gaben den
Klassen Artikel, Bild und Produkt ein Attribut für Sternebewertungen (27.02.2024), fügten den meisten Seitenklassen `xrowmetadata` hinzu
(02.04.2024), fügten die Klasse `review` hinzu (12.09.2026) und machten `product` zu einem Container (30.09.2026).

## Verwandte Seiten

- [sevenx_multisite](README.md)
- [Die Demo-Inhaltspakete](die-demo-inhaltspakete.md)
- [Pakettypen](../grundlagen/pakettypen.md)
- [Installationselemente und Handler](../grundlagen/installationselemente-und-handler.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
