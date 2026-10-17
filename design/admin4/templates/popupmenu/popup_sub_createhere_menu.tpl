<!-- Create here menu -->

<div class="popupmenu" id="CreateHereMenu" data-menu-depth="1">
    <template id="menu-classes-template"><a id="menu-item-create-here" href="#" data-menu-form="menu-form-create-here" data-menu-form-values='["classID","%classID%"]'>%name%</a></template>
    <div id="menu-classes" data-menu-variable="%classList%" data-menu-template="menu-classes-template"></div>
</div>

{* Create here form *}
<form id="menu-form-create-here" method="post" action={"/content/action"|ezurl}>
  <input type="hidden" name="NewButton" value="x" />
  <input type="hidden" name="ContentNodeID" value="%nodeID%" />
  <input type="hidden" name="NodeID" value="%nodeID%" />
  <input type="hidden" name="ContentObjectID" value="%objectID%" />
  <input type="hidden" name="ClassID" value="%classID%" />
  <input type="hidden" name="ViewMode" value="full" />
  {* <input type="hidden" name="ContentLanguageCode" value="eng-GB" /> *}
</form>