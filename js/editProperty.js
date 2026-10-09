/* ==================================================
   EDIT PROPERTY — Locally
================================================== */

const propertyData = window.editPropertyData || {};

const form = document.getElementById("propertyForm");
const propertyName = document.getElementById("propertyName");
const province = document.getElementById("province");
const propertyPrice = document.getElementById("propertyPrice");
const propertySize = document.getElementById("propertySize");
const sizeUnit = document.getElementById("sizeUnit");
const propertyType = document.getElementById("propertyType");
const bedrooms = document.getElementById("bedrooms");
const bedroomsValue = document.getElementById("bedroomsValue");
const bathrooms = document.getElementById("bathrooms");
const bathroomsValue = document.getElementById("bathroomsValue");
const propertyDescription = document.getElementById("propertyDescription");
const propertyStatus = document.getElementById("propertyStatus");
const propertyTags = document.getElementById("propertyTags");
const tagsContainer = document.getElementById("tagsContainer");
const tagInput = document.getElementById("tagInput");
const addTagBtn = document.getElementById("addTagBtn");
const propertyAddress = document.getElementById("propertyAddress");
const propertyCanton = document.getElementById("propertyCanton");
const propertyLatitude = document.getElementById("propertyLatitude");
const propertyLongitude = document.getElementById("propertyLongitude");
const searchInput = document.getElementById("searchInput");
const gallery = document.querySelector(".gallery");

const MAX_IMAGE_SIZE = 5 * 1024 * 1024;
const MAX_IMAGES = 7;
const ALLOWED_IMAGES = [
    "image/jpeg",
    "image/png",
    "image/webp"
];
const VALID_SIZE_UNITS = ["m2", "sqft"];

let tags = [];
let map;
let marker;
let autocomplete;
let geocoder;


/* ==================================================
   ALERTS
================================================== */

function alertBox(icon, title, text) {
    return Swal.fire({
        icon,
        title,
        text,
        confirmButtonColor: "#334d20"
    });
}


function escapeHtml(value) {
    return String(value)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}


function normalizeText(value) {
    return String(value ?? "")
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .trim();
}


/* ==================================================
   INITIALIZE FORM
================================================== */

function initializeForm() {
    if (propertyName) {
        propertyName.value = propertyData.name ?? "";
    }

    if (propertyPrice) {
        propertyPrice.value = propertyData.price ?? "";
    }

    if (propertySize) {
        propertySize.value = propertyData.size ?? "";
    }

    /*
     * SIZE UNIT
     * Ensure the selector always has a valid value.
     */

    if (sizeUnit) {
        const storedUnit = String(
            propertyData.size_unit ?? ""
        ).trim().toLowerCase();

        sizeUnit.value = VALID_SIZE_UNITS.includes(storedUnit)
            ? storedUnit
            : "m2";
    }

    if (propertyType) {
        propertyType.value = propertyData.property_type ?? "";
    }

    if (province) {
        province.value = propertyData.province ?? "";
    }

    const bedroomCount = Number(propertyData.bedrooms) || 0;

    if (bedrooms) {
        bedrooms.value = bedroomCount;
    }

    if (bedroomsValue) {
        bedroomsValue.textContent = bedroomCount;
    }

    const bathroomCount = Number(propertyData.bathrooms) || 0;

    if (bathrooms) {
        bathrooms.value = bathroomCount;
    }

    if (bathroomsValue) {
        bathroomsValue.textContent = bathroomCount;
    }

    if (propertyDescription) {
        propertyDescription.value = propertyData.description ?? "";
    }

    if (propertyStatus) {
        propertyStatus.value = propertyData.status || "active";
    }

    if (propertyAddress) {
        propertyAddress.value = propertyData.address ?? "";
    }

    if (propertyCanton) {
        propertyCanton.value = propertyData.canton ?? "";
    }

    if (propertyLatitude) {
        propertyLatitude.value = propertyData.latitude ?? "";
    }

    if (propertyLongitude) {
        propertyLongitude.value = propertyData.longitude ?? "";
    }

    if (searchInput) {
        searchInput.value = propertyData.address ?? "";
    }

    /* AMENITIES */

    const amenities = Array.isArray(propertyData.amenities)
        ? propertyData.amenities
        : [];

    document
        .querySelectorAll('input[name="amenities[]"]')
        .forEach(input => {
            input.checked = amenities.includes(input.value);
        });

    /* TAGS */

    tags = Array.isArray(propertyData.tags)
        ? [...propertyData.tags]
        : [];

    renderTags();
    syncTags();

    /* STATUS */

    updateStatusUI();
}


