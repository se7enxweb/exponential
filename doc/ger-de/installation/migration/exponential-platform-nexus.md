---
title: Exponential 6 und Exponential Platform Nexus
audience: admin, developer, project-manager
category: install-upgrade
order: 21
summary: Eine Exponential-6-Site in Exponential Platform Nexus überführen (Linien 1.0.0.x und v2.5.0.x auf Platform 2.5, 1.1.0.x auf 3.3, 1.2.0.x auf 4.6, 1.3.0.x auf v5) und zurück – welche Linie, die Datenbank, die Demo-Site, die Sie nicht wollen, Netgen Layouts im Vergleich zu Exponential Layouts, Prüfung, Rückweg und Checkliste.
keywords: Exponential Platform Nexus, Nexus, Netgen Layouts, Media-Site, Site-Bundle, Migration, media site, site bundle, 1.0.0.x, 1.1.0.x, 1.2.0.x, 1.3.0.x, v2.5.0.x, CJW Nexus, Exponential Layouts, nglayouts:export, exp:layouts, migration
type: install
---
# Exponential 6 und Exponential Platform Nexus

**Exponential Platform Nexus** (`se7enxweb/exponential-platform-nexus`) ist eine vollständige Site-Distribution: Netgen
Layouts mit seinem visuellen Editor, die Netgen Media-Site, Tags, die Site API und eine Administration, auf je einer
Generation von Exponential Platform pro Release-Linie. Die Legacy-fähigen Linien betreiben über die Legacy-Bridge
außerdem den Kernel von Exponential 6.

