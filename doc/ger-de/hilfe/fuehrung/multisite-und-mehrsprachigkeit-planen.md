---
title: Multisite- und mehrsprachige Projekte planen
audience: project-manager, admin
category: leadership
order: 70
summary: Mehrere Sites und Sprachen in einer Installation planen: Siteaccesses, Sprachen pro Site, Übersetzungen, Rechts-nach-links-Schrift, die Grenze von 62 Sprachen, Links zwischen Sites und ein Planungsbogen.
keywords: Multisite, mehrsprachig, Sprachen, Übersetzung, Siteaccess, Host, URL, Inhaltssprachen, Locales, Übersetzung der Oberfläche, Rechts-nach-links, Links zwischen Sites, Einstellungen pro Site, Projektleiter, Administrator, multi-site, multilingual, languages, translation, SiteLanguageList, content languages, locales, interface translation, right to left, RTL, cross-site links, ezurlmultisite, per-site settings, project manager, administrator
type: help
---
# Multisite- und mehrsprachige Projekte planen

Diese Seite ist für Projektleiter und Administratoren, die mehr als eine Site, mehr als eine Sprache oder beides in
einer Exponential-Installation planen. Sie beantwortet: was ein Siteaccess ist und wie Besucher ihn erreichen, wie
Sprachen pro Site angezeigt werden, was das Produkt für Sprachen und Rechts-nach-links-Schriften unterstützt, welche
Grenzen Sie kennen sollten, bevor Sie etwas zusagen, und wie Sie die Arbeit planen. Die Einzelheiten stehen in den
Leitfäden und Funktionsseiten, auf die jeder Abschnitt verweist; diese Seite ordnet sie als Entscheidungen an.

## Inhalt

