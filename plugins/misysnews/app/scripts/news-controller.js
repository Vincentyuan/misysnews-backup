(function() {
  'use strict';

  angular.module('misysnews')
    .controller('NewsCtrl', NewsController);

  NewsController.$inject = ['$window', '$q', '$location', '$timeout', '$http', 'Feed','$rootScope'];

  function NewsController($window, $q, $location, $timeout, $http, Feed,$rootScope) {
    // CONSTANT
    var SLIDE_DURATION = 20000;
    var FEEDS_URL = 'wp-content/plugins/misysnews/feeds.json.php';

    var self = this;
    // Loading status enum
    self.STATUS = {
      NOT_LOADED: 'not loaded',
      LOADING: 'loading',
      LOADED: 'loaded',
      FAILED: 'failed'
    };

    // last retrieved version of feeds definition (Metadata)
    self.feedsDefinition = null;
    // model[slide.id] = {..slide..} (Data)
    self.model = [];
    // categories[idx] = [{..category..}]
    self.categories = [];

    // Holds the URL of the current slide background image displayed
    self.currentBackgroundImageURL = null;
    // Holds the current category title
    self.currentCategoryTitle = 'Loading...';
    // Holds the current slide title
    self.currentSlideTitle = '';
    // Live status when loading feeds
    self.feedStatuses = {};
    // version information
    self.versionData = {};
    self.versionData.version = "";
    self.versionData.serialNumber = "";


    // Holds the retrieved model of the bottom bar {meteo : true, horoscope : true, anniversaries : false ...}
    self.barConfig = null;

    // fetch feeds definition, initialize data, create layout and launch scenario
    self.initialize = function() {
      self.updateFeedsDefinition(FEEDS_URL).then(
        function success(data) {
          self.barConfig = data.data.bar;
          self.categories = self.getValidFeedsData(data.data.feeds);
          $timeout(function(){
            self.createFullLayout();
            self.scenario();
          }, 2000);
        },
        function fail(data){
          // alert("error?");
        }
        );
    }
    // function to check the feeds is valid or not
    self.getValidFeedsData = function(feeds){
      feeds.filter(function(value){return value.displayFrontend});
            // self.checkValidTime(feeds);
      for(var i = 0; i < feeds.length ; i++){
        for(var j = 0; j< feeds[i].values.length ; j++){
          if(self.checkValidTime(feeds[i].values[j])){
            // this means it's a valid feeds.
          }else{
            feeds[i].values.splice(j,1);
            j--;
          }
        }
        if(feeds[i].values.length == 0){
          feeds.splice(i,1);
          i--;
        }
      }
      return feeds;
    }
    // function called during the feed loaded.
    // check the current time is valid or not if not valid delete it from array
    self.checkValidTime = function(feedItem){
      var currentDate = self.getDateFromJSDateObj(new Date());

      if( typeof feedItem.valid_time_to =='undefined' || feedItem.valid_time_to == "" ||(currentDate >= feedItem.valid_time_from && currentDate <= feedItem.valid_time_to)){
        return true;
      }else{
        return false;
      }
    }
    self.getDateFromJSDateObj = function (obj){
      var mm = obj.getMonth() + 1; // getMonth() is zero-based
        var dd = obj.getDate();

        return [obj.getFullYear(),
                (mm>9 ? '' : '0') + mm,
                (dd>9 ? '' : '0') + dd
               ].join('');
    }
    self.getVersion = function(){
      return $http({
        method:"GET",
        url:"wp-content/plugins/misysnews/checkversion.php",
        params: {
           version:self.versionData.version,
           serialnumber:self.versionData.serialNumber
         }
      });
    };
    // resolved only if feeds definiton has changed
    self.updateFeedsDefinition = function(url) {
      var deferred = $q.defer();

      var mode = $location.search().mode;
      if(mode && mode !== ''){
        url += '?mode=' + mode;
      }

      $http.get(url).then(
        function success(data) {
          if(!angular.equals(data, self.feedsDefinition)) {
            self.feedsDefinition = data;
            deferred.resolve(self.feedsDefinition);
          }else{
            deferred.reject('no change')
          }
        },
        function error(reason) {
          deferred.reject(reason);
        }
      );

      return deferred.promise;
    }

    // Must be called when DOM ready
    self.createFullLayout = function() {
      jQuery('#fullpage').fullpage({
        verticalCentered: false,
        resize : true,
        scrollingSpeed: 700,
        easing: 'linear',
        menu: false,
        navigation: false,
        slidesNavigation: false,
        continuousVertical: true,
        loopHorizontal: true,
        autoScrolling: true,
        scrollOverflow: false,
        css3: true,
        animateAnchor: true,
        afterLoad: function(anchorLink, index){
          var currentCategory = self.categories[index-1];
          var currentSlide = currentCategory.values[0];

          // Sync view's properties after category changed, or single slide category changed (no afterSlideLoad called bug?)
          self.currentBackgroundImageURL = self.getBackgroundImage(currentSlide);
          self.currentCategoryTitle = currentCategory.name;
          self.currentSlideTitle = currentSlide.id;
        },
        afterSlideLoad: function(anchorLink, index, slideAnchor, slideIndex){
          var currentCategory = self.categories[index-1];
          var currentSlide = currentCategory.values[slideIndex];

          // Sync view's properties after slide changed
          self.currentBackgroundImageURL = self.getBackgroundImage(currentSlide);
          self.currentCategoryTitle = currentCategory.name;
          self.currentSlideTitle = currentSlide.id;
        }
      });
    }

    // Fetch all feeds, time auto switch of slides for one round and call itself recursively
    self.scenario = function() {
      self.getVersion().then(function success(data){
        if(self.versionData.version==data.data.version &&self.versionData.serialNumber == data.data.serialnumber){
          //this means this is already the latest version
        }else {
          location.reload();
        }
      },function error(response){
        location.reload();
      });
      angular.forEach(self.categories, function(category, idx) {
        angular.forEach(category.values, function(slide, idx) {
          if(self.checkValidTime(slide)){
            self.getFeed(slide);
          }else{
            location.reload();
          }

        });
      });

      var timer = 5500;
      var categoryCount = 0;
      var slideCount = 0;
      var totalSlideCount = 0;
      var lastSlides = [];
      for (var i = 0; i < self.categories.length; i++) {
        for (var j = 0; j < self.categories[i].values.length; j++) {
          totalSlideCount++;
          timer += SLIDE_DURATION;
          $timeout(function(){
            slideCount++;
            jQuery.fn.fullpage.moveSlideRight();

            if(lastSlides[categoryCount] == slideCount) {
              slideCount = 0;

              if(categoryCount == lastSlides.length-1) {
                categoryCount = 0;
              }else{
                categoryCount++;
              }

              jQuery.fn.fullpage.moveSectionDown();
            }
          }, timer);
        }
        lastSlides[i] = j;
      }

      $timeout(function() {
        /*self.updateFeedsDefinition(FEEDS_URL).then(
          function success(data) {
            self.barConfig = data.data.bar;
            self.categories = data.data.feeds;
            self.scenario();
          },
          function notChangedOrFailed(reason) {
            self.scenario();
          });*/

        self.scenario();

      }, SLIDE_DURATION*(totalSlideCount));
    };

    // Fetch slide.feedURL, sync loading status and update model.
    self.getFeed = function(slide) {
      if( angular.isUndefined(self.feedStatuses[slide.id]) ) {
        self.feedStatuses[slide.id] = {
          status: self.STATUS.NOT_LOADED,
          lastRefresh: null,
          lastError: null
        }
      }

      if(slide.feedURL && slide.feedURL !== '') {

        self.feedStatuses[slide.id].status = self.STATUS.LOADING;
        self.feedStatuses[slide.id].lastRefresh = new Date();

        var phpFetchURL = 'wp-content/plugins/misysnews/getURL.php?url=' + slide.feedURL;
        Feed.fetch(phpFetchURL).then(
          function success(data) {
            self.model[slide.id] = data;
            self.feedStatuses[slide.id].status = self.STATUS.LOADED;
          },
          function error(data) {
            self.feedStatuses[slide.id].status = self.STATUS.FAILED;
            self.feedStatuses[slide.id].lastError = data.data;
          });
      }
    };

    // [BOUND] Used in misysnews page template to update page background with Hero image
    self.getBackgroundImage = function(slide) {
      if(!slide) return '';

      if(angular.isDefined(slide.background)) {
        return slide.background;
      }else{
        if(self.model && self.model[slide.id] && self.model[slide.id][0])
        return self.model[slide.id][0].image;
      }

    };

    // [BOUND] Display feeds loading status
    self.getLoadingStatus = function() {
      var all = 0;
      var ok = 0;
      var ko = 0;
      angular.forEach(self.feedStatuses, function(value, key) {
        switch(value.status) {
          case self.STATUS.FAILED: ko++;
          case self.STATUS.LOADED: ok++;
          default: all++;
        }
      });

      if(ok + ko < all) {
        return 'Loading ' + all + ' feeds (' + ok + ' OK / ' + ko + ' failed).';
      }else{
        if(ko > 0) {
          return 'Done loading ' + all + ' feeds (' + ko + ' failed).';
        }
        return '';
      }
    };

    // get version then start the website
    self.getVersion().then(function success(data){
      if(data.data){
         var version = data.data.version;
         var serialnumber = data.data.serialnumber;
         self.versionData.version=version;
         self.versionData.serialNumber=serialnumber;
         self.initialize();
      }
    },function error(response){
      alert("error ? get version?")
    });


  }
})();
