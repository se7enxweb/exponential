---
title: site.ini [SiteAccessSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SiteAccessSettings] in site.ini: ForceVirtualHost, CheckValidity, RequireUserLogin, AvailableSiteAccessList, RelatedSiteAccessList, AnonymousAccessList, MatchOrder, URIMatchType ..."
keywords: Einstellung, site.ini, SiteAccessSettings, INI, setting, ForceVirtualHost, CheckValidity, RequireUserLogin, AvailableSiteAccessList, RelatedSiteAccessList, AnonymousAccessList, MatchOrder, URIMatchType, URIMatchElement, URIMatchRegexp, URIMatchRegexpItem, URIMatchMapItems, HostMatchType, HostMatchElement, HostMatchRegexp, HostMatchRegexpItem, HostMatchSubtextPre, HostMatchSubtextPost, HostMatchMethod, HostUriMatchMethodDefault, HostUriMatchMapItems, DefaultHostUriRedirect, DefaultHostUriMatchMapItems, ServerVariableName, RemoveSiteAccessIfDefaultAccess, PathPrefix, PathPrefixExclude, ShowHiddenNodes, DetectMobileDevice, MobileSiteAccessURL
type: generated
generated: exp:help:examples
---
# site.ini [SiteAccessSettings]

Der Block `[SiteAccessSettings]` von `settings/site.ini`, mit 37 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ForceVirtualHost

> Auf true setzen, wenn Sie den Modus VirtualHost erzwingen wollen (mit RewriteRules in Apache)  
> Wenn true, funktioniert der Modus ohne virtuellen Host nicht korrekt  

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
ForceVirtualHost=false
*/ ?>
```

## CheckValidity

> Auf true setzen, wenn das Setup-System aktiviert werden soll  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
CheckValidity=true
*/ ?>
```

## RequireUserLogin

> Auf true setzen, wenn Sie keinen anonymen Zugriff auf Ihre Website wollen  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
RequireUserLogin=true
*/ ?>
```

## AvailableSiteAccessList

> Eine Liste der aktuell aktiven Siteaccesses  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
# ein leeres "AvailableSiteAccessList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AvailableSiteAccessList[]
*/ ?>
```

## RelatedSiteAccessList

> Eine Liste verwandter Siteaccesses, die dieselbe Datenbank teilen  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
# ein leeres "RelatedSiteAccessList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RelatedSiteAccessList[]
*/ ?>
```

## AnonymousAccessList

> Eine Liste von Modulen oder Modulen/Ansichten, die keine Anmeldung erfordern  
> Das Mega-Menü von admin4 (user/megamenu/<tab>) beantwortet sein Skript selbst: Ein nicht angemeldeter Besucher  
> erhält ein bloßes 403 (X-Exp-Mega: signed-out) statt der Anmeldeseite mit 200. Die Ansicht prüft die  
> Anmeldung, den Siteaccess und den Reiter selbst (siehe PolicyOmitList\[\] unten).  
> Browser senden die Berichte der Content Security Policy ohne Anmeldung; csp/report nimmt sie in jedem  
> Siteaccess an, die Administration eingeschlossen (doc/eng-us/features/6.0/content-security-policy.md, "Reports").  

Standard: eine Liste mit 6: `user/register`, `user/success`, `user/activate`, `user/forgotpassword`, `user/megamenu`, `csp/report`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
# ein leeres "AnonymousAccessList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AnonymousAccessList[]
AnonymousAccessList[]=user/register
AnonymousAccessList[]=user/success
AnonymousAccessList[]=user/activate
AnonymousAccessList[]=user/forgotpassword
AnonymousAccessList[]=user/megamenu
AnonymousAccessList[]=csp/report
*/ ?>
```

## MatchOrder

> Ein Array der Zugriffsarten, die versucht werden; mögliche Einträge sind: host, host\_uri, uri, port und servervar  