1. [Die Bausteine](#die-bausteine)
2. [Multisite: eine Installation, mehrere Sites](#multisite-eine-installation-mehrere-sites)
3. [Wie Besucher eine Site erreichen](#wie-besucher-eine-site-erreichen)
4. [Mehrsprachigkeit: Inhaltssprachen pro Site](#mehrsprachigkeit-inhaltssprachen-pro-site)
5. [Was das Produkt bei Sprachen unterstützt](#was-das-produkt-bei-sprachen-unterstützt)
6. [Rechts-nach-links-Sprachen](#rechts-nach-links-sprachen)
7. [Grenzen, die Sie kennen sollten, bevor Sie etwas zusagen](#grenzen-die-sie-kennen-sollten-bevor-sie-etwas-zusagen)
8. [Betrieb mit vielen Sites](#betrieb-mit-vielen-sites)
9. [Rollen für Übersetzer und Site-Teams](#rollen-für-übersetzer-und-site-teams)
10. [Der Planungsbogen](#der-planungsbogen)
11. [Checklisten](#checklisten)

## Die Bausteine

| Begriff | Was es ist | Lesen |
|---|---|---|
| **Siteaccess** | „Ein Zugang zur Installation: ein Name, ein Satz Einstellungen in `settings/siteaccess/<name>/` und eine Regel, auf welche Anfragen er antwortet“ | [10.2 Siteaccesses und Site-Adressen](../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site) |
| **Inhaltssprache** | Eine Sprache, in der Inhalte verfasst werden können (Setup > Sprachen) | [Inhaltssprachen](../../anleitungen/inhaltssprachen.md) |
| **Locale** | Die Datei, die eine Sprache und ein Land benennt und Datums-, Zahlen- und Währungsformate sowie die Textrichtung enthält | [Sprachen in Exponential](../../sprachen.md) |
| **Übersetzung der Administration** | Die Texte der Oberfläche für eine Sprache | ebenda |
| **Locale eines Siteaccess** | Die Locale, mit der ein Siteaccess formatiert, und die Übersetzung, die seine Oberfläche verwendet | ebenda |

Vier Dinge werden leicht verwechselt: Locale, Übersetzung der Administration, Inhaltssprache und Locale eines
Siteaccess ([Sprachen, Abschnitt 1](../../sprachen.md#1-locales-übersetzungen-inhaltssprachen-und-siteaccesses)).
Verwenden Sie die Begriffe in Ihren Plänen genau; das vermeidet die meisten Missverständnisse mit dem technischen Team.

## Multisite: eine Installation, mehrere Sites

Die Unterstützung für Multisite ist eine der wichtigsten Funktionen des Kernels, die die README aufführt
([Was Exponential ist](was-exponential-ist.md#was-der-kernel-leistet)). Mehrere Sites aus einer Installation bedeutet,
dass jede Site aus einem oder mehreren Siteaccesses besteht, jeder mit „eigenem Host, Pfad, eigener Sprache und eigenem
Design“ ([Wer ist 7x?](../wer-ist-7x.md#cms-cmf-und-shop-in-einem)). Das voreingestellte Site-Paket des Installers,
`sevenx_multisite`, ist selbst eine Multisite-Installation
([4.3 Site-Pakete](../../installation/04-installationsweg-waehlen.md#43-site-pakete-was-installiert-wird)).

### Formen eines Multisite-Projekts

| Form | Wie sie gebaut wird | Zu bedenken |
|---|---|---|
| Mehrere Marken, ein Inhaltsbaum | Jede Site zeigt ihren eigenen Teilbaum; ein Siteaccess pro Site | Links zwischen den Sites brauchen `ezurlmultisite` ([Links zwischen Sites](../../funktionen/6.0/websiteuebergreifende-links-im-multisite.md)) |
| Eine Site, mehrere Sprachen | Ein öffentlicher Siteaccess pro Sprache, alle mit derselben Datenbank | Siehe [Mehrsprachigkeit](#mehrsprachigkeit-inhaltssprachen-pro-site) |
| Mehrere Sites, jede in einer eigenen Erweiterung | Einstellungen pro Site innerhalb der Erweiterung, nur aktiv, während ihre Siteaccesses laufen | [Einstellungen pro Site in Erweiterungen](../../funktionen/6.0/ini-ueberschreibungen-fuer-mehrere-websites.md) |
| Getrennte Administrationen pro Site | Mehrere Administrations-Siteaccesses | Rollen nach Siteaccess und Teilbaum eingeschränkt |

Fragen, die über die Form entscheiden:

- [ ] Teilen sich die Sites Inhalte (ein Artikel auf zwei Sites) oder nur die Installation?
- [ ] Hat jede Site ihr eigenes Design?
- [ ] Teilen sich die Sites Redakteure, oder hat jede ihr eigenes Team?
- [ ] Teilen sich die Sites eine Datenbank? Siteaccesses, die sich eine teilen, werden in `RelatedSiteAccessList[]`
      aufgeführt ([10.2](../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site)).
- [ ] Werden die Sites gemeinsam aktualisiert? Eine Installation bedeutet eine Kernel-Version für alle.

> **Hinweis**
> Eine Installation ist ein Kernel, ein Satz Erweiterungen und (meist) eine Datenbank. Das ist die Stärke von Multisite
> (ein Ort für Updates und Absicherung) und zugleich ihre Einschränkung (jede Site bewegt sich mit den anderen). Halten
> Sie diese Abwägung im Projektauftrag fest.

## Wie Besucher eine Site erreichen

Der Installer hat eine von drei Varianten gewählt, und sie lässt sich später ändern
([10.2](../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site)):

| Variante | Beispiel | Die Quellen sagen |
|---|---|---|
| URL | `/admin`, `/de` | „ein Hostname für alles; am einfachsten hinter jedem Webserver“ |
| Host | `admin.example.com` | „ein Hostname pro Siteaccess; am besten für den Produktivbetrieb und für HTTPS-Zertifikate pro Host“ |
| Port | `:8081` | „ein Port pro Siteaccess; nützlich für die lokale Arbeit“ |

Weitere Möglichkeiten: eine Host-Zuordnung, die nach Anfang, Ende oder einem Teil eines Hosts sucht (sodass ein
Test-Host den Siteaccess des Live-Hosts erreicht), und eine Weiterleitung von `/` auf die Sprache des Besuchers, gewählt
nach Host und Browsersprache, mit einer Adresse pro Sprache und ohne Cookie
([Siteaccess nach Host-Präfix und die Weiterleitung auf die Sprache](../../funktionen/6.0/siteaccess-zuordnung-nach-host-und-sprache.md)).

Entscheiden Sie gemeinsam mit Ihrem Administrator und der für SEO verantwortlichen Person: ein Host pro Sprache oder
ein Host mit einem Pfad pro Sprache. Das Produkt unterstützt beides; die Wahl betrifft Zertifikate, Webanalyse und die
Art, wie Menschen Links teilen.

## Mehrsprachigkeit: Inhaltssprachen pro Site

So funktioniert es ([Inhaltssprachen](../../anleitungen/inhaltssprachen.md)):

- **Eine Sprache hinzuzufügen übersetzt nichts und ändert keinen Siteaccess.** Redakteure übersetzen Inhalte im Editor;
  der Administrator fügt die Sprache zu `SiteLanguageList` jedes Siteaccess hinzu, der sie anzeigen soll.
- **Jeder Siteaccess zeigt ein Objekt in der ersten Sprache seiner `SiteLanguageList`, die das Objekt hat.** Objekte in
  keiner davon werden ausgeblendet, sofern sie nicht „immer verfügbar“ sind.
- **Jedes Objekt hat eine Hauptsprache**, die nicht entfernt werden kann, solange sie die Hauptsprache ist.
- **Das Entfernen einer Übersetzung löscht Inhalte endgültig**; eine Sprache lässt sich nur entfernen, wenn kein Objekt
  und keine Klasse sie verwendet, sodass dabei nie Inhalte gelöscht werden.
- **Übersetzer können auf ihre Sprache beschränkt werden**: `content/edit`, `content/translate` und `content/create`
  lassen sich im Rolleneditor nach Sprache einschränken.
- **Links zum Sprachwechsel**: `TranslationSA[]` benennt die Siteaccesses der Übersetzungen, und der Operator
  `language_switcher` erzeugt Links zwischen den Sprachversionen einer Seite.

Eine typische Einrichtung, aus [10.14 Mehrsprachige Sites](../../installation/10-nach-der-installation.md#1014-mehrsprachige-sites):

```ini
# settings/siteaccess/site_de/site.ini.append.php
[RegionalSettings]
Locale=ger-DE
ContentObjectLocale=ger-DE
SiteLanguageList[]
SiteLanguageList[]=ger-DE
SiteLanguageList[]=eng-GB
ShowUntranslatedObjects=disabled
TextTranslation=enabled
```

> **Warnung**
> „Ändern Sie `ContentObjectLocale` einer bestehenden Site nicht leichtfertig: Objekte, die es in der neuen
> Standardsprache nicht gibt, verschwinden aus den Listen.“ Und geben Sie bei der Installation jede Sprache an, die das
> Site-Paket braucht, sonst werden Seiten ohne Bilder oder Unterelemente angezeigt
> ([10.14](../../installation/10-nach-der-installation.md#1014-mehrsprachige-sites)).

### Entscheidungen zum Fallback pro Site

Entscheiden Sie für jeden öffentlichen Siteaccess:

| Entscheidung | Möglichkeiten | Wirkung |
|---|---|---|
| Welche Sprachen, in welcher Reihenfolge | `SiteLanguageList[]` | Die erste Sprache, die ein Objekt hat, wird angezeigt |
| Nicht übersetzte Inhalte | `ShowUntranslatedObjects=disabled` (ausgeliefert) oder `enabled` | Ausgeblendet oder in ihrer Hauptsprache angezeigt |
| Gemeinsam genutzte Objekte (Ordner, Benutzer, Medien) | „Immer verfügbar“ pro Objekt, Voreinstellung pro Klasse | In der Hauptsprache angezeigt, wo es keine Übersetzung gibt |

## Was das Produkt bei Sprachen unterstützt

Aus [Sprachen in Exponential](../../sprachen.md):

- **368 Locale-Dateien** (361 Sprachen und Länder, die Testsprache Lorem Ipsum und 6 Varianten), 43
  davon von rechts nach links geschrieben, können als Inhaltssprachen und Locales von Siteaccesses gewählt werden.
- **Übersetzungen der Administration** (die aktuellen Gesamtzahlen stehen am Anfang der Seite [Sprachen in Exponential](../../sprachen.md)), aufgeführt in
  [Sprachen in Exponential](../../sprachen.md).
- **Unterstützungsstufen**:

| Stufe | Bedeutung |
|---|---|
| Vollständig: Oberfläche und Inhalte | Die Administration spricht die Sprache vollständig; Inhalte, Datumsangaben, Zahlen und Währung folgen der Locale |
| Teilweise Oberfläche, vollständige Inhalte | Eine ältere Übersetzung der Administration deckt 16 bis 39 % der heutigen Texte ab; der Rest erscheint auf Englisch |
| Nur Inhalte | Keine Übersetzung der Administration; die Oberfläche bleibt englisch, Inhalte und Formate folgen der Locale |

Planen Sie entsprechend: Redakteure, die die Administration in ihrer eigenen Sprache brauchen, brauchen eine
„vollständige“ Sprache oder ein Übersetzungsprojekt
([Sprachen, Abschnitt 8](../../sprachen.md#8-eine-übersetzung-der-administrationsoberfläche-hinzufügen-oder-vervollständigen)). Eine Locale ohne
eigene Übersetzung greift auf eine andere derselben Sprache zurück, sofern es eine gibt
([Sprachen, Abschnitt 13](../../sprachen.md#13-übersetzungs-fallbacks)).

## Rechts-nach-links-Sprachen

- Wenn die Sprache der Oberfläche von rechts nach links geschrieben wird, dreht sich die Administration um: `dir="rtl"`,
  Seitenspalten vertauscht, Text beginnt rechts ([Rechts nach links](../../funktionen/6.0/rechts-nach-links.md)).
- Inhalte in einer Rechts-nach-links-Inhaltssprache werden dort, wo sie angezeigt und bearbeitet werden, auf dieselbe
  Weise gekennzeichnet, unabhängig von der Sprache der Oberfläche.
- Das Design der öffentlichen Site ist Sache Ihres Projekts: Templates und Stylesheets Ihrer Designs müssen
  Rechts-nach-links beherrschen. Nehmen Sie eine Prüfung der Rechts-nach-links-Darstellung in die Qualitätssicherung auf,
  wenn eine Rechts-nach-links-Sprache zum Umfang gehört.
- Als PDF heruntergeladene Hilfeseiten drucken Rechts-nach-links-Text von rechts nach links; arabische Buchstaben werden
  in ihren Grundformen gezeichnet, ohne Verbindung ([Das Hilfesystem](../../funktionen/6.0/hilfesystem.md)).

## Grenzen, die Sie kennen sollten, bevor Sie etwas zusagen

| Grenze | Wert | Quelle |
|---|---|---|
| Inhaltssprachen pro Installation | höchstens 62 mit 64-Bit-PHP, 30 mit 32-Bit-PHP | [Sprachen, Abschnitt 11](../../sprachen.md#11-die-grenze-von-62-inhaltssprachen) |
| Eine hinzugefügte und nie verwendete Sprache | belegt trotzdem ihren Platz, bis sie entfernt wird | ebenda |
| 64-Bit-PHP | empfohlen „für die Unterstützung von mehr als 30 Sprachen“ | [Unterstützte Betriebssysteme](../betriebssystemunterstuetzung.md) |
| Eine Sprache entfernen | nur, wenn kein Objekt und keine Klasse eine Übersetzung in ihr hat | [Inhaltssprachen](../../anleitungen/inhaltssprachen.md) |
| SQLite und mehrere Maschinen | SQLite braucht einen Host; mehrere Webserver brauchen einen Datenbankserver | [Datenbankunterstützung](../datenbankunterstuetzung.md) |

Die Grenze von 62 Sprachen ist gewollt: Sie anzuheben würde das Datenbankschema jeder Installation ändern, „um einen
Fall zu bedienen, den keine bekannte Site erreicht hat“
([Sprachen, Abschnitt 11](../../sprachen.md#11-die-grenze-von-62-inhaltssprachen)).

## Betrieb mit vielen Sites

Für Administratoren, die mehrere Sites in einer Installation betreiben:

| Bedarf | Funktion | Lesen |
|---|---|---|
| Einstellungen für alle Siteaccesses einer Site, in der Erweiterung dieser Site abgelegt | Override-Einstellungen pro Site | [Einstellungen pro Site in Erweiterungen](../../funktionen/6.0/ini-ueberschreibungen-fuer-mehrere-websites.md) |
| Logs jeder Site in einem eigenen Verzeichnis; der Cache einer Site im Arbeitsspeicher; INI-Cache pro Site geleert | `UseGlobalLogDir`, `LogVarDir`, `CacheVarDir`, `INICacheDir` | [Logs, INI-Cache und Ablauf pro Site](../../funktionen/6.0/log-und-cache-pfade-fuer-mehrere-websites.md) |
| Links von einer Site auf Inhalte einer anderen | `ezurlmultisite` | [Links zwischen Sites](../../funktionen/6.0/websiteuebergreifende-links-im-multisite.md) |
| Die Caches der Sites vorwärmen | Vorladen | [Caches vorladen](../../anleitungen/caches-vorwaermen.md) |
| Der Name der Installation in den Seiten | Name der Installation | [Name der Installation in Seiten](../../funktionen/6.0/installationsname-in-seiten.md) |

## Rollen für Übersetzer und Site-Teams

| Teammitglied | Zu planende Richtlinien | Einschränkung |
|---|---|---|
| Übersetzer für Deutsch | `content/edit` oder `content/translate`, `content/create` | Sprache: Deutsch ([Inhaltssprachen, Abschnitt 7](../../anleitungen/inhaltssprachen.md)) |
| Redakteur nur für Site A | `content/*` wie vereinbart | Teilbaum von Site A oder ihre Sektion ([Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)) |
| Redakteur, der sich nur an einer Administration anmelden darf | `user/login` | Siteaccess |
| Verantwortlicher für eine Sprache | Rechte eines Übersetzers plus Freigabe | Sprache und Workflow |

## Der Planungsbogen

```text
Multisite and language plan for <project name>                  <date>

Sites
| Site        | Siteaccess(es)       | Reached by (URL/host/port) | Design        | Subtree/root      | Editors (group) |
|-------------|----------------------|----------------------------|---------------|-------------------|-----------------|
| <site A>    | <site_a_en, site_a_de> | <host or path>          | <design>      | <node or folder>  | <group>         |
| Administration | <admin>           | <host or path>             | admin4        |                   | <groups>        |

Languages
| Language (locale) | Content language? | Admin translation level | Right to left | Shown on siteaccesses | Translator (group) |
|-------------------|-------------------|-------------------------|---------------|-----------------------|--------------------|
| <eng-US>          | yes               | full                    | no            | <list>                | <group>            |
| <ger-DE>          | yes               | full                    | no            | <list>                | <group>            |

Per siteaccess: SiteLanguageList order, ShowUntranslatedObjects, always-available classes
Cross-site links needed: <yes/no>     Per-site logs/caches: <yes/no>
Content languages in use: <n> of at most 62
Open questions: <list>
```

## Checklisten

**Vor der Umsetzung**

- [ ] Jede Site und jede Sprache steht mit einem Verantwortlichen im Planungsbogen.
- [ ] Zugangsvariante gewählt (URL, Host oder Port) und mit den Verantwortlichen für DNS und Zertifikate abgestimmt.
- [ ] Die Stufe der Administrationsübersetzung jeder Sprache gegen das geprüft, was ihre Redakteure brauchen.
- [ ] Rechts-nach-links-Designs ausdrücklich im Umfang oder ausdrücklich ausgeschlossen.
- [ ] Die Zahl der Sprachen liegt deutlich unter der Grenze, mit Raum für Wachstum.

**Vor dem Start**

- [ ] Jeder Siteaccess zeigt zuerst die erwartete Sprache und den erwarteten Fallback.
- [ ] „Immer verfügbar“ ist bei Ordnern, Benutzern und Medien gesetzt, die jede Sprache braucht.
- [ ] Links zum Sprachwechsel in beide Richtungen getestet.
- [ ] Links zwischen Sites führen zur richtigen Site (kein 404).
- [ ] Übersetzungen für jede veröffentlichte Seite jeder Sprache vollständig.
- [ ] Rechts-nach-links-Seiten von jemandem geprüft, der die Sprache liest.

## Verwandte Seiten

- [Inhaltssprachen](../../anleitungen/inhaltssprachen.md)
- [Sprachen in Exponential](../../sprachen.md)
- [Sprachen für Redakteure](../sprachen.md)
- [Rechts nach links](../../funktionen/6.0/rechts-nach-links.md)
- [Übersetzungen und Sprachen](../../funktionen/6.0/uebersetzungen-und-sprachen.md)
- [Kapitel 10: Nach der Installation](../../installation/10-nach-der-installation.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
