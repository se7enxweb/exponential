{def $startyear=1998}
<footer class="footer-main">
    <div class="container">
      <div class="copyright-text">
         {if $pagedesign.data_map.hide_powered_by.data_int|not}
             Powered by <a href="https://exponential.se7enx.com" title="Exponential CMS Open Source Web Content Management">Exponential CMS{* Open Source Web Content Management *}</a>. 
         {/if}

	 Copyright &#169; {$startyear} - {currentdate()|datetime( 'custom', '%Y' )} {ezini( 'SiteSettings', 'SiteName', 'site.ini' )}. All rights reserved.
       </div>

        {if $pagedesign.data_map.hide_powered_by.data_int|not}
            <div class="poweredby-text">
                Powered by <a href="/ezinfo/about" title="Exponential CMS Open Source Web Content Management">Exponential</a>
            </div>
	{/if}

{*
      <div class="poweredby-text">
        {if $pagedesign.data_map.footer_text.has_content}
            {$pagedesign.data_map.footer_text.content}
        {/if}
      </div>
*}
{*
      <div class="hosting-sponsor">
          Proudly hosted by
          <br />
          <a href="http://se7enx.com" title="se7enx.com">se7enx.com</a>
          <br />
          <a href="#top" class="footer-logo">
            <img src={'bc-icon.png'|ezimage} alt="Se7enx" border="0" width="65" height="65" style="position:relative;top:3px;padding-right:1px;" />
          </a>
        </div>
*}
    </div>
</footer>
