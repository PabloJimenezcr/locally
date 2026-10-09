/*==================================================
    ADD PROPERTY
==================================================*/

const form =
    document.getElementById("propertyForm");

const tagsContainer =
    document.getElementById("tagsContainer");

const tagInput =
    document.getElementById("tagInput");

const addTagBtn =
    document.getElementById("addTagBtn");

const propertyTags =
    document.getElementById("propertyTags");

const propertyStatus =
    document.getElementById("propertyStatus");

const propertyCanton =
    document.getElementById("propertyCanton");

const propertyAddress =
    document.getElementById("propertyAddress");

const propertyLatitude =
    document.getElementById("propertyLatitude");

const propertyLongitude =
    document.getElementById("propertyLongitude");


/*==================================================
    CONSTANTES
==================================================*/

const MAX_IMAGE_SIZE =
    5 * 1024 * 1024;

const MAX_IMAGES = 7;

const ALLOWED_IMAGES = [
    "image/jpeg",
    "image/png",
    "image/webp"
];


/*==================================================
    SWEET ALERT
==================================================*/

function alertBox(icon, title, text) {

    return Swal.fire({

        icon,
        title,
        text,

        confirmButtonColor:
            "#334d20"

    });

}


/*==================================================
    COUNTERS
==================================================*/


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



/*==================================================
    TAGS
==================================================*/

let tags = [];


function syncTags() {

    propertyTags.value =
        JSON.stringify(tags);

}


function renderTags() {

    tagsContainer.innerHTML = "";


    tags.forEach(
        (tag, index) => {

            const element =
                document.createElement("span");


            element.innerHTML = `
                ${tag}
                <i
                    class="ri-close-line"
                    role="button"
                    aria-label="Remove ${tag}">
                </i>
            `;


            element
                .querySelector("i")
                .addEventListener(
                    "click",
                    () => {

                        tags.splice(
                            index,
                            1
                        );

                        renderTags();
                        syncTags();

                    }
                );


            tagsContainer.appendChild(
                element
            );

        }
    );

}


function addTag() {

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


addTagBtn.addEventListener(
    "click",
    addTag
);


tagInput.addEventListener(
    "keydown",
    e => {

        if (e.key === "Enter") {

            e.preventDefault();

            addTag();

        }

    }
);


/*==================================================
    STATUS
==================================================*/

document
    .querySelectorAll(".box")
    .forEach(box => {

        box.addEventListener(
            "click",
            () => {

                document
                    .querySelectorAll(".box")
                    .forEach(
                        item =>
                            item.classList.remove(
                                "active"
                            )
                    );


                box.classList.add(
                    "active"
                );


                propertyStatus.value =
                    box.dataset.status;

            }
        );

    });


/*==================================================
    IMAGES
==================================================*/

const gallery =
    document.querySelector(".gallery");


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


        /*------------------------------------------
            TIPO
        ------------------------------------------*/

        if (
            !ALLOWED_IMAGES.includes(
                file.type
            )
        ) {

            input.value = "";

            return alertBox(

                "warning",

                "Imagen inválida",

                "Solo se permiten JPG, JPEG, PNG y WEBP."

            );

        }


        /*------------------------------------------
            TAMAÑO
        ------------------------------------------*/

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


        /*------------------------------------------
            SLOT
        ------------------------------------------*/

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


        /*------------------------------------------
            PREVIEW
        ------------------------------------------*/

        const image =
            document.createElement(
                "img"
            );


        image.src =
            URL.createObjectURL(
                file
            );


        image.className =
            "image-preview";


        image.alt =
            "Property image preview";


        /*------------------------------------------
            REMOVE BUTTON
        ------------------------------------------*/

        const removeButton =
            document.createElement(
                "button"
            );


        removeButton.type =
            "button";


        removeButton.className =
            "remove-image";


        removeButton.innerHTML =
            '<i class="ri-close-line" aria-hidden="true"></i>';


        removeButton.setAttribute(
            "aria-label",
            "Remove image"
        );


        removeButton.addEventListener(
            "click",
            e => {

                e.preventDefault();

                e.stopPropagation();

                input.value = "";


                URL.revokeObjectURL(
                    image.src
                );


                image.remove();

                removeButton.remove();


                if (content) {

                    content.style.display =
                        "";

                }

            }
        );


        /*------------------------------------------
            OCULTAR CONTENIDO
        ------------------------------------------*/

        if (content) {

            content.style.display =
                "none";

        }


        slot.appendChild(
            image
        );


        slot.appendChild(
            removeButton
        );

    }
);


/*==================================================
    GOOGLE MAPS
==================================================*/

let map;

let marker;

let autocomplete;

let geocoder;


