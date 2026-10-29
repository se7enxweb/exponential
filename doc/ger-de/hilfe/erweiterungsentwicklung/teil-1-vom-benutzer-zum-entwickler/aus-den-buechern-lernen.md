---
title: Aus den freien Büchern lernen
audience: user, developer, designer, app-creator
category: extension-development
order: 140
summary: Drei Bücher über die Codebasis, von se7enxweb auf GitHub aufbewahrt, zwei davon unter der GNU FDL - was jedes behandelt, wie Sie sie mit Blick auf Exponential 6 lesen und wie Sie sie wiederverwenden.
keywords: Bücher, Lernen, freie Dokumentation, PDF, Quellen, Geschichte, books, learning, Learning eZ Publish, Building an eZ Publish Site, Basics guide, GFDL, free documentation, source, history
type: help
---
# Aus den freien Büchern lernen

Teil 1, Kapitel 14. Über die Codebasis, die Exponential fortführt, wurden mehrere Bücher geschrieben, als sie noch
eZ Publish hieß. Drei davon werden in Repositories der Organisation se7enxweb aufbewahrt. Sie vermitteln die Konzepte,
die sich nicht geändert haben (Inhaltsklassen und -objekte, der Knotenbaum, Templates, Overrides, Siteaccesses,
Erweiterungen), in der ausführlichen, geduldigen Form, die nur ein Buch erlaubt.

## Die drei Bücher

| Repository | Was es ist | Lizenz (wie im Repository angegeben) |
|---|---|---|
| [Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish) | „Learning eZ Publish“, beschrieben als das erste Buch, das über das System veröffentlicht wurde. Ein PDF und seine Quellen | GNU Free Documentation License 1.3 (`LICENSE.md`, `COPYRIGHT.md`, `AUTHORS.md` im Repository) |
| [Book-Building-An-eZPublish-Site](https://github.com/se7enxweb/Book-Building-An-eZPublish-Site) | „Building a eZ Publish Site“, der Bau einer vollständigen Website Schritt für Schritt. Quellen | GNU Free Documentation License 1.3 (`LICENSE.md`, `COPYRIGHT.md`, `AUTHORS.md`) |
| [Book-eZPublish-Basics-GuideBook](https://github.com/se7enxweb/Book-eZPublish-Basics-GuideBook) | „eZ Publish Basics (Guide)“, ein Minibuch. Ein PDF | Die Beschreibung des Repositorys sagt, es sei unter der GNU FDL veröffentlicht worden, aber das Repository enthält keine Lizenzdatei. Verlinken Sie es; kopieren Sie nichts daraus. |

Das Portal Share Exponential verlinkt außerdem eine Bücherseite aus seinem Menü **Learn**
([Share Exponential](share-exponential-earth.md)).

## Sie mit Blick auf Exponential 6 lesen

Die Bücher beschreiben ältere Releases. Lesen Sie sie für die Konzepte und halten Sie diese Tabelle bereit:

| In den Büchern | In Exponential 6 | In diesem Buch |
|---|---|---|
| Installation aus einem Tarball und mit einem Web-Assistenten | `composer`, Git-Klone, `exp:install`, der Kickstarter oder der Setup-Assistent | [Eine Entwicklungsinstallation einrichten](eine-entwicklungsinstallation-einrichten.md) |
| `settings/override/site.ini.append.php` von Hand bearbeiten | Weiterhin möglich; `./console exp:ini` tut es mit einer Sicherung und einem Diff | [Einstellungen mit exp:ini ändern](../teil-2-builder/der-befehl-exp-ini.md) |
| Die Ordner `design/`, das Override-System | Im Prinzip unverändert; mehr Designs (admin4, admin4l) | [Teil 2](../teil-2-builder/README.md) |
| Erweiterungen mit `include_once()` von Kernel-Dateien | Autoloading | [Autoloads und Klassenbenennung](../teil-3-erweiterungsentwickler/autoloads-und-klassennamen.md) |
| Beispiele für PHP 4 und PHP 5 | PHP 8.0 bis 8.5 (PHP 7.4 ohne Support nutzbar) | [Teil 4](../teil-4-aktualisieren/README.md) |
| JavaScript in Templates | Nur Skriptdateien, unter einer Content Security Policy | [Content Security Policy für Erweiterungsautoren](../teil-3-erweiterungsentwickler/content-security-policy-fuer-erweiterungen.md) |

## Ihren Text wiederverwenden

Die beiden Bücher unter der GNU FDL 1.3 dürfen zitiert, übersetzt und bearbeitet werden, unter derselben Lizenz und mit
Quellenangabe. Dieselbe Lizenz gilt für diese Hilfe; ein Abschnitt dieser Bücher kann also Teil einer Hilfeseite
werden, wenn Sie:

1. den Hinweis auf die GNU FDL 1.3 beibehalten (der Hinweis dieser Hilfe ist bereits einer),
2. das Buch nennen und sein Repository verlinken,
3. angeben, was Sie geändert haben (zum Beispiel „für Exponential 6 aktualisiert“),
4. die Copyright-Zeilen der Autoren beibehalten; `AUTHORS.md` und `COPYRIGHT.md` in jedem Repository nennen sie.

Was die GNU FDL im Einzelnen verlangt, steht auf [gnu.org](https://www.gnu.org/licenses/fdl-1.3.html) und auf der Seite
[Freie Dokumentation](../../freie-dokumentation.md) dieser Hilfe. Im Zweifel verlinken Sie stattdessen; siehe
[Verlinken oder kopieren](verlinken-oder-kopieren.md).

## Ein Buch auf den neuesten Stand bringen

Ein Buch unter einer freien Dokumentationslizenz kann jeder verbessern. Wenn Sie ein Kapitel finden, dessen Beispiel
nicht mehr funktioniert, ist ein Pull Request an das Repository des Buchs mit der Fassung für Exponential 6 ein
willkommener Beitrag und ein guter erster Pull Request für neue Mitwirkende (siehe
[Mit Pull Requests beitragen](../teil-5-erweitern-forken-zurueckgeben/mit-pull-requests-zurueckgeben.md)).

## Verwandte Seiten

- Zurück: [eZpedia (ezpedia.exponential.earth)](ezpedia-exponential-earth.md)
- Weiter: [Lizenzen lesen, bevor Sie etwas wiederverwenden](lizenzen-vor-der-wiederverwendung-lesen.md)
- [Wer ist 7x?](../../wer-ist-7x.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
