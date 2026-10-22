---
title: URL-Aliase und Routing
audience: cms-developer
category: cms-development
order: 22
summary: Für CMS-Entwickler, wie eine Adresse zu einer Seite gelangt. Die Seite behandelt die System-URL aus Modul und View, URL-Aliase aus dem Baum, die Einstellungen zur Umwandlung, Verlaufs-Aliase nach Verschiebungen, globale Aliase und Platzhalter für Weiterleitungen, View-Parameter, das Bauen von Links in Templates und PHP sowie den Neuaufbau von Aliasen.
keywords: URL-Alias, Routing, sprechende URLs, System-URL, content/view/full, Modul, View, URLTranslator, TransformationGroup, WordSeparator, Verlaufs-Alias, Weiterleitung, globaler URL-Alias, URL-Platzhalter, View-Parameter, ezurl, eZURI, updateniceurls, verify_aliases, Slug, URL alias, nice URLs, system URL, history alias, redirect, global URL alias, URL wildcard, view parameters
type: help
---
# URL-Aliase und Routing

Diese Seite richtet sich an Entwickler, die wissen müssen, wie Exponential eine Adresse in eine Seite verwandelt und wie
Adressen stabil bleiben. Für Inhalte sind keine Routendateien zu schreiben: Jeder Knoten erhält seine Adresse aus dem
Baum, und jede andere Seite ist eine Modul-View mit festem Muster.

## Inhalt

