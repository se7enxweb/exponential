---
title: Der Wartungslebenszyklus
audience: developer, designer, app-creator
category: extension-development
order: 130
summary: Das Leben einer Erweiterung vom ersten bis zum letzten Release - unterstützte Versionen, PHP- und Kernel-Releases verfolgen, erst als veraltet markieren, dann entfernen, Branches für alte Versionen und die Pflege ehrlich beenden.
keywords: Wartung, Pflege, Lebenszyklus, Unterstützung, PHP-Versionen, Kernel-Releases, Deprecation, Branches, Ende der Unterstützung, Archiv, Übergabe, maintenance, life cycle, support, PHP versions, kernel releases, deprecation, branches, end of life, archive, hand-over
type: help
---
# Der Wartungslebenszyklus

Teil 7, Kapitel 13. Ein Release ist ein Moment; eine Erweiterung lebt jahrelang. Dieses Kapitel betrachtet die ganze
Spanne: was „unterstützt“ bedeutet, wie Sie mit PHP und dem Kernel Schritt halten, wie Sie Dinge ändern, ohne
Benutzer zu überraschen, und wie Sie die Pflege gut beenden.

## Die Phasen

| Phase | Was geschieht | Typische Releases |
|---|---|---|
| Jung (0.x oder 1.0) | der Entwurf bewegt sich noch; Benutzer rechnen mit Änderungen | häufig, klein |
| Stabil (1.x und später) | Benutzer sind darauf angewiesen; Änderungen sind vorsichtig | Patch-Releases, gelegentlich Minor-Releases |
| Ausgereift | sie tut, was sie soll; die Arbeit besteht meist im Schritthalten | Patch-Releases, wenn PHP oder der Kernel sich bewegen |
| Ende | der Maintainer hört auf, oder der Kernel erledigt die Aufgabe jetzt selbst | ein letztes Release, dann ein klarer Hinweis |

## Sagen Sie, was Sie unterstützen

Schreiben Sie es in die README, als kleine Tabelle:

| | Unterstützt |
|---|---|
| Exponential | 6.0.14 und später |
| PHP | 8.0 bis 8.5 |
| Datenbanken | SQLite, MySQL, MariaDB, PostgreSQL |

und halten Sie `composer.json` im Gleichschritt ([Composer und Packagist](composer-und-packagist.md)). Benutzer können
dann vor der Installation entscheiden, statt es hinterher herauszufinden.

## Schritt halten

| Ereignis | Was Sie tun |
|---|---|
| Eine neue PHP-Minor-Version (jährlich, meist gegen Ende des Jahres) | sie in die CI-Matrix aufnehmen; neue Deprecations beheben; veröffentlichen |
| Eine PHP-Version erreicht ihr Lebensende | entscheiden, ob Sie sie weiter unterstützen; wenn Sie sie aufgeben, ist das mindestens ein Minor-Release, angekündigt |
| Ein Kernel-Release | seine Verhaltensänderungen und sein Changelog lesen; testen; bei Bedarf veröffentlichen |
| Eine mitgelieferte Bibliothek veröffentlicht eine Korrektur | sie aktualisieren; veröffentlichen |

Die eigene PHP-Geschichte des Kernels ist ein guter Kalender dafür, was Sie erwartet:
[PHP 8 support](../../../bc/6.0/php8.md).

## Dinge ohne Überraschungen ändern

1. **Erst als veraltet markieren, dann entfernen.** Lassen Sie die alte Einstellung, das alte Template oder den alten
   Operator mindestens ein Minor-Release lang funktionieren, mit einem Hinweis im Changelog und, wo möglich, einer
   Meldung in der Debug-Ausgabe.
2. **Entfernen Sie nur in einem Major-Release**, aufgeführt in den Upgrade-Hinweisen.
3. **Benennen Sie durch Hinzufügen um**: zuerst der neue Name, der alte Name als Alias
   ([Namen und Marken bei einem Upgrade](../teil-4-aktualisieren/namen-und-marken-bei-einem-upgrade.md)).

## Alte Versionen

Die meisten Erweiterungen pflegen nur ihre neueste Version. Wenn Benutzer Korrekturen für eine ältere Major-Version
brauchen (weil die neue einen neueren Kernel voraussetzt), führen Sie einen Branch wie `1.x` und veröffentlichen Sie
von dort Patch-Versionen. Kündigen Sie an, wie lange Sie das tun.

## Gut beenden

Wenn Sie aufhören, eine Erweiterung zu pflegen:

- Sagen Sie es ganz oben in der README, mit dem Datum und, falls es einen gibt, dem Ersatz.
- Bieten Sie sie zur Übernahme an
  ([Eine verwaiste Erweiterung übernehmen](../teil-5-erweitern-forken-zurueckgeben/eine-verwaiste-erweiterung-uebernehmen.md)
  beschreibt die andere Seite). Ein neuer Maintainer ist besser als ein Archiv.
- Wenn niemand sie übernimmt, archivieren Sie das Repository auf GitHub: Es bleibt lesbar und installierbar, und
  jeder kann sehen, dass es nicht mehr gepflegt wird. Löschen Sie es nicht: Websites sind auf seine Tags angewiesen.
- Kennzeichnen Sie das Packagist-Paket als aufgegeben (abandoned), mit dem Ersatz, falls es einen gibt.

## Verwandte Seiten

- Zurück: [Ein Release ankündigen](ein-release-ankuendigen.md)
- Weiter: [Die Release-Checkliste](die-release-checkliste.md)
- [Der Weg zum Maintainer](../teil-5-erweitern-forken-zurueckgeben/der-weg-zum-maintainer.md)
- [Supported PHP versions](https://www.php.net/supported-versions.php) auf php.net (auf Englisch)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