Standard: `uri;host;port`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
MatchOrder=uri;host;port
*/ ?>
```

## URIMatchType

> MatchOrder : uri  
> Mögliche Werte: disabled, map, element, text oder regexp  
> disabled - schaltet den URI-Abgleich ab  
> map - URL über das Array URIMatchMapItems abgleichen  
> element  - ordnet ein URL-Element einem Siteaccess-Namen zu  
> text - URL über Teilzeichenketten davor oder danach abgleichen (URIMatchSubtextPre, URIMatchSubtextPost)  
> regexp   - gegen einen regulären Ausdruck abgleichen und einen Teil davon herausziehen  

Standard: `element`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
URIMatchType=element
*/ ?>
```

## URIMatchElement

Standard: `1`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
URIMatchElement=1
*/ ?>
```

## URIMatchRegexp

Standard: `([^/]+)/`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
URIMatchRegexp=([^/]+)/
*/ ?>
```

## URIMatchRegexpItem

Standard: `1`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
URIMatchRegexpItem=1
*/ ?>
```

## URIMatchMapItems

> URIMatchSubtextPre=  
> URIMatchSubtextPost=  
> Fügen Sie hier Array-Einträge hinzu, wenn Sie URIMatchType=map gewählt haben  
> Jeder Eintrag besteht aus uri;accessname  

Standard: eine Liste mit 2: `admin;admin`, `ADMIN;admin`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
# ein leeres "URIMatchMapItems[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
URIMatchMapItems[]
URIMatchMapItems[]=admin;admin
URIMatchMapItems[]=ADMIN;admin
*/ ?>
```

## HostMatchType

> MatchOrder : host  
> Verwenden Sie disabled, map, element, text oder regexp  

Standard: `map`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
HostMatchType=map
*/ ?>
```

## HostMatchElement

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
HostMatchElement=0
*/ ?>
```

## HostMatchRegexp

Standard: `^(.+)\.example\.com$`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
HostMatchRegexp=^(.+)\.example\.com$
*/ ?>
```

## HostMatchRegexpItem

Standard: `1`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
HostMatchRegexpItem=1
*/ ?>
```

## HostMatchSubtextPre

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
HostMatchSubtextPre=
*/ ?>
```

## HostMatchSubtextPost

Standard: `.example.com`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
HostMatchSubtextPost=.example.com
*/ ?>
```

## HostMatchMethod

> Fügen Sie hier Array-Einträge hinzu, wenn Sie HostMatchType=map gewählt haben  
> Jeder Eintrag besteht aus hostname;accessname\[;start|end|part|strict\]  
> Der letzte, optionale Teil ist die Methode des Host-Abgleichs des Eintrags, wie bei  
> host\_uri: strict (der Host wie aufgeführt), start (der Host beginnt damit, ein  
> Test-Host example.com.test.local passt also auf example.com), end oder part.  
> Ohne ihn gilt HostMatchMethod unten. Wie bei host\_uri kennt der statische Cache  
> nur den strikten Abgleich. Jede Methode vergleicht Hostnamen ohne Beachtung der  
> Groß-/Kleinschreibung und eines abschließenden Punkts, ignoriert den Port der Anfrage, außer der Eintrag  
> nennt einen (example.com:8080), und nimmt einen in Unicode geschriebenen Namen  
> (münchen.example) für die ASCII-Form, die Browser senden (xn--mnchen-3ya.example).  
> start und part nehmen auch Hosts an, die Sie nicht gemeint haben (example.com.anything): Verwenden Sie  
> sie nur, wo der Webserver allein für Ihre eigenen Hosts antwortet.  
> HostMatchMapItems\[\]=example.com;user  
> HostMatchMapItems\[\]=admin.example.com;admin  
> HostMatchMapItems\[\]=example.com;user;start  

