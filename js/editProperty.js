/*==================================================
    EDIT PROPERTY
==================================================*/


/*==================================================
    DATA
==================================================*/

const propertyData = window.editPropertyData || {};


/*==================================================
    ELEMENTS
==================================================*/

const form = document.getElementById("propertyForm");

const propertyName =
    document.getElementById("propertyName");

const province =
    document.getElementById("province");

const propertyPrice =
    document.getElementById("propertyPrice");

const propertySize =
    document.getElementById("propertySize");

const sizeUnit =
    document.getElementById("sizeUnit");

const propertyType =
    document.getElementById("propertyType");

const bedrooms =
    document.getElementById("bedrooms");

const bedroomsValue =
    document.getElementById("bedroomsValue");

const bathrooms =
    document.getElementById("bathrooms");

const bathroomsValue =
    document.getElementById("bathroomsValue");

const propertyDescription =
    document.getElementById("propertyDescription");

const propertyStatus =
    document.getElementById("propertyStatus");

const propertyTags =
    document.getElementById("propertyTags");

const tagsContainer =
    document.getElementById("tagsContainer");

const tagInput =
    document.getElementById("tagInput");

const addTagBtn =
    document.getElementById("addTagBtn");

const propertyAddress =
    document.getElementById("propertyAddress");

const propertyCanton =
    document.getElementById("propertyCanton");

const propertyLatitude =
    document.getElementById("propertyLatitude");

const propertyLongitude =
    document.getElementById("propertyLongitude");

const searchInput =
    document.getElementById("searchInput");

const gallery =
    document.querySelector(".gallery");


const MAX_IMAGE_SIZE =
    5 * 1024 * 1024;


const ALLOWED_IMAGES = [
    "image/jpeg",
    "image/png",
    "image/webp"
];


/*==================================================
    ALERT
==================================================*/

function alertBox(icon, title, text) {

    return Swal.fire({

        icon,
        title,
        text,

        confirmButtonColor: "#334d20"

    });

}


/*==================================================
    INITIALIZE FORM DATA
==================================================*/

function initializeForm() {

    if (!propertyData) {
        return;
    }


    /* PROPERTY NAME */

    if (propertyName) {

        propertyName.value =
            propertyData.name || "";

    }


    /* PRICE */

    if (propertyPrice) {

        propertyPrice.value =
            propertyData.price || "";

    }


    /* SIZE */

    if (propertySize) {

        propertySize.value =
            propertyData.size || "";

    }


    /* SIZE UNIT */

    if (sizeUnit) {

        sizeUnit.value =
            propertyData.size_unit || "m2";

    }


    /* PROPERTY TYPE */

    if (propertyType) {

        propertyType.value =
            propertyData.property_type || "";

    }


    /* PROVINCE */

    if (province) {

        province.value =
            propertyData.province || "";

    }


    /* BEDROOMS */

    if (bedrooms) {

        bedrooms.value =
            Number(propertyData.bedrooms) || 0;

    }

    if (bedroomsValue) {

        bedroomsValue.textContent =
            Number(propertyData.bedrooms) || 0;

    }


    /* BATHROOMS */

    if (bathrooms) {

        bathrooms.value =
            Number(propertyData.bathrooms) || 0;

    }

    if (bathroomsValue) {

        bathroomsValue.textContent =
            Number(propertyData.bathrooms) || 0;

    }


    /* DESCRIPTION */

    if (propertyDescription) {

        propertyDescription.value =
            propertyData.description || "";

    }


    /* STATUS */

    if (propertyStatus) {

        propertyStatus.value =
            propertyData.status || "active";

    }


    /* ADDRESS */

    if (propertyAddress) {

        propertyAddress.value =
            propertyData.address || "";

    }


    /* CANTON */

    if (propertyCanton) {

        propertyCanton.value =
            propertyData.canton || "";

    }


    /* LATITUDE */

    if (propertyLatitude) {

        propertyLatitude.value =
            propertyData.latitude || "";

    }


    /* LONGITUDE */

    if (propertyLongitude) {

        propertyLongitude.value =
            propertyData.longitude || "";

    }


    /* SEARCH INPUT */

    if (
        searchInput &&
        propertyData.address
    ) {

        searchInput.value =
            propertyData.address;

    }


    /* AMENITIES */

    const amenities =
        Array.isArray(propertyData.amenities)
            ? propertyData.amenities
            : [];


    document
        .querySelectorAll(
            'input[name="amenities[]"]'
        )
        .forEach(input => {

            input.checked =
                amenities.includes(input.value);

        });


    /* TAGS */

    tags = Array.isArray(propertyData.tags)
        ? [...propertyData.tags]
        : [];

    renderTags();
    syncTags();


    /* STATUS BUTTON */

    updateStatusUI();

}


