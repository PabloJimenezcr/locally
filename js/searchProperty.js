/*==================================================
    DASHBOARD
==================================================*/


/*==================================================
    SEARCH PROPERTIES
==================================================*/

const propertySearch =
    document.getElementById("propertySearch");

const propertyItems =
    document.querySelectorAll(".property-item");

const noSearchResults =
    document.getElementById("noSearchResults");


/*==================================================
    NORMALIZAR TEXTO
==================================================*/

function normalizeText(text) {

    return text
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .trim();

}


/*==================================================
    SEARCH
==================================================*/

if (propertySearch) {

    propertySearch.addEventListener(
        "input",
        function () {

            const search =
                normalizeText(this.value);

            let visibleCount = 0;


            propertyItems.forEach(item => {

                const name =
                    normalizeText(
                        item.dataset.name || ""
                    );

                const canton =
                    normalizeText(
                        item.dataset.canton || ""
                    );

                const province =
                    normalizeText(
                        item.dataset.province || ""
                    );

                const status =
                    normalizeText(
                        item.dataset.status || ""
                    );


                const matches =
                    name.includes(search) ||
                    canton.includes(search) ||
                    province.includes(search) ||
                    status.includes(search);


                if (matches) {

                    item.style.display = "";

                    visibleCount++;

                } else {

                    item.style.display = "none";

                }

            });


            /*======================================
                MOSTRAR / OCULTAR EMPTY STATE
            ======================================*/

            if (noSearchResults) {

                noSearchResults.classList.toggle(
                    "d-none",
                    visibleCount > 0
                );

            }

        }
    );

}