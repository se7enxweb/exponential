---
title: "Marken- und Namensregeln: Exponential, 7x und die Foundation"
audience: sales-marketing, editor, developer
category: business
order: 100
summary: Wie Sie die Namen des Produkts, seiner Produktfamilie, des Unternehmens 7x und der Exponential Foundation richtig schreiben, wann der alte Name eZ Publish erscheinen darf und welche Bezeichner im Code sich nie ändern - mit einer Tabelle für Richtig und Falsch und einer Checkliste.
keywords: Marke, Namensregeln, Name, Schreibweise, Produktfamilie, Stil, amerikanisches Englisch, Umbenennung, Exponential, 7x, se7enx, se7enxweb, Exponential Foundation, eZ Publish, Velocity, Exponential Intelligence, Exponential Layouts, brand, naming, product family, style, spelling, American English, rebranding
type: help
---
# Marken- und Namensregeln: Exponential, 7x und die Foundation

Diese Seite richtet sich an alle, die über Exponential schreiben: Vertrieb und Marketing, Redakteure, die Webseiten
und Newsletter schreiben, und Entwickler, die README-Dateien, Commit-Nachrichten, Release Notes oder Texte der
Oberfläche verfassen. Sie beantwortet: Was bezeichnet jeder Name, wie wird er geschrieben, wann darf der alte Name
erscheinen, und welche Namen im Code dürfen nie „korrigiert“ werden?

Jede der folgenden Regeln stammt aus einer Quelle in dieser Dokumentation. Wo die Quellen keine Regel festlegen (zum
Beispiel zur Verwendung des Logos), sagt die Seite das.

## Inhalt