/*==================================================
    COUNTERS
==================================================*/

document
    .querySelectorAll(".counter")
    .forEach(counter => {

        const value =
            counter.querySelector("span");

        const minus =
            counter.querySelector(".btn-minus");

        const plus =
            counter.querySelector(".btn-plus");

        const hiddenInput =
            counter.parentElement.querySelector(
                'input[type="hidden"]'
            );


        if (
            !value ||
            !minus ||
            !plus ||
            !hiddenInput
        ) {
            return;
        }


        minus.addEventListener(
            "click",
            () => {

                let number =
                    Number(hiddenInput.value) || 0;


                if (number > 0) {

                    number--;

                }


                hiddenInput.value =
                    number;

                value.textContent =
                    number;

            }
        );


        plus.addEventListener(
            "click",
            () => {

                let number =
                    Number(hiddenInput.value) || 0;


                number++;


                hiddenInput.value =
                    number;

                value.textContent =
                    number;

            }
        );

    });


/*==================================================
    TAGS
==================================================*/

let tags = [];


function syncTags() {

    if (!propertyTags) {
        return;
    }

    propertyTags.value =
        JSON.stringify(tags);

}


function renderTags() {

    if (!tagsContainer) {
        return;
    }


    tagsContainer.innerHTML = "";


    tags.forEach(
        (tag, index) => {

            const element =
                document.createElement("span");


            element.innerHTML = `
                ${escapeHtml(tag)}
                <i
                    class="ri-close-line"
                    role="button"
                    aria-label="Remove ${escapeHtml(tag)}">
                </i>
            `;


            const removeButton =
                element.querySelector("i");


            removeButton.addEventListener(
                "click",
                () => {

                    tags.splice(index, 1);

                    renderTags();
                    syncTags();

                }
            );


            tagsContainer.appendChild(element);

        }
    );

}


function addTag() {

    if (!tagInput) {
        return;
    }


    const value =
        tagInput.value.trim();


    if (!value) {
        return;
    }


    if (value.length < 2) {

        return alertBox(
            "warning",
            "Tag inválido",
            "El tag debe tener al menos 2 caracteres."
        );

    }


    if (value.length > 50) {

        return alertBox(
            "warning",
            "Tag demasiado largo",
            "El tag no puede superar los 50 caracteres."
        );

    }


    const exists =
        tags.some(
            tag =>
                tag.toLowerCase() ===
                value.toLowerCase()
        );


    if (exists) {

        return alertBox(
            "info",
            "Tag existente",
            "Ese tag ya fue agregado."
        );

    }


    tags.push(value);

    tagInput.value = "";

    renderTags();
    syncTags();

}


function escapeHtml(value) {

    return String(value)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");

}


if (addTagBtn) {

    addTagBtn.addEventListener(
        "click",
        addTag
    );

}


if (tagInput) {

    tagInput.addEventListener(
        "keydown",
        e => {

            if (e.key === "Enter") {

                e.preventDefault();

                addTag();

            }

        }
    );

}


/*==================================================
    STATUS
==================================================*/

function updateStatusUI() {

    document
        .querySelectorAll(".box")
        .forEach(box => {

            box.classList.toggle(
                "active",
                box.dataset.status ===
                propertyStatus.value
            );

        });

}


document
    .querySelectorAll(".box")
    .forEach(box => {

        box.addEventListener(
            "click",
            () => {

                propertyStatus.value =
                    box.dataset.status;

                updateStatusUI();

            }
        );

    });

/*==================================================
    EXISTING IMAGES
==================================================*/

