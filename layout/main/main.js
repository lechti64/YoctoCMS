// Change the background color of nav after scrolling
$(window).on("scroll", function() {
    var navigation = $(".navigation");
    navigation.toggleClass("navigation--fixed", navigation.offset().top !== 0);
}).trigger("scroll");