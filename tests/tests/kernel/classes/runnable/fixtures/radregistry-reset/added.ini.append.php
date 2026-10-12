<?php /* #?ini charset="utf-8"?
# Fixture of RadSurveyDeclaredRegistriesTest RD-06: an extension that adds without a reset line keeps the others.

[RegistrySettings]
Registries[]=radreset_fourth

[Registry_radreset_fourth]
Title=Fourth
Ini=radfixture.ini
Section=Action_*
Entries[Class]=expIniAction
*/ ?>
