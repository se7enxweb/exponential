{* Make sure to normalize floats from db  *}
{def $latitude  = $attribute.content.latitude|explode(',')|implode('.')
     $longitude = $attribute.content.longitude|explode(',')|implode('.')}
{run-once}
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor={ezini('GMapSettings', 'UseSensor', 'ezgmaplocation.ini')}"></script>
<script type="text/javascript">
{literal}
function eZGmapLocation_MapView( attributeId, latitude, longitude )
{
    var zoommax = 13;

    if( latitude && longitude )
    {
        var startPoint = new google.maps.LatLng( latitude, longitude );
        var zoom = zoommax;

    }
  else
  {
      var startPoint = new google.maps.LatLng( 0, 0 );
      var zoom = 0;
  }

  var map = new google.maps.Map( document.getElementById( 'ezgml-map-' + attributeId ), { center: startPoint, zoom : zoom, mapTypeId: google.maps.MapTypeId.ROADMAP } );
    var marker = new google.maps.Marker({ map: map, position: startPoint });
}
{/literal}
</script>
{/run-once}

{if $attribute.has_content}
<script type="text/javascript">
<!--

if ( window.addEventListener )
    window.addEventListener('load', function(){ldelim} eZGmapLocation_MapView( {$attribute.id}, {first_set( $latitude, '0.0')}, {first_set( $longitude, '0.0')} ) {rdelim}, false);
else if ( window.attachEvent )
    window.attachEvent('onload', function(){ldelim} eZGmapLocation_MapView( {$attribute.id}, {first_set( $latitude, '0.0')}, {first_set( $longitude, '0.0')} ) {rdelim} );

-->
</script>
<div id="ezgml-map-{$attribute.id}" style="width: 100%; height: 330px;">
{/if}
