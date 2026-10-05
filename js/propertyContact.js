/*==================================================
    PROPERTY CONTACT FORM
==================================================*/

const contactForm =
    document.getElementById(
        "propertyContactForm"
    );


/*==================================================
    FORM TIME
==================================================*/

const formTime =
    document.getElementById("formTime");


if (formTime) {

    formTime.value =
        Math.floor(
            Date.now() / 1000
        );

}


/*==================================================
    ALERT
==================================================*/

function contactAlert(
    icon,
    title,
    text
) {

    return Swal.fire({

        icon,
        title,
        text,

        confirmButtonColor:
            "#334d20"

    });

}


/*==================================================
    SUBMIT
==================================================*/

if (contactForm) {

    contactForm.addEventListener(
        "submit",
        async event => {

            event.preventDefault();


            const name =
                document.getElementById(
                    "contactName"
                );


            const email =
                document.getElementById(
                    "contactEmail"
                );


            const message =
                document.getElementById(
                    "contactMessage"
                );


            /*======================================
                FRONTEND VALIDATION
            ======================================*/

            if (!name.value.trim()) {

                name.focus();

                return contactAlert(
                    "warning",
                    "Name required",
                    "Please enter your name."
                );

            }


            if (
                name.value.trim().length < 2
            ) {

                name.focus();

                return contactAlert(
                    "warning",
                    "Invalid name",
                    "Please enter a valid name."
                );

            }


            if (!email.value.trim()) {

                email.focus();

                return contactAlert(
                    "warning",
                    "Email required",
                    "Please enter your email."
                );

            }


            if (
                !email.checkValidity()
            ) {

                email.focus();

                return contactAlert(
                    "warning",
                    "Invalid email",
                    "Please enter a valid email address."
                );

            }


            if (!message.value.trim()) {

                message.focus();

                return contactAlert(
                    "warning",
                    "Message required",
                    "Please enter your message."
                );

            }


            if (
                message.value.trim().length < 10
            ) {

                message.focus();

                return contactAlert(
                    "warning",
                    "Message too short",
                    "Please tell us a little more about your inquiry."
                );

            }


            /*======================================
                SEND
            ======================================*/

            const submitButton =
                contactForm.querySelector(
                    'button[type="submit"]'
                );


            const originalButton =
                submitButton.innerHTML;


            submitButton.disabled =
                true;


            submitButton.innerHTML = `
                <span
                    class="spinner-border spinner-border-sm me-2"
                    aria-hidden="true">
                </span>
                Sending...
            `;


            const formData =
                new FormData(
                    contactForm
                );


            try {

                const response =
                    await fetch(
                        "actions/sendPropertyInquiry.php",
                        {
                            method: "POST",
                            body: formData
                        }
                    );


                const result =
                    (
                        await response.text()
                    ).trim();


                console.log(
                    "Property inquiry:",
                    result
                );


                switch (result) {

                    case "success":

                        await contactAlert(
                            "success",
                            "Message sent",
                            "Thank you. Our team will contact you soon."
                        );


                        contactForm.reset();


                        if (formTime) {

                            formTime.value =
                                Math.floor(
                                    Date.now() / 1000
                                );

                        }

                        break;


                    case "invalid_request":

                        contactAlert(
                            "error",
                            "Invalid request",
                            "The request could not be processed."
                        );

                        break;


                    case "spam_detected":

                        contactAlert(
                            "error",
                            "Unable to send",
                            "The message could not be submitted."
                        );

                        break;


                    case "rate_limited":

                        contactAlert(
                            "warning",
                            "Please wait",
                            "Please wait a moment before sending another message."
                        );

                        break;


                    case "invalid_property":

                        contactAlert(
                            "error",
                            "Property unavailable",
                            "This property is no longer available."
                        );

                        break;


                    case "empty_fields":

                        contactAlert(
                            "warning",
                            "Incomplete information",
                            "Please complete all required fields."
                        );

                        break;


                    case "invalid_email":

                        contactAlert(
                            "warning",
                            "Invalid email",
                            "Please enter a valid email address."
                        );

                        break;


                    case "invalid_message":

                        contactAlert(
                            "warning",
                            "Invalid message",
                            "Please enter a valid message."
                        );

                        break;


                    case "mail_error":

                        contactAlert(
                            "error",
                            "Unable to send",
                            "We could not send your message. Please try again."
                        );

                        break;


                    default:

                        contactAlert(
                            "error",
                            "Something went wrong",
                            "Please try again later."
                        );

                }

            } catch (error) {

                console.error(
                    "Contact form error:",
                    error
                );


                contactAlert(
                    "error",
                    "Connection error",
                    "We could not contact the server."
                );

            }


            submitButton.disabled =
                false;


            submitButton.innerHTML =
                originalButton;

        }
    );

}