WebDAV in Exponential
~~~~~~~~~~~~~~~~~~~~~

Serverkonfiguration
===================

Die Exponential-Installation muss so konfiguriert sein, dass alle
WebDAV-Anfragen über index_webdav.php laufen (bis 6.0.14 hieß es webdav.php;
webdav.php leitet jetzt nur noch weiter und wird in einer späteren
Hauptversion entfernt, siehe
doc/ger-de/bc/6.0/webdav-einstiegsskript-index-webdav.md).

Wie WebDAV in einer Exponential-Installation eingeschaltet wird, war unter
http://ez.no/doc/ez_publish/technical_manual/4_0/features/webdav beschrieben.

Außerdem muss WebDAV in webdav.ini eingeschaltet sein::

  [GeneralSettings]
  EnableWebDAV=true

Für jeden Siteaccess sollte es eine webdav.ini.append.php geben, die den
Startknoten des Siteaccesses nennt::

 [GeneralSettings]
 StartNode=2

Das WebDAV-Protokoll wird so eingeschaltet (in webdav.ini oder in
Überschreibungen)::

 [GeneralSettings]
 Logging=enable

Die WebDAV-Protokolle liegen in var/log/webdav.log (von index_webdav.php) und
var/log/<siteaccess>/log/webdav.log (von ezwebdavcontentbackend.php).

Bei mehreren Siteaccesses sollten die Einstellungen PathPrefix und
PathPrefixExclude in site.ini und ihren Überschreibungen verwendet werden
(PathPrefix blendet diesen Teil am Anfang des URL-Alias aus;
PathPrefixExclude nennt URLs, die davon ausgenommen sind)::

  [SiteAccessSettings]

  # Hides this part from the start of the url alias
  PathPrefix=

  # Which URLs to exclude from being affected by PathPrefix setting.
  # URLs containing the specified texts after siteaccess name will not be affected by PathPrefix
  PathPrefixExclude[]
  #PathPrefixExclude[]=media

Standardmäßig kommen über WebDAV gelöschte Dateien in den Papierkorb. Den
Papierkorb zu leeren ist Aufgabe des Administrators der Site. Sollen die
Dateien direkt entfernt werden (ohne Papierkorb), muss diese Einstellung in
content.ini geändert werden
::

  [RemoveSettings]
  # delete or trash
  DefaultRemoveAction=delete

Wie Dateinamen beim Hochladen gebildet werden, ändern Sie mit der Einstellung
TransformationGroup in site.ini und ihren Überschreibungen (urlalias_iri für
URL-Aliase neuer Art mit Unicode, urlalias_compat für die alte Art)::

  [URLTranslator]
  TransformationGroup=urlalias
  # Uncomment this to get the new-style url aliases with Unicode support
  #TransformationGroup=urlalias_iri
  # Uncomment this to get the old-style url aliases
  #TransformationGroup=urlalias_compat


Clientkonfiguration
===================

In den Beispielen heißt der WebDAV-Host webdav.ezp.

BitKinex
--------

Die Prüfung des erfolgreichen Hochladens abschalten: mit der rechten
Maustaste auf die WebDAV-Verbindung klicken, Properties wählen, zum Abschnitt
Transfers gehen, den Haken bei 'Inherit properties...' entfernen und dann bei
'Upload: destination file exists' in der Liste 'Post-transfer data integrity
checks'.


Cadaver
-------

Auf der Kommandozeile eingeben::

  cadaver webdav.ezp

Oder cadaver starten und eingeben::

  open webdav.ezp

Cadaver arbeitet mit WebDAV-Ressourcen über Unix-ähnliche Befehle. 'help'
listet die möglichen Befehle, 'help <befehl>' erklärt einen Befehl, zum
Beispiel 'help get'.


Finder
------

@todo Beschreiben, wie im Finder eine WebDAV-Verbindung eingerichtet wird.


Internet Explorer
-----------------

@todo Beschreiben, wie im IE eine WebDAV-Verbindung eingerichtet wird.

Basic-Authentifizierung in der Registry einschalten (Start->Ausführen ...,
regedit.exe), unter dem Schlüssel:

HKEY_LOCAL_MACHINE\SYSTEM\CurrentControlSet\Services\WebClient\Parameters

Legen Sie (falls nicht vorhanden) einen DWORD-Wert mit dem Namen UseBasicAuth
und dem Wert 1 an (1 = Basic-Authentifizierung ein, 0 = aus).


Konqueror
---------

In Konqueror genügt es, den Namen des WebDAV-Servers mit dem Protokoll
webdav:// einzugeben::

  webdav://webdav.ezp


Nautilus
--------

@todo Beschreiben, wie in Nautilus eine WebDAV-Verbindung eingerichtet wird.


Aktuelle Probleme
=================

Inhalte zwischen 2 Siteaccesses verschieben oder kopieren
---------------------------------------------------------

Das ist noch nicht möglich (es kommt 409 Conflict zurück); manche Clients wie
BitKinex verhalten sich aber seltsam und versuchen es erneut, wobei die
Quelldatei verschwindet und die Zieldatei nicht erscheint.




..
   Local Variables:
   mode: rst
   fill-column: 79
   End:
   vim: et syn=rst tw=79
