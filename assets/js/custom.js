var header = $(".header_section");
console.log(header);
// to get current year
function getYear() {
    var currentDate = new Date();
    var currentYear = currentDate.getFullYear();
    document.querySelector("#displayYear").innerHTML = currentYear;
}

getYear();


// isotope js
// $(window).on('load', function () {
//     $('.filters_menu li').click(function () {
//         $('.filters_menu li').removeClass('active');
//         $(this).addClass('active');
//
//         var data = $(this).attr('data-filter');
//         $grid.isotope({
//             filter: data
//         })
//     });
//
//     var $grid = $(".grid").isotope({
//         itemSelector: ".all",
//         percentPosition: false,
//         masonry: {
//             columnWidth: ".all"
//         }
//     })
// });

// nice select
$(document).ready(function() {
    $('select').niceSelect();
  });

/** google_map js **/
function myMap() {
    var mapProp = {
        center: new google.maps.LatLng(40.712775, -74.005973),
        zoom: 18,
    };
    var map = new google.maps.Map(document.getElementById("googleMap"), mapProp);
}

// client section owl carousel
$(".client_owl-carousel").owlCarousel({
    loop: true,
    margin: 0,
    dots: false,
    nav: true,
    navText: [],
    autoplay: true,
    autoplayHoverPause: true,
    navText: [
        '<i class="fa fa-angle-left" aria-hidden="true"></i>',
        '<i class="fa fa-angle-right" aria-hidden="true"></i>'
    ],
    responsive: {
        0: {
            items: 1
        },
        768: {
            items: 2
        },
        1000: {
            items: 2
        }
    }
});

$(document).ready(function () {
  var header = $(".header"); // 修改這裡
  console.log("header length:", header.length); // 測試是否選到元素

  $(window).scroll(function () {
    if ($(this).scrollTop() > 50) {
      header.addClass("header_scrolled"); // 滾動時變色
    } else {
      header.removeClass("header_scrolled"); // 滑回頂部時變回白色
    }
  });
});

$(document).ready(function () {
  var header = $(".header");
  var logo = $(".logo img"); // 選取 LOGO 圖片
  // var navLinks = $(".navmenu .navcolor"); // 選取導航列文字

  $(window).scroll(function () {
    if ($(this).scrollTop() > 50) {
      header.addClass("header_scrolled");
      logo.attr("src", "assets/img/logo-white.svg"); // 替換 LOGO 圖片
      $(".navmenu span").addClass("t_navcolor");
      $("#hbg").addClass("navcolor");
    } else {
      header.removeClass("header_scrolled");
      logo.attr("src", "assets/img/logo-blue.svg"); // 恢復原本 LOGO
      $(".navmenu span").removeClass("t_navcolor"); // 變回黑色  
      $("#hbg").removeClass("navcolor");
    }
  });
});


$(document).ready(function() {
    // 獲取當前 URL 參數
    const urlParams = new URLSearchParams(window.location.search);
    const currentFilter = urlParams.get('filter') || '*';

    // 初始化 Isotope
    var $grid = $(".grid").isotope({
        itemSelector: '.box',
        layoutMode: 'vertical'
    });

    // 設置初始過濾狀態
    $(".filters_menu li").removeClass("active");
    $(`.filters_menu li[data-filter="${currentFilter}"]`).addClass("active");

    // 過濾器點擊事件
    $(".filters_menu li").click(function() {
        const filter = $(this).attr("data-filter");
        console.log("filter",filter)
        // 構建新的 URL
        let newUrl = new URL(window.location.href);
        let searchParams = new URLSearchParams(newUrl.search);

        // 更新或移除 filter 參數
        if (filter === '*') {
            searchParams.delete('filter');
        } else {
            searchParams.set('filter', filter);
        }

        // 重置頁碼
        searchParams.set('page', '1');

        // 保留 category 參數（如果存在）
        if (!searchParams.has('category') && urlParams.has('category')) {
            searchParams.set('category', urlParams.get('category'));
        }

        // 更新 URL 並重新載入頁面
        newUrl.search = searchParams.toString();
        window.location.href = newUrl.toString().replace('#lecture_section','')+"/#lecture_section";
    });
});