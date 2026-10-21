---
title: site.ini [SiteSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SiteSettings] in site.ini: SiteName, SiteURL, SiteDescription, MetaDataArray, IndexPage, ErrorHandler, DefaultPage, DefaultAccess ..."
keywords: Einstellung, site.ini, SiteSettings, INI, setting, SiteName, SiteURL, SiteDescription, MetaDataArray, IndexPage, ErrorHandler, DefaultPage, DefaultAccess, FrontSiteAccess, LoginPage, SSLPort, SSLProxyServerName, SiteList, 64bitCompatibilityMode, HTTPAUTHServerVariable, AdditionalLoginFormActionURL, AllowedRedirectHosts, DisallowedReturnViews
type: generated
generated: exp:help:examples
---
# site.ini [SiteSettings]

Der Block `[SiteSettings]` von `settings/site.ini`, mit 18 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## SiteName

> Name der Website, wird in den Standard-Templates in Titeln verwendet.  

Standard: `Exponential`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
SiteName=Exponential
*/ ?>
```

## SiteURL

> URL der Website, oft verwendet, um in E-Mails usw. auf die Website zu verlinken;  
> darf nicht mit http:// beginnen und nicht mit / enden, z. B.:  
> SiteURL=example.com  
> SiteURL=example.com/french  
> SiteURL=example.com/index.php/english  

Standard: `example.com`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
SiteURL=example.com
*/ ?>
```

## SiteDescription

> (Kurze) Beschreibung der Website, wird in Templates im HTML-Kopf verwendet.  

Standard: `This is an Exponential website about online shopping!`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
SiteDescription=This is an Exponential website about online shopping!
*/ ?>
```

## MetaDataArray

> Die Teile der URL, die für die Erkennung des Siteaccess verwendet werden,  
> falls vorhanden, nötig für die Rückwärtssuche. Verwendet den Namen des Siteaccess,  
> wenn nicht definiert und die aktuelle Abgleichsart uri ist.  
> Hinweis: kann nicht in den Einstellungen von ActiveAccessExtension definiert werden  
> z. B.: SiteUriParts\[\]=english  
> SiteUriParts\[\]  
> Liste der Metadaten, die im Pagelayout gesetzt werden  

Standard: ein Hash mit 4: `author=Exponential`, `copyright=Exponential`, `description=Content Management System`, `keywords=cms, publish, e-commerce, content management, development framework`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
# ein leeres "MetaDataArray[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
MetaDataArray[]
MetaDataArray[author]=Exponential
MetaDataArray[copyright]=Exponential
MetaDataArray[description]=Content Management System
MetaDataArray[keywords]=cms, publish, e-commerce, content management, development framework
*/ ?>
```

## IndexPage

> Welche Seite angezeigt wird, wenn der Wurzel-Index (/) aufgerufen wird  

Standard: `/content/view/full/2/`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
IndexPage=/content/view/full/2/
*/ ?>
```

## ErrorHandler

> Was zu tun ist, wenn ein Modul nicht existiert; verwenden Sie defaultpage oder displayerror  
> Bei defaultpage wird bei einem Fehler die DefaultPage angezeigt  

Standard: `displayerror`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
ErrorHandler=displayerror
*/ ?>
```

## DefaultPage

> Die anzuzeigende Standardseite; z. B. wird sie nach der Anmeldung eines Benutzers als Standardweiterleitung verwendet  

Standard: `/content/view/full/2/`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
DefaultPage=/content/view/full/2/
*/ ?>
```

## DefaultAccess

> Der Standardzugriff ist beim URI-Abgleich nötig, weil sich  
> bei leeren URLs der Zugriff nicht ermitteln lässt  

Standard: `admin`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
DefaultAccess=admin
*/ ?>
```

## FrontSiteAccess

> Die Website, zu der ein Administrations-Siteaccess gehört: Seine Links „Website öffnen“ und „Auf der  
> Website ansehen“ (Template-Operator siteaccess\_url() ohne Parameter) führen zu  
> diesem Siteaccess. Setzen Sie ihn in der site.ini.append.php des Administrations-Siteaccess, wenn eine  
> Installation mehrere Websites bedient, jede mit eigener Administration. Leer: DefaultAccess.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
FrontSiteAccess=
*/ ?>
```

## LoginPage

> Wie die Anmeldeseite behandelt wird: embedded zeigt sie im Standard-Pagelayout,  
> custom verwendet loginpagelayout.tpl  

Standard: `custom`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
LoginPage=custom
*/ ?>
```

