(function(){

  angular.module('misysnews')
    .factory('Feed', FeedFactory);

  FeedFactory.$inject = ['$http', '$q'];

  function FeedFactory($http, $q) {
    var Feed = {
      title: '',
      url : '',
      rawData : null,
      feedData : null,

      fetch : fetch,
      transform : transform,
      save : save
    };

    function fetch(url) {
      var deferred = $q.defer();
      $http({
          method: 'GET',
          url: url
      }).then(function(data) {
          Feed.rawData = data;
          Feed.feedData = Feed.transform();
          deferred.resolve(Feed.feedData);
      }, function(data) {
        deferred.reject(data);
      });

      return deferred.promise;
    }

    function transform() {
      var x2js = new X2JS();
      var obj = x2js.xml_str2json(Feed.rawData.data);

      var feedData = [];
      var items = obj.rss.channel.item;
      if(!angular.isArray(items)) {
        items = [items];
      }
      angular.forEach(items, function(item, idx) {
        // Extract description
        var cleanDescription = item.description.toString().replace(/<\/?[^>]+>/gi, '');

        // Extract publication date
        var pubDate = new Date(item.pubDate);

        // Extract image
        var enclosureUrl;
        // 1. normal
        if(item.enclosure) {
          enclosureUrl = item.enclosure._url;
        }
        // 2. TechCrunch
        else if(item.content && item.content[0] && item.content[0]._url) {
          enclosureUrl = item.content[0]._url;
        }

        feedData.push({
          title: item.title.toString(),
          description: cleanDescription,
          image: enclosureUrl,
          pubDate: pubDate,
          link: item.link
        });
      });

      return feedData;
    }

    function save() {

    }

    return Feed;
  }
})();
