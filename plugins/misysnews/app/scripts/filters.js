(function() {
  'use strict';

  angular.module('misysnews')
    .filter('round', RoundFilter)
    .filter('strip', SmartStripFilter)
    .filter('stripHoro', StripHoroscopeFilter)
    .filter('excludeUncomplete', StripNewsFilter);

  function RoundFilter() {
    return function(input) {
      return Math.round(input);
    };
  }

  function SmartStripFilter() {
    return function(input, maxLen) {
      if(!input) return '';
      if(!maxLen) return input;
      if(input.length <= maxLen) return input;

      var str = input.substring(0, maxLen);
      var lastDotIdx = str.lastIndexOf('.');
      str = str.substring(0, lastDotIdx+1);

      return str;
    }
  }

  StripHoroscopeFilter.$inject = ['$filter'];
  function StripHoroscopeFilter($filter) {
    return function(input) {
      return $filter('strip')(input, 200);
    }
  }

  function StripNewsFilter() {
    return function(news) {
      var res = [];

      angular.forEach(news, function(item, idx) {
        if(item.description && item.description.trim() !== ''
          && item.image && item.image.trim() !== '') {
          res.push(item);
        }
      });

      return res;
    }
  }

})();