/* ==================================================
   BEDROOMS AND BATHROOMS COUNTERS
================================================== */


/* ==================================================
   BEDROOMS AND BATHROOMS COUNTERS
================================================== */

const BATHROOM_STEPS = [
    0,
    1,
    1.5,
    2,
    2.5,
    3,
    3.5,
    4,
    4.5,
    5,
    5.5,
    6,
    7,
    8
];

function formatCounterValue(number) {
    return Number.isInteger(number)
        ? String(number)
        : number.toFixed(1);
}

document.querySelectorAll(".counter").forEach(counter => {
    const value = counter.querySelector("span");
    const minus = counter.querySelector(".btn-minus");
    const plus = counter.querySelector(".btn-plus");

    const hiddenInput = counter.parentElement.querySelector(
        'input[type="hidden"]'
    );

    if (!value || !minus || !plus || !hiddenInput) {
        return;
    }

    const isBathroomCounter = hiddenInput.id === "bathrooms";

    function updateCounter(direction) {
        let currentValue = Number(hiddenInput.value);

        if (!Number.isFinite(currentValue)) {
            currentValue = 0;
        }

        let nextValue;

        if (isBathroomCounter) {
            let currentIndex = BATHROOM_STEPS.findIndex(
                step => Math.abs(step - currentValue) < 0.001
            );

            // Compatibility with older saved values.
            if (currentIndex === -1) {
                currentIndex = BATHROOM_STEPS.reduce(
                    (bestIndex, step, index, steps) => {
                        return Math.abs(step - currentValue) <
                            Math.abs(steps[bestIndex] - currentValue)
                            ? index
                            : bestIndex;
                    },
                    0
                );
            }

            const nextIndex = Math.max(
                0,
                Math.min(
                    BATHROOM_STEPS.length - 1,
                    currentIndex + direction
                )
            );

            nextValue = BATHROOM_STEPS[nextIndex];

        } else {
            // Bedrooms remain whole numbers.
            nextValue = Math.max(0, currentValue + direction);
        }

        const formattedValue = formatCounterValue(nextValue);

        hiddenInput.value = formattedValue;
        value.textContent = formattedValue;
    }

    minus.addEventListener("click", () => {
        updateCounter(-1);
    });

    plus.addEventListener("click", () => {
        updateCounter(1);
    });
});


/* ==================================================
   TAGS
================================================== */

function syncTags() {
    if (propertyTags) {
        propertyTags.value = JSON.stringify(tags);
    }
}


function renderTags() {
    if (!tagsContainer) {
        return;
    }

    tagsContainer.innerHTML = "";

    tags.forEach((tag, index) => {
        const element = document.createElement("span");

        element.innerHTML = `
            ${escapeHtml(tag)}
            <i
                class="ri-close-line"
                role="button"
                tabindex="0"
                aria-label="Remove ${escapeHtml(tag)}">
            </i>
        `;

        const removeButton = element.querySelector("i");

        const removeTag = () => {
            tags.splice(index, 1);

            renderTags();
            syncTags();
        };

        removeButton.addEventListener("click", removeTag);

        removeButton.addEventListener("keydown", event => {
            if (event.key === "Enter" || event.key === " ") {
                event.preventDefault();
                removeTag();
            }
        });

        tagsContainer.appendChild(element);
    });
}


