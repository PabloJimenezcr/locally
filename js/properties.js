/* =========================================================
   LOCALLY - PROPERTIES
   Filters / Price Range / Sort
========================================================= */


/* =========================================================
   ELEMENTOS
========================================================= */

const filtersForm = document.getElementById("filtersForm");

const propertyTypeInput =
    document.getElementById("propertyTypeInput");

const bedroomsInput =
    document.getElementById("bedroomsInput");

const bathroomsInput =
    document.getElementById("bathroomsInput");

const priceRange =
    document.getElementById("priceRange");

const maxPriceLabel =
    document.getElementById("maxPriceLabel");

const sortSelect =
    document.getElementById("sortSelect");


/* =========================================================
   PROPERTY TYPE
========================================================= */

const propertyTypeButtons =
    document.querySelectorAll("[data-property-type]");


propertyTypeButtons.forEach((button) => {

    button.addEventListener("click", function () {

        const value =
            this.dataset.propertyType;


        if (propertyTypeInput) {

            propertyTypeInput.value = value;

        }


        propertyTypeButtons.forEach((btn) => {

            btn.classList.remove("active");

        });


        this.classList.add("active");

    });

});


/* =========================================================
   BEDROOMS
========================================================= */

const bedroomButtons =
    document.querySelectorAll("[data-bedrooms]");


bedroomButtons.forEach((button) => {

    button.addEventListener("click", function () {

        const value =
            Number(this.dataset.bedrooms);


        if (bedroomsInput) {

            bedroomsInput.value = value;

        }


        bedroomButtons.forEach((btn) => {

            btn.classList.remove("active");

        });


        this.classList.add("active");

    });

});


/* =========================================================
   BATHROOMS
========================================================= */

const bathroomButtons =
    document.querySelectorAll("[data-bathrooms]");


bathroomButtons.forEach((button) => {

    button.addEventListener("click", function () {

        const value =
            Number(this.dataset.bathrooms);


        if (bathroomsInput) {

            bathroomsInput.value = value;

        }


        bathroomButtons.forEach((btn) => {

            btn.classList.remove("active");

        });


        this.classList.add("active");

    });

});


/* =========================================================
   PRICE RANGE
========================================================= */

function formatPrice(value) {

    value = Number(value);


    if (value >= 1000000) {

        const millions =
            value / 1000000;


        return "$" +
            millions.toFixed(
                millions % 1 === 0
                    ? 0
                    : 1
            ) +
            "M";

    }


    return "$" +
        Math.round(value / 1000) +
        "k";

}


/* =========================================================
   ACTUALIZAR PRICE RANGE
========================================================= */

function updatePriceRange() {

    if (!priceRange || !maxPriceLabel) {
        return;
    }


    const value =
        Number(priceRange.value);

    const min =
        Number(priceRange.min);

    const max =
        Number(priceRange.max);


    const percent =
        ((value - min) / (max - min)) * 100;


    /*
     * Actualiza visualmente
     * el progreso del slider.
     */

    priceRange.style.background =
        `linear-gradient(
            to right,
            #334d20 ${percent}%,
            #e5e7eb ${percent}%
        )`;


    /*
     * Actualiza el valor máximo visible.
     */

    maxPriceLabel.textContent =
        formatPrice(value);

}


if (priceRange) {

    priceRange.addEventListener(
        "input",
        updatePriceRange
    );


    updatePriceRange();

}


/* =========================================================
   SORT
========================================================= */

if (sortSelect && filtersForm) {

    sortSelect.addEventListener(
        "change",
        function () {

            filtersForm.submit();

        }
    );

}