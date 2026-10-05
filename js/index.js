/* =========================================================
   LOCALLY - HOME / INDEX
========================================================= */


/* =========================================================
   CONTACT FORM
========================================================= */

const contactForm =
    document.getElementById("contactForm");

const contactFormTime =
    document.getElementById("contactFormTime");

const contactSubmit =
    document.getElementById("contactSubmit");


/*
 * Guardar momento en que se renderizó el formulario.
 */

if (contactForm && contactFormTime) {

    contactFormTime.value =
        Math.floor(Date.now() / 1000);

}


/* =========================================================
   CONTACT VALIDATION
========================================================= */

function validateContactField(input) {

    const value =
        input.value.trim();


    /* Nombre */

    if (input.name === "name") {

        if (!value) {
            return "Please enter your name.";
        }

        if (value.length < 3) {
            return "Your name must contain at least 3 characters.";
        }

    }


    /* Email */

    if (input.name === "email") {

        if (!value) {
            return "Please enter your email.";
        }

        if (!input.checkValidity()) {
            return "Please enter a valid email address.";
        }

    }


    /* Phone */

    /* Phone */
    if (input.name === "phone") {

        if (!value) {
            return "Please enter your phone number.";
        }

        const phoneRegex = /^[0-9+\-\s()]{7,30}$/;

        if (!phoneRegex.test(value)) {
            return "Please enter a valid phone number.";
        }

        const digitsOnly = value.replace(/\D/g, "");

        if (digitsOnly.length < 7) {
            return "Please enter a valid phone number.";
        }
    }


    /* Message */

    if (input.name === "message") {

        if (!value) {
            return "Please enter your message.";
        }

        if (value.length < 10) {
            return "Your message must contain at least 10 characters.";
        }

    }


    return null;
}


/* =========================================================
   CONTACT SUBMIT
========================================================= */

if (contactForm) {

    contactForm.addEventListener(
        "submit",
        async function (e) {

            e.preventDefault();


            const inputs =
                contactForm.querySelectorAll(
                    "input:not([type='hidden']):not([name='website']), textarea"
                );


            /* ==========================
               FRONTEND VALIDATION
            ========================== */

            for (const input of inputs) {

                const error =
                    validateContactField(input);


                if (error) {

                    await Swal.fire({

                        icon: "warning",

                        title: "Check your information",

                        text: error,

                        confirmButtonColor: "#334d20"

                    });


                    input.focus();

                    return;

                }

            }


            /* ==========================
               PREVENT DOUBLE CLICK
            ========================== */

            if (contactSubmit) {

                contactSubmit.disabled = true;

                contactSubmit.dataset.originalText =
                    contactSubmit.innerHTML;

                contactSubmit.innerHTML =
                    '<i class="ri-loader-4-line ri-spin" aria-hidden="true"></i> Sending...';

            }


            /* ==========================
               LOADING
            ========================== */

            Swal.fire({

                title: "Sending message...",

                text: "Please wait a moment.",

                allowOutsideClick: false,

                allowEscapeKey: false,

                didOpen: () => {

                    Swal.showLoading();

                }

            });


            try {

                const response =
                    await fetch(
                        contactForm.action,
                        {
                            method: "POST",
                            body: new FormData(contactForm)
                        }
                    );


                const result =
                    (await response.text()).trim();


                console.log(
                    "Contact:",
                    result
                );


                Swal.close();


                /* ==========================
                   RESPONSES
                ========================== */

                switch (result) {

                    case "success":

                        await Swal.fire({

                            icon: "success",

                            title: "Message sent!",

                            text: "Thank you for contacting Locally. Our team will get back to you soon.",

                            confirmButtonColor: "#334d20"

                        });


                        contactForm.reset();


                        if (contactFormTime) {

                            contactFormTime.value =
                                Math.floor(Date.now() / 1000);

                        }


                        break;


                    case "spam_detected":

                        Swal.fire({

                            icon: "error",

                            title: "Request blocked",

                            text: "We could not process your request.",

                            confirmButtonColor: "#334d20"

                        });

                        break;


                    case "rate_limited":

                        Swal.fire({

                            icon: "warning",

                            title: "Please wait",

                            text: "You recently sent a message. Please try again in a moment.",

                            confirmButtonColor: "#334d20"

                        });

                        break;


                    case "invalid_data":

                        Swal.fire({

                            icon: "warning",

                            title: "Check your information",

                            text: "Please verify the information you entered.",

                            confirmButtonColor: "#334d20"

                        });

                        break;


                    case "mail_error":

                        Swal.fire({

                            icon: "error",

                            title: "Message not sent",

                            text: "We couldn't send your message. Please try again later.",

                            confirmButtonColor: "#334d20"

                        });

                        break;


                    case "server_error":

                    default:

                        Swal.fire({

                            icon: "error",

                            title: "Something went wrong",

                            text: "We couldn't process your request. Please try again later.",

                            confirmButtonColor: "#334d20"

                        });

                        break;

                }


            } catch (error) {

                console.error(
                    "Contact form error:",
                    error
                );


                Swal.close();


                Swal.fire({

                    icon: "error",

                    title: "Connection error",

                    text: "We couldn't contact the server.",

                    confirmButtonColor: "#334d20"

                });


            } finally {

                if (contactSubmit) {

                    contactSubmit.disabled = false;

                    contactSubmit.innerHTML =
                        contactSubmit.dataset.originalText ||
                        "Send";

                }

            }

        }
    );

}