[Übersicht Migration](README.md) · [Checkliste](checkliste.md) · Das eigene Handbuch des Produkts:
[Nexus, Kapitel 12: Migration nach Nexus](https://github.com/se7enxweb/exponential-platform-nexus/blob/1.3.0.x/doc/book/12-migrating-into.md) und
[Kapitel 11: Upgrade zwischen Linien](https://github.com/se7enxweb/exponential-platform-nexus/blob/1.3.0.x/doc/book/11-upgrading-between-lines.md)

## Inhalt

- [1. Die Release-Linien](#1-die-release-linien)
- [2. Welche Linie Sie anstreben sollten](#2-welche-linie-sie-anstreben-sollten)
- [3. Von Exponential 6 zu Nexus](#3-von-exponential-6-zu-nexus)
- [4. Seitenaufbau: Exponential Layouts und Netgen Layouts](#4-seitenaufbau-exponential-layouts-und-netgen-layouts)
- [5. Von Nexus zu Exponential 6](#5-von-nexus-zu-exponential-6)
- [6. Prüfen, Rückweg](#6-prüfen-rückweg)
- [7. Risiken](#7-risiken)
- [8. Checkliste](#8-checkliste)

## 1. Die Release-Linien

„Nexus 2.5“, „Nexus 4.x“ und „Nexus 5.x“ sind die Plattform-Generationen innerhalb von Nexus, nicht seine
Versionsnummern. Ausgelesen aus der `composer.json` der Release-Tags am 2026-10-18:

| Nexus-Linie | Neuestes Release | Generation | Symfony | PHP | Legacy-Kernel |
|---|---|---|---|---|---|
| `1.0.0.x` | `1.0.0.11` | Fork des Kernels von eZ Platform 2.5 (`~7.5.40`) | 3.4 | `^7.1.3` bis `^8.6` | ja: `se7enxweb/exponential ^6.0.12`, `legacy-bridge ^2.1` |
| `v2.5.0.x` (Branch `master`) | `v2.5.0.7` | 2.5 | 3.4 | wie 1.0.0.x | ja |
| `1.1.0.x` | `v1.1.0.8` | 3.3 (`se7enxweb/oss ~3.3.0`) | 5.4 | `^8.0` | ja: `legacy-bridge ^3.0`, `site-legacy-bundle ^2.0` |
| `1.2.0.x` | `v1.2.0.1` | 4.6 (`se7enxweb/oss ~4.6.0`) | 5.4 | `>=8.2` | ja: `site-legacy-bundle v2.0.0` mit dem Legacy-Installer |
| `1.3.0.x` | `1.3.0.6` | v5 (`exponential-platform-dxp dev-master`) | 7.4 | `>=8.4` | **nein** |

Verwandte Produkte: **CJW Exponential Platform Nexus** (`se7enxweb/cjw-exponential-platform-nexus`, `1.0.0.12`, die
Generation 2.5 mit Exponential 6 und mitgelieferten Vendor-Dateien), dessen Starter (`1.0.0.3`) und der **Nexus Starter**
(`se7enxweb/exponential-platform-nexus-starter`, `1.0.0.0`, Platform v5, kein Legacy-Kernel in seiner `composer.json`).

> **Warnung**
> Fordern Sie bei Composer oder Git immer eine ausdrücklich angegebene Nexus-Version an. Das Repository enthält Tags der
> Netgen Media-Site, von der es abgezweigt wurde; ein `composer create-project` ohne Version kann daher eines davon
> statt Nexus installieren ([Kapitel 16.1](../16-migration-von-ez-platform-und-ibexa.md#161-die-beiden-ziele)).

## 2. Welche Linie Sie anstreben sollten

Die Regel des Produkts lautet **zuerst dieselbe Generation**. Eine Exponential-6-Datenbank ist eine Legacy-Datenbank,
also:

| Sie haben | Erstes Ziel | Danach |
|---|---|---|
| Exponential 6 | `1.0.0.x` (2.5): Es betreibt Ihren Exponential-6-Kernel neben dem Symfony-Stack auf derselben Datenbank | Linie für Linie aufsteigen, mit Kapitel 11 des Produkts |
| Exponential Platform Legacy `v2.5.0.x`, `v3.3.44.x`, `v4.6.23.x` | `1.0.0.x`, `1.1.0.x`, `1.2.0.x` | die Netgen-Pakete, die Layouts-Tabellen und die Konfiguration des Site-Bundles ergänzen |
| Exponential Platform Legacy `v5.0.x` | `1.3.0.x` hat keinen Legacy-Kernel | zuerst portieren, was die Legacy-Hälfte erledigt hat, oder bei Platform Legacy bleiben und Netgen Layouts selbst ergänzen |

Die Linien `1.0.0.x` bis `1.2.0.x` halten die Legacy-Administration und Ihre Legacy-Extensions lauffähig, während Sie
portieren. `1.3.0.x` ist eine reine Symfony-Anwendung: Das Handbuch des Produkts führt auf, was wegfällt (die
Legacy-Bundles, `ezpublish_legacy/`, der Legacy-Cron, die Siteaccesses `legacy_admin` und `ngadminui`).

## 3. Von Exponential 6 zu Nexus

Kapitel 12.4 des Produkts („From eZ Publish 4.x and Exponential 6“) beschreibt das Verfahren; kurz gesagt:

1. **Bestandsaufnahme, Einfrieren, Sichern** ([Checkliste](checkliste.md)).
2. **Bringen Sie die Datenbank auf das neueste Schema von Exponential 6** ([Kapitel 11](../11-aktualisieren.md)) und
   prüfen Sie sie mit `ezsqldiff.php`, zuerst das Referenzschema und dann Ihre Datenbank.
3. **Legen Sie die Tabellen an, die der Kernel 2.5 erwartet** und die einer reinen Legacy-Datenbank fehlen:
   Vergleichen Sie mit `data/mysql/schema.sql` des installierten `se7enxweb/ezpublish-kernel` 7.5 und legen Sie die
   fehlenden an ([Kapitel 16.6.1](../16-migration-von-ez-platform-und-ibexa.md#1661-was-beide-gemeinsam-haben)).
4. **Legen Sie das Nexus-Projekt `1.0.0.x` aus einem Release-Tag an** (zum Beispiel `git clone -b 1.0.0.x ...` und
   `git checkout 1.0.0.11`, dann `composer install`). **Führen Sie den Installer nicht** gegen Ihre Datenbank aus: Er
   baut ein Repository von Grund auf neu. Das Paket mit den Demodaten kann installiert bleiben; nur der Installer liest es.
5. **Legen Sie Ihre Legacy-Extensions und Siteaccess-Einstellungen** dort ab, wo das Projekt sie hält
   (`src/AppBundle/ezpublish_legacy/`, von den Skripten des Projekts nach `ezpublish_legacy/` verlinkt), kopieren Sie
   den Speicher nach `ezpublish_legacy/var/<site>/storage` und richten Sie `app/config/parameters.yml` auf die
   Datenbank aus.
6. **Ersetzen Sie die Host-Zuordnung der Demo** in der Siteaccess-Konfiguration durch Ihre eigenen Hostnamen.
7. **Legen Sie die Tabellen von Netgen Layouts an** (die Layouts-Migrationen unter MySQL oder die Schemadatei, die das
   Layouts-Paket mitliefert) und setzen Sie die **Ort-Parameter des Site-Bundles** (`ngsite.default.locations.site_info.id`,
   `ngsite.default.locations.tree_root.id` und die übrigen, die Kapitel 12.9 des Produkts aufführt) auf Orte in
   *Ihrem* Baum. Eine falsche ID führt zu einem 404 oder einem leeren Block, nicht zu einem Fehler bei der Bereitstellung.
8. **Lassen Sie die Legacy-Siteaccesses weiterlaufen** (`legacy_mode: true`), während der öffentliche Siteaccess
   Inhaltstyp für Inhaltstyp auf Twig-Templates und Netgen Layouts umzieht.
9. **XmlText bleibt unter `1.0.0.x` XmlText** (`se7enxweb/ezplatform-xmltext-fieldtype v1.8.11` gehört zum Projekt).
   Wandeln Sie mit `ezxmltext:convert-to-richtext` (aus diesem Paket) nur dann in RichText um, wenn Sie es in der
   Symfony-Administration brauchen, spätestens aber vor dem Wechsel auf `1.1.0.x`.
10. **Passwörter**: Der Kernel 2.5 akzeptiert die Legacy-Hash-Typen; ab 3.0 funktionieren nur die Typen 6 und 7.
    Bitten Sie Benutzer mit älteren Typen um ein Zurücksetzen, bevor Sie auf `1.1.0.x` wechseln.
11. **Bauen Sie** Caches, Assets und den Suchindex auf, prüfen Sie und proben Sie die Umstellung an einer frischen Kopie.

Ihre eigenen Inhaltstypen werden mit Ihren eigenen Templates dargestellt: Ordnen Sie sie in der View-Konfiguration
zu, statt sie in die Demo-Typen `ng_*` umzubenennen.

## 4. Seitenaufbau: Exponential Layouts und Netgen Layouts

Exponential Layouts (`se7enxweb/explayouts`) ist die Portierung von Netgen Layouts auf den Kernel von Exponential 6:
dieselbe Datenstruktur auf `explayouts_*`-Tabellen, der Editor als Legacy-Modul ([Kapitel 16.6.7](../16-migration-von-ez-platform-und-ibexa.md#1667-eine-durchgeführte-portierung-netgen-layouts-und-die-media-site)).
Es sind zwei Implementierungen; es gibt keinen Befehl, der die eine in die andere kopiert.

| Richtung | Was es gibt | Was Sie tun |
|---|---|---|
| Zwischen Exponential-Installationen | `./console exp:layouts export` / `import` (ZIP-Transferdatei) | das übliche Werkzeug |
| Zwischen Nexus-Installationen | `php bin/console nglayouts:export layout <uuid>` / `nglayouts:import` (JSON) | Kapitel 12.11 des Produkts |
| Exponential Layouts zu Netgen Layouts | kein Konverter | die Layouts im Nexus-Editor neu aufbauen oder ein Skript schreiben, das die `explayouts_*`-Zeilen in die `nglayouts_*`-Tabellen überträgt (die Portierung folgt der Netgen-Datenstruktur, Kapitel 16.6.7) |
| Netgen Layouts zu Exponential Layouts | die explayouts-Befehle `ext:explayouts:updatelinklocationreferences` (Block-Links von `ibexa-location://<id>` zu `exp-remote-location://<remote id>`) und `ext:explayouts:updatecomponentblockidentifiers` (`ibexa_component_<type>` zu `exp_component_<type>`) wandeln Daten um, die von der Symfony-Seite stammen | die Zeilen kopieren (mit einem eigenen Skript: Tabellen- und Spaltennamen unterscheiden sich) und dann beide Befehle ausführen |

Layouts verweisen über ID oder Remote-ID auf Inhalte. Halten Sie die Remote-IDs identisch, wenn Inhalte zwischen
Installationen umziehen, sonst zeigen die Blöcke ins Leere (Kapitel 12.11 des Produkts).

## 5. Von Nexus zu Exponential 6

Der Wechsel von einer Nexus-Linie zurück zu Exponential 6 allein ist „Pfad B“ aus
[Kapitel 16.6](../16-migration-von-ez-platform-und-ibexa.md#166-weg-b-hinunter-zum-legacy-kernel-von-exponential-60):

- **Von `1.0.0.x` bis `1.2.0.x`**: Der Legacy-Kernel ist bereits vorhanden. Installieren Sie Exponential 6, übernehmen
  Sie die Legacy-Einstellungen, Extensions und den Speicher, wenden Sie die Update-Dateien von Exponential 6 an und
  liefern Sie die Site mit Velocity oder PHP-FPM aus. Die Seite zu Platform Legacy enthält dieselben Schritte
  ([Abschnitt 5](exponential-platform-legacy.md#5-von-platform-legacy-zurück-zu-exponential-6-allein)).
- **Von `1.3.0.x`**: Die Datenbank hat die v5-Tabellennamen `ibexa_*` und keine reinen Legacy-Tabellen. Folgen Sie
  Kapitel 16.6.4 (die Umbenennungen oder die Übersetzer-Extension) und 16.6.9 (Zeile „5.0“), legen Sie die reinen
  Legacy-Tabellen neu an und wandeln Sie RichText um oder stellen Sie es dar ([16.6.3](../16-migration-von-ez-platform-und-ibexa.md#1663-feldtypen)).
- **Layouts**: Bauen Sie die Netgen-Layouts als Exponential Layouts neu auf, oder kopieren Sie die Zeilen und führen
  Sie die beiden Umwandlungsbefehle aus [Abschnitt 4](#4-seitenaufbau-exponential-layouts-und-netgen-layouts) aus.
  Das Media-Design gibt es für den Legacy-Kernel als `se7enxweb/sevenx_themes_media`.
- **Twig-Templates** werden von Hand zu TPL; ein Konverter trifft die Syntax, nicht die Semantik (16.6.7).

## 6. Prüfen, Rückweg

Prüfen Sie so, wie es das Handbuch des Produkts beschreibt: Zahlen vorher und nachher, die Startseite und eine Seite
pro Inhaltstyp, eine über Layouts verwaltete Seite, den Layouts-Editor, eine Bildvariante, die Suche, Formulare, die
Anmeldung eines Redakteurs und eines Benutzers mit einem alten Passwort-Hash. Für den Rückweg lassen Sie die Quelle
unangetastet, bis sich die Umstellung bewährt hat; alle Arbeiten erfolgen an Kopien.

## 7. Risiken

| Risiko | Gegenmaßnahme |
|---|---|
| Der Demo-Installer wird auf Ihren Daten ausgeführt | niemals auf einer migrierten Datenbank ausführen |
| Ort-IDs der Demo in der Konfiguration | vor dem Go-live die Konfiguration mit grep nach jeder Ort-ID durchsuchen |
| Eine Installation ohne Version greift ein Upstream-Tag der Media-Site | immer die Version angeben |
| Zu früher Wechsel auf `1.3.0.x` | dort fehlen der Legacy-Kernel und seine Administration; zuerst portieren |
| Layouts, die auf fehlende Inhalte zeigen | Remote-IDs beibehalten; den Import zuerst mit `--mode=skip` ausführen |
| Alte Passwort-Hash-Typen | Zurücksetzen vor 3.0 und späteren Linien |

## 8. Checkliste

- [ ] Linie nach Generation gewählt (`1.0.0.x` ausgehend von Exponential 6); Version ausdrücklich angegeben.
- [ ] Datenbank auf dem neuesten Schema von Exponential 6; 2.5-Tabellen ergänzt; `ezsqldiff.php` ohne Befund.
- [ ] Projekt aus einem Release-Tag; Installer **nicht** auf den Daten ausgeführt; Host-Zuordnung der Demo ersetzt.
- [ ] Legacy-Extensions, Einstellungen und Speicher an ihrem Platz; Legacy-Siteaccesses auf `legacy_mode`.
- [ ] Layouts-Tabellen vorhanden; Ort-Parameter des Site-Bundles zeigen auf Ihren Baum.
- [ ] Seitenstrukturen als Layouts und Regeln neu aufgebaut; Remote-IDs beibehalten.
- [ ] Strategie für XmlText festgelegt; Zurücksetzen von Passwörtern vor 3.0 geplant.
- [ ] Während des Einfrierens an einer frischen Kopie geprüft; Rückweg abgestimmt.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->

Diese Seite ist Teil der Exponential-Dokumentation und freie Dokumentation unter der GNU Free Documentation License
1.3 oder einer späteren Version ([die Lizenz dieser Dokumentation](../../README-LIZENZ.md)).