document
    .querySelectorAll(".remove-existing-image")
    .forEach(button => {

        button.addEventListener(
            "click",
            e => {

                e.preventDefault();

                e.stopPropagation();


                const imageId =
                    button.dataset.imageId;


                if (!imageId) {
                    return;
                }


                const slot =
                    button.closest(".upload-slot");


                if (!slot) {
                    return;
                }


                Swal.fire({

                    icon: "warning",

                    title: "Eliminar imagen",

                    text:
                        "La imagen se eliminará cuando guardes los cambios.",

                    showCancelButton: true,

                    confirmButtonText: "Eliminar",

                    cancelButtonText: "Cancelar",

                    confirmButtonColor: "#334d20",

                    cancelButtonColor: "#6c757d"

                }).then(result => {

                    if (!result.isConfirmed) {
                        return;
                    }


                    /*==================================
                        AGREGAR ID A ELIMINAR
                    ==================================*/

                    const hiddenInput =
                        document.createElement(
                            "input"
                        );


                    hiddenInput.type =
                        "hidden";


                    hiddenInput.name =
                        "deleted_images[]";


                    hiddenInput.value =
                        imageId;


                    form.appendChild(
                        hiddenInput
                    );


                    /*==================================
                        LIMPIAR SLOT
                    ==================================*/

                    slot.classList.remove(
                        "has-existing-image"
                    );


                    const existingImage =
                        slot.querySelector(
                            ".existing-image-preview"
                        );


                    existingImage?.remove();


                    button.remove();


                    /*==================================
                        VOLVER A MOSTRAR UPLOAD
                    ==================================*/

                    const content =
                        document.createElement(
                            "div"
                        );


                    content.className =
                        "content";


                    content.innerHTML = `
                        <i
                            class="ri-camera-line"
                            aria-hidden="true">
                        </i>

                        <span>
                            Upload
                        </span>
                    `;


                    slot.appendChild(
                        content
                    );

                });

            }
        );

    });


/*==================================================
    NEW IMAGES
==================================================*/

if (gallery) {

    gallery.addEventListener(
        "change",
        e => {

            const input =
                e.target;


            if (
                !input.classList.contains(
                    "file-input"
                )
            ) {

                return;

            }


            const file =
                input.files[0];


            if (!file) {
                return;
            }


            /* TYPE */

            if (
                !ALLOWED_IMAGES.includes(
                    file.type
                )
            ) {

                input.value = "";

                return alertBox(
                    "warning",
                    "Imagen inválida",
                    "Solo se permiten JPG, JPEG y WEBP."
                );

            }


            /* SIZE */

            if (
                file.size >
                MAX_IMAGE_SIZE
            ) {

                input.value = "";

                return alertBox(
                    "warning",
                    "Imagen demasiado grande",
                    "Cada imagen puede tener un máximo de 5 MB."
                );

            }


            const slot =
                input.closest(
                    ".upload-slot"
                );


            if (!slot) {
                return;
            }


            const content =
                slot.querySelector(
                    ".content"
                );


            const oldPreview =
                slot.querySelector(
                    ".image-preview"
                );


            const oldRemove =
                slot.querySelector(
                    ".remove-image"
                );


            oldPreview?.remove();
            oldRemove?.remove();


            if (content) {

                content.style.display =
                    "none";

            }


            const preview =
                document.createElement("img");


            preview.className =
                "image-preview";

            preview.src =
                URL.createObjectURL(file);

            preview.alt =
                "New property image";


            const removeButton =
                document.createElement(
                    "button"
                );


            removeButton.type =
                "button";

            removeButton.className =
                "remove-image";

            removeButton.innerHTML =
                "&times;";

            removeButton.setAttribute(
                "aria-label",
                "Remove selected image"
            );


            removeButton.addEventListener(
                "click",
                e => {

                    e.preventDefault();

                    e.stopPropagation();

                    input.value = "";

                    preview.remove();

                    removeButton.remove();


                    if (content) {

                        content.style.display =
                            "";

                    }

                }
            );


            slot.appendChild(preview);

            slot.appendChild(removeButton);

        }
    );

}


/*==================================================
    GOOGLE MAPS
==================================================*/

let map;
let marker;
let autocomplete;
let geocoder;


function getAddressComponent(
    components,
    type
) {

    return components?.find(
        component =>
            component.types.includes(type)
    )?.long_name || "";

}


