ClassicEditor
    .create(document.querySelector("textarea"), {
        language: "fr"
    })
    .catch(error => {
        console.error(error);
    });