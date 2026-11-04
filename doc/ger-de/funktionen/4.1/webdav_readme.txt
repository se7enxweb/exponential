WebDAV-Engine von Exponential auf Basis der eZ Components
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. contents::

Einführung
==========

Neu in Exponential 4.1 ist die Portierung der WebDAV-Engine auf die
eZ Components. Der Funktionsumfang der WebDAV-Engine bleibt gleich, sie baut
aber auf der soliden Grundlage der eZ Components mit ihren Unit-Tests, ihrer
Dokumentation und ihrer Erweiterbarkeit auf.

Die Grundlage der eZ Components erleichtert die Wartung und das Hinzufügen
neuer Funktionen wie Sperren (Locking).


Neue Funktionen
===============

Verhalten beim Löschen von Dateien
----------------------------------

Standardmäßig kommen über WebDAV gelöschte Dateien in den Papierkorb. Den
Papierkorb zu leeren ist Aufgabe des Administrators der Site. Sollen die
Dateien direkt entfernt werden (ohne Papierkorb), muss diese Einstellung in
content.ini geändert werden
::

  [RemoveSettings]
  # delete or trash
  DefaultRemoveAction=delete


Kopieren
--------

Die frühere WebDAV-Engine von Exponential konnte Dateien und Ordner nicht
kopieren. Die neue Umsetzung auf Basis der eZ Components kann Dateien und
Ordner kopieren.

Kopieren und Verschieben von Dateien und Ordnern zwischen Siteaccesses wird
nicht unterstützt.


Unterstützte WebDAV-Clients
===========================

Die Webdav-Komponente der eZ Components wurde mit Blick auf die
Kompatibilität mit möglichst vielen WebDAV-Clients entworfen. Sie enthält
Tausende Unit-Tests, die diese Kompatibilität prüfen.

Leider ist die Zahl der unterstützten Clients für die WebDAV-Engine von
Exponential kleiner, vor allem wegen fehlender Sperren und weil für WebDAV
keine Digest-Authentifizierung umgesetzt ist (nur die vorhandene
Basic-Authentifizierung). Diese Punkte sollten in einer späteren Fassung der
WebDAV-Engine behoben werden und mehr Clients ermöglichen.

Unten folgt eine Liste von WebDAV-Clients mit ihren bekannten Problemen. Für
jeden Client ist beschrieben, wie man eine WebDAV-Verbindung zu einer
Exponential-Installation öffnet. In den Beispielen heißt der WebDAV-Host
**webdav.ezp**.


Bitkinex
--------

Unterstützt. Da Microsoft Windows UTF-8-Zeichen in Dateinamen manchmal nicht
richtig kodiert, ist bei Dateinamen mit chinesischen oder japanischen Zeichen
Vorsicht geboten; es können seltsame Zeichen in Dateinamen entstehen.


Konfiguration
'''''''''''''

Dieser WebDAV-Client hat ein bekanntes Problem beim Hochladen von Dateien mit
Leerzeichen im Namen oder mit großgeschriebenen Endungen: Da Exponential
Dateinamen in saubere URL-Aliase umwandelt, erkennt Bitkinex nicht richtig,
dass die Datei hochgeladen wurde, und versucht es erneut, endlos.

Lösung: die Prüfung des erfolgreichen Hochladens abschalten. Klicken Sie mit
der rechten Maustaste auf die WebDAV-Verbindung, wählen Sie Properties, gehen
Sie zum Abschnitt Transfers, entfernen Sie den Haken bei 'Inherit
properties...' und dann bei 'Upload: destination file exists' in der Liste
'Post-transfer data integrity checks'.


Eine Verbindung anlegen
'''''''''''''''''''''''

Gehen Sie im Hauptfenster des Programms zum Menüpunkt Data Source->
New->Http/WebDAV, geben Sie der Verbindung einen Namen, tragen Sie
**webdav.ezp** als Serveradresse ein und Ihren Benutzernamen und Ihr Passwort
im Feld Authorization.


cadaver
-------

Unterstützt. Dieser Kommandozeilen-Client setzt den WebDAV-RFC sehr gut um und
funktioniert bekanntermaßen ohne Probleme mit Exponential.


Eine Verbindung anlegen
'''''''''''''''''''''''

Um eine Verbindung zu öffnen, geben Sie auf der Kommandozeile ein::

  cadaver webdav.ezp

Oder starten Sie cadaver und geben ein::

  open webdav.ezp

Cadaver arbeitet mit WebDAV-Ressourcen über Unix-ähnliche Befehle. 'help'
listet die möglichen Befehle, 'help <befehl>' erklärt einen Befehl, zum
Beispiel 'help get'.


Finder
------

Inhalte lesen wird unterstützt, Inhalte anlegen (hochladen, Ordner anlegen)
noch nicht, weil der WebDAV-Engine Sperren fehlen. Sperren waren für eine
spätere Fassung der WebDAV-Engine geplant; dann sollte auch der Finder
unterstützt werden.

