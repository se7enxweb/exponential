# Die Vorschau im Siteaccess und auf dem Host, zu dem der Inhalt gehört (6.0.15)

Lesen Sie diese Seite, wenn eine Exponential-Installation mehrere Websites aus einem Inhaltsbaum ausliefert (eine
Site-Familie: eine Hauptsite und Microsites, jede mit eigenem Siteaccess und oft eigenem Host), oder wenn Sie Code
schreiben, der wissen muss, auf welcher Site ein Knoten veröffentlicht ist.

## Was sich geändert hat

Vor 6.0.15 wählte die Vorschau einer Version (Inhalt > Vorschau, `content/versionview`) ihren Siteaccess nur nach der
Sprache: den ersten Siteaccess aus `site.ini [SiteAccessSettings] RelatedSiteAccessList[]`, dessen
`[RegionalSettings] ContentObjectLocale` die Sprache der Version war, sonst `[SiteSettings] DefaultAccess`. Eine Seite
einer Microsite wurde daher im Design der Hauptsite gezeigt, mit deren Menü, Fußzeile und Pfadpräfix und mit Links auf
die Pfade der Hauptsite. Die Vorschau lief außerdem immer auf dem Host der Administration, sodass alles, was vom Host
abhängt (kanonische Adresse, hreflang, absolute Links, Cookies), von der veröffentlichten Seite abwich. Die Redaktion
konnte einen anderen Siteaccess wählen, musste aber wissen, welchen.

Seit 6.0.15:

1. **Der Siteaccess kommt aus dem Baum.** Die Vorschau wählt den Siteaccess vor, dessen Inhaltswurzel den Knoten
   enthält, in der Sprache der Version.
2. **Der Host kommt vom Siteaccess.** Wird dieser Siteaccess auf einem anderen Host ausgeliefert, zeigt sich die
   Vorschau auf diesem Host, mit einem signierten Einmal-Token, sodass sich die Redaktion dort nicht anmelden muss.
3. **"View on site"** eines Knotens (Knotenansicht in admin4) öffnet die veröffentlichte Seite auf der Site und dem
   Host, zu denen sie gehört.

Die Siteaccess-Auswahl der Vorschau funktioniert weiter: Sie zeigt den gewählten Siteaccess ausgewählt, und ein
Siteaccess, den die Redaktion dort wählt, bleibt erhalten.

## Zu welchem Siteaccess ein Knoten gehört

`expSiteAccessForNode` (`kernel/classes/expsiteaccessfornode.php`):

- **Kandidaten:** die Siteaccesses aus `RelatedSiteAccessList[]` des Siteaccess, in dem die Vorschau geöffnet wird (der
  Administration), `AvailableSiteAccessList[]`, wenn diese Liste leer ist. Administrations- und Redaktions-Siteaccesses
  bleiben außen vor (`expSiteAccessOverview::kindOf()`).
- **Inhaltswurzel eines Siteaccess:** die tiefste von `content.ini [NodeSettings] RootNode`, dem Knoten von
  `site.ini [SiteSettings] IndexPage` (`/content/view/full/<Knoten>` oder ein URL-Alias) und dem Knoten von
  `[SiteAccessSettings] PathPrefix`. Ein Siteaccess enthält einen Knoten, wenn seine Wurzel der Knoten oder ein
  Vorfahre davon ist.
- **Die spezifischste gewinnt:** Von den Siteaccesses, die den Knoten enthalten, der mit der tiefsten Wurzel. Eine
  Microsite unter Knoten 60 gewinnt gegen die Hauptsite mit Wurzel 2, gleich in welcher Reihenfolge die Liste steht.
- **Sprache:** Bei gleich tiefen Wurzeln kommt zuerst der Siteaccess, dessen `ContentObjectLocale` die Sprache der
  Version ist, dann einer, dessen `SiteLanguageList[]` sie hat (früher in seiner Liste zuerst), dann die übrigen; zuletzt
  entscheidet die Reihenfolge der Liste.
