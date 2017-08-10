(function() {
  'use strict';

  angular.module('misysnews')
    .controller('BarCtrl', BarController);

  BarController.$inject = ['$scope', '$interval', '$timeout', '$http', 'Feed','$rootScope'];


  function BarController($scope, $interval, $timeout, $http, Feed,$rootScope){
    var self = this;
    var weekDays = ['DIM', 'LUN', 'MAR', 'MER', 'JEU', 'VEN', 'SAM'];
    var weatherMap = {
      'clear sky' : 'day-sunny',
      'sky is clear' : 'day-sunny',
      'few clouds' : 'day-sunny-overcast',
      'overcast clouds' : 'day-sunny-overcast',
      'scattered clouds' : 'day-cloudy',
      'broken clouds' : 'cloudy',
      'shower rain' : 'showers',
      'rain' : 'rain',
      'heavy rain' : 'rain',
      'very heavy rain' : 'rain',
      'light rain' : 'rain-mix',
      'moderate rain' : 'rain-mix',
      'heavy intensity rain' : 'rain',
      'thunderstorm' : 'thunderstorm',
      'snow' : 'snow',
      'light snow' : 'snow',
      'mist' : 'fog'
    };

    self.refreshHour = function() {
      var now = new Date();
      $scope.currentDate = weekDays[now.getDay()] + ' ' + now.getDate();
      var min = now.getMinutes();
      var hours = now.getHours();
      $scope.currentTime = (hours < 10 ? '0' : '') + hours + ':' + (min < 10 ? '0' : '') + min;
    };

    self.slideBox = function() {
      jQuery('#slidebox').scrollbox({
        delay: 10
      });
    };

    self.getAnniversaries = function(){
     //ajaxRequestWithParameter("GET",'wp-content/plugins/misysnews/anniversaries.json',{},
     $scope.anniversaries = [];
     $http({
       method:'GET',
       url:'wp-content/plugins/misysnews/anniversaries.json'
     }).then(
      function success(data) {
        var now = new Date();
        var month = now.getMonth()+1;
        month = (month <= 9) ? '0' + month : month;
        var date = now.getDate();
        date = (date <= 9) ? '0' + date : date;
        var key = date + '.' + month;
        var todayAnniversaries = [];
        for (var i = 0; i < data.data.length; i++){
          if(data.data[i][0] == key){
            todayAnniversaries.push( data.data[i][1] );
          }
        }
        $scope.anniversaries = todayAnniversaries;
      }
    );
  };

    self.getMeteo = function() {
      $http({
        method: 'JSONP',
        url: 'http://api.openweathermap.org/data/2.5/forecast/daily?q=Paris,%20FR&mode=json&units=metric&cnt=7&callback=JSON_CALLBACK&APPID=cd9da33c9e2fda0a1000c7c30845a593'
      }).then(
        function success(data) {
          var meteo = [];

          var time = (new Date()).getTime();
          var oneDay = 24 * 60 * 60 * 1000;
          angular.forEach(data.data.list, function(value, idx) {
            var newDay = new Date(time);
            var dayMeteo = {
              date : weekDays[newDay.getDay()] + ' ' + newDay.getDate(),
              weather : weatherMap[value.weather[0].description],
              temperatures : [value.temp.morn, value.temp.max]
            };
            time += oneDay;
            meteo.push(dayMeteo);
          });

         $scope.meteo = meteo;
        },
        function error(data) {
        }
      );
    };
    self.getHoroscope2 = function() {
      $http({
        method: 'GET',
        url: 'wp-content/plugins/misysnews/horoscope.php'
      }).then(
        function success(data) {
          if(data.data.length == 12){
            var horo = {};
            angular.forEach(data.data, function(value, idx) {
              horo[value.sign] = value.text
            });

            $scope.horoscope = horo;
          }
        },
        function error(data) {
          //self.getHoroscope();
        });
      };

    self.getMenus = function() {
      $http.get('wp-content/plugins/misysnews/weekmenu.php').then(
        function success(data) {
          var d = new Date();
          var n = d.getDay();
          if(n >= 1 && n <= 5) {
            $scope.menus = data.data[n-1];
          }else{
            $scope.menus = ['Rien. C\'est le weekend.'];
          }
        },
       function fail(reason) {
                $scope.menus = ['/!\\ Impossible de récupérer le menu.'];
        });
      };






    self.refreshAllBarFeeds = function() {
      self.getMeteo();
      self.getHoroscope2();
      self.getAnniversaries();
      self.getMenus();
    };


    self.refreshAllBarFeeds();

    $interval(self.refreshHour, 1000);
    $interval(self.refreshAllBarFeeds, 60*60*1000);
    $timeout(self.slideBox, 20000);
  }

})();
