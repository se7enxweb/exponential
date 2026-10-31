---
title: velocity.ini [ServerSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ServerSettings] in velocity.ini: Engine, ScriptPath, DocumentRoot, FollowSymlinks, Host, Port, HTTPSPort, User ..."
keywords: Einstellung, velocity.ini, ServerSettings, INI, setting, Engine, ScriptPath, DocumentRoot, FollowSymlinks, Host, Port, HTTPSPort, User, Group, AllowRootWorkers, Workers, SpareWorkers, IdleWorkerTimeout, Instances, SharedInstances, Nice, StatTtl, Zygote, Brand, BrandUrl, Maintainer, MaintainerUrl, BrandMark, BrandColor, BrandAccent, BrandBackground, BrandIcon, BrandOgImage, BrandDescription, PreloadWarmup
type: generated
generated: exp:help:examples
---
# velocity.ini [ServerSettings]

Der Block `[ServerSettings]` von `settings/velocity.ini`, mit 48 Einstellung(en).

Bearbeiten Sie `settings/velocity.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/velocity.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/velocity.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/velocity.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Engine

> Der Server, den start, stop und status steuern, wenn kein --engine angegeben ist:  
>
>   php         der eingebaute Webserver von PHP (php -S), mit dem PHP, das  
>               exp:velocity ausführt. Für die Entwicklung: nichts zu installieren und nichts,  
>               was fehlen könnte, aber kein TLS und kein Neuladen. Der ausgelieferte Standard,  
>               weil er immer funktioniert. Siehe \[PHPServerSettings\].  
>   frankenphp  FrankenPHP, der Webserver Caddy mit eingebautem PHP, als eine  
>               Binärdatei, die \`exp:velocity install\` herunterlädt. Die  
>               produktionsreife Alternative.  
>               Siehe \[FrankenPHPSettings\].  
>   qbix        der mitgelieferte Qbix-Server (vendor/se7enxweb/exponential-velocity,  
>               früher vendor/se7enxweb/qbix-webserver; beide werden gefunden), ein  
>               PHP-Prozess mit dauerhaften Workern oder Workern pro Anfrage. Die  
>               empfohlene Engine für jede Stufe (Entwicklung, Alpha,  
>               Beta, Demo, Stable und Produktion) und die schnellste.  
>
> Setzen Sie hier qbix für jede Website; frankenphp ist die produktionsreife  
> Alternative. Die Verben sind für jede  
> Engine gleich, und --engine=<name> (oder --all) erreicht die anderen für einen Befehl.  
>
> Die Engines können zum Testen nebeneinander laufen: Jede hat einen eigenen Port  
> (php 8087, qbix 8088 unten, frankenphp 8089), eine eigene PID-Datei und eigene Logs.  
> \[FrankenPHPSettings\] und \[PHPServerSettings\] dürfen Port, HTTPSPort, Host,  
> Workers, SpareWorkers und DocumentRoot für ihre Engine setzen; leer gelassen, gilt der  
> Wert hier. Einstellungen, die nur ein Server hat, meldet \`status\`,  
> wenn sie für einen anderen gesetzt sind. Siehe doc/eng-us/bc/6.0/velocity-engines.md.  

Standard: `php`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
Engine=php
*/ ?>
```

## ScriptPath

> Das Skript des Qbix-Servers. Relative Pfade gelten ab dem Wurzelverzeichnis der  
> Installation. Nur die Engine qbix; Port, HTTPSPort, Host und Workers unten sind die der  
> Engine qbix und die der anderen, wenn deren eigene Blöcke sie leer lassen.  
> Ab Exponential Velocity 0.0.4.41 hält die Engine den Server in  
> sbin/qbixserver.php (ihr qbixctl und qbixconsole ebenfalls in sbin/, ihre Shell in  
> bin/), und qbixserver.php oben im Paket ist eine Weiterleitung, die ihn  
> im selben Prozess ausführt: Der Wert unten funktioniert mit der Engine vor und  
> nach diesem Release, und sbin/qbixserver.php kann stattdessen genannt werden (der andere  
> Pfad wird verwendet, wenn der genannte nicht da ist). Ein über einen der beiden Pfade gestarteter Server  
> gehört für status, stop und restart zu dieser Installation.  

