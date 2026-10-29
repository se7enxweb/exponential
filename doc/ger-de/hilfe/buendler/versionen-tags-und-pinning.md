---
title: Versionen, Tags und Pinning in einem Bundle
audience: bundler, developer
category: bundling
order: 70
summary: Wie Exponential und seine Erweiterungen versioniert werden, warum sich ein veröffentlichter Release-Tag nie ändert und was das für ein Bundle bedeutet, wie Sie das neueste Release richtig finden (Versionssortierung, nicht Namenssortierung), wie sich die Tilde-Bedingungen in der composer.json verhalten und wie Sie Ihr eigenes Bundle versionieren.
keywords: Version, Tag, Release, festlegen, unveränderlich, Versionssortierung, Tilde, Bundle-Version, version, pin, immutable, Packagist, sort -V, version:refname, tilde, ~, branch alias, dev-main, 6.0.x-dev, bundle version
type: help
---
# Versionen, Tags und Pinning in einem Bundle

Diese Seite richtet sich an Bündler, die entscheiden, welche Versionen in ein Bundle kommen und wie das Bundle selbst
nummeriert wird.

## Inhalt

1. [Wie Exponential versioniert wird](#wie-exponential-versioniert-wird)
2. [Ein veröffentlichtes Release ändert sich nie](#ein-veröffentlichtes-release-ändert-sich-nie)
3. [Das neueste Release richtig finden](#das-neueste-release-richtig-finden)
4. [Tilde-Bedingungen](#tilde-bedingungen)
5. [Pinning in einem Bundle](#pinning-in-einem-bundle)
6. [Ihr eigenes Bundle versionieren](#ihr-eigenes-bundle-versionieren)

## Wie Exponential versioniert wird

| Was | Name | Liefert Ihnen |
|---|---|---|
| Release-Tags | `v6.0.0` bis `v6.0.14` (die Linie 6.0) | ein genaues, dauerhaftes Release |
| Standard-Branch | `main` | die laufende Entwicklung der Linie 6.0, derzeit 6.0.15 |
| Release-Branches | `6.0.7` bis `6.0.14` | den Stand jeder Release-Linie |

Quelle: [3.1 Versionen, Tags und Branches](../../installation/03-den-code-beschaffen.md#31-versionen-tags-und-branches). `main`
weist sich als `6.0.15stable` aus (`lib/version.php`), und die `composer.json` gibt ihm den Branch-Alias `6.0.x-dev`,
sodass Pakete, die `se7enxweb/exponential` mit einer Bedingung wie `^6.0.14` verlangen, weiterhin dagegen aufgelöst
werden.

Jede Erweiterung ist ein eigenes Composer-Paket mit eigenen Tags; `ezinfo.php` und `extension.xml` eines Releases
tragen dessen Nummer ([Metadaten von Erweiterungen](../../spezifikationen/6.0/extension-metadaten.md#release-regel)).

## Ein veröffentlichtes Release ändert sich nie

"Einmal veröffentlicht, sind ein Tag und die Paketversion, die Packagist daraus gebaut hat, dauerhaft; eine Korrektur
kommt immer als nächste Version" ([3.1](../../installation/03-den-code-beschaffen.md#31-versionen-tags-und-branches)).
`SECURITY.md` sagt dasselbe für Sicherheitskorrekturen: "Eine Korrektur für ein Problem in `v6.0.14` oder einem
früheren Tag erreicht Sie als nächstes Release, nicht als korrigierte Kopie des alten".

Für ein Bundle bedeutet das:

- Ein festgelegter Tag liefert Ihnen jedes Mal dieselben Dateien. Das macht reproduzierbare Bundles erst möglich.
- Eine Korrektur, die Sie brauchen, steckt in der **nächsten** Version. Planen Sie, Ihr Bundle neu zu
  veröffentlichen, statt auf einen korrigierten Tag zu warten.
- Dieselbe Regel gilt für Ihr eigenes Bundle: Verschieben, löschen oder erstellen Sie einen veröffentlichten Tag nie
  neu. Ist ein veröffentlichtes Bundle fehlerhaft, veröffentlichen Sie die nächste Version und sagen Sie in deren
  Hinweisen, was an der vorigen falsch war.

## Das neueste Release richtig finden

```bash
git ls-remote --tags https://github.com/se7enxweb/exponential.git 'v6*'
git tag -l 'v*' --sort=version:refname | tail -5      # inside a clone, after git fetch --tags
```

"Sortieren Sie immer nach Version (`--sort=version:refname` oder `sort -V`), nie nach Namen: Nach Namen steht
`v6.0.10` vor `v6.0.8`, und ein Skript, das die letzte Zeile einer Namenssortierung nimmt, wählt das falsche
Release." Prüfen Sie zusätzlich die Release-Hinweise unter <https://github.com/se7enxweb/exponential/releases>, denn
ein Tag kann vor seinen Hinweisen existieren.

## Tilde-Bedingungen

Die `composer.json` im Wurzelverzeichnis legt Erweiterungen mit `~` fest, zum Beispiel
`"se7enxweb/ezoracle": "~2.3.8"`. "Die Versionsbedingungen verwenden `~`, wodurch die letzte Stelle frei bleibt:
`~6.0.8` akzeptiert `6.0.9` und `6.0.10`, aber nicht `6.1.0`"
([Standardauswahl an Erweiterungen](../../funktionen/6.0/standard-extension-auslieferung.md#wie-es-funktioniert)).

Die letzte Stelle zählt immer weiter: Nach `.9` kommt `.10`, und eine vierteilige Version wie `~1.1.6.6`
(`bcgooglesitemaps`) lässt ihre vierte Stelle auf dieselbe Weise frei.

## Pinning in einem Bundle

| Schicht | Festlegen mit | Warum |
|---|---|---|
| Der Kernel | einem genauen Tag (`v6.0.14`) oder einem festgehaltenen Commit von `main` | die Basis muss in jedem Build identisch sein |
| Erweiterungen | den `~`-Bedingungen des Kernels plus **Ihrer `composer.lock`** | die Bedingung sagt, was kommen darf; die Lock-Datei sagt, was gekommen ist |
| Ihre eigenen Erweiterungen | genauen Tags Ihrer Repositorys | Sie steuern deren Releases |
| Seitenpakete | der Paketversion in `package.xml` und der Datei, die Sie ausliefern | siehe [Seitenpakete in einem Bundle](seitenpakete-in-einem-bundle.md) |
| Laufzeitumgebung | Image-Digests, Versionen der Betriebssystempakete | damit ein Neubau nicht unbemerkt PHP austauscht |

## Ihr eigenes Bundle versionieren

- Geben Sie dem Bundle eine eigene Version, unabhängig von der von Exponential, und nennen Sie die enthaltene
  Exponential-Version in den Release-Hinweisen ("Example Bundle 3.2, basierend auf Exponential 6.0.14").
- Erhöhen Sie für Korrekturen nur die letzte Stelle; ändern Sie eine höhere Stelle nur als bewusste Entscheidung
  darüber, was das Release bedeutet.
- Schreiben Sie die Version in dem Commit in die Dateien des Bundles, den Sie taggen, nie nach dem Tag.
- Führen Sie ein Änderungsprotokoll mit den Kategorien Added, Updated, Removed, Renamed, damit die Empfänger sehen,
  was sich geändert hat.

## Verwandte Seiten

- [Lock-Dateien und reproduzierbare Builds](lock-dateien-und-reproduzierbare-builds.md)
- [Bundles auf Composer-Basis](bundles-auf-composer-basis.md)
- [Updates und Upgrade-Pfade](updates-und-upgrade-pfade.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
