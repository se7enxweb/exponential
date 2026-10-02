{* admin4's version of ezmbpaex's users' view (override users_class_group_override: user and user group objects).
   ezmbpaex renders those with the view cache off -- kept here -- and its own template was a copy of the old node
   view; admin4 sits above the extension's admin folder in the design order, so this one is used and the users
   get the admin4 node view. The body is shared with node/view/full.tpl; including full.tpl itself would come
   back here through the same override. *}
{set-block scope=root variable=cache_ttl}0{/set-block}
{include uri='design:node/view/a4_full_body.tpl'}
