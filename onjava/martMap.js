let map, geocoder, places;

window.onload = () => {
    if (typeof kakao === 'undefined') {
        alert("카카오 지도 API를 불러올 수 없습니다.");
        return;
    }
    
    geocoder = new kakao.maps.services.Geocoder();
    places = new kakao.maps.services.Places();
    
    // 기본 메시지 표시
    const container = document.getElementById('flyerContainer');
    if (container) {
        container.innerHTML = '<p>지도에서 마트를 선택하세요.</p>';
    }
    
    navigator.geolocation.getCurrentPosition((pos) => {
        const lat = pos.coords.latitude;
        const lng = pos.coords.longitude;
        initMap(lat, lng);
    }, () => {
        initMap(37.5665, 126.9780); // 기본: 서울
    });
};

function initMap(lat, lng) {
    const container = document.getElementById('map');
    map = new kakao.maps.Map(container, {
        center: new kakao.maps.LatLng(lat, lng),
        level: 4
    });
    searchMarts(lat, lng);
}

function searchMarts(lat, lng) {
    const coord = new kakao.maps.LatLng(lat, lng);
    const options = { location: coord, radius: 3000 };
    
    places.keywordSearch("마트", (results, status) => {
        if (status === kakao.maps.services.Status.OK) {
            results.forEach(place => {
                const marker = new kakao.maps.Marker({
                    map,
                    position: new kakao.maps.LatLng(place.y, place.x)
                });
                
                kakao.maps.event.addListener(marker, 'click', () => {
                    showMartInfo(place);
                    loadFlyers(place.place_name); // 전단지 로드 추가
                });
            });
        }
    }, options);
}

function showMartInfo(place) {
    const martInfo = document.getElementById('martInfo');
    const martImage = document.getElementById('martImage');
    const martName = document.getElementById('martName');
    const martAddress = document.getElementById('martAddress');
    
    const name = place.place_name;
    martName.textContent = name;
    martAddress.textContent = place.road_address_name || place.address_name;
    
    let keyword = 'mart';
    const lowerName = name.toLowerCase();
    if (lowerName.includes('emart')) keyword = 'emart';
    else if (lowerName.includes('홈플러스') || lowerName.includes('homeplus')) keyword = 'homeplus';
    else if (lowerName.includes('롯데마트') || lowerName.includes('lotte')) keyword = 'lottemart';
    else if (lowerName.includes('하나로') || lowerName.includes('hanaro')) keyword = 'hanaro mart';
    else if (lowerName.includes('gs') || lowerName.includes('더프레시')) keyword = 'gs supermarket';
    
    martImage.src = `https://source.unsplash.com/400x200/?${keyword}`;
    martImage.onerror = () => {
        martImage.src = "images/default-mart.jpg";
    };
    
    martInfo.style.display = 'flex';
}

// 전단지 로드 함수
function loadFlyers(martName) {
    fetch(`fetch_flyers.php?mart=${encodeURIComponent(martName)}`)
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('flyerContainer');
            container.innerHTML = '';
            
            if (data.length > 0) {
                // 마트 이름 표시
                const title = document.createElement('h3');
                title.textContent = `${martName} - 전단지`;
                title.className = 'flyer-title';
                container.appendChild(title);
                
                data.forEach(flyer => {
                    const flyerDiv = document.createElement('div');
                    flyerDiv.className = 'flyer-item';
                    
                    const img = document.createElement('img');
                    img.src = flyer.image_url;
                    img.className = 'flyer-image';
                    img.alt = '전단지 이미지';
                    
                    // 이미지 로드 에러 처리
                    img.onerror = () => {
                        img.src = 'images/no-image.png';
                        img.alt = '이미지를 불러올 수 없습니다';
                    };
                    
                    // 이미지 클릭 시 크게 보기
                    img.addEventListener('click', () => {
                        openImageModal(flyer.image_url, martName);
                    });
                    
                    const info = document.createElement('div');
                    info.className = 'flyer-info';
                    info.innerHTML = `
                        <p><strong>등록자:</strong> ${flyer.uploaded_by}</p>
                        <p><strong>등록일:</strong> ${formatDate(flyer.created_at)}</p>
                    `;
                    
                    flyerDiv.appendChild(img);
                    flyerDiv.appendChild(info);
                    container.appendChild(flyerDiv);
                });
            } else {
                const noFlyer = document.createElement('div');
                noFlyer.className = 'no-flyer';
                noFlyer.innerHTML = `
                    <p>📋 ${martName}의 등록된 전단지가 없습니다.</p>                  
                `;
                container.appendChild(noFlyer);
            }
        })
        .catch(error => {
            console.error('전단지 로드 실패:', error);
            const container = document.getElementById('flyerContainer');
            container.innerHTML = `
                <div class="error-message">
                    <p>⚠️ 전단지를 불러오는데 실패했습니다.</p>
                    <p>잠시 후 다시 시도해주세요.</p>
                </div>
            `;
        });
}

// 이미지 모달 열기 함수
function openImageModal(imageSrc, martName) {
    const modal = document.createElement('div');
    modal.className = 'image-modal';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h4>${martName} 전단지</h4>
                <span class="close-btn">&times;</span>
            </div>
            <div class="modal-body">
                <img src="${imageSrc}" alt="전단지 이미지" class="modal-image">
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // 모달 닫기 이벤트
    const closeBtn = modal.querySelector('.close-btn');
    const modalContent = modal.querySelector('.modal-content');
    
    closeBtn.addEventListener('click', () => {
        document.body.removeChild(modal);
    });
    
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            document.body.removeChild(modal);
        }
    });
    
    // ESC 키로 모달 닫기
    const handleEscape = (e) => {
        if (e.key === 'Escape') {
            document.body.removeChild(modal);
            document.removeEventListener('keydown', handleEscape);
        }
    };
    document.addEventListener('keydown', handleEscape);
}

// 날짜 포맷 함수
function formatDate(dateString) {
    const date = new Date(dateString);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    
    return `${year}.${month}.${day} ${hours}:${minutes}`;
}

function searchAddress() {
    const keyword = document.getElementById('searchInput').value.trim();
    if (!keyword) return;
    
    geocoder.addressSearch(keyword, function (result, status) {
        if (status === kakao.maps.services.Status.OK) {
            const coords = new kakao.maps.LatLng(result[0].y, result[0].x);
            map.setCenter(coords);
            searchMarts(result[0].y, result[0].x);
        } else {
            alert("주소를 찾을 수 없습니다.");
        }
    });
}