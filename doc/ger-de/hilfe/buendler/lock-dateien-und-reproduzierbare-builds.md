---
title: Lock-Dateien und reproduzierbare Builds
audience: bundler, developer
category: bundling
order: 90
summary: Warum ein Bundle seine eigene composer.lock mitbringen muss (das Kernel-Repository hat keine), wie Sie dasselbe Bundle zweimal gleich bauen, was neben Composer noch festgelegt werden muss und ein Build-Protokoll, das Sie zu jedem Release aufbewahren.
keywords: composer.lock, reproduzierbarer Build, Lock-Datei, Build-Protokoll, Prüfsumme, reproducible build, lock file, composer install, composer update, build record, checksum, SHA256SUMS, image digest, pinning
type: help
---
# Lock-Dateien und reproduzierbare Builds

Diese Seite richtet sich an alle, die den Build eines Bundles pflegen. Ein Bundle ist reproduzierbar, wenn zweimaliges
Bauen desselben Releases dieselben Dateien ergibt. Darauf verlassen sich die Empfänger, wenn sie ein Problem melden,
und Sie, wenn Sie es beheben.

## Inhalt

1. [Warum die Lock-Datei Ihre ist](#warum-die-lock-datei-ihre-ist)
2. [Zweimal gleich bauen](#zweimal-gleich-bauen)
3. [Was sonst noch festgelegt werden muss](#was-sonst-noch-festgelegt-werden-muss)
4. [Das Build-Protokoll](#das-build-protokoll)
5. [Wenn sich der Build trotzdem ändert](#wenn-sich-der-build-trotzdem-ändert)

## Warum die Lock-Datei Ihre ist

Das Kernel-Repository liefert die `composer.json`, aber keine `composer.lock`: Die Lock-Datei ist "Ihre eigene, sie
ist nicht Teil des Repositorys"
([3.2 A](../../installation/03-den-code-beschaffen.md#a-git-clone-und-composer-install-empfohlen)). Ohne Lock-Datei löst
jedes `composer install` die `~`-Bedingungen erneut auf und kann neuere Releases von Erweiterungen nehmen als der
letzte Build.

Ein Bundle geht daher so vor:

1. Es löst einmal mit `composer update` auf, wenn Sie ein Release vorbereiten;
2. es testet dieses Ergebnis;
3. es checkt die entstandene `composer.lock` mit dem Bundle ein;
4. es baut jedes Image oder Archiv dieses Releases mit `composer install`, das genau das installiert, was die
   Lock-Datei nennt.

## Zweimal gleich bauen

```bash
composer validate --no-check-publish
composer install --no-dev --no-interaction
composer show > var/log/bundle-composer-show.txt
```

- `composer install` aus der Lock-Datei ergibt dieselben Paketversionen.
- `--no-interaction` lässt Composer scheitern, statt eine Frage zu stellen, die niemand beantworten kann.
- Bewahren Sie die Ausgabe von `composer show` mit dem Release auf: Es ist die Liste, nach der Ihr Supportteam fragen
  wird.

## Was sonst noch festgelegt werden muss

Composer deckt nur PHP-Pakete ab. Ein Bundle hängt außerdem ab von:

| Schicht | Festlegen mit |
|---|---|
| Dem Basis-Image oder Betriebssystem | einem Image-Digest oder einem datierten Snapshot der Repositorys der Distribution |
| PHP und seinen Erweiterungen | den genauen Paketversionen oder dem Image-Tag |
| Velocity außerhalb von Composer | einem festen Image-Tag ("Legen Sie in Produktion einen festen Tag fest", [Container](../hosting/modelle/container-docker-und-podman.md#images)) oder einem Release, dessen Dateien Sie gegen seine `SHA256SUMS` prüfen ([Velocity-Pakete](../../funktionen/6.0/velocity-pakete-und-binaerdateien.md#eine-form-wählen)) |
| Seitenpaketen | den `.ezpkg`-Dateien, die Sie ausliefern, mit ihren Prüfsummen ([Seitenpakete in einem Bundle](seitenpakete-in-einem-bundle.md)) |
| FrankenPHP, falls verwendet | `exp:velocity install` lädt ein festgelegtes Release herunter und prüft dessen SHA-256 ([FrankenPHP](../../bc/6.0/frankenphp.md)) |

## Das Build-Protokoll

Führen Sie ein Protokoll je Bundle-Release, neben den Release-Hinweisen:

```text
Bundle:                  <name> <version>
Built on:                <date>, by <pipeline or person>
Exponential:             <tag or commit>   (php bin/php/console --version)
composer.lock:           <checksum of the file>
Base image / OS:         <digest or version>
PHP:                     <version> with <extensions>
Velocity / web server:   <version>
Site package(s):         <name> <version>, <checksum>
Integrity check:         php bin/php/checkmanifest.php --all -> exit <code>
Tests run:               <list, result>
Source offer published:  <URL>
```

Die Integritätsprüfung wird unter [Integritätsprüfungen](integritaetspruefungen.md) erklärt, das Quellcode-Angebot unter
[GPL-Pflichten beim Weitergeben](gpl-pflichten-beim-weitergeben.md).

## Wenn sich der Build trotzdem ändert

- **Ein Paket ist verschwunden oder umgezogen.** Spiegeln Sie, was Sie ausliefern (Composer-Pakete, Seitenpakete),
  damit sich ein altes Release neu bauen lässt; die Quellarchive, die Sie für die GPL veröffentlicht haben, bewahren
  Sie ohnehin auf.
- **Die Lock-Datei installiert sich unter einem neueren PHP nicht mehr.** Das ist ein neues Bundle-Release: erneut
  auflösen, testen, unter einer neuen Version veröffentlichen.
- **Jemand hat `composer update` in ein Build-Skript geschrieben.** Entfernen Sie es; das Release ist nicht mehr das,
  was Sie getestet haben.

## Verwandte Seiten

- [Versionen, Tags und Pinning](versionen-tags-und-pinning.md)
- [Bundles auf Composer-Basis](bundles-auf-composer-basis.md)
- [Ein Bundle testen](ein-bundle-testen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
