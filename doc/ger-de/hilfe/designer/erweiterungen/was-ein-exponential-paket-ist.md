---
title: "Was ein Exponential-Paket (.ezpkg) ist"
audience: designer
category: theme-extensions
order: 340
summary: Ein Exponential-Paket ist ein Verzeichnis mit einer package.xml, exportiert als eine .ezpkg-Datei (ein gzip-komprimiertes tar); was es tragen kann, wo Pakete liegen, wie sie installiert werden und wie es sich von einem Composer-Paket unterscheidet.
keywords: Paket, ezpkg, package.xml, package.json, Repository, var/storage/packages, installieren, importieren, exportieren, Inhaltsklassen, Seitenstil, package, install, import, export, content classes, site style
type: help
---
# Was ein Exponential-Paket (.ezpkg) ist

Diese Seite ist für Designer, die „Paket“ hören und nicht sicher sind, ob ein Composer-Paket oder ein Exponential-Paket
gemeint ist. Beides gibt es; sie haben verschiedene Aufgaben.

## Kurz gesagt

- Ein **Exponential-Paket** ist ein Verzeichnis mit einer `package.xml` (oder im JSON-Format einer `package.json`) und den
  Dateien, die sie nennt: Inhaltsklassen, Inhaltsobjekte, Erweiterungen, Einstellungsdateien, Stylesheets, Bilder.
- Exportiert ist es eine Datei `<name>-<version>.ezpkg`: ein gzip-komprimiertes tar.
- Pakete liegen in Repositories unter `var/storage/packages/` (`local` für hier erstellte oder importierte Pakete, ein
  Verzeichnis pro Anbieter).
- Ein Paket wird **importiert** (aufbewahrt), **installiert** (seine Elemente werden auf der Website angelegt) oder
  **exportiert** (als `.ezpkg` geschrieben).

## Was es tragen kann, nach Ersteller

| Assistent (Ersteller) | Trägt |
|---|---|
| Export von Inhaltsklassen (`ezcontentclass`) | Klassendefinitionen, keine Inhalte |
| Export von Inhaltsobjekten (`ezcontentobject`) | Knoten oder Teilbäume mit ihren Inhalten, wahlweise mit ihren Klassen und Templates |
| Extension-Export (`ezextension`) | Die Dateien von Erweiterungen |
| Seitenstil (`ezstyle`) | Zwei Stylesheets, ihre Bilder und ein Vorschaubild |

## Composer-Paket oder Exponential-Paket

| | Composer-Paket | Exponential-Paket |
|---|---|---|
| Installiert | Dateien (den Theme-Ordner) | Dateien und **Datenbankinhalte** (Klassen, Objekte) |
| Installiert mit | `composer require` | **Setup > Pakete** oder `php ezpm.php` |
| Updates | `composer update` | Eine neue Paketversion, erneut installiert |
| Zu finden auf | Packagist | Wo immer Sie die Datei veröffentlichen |

Viele Themes verwenden beides: ein Composer-Paket für die Erweiterung und darin `.ezpkg`-Dateien für Inhaltsklassen
([Inhaltsklassen-Pakete mitliefern](inhaltsklassen-pakete-beilegen.md)). Die Wahl bespricht
[Composer-Paket oder .ezpkg](composer-oder-ezpkg.md).

## Sicherheit, auf die Sie sich verlassen können

Ein hochgeladenes Archiv wird untersucht, bevor etwas geschrieben wird: Größen- und Eintragsgrenzen, keine Pfade außerhalb
des Pakets, keine Links oder Geräte, eine wohlgeformte `package.xml` und kein vorhandenes Paket desselben Namens. Ein
Paket zu entfernen, löscht sein Verzeichnis, deinstalliert aber nicht, was es installiert hat. Einzelheiten:
[Pakete](../../../anleitungen/pakete.md).

## Verwandte Seiten

- [Der Paketerstellungs-Assistent Schritt für Schritt](paketerstellungs-assistent-schritt-fuer-schritt.md)
- [Klassische XML- und JSON-Pakete](klassische-und-json-paketformate.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
