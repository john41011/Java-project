let map;
let markers = [];
let geocoder;
let ps;

window.addEventListener("DOMContentLoaded", () => {
  if (typeof kakao === 'undefined') {
    alert("❌ 카카오 지도 API가 로드되지 않았습니다.");
    return;
  }

  const mapContainer = document.getElementById('map');
  if (!mapContainer) {
    alert("❌ #map 요소가 존재하지 않습니다.");
    return;
  }

  geocoder = new kakao.maps.services.Geocoder();
  ps = new kakao.maps.services.Places();

  // 사용자 위치 기반으로 지도 초기화
  navigator.geolocation.getCurrentPosition((pos) => {
    const lat = pos.coords.latitude;
    const lng = pos.coords.longitude;
    initMap(lat, lng);
    searchPlaces('편의점', lat, lng);
  }, () => {
    // 위치 정보 실패 시 기본 위치
    const lat = 37.5665, lng = 126.9780;
    initMap(lat, lng);
    searchPlaces('편의점', lat, lng);
  });
});

function initMap(lat, lng) {
  const container = document.getElementById('map');
  const options = {
    center: new kakao.maps.LatLng(lat, lng),
    level: 4
  };
  map = new kakao.maps.Map(container, options);
}

function searchPlaces(keyword, lat, lng) {
  clearMarkers();
  const coord = new kakao.maps.LatLng(lat, lng);
  const options = { location: coord, radius: 3000 };

  ps.keywordSearch(keyword, function (results, status) {
    if (status === kakao.maps.services.Status.OK) {
      results.forEach(place => {
        const marker = new kakao.maps.Marker({
          map: map,
          position: new kakao.maps.LatLng(place.y, place.x)
        });
        markers.push(marker);
      });
    } else {
      console.warn(`🔍 '${keyword}' 검색 실패:`, status);
    }
  }, options);
}

function searchPlacesMulti(keywords, lat, lng) {
  clearMarkers();
  const coord = new kakao.maps.LatLng(lat, lng);
  const options = { location: coord, radius: 3000 };

  keywords.forEach(keyword => {
    ps.keywordSearch(keyword, function (results, status) {
      if (status === kakao.maps.services.Status.OK) {
        results.forEach(place => {
          const marker = new kakao.maps.Marker({
            map: map,
            position: new kakao.maps.LatLng(place.y, place.x)
          });
          markers.push(marker);
        });
      } else {
        console.warn(`🔍 '${keyword}' 검색 실패:`, status);
      }
    }, options);
  });
}

function clearMarkers() {
  markers.forEach(marker => marker.setMap(null));
  markers = [];
}

function searchAddress() {
  const keyword = document.getElementById('searchInput').value.trim();
  if (!keyword) return;

  geocoder.addressSearch(keyword, function (result, status) {
    if (status === kakao.maps.services.Status.OK) {
      const coords = new kakao.maps.LatLng(result[0].y, result[0].x);
      map.setCenter(coords);
      searchPlaces('편의점', result[0].y, result[0].x);
    } else {
      alert("주소를 찾을 수 없습니다.");
    }
  });
}

function filterByBrand(brand) {
  const center = map.getCenter();
  const lat = center.getLat();
  const lng = center.getLng();

  if (Array.isArray(brand)) {
    searchPlacesMulti(brand, lat, lng);
  } else {
    searchPlaces(brand, lat, lng);
  }
}
