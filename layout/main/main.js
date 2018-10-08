// Change the background color of nav after scrolling
$(window).on("scroll", function() {
    var nav = $(".nav");
    if(nav.offset().top === 0) {
        nav.removeClass("nav--fixed");
    }
    else {
        nav.addClass("nav--fixed");
    }
});