function addTag() {
    if (!tagInput) {
        return;
    }

    const value = tagInput.value.trim();

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

    const exists = tags.some(
        tag => tag.toLowerCase() === value.toLowerCase()
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


if (addTagBtn) {
    addTagBtn.addEventListener("click", addTag);
}


if (tagInput) {
    tagInput.addEventListener("keydown", event => {
        if (event.key === "Enter") {
            event.preventDefault();
            addTag();
        }
    });
}


/* ==================================================
   PROPERTY STATUS
================================================== */

function updateStatusUI() {
    if (!propertyStatus) {
        return;
    }

    document.querySelectorAll(".box").forEach(box => {
        box.classList.toggle(
            "active",
            box.dataset.status === propertyStatus.value
        );
    });
}


document.querySelectorAll(".box").forEach(box => {
    box.addEventListener("click", () => {
        if (!propertyStatus) {
            return;
        }

        propertyStatus.value = box.dataset.status;

        updateStatusUI();
    });
});


/* ==================================================
   EXISTING IMAGES — REMOVE
================================================== */

document.querySelectorAll(".remove-existing-image").forEach(button => {
    button.addEventListener("click", event => {
        event.preventDefault();
        event.stopPropagation();

        const imageId = button.dataset.imageId;
        const slot = button.closest(".upload-slot");

        if (!imageId || !slot || !form) {
            return;
        }

        Swal.fire({
            icon: "warning",
            title: "Eliminar imagen",
            text: "La imagen se eliminará cuando guardes los cambios.",
            showCancelButton: true,
            confirmButtonText: "Eliminar",
            cancelButtonText: "Cancelar",
            confirmButtonColor: "#334d20",
            cancelButtonColor: "#6c757d"
        }).then(result => {
            if (!result.isConfirmed) {
                return;
            }

            /*
             * Register the image for deletion.
             * The backend will remove it when saving.
             */

            const hiddenInput = document.createElement("input");

            hiddenInput.type = "hidden";
            hiddenInput.name = "deleted_images[]";
            hiddenInput.value = imageId;

            form.appendChild(hiddenInput);

            /* Clear the slot */

            slot.classList.remove("has-existing-image");

            slot.querySelector(".existing-image-preview")?.remove();
            slot.querySelector(".photo-badge")?.remove();

            button.remove();

            /* Restore the upload prompt */

            let content = slot.querySelector(".content");

            if (!content) {
                content = document.createElement("div");
                content.className = "content";

                slot.appendChild(content);
            }

            content.style.display = "";

            content.innerHTML = `
                <i class="ri-camera-line" aria-hidden="true"></i>
                <span>Upload</span>
            `;
        });
    });
});


/* ==================================================
   NEW IMAGES — VALIDATION AND PREVIEW
================================================== */

if (gallery) {
    gallery.addEventListener("change", event => {
        const input = event.target;

        if (!input.classList.contains("file-input")) {
            return;
        }

        const file = input.files?.[0];

        if (!file) {
            return;
        }

        /* FILE TYPE */

        if (!ALLOWED_IMAGES.includes(file.type)) {
            input.value = "";

            return alertBox(
                "warning",
                "Imagen inválida",
                "Solo se permiten archivos JPG, PNG y WEBP."
            );
        }

        /* FILE SIZE */

        if (file.size > MAX_IMAGE_SIZE) {
            input.value = "";

            return alertBox(
                "warning",
                "Imagen demasiado grande",
                "Cada imagen puede tener un máximo de 5 MB."
            );
        }

        const slot = input.closest(".upload-slot");

        if (!slot) {
            return;
        }

        /* Clear any previous preview */

        slot.querySelector(".image-preview")?.remove();
        slot.querySelector(".remove-image")?.remove();

        const content = slot.querySelector(".content");

        if (content) {
            content.style.display = "none";
        }

        /*
         * If a new photo replaces an existing one,
         * queue the previous photo for deletion.
         */

        const oldImage = slot.querySelector(".existing-image-preview");
        const oldImageButton = slot.querySelector(".remove-existing-image");
        const existingImageId = oldImageButton?.dataset.imageId;

        if (existingImageId && form) {
            const alreadyQueued = [
                ...form.querySelectorAll('input[name="deleted_images[]"]')
            ].some(hidden => hidden.value === existingImageId);

            if (!alreadyQueued) {
                const hiddenInput = document.createElement("input");

                hiddenInput.type = "hidden";
                hiddenInput.name = "deleted_images[]";
                hiddenInput.value = existingImageId;

                form.appendChild(hiddenInput);
            }
        }

        oldImage?.remove();
        oldImageButton?.remove();

        slot.querySelector(".photo-badge")?.remove();
        slot.classList.remove("has-existing-image");

        /* Preview */

        const preview = document.createElement("img");

        preview.className = "image-preview";
        preview.src = URL.createObjectURL(file);
        preview.alt = "Vista previa de nueva imagen";

        preview.onload = () => {
            URL.revokeObjectURL(preview.src);
        };

        /* Remove selected image */

        const removeButton = document.createElement("button");

        removeButton.type = "button";
        removeButton.className = "remove-image";

        removeButton.innerHTML = `
            <i class="ri-close-line" aria-hidden="true"></i>
        `;

        removeButton.setAttribute(
            "aria-label",
            "Remove selected image"
        );

        removeButton.addEventListener("click", e => {
            e.preventDefault();
            e.stopPropagation();

            input.value = "";

            preview.remove();
            removeButton.remove();

            if (content) {
                content.style.display = "";
            }
        });

        slot.appendChild(preview);
        slot.appendChild(removeButton);
    });
}


/* ==================================================
   GOOGLE MAPS HELPERS
================================================== */

function getAddressComponent(components, type) {
    return components?.find(
        component => component.types.includes(type)
    )?.long_name || "";
}


function updateLocationData(location) {
    if (!geocoder) {
        return;
    }

    geocoder.geocode({ location }, (results, status) => {
        if (status !== "OK" || !results?.length) {
            return;
        }

        const components = results[0].address_components || [];

        const address = results[0].formatted_address || "";

        const provinceValue = getAddressComponent(
            components,
            "administrative_area_level_1"
        );

        let cantonValue = getAddressComponent(
            components,
            "administrative_area_level_2"
        );

        /*
         * Fallback for Costa Rican addresses
         * such as "Sardinal de Carrillo".
         */

        if (!cantonValue) {
            const locality = getAddressComponent(
                components,
                "locality"
            );

            if (locality) {
                const match = locality.match(/^(.+?)\s+de\s+(.+)$/i);

                cantonValue = match
                    ? match[2].trim()
                    : locality;
            }
        }

        if (propertyAddress) {
            propertyAddress.value = address;
        }

        if (propertyCanton) {
            propertyCanton.value = cantonValue;
        }

        if (searchInput) {
            searchInput.value = address;
        }

        /* Match Google's province to the form options */

        if (province && provinceValue) {
            const normalizedProvince = normalizeText(
                provinceValue
            ).replace("provincia de ", "");

            const option = [...province.options].find(item => {
                const optionValue = normalizeText(
                    item.value
                ).replace("provincia de ", "");

                return optionValue === normalizedProvince;
            });

            if (option) {
                province.value = option.value;
            }
        }
    });
}


/* ==================================================
   GOOGLE MAPS INITIALIZATION
================================================== */

function initMap() {
    const mapElement = document.getElementById("map");

    if (
        !mapElement ||
        !searchInput ||
        !propertyLatitude ||
        !propertyLongitude
    ) {
        return;
    }

    const rawLatitude = propertyData.latitude;
    const rawLongitude = propertyData.longitude;

    const latitude = Number(rawLatitude);
    const longitude = Number(rawLongitude);

    const hasLocation =
        rawLatitude !== "" &&
        rawLatitude != null &&
        rawLongitude !== "" &&
        rawLongitude != null &&
        Number.isFinite(latitude) &&
        Number.isFinite(longitude);

    const defaultLocation = hasLocation
        ? {
            lat: latitude,
            lng: longitude
        }
        : {
            lat: 9.9281,
            lng: -84.0907
        };

    geocoder = new google.maps.Geocoder();

    map = new google.maps.Map(mapElement, {
        center: defaultLocation,
        zoom: hasLocation ? 16 : 13
    });

    marker = new google.maps.Marker({
        position: defaultLocation,
        map,
        draggable: true
    });

    autocomplete = new google.maps.places.Autocomplete(
        searchInput,
        {
            fields: [
                "formatted_address",
                "address_components",
                "geometry"
            ]
        }
    );

    /* AUTOCOMPLETE */

    autocomplete.addListener("place_changed", () => {
        const place = autocomplete.getPlace();

        if (!place.geometry?.location) {
            return alertBox(
                "warning",
                "Ubicación no encontrada",
                "Selecciona una dirección válida de la lista."
            );
        }

        const location = place.geometry.location;

        map.setCenter(location);
        map.setZoom(16);

        marker.setPosition(location);

        propertyLatitude.value = location.lat();
        propertyLongitude.value = location.lng();

        updateLocationData(location);
    });

    /* DRAG MARKER */

    marker.addListener("dragend", () => {
        const position = marker.getPosition();

        if (!position) {
            return;
        }

        propertyLatitude.value = position.lat();
        propertyLongitude.value = position.lng();

        updateLocationData(position);
    });
}


if (
    typeof google !== "undefined" &&
    google.maps
) {
    initMap();
}


/* ==================================================
   FORM VALIDATION
================================================== */

function validateProperty() {

    /* NAME */

    if (!propertyName?.value.trim()) {
        propertyName?.focus();

        alertBox(
            "warning",
            "Nombre requerido",
            "Ingresa el nombre de la propiedad."
        );

        return false;
    }

    if (
        propertyName.value.trim().length < 3 ||
        propertyName.value.trim().length > 150
    ) {
        propertyName.focus();

        alertBox(
            "warning",
            "Nombre inválido",
            "El nombre debe tener entre 3 y 150 caracteres."
        );

        return false;
    }

    /* PROVINCE */

    if (!province?.value) {
        province?.focus();

        alertBox(
            "warning",
            "Provincia requerida",
            "Selecciona una provincia."
        );

        return false;
    }

    /* PRICE */

    if (
        !propertyPrice?.value ||
        Number(propertyPrice.value) <= 0
    ) {
        propertyPrice?.focus();

        alertBox(
            "warning",
            "Precio inválido",
            "Ingresa un precio mayor que cero."
        );

        return false;
    }

    /* SIZE */

    if (
        !propertySize?.value ||
        Number(propertySize.value) <= 0
    ) {
        propertySize?.focus();

        alertBox(
            "warning",
            "Tamaño inválido",
            "Ingresa un tamaño mayor que cero."
        );

        return false;
    }

    /* SIZE UNIT */

    if (
        !sizeUnit ||
        !VALID_SIZE_UNITS.includes(sizeUnit.value)
    ) {
        sizeUnit?.focus();

        alertBox(
            "warning",
            "Unidad requerida",
            "Selecciona M² o SQFT."
        );

        return false;
    }

    /* PROPERTY TYPE */

    if (!propertyType?.value) {
        propertyType?.focus();

        alertBox(
            "warning",
            "Tipo requerido",
            "Selecciona el tipo de propiedad."
        );

        return false;
    }

    /* DESCRIPTION */

    const description = propertyDescription?.value.trim() || "";

    if (!description) {
        propertyDescription?.focus();

        alertBox(
            "warning",
            "Descripción requerida",
            "Ingresa una descripción."
        );

        return false;
    }

    if (
        description.length < 20 ||
        description.length > 5000
    ) {
        propertyDescription?.focus();

        alertBox(
            "warning",
            "Descripción inválida",
            "La descripción debe tener entre 20 y 5000 caracteres."
        );

        return false;
    }

    /* LOCATION */

    if (
        !propertyAddress?.value.trim() ||
        !propertyCanton?.value.trim() ||
        !propertyLatitude?.value ||
        !propertyLongitude?.value
    ) {
        searchInput?.focus();

        alertBox(
            "warning",
            "Ubicación incompleta",
            "Selecciona una ubicación válida en el mapa."
        );

        return false;
    }

    /* IMAGES */

    const imageInputs = [
        ...document.querySelectorAll(".file-input")
    ];

    const newImagesCount = imageInputs.reduce(
        (total, input) => total + (input.files?.length || 0),
        0
    );

    const deletedInputs = [
        ...form.querySelectorAll('input[name="deleted_images[]"]')
    ];

    const deletedIds = new Set(
        deletedInputs.map(input => input.value)
    );

    const existingImageButtons = [
        ...document.querySelectorAll(".remove-existing-image")
    ];

    const existingImageCount = existingImageButtons.filter(
        button => !deletedIds.has(button.dataset.imageId)
    ).length;

    if (newImagesCount + existingImageCount > MAX_IMAGES) {
        alertBox(
            "warning",
            "Demasiadas imágenes",
            "La propiedad puede tener un máximo de 7 imágenes."
        );

        return false;
    }

    if (newImagesCount + existingImageCount === 0) {
        alertBox(
            "warning",
            "Imagen requerida",
            "La propiedad debe tener al menos una imagen."
        );

        return false;
    }

    return true;
}


/* ==================================================
   SUBMIT — UPDATE PROPERTY
================================================== */

if (form) {
    form.addEventListener("submit", async event => {
        event.preventDefault();

        if (!validateProperty()) {
            return;
        }

        const formData = new FormData(form);

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
            const response = await fetch(
                "actions/updateProperty.php",
                {
                    method: "POST",
                    body: formData
                }
            );

            const result = (
                await response.text()
            ).trim();

            console.log("Update property:", result);

            Swal.close();

            /*
             * The PHP backend returns the names of missing fields:
             * empty_fields:name,canton,latitude
             */

            if (result.startsWith("empty_fields:")) {
                const missingFields = result
                    .slice("empty_fields:".length)
                    .split(",")
                    .filter(Boolean);

                const fieldLabels = {
                    name: "Property name",
                    province: "Province",
                    canton: "Canton",
                    price: "Price",
                    size: "Size",
                    size_unit: "Size unit",
                    property_type: "Property type",
                    description: "Description",
                    address: "Address",
                    latitude: "Latitude",
                    longitude: "Longitude"
                };

                const missingLabels = missingFields
                    .map(field => fieldLabels[field] || field)
                    .join(", ");

                await alertBox(
                    "warning",
                    "Campos incompletos",
                    `El servidor no recibió estos campos: ${missingLabels}.`
                );

                return;
            }

            switch (result) {

                case "success":

                    await Swal.fire({
                        icon: "success",
                        title: "¡Propiedad actualizada!",
                        text: "Los cambios se guardaron correctamente.",
                        confirmButtonColor: "#334d20"
                    });

                    window.location.href = "dashboard.php";

                    break;

                case "invalid_request":

                    alertBox(
                        "error",
                        "Solicitud inválida",
                        "No fue posible procesar la solicitud."
                    );

                    break;

                case "not_logged":

                    await alertBox(
                        "warning",
                        "Sesión expirada",
                        "Debes iniciar sesión nuevamente."
                    );

                    window.location.href = "login.php";

                    break;

                case "property_not_found":

                    await alertBox(
                        "error",
                        "Propiedad no encontrada",
                        "No fue posible encontrar la propiedad o no tienes permiso para editarla."
                    );

                    window.location.href = "dashboard.php";

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

                case "invalid_size_unit":

                    alertBox(
                        "warning",
                        "Unidad inválida",
                        "Selecciona M² o SQFT."
                    );

                    break;

                case "invalid_bedrooms":

                    alertBox(
                        "warning",
                        "Habitaciones inválidas",
                        "Revisa la cantidad de habitaciones."
                    );

                    break;

                case "invalid_bathrooms":

                    alertBox(
                        "warning",
                        "Baños inválidos",
                        "Revisa la cantidad de baños."
                    );

                    break;

                case "invalid_property_type":

                    alertBox(
                        "warning",
                        "Tipo de propiedad inválido",
                        "Selecciona un tipo de propiedad válido."
                    );

                    break;

                case "invalid_status":

                    alertBox(
                        "warning",
                        "Estado inválido",
                        "Selecciona Active o Inactive."
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
                        "Una de las imágenes no tiene un formato permitido. Usa JPG, PNG o WEBP."
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
                        "La propiedad puede tener un máximo de 7 imágenes."
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

                    console.error(
                        "Respuesta inesperada del servidor:",
                        result
                    );

                    alertBox(
                        "error",
                        "Error inesperado",
                        "Ocurrió un error al actualizar la propiedad. Revisa la consola para más detalles."
                    );
            }

        } catch (error) {
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
    });
}


/* ==================================================
   INITIALIZE
================================================== */

initializeForm();