Standard: `vendor/se7enxweb/exponential-velocity/qbixserver.php`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
ScriptPath=vendor/se7enxweb/exponential-velocity/qbixserver.php
*/ ?>
```

## DocumentRoot

> An den Server übergebenes Dokumentenverzeichnis. Leer bedeutet das Wurzelverzeichnis der Installation,  
> wo index.php liegt.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
DocumentRoot=
*/ ?>
```

## FollowSymlinks

> Dateien ausliefern, zu denen ein symbolischer Link im Dokumentenverzeichnis außerhalb davon  
> führt. disabled lehnt sie ab: Die Engine qbix antwortet mit 403, die Engine php mit  
> 404. Ein im Dokumentenverzeichnis abgelegter Link (durch einen Upload, einen Installer oder einen  
> gemeinsamen Asset-Baum) würde sonst ausliefern, worauf er zeigt.  
> enabled ist für Installationen, die absichtlich verlinken, etwa Erweiterungen,  
> die aus einem gemeinsamen Checkout verlinkt sind, oder var/storage auf einer anderen Festplatte. Ein Pfad,  
> der mit ../ hinausklettert, wird in beiden Fällen abgelehnt, und keine Engine liefert je  
> eine .php-Datei als Datei aus. Die Engine frankenphp folgt Links immer  
> (der Dateiserver von Caddy hat dafür keinen Schalter).  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
FollowSymlinks=disabled
*/ ?>
```

## Host

> Bindeadresse. 127.0.0.1 hält den Server nur über einen Tunnel oder  
> einen Reverse-Proxy erreichbar, was der richtige Standard ist: Dieser Server hat keine eigene HTTP-  
> Authentifizierung und liefert das ganze Dokumentenverzeichnis aus.  

Standard: `127.0.0.1`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
Host=127.0.0.1
*/ ?>
```

## Port

Standard: `8088`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
Port=8088
*/ ?>
```

## HTTPSPort

Standard: `8080`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
HTTPSPort=8080
*/ ?>
```

## User