function updateLocationData(
    location
) {

    if (!geocoder) {
        return;
    }


    geocoder.geocode(
        {
            location
        },
        (
            results,
            status
        ) => {

            if (
                status !==
                "OK" ||
                !results?.length
            ) {

                return;

            }


            const components =
                results[0]
                    .address_components ||
                [];


            const address =
                results[0]
                    .formatted_address ||
                "";


            const provinceValue =
                getAddressComponent(
                    components,
                    "administrative_area_level_1"
                );


            let cantonValue =
                getAddressComponent(
                    components,
                    "administrative_area_level_2"
                );


            /*
             * FALLBACK COSTA RICA
             */

            if (!cantonValue) {

                const locality =
                    getAddressComponent(
                        components,
                        "locality"
                    );


                if (locality) {

                    const match =
                        locality.match(
                            /^(.+?)\s+de\s+(.+)$/i
                        );


                    cantonValue =
                        match
                            ? match[2].trim()
                            : locality;

                }

            }


            propertyAddress.value =
                address;


            propertyCanton.value =
                cantonValue;


            if (searchInput) {

                searchInput.value =
                    address;

            }


            /* PROVINCE */

            if (province) {

                const provinceOption =
                    [...province.options]
                        .find(
                            option =>
                                normalizeText(
                                    option.value
                                ) ===
                                normalizeText(
                                    provinceValue
                                ).replace(
                                    "provincia de ",
                                    ""
                                )
                        );


                if (provinceOption) {

                    province.value =
                        provinceOption.value;

                }

            }

        }
    );

}


function normalizeText(text) {

    return String(text)
        .toLowerCase()
        .normalize("NFD")
        .replace(
            /[\u0300-\u036f]/g,
            ""
        )
        .trim();

}


function initMap() {

    const latitude =
        Number(
            propertyData.latitude
        );

    const longitude =
        Number(
            propertyData.longitude
        );


    const hasLocation =
        Number.isFinite(latitude) &&
        Number.isFinite(longitude);


    const defaultLocation =
        hasLocation
            ? {
                lat: latitude,
                lng: longitude
            }
            : {
                lat: 9.9281,
                lng: -84.0907
            };


    geocoder =
        new google.maps.Geocoder();


    map = new google.maps.Map(
        document.getElementById("map"),
        {
            center: defaultLocation,
            zoom: hasLocation
                ? 16
                : 13
        }
    );


    marker =
        new google.maps.Marker({
            position: defaultLocation,
            map,
            draggable: true
        });


    /*
     * Autocomplete
     */

    autocomplete =
        new google.maps.places.Autocomplete(
            searchInput
        );


    autocomplete.addListener(
        "place_changed",
        () => {

            const place =
                autocomplete.getPlace();


            if (!place.geometry) {

                return alertBox(
                    "warning",
                    "Ubicación no encontrada",
                    "Selecciona una dirección válida."
                );

            }


            const location =
                place.geometry.location;


            map.setCenter(
                location
            );

            map.setZoom(16);

            marker.setPosition(
                location
            );


            propertyLatitude.value =
                location.lat();

            propertyLongitude.value =
                location.lng();


            updateLocationData(
                location
            );

        }
    );


    /*
     * Drag marker
     */

    marker.addListener(
        "dragend",
        () => {

            const position =
                marker.getPosition();


            propertyLatitude.value =
                position.lat();


            propertyLongitude.value =
                position.lng();


            updateLocationData(
                position
            );

        }
    );

}


if (
    typeof google !== "undefined" &&
    google.maps
) {

    initMap();

}


/*==================================================
    VALIDATION
==================================================*/

function validateProperty() {

    /* NAME */

    if (
        !propertyName.value.trim()
    ) {

        propertyName.focus();

        return alertBox(
            "warning",
            "Nombre requerido",
            "Ingresa el nombre de la propiedad."
        );

    }


    if (
        propertyName.value.trim().length < 3
    ) {

        propertyName.focus();

        return alertBox(
            "warning",
            "Nombre inválido",
            "Debe tener al menos 3 caracteres."
        );

    }


    /* PROVINCE */

    if (!province.value) {

        province.focus();

        return alertBox(
            "warning",
            "Provincia requerida",
            "Selecciona una provincia."
        );

    }


    /* PRICE */

    if (
        !propertyPrice.value ||
        Number(propertyPrice.value) <= 0
    ) {

        propertyPrice.focus();

        return alertBox(
            "warning",
            "Precio inválido",
            "Ingresa un precio mayor que cero."
        );

    }


    /* SIZE */

    if (
        !propertySize.value ||
        Number(propertySize.value) <= 0
    ) {

        propertySize.focus();

        return alertBox(
            "warning",
            "Tamaño inválido",
            "Ingresa un tamaño mayor que cero."
        );

    }


    /* TYPE */

    if (!propertyType.value) {

        propertyType.focus();

        return alertBox(
            "warning",
            "Tipo requerido",
            "Selecciona el tipo de propiedad."
        );

    }


    /* DESCRIPTION */

    const description =
        propertyDescription.value.trim();


    if (!description) {

        propertyDescription.focus();

        return alertBox(
            "warning",
            "Descripción requerida",
            "Ingresa una descripción."
        );

    }


    if (
        description.length < 20
    ) {

        propertyDescription.focus();

        return alertBox(
            "warning",
            "Descripción muy corta",
            "Debe tener al menos 20 caracteres."
        );

    }


    /* LOCATION */

    if (
        !propertyAddress.value ||
        !propertyCanton.value ||
        !propertyLatitude.value ||
        !propertyLongitude.value
    ) {

        searchInput.focus();

        return alertBox(
            "warning",
            "Ubicación incompleta",
            "Selecciona una ubicación válida en el mapa."
        );

    }


    return true;

}


