---
title: Versionsnummern
audience: developer, partner, admin
category: install-upgrade
order: 55
summary: Die Versionsfelder eines Seitenpakets, Versionsnummer und Release, die Produktversion in ezpublish, das Attribut version des Elements package und die Version in index.xml, wofür jedes genutzt wird, wie eZPackageVersion sie vergleicht und die Fallen in den Formen, die die ausgelieferten Pakete nutzen.
keywords: Version, Release, Versionsnummer, Release-Nummer, min-version, getVersion, eZPackageVersion, compareFull, Semantic Versioning, SemVer, Version in ezpublish, named-version, Version in index.xml, 15-stable, version, release, version number, release number, ezpublish version, index.xml version
type: help
---
# Versionsnummern

Diese Seite ist für Entwickler, die ein Seitenpaket oder ein Paket, das es verlangt, veröffentlichen. Ein Paket hat mehrere Versionsfelder,
und sie erfüllen verschiedene Aufgaben: Eines entscheidet, ob eine Anforderung erfüllt ist, eines wird nur gezeigt, eines wird gar nicht
gelesen. Sie richtig zu setzen zählt am meisten für Pakete, die andere Pakete verlangen, weil der Setup-Assistent Versionen vergleicht, um zu
entscheiden, ob er ein Paket erneut herunterlädt.

## Inhalt

