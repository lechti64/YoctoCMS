// Changer la couleur de fond de la navigation après le défilement
$(window).on("scroll", function () {
    var navigation = $(".navigation");
    navigation.toggleClass("navigation--fixed", navigation.offset().top !== 0);
});