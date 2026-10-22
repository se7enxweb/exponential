---
title: "Sprechende URLs: URL-Aliase und URL-Platzhalter"
audience: web-framework-developer, developer
category: web-frameworks
order: 70
summary: Wie Exponential lesbare Adressen in Moduladressen umsetzt - automatische URL-Aliase für Inhaltsknoten, globale Aliase für Modul-Views, Platzhaltermuster für Weiterleitungen und Umschreibungen, Verlaufsweiterleitungen beim Verschieben von Inhalten - im Vergleich mit Slugs, Permalinks und Weiterleitungsrouten in anderen Frameworks.
keywords: URL-Alias, sprechende URLs, Slug, Permalink, URL-Platzhalter, Weiterleitung, Umschreiben, URL alias, nice URLs, URL wildcard, redirect, rewrite, eZURLAliasML, eZURLWildcard, URLTranslator, updateniceurls, globaler Alias, global alias, Web-Framework-Entwickler, web framework developer
type: help
---
# Sprechende URLs: URL-Aliase und URL-Platzhalter

Diese Seite richtet sich an Entwickler, die eine Spalte `slug` und eine Route mit einem Parameter `{slug}` anlegen
würden. In Exponential tun Sie das nicht: Jeder Inhaltsknoten hat einen **URL-Alias**, der aus den Namen seines Pfades
gebildet wird, und der Kernel übersetzt den Alias vor dem Routing in `content/view/full/<node>`
([Der Request-Lebenszyklus](der-request-lebenszyklus.md)). Zwei weitere Mechanismen decken ab, was Routentabellen
üblicherweise für Weiterleitungen leisten: **globale Aliase** und **URL-Platzhalter**.

## Inhalt

1. [Drei Arten von Adressen](#drei-arten-von-adressen)
2. [Knoten-Aliase: Slugs, die Sie nie schreiben](#knoten-aliase-slugs-die-sie-nie-schreiben)
3. [Globale Aliase für Modul-Views](#globale-aliase-für-modul-views)
4. [URL-Platzhalter](#url-platzhalter)
5. [Verwendung im Code](#verwendung-im-code)
6. [Im Vergleich mit anderen Frameworks](#im-vergleich-mit-anderen-frameworks)

## Drei Arten von Adressen

Aus [URL-Platzhalter und eigene Aliase](../ersteller/website-ersteller/url-platzhalter.md):

| Art | Wo angelegt | Beispiel |
|---|---|---|
| Knoten-Alias | automatisch sowie im Reiter „URL-Aliase“ des Knotens | `/About-us` |
| Globaler Alias | **Setup > URL-Übersetzer** (`/content/urltranslator`) | `login` für `user/login` |
| Platzhalter | **Setup > URL Platzhalter** (`/content/urlwildcards`) | `news/*` nach `articles/{1}` |

## Knoten-Aliase: Slugs, die Sie nie schreiben

- Ein Alias wird aus dem Namen des Objekts oder aus dem URL-Alias-Namensmuster der Klasse gebildet und durch
  `site.ini [URLTranslator] TransformationGroup` umgewandelt (ausgeliefert wird `urlalias`; `urlalias_iri` behält
  Buchstaben wie Akzente bei).
- Jede Übersetzung hat ihren eigenen Alias in ihrer Sprache.
- Wird ein Knoten umbenannt oder verschoben, entsteht der neue Alias, und der **alte bleibt als Weiterleitung
  erhalten**: Es gibt keine Weiterleitungstabelle zu pflegen.
- Ein Knoten kann im Reiter „URL-Aliase“ zusätzliche Aliase erhalten (Kampagnenadressen, die Adresse einer alten Site).
- Bauen Sie die Aliase nach einem Import oder einer Änderung der Transformationsgruppe mit
  `php bin/php/updateniceurls.php` neu auf.

Einzelheiten: [URL-Aliase und sprechende URLs](../ersteller/website-ersteller/url-aliasse-und-sprechende-urls.md). Die
zugrunde liegende Tabelle wird von `eZURLAliasML` (`kernel/classes/ezurlaliasml.php`) verwaltet.

## Globale Aliase für Modul-Views

Ihre Modul-Views liegen unter `/<module>/<view>`. Ein globaler Alias gibt einer View eine freundlichere Adresse
(`login` für `user/login`), entweder als **Weiterleitung** (301 auf das Ziel) oder **direkt** (der Besucher behält die
eingegebene Adresse). Legen Sie sie in der Administrationsoberfläche an oder liefern Sie sie als Teil Ihrer
Installationsschritte aus.

## URL-Platzhalter

Ein Platzhalter ist ein Muster mit `*` und ein Ziel mit den Platzhaltern `{1}`, `{2}`, wiederum als Weiterleitung oder
direkt:

| Muster | Ziel | Aus `news/2026/october` wird |
|---|---|---|
| `news/*` | `articles/{1}` | `articles/2026/october` |
| `old-blog/*` | `https://blog.example.org/{1}` | eine Weiterleitung auf die andere Site |

Platzhalter werden von `eZURLWildcard::translate()` (`kernel/classes/ezurlwildcard.php`) übersetzt, über
`site.ini [URLTranslator] WildcardTranslation` geschaltet (ausgeliefert: `enabled`), wobei
`MaximumWildcardIterations` (20) Schleifen verhindert. Ein URL-Alias, der auf eine Adresse passt, hat Vorrang vor einem
Platzhalter. **Eine Adresse ausprobieren** auf der Platzhalterseite zeigt, was aus einer Adresse wird, ohne etwas zu
ändern ([Links, URL-Aliase, Platzhalter](../../anleitungen/urls-und-aliase.md#4-url-platzhalter)).

## Verwendung im Code

| Bedarf | Verwenden |
|---|---|
| Auf einen Knoten verlinken | `{$node.url_alias\|ezurl}` in Templates, `eZURI::build( $node->attribute( 'url_alias' ) )` in PHP |
| Einen Knoten über seinen Alias finden | `eZContentObjectTreeNode::fetchByURLPath( 'about_us' )` ([Die Content-API](../ersteller/anwendungsentwickler/die-content-api.md#ein-einzelnes-element-abrufen)) |
| Nie eine Systemadresse ausgeben | Regeln können von Benutzern eingegebene Adressen `/content/view/full/<id>` weiterleiten oder ablehnen ([Request-Regeln](../../funktionen/6.0/anfrageregeln.md)) |

## Im Vergleich mit anderen Frameworks

| Gewohnheit aus Frameworks | In Exponential |
|---|---|
| Ein Feld `slug` und ein eindeutiger Index | der URL-Alias, erzeugt und pro Übersetzung gepflegt |
| Eine Weiterleitungstabelle oder ein Paket für verschobene Seiten | der Verlaufsalias, automatisch angelegt |
| `Route::redirect()`, `RedirectView`, `redirect()` in Routen | globaler Alias (Weiterleitung) oder Platzhalter |
| Eine Catch-all-Route, die einen Pfad nachschlägt | die Alias-Übersetzung erledigt das für Inhalte bereits |
| Lokalisierte Routen | ein Alias pro Übersetzung; Siteaccesses pro Sprache ([Siteaccesses](siteaccesses-hosts-und-mandanten.md)) |

## Verwandte Seiten

- [Routing: Module, Views und Parameter](routing-mit-modulen-und-views.md)
- [Weiterleitungen und verschobene Inhalte](../ersteller/website-ersteller/weiterleitungen-und-verschobene-inhalte.md)
- [Sichere Weiterleitungen](../../funktionen/6.0/sichere-weiterleitungen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
