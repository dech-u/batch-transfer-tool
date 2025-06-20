// document.addEventListener("DOMContentLoaded", function () {
//     const eventDescElements = document.querySelectorAll(".eventDesc");

//     eventDescElements.forEach(function (eventDescElement) {
//         const originalText = eventDescElement.textContent;
//         const maxLength = 80;
//         const readMoreBtn = eventDescElement.nextElementSibling; // Assuming the button is a sibling

//         if (originalText.length > maxLength) {
//             const truncatedText = originalText.substring(0, maxLength) + "...";
//             eventDescElement.textContent = truncatedText;
//             readMoreBtn.style.display = "inline"; // Show the button
//         } else {
//             readMoreBtn.style.display = "none"; // Hide the button
//         }

//         // Add event listener to "Read More" button
//         readMoreBtn.addEventListener("click", function () {
//             // Get the title from eventDescWrapper
//             const title = eventDescElement.parentElement.querySelector(".eventTitle").textContent;
//             // Get the date and month from eventDateWrapper
//             const date = eventDescElement.parentElement.previousElementSibling.querySelector(".date").textContent;
//             const month = eventDescElement.parentElement.previousElementSibling.querySelector(".month").textContent;
//             // Display the full event information in the modal
//             document.getElementById("fullEventTitle").textContent = title;
//             document.getElementById("fullEventDate").textContent = `${date} ${month}`;
//             document.getElementById("fullEventDescription").textContent = originalText;
//             // Show the modal
//             const modal = document.getElementById("eventModal");
//             modal.style.display = "block";
//             // Disable scrolling
//             document.body.classList.add("modal-open");
//         });
//     });

//     // Close the modal when the close button is clicked
//     document.querySelector(".close").addEventListener("click", function () {
//         const modal = document.getElementById("eventModal");
//         modal.style.display = "none";
//         // Enable scrolling
//         document.body.classList.remove("modal-open");
//     })
// });


document.addEventListener("DOMContentLoaded", function () {
    var swiper = new Swiper(".swiper-container", {
        slidesPerView: 3,
        spaceBetween: 30,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
    });

    // Close the modal when clicking outside of it
    window.addEventListener("click", function (event) {
        if (event.target == document.getElementById("eventModal")) {
            const modal = document.getElementById("eventModal");
            modal.style.display = "none";
            // Enable scrolling
            document.body.classList.remove("modal-open");
        }
    });
});
// Get the modal
var modal = document.getElementById("announcementModal");

// Get the span that closes the modal
var spanClose = document.getElementsByClassName("closeBtn")[0];

// Get all the spans with the rightArr class
var rightArrs = document.querySelectorAll(".rightArr");

// Loop through each rightArr span and add a click event listener
rightArrs.forEach(function (rightArr) {
    rightArr.addEventListener("click", function () {
        modal.style.display = "block";
        document.body.classList.add("modal-open"); // Disable body scroll
    });
});

// When the user clicks on <span> (x), close the modal
if (spanClose) {
    spanClose.onclick = function () {
        modal.style.display = "none";
        document.body.classList.remove("modal-open"); // Enable body scroll
    }    
}


// When the user clicks anywhere outside of the modal, close it
window.onclick = function (event) {
    if (event.target == modal) {
        modal.style.display = "none";
        document.body.classList.remove("modal-open"); // Enable body scroll
    }
}
