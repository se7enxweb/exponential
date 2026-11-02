# Statisches Veröffentlichen für Multisite-Installationen

Diese Anleitung ist für Administratoren einer Installation mit Exponential 6.0.15+, die mehrere Sites oder Sprachen aus
einer Datenbank ausliefert. Sie zeigt, wie jede ihr eigenes Ziel bekommt, wie sie getrennt bleiben und nach eigenem
Plan veröffentlicht werden.

[Anleitungen](README.md) · Verwandt: [Sprachen auf einer statischen Site](../hilfe/statisches-veroeffentlichen/sprachen-auf-einer-statischen-site.md),
[Statisches Veröffentlichen: ein Überblick](statisches-veroeffentlichen.md)

## Was Sie am Ende haben

Entweder ein Ziel je öffentlichem Siteaccess, jedes mit eigenem Repository oder Branch, eigener öffentlicher Adresse
und eigenen Richtlinien, oder ein Ziel, das mehrere Siteaccesses, die sich über ihr URI-Präfix einen Host teilen (`/`,
`/bold/`, `/bold_ger/`), in eine Kopie veröffentlicht, alle aus einer Installation aufgelistet und gebaut.

## Mehrere Siteaccesses in einer Kopie

Leben mehrere Sites oder Sprachen auf einem Host und werden über den ersten Teil der Adresse unterschieden (site.ini
`[SiteAccessSettings] MatchOrder` mit `uri`, `URIMatchType=element` oder `map`, oder `host_uri` mit einer URI), kann ein
Ziel sie gemeinsam veröffentlichen; die Kopie behält dieselben Adressen wie der Live-Host:

```ini
[Target_sites]
Name=Alle Sites auf www.example.com
SiteAccessList[]
SiteAccessList[]=site
SiteAccessList[]=bold
SiteAccessList[]=bold_ger
DefaultSiteAccess=site
Remote=https://github.com/example/sites.git
PublicURL=https://www.example.com/
CustomDomain=www.example.com
```

- Der `DefaultSiteAccess` wird im Stamm der Kopie ausgeliefert (`/`, `/about-us/`); jeder andere Siteaccess liegt im
  Ordner seines URI-Präfixes (`/bold/`, `/bold_ger/team/`), genau dort, wo der Live-Host ihn ausliefert.
- Ohne `DefaultSiteAccess` liegt jeder Siteaccess in seinem Ordner (auch `/site/`), und der Stamm der Kopie ist eine
  kleine Seite, die auf jeden verlinkt. Ein Ziel mit einem Siteaccess hat ihn immer im Stamm.
- `SiteAccess=` gilt weiter als Form für einen; nennt `SiteAccessList[]` welche, wird `SiteAccess` nicht gelesen.
- Die Prüfungen: nur Front-Siteaccesses (nie ein Administrations- oder Redaktions-Siteaccess); jeder Siteaccess außer
  dem Standard muss über ein URI-Präfix gewählt werden, sonst hat er in einer mit anderen geteilten Kopie keinen Ordner
  (ein nur über den Host gewählter Siteaccess kann der `DefaultSiteAccess` sein oder ein eigenes Ziel bekommen); zwei
  Siteaccesses können sich keinen Ordner teilen.
- Links zwischen den Siteaccesses der Kopie bleiben in der Kopie (relativ oder unter dem Basispfad): der
  Sprachumschalter von `/about-us/` nach `/bold_ger/` funktioniert auch offline. Ein Link zu einem Siteaccess, den das
  Ziel nicht veröffentlicht (`/classic/...`), führt aus der Kopie hinaus, und `OutsideLinks` entscheidet: zur
  Live-Site oder herausgenommen.
- Der Sprachumschalter (`/bold/switchlanguage/to/bold_ger/careers`) führt zur selben Seite im anderen Siteaccess der
  Kopie (`/bold_ger/karriere/`), oder zu dessen Startseite, wenn die Kopie dort keine Seite dieses Knotens enthält.
- `hreflang`-Alternativen zwischen ihnen werden als absolute Adressen unter `PublicURL` geschrieben.
- Eine `sitemap.xml` listet die Seiten aller, eine `robots.txt` verweist darauf, und `404.html` ist die 404-Seite des
  Standard-Siteaccess (ohne Standard die des ersten). Umleitungsseiten werden je Siteaccess in seinem Ordner
  geschrieben.
- Der Suchindex bleibt je Sprache; jeder Eintrag nennt seinen Siteaccess, und die Suchseite bietet "Alle Sites" oder
  einen davon an (`search.html?site=bold_ger`).
- `MaxPages` zählt je Siteaccess. Das Manifest nennt die Siteaccesses mit ihren Ordnern und bei jeder Seite ihren
  Siteaccess; veraltete Seiten, Verlauf und das Panel "Diese Seite veröffentlichen" arbeiten mit den Seitenpfaden
  jedes Siteaccess.
- Eine Inhaltsveröffentlichung markiert die Seiten des Knotens in jedem Siteaccess der Kopie als veraltet; `--nodes`
  baut die Seite eines Knotens in jedem von ihnen.
- Nur einen davon bauen: `./console exp:static:build sites --only-siteaccess=bold_ger`, ein Teil-Build; die Seiten
  der anderen bleiben, wie der letzte Build sie hinterließ. (`--siteaccess` bleibt die Skriptoption, die bestimmt,
  wessen Einstellungen gelesen werden.) `--paths` nimmt Pfade der Kopie (`/bold_ger/team`).

Im Assistenten listet Schritt 1 die Front-Siteaccesses mit ihrem Ordner: haken Sie die zu veröffentlichenden an und
wählen Sie den im Stamm (oder keinen). Ein einzelner angehakter Siteaccess wird wie bisher als `SiteAccess=`
gespeichert.

## Ein Ziel je Siteaccess

Ein Ziel kann auch genau einen Front-Siteaccess veröffentlichen. Seine Seiten, Sprachen, sein Design und sein Adressschema sind
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