Eine Verbindung anlegen
'''''''''''''''''''''''

Wählen Sie im Finder-Menü Gehe zu->Mit Server verbinden ... und tragen Sie
**http://webdav.ezp** als Serveradresse ein.


Internet Explorer
-----------------

Teilweise unterstützt.

Weil der Internet Explorer erst in den Dateimanager von Microsoft Windows
(Windows Explorer) integriert und dann wieder davon getrennt wurde, gibt es in
Microsoft Windows eigentlich zwei Handler für WebDAV:

- Web Folder Client
- WebDAV Mini-Redirector

Der Web Folder Client wurde vor Windows XP verwendet; er wird aktiviert, wenn
beim Anlegen einer neuen WebDAV-Verbindung eine Portnummer angegeben wird
(siehe nächsten Abschnitt). Der Client muss auf die neueste Version von
http://www.microsoft.com/downloads/details.aspx?familyid=17C36612-632E-4C04-9382-987622ED1D64
aktualisiert werden (auf den 64-Bit-Ausgaben von Microsoft Vista nicht
möglich).

Bekannte Fehler des Web Folder Client:
http://greenbytes.de/tech/webdav/webfolder-client-list.html

Der Mini-Redirector kam mit Windows XP und wird verwendet, wenn eine
WebDAV-Ressource unter einem Laufwerksbuchstaben eingebunden wird (siehe
nächsten Abschnitt).

Bekannte Fehler des WebDAV Mini-Redirector:
http://greenbytes.de/tech/webdav/webdav-redirector-list.html

Da Microsoft Windows UTF-8-Zeichen in Dateinamen manchmal nicht richtig
kodiert, ist bei Dateinamen mit UTF-8-Zeichen Vorsicht geboten; Übertragungen
können fehlschlagen.

Das Hochladen einer Datei kann manchmal mit der Fehlermeldung "Der Parameter
ist falsch" hängen bleiben.


Konfiguration
'''''''''''''

Damit der Internet Explorer eine WebDAV-Verbindung zu einer
Exponential-Installation öffnen kann, sind mehrere Schritte nötig.

1. Basic-Authentifizierung in der Registry einschalten (Start->Ausführen ...,
regedit.exe), unter dem Schlüssel:

HKEY_LOCAL_MACHINE\SYSTEM\CurrentControlSet\Services\WebClient\Parameters

Legen Sie (falls nicht vorhanden) einen DWORD-Wert mit dem Namen UseBasicAuth
und dem Wert 1 an (1 = Basic-Authentifizierung ein, 0 = aus).

Legen Sie (falls nicht vorhanden) einen DWORD-Wert mit dem Namen
BasicAuthLevel und dem Wert 2 an (0 = Basic-Authentifizierung aus, 1 = nur
bei SSL-Verbindungen, 2 = auch ohne SSL).

2. Das Software-Update für Webordner (KB907306) von Microsoft installieren
(auf den 64-Bit-Ausgaben von Microsoft Vista nicht möglich):

http://www.microsoft.com/downloads/details.aspx?familyid=17C36612-632E-4C04-9382-987622ED1D64

3. Windows neu starten.


Eine Verbindung anlegen
'''''''''''''''''''''''

Um im Internet Explorer eine WebDAV-Verbindung anzulegen, wählen Sie
Datei->Öffnen (oder Strg-O). Geben Sie im Dialog **http://webdav.ezp/** ein
und setzen Sie den Haken bei Als Webordner öffnen.

Im Internet Explorer 8 gibt es diese Option nicht mehr. Ohne einen anderen
WebDAV-Client bleibt dann nur, eine Netzwerkverbindung dafür anzulegen (siehe
unten). Der Internet Explorer selbst ist dafür nicht nötig; das Folgende geht
also auch ohne ihn.