> Der Benutzer und die Gruppe, als die die Worker laufen, wenn der Server als root gestartet wird  
> (die Engine qbix; User und Group von Apache). Der Serverprozess selbst bleibt  
> root (er bindet die Ports und liest das Zertifikat), und jeder Worker gibt  
> root ab, bevor er irgendetwas der Anwendung ausführt, sodass das, was die Website schreibt  
> (Bildvarianten, Caches, Sitzungen), dem Benutzer der Website gehört, wie  
> unter Apache und PHP-FPM, und nicht root.  
>
> Leer: VC\_RUN\_USER / VC\_RUN\_GROUP aus der Umgebung oder /etc/vc/envvars,  
> sonst Eigentümer und Gruppe des Dokumentenverzeichnisses. Wenn das Dokumentenverzeichnis  
> selbst root gehört, bleiben die Worker root, mit einer Warnung. Ein Benutzer oder  
> eine Gruppe, die nicht existiert, hält den Start an. root wird abgelehnt, außer bei  
> AllowRootWorkers=enabled. Namen oder numerische IDs (#10009).  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
User=
*/ ?>
```

## Group

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
Group=
*/ ?>
```

## AllowRootWorkers

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
AllowRootWorkers=disabled
*/ ?>
```

## Workers

> Immer ausdrücklich. Sich selbst überlassen, bemisst der Server den Pool nach dem Rechner  
> und kann eine Zahl wählen, die so groß ist, dass er Verbindungen annimmt und keine beantwortet.  
> Mit gesetztem SpareWorkers ist dies das Höchste, auf das der Pool wächst, nicht das, was er ausführt.  

Standard: `4`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
Workers=4
*/ ?>
```

## SpareWorkers

> Dynamischer Pool. Worker, die im Ruhezustand laufen; mehr werden geforkt, wenn alle  
> beschäftigt sind, bis zu Workers, und nach IdleWorkerTimeout  
> Sekunden ohne Anfrage wieder stillgelegt. Jeder Worker hält ab dem Fork ~10 MB echten  
> eigenen Speicher, untätig oder nicht, ein großer fester Pool bezahlt also  
> für Worker, die nie etwas bedienen. 0 = fester Pool (alle Workers, immer).  

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
SpareWorkers=0
*/ ?>
```

## IdleWorkerTimeout

Standard: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
IdleWorkerTimeout=60
*/ ?>
```

## Instances

> Wie viele Server auf denselben Ports laufen (nur die qbix-Engine). Ein Server  
> beantwortet gecachte Seiten in seinem eigenen Prozess, etwa 0,3 ms CPU pro Seite, also ist ein  
> Server durch einen Kern begrenzt: ~3.500 gecachte Seiten pro Sekunde gemessen auf einer  
> Maschine mit 12 Kernen. Bei mehr als einem lauscht jede Instanz auf denselben Ports  
> mit SO\_REUSEPORT, und der Kernel verteilt die Verbindungen auf sie: zwei  
> gemessen 6.600-6.900, vier 12.200-13.100. Renders werden nicht schneller; sie sind  
> durch die Worker begrenzt.  
>
> Jede Instanz hat ihren eigenen Worker-Pool: Workers und SpareWorkers oben gelten  
> pro Instanz, teilen Sie sie also durch Instances, um denselben Speicher zu behalten. Jede hat  
> ihre eigene PID-Datei neben PidFile (server.pid, server.1.pid, ...). graceful  
> lädt sie nacheinander neu, damit der Port weiter antwortet. Nur Linux  
> und die BSDs.  

Standard: `1`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
Instances=1
*/ ?>
```

## SharedInstances

> enabled: die Instanzen teilen sich einen Cache kompilierter Skripte und ein APCu-  
> Segment (die Engine startet die erste und forkt die anderen daraus, Exponential  
> Velocity 0.0.4.56 und später), sodass ein Skript einmal kompiliert wird und ein Ergebnis, das der  
> Kernel in APCu hält (der Query-Cache), einmal bezahlt wird, nicht einmal pro  
> Instanz; alle schreiben in LogFile. disabled: jede Instanz wird für sich gestartet,  
> mit eigenen Caches und einem eigenen Log (console.1.log ...). Eine mit graceful  
> neu geladene Instanz richtet PHP neu ein und hat von da an eigene Caches.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
SharedInstances=enabled
*/ ?>
```

## Nice

> Die Priorität (Nice-Wert, -20 bis 19), mit der der Server und seine Worker laufen,  
> egal mit welchem Wert der Aufrufer von start, restart oder deploy läuft: Ein Deployment, das  
> unter nice -n 19 gestartet wurde, ließ früher jeden Worker auf 19. Ihn unter den Wert  
> des Aufrufers zu senken braucht root. inherit behält den Wert des Aufrufers (das alte Verhalten).  
> exp:velocity status meldet einen laufenden Elternprozess mit einem anderen Wert.  

Standard: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
Nice=5
*/ ?>
```

## StatTtl

> Wie lange ein Worker behalten darf, was er über Dateien weiß (ob ein Pfad  
> existiert, mtime und Größe einer Datei), über Anfragen hinweg, in Sekunden (höchstens 10),  
> so wie opcache.revalidate\_freq es für Kompilierungen tut. 0 vergisst es am Ende  
> jeder Anfrage. Änderungen, die der Worker selbst macht, clearstatcache() und ein  
> Programm, das er ausführt (ImageMagick), werden in beiden Fällen sofort gesehen; nur eine Änderung  
> eines anderen Prozesses (ein anderer Worker, Apache auf denselben Dateien, ein Editor)  
> kann bis zu so lange brauchen, um sichtbar zu werden. 1 maß 13 % weniger CPU pro gerenderter Seite.  

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
StatTtl=0
*/ ?>
```

## Zygote

> Worker nach dem Start aus einer Zygote forken (einem Prozess, der geforkt wurde, bevor der  
> Server seine erste Verbindung angenommen hat) statt aus dem Server selbst.  
> Ein aus dem Server geforkter Worker erbt jede offene TLS-Verbindung und kann sie nicht  
> schließen (das würde sie auch für den Server beenden), daher hielt unter Last jeder  
> die Verbindungen der Besucher offen, bis er stillgelegt wurde: bis zu 50 in CLOSE-WAIT nach einem  
> 20-sekündigen Ansturm hier. enabled, disabled oder leer für den Standard der Engine  
> (enabled). Braucht die PHP-Erweiterung sockets; scheitert die Zygote, werden die Worker  
> wie bisher aus dem Server geforkt.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
Zygote=
*/ ?>
```

## Brand

> Produktname in den ausgelieferten /Q/-Ansichten: Überschrift und Titel  
> des Dashboards, der Rahmen der Doku, das Panel, das Manifest. Leer  
> behält den eigenen Standard des Servers ("Qbix Server"); eine Installation,  
> die sich selbst benennt, überschreibt dies in settings/override/velocity.ini.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
Brand=
*/ ?>
```

## BrandUrl

> Wohin der Markenname verlinkt (die Startseite des Produkts), und die Nennung des  
> Maintainers. Alles optional; leer zeigt einfachen Text ohne Link.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
BrandUrl=
*/ ?>
```

## Maintainer

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
Maintainer=
*/ ?>
```

## MaintainerUrl

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
MaintainerUrl=
*/ ?>
```

## BrandMark

> Symbol und Linkvorschau der /Q/-Ansichten (Favicon, Startbildschirm-Symbole, Web-App-  
> Manifest, das Bild, das ein geteilter Link zeigt). Alles optional.  
>   BrandMark        der Buchstabe auf dem gezeichneten Symbol (Standard: der erste der Marke)  
>   BrandColor       die Farbe des Buchstabens, #rgb oder #rrggbb  
>   BrandAccent      die Farbe der Geschwindigkeitsstreifen daneben  
>   BrandBackground  die Kachel des Symbols und die Theme-Farbe  
>   BrandIcon        eine Bilddatei statt des gezeichneten Zeichens (.svg/.png/.ico)  
>   BrandOgImage     ein PNG/JPEG mit 1200x630 statt der gezeichneten Vorschau  
>   BrandDescription eine Zeile für Linkvorschauen und das Manifest  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
BrandMark=
*/ ?>
```

## BrandColor

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
BrandColor=
*/ ?>
```

## BrandAccent

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
BrandAccent=
*/ ?>
```

## BrandBackground

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
BrandBackground=
*/ ?>
```

## BrandIcon

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
BrandIcon=
*/ ?>
```

## BrandOgImage

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
BrandOgImage=
*/ ?>
```

## BrandDescription

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
BrandDescription=
*/ ?>
```

## PreloadWarmup

> Den Kernel laden und eine Seite im Elternprozess rendern, bevor geforkt wird, damit die Worker  
> sie per Copy-on-Write teilen, statt sie jeweils aufzubauen. 'enabled' oder 'disabled'.  
>
> STATUS: DEAKTIVIERT gelassen. Aktivieren Sie es auf einer Website mit echten Inhalten nicht ohne den  
> Regressionsdurchgang unten; es hat bei jedem Versuch eine andere Funktion beschädigt.  
>
> Das Vorwärmen teilt die vorgewärmte Arena (~21 MB/Worker gegenüber ~209) und rendert mit dem  
> umfassenden Zurücksetzen der statischen Eigenschaften unten korrekte, gestaltete Seiten mit korrekten  
> Brotkrumenpfaden. Aber dieses Zurücksetzen (jede statische Eigenschaft der Benutzerklassen) zerstörte  
> auch den AJAX-Endpunkt von ezjscore (/ezjscore/call/...), sodass die Schaltflächen „Mehr laden“  
> 502 zurückgaben; die Klassen ezjsc\* auszulassen half nicht, der fatale Fehler lag in  
> einem anderen Zurücksetzen. Das ist die vierte verschiedene Funktion, die das Vorwärmen beschädigt hat  
> (falsche Seiten, fehlendes CSS, falscher Brotkrumenpfad, totes „Mehr laden“), jede trat  
> erst auf, nachdem die vorige behoben war. Der Ansatz, im Elternprozess zu rendern, koppelt zu viel  
> vom statischen Anfragezustand von eZ, um ihn durch Zurücksetzen sicher zu teilen; ein brauchbares  
> Vorwärmen braucht einen anderen Mechanismus (z. B. einen Schnappschuss des Pools VOR dem  
> Rendern, eine Änderung der Engine) und eine Regressionssuite, die JEDE Funktion prüft,  
> AJAX eingeschlossen. Die Speicherersparnis ist hier nicht die entscheidende Grenze,  
> der Durchsatz und die Grenzen der Event-Schleife kommen zuerst, daher ist es aus.  
>
> Der Weg hierher war lang. Das Leeren der Anfrage-Globals nach Namen behob drei Fehler  
> (Routing, die Zuordnung der Template-Overrides, das CSS-Flag „geladen“ von ezjscore), aber ein  
> vierter tauchte immer wieder auf: Die Startseite las einen eingefrorenen aktuellen Knoten und zeigte den  
> falschen Brotkrumenpfad. Lecks zu benennen konvergierte nicht, daher macht bin/php/velocity-warmup.php  
> jetzt das Gegenteil: Nach dem Rendern setzt es JEDE statische Eigenschaft der Benutzerklassen  
> auf ihren deklarierten Standard zurück (den Zustand, den ein frisch gestarteter Worker  
> hat) und leert die Anfrage-Globals, wobei nur die reinen Konfigurations- und Typ-  
> register behalten werden (eZINI, eZDataType, eZWorkflowType, eZModule, eZExtension, eZLocale,  
> eZCharsetInfo, eZTextCodec). Diese sind teuer neu aufzubauen und enthalten keinen Anfrage-  
> zustand; sie ebenfalls zurückzusetzen ließ Seiten in Zeitüberschreitungen laufen und verdoppelte den Speicher. Alles andere  
> (Template-Engine, Design-Auflöser, Caches der Inhaltsklassen, der aktuelle Knoten)  
> wird zurückgesetzt, was den Brotkrumenpfad und jedes künftige Leck derselben Art schließlich behob. Die  
> Datenbank wird vor dem Fork geschlossen.  
>
> Prüfen Sie nach jeder Änderung: mehrere verschiedene URLs liefern verschiedene Inhalte, jede  
> trägt ein auflösbares Stylesheet, die Startseite hat KEINEN Brotkrumenpfad, und eine tiefe  
> Seite hat ihren EIGENEN korrekten Brotkrumenpfad. Dieses letzte Paar ist der Test, der  
> das Leck des aktuellen Knotens findet.  
>
> Es rendert eine repräsentative Seite im Elternprozess, damit die Worker die vorgewärmte  
> Zend-Arena per Copy-on-Write erben: gemessen ~17-24 MB privat pro warmem Worker damit,  
> gegenüber ~209 MB ohne. Das Rendern im Elternprozess hinterlässt Anfragezustand von eZ,  
> den der Schnappschuss des Pools sonst als Grundlage jedes Workers einfrieren würde,  
> daher leert bin/php/velocity-warmup.php ihn nach dem Rendern: die Globals des Anfrage-Routings  
> (sonst lieferte jede URL die Startseite), die Zuordnung des Template-Override-Caches  
> (sonst verloren andere Seitentypen ihr CSS) und die statische Eigenschaft „bereits geladen“ des Packers von ezjscore  
> (sonst wurden vom Packer geladene Stylesheets, z. B. der Suchseite, übersprungen).  
> Die Datenbank wird vor dem Fork geschlossen, sodass kein Worker die Verbindung teilt.  
>
> Wenn Sie ändern, was das Vorwärmen rendert oder wie es zurücksetzt, prüfen Sie erneut, BEVOR  
> Sie ihm vertrauen: Mehrere verschiedene URLs müssen verschiedene Inhalte liefern UND jede muss  
> ein auflösbares Stylesheet tragen.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
PreloadWarmup=disabled
*/ ?>
```

## ForkPerRequest

> Wie lange ein Worker lebt.  
>
>   enabled    eine Anfrage pro Worker, dann forkt der Elternprozess einen frischen.  
>              Nichts, was eine Anfrage zurücklässt, kann die nächste erreichen, wie unter  
>              php-fpm, und KeepGlobals hat nichts mehr zu tun. Der Standard  
>              bis 2026-09-30.  
>   disabled   dauerhafte Worker: Jeder bedient Anfragen, bis er die  
>              maxRequests des Servers erreicht hat, wobei der Zustand der Anfrage  
>              dazwischen geleert wird. Dafür gibt es KeepGlobals unten. Der  
>              ausgelieferte Standard seit 2026-09-30 (an diesem Tag genehmigt), aus den  
>              Gründen am Ende dieses Kommentars.  
>
> Gemessen auf einer Installation der Demo-Website, gerenderte Startseite, sechzehn  
> Worker, sechzehn gleichzeitig, die beiden Modi dreimal abgewechselt mit demselben  
> Servercode: 23,6-23,9 Anfragen/s mit einem frischen Worker pro Anfrage gegenüber  
> 17,5-19,8 mit dauerhaften, p99 0,97-1,10 s gegenüber 1,35-1,49 s. Einen  
> benutzten Worker zu leeren kostete mehr, als einen neuen zu forken.  
>
> Braucht einen Server, der in diesem Modus Header übertragen und die Shutdown-  
> Funktionen der Anfrage ausführen kann, qbix-webserver v0.0.4.25 oder später. Bei älteren  
> Versionen wird eine Anmeldung angenommen und dann vergessen: Die Antwort trägt  
> weder das Sitzungs-Cookie noch die Weiterleitung.  
>
> Am 2026-09-30 auf disabled gesetzt, wegen der Zeit, die eine Anfrage braucht, und nicht wegen  
> der Zahl, die sechzehn gleichzeitige schaffen. Ein frischer Worker beginnt jede  
> Anfrage kalt: eine neue Datenbankverbindung (40-70 ms bei Oracle, wo eine  
> dauerhafte in 0,5 ms wiederverwendet wird), leere stat- und realpath-Caches und  
> nichts, was der Kernel pro Prozess behält. Angemeldete Administrationsseiten brauchten 256-284 ms  
> gegenüber 60 ms bei Apache; mit dauerhaften Workern 73-80 ms, und ein Veröffentlichen ging  
> von 2,0 s auf 1,4 s. Der Zustand, den eine Anfrage hinterlässt, wird geleert, nachdem die Antwort  
> hinausgegangen ist, er kostet die nächste Anfrage auf einem ruhigen Server also nichts.  
> Dauerhafte Worker brauchen einen Server, der REQUEST\_TIME\_FLOAT pro Anfrage setzt  
> (der Kernel unterscheidet seine Caches pro Anfrage danach), was er ab der  
> Korrektur der Engine vom 2026-09-30 tut (Velocity v0.0.4.42 oder später).  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
ForkPerRequest=disabled
*/ ?>
```

## StreamTimeout

> Streaming-Antworten (Server-Sent Events). Eine Antwort mit Content-Type  
> text/event-stream oder X-Accel-Buffering: no wird Stück für Stück gesendet, bei jedem  
> flush, auch von den dauerhaften Workern (Velocity ab v0.0.4.48; ältere  
> Engines senden sie am Ende ganz und ignorieren diese Einstellungen).  
>
> StreamTimeout: Sekunden, die ein Stream oder eine Anfrage mit Accept: text/event-stream  
> vor ihrem ersten Byte laufen darf, statt der Zeitgrenze der Anfrage (30 s);  
> dann endet der Stream sauber, und eine EventSource verbindet sich neu. 0 = keine Grenze.  
> Leer = der Standard der Engine, 300.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
StreamTimeout=
*/ ?>
```

## StreamShare

> Jeder offene Stream hält einen Worker. StreamShare ist der Anteil von Workers, den  
> Event-Streams gleichzeitig halten dürfen (Standard der Engine 0.25, mindestens einer);  
> MaxStreams dasselbe als Zahl von Workern, stattdessen verwendet, wenn über 0. Über  
> der Grenze wird ein Event-Stream mit 503 und Retry-After beantwortet, sodass Seiten immer  
> den Rest des Pools behalten. Leer = der Standard der Engine.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
StreamShare=
*/ ?>
```

## MaxStreams

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
MaxStreams=
*/ ?>
```

## RetryWithin

> Sekunden, nachdem eine Anfrage ihren Worker erreicht hat, innerhalb derer ein GET, dessen Worker  
> ohne Antwort gestorben ist, erneut ausgeführt wird, einmal (nie ein Event-Stream, nie eine  
> Antwort, die schon begonnen hatte). Leer = der Standard der Engine, 10.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
RetryWithin=
*/ ?>
```

## GraphQLWebSocketPaths

> GraphQL-Subscriptions über einen WebSocket (das Protokoll graphql-transport-ws;  
> doc/ger-de/funktionen/6.0/graphql.md, „Subscriptions“). Auf diesen Pfaden nimmt  
> der Server den WebSocket selbst an und öffnet für jede Subscription den Event-Stream  
> des Kernels auf demselben Pfad, über die Worker: Jede offene Subscription ist ein  
> Stream (StreamShare und StreamTimeout oben gelten für ihn; endet ein Stream nach  
> graphql.ini SubscriptionMaxDuration, öffnet der Server ihn dort wieder, wo er  
> aufhörte). Kein Pfad: kein WebSocket. Braucht eine Engine mit der Brücke (Velocity  
> nach v0.0.4.58); eine ältere ignoriert diese Einstellungen.  

Standard: eine Liste mit 1: `/api/graphql/subscribe`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
# ein leeres "GraphQLWebSocketPaths[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
GraphQLWebSocketPaths[]
GraphQLWebSocketPaths[]=/api/graphql/subscribe
*/ ?>
```

## GraphQLWebSocketInitTimeout

> Sekunden, die ein Client hat, um connection\_init zu senden (leer: 10), die eine  
> Verbindung ohne Subscription offen bleiben darf (leer: 60), zwischen zwei Pings an  
> den Client (leer: 30; 0: keine), und die Subscriptions, die eine Verbindung halten  
> darf (leer: 20).  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
GraphQLWebSocketInitTimeout=
*/ ?>
```

## GraphQLWebSocketIdleTimeout

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
GraphQLWebSocketIdleTimeout=
*/ ?>
```

## GraphQLWebSocketPingInterval

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
GraphQLWebSocketPingInterval=
*/ ?>
```

## GraphQLWebSocketMaxSubscriptions

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
GraphQLWebSocketMaxSubscriptions=
*/ ?>
```

## EnginePhar

> Die Engine aus einem Archiv statt aus den Dateien auf der Festplatte ausführen.  
>
> Der Bootstrap lädt die Engine nur dann aus dist/engine.phar, wenn die  
> Umgebungsvariable EXP\_ENGINE\_PHAR ein vorhandenes Archiv nennt. Nichts  
> setzte sie früher, das Archiv ließ sich also bauen und untersuchen, aber nie ausführen;  
> es zu testen hieß, die Variable von Hand in das zu exportieren, was den  
> Server startete, und Setup > Systeminformationen konnte immer nur "Individual files  
> on disk" sagen.  
>
>   disabled   die Dateien auf der Festplatte. Der Standard und das, was eine normale Installation will.  
>   enabled    dist/engine.phar, wo immer expPhar es ablegt.  
>   <path>     dieses Archiv, relativ zum Wurzelverzeichnis der Installation oder absolut.  
>
> Bauen oder erneuern Sie das Archiv zuerst mit bin/php/phar.php build. Ein Pfad, der  
> nicht existiert, hält den Server vom Start ab, statt still  
> zurückzufallen, weil ein stilles Zurückfallen nicht von Erfolg zu unterscheiden ist.  
>
> Gemessen auf dieser Installation lag ein Archiv bei warmem Opcode-Cache im Rauschen  
> der Dateien auf der Festplatte. Es lohnt sich für die Verteilung und  
> um zu wissen, dass die Engine genau das ist, was gebaut wurde, nicht für die Geschwindigkeit.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
EnginePhar=disabled
*/ ?>
```

## StaticMaxAge

> Wie lange ein Browser ein Stylesheet, Skript oder Bild behalten darf, ohne erneut  
> zu fragen, in Sekunden. 0 belässt den eigenen Standard des Servers, der Caching erlaubt  
> und den Client dann trotzdem neu prüfen lässt, sodass eine Seite mit 41 Assets  
> bei jedem Aufruf 41 bedingte Anfragen kostet.  
>
> Ein Jahr ist das, was der andere Webserver vor dieser Installation für dieselben  
> Dateien schon sendet. Der Preis ist, dass eine an Ort und Stelle ersetzte Datei nicht  
> bemerkt wird, bis die Lebensdauer abläuft; leeren Sie den Cache eines Browsers oder senken Sie dies,  
> während Sie an einem Stylesheet arbeiten.  

Standard: `31536000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
StaticMaxAge=31536000
*/ ?>
```

## HTTP2

> Mit Clients, die beim TLS-Handshake danach fragen, HTTP/2 sprechen.  
>
> Eine Seite dieser Installation verweist auf 41 Assets. Über HTTP/1.1 holt ein Browser  
> sie in Wellen zu etwa sechs auf einmal; über HTTP/2 teilen sie sich eine  
> Verbindung. Hier gemessen, dauerte die Auslieferung der Assets 393-418 ms gegenüber 85-104 ms  
> über ein Frontend, das es sprach, bei identischen Bytes.  
>
> Das Protokoll wird beim Handshake vereinbart, und ein Client fällt  
> danach nicht zurück; ein Fehler wäre also eine Seite, die nie ankommt, statt einer,  
> die langsam ankommt. Deshalb beantwortet der Server jeden noch offenen Stream  
> mit einer lesbaren Seite, statt still zu schließen, und deshalb ist dies ein Schalter  
> und nicht der Standard.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
HTTP2=enabled
*/ ?>
```

## HTTP2ErrorImage

> Das Bild auf dieser Seite. Leer verwendet das, das der Server zeichnet, das  
> eingebettet ist und keine zweite Anfrage braucht: Die Seite erscheint genau dann, wenn die  
> Verbindung, die sie trägt, unzuverlässig ist. Zeigen Sie auf ein svg, png oder jpg, um  
> es zu ändern, oder setzen Sie errorPage.image des Servers auf "none" für kein Bild.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
HTTP2ErrorImage=
*/ ?>
```

## ResponseHeaders

> Header, die jeder Antwort hinzugefügt werden, die der Server selbst baut: statische Dateien,  
> skalierte Bilder, seine eigenen Fehlerseiten, Weiterleitungen und 304-Antworten. Dieselben  
> vier, die die Frontends Apache und nginx dieser Installation bei jeder  
> Antwort senden ("Header always set", "add\_header ... always"), was der Server bei einer  
> statischen Datei gar nicht sendete. Ein "Name: value" pro Zeile. Ein Header, den eine  
> Antwort schon trägt, wird nie ersetzt oder doppelt gesendet. Eine leere Liste  
> (ResponseHeaders\[\] allein) fügt keine hinzu.  
>
> Framing-Header (Content-Length, Content-Type, Connection, Date ...) lassen sich hier nicht  
> setzen, und Strict-Transport-Security hat eine eigene Einstellung, HSTSMaxAge  
> in \[HTTPSSettings\], weil es nie über einfaches HTTP hinausgehen darf.  

Standard: eine Liste mit 4: `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, `Referrer-Policy: strict-origin-when-cross-origin`, `Permissions-Policy: camera=(), microphone=(), payment=(), usb=()`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
# ein leeres "ResponseHeaders[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ResponseHeaders[]
ResponseHeaders[]=X-Content-Type-Options: nosniff
ResponseHeaders[]=X-Frame-Options: SAMEORIGIN
ResponseHeaders[]=Referrer-Policy: strict-origin-when-cross-origin
ResponseHeaders[]=Permissions-Policy: camera=(), microphone=(), payment=(), usb=()
*/ ?>
```

## ResponseHeadersOnScripts

> Sie auch dem hinzufügen, was index.php, index\_rest.php und index\_treemenu.php  
> antworten, jeden nur, wenn das Skript diesen Header nicht selbst gesendet hat: Eine  
> Seite, die ihr eigenes X-Frame-Options setzt, behält es. Der Kernel sendet diese vier  
> auf seinen Seiten schon; REST-Antworten und das Baummenü tragen keine  
> eigenen. enabled oder disabled.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
ResponseHeadersOnScripts=enabled
*/ ?>
```

## PidFile

Standard: `var/vc/qbix/run/server.pid`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
PidFile=var/vc/qbix/run/server.pid
*/ ?>
```

## LogFile

Standard: `var/vc/qbix/run/console.log`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ServerSettings]
LogFile=var/vc/qbix/run/console.log
*/ ?>
```

## Siehe auch

- [Alle Blöcke von velocity.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/velocity.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