/* =========================================================
   TESTIMONIAL SWIPER
========================================================= */

const testimonialSection =
    document.querySelector(
        ".testimonial-wrapper"
    );

const testimonialImages =
    document.querySelector(
        ".testimonialImages"
    );

const testimonialContent =
    document.querySelector(
        ".testimonialContent"
    );


if (
    testimonialSection &&
    testimonialImages &&
    testimonialContent &&
    typeof Swiper !== "undefined"
) {

    const imageSwiper =
        new Swiper(
            ".testimonialImages",
            {
                slidesPerView: 1,
                allowTouchMove: false,
                speed: 1000
            }
        );


    const contentSwiper =
        new Swiper(
            ".testimonialContent",
            {
                slidesPerView: 1,
                spaceBetween: 30,
                loop: true,
                speed: 1000,

                autoplay: {
                    delay: 4000,
                    disableOnInteraction: false
                },

                pagination: {
                    el: ".swiper-pagination",
                    clickable: true
                }
            }
        );


    contentSwiper.on(
        "slideChange",
        function () {

            imageSwiper.slideToLoop(
                contentSwiper.realIndex
            );

        }
    );


    testimonialSection.addEventListener(
        "mouseenter",
        () => {

            contentSwiper.autoplay.stop();

        }
    );


    testimonialSection.addEventListener(
        "mouseleave",
        () => {

            contentSwiper.autoplay.start();

        }
    );

}


/* =========================================================
   SCROLL TO TOP
========================================================= */

const scrollTopBtn =
    document.getElementById(
        "scrollTopBtn"
    );

const wave =
    document.getElementById(
        "wavePath"
    );


if (scrollTopBtn && wave) {

    let scrollPercent = 0;

    let currentLevel = 0;

    let time = 0;


    window.addEventListener(
        "scroll",
        () => {

            const scrollTop =
                window.scrollY;

            const docHeight =
                document.documentElement
                    .scrollHeight -
                window.innerHeight;


            scrollPercent =
                docHeight > 0
                    ? scrollTop / docHeight
                    : 0;


            if (scrollTop > 100) {

                scrollTopBtn.classList.add(
                    "show"
                );

            } else {

                scrollTopBtn.classList.remove(
                    "show"
                );

            }


            if (scrollPercent > 0.45) {

                scrollTopBtn.classList.add(
                    "is-covered"
                );

            } else {

                scrollTopBtn.classList.remove(
                    "is-covered"
                );

            }

        }
    );


    scrollTopBtn.addEventListener(
        "click",
        () => {

            window.scrollTo({

                top: 0,

                behavior: "smooth"

            });

        }
    );


    scrollTopBtn.addEventListener(
        "keydown",
        (e) => {

            if (
                e.key === "Enter" ||
                e.key === " "
            ) {

                e.preventDefault();


                window.scrollTo({

                    top: 0,

                    behavior: "smooth"

                });

            }

        }
    );


    function animateWave() {

        const width = 100;

        const height = 100;


        currentLevel +=
            (scrollPercent - currentLevel) *
            0.08;


        const level =
            height *
            (1 - currentLevel);


        let path =
            `M0 ${level}`;


        for (
            let x = 0;
            x <= width;
            x++
        ) {

            const y =
                level +
                Math.sin(
                    (x + time) * 0.06
                ) * 3 +
                Math.cos(
                    (x + time * 0.5) * 0.08
                ) * 2;


            path +=
                ` L${x} ${y}`;

        }


        path +=
            " L100 100 L0 100 Z";


        wave.setAttribute(
            "d",
            path
        );


        if (scrollPercent > 0.01) {

            time += 0.35;

        }


        requestAnimationFrame(
            animateWave
        );

    }


    animateWave();

}