1. [Die Namen in einer Tabelle](#die-namen-in-einer-tabelle)
2. [Das Produkt: Exponential](#das-produkt-exponential)
3. [Die Produktfamilie](#die-produktfamilie)
4. [Das Unternehmen: 7x, se7enx und se7enxweb](#das-unternehmen-7x-se7enx-und-se7enxweb)
5. [Die gemeinnützige Organisation: die Exponential Foundation](#die-gemeinnützige-organisation-die-exponential-foundation)
6. [Der alte Name: eZ Publish, nur als Geschichte](#der-alte-name-ez-publish-nur-als-geschichte)
7. [Bezeichner im Code bleiben, wie sie sind](#bezeichner-im-code-bleiben-wie-sie-sind)
8. [Andere Unternehmen und Produkte](#andere-unternehmen-und-produkte)
9. [Sprache und Schreibweise](#sprache-und-schreibweise)
10. [Richtig und Falsch](#richtig-und-falsch)
11. [Was die Quellen nicht entscheiden](#was-die-quellen-nicht-entscheiden)
12. [Eine Checkliste vor dem Veröffentlichen](#eine-checkliste-vor-dem-veröffentlichen)

## Die Namen in einer Tabelle

| Name | Was es ist | So schreiben | Quelle |
|---|---|---|---|
| **Exponential** | Das Produkt: der Kernel für CMS, CMF und E-Commerce | `Exponential`; mit der Hauptversionslinie: `Exponential 6`, `Exponential 6.0.x`; ein Release: `Exponential 6.0.14` | [Wer ist 7x?](../wer-ist-7x.md#die-namen-brookins-consulting-7x-se7enx-und-exponential) |
| **7x** | Das Unternehmen, das Exponential leitet | `7x` (Ziffer 7, kleines x) | [Wer ist 7x?](../wer-ist-7x.md#die-namen-brookins-consulting-7x-se7enx-und-exponential) |
| **se7enx** | Wie 7x in seiner Domain ausgeschrieben wird | `se7enx.com` | ebenda |
| **se7enxweb** | Das GitHub-Konto und der Composer-Vendor-Name von 7x | `se7enxweb`, `se7enxweb/exponential` | ebenda |
| **Exponential Foundation** | Die gemeinnützige Organisation, die das Projekt betreibt und finanziert | `Exponential Foundation`; im Satz `die Exponential Foundation` | [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md) |
| **exponential.earth** | Die Website des Open-Source-Projekts | kleingeschrieben, als Adresse | [Wer ist 7x?](../wer-ist-7x.md#die-namen-brookins-consulting-7x-se7enx-und-exponential) |
| **Brookins Consulting** | Der frühere Name von 7x | Nur in der Geschichte: „7x hieß früher Brookins Consulting“ | [Wer ist 7x?](../wer-ist-7x.md#von-brookins-consulting-zu-7x-in-drei-stufen) |
| **eZ Publish** | Der Name, den die Codebasis trug, solange eZ Systems sie entwickelte | Nur in der Vergangenheitsform, als Geschichte | [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md#glossar) |

## Das Produkt: Exponential

Ab Version 6.0.10 (August 2025) nennt sich die Software **Exponential**. Der Installer, die Administration, die
Skripte für die Kommandozeile, die Übersetzungen, die Dokumentation und der Paketserver verwenden alle diesen Namen
([Das Produkt heißt Exponential](../../funktionen/6.0/umbenennung-in-exponential.md)).

Regeln:

- Schreiben Sie **Exponential** mit großem E. Es ist ein Eigenname und steht ohne Artikel: „Exponential
  veröffentlicht …“, nicht „das Exponential veröffentlicht …“.
- Wenn die Hauptversionslinie wichtig ist, schreiben Sie **Exponential 6** oder **Exponential 6.0.x**; bei einem
  Release die vollständige Nummer: **Exponential 6.0.15**. Im Fließtext stehen Versionen ohne `v`; das `v` gehört
  zum Git-Tag (`v6.0.14`).
- Die Projekt-Website kündigte den Namen als „'Exponential' or 'Exponential (CMS)'“ an
  ([Wer ist 7x?](../wer-ist-7x.md#warum-das-produkt-exponential-heißt)). „Exponential CMS“ wird zum Beispiel in
  `CONTRIBUTING.md` verwendet. Verwenden Sie es dort, wo „Content-Management-System“ der Klarheit halber gesagt werden
  muss, etwa bei der ersten Erwähnung für ein externes Publikum.
- Die Konstante `EDITION` in `lib/version.php` lautet `Exponential` ([Das Produkt heißt Exponential](../../funktionen/6.0/umbenennung-in-exponential.md#was-sich-geändert-hat)).

## Die Produktfamilie

Verwenden Sie die Namen genau so, wie die Seite der Foundation sie schreibt ([Die Produktfamilie auf einen Blick](../wer-ist-die-exponential-foundation.md#die-produktfamilie-im-überblick)).

| Produkt | Genauer Name | Kurzform (nach der ersten Erwähnung) | Paket |
|---|---|---|---|
| Der Kernel | Exponential 6 | Exponential | `se7enxweb/exponential` |
| Das einfache CMS der Linie 2.x | Exponential Basic | Basic | `se7enxweb/exponentialbasic` (Repository) |
| Reine Symfony-Plattform | Exponential Platform 3.2.9 | Exponential Platform | `exponential-platform` |
| Doppelter Kernel | Exponential Platform Legacy | Legacy | `exponential-platform-legacy` |
| Doppelter Kernel mit Layouts und Media-Site | Exponential Platform Nexus | Nexus | `exponential-platform-nexus` |
| Reine Symfony-DXP | Exponential Platform DXP | DXP | `exponential-platform-dxp` |
| Web- und Anwendungsserver | Exponential Velocity | Velocity | `se7enxweb/exponential-velocity` |
| Schreibassistent | Exponential Intelligence | Exponential Intelligence, oder `expai` in technischen Texten | `se7enxweb/expai` |
| Seitenbaukasten | Exponential Layouts | Layouts, der Layout-Editor | `se7enxweb/explayouts` |
| Alternative Administration | AdminUI | AdminUI | `exp_adminui` |
| Die Hilfe | das Hilfesystem | die Hilfe | Teil des Kernels |

Hinweise:

- **Velocity** heißt in Befehlen auch `vc`, und **qbix** ist der Name der Engine in den Einstellungen von
  Exponential und der Name des Upstream-Projekts, von dem es abgespalten wurde ([Glossar](../../glossar.md)). In
  Marketingtexten schreiben Sie **Exponential Velocity** oder **Velocity**.
- Schreiben Sie Paketnamen in Code-Formatierung und klein (`expai`, `explayouts`, `cjw_newsletter`), genau wie in
  `composer.json`.
- „Legacy“ hat eine bestimmte Bedeutung: der Kernel von Exponential 6, wenn er neben einer Symfony-Plattform läuft
  ([Glossar](../wer-ist-die-exponential-foundation.md#glossar)). Verwenden Sie „Legacy“ nicht im Sinne von „alt“,
  wenn Sie über Exponential 6 selbst sprechen; es ist die gepflegte Linie.

## Das Unternehmen: 7x, se7enx und se7enxweb

- **7x** ist der Name des Unternehmens. Es „took over leadership of the project and it's development, support,
  adoption and community growth in 2023“ (README, „Who is 7x“), übernahm also 2023 die Führung des Projekts und
  seiner Entwicklung, seines Supports, seiner Verbreitung und des Wachstums seiner Community.
- Seine Domain ist `se7enx.com`, sein GitHub-Konto und Composer-Vendor `se7enxweb`.
- 7x ist „the North American corporation“ (README), das nordamerikanische Unternehmen. In Marketingtexten ist „7x,
  das Unternehmen, das Exponential leitet“ eine sichere Beschreibung.
- **Brookins Consulting** ist Geschichte: „Previously before 2022, 7x was called Brookins Consulting“ (README), vor
  2022 hieß 7x also Brookins Consulting. Verwenden Sie den Namen nur, wenn Sie die Geschichte erzählen.

## Die gemeinnützige Organisation: die Exponential Foundation

- Schreiben Sie **die Exponential Foundation**. Sie ist „a 501(c)(3) Non-Profit Corporation founded in Seattle,
  Washington, USA“, gegründet am 19. März 2026 ([Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md#in-eigenen-worten)).
- 7x und die Foundation sind **zwei Organisationen**. Schreiben Sie nie so, als wären sie eine, und schreiben Sie nie,
  dass die Foundation Dienstleistungen verkauft: 7x ist das Unternehmen, die Foundation betreibt und finanziert das
  Projekt ([Die Foundation und 7x](../wer-ist-die-exponential-foundation.md#die-foundation-und-7x)).
- Der Copyright-Inhaber der Software wird **Exponential Foundation and 7x** geschrieben (`COPYRIGHT.md`); die
  Dokumentation trägt denselben Vermerk, ohne dritten Rechteinhaber: Mitwirkende übertragen ihr Urheberrecht über das CLA
  an beide ([Die Lizenz dieser Dokumentation](../../README-LIZENZ.md)). Übernehmen Sie den Vermerk genau, kürzen Sie ihn nie.

## Der alte Name: eZ Publish, nur als Geschichte

Exponential führt die Codebasis von eZ Publish fort, einem Produkt, das sein Hersteller nach fast 18 Jahren
eingestellt hat. Der Name **eZ Publish** gehört zu dieser Vergangenheit ([Wer ist 7x?](../wer-ist-7x.md#die-jahre-von-ez-publish-1999-bis-2021)).

Regeln:

- Verwenden Sie „eZ Publish“ nur in der Vergangenheitsform, für die Geschichte: „entwickelt, als die Software noch
  eZ Publish hieß“, „die Jahre von eZ Publish“, „eine Site, die auf eZ Publish 4 gebaut wurde“.
- Stellen Sie „eZ Publish“ nie als aktuellen Namen, als zweiten Namen oder in einem Produkttitel dar.
- Wenn Sie eine Migration beschreiben, sagen Sie sinngemäß, was die Dienstleistungsseite von 7x sagt: Eine Site, die
  gebaut wurde, als die Software noch eZ Publish hieß, wird auf das aktuelle Exponential 6 gebracht ([Wer ist 7x?](../wer-ist-7x.md#dienstleistungen-und-hosting)).
- Exponential steht in keiner Verbindung zu eZ Systems oder Ibexa. Die README sagt: „This project is not associated
  with the original eZ Publish software or its original developer, eZ Systems or Ibexa“. Deuten Sie nie eine
  Partnerschaft, eine Empfehlung oder eine Kontinuität der Unternehmen an.
- Manche historischen Quellen tragen den alten Namen in ihrem eigenen Titel, zum Beispiel eZpedia, „The free eZ
  Publish encyclopedia that anyone can edit“, oder das Buch „Learning eZ Publish“. Zitieren Sie solche Titel
  unverändert.

## Bezeichner im Code bleiben, wie sie sind

Die Umbenennung „touched text, not identifiers“, betraf also Texte, nicht Bezeichner ([Was absichtlich nicht geändert wurde](../../funktionen/6.0/umbenennung-in-exponential.md#was-sich-mit-absicht-nicht-geändert-hat)).
„Korrigieren“ Sie Folgendes nie, weder im Code noch in der Dokumentation von Code noch im technischen Marketing:

- PHP-Klassen mit dem Präfix `eZ`: `eZContentObject`, `eZINI`, `eZDB`, `eZPersistentObject`;
- Datenbanktabellen wie `ezcontentobject` und Pfade im Stil von `ezpublish-legacy`;
- Template-Operatoren, Fetch-Funktionen, Namen von INI-Blöcken und -Schlüsseln;
- Paketnamen von Erweiterungen wie `se7enxweb/ezwebin`, `ezflow`, `eztags`;
- Skriptnamen wie `bin/php/ezcache.php`.

`eZ` für sich allein ist ein Präfix im Code, nicht der Produktname. Die Regel lautet: Benennen Sie das **Produkt** um,
nie ein Symbol oder eine Datei.

### Namen für neue Dinge

Die Hilfe legt keine schriftliche Regel für die Benennung neuer Module oder Klassen fest. Was Sie in den Quellen
beobachten können: Neue Produkte und Pakete der Linie 6.0.x verwenden `Exponential` oder `exp` (Exponential Velocity,
Exponential Intelligence als `expai`, Exponential Layouts als `explayouts`, `expqueue`, `exp_adminui`,
Konsolenbefehle mit dem Präfix `exp:`, siehe das [Glossar](../../glossar.md)), und die eigenen Erweiterungen von 7x
verwenden `sevenx_` (`sevenx_authentication_2fa`, `sevenx_dse`). Folgen Sie diesem Muster und fragen Sie im
Zweifel die Maintainer.

## Andere Unternehmen und Produkte

Die Quellen erwähnen andere Unternehmen sachlich. Behalten Sie diesen Ton bei.

| Name | Wie die Quellen ihn verwenden | Ihre Regel |
|---|---|---|
| eZ Systems | Entwickler von eZ Publish; sein Copyright von 1999 bis 2014 bleibt im Code | Nur als Geschichte und in Copyright-Vermerken nennen |
| Ibexa, eZ Platform, Ibexa DXP OSS | Die auf Symfony basierenden Nachfolger von eZ Publish; das Produkt wurde „At the specific request of Ibexa“ umbenannt, auf ausdrücklichen Wunsch von Ibexa | Nur sachliche Aussagen; keine Vergleiche über das hinaus, was die Quellen sagen ([Exponential sachlich vergleichen](exponential-sachlich-vergleichen.md)) |
| Netgen | Exponential Layouts bringt „the Netgen Layouts concept to the Exponential kernel“, also das Konzept von Netgen Layouts in den Kernel von Exponential | Nennen Sie sie, wo ihre Arbeit verwendet wird |
| Mugo Web | Funktionen von LoveStack wurden „with full credit to Mugo Web“ nachgebaut, unter voller Nennung von Mugo Web | Nennen Sie sie |
| CJW Network | `cjw_newsletter`, request-shield | Nennen Sie sie; request-shield ist „a project of CJW Network ... not of the Foundation“, ein Projekt von CJW Network und nicht der Foundation |

Quellen: [Wer ist 7x?](../wer-ist-7x.md#mitwirkende-und-freunde-des-projekts),
[Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md#begleitprojekte).

## Sprache und Schreibweise

- **Amerikanisches Englisch** in der englischen Oberfläche und Dokumentation: license (nicht licence), color,
  behavior, organization. Die Übersetzung der Administration `eng-US` ist in amerikanischem Englisch geschrieben
  ([Sprachen in Exponential](../../sprachen.md#8-eine-übersetzung-der-administrationsoberfläche-hinzufügen-oder-vervollständigen)).
- **Verwenden Sie das Wort, das der Leser auf dem Bildschirm sieht.** Der Stilleitfaden der Hilfe legt die Regel fest
  „Use the product name the reader sees on screen, not an internal project or code name“ - den Produktnamen, den der
  Leser auf dem Bildschirm sieht, nicht einen internen Projekt- oder Codenamen - und bevorzugt Begriffe der
  Oberfläche wie „Objekt“, „Inhaltsklasse“, „Entwurf“, „Papierkorb“
  (`doc/eng-us/specifications/6.0/help-style-guide.md`, ein Vorschlag vom 18. Oktober 2026).
- Schreiben Sie die Namen von Menüs und Schaltflächen so, wie sie erscheinen: **Setup**, **Zur Veröffentlichung
  schicken**.

## Richtig und Falsch

| Richtig | Falsch |
|---|---|
| Exponential 6.0.15 | eZ Publish 6, eZ Exponential, Exponential Publish |
| Exponential ist Open Source unter der GNU GPL v2 oder höher | Exponential ist Freeware, Exponential ist gemeinfrei |
| 7x leitet Exponential | 7x besitzt Exponential (das Copyright ist geteilt, die Lizenz ist die GPL) |
| die Exponential Foundation, eine gemeinnützige Organisation | die 7x Foundation, Exponential Inc. |
| Exponential führt die Codebasis von eZ Publish fort | Exponential ist das neue eZ Publish, eZ Publish heißt jetzt Exponential von eZ Systems |
| Sites, die in den Jahren von eZ Publish gebaut wurden | eZ-Publish-Sites (als trüge das Produkt noch diesen Namen) |
| Exponential Velocity, Velocity | Exponential Qbix (Qbix ist die Upstream-Engine) |
| Exponential Intelligence, `expai` | Exponential AI, ExpAI, „die KI veröffentlicht Ihre Inhalte“ (sie veröffentlicht nie von sich aus) |
| Exponential Layouts, der Layout-Editor | Netgen Layouts für Exponential (es ist eine Portierung des Konzepts, mit eigenem Namen) |
| `eZContentObject`, `eZINI` im Code | `ExponentialContentObject` (eine Klasse, die es nicht gibt) |
| license, color, behavior | licence, colour, behaviour |
| steht in keiner Verbindung zu eZ Systems oder Ibexa | offizieller Nachfolger von eZ Publish durch Ibexa |

## Was die Quellen nicht entscheiden

Die für diese Seite verwendete Dokumentation legt **keine** Regeln fest für:

- das Logo: seine Dateien, Farben, Schutzzone oder Verwendung durch Partner;
- den Markenschutzstatus irgendeines Namens;
- eine Tagline oder einen Slogan;
- die Bezeichnung von Partnern oder Wiederverkäufern („zertifiziert“, „offizieller Partner“).

Erfinden Sie dafür keine Regeln. Fragen Sie 7x oder die Foundation über ihre Websites, und bewahren Sie deren Antwort
bei Ihren Markendateien auf. Die README vermerkt, dass die Plattformlinie umbenannt wurde, „to avoid any trademark
issues“, um markenrechtliche Probleme zu vermeiden; das ist eine Aussage darüber, warum sich ein Name geändert hat,
keine Markenrichtlinie.

## Eine Checkliste vor dem Veröffentlichen

- [ ] Jeder Produktname ist geschrieben wie in [Die Produktfamilie](#die-produktfamilie).
- [ ] „eZ Publish“ erscheint nur in der Vergangenheitsform, als Geschichte.
- [ ] Kein Satz deutet eine Verbindung zu eZ Systems oder Ibexa an.
- [ ] 7x und die Exponential Foundation werden nicht verwechselt.
- [ ] Bezeichner im Code (`eZ*`-Klassen, `ez*`-Dateien und -Tabellen) sind unverändert.
- [ ] Copyright- und Lizenzvermerke sind genau übernommen.
- [ ] Amerikanische Schreibweise in englischen Texten.
- [ ] Versionsnummern sind vollständig geschrieben (`6.0.14`), mit Datum, wenn es darauf ankommt.
- [ ] Die Arbeit anderer (Netgen, Mugo Web, CJW Network) ist genannt, wo sie verwendet wird.

## Verwandte Seiten

- [Vertrieb und Marketing: hier beginnen](vertrieb-und-marketing-hier-beginnen.md)
- [Positionierung und Nutzenversprechen](positionierung-und-nutzenversprechen.md)
- [Fallstudien und Content-Marketing schreiben](fallstudien-und-content-marketing.md)
- [Lizenzen für Käufer erklärt](lizenzen-fuer-kaeufer.md)
- [Was Exponential ist: die Produktfamilie](../fuehrung/was-exponential-ist.md)
- [Das Produkt heißt Exponential](../../funktionen/6.0/umbenennung-in-exponential.md)
- [Wer ist 7x?](../wer-ist-7x.md), [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
