---
title: "Checkliste: Anforderungen"
audience: businesses, project-manager, website-creator
category: business-solutions
order: 40
summary: Eine kopierfertige Checkliste der geschäftlichen Anforderungen für ein Exponential-Projekt, nach Bereichen gegliedert (Inhalte, Bearbeitung, Benutzer, Shop, Formulare, Mail, Sprachen, Websites, Suche, Apps, Betrieb, Sicherheit, Barrierefreiheit), mit der eingebauten Funktion oder Hilfeseite, die für jede Zeile zu prüfen ist, und Spalten für Priorität und Entscheidung.
keywords: Anforderungen, Checkliste, Umfang, Muss, Sollte, Funktionen, Inhalte, Workflow, Shop, Formulare, Newsletter, mehrsprachig, Multisite, Suche, REST API, Hosting, Sicherheit, Barrierefreiheit, Vorlage, requirements, checklist, scope, must have, should have, features, content, store, forms, multilingual, multisite, search, security, accessibility, template
type: help
---
# Checkliste: Anforderungen

Diese Seite ist für Inhaber, Führungskräfte und Projektleiter, die aufschreiben müssen, was eine Website können muss.
Jede Zeile nennt die Hilfeseite, auf der Sie prüfen können, wie Exponential es macht, sodass das Gespräch mit einem
Partner bei dem beginnt, was eingebaut ist, und nicht bei eigenem Code. Die Fassung für Projektleiter mit mehr
technischen Zeilen ist die Anforderungs-Checkliste in den [Projektvorlagen](../../fuehrung/projektvorlagen.md#anforderungs-checkliste).

## Inhalt

1. [So füllen Sie sie aus](#so-füllen-sie-sie-aus)
2. [Die Checkliste](#die-checkliste)
3. [Zeilen, die Sie selbst ergänzen](#zeilen-die-sie-selbst-ergänzen)
4. [Nach dem Ausfüllen](#nach-dem-ausfüllen)

## So füllen Sie sie aus

- **Priorität**: M = muss, S = sollte, K = kann später, N = nicht nötig.
- **Prüfung**: Öffnen Sie die verlinkte Seite oder bitten Sie Ihren Partner, es auf einer Testinstallation zu zeigen.
- **Entscheidung**: eingebaut, Erweiterung, Sonderentwicklung oder gestrichen.
- Löschen Sie Bereiche, die Sie nicht brauchen.

## Die Checkliste

```text
ANFORDERUNGS-CHECKLISTE - <Projekt>   Version <x>, <Datum>   Verantwortlich: <Name>
Priorität: M / S / K / N     Entscheidung: eingebaut / Erweiterung / Sonderentwicklung / gestrichen

INHALTE UND BEARBEITUNG                                        Prio  Entsch.   Notizen
[ ] Eigene Inhaltstypen (Produkte, Veranstaltungen, Dokumente ...) ___  ________  ______
[ ] Versionen und Verlauf jeder Änderung                       ___   ________  ______
[ ] Freigabe vor der Veröffentlichung; zeitgesteuerte Veröffentl. ___ ________  ______
[ ] Visueller Seitenbaukasten für Landingpages                 ___   ________  ______
[ ] Bildformate und automatische Bildgrößen                    ___   ________  ______
[ ] Schreibassistent für Redakteure (optional)                 ___   ________  ______

BENUTZER UND ZUGÄNGE
[ ] Rollen mit eingeschränkten Rechten pro Sektion oder Teilbaum ___  ________  ______
[ ] Zweistufige Anmeldung für Mitarbeiter                      ___   ________  ______
[ ] Anmeldung mit unserem Verzeichnis (LDAP)                   ___   ________  ______
[ ] Registrierung von Besuchern und Bereiche nur für Mitglieder ___  ________  ______

SHOP
[ ] Produktkatalog, Warenkorb, Kasse, Bestellungen             ___   ________  ______
[ ] USt-Regeln, Währungen, Ländergruppen                       ___   ________  ______
[ ] Zahlungsanbieter: <Name>                                   ___   ________  ______
[ ] Bestellbelege und Bestätigungsmail                         ___   ________  ______

FORMULARE UND MAIL
[ ] Kontakt- und Lead-Formulare mit Export                     ___   ________  ______
[ ] Spamschutz (Formular-Tokens, CAPTCHA)                      ___   ________  ______
[ ] Newsletter mit Double-Opt-in                               ___   ________  ______
[ ] Mail-Einwilligung und Abmeldung mit einem Klick            ___   ________  ______

SPRACHEN UND WEBSITES
[ ] Sprachen: <Liste>; Rechts-nach-links nötig? ___            ___   ________  ______
[ ] Mehrere Websites oder Marken aus einer Installation        ___   ________  ______

SUCHE UND AUFFINDBARKEIT
[ ] Suche auf der Website (eingebaut oder Solr für große Sites) ___  ________  ______
[ ] Sprechende URLs, Weiterleitungen, Sitemaps, robots.txt     ___   ________  ______
[ ] Feeds (RSS, Atom, Podcasts)                                ___   ________  ______

APPS UND INTEGRATIONEN
[ ] REST API für eine mobile App oder ein anderes System       ___   ________  ______
[ ] Anbindungen an: <Systeme>                                  ___   ________  ______

BETRIEB
[ ] Hosting-Modell und Datenbank gewählt                       ___   ________  ______
[ ] Backups mit getesteter Wiederherstellung                   ___   ________  ______
[ ] Wartungsmodus für geplante Arbeiten                        ___   ________  ______
[ ] Monitoring und Fehlerreferenzen                            ___   ________  ______

SICHERHEIT, DATEN UND BARRIEREFREIHEIT
[ ] Prüfprotokoll, wer was getan hat                           ___   ________  ______
[ ] Aufbewahrungsfristen für Logs und Daten festgelegt         ___   ________  ______
[ ] Content Security Policy auf der öffentlichen Site erzwungen ___  ________  ______
[ ] Einzuhaltender Standard für Barrierefreiheit: <Standard>   ___   ________  ______
```

Wo Sie jeden Bereich prüfen:

| Bereich | Hilfeseiten |
|---|---|
| Inhalte und Bearbeitung | [Das Inhaltsmodell und das Bearbeiten von Inhalten](../../../anleitungen/inhaltsmodell-und-bearbeitung.md), [Versionen eines Objekts](../../../anleitungen/inhaltsverlauf.md), [Workflows](../../../anleitungen/workflows.md), [Landingpages mit Layouts](../../ersteller/website-ersteller/landingpages-mit-dem-layouts-editor.md), [Bilder und Bildvarianten](../../ersteller/website-ersteller/bilder-und-bildvarianten.md), [Exponential Intelligence](../../ersteller/anwendungsentwickler/exponential-intelligence.md) |
| Benutzer und Zugänge | [Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md), [Zweistufige Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md), [Verzeichnisanmeldung und LDAP](../intranets/verzeichnisanmeldung-und-ldap.md), [Mitgliederregistrierung und -bereiche](../../ersteller/website-ersteller/mitgliederregistrierung-und-mitgliederbereiche.md) |
| Shop | [Shop-Überblick](../../ersteller/website-ersteller/shop-ueberblick.md), [Produkte, Preise und Mehrwertsteuer](../../ersteller/website-ersteller/produkte-preise-und-mehrwertsteuer.md), [Zahlungsanbieter](../../ersteller/website-ersteller/payment-gateways.md), [Bestellbelege](../../../funktionen/6.0/bestellbelege.md) |
| Formulare und Mail | [Formulare mit dem Information Collector](../../ersteller/website-ersteller/formulare-mit-dem-informationssammler.md), [Spamschutz für Formulare](../../ersteller/website-ersteller/spamschutz-fuer-formulare.md), [Newsletter mit cjw_newsletter](../../ersteller/website-ersteller/newsletter-mit-cjw-newsletter.md), [Einwilligung und Formulare](../sicherheit/einwilligung-und-formulare.md) |
| Sprachen und Websites | [Mehrsprachige Websites](../../ersteller/website-ersteller/mehrsprachige-websites.md), [Websites mit Rechts-nach-links-Schrift](../../ersteller/website-ersteller/websites-von-rechts-nach-links.md), [Multisite einrichten](../../ersteller/website-ersteller/multisite-einrichten.md) |
| Suche und Auffindbarkeit | [Suche auf der Website](../../ersteller/website-ersteller/suche-auf-der-website.md), [URL-Aliase und sprechende URLs](../../ersteller/website-ersteller/url-aliasse-und-sprechende-urls.md), [Sitemaps](../../ersteller/website-ersteller/sitemaps.md), [RSS-Feeds](../../ersteller/website-ersteller/rss-feeds-fuer-ihre-website.md) |
| Apps und Integrationen | [REST API](../../ersteller/anwendungsentwickler/rest-api.md), [Headless Exponential](../../ersteller/anwendungsentwickler/headless-exponential.md), [OAuth und API-Schlüssel](../../ersteller/anwendungsentwickler/oauth-und-api-schluessel.md) |
| Betrieb | [Hosting auswählen](../auswahl/hosting-auswaehlen.md), [Backups und Wiederherstellungen](../../ersteller/website-ersteller/backups-und-wiederherstellungen.md), [Wartungsmodus](../../../funktionen/6.0/wartungsmodus.md) |
| Sicherheit, Daten, Barrierefreiheit | [Das Prüfprotokoll](../../../funktionen/6.0/pruefprotokoll.md), [Aufbewahrung und Löschung von Daten](../sicherheit/aufbewahrung-und-loeschung-von-daten.md), [Content Security Policy](../../../funktionen/6.0/content-security-policy.md), [Barrierefreiheit testen](../../ersteller/website-ersteller/barrierefreiheit-testen.md) |

## Zeilen, die Sie selbst ergänzen

- [ ] Rechtliche und vertragliche Regeln Ihrer Branche (fragen Sie Ihren Berater).
- [ ] Integrationen mit Ihren eigenen Systemen (ERP, CRM, Buchhaltung).
- [ ] Marken- und Gestaltungsregeln.
- [ ] Erwartungen an die Leistung zu Ihrer geschäftigsten Zeit.
- [ ] Schulung und Dokumentation für Ihre Mitarbeiter.

## Nach dem Ausfüllen

1. Hängen Sie sie an die [Projektbeschreibung](vorlage-fuer-projektbeschreibungen.md) an.
2. Bitten Sie jeden Partner, pro Zeile anzugeben, ob es eingebaut, eine Erweiterung oder Sonderentwicklung ist, und die
   eingebauten Funktionen auf einer Testinstallation zu zeigen.
3. Verwenden Sie die Antworten im [Bewertungsbogen für Anbieter](bewertungsbogen-fuer-anbieter.md).
4. Bewahren Sie sie als Abnahmeliste für den Start auf ([Qualitätsschranken und Abnahme](../../fuehrung/qualitaetsschranken-und-abnahme.md)).

## Verwandte Seiten

- [Projektvorlagen](../../fuehrung/projektvorlagen.md)
- [Was Sie bauen können](../../ersteller/website-ersteller/was-sie-bauen-koennen.md)
- [Funktionshighlights für Kunden](../../geschaeft/funktionshighlights-fuer-kunden.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
