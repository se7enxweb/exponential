---
title: Release-Linie und Support-Richtlinie
audience: businesses, project-manager, admin
category: business-solutions
order: 20
summary: Welche Exponential-Versionen gepflegt werden, wie Releases und Sicherheitskorrekturen Sie erreichen, welche PHP-Versionen unterstützt werden und was das für ein Unternehmen bedeutet, das Jahre im Voraus plant.
keywords: Release-Linie, Support-Richtlinie, gepflegte Versionen, Sicherheitskorrekturen, PHP-Versionen, Tags, Changelog, Ende der Unterstützung, release line, support policy, maintained versions, security fixes, PHP versions, 6.0.x, tags, Packagist, changelog, end of life, SECURITY.md
type: help
---
# Release-Linie und Support-Richtlinie

Diese Seite richtet sich an Inhaber, Führungskräfte und Projektleitungen, die wissen müssen, wie lange eine auf
Exponential gebaute Website unterstützt wird, wie Korrekturen sie erreichen und welche PHP-Versionen ihr Hosting anbieten
muss. Sie fasst die Zusagen zusammen, die das Projekt schriftlich festgehalten hat, und sagt, was jede für ein
Unternehmen bedeutet. Sie fügt kein eigenes Versprechen hinzu.

## Inhalt