Standard: `strict`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
HostMatchMethod=strict
*/ ?>
```

## HostUriMatchMethodDefault

> MatchOrder : host\_uri  
> Fügen Sie hier Array-Einträge hinzu, wenn Sie MatchOrder host\_uri verwenden  
> Jeder Eintrag besteht aus hostname;uri;accessname\[;start|end|part|strict\]  
> Der letzte, optionale Parameter steuert die Methode des Host-Abgleichs, standardmäßig 'strict'  
>
> Hinweis: Der statische Cache unterstützt nur den strikten Abgleich, weil die Rewrite-  
>       Regeln eine strikte Konvention zwischen Host/URI und Treffer brauchen (Grundlage für den Ordnernamen).  
>       Der URI-Teil ist ebenfalls optional, so lässt sich damit auch reiner Host-Abgleich machen.  
>       Leere Hostnamen-Teile sind nicht möglich.  
>
> Reihenfolge: Exponential durchsucht HostUriMatchMapItems\[\] von oben nach unten; setzen Sie also  
>           spezifischere Einträge nach oben und Einträge mit leerem URI-Teil nach unten.  
> Ändern Sie HostUriMatchMethodDefault auf eine dieser Optionen, um den Standardwert zu ändern  

Standard: `strict`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
HostUriMatchMethodDefault=strict
*/ ?>
```

## HostUriMatchMapItems

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
# ein leeres "HostUriMatchMapItems[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
HostUriMatchMapItems[]
*/ ?>
```

## DefaultHostUriRedirect

> # This example will match on example.com/nor  
> HostUriMatchMapItems\[\]=example.com;nor;example\_nor  
> # This example will match on example.com/eng & www.example.com/eng  
> HostUriMatchMapItems\[\]=example.com;eng;example\_eng;end  
> # This example will match on example.com/eng & example.com.localhost/eng  
> HostUriMatchMapItems\[\]=example.com;eng;example\_eng;start  
> # This example will match on example.com/eng, www.example.com/eng & example.com.localhost/eng  
> HostUriMatchMapItems\[\]=example.com;eng;example\_eng;part  
> # This example will match on example.com  
> HostUriMatchMapItems\[\]=example.com;;examle  
> Der Siteaccess einer Adresse, auf die keine Prüfung aus MatchOrder passt, wo  
> DefaultAccess gelten würde: typischerweise die Adresse ohne Siteaccess- oder Sprach-  
> segment (example.com/), mit jedem MatchOrder (uri, host\_uri, ...). Gewählt nach Host  
> und nach den Sprachen, die der Browser akzeptiert (Accept-Language, die gewünschteste zuerst).  
> Jeder Eintrag ist hostname;uri;accessname\[;method\[;language\]\]: method wie bei  
> HostUriMatchMapItems, leer oder "default" für HostUriMatchMethodDefault; ein leerer  
> Hostname mit part passt auf jeden Host; eine Sprache wie "de" passt auch auf regionale  
> Formen (de-CH). Der erste Eintrag für den Host, dessen Sprache der Browser akzeptiert,  
> gewinnt, sonst der erste Eintrag für den Host ohne Sprache.  
> Eine GET- oder HEAD-Anfrage wird einmal (302) an dieselbe Adresse mit dem URI-  
> Teil weitergeleitet, die Adresse bleibt, wie sie angefragt wurde: example.com/ geht nach example.com/ger,  
> example.com/news/an-article nach example.com/ger/news/an-article, mit der Query.  
> Das braucht eine Prüfung, die die Adresse mit dem URI-Teil für diesen Siteaccess annimmt  
> (sonst käme sie hierher zurück); ohne eine, bei anderen Anfragen oder mit  
> DefaultHostUriRedirect=disabled wird die Seite an der Adresse selbst angezeigt, ihre  
> Links tragen den URI-Teil. Eine Adresse, auf die eine Prüfung passt (/ger/..., /admin/...),  
> wird nie weitergeleitet. Die Location ist ein Pfad auf dem angefragten Host  
> (/ger/news?x=1), gebildet aus der Index-Datei und dem URI-Teil des Eintrags, sodass  
> RemoveSiteAccessIfDefaultAccess ihn nicht zu einer Schleife kürzt.  
> Wenn die Sprache des Browsers die Antwort für den Host ändern kann (ein Eintrag nennt  
> eine Sprache, und die Einträge führen zu mehr als einem Siteaccess oder URI-Teil, oder kein  
> Eintrag gilt ohne Sprache), variieren die Weiterleitung, eine so angezeigte Seite und der  
> Standard-Siteaccess, der für andere Browser antwortet, nach Accept-Language: Sie werden  
> mit Vary: Accept-Language und Cache-Control: private gesendet, und weder der HTTP-  
> Cache noch sein früher Ausstieg behält oder liefert sie. Ein Eintrag, der einen Siteaccess nennt, der  
> nicht in AvailableSiteAccessList steht, wird ignoriert (und protokolliert). Seiten, die der  
> statische Cache oder der HTTP-Cache behalten hat, bevor ein Eintrag hinzugefügt wurde, werden ohne die  
> Weiterleitung ausgeliefert, bis diese Caches geleert sind.  
> Eine über die URL erkannte Website (MatchOrder=uri) mit dem Sprach-Siteaccess ger:  
>   DefaultHostUriMatchMapItems\[\]=;ger;ger;part  
> Eine über Host und URI erkannte Website, Deutsch und Englisch nach Browser:  
>   HostUriMatchMapItems\[\]=example.com;de;site\_de  
>   HostUriMatchMapItems\[\]=example.com;en;site\_en  
>   DefaultHostUriMatchMapItems\[\]=example.com;de;site\_de;;de  
>   DefaultHostUriMatchMapItems\[\]=example.com;en;site\_en  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
DefaultHostUriRedirect=enabled
*/ ?>
```

