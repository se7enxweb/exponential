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

> Set to true if you want to force VirualHost mode (with RewriteRules in apache)  
> If this is set to true nonVirtualHost mode will not work correctly  

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
ForceVirtualHost=false
*/ ?>
```

## CheckValidity

> Set to true if you want the setup system to be activated  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
CheckValidity=true
*/ ?>
```

## RequireUserLogin

> Set the true if you don't want anonymous access to your site  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
RequireUserLogin=true
*/ ?>
```

## AvailableSiteAccessList

> A list over current active siteaccesses  

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

> A list of related siteaccesses sharing the same database  

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

> A list of module or module/views that don't require user login  
> admin4's mega menu (user/megamenu/<tab>) answers its script itself: a visitor who is not signed in  
> gets a bare 403 (X-Exp-Mega: signed-out) instead of the sign-in page with a 200. The view checks the  
> sign-in, the siteaccess and the tab on its own (see PolicyOmitList\[\] below).  
> Browsers send the reports of the Content Security Policy without signing in; csp/report takes them on every  
> siteaccess, the administration included (doc/eng-us/features/6.0/content-security-policy.md, "Reports").  

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

> An array of access types which are tried, possible entries are: host, host\_uri, uri, port and servervar  

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
> Possible values: disabled, map, element, text or regexp  
> disabled - turns off uri matching  
> map - match URL using URIMatchMapItems array  
> element  - maps a url element to a siteaccess name  
> text - match URL using pre or post sub-strings (URIMatchSubtextPre, URIMatchSubtextPost)  
> regexp   - match against a regexp and extract a portion of it  

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
> Add array entries here if you chose URIMatchType=map  
> Each entry consists of the uri;accessname  

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
> Use either disabled, map, element, text or regexp  

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

> Add array entries here if you chose HostMatchType=map  
> Each entry consists of the hostname;accessname\[;start|end|part|strict\]  
> The last optional part is the host matching method of the entry, as for  
> host\_uri: strict (the host as listed), start (the host begins with it, so a  
> test host example.com.test.local is matched by example.com), end or part.  
> Without it HostMatchMethod below applies. As for host\_uri, the static cache  
> only knows strict matching. Every method compares host names without regard to  
> case and to a trailing dot, ignores the port of the request unless the entry  
> names one (example.com:8080), and takes a name written in Unicode  
> (münchen.example) for the ASCII form browsers send (xn--mnchen-3ya.example).  
> start and part also take hosts you did not mean (example.com.anything): use  
> them only where the web server answers for your own hosts alone.  
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
> Add array entries here if you use MatchOrder host\_uri  
> Each entry consists of the hostname;uri;accessname\[;start|end|part|strict\]  
> Last optional paramter controls host matching method, 'strict' by default  
>
> Note: Only strict matching is supported by static cache, reason is the rewrite  
>       rules needs a strict convention between host/uri & match (basis for folder name).  
>       Uri part is also optional, so you can do pure host matching with this as well.  
>       Empty hostname parts are not possible.  
>
> Ordering: Exponential searches HostUriMatchMapItems\[\] from top to bottom, so place  
>           more specific entries at the top and entries with empty uri parts at the bottom.  
> Change HostUriMatchMethodDefault to one of these options to chenge default value  

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
> The siteaccess of an address that no probe of MatchOrder matches, where  
> DefaultAccess would apply: typically the address without siteaccess or language  
> segment (example.com/), with any MatchOrder (uri, host\_uri, ...). Chosen by host  
> and by the languages the browser accepts (Accept-Language, the most wanted first).  
> Each entry is hostname;uri;accessname\[;method\[;language\]\]: method as for  
> HostUriMatchMapItems, empty or "default" for HostUriMatchMethodDefault; an empty  
> hostname with part matches any host; language such as "de" also matches regional  
> forms (de-CH). The first entry for the host whose language the browser accepts  
> wins, else the first entry for the host without a language.  
> A GET or HEAD request is sent on (302), once, to the same address with the uri  
> part, the address kept as it was asked for: example.com/ goes to example.com/ger,  
> example.com/news/an-article to example.com/ger/news/an-article, with the query.  
> That needs a probe that takes the address with the uri part for that siteaccess  
> (else it would come back here); without one, for other requests, or with  
> DefaultHostUriRedirect=disabled the page is shown at the address itself, its  
> links carrying the uri part. An address a probe matches (/ger/..., /admin/...)  
> is never sent on. The Location is a path on the host that was asked  
> (/ger/news?x=1), built from the index file and the uri part of the entry, so  
> RemoveSiteAccessIfDefaultAccess does not shorten it into a loop.  
> When the browser's language can change the answer for the host (an entry names  
> a language, and the entries lead to more than one siteaccess or uri part, or no  
> entry applies without a language), the redirect, a page shown this way and the  
> default siteaccess answering for other browsers vary by Accept-Language: they are  
> sent with Vary: Accept-Language and Cache-Control: private, and neither the HTTP  
> cache nor its early exit keeps or serves them. An entry naming a siteaccess that  
> is not in AvailableSiteAccessList is ignored (and logged). Pages kept by the  
> static cache or the HTTP cache before an entry was added are served without the  
> redirect until those caches are cleared.  
> A site matched by the URL (MatchOrder=uri) with the language siteaccess ger:  
>   DefaultHostUriMatchMapItems\[\]=;ger;ger;part  
> A site matched by host and uri, German and English by the browser:  
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
> Set name of server variable used to match a siteaccess  

Standard: `SITEACCESS`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
ServerVariableName=SITEACCESS
*/ ?>
```