1. [Die gepflegte Linie](#die-gepflegte-linie)
2. [Die bisherigen 6.0.x-Releases](#die-bisherigen-60x-releases)
3. [Wie Sicherheitskorrekturen Sie erreichen](#wie-sicherheitskorrekturen-sie-erreichen)
4. [PHP-Versionen](#php-versionen)
5. [Release-Regeln, auf die Sie sich verlassen können](#release-regeln-auf-die-sie-sich-verlassen-können)
6. [Wo Sie nachlesen, was sich geändert hat](#wo-sie-nachlesen-was-sich-geändert-hat)
7. [Was das für Ihre Planung bedeutet](#was-das-für-ihre-planung-bedeutet)
8. [Was das Projekt nicht veröffentlicht](#was-das-projekt-nicht-veröffentlicht)

## Die gepflegte Linie

Aus [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md):

| Version | Unterstützt |
|---|---|
| 6.0.x, aktuelle Linie (`main` und der nächste daraus geschnittene Tag) | ja |
| 6.0.x, veröffentlichte Tags (`v6.0.0` bis `v6.0.14`) | korrigiert durch Aktualisierung auf die neueste 6.0.x |
| 5.x, 4.x, 3.x und älter | nein |

In einfachen Worten: **Bleiben Sie auf dem neuesten 6.0.x-Release.** Wenn Sie eine ältere 6.0.x betreiben, sind Sie nicht
abgeschnitten, aber der Weg zu einer Korrektur ist eine Aktualisierung innerhalb der Linie. Wenn Sie 5.x oder älter
betreiben, planen Sie ein Upgrade-Projekt ([Upgrade](../../../anleitungen/aktualisieren.md)).

## Die bisherigen 6.0.x-Releases

Exponential 6.0.0 erschien am 1. Januar 2024 und 6.0.14 am 7. Juni 2026; die Linie 6.0.15 ist in Entwicklung, und ihr
Changelog sagt „Not released yet“ ([Wer ist 7x?, die 6.0.x-Releases](../../wer-ist-7x.md#die-releases-60x),
[Changelog 6.0.15](../../../../changelogs/6.0/6.0.15.md)).

Releases, die für die Planung wichtig sind, aus
[Release- und Upgrade-Planung](../../fuehrung/release-und-upgrade-planung.md#die-bisherigen-versionen):

| Release | Warum es wichtig ist |
|---|---|
| 6.0.7 | Das letzte Release für PHP 7.4 |
| 6.0.8 | Braucht PHP 8.1 |
| 6.0.10 | Das erste Release mit dem Namen Exponential |
| 6.0.13 | Sicherheitskorrekturen und Prüfungen der Sortierspalten |
| 6.0.14 | MongoDB als Datenbank und die Upgrade-Dateiprüfung |
| Linie 6.0.15 | Velocity, das Design admin4, der Audit-Trail, Exponential Layouts, Exponential Intelligence, das Hilfesystem |

> **Hinweis**
> Das Projekt veröffentlicht keinen festen Release-Kalender. Die 6.0.x-Tags kamen in Abständen von etwa einem bis fünf
> Monaten ([Release- und Upgrade-Planung](../../fuehrung/release-und-upgrade-planung.md#die-bisherigen-versionen)). Planen
> Sie mit den Tags, die existieren, nicht mit einem Datum.

## Wie Sicherheitskorrekturen Sie erreichen

[SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) legt den Ablauf fest:

1. Ein Sicherheitsproblem wird vertraulich per E-Mail an die Adresse in SECURITY.md gemeldet, nicht im öffentlichen
   Issue-Tracker. „Sie können innerhalb eines Werktags mit einer ersten Antwort rechnen.“
2. Der Meldende erfährt, ob die Meldung angenommen wird, und wird auf dem Laufenden gehalten, während eine Korrektur
   vorbereitet wird.
3. Ein angenommenes Problem wird auf `main` behoben und mit der nächsten 6.0.x-Version veröffentlicht.
4. Ein veröffentlichter Tag wird nie geändert: „Eine Korrektur für ein Problem in `v6.0.14` oder einem früheren Tag
   erreicht Sie als nächstes Release, nicht als korrigierte Kopie des alten.“

Für ein Unternehmen bedeutet das: **Aktuell zu bleiben ist Ihr Sicherheitsplan.** Planen Sie eine routinemäßige
Aktualisierung innerhalb der Linie als wiederkehrende Aufgabe ein. Der Sicherheitsbereich sagt mehr dazu:
[Sicherheitsupdates und Offenlegung](../sicherheit/sicherheitsupdates-und-offenlegung.md).

## PHP-Versionen

Aus der [README, „What version of PHP is required“](https://github.com/se7enxweb/exponential/blob/main/README.md#what-version-of-php-is-required) und
[Wer ist die Exponential Foundation?, PHP-Versionen](../../wer-ist-die-exponential-foundation.md#php-versionen):

| PHP | Exponential 6 |
|---|---|
| 7.4 | Nur bis 6.0.7, veraltet |
| 8.0 | Die älteste unterstützte Version, sodass das Standard-PHP von Red Hat Enterprise Linux 9 es unter Apache, PHP-FPM oder Exponential Velocity (ab dessen Release 0.0.4.54) ausführt |
| 8.1 bis 8.5 | Unterstützt, Exponential Velocity eingeschlossen |
| 8.6 und neuer | Durch die Anforderung `php` in `composer.json` zugelassen; die README nennt „PHP 8.2, 8.3, 8.4, 8.5, 8.6 and beyond php9“ als Ziel |

Der Rat der README: „Bitte verwenden Sie die neueste PHP-Version, die Ihr Betriebssystem bietet.“ Die Continuous
Integration führt die Testsuite unter PHP 8.1 bis 8.5 aus und prüft, dass PHP 8.0 weiterhin startet
([PHP-Versionen](../../wer-ist-die-exponential-foundation.md#php-versionen)).

> **Tipp**
> PHP und Exponential bewegen sich gemeinsam. Bevor Sie einen Termin für eine Aktualisierung zusagen, fragen Sie Ihren
> Hosting-Anbieter, welche PHP-Versionen er anbietet und wann er eine abkündigt. **Setup > Systeminformationen** warnt,
> wenn die Sicherheitsunterstützung eines PHP-Zweigs geendet hat
> ([Systeminformationen, Zustandsprüfungen](../../../anleitungen/systeminformationen.md#3-zustandsprüfungen)).

## Release-Regeln, auf die Sie sich verlassen können

Aus [Wer ist die Exponential Foundation?, Releases und Versionsnummern](../../wer-ist-die-exponential-foundation.md#releases-und-versionsnummern):

- **Veröffentlichte Versionen ändern sich nie.** Was Sie getestet haben, ist das, was Sie ausrollen.
- **Die letzte Stelle zählt weiter.** Nach `0.1.9` kommt `0.1.10`; Versionen werden nach Version sortiert, nicht nach
  Namen.
- **Erweiterungen tragen ihre eigene Version** in `ezinfo.php` und `extension.xml`, und **Setup > Extensions** zeigt sie.
- **Jedes Paket liegt auf GitHub und Packagist**, sodass sich der genaue Code jeder Version jederzeit erneut abrufen
  lässt.

## Wo Sie nachlesen, was sich geändert hat

| Dokument | Wofür Sie es nutzen |
|---|---|
| [Changelogs je Release](../../../../changelogs/6.0/6.0.15.md) | Was eine Version enthält, mit einem Abschnitt „Upgrading“ |
| [Hinweise zu Verhaltensänderungen](../../../../eng-us/bc/6.0/behaviour-changes-2026-10.md) | Was sich anders verhält und was zu prüfen ist, je Zeitraum |
| [Release Notes der Erweiterungen](../../../../changelogs/extensions/README.md) | Was ein Release einer Erweiterung geändert hat |
| [Die Geschichte](../../../../eng-us/history/README.md) | Die Geschichte jeder Änderung seit Dezember 2023, nach Monaten |

Geben Sie einer Person in Ihrem Team die Aufgabe, diese vor jeder Aktualisierung zu lesen.

## Was das für Ihre Planung bedeutet

- [ ] Ihre Website läuft mit einem 6.0.x-Release, und Sie wissen, mit welchem.
- [ ] Die Aktualisierung auf die neueste 6.0.x steht mindestens so oft im Kalender, wie neue Tags erscheinen.
- [ ] Ihr Hosting bietet eine PHP-Version im unterstützten Bereich an, und Sie wissen, wann sie abgekündigt wird.
- [ ] Jemand liest vor jeder Aktualisierung den Changelog und die Hinweise zu Verhaltensänderungen.
- [ ] Ihr Partner oder Ihr Team weiß, wie man ein Sicherheitsproblem vertraulich meldet.

## Was das Projekt nicht veröffentlicht

Das Projekt veröffentlicht kein einzelnes Dokument mit einer Support-Richtlinie, keinen Release-Kalender, kein Datum für
das Ende der Unterstützung von 6.0 und kein Service Level Agreement
([Release- und Upgrade-Planung](../../fuehrung/release-und-upgrade-planung.md#wozu-sich-das-projekt-verpflichtet),
[SLA-Optionen für Exponential-Hosting](../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md)). Kommerzieller Support und
Hosting-Bedingungen kommen von 7x und von Hosting-Partnern
([Wer ist 7x?, Dienstleistungen und Hosting](../../wer-ist-7x.md#dienstleistungen-und-hosting)). Braucht Ihr Unternehmen eine
vertragliche Zusage, holen Sie sie sich schriftlich von ihnen; siehe
[Grundlagen zu Verträgen und SLAs](../auswahl/grundlagen-zu-vertraegen-und-slas.md).

## Verwandte Seiten

- [Aktualisierungen ohne Überraschungen](aktualisierungen-ohne-ueberraschungen.md)
- [Release- und Upgrade-Planung](../../fuehrung/release-und-upgrade-planung.md)
- [Zusammenarbeit mit 7x und dem Exponential-Projekt](../auswahl/zusammenarbeit-mit-7x-und-dem-projekt.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
