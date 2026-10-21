---
title: Mobile Strategie
audience: businesses, project-manager, sales-marketing
category: business-solutions
order: 10
summary: Wie ein Unternehmen zwischen einer responsiven Website, einer eigenen App auf der REST API oder beidem entscheidet, mit Exponential als der einen Quelle für Inhalte - die Optionen, was jede an Arbeit und Betrieb kostet, Redakteure auf Telefonen und die Fragen, die zuerst zu beantworten sind.
keywords: mobile Strategie, mobile App, responsive Website, headless, hybrid, REST API, App, Telefone, Redakteure auf Telefonen, Entscheidung, Unternehmen, mobile strategy, mobile app, responsive website, hybrid, app, phones, editors on phones, decision, business
type: help
---
# Mobile Strategie

Diese Seite ist für Inhaber und Führungskräfte, denen die Frage gestellt wird: „Brauchen wir eine App?“ Sie stellt die
Optionen dar, die Exponential unterstützt, von einer Website, die auf Telefonen gut funktioniert, bis zu einer nativen
App, die dieselben Inhalte über die API liest, und hilft Ihnen, mit Fragen zu Ihren Kunden statt zur Technik zu
entscheiden. Sie enthält keine Marktzahlen; messen Sie Ihre eigenen Besucher, bevor Sie entscheiden.

## Inhalt