## DefaultHostUriMatchMapItems

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
# ein leeres "DefaultHostUriMatchMapItems[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
DefaultHostUriMatchMapItems[]
*/ ?>
```

## ServerVariableName

> DefaultHostUriMatchMapItems\[\]=example.com;ger;example\_ger;default;de  
> DefaultHostUriMatchMapItems\[\]=example.com;eng;example\_eng;default;en  
> DefaultHostUriMatchMapItems\[\]=example.com;eng;example\_eng  
> MatchOrder : servervar  
> Den Namen der Servervariable setzen, über die ein Siteaccess erkannt wird  

Standard: `SITEACCESS`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
ServerVariableName=SITEACCESS
*/ ?>
```

## RemoveSiteAccessIfDefaultAccess

> Ob der Siteaccess zur URL hinzugefügt wird, wenn der aktuelle Siteaccess  
> derselbe wie der Standard-Siteaccess ist. Eine Änderung dieser Einstellung  
> erfordert das Leeren des Caches.  
>
> Diese Einstellung funktioniert auch mit dem Abgleich host\_uri und entfernt den URI-Teil  
> aus der URL, wenn es der Standard-Siteaccess ist. Sorgen Sie dann dafür, dass  
> der Hostname ohne URL-Teil in Ihrer Kette aus MatchOrder zum selben Siteaccess  
> führt.  
>
> Ist zum Beispiel der Standard-Siteaccess 'en' und Sie aktivieren  
> dies, erreichen Sie den Siteaccess mit 'index.php' statt mit  
> 'index.php/en/'.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
RemoveSiteAccessIfDefaultAccess=disabled
*/ ?>
```

## PathPrefix

> Ein anderer Siteaccess, dessen Siteaccess-Einstellungen der Erweiterungen dieser ebenfalls verwendet  
> (extension/<ext>/settings/siteaccess/<that siteaccess> und das settings/override  
> der Erweiterung, das er mitbringt), unter denen, die eine Erweiterung für diesen  
> Siteaccess selbst mitliefert. Ein Übersetzungs-Siteaccess, der aus dem Benutzer-Siteaccess erstellt wurde, setzt  
> ihn auf diesen Siteaccess, damit er mit demselben Design und denselben Erweiterungs-  
> einstellungen in seiner eigenen Sprache rendert. Nur aus der eigenen  
> settings/siteaccess/<name>/site.ini.append.php dieses Siteaccess gelesen, weil die Erweiterungs-  
> einstellungen eingeordnet werden, bevor die Siteaccess-Einstellungen zusammengeführt sind.  
> ExtensionSettingsSiteAccess=site  
> Blendet diesen Teil am Anfang des URL-Alias aus  
> Wenn Sie diese Einstellung verwenden, müssen Sie auch die Einstellung PathPrefix in logfile.ini verwenden.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
PathPrefix=
*/ ?>
```

## PathPrefixExclude