## RemoveSiteAccessIfDefaultAccess

> Wheter to add siteaccess to the url if current siteaccess  
> is the same as default siteaccess. Changing this setting will  
> require clearing the cache.  
>
> This setting also works with host\_uri matching removing uri part  
> from url if it's default siteacces. In this case, make sure  
> host name without url part goes to same siteaccess in your  
> match order chain.  
>
> For instance if the default siteaccess is 'en' and you enable  
> this you can access the siteacccess with 'index.php' instead of  
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

> Another siteaccess whose extension siteaccess settings this one uses as well  
> (extension/<ext>/settings/siteaccess/<that siteaccess>, and the extension's  
> settings/override it brings along), below the ones an extension ships for this  
> siteaccess itself. A translation siteaccess made from the user siteaccess sets  
> it to that siteaccess, so it renders with the same design and extension  
> settings in its own language. Read from this siteaccess's own  
> settings/siteaccess/<name>/site.ini.append.php only, because the extension  
> settings are placed before the siteaccess settings are merged.  
> ExtensionSettingsSiteAccess=site  
> Hides this part from the start of the url alias  
> If you use this setting, you also need to use the PathPrefix setting in logfile.ini.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
PathPrefix=
*/ ?>
```

## PathPrefixExclude

> Which URLs to exclude from being affected by PathPrefix setting.  
> URLs containing the specified texts after siteaccess name will not be affected by PathPrefix  

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
> The kind of this siteaccess as Setup > System information shows it (admin, editor, login, site or a name of  
> your own). Not set: worked out from its designs and RequireUserLogin.  
> SiteAccessType=  
> Whether to show hidden nodes  
> If set to false, user is not able to see any hidden nodes  
> Should be overriden for particular siteaccesses  

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
ShowHiddenNodes=false
*/ ?>
```

## DetectMobileDevice

> Whether to detect mobile devices or not  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
DetectMobileDevice=disabled
*/ ?>
```

## MobileSiteAccessURL

> URL address to the mobile devices optimized interface  

Standard: `http://m.example.com`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
MobileSiteAccessURL=http://m.example.com
*/ ?>
```

## MobileSiteAccessList

> List of available siteaccesses capable to handle mobile devices  

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

> Number of seconds a session cookie will last  

Standard: `7200`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
MobileDeviceDetectCookieTimeout=7200
*/ ?>
```

## MobileDeviceFilterClass

> Mobile device filter class name.  
> Default implementation uses RegExp to match against User Agent string as well as against User Agent codes  
> See \[SiteAccessSettings\].MobileUserAgentRegexps array for the list of available RegExps  
> and \[SiteAccessSettings\].MobileUserAgentCodes value for the list of available codes  

