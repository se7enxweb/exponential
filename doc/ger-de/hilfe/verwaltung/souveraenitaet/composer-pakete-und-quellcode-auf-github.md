---
title: Composer-Pakete und Quellcode auf GitHub
audience: government, admin, developer
category: public-sector
order: 150
summary: Wie der Code von Exponential zu einer öffentlichen Stelle gelangt (GitHub-Repositorys und Composer-Pakete auf Packagist), warum veröffentlichte Versionen sich nie ändern, wie Sie eine eigene Kopie jeder Quelle behalten, von der Sie abhängen, und wie Sie prüfen, was eine Installation enthält.
keywords: Composer, Packagist, GitHub, Quellcode, Pakete, composer.lock, Spiegel, Releases, Tags, unveränderliche Versionen, Lieferkette, Souveränität, source code, packages, mirror, releases, immutable versions, supply chain, sovereignty
type: help
---
# Composer-Pakete und Quellcode auf GitHub

Diese Seite richtet sich an Administratoren und Entwickler, die wissen müssen, woher der Code kommt und wie er verfügbar
bleibt, auch wenn ein fremder Dienst es nicht ist. Jedes Paket des Projekts ist ein GitHub-Repository der Organisation
se7enxweb und ein Composer-Paket; `composer require` oder ein Git-Klon installiert es
([Wer ist die Exponential Foundation?](../../wer-ist-die-exponential-foundation.md#die-zusage-zur-unterstützung),
[Den Code beziehen](../../../installation/03-den-code-beschaffen.md)).

## Inhalt

1. [Wo der Code liegt](#wo-der-code-liegt)
2. [Veröffentlichte Versionen ändern sich nie](#veröffentlichte-versionen-ändern-sich-nie)
3. [Wissen, was Sie betreiben](#wissen-was-sie-betreiben)
4. [Eine eigene Kopie behalten](#eine-eigene-kopie-behalten)
5. [Aktualisieren](#aktualisieren)

## Wo der Code liegt

| Teil | Wo |
|---|---|
| Der Kernel | GitHub `se7enxweb/exponential`, Composer-Paket `se7enxweb/exponential` |
| Vom Kernel benötigte Erweiterungen | Dutzende `se7enxweb/*`-Pakete, aufgeführt in der `composer.json` im Wurzelverzeichnis |
| Velocity | `se7enxweb/exponential-velocity` |
| Bibliotheken von Drittanbietern | ihre eigenen Composer-Pakete |

## Veröffentlichte Versionen ändern sich nie

Die Sicherheitsrichtlinie des Projekts sagt: „A published tag is never changed: a fix for a problem in `v6.0.14` or an
earlier tag reaches you as the next release, not as a corrected copy of the old one“ (ein veröffentlichter Tag wird
nie geändert: Eine Korrektur für ein Problem in `v6.0.14` oder einem früheren Tag erreicht Sie als nächstes Release,
nicht als korrigierte Kopie des alten; SECURITY.md, zitiert in
[Wer ist die Exponential Foundation?](../../wer-ist-die-exponential-foundation.md#die-zusage-zur-unterstützung)). Für eine
öffentliche Stelle heißt das: Eine Versionsnummer bezeichnet genau einen Inhalt, was Prüfungen und reproduzierbare
Builds erleichtert.

## Wissen, was Sie betreiben

- Die `composer.lock` Ihrer Installation verzeichnet die genaue Version und den Commit jedes Pakets.
- **Setup > Updates and packages** (`ezupdate`) zeigt die installierten Pakete, wie jedes bezogen wurde und bei
  Git-Arbeitskopien den Branch und den Commit ([ezupdate](../../../../eng-us/features/6.0/extensions/ezupdate.md)).
- Die Info-Seite listet jede Erweiterung mit ihrer Version und Lizenz auf
  ([Info- und Paketseiten](../../../../eng-us/features/6.0/about-and-package-pages.md)).
- **Setup > Upgrade-Prüfung** vergleicht die Dateien mit den mitgelieferten Prüfsummen
  ([Upgrade-Prüfung](../../../anleitungen/upgrade-pruefung.md)).

## Eine eigene Kopie behalten

Um unabhängig davon zu bleiben, ob GitHub und Packagist erreichbar sind:

1. Bewahren Sie den Code Ihrer Installation, einschließlich `vendor/` oder der installierten
   Erweiterungsverzeichnisse, zusammen mit `composer.lock` in Ihrem eigenen Repository oder Artefaktspeicher auf.
2. Spiegeln Sie bei Bedarf die GitHub-Repositorys, von denen Sie abhängen, auf Ihren eigenen Git-Server.
3. Composer kann Ihre eigenen Paket-Repositorys verwenden; `ezupdate` listet die `repositories` der `composer.json`
   auf, und packagist.org lässt sich dort abschalten.
4. Testen Sie, ob sich eine frische Installation allein aus Ihren Kopien aufbauen lässt.

## Aktualisieren

Updates sind Composer-Vorgänge: über **Setup > Updates and packages** (eine Prüfung, die nichts ändert, ein
Probelauf, eine Bestätigung der Datensicherung) oder auf der Kommandozeile
([Aktualisieren](../../../anleitungen/aktualisieren.md),
[Automatische Updates mit ezupdate](../../hosting/betrieb/automatische-updates-mit-ezupdate.md)). Lesen Sie das
Changelog jedes Releases, bevor Sie eine Produktivwebsite aktualisieren.

## Verwandte Seiten

- [Den Code beziehen](../../../installation/03-den-code-beschaffen.md)
- [Composer und Git auf Servern](../../hosting/grundlagen/composer-und-git-auf-servern.md)
- [Mit Git aktualisieren](../../mit-git-aktualisieren.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