1. [Zwei Arten von Adressen](#zwei-arten-von-adressen)
2. [Wie eine Anfrage aufgelöst wird](#wie-eine-anfrage-aufgelöst-wird)
3. [Wie Aliase entstehen](#wie-aliase-entstehen)
4. [Wenn Inhalte verschoben oder umbenannt werden](#wenn-inhalte-verschoben-oder-umbenannt-werden)
5. [Globale Aliase und Platzhalter](#globale-aliase-und-platzhalter)
6. [View-Parameter](#view-parameter)
7. [Links bauen](#links-bauen)
8. [Aliase neu aufbauen und prüfen](#aliase-neu-aufbauen-und-prüfen)
9. [Eigene Seiten](#eigene-seiten)

## Zwei Arten von Adressen

| Art | Beispiel | Stammt aus |
|---|---|---|
| System-URL | `/content/view/full/61`, `/user/login`, `/content/search` | `<module>/<view>/<parameters>`, festgelegt in der `module.php` jedes Moduls |
| URL-Alias | `/news/opening-of-the-new-hall` | dem Pfad des Knotens im Baum, ein Alias je Sprache |

Beide funktionieren für jeden Knoten; Aliase sind das, was Besucher und Suchmaschinen sehen. Der Siteaccess-Teil einer
Adresse (`/ger/...` bei Zuordnung über URI) wird abgetrennt, bevor eine der beiden nachgeschlagen wird
([Siteaccesses und Multisite](siteaccesses-und-multisite.md)).

## Wie eine Anfrage aufgelöst wird

1. Der Siteaccess wird anhand von Host, Port oder erstem Pfadelement gewählt.
2. Ist `site.ini [URLTranslator] Translation=enabled` (mitgeliefert), wird der Rest des Pfads in der Tabelle der
   URL-Aliase nachgeschlagen (`ezurlalias_ml`, `kernel/classes/ezurlaliasml.php`); ein Treffer liefert die System-URL,
   für einen Knoten `content/view/full/<node id>`.
3. Passt kein Alias und ist `WildcardTranslation=enabled`, werden die URL-Platzhalter versucht.
4. Modul und View laufen mit den Parametern; `content/view` prüft `content/read` und rendert den Knoten mit seinen
   Templates.

## Wie Aliase entstehen

Der Alias eines Knotens ist der Alias seines übergeordneten Knotens plus ein Segment aus dem Namen des Objekts (oder
dem Muster für den URL-Alias der Klasse). Das Segment wird durch die Einstellungen in `[URLTranslator]` umgewandelt:

| Einstellung | Mitgeliefert | Wirkung |
|---|---|---|
| `TransformationGroup` | `urlalias` | die Regeln zur Zeichenumwandlung für Segmente |
| `WordSeparator` | `dash` | Leerzeichen werden zu `-` |
| `UrlAliasNameLimit` | `255` | das längste Segment |

Zwei Geschwister mit gleichem Namen erhalten unterschiedliche Segmente (eine Zahl wird angehängt). Jede Übersetzung
eines Objekts hat ihren eigenen Alias in ihrer Sprache; die deutsche Seite eines Knotens kann also eine deutsche
Adresse haben.

## Wenn Inhalte verschoben oder umbenannt werden

Das Umbenennen eines Objekts oder das Verschieben eines Knotens erzeugt neue Aliase und behält die alten als
**Verlaufs**-Einträge, die weiterhin zum Knoten führen. Links, die Redakteure in Rich Text auf Inhalte gesetzt haben,
zeigen auf Knoten- oder Objekt-IDs, nicht auf Adressen, und folgen Verschiebungen daher von selbst
([Rich Text](rich-text.md)). Alte Adressen aus einem anderen System sind kein Verlauf: Fügen Sie sie als eigene Aliase
oder Platzhalter hinzu ([Checkliste für die Migration](../../installation/migration/checkliste.md)).

## Globale Aliase und Platzhalter

In der Administration (**Setup** > **URL-Übersetzer** und **Setup** > **URL Platzhalter**) verwalten Administratoren:

| Werkzeug | Seite | Verwendung |
|---|---|---|
| Globaler URL-Alias | `/content/urltranslator` | eine feste Adresse zu einer System-URL oder einem Knoten, als 301-Weiterleitung oder unter dem Alias angezeigt |
| URL-Platzhalter | `/content/urlwildcards` | ein Muster wie `old-blog/*` zu einem Ziel mit `{1}`, weiterleitend oder direkt |
| Eigener Alias an einem Knoten | die Registerkarte der URL-Aliase des Knotens | eine zusätzliche Adresse für einen Knoten in einer Sprache |

Jede Seite und jede Schaltfläche beschreibt [Links, URL-Aliase und Platzhalter](../../anleitungen/urls-und-aliase.md).

## View-Parameter

Jede Adresse kann Paare `/(name)/value` tragen: `/news/(offset)/10` blättert eine Liste, und Ihre Templates lesen
`$view_parameters.offset`. View-Parameter gehören zum Schlüssel des View-Cache, jeder Wert wird also getrennt
zwischengespeichert ([View-Parameter](../template-sprache/ansichtsparameter.md)). Verwenden Sie sie zum Blättern und für
einfache Filter, und beschränken Sie die Werte auf eine bekannte Menge, damit Robots den Cache nicht mit endlosen
Varianten füllen können.

## Links bauen

Schreiben Sie Adressen in Templates nie von Hand; lassen Sie `ezurl` den Siteaccess und die Indexdatei ergänzen:

```smarty
<a href={$node.url_alias|ezurl}>{$node.name|wash()}</a>
<a href={'/content/search'|ezurl}>Search</a>
<link rel="canonical" href={$node.object.main_node.url_alias|ezurl( 'double', 'full' )}>
```

`ezurl` setzt die Anführungszeichen selbst, schreiben Sie also keine darum ([URL-Operatoren](../designer/vorlagen/url-operatoren.md)).
In PHP liefert `eZURI::build( '/content/view/full/2' )` denselben Pfad und `eZURI::build( $url, true )` die absolute URL
([Links, URL-Aliase und Platzhalter: für Entwickler](../../anleitungen/urls-und-aliase.md#für-entwickler)). Links zwischen
mehreren Websites in einem Baum: [Siteaccesses und Multisite](siteaccesses-und-multisite.md#links-zwischen-websites).

## Aliase neu aufbauen und prüfen

Nach einem großen Import oder einer Änderung der Umwandlungseinstellungen:

```bash
./console exp:updateniceurls --help --allow-root-user
./console exp:verify_aliases --help --allow-root-user
```

`updateniceurls` baut die Aliase aller Knoten neu auf; `verify_aliases` prüft die Alias-Tabelle und repariert mit
`--fix`, was sicher ist ([Inhaltssprachen, Abschnitt 10](../../anleitungen/inhaltssprachen.md#10-befehle-für-die-shell)).
Führen Sie beides auf einer großen Website zuerst an einer Kopie aus.

## Eigene Seiten

Eine Seite, die kein Inhalt ist (ein Dashboard, ein Formular-Handler, ein JSON-Endpunkt), ist eine View eines Moduls in
Ihrer Erweiterung: `/exp_mysite/report/(year)/2026` ist das Modul `exp_mysite`, die View `report`. Module deklarieren
ihre Views, Parameter und Richtlinienfunktionen in `module.php`
([Module und Views](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/module-und-views.md)). Ein globaler URL-Alias
kann einer solchen Seite eine freundlichere Adresse geben.

## Verwandte Seiten

- [Objekte, Knoten und Platzierungen](objekte-knoten-und-platzierungen.md)
- [SEO-Grundlagen](seo-grundlagen.md)
- [Sichere Weiterleitungen](../../funktionen/6.0/sichere-weiterleitungen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