- **Kein Siteaccess enthält den Knoten** (etwa ein Knoten unter Medien oder Benutzer): Es gilt die Regel vor 6.0.15,
  der erste verwandte Siteaccess in der Sprache, sonst `DefaultAccess`.

## Auf welchem Host die Vorschau gezeigt wird

Die Adresse des gewählten Siteaccess ist die, die auch der Link "Open the site" der Administration verwendet
(`ezpSiteAccessURL::root()`).

- **Per URI erkannt** auf dem Host der Administration: Die Vorschau bleibt, wie sie war
  (`content/versionview/<Objekt>/<Version>/<Sprache>/site_access/<Siteaccess>`).
- **Auf einem anderen Host ausgeliefert:** `content/versionview` mit diesem Siteaccess leitet den Vorschau-Frame auf
  `https://<Host>/content/hostpreview/<Token>` weiter. Das gilt in jedem Administrationsdesign (admin, admin3, admin4,
  adminui) ohne Änderung an deren Templates.

Die Vorschau bleibt auf dem Host der Administration, wenn eine Weiterleitung nicht auf den Zielhost gehen darf
(`eZRedirectManager::isOwnHost()`), wenn der Siteaccess eine Anmeldung verlangt (`RequireUserLogin=true`), wenn der
Geheimnisspeicher nicht eingerichtet ist oder bei `PreviewOnOwningHost=disabled`.

### Das Einmal-Token

`expContentHostPreview` (`kernel/classes/expcontenthostpreview.php`), Ansicht `content/hostpreview/<Token>`:

- **Signiert** mit HMAC-SHA256 unter dem Geheimnis `content.hostpreview.key` des
  [Geheimnisspeichers](geheimnisspeicher.md); gebunden an Benutzer, Objekt, Version, Sprache, Siteaccess, Ansichtsmodus,
  Platzierung und die Herkunft der Administration.
- **Kurzlebig:** `PreviewTokenLifetime` Sekunden (Standard 300, zwischen 60 und 900).
- **Einmalig:** Ein zweiter Aufruf mit demselben Token (Neu laden, kopierte Adresse) antwortet mit 410; die Vorschau in
  der Administration erneut zu öffnen, stellt ein neues Token aus.
- **Nicht mehr:** `content/hostpreview` braucht keine Richtlinie (`PolicyOmitList[]`); das Token erlaubt, genau diese
  Version einmal zu sehen. Niemand wird angemeldet, keine Sitzung wird gestartet. Nie im Cache, nie indexiert, kein
  Referrer.
- **Frames:** `content/hostpreview` entfernt `X-Frame-Options` und ergänzt `frame-ancestors` um die Herkunft der
  Administration. Header, die der Webserver selbst setzt, müssen es ebenfalls erlauben.

## "View on site"

Der Template-Operator `exp_node_site_url( $node[, $language] )` liefert die Adresse, unter der ein Knoten auf seiner
eigenen Site veröffentlicht ist. Ein versteckter Knoten auf einem Siteaccess, der versteckte Knoten nicht zeigt, führt
zur Vorschau in der Administration.

## Einstellungen

```ini
[SiteAccessSettings]
PreviewInOwningSiteAccess=enabled
PreviewOnOwningHost=enabled
PreviewTokenLifetime=300
```

Ein Update braucht nichts: Beide Schalter sind standardmäßig an, und eine Installation mit einer Site (oder nur per URI
erkannten Siteaccesses) sieht dieselbe Vorschau wie bisher.

## Siehe auch

- [Vorschaulinks](vorschaulinks.md)
- [Der Geheimnisspeicher](geheimnisspeicher.md)
- [Sichere Weiterleitungen](sichere-weiterleitungen.md)
- [Websiteübergreifende Links im Multisite](websiteuebergreifende-links-im-multisite.md)
- [site.ini \[SiteAccessSettings\]](../../beispiele/ini-einstellungen/site/SiteAccessSettings.md)
