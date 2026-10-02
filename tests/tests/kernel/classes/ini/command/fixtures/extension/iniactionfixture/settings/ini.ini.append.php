<?php /* #?ini charset="utf-8"?

# What an extension writes to add an exp:ini action and a scope provider
# (guide doc/bc/6.0/console-exp-ini.md, "Adding an action").
[IniCommandSettings]
Actions[count]=iniActionFixtureCount
ActionAliases[n]=count
# switches the built-in copy action off
Actions[copy]=
ScopeProviders[]=iniActionFixtureScopeProvider
*/ ?>