/*==================================================
    SUBMIT
==================================================*/

form.addEventListener(
    "submit",
    async e => {

        e.preventDefault();


        if (!validateProperty()) {
            return;
        }


        const formData =
            new FormData(form);


        formData.set(
            "tags",
            JSON.stringify(tags)
        );


        Swal.fire({

            title: "Actualizando propiedad...",

            text: "Estamos guardando los cambios.",

            allowOutsideClick: false,

            allowEscapeKey: false,

            didOpen: () => {

                Swal.showLoading();

            }

        });


        try {

            const response =
                await fetch(
                    "actions/updateProperty.php",
                    {
                        method: "POST",
                        body: formData
                    }
                );


            const result =
                (await response.text())
                    .trim();


            console.log(
                "Update property:",
                result
            );


            Swal.close();


            switch (result) {

                case "success":

                    await Swal.fire({

                        icon: "success",

                        title: "¡Propiedad actualizada!",

                        text: "Los cambios se guardaron correctamente.",

                        confirmButtonColor:
                            "#334d20"

                    });


                    window.location.href =
                        "dashboard.php";


                    break;


                case "not_logged":

                    await alertBox(
                        "warning",
                        "Sesión expirada",
                        "Debes iniciar sesión nuevamente."
                    );


                    window.location.href =
                        "login.php";


                    break;


                case "property_not_found":

                    await alertBox(
                        "error",
                        "Propiedad no encontrada",
                        "No fue posible encontrar la propiedad."
                    );


                    window.location.href =
                        "dashboard.php";


                    break;


                case "empty_fields":

                    alertBox(
                        "warning",
                        "Campos incompletos",
                        "Completa la información requerida."
                    );

                    break;


                case "invalid_name":

                    alertBox(
                        "warning",
                        "Nombre inválido",
                        "Debe tener entre 3 y 150 caracteres."
                    );

                    break;


                case "invalid_price":

                    alertBox(
                        "warning",
                        "Precio inválido",
                        "Ingresa un precio mayor que cero."
                    );

                    break;


                case "invalid_size":

                    alertBox(
                        "warning",
                        "Tamaño inválido",
                        "Ingresa un tamaño mayor que cero."
                    );

                    break;


                case "invalid_description":

                    alertBox(
                        "warning",
                        "Descripción inválida",
                        "Debe tener entre 20 y 5000 caracteres."
                    );

                    break;


                case "invalid_location":

                    alertBox(
                        "warning",
                        "Ubicación inválida",
                        "Selecciona una ubicación válida."
                    );

                    break;


                case "invalid_image":

                    alertBox(
                        "warning",
                        "Imagen inválida",
                        "Una de las imágenes no es válida."
                    );

                    break;


                case "large_image":

                    alertBox(
                        "warning",
                        "Imagen demasiado grande",
                        "Cada imagen puede tener un máximo de 5 MB."
                    );

                    break;


                case "too_many_images":

                    alertBox(
                        "warning",
                        "Demasiadas imágenes",
                        "La propiedad puede tener un máximo de 4 imágenes."
                    );

                    break;


                case "upload_error":

                    alertBox(
                        "error",
                        "Error con las imágenes",
                        "No fue posible guardar las imágenes."
                    );

                    break;


                case "server_error":

                    alertBox(
                        "error",
                        "Error del servidor",
                        "No fue posible actualizar la propiedad."
                    );

                    break;


                default:

                    console.log(
                        "Respuesta inesperada:",
                        result
                    );


                    alertBox(
                        "error",
                        "Error inesperado",
                        "Ocurrió un error al actualizar la propiedad."
                    );

            }

        }

        catch (error) {

            console.error(
                "Update property error:",
                error
            );


            Swal.close();


            alertBox(
                "error",
                "Error de conexión",
                "No se pudo contactar con el servidor."
            );

        }

    }
);


/*==================================================
    INITIALIZE
==================================================*/

initializeForm();