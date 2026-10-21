---
title: "Ihre Theme-Erweiterung benennen"
audience: designer
category: theme-extensions
order: 30
summary: Die Regeln für den Ordnernamen einer Theme-Erweiterung, ihren Design-Namen, ihr Klassenpräfix und ihren Composer-Paketnamen, mit den Konventionen, denen echte Themes folgen, und den Namen, die Sie vermeiden sollten.
keywords: Benennung, Name der Erweiterung, Ordnername, Design-Name, Paketname, Composer-Name, Vendor, Präfix, exp, sevenx_themes, cjw_themes, naming, extension name, folder name, design name, package name, prefix
type: help
---
# Ihre Theme-Erweiterung benennen

Diese Seite ist für Designer, die ein neues Theme beginnen. Ein Name ist billig gewählt und teuer geändert: Er ist der
Ordnername, der Design-Name in den Einstellungen, das Präfix von Klassen und der Composer-Paketname. Ändern Sie ihn nach
einem Release, muss jede Installation, die das Theme verwendet, folgen.

## Die Namen, die ein Theme hat

| Name | Beispiel | Wo er erscheint |
|---|---|---|
| Name der Erweiterung (Ordner) | `mytheme_jumper` | `extension/<name>/`, `ActiveExtensions[]`, `DesignExtensions[]`, die Klasse `<name>Info` in `ezinfo.php` |
| Design-Name | `jumper` | `design/<design>/`, `site.ini [DesignSettings] SiteDesign` |
| Lesbarer Name | `My Theme Jumper` | `Name` in `ezinfo.php`, `<name>` in `extension.xml`, die Seite Über |
| Composer-Paket | `yourvendor/mytheme_jumper` | `name` in `composer.json`, Packagist |
| Repository | `github.com/yourvendor/mytheme_jumper` | `Info_url`, `homepage` |

Name der Erweiterung und Design-Name dürfen sich unterscheiden. `cjw_themes_jumper` trägt das Design `starter`;
`sevenx_themes_simple` trägt `simple`; `sevenx_themes_media` trägt `media`.

## Die Regeln

1. **Name der Erweiterung**: Kleinbuchstaben, Ziffern und Unterstriche, beginnend mit einem Buchstaben. Die
   RAD-Assistenten erzwingen genau das, mit drei bis einundvierzig Zeichen ([Der Design-Erweiterungs-Assistent](der-design-erweiterungs-assistent.md)).
2. **Design-Name**: dieselben Zeichen. Er darf nicht mit einem Design kollidieren, das es in der Installation schon gibt
   (`standard`, `admin`, `admin3`, `admin4`, `admin4l`, `base`, `ezwebin` und die Designs anderer Themes, die Sie
   kombinieren wollen).
3. **Composer-Paketname**: kleingeschrieben `vendor/name`. Bindestriche und Unterstriche erlaubt Composer beide; den
   Ordner, in den es installiert, bestimmt die Regel aus
   [Der Composer-Installer und der Ordnername der Erweiterung](composer-installer-und-erweiterungsname.md).

## Bewährte Praxis

- **Stellen Sie Ihre Organisation voran**, damit zwei Themes nie kollidieren: `acme_themes_harbor`, nicht `harbor`.
- **Halten Sie den Produktnamen aus dem Namen Ihres Themes heraus.** Ein Theme ist nicht „Exponential Harbor“; es ist
  „Harbor, ein Theme für Exponential“. Siehe [Namen und Marken](../verkauf/namen-und-marken.md) und
  [Marken- und Namensregeln](../../geschaeft/marken-und-namensregeln.md).
- **`sevenx` und `exp` sind die Präfixe von 7x.** Die eigenen Themes von 7x heißen `sevenx_themes_*`, weil `sevenx` das
  offizielle öffentliche PHP-Code-Präfix von 7x ist (ein Name kann nicht mit einer Ziffer beginnen, also kann „7x“ keines
  sein); 7x verwendet `exp` bewusst für Arbeit, die es dem Projekt Exponential und der Exponential Foundation zuordnet.
  Ihr Theme verwendet Ihr eigenes Präfix und benennt nie bestehende Kernel-Bezeichner wie `eZINI` oder `eZTemplate` um.
  Siehe [Namenspräfixe: sevenx und exp](../../../standards/namenspraefixe-sevenx-und-exp.md).
- **Suchen Sie, bevor Sie wählen**: Packagist, GitHub und
  [projects.exponential.earth](https://projects.exponential.earth/) (nach Typ durchsuchen).

## Schritt für Schritt

1. Schreiben Sie die fünf Namen der Tabelle oben auf.
2. Prüfen Sie den Namen der Erweiterung: `ls extension/ | grep -x <name>` gibt nichts aus.
3. Prüfen Sie den Paketnamen: Öffnen Sie `https://packagist.org/packages/<vendor>/<name>`; eine Seite „not found“
   bedeutet, dass er frei ist.
4. Prüfen Sie den Design-Namen: `ls design/ extension/*/design/ | grep -x <design>` gibt nichts aus.
5. Verwenden Sie die Namen ab dem ersten Commit einheitlich in jeder Datei.

## Verwandte Seiten

- [Die Ordnerstruktur einer Theme-Erweiterung](ordnerstruktur-einer-theme-erweiterung.md)
- [composer.json für ein Theme](composer-json-fuer-ein-theme.md)
- [Einen Fork fair umbenennen](../lebenszyklus/einen-fork-umbenennen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
