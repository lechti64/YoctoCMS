// Tri des liens dans un container
function sortable(container, swapThreshold) {
    return Sortable.create(container.get(0), {
        group: "navigation-links",
        animation: 150,
        swapThreshold: swapThreshold,
        invertSwap: true,
        dragoverBubble: true,
        onEnd: function (event) {
            var link = $(event.item);
            var target = $(event.to);
            // Le lien est ou devient un enfant
            if (target.hasClass("navigation-links-children")) {
                var parenUid = target.parent(".navigation-link").data("uid");
                // Mise à jour du champ caché navigation-link-uid
                $("[name='navigation-link-uid[" + link.data("uid") + "]']").val(parenUid);
                // Mise à jour des enfants
                link.find(".navigation-link").each(function () {
                    var child = $(this);
                    // Mise à jour du champ caché navigation-link-uid
                    $("[name='navigation-link-uid[" + child.data("uid") + "]']").val(parenUid);
                    // Déplace l'enfant au même niveau que son parent
                    child.appendTo(target);
                });
                // Bloque l'ajout d'enfant au lien
                link.children(".navigation-links-children").hide();
            }
            // Le lien est ou devient un parent
            else {
                // Mise à jour du champ caché navigation-link-uid
                $("[name='navigation-link-uid[" + link.data("uid") + "]']").val(0);
                // Autorise l'ajout d'enfant au lien
                link.children(".navigation-links-children").show();
            }
        }
    });
}
// Tri des liens parents
sortable($("#navigation-links"), 0.02);

// Ajout d'un lien
var uid = 1;
var idsUids = {};
function addLink(link) {
    // Ajoute l'id et l'uid au tableau de relation id / uid
    if (link.id) {
        idsUids[link.id] = uid;
    }
    // Ajoute l'uid au lien
    link.uid = uid++;
    // Ajoute le l'uid du parent au lien
    if (link.navigationLinkId) {
        link.navigationLinkUid = idsUids[link.navigationLinkId];
    } else {
        link.navigationLinkUid = 0;
    }
    // Crée le lien
    var linkDOM = $("<li>")
        .attr("data-uid", link.uid)
        .addClass("navigation-link list-group-item list-group-item-action")
        .append(
            $("<i>").addClass("fas fa-arrows-alt mr-3"),
            $("<span>").attr("id", "visible-title-" + link.uid).text(link.title),
            $("<input>").attr({
                type: "hidden",
                name: "id[" + link.uid + "]",
                value: link.id
            }),
            $("<input>").attr({
                type: "hidden",
                name: "title[" + link.uid + "]",
                value: link.title
            }),
            $("<input>").attr({
                type: "hidden",
                name: "page-id[" + link.uid + "]",
                value: link.pageId
            }),
            $("<input>").attr({
                type: "hidden",
                name: "visibility[" + link.uid + "]",
                value: link.visibility
            }),
            $("<input>").attr({
                type: "hidden",
                name: "blank[" + link.uid + "]",
                value: link.blank
            }),
            $("<input>").attr({
                type: "hidden",
                name: "navigation-link-uid[" + link.uid + "]",
                value: link.navigationLinkUid
            }),
            $("<ul>")
                .addClass("list-group navigation-links-children")
                // Bloque l'ajout d'enfant au lien si il s'agit d'un enfant
                .toggle(link.navigationLinkUid === 0)
        );
    // Tri des liens enfants
    sortable(linkDOM.find(".navigation-links-children"), 0.9);
    // Ajoute le lien au DOM
    linkDOM.appendTo(
        link.navigationLinkId
            ? ".navigation-link[data-uid=" + link.navigationLinkUid + "] > .navigation-links-children"
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
    $("#edit-title").val($("[name='title[" + selectedUid + "]']").val());
    $("#edit-page-id").val($("[name='page-id[" + selectedUid + "]']").val());
    $("#edit-visibility-" + $("[name='visibility[" + selectedUid + "]']").val()).prop("checked", true);
    $("#edit-blank").prop("checked", ($("[name='blank[" + selectedUid + "]']").val() === "true"));
}

// Sélection d'un lien lors d'un clic dessus
$(document).on("click", ".navigation-link", function (event) {
    selectLink($(this));
    event.stopPropagation();
});

// Répercute les changements des champs d'édition dans le champs cachés
$("#navigation-link-fields").on("change", function () {
    $("[name='title[" + selectedUid + "]']").val($("#edit-title").val());
    $("[name='page-id[" + selectedUid + "]']").val($("#edit-page-id").val());
    $("[name='visibility[" + selectedUid + "]']").val($("[name=edit-visibility]:checked").val())
    $("[name='blank[" + selectedUid + "]']").val($("#edit-blank").is(":checked"));
});

// Répercute le changement des titres visibles en direct
$("#edit-title").on("keyup change", function () {
    $("#visible-title-" + selectedUid).text($(this).val());
});

// Crée les liens parents
<?php foreach ($this->navigationLinksParents as $navigation): ?>
addLink(<?php echo json_encode($navigation); ?>);
<?php endforeach; ?>

// Crée les liens enfants
<?php foreach ($this->navigationLinksChildren as $navigation): ?>
addLink(<?php echo json_encode($navigation); ?>);
<?php endforeach; ?>