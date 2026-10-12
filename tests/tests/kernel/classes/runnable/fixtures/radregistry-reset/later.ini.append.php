<?php /* #?ini charset="utf-8"?
# Fixture of RadSurveyDeclaredRegistriesTest RD-06: a later file that empties the list and then adds one; the
# line given before the reset in the same file is dropped too. Sections[] of radreset_third is reset after a value.

[RegistrySettings]
Registries[]=radreset_dropped
Registries[]
Registries[]=radreset_third

[Registry_radreset_third]
Title=Third
Ini=radfixture.ini
Sections[]=Gone_*
Sections[]
Sections[]=Action_*
Entries[Class]=expIniAction

[Registry_radreset_dropped]
Title=Dropped
List=expRADSurvey::NO_SUCH_LIST
*/ ?>