> Welche URLs von der Einstellung PathPrefix ausgenommen werden.  
> URLs, die nach dem Namen des Siteaccess die angegebenen Texte enthalten, sind von PathPrefix nicht betroffen  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
# ein leeres "PathPrefixExclude[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PathPrefixExclude[]
*/ ?>
```

## ShowHiddenNodes

> PathPrefixExclude\[\]=media  
> PathPrefixExclude\[\]=users  
> Die Art dieses Siteaccess, wie Setup > Systeminformationen sie zeigt (admin, editor, login, site oder ein eigener  
> Name). Nicht gesetzt: aus seinen Designs und RequireUserLogin ermittelt.  
> SiteAccessType=  
> Ob verborgene Knoten angezeigt werden  
> Wenn false, kann der Benutzer keine verborgenen Knoten sehen  
> Sollte für bestimmte Siteaccesses überschrieben werden  

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
ShowHiddenNodes=false
*/ ?>
```

## DetectMobileDevice

> Ob mobile Geräte erkannt werden  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
DetectMobileDevice=disabled
*/ ?>
```

## MobileSiteAccessURL

> URL der für mobile Geräte optimierten Oberfläche  

Standard: `http://m.example.com`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
MobileSiteAccessURL=http://m.example.com
*/ ?>
```

## MobileSiteAccessList

> Liste der verfügbaren Siteaccesses, die mobile Geräte bedienen können  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
# ein leeres "MobileSiteAccessList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
MobileSiteAccessList[]
*/ ?>
```

## MobileDeviceDetectCookieTimeout

> Sekunden, die ein Sitzungs-Cookie hält  

Standard: `7200`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
MobileDeviceDetectCookieTimeout=7200
*/ ?>
```

## MobileDeviceFilterClass

> Klassenname des Filters für mobile Geräte.  
> Die Standardimplementierung prüft per RegExp gegen die User-Agent-Zeichenkette und gegen User-Agent-Codes  
> Die verfügbaren RegExps stehen im Array \[SiteAccessSettings\].MobileUserAgentRegexps  
> und die verfügbaren Codes im Wert \[SiteAccessSettings\].MobileUserAgentCodes  

Standard: `ezpMobileDeviceRegexpFilter`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
MobileDeviceFilterClass=ezpMobileDeviceRegexpFilter
*/ ?>
```

## MobileUserAgentRegexps

> Liste der verfügbaren regulären Ausdrücke, die gegen den User-Agent geprüft werden  
> Der Schlüssel ist ein User-Agent-Alias, der im Template das Gerät oder das mobile Betriebssystem bestimmen hilft  

