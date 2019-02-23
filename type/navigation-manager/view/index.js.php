// Tri des liens
function sortable(container) {
    return Sortable.create(container.get(0), {
        group: "navigation-links",
        animation: 150,
        emptyInsertThreshold: 5,
        dragoverBubble: true,
        onEnd: function (event) {
            var link = $(event.item);
            var target = $(event.to);
            // Le lien est un enfant
            if (target.hasClass("navigation-links-children")) {
                // Répercute le changement de navigationUid dans le champ caché
                var parenUid = target.parent(".navigation-link").data("uid");
                $("#navigation-uid-" + link.data("uid")).val(parenUid);
                // Déplace les enfants et répercute le changement de navigationUid dans le champ caché
                link.find(".navigation-link").each(function () {
                    var child = $(this);
                    $("#navigation-uid-" + child.data("uid")).val(parenUid);
                    child.appendTo(target);
                });
                // Désactive le tri des enfants
                link.children(".navigation-links-children").hide();
            }
            // Le lien est un parent
            else {
                // Répercute le changement de navigationUid dans le champ caché
                $("#navigation-uid-" + link.data("uid")).val(0);
                // Active le tri des enfants
                link.children(".navigation-links-children").show();
            }
        }
    });
}
sortable($("#navigation-links"));

// Ajout d'un lien
var uid = 1;
var idsUids = {};
function addLink(link) {
    // Ajoute l'id et l'uid du lien au tableau de relation id / uid
    if (link.id) {
        idsUids[link.id] = uid;
    }
    // Ajoute l'id unique au lien
    link.uid = uid++;
    // Ajoute le navigation uid
    if (link.navigationId) {
        link.navigationUid = idsUids[link.navigationId];
    } else {
        link.navigationUid = 0;
    }
    // Crée le lien
    var linkDOM = $("<li>")
        .attr("data-uid", link.uid)
        .addClass("navigation-link list-group-item list-group-item-action")
        .append(
            $("<i>").addClass("fas fa-arrows-alt mr-3"),
            $("<span>").attr("id", "visible-title-" + link.uid).text(link.title),
            $("<input>").attr({type: "hidden", id: "uid-" + link.uid, name: "uid[]", value: link.uid}),
            $("<input>").attr({type: "hidden", id: "id-" + link.uid, name: "id[]", value: link.id}),
            $("<input>").attr({type: "hidden", id: "title-" + link.uid, name: "title[]", value: link.title}),
            $("<input>").attr({type: "hidden", id: "page-id-" + link.uid, name: "page-id[]", value: link.pageId}),
            $("<input>").attr({
                type: "hidden",
                id: "visibility-" + link.uid,
                name: "visibility[]",
                value: link.visibility
            }),
            $("<input>").attr({type: "hidden", id: "blank-" + link.uid, name: "blank[]", value: link.blank}),
            $("<input>").attr({
                type: "hidden",
                id: "navigation-uid-" + link.uid,
                name: "navigation-uid[]",
                value: link.navigationUid
            }),
            $("<input>").attr({type: "hidden", id: "position-" + link.uid, name: "position[]", value: link.position}),
            $("<ul>")
                .addClass("list-group navigation-links-children")
                // Désactive le tri pour les enfants du lien si il est lui même un enfant
                .toggle(link.navigationUid === 0)
        );
    // Ajoute le tri au liens enfants
    sortable(linkDOM.find(".navigation-links-children"));
    // Ajoute le lien au DOM
    linkDOM.appendTo(
        link.navigationId
            ? ".navigation-link[data-uid=" + link.navigationUid + "] > .navigation-links-children"
            : "#navigation-links"
    );
    // Sélectionne le premier lien ajouté
    if ($("#navigation-links").children().length === 1) {
        selectLink(linkDOM)
    }
}

// Sélection d'un lien
var selectedUid;
function selectLink(link) {
    // Sélectionne le lien
    $(".navigation-link.active").removeClass("active");
    link.addClass("active");
    selectedUid = link.data("uid");
    // Affiche les champs d'éditions
    $("#navigation-link-fields.d-none").removeClass("d-none");
    // Mise à jour des champs d'édition
    $("#edit-title").val($("#title-" + selectedUid).val());
    $("#edit-page-id").val($("#page-id-" + selectedUid).val());
    $("#edit-visibility-" + $("#visibility-" + selectedUid).val()).prop("checked", true);
    $("#edit-blank").prop("checked", ($("#blank-" + selectedUid).val() === "true"));
}

// Sélection d'un lien lors d'un clic dessus
$(document).on("click", ".navigation-link", function (event) {
    selectLink($(this));
    event.stopPropagation();
});

// Répercute les changements des champs d'édition dans le champs cachés
$("#navigation-link-fields").on("change", function () {
    $("#title-" + selectedUid).val($("#edit-title").val());
    $("#page-id-" + selectedUid).val($("#edit-page-id").val());
    $("#visibility-" + selectedUid).val($("[name=edit-visibility]:checked").val())
    $("#blank-" + selectedUid).val($("#edit-blank").is(":checked"));
});

// Répercute le changement des titres visibles en direct
$("#edit-title").on("keyup change", function () {
    $("#visible-title-" + selectedUid).text($(this).val());
});

// Ajout des liens au chargement
<?php foreach ($this->navigations as $navigation): ?>
<?php if($navigation->navigationId === 0): ?>
addLink(<?php echo json_encode($navigation); ?>);
<?php endif; ?>
<?php endforeach; ?>
<?php foreach ($this->navigations as $navigation): ?>
<?php if($navigation->navigationId): ?>
addLink(<?php echo json_encode($navigation); ?>);
<?php endif; ?>
<?php endforeach; ?>