## SSLPort

> Der SSL-Port; der Standard sollte für die meisten Websites passen, kann aber  
> geändert werden, wenn er abweicht. Wird der Port erkannt, laufen alle Weiterleitungen  
> mit dem Protokoll https.  

Standard: `443`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
SSLPort=443
*/ ?>
```

## SSLProxyServerName

> Wenn Sie einen SSL-Proxy verwenden, geben Sie hier den Namen des Proxyservers ein  

Standard: `localhost`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
SSLProxyServerName=localhost
*/ ?>
```

## SiteList

> Eine Liste der Websites, die dieser Server bereitstellt; meist der  
> Siteaccess für die Benutzeransicht, wenn mehrere Pakete installiert werden.  
> z. B.  
> SiteList\[\]=news  
> SiteList\[\]=plain  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
# ein leeres "SiteList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SiteList[]
*/ ?>
```

## 64bitCompatibilityMode

> Aktiviert/deaktiviert den 64-Bit-Kompatibilitätsmodus.  
> Betrifft manche PHP-Funktionen wie crc32.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
64bitCompatibilityMode=enabled
*/ ?>
```

## HTTPAUTHServerVariable

> Legt bei FastCGI-/CGI-Einrichtungen den Namen der Servervariable mit der Zeichenkette der HTTP-Authentifizierung fest.  
> Nur die Schnittstellen WebDAV und SOAP hängen von dieser Einstellung ab.  

Standard: `Authorization`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
HTTPAUTHServerVariable=Authorization
*/ ?>
```

## AdditionalLoginFormActionURL

> Mit dieser Einstellung geben Sie eine zusätzliche URL für die Anmeldung an, die Sie dann in Ihrem Anmelde-Template  
> verwenden, um die Anmeldung an einer anderen als der aktuellen Website auszuführen. Zum Beispiel können Sie sich von der Benutzer-Website  
> direkt an der Administrationsoberfläche anmelden. Beachten Sie, dass die Standard-Templates von Exponential  
> diese Funktion nicht unterstützen.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
AdditionalLoginFormActionURL=
*/ ?>
```

## AllowedRedirectHosts

> Array der Hosts, die für Weiterleitungen mit voller URL aus Modulen von Exponential erlaubt sind.  
> Hinweis: Der aktuelle Host und die konfigurierten Hosts (HostMatchMapItems/HostUriMatchMapItems) gelten immer als vertrauenswürdig  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
# ein leeres "AllowedRedirectHosts[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AllowedRedirectHosts[]
*/ ?>
```

## DisallowedReturnViews

> AllowedRedirectHosts\[\]=www.sometrustedhost.com  
> Modulansichten, zu denen „zurück, woher Sie kamen“ nie zurückkehrt: Abbrechen und Verwerfen von user/edit,  
> user/password, user/setting, section/edit, Bearbeitungen von Zuständen, role/edit, class/edit und class/groupedit, die zu  
> der Seite gehen, die das Formular nennt (RedirectIfDiscarded), oder zur zuletzt angesehenen Seite. Diese Ansichten handeln auf einen POST, beenden die  
> Sitzung oder starten einen Download, ein GET dort zeigt also nichts oder tut etwas. Die laufende Ansicht selbst wird  
> immer übersprungen. Siehe doc/eng-us/features/6.0/safe-redirects.md  

Standard: eine Liste mit 14: `content/action`, `content/removeobject`, `content/removenode`, `content/removeassignment`, `content/removeeditversion`, `content/download`, `user/logout`, `user/login`, `user/activate`, `user/success`, `user/forgotpassword`, `user/register` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSettings]
# ein leeres "DisallowedReturnViews[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
DisallowedReturnViews[]
DisallowedReturnViews[]=content/action
DisallowedReturnViews[]=content/removeobject
DisallowedReturnViews[]=content/removenode
DisallowedReturnViews[]=content/removeassignment
DisallowedReturnViews[]=content/removeeditversion
DisallowedReturnViews[]=content/download
DisallowedReturnViews[]=user/logout
DisallowedReturnViews[]=user/login
DisallowedReturnViews[]=user/activate
DisallowedReturnViews[]=user/success
DisallowedReturnViews[]=user/forgotpassword
DisallowedReturnViews[]=user/register
DisallowedReturnViews[]=layout/set
DisallowedReturnViews[]=shop/add
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