/*==================================================
    OBTENER COMPONENTE DE DIRECCIÓN
==================================================*/

function getAddressComponent(
    components,
    type
) {

    return components?.find(
        component =>
            component.types.includes(
                type
            )
    )?.long_name || "";

}


/*==================================================
    OBTENER UBICACIÓN ADMINISTRATIVA
==================================================*/

function updateLocationData(
    location
) {

    geocoder.geocode(
        { location },
        (results, status) => {

            if (
                status !== "OK" ||
                !results?.length
            ) {

                console.warn(
                    "Reverse geocoding failed:",
                    status
                );

                return;

            }


            const components =
                results[0]
                    .address_components ||
                [];


            /*--------------------------------------
                DIRECCIÓN
            --------------------------------------*/

            propertyAddress.value =
                results[0]
                    .formatted_address ||
                "";


            /*--------------------------------------
                PROVINCIA
            --------------------------------------*/

            const province =
                getAddressComponent(
                    components,
                    "administrative_area_level_1"
                );


            /*--------------------------------------
                CANTÓN
            --------------------------------------*/

            let canton =
                getAddressComponent(
                    components,
                    "administrative_area_level_2"
                );


            /*
             * FALLBACK PARA COSTA RICA
             *
             * Algunas direcciones pueden devolver
             * "Sardinal de Carrillo" como locality.
             */

            if (!canton) {

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


                    canton =
                        match
                            ? match[2].trim()
                            : locality;

                }

            }


            /*--------------------------------------
                GUARDAR DATOS
            --------------------------------------*/

            propertyCanton.value =
                canton;


            /*
             * Mantener la provincia sincronizada
             * con Google Maps.
             */

            const provinceSelect =
                document.getElementById(
                    "province"
                );


            if (provinceSelect) {

                const option =
                    [...provinceSelect.options]
                        .find(
                            option =>

                                option.value
                                    .toLowerCase()
                                    .includes(

                                        province
                                            .replace(
                                                "Provincia de ",
                                                ""
                                            )
                                            .toLowerCase()

                                    )
                        );


                if (option) {

                    provinceSelect.value =
                        option.value;

                }

            }


            console.log(
                "LOCATION DATA:",
                {

                    province,

                    canton,

                    address:
                        propertyAddress.value,

                    latitude:
                        propertyLatitude.value,

                    longitude:
                        propertyLongitude.value

                }
            );

        }
    );

}


/*==================================================
    INICIALIZAR MAPA
==================================================*/

function initMap() {

    const defaultLocation = {

        lat: 9.9281,

        lng: -84.0907

    };


    geocoder =
        new google.maps.Geocoder();


    map =
        new google.maps.Map(

            document.getElementById(
                "map"
            ),

            {

                center:
                    defaultLocation,

                zoom: 13

            }

        );


    marker =
        new google.maps.Marker({

            position:
                defaultLocation,

            map:
                map,

            draggable:
                true

        });


    const input =
        document.getElementById(
            "searchInput"
        );


    autocomplete =
        new google.maps.places.Autocomplete(
            input
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


            map.setZoom(
                16
            );


            marker.setPosition(
                location
            );


            propertyLatitude.value =
                location.lat();


            propertyLongitude.value =
                location.lng();


            /*
             * Aquí dejamos que Geocoder
             * determine provincia/cantón.
             */

            updateLocationData(
                location
            );

        }
    );


    /*--------------------------------------
        MARCADOR
    --------------------------------------*/

    marker.addListener(
        "dragend",
        () => {

            const position =
                marker.getPosition();


            propertyLatitude.value =
                position.lat();


            propertyLongitude.value =
                position.lng();


            /*
             * Volvemos a obtener
             * provincia/cantón/dirección.
             */

            updateLocationData(
                position
            );

        }
    );

}


initMap();


/*==================================================
    VALIDAR PROPIEDAD
==================================================*/