Für eine Netzwerkverbindung klicken Sie mit der rechten Maustaste auf
Arbeitsplatz und wählen Netzlaufwerk verbinden (oder gehen zu Netzwerkumgebung
und klicken auf Netzwerkressource hinzufügen). Klicken Sie im Assistenten auf
"Eine andere Netzwerkressource auswählen" (unter Windows Vista und Windows 7
gibt es einen Zwischenschritt mit dem Link "Verbindung mit einer Website
herstellen, auf der Sie Dokumente und Bilder speichern können") und geben Sie
**http://webdav.ezp:port/siteaccess** ein, wobei *siteaccess* der zu
verwendende Siteaccess ist (z. B. **plain_site_user**) und *port* der Port der
WebDAV-Verbindung (standardmäßig **80**). Sie werden nach Benutzername und
Passwort gefragt. Die Verbindung erscheint als Verknüpfung im Arbeitsplatz.
Lässt sie sich später nicht öffnen, ziehen Sie sie aus dem Arbeitsplatz in
ein Fenster des Internet Explorers.

Schlagen diese Schritte fehl, löschen Sie die angelegte Verbindung, starten
den Dienst WebClient neu (Systemsteuerung -> Verwaltung -> Dienste) und
versuchen es noch einmal.

Ein anderer Weg in Windows führt über den (fehlerhaften) WebDAV
Mini-Redirector: Verbinden Sie einen Laufwerksbuchstaben (z. B. Z:) wie oben
über den Assistenten mit dem WebDAV-Server. Wählen Sie auf der ersten Seite
einen Laufwerksbuchstaben, geben Sie **\\webdav.ezp\siteaccess** im Feld
Ordner ein und setzen Sie den Haken bei "Verbindung unter anderem
Benutzernamen herstellen". Möglicherweise werden Sie mehrmals nach denselben
Zugangsdaten gefragt, bevor der Inhalt der WebDAV-Verbindung erscheint. Die
Verbindung erscheint als Laufwerk im Arbeitsplatz; getrennt wird sie mit
Rechtsklick auf Arbeitsplatz und Netzlaufwerk trennen ...


Konqueror
---------

Teilweise unterstützt. Verzeichnisse anlegen funktioniert, Dateien hochladen
in Konqueror 3.5.8 und neuer nicht. Siehe
http://bugs.kde.org/show_bug.cgi?id=166081. Das ist ein Fehler von Konqueror.
Eine Version vor 3.5.8 hat ihn nicht.

Alternativ lässt sich die Klasse ezcWebdavKonquerorCompatibleTransport der
Webdav-Komponente mit diesem Patch korrigieren:
http://issues.ez.no/IssueView.php?Id=11951&ProjectId=3&Anchor=Comment261197
Der Patch behebt das Hochladen in Konqueror 3.5.8 und neuer. Er sollte später
in die eZ Components aufgenommen werden.


Eine Verbindung anlegen
'''''''''''''''''''''''

Geben Sie in Konqueror den Namen des WebDAV-Servers mit dem Protokoll
**webdav://** in die Adressleiste ein::

  webdav://webdav.ezp


Nautilus
--------

Teilweise unterstützt. Eine Datei in ein leeres Verzeichnis hochzuladen,
indem man sie in das geöffnete Verzeichnis zieht, funktioniert nicht.
Ziehen Sie die Datei stattdessen eine Ebene höher auf das Verzeichnis (also
nicht /plain_site_user/Content/folder öffnen, sondern /plain_site_user/Content
und die Datei auf das Verzeichnis "folder" ziehen).


Eine Verbindung anlegen
'''''''''''''''''''''''

Wählen Sie in Nautilus Datei->Mit Server verbinden ..., wählen Sie im
Auswahlmenü Dienst-Typ WebDAV (HTTP) und tragen Sie **webdav.ezp** im Feld
Server ein. Die Verbindung erscheint im Hauptfenster unter Orte mit dem Namen
"webdav.ezp".

Nautilus ab Version 2.24.1 kann bei Mit Server verbinden ... einen Fehler
haben. Funktioniert der Weg oben nicht, geben Sie die URL der
WebDAV-Ressource in einer dieser Formen in die Adressleiste ein::

  dav://webdav.ezp/
  dav://webdav.ezp/siteaccess
  dav://username@webdav.ezp/siteaccess
  dav://username:password@webdav.ezp/siteaccess


Transmit
--------

Unterstützt.


Konfiguration
'''''''''''''

Schalten Sie das Hochladen von **.DS_Store**-Dateien ab: Menü Transmit->
Advanced Preferences ..., auf Advanced Preferences klicken, mit dem Plus eine
neue Einstellung anlegen, **webdav.ezp** eingeben und den Haken bei Transfer
.DS_Store files entfernen.


Eine Verbindung anlegen
'''''''''''''''''''''''

Klicken Sie in Transmit auf den Reiter Connect neben Favorites, tragen Sie
**webdav.ezp** im Feld Server ein, Benutzername und Passwort in die
jeweiligen Felder, wählen Sie WebDAV im Auswahlmenü Protocol und klicken Sie
auf Connect. Die Verbindung erscheint als Reiter im Hauptfenster. Über
Favorites->Add to Favorites ... lässt sie sich zu den Favoriten hinzufügen.


Geplante Funktionen
===================

Sperren
-------

Derzeit gibt es in WebDAV von Exponential noch keine Sperren. Sie sollten in
einem späteren Release hinzukommen, auf Basis der neuen Objektzustände von
Exponential.


Bessere Unterstützung von WebDAV-Clients
----------------------------------------

Derzeit ist die Zahl der unterstützten WebDAV-Clients begrenzt. In einer
späteren Fassung von WebDAV sollten mehr Clients unterstützt werden, vor allem
dank der Sperren.
