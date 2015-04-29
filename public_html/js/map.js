/**
 * Created by Richard on 2015-03-08.
 */


function initialize() {
    var mapCanvas = document.getElementById('map-canvas');
    var myLatlng = new google.maps.LatLng(45.320508, -75.896173);


    var mapOptions = {
        center: new google.maps.LatLng(45.320538, -75.902224),
        zoom: 15,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        disableDefaultUI: true
    }
    var map = new google.maps.Map(mapCanvas, mapOptions)

    var marker = new google.maps.Marker({
        position: myLatlng,
        map: map,
        title: 'March Tennis Club'
    });
}
google.maps.event.addDomListener(window, 'load', initialize);