Standard: `ezpMobileDeviceRegexpFilter`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
MobileDeviceFilterClass=ezpMobileDeviceRegexpFilter
*/ ?>
```

## MobileUserAgentRegexps

> List of available regular expressions to match against User Agent  
> Key is a User Agent alias which helps to determine the device or mobile OS in the template  

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

> List of various codes to match against User String  

Standard: `1207|3gso|4thp|501i|502i|503i|504i|505i|506i|6310|6590|770s|802s|a wa|acer|acs-|airn|alav|asus|attw|au-m|aur |aus |abac|acoo|aiko|alco|alca|amoi|anex|anny|anyw|aptu|arch|argo|bell|bird|bw-n|bw-u|beck|benq|bilb|blac|c55/|cdm-|chtm|capi|cond|craw|dall|dbte|dc-s|dica|ds-d|ds12|dait|devi|dmob|doco|dopo|el49|erk0|esl8|ez40|ez60|ez70|ezos|ezze|elai|emul|eric|ezwa|fake|fly-|fly_|g-mo|g1 u|g560|gf-5|grun|gene|go.w|good|grad|hcit|hd-m|hd-p|hd-t|hei-|hp i|hpip|hs-c|htc |htc-|htca|htcg|htcp|htcs|htct|htc_|haie|hita|huaw|hutc|i-20|i-go|i-ma|i230|iac'|iac-|iac/|ig01|im1k|inno|iris|jata|java|kddi|kgt'|kgt/|kpt |kwc-|klon|lexi|lg g|lg-a|lg-b|lg-c|lg-d|lg-f|lg-g|lg-k|lg-l|lg-m|lg-o|lg-p|lg-s|lg-t|lg-u|lg-w|lg/k|lg/l|lg/u|lg50|lg54|lge-|lge/|lynx|leno|m1-w|m3ga|m50/|maui|mc01|mc21|mcca|medi|meri|mio8|mioa|mo01|mo02|mode|modo|mot |mot-|mt50|mtp1|mtv |mate|maxo|merc|mits|mobi|motv|mozz|n100|n101|n102|n202|n203|n300|n302|n500|n502|n505|n700|n701|n710|nec-|nem-|newg|neon|netf|noki|nzph|o2 x|o2-x|opwv|owg1|opti|oran|p800|pand|pg-1|pg-2|pg-3|pg-6|pg-8|pg-c|pg13|phil|pn-2|pt-g|palm|pana|pire|pock|pose|psio|qa-a|qc-2|qc-3|qc-5|qc-7|qc07|qc12|qc21|qc32|qc60|qci-|qwap|qtek|r380|r600|raks|rim9|rove|s55/|sage|sams|sc01|sch-|scp-|sdk/|se47|sec-|sec0|sec1|semc|sgh-|shar|sie-|sk-0|sl45|slid|smb3|smt5|sp01|sph-|spv |spv-|sy01|samm|sany|sava|scoo|send|siem|smar|smit|soft|sony|t-mo|t218|t250|t600|t610|t618|tcl-|tdg-|telm|tim-|ts70|tsm-|tsm3|tsm5|tx-9|tagt|talk|teli|topl|hiba|up.b|upg1|utst|v400|v750|veri|vk-v|vk40|vk50|vk52|vk53|vm40|vx98|virg|vite|voda|vulc|w3c |w3c-|wapj|wapp|wapu|wapm|wig |wapi|wapr|wapv|wapy|wapa|waps|wapt|winc|winw|wonu|x700|xda2|xdag|yas-|your|zte-|zeto|acs-|alav|alca|amoi|aste|audi|avan|benq|bird|blac|blaz|brew|brvw|bumb|ccwa|cell|cldc|cmd-|dang|doco|eml2|eric|fetc|hipt|http|ibro|idea|ikom|inno|ipaq|jbro|jemu|java|jigs|kddi|keji|kyoc|kyok|leno|lg-c|lg-d|lg-g|lge-|libw|m-cr|maui|maxo|midp|mits|mmef|mobi|mot-|moto|mwbp|mywa|nec-|newt|nok6|noki|o2im|opwv|palm|pana|pant|pdxg|phil|play|pluc|port|prox|qtek|qwap|rozo|sage|sama|sams|sany|sch-|sec-|send|seri|sgh-|shar|sie-|siem|smal|smar|sony|sph-|symb|t-mo|teli|tim-|tosh|treo|tsm-|upg1|upsi|vk-v|voda|vx52|vx53|vx60|vx61|vx70|vx80|vx81|vx83|vx85|wap-|wapa|wapi|wapp|wapr|webc|whit|winw|wmlb|xda-`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteAccessSettings]
MobileUserAgentCodes=1207|3gso|4thp|501i|502i|503i|504i|505i|506i|6310|6590|770s|802s|a wa|acer|acs-|airn|alav|asus|attw|au-m|aur |aus |abac|acoo|aiko|alco|alca|amoi|anex|anny|anyw|aptu|arch|argo|bell|bird|bw-n|bw-u|beck|benq|bilb|blac|c55/|cdm-|chtm|capi|cond|craw|dall|dbte|dc-s|dica|ds-d|ds12|dait|devi|dmob|doco|dopo|el49|erk0|esl8|ez40|ez60|ez70|ezos|ezze|elai|emul|eric|ezwa|fake|fly-|fly_|g-mo|g1 u|g560|gf-5|grun|gene|go.w|good|grad|hcit|hd-m|hd-p|hd-t|hei-|hp i|hpip|hs-c|htc |htc-|htca|htcg|htcp|htcs|htct|htc_|haie|hita|huaw|hutc|i-20|i-go|i-ma|i230|iac'|iac-|iac/|ig01|im1k|inno|iris|jata|java|kddi|kgt'|kgt/|kpt |kwc-|klon|lexi|lg g|lg-a|lg-b|lg-c|lg-d|lg-f|lg-g|lg-k|lg-l|lg-m|lg-o|lg-p|lg-s|lg-t|lg-u|lg-w|lg/k|lg/l|lg/u|lg50|lg54|lge-|lge/|lynx|leno|m1-w|m3ga|m50/|maui|mc01|mc21|mcca|medi|meri|mio8|mioa|mo01|mo02|mode|modo|mot |mot-|mt50|mtp1|mtv |mate|maxo|merc|mits|mobi|motv|mozz|n100|n101|n102|n202|n203|n300|n302|n500|n502|n505|n700|n701|n710|nec-|nem-|newg|neon|netf|noki|nzph|o2 x|o2-x|opwv|owg1|opti|oran|p800|pand|pg-1|pg-2|pg-3|pg-6|pg-8|pg-c|pg13|phil|pn-2|pt-g|palm|pana|pire|pock|pose|psio|qa-a|qc-2|qc-3|qc-5|qc-7|qc07|qc12|qc21|qc32|qc60|qci-|qwap|qtek|r380|r600|raks|rim9|rove|s55/|sage|sams|sc01|sch-|scp-|sdk/|se47|sec-|sec0|sec1|semc|sgh-|shar|sie-|sk-0|sl45|slid|smb3|smt5|sp01|sph-|spv |spv-|sy01|samm|sany|sava|scoo|send|siem|smar|smit|soft|sony|t-mo|t218|t250|t600|t610|t618|tcl-|tdg-|telm|tim-|ts70|tsm-|tsm3|tsm5|tx-9|tagt|talk|teli|topl|hiba|up.b|upg1|utst|v400|v750|veri|vk-v|vk40|vk50|vk52|vk53|vm40|vx98|virg|vite|voda|vulc|w3c |w3c-|wapj|wapp|wapu|wapm|wig |wapi|wapr|wapv|wapy|wapa|waps|wapt|winc|winw|wonu|x700|xda2|xdag|yas-|your|zte-|zeto|acs-|alav|alca|amoi|aste|audi|avan|benq|bird|blac|blaz|brew|brvw|bumb|ccwa|cell|cldc|cmd-|dang|doco|eml2|eric|fetc|hipt|http|ibro|idea|ikom|inno|ipaq|jbro|jemu|java|jigs|kddi|keji|kyoc|kyok|leno|lg-c|lg-d|lg-g|lge-|libw|m-cr|maui|maxo|midp|mits|mmef|mobi|mot-|moto|mwbp|mywa|nec-|newt|nok6|noki|o2im|opwv|palm|pana|pant|pdxg|phil|play|pluc|port|prox|qtek|qwap|rozo|sage|sama|sams|sany|sch-|sec-|send|seri|sgh-|shar|sie-|siem|smal|smar|sony|sph-|symb|t-mo|teli|tim-|tosh|treo|tsm-|upg1|upsi|vk-v|voda|vx52|vx53|vx60|vx61|vx70|vx80|vx81|vx83|vx85|wap-|wapa|wapi|wapp|wapr|webc|whit|winw|wmlb|xda-
*/ ?>
```

## NormalizeSANames

> Enabling non automatic conversion of siteaccess names  
> NormalizeSANames=enabled - for backward compability (all non alpha characters are converted to underscore)  
> RedirectOnNormalize=enabled - when transformation occurs, the page will hold a 301 status  

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
