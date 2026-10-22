---
title: Erweiterungs-Bundles
audience: bundler, developer
category: bundling
order: 330
summary: Wie Sie mehrere Exponential-Erweiterungen als ein Bundle ausliefern - ein Composer-Metapaket oder ein dokumentierter Satz, Abhängigkeiten in extension.xml, Ladereihenfolge, übereinstimmende Versions- und Lizenzfelder, die Prüfung auf Geheimnisse bei exportierten Paketen, Tests gegen jedes Exponential-Release und Dokumentation.
keywords: Erweiterungs-Bundle, Metapaket, Abhängigkeiten, Ladereihenfolge, Lizenz, Geheimnisprüfung, Test, extension bundle, meta package, Composer, extension.xml, requires, extends, loading order, ActiveExtensions, ezinfo.php, version, license, secret check, test, bundle
type: help
---
# Erweiterungs-Bundles

Diese Seite richtet sich an Entwickler und Firmen, die einen Satz zusammengehöriger Erweiterungen ausliefern: eine
Shop-Suite, ein Intranet-Kit, ein SEO-Paket. Die Checkliste für eine einzelne Erweiterung ist
[Eine Erweiterung veröffentlichen](../partner/eine-erweiterung-veroeffentlichen.md); diese Seite behandelt den Satz.

## Inhalt

1. [Formen eines Erweiterungs-Bundles](#formen-eines-erweiterungs-bundles)
2. [Abhängigkeiten](#abhängigkeiten)
3. [Ladereihenfolge](#ladereihenfolge)
4. [Übereinstimmende Metadaten](#übereinstimmende-metadaten)
5. [Eine Erweiterung als .ezpkg paketieren](#eine-erweiterung-als-ezpkg-paketieren)
6. [Testen und pflegen](#testen-und-pflegen)

## Formen eines Erweiterungs-Bundles

| Form | Wie | Geeignet für |
|---|---|---|
| Ein Composer-Metapaket | ein Paket, dessen `composer.json` nur Ihre Erweiterungen in passenden Versionen per `require` verlangt | mit Composer installierte Websites, also jede aktuelle Installation |
| Ein dokumentierter Satz | eine README, die jedes Paket und jede Version aufführt | kleine Sätze |
| Ein Seitenpaket, das sie aktiviert | ein Seitenpaket, dessen Einstellungen die Erweiterungen aufführen | neue Websites, die um den Satz herum gebaut werden ([Seitenpakete in einem Bundle](seitenpakete-in-einem-bundle.md)) |

Jede Erweiterung bleibt ein eigenes Composer-Paket mit eigenem Repository und eigenen Tags.

## Abhängigkeiten

`extension.xml` deklariert Abhängigkeiten zwischen Erweiterungen:

```xml
<dependencies>
    <requires>
        <extension name="ezjscore" />
    </requires>
    <extends>
        <extension name="ezwt" />
    </extends>
</dependencies>
```

"`<requires>` nennt Erweiterungen, die aktiv sein müssen; `<extends>` nennt Erweiterungen, auf denen diese aufbaut"
([Metadaten von Erweiterungen](../../spezifikationen/6.0/extension-metadaten.md)). Spiegeln Sie dieselben Abhängigkeiten
in jeder `composer.json`, damit Composer sie installiert.

## Ladereihenfolge

Die Reihenfolge von `ActiveExtensions` entscheidet, wessen Einstellungen und Templates gewinnen. Dokumentieren Sie die
Reihenfolge, die Ihr Bundle braucht, und testen Sie sie; **Setup > Extensions** zeigt sie
([Liste der Erweiterungen](../../funktionen/6.0/extension-liste-und-downloads.md),
[Ladereihenfolge und sicheres Speichern](../../funktionen/6.0/ladereihenfolge-der-extensions.md)).

## Übereinstimmende Metadaten

Für jede Erweiterung des Satzes gilt:

- `ezinfo.php` und `extension.xml` nennen dieselbe `Version`, Lizenz, dasselbe Urheberrecht und dieselbe Info-URL;
  "`//autogentag//` und `dev-master` sind keine Versionen"
  ([Metadaten von Erweiterungen](../../spezifikationen/6.0/extension-metadaten.md#release-regel));
- `composer.json` nennt dieselbe Lizenz als SPDX-Bezeichner;
- eine Erweiterung mit eigener `share/filelist.md5` frischt diese im selben Release auf, denn die Dateikonsistenzprüfung
  der Administration listet jede veraltete Zeile als geänderte Datei ([Integritätsprüfungen](integritaetspruefungen.md)).

## Eine Erweiterung als .ezpkg paketieren

Setup > Extensions und `ezpm.php add` können eine Erweiterung als Paket einpacken, aber der Export ist standardmäßig
ausgeschaltet und braucht `package.ini [ExtensionExportSettings] AllowedExtensions[]` und die Richtlinie
`package/export_extension`. "Eine Erweiterung, die einen geheimen Wert oder Schlüsselmaterial enthält, wird nicht
eingepackt" ([Die Prüfung auf Geheimnisse](../../bc/6.0/geheimnispruefung-fuer-erweiterungspakete.md)). Für ein Bundle sind
Composer-Pakete aus Ihren Repositorys die vorrangige Form; verwenden Sie `.ezpkg` dort, wo Empfänger über das
Paketsystem installieren.

## Testen und pflegen

- [ ] Installieren Sie den ganzen Satz auf einer sauberen Installation jeder Exponential-Version, die Sie unterstützen,
      auf jeder Datenbank, die Sie zusagen.
- [ ] Aktivieren Sie in der dokumentierten Reihenfolge; führen Sie `php bin/php/console exp:checkclasses` aus, das
      "jede deklarierte Klasse lädt und die meldet, die PHP nicht laden will"
      ([10.7](../../installation/10-nach-der-installation.md#107-autoloads-der-klassen)).
- [ ] Testen Sie gegen jedes neue Exponential-Release ([Releases testen](../partner/releases-testen.md)).
- [ ] Veröffentlichen Sie den Satz gemeinsam, mit einem Änderungsprotokoll, das die Version jeder Erweiterung nennt.

## Verwandte Seiten

- [Eine Erweiterung als Partner veröffentlichen](../partner/eine-erweiterung-veroeffentlichen.md)
- [Partner für Erweiterungen und Themes](../partner/partner-fuer-erweiterungen-und-themes.md)
- [Erweiterungen aus einem Bundle veröffentlichen](erweiterungen-aus-einem-bundle-veroeffentlichen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
