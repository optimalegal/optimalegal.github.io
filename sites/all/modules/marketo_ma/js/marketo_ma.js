var _____WB$wombat$assign$function_____ = function(name) {return (self._wb_wombat && self._wb_wombat.local_init && self._wb_wombat.local_init(name)) || self[name]; };
if (!self.__WB_pmw) { self.__WB_pmw = function(obj) { this.__WB_source = obj; return this; } }
{
  let window = _____WB$wombat$assign$function_____("window");
  let self = _____WB$wombat$assign$function_____("self");
  let document = _____WB$wombat$assign$function_____("document");
  let location = _____WB$wombat$assign$function_____("location");
  let top = _____WB$wombat$assign$function_____("top");
  let parent = _____WB$wombat$assign$function_____("parent");
  let frames = _____WB$wombat$assign$function_____("frames");
  let opener = _____WB$wombat$assign$function_____("opener");

(function($) {
  Drupal.behaviors.marketo_ma = {
    attach: function(context, settings) {
      if (typeof settings.marketo_ma !== 'undefined' && settings.marketo_ma.track) {
        jQuery.ajax({
          url: document.location.protocol + settings.marketo_ma.library,
          dataType: 'script',
          cache: true,
          success: function() {
            Munchkin.init(settings.marketo_ma.key);
            if (typeof settings.marketo_ma.actions !== 'undefined') {
              jQuery.each(settings.marketo_ma.actions, function() {
                Drupal.behaviors.marketo_ma.marketoMunchkinFunction(this.action, this.data, this.hash);
              });
            }
          }
        });
      }
    },
    marketoMunchkinFunction: function(leadType, data, hash) {
      mktoMunchkinFunction(leadType, data, hash);
    }
  };

})(jQuery);


}
/*
     FILE ARCHIVED ON 15:58:11 Sep 08, 2015 AND RETRIEVED FROM THE
     INTERNET ARCHIVE ON 05:40:57 Jan 12, 2021.
     JAVASCRIPT APPENDED BY WAYBACK MACHINE, COPYRIGHT INTERNET ARCHIVE.

     ALL OTHER CONTENT MAY ALSO BE PROTECTED BY COPYRIGHT (17 U.S.C.
     SECTION 108(a)(3)).
*/
/*
playback timings (ms):
  LoadShardBlock: 128.047 (3)
  PetaboxLoader3.datanode: 141.216 (4)
  CDXLines.iter: 22.393 (3)
  RedisCDXSource: 11.973
  exclusion.robots: 0.237
  load_resource: 53.062
  esindex: 0.017
  PetaboxLoader3.resolve: 29.768
  exclusion.robots.policy: 0.227
  captures_list: 165.75
*/