function validateProperty() {

    const name =
        document.getElementById(
            "propertyName"
        );


    const province =
        document.getElementById(
            "province"
        );


    const price =
        document.getElementById(
            "propertyPrice"
        );


    const size =
        document.getElementById(
            "propertySize"
        );


    const type =
        document.getElementById(
            "propertyType"
        );


    const description =
        document.getElementById(
            "propertyDescription"
        );


    /*================================================
        NOMBRE
    ================================================*/

    if (
        !name.value.trim()
    ) {

        name.focus();

        return alertBox(

            "warning",

            "Nombre requerido",

            "Ingresa el nombre de la propiedad."

        );

    }


    if (
        name.value.trim().length < 3
    ) {

        name.focus();

        return alertBox(

            "warning",

            "Nombre inválido",

            "Debe tener al menos 3 caracteres."

        );

    }


    /*================================================
        PROVINCIA
    ================================================*/

    if (
        !province.value
    ) {

        province.focus();

        return alertBox(

            "warning",

            "Provincia requerida",

            "Selecciona una provincia."

        );

    }


    /*================================================
        PRECIO
    ================================================*/

    if (

        !price.value ||
        Number(price.value) <= 0

    ) {

        price.focus();

        return alertBox(

            "warning",

            "Precio inválido",

            "Ingresa un precio mayor que cero."

        );

    }


    /*================================================
        TAMAÑO
    ================================================*/

    if (

        !size.value ||
        Number(size.value) <= 0

    ) {

        size.focus();

        return alertBox(

            "warning",

            "Tamaño inválido",

            "Ingresa un tamaño mayor que cero."

        );

    }


    /*================================================
        TIPO
    ================================================*/

    if (
        !type.value
    ) {

        type.focus();

        return alertBox(

            "warning",

            "Tipo requerido",

            "Selecciona el tipo de propiedad."

        );

    }


    /*================================================
        DESCRIPCIÓN
    ================================================*/

    const descriptionValue =
        description.value.trim();


    if (!descriptionValue) {

        description.focus();

        return alertBox(

            "warning",

            "Descripción requerida",

            "Ingresa una descripción."

        );

    }


    if (
        descriptionValue.length < 20
    ) {

        description.focus();

        return alertBox(

            "warning",

            "Descripción muy corta",

            "Debe tener al menos 20 caracteres."

        );

    }


    /*================================================
        UBICACIÓN
    ================================================*/

    if (

        !propertyAddress.value ||
        !propertyCanton.value ||
        !propertyLatitude.value ||
        !propertyLongitude.value

    ) {

        document
            .getElementById(
                "searchInput"
            )
            .focus();


        return alertBox(

            "warning",

            "Ubicación incompleta",

            "Selecciona una dirección válida en el mapa."

        );

    }


    /*================================================
        IMÁGENES
    ================================================*/

    const imageInputs = [
        ...document.querySelectorAll(
            ".file-input"
        )
    ];


    const primaryImage =
        imageInputs[0];


    const selectedImages =
        imageInputs.filter(
            input =>
                input.files.length > 0
        );


    /*----------------------------------------------
        PRIMERA IMAGEN OBLIGATORIA
    ----------------------------------------------*/

    if (

        !primaryImage ||
        primaryImage.files.length === 0

    ) {

        primaryImage?.focus();


        return alertBox(

            "warning",

            "Main image required",

            "You must upload the first image. This will be the main property image."

        );

    }


    /*----------------------------------------------
        MÁXIMO 7 IMÁGENES
    ----------------------------------------------*/

    if (
        selectedImages.length > MAX_IMAGES
    ) {

        return alertBox(

            "warning",

            "Too many images",

            "You can upload up to 7 images per property."

        );

    }


    /*================================================
        TODO CORRECTO
    ================================================*/

    return true;

}


/*==================================================
    GUARDAR PROPIEDAD
==================================================*/

