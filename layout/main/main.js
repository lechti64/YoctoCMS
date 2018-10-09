// Change the background color of nav after scrolling
$(window).on("scroll", function() {
    var navbar = $(".navbar");
    navbar.toggleClass("navbar--fixed", navbar.offset().top !== 0);
});