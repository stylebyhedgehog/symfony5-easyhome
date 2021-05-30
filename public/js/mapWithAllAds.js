ymaps.ready(init);

function init() {
    myMap = new ymaps.Map('map', {
        center: [55.8021303942946, 49.106238365611695],
        zoom: 12,
        controls: ['zoomControl']
    });

    $('.address_field').each(function () {
        let currentElement = $(this);
        let id_currentElement = currentElement.attr('id')
        let address = currentElement.text()
        let myGeocoder = ymaps.geocode(address);

        myGeocoder.then(function (geoObject) {

                let coord = geoObject.geoObjects.properties.get('metaDataProperty').GeocoderResponseMetaData.Point.coordinates;
                let norm_coord = [coord[1], coord[0]]
                console.log(currentElement.parent().prop('outerHTML'))
                var placemark = new ymaps.Placemark(norm_coord, {
                    balloonContentHeader: currentElement.parent().prop('outerHTML'),
                    balloonContentBody: $('#carouselExampleIndicators' + id_currentElement).prop('outerHTML'),
                    balloonContentFooter:
                        '<div class="d-flex justify-content-between align-items-center">' +
                        $('#updateDate' + id_currentElement).prop('outerHTML') +
                        $('#price' + id_currentElement).prop('outerHTML') +
                        '</div>'
                });
                myMap.geoObjects.add(placemark);

            },
            function (err) {
                console.log("ошибка")
            });


    })

}

