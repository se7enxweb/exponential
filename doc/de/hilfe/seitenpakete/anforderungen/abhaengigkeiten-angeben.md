---
title: Abhängigkeiten angeben
audience: developer, partner, admin
category: install-upgrade
order: 57
summary: Wie ein Seitenpaket die Pakete nennt, die es braucht, das Element require mit type, name und min-version, warum die Liste flach und in Installationsreihenfolge sein muss, was der Setup-Assistent und der Schritt zum Anlegen der Websites damit tun, welche Fehler eine kaputte Abhängigkeit ergibt und wie die ausgelieferten Pakete ihre angeben.
keywords: Abhängigkeiten, requires, require, min-version, ezpackage, provides, conflicts, obsoletes, Installationsreihenfolge, flache Liste, index.xml, EZSW-050, EZSW-051, URL unbekannt, benötigt von, dependencies, install order, flat list, URL is unknown, required by
type: help
---
# Abhängigkeiten angeben

Diese Seite ist für Entwickler, die ein Seitenpaket aus anderen Paketen bauen: Inhaltsklassen, Demo-Inhalte, Seitenstile, Erweiterungen. Ein
Seitenpaket installiert selbst fast nichts; es nennt die Pakete, die die Arbeit machen, und der Setup-Assistent holt und installiert sie. Ein
falscher oder fehlender Eintrag hier ist der häufigste Grund, warum eine neue Website nur halb installiert wird. Der allgemeine Mechanismus
steht auf [Abhängigkeiten und die Installationsreihenfolge](../grundlagen/abhaengigkeiten-und-die-installationsreihenfolge.md); diese Seite behandelt, was Sie
schreiben.

## Inhalt

