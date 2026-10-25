{* The request's own fields, sent again with the answer of "Verify it's you" (expReauth::flatten()), so the action runs as
   it was first sent; for a GET only the mark that it is asked for again. Variables: replay (name, value), replay_get. *}
{if $replay_get}<input type="hidden" name="ExpReauthReplayGet" value="1" />{/if}
{foreach $replay as $field}<input type="hidden" name="{$field.name|wash}" value="{$field.value|wash}" />
{/foreach}