Standard: ein Hash mit 10: `AndroidDevice=/android.*mobile/i`, `AndroidTabletDevice=/android(?!.*mobile)/i`, `IPhoneDevice=/(iphone|ipod)/i`, `IPadDevice=/ipad/i`, `OperaBrowser=/opera mini/i`, `BlackBerryDevice=/blackberry/i`, `BlackBerryTabletDevice=/rim tablet os/i`, `PalmDevice=/(pre\/|palm os|palm|hiptop|avantgo|plucker|xiino|blazer|elaine)/i`, `WindowsDevice=/(iris|3g_t|windows ce|opera mobi|windows ce; smartphone;|windows ce; iemobile)/i`, `Generic=/(mini 9.5|vx1000|lge |m800|e860|u940|ux840|compal|wireless| mobi|ahong|lg380|lgku|lgu900|lg210|lg47|lg920|lg840|lg370|sam-r|mg50|s55|g83|t66|vx400|mk99|d615|d763|el370|sl900|mp500|samu3|samu4|vx10|xda_|samu5|samu6|samu7|samu9|a615|b832|m881|s920|n210|s700|c-810|_h797|mob-x|sk16d|848b|mowser|s580|r800|471x|v120|rim8|c500foma:|160x|x160|480x|x640|t503|w839|i250|sprint|w398samr810|m5252|c7100|mt126|x225|s5330|s820|htil-g1|fly v71|s302|-x113|novarra|k610i|-three|8325rc|8352rc|sanyo|vx54|c888|nx250|n120|mtk |c5588|s710|t880|c5005|i;458x|p404i|s210|c5100|teleca|s940|c500|s590|foma|samsu|vx8|vx9|a1000|_mms|myx|a700|gu1100|bc831|e300|ems100|me701|me702m-three|sd588|s800|8325rc|ac831|mw200|brew |d88|htc\/|htc_touch|355x|m50|km100|d736|p-9521|telco|sl74|ktouch|m4u\/|me702|8325rc|kddi|phone|lg |sonyericsson|samsung|240x|x320|vx10|nokia|sony cmd|motorola|up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|pocket|kindle|mobile|psp|treo)/i`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
# ein leeres "MobileUserAgentRegexps[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
MobileUserAgentRegexps[]
MobileUserAgentRegexps[AndroidDevice]=/android.*mobile/i
MobileUserAgentRegexps[AndroidTabletDevice]=/android(?!.*mobile)/i
MobileUserAgentRegexps[IPhoneDevice]=/(iphone|ipod)/i
MobileUserAgentRegexps[IPadDevice]=/ipad/i
MobileUserAgentRegexps[OperaBrowser]=/opera mini/i
MobileUserAgentRegexps[BlackBerryDevice]=/blackberry/i
MobileUserAgentRegexps[BlackBerryTabletDevice]=/rim tablet os/i
MobileUserAgentRegexps[PalmDevice]=/(pre\/|palm os|palm|hiptop|avantgo|plucker|xiino|blazer|elaine)/i
MobileUserAgentRegexps[WindowsDevice]=/(iris|3g_t|windows ce|opera mobi|windows ce; smartphone;|windows ce; iemobile)/i
MobileUserAgentRegexps[Generic]=/(mini 9.5|vx1000|lge |m800|e860|u940|ux840|compal|wireless| mobi|ahong|lg380|lgku|lgu900|lg210|lg47|lg920|lg840|lg370|sam-r|mg50|s55|g83|t66|vx400|mk99|d615|d763|el370|sl900|mp500|samu3|samu4|vx10|xda_|samu5|samu6|samu7|samu9|a615|b832|m881|s920|n210|s700|c-810|_h797|mob-x|sk16d|848b|mowser|s580|r800|471x|v120|rim8|c500foma:|160x|x160|480x|x640|t503|w839|i250|sprint|w398samr810|m5252|c7100|mt126|x225|s5330|s820|htil-g1|fly v71|s302|-x113|novarra|k610i|-three|8325rc|8352rc|sanyo|vx54|c888|nx250|n120|mtk |c5588|s710|t880|c5005|i;458x|p404i|s210|c5100|teleca|s940|c500|s590|foma|samsu|vx8|vx9|a1000|_mms|myx|a700|gu1100|bc831|e300|ems100|me701|me702m-three|sd588|s800|8325rc|ac831|mw200|brew |d88|htc\/|htc_touch|355x|m50|km100|d736|p-9521|telco|sl74|ktouch|m4u\/|me702|8325rc|kddi|phone|lg |sonyericsson|samsung|240x|x320|vx10|nokia|sony cmd|motorola|up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|pocket|kindle|mobile|psp|treo)/i
*/ ?>
```

## MobileUserAgentCodes

> Liste verschiedener Codes, die gegen die User-Zeichenkette geprüft werden  

Standard: `1207|3gso|4thp|501i|502i|503i|504i|505i|506i|6310|6590|770s|802s|a wa|acer|acs-|airn|alav|asus|attw|au-m|aur |aus |abac|acoo|aiko|alco|alca|amoi|anex|anny|anyw|aptu|arch|argo|bell|bird|bw-n|bw-u|beck|benq|bilb|blac|c55/|cdm-|chtm|capi|cond|craw|dall|dbte|dc-s|dica|ds-d|ds12|dait|devi|dmob|doco|dopo|el49|erk0|esl8|ez40|ez60|ez70|ezos|ezze|elai|emul|eric|ezwa|fake|fly-|fly_|g-mo|g1 u|g560|gf-5|grun|gene|go.w|good|grad|hcit|hd-m|hd-p|hd-t|hei-|hp i|hpip|hs-c|htc |htc-|htca|htcg|htcp|htcs|htct|htc_|haie|hita|huaw|hutc|i-20|i-go|i-ma|i230|iac'|iac-|iac/|ig01|im1k|inno|iris|jata|java|kddi|kgt'|kgt/|kpt |kwc-|klon|lexi|lg g|lg-a|lg-b|lg-c|lg-d|lg-f|lg-g|lg-k|lg-l|lg-m|lg-o|lg-p|lg-s|lg-t|lg-u|lg-w|lg/k|lg/l|lg/u|lg50|lg54|lge-|lge/|lynx|leno|m1-w|m3ga|m50/|maui|mc01|mc21|mcca|medi|meri|mio8|mioa|mo01|mo02|mode|modo|mot |mot-|mt50|mtp1|mtv |mate|maxo|merc|mits|mobi|motv|mozz|n100|n101|n102|n202|n203|n300|n302|n500|n502|n505|n700|n701|n710|nec-|nem-|newg|neon|netf|noki|nzph|o2 x|o2-x|opwv|owg1|opti|oran|p800|pand|pg-1|pg-2|pg-3|pg-6|pg-8|pg-c|pg13|phil|pn-2|pt-g|palm|pana|pire|pock|pose|psio|qa-a|qc-2|qc-3|qc-5|qc-7|qc07|qc12|qc21|qc32|qc60|qci-|qwap|qtek|r380|r600|raks|rim9|rove|s55/|sage|sams|sc01|sch-|scp-|sdk/|se47|sec-|sec0|sec1|semc|sgh-|shar|sie-|sk-0|sl45|slid|smb3|smt5|sp01|sph-|spv |spv-|sy01|samm|sany|sava|scoo|send|siem|smar|smit|soft|sony|t-mo|t218|t250|t600|t610|t618|tcl-|tdg-|telm|tim-|ts70|tsm-|tsm3|tsm5|tx-9|tagt|talk|teli|topl|hiba|up.b|upg1|utst|v400|v750|veri|vk-v|vk40|vk50|vk52|vk53|vm40|vx98|virg|vite|voda|vulc|w3c |w3c-|wapj|wapp|wapu|wapm|wig |wapi|wapr|wapv|wapy|wapa|waps|wapt|winc|winw|wonu|x700|xda2|xdag|yas-|your|zte-|zeto|acs-|alav|alca|amoi|aste|audi|avan|benq|bird|blac|blaz|brew|brvw|bumb|ccwa|cell|cldc|cmd-|dang|doco|eml2|eric|fetc|hipt|http|ibro|idea|ikom|inno|ipaq|jbro|jemu|java|jigs|kddi|keji|kyoc|kyok|leno|lg-c|lg-d|lg-g|lge-|libw|m-cr|maui|maxo|midp|mits|mmef|mobi|mot-|moto|mwbp|mywa|nec-|newt|nok6|noki|o2im|opwv|palm|pana|pant|pdxg|phil|play|pluc|port|prox|qtek|qwap|rozo|sage|sama|sams|sany|sch-|sec-|send|seri|sgh-|shar|sie-|siem|smal|smar|sony|sph-|symb|t-mo|teli|tim-|tosh|treo|tsm-|upg1|upsi|vk-v|voda|vx52|vx53|vx60|vx61|vx70|vx80|vx81|vx83|vx85|wap-|wapa|wapi|wapp|wapr|webc|whit|winw|wmlb|xda-`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
MobileUserAgentCodes=1207|3gso|4thp|501i|502i|503i|504i|505i|506i|6310|6590|770s|802s|a wa|acer|acs-|airn|alav|asus|attw|au-m|aur |aus |abac|acoo|aiko|alco|alca|amoi|anex|anny|anyw|aptu|arch|argo|bell|bird|bw-n|bw-u|beck|benq|bilb|blac|c55/|cdm-|chtm|capi|cond|craw|dall|dbte|dc-s|dica|ds-d|ds12|dait|devi|dmob|doco|dopo|el49|erk0|esl8|ez40|ez60|ez70|ezos|ezze|elai|emul|eric|ezwa|fake|fly-|fly_|g-mo|g1 u|g560|gf-5|grun|gene|go.w|good|grad|hcit|hd-m|hd-p|hd-t|hei-|hp i|hpip|hs-c|htc |htc-|htca|htcg|htcp|htcs|htct|htc_|haie|hita|huaw|hutc|i-20|i-go|i-ma|i230|iac'|iac-|iac/|ig01|im1k|inno|iris|jata|java|kddi|kgt'|kgt/|kpt |kwc-|klon|lexi|lg g|lg-a|lg-b|lg-c|lg-d|lg-f|lg-g|lg-k|lg-l|lg-m|lg-o|lg-p|lg-s|lg-t|lg-u|lg-w|lg/k|lg/l|lg/u|lg50|lg54|lge-|lge/|lynx|leno|m1-w|m3ga|m50/|maui|mc01|mc21|mcca|medi|meri|mio8|mioa|mo01|mo02|mode|modo|mot |mot-|mt50|mtp1|mtv |mate|maxo|merc|mits|mobi|motv|mozz|n100|n101|n102|n202|n203|n300|n302|n500|n502|n505|n700|n701|n710|nec-|nem-|newg|neon|netf|noki|nzph|o2 x|o2-x|opwv|owg1|opti|oran|p800|pand|pg-1|pg-2|pg-3|pg-6|pg-8|pg-c|pg13|phil|pn-2|pt-g|palm|pana|pire|pock|pose|psio|qa-a|qc-2|qc-3|qc-5|qc-7|qc07|qc12|qc21|qc32|qc60|qci-|qwap|qtek|r380|r600|raks|rim9|rove|s55/|sage|sams|sc01|sch-|scp-|sdk/|se47|sec-|sec0|sec1|semc|sgh-|shar|sie-|sk-0|sl45|slid|smb3|smt5|sp01|sph-|spv |spv-|sy01|samm|sany|sava|scoo|send|siem|smar|smit|soft|sony|t-mo|t218|t250|t600|t610|t618|tcl-|tdg-|telm|tim-|ts70|tsm-|tsm3|tsm5|tx-9|tagt|talk|teli|topl|hiba|up.b|upg1|utst|v400|v750|veri|vk-v|vk40|vk50|vk52|vk53|vm40|vx98|virg|vite|voda|vulc|w3c |w3c-|wapj|wapp|wapu|wapm|wig |wapi|wapr|wapv|wapy|wapa|waps|wapt|winc|winw|wonu|x700|xda2|xdag|yas-|your|zte-|zeto|acs-|alav|alca|amoi|aste|audi|avan|benq|bird|blac|blaz|brew|brvw|bumb|ccwa|cell|cldc|cmd-|dang|doco|eml2|eric|fetc|hipt|http|ibro|idea|ikom|inno|ipaq|jbro|jemu|java|jigs|kddi|keji|kyoc|kyok|leno|lg-c|lg-d|lg-g|lge-|libw|m-cr|maui|maxo|midp|mits|mmef|mobi|mot-|moto|mwbp|mywa|nec-|newt|nok6|noki|o2im|opwv|palm|pana|pant|pdxg|phil|play|pluc|port|prox|qtek|qwap|rozo|sage|sama|sams|sany|sch-|sec-|send|seri|sgh-|shar|sie-|siem|smal|smar|sony|sph-|symb|t-mo|teli|tim-|tosh|treo|tsm-|upg1|upsi|vk-v|voda|vx52|vx53|vx60|vx61|vx70|vx80|vx81|vx83|vx85|wap-|wapa|wapi|wapp|wapr|webc|whit|winw|wmlb|xda-
*/ ?>
```

## NormalizeSANames

> Aktiviert die nicht automatische Umwandlung von Siteaccess-Namen  
> NormalizeSANames=enabled - für Rückwärtskompatibilität (alle nicht alphabetischen Zeichen werden in Unterstriche umgewandelt)  
> RedirectOnNormalize=enabled - wenn eine Umwandlung erfolgt, hat die Seite den Status 301  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
NormalizeSANames=enabled
*/ ?>
```

## RedirectOnNormalize

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
RedirectOnNormalize=disabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
