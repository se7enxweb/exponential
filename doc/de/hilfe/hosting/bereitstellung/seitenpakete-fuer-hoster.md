---
title: Seitenpakete für Hoster
audience: hosting
category: hosting
order: 60
summary: Was eine Exponential-Installation auf eine neue Website bringt - die Seitenpakete, die der Paketindex anbietet (sevenx_multisite und die anderen, mit oder ohne Demo-Inhalte), woher Pakete kommen (entfernter Index, lokales Repository, Hochladen), Installationen ohne Internet und wie ein Hoster pro Tarif wählt.
keywords: Seitenpaket, sevenx_multisite, sevenx_multisite_clean, sevenx_site, Demo-Inhalte, clean, Paketindex, RemotePackagesIndexURL, Paket-Repository, var/storage/packages, Installation ohne Internet, ezpkg, Installationsskripte von Paketen, site package, demo content, package index, package repository, offline install, package install scripts
type: help
---
# Seitenpakete für Hoster

Diese Seite ist für Hoster, die wählen, was eine neue Kunden-Website bei der Installation enthält. Sie beantwortet: welche Seitenpakete es
gibt, was „clean“ bedeutet, woher der Installer Pakete holt und wie Sie ohne Internetzugang installieren. Die Referenz ist
[4.3 Seitenpakete](../../../installation/04-installationsweg-waehlen.md#43-site-pakete-was-installiert-wird).

## Inhalt

1. [Was ein Seitenpaket ist](#was-ein-seitenpaket-ist)
2. [Die Pakete, aus denen Sie wählen](#die-pakete-aus-denen-sie-wählen)
3. [Woher Pakete kommen](#woher-pakete-kommen)
4. [Installationen ohne Internet und unter Kontrolle](#installationen-ohne-internet-und-unter-kontrolle)
5. [Pro Tarif wählen](#pro-tarif-wählen)

## Was ein Seitenpaket ist

Der Installer installiert ein **Seitenpaket**: ein Exponential-Paket vom Typ `site` plus die Pakete, die es verlangt. Pakete liegen im
Paket-Repository unter `var/storage/packages/`; das Vendor-Verzeichnis wird durch `settings/package.ini [RepositorySettings] Vendor`
(`7x`) festgelegt.

Ein Seitenpaket kann Installationsskripte mitbringen, die an festen Punkten von `CreateSites` laufen (vor der Installation, Einstellungen,
Rollen, Präferenzen, nach der Installation). Dieser „Multi-Site-Installer“ ist keine vierte Installationsmethode: Er ist der Teil des
Seitenpakets an jeder Installation, welche Oberfläche sie auch gestartet hat.

## Die Pakete, aus denen Sie wählen

Der Paketindex dieser Version bietet elf Seitenpakete an; die, die Sie am ehesten wählen:

| Seitenpaket | Zusammenfassung im Index | Hinweise |
|---|---|---|
| `sevenx_multisite` | 7x MultiSite Default Installation | der Standard von `exp:install --package` und `exp:kickstarter ini --yes`; mit Demo-Inhalten |
| `sevenx_multisite_clean` | dasselbe, ohne Demo-Inhalte | |
| `sevenx_site`, `sevenx_site_clean` | 7x Simple Website Interface | eine einfachere Website, mit oder ohne Demo-Inhalte |
| `ezwebin_site`, `ezflow_site`, `ezdemo_site` (jeweils auch `..._clean`), `plain_site` | die älteren Seitenpakete | für Websites behalten, die darauf aufbauen |

Ein Paket `_clean` installiert dieselbe Struktur ohne die Demo-Artikel und -Bilder: Wählen Sie es für eine echte Website, die Sie selbst
füllen, und das vollständige, um Exponential zu bewerten.

## Woher Pakete kommen

| Quelle | Wie |
|---|---|
| Der entfernte Paketindex | `[RepositorySettings] RemotePackagesIndexURL` in `settings/package.ini`; ein aufgeführtes Paket, das noch nicht importiert ist, wird heruntergeladen und importiert, mit den Paketen, die es verlangt |
| Das lokale Repository | ein Paket, das bereits unter `var/storage/packages/` importiert ist, wird so genutzt, wie es ist |
| Ein Hochladen | die Seite Seitenpaket des Assistenten hat ein Feld **Paket hochladen** für eine Datei `.ezpkg` |

## Installationen ohne Internet und unter Kontrolle

Wenn sich der Index nicht herunterladen lässt, sucht `SiteTypes` stattdessen nach einer `index.xml` im System-Repository; so funktioniert
eine Installation ohne Internet. Für einen Hoster heißt das:

- Server ohne ausgehenden Internetzugang können aus einem lokalen Repository installieren, das Sie einmal füllen.
- Ein Hoster, der möchte, dass jede Kunden-Website mit demselben getesteten Satz von Paketen beginnt, kann diesen Satz lokal halten und
  mit dem Kickstarter installieren, der Methode für den Fall, dass das Seitenpaket aus einem lokalen Paket-Repository kommt
  ([Der Kickstarter für Hoster](der-kickstarter-fuer-hoster.md)).
- Ein Probelauf (`exp:install --dry-run` oder `exp:kickstarter run --dry-run`) holt die Pakete in ein temporäres Repository und prüft
  sie, ohne zu installieren.

## Pro Tarif wählen

| Kunde | Paket |
|---|---|
| möchte Exponential ausprobieren | `sevenx_multisite` (Demo-Inhalte) |
| beginnt eine echte Website | `sevenx_multisite_clean` oder `sevenx_site_clean` |
| migriert eine bestehende Website | keines davon: die Website kommt aus ihrem Backup ([Kunden migrieren](../migrationen/README.md)) |

Installieren Sie mit `exp:install --package=<name>` oder mit `Site_package` im Abschnitt `[site_types]` von `kickstart.ini`
([6.4](../../../installation/06-kickstarter.md#64-referenz-zu-kickstartini)).

## Verwandte Seiten

- [Ein-Klick-Installer](ein-klick-installer-fuer-exponential.md)
- [Mehrere Websites in einer Installation](mehrere-websites-in-einer-installation.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
