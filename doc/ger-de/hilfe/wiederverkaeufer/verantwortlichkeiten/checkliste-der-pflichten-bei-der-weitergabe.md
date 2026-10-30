---
title: Checkliste der Pflichten bei der Weitergabe
audience: reseller, partner, developer
category: reselling
order: 450
summary: Die Checkliste, die ein Wiederverkäufer durcharbeitet, bevor er Kopien von Exponential an jemanden übergibt - Images, Appliances, Downloads, OEM-Produkte, Dateien, die ein Kunde mitnimmt -, mit Lizenztexten, Urheberrechtshinweisen, Quellcode, gekennzeichneten Änderungen, Lizenzen von Erweiterungen und Komponenten, Dokumentation und Benennung. Keine Rechtsberatung.
keywords: Weitergabe, Pflichten aus der GPL, Lizenz, LICENSE, COPYRIGHT.md, Quellcode, Urheberrechtshinweise, geänderte Dateien, Erweiterungen, Komponenten Dritter, GFDL, Dokumentation, Image, Appliance, OEM, distribution, GPL obligations, license, source code, copyright notices, modified files, extensions, third-party components, documentation, image, appliance
type: help
---
# Checkliste der Pflichten bei der Weitergabe

Diese Seite ist für technische Leiter und Produktmanager bei Wiederverkäufern, die Kopien von Exponential an andere
übergeben. Eine Website für einen Kunden zu betreiben ist keine Weitergabe; jemandem eine Kopie zu geben schon
([Was Wiederverkauf bei freier Software bedeutet](../grundlagen/was-wiederverkauf-bei-freier-software-bedeutet.md#eine-website-für-einen-kunden-zu-betreiben-ist-keine-weitergabe)).
Diese Checkliste ist eine Gedächtnisstütze, **keine Rechtsberatung**: Lesen Sie die Lizenzen und fragen Sie für Ihre
Paketierung einen Rechtsberater.

## Inhalt

1. [Wann dies gilt](#wann-dies-gilt)
2. [Die Checkliste](#die-checkliste)
3. [Lizenzen der Komponenten](#lizenzen-der-komponenten)
4. [Angebote zum Quellcode](#angebote-zum-quellcode)
5. [Aufzeichnung eines Release](#aufzeichnung-eines-release)

## Wann dies gilt

| Sie geben jemandem | Gilt |
|---|---|
| Ein vorinstalliertes Server-Image, einen Container oder eine Appliance | Ja |
| Ein herunterladbares OEM-Produkt oder Paket | Ja |
| Die Dateien der Website am Ende eines Projekts oder Vertrags | Ja |
| Eine Kopie Ihrer geänderten Version an den Entwickler eines Kunden | Ja |
| Zugang über einen Browser zu einer Website, die Sie hosten | Nein |

## Die Checkliste

**Lizenz und Hinweise**

- [ ] `LICENSE`, `LICENSE.md` und `COPYRIGHT.md` enthalten und unverändert.
- [ ] Jeder Urheberrechtshinweis in den Dateien erhalten.
- [ ] Keine Bedingung hinzugefügt, die die Rechte des Empfängers aus der GPL einschränkt (kein „darf nicht
      weitergegeben werden“, kein „Quellcode nicht enthalten“).

**Quellcode**

- [ ] Der zugehörige Quellcode von allem Weitergegebenen wird bereitgestellt, einschließlich Ihrer Änderungen.
      Exponential ist PHP, die Dateien sind also der Quellcode.
- [ ] Geänderte Dateien tragen einen Hinweis, dass Sie sie geändert haben, und das Datum (GPL Abschnitt 2a).
- [ ] Generierten oder minifizierten Dateien liegen ihre Quellen bei.

**Erweiterungen und Komponenten**

- [ ] Die Lizenz jeder Erweiterung aus ihrer `ezinfo.php` und `extension.xml` oder unter **Info > Über** gelesen.
- [ ] Komponenten Dritter in der Tabelle **Third-party software** der Seite „Über“ geprüft
      ([Die Seiten „Über“ und „Copyright“](../../../funktionen/6.0/info-und-paketseiten.md)).
- [ ] Ihre eigenen Erweiterungen so lizenziert, dass sie mit GPL-Code weitergegeben werden dürfen (fragen Sie einen
      Rechtsberater).
- [ ] Schriften und Bilder in Themes für die Weitergabe lizenziert.

**Dokumentation**

- [ ] Hilfeseiten, die Sie beifügen, behalten ihre Hinweise und kommen mit dem Text der GNU FDL
      ([Dokumentation, die Sie Ihren Kunden geben können](../kunden/dokumentation-fuer-ihre-kunden.md)).

**Benennung**

- [ ] Das Produkt korrekt benannt; „auf Basis von Exponential“, wo es Ihr eigenes Produkt ist
      ([Namen und Marke für Wiederverkäufer](../verkauf/namen-und-marke.md)).

Quelle der Kernliste: [Vertriebspartner und Wiederverkäufer](../../partner/vertriebspartner.md#pflichten-eines-distributors)
und [Lizenzierung für Hoster](../../hosting/geschaeft/lizenzierung-fuer-hoster.md#images-vorlagen-und-ein-klick-installer).

## Lizenzen der Komponenten

| Komponente | Lizenz | Wo Sie prüfen |
|---|---|---|
| Der Kernel | GPL v2 oder später | `COPYRIGHT.md`, `composer.json` (`GPL-2.0-or-later`) |
| Jede Erweiterung | Ihre eigene | `ezinfo.php`, `extension.xml`, **Info > Über** |
| Exponential Velocity | MIT | Seine `composer.json` |
| Komponenten Dritter | Ihre eigenen | Die Tabelle der Drittsoftware auf der Seite „Über“ |
| Diese Dokumentation | GNU FDL 1.3 oder später | [Die Lizenz dieser Dokumentation](../../../README-LIZENZ.md) |

Prüfen Sie die Seite „Über“ genau der Installation, die Sie ausliefern, bevor Sie „alles steht unter der GPL“
schreiben.

## Angebote zum Quellcode

Der einfachste Weg, die Anforderung an den Quellcode zu erfüllen, ist, den Quellcode mit der Kopie auszuliefern.
Bauen Sie Images und Installer aus den öffentlichen Repositorys und Paketen, lassen Sie sie, wo Sie können,
unverändert, und legen Sie Ihre eigene Konfiguration in Dateien, die zur Installation gehören, etwa
`settings/override` ([Lizenzierung für Hoster](../../hosting/geschaeft/lizenzierung-fuer-hoster.md)). Wenn Sie Ihre
Änderungen an das Projekt zurückgeben, entfällt beim nächsten Release der größte Teil der Last
([Code zurückgeben](../../partner/code-zurueckgeben.md)).

## Aufzeichnung eines Release

```text
Product / image: ______________   Version: ______   Date: ________
Exponential version: 6.0.__   Extensions (versions, licenses): ______________
Changed files (with notices): ______________
Source provided: with the copy / at <location>
License files included: LICENSE, LICENSE.md, COPYRIGHT.md, GFDL text (if docs included)
Reviewed by: ______________   Counsel consulted: yes / no
```

## Verwandte Seiten

- [OEM-Produkt-Wiederverkäufer](../modelle/oem-produkt-wiederverkaeufer.md)
- [Lizenzierung für Partner](../../partner/lizenzierung-fuer-partner.md)
- [Paketersteller: hier beginnen](../../buendler/buendler-hier-beginnen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
