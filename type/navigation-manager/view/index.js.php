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
function addLink(link, prepend) {
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
            $("<i>").attr("id", "visible-icon-" + link.uid).addClass(link.icon ? link.icon + " mr-2" : ""),
            $("<span>").attr("id", "visible-title-" + link.uid).text(link.title),
            $("<input>").attr({
                type: "hidden",
                name: "id[" + link.uid + "]",
                value: link.id
            }),
            $("<input>").attr({
                type: "hidden",
                name: "icon[" + link.uid + "]",
                value: link.icon
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
        )
    ;
    // Tri des liens enfants
    sortable(linkDOM.find(".navigation-links-children"), 0.9);
    // Ajoute le lien au DOM
    var target = link.navigationLinkId
        ? ".navigation-link[data-uid=" + link.navigationLinkUid + "] > .navigation-links-children"
        : "#navigation-links";
    if (prepend) {
        linkDOM.prependTo(target);
    } else {
        linkDOM.appendTo(target);
    }

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
    $("#navigation-link-fields").removeClass("d-none");
    // Mise à jour des champs d'édition
    $("#edit-title").val($("[name='title[" + selectedUid + "]']").val());
    $("#edit-page-id").val($("[name='page-id[" + selectedUid + "]']").val());
    $("#edit-visibility-" + $("[name='visibility[" + selectedUid + "]']").val()).prop("checked", true);
    $("#edit-blank").prop("checked", ($("[name='blank[" + selectedUid + "]']").val() === "true"));
    // Sélectionne l'icône
    resetIconPicker();
    var icon = $("[name='icon[" + selectedUid + "]']").val();
    if (icon) {
        $("#edit-icon-dropdown-toggle").data("iconpicker").update(icon);
    }
    $("#edit-icon-delete").prop("disabled", !icon);
    $("#edit-icon").val(icon);
}

// Réinitialisation du sélecteur d'icône
function resetIconPicker() {
    $(".iconpicker-item").removeClass("iconpicker-selected bg-primary");
    $("#edit-icon-helper").removeAttr("class");
    $("#edit-icon").val("");
    $("#edit-icon-delete").prop("disabled", true);
}

// Sélecteur d'icône
$("#edit-icon-dropdown-toggle")
    .iconpicker({
        animation: false,
        templates: {
            search: '<input type="search" class="form-control iconpicker-search" placeholder="Rechercher" />'
        }
    })
    .on("iconpickerSelected", function (event) {
        $("#edit-icon").val(event.iconpickerValue).trigger("change");
        $("#edit-icon-delete").prop("disabled", false);
    });

// Suppression d'un icône
$("#edit-icon-delete").on("click", function () {
    resetIconPicker();
    $("#edit-icon").trigger("change");
});

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
    $("[name='icon[" + selectedUid + "]']").val($("#edit-icon").val());
});

// Répercute le changement des titres visibles en direct
$("#edit-title").on("keyup change", function () {
    $("#visible-title-" + selectedUid).text($(this).val());
});
// Répercute le changement des icônes visibles en direct
$("#edit-icon").on("change", function () {
    var icon = $(this).val();
    $("#visible-icon-" + selectedUid).attr("class", icon ? icon + " mr-2" : "");
});

// Ajout d'un lien
$("#add").on("click", function () {
    // Ajoute le lien
    addLink({
        pageId: 0,
        title: "Nouveau lien",
        visibility: "inherit"
    }, true);
    selectLink($(".navigation-link").first())
    // Ferme le modal
    $('#add-modal').modal('hide');
});

// Suppression d'un lien
$("#delete-modal-submit").on("click", function () {
    // Supprime le lien
    $(".navigation-link[data-uid=" + selectedUid + "]").remove();
    selectedUid = undefined;
    // Cache les champs d'éditions
    $("#navigation-link-fields").addClass("d-none");
    var links = $(".navigation-link");
    if (links.length) {
        selectLink(links.first())
    }
    // Ferme le modal
    $('#delete-modal').modal('hide');
});

// Ajoute les liens parents
<?php foreach ($this->navigationLinksParents as $navigation): ?>
addLink(<?php echo json_encode($navigation); ?>);
<?php endforeach; ?>

// Ajoute les liens enfants
<?php foreach ($this->navigationLinksChildren as $navigation): ?>
addLink(<?php echo json_encode($navigation); ?>);
<?php endforeach; ?>