form.addEventListener(
    "submit",
    async e => {

        e.preventDefault();


        /*------------------------------------------
            VALIDAR
        ------------------------------------------*/

        if (
            !validateProperty()
        ) {

            return;

        }


        /*------------------------------------------
            FORM DATA
        ------------------------------------------*/

        const formData =
            new FormData(form);


        formData.set(
            "tags",
            JSON.stringify(tags)
        );


        /*------------------------------------------
            LOADING
        ------------------------------------------*/

        Swal.fire({

            title:
                "Guardando propiedad...",

            text:
                "Un momento, estamos guardando la información.",

            allowOutsideClick:
                false,

            allowEscapeKey:
                false,

            didOpen:
                () =>
                    Swal.showLoading()

        });


        try {

            /*--------------------------------------
                REQUEST
            --------------------------------------*/

            const response =
                await fetch(

                    "actions/createProperty.php",

                    {

                        method:
                            "POST",

                        body:
                            formData

                    }

                );


            /*--------------------------------------
                RESPONSE
            --------------------------------------*/

            const result =
                (
                    await response.text()
                ).trim();


            console.log(
                "Create property:",
                result
            );


            Swal.close();


            /*--------------------------------------
                RESPUESTAS
            --------------------------------------*/

            switch (result) {


                /*==================================
                    SUCCESS
                ==================================*/

                case "success":

                    await Swal.fire({

                        icon:
                            "success",

                        title:
                            "¡Propiedad creada!",

                        text:
                            "La propiedad se guardó correctamente.",

                        confirmButtonColor:
                            "#334d20"

                    });


                    window.location.href =
                        "dashboard.php";


                    break;


                /*==================================
                    INVALID REQUEST
                ==================================*/

                case "invalid_request":

                    alertBox(

                        "error",

                        "Solicitud inválida",

                        "No fue posible procesar la solicitud."

                    );

                    break;


                /*==================================
                    EMPTY FIELDS
                ==================================*/

                case "empty_fields":

                    alertBox(

                        "warning",

                        "Campos incompletos",

                        "Completa la información requerida."

                    );

                    break;


                /*==================================
                    NAME
                ==================================*/

                case "invalid_name":

                    alertBox(

                        "warning",

                        "Nombre inválido",

                        "El nombre debe tener entre 3 y 150 caracteres."

                    );

                    break;


                /*==================================
                    PRICE
                ==================================*/

                case "invalid_price":

                    alertBox(

                        "warning",

                        "Precio inválido",

                        "Ingresa un precio mayor que cero."

                    );

                    break;


                /*==================================
                    SIZE
                ==================================*/

                case "invalid_size":

                    alertBox(

                        "warning",

                        "Tamaño inválido",

                        "Ingresa un tamaño mayor que cero."

                    );

                    break;


                /*==================================
                    BEDROOMS
                ==================================*/

                case "invalid_bedrooms":

                    alertBox(

                        "warning",

                        "Habitaciones inválidas",

                        "Revisa la cantidad de habitaciones."

                    );

                    break;


                /*==================================
                    BATHROOMS
                ==================================*/

                case "invalid_bathrooms":

                    alertBox(

                        "warning",

                        "Baños inválidos",

                        "Revisa la cantidad de baños."

                    );

                    break;


                /*==================================
                    SIZE UNIT
                ==================================*/

                case "invalid_size_unit":

                    alertBox(

                        "warning",

                        "Unidad inválida",

                        "Selecciona M² o SQFT."

                    );

                    break;


                /*==================================
                    PROPERTY TYPE
                ==================================*/

                case "invalid_property_type":

                    alertBox(

                        "warning",

                        "Tipo de propiedad inválido",

                        "Selecciona un tipo válido."

                    );

                    break;


                /*==================================
                    STATUS
                ==================================*/

                case "invalid_status":

                    alertBox(

                        "warning",

                        "Estado inválido",

                        "Selecciona Active o Inactive."

                    );

                    break;


                /*==================================
                    DESCRIPTION
                ==================================*/

                case "invalid_description":

                    alertBox(

                        "warning",

                        "Descripción inválida",

                        "La descripción debe tener entre 20 y 5000 caracteres."

                    );

                    break;


                /*==================================
                    LOCATION
                ==================================*/

                case "invalid_location":

                    alertBox(

                        "warning",

                        "Ubicación inválida",

                        "Selecciona una ubicación válida en el mapa."

                    );

                    break;


                /*==================================
                    PRIMARY IMAGE
                ==================================*/

                case "primary_image_required":

                    alertBox(

                        "warning",

                        "Main image required",

                        "You must upload the first image before creating the property."

                    );

                    break;


                /*==================================
                    INVALID IMAGE
                ==================================*/

                case "invalid_image":

                    alertBox(

                        "warning",

                        "Imagen inválida",

                        "Una de las imágenes no es válida."

                    );

                    break;


                /*==================================
                    LARGE IMAGE
                ==================================*/

                case "large_image":

                    alertBox(

                        "warning",

                        "Imagen demasiado grande",

                        "Cada imagen puede tener un máximo de 5 MB."

                    );

                    break;


                /*==================================
                    TOO MANY IMAGES
                ==================================*/

                case "too_many_images":

                    alertBox(

                        "warning",

                        "Too many images",

                        "You can upload up to 7 images per property."

                    );

                    break;


                /*==================================
                    UPLOAD ERROR
                ==================================*/

                case "upload_error":

                    alertBox(

                        "error",

                        "Error al guardar imágenes",

                        "No fue posible guardar una de las imágenes."

                    );

                    break;


                /*==================================
                    SERVER ERROR
                ==================================*/

                case "server_error":

                    alertBox(

                        "error",

                        "Error del servidor",

                        "No fue posible guardar la propiedad."

                    );

                    break;


                /*==================================
                    NOT LOGGED
                ==================================*/

                case "not_logged":

                    await alertBox(

                        "warning",

                        "Sesión expirada",

                        "Debes iniciar sesión nuevamente."

                    );


                    window.location.href =
                        "login.php";


                    break;


                /*==================================
                    DEFAULT
                ==================================*/

                default:

                    console.log(
                        "Respuesta inesperada:",
                        result
                    );


                    alertBox(

                        "error",

                        "Error inesperado",

                        "Ocurrió un error inesperado. Inténtalo nuevamente."

                    );

                    break;

            }


        } catch (error) {

            console.error(
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