1. [Die Versionsfelder](#die-versionsfelder)
2. [Wie eine Paketversion gelesen wird](#wie-eine-paketversion-gelesen-wird)
3. [Was die ausgelieferten Pakete nutzen](#was-die-ausgelieferten-pakete-nutzen)
4. [Fallen](#fallen)
5. [Empfohlene Form](#empfohlene-form)
6. [Verwandte Seiten](#verwandte-seiten)

## Die Versionsfelder

| Feld | Wo | Genutzt für | Erzwungen |
|---|---|---|---|
| Versionsnummer | `<version><number>` | Die Paketversion, mit dem Release verbunden | Muss ein einfaches Token sein, bis zu 64 Zeichen aus `0-9 A-Z a-z . _ + -` |
| Release | `<version><release>` | Die zweite Hälfte der Paketversion | Dieselbe Regel für Tokens |
| Produktversion | `<ezpublish><version>` und `<named-version>` | Auf der Paketseite als **Für Exponential** gezeigt | Nur, dass `<ezpublish>` existiert (bei `package/upload`) |
| Formatversion | Attribut `version` von `<package>` | Nichts: `parseDOMTree()` liest es nicht | Nein |
| Version im Index | Attribut `version` in `index.xml` | Im Schritt der Seitentypen des Setup-Assistenten gezeigt; mit einem importierten Paket verglichen | Nein |

Die Regel für Tokens ist `eZPackage::isValidDefinitionToken()` in `kernel/classes/ezpackage.php`; eine Definition, die sie verletzt, wird
gar nicht gelesen ([Pflichtfelder in package.xml](pflichtfelder-in-package-xml.md)).

## Wie eine Paketversion gelesen wird

`eZPackage::getVersion()` verbindet die beiden Hälften mit einem Bindestrich: `<number>-<release>`. Mit diesem Paar wird `min-version` in
einem `<require>` verglichen, und dieses vergleicht der Setup-Assistent zwischen einem importierten Paket und dem Index. Der Vergleich ist
`eZPackageVersion::compareFull()` in `kernel/classes/ezpackageversion.php`:

1. Der Teil nach dem **letzten** Bindestrich ist nur dann die Release-Nummer, wenn er **nur aus Ziffern** besteht. `1.0-1` ist Version
   `1.0`, Release `1`. Sonst ist der ganze Text die Versionsnummer, ohne Release.
2. Versionsnummern werden nach dem Vorrang von Semantic Versioning 2.0.0 verglichen. Ältere Formen werden ebenfalls gelesen: `1` und `1.0`
   als `1.0.0`, `1.2.3.4` mit dem vierten Teil nach PATCH, `3.4.0beta1` als Vorabversion `beta1`.
3. Eine Version mit Vorabversion kommt **vor** derselben Version ohne. Bezeichner von Vorabversionen werden einzeln verglichen: numerische
   numerisch, andere in ASCII-Reihenfolge.
4. Die Release-Nummern werden zuletzt verglichen, numerisch, eine fehlende als `0`: `1.0-1` < `1.0-2` < `1.0.1-1`.

## Was die ausgelieferten Pakete nutzen

In den zwei Quell-Repositorys von 7x am 18.10.2026 geprüft:

| Pakete | `<number>` | `<release>` | `<ezpublish><version>` |
|---|---|---|---|
| Die 5 Seitenpakete, `cjw_multisite_democontent`, beide Pakete `sevenx_multisite_democontent` | `6.0` | `15-stable` | `6.0.15-stable` |
| `sevenx_democontent` | `6.0` | `14-stable` | `6.0.14-stable` |
| `sevenx_banners`, `sevenx_classes`, `sevenx_democontent_clean`, die drei `sevenx_design_*` | `6.0` | `10-stable` | `6.0.10-stable` |
| `sevenx_extension` | `6.0` | `6.0.15-stable` | `6.0.15-stable` |

Das Release folgt der Patch-Nummer eines Release von Exponential 6.0 (10, 14, 15), demselben Release, das `<ezpublish><version>` nennt. Die
Seitenpakete verlangen ihre Abhängigkeiten mit `min-version="5.1"` oder `min-version="1.0"`, und in der `index.xml` von 6.0.15 werden die
Seitenpakete von 7x mit `version="6.0-15-stable"` aufgeführt, demselben Text wie ihr `<number>-<release>` (bis zum 19.10.2026 wurden die vier
Seitenpakete von 7x als `1.0.0-0` und `cjw_multisite` als `6.0.15-0` aufgeführt).

## Fallen

**Ein Release mit einem Zustandswort (in 6.0.15 behoben).** Ab 6.0.15 liest `compareFull()` Ziffern, gefolgt von einem Zustandswort, als
Release: `6.0-15-stable` (und `6.0-15stable`) ist Version `6.0`, Release `15`. Ein stabiles Wort (`stable`, `final`, `release`, `ga`)
bedeutet keine Vorabversion, sodass `6.0-15-stable` gleich `6.0-15` ist und ein `min-version="6.0"` erfüllt. Jedes andere Wort macht eine
Vorabversion dieses Release: `6.0-15-rc1` < `6.0-15`. Dieselben stabilen Wörter nach einer alten Versionsnummer (`6.0.15stable`, `1.0-final`)
sind ebenfalls keine Vorabversion.

> **Hinweis**
> Vor 6.0.15 war ein Release, das nicht nur aus Ziffern bestand, kein Release: `6.0-15-stable` wurde als Version `6.0.0` mit der
> Vorabversion `15-stable` gelesen, niedriger als `6.0`, sodass ein `min-version="6.0"` **nicht** erfüllt war und der Assistent das
> importierte Paket als zu alt behandelte. Releases wurden damals auch als Text verglichen (`9-stable` über `15-stable`); ab 6.0.15 werden sie
> als Zahlen verglichen. Eine Installation älter als 6.0.15 liest sie weiterhin auf die alte Weise, sodass ein Paket für eine solche weiter
> `5.1` oder `1.0` verlangen sollte, wie es die ausgelieferten Seitenpakete tun.

**Auch die Version im Index wird verglichen.** Wenn der Index eine höhere Version führt als ein importiertes Paket, bietet der Schritt der
Seitentypen den Eintrag des Index statt des importierten an (`eZStepSiteTypes::createSitePackagesList()`). Im Kickstart-Modus wird der
Eintrag des Index immer vorgezogen. Halten Sie die Version im Index gleich dem `<number>-<release>` des Pakets.

**Die Produktversion ist keine Prüfung der Kompatibilität.** Nichts vergleicht `<ezpublish><version>` mit dem laufenden Release. Sagen Sie
in der Beschreibung und im README des Quell-Repositorys, für welche Releases ein Paket gedacht ist.

## Empfohlene Form

Für ein neues Paket oder ein neues eigenes Release:

- [ ] `<number>`: Semantic Versioning, drei Zahlen (`1.0.0`, `1.2.0`). Die Assistenten zum Erstellen bestehen darauf: höchstens 24 Zeichen,
      weil die Tabelle `ezpackage` `<number>-<release>` in 30 Zeichen speichert (`eZPackageVersion::MAX_NEW_LENGTH`).
- [ ] `<release>`: eine ganze Zahl, `1` für den ersten Build einer Version, erhöht, wenn dieselbe Version erneut paketiert wird.
- [ ] `<ezpublish><version>`: das Release von Exponential, auf dem Sie es gebaut und getestet haben, etwa `6.0.15-stable`, mit
      `<named-version>6.0</named-version>`.
- [ ] Das Attribut `version` in `index.xml`: dasselbe `<number>-<release>`.
- [ ] Das Attribut `release` des Eintrags im Changelog: dasselbe Release ([Einträge im Changelog](eintraege-im-changelog.md)).
- [ ] Eine `min-version` in einem Seitenpaket, die eine Version nennt, die das verlangte Paket wirklich hat, gleich geschrieben.

```xml
<version>
  <number>1.2.0</number>
  <release>1</release>
</version>
```

Wie Sie Ihre eigenen Releases über die Zeit nummerieren, steht auf [Eigene Pakete versionieren](../eigene/eigene-pakete-versionieren.md).
Die Einstellungen und die Seite der Assistenten stehen in [Paketlizenzen und Semantic Versioning](../../../../en/us/features/6.0/package-licenses-and-versions.md).

## Verwandte Seiten

- [Anforderungen an die Dokumentation von Seitenpaketen](README.md)
- [Abhängigkeiten angeben](abhaengigkeiten-angeben.md)
- [Pflichtfelder in package.xml](pflichtfelder-in-package-xml.md)
- [Das Format index.xml](../paketserver/das-format-index-xml.md)
- [Semantic Versioning 2.0.0](https://semver.org/)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
