<?php /* #?ini charset="utf-8"?

# The registry of ezjscore for the extension point survey (setup/rad, setup/radsurvey/(show)/registries;
# doc/bc/6.0/rad-extension-surface.md "Registries declared in rad.ini"; the format is described in settings/rad.ini).
# Every [ezjscServer_<name>] block of every active extension's ezjscore.ini names a server function class. The survey
# also lists, under "Remote services (expservices)", every service a class of this registry that extends
# expServiceBase declares in its static $services. No empty "Registries[]" line: it would clear the kernel's list.
[RegistrySettings]
Registries[]=ezjscserver

[Registry_ezjscserver]
Title=Server functions of ezjscore
Description=The server functions of ezjscore (ezjscore.ini [ezjscServer_<name>] Class=), each called as ezjscore/call/<name>::<function>.
Doc=doc/bc/6.0/backend_ezjscore_services.md
Ini=ezjscore.ini
Section=ezjscServer_*
Entries[Class]=class

*/ ?>