1. [Das Element requires](#das-element-requires)
2. [Was der Code damit tut](#was-der-code-damit-tut)
3. [Regeln, die aus dem Code folgen](#regeln-die-aus-dem-code-folgen)
4. [Was die ausgelieferten Pakete angeben](#was-die-ausgelieferten-pakete-angeben)
5. [Die anderen Abschnitte zu Abhängigkeiten](#die-anderen-abschnitte-zu-abhängigkeiten)
6. [Fehler und was sie bedeuten](#fehler-und-was-sie-bedeuten)
7. [Checkliste](#checkliste)
8. [Verwandte Seiten](#verwandte-seiten)

## Das Element requires

```xml
<dependencies>
  <provides />
  <requires>
    <require type="ezpackage" name="sevenx_classes" min-version="5.1" />
    <require type="ezpackage" name="sevenx_multisite_democontent" min-version="5.1" />
  </requires>
  <obsoletes />
  <conflicts />
</dependencies>
```

Das ist das `<dependencies>` von `sevenx_multisite`. `eZPackage::parseDependencyTree()` behält jedes Attribut eines `<require>`, wie es ist.

| Attribut | Bedeutung | Erzwungen |
|---|---|---|
| `type` | `ezpackage` für ein Paket | Ja: Der Schritt zum Anlegen der Websites installiert nur Anforderungen `type="ezpackage"` |
| `name` | Der `<name>` des verlangten Pakets | Ja: Es wird lokal und in `index.xml` unter diesem Namen nachgeschlagen |
| `min-version` | Die niedrigste annehmbare `<number>-<release>` | Verglichen von `eZPackageVersion::compareFull()`; der Kickstart-Weg liest sie ohne Rückfall |

## Was der Code damit tut

1. **Schritt der Seitentypen** (`eZStepSiteTypes::downloadDependantPackages()` in `kernel/setup/steps/ezstep_site_types.php`). Für jedes
   `<require>` des gewählten Seitenpakets: Wenn kein Paket dieses Namens importiert ist oder das importierte älter als `min-version` ist,
   wird das alte entfernt und das Paket von der `url` seines Eintrags in derselben `index.xml` heruntergeladen und dann importiert.
2. **Kickstart** (`init()` desselben Schritts). Für ein bereits importiertes Seitenpaket prüft er jede Anforderung gegen `min-version` und
   bleibt auf dem Schritt, solange eine fehlt oder zu alt ist.
3. **Schritt zum Anlegen der Websites** (`kernel/setup/steps/ezstep_create_sites.php`). In der Reihenfolge von `<requires>` holt er jedes
   verlangte Paket; die mit `install_type="install"` werden installiert, die anderen (Seitenstile) nur gelesen, zum Beispiel für die
   CSS-Dateien des Seitenstils.
4. **Installation** (`eZPackage::installUnaudited()`). Ein Paket zu installieren installiert zuerst seine eigenen verlangten Pakete, wenn sie
   im Repository vorhanden sind.
5. **Die Paketseite** zeigt **Benötigt**, mit `>=` und der `min-version`, und **fehlt** für ein Paket, das nicht da ist, sowie
   **Benötigt von** aus den Listen der anderen Pakete (`kernel/classes/ezpackagecatalog.php`).

## Regeln, die aus dem Code folgen

**Führen Sie jedes Paket direkt auf.** Schritt 1 lädt nur die Anforderungen des gewählten Seitenpakets herunter, nicht die Anforderungen
dieser Anforderungen. Ein Paket, das die Website braucht, erreicht die Installation nur, wenn das Seitenpaket es nennt oder wenn es vorher
importiert wurde.

**Reihenfolge ist Installationsreihenfolge.** Schritt 3 installiert in der geschriebenen Reihenfolge. Setzen Sie die Inhaltsklassen vor die
Inhalte, die sie nutzen, und Erweiterungen vor Inhalte, die ihre Datentypen brauchen.

**Jede Anforderung muss auf demselben Paketserver liegen.** Eine Anforderung, die nicht importiert ist und keinen Eintrag in der vom
Assistenten gelesenen `index.xml` hat, lässt sich nicht herunterladen (die URL ist unbekannt).

**Schreiben Sie `min-version` in der Form, die das verlangte Paket nutzt.** Eine `min-version`, die höher ist als die Version, die das Paket
wirklich hat, lässt den Assistenten es bei jedem Lauf entfernen und erneut herunterladen und blockiert einen Kickstart. Weil die
ausgelieferten Pakete Releases wie `15-stable` nutzen, erfüllen sie ein `min-version="6.0"` nicht; siehe
[Versionsnummern](versionsnummern.md#fallen).

## Was die ausgelieferten Pakete angeben

In den Quell-Repositorys am 18.10.2026 geprüft. Nur die fünf Seitenpakete haben Anforderungen; keines der Pakete, die sie verlangen, hat
welche.

| Seitenpaket | Verlangt, in Reihenfolge | min-version |
|---|---|---|
| `sevenx_multisite`, `sevenx_multisite_clean` | `sevenx_classes`, dann das passende Paket `sevenx_multisite_democontent` | `5.1` |
| `cjw_multisite` | `sevenx_classes`, `cjw_multisite_democontent` | `5.1` |
| `sevenx_site` | `ezwt_extension`, `ezstarrating_extension`, `ezgmaplocation_extension`, `ezpaypal_extension`, `sevenx_extension`, `sevenx_classes`, `sevenx_banners`, `sevenx_democontent`, `sevenx_design_cleangray`, `sevenx_design_gray`, `sevenx_design_blue` | `5.1`, außer `1.0` für `ezstarrating_extension`, `ezgmaplocation_extension` und `ezpaypal_extension` |
| `sevenx_site_clean` | 10 Pakete nach demselben Muster | `5.1` und `1.0` |

Alle stehen in der `index.xml` von 6.0.15. `cjw_multisite` liegt in einem eigenen Quell-Repository, verlangt aber `sevenx_classes` aus dem von
7x: Anforderungen gehen frei über Repositorys hinweg, solange der Index beide aufführt.

## Die anderen Abschnitte zu Abhängigkeiten

`<provides>`, `<obsoletes>` und `<conflicts>` werden in derselben Form gelesen und geschrieben. Weder der Setup-Assistent noch die
Installation handeln nach `<obsoletes>` oder `<conflicts>`. `<provides>` füllen die Paket-Ersteller (zum Beispiel ein Eintrag
`ezcontentclass` pro Klasse), und die Paketseite liest es, um die Klassen aufzulisten, die ein Paket mitbringt. Die ausgelieferten
Seitenpakete lassen alle drei leer.

## Fehler und was sie bedeuten

| Meldung | Wo | Ursache |
|---|---|---|
| „Der Download des Pakets '...' ist gescheitert. Sie können das Paket manuell uploaden.“ | Schritt der Seitentypen | Die Anforderung hat keine `url` in `index.xml` |
| „Der Download des Pakets '...' ist nach N Versuchen fehlgeschlagen: ...“ | Schritt der Seitentypen | Die `url` hat nicht geantwortet; siehe [Wenn der Paketserver nicht erreichbar ist](../paketserver/wenn-der-paketserver-nicht-erreichbar-ist.md) |
| `EZSW-050` (`Could not fetch required package: '...'`) | Schritt zum Anlegen der Websites | Die Anforderung liegt nicht im Repository |
| `EZSW-051` (`Unable to install package '...'`) | Schritt zum Anlegen der Websites | Die Installation der Anforderung ist gescheitert |

## Checkliste

- [ ] Jedes Paket, das die Website braucht, ist ein `<require type="ezpackage">` des Seitenpakets selbst.
- [ ] Die Reihenfolge ist zuerst Klassen und Erweiterungen, dann Inhalte, dann Seitenstile.
- [ ] Jeder `name` ist genau so geschrieben wie der `<name>` dieses Pakets.
- [ ] Jede `min-version` wird von der Version erfüllt, die das Paket wirklich hat.
- [ ] Jedes verlangte Paket steht in derselben `index.xml`, mit funktionierender `url`.
- [ ] Eine frische Installation in einer Sandbox endet ohne `EZSW-050` oder `EZSW-051`.

## Verwandte Seiten

- [Anforderungen an die Dokumentation von Seitenpaketen](README.md)
- [Abhängigkeiten und die Installationsreihenfolge](../grundlagen/abhaengigkeiten-und-die-installationsreihenfolge.md)
- [Versionsnummern](versionsnummern.md)
- [Das Format index.xml](../paketserver/das-format-index-xml.md)
- [In einer Sandbox testen](../eigene/in-einer-sandbox-testen.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
