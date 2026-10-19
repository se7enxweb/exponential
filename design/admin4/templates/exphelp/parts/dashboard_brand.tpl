{* The Exponential logo above "Help center" on the help dashboard, so a reader knows which product the help is for.
   admin4 (and admin4l through its chain): the header's own drawing (parts/a4_logo.tpl), inline, so its letters take
   the ink color and its squares the surface and brand tokens in light and dark mode alike. The drawings inside are
   hidden from assistive technology; this wrapper is the image and carries its name. Its size is fixed in help.css
   (.a4-help-brand), so nothing moves while the page loads. design/admin has its own copy of this part. *}
<p class="a4-help-brand" role="img" aria-label="Exponential">{include uri='design:parts/a4_logo.tpl'}</p>