1. [Bei dem beginnen, was Kunden tun](#bei-dem-beginnen-was-kunden-tun)
2. [Die drei Optionen](#die-drei-optionen)
3. [Warum eine Quelle für Inhalte wichtig ist](#warum-eine-quelle-für-inhalte-wichtig-ist)
4. [Was eine App Ihnen an Arbeit hinzufügt](#was-eine-app-ihnen-an-arbeit-hinzufügt)
5. [Redakteure auf Telefonen](#redakteure-auf-telefonen)
6. [Fragen, die zuerst zu beantworten sind](#fragen-die-zuerst-zu-beantworten-sind)

## Bei dem beginnen, was Kunden tun

Bevor Sie einen Kanal wählen, finden Sie aus Ihren eigenen Daten heraus:

- [ ] Welcher Anteil der Besuche und Bestellungen von Telefonen kommt (Ihre Analysen; die Optionen von Exponential für
      Statistiken stehen unter [Besucherstatistiken und Analysen](../../../../eng-us/help/creators/website-creators/statistics-and-analytics.md)).
- [ ] Was Kunden auf einem Telefon tun: Öffnungszeiten nachsehen, Neuigkeiten lesen, nachbestellen, eine Bestellung
      prüfen, einen Katalog durchstöbern.
- [ ] Was sie mit einer App öfter tun würden: Offline-Nutzung, Benachrichtigungen, eine Kundenkarte, Scannen.
- [ ] Welche Partner oder Marktplätze sie bereits nutzen, wo Ihre Inhalte stattdessen erscheinen könnten
      ([Verkaufen über Netzwerke und Partner](verkaufen-ueber-netzwerke-und-partner.md)).

Wenn Kunden vor allem Dinge nachsehen und gelegentlich kaufen, bedient sie meist eine schnelle responsive Website. Wenn
sie täglich wiederkommen und Funktionen des Telefons brauchen, kann eine App ihre Kosten wert sein.

## Die drei Optionen

| Option | Wie sie mit Exponential funktioniert | Gut für |
|---|---|---|
| **Responsive Website** | Das Design der Website passt sich dem Bildschirm an; das öffentliche Standarddesign wird als responsiv beschrieben | Die meisten Unternehmen; in jedem Fall der erste Schritt |
| **Hybrid**: Website plus App | Die Website bleibt in den Templates von Exponential; eine App liest dieselben Inhalte über die REST API | Unternehmen mit einem treuen, wiederkehrenden Publikum |
| **Headless**: ein eigenes Frontend | Ein eigenes Frontend (App, Single Page Application, Generator für statische Websites) rendert alle Inhalte aus der API | Teams mit eigenen Frontend-Entwicklern |

Die Dokumentation für App-Entwickler empfiehlt: „Many projects keep the site in templates and add an app on the API.
That keeps preview, layouts and SEO simple for the site, and gives the app the same content. Start hybrid unless there
is a reason not to“ (Viele Projekte behalten die Website in Templates und fügen eine App auf der API hinzu. Das hält
Vorschau, Layouts und SEO für die Website einfach und gibt der App dieselben Inhalte. Beginnen Sie hybrid, wenn nichts
dagegen spricht, [Headless Exponential](../../../../eng-us/help/creators/app-creators/headless-exponential.md#hybrid-templates-and-api-together)).

## Warum eine Quelle für Inhalte wichtig ist

Welche Option Sie auch wählen, Redakteure behalten einen Arbeitsort: Inhaltsklassen, Bearbeitung, Versionen,
Übersetzungen, Workflows und Berechtigungen bleiben in Exponential, und jeder API-Aufruf wird gegen die Richtlinien des
Benutzers geprüft
([Headless Exponential](../../../../eng-us/help/creators/app-creators/headless-exponential.md#what-you-keep-and-what-you-replace)).
Für ein Unternehmen bedeutet das:

- eine Produktbeschreibung, ein Preis und ein Foto, auf der Website und in der App gezeigt;
- ein Freigabeablauf, ein Übersetzungsablauf;
- ein Ort, um einen Fehler zu korrigieren.

## Was eine App Ihnen an Arbeit hinzufügt

| Punkt | Nur Website | Mit einer App |
|---|---|---|
| Design und Frontend-Code | Ein Design | Das Design plus die eigenen Bildschirme der App, pro Plattform |
| Vorschau unveröffentlichter Inhalte | Eingebaut, mit den Templates der Website | Die App braucht eine eigene Vorschau; die Seite für App-Entwickler nennt das „the part teams forget“ (den Teil, den Teams vergessen) |
| Releases | Die Website ausrollen | Die Website ausrollen und Updates der App über App-Stores veröffentlichen |
| Zugangsdaten | Die Konten der Redakteure | Dazu die Zugangsdaten der App, rotiert und widerrufen ([Apps und API-Schlüssel](apps-und-api-schluessel.md)) |
| Verfügbarkeit | Die Website muss antworten | Die Website und die API müssen antworten ([Zuverlässigkeit des App-Backends](zuverlaessigkeit-des-app-backends.md)) |
| Sichtbarkeit in Suchmaschinen | Seiten werden indiziert | Inhalte in der App werden nicht indiziert; die Website trägt weiterhin die SEO |

Bitten Sie jede Agentur, die ein Angebot für eine App abgibt, jede Zeile dieser Tabelle abzudecken.

## Redakteure auf Telefonen

Auch Redakteure können von Telefonen aus arbeiten:

- Das Design **admin4** hat ein Telefonlayout mit einem Hamburger-Menü, das „always fits the visible screen“ (immer auf
  den sichtbaren Bildschirm passt), Kacheln von 44 px für die Tabs und eine Anmeldeseite, die „fast on phones“ (auf
  Telefonen schnell) ist, mit Text von 16 px, „so mobile Safari does not zoom“ (damit das mobile Safari nicht zoomt,
  [Design admin4](../../../../eng-us/features/6.0/admin4-design.md)).
- **admin3** ist die klassische Administration „with a sidebar that collapses, menus that work by touch, and content
  forms that stack instead of overflowing“ (mit einer einklappbaren Seitenleiste, Menüs, die per Berührung
  funktionieren, und Inhaltsformularen, die sich stapeln, statt überzulaufen,
  [Das responsive Design der Administration](../../../../eng-us/features/6.0/admin3-responsive-admin.md)).

Das zählt für kleine Teams: Eine Neuigkeit, eine Änderung der Öffnungszeiten oder eine Preiskorrektur lässt sich
unterwegs erledigen.

## Fragen, die zuerst zu beantworten sind

- [ ] Was werden Kunden auf einem Telefon tun, das sie heute nicht gut tun können?
- [ ] Funktioniert die Website bereits auf Telefonen? (Wenn nicht, beheben Sie das zuerst:
      [Responsive Websites](responsive-websites.md).)
- [ ] Welche Inhalte und Funktionen würde die App brauchen: nur Lesen oder auch Bestellungen, Konten, Formulare?
- [ ] Wer baut und pflegt die App, für wie viele Plattformen und wie lange?
- [ ] Wie meldet die App Benutzer an ([Apps und API-Schlüssel](apps-und-api-schluessel.md))?
- [ ] Wer prüft Inhalte in der Vorschau, bevor sie die App erreichen?
- [ ] Was geschieht mit Kunden, wenn die API nicht erreichbar ist
      ([Zuverlässigkeit des App-Backends](zuverlaessigkeit-des-app-backends.md))?

## Verwandte Seiten

- [Responsive Websites](responsive-websites.md)
- [Headless mit der REST API](headless-mit-der-rest-api.md)
- [Checkliste mobil](checkliste-mobil.md)
- [Headless Exponential](../../../../eng-us/help/creators/app-creators/headless-exponential.md)
- [Wie Unternehmen online verkaufen](../wie-unternehmen-online-verkaufen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
