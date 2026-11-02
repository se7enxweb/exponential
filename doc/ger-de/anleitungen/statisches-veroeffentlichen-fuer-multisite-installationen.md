# Statisches Veröffentlichen für Multisite-Installationen

Diese Anleitung ist für Administratoren einer Installation mit Exponential 6.0.15+, die mehrere Sites oder Sprachen aus
einer Datenbank ausliefert. Sie zeigt, wie jede ihr eigenes Ziel bekommt, wie sie getrennt bleiben und nach eigenem
Plan veröffentlicht werden.

[Anleitungen](README.md) · Verwandt: [Sprachen auf einer statischen Site](../hilfe/statisches-veroeffentlichen/sprachen-auf-einer-statischen-site.md),
[Statisches Veröffentlichen: ein Überblick](statisches-veroeffentlichen.md)

## Was Sie am Ende haben

Ein Ziel je öffentlichem Siteaccess, jedes mit eigenem Repository oder Branch, eigener öffentlicher Adresse und eigenen
Richtlinien, alle aus einer Installation aufgelistet und gebaut.

## Ein Ziel je Siteaccess

Ein Ziel veröffentlicht genau einen Front-Siteaccess. Seine Seiten, Sprachen, sein Design und sein Adressschema sind
die dieses Siteaccess. Eine Installation mit den Sites `site`, `shop_eng` und `blog` und einem deutschen `site_ger` hat
ein Ziel für jede, die eine öffentliche Kopie haben soll:

```ini
[StaticPublishSettings]
Enabled=enabled
TargetList[]
TargetList[]=site
TargetList[]=site-ger
TargetList[]=blog

[Target_site]
SiteAccess=site
Remote=https://github.com/example/site.git
PublicURL=https://www.example.com/
CustomDomain=www.example.com

[Target_site-ger]
SiteAccess=site_ger
Remote=https://github.com/example/site-de.git
PublicURL=https://de.example.com/
CustomDomain=de.example.com

[Target_blog]
SiteAccess=blog
Remote=https://github.com/example/blog.git
BasePath=/blog/
PublicURL=https://example.github.io/blog/
```

Lassen Sie die Sites aus, die nicht statisch sein können (hier den Shop); sie bleiben auf Ihrem Hosting, und Links aus
den Kopien dorthin führen zur Live-Site.

- Jedes Ziel hat seinen eigenen Ordner (`var/<site>/static-publish/<ziel>/`), sein eigenes Git-Repository, Tags
  `static/<ziel>/<n>`, Verlauf, Sperre und veraltete Seiten. Zwei Ziele teilen sich nie einen Ordner.
- Ein Repository je Ziel ist am einfachsten. Zwei Ziele können sich ein Repository auf verschiedenen `Branch`es teilen;
  ein GitHub-Repository liefert Pages nur aus einem Branch aus, das passt also zu anderen Hosts oder zu einem Branch,
  den Sie selbst zusammenführen.
- `exp:static:targets` listet und prüft sie alle; der Dashboard-Block zeigt jedes mit seinen veralteten Seiten.

## Sprachen

Eine Sprache, die ein eigener Siteaccess ist, bekommt ihr eigenes Ziel, wie oben. Die `hreflang`-Links jeder Seite werden
als absolute Adressen geschrieben: auf die Seite in dieser Kopie, wenn sie sie enthält, sonst auf die Live-Site. Damit
der Sprachumschalter von einer Kopie zur anderen führt, setzen Sie die `LiveURL` jedes Ziels, wo es passt, auf die
öffentliche Adresse der anderen Kopie, oder verlinken Sie die Kopien im Design. Der Suchindex einer Kopie wird je
Sprache geführt.

## Zugangsdaten und Berechtigungen

- Ein Geheimnis kann mehreren Zielen dienen (ein Token mit Zugriff auf alle ihre Repositorys), oder ein Deploy-Key je
  Ziel (`exp:static:keys <ziel> --generate`). Ein Deploy-Key ist von Haus aus auf ein Repository beschränkt.
- Die Richtlinien `staticpublish/*` nehmen eine Ziel-Beschränkung: Geben Sie den Redakteuren des Blogs `build` und
  `publish` nur für das Ziel `blog`.
- `exp:static:targets --export=site,blog` bringt Ziele als Migration in eine andere Installation; die Zugangsdaten
  reisen als ihr Name.

## Bauen

Builds verschiedener Ziele dürfen nebeneinander laufen; ein Ziel führt einen Job zur Zeit aus. Auf einem ausgelasteten
Server geben Sie jedem Ziel einen eigenen `Schedule` oder setzen auf `Triggers[]=publish`, das nur die Seiten baut, die
eine Veröffentlichung von Inhalten veraltet gemacht hat. Inhalte, die mehrere Sites zeigen, markieren beim
Veröffentlichen ihre Seiten in jedem Ziel mit `Triggers[]=publish` als veraltet, und der Cronjob-Teil `staticpublish`
baut jedes davon nach der Verzögerung.

## Nächster Schritt

[Statisches Veröffentlichen unter Windows und in Clustern](statisches-veroeffentlichen-unter-windows-und-in-clustern.md).
