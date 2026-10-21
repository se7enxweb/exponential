---
title: Was Sie bauen können
audience: website-creator, project-manager
category: website-creation
order: 30
summary: Die Arten von Websites, für die Exponential gebaut ist, die Teile, die für jede Art mitgeliefert werden, und die Fragen, an denen Sie erkennen, ob eine Website-Idee passt, bevor Sie beginnen.
keywords: Website, was kann ich bauen, Unternehmenswebsite, Magazin, Intranet, Shop, Multisite, mehrsprachig, Landingpages, Newsletter, Formulare, Mitgliederbereich, Website-Ersteller, website, what can I build, corporate site, magazine, intranet, shop, multisite, multilingual, landing pages, newsletter, forms, member area, website creator
type: help
---
# Was Sie bauen können

Diese Seite richtet sich an Website-Ersteller, die vor dem Start wissen wollen, welche Arten von Websites Exponential
ab Werk abdeckt und welche Teile des Produkts jede Art verwendet. Sie beantwortet: was im Lieferumfang steckt, woraus
eine typische Website jeder Art besteht und an welchen Fragen Sie erkennen, dass die Idee passt.

## Inhalt

1. [Die kurze Antwort](#die-kurze-antwort)
2. [Arten von Websites und die Teile, die sie verwenden](#arten-von-websites-und-die-teile-die-sie-verwenden)
3. [Was jede Website erhält](#was-jede-website-erhält)
4. [Was Sie mit Erweiterungen ergänzen](#was-sie-mit-erweiterungen-ergänzen)
5. [Passt meine Idee?](#passt-meine-idee)

## Die kurze Antwort

Exponential ist ein Open-Source-Content-Management-System und Anwendungsframework. Seine zentrale Idee ist ein
Inhaltsmodell, das Sie selbst definieren: Arten von Inhalten (Inhaltsklassen wie Article oder Product) sind Listen
typisierter Felder, und jeder Inhalt ist ein Objekt einer solchen Klasse, das mit seinen Versionen und Übersetzungen in
einem Inhaltsbaum liegt ([1.1 Was Exponential ist](../../../installation/01-einfuehrung.md#11-was-exponential-ist)).
Nachrichtenveröffentlichung, E-Commerce, Foren, Benutzerverwaltung, Rollen und Richtlinien, mehrsprachige Inhalte und
eine Template-Sprache sind eingebaut.

Alles, was "Seiten mit strukturierten Inhalten, die Besuchern über Templates gezeigt werden" ist, passt also von Natur
aus: von einer fünfseitigen Unternehmenswebsite bis zu einem Website-Netzwerk in vielen Sprachen.

## Arten von Websites und die Teile, die sie verwenden

| Art der Website | Woraus sie in Exponential besteht | Lesen Sie zuerst |
|---|---|---|
| Website eines Unternehmens oder einer Organisation | Ordner und Artikel im Inhaltsbaum, ein Design, Menüs, ein Kontaktformular | [Ihre Website planen](ihre-website-planen.md), [Ein Kontaktformular bauen](ein-kontaktformular-bauen.md) |
| Magazin, Nachrichten oder Blog | Artikel- und Blog-Klassen, Ansichtsmodi (full, line), RSS-Feeds, Tags, Suche | [Ihr Inhaltsmodell entwerfen](ihr-inhaltsmodell-entwerfen.md), [RSS-Feeds für Ihre Website](rss-feeds-fuer-ihre-website.md), [Verschlagworten mit eztags](verschlagworten-mit-eztags.md) |
| Kampagnen- und Landingpages | Der Layouts-Editor: Layouts mit Zonen und Blöcken, Regeln, die Seiten ein Layout geben | [Landingpages mit dem Layouts-Editor](landingpages-mit-dem-layouts-editor.md) |
| Mehrere Marken oder Regionen | Mehrere Siteaccesses in einer Installation, jeder mit eigenem Host, Design und eigenen Sprachen | [Multisite einrichten](multisite-einrichten.md) |
| Mehrsprachige Website | Inhaltssprachen, Übersetzungen je Objekt, ein Siteaccess je Sprache | [Eine mehrsprachige Website bauen](mehrsprachige-websites.md) |
| Mitgliederbereich oder Intranet | Benutzerregistrierung, nach Sektion eingeschränkte Rollen, eine Anmeldepflicht je Siteaccess | [Registrierung von Mitgliedern und Bereiche nur für Mitglieder](mitgliederregistrierung-und-mitgliederbereiche.md) |
| Onlineshop | Produktklassen mit einem Preis-Datentyp, Warenkorb, Kasse, Bestellungen, Mehrwertsteuer, Payment-Gateways | [Einen Shop betreiben: Überblick](shop-ueberblick.md) |
| Website rund um einen Newsletter | `cjw_newsletter`: Listen, Abonnements, Ausgaben, Versand | [Newsletter mit cjw_newsletter](newsletter-mit-cjw-newsletter.md) |
| Website mit einer Anwendung darin | Eigene Module und Ansichten in einer Erweiterung, neben den Inhalten | [Anwendungsentwickler: hier beginnen](../anwendungsentwickler/anwendungsentwickler-hier-beginnen.md) |

## Was jede Website erhält

Was immer Sie bauen, diese Dinge kommen mit dem Kernel und sind vom ersten Tag an bereit:

- **Die Administrationsoberfläche** für Redaktion und Administratoren, mit einem Inhaltsbaum, einer Medienbibliothek,
  Benutzern und Rollen ([Rundgang durch die Administration](../../rundgang-durch-die-administration.md)).
- **Versionen und Entwürfe** für jedes Objekt und ein Papierkorb, aus dem Sie wiederherstellen können
  ([Veröffentlichen und Versionen](../../veroeffentlichen-und-versionen.md)).
- **Rollen und Richtlinien**: wer was lesen, anlegen, bearbeiten oder veröffentlichen darf, eingeschränkt nach Sektion,
  Teilbaum, Klasse oder Sprache ([Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)).
- **URL-Aliasse**: lesbare Adressen aus den Namen der Inhalte
  ([URL-Aliasse und sprechende URLs](url-aliasse-und-sprechende-urls.md)).
- **Caches** auf mehreren Ebenen, sodass Seiten ohne zusätzlichen Aufwand schnell sind
  ([Leistung und Caching](leistung-und-caching.md)).
- **Suche** eingebaut, mit Solr als Option für große Websites ([Suche auf der Website](suche-auf-der-website.md)).
- **Eine Content Security Policy** für die Administration und die Werkzeuge, um Ihrer Website eine zu geben
  ([Content Security Policy](../../../funktionen/6.0/content-security-policy.md)).
- **Diese Hilfe**, in der Administration, für Sie und Ihre Redaktion.

## Was Sie mit Erweiterungen ergänzen

Erweiterungen sind Composer-Pakete, die Funktionen hinzufügen ([Erweiterungen](../../../anleitungen/extensions.md)).
Diejenigen, denen ein Website-Ersteller am häufigsten begegnet, sind jeweils unter
[Erweiterungen](../../../funktionen/6.0/erweiterungen/README.md) dokumentiert:

| Bedarf | Erweiterung |
|---|---|
| Ein fertiges Design | `sevenx_themes_simple`, `ezwebin`, `ezdemo` |
| Landingpages | die Erweiterungen `explayouts*` (der Layouts-Editor) oder `ezflow` für klassische Blockseiten |
| Tags | `eztags` |
| Newsletter | `cjw_newsletter` |
| XML-Sitemaps | `bcgooglesitemaps` |
| Seitenmetadaten | `xrowmetadata` |
| CAPTCHA in Formularen | `recaptcha`, `hcaptcha` |
| Solr-Suche | `ezfind` |
| Zahlung | `ezpaypal`, `ezauthorize` |

## Passt meine Idee?

Beantworten Sie diese Fragen, bevor Sie beginnen; jede Antwort verweist auf eine Seite, die die Möglichkeiten erklärt.

- [ ] Lassen sich die Inhalte als eine Handvoll Arten (Klassen) mit Feldern beschreiben? Wenn ja, passt das
      Inhaltsmodell. ([Ihr Inhaltsmodell entwerfen](ihr-inhaltsmodell-entwerfen.md))
- [ ] Werden andere Personen als Sie die Inhalte bearbeiten? Dann planen Sie Rollen und eine Übergabe an die Redaktion
      ([Die Website an die Redaktion übergeben](die-website-an-die-redaktion-uebergeben.md)).
- [ ] Wie viele Sprachen, wie viele Websites? ([Eine mehrsprachige Website bauen](mehrsprachige-websites.md))
- [ ] Melden sich Besucher an, kaufen sie oder abonnieren sie etwas?
      ([Registrierung von Mitgliedern und Bereiche nur für Mitglieder](mitgliederregistrierung-und-mitgliederbereiche.md),
      [Einen Shop betreiben: Überblick](shop-ueberblick.md), [Newsletter mit cjw_newsletter](newsletter-mit-cjw-newsletter.md))
- [ ] Braucht sie Logik, die keine Erweiterung bietet? Dann schreibt ein Anwendungsentwickler ein Modul
      ([Exponential als Anwendungsplattform](../anwendungsentwickler/exponential-als-anwendungsplattform.md)).
- [ ] Wo wird sie laufen? ([Hosting für eine Website](hosting-fuer-eine-website.md))

> **Hinweis**
> Wenn die Antwort auf eine Frage "wissen wir noch nicht" lautet, ist das eine Planungsaufgabe, kein Hindernis. Die
> Seite [Ihre Website planen](ihre-website-planen.md) macht aus diesen Fragen einen Plan.

## Verwandte Seiten

- [Website-Ersteller: hier beginnen](website-ersteller-hier-beginnen.md)
- [Ihre Website planen](ihre-website-planen.md)
- [Was Exponential ist: die Produktfamilie](../../fuehrung/was-exponential-ist.md)
- [Wer ist 7x?](../../wer-ist-7x.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
