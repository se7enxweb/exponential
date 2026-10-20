---
title: exp:resetuserpassword
audience: developer, admin
category: examples
summary: Reset a user password with admin authentication or root bypass.
keywords: Konsole, Befehl, exp:resetuserpassword, resetuserpassword, console, command, CLI, resetuserpassword.php
type: generated
generated: exp:help:examples
---
# exp:resetuserpassword

Reset a user password with admin authentication or root bypass.

Skript `bin/php/resetuserpassword.php`, Code in `kernel/private/classes/commands/resetuserpassword.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:resetuserpassword --help
php bin/php/resetuserpassword.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Reset a user password with admin authentication or root bypass.

Admin auth (required unless bypassed with --allow-root-user):
  ./bin/php/resetuserpassword.php -a admin -ap 'adminpass' -u admin -p 'newpass'

Root bypass (resets any user without admin auth):
  ./bin/php/resetuserpassword.php --allow-root-user -u admin -p 'newpass'

Generate a random password:
  ./bin/php/resetuserpassword.php -a admin -ap 'adminpass' -u admin -g -l 16
```

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/resetuserpassword.php`, `kernel/private/classes/commands/resetuserpassword.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