/* =========================================================
   PAGE LOADER
========================================================= */

document.documentElement.classList.add(
    "is-loading"
);


window.addEventListener(
    "load",
    () => {

        const loader =
            document.getElementById(
                "locallyLoader"
            );


        if (
            !loader ||
            typeof gsap === "undefined"
        ) {

            document.documentElement.classList.remove(
                "is-loading"
            );

            return;

        }


        const tl =
            gsap.timeline({

                defaults: {
                    ease: "power3.out"
                }

            });


        tl.to(
            ".sun-pulse",
            {
                opacity: 1,
                scale: 1.12,
                duration: 1.2,
                ease: "power2.out"
            }
        )

            .to(
                ".topography-lines",
                {
                    opacity: 1,
                    duration: 1
                },
                "-=0.8"
            )

            .to(
                ".topography-lines span",
                {
                    rotate: "+=18",
                    scale: 1.08,
                    duration: 2.6,
                    stagger: 0.12,
                    ease: "sine.inOut"
                },
                "-=1"
            )

            .to(
                ".logo-glow",
                {
                    opacity: 1,
                    scale: 1.15,
                    duration: 0.9
                },
                "-=1.8"
            )

            .to(
                ".loader-logo",
                {
                    opacity: 1,
                    y: 0,
                    scale: 1,
                    duration: 1.2,
                    ease: "power4.out"
                },
                "-=1.45"
            )

            .to(
                ".leaf",
                {
                    opacity: 1,
                    y: "random(-28, -50)",
                    x: "random(-20, 22)",
                    rotate: "random(-45, 45)",
                    duration: 1.9,
                    stagger: 0.12,
                    ease: "sine.inOut"
                },
                "-=0.9"
            )

            .to(
                ".nature-particles span",
                {
                    opacity: 1,
                    y: -45,
                    x: "random(-32, 32)",
                    scale: "random(0.7, 1.5)",
                    duration: 2,
                    stagger: 0.055,
                    ease: "sine.inOut"
                },
                "-=1.5"
            )

            .to(
                ".bird-one",
                {
                    opacity: 1,
                    x: window.innerWidth + 180,
                    y: -35,
                    duration: 2,
                    ease: "power2.inOut"
                },
                "-=1.55"
            )

            .to(
                ".bird-two",
                {
                    opacity: 0.75,
                    x: -(window.innerWidth + 180),
                    y: 25,
                    duration: 2.2,
                    ease: "power2.inOut"
                },
                "-=1.85"
            )

            .to(
                ".loader-text",
                {
                    opacity: 1,
                    y: 0,
                    duration: 0.8
                },
                "-=1.05"
            )

            .to(
                ".loader-line span",
                {
                    width: "100%",
                    duration: 1.2,
                    ease: "power2.inOut"
                },
                "-=0.55"
            )

            .to(
                ".loader-content",
                {
                    y: -18,
                    scale: 0.96,
                    opacity: 0,
                    duration: 0.65,
                    ease: "power3.inOut"
                },
                "+=0.25"
            )

            .to(
                ".leaf-reveal",
                {
                    y: "-120%",
                    duration: 1.1,
                    ease: "power4.inOut"
                },
                "-=0.35"
            )

            .to(
                loader,
                {
                    opacity: 0,
                    duration: 0.35,

                    onComplete: () => {

                        document.documentElement.classList.remove(
                            "is-loading"
                        );

                        loader.classList.add(
                            "hide"
                        );

                        loader.style.display =
                            "none";

                    }

                }
            );

    }
);


